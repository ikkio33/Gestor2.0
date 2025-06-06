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
            <div id="turnos-pendientes" class="d-flex flex-wrap justify-content-center">
                <div class="text-muted">Cargando pendientes...</div>
            </div>
        </section>
        <!-- <header class="mb-4 text-center">
            <h1>Turno Actual</h1>
            <p class="text-muted">Tu posición y los turnos pendientes en tu servicio</p>
        </header> -->

        <!-- Turno en Atención -->
        <section class="mb-5 text-center">
            <!-- <h2 class="mb-3">Turnos en Atención</h2> -->
            <div id="turno-en-atencion" class="turnos-list"></div>
        </section>

        <!-- Turnos Pendientes -->

        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const codigo = params.get('codigo');
            const letra = codigo ? codigo.charAt(0).toUpperCase() : null;

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
                        contenedor.innerHTML = '';
                        return;
                    }

                    const data = await res.json();
                    const actual = data.atendiendo;   // Aquí cambio importante
                    const miTurno = data.mi_turno;

                    if (!miTurno || !miTurno.codigo_turno.startsWith(letra)) {
                        contenedor.innerHTML = '';
                        return;
                    }

                    // Mostrar turno en atención como tarjeta
                    atencionDiv.innerHTML = '';
                    if (actual && actual.estado === 'atendiendo') {  // Y acá también
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
                        atencionDiv.innerHTML = `
                            <div class="no-turnos-box">
                                
                            </div>
                        `;
                    }

                    // Mostrar turnos pendientes
                    pendientesDiv.innerHTML = '';
                    const pendientes = data.turnos_delante || [];
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
                }
            }

            cargarDatosTurno();
            setInterval(cargarDatosTurno, 5000); // Refresca cada 5 segundos
        });
    </script>

</body>

</html>
