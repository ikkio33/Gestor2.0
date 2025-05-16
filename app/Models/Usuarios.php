<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Usuarios extends Authenticatable
{
    protected $fillable = ['nombre', 'email', 'password', 'rol', 'meson_id'];

    protected $username = 'nombre';

    public function meson()
    {
        return $this->belongsTo(Meson::class, 'meson_id');
    }

    public function turnos()
    {
        return $this->hasMany(Turno::class, 'usuario_id');
    }

    /**
     * Establece la contraseña cifrada antes de guardar en la base de datos
     *
     * @param  string  $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            // Evitar rehashear una contraseña ya hasheada
            if (Hash::needsRehash($password)) {
                $this->attributes['password'] = Hash::make($password);
            } else {
                $this->attributes['password'] = $password;
            }
        }
    }
}
