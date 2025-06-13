<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Meson extends Model
{
    use HasFactory;

    protected $table = 'meson';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['nombre', 'estado', 'disponible', 'funcionario_id'];

    // Servicios asignados
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'meson_servicio', 'meson_id', 'servicio_id');
    }

    // Funcionario asignado (usuario con rol funcionario)
    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Usuarios::class, 'funcionario_id', 'id');
    }

    /**
     * Obtener los turnos asociados a este mesón a través de los usuarios asignados
     */
    public function turnos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Turno::class,
            Usuarios::class,
            'meson_id',
            'usuario_id',
            'id',
            'id'
        );
    }

    // Mutator para estado y disponible basado en funcionario_id
    public function setFuncionarioIdAttribute($value)
    {
        $this->attributes['funcionario_id'] = $value;

        if ($value !== null) {
            $this->attributes['disponible'] = false;
            $this->attributes['estado'] = 'Ocupado';
        } else {
            $this->attributes['disponible'] = true;
            $this->attributes['estado'] = 'Libre';
        }
    }

    // Asignar mesón a un funcionario
    public function asignarA(Usuarios $user): void
    {
        $this->funcionario_id = $user->id; // Esto activará el mutator
        $this->save();
    }

    // Liberar mesón
    public function liberar(): void
    {
        $this->funcionario_id = null; // Esto activará el mutator
        $this->save();
    }
}
