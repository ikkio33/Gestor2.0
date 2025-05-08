<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['nombre', 'servicio_id'];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
