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

        $servicio = DB::table('servicios')->find($data['servicio_id']);
        if (! $servicio) {
            return back()->withErrors(['servicio_id' => 'Servicio no válido.']);
        }
        $letra = $servicio->letra;

        $maxHoy = DB::table('turnos')
            ->where('servicio_id', $data['servicio_id'])
            ->whereDate('created_at', now()->toDateString())
            ->max('numero_turno') ?? 0;

        $nuevoNumero = $maxHoy + 1;
        $codigoTurno = $letra . str_pad($nuevoNumero, 2, '0', STR_PAD_LEFT);

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

        return redirect()->route('totem.confirmacion', [
            'codigo' => $codigoTurno,
            'rut'    => $data['rut'],
        ]);
    }

    public function confirmacion(Request $request)
    {
        $codigo = $request->query('codigo');

        if (! $codigo) {
            return redirect()->route('totem.show')->with('error', 'Falta el código para mostrar la confirmación.');
        }

        // 1. Buscar el turno actual por código
        $turnoActual = DB::table('turnos')
            ->where('codigo_turno', $codigo)
            ->first();

        if (! $turnoActual) {
            return redirect()->route('totem.show')->with('error', 'Turno no encontrado.');
        }

        $servicioId = $turnoActual->servicio_id;

        // 2. Turnos pendientes SOLO del MISMO servicio
        $turnosPendientes = DB::table('turnos')
            ->where('servicio_id', $servicioId)
            ->where('estado', 'pendiente')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('numero_turno')
            ->get();

        // 3. Turnos en atención SOLO del MISMO servicio
        $turnosAtendiendo = DB::table('turnos')
            ->where('servicio_id', $servicioId)
            ->where('estado', 'atendiendo')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. URL QR sin rut
        $url = 'http://127.0.0.1:8000/gesnot/turnos?codigo=' . urlencode($codigo);
        $qr = QrCode::size(200)->generate($url);

        // 5. Retornar vista con turno actual, pendientes y en atención del mismo servicio
        return view('totem.confirmacion', [
            'qr' => $qr,
            'codigo' => $codigo,
            'turnoActual' => $turnoActual,
            'turnosPendientes' => $turnosPendientes,
            'turnosAtendiendo' => $turnosAtendiendo,
        ]);
    }
}
