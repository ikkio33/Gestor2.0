@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Estadísticas</h1>

        <!-- Formulario de filtros -->
        <form method="GET" action="{{ route('Admin.estadisticas.index') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="mes" class="form-select">
                        <option value="">-- Filtrar por mes --</option>
                        @foreach ($meses as $m)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="dia" class="form-select">
                        <option value="">-- Filtrar por día --</option>
                        @foreach ($dias as $d)
                            <option value="{{ $d }}" {{ $dia == $d ? 'selected' : '' }}>
                                Día {{ $d }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('Admin.estadisticas.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                </div>
            </div>
        </form>

        <!-- Estadísticas -->
        <div class="row">
            <!-- Total turnos -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total de Turnos</h5>
                        <p class="card-text fs-4">{{ $totalTurnos }}</p>
                    </div>
                </div>
            </div>

            <!-- Turnos por servicio -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Turnos por Servicio</h5>
                        <ul class="list-group">
                            @forelse ($turnosPorServicio as $servicioInfo)
                                <li class="list-group-item">
                                    {{ $servicioInfo['servicio']->nombre ?? 'Servicio desconocido' }}:
                                    {{ $servicioInfo['total'] }} turnos
                                </li>
                            @empty
                                <li class="list-group-item">No hay datos disponibles.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tiempo promedio de espera -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Tiempo de Espera Promedio</h5>
                        <p class="card-text fs-4">
                            {{ $promedioEspera ? number_format($promedioEspera, 2) . ' minutos' : 'No disponible' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
