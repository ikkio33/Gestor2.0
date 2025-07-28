<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class TotemController extends Controller
{
    public function show()
    {
        return view('totem.totem');
    }

    // Método para procesar ingreso RUT
    public function select(Request $request)
    {
        $request->validate([
            'rut' => [
                'required',
                function ($attribute, $value, $fail) {
                    $value = trim($value);

                    if (self::esRut($value)) {
                        if (!self::validarRut($value)) {
                            $fail('El RUT ingresado no es válido. Revisa el dígito verificador.');
                        }
                    } elseif (self::esPasaporte($value)) {
                        // OK, es un pasaporte válido (pero acá es raro que pase, igual lo dejamos)
                    } else {
                        $fail('Debe ingresar un RUT chileno válido o un pasaporte válido.');
                    }
                },
            ],
        ]);

        $rut = $request->input('rut');

        $compareciente = DB::table('compareciente')
            ->where('rut', $rut)
            ->first();

        if (!$compareciente) {
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

        // PASAMOS una variable común "identificacion" para que la vista funcione igual
        return view('totem.seleccionar', [
            'identificacion' => $rut,
            'servicios' => $servicios,
            'materiasPorServicio' => $materiasPorServicio,
        ]);
    }

    // Método para procesar ingreso de pasaporte
    public function selectPasaporte(Request $request)
    {
        $request->validate([
            'pasaporte' => ['required', 'alpha_num', 'min:5', 'max:20'],
        ], [
            'pasaporte.alpha_num' => 'El pasaporte debe contener solo letras y números.',
        ]);

        $pasaporte = $request->input('pasaporte');

        $compareciente = DB::table('compareciente')
            ->where('pasaporte', $pasaporte)
            ->first();

        if (!$compareciente) {
            DB::table('compareciente')->insert([
                'pasaporte'  => $pasaporte,
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

        return view('totem.seleccionar', [
            'identificacion' => $pasaporte,
            'servicios' => $servicios,
            'materiasPorServicio' => $materiasPorServicio,
        ]);
    }

    public function confirmar(Request $request)
    {
        $data = $request->validate([
            'rut' => [
                'required',
                function ($attribute, $value, $fail) {
                    $value = trim($value);

                    if (self::esRut($value)) {
                        if (!self::validarRut($value)) {
                            $fail('El RUT ingresado no es válido. Revisa el dígito verificador.');
                        }
                    } elseif (self::esPasaporte($value)) {
                        // OK
                    } else {
                        $fail('Debe ingresar un RUT chileno válido o un pasaporte válido.');
                    }
                },
            ],
            'servicio_id'  => ['required', 'integer'],
            'materia_id'   => ['nullable', 'integer'],
        ]);

        $compareciente = DB::table('compareciente')
            ->where('rut', $data['rut'])
            ->orWhere('pasaporte', $data['rut'])
            ->first();

        $comparecienteId = $compareciente
            ? $compareciente->id
            : DB::table('compareciente')->insertGetId([
                'rut'        => self::esRut($data['rut']) ? $data['rut'] : null,
                'pasaporte'  => self::esPasaporte($data['rut']) ? $data['rut'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $servicio = DB::table('servicios')->find($data['servicio_id']);
        if (!$servicio) {
            return back()->withErrors(['servicio_id' => 'Servicio no válido.']);
        }

        $letra = $servicio->letra;

        $maxHoy = DB::table('turnos')
            ->where('servicio_id', $data['servicio_id'])
            ->whereDate('created_at', now()->toDateString())
            ->max('numero_turno') ?? 0;

        $nuevoNumero = $maxHoy + 1;
        $codigoTurno = $letra . str_pad($nuevoNumero, 2, '0', STR_PAD_LEFT);

        $tokenSeguridad = Str::random(32);

        DB::table('turnos')->insert([
            'codigo_turno'  => $codigoTurno,
            'cliente_id'    => $comparecienteId,
            'numero_turno'  => $nuevoNumero,
            'servicio_id'   => $data['servicio_id'],
            'materia_id'    => $data['materia_id'] ?? null,
            'estado'        => 'pendiente',
            'token_qr'      => $tokenSeguridad,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Aquí agregamos 'imprimir' => 1 para que dispare la impresión automática
        return redirect()->route('totem.confirmacion', [
            'codigo'   => $codigoTurno,
            'rut'      => $data['rut'],
            'imprimir' => 1,
        ]);
    }


    public function confirmacion(Request $request)
    {
        $codigo = $request->query('codigo');

        if (!$codigo) {
            return redirect()->route('totem.show')->with('error', 'Falta el código para mostrar la confirmación.');
        }

        $turnoActual = DB::table('turnos')
            ->where('codigo_turno', $codigo)
            ->first();

        if (!$turnoActual) {
            return redirect()->route('totem.show')->with('error', 'Turno no encontrado.');
        }

        $servicioId = $turnoActual->servicio_id;

        $turnosPendientes = DB::table('turnos')
            ->where('servicio_id', $servicioId)
            ->where('estado', 'pendiente')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('numero_turno')
            ->get();

        $turnosAtendiendo = DB::table('turnos')
            ->where('servicio_id', $servicioId)
            ->where('estado', 'atendiendo')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('updated_at', 'desc')
            ->get();

        $url = url('/gesnot/turnos?codigo=' . urlencode($codigo));
        $qr = QrCode::size(200)->generate($url);

        return view('totem.confirmacion', [
            'qr'               => $qr,
            'codigo'           => $codigo,
            'turnoActual'      => $turnoActual,
            'turnosPendientes' => $turnosPendientes,
            'turnosAtendiendo' => $turnosAtendiendo,
        ]);
    }

    /**
     * Determina si el input parece un RUT (estructura con guion y puntos opcionales)
     */
    private static function esRut($valor)
    {
        return preg_match('/^\d{1,2}\.?\d{3}\.?\d{3}-[\dkK]$/', $valor);
    }

    /**
     * Valida el RUT chileno con dígito verificador correcto
     */
    private static function validarRut($rutCompleto)
    {
        $rut = strtoupper(preg_replace('/[^0-9kK]/', '', $rutCompleto));

        if (strlen($rut) < 2) return false;

        $cuerpo = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        if (!ctype_digit($cuerpo)) return false;

        $suma = 0;
        $multiplo = 2;

        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
            $suma += $multiplo * intval($cuerpo[$i]);
            $multiplo = $multiplo == 7 ? 2 : $multiplo + 1;
        }

        $resto = $suma % 11;
        $dvEsperado = 11 - $resto;

        if ($dvEsperado == 11) $dvEsperado = '0';
        elseif ($dvEsperado == 10) $dvEsperado = 'K';
        else $dvEsperado = (string)$dvEsperado;

        return $dv === $dvEsperado;
    }

    /**
     * Verifica si el valor tiene un formato de pasaporte válido
     */
    private static function esPasaporte($valor)
    {
        return preg_match('/^[A-Z0-9]{6,12}$/i', $valor);
    }

    public function pasaporte()
    {
        return view('totem.pasaporte'); // Vista para ingreso de pasaporte
    }

    public function generarPdfTurno($codigo)
    {
        $qr = QrCode::size(100)->generate(route('totem.verTurno', $codigo));

        $pdf = Pdf::loadView('totem.ticket_pdf', [
            'codigo' => $codigo,
            'qr' => $qr
        ]);

        return $pdf->stream("turno_{$codigo}.pdf");
    }

    public function imprimirTicket($codigo)
    {
        $qr = QrCode::size(100)->generate(route('totem.verTurno', $codigo));

        $pdf = Pdf::loadView('totem.ticket_pdf', [
            'codigo' => $codigo,
            'qr' => $qr
        ]);

        return $pdf->stream("turno_$codigo.pdf"); // o ->download() si quieres descargar
    }
}
