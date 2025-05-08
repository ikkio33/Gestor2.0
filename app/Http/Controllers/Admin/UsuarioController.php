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
            'password' => 'required|string|min:6',
            'rol' => 'required|in:administrador,funcionario,soporte',
        ]);

        $hashedPassword = Hash::make($request->password);

        $usuario = new Usuarios();
        $usuario->nombre = $request->nombre;
        $usuario->email = $request->email;
        $usuario->password = $hashedPassword;  
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
        'password' => 'nullable|string|min:6', 
        'rol' => 'required|in:administrador,funcionario,soporte',
    ]);

    $data = $request->all();

    if (empty($data['password'])) {
        unset($data['password']);
    } else {
        $data['password'] = Hash::make($data['password']);
    }

    $usuario->update($data);

    return redirect()->route('Admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
}


    public function destroy(Usuarios $usuario)
    {
        $usuario->delete();

        return redirect()->route('Admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
