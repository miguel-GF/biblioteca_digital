<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Repos\Data\AreaRepoData;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $areaRepo = new AreaRepoData();
        $areas = $areaRepo->get(['destacada' => 'si']);

        $sugeridos = Libro::sugeridos()
            ->limit(10)
            ->get()
            ->map(fn ($libro) => $libro->resumenVista());

        $totalSugeridos = Libro::where('sugerido', true)->count();

        return Inertia::render('Home', [
            'areas' => $areas,
            'sugeridos' => $sugeridos,
            'totalSugeridos' => $totalSugeridos,
        ]);
    }
}
