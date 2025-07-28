<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Turno en Espera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .turnos-list {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .turno-box {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            min-width: 300px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .badge-turno {
            font-size: 1.5rem;
            margin: 0.25rem;
        }

        .no-turnos-box {
            color: #6c757d;
            font-style: italic;
        }

        .new-turno {
            border: 2px solid #0d6efd;
            animation: blink 1s infinite alternate;
        }

        @keyframes blink {
            from {
                background-color: #f0f8ff;
            }

            to {
                background-color: #e0f0ff;
            }
        }
    </style>
</head>

<body>

    <div class="container py-4" id="contenedor">
        <section>
            <h2 class="mb-3 text-center">Turnos pendientes antes que tú</h2>
            <section class="mb-4 text-center">
                <div id="mi-turno-box" class="turnos-list"></div>
            </section>

            <div id="turnos-pendientes" class="d-flex flex-wrap justify-content-center">
                <div class="text-muted">Cargando pendientes...</div>
            </div>
        </section>

        <!-- Turno en Atención -->
        <section class="mb-5 text-center">
            <div id="turno-en-atencion" class="turnos-list"></div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const codigo = params.get('codigo');
            const letra = codigo ? codigo.charAt(0).toUpperCase() : null;

            const miTurnoBox = document.getElementById('mi-turno-box');
            const contenedor = document.getElementById('contenedor');
            const atencionDiv = document.getElementById('turno-en-atencion');
            const pendientesDiv = document.getElementById('turnos-pendientes');

            if (!codigo || codigo.length < 2) {
                contenedor.innerHTML = '<div class="alert alert-danger text-center">Código inválido</div>';
                return;
            }

            async function cargarDatosTurno() {
                try {
                    const res = await fetch(`/api/estado-turno/${codigo}`);
                    if (!res.ok) {
                        const errorText = await res.text();
                        console.warn("Error del servidor:", errorText);
                        contenedor.innerHTML = '<div class="alert alert-danger text-center">Error al obtener datos</div>';
                        atencionDiv.innerHTML = '';
                        pendientesDiv.innerHTML = '';
                        return;
                    }

                    const data = await res.json();
                    const actual = data.turnos_en_atencion?.[0];
                    const miTurno = data.mi_turno;
                    const pendientes = data.turnos_anteriores_pendientes || []; // 👈 CORREGIDO
                    const cantidadPendientes = data.total_pendientes_mismo_servicio ?? pendientes.length;

                    if (!miTurno) {
                        contenedor.innerHTML = `
                        <div class="alert alert-warning text-center">
                            El turno no está registrado para el día de hoy.
                        </div>
                    `;
                        atencionDiv.innerHTML = '';
                        pendientesDiv.innerHTML = '';
                        return;
                    }

                    if (!miTurno.codigo_turno.startsWith(letra)) {
                        contenedor.innerHTML = `
                        <div class="alert alert-danger text-center">
                            No tienes permiso para ver este turno.
                        </div>
                    `;
                        atencionDiv.innerHTML = '';
                        pendientesDiv.innerHTML = '';
                        return;
                    }

                    if (['atendido', 'cancelado'].includes(miTurno.estado)) {
                        contenedor.innerHTML = `
                        <div class="alert alert-info text-center">
                            Este turno ya fue atendido o cancelado.
                        </div>
                    `;
                        atencionDiv.innerHTML = '';
                        pendientesDiv.innerHTML = '';
                        return;
                    }

                    if (miTurno.estado === 'atendiendo') {
                        contenedor.innerHTML = `
                        <div class="alert alert-success text-center">
                            Están llamando este número, por favor dirígete al mesón indicado.
                        </div>
                    `;
                    }

                    miTurnoBox.innerHTML = `
                    <div class="turno-box shadow ${miTurno.estado === 'atendiendo' ? 'new-turno' : ''}">
                        <h5 class="mb-2">Tu Turno</h5>
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                            <span class="badge bg-dark badge-turno">${miTurno.codigo_turno}</span>
                            <span class="fw-semibold">${miTurno.servicio?.nombre ?? 'Servicio desconocido'}</span>
                            <span class="text-muted">
                                Estado: <strong>${miTurno.estado.toUpperCase()}</strong>
                            </span>
                            ${miTurno.meson?.nombre ? `<span class="text-muted"><i class="fas fa-desktop me-1"></i> Mesón ${miTurno.meson.nombre}</span>` : ''}
                            <span class="text-muted">
                                Turnos antes que tú: <strong>${pendientes.length}</strong>
                            </span>
                        </div>
                    </div>
                `;

                    // Turno en atención
                    atencionDiv.innerHTML = '';
                    if (actual && actual.estado === 'atendiendo') {
                        atencionDiv.innerHTML = `
                        <div class="turno-box shadow new-turno">
                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-4 overflow-auto">
                                <span class="badge bg-primary badge-turno">${actual.codigo_turno}</span>
                                <span class="fw-bold text-truncate" style="max-width: 25%; white-space: nowrap;">${actual.servicio.nombre}</span>
                                <span class="text-muted d-inline-flex align-items-center">
                                    <i class="fas fa-desktop me-1"></i> Mesón ${actual.meson.nombre}
                                </span>
                            </div>
                        </div>
                    `;
                    } else {
                        atencionDiv.innerHTML = `<div class="no-turnos-box"></div>`;
                    }

                    // Turnos pendientes
                    pendientesDiv.innerHTML = '';
                    if (pendientes.length === 0) {
                        pendientesDiv.innerHTML = '<div class="text-muted">No hay turnos anteriores pendientes</div>';
                        return;
                    }

                    pendientes.forEach(t => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-secondary badge-turno';
                        badge.textContent = t.codigo_turno;
                        pendientesDiv.appendChild(badge);
                    });

                } catch (error) {
                    console.error("Error al cargar datos:", error);
                    contenedor.innerHTML = '<div class="alert alert-danger text-center">Error al cargar datos</div>';
                    atencionDiv.innerHTML = '';
                    pendientesDiv.innerHTML = '';
                }
            }

            cargarDatosTurno();
            setInterval(cargarDatosTurno, 5000); // Refresca cada 5 segundos
        });
    </script>

</body>

</html>