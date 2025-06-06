<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurnoEstadoController;
use App\Http\Controllers\ApiTurnoController;

Route::get('/estado-turno/{codigo}', [TurnoEstadoController::class, 'estado']);
Route::get('/turnos/{codigo}', [ApiTurnoController::class, 'mostrarPorCodigo']);
Route::get('/turnos/cantidad_antes/{codigo}', [ApiTurnoController::class, 'cantidadTurnosAntes']);