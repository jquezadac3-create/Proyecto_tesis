<?php

namespace App\Services;

use App\Http\Controllers\Routes\BoletoRoutesController;
use Illuminate\Support\Facades\DB;

class CancelPrepurchase
{
    public static function cancel()
    {
        $boletoController = new BoletoRoutesController();
        $pendents = DB::table('transactions')
            ->where('status', '-1')
            ->where(
                'updated_at',
                '<=',
                now()->subminutes(10)
            )
            ->orderBy('id')
            ->select('clientTransactionId');

        $pendents->lazy()->each(fn($item) => $boletoController->revertStockMovements($item->clientTransactionId));

        $pendents->update([
            'status' => '-2',
            'updated_at' => now(),
        ]);
    }
}
