<?php

namespace App\Http\Controllers\Admin;

use App\Models\Servicio;
use App\Models\Materia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrganizadorController extends Controller
{
    public function index()
    {
        $servicios = Servicio::with('materias')->orderBy('letra')->get();
        return view('Admin.Organizador.index', compact('servicios'));
    }

    public function storeServicio(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);

        $letrasDisponibles = array_diff(range('A', 'Z'), ['O', 'I']); 
        $letrasUsadas = Servicio::pluck('letra')->toArray();

        $letraLibre = collect($letrasDisponibles)->first(function ($letra) use ($letrasUsadas) {
            return !in_array($letra, $letrasUsadas);
        });

        if (!$letraLibre) {
            return redirect()->back()->with('error', 'No hay letras disponibles para asignar al nuevo servicio.');
        }

        Servicio::create([
            'nombre' => $request->nombre,
            'letra' => $letraLibre
        ]);

        return redirect()->back()->with('success', 'Servicio creado correctamente.');
    }

    public function storeMateria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'servicio_id' => 'required|exists:servicios,id'
        ]);

        Materia::create($request->only('nombre', 'servicio_id'));

        return redirect()->back()->with('success', 'Materia agregada correctamente.');
    }

    public function deleteServicio($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->materias()->delete();
        $servicio->delete();

        return redirect()->back()->with('success', 'Servicio eliminado correctamente.');
    }

    public function deleteMateria($id)
    {
        Materia::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Materia eliminada correctamente.');
    }

    public function moverMateria(Request $request)
    {
        $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'servicio_id' => 'required|exists:servicios,id'
        ]);

        $materia = Materia::findOrFail($request->materia_id);

        if ($materia->servicio_id == $request->servicio_id) {
            return response()->json(['success' => true]);
        }

        $materia->servicio_id = $request->servicio_id;
        $materia->save();

        return response()->json(['success' => true]);
    }
}
