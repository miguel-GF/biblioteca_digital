<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    public function store(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = Usuario::where('correo', $request->correo)
          ->where('password', $request->password)
          ->where('status', 'Activo')
          ->where('tipo', 'Sistema')
          ->first();

        if (!$usuario) {
            return back()->withErrors(['correo' => 'Credenciales inválidas']);
        }

        // Autenticación manual usando tabla usuarios
        Auth::login($usuario);

        // Actualiza fecha y hora de acceso
        $usuario->update([
            'fechaacceso' => now()->format('Y-m-d'),
            'horaacceso' => now()->format('H:i:s'),
        ]);

        return redirect()->intended('/home');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/home');
    }
}
