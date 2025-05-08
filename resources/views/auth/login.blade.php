<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="">
<head>
    <title>Iniciar sesión</title>
</head>
<body>
    <h1>Login</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <div>
            <label for="nombre">Usuario:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
