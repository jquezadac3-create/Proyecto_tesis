<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaEstadoSri extends Model
{
    protected $table = 'factura_estado_sri';

    protected $fillable = [
        'factura_cabecera_id',
        'clave_acceso',
        'estado_recepcion',
        'estado_autorizacion'
    ];

    protected function casts():array
    {
        return [
            'created_at' => 'datetime: d/m/Y H:i',
            'updated_at' => 'datetime: d/m/Y H:i',
        ];
    }

    public function factura()
    {
        return $this->belongsTo(FacturaCabecera::class, 'factura_cabecera_id');
    }
}
