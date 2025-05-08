@extends('layouts.funcionario')

@section('titulo', 'Llamar Turnos')

@section('contenido')
    <h2>Turnos en Atención</h2>

    @if($turnoActual)
        <p><strong>En atención:</strong> {{ $turnoActual->codigo }}</p>
    @else
        <p>No hay turno en atención.</p>
    @endif

    @if($siguienteTurno)
        <p><strong>Siguiente turno:</strong> {{ $siguienteTurno->codigo }}</p>
        <form action="{{ route('funcionario.turno.llamar') }}" method="POST">
            @csrf
            <input type="hidden" name="turno_id" value="{{ $siguienteTurno->id }}">
            <button type="submit">Llamar Siguiente</button>
        </form>
    @else
        <p>No hay turnos en espera.</p>
    @endif
@endsection
