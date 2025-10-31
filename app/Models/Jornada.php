<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jornada extends Model
{
    protected $table = 'jornadas';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'cantidad_aforo',
        'estado',
    ];
}
