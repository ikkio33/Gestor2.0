@section('styles')
<link href="{{ asset('css/vista.css') }}" rel="stylesheet">
<script src="{{ asset('js/vista.js') }}" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@yield('styles')

<div class="container-fluid px-3 py-4">
    <h1 class="text-center display-3 fw-bold mb-5">Turnos en Atención</h1>

    <!-- 🔁 Aquí irá el contenido dinámico generado por JS -->
    <div id="contenedor-turnos-en-atencion" class="turnos-list">
        <div class="no-turnos-box">
            <p>No hay turnos en atención en este momento.</p>
        </div>
    </div>
</div>

@if ($nuevoTurno)
<audio autoplay>
    <source src="{{ asset('assets/audio/turno_llamado.mp3') }}" type="audio/mpeg">
</audio>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mensaje = "Turno {{ $codigoActual }}, diríjase al mesón {{ $mesonActual }}.";
        const voz = new SpeechSynthesisUtterance(mensaje);
        voz.lang = "es-CL";
        speechSynthesis.speak(voz);
    });
</script>
@endif