@extends('layouts.app')

@section('content')
<div class="container-fluid fullscreen-container">

    <!-- Zona de filtros y resumen -->
    <div class="filtros-resumen">
        <h1 class="mt-3 mb-2">Estadísticas de Turnos</h1>

        <!-- Filtros -->
        <form id="formFiltros" class="mb-3">
            <div class="row g-3">
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Funcionarios</label>
                    <div class="form-control overflow-auto" style="max-height: 150px;">
                        @foreach ($funcionarios as $funcionario)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="usuarios_id[]" id="usuario_{{ $funcionario->id }}"
                                value="{{ $funcionario->id }}"
                                {{ (is_array(request('usuarios_id')) && in_array($funcionario->id, request('usuarios_id'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="usuario_{{ $funcionario->id }}">{{ $funcionario->nombre }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Servicios</label>
                    <div class="form-control overflow-auto" style="max-height: 150px;">
                        @foreach ($servicios as $servicio)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                name="servicios_id[]" id="servicio_{{ $servicio->id }}"
                                value="{{ $servicio->id }}"
                                {{ (is_array(request('servicios_id')) && in_array($servicio->id, request('servicios_id'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="servicio_{{ $servicio->id }}">{{ $servicio->nombre }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-2">
                    <label for="hora" class="form-label">Hora</label>
                    <select name="hora" id="hora" class="form-select">
                        <option value="">-- Todas las horas --</option>
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ $i }}" {{ request('hora') == $i ? 'selected' : '' }}>
                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}:00
                            </option>
                            @endfor
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="btnFiltrar" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="btnLimpiar" class="btn btn-secondary w-100">Limpiar</button>
                </div>

                <div class="col-md-3 offset-md-5 d-flex align-items-end justify-content-end">
                    <label for="tipoGrafico" class="form-label me-2">Tipo de gráfico</label>
                    <select id="tipoGrafico" class="form-select w-auto">
                        <option value="bar">Barras</option>
                        <option value="pie">Torta</option>
                        <option value="line">Líneas</option>
                        <option value="doughnut">Rosquilla</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Resumen -->
        <div class="row my-4" id="resumenEstadisticas">
            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total de Turnos</h5>
                        <p class="fs-4" id="totalTurnos">-</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title">Promedio de Espera</h5>
                        <p class="fs-4" id="promedioEspera">-</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow h-100 d-flex align-items-center justify-content-center">
                    <small class="text-muted fst-italic">Seleccione filtros y presione "Filtrar" para mostrar gráficos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Zona de gráficos -->
    <div class="graficos-container">
        <div class="row g-4">
            <!-- Gráfico Funcionarios -->
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body position-relative">
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 expand-btn" data-target="graficoFuncionarios">⤢</button>
                        <h5 class="card-title">Comparación por Funcionarios</h5>
                        <canvas id="graficoFuncionarios"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico Servicios -->
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body position-relative">
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 expand-btn" data-target="graficoServicios">⤢</button>
                        <h5 class="card-title">Comparación por Servicios</h5>
                        <canvas id="graficoServicios"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico Fechas -->
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body position-relative">
                        <button type="button" class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 expand-btn" data-target="graficoFechas">⤢</button>
                        <h5 class="card-title">Comparación por Fecha</h5>
                        <canvas id="graficoFechas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de expansión -->
    <div class="modal fade" id="modalExpandirGrafico" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Vista Ampliada del Gráfico</h5>
                    <button type="button" id="btnDescargarGrafico" class="btn btn-outline-primary btn-sm me-2">
                        Descargar PNG
                    </button>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center" style="overflow-x: auto;">
                    <canvas id="canvasExpandido" style="min-width: 800px; height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.getElementById('btnFiltrar').addEventListener('click', function() {
        const form = document.getElementById('formFiltros');
        const params = new URLSearchParams(new FormData(form)).toString();

        fetch("{{ route('Admin.estadisticas.ajax') }}?" + params, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    actualizarResumen(data);
                    actualizarGraficos(data);
                } else {
                    alert('Error al obtener estadísticas');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error en la conexión');
            });
    });

    function limpiarResumenYGraficos() {
        document.getElementById('totalTurnos').textContent = '-';
        document.getElementById('promedioEspera').textContent = '-';
        if (window.graficoFuncionarios) window.graficoFuncionarios.destroy();
        if (window.graficoServicios) window.graficoServicios.destroy();
        if (window.graficoFechas) window.graficoFechas.destroy();
    }

    // Expansión
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const originalId = this.dataset.target;
            const originalCanvas = document.getElementById(originalId);
            const chartInstance = Chart.getChart(originalCanvas);

            if (!chartInstance) {
                alert('El gráfico aún no ha sido generado. Por favor, aplique los filtros primero.');
                return;
            }

            const ctxExpandido = document.getElementById('canvasExpandido').getContext('2d');
            if (window.graficoExpandido) window.graficoExpandido.destroy();

            window.graficoExpandido = new Chart(ctxExpandido, {
                type: chartInstance.config.type,
                data: JSON.parse(JSON.stringify(chartInstance.data)),
                options: JSON.parse(JSON.stringify(chartInstance.options))
            });

            // Botón de descarga
            document.getElementById('btnDescargarGrafico').onclick = function() {
                const canvas = document.getElementById('canvasExpandido');
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'grafico-expandido.png';
                link.click();
            };

            new bootstrap.Modal(document.getElementById('modalExpandirGrafico')).show();
        });
    });
</script>
<script src="{{ asset('js/estadisticas.js') }}"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/estadisticas.css') }}">
@endpush