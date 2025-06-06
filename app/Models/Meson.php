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

    // Mutator para mantener estado sincronizado con disponible
    public function setDisponibleAttribute($value)
    {
        $this->attributes['disponible'] = $value;
        $this->attributes['estado'] = $value ? 'Libre' : 'Ocupado';
    }

    public function asignarA(Usuarios $user): void
    {
        if ($prev = $this->usuario) {
            if ($prev->id !== $user->id) {
                $prev->meson_id = null;
                $prev->save();
            }
        }

        $user->meson_id = $this->id;
        $user->save();

        // Aquí basta con cambiar disponible, el mutator actualiza estado
        $this->disponible = false;
        $this->save();
    }

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
