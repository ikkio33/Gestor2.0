@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Detalles del Usuario</h2>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $usuario->nombre }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $usuario->email }}</p>
            <p class="card-text"><strong>Rol:</strong> {{ ucfirst($usuario->rol) }}</p>
            <p class="card-text"><strong>Fecha de creación:</strong> {{ $usuario->created_at->format('d/m/Y H:i') }}</p>
            <p class="card-text"><strong>Última actualización:</strong> {{ $usuario->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('Admin.usuarios.index') }}" class="btn btn-secondary mt-3">Volver</a>
</div>
@endsection
