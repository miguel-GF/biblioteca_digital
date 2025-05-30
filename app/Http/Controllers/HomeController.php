<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categorias = Categoria::select('id', 'nombre')->where('destacada', true)->get();

        return Inertia::render('Home', [
            'categorias' => $categorias
        ]);
    }
}
