document.addEventListener('DOMContentLoaded', () => {
    // Guardamos los códigos actuales para detectar cambios
    let turnosActualesCodigos = [];

    async function cargarTurnoActual() {
        try {
            console.log('[INFO] Llamando al servidor para verificar turnos nuevos...');

            const response = await fetch('/turno-actual-publico');

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Respuesta no es JSON, posible redirección o error.');
            }

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log('[DEBUG] Turnos actuales recibidos:', data);

            const turnosList = document.querySelector('.turnos-list');
            if (!turnosList) {
                console.warn('[WARN] No se encontró el contenedor .turnos-list en el DOM.');
                return;
            }

            if (data.nuevo && Array.isArray(data.turnos) && data.turnos.length > 0) {
                // Extraemos los códigos actuales para comparar
                const nuevosCodigos = data.turnos.map(t => t.codigo);

                // Detectamos si hay algún código nuevo que no estaba antes
                const hayTurnoNuevo = nuevosCodigos.some(codigo => !turnosActualesCodigos.includes(codigo));

                if (hayTurnoNuevo) {
                    console.log('[INFO] Nuevo/actualizado turno detectado:', nuevosCodigos);

                    turnosActualesCodigos = nuevosCodigos;

                    // Generamos el HTML para todos los turnos
                    turnosList.innerHTML = data.turnos.map(t => `
                        <div class="turno-box shadow new-turno">
                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-4 overflow-auto">
                                <span class="badge bg-primary badge-turno">${t.codigo}</span>
                                <span class="fw-bold text-truncate" style="max-width: 25%; white-space: nowrap;">${t.servicio}</span>
                                <span class="text-muted d-inline-flex align-items-center">
                                    <i class="fas fa-desktop me-1"></i> Mesón ${t.meson}
                                </span>
                            </div>
                        </div>
                    `).join('');

                    // Reproducir audio y voz para el primer turno (el más reciente)
                    const primerTurno = data.turnos[0];
                    const audio = new Audio('/assets/audio/turno_llamado.mp3');
                    await audio.play();

                    const mensaje = `Turno ${primerTurno.codigo}, diríjase al mesón ${primerTurno.meson}.`;
                    const voz = new SpeechSynthesisUtterance(mensaje);
                    voz.lang = 'es-CL';
                    speechSynthesis.speak(voz);

                } else {
                    console.log('[INFO] Los turnos actuales no cambiaron, no actualizamos DOM ni audio.');
                }

            } else if (!data.nuevo) {
                console.log('[INFO] No hay turnos nuevos.');
                turnosActualesCodigos = []; // Reiniciamos para detectar futuro turno nuevo

                turnosList.innerHTML = `
                    <div class="no-turnos-box">
                        <p>No hay turnos en atención en este momento.</p>
                    </div>
                `;
            } else {
                console.log('[INFO] Formato inesperado o datos incompletos.');
            }

        } catch (error) {
            console.error('Error al cargar turnos actuales:', error);
        }
    }

    cargarTurnoActual();
    setInterval(cargarTurnoActual, 5000);
});
