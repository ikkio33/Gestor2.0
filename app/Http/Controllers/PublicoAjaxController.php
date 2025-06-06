<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;

class PublicoAjaxController extends Controller
{
    /**
     * Muestra la vista pública con AJAX para mostrar los turnos actuales en atención.
     */
    public function vista()
    {
        return view('publico.ajax.index');
    }

    /**
     * Devuelve en formato JSON los últimos 6 turnos que están en atención,
     * ordenados desde el más reciente al más antiguo (último llamado arriba).
     */
    public function turnosActuales()
    {
        // Obtener hasta 6 turnos en atención
        $turnos = Turno::with(['servicio', 'meson'])
            ->where('estado', 'atendiendo', now())
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        if ($turnos->isEmpty()) {
            return response()->json(['nuevo' => false]);
        }

        // Tomamos el turno actual (más reciente)
        $turnoActual = $turnos->first();

        // Contamos cuántos turnos pendientes quedan del mismo servicio
        $pendientesMismoServicio = Turno::where('estado', 'pendiente')
            ->where('servicio_id', $turnoActual->servicio_id)
            ->count();

        // Mapear los turnos
        $datosTurnos = $turnos->map(function ($turno) {
            return [
                'codigo' => $turno->codigo_turno,
                'servicio' => $turno->servicio->nombre ?? 'Servicio desconocido',
                'meson' => $turno->meson ? $turno->meson->nombre : 'Mesón no asignado',
            ];
        });

        return response()->json([
            'nuevo' => true,
            'turnos' => $datosTurnos,
            'pendientes_mismo_servicio' => $pendientesMismoServicio
        ]);
    }
}
