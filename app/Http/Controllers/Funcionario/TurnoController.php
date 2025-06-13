<?php
//namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meson;
use Illuminate\Support\Facades\Auth;

class MesonController extends Controller
{
    // Devuelve mesón asignado y mesones disponibles
    public function disponibles()
    {
        $userId = Auth::id();

        $mesonAsignado = Meson::where('usuario_id', $userId)->first();

        // Los mesones disponibles son los que tienen disponible = true
        $mesonesDisponibles = Meson::where('disponible', true)->get();

        return response()->json([
            'meson_asignado' => $mesonAsignado,
            'mesones' => $mesonesDisponibles,
        ]);
    }

    // Asigna mesón al usuario, libera mesón anterior si existía
    public function asignar(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|exists:meson,id',
        ]);

        $user = Auth::user();
        // Ensure $user is an Eloquent model instance
        if (!($user instanceof \App\Models\User)) {
            $user = \App\Models\User::find(Auth::id());
        }
        $nuevoMeson = Meson::findOrFail($request->meson_id);

        if (!$nuevoMeson->disponible) {
            return response()->json(['error' => 'Este mesón ya está ocupado.'], 409);
        }

        // Liberar mesón anterior (si existe)
        if ($user->meson_id && $user->meson_id != $nuevoMeson->id) {
            $mesonAnterior = Meson::find($user->meson_id);
            if ($mesonAnterior) {
                $mesonAnterior->disponible = true;
                $mesonAnterior->usuario_id = null;
                $mesonAnterior->save();
            }
        }

        // Asignar nuevo mesón
        $nuevoMeson->disponible = false;
        $nuevoMeson->usuario_id = $user->id;
        $nuevoMeson->save();

        // Actualizar usuario también (opcional, si tienes columna meson_id)
        $user->meson_id = $nuevoMeson->id;
        if ($user instanceof \App\Models\User) {
            $user->save();
        } else {
            \App\Models\User::where('id', $user->id)->update(['meson_id' => $nuevoMeson->id]);
        }

        return response()->json(['success' => true, 'message' => 'Mesón asignado correctamente.']);
    }

    // Libera el mesón asignado al usuario
    public function liberar()
    {
        $user = Auth::user();

        if (!$user->meson_id) {
            return response()->json(['error' => 'No tienes mesón asignado.'], 404);
        }

        $meson = Meson::find($user->meson_id);
        if (!$meson) {
            return response()->json(['error' => 'Mesón no encontrado.'], 404);
        }

        $user->meson_id = null;
        if ($user instanceof \App\Models\User) {
            $user->save();
        } else {
            \App\Models\User::where('id', $user->id)->update(['meson_id' => null]);
        }
        $meson->save();

        $user->meson_id = null;
        if ($user instanceof \App\Models\User) {
            $user->save();
        } else {
            \App\Models\User::where('id', $user->id)->update(['meson_id' => null]);
        }

        return response()->json(['success' => true, 'message' => 'Mesón liberado correctamente.']);
    }
}
