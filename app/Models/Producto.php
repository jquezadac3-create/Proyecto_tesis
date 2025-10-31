<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    // Desactivar timestamps automáticos
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'cantidad',
        'cantidad_actual',
        'tipo_producto',
        'impuesto',
        'precio_venta_sin_iva',
        'precio_venta_final',
        'costo',
        'categoria_id',
        'abono',
        'id_abono',
        'fecha_creacion',
    ];

    // Si quieres que fecha_creacion sea tratada como datetime
    protected $dates = ['fecha_creacion'];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    public function abonoRelacion()
    {
        return $this->belongsTo(Abono::class, 'id_abono');
    }

    public function casts() {
        return [
            'precio_venta_sin_iva' => 'decimal:4',
            'precio_venta_final' => 'decimal:4'
        ];
    }
}
