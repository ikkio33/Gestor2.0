@extends('layouts.seleccionar')

@section('title', 'Seleccione Servicio')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/servicios.css') }}">
<script src="{{ asset('js/servicios.js') }}"></script>
@endsection

@section('content')
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h2 class="text-primary">Seleccione un Servicio</h2>
        <p class="text-muted">RUT: {{ $rut ?? 'No disponible' }}</p>
    </div>

    @if(isset($servicios) && $servicios->count() <= 6)
        <div class="row">
            @foreach($servicios as $servicio)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <strong>{{ $servicio->nombre }} ({{ $servicio->letra }})</strong>
                        </div>
                        <div class="card-body">
                            @if(!empty($materiasPorServicio[$servicio->id]))
                                <button type="button" class="btn btn-outline-primary w-100 btn-service" data-service-id="{{ $servicio->id }}">
                                    Mostrar Materias
                                </button>
                                <div id="materias-{{ $servicio->id }}" class="materias-container d-none mt-3">
                                    @foreach($materiasPorServicio[$servicio->id] as $materia)
                                        <form method="POST" action="{{ route('totem.confirmar') }}" class="mb-2">
                                            @csrf
                                            <input type="hidden" name="rut" value="{{ $rut }}">
                                            <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                            <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                            <button type="submit" class="btn btn-outline-primary w-100">
                                                {{ $materia->nombre }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @else
                                <form method="POST" action="{{ route('totem.confirmar') }}">
                                    @csrf
                                    <input type="hidden" name="rut" value="{{ $rut }}">
                                    <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        Seleccionar Servicio
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($servicios->count() > 8)
        @php
            $chunks = $servicios->chunk(ceil($servicios->count() / 2));
        @endphp
        <div class="row">
            @foreach($chunks as $colIndex => $colServicios)
                <div class="col-md-6">
                    <div class="accordion" id="accordionCol{{ $colIndex }}">
                        @foreach($colServicios as $i => $servicio)
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="heading{{ $colIndex }}-{{ $i }}">
                                    <button class="accordion-button collapsed fs-5" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $colIndex }}-{{ $i }}"
                                            aria-expanded="false"
                                            aria-controls="collapse{{ $colIndex }}-{{ $i }}">
                                        {{ $servicio->nombre }} ({{ $servicio->letra }})
                                    </button>
                                </h2>
                                <div id="collapse{{ $colIndex }}-{{ $i }}" class="accordion-collapse collapse"
                                     aria-labelledby="heading{{ $colIndex }}-{{ $i }}"
                                     data-bs-parent="#accordionCol{{ $colIndex }}">
                                    <div class="accordion-body">
                                        @if(!empty($materiasPorServicio[$servicio->id]))
                                            @foreach($materiasPorServicio[$servicio->id] as $materia)
                                                <form method="POST" action="{{ route('totem.confirmar') }}" class="mb-2">
                                                    @csrf
                                                    <input type="hidden" name="rut" value="{{ $rut }}">
                                                    <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                                    <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                                    <button type="submit" class="btn btn-outline-primary w-100">
                                                        {{ $materia->nombre }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        @else
                                            <form method="POST" action="{{ route('totem.confirmar') }}">
                                                @csrf
                                                <input type="hidden" name="rut" value="{{ $rut }}">
                                                <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                                <button type="submit" class="btn btn-outline-secondary w-100">
                                                    Seleccionar Servicio
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="accordion" id="accordionServicios">
            @foreach($servicios as $i => $servicio)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $i }}">
                        <button class="accordion-button collapsed fs-5" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $i }}"
                                aria-expanded="false"
                                aria-controls="collapse{{ $i }}">
                            {{ $servicio->nombre }} ({{ $servicio->letra }})
                        </button>
                    </h2>
                    <div id="collapse{{ $i }}" class="accordion-collapse collapse"
                         aria-labelledby="heading{{ $i }}"
                         data-bs-parent="#accordionServicios">
                        <div class="accordion-body">
                            @if(!empty($materiasPorServicio[$servicio->id]))
                                @foreach($materiasPorServicio[$servicio->id] as $materia)
                                    <form method="POST" action="{{ route('totem.confirmar') }}" class="mb-2">
                                        @csrf
                                        <input type="hidden" name="rut" value="{{ $rut }}">
                                        <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                        <input type="hidden" name="materia_id" value="{{ $materia->id }}">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            {{ $materia->nombre }}
                                        </button>
                                    </form>
                                @endforeach
                            @else
                                <form method="POST" action="{{ route('totem.confirmar') }}">
                                    @csrf
                                    <input type="hidden" name="rut" value="{{ $rut }}">
                                    <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        Seleccionar Servicio
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
