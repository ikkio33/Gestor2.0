@extends('layouts.app')

@section('title', 'Organizador de Servicios y Materias')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Organizador</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Agregar Servicio --}}
    <form method="POST" action="{{ route('Admin.organizador.storeServicio') }}" class="mb-4">
        @csrf
        <div class="input-group">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del servicio" required>
            <button class="btn btn-primary">Agregar Servicio</button>   
        </div>
    </form>

    <div class="row" id="serviciosContainer">
        @foreach ($servicios as $servicio)
        <div class="col-md-4 mb-4">
            <div class="card" data-servicio-id="{{ $servicio->id }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $servicio->letra }} - {{ $servicio->nombre }}</strong>
                    <form action="{{ route('Admin.organizador.deleteServicio', $servicio->id) }}" method="POST" onsubmit="return confirm('¿Eliminar servicio?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-accion btn-eliminar btn-sm px-2 py-1">X</button>
                    </form>
                </div>
                <ul class="list-group list-group-flush materia-list" data-servicio-id="{{ $servicio->id }}">
                    @foreach ($servicio->materias as $materia)
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-materia-id="{{ $materia->id }}">
                        {{ $materia->nombre }}
                        <form action="{{ route('Admin.organizador.deleteMateria', $materia->id) }}" method="POST" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button class="btn-accion btn-eliminar btn-sm px-2 py-1">X</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                <div class="card-footer">
                    <form action="{{ route('Admin.organizador.storeMateria') }}" method="POST">
                        @csrf
                        <input type="hidden" name="servicio_id" value="{{ $servicio->id }}">
                        <div class="input-group">
                            <input type="text" name="nombre" class="form-control" placeholder="Nueva materia" required>
                            <button class="btn-accion btn-guardar px-3">+</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.querySelectorAll('.materia-list').forEach(list => {
        new Sortable(list, {
            group: 'materias',
            animation: 150,
            onEnd: function(evt) {
                const materiaId = evt.item.dataset.materiaId;
                const nuevoServicioId = evt.to.dataset.servicioId;

                fetch("{{ route('Admin.organizador.moverMateria') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            materia_id: materiaId,
                            servicio_id: nuevoServicioId
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Error al mover materia');
                        }
                    });
            }
        });
    });
</script>
@endsection
