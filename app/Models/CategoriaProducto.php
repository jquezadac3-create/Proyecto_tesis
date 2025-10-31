<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categoria_productos';

    // No usamos created_at ni updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}
