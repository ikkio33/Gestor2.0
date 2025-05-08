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
    
    $mesonesDisponibles = Meson::doesntHave('usuario')->get();

    $mesonesOcupados = Meson::has('usuario')->get();
    
    return view('tu.vista', compact('mesonesDisponibles', 'mesonesOcupados'));
}
    public function seleccionar()
{
    
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
            'meson_id' => 'required|exists:meson,id',
        ]);

        $meson = Meson::where('id', $request->meson_id)
            ->where('disponible', true)
            ->firstOrFail();

        $user = Usuarios::find(Auth::id());
        if (!$user) {
            return redirect()->route('funcionario.meson.seleccionar')
                ->with('error', 'Usuario no encontrado.');
        }

        if ($previous = $meson->usuario) {
            if ($previous->id !== $user->id) {
                $previous->meson_id = null;
                $previous->save();
            }
        }

        $user->meson_id = $meson->id;
        $user->save();
        $meson->disponible = false;
        $meson->save();

        Session::put('meson_id', $meson->id);

        return redirect()->route('funcionario.meson.seleccionar')
            ->with('success', 'Mesón asignado correctamente.');
    }

    public function liberar(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|exists:meson,id',
        ]);

        $meson = Meson::findOrFail($request->meson_id);

        if ($user = $meson->usuario) {
            $user->meson_id = null;
            $user->save();

            $meson->disponible = true;
            $meson->save();

            return redirect()->route('funcionario.meson.seleccionar')
                ->with('success', 'Mesón liberado correctamente.');
        }

        return redirect()->route('funcionario.meson.seleccionar')
            ->with('error', 'Este mesón no tiene usuario asignado.');
    }
}

