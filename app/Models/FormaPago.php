<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormaPago extends Model
{
      use HasFactory;

    // Nombre de la tabla
    protected $table = 'formas_pago';

    // Llave primaria
    protected $primaryKey = 'id';

    // Si no usas timestamps created_at y updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'codigo',
        'forma_pago',
    ];

    // Relación: una forma de pago puede estar en muchas facturas
    public function facturas(){
        return $this->hasMany(FacturaCabecera::class, 'forma_pago', 'codigo');
    }
}
