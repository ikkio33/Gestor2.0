<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiTurnoController extends Controller
{
    public function mostrarPorCodigo($codigo)
    {
        $zonaLocal = 'America/Santiago';
        $inicioDia = Carbon::now($zonaLocal)->startOfDay()->setTimezone('UTC');
        $finDia = Carbon::now($zonaLocal)->endOfDay()->setTimezone('UTC');

        $turnoActual = Turno::with(['servicio', 'meson'])
            ->where('codigo_turno', $codigo)
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->first();

        if (! $turnoActual) {
            return response()->json([
                'error' => 'Turno no encontrado para hoy con el código: ' . $codigo,
            ], 404);
        }

        $mensaje = match ($turnoActual->estado) {
            'cancelado'  => 'Este turno ha sido cancelado.',
            'atendido'   => 'Este turno ya fue atendido.',
            'atendiendo' => 'Este turno está siendo llamado. Por favor dirígete al mesón.',
            'pendiente'  => 'Este turno está pendiente de ser llamado.',
            default      => 'Estado del turno desconocido.',
        };

        $letra = $turnoActual->servicio->letra ?? '';
        $numero = $turnoActual->numero_turno ?? 0;
        $codigoTurno = $letra . str_pad($numero, 2, '0', STR_PAD_LEFT);

        if ($turnoActual->estado !== 'pendiente') {
            return response()->json([
                'mi_turno' => [
                    'id' => $turnoActual->id,
                    'codigo_turno' => $codigoTurno,
                    'estado' => $turnoActual->estado,
                    'mensaje' => $mensaje,
                    
                    'servicio' => [
                        'id' => $turnoActual->servicio->id,
                        'nombre' => $turnoActual->servicio->nombre,
                        'letra' => $letra,
                    ],
                ],
                'turnos_anteriores_pendientes' => [],
                'total_pendientes_mismo_servicio' => 0,
                'turnos_en_atencion' => [],
            ]);
        }

        // Para turno pendiente
        $turnosAnteriores = Turno::with('servicio')
            ->where('estado', Turno::ESTADO_PENDIENTE)
            ->where('servicio_id', $turnoActual->servicio_id)
            ->where('numero_turno', '<', $numero)
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->orderBy('numero_turno')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'codigo_turno' => $t->servicio->letra . str_pad($t->numero_turno, 2, '0', STR_PAD_LEFT),
                'estado' => $t->estado,
                'created_at' => $t->created_at->toDateTimeString(),
                'servicio' => [
                    'nombre' => $t->servicio->nombre,
                    'letra' => $t->servicio->letra,
                ],
            ]);

        $totalPendientes = Turno::where('estado', Turno::ESTADO_PENDIENTE)
            ->where('servicio_id', $turnoActual->servicio_id)
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->count();

        $turnosEnAtencion = Turno::with(['servicio', 'meson'])
            ->where('estado', 'atendiendo')
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->whereHas('servicio', fn($q) => $q->where('letra', $letra))
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'codigo_turno' => $t->servicio->letra . str_pad($t->numero_turno, 2, '0', STR_PAD_LEFT),
                'estado' => $t->estado,
                'servicio' => [
                    'nombre' => $t->servicio->nombre,
                    'letra' => $t->servicio->letra,
                ],
                'meson' => [
                    'nombre' => $t->meson->nombre ?? 'Sin mesón',
                ],
            ]);

        return response()->json([
            'mi_turno' => [
                'id' => $turnoActual->id,
                'codigo_turno' => $codigoTurno,
                'estado' => $turnoActual->estado,
                'mensaje' => $mensaje,
                'servicio' => [
                    'id' => $turnoActual->servicio->id,
                    'nombre' => $turnoActual->servicio->nombre,
                    'letra' => $letra,
                ],
            ],
            'turnos_anteriores_pendientes' => $turnosAnteriores,
            'total_pendientes_mismo_servicio' => $totalPendientes,
            'turnos_en_atencion' => $turnosEnAtencion,
        ]);
    }


    public function turnoActual()
    {
        $inicioDia = Carbon::now()->startOfDay();
        $finDia = Carbon::now()->endOfDay();

        $turnoActual = Turno::with('servicio')
            ->where('estado', 'atendiendo')
            ->whereBetween('created_at', [$inicioDia, $finDia])
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
        $inicioDia = Carbon::now()->startOfDay();
        $finDia = Carbon::now()->endOfDay();

        $turnos = Turno::with('servicio')
            ->where('estado', 'pendiente')
            ->whereBetween('created_at', [$inicioDia, $finDia])
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
        $inicioDia = Carbon::now()->startOfDay();
        $finDia = Carbon::now()->endOfDay();

        $turno = Turno::with('servicio')
            ->where('codigo_turno', $codigo)
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no encontrado'], 404);
        }

        $letra = $turno->servicio->letra;
        $numero = $turno->numero_turno;

        $cantidadAntes = Turno::where('estado', 'pendiente')
            ->whereHas('servicio', fn($q) => $q->where('letra', $letra))
            ->where('numero_turno', '<', $numero)
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->count();

        return response()->json([
            'turno' => $codigo,
            'cantidad_antes' => $cantidadAntes,
        ]);
    }
}
