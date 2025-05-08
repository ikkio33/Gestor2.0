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
        $mesonId = Session::get('meson_id');

        if (!$mesonId) {
            return redirect()->route('funcionario.meson.seleccionar')->with('error', 'Primero debes seleccionar un mesón.');
        }

        $turnoActual = Turno::where('meson_id', $mesonId)->where('estado', 'atendiendo')->first();
        $siguienteTurno = Turno::where('meson_id', $mesonId)->where('estado', 'esperando')->orderBy('created_at')->first();

        return view('funcionario.meson.llamada', compact('turnoActual', 'siguienteTurno'));
    }

    public function llamar(Request $request)
    {
        $mesonId = Session::get('meson_id');

        if (!$mesonId) {
            return redirect()->route('funcionario.meson.seleccionar')->with('error', 'Primero debes seleccionar un mesón.');
        }

        // Terminar turno anterior si existe
        $turnoActual = Turno::where('meson_id', $mesonId)->where('estado', 'atendiendo')->first();
        if ($turnoActual) {
            $turnoActual->estado = 'terminado';
            $turnoActual->save();
        }

        // Llamar al siguiente turno
        $turno = Turno::findOrFail($request->input('turno_id'));
        $turno->estado = 'atendiendo';
        $turno->meson_id = $mesonId;
        $turno->save();

        return redirect()->route('funcionario.meson.llamada')->with('success', 'Turno en atención.');
    }
}
