<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compareciente extends Model
{
    use HasFactory;

    
    protected $table = 'compareciente';

    
    protected $fillable = [
        'nombre',  
        'rut',     
        'email',   
        'telefono' 
    ];

    
}

