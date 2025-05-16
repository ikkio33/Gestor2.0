@extends('layouts.funcionario')

@section('content')
<div class="container">
    <h2 class="mb-4">Gestión de Mesones</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3>Asignar Mesón</h3>
        </div>
        <div class="card-body">
            @if($mesonesDisponibles->isEmpty())
                <div class="alert alert-warning">No hay mesones disponibles en este momento.</div>
            @else
                <form action="{{ route('funcionario.meson.asignar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="meson" class="form-label">Mesones disponibles</label>
                        <select name="meson_id" id="meson" class="form-select" required>
                            @foreach($mesonesDisponibles as $meson)
                                <option value="{{ $meson->id }}">{{ $meson->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Asignarme este mesón</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-white">
            <h3>Mesones en Uso</h3>
        </div>
        <div class="card-body">
            @if($mesonesOcupados->isEmpty())
                <div class="alert alert-info">No hay mesones ocupados actualmente.</div>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mesón</th>
                            <th>Usuario asignado</th>
                            <th>Servicios en atención</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mesonesOcupados as $meson)
                            <tr>
                                <td>{{ $meson->nombre }}</td>
                                <td>{{ $meson->usuario?->nombre ?? 'Sin asignar' }}</td>
                                <td>
                                    <ul>
                                        @foreach($meson->usuario?->turnos ?? [] as $turno)
                                            <li>{{ $turno->servicio->nombre }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @if($meson->usuario && $meson->usuario->id === Auth::id())
                                        <form action="{{ route('funcionario.meson.liberar') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="meson_id" value="{{ $meson->id }}">
                                            <button type="submit" class="btn btn-danger">Liberar mesón</button>
                                        </form>
                                    @else
                                        <span class="text-muted">No autorizado</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="card">
        <div class="card-header bg-info text-white">
            <h3>Mesones en Uso</h3>
        </div>
        <div class="card-body">
            @if($mesonesOcupados->isEmpty())
                <div class="alert alert-info">No hay mesones ocupados actualmente.</div>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mesón</th>
                            <th>Usuario asignado</th>
                            <th>Servicios en atención</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mesonesOcupados as $meson)
                            <tr>
                                <td>{{ $meson->nombre }}</td>
                                <td>{{ $meson->usuario?->nombre ?? 'Sin asignar' }}</td>
                                <td>
                                    <ul>
                                        @foreach($meson->usuario?->turnos ?? [] as $turno)
                                            <li>{{ $turno->servicio->nombre }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @if($meson->usuario && $meson->usuario->id === Auth::id())
                                        <form action="{{ route('funcionario.meson.liberar') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="meson_id" value="{{ $meson->id }}">
                                            <button type="submit" class="btn btn-danger">Liberar mesón</button>
                                        </form>
                                    @else
                                        <span class="text-muted">No autorizado</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
