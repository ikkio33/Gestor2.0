<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meson;
use App\Models\Turno;
use App\Models\Usuarios;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'meson_id' => 'required|exists:meson,id',
            ]);

            $user = Usuarios::find(Auth::id());
            $meson = Meson::findOrFail($request->meson_id);

            // Verificar si el mesón está disponible
            if ($meson->disponible === false) {
                return redirect()->route('funcionario.dashboard')
                    ->with('error', 'Este mesón ya está ocupado.');
            }

            // Liberar mesón anterior si el usuario ya tenía uno
            if ($user->meson_id && $user->meson_id != $meson->id) {
                $mesonAnterior = Meson::find($user->meson_id);
                if ($mesonAnterior) {
                    $mesonAnterior->disponible = true;
                    $mesonAnterior->save();
                }
            }

            // Asignar nuevo mesón
            $user->meson_id = $meson->id;
            $user->save();

            $meson->disponible = false;
            $meson->save();

            session(['meson_id' => $meson->id]);

            return redirect()->route('funcionario.dashboard')
                ->with('success', 'Mesón asignado correctamente.');
        }

        // --- GET ---
        $meson_id = session('meson_id');
        $meson = null;

        if ($meson_id) {
            $meson = Meson::find($meson_id);
            if (!$meson) {
                session()->forget('meson_id');
            }
        }

        $turno_actual = null;
        $turnos_espera = collect();
        $turnos_atendidos = collect();

        $mesones_disponibles = Meson::where('disponible', true)->get();
        $mesones_ocupados = Meson::where('disponible', false)->get();

        if ($meson) {
            $servicio_ids = $meson->servicios->pluck('id')->toArray();

            $turno_actual = Turno::where('estado', 'atendiendo')
                ->where('meson_id', $meson->id)
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
                ->where('meson_id', $meson->id)
                ->whereDate('created_at', now())
                ->orderByDesc('updated_at')
                ->take(6)
                ->get()
                ->map(function ($t) {
                    $t->minutos_atencion = $t->created_at->diffInMinutes($t->updated_at);
                    return $t;
                });
        }

        return view('funcionario.dashboard', [
            'meson' => $meson,
            'turno_actual' => $turno_actual,
            'turnos_espera' => $turnos_espera,
            'turnos_atendidos' => $turnos_atendidos,
            'mesones_disponibles' => $mesones_disponibles,
            'mesones_ocupados' => $mesones_ocupados,
        ]);
    }

    public function llamarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
        ]);

        $meson_id = session('meson_id');

        if (!$meson_id) {
            return redirect()->route('funcionario.dashboard')
                ->with('error', 'Primero debes seleccionar un mesón.');
        }

        // Finalizar turno actual si existe
        $turno_actual = Turno::where('meson_id', $meson_id)
            ->where('estado', 'atendiendo')
            ->first();

        if ($turno_actual) {
            $turno_actual->estado = 'atendido';
            $turno_actual->save();
        }

        // Llamar al siguiente turno
        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendiendo';
        $turno->meson_id = $meson_id;
        $turno->save();

        return redirect()->route('funcionario.dashboard')
            ->with('success', 'Turno en atención.');
    }

    public function finalizarTurno(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
        ]);

        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendido';
        $turno->save();

        return redirect()->route('funcionario.dashboard')
            ->with('success', 'Turno finalizado correctamente.');
    }
}
