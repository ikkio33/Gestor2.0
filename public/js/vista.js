let ultimoCodigo = null;
let turnosAnunciados = new Set();

document.addEventListener('DOMContentLoaded', function () {
    cargarTurnosEnAtencion();
    setInterval(cargarTurnosEnAtencion, 3000);
});

function cargarTurnosEnAtencion() {
    fetch('/publico/turno-en-atencion')
        .then(response => response.json())
        .then(data => {
            const contenedor = document.getElementById('contenedor-turnos-en-atencion');
            if (!contenedor) return;

            const turnos = data.turnos || [];

            if (turnos.length === 0) {
                if (!contenedor.querySelector('.no-turnos-box')) {
                    contenedor.innerHTML = `
                        <div class="no-turnos-box">
                            <p>No hay turnos en atención en este momento.</p>
                        </div>`;
                }
                return;
            }

            if (turnos[0].codigo === ultimoCodigo) return;
            ultimoCodigo = turnos[0].codigo;

            contenedor.innerHTML = '';

            turnos.slice(0, 5).forEach((turno, index) => {
                const esNuevo = index === 0 && turno.es_nuevo;
                const claseNuevo = esNuevo ? 'new-turno' : '';

                const turnoHTML = `
                    <div class="turno-box shadow ${claseNuevo}">
                        <div class="d-flex justify-content-center align-items-center flex-nowrap gap-4 overflow-auto">
                            <span class="badge bg-primary badge-turno">${turno.codigo}</span>
                            <span class="fw-bold text-truncate" style="max-width: 25%; white-space: nowrap;">
                                ${turno.nombre_servicio ?? 'Servicio desconocido'}
                            </span>
                            <span class="text-muted d-inline-flex align-items-center">
                                <i class="fas fa-desktop me-1"></i> Mesón ${turno.meson_nombre ?? 'N/D'}
                            </span>
                        </div>
                    </div>
                `;

                contenedor.insertAdjacentHTML('beforeend', turnoHTML);

                if (esNuevo) {
                    reproducirAudio(turno.codigo, turno.meson_nombre);
                }
            });
        })
        .catch(error => {
            console.error('Error al obtener los turnos en atención:', error);
        });
}

function reproducirAudio(codigo, meson) {
    if (turnosAnunciados.has(codigo)) return;
    turnosAnunciados.add(codigo);

    const audio = new Audio('/assets/audio/turno_llamado.mp3');
    audio.play();

    const mensaje = `Turno ${codigo}, diríjase al mesón ${meson}.`;
    const voz = new SpeechSynthesisUtterance(mensaje);
    voz.lang = "es-CL";
    speechSynthesis.speak(voz);
}
