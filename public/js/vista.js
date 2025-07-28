let ultimoCodigo = null;
let turnosAnunciados = new Set();
let isLoading = true;
let currentLayoutType = null;
let currentTurnosOnScreen = []; // Array para mantener los turnos actualmente en pantalla

document.addEventListener('DOMContentLoaded', function () {
    cargarTurnosEnAtencion();
    setInterval(cargarTurnosEnAtencion, 3000);
});

async function cargarTurnosEnAtencion() {
    const turnosGridContainer = document.getElementById('turnos-grid-container');
    const column1 = document.getElementById('turnos-column-1');
    const column2 = document.getElementById('turnos-column-2');
    const noTurnosMessage = document.getElementById('no-turnos-message');
    const loadingMessage = document.getElementById('loading-message');

    if (!turnosGridContainer || !column1 || !column2 || !noTurnosMessage || !loadingMessage) {
        console.error("Elementos DOM requeridos no encontrados.");
        return;
    }

    if (isLoading || turnosGridContainer.style.display === 'none') {
        loadingMessage.style.display = 'block';
        noTurnosMessage.style.display = 'none';
        turnosGridContainer.style.display = 'none';
    }

    try {
        const response = await fetch('/publico/turno-en-atencion');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        let turnosRecibidos = data.turnos || [];

        loadingMessage.style.display = 'none';
        isLoading = false;

        if (turnosRecibidos.length === 0) {
            noTurnosMessage.style.display = 'block';
            turnosGridContainer.style.display = 'none';
            column1.innerHTML = '';
            column2.innerHTML = '';
            currentLayoutType = null;
            currentTurnosOnScreen = []; // Limpiar la cola de turnos
            return;
        }

        noTurnosMessage.style.display = 'none';
        turnosGridContainer.style.display = 'flex';

        // Lógica de audio
        let currentFirstTurnoCode = turnosRecibidos.length > 0 ? turnosRecibidos[0].codigo : null;
        let shouldAnnounce = false;
        if (currentFirstTurnoCode && currentFirstTurnoCode !== ultimoCodigo) {
            ultimoCodigo = currentFirstTurnoCode;
            if (turnosRecibidos[0].es_nuevo && !turnosAnunciados.has(ultimoCodigo)) {
                shouldAnnounce = true;
            }
        }
        if (turnosAnunciados.size > 20) {
            turnosAnunciados.clear();
        }

        // ***** Lógica para la cola y manejo de 10 turnos *****
        const maxDisplayTurnos = 10;
        const maxTurnosSingleColumn = 4;

        // Si el número de turnos recibidos excede el máximo permitido,
        // tomamos solo los más recientes para mostrar (FIFO).
        // Si el backend ya maneja esto, esta línea es redundante pero segura.
        let turnosToShow = turnosRecibidos.slice(0, maxDisplayTurnos);


        let newLayoutType;
        if (turnosToShow.length <= maxTurnosSingleColumn) {
            newLayoutType = 'single';
        } else {
            newLayoutType = 'double';
        }

        const layoutChanged = newLayoutType !== currentLayoutType;
        currentLayoutType = newLayoutType;

        if (layoutChanged) {
            column1.innerHTML = '';
            column2.innerHTML = '';
            turnosGridContainer.classList.toggle('single-column-layout', newLayoutType === 'single');
            column2.style.display = (newLayoutType === 'single') ? 'none' : 'flex';
            currentTurnosOnScreen = []; // Resetear la lista de turnos en pantalla
        }

        // Identificar turnos a añadir y a remover
        const newTurnoCodes = new Set(turnosToShow.map(t => t.codigo));
        const oldTurnoCodes = new Set(currentTurnosOnScreen.map(t => t.codigo));

        const turnosToAdd = turnosToShow.filter(t => !oldTurnoCodes.has(t.codigo));
        const turnosToRemove = currentTurnosOnScreen.filter(t => !newTurnoCodes.has(t.codigo));

        // Remover turnos antiguos con animación
        turnosToRemove.forEach(turno => {
            const boxToRemove = document.getElementById(`turno-${turno.codigo}`);
            if (boxToRemove) {
                boxToRemove.classList.add('fadeOut');
                boxToRemove.addEventListener('animationend', () => {
                    boxToRemove.remove();
                }, { once: true });
            }
        });

        // Actualizar la lista de turnos en pantalla (solo con los turnos que realmente se mostrarán)
        // Esto es crucial para la próxima iteración.
        currentTurnosOnScreen = turnosToShow;

        // Añadir/Actualizar turnos en el DOM
        // Reconstruimos completamente para asegurar el orden y la distribución correcta
        // sin depender de la manipulación individual de hijos si la lista cambia significativamente
        if (!layoutChanged && turnosToAdd.length === 0 && turnosToRemove.length === 0) {
            // Si no hay cambios de layout y la lista de turnos es la misma, no hacemos nada más.
            // Esto evita repintados innecesarios y el parpadeo.
            console.log("No hay cambios significativos en los turnos. Saltando actualización DOM.");
        } else {
            // Re-renderizamos todo si hay cambios o si el layout cambió
            column1.innerHTML = '';
            column2.innerHTML = '';

            turnosToShow.forEach((turno, index) => {
                const newBox = crearTurnoBox(turno);
                newBox.id = `turno-${turno.codigo}`;

                let targetColumn;
                if (newLayoutType === 'single') {
                    targetColumn = column1;
                } else {
                    targetColumn = index < 4 ? column1 : column2;
                }

                targetColumn.appendChild(newBox);
                void newBox.offsetWidth; // Trigger reflow
                newBox.classList.add('fadeInUp');
            });
        }

        // Llama a reproducir audio
        if (shouldAnnounce) {
            reproducirAudio(turnosRecibidos[0].codigo, turnosRecibidos[0].meson_nombre);
        }

    } catch (error) {
        console.error('Error al obtener los turnos en atención:', error);
        loadingMessage.style.display = 'none';
        noTurnosMessage.textContent = 'Error al cargar turnos. Intente de nuevo más tarde.';
        noTurnosMessage.style.display = 'block';
        turnosGridContainer.style.display = 'none';
        column1.innerHTML = '';
        column2.innerHTML = '';
        currentLayoutType = null;
        currentTurnosOnScreen = [];
    }
}

function crearTurnoBox(turno) {
    const turnoBox = document.createElement('div');
    turnoBox.className = 'turno-box';
    turnoBox.innerHTML = `
        <div class="info-numero">${turno.codigo}</div>
        <div class="info-meson">Mesón ${turno.meson_nombre ?? 'N/D'}</div>
    `;
    return turnoBox;
}

function reproducirAudio(codigo, meson) {
    if (turnosAnunciados.has(codigo)) {
        console.log(`Turno ${codigo} ya anunciado, omitiendo.`);
        return;
    }
    turnosAnunciados.add(codigo);

    const beep = document.getElementById('beep');
    if (beep) {
        beep.play().catch(e => console.warn("Error al reproducir beep:", e));
    } else {
        console.warn("Elemento de audio 'beep' no encontrado.");
    }

    const mensaje = `Turno ${codigo}, diríjase al mesón ${meson}.`;
    const voz = new SpeechSynthesisUtterance(mensaje);
    voz.lang = "es-CL";
    speechSynthesis.speak(voz);
}