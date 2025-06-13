@extends('layouts.funcionario')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Centro de Mando - Funcionario: {{ Auth::user()->nombre }}</h2>

    <div class="row">
        {{-- Columna: Turnos Pendientes --}}
        <div class="col-md-6">
            <section id="turnosPendientesSection" class="card shadow-sm mb-4" style="max-height: 600px; overflow-y: auto;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Turnos Pendientes</h5>
                </div>
                <div class="card-body" id="turnosPendientesList">
                    {{-- Turnos cargados vía AJAX --}}
                </div>
                <div id="mensajePendientesVacio" class="text-center text-muted p-3" style="display: none;">
                    <strong>Bandeja vacía</strong><br>
                    No hay turnos pendientes.
                </div>
            </section>
        </div>

        {{-- Columna: Turno en Atención --}}
        <div class="col-md-6">
            <section id="turnoAtencionSection" class="card shadow-sm mb-4" style="position: relative;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Turno en Atención</h5>
                </div>
                <div class="card-body" id="turnoAtencionContent" style="position: relative;">
                    {{-- Turno cargado vía AJAX --}}

                    {{-- Indicador pequeño para rellamadas --}}
                    <div id="contadorRellamadas" style="
                        position: absolute;
                        bottom: 8px;
                        right: 12px;
                        font-size: 0.75rem;
                        color: #555;
                        opacity: 0.7;
                        user-select: none;
                    ">
                        Rellamado 0 veces
                    </div>

                    {{-- Animación sutil de rellamado --}}
                    <div id="animacionRellamado" style="
                        position: absolute;
                        bottom: 8px;
                        right: 80px;
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        background-color: #28a745;
                        opacity: 0;
                        pointer-events: none;
                        transition: opacity 0.3s ease;
                    "></div>
                </div>
                <div id="mensajeAtencionVacio" class="text-center text-muted p-3" style="display: none;">
                    <strong>Bandeja vacía</strong><br>
                    No hay turnos en atención.
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/funcionario/centro-mando.js') }}"></script>
@endpush