<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qr extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'factura_id',
        'authorized_by',
        'qr_code_data',
        'status',
    ];

    public function factura()
    {
        return $this->belongsTo(FacturaCabecera::class, 'factura_id');
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}
