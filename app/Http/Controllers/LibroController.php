<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidadorException;
use App\Http\Requests\LibroGuardarRequest;
use App\Models\Area;
use App\Models\Autor;
use App\Models\Editorial;
use App\Models\Libro;
use App\Repos\Data\AlumnoRepoData;
use App\Repos\Data\AreaRepoData;
use App\Repos\Data\AutorRepoData;
use App\Repos\Data\EditorialRepoData;
use App\Utilerias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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

      // Obtención/Creación de Entidades (Área, Editorial, Autor)
      $areaObj      = $this->findOrCreateEntity(
        $request->areaId,
        $request->areaNombre,
        'Area',
        'area',
        'AreaRepoData'
      );

      $editorialObj = $this->findOrCreateEntity(
        $request->editorialId,
        $request->editorialNombre,
        'Editorial',
        'editorial',
        'EditorialRepoData'
      );

      $autorObj     = $this->findOrCreateEntity(
        $request->autorId,
        $request->autorNombre,
        'Autor',
        'autor',
        'AutorRepoData'
      );

      // --- Lógica del Archivo ---
      $areaCarpeta = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($areaObj->area)));

      $archivo = $request->file('archivoPDF');
      $archivoNombreOriginal = $archivo->getClientOriginalName();
      Log::debug("archivoNombreOriginal -> $archivoNombreOriginal");
      $extension = strtolower($archivo->getClientOriginalExtension());
      $nombreTituloTratado = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($request->titulo)));
      $archivoNombre = $nombreTituloTratado . "." . $extension;
      $rutaArchivo = $archivo->storeAs($areaCarpeta, $archivoNombre, 'public');

      // --- Creación del Libro ---
      Libro::create([
        'titulo'                    => $request->titulo,
        'area_id'                   => $areaObj->id,
        'area'                      => $areaObj->area,
        'registro_autor_id'         => auth()->id() ?? null,
        'archivo_ruta'              => $rutaArchivo,
        'archivo_tamanio'           => $archivo->getSize(),
        'archivo_nombre'            => $archivoNombre,
        'archivo_nombre_original'   => $archivoNombreOriginal,
        'archivo_mime_type'         => $archivo->getClientMimeType(),
        'registro_fecha'            => now(),

        // OPCIONALES
        'cod_barras'    => $request->codigoBarras ?: null,
        'autor_id'      => $autorObj->id ?? null,
        'autor'         => $autorObj->autor ?? null,
        'editorial_id'  => $editorialObj->id ?? null,
        'editorial'     => $editorialObj->editorial ?? null,
        'descripcion'   => $request->descripcion ?: null,
        'anio'          => $request->anio ?: null,
        'status'        => $request->status ?? 'activo',
        'clave'         => null,
        'isbn'          => null,

        // SUGERIDOS
        'sugerido'      => $request->boolean('sugerido'),
        'importancia'   => (int) ($request->input('importancia') ?: 1),
      ]);

      DB::commit();
      return redirect()->route('libro.alta')->with('success', 'Libro guardado correctamente.');
    } catch (ValidadorException $th) {
      // Mantenemos esta, por si la usas en otro lado
      DB::rollBack();
      Log::warning($th->getMessage());
      return redirect()->route('libro.alta')->with('error', $th->getMessage());
    } catch (\Throwable $th) {
      DB::rollBack();
      Log::error('Ocurrió un error al agregar libro');
      Log::error($th->getMessage());
      return redirect()->route('libro.alta')->with('error', 'Error al guardar el libro: ' . $th->getMessage());
    }
  }

  /**
   * Función auxiliar para refactorizar la lógica repetitiva de obtener/crear entidades.
   * * NOTA: Esta función DEBE agregarse como un método privado en tu controlador.
   * Asume que las clases de Modelos (Area, Editorial, Autor) están importadas.
   */
  private function findOrCreateEntity($id, $name, string $modelName, string $columnName, string $repoName)
  {
    $obj = null;
    $modelClass = "\\App\\Models\\{$modelName}"; // Ajusta el namespace si es necesario
    $repoClass = "\\App\Repos\\Data\\{$repoName}"; // Ajusta el namespace si es necesario

    if (!empty($id)) {
      // Opción 1: Obtener por ID (asume que el repo tiene un getById)
      $repoData = new $repoClass();
      $obj = $repoData->getById($id);
    } elseif (!empty($name)) {
      // Opción 2: Encontrar o Crear por Nombre (normalizando el texto)
      $normalizedName = Utilerias::normalizarTexto($name);
      // **IMPORTANTE**: ELIMINAR la validación de wasRecentlyCreated aquí.
      $obj = $modelClass::firstOrCreate([$columnName => $normalizedName]);
    }

    // Opcional: Validar que si se proporcionó un ID, el objeto exista.
    if (!empty($id) && is_null($obj)) {
      throw new ValidadorException("No se encontró la entidad de {$modelName} con el ID proporcionado.");
    }

    return $obj;
  }

  public function guardarDeprecated(LibroGuardarRequest $request)
  {
    try {
      DB::beginTransaction();

      // AREA
      $areaObj = null;
      if ($request->areaId != '') {
        $areaRepoData = new AreaRepoData();
        $areaObj = $areaRepoData->getById($request->areaId);
      } else {
        $areaObj = Area::firstOrCreate(['area' => Utilerias::normalizarTexto($request->areaNombre)]);
        if (!$areaObj->wasRecentlyCreated) {
          throw new ValidadorException('El área ya existe, no se completo el alta del libro.');
        }
      }

      // EDITORIAL
      $editorialObj = null;
      if ($request->editorialId != '') {
        $editorialRepoData = new EditorialRepoData();
        $editorialObj = $editorialRepoData->getById($request->editorialId);
      } elseif (!empty($request->editorialNombre)) {
        $editorialObj = Editorial::firstOrCreate(['editorial' => Utilerias::normalizarTexto($request->editorialNombre)]);
        if (!$editorialObj->wasRecentlyCreated) {
          throw new ValidadorException('La editorial ya existe, no se completo el alta del libro.');
        }
      }

      // AUTOR
      $autorObj = null;
      if ($request->autorId != '') {
        $autorRepoData = new AutorRepoData();
        $autorObj = $autorRepoData->getById($request->autorId);
      } elseif (!empty($request->autorNombre)) {
        $autorObj = Autor::firstOrCreate(['autor' => Utilerias::normalizarTexto($request->autorNombre)]);
        if (!$autorObj->wasRecentlyCreated) {
          throw new ValidadorException('El autor ya existe, no se completo el alta del libro.');
        }
      }

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
        'titulo' => $request->titulo,
        'area_id' => $areaObj->id,
        'area' => $areaObj->area,
        'registro_autor_id' => auth()->id() ?? null,
        'archivo_ruta' => $rutaArchivo,
        'archivo_tamanio' => $archivo->getSize(),
        'archivo_nombre' => $archivoNombre,
        'archivo_nombre_original' => $archivoNombreOriginal,
        'archivo_mime_type' => $archivo->getClientMimeType(),
        'registro_fecha' => now(), // Laravel lo almacena automáticamente
        // OPCIONALES
        'cod_barras' => !empty($request->codigoBarras) ? $request->codigoBarras : null,
        'autor_id' => !empty($autorObj->id) ? $autorObj->id : null,
        'autor' => !empty($autorObj->autor) ? $autorObj->autor : null,
        'editorial_id' => !empty($editorialObj->id) ? $editorialObj->id : null,
        'editorial' => !empty($editorialObj->editorial) ? $editorialObj->editorial : null,
        'descripcion' => !empty($request->descripcion) ? $request->descripcion : null,
        'anio' => !empty($request->anio) ? $request->anio : null,
        'status' => $request->status ?? 'activo',
        'clave' => null,
        'isbn' => null,
      ]);
      DB::commit();
      return redirect()->route('libro.alta')->with('success', 'Libro guardado correctamente.');
    } catch (ValidadorException $th) {
      DB::rollBack();
      Log::warning($th->getMessage());
      return redirect()->route('libro.alta')->with('error', $th->getMessage());
    } catch (\Throwable $th) {
      DB::rollBack();
      Log::error('Ocurrió un error al agregar libro');
      Log::error($th->getMessage());
      return redirect()->route('libro.alta')->with('error', $th->getMessage());
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
      $librosConUrl = $librosPorArea
        ->map(fn ($libro) => $libro->resumenVista())
        ->values(); // Usa values() para resetear las claves del array

      return [
        'area' => $area,
        'libros' => $librosConUrl, // Ahora 'libros' contiene los objetos con 'archivo_url'
      ];
    })->values(); // Usa values() aquí también para resetear las claves si no quieres las áreas como claves

    $areaRepo = new AreaRepoData();
    $areas = $areaRepo->get([]);

    return Inertia::render('LibrosPorCategoria', [
      'query' => $query ?: '',
      'areaId' => $areaId ?: '',
      'agrupados' => $agrupados,
      'areas' => $areas,
    ]);
  }

  public function listarSugeridos()
  {
    $sugeridos = Libro::sugeridos()
      ->get()
      ->map(fn ($libro) => $libro->resumenVista());

    return Inertia::render('LibrosSugeridos', [
      'sugeridos' => $sugeridos,
    ]);
  }

  /**
   * Pantalla administrativa para marcar libros como sugeridos
   * y asignarles una importancia.
   */
  public function adminSugeridos(Request $request)
  {
    $busqueda = $request->input('busqueda');

    $sugeridos = Libro::where('sugerido', true)
      ->orderByDesc('importancia')
      ->orderBy('titulo')
      ->get()
      ->map(fn ($libro) => $libro->resumenVista());

    $libros = Libro::query()
      ->when($busqueda, function ($query) use ($busqueda) {
        $query->where(function ($subQuery) use ($busqueda) {
          $subQuery->where('titulo', 'like', "%{$busqueda}%")
            ->orWhere('autor', 'like', "%{$busqueda}%")
            ->orWhere('editorial', 'like', "%{$busqueda}%");
        });
      })
      ->orderBy('titulo')
      ->paginate(10)
      ->withQueryString()
      ->through(fn ($libro) => $libro->resumenVista());

    return Inertia::render('LibrosSugeridosAdmin', [
      'sugeridos' => $sugeridos,
      'libros' => $libros,
      'busqueda' => $busqueda ?: '',
    ]);
  }

  /**
   * Guarda de una sola vez todos los libros sugeridos:
   * altas, bajas, orden e importancias.
   */
  public function guardarSugeridos(Request $request)
  {
    $validated = $request->validate([
      'sugeridos' => 'present|array',
      'sugeridos.*.id' => 'required|integer|distinct|exists:libros,id',
      'sugeridos.*.importancia' => 'required|integer|min:1|max:100',
    ]);

    DB::transaction(function () use ($validated) {
      $ids = collect($validated['sugeridos'])->pluck('id')->all();

      // Los que ya no vienen en la lista dejan de ser sugeridos.
      Libro::where('sugerido', true)
        ->whereNotIn('id', $ids)
        ->update(['sugerido' => false]);

      // Altas y actualizaciones de importancia.
      foreach ($validated['sugeridos'] as $item) {
        Libro::where('id', $item['id'])->update([
          'sugerido' => true,
          'importancia' => (int) $item['importancia'],
        ]);
      }
    });

    return back()->with('success', 'Sugeridos guardados correctamente.');
  }

  public function validarMatricula(Request $request)
  {
    $matricula = $request->query('matricula');

    if (!$matricula) {
      return response()->json(['message' => 'Matrícula requerida'], 400);
    }

    $alRepo = new AlumnoRepoData();
    $alumno = $alRepo->buscarPorMatricula($matricula);

    if (empty($alumno)) {
      return response()->json(['message' => 'Alumno no encontrado'], 400);
    }

    return response()->json([
      'message' => 'Matrícula válida',
      'id' => $alumno->idalumnos,
      'existe' => true
    ]);
  }

  public function descargar(Request $request, $id)
  {
    $libro = Libro::findOrFail($id);

    // Asegúrate de que la ruta esté protegida y sea válida
    $ruta = storage_path("app/public/{$libro->archivo_ruta}");

    if (!file_exists($ruta)) {
      abort(404, 'Archivo no encontrado');
    }

    return response()->download($ruta, $libro->archivo_nombre);
  }
}
