<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Turno Generado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .confirm-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; 
        }

        .confirm-card {
            border: 1px dashed #000;
            padding: 20px;
            width: 300px;
        }

        .display-code {
            font-size: 40px;
            font-weight: bold;
            margin: 15px 0;
        }

        .ticket-datetime,
        .ticket-message {
            font-size: 14px;
            margin: 5px 0;
        }

        .qr-container {
            margin-top: 15px;
        }

        .btn-continuar {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #004080;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-continuar:hover {
            background-color: #002c5f;
        }

        /* ==== ESTILOS PARA IMPRESIÓN ==== */
        @media print {
            body {
                zoom: 80%; 
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .confirm-wrapper {
                height: auto !important;
                align-items: flex-start !important;
                padding-top: 0mm; 
            }

            .btn-continuar {
                display: none !important;
            }

            @page {
                margin: 5mm 5mm 5mm 5mm; 
            }
        }
    </style>
</head>
<body>
<div class="confirm-wrapper">
    <div class="confirm-card">
        <h2>¡Turno Generado!</h2>
        <div class="display-code">{{ $codigo }}</div>
        <p class="ticket-datetime">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p class="ticket-message">Gracias por su visita. Por favor espere a ser llamado en pantalla.</p>
        <div class="qr-container">
            {!! $qr !!}
            <p>Escanee este código para ver su turno desde el teléfono.</p>
        </div>
        <button id="btnContinuar" class="btn-continuar">Continuar</button>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(() => {
            window.print();
            setTimeout(() => {
                window.location.href = "{{ route('totem.show') }}";
            }, 10000);
        }, 300);
    });

    document.getElementById('btnContinuar').addEventListener('click', function () {
        window.location.href = "{{ route('totem.show') }}";
    });
</script>
</body>
</html>
