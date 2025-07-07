<?php

namespace App\Http\Controllers;

use App\Http\Requests\LibroGuardarRequest;
use App\Models\Libro;
use App\Repos\Data\AlumnoRepoData;
use App\Repos\Data\AreaRepoData;
use App\Repos\Data\AutorRepoData;
use App\Repos\Data\EditorialRepoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class LibroController extends Controller
{
  public function getDatosLibroAlta(Request $request)
  {
    $areaRepo = new AreaRepoData();
    $areas = $areaRepo->get([]);

    $autorRepo = new AutorRepoData();
    $autores = $autorRepo->get([]);

    $editorialRepo = new EditorialRepoData();
    $editoriales = $editorialRepo->get([]);

    // Retornar la vista de Inertia con los datos
    return Inertia::render('LibroAlta', [
      'autores' => $autores,
      'editoriales' => $editoriales,
      'areas' => $areas,
    ]);
  }

  public function guardar(LibroGuardarRequest $request)
  {
    try {
      DB::beginTransaction();
      Log::debug('entro');
      $areaRepoData = new AreaRepoData();
      $areaObj = $areaRepoData->getById($request->areaId);

      $editorialRepoData = new EditorialRepoData();
      $editorialObj = $editorialRepoData->getById($request->editorialId);

      $autorRepoData = new AutorRepoData();
      $autorObj = $autorRepoData->getById($request->autorId);

      $areaCarpeta = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($areaObj->area)));

      // Almacenar el archivo
      // storage/app/public/areas
      $archivo = $request->file('archivoPDF');
      $archivoNombreOriginal = $archivo->getClientOriginalName();
      Log::debug("archivoNombreOriginal -> $archivoNombreOriginal");
      $extension = strtolower($archivo->getClientOriginalExtension());
      $nombreTituloTratado = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($request->titulo)));
      $archivoNombre = $nombreTituloTratado . "." . $extension;
      $rutaArchivo = $archivo->storeAs($areaCarpeta, $archivoNombre, 'public');

      // Crear el libro
      Libro::create([
        'cod_barras' => $request->codigoBarras,
        'autor_id' => $request->autorId,
        'autor' => $autorObj->autor,
        'editorial_id' => $request->editorialId,
        'editorial' => $editorialObj->editorial,
        'area_id' => $request->areaId,
        'area' => $areaObj->area,
        'clave' => null,
        'isbn' => null,
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion ?? null,
        'anio' => $request->anio,
        'status' => $request->status ?? 'activo',
        'registro_autor_id' => auth()->id() ?? null,
        'archivo_ruta' => $rutaArchivo,
        'archivo_tamanio' => $archivo->getSize(),
        'archivo_nombre' => $archivoNombre,
        'archivo_nombre_original' => $archivoNombreOriginal,
        'archivo_mime_type' => $archivo->getClientMimeType(),
        'registro_fecha' => now(), // Laravel lo almacena automáticamente
      ]);
      DB::commit();
      return redirect()->route('libro.alta')->with('success', 'Libro guardado correctamente.');
    } catch (\Throwable $th) {
      DB::rollBack();
      Log::error('Ocurrió un erro al agregar libro');
      Log::error($th->getMessage());
      // Redirecciona de nuevo con mensaje de error
      // return redirect()
      //     ->back()
      //     ->with('error', 'Ocurrió un error al guardar el libro. Por favor intenta más tarde.');
      Log::error('ante del redirect de error');
      return redirect()->route('libro.alta')->with('error', 'Ocurrió un error al guardar el libro. Por favor intenta más tarde.');
      // Inertia::render('LibroAlta', [
      //   'error' => 'Ocurrió un error al guardar el libro. Por favor intenta más tarde.',
      //   'autores' => [],
      //   'editoriales' => [],
      //   'areas' => [],
      // ]);
    }
  }

  public function listarLibrosPorCategoria(Request $request)
  {
    $query = $request->input('busqueda');
    $areaId = $request->input('areaId');

    $libros = Libro::query();

    if (!empty($query)) {
      $libros->where(function ($q) use ($query) {
        $q->where('titulo', 'like', "%{$query}%")
          ->orWhere('autor', 'like', "%{$query}%")
          ->orWhere('editorial', 'like', "%{$query}%");
      });
    }

    if (!empty($areaId)) {
      $libros->where('area_id', $areaId);
    }

    $libros = $libros->get();

    $agrupados = $libros->groupBy('area')->map(function ($librosPorArea, $area) {
      // Mapea cada libro dentro de este grupo para añadir la URL completa
      $librosConUrl = $librosPorArea->map(function ($libro) {
        return [
          'id' => $libro->id,
          'titulo' => $libro->titulo,
          'autor' => $libro->autor,
          'editorial' => $libro->editorial,
          'anio' => $libro->anio,
          'descripcion' => $libro->descripcion,
          'area' => $libro->area, // Asegúrate de incluir todas las propiedades que necesitas
          // ¡Aquí es donde aplicamos Storage::url()!
          'archivo_url' => Storage::url($libro->archivo_ruta),
          // Si aún necesitas la ruta original por alguna razón, también la puedes pasar
          'archivo_ruta' => $libro->archivo_ruta,
        ];
      })->values(); // Usa values() para resetear las claves del array

      return [
        'area' => $area,
        'libros' => $librosConUrl, // Ahora 'libros' contiene los objetos con 'archivo_url'
      ];
    })->values(); // Usa values() aquí también para resetear las claves si no quieres las áreas como claves

    $areaRepo = new AreaRepoData();
    $areas = $areaRepo->get([]);

    return Inertia::render('LibrosPorCategoria', [
      'query' => $query,
      'areaId' => $areaId ?? null,
      'agrupados' => $agrupados,
      'areas' => $areas,
    ]);
  }

  public function descargar(Request $request, $id)
  {
    $matricula = $request->query('matricula');

    if (!$matricula) {
      abort(400, 'Matrícula requerida');
    }

    $alRepo = new AlumnoRepoData();
    $alumnoEncontrado = $alRepo->buscarPorMatricula($matricula);
    if (empty($alumnoEncontrado) || $alumnoEncontrado === null) {
      abort(400, 'Alumno no encontrado, no puede descargar el libro.');
    }

    $libro = Libro::findOrFail($id);

    // Asegúrate de que la ruta esté protegida y sea válida
    $ruta = storage_path("app/public/{$libro->archivo_ruta}");

    if (!file_exists($ruta)) {
      abort(404, 'Archivo no encontrado');
    }

    return response()->download($ruta, "{$libro->titulo}.pdf");
  }
}
