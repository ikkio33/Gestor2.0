@extends('layouts.funcionario')

@section('content')
<div class="welcome-message mb-4">
    <h1>Bienvenido, {{ Auth::user()->nombre }}!</h1>
    <p class="text-muted">Desde aquí podrás gestionar tus tareas del día.</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Seleccionar Mesón</h5>
                <p class="card-text">Selecciona el mesón que vas a atender.</p>
                <a href="{{ route('funcionario.meson.seleccionar') }}" class="btn btn-primary">Seleccionar</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Gestionar Turnos</h5>
                <p class="card-text">Visualiza los turnos y llama a los clientes cuando estén listos.</p>
                <a href="{{ route('funcionario.turnos.index') }}" class="btn btn-secondary">Ver Turnos</a>
            </div>
        </div>
    </div>
</div>
@endsection