<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoletoSorteo extends Model
{
    use HasFactory;

    protected $table = 'boletos_sorteo';
    
    public $timestamps = false;
    
    protected $fillable = [
        'factura_id',
        'numero_factura',
        'nombre_cliente',
        'periodo_id',
        'producto_id',
        'nombre_producto',
        'jornada_id',
        'nombre_jornada',
        'abono_id',
        'nombre_abono',
        'numero_boleto',
        'es_ganador',
        'premio_ganado',
        'ya_participo',
        'sorteo_id',
    ];

    /**
     * Relación con Sorteo (opcional)
     */
    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class, 'sorteo_id');
    }

    /**
     * Relación con FacturaCabecera
     */
    public function factura()
    {
        return $this->belongsTo(FacturaCabecera::class, 'factura_id');
    }
}
