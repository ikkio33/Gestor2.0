document.addEventListener('DOMContentLoaded', function () {
    const toggleMesonesBtn = document.getElementById('toggleMesonesBtn');
    const mesonesSection = document.getElementById('mesonesSection');
    toggleMesonesBtn.onclick = () => {
        mesonesSection.style.display = mesonesSection.style.display === 'none' ? 'block' : 'none';
        if (mesonesSection.style.display === 'block') {
            cargarMesonesDisponibles();
            cargarMesonesAsignados();
        }
    };

    /*
    const toggleTurnosPendientesBtn = document.getElementById('toggleTurnosPendientesBtn');
    const turnosPendientesSection = document.getElementById('turnosPendientesSection');
    toggleTurnosPendientesBtn.onclick = () => {
        const isHidden = turnosPendientesSection.style.display === 'none' || turnosPendientesSection.style.display === '';
        turnosPendientesSection.style.display = isHidden ? 'block' : 'none';
        if(isHidden) cargarTurnosPendientes();
    };
/*
    const toggleTurnoAtencionBtn = document.getElementById('toggleTurnoAtencionBtn');
    const turnoAtencionSection = document.getElementById('turnoAtencionSection');
    toggleTurnoAtencionBtn.onclick = () => {
        const isHidden = turnoAtencionSection.style.display === 'none' || turnoAtencionSection.style.display === '';
        turnoAtencionSection.style.display = isHidden ? 'block' : 'none';
        if(isHidden) cargarTurnoAtencion();
    };
    */

    // Función común para hacer POST con CSRF
    function postJson(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        }).then(res => res.json());
    }

    // --- FUNCIONES PARA MESONES ---

    // Carga y muestra mesones disponibles
    function cargarMesonesDisponibles() {
        fetch('/funcionario/mesones-disponibles') // <-- Ajuste aquí
            .then(res => res.json())
            .then(data => {
                const contenedor = document.getElementById('mesonesDisponiblesList');
                contenedor.innerHTML = '';

                if (!data.mesonesDisponibles || data.mesonesDisponibles.length === 0) {
                    contenedor.innerHTML = '<p>No hay mesones disponibles.</p>';
                    return;
                }

                data.mesonesDisponibles.forEach(meson => {
                    const mesonDiv = document.createElement('div');
                    mesonDiv.classList.add('card', 'mb-2');
                    mesonDiv.innerHTML = `
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span>Mesón: ${meson.nombre} - Estado: ${meson.estado}</span>
                        <button class="btn btn-sm btn-primary btn-asignar" data-id="${meson.id}">Asignar</button>
                    </div>
                `;
                    contenedor.appendChild(mesonDiv);
                });

                // Evento para botones de asignar
                contenedor.querySelectorAll('.btn-asignar').forEach(btn => {
                    btn.onclick = () => {
                        const mesonId = btn.dataset.id;
                        postJson('/funcionario/asignar-meson', { meson_id: mesonId }) // <-- Ajuste aquí
                            .then(data => {
                                if (data.success) {
                                    alert('Mesón asignado correctamente');
                                    cargarMesonesDisponibles(); // Refrescar lista
                                    cargarMesonesAsignados();
                                } else {
                                    alert(data.error || 'Error al asignar mesón');
                                }
                            });
                    };
                });
            })
            .catch(err => {
                console.error('Error al cargar mesones:', err);
            });
    }



    // Carga y muestra mesones asignados
    function cargarMesonesAsignados() {
        fetch('/funcionario/mesones-asignados') // <-- Ajuste aquí
            .then(res => res.json())
            .then(data => {
                const contenedor = document.getElementById('mesonesAsignadosList');
                contenedor.innerHTML = '';

                if (!data.mesonesAsignados || data.mesonesAsignados.length === 0) {
                    contenedor.innerHTML = '<p>No hay mesones asignados.</p>';
                    return;
                }

                data.mesonesAsignados.forEach(meson => {
                    const mesonDiv = document.createElement('div');
                    mesonDiv.classList.add('card', 'mb-2');
                    mesonDiv.innerHTML = `
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span>Mesón: ${meson.nombre} - Estado: ${meson.estado}</span>
                            <button class="btn btn-sm btn-danger btn-liberar" data-id="${meson.id}">Liberar</button>
                        </div>
                    `;
                    contenedor.appendChild(mesonDiv);
                });

                // Asociar evento a botones liberar
                contenedor.querySelectorAll('.btn-liberar').forEach(btn => {
                    btn.onclick = () => {
                        const mesonId = btn.dataset.id;
                        postJson('/funcionario/liberar-meson', { meson_id: mesonId }) // <-- Ajuste aquí
                            .then(data => {
                                if (data.success) {
                                    alert('Mesón liberado');
                                    cargarMesonesDisponibles();
                                    cargarMesonesAsignados();
                                } else {
                                    alert(data.error);
                                }
                            });
                    };
                });
            });
    }

    /*
    // --- FUNCIONES PARA TURNOS (comentadas) ---

    // Cargar turnos pendientes agrupados por código de servicio
    function cargarTurnosPendientes() {
        // Código que ya tienes para cargar y mostrar turnos pendientes
    }

    // Cargar turno en atención
    function cargarTurnoAtencion() {
        // Código que ya tienes para cargar y mostrar el turno que está en atención
    }
    */

    // Inicialización: mostrar mesones (disponibles y asignados), ocultar otras secciones
    mesonesSection.style.display = 'block';
    // turnosPendientesSection.style.display = 'none';
    // turnoAtencionSection.style.display = 'none';

    // Carga inicial de mesones para mostrar
    cargarMesonesDisponibles();
    cargarMesonesAsignados();
});
