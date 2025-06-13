@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Asignar Funcionario a Mesón</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('Admin.asignaciones.store') }}" method="POST" class="mb-4">
        @csrf

        <div class="mb-3">
            <label for="usuario_id" class="form-label">Funcionario</label>
            <select name="usuario_id" id="usuario_id" class="form-select" required>
                <option value="" disabled selected>Seleccione un funcionario</option>
                @foreach($funcionariosSinMeson as $funcionario)
                    <option value="{{ $funcionario->id }}">{{ $funcionario->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="meson_id" class="form-label">Mesón</label>
            <select name="meson_id" id="meson_id" class="form-select" required>
                <option value="" disabled selected>Seleccione un mesón</option>
                @foreach($mesones as $meson)
                    <option value="{{ $meson->id }}">{{ $meson->nombre }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Asignar</button>
    </form>

    <h3>Mesones Asignados</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Funcionario</th>
                <th>Mesón asignado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($funcionariosConMeson as $funcionario)
                <tr>
                    <td>{{ $funcionario->nombre }}</td>
                    <<td>{{ optional($funcionario->meson)->nombre ?? 'Sin mesón' }}</td>

                    <td>
                        <form action="{{ route('Admin.asignaciones.liberar') }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Seguro que quieres liberar este mesón?');">
                            @csrf
                            <input type="hidden" name="usuario_id" value="{{ $funcionario->id }}">
                            <button type="submit" class="btn btn-sm btn-danger">Liberar Mesón</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
