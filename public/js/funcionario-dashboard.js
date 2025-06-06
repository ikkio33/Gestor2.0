document.addEventListener('DOMContentLoaded', function () {
    const turnoActualDiv = document.getElementById('turno-actual');
    const contenedor = document.getElementById('contenedor-turnos-espera');
    let intervaloTiempoAtencion;

    function cargarTurnos() {
        fetch('/funcionario/dashboard/turnos/ajax')
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.turnos.length) {
                    contenedor.innerHTML = '<p class="text-center text-muted">No hay turnos en espera.</p>';
                    return;
                }

                let html = '';
                data.turnos.forEach(turno => {
                    html += `
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <strong>${turno.codigo}</strong> - ${turno.servicio_nombre}
                                <br>
                                <small class="text-muted">${turno.created_at_formatted}</small>
                            </div>
                        </div>
                    `;
                });

                contenedor.innerHTML = html;
            })
            .catch(error => {
                console.error('Error cargando turnos:', error);
                contenedor.innerHTML = '<p class="text-danger text-center">Error al cargar los turnos.</p>';
            });
    }

    function cargarTurnoActual() {
        fetch('/funcionario/turno/actual/ajax')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.turno) {
                    const turno = data.turno;
                    turnoActualDiv.innerHTML = `
                        <h4>
                            <i class="bi bi-person-lines-fill me-2"></i> Atendiendo Turno: ${turno.codigo}
                        </h4>
                        <p>Tiempo en Atención: <span id="tiempo-atencion">Calculando...</span></p>
                        <form action="/funcionario/turnos/rellamar" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${data.csrf}">
                            <input type="hidden" name="turno_id" value="${turno.id}">
                            <button class="btn btn-secondary btn-sm me-2"><i class="bi bi-bullhorn me-1"></i> Re-Llamar</button>
                        </form>
                        <form action="/funcionario/turnos/finalizar" method="POST" class="d-inline me-2">
                            <input type="hidden" name="_token" value="${data.csrf}">
                            <input type="hidden" name="turno_id" value="${turno.id}">
                            <button class="btn btn-danger btn-sm"><i class="bi bi-check-circle me-1"></i> Finalizar Atención</button>
                        </form>
                        <form action="/funcionario/turnos/cancelar" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${data.csrf}">
                            <input type="hidden" name="turno_id" value="${turno.id}">
                            <button class="btn btn-warning btn-sm text-white"><i class="bi bi-x-circle me-1"></i> Cancelar Turno</button>
                        </form>
                    `;

                    if (intervaloTiempoAtencion) clearInterval(intervaloTiempoAtencion);
                    const inicio = new Date(turno.inicio_atencion);

                    function actualizarTiempo() {
                        const ahora = new Date();
                        const diff = ahora - inicio;
                        const min = Math.floor(diff / 60000);
                        const seg = Math.floor((diff % 60000) / 1000);
                        const span = document.getElementById('tiempo-atencion');
                        if (span) span.textContent = `${min} min ${seg} seg`;
                    }

                    actualizarTiempo();
                    intervaloTiempoAtencion = setInterval(actualizarTiempo, 1000);
                } else {
                    turnoActualDiv.innerHTML = '<p>No hay turno en atención.</p>';
                }
            })
            .catch(error => {
                console.error('Error turno actual:', error);
                turnoActualDiv.innerHTML = '<p class="text-danger">Error al cargar el turno actual.</p>';
            });
    }

    // Gestión del ícono en el colapso
    const toggleIcon = document.getElementById('toggleIcon');
    const collapseDiv = document.getElementById('gestionMesones');
    collapseDiv.addEventListener('show.bs.collapse', () => {
        toggleIcon.classList.replace('bi-chevron-down', 'bi-chevron-up');
    });
    collapseDiv.addEventListener('hide.bs.collapse', () => {
        toggleIcon.classList.replace('bi-chevron-up', 'bi-chevron-down');
    });

    // Inicializar cargas
    cargarTurnos();
    cargarTurnoActual();
    setInterval(cargarTurnos, 5000);
    setInterval(cargarTurnoActual, 5000);
});
