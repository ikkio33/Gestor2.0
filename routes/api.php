<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;

Route::get('/turno-info/{codigo}', function ($codigo) {
    $turno = DB::table('turnos')
        ->where('codigo_turno', $codigo)
        ->where('estado', 'pendiente')
        ->first();

    if (! $turno) {
        return response()->json([
            'error' => 'Turno no encontrado o ya fue atendido'
        ], 404);
    }

    $servicio = DB::table('servicios')->find($turno->servicio_id);
    $materia  = DB::table('materias')->find($turno->materia_id);

    $pendientesAntes = DB::table('turnos')
        ->where('servicio_id', $turno->servicio_id)
        ->whereDate('created_at', now()->toDateString())
        ->where('estado', 'pendiente')
        ->where('numero_turno', '<', $turno->numero_turno)
        ->count();

    return response()->json([
        'codigo'           => $turno->codigo_turno,
        'estado'           => $turno->estado,
        'servicio'         => $servicio->nombre ?? 'No especificado',
        'materia'          => $materia->nombre ?? 'No especificada',
        'pendientesAntes'  => $pendientesAntes,
        'fecha'            => Carbon::parse($turno->created_at)->format('d/m/Y H:i'),
    ]);
});
