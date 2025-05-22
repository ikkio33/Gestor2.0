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
        // Obtener hasta 6 turnos en estado 'atendiendo', ordenados por actualización descendente
        $turnos = Turno::with(['servicio', 'meson'])
            ->where('estado', 'atendiendo')
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        if ($turnos->isEmpty()) {
            // No hay turnos atendiendo, indicamos que no hay datos nuevos
            return response()->json(['nuevo' => false]);
        }

        // Mapear los turnos para solo enviar los datos necesarios
        $datosTurnos = $turnos->map(function ($turno) {
            return [
                'codigo' => $turno->codigo_turno,
                'servicio' => $turno->servicio->nombre ?? 'Servicio desconocido',
                'meson' => $turno->meson ? $turno->meson->nombre : 'Mesón no asignado',
            ];
        });

        // Devolvemos la lista de turnos y confirmamos que hay datos nuevos
        return response()->json([
            'nuevo' => true,
            'turnos' => $datosTurnos,
        ]);
    }
}
