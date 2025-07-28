<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Turno extends Model
{
    use HasFactory;

    protected $table = 'turnos';

    protected $fillable = [
        'cliente_id',
        'servicio_id',
        'estado',
        'tiempo_espera',
        'tiempo_atencion',
        'meson_id',
        'usuario_id',
        'turno',
        'materia_id',
        'codigo_turno',
        'inicio_atencion',
        'fin_atencion',
    ];

    // Declarar atributos de fecha para que Eloquent los transforme automáticamente en Carbon
    protected $dates = [
        'inicio_atencion'=> 'datetime',
        'fin_atencion'=> 'datetime',
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
    ];

    protected $casts = [
    'inicio_atencion' => 'datetime',
    'fin_atencion' => 'datetime',
];


    // Estados posibles
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_ESPERA = 'en_espera';
    public const ESTADO_ATENDIDO = 'atendido';
    public const ESTADO_ATENDIENDO = 'atendiendo';
    public const ESTADO_CANCELADO = 'cancelado';

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Compareciente::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function meson()
    {
        return $this->belongsTo(Meson::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'usuario_id');
    }

    // Generación automática de código al crear
    protected static function booted()
    {
        static::creating(function ($turno) {
            if (empty($turno->codigo_turno)) {
                $turno->codigo_turno = Str::random(10);
            }
        });
    }

    // Accesor para duración de atención en formato legible
    public function getDuracionAtencionAttribute()
    {
        if ($this->inicio_atencion && $this->fin_atencion) {
            $segundos = $this->inicio_atencion->diffInSeconds($this->fin_atencion);
            $minutos = floor($segundos / 60);
            $segundos = $segundos % 60;
            return "{$minutos}m {$segundos}s";
        }
        return null;
    }
}
