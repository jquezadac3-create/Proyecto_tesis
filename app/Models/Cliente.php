<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';

    public $timestamps = false; // Deshabilitar el timestamp automatico de laravel 

    protected $fillable = [
        'nombres',
        'apellidos',
        'tipo_identificacion',
        'numero_identificacion',
        'email',
        'telefono',
        'direccion',
        'fecha_creacion'
    ];

    // Manejar la fecha como dateTime
    protected $dates = ['fecha_creacion'];

    public function getNombreCompletoAttribute(){
        return trim(($this->nombres ?? '') . ' ' . ($this->apellidos ?? ''));
    }
}
