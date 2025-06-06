@extends('layouts.funcionario')

@section('content')
<div class="centro-mando-container mt-4">
    <h2>Centro de Mando - Funcionario: {{ Auth::user()->nombre }}</h2>

    {{-- Botones para mostrar/ocultar --}}
    <div class="mb-3">
        <button id="toggleMesonesBtn" class="btn btn-primary me-2">Mostrar/Ocultar Mesones</button>
    </div>

    {{-- Sección Mesones --}}
    <div id="mesonesSection" class="mesones-grid mb-4">
        <!-- Columna: Mesones Disponibles -->
        <section id="mesonesDisponiblesSection" class="mesones-columna">
            <div id="mesonesDisponiblesWrapper" class="mesones-scroll-container">
                <div class="header">
                    <h4>Mesones Disponibles</h4>
                </div>
                <div id="mesonesDisponiblesList" class="mesones-lista">
                    {{-- Aquí se cargarán los mesones disponibles dinámicamente --}}
                </div>
            </div>
        </section>

        <!-- Columna: Mesones Asignados -->
        <section id="mesonesAsignadosSection" class="mesones-columna">
            <div id="mesonesAsignadosWrapper" class="mesones-scroll-container">
                <h4>Mesones Asignados</h4>
                <div id="mesonesAsignadosList" class="mesones-lista">
                    {{-- Aquí se cargarán los mesones asignados dinámicamente --}}
                </div>
            </div>
        </section>
    </div>

    {{-- Sección Turnos Pendientes --}}
    <section id="turnosPendientesSection" class="turnos-section mb-4" style="display:none;">
        <h4>Turnos Pendientes</h4>
        <div id="turnosPendientesList">
            {{-- Aquí cargará la lista de turnos pendientes vía AJAX --}}
        </div>
    </section>

    {{-- Sección Turno en Atención --}}
    <section id="turnoAtencionSection" class="turnos-section" style="display:none;">
        <h4>Turno en Atención</h4>
        <div id="turnoAtencionContent">
            {{-- Aquí cargará el turno en atención vía AJAX --}}
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/funcionario/centro_mando.js') }}"></script>
@endpush