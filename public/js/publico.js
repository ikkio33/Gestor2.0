document.addEventListener('DOMContentLoaded', () => {
    async function cargarTurnosActuales() {
        try {
            const response = await fetch('/nueva/turnos-actuales');

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Respuesta no es JSON, posible redirección o error.');
            }

            const data = await response.json();
            const turnos = data.turnos || [];

            const contenedorTurnos = document.querySelector('.turnos-list');
            if (contenedorTurnos) {
                if (!data.nuevo || turnos.length === 0) {
                    contenedorTurnos.innerHTML = '<p>No hay turnos en atención actualmente.</p>';
                } else {
                    contenedorTurnos.innerHTML = turnos.map(turno => `
                        <div class="turno-box">
                            <h2>Turno ${turno.codigo}</h2>
                            <p>Servicio: ${turno.servicio}</p>
                            <p>Mesón: ${turno.meson}</p>
                        </div>
                    `).join('');
                }
            }

        } catch (error) {
            console.error('Error al cargar turnos actuales:', error);
        }
    }

    // Carga inicial
    cargarTurnosActuales();
    setInterval(cargarTurnosActuales, 5000);
});
