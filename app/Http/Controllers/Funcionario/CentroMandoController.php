<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Servicio;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Meson;

class CentroMandoController extends Controller
{
    // Vista principal del centro de mando
    public function index()
    {
        return view('funcionario.centro-mando');
    }

    // Obtener turnos pendientes del día actual, agrupados por servicio (máximo 3 por grupo)
    public function turnosPendientes()
    {
        $hoy = Carbon::today();

        // Obtener los mesones asignados al funcionario autenticado
        $funcionarioId = Auth::id();
        $mesonesAsignados = Meson::where('funcionario_id', $funcionarioId)->pluck('id');

        if ($mesonesAsignados->isEmpty()) {
            return response()->json(['turnos' => []]); // Sin mesones asignados, sin turnos
        }

        // Obtener IDs de servicios asociados a esos mesones
        $serviciosIds = DB::table('meson_servicio')
            ->whereIn('meson_id', $mesonesAsignados)
            ->pluck('servicio_id')
            ->unique();

        if ($serviciosIds->isEmpty()) {
            return response()->json(['turnos' => []]); // Sin servicios asignados, sin turnos
        }

        // Obtener turnos pendientes filtrados por servicios asignados
        $turnos = Turno::where('estado', 'pendiente')
            ->whereDate('created_at', $hoy)
            ->whereIn('servicio_id', $serviciosIds)
            ->with('servicio')
            ->get()
            ->groupBy('servicio_id');

        $response = [];

        foreach ($turnos as $servicioId => $grupo) {
            $servicio = $grupo->first()->servicio;
            if (!$servicio) continue;

            $response[$servicio->nombre] = $grupo->take(3)->map(function ($turno) {
                return [
                    'id' => $turno->id,
                    'codigo' => $turno->codigo_turno,
                    'tiempo_espera' => $turno->created_at->diffForHumans(null, true),
                ];
            });
        }

        return response()->json(['turnos' => $response]);
    }
    // Obtener turno actualmente en atención, incluyendo servicio y mesón
    public function turnoEnAtencion()
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->meson) {
            return response()->json(['turno' => null]);
        }

        $mesonId = $usuario->meson->id;

        $turno = Turno::where('estado', 'atendiendo')
            ->whereHas('usuario.meson', function ($query) use ($mesonId) {
                $query->where('id', $mesonId);
            })
            ->with(['servicio', 'usuario.meson'])
            ->first();

        if (!$turno) {
            return response()->json(['turno' => null]);
        }

        return response()->json([
            'turno' => [
                'id' => $turno->id,
                'codigo' => $turno->codigo_turno,
                'nombre_servicio' => $turno->servicio->nombre ?? 'Servicio desconocido',
                'estado' => $turno->estado,
                'updated_at' => $turno->updated_at,
                'meson_nombre' => $turno->usuario && $turno->usuario->meson
                    ? $turno->usuario->meson->nombre
                    : 'Mesón desconocido',
            ]
        ]);
    }


    // Llamar un turno: cambia el estado a "atendiendo"
    public function llamarTurno(Request $request)
    {
        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendiendo';
        $turno->usuario_id = Auth::id(); // ← Asignamos el funcionario que llama
        $turno->save();

        return response()->json(['success' => true]);
    }

    // Rellamar a un turno: útil para efectos visuales/sonoros en el frontend
    // Rellamar un turno: solo actualiza updated_at para marcar nueva llamada
    public function rellamarTurno(Request $request)
    {
        $turno = Turno::findOrFail($request->turno_id);

        if ($turno->estado !== 'atendiendo') {
            return response()->json([
                'success' => false,
                'message' => 'El turno no está en atención, no se puede re-llamar.'
            ], 400);
        }

        $turno->touch(); // Actualiza updated_at automáticamente
        return response()->json(['success' => true]);
    }

    // Cancelar turno: cambia estado 
    public function cancelarTurno(Request $request)
    {
        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'cancelado';
        $turno->save();
    }

    // Finalizar la atención de un turno
    public function finalizarTurno(Request $request)
    {
        $turno = Turno::findOrFail($request->turno_id);
        $turno->estado = 'atendido';
        $turno->save();
    }

    public function asignarMeson(Request $request)
    {
        $request->validate([
            'meson_id' => 'required|integer|exists:mesones,id',
        ]);

        $meson = Meson::findOrFail($request->meson_id);
        $meson->funcionario_id = Auth::id(); // Asignar el usuario actual (funcionario) al mesón
        $meson->save();

        return response()->json(['success' => true, 'message' => 'Mesón asignado correctamente.']);
    }

    public function turnoEnAtencionPublico()
    {
        $turnos = Turno::where('estado', 'atendiendo')
            ->with(['servicio', 'usuario.meson'])
            ->orderBy('updated_at', 'desc') // los más recientes primero
            ->take(4) // máximo 4 turnos
            ->get();

        if ($turnos->isEmpty()) {
            return response()->json(['turnos' => []]);
        }

        // Formatear turnos
        $turnosFormateados = $turnos->map(function ($turno) {
            return [
                'id' => $turno->id,
                'codigo' => $turno->codigo_turno,
                'nombre_servicio' => $turno->servicio->nombre ?? 'Servicio desconocido',
                'updated_at' => $turno->updated_at,
                'meson_nombre' => $turno->usuario && $turno->usuario->meson
                    ? $turno->usuario->meson->nombre
                    : 'Mesón desconocido',
            ];
        });

        return response()->json(['turnos' => $turnosFormateados]);
    }


    public function turnosEnAtencionPublicoTodos()
    {
        $turnos = Turno::where('estado', 'atendiendo')
            ->with(['servicio', 'usuario.meson'])
            ->get();

        $resultado = $turnos->map(function ($turno) {
            return [
                'id' => $turno->id,
                'codigo' => $turno->codigo_turno,
                'nombre_servicio' => $turno->servicio->nombre ?? 'Servicio desconocido',
                'updated_at' => $turno->updated_at,
                'meson_nombre' => $turno->usuario && $turno->usuario->meson
                    ? $turno->usuario->meson->nombre
                    : 'Mesón desconocido',
            ];
        });

        return response()->json(['turnos' => $resultado]);
    }
}
