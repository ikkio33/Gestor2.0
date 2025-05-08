@extends('layouts.layout_totem')

@section('title', 'Ingreso de RUT')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/totem.css') }}">
@endsection

@section('content')
<div class="totem-wrapper">
    <div class="card totem-card">
        <div class="card-header text-center bg-primary text-white">
            <h2 class="mb-0">Ingrese su RUT</h2>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('totem.select') }}" method="POST" id="rutForm">
                @csrf
                <div class="mb-4">
                    <input type="text"
                           name="rut"
                           id="rutInput"
                           class="form-control form-control-lg text-center fs-2 py-3"
                           placeholder="Ej: 12.345.678-K"
                           required
                           readonly
                           autocomplete="off">
                    <div class="form-text text-center mt-2">Use el teclado en pantalla para ingresar su RUT</div>
                </div>  

                <div class="touch-keyboard mt-4">
                    @foreach (array_merge(range(1,9), ['K',0,'.']) as $valor)
                        <button type="button" 
                                class="keyboard-key"
                                data-value="{{ $valor }}">
                            {{ $valor }}
                        </button>
                    @endforeach
                    <button type="button" 
                            class="keyboard-key btn-danger"
                            id="deleteBtn">
                        ← Borrar
                    </button>   
                    <button type="submit" 
                            class="keyboard-key btn-success">
                        Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/totem.js') }}"></script>
@endsection