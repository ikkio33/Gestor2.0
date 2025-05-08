@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Crear Mesón</h2>

    <form action="{{ route('Admin.mesones.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado:</label>
            <input type="text" name="estado" id="estado" class="form-control" value="disponible" required>
        </div>

        <div class="form-check form-switch mb-4">
            <input type="hidden" name="disponible" value="0">
            <input class="form-check-input" type="checkbox" name="disponible" id="disponible" value="1" checked>
            <label class="form-check-label" for="disponible">Disponible</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Servicios Asociados:</label><br>
            @foreach (\App\Models\Servicio::all() as $servicio)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="servicios[]" value="{{ $servicio->id }}" id="serv_{{ $servicio->id }}">
                <label class="form-check-label" for="serv_{{ $servicio->id }}">
                    {{ htmlspecialchars($servicio->nombre) }}
                </label>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        
        <a href="{{ route('Admin.mesones.index') }}" class="btn btn-secondary">Cancelar</a>
        
    </form>
</div>
@endsection