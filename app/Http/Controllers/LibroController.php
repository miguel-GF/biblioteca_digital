<?php

namespace App\Http\Controllers;

use App\Repos\Data\AreaRepoData;
use App\Repos\Data\AutorRepoData;
use App\Repos\Data\EditorialRepoData;
use Illuminate\Http\Request;
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
}
