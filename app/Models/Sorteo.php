<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sorteo extends Model
{
    use HasFactory;

    protected $table = 'sorteos';

    public $timestamps = false;

    protected $fillable = [
        'periodo_id',
        'nombre',
        'num_premios',
        'posicion_ganadora',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación con las jornadas seleccionadas para este sorteo
     */
    public function jornadas()
    {
        return $this->belongsToMany(
            Jornada::class,
            'sorteos_jornadas',
            'sorteo_id',
            'jornada_id'
        )->withTimestamps();
    }

    /**
     * Relación con los boletos que participan en este sorteo
     */
    public function boletos()
    {
        return $this->hasMany(BoletoSorteo::class, 'sorteo_id', 'id');
    }
}
