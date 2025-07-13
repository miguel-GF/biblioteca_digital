<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibroController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

Route::get('/home', [HomeController::class, 'index']);
Route::get('/libros', [LibroController::class, 'listarLibrosPorCategoria']);
Route::get('/validar-matricula', [LibroController::class, 'validarMatricula']);
Route::get('/descargar-libro/{id}', [LibroController::class, 'descargar']);
Route::get('/preview-libro/{archivo}', function ($archivo) {
  $path = storage_path("app/public/{$archivo}");
  if (!file_exists($path)) {
    abort(404, 'Archivo no encontrado');
  }
  return Response::file($path, [
    'Content-Type' => 'application/pdf',
  ]);
})->where('archivo', '.*'); // 👈 Esto permite rutas con subdirectorios;

// Route::get('/home', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
  Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
  })->name('dashboard');
  Route::get('/dashboard/libro/alta', [LibroController::class, 'getDatosLibroAlta'])->name('libro.alta');
  Route::post('/dashboard/libro/guardar', [LibroController::class, 'guardar'])->name('libro.guardar');
});

Route::fallback(function () {
  if (Auth::check()) {
    return redirect('/dashboard');
  }
  return redirect('/home');
});
