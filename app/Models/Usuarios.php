<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Usuarios extends Authenticatable
{
    protected $fillable = ['nombre', 'email', 'password', 'rol'];

    // Un funcionario tiene un mesón asignado (1 a 1)
    public function meson()
    {
        return $this->hasOne(Meson::class, 'funcionario_id', 'id');
    }

    // Turnos del usuario
    public function turnos()
    {
        return $this->hasMany(Turno::class, 'usuario_id');
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            if (Hash::needsRehash($password)) {
                $this->attributes['password'] = Hash::make($password);
            } else {
                $this->attributes['password'] = $password;
            }
        }
    }
}
