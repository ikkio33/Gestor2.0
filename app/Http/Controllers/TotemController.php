<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TotemController extends Controller
{
    public function show()
    {
        return view('totem.totem');
    }

    public function select(Request $request)
    {
        $request->validate([
            'rut' => ['required', 'regex:/^\d{1,2}\.?\d{3}\.?\d{3}-[\dKk]$/']
        ]);

        $rut = $request->input('rut');

        $compareciente = DB::table('compareciente')
            ->where('rut', $rut)
            ->first();

        if (! $compareciente) {
            DB::table('compareciente')->insert([
                'rut'        => $rut,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $servicios = DB::table('servicios')
            ->orderBy('letra')
            ->orderBy('nombre')
            ->get();

        $materias = DB::table('materias')
            ->orderBy('nombre')
            ->get();

        $materias = DB::table('materias')
            ->orderBy('nombre')
            ->get();


        $materiasPorServicio = [];
        foreach ($materias as $materia) {
            $materiasPorServicio[$materia->servicio_id][] = $materia;
        }

        return view('totem.seleccionar', compact('rut', 'servicios', 'materiasPorServicio'));
    }

    public function confirmar(Request $request)
    {
        $data = $request->validate([
            'rut'          => ['required', 'regex:/^\d{1,2}\.?\d{3}\.?\d{3}-[\dKk]$/'],
            'servicio_id'  => ['required', 'integer'],
            'materia_id'   => ['nullable', 'integer'],
        ]);

        // (2) Nos aseguramos de que el compareciente existe y obtenemos su ID
        $compareciente = DB::table('compareciente')
            ->where('rut', $data['rut'])
            ->first();

        $comparecienteId = $compareciente
            ? $compareciente->id
            : DB::table('compareciente')->insertGetId([
                'rut'        => $data['rut'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        // Verificar servicio
        $servicio = DB::table('servicios')->find($data['servicio_id']);
        if (! $servicio) {
            return back()->withErrors(['servicio_id' => 'Servicio no válido.']);
        }
        $letra = $servicio->letra;

        // Generar número de turno
        $maxHoy = DB::table('turnos')
            ->where('servicio_id', $data['servicio_id'])
            ->whereDate('created_at', now()->toDateString())
            ->max('numero_turno') ?? 0;

        $nuevoNumero = $maxHoy + 1;
        $codigoTurno = $letra . str_pad($nuevoNumero, 2, '0', STR_PAD_LEFT);

        // Insertar turno
        DB::table('turnos')->insert([
            'codigo_turno'  => $codigoTurno,
            'cliente_id'    => $comparecienteId,
            'numero_turno'  => $nuevoNumero,
            'servicio_id'   => $data['servicio_id'],
            'materia_id'    => $data['materia_id'] ?? null,
            'estado'        => 'pendiente',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // (3) Redirigir a confirmación, pasando código *y* rut como query params
        return redirect()->route('totem.confirmacion', [
            'codigo' => $codigoTurno,
            'rut'    => $data['rut'],
        ]);
    }

    public function confirmacion(Request $request)
    {
        $codigo = $request->query('codigo');
        $rut    = $request->query('rut');

        if (! $codigo || ! $rut) {
            return redirect()->route('totem.show')->with('error', 'Faltan datos para mostrar la confirmación.');
        }
        $url = route('turno.estado', ['codigo' => $codigo]);
        // $url = 'https://gesnote.cl' . route('turno.estado', ['codigo' => $codigo], false);
        // Generar el código QR
        $qr = QrCode::size(200)->generate($url);


        return view('totem.confirmacion', compact('codigo', 'rut', 'qr'));
    }

    public function estadoTurno(Request $request)
    {
        $codigo = $request->query('codigo');

        // Buscar info real del turno 
        return "Estado actual del turno: $codigo (ejemplo)";
    }
}
