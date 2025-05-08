@extends('layouts.app')

@section('content')
<div class="mt-4">
    <h2 class="mb-4">Administrar Mesones</h2>
    <a href="{{ route('Admin.mesones.create') }}" class="btn btn-primary mb-3">+ Crear nuevo mesón</a>

    <form class="row g-3 mb-4" method="GET" action="{{ route('Admin.mesones.index') }}">
        <div class="col-md-3">
            <select name="disponible" class="form-select">
                <option value="">-- Disponibilidad --</option>
                <option value="1" {{ request('disponible') === '1' ? 'selected' : '' }}>Disponible</option>
                <option value="0" {{ request('disponible') === '0' ? 'selected' : '' }}>No disponible</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="servicio" class="form-control" placeholder="Buscar servicio..." value="{{ request('servicio') }}">
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-primary w-100" type="submit">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('Admin.mesones.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Disponible</th>
                    <th>Servicios Asociados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mesones as $meson)
                <tr>
                    <td>{{ $meson->nombre }}</td>
                    <td>{{ $meson->estado }}</td>
                    <td>
                        <span class="badge bg-{{ $meson->disponible ? 'success' : 'danger' }}">
                            {{ $meson->disponible ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td>
                        @if ($meson->servicios)
                        @foreach (explode(',', $meson->servicios) as $servicio)
                        <span class="badge bg-info text-dark">{{ trim($servicio) }}</span>
                        @endforeach
                        @else
                        <span class="badge bg-warning text-dark">Sin servicios</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('Admin.mesones.edit', $meson) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('Admin.mesones.destroy', $meson->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este mesón?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5">No hay mesones registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $mesones->links() }}
</div>
@endsection