<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Meson extends Model
{
    use HasFactory;

    protected $table = 'meson';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = ['nombre', 'estado', 'disponible'];

    
    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class, 'meson_servicio', 'meson_id', 'servicio_id');
    }

    
    public function usuario(): HasOne
    {
        return $this->hasOne(Usuarios::class, 'meson_id');
    }

    /**
     * Obtener todos los turnos a través del usuario asignado.
     */
    public function turnos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Turno::class,
            Usuarios::class,
            'meson_id',   // FK en usuarios
            'usuario_id', // FK en turnos
            'id',         // PK en meson
            'id'          // PK en usuarios
        );
    }

    /**
     * Asigna este mesón a un usuario, liberando al anterior si existe.
     */
    public function asignarA(Usuarios $user): void
    {
        // Liberar usuario previo si existe
        if ($prev = $this->usuario) {
            if ($prev->id !== $user->id) {
                $prev->meson_id = null;
                $prev->save();
            }
        }

        // Asignar mesón al usuario actual
        $user->meson_id = $this->id;
        $user->save();

        // Marcar mesón como ocupado
        $this->disponible = false;
        $this->save();
    }

    /**
     * Libera este mesón del usuario asignado.
     */
    public function liberar(): void
    {
        if ($user = $this->usuario) {
            $user->meson_id = null;
            $user->save();

            $this->disponible = true;
            $this->save();
        }
    }
}
