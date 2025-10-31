<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaSorteo extends Model
{
    protected $table = 'facturas_sorteo';

    protected $fillable = [
        'numero_factura',
        'nombre',
        'cantidad',
        'periodo_id', 
    ];

    public $timestamps = false;

    public function periodo(){
        return $this->belongsTo(PeriodoCampeonato::class, 'periodo_id');
    }
}
