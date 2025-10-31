<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'codigo_establecimiento',
        'serie_ruc',
        'direccion_matriz',
        'numero_factura',
        'direccion_establecimiento',
        'tipo_contribuyente',
        'obligado_contabilidad',
        'ambiente',
        'estado_electronica',
        'firma_contrasenia',
        'firma_path',
        'logo_path',
    ];

    protected $hidden = [
        'firma_contrasenia'
    ];
}
