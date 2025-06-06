<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;

class ApiTurnoController extends Controller
{
    public function mostrarPorCodigo($codigo)
    {
        $turnoActual = Turno::with('servicio')
            ->where('codigo_turno', $codigo)
            ->first();

        if (! $turnoActual) {
            return response()->json([
                'error' => 'Turno no encontrado.'
            ], 404);
        }

        $letra = $turnoActual->servicio->letra ?? '';
        $numero = $turnoActual->numero_turno ?? 0;

        $codigoTurno = $letra . str_pad($numero, 2, '0', STR_PAD_LEFT);

        // Turnos anteriores pendientes (misma letra y número menor)
        $turnosAnteriores = Turno::with('servicio')
            ->where('estado', Turno::ESTADO_PENDIENTE)
            ->whereHas('servicio', fn($q) => $q->where('letra', $letra))
            ->where('numero_turno', '<', $numero)
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('numero_turno')
            ->get()
            ->map(fn($t) => [
                'codigo_turno' => $t->servicio->letra . str_pad($t->numero_turno, 2, '0', STR_PAD_LEFT),
                'estado' => $t->estado,
                'created_at' => $t->created_at->toDateTimeString(),
            ]);

        // Total pendientes mismo servicio
        $totalPendientes = Turno::where('estado', Turno::ESTADO_PENDIENTE)
            ->where('servicio_id', $turnoActual->servicio_id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Turnos en atención con misma letra
        $turnosEnAtencion = Turno::with(['servicio', 'meson'])
            ->where('estado', 'atendiendo')
            ->whereDate('created_at', now()->toDateString())
            ->whereHas('servicio', fn($q) => $q->where('letra', $letra))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($t) => [
                'codigo_turno' => $t->servicio->letra . str_pad($t->numero_turno, 2, '0', STR_PAD_LEFT),
                'estado' => $t->estado,
                'servicio' => ['nombre' => $t->servicio->nombre],
                'meson' => ['nombre' => $t->meson->nombre ?? 'Sin mesón'],
            ]);

        return response()->json([
            'turno_actual' => [
                'codigo_turno' => $codigoTurno,
                'estado' => $turnoActual->estado,
                'servicio' => $turnoActual->servicio->nombre ?? 'Desconocido',
                'numero_turno' => $numero,
                'created_at' => $turnoActual->created_at->toDateTimeString(),
            ],
            'turnos_anteriores_pendientes' => $turnosAnteriores,
            'total_pendientes_mismo_servicio' => $totalPendientes,
            'turnos_en_atencion' => $turnosEnAtencion,
        ]);
    }
    public function turnoActual()
    {
        $turnoActual = Turno::with('servicio')
            ->where('estado', 'atendiendo') // o el estado que uses para turno en atención
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('updated_at', 'desc')
            ->first();

        if (! $turnoActual) {
            return response()->json(['mi_turno' => null]);
        }

        return response()->json(['mi_turno' => [
            'letra' => $turnoActual->servicio->letra,
            'numero' => $turnoActual->numero_turno,
            'servicio' => [
                'nombre' => $turnoActual->servicio->nombre,
            ],
            'estado' => $turnoActual->estado,
        ]]);
    }

    public function turnosEnEspera()
    {
        $turnos = Turno::with('servicio')
            ->where('estado', 'pendiente')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('numero_turno')
            ->get();

        $result = $turnos->map(function ($turno) {
            return [
                'letra' => $turno->servicio->letra,
                'numero' => $turno->numero_turno,
                'servicio' => [
                    'nombre' => $turno->servicio->nombre,
                ],
                'estado' => $turno->estado,
            ];
        });

        return response()->json($result);
    }

    public function cantidadTurnosAntes($codigo)
    {
        $turno = Turno::with('servicio')->where('codigo_turno', $codigo)->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no encontrado'], 404);
        }

        $letra = $turno->servicio->letra;
        $numero = $turno->numero_turno;

        $cantidadAntes = Turno::where('estado', 'pendiente')
            ->whereHas('servicio', fn($q) => $q->where('letra', $letra))
            ->where('numero_turno', '<', $numero)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return response()->json([
            'turno' => $codigo,
            'cantidad_antes' => $cantidadAntes,
        ]);
    }
}
