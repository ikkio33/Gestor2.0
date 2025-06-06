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
use App\Http\Controllers\Funcionario\CentroMandoController;
use App\Http\Controllers\Funcionario\TurnoController as FuncionarioTurnoController;
use App\Http\Controllers\TurnoEstadoController;
use App\Http\Controllers\PublicoAjaxController;
use App\Http\Controllers\ApiTurnoController;

// Autenticación
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Vista pública
Route::get('/vista', [TurnoController::class, 'vista'])->name('vista.publica');
Route::get('/turno/seguimiento/{codigo}', [TurnoController::class, 'seguimiento'])->name('turno.seguimiento');
Route::get('/estado-turno/{codigo}', [TurnoEstadoController::class, 'estado'])->name('turno.estado');
Route::view('/gesnot/turnos', 'publico.turnos')->name('gesnot.turnos');
Route::get('/turno/{codigo}', [ApiTurnoController::class, 'mostrarPorCodigo']);
Route::get('turnos/{codigo}', [ApiTurnoController::class, 'mostrarPorCodigo']);
Route::get('/turno-actual-publico', [TurnoController::class, 'turnoActualPublico']);




// Totem
Route::get('/ingresar-rut', [TotemController::class, 'show'])->name('totem.show');
Route::post('/seleccionar', [TotemController::class, 'select'])->name('totem.select');
Route::post('/totem/confirmar', [TotemController::class, 'confirmar'])->name('totem.confirmar');
Route::get('/totem/confirmacion', [TotemController::class, 'confirmacion'])->name('totem.confirmacion');
Route::view('/totem', 'totem.totem')->name('totem');
Route::post('/funcionario/llamar-ajax', [TurnoController::class, 'llamarAjax'])->name('funcionario.llamar.ajax');

// Rutas protegidas - Administrador
Route::middleware(['auth', 'role:administrador'])
    ->prefix('Admin')
    ->name('Admin.')
    ->group(function () {
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

            // Llamado de turnos dentro del organizador (opcional)
            Route::get('/turnos-espera/ajax', [TurnoController::class, 'turnosEnEsperaAjax'])->name('turnos.ajax');
            Route::get('/turnos/ajax', [TurnoController::class, 'turnosEnEsperaAjax'])->name('funcionario.turnos.ajax');
            Route::post('/turnos/llamar-ajax', [TurnoController::class, 'llamarAjax'])->name('funcionario.llamar.ajax');
            // otras rutas...
        });
    });

// Rutas protegidas - Funcionario
Route::middleware(['auth'])
    ->prefix('funcionario')
    ->name('funcionario.')
    ->group(function () {
        // Ruta para mostrar la vista con mesones disponibles
        Route::get('/centro-mando', [CentroMandoController::class, 'index'])->name('centro-mando');

        // Ruta AJAX para obtener mesones disponibles
        Route::get('/mesones-disponibles', [CentroMandoController::class, 'mesonesDisponiblesAjax'])->name('centro-mando.mesones-disponibles');

        /*
        Route::post('/centro-mando/asignar-meson', [CentroMandoController::class, 'asignarMeson'])->name('centro-mando.asignarMeson');
        Route::post('/centro-mando/liberar-meson', [CentroMandoController::class, 'liberarMeson'])->name('centro-mando.liberarMeson');
        Route::get('/centro-mando/turnos-pendientes', [CentroMandoController::class, 'turnosPendientes'])->name('centro-mando.turnosPendientes');
        Route::post('/centro-mando/llamar-turno', [CentroMandoController::class, 'llamarTurno'])->name('centro-mando.llamarTurno');
        Route::post('/centro-mando/cancelar-turno', [CentroMandoController::class, 'cancelarTurno'])->name('centro-mando.cancelarTurno');
        Route::post('/centro-mando/terminar-atencion', [CentroMandoController::class, 'terminarAtencion'])->name('centro-mando.terminarAtencion');
        Route::post('/centro-mando/rellamar-turno', [CentroMandoController::class, 'rellenarTurno'])->name('centro-mando.rellamarTurno');
        */
    });




// Nueva vista pública con AJAX
Route::prefix('nueva')
    ->name('publico.ajax.')
    ->controller(PublicoAjaxController::class)
    ->group(function () {
        Route::get('/', 'vista')->name('vista');
        Route::get('/turno-actual', 'turnoActual')->name('turno.actual');
        Route::get('/turnos-actuales', 'turnosActuales')->name('turnos.actuales');
    });

// Ruta de soporte
Route::view('/soporte', 'soporte.index')->name('soporte.index');
