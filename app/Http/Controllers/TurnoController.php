<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TurnoController extends Controller
{
    public function vista()
    {
        $ultimoTurnoArchivo = 'ultimo_turno.txt';
        $ultimoTurnoAnterior = Storage::exists($ultimoTurnoArchivo) ? Storage::get($ultimoTurnoArchivo) : null;

        $turnos = Turno::with(['servicio', 'meson'])
            ->where('estado', 'atendiendo')
            ->whereDate('created_at', now())
            ->latest('created_at')
            ->take(8)
            ->get();

        $codigoActual = $turnos->first()->codigo_turno ?? null;
        $mesonActual = $turnos->first()->meson->nombre ?? null;
        $nuevoTurno = $codigoActual && $codigoActual !== $ultimoTurnoAnterior;

        if ($codigoActual) {
            Storage::put($ultimoTurnoArchivo, $codigoActual);
        }

        return view('vista', compact('turnos', 'nuevoTurno', 'codigoActual', 'mesonActual'));
    }

    public function seguimiento($codigo)
    {
        $turno = Turno::where('codigo_seguimiento', $codigo)->firstOrFail();

        $turno_antes = Turno::where('estado', 'esperando')
            ->where('created_at', '<', $turno->created_at)
            ->count();

        return view('turno.seguimiento', compact('turno', 'turno_antes'));
    }
    public function estado($codigo)
    {
        $turno = DB::table('turnos')
            ->where('codigo_turno', $codigo)
            ->first();

        if (!$turno) {
            abort(404, 'Turno no encontrado');
        }

        if (!in_array($turno->estado, ['pendiente', 'llamado'])) {
            // Si ya fue atendido, muestra vista expirado o mensaje
            return response()->view('turno.expirado', ['codigo' => $codigo], 410);
        }

        // Pasa solo el código del turno a la vista para evitar problemas
        return view('turno.estado', ['codigo' => $turno->codigo_turno]);
    }
}
