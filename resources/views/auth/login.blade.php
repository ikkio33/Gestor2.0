<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="container">
        <div class="sidebar">

            <div class="logo-container">
                <img src="{{ asset('img/logo.png') }}" alt="Logo de Gesnot" class="logo">
            </div>
            <h1>Iniciar Sesión</h1>

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>¡Error!</strong> Las credenciales proporcionadas son incorrectas.
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="nombre">Usuario:</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
        </div>
        <div class="content-bg"></div>
    </div>
</body>

</html>
