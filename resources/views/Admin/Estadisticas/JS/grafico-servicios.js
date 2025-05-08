let grafico = null;

function actualizarGrafico() {
  const fechaInicio = document.getElementById('fecha_inicio').value;
  const fechaFin = document.getElementById('fecha_fin').value;
  const mesonId = document.getElementById('meson_id').value;

  const params = new URLSearchParams({
    fecha_inicio: fechaInicio,
    fecha_fin: fechaFin,
    meson_id: mesonId
  });

  fetch('datos_servicios.php?' + params)
    .then(res => res.json())
    .then(data => {
      const ctx = document.getElementById('graficoServicios').getContext('2d');

      if (grafico) grafico.destroy();

      grafico = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(d => d.nombre),
          datasets: [{
            label: 'Veces seleccionado',
            data: data.map(d => d.cantidad),
            backgroundColor: '#3498db'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    });
}

document.addEventListener('DOMContentLoaded', actualizarGrafico);
