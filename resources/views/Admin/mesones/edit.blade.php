@extends('layouts.app') 

@section('content')
<div class="container">
    <h2>Editar Mesón</h2>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('Admin.mesones.update', $meson->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Mesón</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $meson->nombre) }}" required>
        </div>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="disponible" id="disponible" value="1" {{ $meson->disponible ? 'checked' : '' }}>
            <label class="form-check-label" for="disponible">Disponible</label>
        </div>


        <div class="mb-3">
            <label class="form-label">Servicios que atiende</label>
            <div class="row">
                @foreach ($servicios as $servicio)
                <div class="col-md-4">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="servicios[]"
                            value="{{ $servicio->id }}"
                            class="form-check-input"
                            id="servicio_{{ $servicio->id }}"
                            {{ in_array($servicio->id, $meson->servicios->pluck('id')->toArray()) ? 'checked' : '' }}>
                        <label class="form-check-label" for="servicio_{{ $servicio->id }}">
                            {{ $servicio->nombre }} ({{ $servicio->letra }})
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        <a href="{{ route('Admin.mesones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection