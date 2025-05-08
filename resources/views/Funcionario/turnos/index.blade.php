@extends('layouts.funcionario')

@section('titulo', 'Seleccionar Mesón')

@section('contenido')
    <h2>Selecciona el Mesón que atenderás</h2>
    <form action="{{ route('funcionario.meson.seleccionar.guardar') }}" method="POST">
        @csrf
        <label for="meson">Mesón:</label>
        <select name="meson_id" id="meson" required>
            @foreach ($mesones as $meson)
                <option value="{{ $meson->id }}">{{ $meson->nombre }}</option>
            @endforeach
        </select>
        <button type="submit">Aceptar</button>
    </form>
@endsection
