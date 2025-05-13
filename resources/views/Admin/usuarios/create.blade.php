@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear nuevo usuario</h1>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Ups!!!</strong> Tenemos problemas con tus datos <br><br>
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('Admin.usuarios.store')}}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>       
        </div>

        <div>
            <label for="email">Correo</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required> 
        </div>

        <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" class="form-control" required>
                <option value="administrador">Administrador</option>
                <option value="funcionario">Funcionario</option>
                <option value="soporte">Soporte</option>
            </select>
        </div>

        <button type="submit" class="btn btn-guardar">Guardar Usuario</button>
        <a href="{{ route('Admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
    </form>
    

</div>
@endsection
