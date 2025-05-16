@extends('layouts.layout_totem')

@section('title', 'Turno Generado')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/confirmacion.css') }}">
@endsection

@section('content')
<div class="confirm-wrapper">
    <div class="confirm-card">
        <h2>¡Turno Generado!</h2>
        <div class="display-code">{{ $codigo }}</div>
        <p>Por favor, espere a ser llamado en pantalla.</p>
        <a href="{{ route('totem.show') }}" class="btn btn-primary">Volver al inicio</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', () => {
    window.print(); // Dispara la impresión
    setTimeout(() => {
        window.location.href = "{{ route('totem.show') }}"; // Redirige después de 3 segundos
    }, 1000);
});
</script>
@endpush
