<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuarios extends Authenticatable
{
    protected $fillable = ['nombre', 'email', 'password', 'rol', 'meson_id'];

    public function meson()
{
    return $this->belongsTo(Meson::class, 'meson_id');
}
public function turnos()
{
    return $this->hasMany(Turno::class, 'usuario_id');
}
}
