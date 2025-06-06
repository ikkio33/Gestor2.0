<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoEstadoController extends Controller
{
    /**
     * Devuelve el estado actual del turno dado un código único.
     *
     * @param  string  $codigo
     * @return \Illuminate\Http\JsonResponse
     */
    public function estado($codigo)
    {
        $turno = Turno::where('codigo_turno', $codigo)->first();

        // Si no existe o está finalizado
        if (!$turno || in_array($turno->estado, ['atendido', 'cancelado'])) {
            return response()->json([
                'mensaje' => 'Este turno no está disponible para seguimiento en este momento.',
            ], 410);
        }

        // Turnos delante del actual
        $turnosDelante = Turno::where('servicio_id', $turno->servicio_id)
            ->whereDate('created_at', now()->toDateString())
            ->where('numero_turno', '<', $turno->numero_turno)
            ->whereIn('estado', ['pendiente', 'espera', 'en_atencion'])
            ->orderBy('numero_turno')
            ->get();

        // Turno en atención (último llamado hoy del mismo servicio)
        $enAtencion = Turno::where('servicio_id', $turno->servicio_id)
            ->whereDate('created_at', now()->toDateString())
            ->where('estado', 'en_atencion')
            ->orderBy('updated_at', 'desc')
            ->first();

        return response()->json([
            'mi_turno' => $turno,
            'en_atencion' => $enAtencion,
            'cantidad_delante' => $turnosDelante->count(),
            'turnos_delante' => $turnosDelante,
        ]);
    }
}
