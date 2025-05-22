<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\TotemController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\EstadisticasController;
use App\Http\Controllers\Admin\MesonController;
use App\Http\Controllers\Admin\OrganizadorController;
use App\Http\Controllers\Funcionario\DashboardController;
use App\Http\Controllers\Funcionario\FuncionarioMesonController;
use App\Http\Controllers\Funcionario\FuncionarioLlamadoController;


// Autenticación
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Vista pública
Route::get('/vista', [TurnoController::class, 'vista'])->name('vista');
Route::get('/turno/seguimiento/{codigo}', [TurnoController::class, 'seguimiento'])->name('turno.seguimiento');
Route::get('/estado-turno/{codigo}', [TurnoController::class, 'estado'])->name('turno.estado');
Route::get('/turno/{codigo}', [TurnoController::class, 'estado'])->name('turno.estado');

// Totem
Route::get('/ingresar-rut',    [TotemController::class, 'show'])->name('totem.show');
Route::post('/seleccionar',     [TotemController::class, 'select'])->name('totem.select');
Route::post('/totem/confirmar', [TotemController::class, 'confirmar'])->name('totem.confirmar');    
Route::get('/totem/confirmacion', [TotemController::class, 'confirmacion'])->name('totem.confirmacion');
Route::get('/totem', fn() => view('totem.totem'))->name('totem');
Route::post('/funcionario/llamar-ajax', [TurnoController::class, 'llamarAjax'])->name('funcionario.llamar.ajax');


// Rutas protegidas (solo administrador)
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('Admin.')->group(function () {
    Route::resource('usuarios', UsuarioController::class);
    Route::get('estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas.index');
    Route::resource('mesones', MesonController::class)->except(['show']);
    Route::prefix('organizador')->name('organizador.')->group(function () {
        Route::get('/', [OrganizadorController::class, 'index'])->name('index');
        Route::post('servicio', [OrganizadorController::class, 'storeServicio'])->name('storeServicio');
        Route::post('materia', [OrganizadorController::class, 'storeMateria'])->name('storeMateria');
        Route::delete('servicio/{id}', [OrganizadorController::class, 'deleteServicio'])->name('deleteServicio');
        Route::delete('materia/{id}', [OrganizadorController::class, 'deleteMateria'])->name('deleteMateria');
        Route::post('moverMateria', [OrganizadorController::class, 'moverMateria'])->name('moverMateria');
        Route::get('/llamar', [FuncionarioLlamadoController::class, 'index'])->name('funcionario.llamar');
        Route::post('funcionario/meson/asignar', [DashboardController::class, 'asignarMeson'])->name('funcionario.meson.asignar');
        Route::post('funcionario/meson/liberar', [DashboardController::class, 'liberarMeson'])->name('funcionario.meson.liberar');
    });
});

// Rutas protegidas(funcionarios)
Route::middleware(['auth'])
    ->prefix('funcionario')
    ->name('funcionario.')
    ->namespace('App\Http\Controllers\Funcionario')
    ->group(function () {
        Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/meson/seleccionar', [FuncionarioMesonController::class, 'seleccionar'])->name('meson.seleccionar');
        Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
        Route::delete('/meson/liberar', [FuncionarioMesonController::class, 'liberar'])->name('meson.liberar');
        Route::get('/llamar', [FuncionarioLlamadoController::class, 'index'])->name('llamar');
        Route::post('/llamar', [FuncionarioLlamadoController::class, 'llamarTurno'])->name('llamar.siguiente');
        Route::post('/finalizar', [FuncionarioLlamadoController::class, 'finalizarTurno'])->name('finalizar.turno');
    });


    Route::prefix('nueva')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicoAjaxController::class, 'vista'])->name('publico.ajax');
    Route::get('/turno-actual', [App\Http\Controllers\PublicoAjaxController::class, 'turnoActual']);
    Route::get('/turnos-actuales', [App\Http\Controllers\PublicoAjaxController::class, 'turnosActuales']);
});

// Ruta de soporte
Route::view('soporte', 'soporte.index')->name('soporte.index');
