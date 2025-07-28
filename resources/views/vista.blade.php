@section('styles')
<link href="{{ asset('css/vista.css') }}" rel="stylesheet">
<script src="{{ asset('js/vista.js') }}?v={{ time() }}" defer></script> {{-- Cache busting --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@yield('styles')

<body class="vista-publica">
    <div class="zona-superior">
        <img src="{{ asset('img/notaria_logo.png') }}" alt="Logo Notaría" class="logo-notaria">
    </div>

    <main class="contenedor-principal">
        <h1 class="text-center display-3 fw-bold mb-5">Turnos en Atención</h1>

        <div id="turnos-grid-container" class="turnos-grid-container">
            <div id="turnos-column-1" class="turnos-column">
                </div>
            <div id="turnos-column-2" class="turnos-column">
                </div>
        </div>

        <div id="no-turnos-message" class="no-turnos-box" style="display: none;">
            <p>No hay turnos en atención en este momento.</p>
        </div>

        <div id="loading-message" class="loading-box" style="display: none;">
            <p>Cargando turnos en atención...</p>
        </div>

    </main>

    <div class="zona-inferior">
        <img src="{{ asset('img/logo.png') }}" alt="Logo GesNot" class="logo-gesnot">
    </div>

    <audio id="beep" preload="auto">
        <source src="{{ asset('build/assets/short-beep.mp3') }}" type="audio/mpeg">
    </audio>

    {{-- Script de voz inicial (el que tenías en el blade) --}}
    {{-- Considera mover esta lógica al vista.js principal si quieres unificar --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const codigoActual = @json($codigoActual ?? null);
            const mesonActual = @json($mesonActual ?? null);

            if (!codigoActual || !mesonActual) return;

            const beep = document.getElementById('beep');
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const mensaje = `Turno ${codigoActual}, diríjase al mesón ${mesonActual}.`;

            const voz = new SpeechSynthesisUtterance(mensaje);
            voz.lang = "es-CL";

            voz.onend = () => {
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        beep.play().catch(e => console.warn("Beep bloqueado tras resume():", e));
                    });
                } else {
                    beep.play().catch(e => console.warn("Beep bloqueado sin resume():", e));
                }
            };

            speechSynthesis.cancel();
            speechSynthesis.speak(voz);
        });
    </script>
</body>