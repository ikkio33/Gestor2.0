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
            ->whereDate('created_at', Carbon::today()) // 👈 FILTRO IMPORTANTE
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


    public function llamarTurno(Request $request)
    {
        $turno = Turno::findOrFail($request->turno_id);
        $usuario = Auth::user();
        $meson = $usuario->meson;

        if (!$meson) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un mesón asignado.'
            ], 400);
        }

        $turno->estado = 'atendiendo';
        $turno->usuario_id = $usuario->id;
        $turno->meson_id = $meson->id;

        // Tiempo de espera: diferencia entre creación y ahora (inicio atención)
        if (is_null($turno->tiempo_espera)) {
            $turno->tiempo_espera = $turno->created_at->diffInSeconds(now());
        }

        // Guardar marca explícita de inicio de atención
        $turno->inicio_atencion = now();

        $turno->save();

        return response()->json(['success' => true]);
    }
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

    // Finalizar turno: guarda usuario final, y tiempo de atención
    public function finalizarTurno(Request $request)
    {
        try {
            $turno = Turno::findOrFail($request->turno_id);

            // Asignar usuario que finaliza si no estaba asignado
            if (is_null($turno->usuario_id)) {
                $turno->usuario_id = Auth::id();
            }

            $turno->fin_atencion = now();

            // Solo calcula tiempo si inicio_atencion está definido
            if (is_null($turno->tiempo_atencion) && !is_null($turno->inicio_atencion)) {
                $turno->tiempo_atencion = $turno->inicio_atencion->diffInSeconds($turno->fin_atencion);
            }

            $turno->estado = 'atendido';
            $turno->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
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
            ->whereDate('created_at', Carbon::today()) // <-- FILTRAMOS POR DÍA ACTUAL
            ->with(['servicio', 'usuario.meson'])
            ->orderBy('updated_at', 'desc') // los más recientes primero
            ->take(10)
            ->get();

        if ($turnos->isEmpty()) {
            return response()->json(['turnos' => []]);
        }

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
}
