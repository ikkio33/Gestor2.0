<?php

namespace App\Http\Controllers\Admin;

use App\Models\Usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuarios::all();
        return view('Admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('Admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'rol' => 'required|in:administrador,funcionario,soporte',
        ]);

        $usuario = new Usuarios();
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        // El modelo Usuarios tiene mutator para cifrar el password
        $usuario->password = $request->password;
        $usuario->rol = $request->rol;
        $usuario->save();

        return redirect()->route('Admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(Usuarios $usuario)
    {
        return view('Admin.usuarios.show', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = Usuarios::findOrFail($id);
        return view('Admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuarios::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string',
            'email' => "required|email|unique:usuarios,email,$id",
            'password' => 'nullable|string|min:6|confirmed',
            'rol' => 'required|in:administrador,funcionario,soporte',
        ]);

        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;

        if (!empty($request->password)) {
            // Si envían contraseña nueva, se cifra en el mutator del modelo
            $usuario->password = $request->password;
        }

        $usuario->save();

        return redirect()->route('Admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuarios $usuario)
    {
        $usuario->delete();

        return redirect()->route('Admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
