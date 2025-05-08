<?php

namespace App\Http\Controllers\Admin;

use App\Models\Servicio;
use Illuminate\Http\Request;
use App\Models\Turno;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        // Recuperar los filtros
        $mes = $request->input('mes');
        $dia = $request->input('dia');

        // Construir la consulta base para los turnos
        $query = Turno::query();

        // Aplicar filtros si se proporcionan
        if ($mes) {
            $query->whereMonth('created_at', $mes);
        }

        if ($dia) {
            $query->whereDay('created_at', $dia);
        }

        // Si no hay filtros, se muestra solo lo de hoy
        if (!$mes && !$dia) {
            $query->whereDate('created_at', Carbon::today());
        }

        // Obtener los turnos con las relaciones necesarias
        $turnos = $query->with('servicio')->get();

        // Estadísticas
        $totalTurnos = $turnos->count();

        // Agrupar los turnos por servicio
        $turnosPorServicio = $turnos->groupBy('servicio_id')->map(function ($items, $servicio_id) {
            $servicio = Servicio::find($servicio_id);
            return [
                'servicio' => $servicio,
                'total' => $items->count(),
            ];
        })->values();

        // Promedio de espera
        $promedioEspera = $turnos->avg('tiempo_espera');

        // Obtener los meses y días disponibles en los turnos
        $meses = Turno::selectRaw('MONTH(created_at) as mes')
                      ->distinct()
                      ->pluck('mes');

        $dias = Turno::selectRaw('DAY(created_at) as dia')
                     ->distinct()
                     ->pluck('dia');

        // Devolver la vista con las estadísticas
        return view('Admin.Estadisticas.index', compact(
            'totalTurnos', 'turnosPorServicio', 'promedioEspera',
            'meses', 'dias', 'mes', 'dia'
        ));
    }
}
