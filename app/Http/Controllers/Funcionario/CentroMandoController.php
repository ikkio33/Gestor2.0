<?php

namespace App\Http\Controllers\Funcionario;

use Illuminate\Http\Request;
use App\Models\Meson;
use App\Models\Turno;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CentroMandoController extends Controller
{
    // Mostrar vista con mesones disponibles
    public function index()
    {
        // Obtener mesones disponibles
        $mesonesDisponibles = Meson::whereIn('estado', ['libre', 'disponible'])->get();

        return view('funcionario.centro-mando', compact('mesonesDisponibles'));
    }

    // AJAX: Obtener mesones disponibles (solo para la llamada AJAX)
    public function mesonesDisponiblesAjax()
    {
        $mesonesDisponibles = Meson::whereIn('estado', ['libre', 'disponible'])->get();

        // Nota: clave 'mesonesDisponibles' para que coincida con el JS
        return response()->json(['mesonesDisponibles' => $mesonesDisponibles]);
    }


    /*
    // AJAX: Asignar meson al usuario actual
    public function asignarMeson(Request $request)
    {
        $usuario = Auth::user();
        $meson = Meson::where('id', $request->meson_id)->where('estado', 'disponible')->first();

        if (!$meson) {
            return response()->json(['error' => 'Mesón no disponible'], 400);
        }

        $meson->estado = 'asignado';
        $meson->funcionario_id = $usuario->id;
        $meson->save();

        return response()->json(['success' => true, 'meson' => $meson]);
    }

    // AJAX: Liberar meson asignado
    public function liberarMeson(Request $request)
    {
        $usuario = Auth::user();
        $meson = Meson::where('id', $request->meson_id)->where('funcionario_id', $usuario->id)->first();

        if (!$meson) {
            return response()->json(['error' => 'Mesón no asignado a este usuario'], 400);
        }

        $meson->estado = 'disponible';
        $meson->usuario_asignado = null;
        $meson->save();

        return response()->json(['success' => true]);
    }

    // AJAX: Obtener turnos pendientes para los mesones asignados (máx 3 por código_servicio)
    public function turnosPendientes()
    {
        $usuario = Auth::user();
        $mesonesAsignados = Meson::where('funcionario_id', $usuario->id)->pluck('nombre');

        $turnos = Turno::whereIn('meson_asignado', $mesonesAsignados)
            ->where('estado', 'pendiente')
            ->orderBy('created_at')
            ->get()
            ->groupBy('codigo_servicio')
            ->map(function ($group) {
                return $group->take(3);
            });

        return response()->json(['turnos' => $turnos]);
    }

    // AJAX: Llamar turno (pasar a 'atendiendo')
    public function llamarTurno(Request $request)
    {
        $turno = Turno::where('id', $request->turno_id)->where('estado', 'pendiente')->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no disponible para llamar'], 400);
        }

        $turno->estado = 'atendiendo';
        $turno->save();

        return response()->json(['success' => true, 'turno' => $turno]);
    }

    // AJAX: Cancelar turno (pasar a 'abandono')
    public function cancelarTurno(Request $request)
    {
        $turno = Turno::where('id', $request->turno_id)->whereIn('estado', ['pendiente', 'atendiendo'])->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no válido para cancelar'], 400);
        }

        $turno->estado = 'abandono';
        $turno->save();

        return response()->json(['success' => true]);
    }

    // AJAX: Terminar atención (liberar turno atendiendo)
    public function terminarAtencion(Request $request)
    {
        $turno = Turno::where('id', $request->turno_id)->where('estado', 'atendiendo')->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no está en atención'], 400);
        }

        $turno->estado = 'finalizado';
        $turno->save();

        return response()->json(['success' => true]);
    }

    // AJAX: Re-llamar turno (quizás para alertar al cliente otra vez)
    public function rellenarTurno(Request $request)
    {
        $turno = Turno::where('id', $request->turno_id)->where('estado', 'atendiendo')->first();

        if (!$turno) {
            return response()->json(['error' => 'Turno no está en atención'], 400);
        }

        // Aquí puedes lanzar notificaciones, sonidos, o lo que se necesite para el re-llamado

        return response()->json(['success' => true]);
    }

    // Liberar meson al cerrar sesión (hook logout)
    public function liberarMesonLogout()
    {
        $usuario = Auth::user();

        Meson::where('funcionario_id', $usuario->id)
            ->update(['estado' => 'disponible', 'usuario_asignado' => null]);
    }
    */
}
