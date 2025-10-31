<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorteoJornada extends Model
{
    use HasFactory;

    protected $table = 'sorteos_jornadas';

    public $timestamps = false;
    
    protected $fillable = [
        'sorteo_id',
        'jornada_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación inversa al sorteo
     */
    public function sorteo()
    {
        return $this->belongsTo(Sorteo::class, 'sorteo_id');
    }

    /**
     * Relación inversa a la jornada
     */
    public function jornada()
    {
        return $this->belongsTo(Jornada::class, 'jornada_id');
    }
}
