<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoJornada extends Model
{
    protected $table = 'productos_jornada';

    // No usamos timestamps
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_producto',
        'id_jornada',
        'stock',
        'stock_actual',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function jornada()
    {
        return $this->belongsTo(Jornada::class, 'id_jornada');
    }
}
