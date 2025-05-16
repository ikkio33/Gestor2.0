<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meson;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios;

class FuncionarioMesonController extends Controller
{
    public function index()
    {
        // Corregido nombres de variables para compact()
        $mesonesDisponibles = Meson::doesntHave('usuario')->get();
        $mesonesOcupados = Meson::has('usuario')->get();

        return view('tu.vista', compact('mesonesDisponibles', 'mesonesOcupados'));
    }

    public function seleccionar()
    {
        // Corregido nombres de variables para compact()
        $mesonesDisponibles = Meson::where('disponible', true)
            ->with('usuario.turnos.servicio')
            ->get();

        $mesonesOcupados = Meson::where('disponible', false)
            ->with('usuario.turnos.servicio')
            ->get();

        return view('funcionario.meson.seleccionar', compact('mesonesDisponibles', 'mesonesOcupados'));
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|exists:meson,id', // tabla singular
        ]);

        $meson = Meson::where('id', $request->meson_id)
            ->where('disponible', true)
            ->firstOrFail();

        $user = Usuarios::find(Auth::id());
        if (!$user) {
            return redirect()->route('funcionario.dashboard')
                ->with('error', 'Usuario no encontrado.');
        }

        // Si el usuario ya tiene otro mesón asignado, liberarlo primero
        if ($user->meson_id && $user->meson_id !== $meson->id) {
            $mesonAntiguo = Meson::find($user->meson_id);
            if ($mesonAntiguo) {
                $mesonAntiguo->disponible = true;
                $mesonAntiguo->save();
            }
        }

        // Asignar nuevo mesón al usuario
        $user->meson_id = $meson->id;
        $user->save();

        // Actualizar mesón para marcarlo como ocupado
        $meson->disponible = false;
        $meson->save();

        // Guardar en sesión
        Session::put('meson_id', $meson->id);

        return redirect()->route('funcionario.dashboard')
            ->with('success', 'Mesón asignado correctamente.');
    }

    public function liberar(Request $request)
    {
        $user = Usuarios::find(Auth::id());

        if (!$user || !$user->meson_id) {
            return redirect()->route('funcionario.dashboard')
                ->with('error', 'No tienes un mesón asignado para liberar.');
        }

        $meson = Meson::find($user->meson_id);
        if ($meson) {
            $meson->disponible = true;
            $meson->save();
        }

        $user->meson_id = null;
        $user->save();

        Session::forget('meson_id');

        return redirect()->route('funcionario.dashboard')
            ->with('success', 'Mesón liberado correctamente.');
    }
}
