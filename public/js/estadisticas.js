let graficoFuncionarios = null;
let graficoServicios = null;
let graficoFechas = null;

// Función para crear o actualizar un gráfico Chart.js
function crearOActualizarGrafico(ctx, tipo, etiquetas, datos, label) {
    if (ctx.chart) {
        ctx.chart.destroy();
    }
    ctx.chart = new Chart(ctx, {
        type: tipo,
        data: {
            labels: etiquetas,
            datasets: [{
                label: label,
                data: datos,
                backgroundColor: generarColores(datos.length, 0.6),
                borderColor: generarColores(datos.length, 1),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: (tipo === 'bar' || tipo === 'line') ? {
                y: { beginAtZero: true }
            } : {}
        }
    });
}

// Genera array de colores RGBA para el gráfico
function generarColores(cantidad, alpha) {
    const baseColores = [
        '255, 99, 132',   // rojo
        '54, 162, 235',   // azul
        '255, 206, 86',   // amarillo
        '75, 192, 192',   // verde agua
        '153, 102, 255',  // morado
        '255, 159, 64',   // naranja
        '199, 199, 199'   // gris
    ];
    let colores = [];
    for (let i = 0; i < cantidad; i++) {
        const c = baseColores[i % baseColores.length];
        colores.push(`rgba(${c}, ${alpha})`);
    }
    return colores;
}

function formatearSegundos(segundos) {
    if (!segundos || isNaN(segundos)) return '00:00:00';
    const h = Math.floor(segundos / 3600);
    const m = Math.floor((segundos % 3600) / 60);
    const s = Math.floor(segundos % 60);
    return [h, m, s].map(val => String(val).padStart(2, '0')).join(':');
}

// Actualiza resumen de estadísticas en pantalla
function actualizarResumen(data) {
    document.getElementById('totalTurnos').textContent = data.totalTurnos ?? '-';

    if (data.promedioEspera != null && !isNaN(data.promedioEspera)) {
        document.getElementById('promedioEspera').textContent = formatearSegundos(data.promedioEspera);
    } else {
        document.getElementById('promedioEspera').textContent = '-';
    }
}


// Actualiza todos los gráficos con la info del JSON recibido
function actualizarGraficos(data) {
    const tipoGrafico = document.getElementById('tipoGrafico').value || 'bar';

    // Gráfico Funcionarios
    const ctxFuncionarios = document.getElementById('graficoFuncionarios').getContext('2d');
    const etiquetasFunc = data.comparacion_funcionarios.map(f => f.funcionario);
    const datosFunc = data.comparacion_funcionarios.map(f => f.total);
    crearOActualizarGrafico(ctxFuncionarios, tipoGrafico, etiquetasFunc, datosFunc, 'Turnos por Funcionario');
    graficoFuncionarios = ctxFuncionarios.chart;

    // Gráfico Servicios
    const ctxServicios = document.getElementById('graficoServicios').getContext('2d');
    const etiquetasServ = data.comparacion_servicios.map(s => s.servicio);
    const datosServ = data.comparacion_servicios.map(s => s.total);
    crearOActualizarGrafico(ctxServicios, tipoGrafico, etiquetasServ, datosServ, 'Turnos por Servicio');
    graficoServicios = ctxServicios.chart;

    // Gráfico Fechas
    const ctxFechas = document.getElementById('graficoFechas').getContext('2d');
    const etiquetasFechas = data.comparacion_fechas.map(f => f.fecha);
    const datosFechas = data.comparacion_fechas.map(f => f.total);
    crearOActualizarGrafico(ctxFechas, tipoGrafico, etiquetasFechas, datosFechas, 'Turnos por Fecha');
    graficoFechas = ctxFechas.chart;
}

// Limpiar resumen y gráficos
function limpiarResumenYGraficos() {
    document.getElementById('totalTurnos').textContent = '-';
    document.getElementById('promedioEspera').textContent = '-';

    if (graficoFuncionarios) {
        graficoFuncionarios.destroy();
        graficoFuncionarios = null;
    }
    if (graficoServicios) {
        graficoServicios.destroy();
        graficoServicios = null;
    }
    if (graficoFechas) {
        graficoFechas.destroy();
        graficoFechas = null;
    }
}

// Botón FILTRAR
document.getElementById('btnFiltrar').addEventListener('click', function () {
    const form = document.getElementById('formFiltros');
    const params = new URLSearchParams(new FormData(form)).toString();

    fetch("/Admin/estadisticas/ajax?" + params, {
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

// Botón LIMPIAR - reinicia todo
document.getElementById('btnLimpiar').addEventListener('click', function () {
    const form = document.getElementById('formFiltros');

    // Limpiar fechas
    form.querySelector('#fecha_desde').value = '';
    form.querySelector('#fecha_hasta').value = '';

    // Limpiar selects
    form.querySelector('#hora').value = '';
    document.getElementById('tipoGrafico').value = 'bar';

    // Limpiar checkboxes
    form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

    // Limpiar gráficos y resumen
    limpiarResumenYGraficos();
});

// Cambio de tipo de gráfico
document.getElementById('tipoGrafico').addEventListener('change', function () {
    document.getElementById('btnFiltrar').click();
});
