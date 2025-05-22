<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meson;
use Illuminate\Support\Facades\Auth;

class MesonController extends Controller
{
    public function disponibles()
    {
        $mesones = Meson::whereNull('usuario_id')->get();
        return response()->json($mesones);
    }

    public function ocupados()
    {
        $mesones = Meson::whereNotNull('usuario_id')->with('usuario')->get();
        return response()->json($mesones);
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|exists:mesones,id',
        ]);

        $meson = Meson::findOrFail($request->meson_id);

        if ($meson->usuario_id) {
            return response()->json(['message' => 'Mesón ya está ocupado'], 409);
        }

        $meson->usuario_id = Auth::id();
        $meson->save();

        return response()->json(['message' => 'Mesón asignado correctamente']);
    }

    public function liberar(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|exists:mesones,id',
        ]);

        $meson = Meson::findOrFail($request->meson_id);

        if ($meson->usuario_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado para liberar este mesón'], 403);
        }

        $meson->usuario_id = null;
        $meson->save();

        return response()->json(['message' => 'Mesón liberado correctamente']);
    }
}
