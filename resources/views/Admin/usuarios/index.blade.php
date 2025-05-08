@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Usuarios</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('Admin.usuarios.create') }}" class="btn btn-primary mb-3">Nuevo Usuario</a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ ucfirst($usuario->rol) }}</td>
                    <td>
                        <a href="{{ route('Admin.usuarios.show', $usuario->id) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('Admin.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('Admin.usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
