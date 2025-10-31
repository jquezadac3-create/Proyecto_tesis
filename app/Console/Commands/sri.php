<?php

namespace App\Console\Commands;

use App\Http\Controllers\FacturaListadoController;
use App\Jobs\ReenviarFacturaSriJob;
use App\Models\FacturaCabecera;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class sri extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sri';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reintenta el envío de facturas al SRI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        FacturaCabecera::whereDoesntHave('facturaEstadoSri', function ($query) {
            $query->where('estado_autorizacion', 'AUTORIZADO');
        })->chunk(100, function ($facturas) {
            $facturas->each(function ($factura) {
                ReenviarFacturaSriJob::dispatch($factura->id);
            });
        });

        $this->info('Facturas reenviadas al SRI correctamente.');
    }
}
