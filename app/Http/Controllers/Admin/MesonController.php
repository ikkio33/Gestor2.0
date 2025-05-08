<?php

namespace App\Http\Controllers\Admin;

use App\Models\Meson;
use App\Models\Servicio;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MesonController extends Controller
{
    public function index(Request $request)
    {
        $disponibleFiltro = $request->input('disponible');
        $servicioFiltro = $request->input('servicio');

        $query = Meson::query()
            ->leftJoin('meson_servicio', 'meson.id', '=', 'meson_servicio.meson_id')
            ->leftJoin('servicios', 'servicios.id', '=', 'meson_servicio.servicio_id')
            ->select('meson.id', 'meson.nombre', 'meson.estado', 'meson.disponible')
            ->selectRaw('GROUP_CONCAT(servicios.nombre SEPARATOR ", ") AS servicios')
            ->groupBy('meson.id', 'meson.nombre', 'meson.estado', 'meson.disponible');

        if ($disponibleFiltro !== null && $disponibleFiltro !== '') {
            $query->where('meson.disponible', $disponibleFiltro);
        }

        if ($servicioFiltro !== null && $servicioFiltro !== '') {
            $query->where('servicios.nombre', 'LIKE', "%{$servicioFiltro}%");
        }

        $mesones = $query->paginate(10)->withQueryString();
        $serviciosDisponibles = Servicio::all();

        return view('Admin.Mesones.index', compact('mesones', 'disponibleFiltro', 'servicioFiltro', 'serviciosDisponibles'));
    }

    public function create()
    {
        return view('Admin.Mesones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'nullable|string|max:50',
            'disponible' => 'nullable|boolean',
            'servicios' => 'nullable|array'
        ]);

        $meson = new Meson();
        $meson->nombre = $request->input('nombre');
        $meson->estado = $request->input('estado', 'libre');
        $meson->disponible = $request->has('disponible');
        $meson->save();

        if ($request->has('servicios')) {
            $meson->servicios()->attach($request->input('servicios'));
        }

        return redirect()->route('Admin.mesones.index')->with('success', 'Mesón creado exitosamente.');
    }

    public function show(Meson $meson)
    {
        return view('Admin.Mesones.show', compact('meson'));
    }

    public function edit($id)
    {
        $meson = Meson::with('servicios')->findOrFail($id);
        $servicios = Servicio::all();

        return view('Admin.Mesones.edit', compact('meson', 'servicios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'nullable|string|max:50',
            'disponible' => 'nullable|boolean',
            'servicios' => 'nullable|array'
        ]);

        $meson = Meson::findOrFail($id);
        $meson->nombre = $request->input('nombre');
        $meson->estado = $request->input('estado', 'libre');
        $meson->disponible = $request->has('disponible');
        $meson->save();

        $meson->servicios()->sync($request->input('servicios', []));

        return redirect()->route('Admin.mesones.index')->with('success', 'Mesón actualizado correctamente.');
    }

    public function destroy($id)
    {
        $meson = Meson::findOrFail($id);
        $meson->delete();

        return redirect()->route('Admin.mesones.index')->with('success', 'Mesón eliminado exitosamente.');
    }
}
