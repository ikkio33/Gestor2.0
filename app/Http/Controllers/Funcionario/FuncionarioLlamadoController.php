<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Meson;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FuncionarioLlamadoController extends Controller
{
    public function index(Request $request)
    {
        $meson_id = session('meson_id');
        if (!$meson_id) return redirect()->route('funcionario.meson.seleccionar');

        $meson = Meson::findOrFail($meson_id);
        $servicio_ids = $meson->servicios->pluck('id')->toArray();

        $turno_actual = Turno::where('estado', 'atendiendo')
            ->where('meson_id', $meson_id)
            ->whereDate('created_at', now())
            ->latest('updated_at')
            ->first();

        $turnos_espera = Turno::where('estado', 'pendiente')
            ->whereIn('servicio_id', $servicio_ids)
            ->whereDate('created_at', now())
            ->orderBy('created_at')
            ->take(6)
            ->get()
            ->map(function ($t) {
                $t->minutos_espera = $t->created_at->diffInMinutes(now());
                return $t;
            });

        $turnos_atendidos = Turno::where('estado', 'atendido')
            ->whereIn('servicio_id', $servicio_ids)
            ->where('meson_id', $meson_id)
            ->whereDate('created_at', now())
            ->orderByDesc('updated_at')
            ->take(6)
            ->get()
            ->map(function ($t) {
                $t->minutos_atencion = $t->created_at->diffInMinutes($t->updated_at);
                return $t;
            });

        $mesones_disponibles = Meson::whereNull('usuario_id')->get();

        return view('funcionario.dashboard', compact(
            'meson',
            'turno_actual',
            'turnos_espera',
            'turnos_atendidos',
            'mesones_disponibles'
        ));
    }

    public function llamarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
        ]);

        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendiendo';
        $turno->meson_id = session('meson_id');
        $turno->updated_at = now();
        $turno->save();

        return redirect()->route('funcionario.dashboard');
    }

    public function finalizarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
        ]);

        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendido';
        $turno->updated_at = now();
        $turno->save();

        return redirect()->route('funcionario.dashboard');
    }
}
