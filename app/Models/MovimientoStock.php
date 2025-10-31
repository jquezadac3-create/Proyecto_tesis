<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'user_id',
        'jornada_id',
        'tipo_movimiento',   
        'stock_anterior',
        'stock_agregado',
        'stock_nuevo',
        'motivo',
        'fecha',
    ];

    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
