<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turno;
use Illuminate\Support\Facades\Session;

class TurnoController extends Controller
{
    public function vistaLlamada()
    {
        $meson_id = Session::get('meson_id');

        if (!$meson_id) {
            return redirect()->route('funcionario.meson.seleccionar')->with('error', 'Primero debes seleccionar un mesón.');
        }

        $turno_actual = Turno::where('meson_id', $meson_id)
            ->where('estado', 'atendiendo')
            ->first();

        $siguiente_turno = Turno::where('estado', 'pendiente') // asegurate de usar "pendiente" en vez de "esperando"
            ->orderBy('created_at')
            ->first();

        return view('funcionario.meson.llamada', compact('turno_actual', 'siguiente_turno'));
    }

    public function llamar(Request $request)
    {
        $meson_id = Session::get('meson_id');

        if (!$meson_id) {
            return redirect()->route('funcionario.meson.seleccionar')->with('error', 'Primero debes seleccionar un mesón.');
        }

        // Terminar turno anterior si existe
        $turno_actual = Turno::where('meson_id', $meson_id)
            ->where('estado', 'atendiendo')
            ->first();

        if ($turno_actual) {
            $turno_actual->estado = 'atendido'; // mismo estado que el resto del sistema
            $turno_actual->save();
        }

        // Llamar al siguiente turno
        $turno = Turno::findOrFail($request->input('turno_id'));
        $turno->estado = 'atendiendo';
        $turno->meson_id = $meson_id;
        $turno->save();

        return redirect()->route('funcionario.meson.llamada')->with('success', 'Turno en atención.');
    }

    public function llamarAjax(Request $request)
{
    $meson_id = Session::get('meson_id');

    if (!$meson_id) {
        return response()->json(['error' => 'Mesón no asignado'], 400);
    }

    // Terminar turno anterior si existe
    $turno_actual = Turno::where('meson_id', $meson_id)
        ->where('estado', 'atendiendo')
        ->first();

    if ($turno_actual) {
        $turno_actual->estado = 'atendido';
        $turno_actual->save();
    }

    // Llamar al siguiente turno
    $turno = Turno::findOrFail($request->input('turno_id'));
    $turno->estado = 'atendiendo';
    $turno->meson_id = $meson_id;
    $turno->save();

    return response()->json([
        'success' => true,
        'turno' => $turno
    ]);
}

}
