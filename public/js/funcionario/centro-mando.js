document.addEventListener('DOMContentLoaded', () => {
    const turnosPendientesList = document.getElementById('turnosPendientesList');
    const turnoAtencionContent = document.getElementById('turnoAtencionContent');
    const contadorRellamadasElem = document.getElementById('contadorRellamadas');
    const animacionRellamadoElem = document.getElementById('animacionRellamado');
    const mensajePendientesVacio = document.getElementById('mensajePendientesVacio');
    const mensajeAtencionVacio = document.getElementById('mensajeAtencionVacio');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let hayTurnoEnAtencion = false;
    let contadorRellamadas = 0;
    let turnoActualId = null;

    const fetchJSON = async (url) => {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`Error al cargar ${url}`);
        return res.json();
    };

    const postTurno = async (url, turnoId) => {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ turno_id: turnoId })
            });
            if (!res.ok) {
                const errorText = await res.text();
                console.error(`Error ${res.status}:`, errorText);
                alert(`Error al procesar la acción: ${errorText}`);
                throw new Error(`Error en ${url}`);
            }
        } catch (err) {
            console.error(err);
            alert('Error inesperado. Revisa la consola para más detalles.');
        }
    };


    const llamarTurno = async (turnoId) => {
        if (hayTurnoEnAtencion) {
            alert('Ya hay un turno en atención. Finalízalo antes de llamar uno nuevo.');
            return;
        }
        await postTurno('/funcionario/centro-mando/llamar-turno', turnoId);
    };

    const activarAnimacionRellamado = () => {
        if (!animacionRellamadoElem) return;
        animacionRellamadoElem.style.opacity = '1';
        setTimeout(() => {
            animacionRellamadoElem.style.opacity = '0';
        }, 600);
    };

    const actualizarContadorRellamadas = () => {
        if (!contadorRellamadasElem) return;
        contadorRellamadasElem.textContent = `Rellamado ${contadorRellamadas} ${contadorRellamadas === 1 ? 'vez' : 'veces'}`;
    };

    const fetchTurnosPendientes = async () => {
        try {
            const data = await fetchJSON('/funcionario/centro-mando/turnos-pendientes');
            const turnos = data.turnos;

            turnosPendientesList.innerHTML = '';

            if (Object.keys(turnos).length === 0) {
                mensajePendientesVacio.style.display = 'block';
                return;
            }

            mensajePendientesVacio.style.display = 'none';

            Object.entries(turnos).forEach(([servicio, lista]) => {
                const grupoDiv = document.createElement('div');
                grupoDiv.classList.add('turno-grupo');

                const titulo = document.createElement('h5');
                titulo.textContent = `Servicio: ${servicio}`;
                grupoDiv.appendChild(titulo);

                lista.forEach(({ id, codigo, tiempo_espera }) => {
                    const turnoDiv = document.createElement('div');
                    turnoDiv.classList.add('turno-item');
                    turnoDiv.innerHTML = `
                        <p><strong>${codigo}</strong> (Espera: ${tiempo_espera})</p>
                        <button class="btn btn-sm btn-primary llamar-btn" data-id="${id}">Llamar</button>
                    `;
                    grupoDiv.appendChild(turnoDiv);
                });

                turnosPendientesList.appendChild(grupoDiv);
            });

            document.querySelectorAll('.llamar-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    await llamarTurno(btn.dataset.id);
                    await actualizarVista();
                });
            });

        } catch (error) {
            console.error(error);
            turnosPendientesList.innerHTML = '<p>Error al cargar turnos pendientes.</p>';
        }
    };

    const fetchTurnoEnAtencion = async () => {
        try {
            const data = await fetchJSON('/funcionario/centro-mando/turno-en-atencion');
            const turno = data.turno;

            turnoAtencionContent.innerHTML = '';

            if (!turno) {
                hayTurnoEnAtencion = false;
                mensajeAtencionVacio.style.display = 'block';
                contadorRellamadas = 0;
                turnoActualId = null;
                actualizarContadorRellamadas();
                return;
            }

            mensajeAtencionVacio.style.display = 'none';

            if (turnoActualId !== turno.id) {
                turnoActualId = turno.id;
                contadorRellamadas = 0;
                actualizarContadorRellamadas();
            }

            hayTurnoEnAtencion = true;

            const div = document.createElement('div');
            div.classList.add('turno-atencion');
            div.innerHTML = `
                <p><strong>Código:</strong> ${turno.codigo}</p>
                <p><strong>Servicio:</strong> ${turno.nombre_servicio}</p>
                <p><strong>Estado:</strong> ${turno.estado}</p>
                <p><strong>Hora de inicio:</strong> ${new Date(turno.updated_at).toLocaleTimeString()}</p>
                <div class="mt-2">
                    <button class="btn btn-warning btn-sm me-2" id="rellamarTurnoBtn">Re-llamar</button>
                    <button class="btn btn-danger btn-sm me-2" id="cancelarTurnoBtn">Cancelar</button>
                    <button class="btn btn-success btn-sm" id="finalizarTurnoBtn">Finalizar atención</button>
                </div>
            `;
            turnoAtencionContent.appendChild(div);

            const addAccion = (id, url) => {
                document.getElementById(id).addEventListener('click', async (e) => {
                    e.preventDefault();

                    if (id === 'rellamarTurnoBtn') {
                        contadorRellamadas++;
                        actualizarContadorRellamadas();
                        activarAnimacionRellamado();
                    }

                    await postTurno(url, turno.id);
                    await actualizarVista();
                });
            };

            addAccion('rellamarTurnoBtn', '/funcionario/centro-mando/rellamar-turno');
            addAccion('cancelarTurnoBtn', '/funcionario/centro-mando/cancelar-turno');
            addAccion('finalizarTurnoBtn', '/funcionario/centro-mando/finalizar-turno');

        } catch (error) {
            console.error(error);
            turnoAtencionContent.innerHTML = '<p>Error al cargar turno en atención.</p>';
        }
    };

    const actualizarVista = async () => {
        await fetchTurnosPendientes();
        await fetchTurnoEnAtencion();
    };

    actualizarVista();
    setInterval(actualizarVista, 1000);
});
