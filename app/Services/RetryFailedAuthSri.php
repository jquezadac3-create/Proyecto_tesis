<?php

namespace App\Services;

use App\Models\Config;
use App\Models\FacturaEstadoSri;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RetryFailedAuthSri
{
    public static function retry()
    {
        $config = Config::first();

        $facturasPendientes = FacturaEstadoSri::where('estado_autorizacion', 'PENDIENTE')->with('factura', 'factura.cliente')->get();

        $facturasPendientes->each(function ($invoice) use ($config) {
            self::retryAuth($invoice, $config);

            $toName = "{$invoice->factura->cliente->nombres} {$invoice->factura->cliente->apellidos}";
            $paths = self::getPaths($invoice->clave_acceso);
            $numeroFactura = str_pad($invoice->factura->numero_secuencia, 9, '0', STR_PAD_LEFT);

            try {
                if ($invoice->factura->cliente->email !== null && $invoice->factura->cliente->numero_identificacion !== '9999999999999') {
                    Mail::to($invoice->factura->cliente->email, $toName)->send(new \App\Mail\SriInvoice(
                        pdfPath: $paths['pdfPath'],
                        authorizedXMLPath: $paths['authorizedXMLPath'],
                        accessKey: $invoice->factura->clave_acceso,
                        numero_Factura: $numeroFactura,
                        cliente: $invoice->factura->cliente,
                    ));
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar correo de factura SRI: ' . $e->getMessage());
            }
        });
    }

    private static function retryAuth($invoice, $config)
    {
        $accessKey = $invoice->clave_acceso;
        SriService::validateAuthorization($accessKey, $invoice, $config->ambiente);
    }

    private static function getPaths($accessKey)
    {
        return [
            'pdfPath' => "pdf/{$accessKey}.pdf",
            'authorizedXMLPath' => "xml/authorized/{$accessKey}_AUTORIZADO.xml",
        ];
    }
}
