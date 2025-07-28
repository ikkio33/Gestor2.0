@extends('layouts.layout_totem')

@section('title', 'Ingreso de Pasaporte')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/totem.css') }}">
<style>
    .touch-keyboard {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        /* menos espacio entre teclas */
    }

    .keyboard-key {
        flex: 1 1 70px;
        /* mínimo 70px, flexible */
        max-width: 90px;
        /* teclas grandes */
        font-weight: 700;
        font-size: 2.2rem;
        padding: 14px 0;
        user-select: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        background-color: #e0e0e0;
        border: 1px solid #ccc;
    }

    .keyboard-key:hover {
        background-color: #f0c419;
        /* amarillo suave al pasar el mouse */
    }

    #pasaporteInput {
        font-size: 2.6rem;
        letter-spacing: 4px;
        text-transform: uppercase;
        user-select: none;
        padding-left: 0.5rem;
    }

    @media (max-width: 600px) {
        .keyboard-key {
            flex: 1 1 60px;
            max-width: 70px;
            font-size: 1.8rem;
            padding: 12px 0;
        }

        #pasaporteInput {
            font-size: 2.2rem;
            letter-spacing: 3px;
        }
    }
</style>
@endsection

@section('content')
<div class="totem-wrapper">
    <div class="card totem-card">
        <div class="card-header text-center bg-primary text-white">
            <h2 class="mb-0">Ingrese su Pasaporte</h2>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('totem.selectPasaporte') }}" method="POST" id="pasaporteForm">
                @csrf
                <div class="mb-4">
                    <input type="text"
                        name="pasaporte"
                        id="pasaporteInput"
                        class="form-control form-control-lg text-center fs-2 py-3"
                        placeholder="Ej: A12345678"
                        required
                        readonly
                        autocomplete="off"
                        maxlength="15"
                        autofocus>
                    <div class="form-text text-center mt-2">Use el teclado en pantalla para ingresar su número de pasaporte</div>
                </div>

                <div class="touch-keyboard mt-4">
                    @foreach (array_merge(range('A', 'Z'), range(0,9)) as $valor)
                    <button type="button"
                        class="keyboard-key"
                        data-value="{{ $valor }}">
                        {{ $valor }}
                    </button>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mt-4 gap-3 flex-wrap">
                    <a href="{{ route('totem') }}" class="btn btn-secondary btn-lg flex-fill">
                        ← Volver al RUT
                    </a>
                    <button type="button"
                        class="btn btn-danger btn-lg flex-fill"
                        id="deletePasaporteBtn">
                        ← Borrar
                    </button>
                    <button type="submit"
                        class="btn btn-success btn-lg flex-fill">
                        Continuar →
                    </button>
                </div>

                @endsection

                @section('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const input = document.getElementById('pasaporteInput');
                        const keys = document.querySelectorAll('.keyboard-key');
                        const deleteBtn = document.getElementById('deletePasaporteBtn');

                        keys.forEach(key => {
                            key.addEventListener('click', () => {
                                const value = key.getAttribute('data-value');
                                if (value) {
                                    input.value += value.toUpperCase();
                                }
                            });
                        });

                        deleteBtn.addEventListener('click', () => {
                            input.value = input.value.slice(0, -1);
                        });
                    });
                </script>
                @endsection