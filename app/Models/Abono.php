<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $table = 'abonos';

    // No usamos timestamps porque tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'descripcion',
        'numero_entradas',
        'costo_total',
        'estado',
        'mostrar_en_web'
    ];

    // Relación con productos
    public function productos(){
        return $this->hasMany(Producto::class, 'id_abono');
    }
}
