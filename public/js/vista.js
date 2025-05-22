document.addEventListener('DOMContentLoaded', () => {
    let turnoActualCodigo = null;

    async function cargarTurnoActual() {
        try {
            const response = await fetch('/funcionario/turno-actual');

            // Validamos que la respuesta sea JSON
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Respuesta no es JSON, posible redirección o error.');
            }

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (data.error) {
                console.warn('Backend reporta error:', data.error);
                return; // O mostrar mensaje en pantalla
            }

            if (data.nuevo && data.codigo !== turnoActualCodigo) {
                turnoActualCodigo = data.codigo;

                const turnosList = document.querySelector('.turnos-list');
                if (turnosList) {
                    turnosList.innerHTML = `
                        <div class="turno-box shadow new-turno">
                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-4 overflow-auto">
                                <span class="badge bg-primary badge-turno">${data.codigo}</span>
                                <span class="fw-bold text-truncate" style="max-width: 25%; white-space: nowrap;">${data.servicio}</span>
                                <span class="text-muted d-inline-flex align-items-center">
                                    <i class="fas fa-desktop me-1"></i> Mesón ${data.meson}
                                </span>
                            </div>
                        </div>
                    `;
                }

                // Reproducir audio
                const audio = new Audio('/assets/audio/turno_llamado.mp3');
                audio.play();

                // Voz sintetizada
                const mensaje = `Turno ${data.codigo}, diríjase al mesón ${data.meson}.`;
                const voz = new SpeechSynthesisUtterance(mensaje);
                voz.lang = 'es-CL';
                speechSynthesis.speak(voz);
            }
        } catch (error) {
            console.error('Error al cargar turno actual:', error);
        }
    }

    cargarTurnoActual();
    setInterval(cargarTurnoActual, 5000);
});
