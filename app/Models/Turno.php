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
        'meson_id',
        'turno',
        'materia_id', // Si usas esta relación
    ];

    // Estados posibles (opcional pero recomendable)
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_ESPERA = 'en_espera';
    public const ESTADO_ATENDIDO = 'atendido';

    public function cliente()
    {
        return $this->belongsTo(Compareciente::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function meson()
    {
        return $this->belongsTo(Meson::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    protected static function booted()
    {
        static::creating(function ($turno) {
            $turno->codigo_seguimiento = Str::random(10);
        });
    }
}
