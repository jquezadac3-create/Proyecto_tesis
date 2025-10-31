<?php

namespace App\Jobs;

use App\Models\FacturaCabecera;
use App\Services\SriService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReenviarFacturaSriJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable;

    public $timeout = 120;

    public $facturaId;

    /**
     * Create a new job instance.
     */
    public function __construct($facturaId) {
        $this->facturaId = $facturaId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {

            $factura = FacturaCabecera::with('cliente', 'detalles', 'facturaEstadoSri')->find($this->facturaId);

            Log::warning('Factura para enviar', ['factura' => $factura]);

            if (!$factura) return;

            $estadoSri = $factura->facturaEstadoSri;

            $accessKey = $estadoSri?->clave_acceso;
            $xml = $accessKey ? Storage::get("xml/signed/{$accessKey}_SIGNED.xml") : null;

            if (!$xml || !$accessKey) {
                $xmlGenerado = $this->generarNuevoXml($factura);
                $xml = $xmlGenerado['xml'];
                $accessKey = $xmlGenerado['accessKey'];
            }

            $reception = SriService::signXML($xml, $accessKey, $factura->id);

            if (!isset($reception['estado']) || $reception['estado'] !== 'RECIBIDA') {
                Log::warning("Factura ID {$factura->id} no fue recibida por el SRI.");
                return;
            }

            $estadoSri = $reception['factura_sri'];

            if ($estadoSri->estado_recepcion === 'DEVUELTA') {
                Log::warning("Factura ID {$factura->id} fue DEVUELTA por el SRI.");
                return;
            }

            // Validar autorización
            $config = \App\Models\Config::first();
            $validacion = SriService::validateAuthorization($accessKey, $estadoSri, $config->ambiente ?? 1);

            Log::info("Estado autorizacion factura ID {$factura->id}: " . ($validacion['estado'] ?? 'PENDIENTEs'));
            if ($validacion['estado'] === 'AUTORIZADO') {
                if ($factura->cliente && $factura->cliente->numero_identificacion !== '9999999999999') {
                    EnviarCorreoFacturaJob::dispatch($factura->id, $accessKey);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error al reenviar factura ID {$this->facturaId} al SRI: " . $e->getMessage());
            return;
        }
    }

    private function generarNuevoXml($factura)
    {
        $factura->detalles_modificados = $factura->detalles->map(fn($item) => [
            'codigo' => $item->producto_id,
            'nombre' => $item->nombre_producto,
            'cantidad' => $item->cantidad,
            'precio_unitario' => $item->precio_unitario,
            'total' => $item->total
        ]);

        $invoiceXml = new \App\Services\InvoiceGenerateXML();
        $xml = $invoiceXml->generateXML($factura, $factura->cliente, $factura->detalles_modificados);

        $accessKey = $xml['accessKey'];

        return [
            'xml' => $xml['xml'],
            'accessKey' => $accessKey
        ];
    }
}
