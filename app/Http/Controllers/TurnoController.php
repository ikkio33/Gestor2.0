<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Support\Facades\Storage;

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
}
