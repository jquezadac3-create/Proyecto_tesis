<?php

namespace App\Jobs;

use App\Models\FacturaCabecera;
use App\Services\SriService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarCorreoFacturaJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $facturaId, public $accessKey) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $factura = FacturaCabecera::with('cliente')->find($this->facturaId);

        if (!$factura || !$factura->cliente) {
            Log::error("Factura o cliente no encontrados para enviar correo.");
            return;
        }

        if ($factura->cliente->numero_identificacion === '9999999999999') {
            Log::info("No se envía correo a CONSUMIDOR FINAL.");
            return;
        }

        $cliente = $factura->cliente;
        $nombre = "{$cliente->nombres} {$cliente->apellidos}";
        $numeroFactura = str_pad($factura->secuencia_factura, 9, '0', STR_PAD_LEFT);

        Log::info("Intentando enviar a {$factura->cliente->nombres} {$factura->cliente->apellidos}.");
        SriService::sendEmail($this->accessKey, $cliente->email, $nombre, $numeroFactura);
    }
}
