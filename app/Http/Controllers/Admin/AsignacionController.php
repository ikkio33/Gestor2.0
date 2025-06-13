<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuarios;
use App\Models\Meson;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function index()
    {
        // Funcionarios sin meson asignado
        $funcionariosSinMeson = Usuarios::where('rol', 'funcionario')
            ->whereNull('meson_id')
            ->get();

        // Funcionarios con meson asignado (y cargamos meson)
        $funcionariosConMeson = Usuarios::where('rol', 'funcionario')
            ->whereNotNull('meson_id')
            ->with('meson')
            ->get();

        // Mesones disponibles (sin funcionario asignado)
        $mesones = Meson::whereNull('funcionario_id')->get();

        return view('admin.asignaciones.index', compact(
            'funcionariosSinMeson',
            'funcionariosConMeson',
            'mesones'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'meson_id' => 'required|exists:meson,id',
        ]);

        $usuario = Usuarios::findOrFail($request->usuario_id);
        $meson = Meson::findOrFail($request->meson_id);

        // Validar que el funcionario y meson estén libres
        if ($usuario->meson_id !== null) {
            return redirect()->back()->withErrors(['usuario_id' => 'El funcionario ya tiene un mesón asignado.']);
        }
        if ($meson->funcionario_id !== null) {
            return redirect()->back()->withErrors(['meson_id' => 'El mesón ya está asignado a otro funcionario.']);
        }

        // Asignar la relación mutua
        $usuario->meson_id = $meson->id;
        $usuario->save();

        $meson->funcionario_id = $usuario->id;
        $meson->save();

        return redirect()->back()->with('success', 'Funcionario asignado correctamente.');
    }

    public function liberar(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
        ]);

        $usuario = Usuarios::findOrFail($request->usuario_id);

        if ($usuario->meson_id !== null) {
            $meson = Meson::find($usuario->meson_id);
            if ($meson) {
                $meson->funcionario_id = null;
                $meson->save();
            }

            $usuario->meson_id = null;
            $usuario->save();
        }

        return redirect()->back()->with('success', 'Mesón liberado correctamente.');
    }
}
