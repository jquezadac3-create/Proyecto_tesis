<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    protected $table = 'factura_detalle';
    public $timestamps = false;

    protected $fillable = [
        'factura_id',
        'producto_id',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'total',
        'abono_id', 
        'jornada_id',
    ];

    // Relación con factura
    public function factura()
    {
        return $this->belongsTo(FacturaCabecera::class, 'factura_id');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
