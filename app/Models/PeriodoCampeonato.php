<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodoCampeonato extends Model
{
    use HasFactory;

    protected $table = 'periodo_campeonato';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'status',
    ];

    public $timestamps = false;
    
}
