<?php

namespace App\Http\Controllers;

// use App\Models\Categoria;
use App\Repos\Data\AreaRepoData;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // $categorias = Categoria::select('id', 'nombre')->where('destacada', true)->get();
        $areaRepo = new AreaRepoData();
        $areas = $areaRepo->get(['destacada' => 'si']);

        return Inertia::render('Home', [
            // 'categorias' => $categorias
            'areas' => $areas
        ]);
    }
}
