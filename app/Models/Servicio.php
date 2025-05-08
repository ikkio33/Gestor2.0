<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'letra'];

    public function mesones(): BelongsToMany
    {
        return $this->belongsToMany(Meson::class, 'meson_servicio', 'servicio_id', 'meson_id');
    }

    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }
}
