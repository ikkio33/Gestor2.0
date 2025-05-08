<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nombre', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return match ($user->rol) {
                'administrador' => redirect()->route('Admin.usuarios.index'),
                'funcionario' => redirect()->route('funcionario.dashboard'),
                'soporte' => redirect()->route('soporte.index'),
                default => redirect('/'),
            };
        }

        return back()->withErrors(['nombre' => 'Credenciales incorrectas.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
