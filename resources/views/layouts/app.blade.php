<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestor de Filas</title>

    <!-- Bootstrap y estilos globales -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @stack('styles') {{-- Permite inyectar CSS adicional desde las vistas --}}
</head>

<body style="height: 100vh; overflow: hidden;">
    <div class="d-flex h-100" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header text-center py-4">
                <div class="logo-container mx-auto">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
                </div>
                <h4 class="text-white mt-2">Gestor de Filas</h4>
            </div>
            <ul class="nav flex-column px-3">
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('Admin.usuarios.index') }}">
                        <i class="bi bi-people me-2"></i>Usuarios
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('Admin.mesones.index') }}">
                        <i class="bi bi-layout-three-columns me-2"></i>Mesones
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('Admin.organizador.index') }}">
                        <i class="bi bi-book me-2"></i>Servicios
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('Admin.asignaciones.index') }}">
                        <i class="bi bi-person-badge me-2"></i>Asignar Funcionario a Mesón
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('Admin.estadisticas.index') }}">
                        <i class="bi bi-bar-chart me-2"></i>Estadísticas
                    </a>
                </li>
                @auth
                <li class="nav-item mt-4">
                    <form action="{{ route('logout') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                    </form>
                </li>
                @endauth
            </ul>
        </div>

        <!-- Botón toggle del sidebar -->
        <button id="toggleSidebar" class="toggle-btn">
            <i class="bi bi-list"></i>
        </button>

        <!-- Contenido principal -->
        <div class="content flex-grow-1 overflow-auto" style="height: 100vh;">
            <div class="p-3">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/layout.js') }}"></script>
    @stack('scripts') {{-- Permite inyectar JS adicional desde las vistas --}}
</body>
</html>
