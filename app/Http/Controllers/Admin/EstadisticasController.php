<?php

namespace App\Http\Controllers\Admin;

use App\Models\Servicio;
use App\Models\Turno;
use App\Models\Usuarios;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        // Mostrar solo usuarios que son funcionarios (ajusta el filtro según tu sistema)
        $funcionarios = Usuarios::where('rol', 'funcionario')->orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();

        return view('Admin.Estadisticas.index', compact('funcionarios', 'servicios'));
    }

    public function obtenerEstadisticasAjax(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $usuariosIds = $request->input('usuarios_id'); // puede ser null o array
        $serviciosIds = $request->input('servicios_id');
        $hora = $request->input('hora');

        $query = Turno::query();

        // Filtrado por fechas
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('created_at', [
                Carbon::parse($fechaDesde)->startOfDay(),
                Carbon::parse($fechaHasta)->endOfDay()
            ]);
        } elseif ($fechaDesde) {
            $query->where('created_at', '>=', Carbon::parse($fechaDesde)->startOfDay());
        } elseif ($fechaHasta) {
            $query->where('created_at', '<=', Carbon::parse($fechaHasta)->endOfDay());
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        // Si vienen funcionarios seleccionados explícitamente
        if (is_array($usuariosIds) && count($usuariosIds) > 0) {
            $query->whereIn('usuario_id', $usuariosIds);
        } else {
            // Si no se seleccionó ninguno, filtrar solo por los usuarios que son funcionarios
            $funcionarioIds = Usuarios::where('rol', 'funcionario')->pluck('id');
            $query->whereIn('usuario_id', $funcionarioIds);
        }

        // Filtrado por servicios
        if (is_array($serviciosIds) && count($serviciosIds) > 0) {
            $query->whereIn('servicio_id', $serviciosIds);
        }

        // Filtrado por hora (si viene)
        if ($hora !== null && $hora !== '') {
            $horaInt = (int)$hora;
            $inicio = str_pad($horaInt, 2, '0', STR_PAD_LEFT) . ':00:00';
            $fin = str_pad($horaInt + 1, 2, '0', STR_PAD_LEFT) . ':00:00';
            $query->whereTime('created_at', '>=', $inicio)
                ->whereTime('created_at', '<', $fin);
        }

        // Obtener los turnos con relaciones
        $turnos = $query->with(['servicio', 'usuario'])->get();

        $totalTurnos = $turnos->count();
        $promedioEspera = $turnos->avg('tiempo_espera');

        // Agrupaciones
        $porFuncionario = $turnos->groupBy('usuario_id')->map(function ($items) {
            $usuario = $items->first()->usuario;
            return [
                'funcionario' => $usuario->nombre ?? 'Desconocido',
                'total' => $items->count(),
                'espera_promedio' => round($items->avg('tiempo_espera'), 2),
                'atendidos' => $items->where('estado', 'atendido')->count(),
                'cancelados' => $items->where('estado', 'cancelado')->count(),
            ];
        })->values();

        $porServicio = $turnos->groupBy('servicio_id')->map(function ($items) {
            $servicio = $items->first()->servicio;
            return [
                'servicio' => $servicio->nombre ?? 'Desconocido',
                'total' => $items->count(),
                'espera_promedio' => round($items->avg('tiempo_espera'), 2),
                'atendidos' => $items->where('estado', 'atendido')->count(),
                'cancelados' => $items->where('estado', 'cancelado')->count(),
            ];
        })->values();

        $porFecha = $turnos->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        })->map(function ($items, $key) {
            return [
                'fecha' => $key,
                'total' => $items->count(),
                'espera_promedio' => round($items->avg('tiempo_espera'), 2),
                'atendidos' => $items->where('estado', 'atendido')->count(),
                'cancelados' => $items->where('estado', 'cancelado')->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'totalTurnos' => $totalTurnos,
            'promedioEspera' => round($promedioEspera, 2),
            'comparacion_funcionarios' => $porFuncionario,
            'comparacion_servicios' => $porServicio,
            'comparacion_fechas' => $porFecha,
        ]);
    }
}
