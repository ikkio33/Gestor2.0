<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    // Definir la tabla que usa este modelo (si es diferente al nombre plural del modelo)
    protected $table = 'turnos'; // Asegúrate de que el nombre de la tabla sea correcto

    // Definir los campos que pueden ser asignados masivamente
    protected $fillable = [
        'cliente_id',  // ID del cliente (relación con la tabla de clientes)
        'servicio_id', // ID del servicio (relación con la tabla de servicios)
        'estado',      // Estado del turno (Ej. pendiente, en espera, atendido, etc.)
        'tiempo_espera', // Tiempo de espera estimado o real
        'meson_id',    // ID del mesón (si hay un mesón asignado)
        'turno',       // Número de turno
    ];

    // Relaciones con otros modelos
    public function cliente()
    {
        return $this->belongsTo(Compareciente::class);  // Relación con el modelo Cliente
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);  // Relación con el modelo Servicio
    }

    public function meson()
    {
        return $this->belongsTo(Meson::class);  // Relación con el modelo Meson
    }
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }
}
