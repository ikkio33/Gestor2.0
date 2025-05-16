<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuarios;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Usuarios::where('nombre', $request->nombre)->first();

        if (!$user) {
            return back()->withErrors(['nombre' => 'El nombre de usuario no existe.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.']);
        }

        Auth::login($user);

        return match ($user->rol) {
            'administrador' => redirect()->route('Admin.usuarios.index'),
            'funcionario' => redirect()->route('funcionario.dashboard'),
            'soporte' => redirect()->route('soporte.index'),
            default => redirect('/'),
        };
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
