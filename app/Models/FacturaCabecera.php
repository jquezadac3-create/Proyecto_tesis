<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaCabecera extends Model
{
    protected $table = 'factura_cabecera';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'secuencia_factura',
        'fecha',
        'cliente_id',
        'forma_pago',
        'subtotal15',
        'subtotal5',
        'subtotal0',
        'descuento',
        'iva15',
        'iva5',
        'ice',
        'adicional',
        'total_factura',
        'periodo_id',
        'status',   
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    // Relación con detalles
    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class, 'factura_id');
    }

    // Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con forma de pago
    public function formaPago()
    {
        return $this->belongsTo(FormaPago::class, 'forma_pago',);
    }

    // Relación con periodo campeonato
    public function periodo()
    {
        return $this->belongsTo(PeriodoCampeonato::class, 'periodo_id');
    }

    public function scopeFromLastWeek($query)
    {
        return $query->whereDate('fecha', '>=', now()->subDays(7)->toDateString())->where('status', '!=', 'anulada');
    }

    public function scopeWhereToday($query, $column = 'fecha')
    {
        return $query->whereDate($column, now()->toDateString())->where('status', '!=', 'anulada');
    }

    public function facturaEstadoSri()
    {
        return $this->hasOne(FacturaEstadoSri::class, 'factura_cabecera_id');
    }
}
