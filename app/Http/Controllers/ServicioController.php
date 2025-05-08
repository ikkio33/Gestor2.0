<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{
    public function seleccionar(Request $request)
    {
        // Asegurarse de que venga el RUT
        if (! $request->has('rut')) {
            return redirect()->route('totem.index');
        }

        $rut = $request->input('rut');

        // Obtener servicios y materias
        $servicios = DB::table('servicios')
            ->orderBy('letra', 'asc')
            ->orderBy('nombre', 'asc')
            ->get();

        $materias = DB::table('materias')
            ->orderBy('nombre', 'asc')
            ->get();

        // Agrupar materias por servicio
        $materiasPorServicio = [];
        foreach ($materias as $m) {
            $materiasPorServicio[$m->servicio_id][] = $m;
        }

        return view('servicios.seleccionar', compact(
            'rut', 'servicios', 'materiasPorServicio'
        ));
    }
}
