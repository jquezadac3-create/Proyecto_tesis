<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'clientTransactionId',
        'invoice_data',
        'response_data',
        'status',
    ];
}
