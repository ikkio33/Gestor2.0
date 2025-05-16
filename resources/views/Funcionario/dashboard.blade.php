@extends('layouts.funcionario')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 text-center">Gestión de Mesones</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Gestión de Mesones --}}
    <div class="container-fluid mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <span>Gestión de Mesones</span>
                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#gestionMesones" aria-expanded="true" aria-controls="gestionMesones">
                    <i class="bi bi-chevron-up" id="toggleIcon"></i>
                </button>
            </div>

            <div class="collapse show" id="gestionMesones">
                <div class="card-body" style="overflow-x: hidden; overflow-y: auto; max-height: 60vh;">
                    <div class="row gy-4">
                        {{-- Asignar Mesón --}}
                        <div class="col-md-6 border-end">
                            <h5>Asignar Mesón</h5>
                            @if($mesones_disponibles->isEmpty())
                                <div class="alert alert-warning">No hay mesones disponibles en este momento.</div>
                            @else
                                <form action="{{ route('funcionario.dashboard') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="meson" class="form-label">Mesones disponibles</label>
                                        <select name="meson_id" id="meson" class="form-select" required>
                                            @foreach($mesones_disponibles as $meson)
                                                <option value="{{ $meson->id }}">{{ $meson->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Asignarme este mesón</button>
                                </form>
                            @endif
                        </div>

                        {{-- Mesones en Uso --}}
                        <div class="col-md-6">
                            <h5>Mesones en Uso</h5>
                            @if($mesones_ocupados->isEmpty())
                                <div class="alert alert-info">No hay mesones ocupados actualmente.</div>
                            @else
                                <div class="mesones-en-uso-container">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mesón</th>
                                                <th>Usuario</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mesones_ocupados as $meson)
                                            <tr>
                                                <td>{{ $meson->nombre }}</td>
                                                <td>{{ $meson->usuario?->nombre ?? 'Sin asignar' }}</td>
                                                <td>
                                                    @if($meson->usuario && $meson->usuario->id === Auth::id())
                                                    <form action="{{ route('funcionario.meson.liberar') }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="meson_id" value="{{ $meson->id }}">
                                                        <button type="submit" class="btn btn-danger btn-sm">Liberar</button>
                                                    </form>
                                                    @else
                                                    <span class="text-muted">No autorizado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila con carrusel y turno actual en dos columnas --}}
<div class="row mb-4">
    {{-- Turnos en Espera (Carrusel) --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Turnos en Espera</h5>
            </div>
            <div class="card-body d-flex justify-content-center p-4" style="max-height: 320px;">
                <div class="w-100" style="max-width: 360px;">
                    @php
                    $chunks = $turnos_espera->chunk(1);
                    $totalSlides = $chunks->count();
                    @endphp

                    @if($chunks->isEmpty())
                    <div class="alert alert-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                        <i class="bi bi-inbox fs-1 me-3"></i> No hay turnos en espera.
                    </div>
                    @else
                    <div id="turnosCarousel" class="carousel carousel-fade slide" data-bs-ride="carousel" data-bs-interval="false">
                        <div class="carousel-inner">
                            @foreach($chunks as $index => $chunk)
                            <div class="carousel-item @if($index === 0) active @endif">
                                @foreach($chunk as $turno)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5>Turno {{ $turno->codigo }}</h5>
                                        <p>Servicio: {{ $turno->servicio->nombre }}</p>
                                        <p>Tiempo en espera:
                                            <span class="tiempo-espera" data-creado="{{ $turno->created_at->toIso8601String() }}">00:00</span>
                                        </p>
                                        <form action="{{ route('funcionario.llamar.siguiente') }}" method="POST" @if($turno_actual) style="pointer-events: none; opacity: 0.5;" @endif>
                                            @csrf
                                            <input type="hidden" name="turno_id" value="{{ $turno->id }}">
                                            <button class="btn btn-success btn-sm w-100" type="submit" @if($turno_actual) disabled @endif>Llamar</button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>

                        @if($totalSlides > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#turnosCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#turnosCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Turno en Atención --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-info text-white">Turno en Atención</div>
            <div class="card-body">
                @if($turno_actual)
                <h4>Atendiendo Turno: {{ $turno_actual->codigo }}</h4>
                <p>Servicio: {{ $turno_actual->servicio->nombre }}</p>
                <form action="{{ route('funcionario.llamar.siguiente') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="turno_id" value="{{ $turno_actual->id }}">
                    <button class="btn btn-secondary btn-sm">Re-Llamar</button>
                </form>
                <form action="{{ route('funcionario.finalizar.turno') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="turno_id" value="{{ $turno_actual->id }}">
                    <button class="btn btn-danger btn-sm">Finalizar Atención</button>
                </form>
                @else
                <p>No hay turno en atención.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function actualizarTiempos() {
        const elementos = document.querySelectorAll('.tiempo-espera');
        const ahora = new Date();

        elementos.forEach(el => {
            const creado = new Date(el.dataset.creado);
            const diffMs = ahora - creado;

            if (diffMs >= 0) {
                const totalSeg = Math.floor(diffMs / 1000);
                const minutos = Math.floor(totalSeg / 60);
                const segundos = totalSeg % 60;

                const minutosFormateados = minutos.toString().padStart(2, '0');
                const segundosFormateados = segundos.toString().padStart(2, '0');

                el.textContent = `${minutosFormateados}:${segundosFormateados}`;
            }
        });
    }

    setInterval(actualizarTiempos, 1000);
    actualizarTiempos();

    // Carrusel Bootstrap
    document.addEventListener('DOMContentLoaded', () => {
        const carouselElement = document.querySelector('#turnosCarousel');
        if (carouselElement) {
            new bootstrap.Carousel(carouselElement, {
                interval: 5000,
                ride: false,
                pause: 'hover',
                wrap: true
            });
        }
    });
</script>
@endpush
