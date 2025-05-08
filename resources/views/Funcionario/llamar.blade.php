@extends('layouts.funcionario')

@section('title', 'Llamado de Turnos')

@section('content')
<div class="container">
    <h2 class="mb-4">📟 Dashboard - {{ $meson->nombre }}</h2>

    {{-- Turno en atención --}}
    @if ($turno_actual)
        <div class="turno-card">
            <h3>🔴 Turno en Atención</h3>
            <p><strong>Turno:</strong> {{ $turno_actual->codigo_turno }}</p>
            <p><strong>Desde:</strong> {{ $turno_actual->created_at->format('H:i') }}</p>
            <form action="{{ route('funcionario.finalizar.turno') }}" method="POST">
                @csrf
                <input type="hidden" name="turno_id" value="{{ $turno_actual->id }}">
                <button type="submit" class="btn btn-danger turno-button">✅ Finalizar turno</button>
            </form>
        </div>
    @else
        <div class="alert alert-warning">No hay turno en atención actualmente.</div>
    @endif

    {{-- Llamar siguiente turno --}}
    <div class="turno-card">
        <h3>📢 Llamar siguiente turno</h3>
        @if ($turnos_espera->isNotEmpty())
            <form action="{{ route('funcionario.llamar.siguiente') }}" method="POST">
                @csrf
                <input type="hidden" name="turno_id" value="{{ $turnos_espera->first()->id }}">
                <button type="submit" class="btn btn-primary turno-button">
                    📣 Llamar turno {{ $turnos_espera->first()->codigo_turno }}
                </button>
            </form>
        @else
            <div class="alert alert-info">No hay turnos pendientes en este momento.</div>
        @endif
    </div>

    {{-- Turnos pendientes --}}
    <div class="turno-card">
        <h3>⏳ Turnos Pendientes</h3>
        @if ($turnos_espera->count() > 1)
            <ul class="turno-list">
                @foreach ($turnos_espera->slice(1) as $turno)
                    <li>
                        <div>
                            <strong>{{ $turno->codigo_turno }}</strong> — {{ $turno->minutos_espera }} min
                            <br><small>{{ $turno->created_at->format('H:i') }}</small>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">No hay más turnos en espera.</p>
        @endif
    </div>

    {{-- Turnos atendidos --}}
    <div class="turno-card">
        <h3>✅ Turnos Atendidos Hoy</h3>
        @if ($turnos_atendidos->isNotEmpty())
            <ul class="turno-list">
                @foreach ($turnos_atendidos as $turno)
                    <li>
                        <div>
                            <strong>{{ $turno->codigo_turno }}</strong><br>
                            <small>Finalizado: {{ $turno->updated_at->format('H:i') }}</small>
                        </div>
                        <span class="badge bg-success">{{ $turno->minutos_atencion }} min</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">Aún no se han atendido turnos hoy.</p>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Refresca la página cada 10 segundos para mantener actualizado el estado
    setInterval(() => location.reload(), 10000);
</script>
@endsection
