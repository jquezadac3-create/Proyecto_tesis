<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\FacturaCabecera;
use App\Models\FacturaEstadoSri;
use App\Models\MovimientoStock;
use App\Models\ProductoJornada;
use App\Models\Qr;
use App\Services\InvoiceGenerateXML;
use App\Services\SriService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacturaListadoController extends Controller
{
    /**
     * Obtener los detalles de una factura específica.
     *
     * @param mixed $id - ID de la factura
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $factura = FacturaCabecera::with(['cliente', 'detalles', 'formaPago', 'detalles.producto', 'facturaEstadoSri'])->withCasts([
            'fecha' => 'datetime:d-m-Y H:i',
        ])->findOrFail($id);

        $config = Config::first();

        return view('components.ventas.listado-facturas.detalle', compact('factura', 'config'));
    }

    public function getSriPdf($id)
    {
        $factura = FacturaEstadoSri::where('factura_cabecera_id', $id)->first();

        if (!$factura || !$factura->clave_acceso) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la factura o no tiene clave de acceso.'
            ], 404);
        }

        $clave = trim($factura->clave_acceso);
        $path = "pdf/{$clave}.pdf";

        if (!Storage::exists($path)) {
            $config = Config::first();
            SriService::validateAuthorization($clave, $factura, $config->ambiente);
        }

        $pdf = Storage::get($path);

        return response()->stream(
            fn() => print($pdf),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$clave}.pdf\""
            ]
        );
    }

    public function resendSriEmail($id)
    {
        $factura = FacturaEstadoSri::with('factura', 'factura.cliente')->where('factura_cabecera_id', $id)->first();

        if (!$factura || !$factura->clave_acceso) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la factura o no tiene clave de acceso.'
            ], 404);
        }

        $noIdentificacion = $factura->factura->cliente->numero_identificacion;
        $message = "";

        if ($noIdentificacion !== "9999999999999") {
            $accessKey = $factura->clave_acceso;
            $email = $factura->factura->cliente->email;
            $nombre = "{$factura->factura->cliente->nombres} {$factura->factura->cliente->apellidos}";
            SriService::sendEmail($accessKey, $email, $nombre, str_pad($factura->factura->secuencia_factura, 9, '0', STR_PAD_LEFT));
            $message = "Correo reenviado a {$email}.";
        } else {
            $message = "La factura pertenece a CONSUMIDOR FINAL, no se envió el correo.";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function resendQrEmail($id)
    {
        $invoice = FacturaCabecera::with('cliente')->find($id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la factura.'
            ], 404);
        }

        $qr = new QrController();
        $qrCode = $qr->generateQrCode($id);

        $cliente = $invoice->cliente->nombre_completo;

        if ($invoice->cliente->numero_identificacion === '9999999999999') {
            return response()->json([
                'success' => false,
                'message' => 'La factura pertenece a CONSUMIDOR FINAL, no se envió el correo.'
            ]);
        }

        $numero_factura = str_pad($invoice->secuencia_factura, 9, '0', STR_PAD_LEFT);

        SriService::sendQrEmail($cliente, $numero_factura, $qrCode, $invoice->cliente->email);

        return response()->json([
            'success' => true,
            'message' => "Correo reenviado a {$invoice->cliente->email}."
        ]);
    }

    public function listadoFacturas()
    {
        $facturas = FacturaCabecera::with('cliente', 'facturaEstadoSri')->withCasts([
            'fecha' => 'datetime:d-m-Y H:i',
        ])->orderByDesc('id')->where('fecha', '>=', now()->subDays(5))->get()
            ->map(fn($factura) => [
                'id' => $factura->id,
                'secuencia_factura' => $factura->secuencia_factura,
                'fecha' => $factura->fecha->format('d-m-Y H:i'),
                'nombres' => $factura->cliente->nombres ?? null,
                'apellidos' => $factura->cliente->apellidos ?? null,
                'total_factura' => $factura->total_factura,
                'estado_autorizacion' => $factura->facturaEstadoSri->estado_autorizacion ?? 'PENDIENTE',
            ]);

        return response()->json([
            'success' => true,
            'data' => $facturas
        ]);
    }

    public function anularFactura($id)
    {
        $factura = FacturaCabecera::find($id);

        if (!$factura) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la factura.'
            ], 404);
        }

        $result = $this->anularValoresFactura($factura);

        return response()->json($result);
    }

    /**
     * Reenviar una factura al SRI para su autorización.
     */
    public function resendToSriInvoice($facturaId)
    {
        $config = Config::first();
        $saved = FacturaEstadoSri::where('factura_cabecera_id', $facturaId);

        $initialData = $this->getInitialData($saved, $facturaId);

        $xml = $initialData['xml'];
        $accessKey = $initialData['accessKey'];
        $newXML = $initialData['newXML'];
        $reception = null;
        $validatedAuth = null;

        if (!$saved->exists()) {
            $reception = SriService::signXML($xml, $accessKey, $facturaId);

            if ($reception['estado'] && $reception['estado'] !== 'RECIBIDA') {
                return response()->json([
                    'success' => false,
                    'message' => $reception['mensaje'] ?: 'Error en la recepción del comprobante por el SRI.',
                    'reception' => $reception
                ]);
            }

            $saved = $reception['factura_sri'];
        } else {
            $saved = $saved->first();

            if ($saved->estado_recepcion === 'RECIBIDA' && $saved->estado_autorizacion === 'AUTORIZADO') {
                return response()->json([
                    'success' => true,
                    'message' => 'La factura ya ha sido autorizada previamente por el SRI.',
                ]);
            }

            if ($newXML || (($saved->estado_recepcion === 'DEVUELTA' || $saved->estado_recepcion === 'PENDIENTE') && $saved->estado_autorizacion !== 'AUTORIZADO')) {
                $reception = SriService::signXML($xml, $accessKey, $facturaId);
                
                if ($reception['estado'] && $reception['estado'] !== 'RECIBIDA') {
                    return response()->json([
                        'success' => false,
                        'message' => $reception['mensaje'] ?: 'Error en la recepción del comprobante por el SRI.',
                        'reception' => $reception
                    ]);
                }
                
                $saved = $reception['factura_sri'];
            }
        }

        $validatedAuth = SriService::validateAuthorization($accessKey, $saved, $config->ambiente);

        $saved = $saved->fresh();

        if ($saved->estado_recepcion === 'DEVUELTA' && $validatedAuth['estado'] !== 'AUTORIZADO') {
            return response()->json([
                'success' => false,
                'message' => 'La factura ha sido devuelta por el SRI. Por favor, verifique los detalles y/o comuníquelo.',
                'authorization' => $validatedAuth,
                'reception' => $reception
            ]);
        }

        if ($validatedAuth['estado'] === 'AUTORIZADO') {
            $saved->load('factura', 'factura.cliente');

            $cliente = $saved->factura->cliente;

            if ($cliente->numero_identificacion !== "9999999999999") {
                $nombreCliente = "{$cliente->nombres} {$cliente->apellidos}";
                $numeroFactura = str_pad($saved->factura->secuencia_factura, 9, '0', STR_PAD_LEFT);
                SriService::sendEmail($accessKey, $cliente->email, $nombreCliente, $numeroFactura);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Factura reenviada al SRI correctamente.',
            'reception' => $reception,
            'authorization' => $validatedAuth
        ]);
    }

    public function execResendToSriInvoice()
    {
        Artisan::call('app:sri');

        return redirect()->back()->with('success', 'Se ha levantado un proceso interno que va a intentar reenviar las facturas pendientes al SRI.');
    }

    private function getInitialData($sriQuery, $facturaId)
    {
        $newXML = false;
        if ($sriQuery->exists()) {
            $accessKey = $sriQuery->first()->clave_acceso;
            $xml = Storage::get("xml/generated/{$accessKey}.xml");

            if (!$xml) {
                $generatedXml = $this->generateNewXml($facturaId);

                $newXML = true;
                $arrayMerged =  array_merge($generatedXml, ['newXML' => $newXML]);

                return $arrayMerged;
            }

            return [
                'xml' => $xml,
                'accessKey' => $accessKey,
                'newXML' => $newXML
            ];
        }

        $newXmlGenerated = $this->generateNewXml($facturaId);
        $newXML = true;
        $arrayMerged = array_merge($newXmlGenerated, ['newXML' => $newXML]);

        return $arrayMerged;
    }

    private function generateNewXml($facturaId)
    {
        $invoice = FacturaCabecera::with('cliente', 'detalles')->find($facturaId);

        $invoice->detalles_modificados = $invoice->detalles->map(fn($item) => [
            'codigo' => $item->producto_id,
            'nombre' => $item->nombre_producto,
            'cantidad' => $item->cantidad,
            'precio_unitario' => $item->precio_unitario,
            'total' => $item->total
        ]);

        $newXmlGenerated = $this->generateInvoiceXml($invoice);

        return $newXmlGenerated;
    }

    private function generateInvoiceXml($invoice)
    {
        $client = $invoice->cliente;
        $items = $invoice->detalles_modificados;

        $invoiceXml = new InvoiceGenerateXML();
        $xml = $invoiceXml->generateXML($invoice, $client, $items);

        return $xml;
    }

    private function anularValoresFactura(FacturaCabecera $factura)
    {
        $factura->status = 'anulada';
        $factura->save();

        $userId = Auth::id();
        $now = now()->setTimezone('America/Guayaquil');

        $factura->load('detalles', 'detalles.producto');

        $movimientos = $factura->detalles->map(fn($item) => [
            'producto_id' => $item->producto_id,
            'user_id' => $userId,
            'jornada_id' => $item->jornada_id,
            'tipo_movimiento' => 'ingreso',
            'stock_anterior' => $item->producto->cantidad,
            'stock_agregado' => +$item->cantidad,
            'stock_nuevo' => $item->producto->cantidad_actual + $item->cantidad,
            'motivo' => "Anular Fact # {$factura->secuencia_factura}",
            'fecha' => $now
        ])->toArray();

        $this->anularValoresKardex($movimientos);
        $this->revertItemstoProducts($factura);
        $this->revokeQr($factura->id);

        return [
            'success' => true,
            'message' => 'Factura anulada correctamente.'
        ];
    }

    private function anularValoresKardex($movimientos)
    {
        MovimientoStock::insert($movimientos);
    }

    private function revertItemstoProducts(FacturaCabecera $factura)
    {
        $factura->load('detalles', 'detalles.producto');

        $factura->detalles->each(function ($item) {
            $producto = $item->producto;
            if ($producto) {
                if ($producto->id_abono) {
                    $producto->cantidad_actual += $item->cantidad;
                } else {
                    ProductoJornada::where('id_producto', $producto->id)
                        ->where('id_jornada', $item->jornada_id)
                        ->increment('stock_actual', $item->cantidad);
                }

                $producto->save();
            }
        });
    }

    private function revokeQr($facturaId)
    {
        Qr::where('factura_id', $facturaId)->update([
            'status' => 'inactive'
        ]);
    }
}
