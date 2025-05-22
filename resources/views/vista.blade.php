@section('styles')
<link href="{{ asset('css/vista.css') }}" rel="stylesheet">
<script src="{{ asset('js/vista.js') }}" defer></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

@yield('styles')

<div class="container-fluid px-3 py-4">
    <h1 class="text-center display-3 fw-bold mb-5">Turnos en Atención</h1>

    @if ($turnos->count())
    <div class="turnos-list">
        @foreach ($turnos->take(6) as $index => $turno)
        <div class="turno-box shadow {{ $index === 0 && $nuevoTurno ? 'new-turno' : '' }}">
            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-4 overflow-auto">
                <span class="badge bg-primary badge-turno">{{ $turno->codigo_turno }}</span>
                <span class="fw-bold text-truncate" style="max-width: 25%; white-space: nowrap;">{{ $turno->servicio->nombre }}</span>
                <span class="text-muted d-inline-flex align-items-center">
                    <i class="fas fa-desktop me-1"></i> Mesón {{ $turno->meson->nombre }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="no-turnos-box">
        <p>No hay turnos en atención en este momento.</p>
    </div>
    @endif

</div>

@if ($nuevoTurno)
<audio autoplay>
    <source src="{{ asset('assets/audio/turno_llamado.mp3') }}" type="audio/mpeg">
</audio>
<script>
    const mensaje = "Turno {{ $codigoActual }}, diríjase al mesón {{ $mesonActual }}.";
    const voz = new SpeechSynthesisUtterance(mensaje);
    voz.lang = "es-CL";
    speechSynthesis.speak(voz);
</script>
@endif