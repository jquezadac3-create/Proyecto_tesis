<?php

namespace App\Services;

use App\Mail\QrCode;
use App\Mail\SriInvoice;
use App\Models\Config;
use App\Models\FacturaEstadoSri;
use Barryvdh\DomPDF\Facade\Pdf;
use DOMDocument;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\Renderers\SvgRenderer;
use Picqer\Barcode\Types\TypeCode128;
use RicorocksDigitalAgency\Soap\Facades\Soap;

class SriService
{

    public static function signXML($result, $accessKey, $facturaId)
    {
        if (!$result) {
            throw new \Exception('El XML no se generó correctamente');
        }

        $config = Config::first();

        $decryptedPass = Crypt::decryptString($config->firma_contrasenia);
        $pathCertificate = Storage::path($config->firma_path);

        $xadesSigner = new XadesBesSriSigner($pathCertificate, $decryptedPass);

        $signedXml = $xadesSigner->signXml($result);

        Storage::put("xml/signed/{$accessKey}_SIGNED.xml", $signedXml['xml']);

        $endpoint = $config->ambiente === 'PRODUCCION' ? config('app.sri.reception_prod') : config('app.sri.reception');

        $validatedXml = Soap::to(endpoint: $endpoint)->call('validarComprobante', [
            'xml' => $signedXml['xml']
        ]);

        $receptionResponse = $validatedXml->response->RespuestaRecepcionComprobante;
        $message = null;

        if ($receptionResponse->estado !== 'RECIBIDA') {
            Log::error('Error en la recepción del comprobante por el SRI: ', [($receptionResponse->comprobantes->comprobante->mensajes ?? 'Mensaje no disponible')]);
            $message = $receptionResponse->comprobantes->comprobante->mensajes->mensaje->mensaje ?? null;
        }

        $facturaSri = FacturaEstadoSri::updateOrCreate(
            [
                'factura_cabecera_id' => $facturaId,
            ],
            [
                'clave_acceso' => $accessKey,
                'estado_recepcion' => strtoupper($receptionResponse->estado) ?? 'PENDIENTE',
            ]
        );

        return [
            'estado' => $receptionResponse->estado,
            'mensaje' => $message,
            'obj' => $receptionResponse,
            'factura_sri' => $facturaSri,
        ];
    }

    public static function validateAuthorization($accessKey, FacturaEstadoSri $facturaSri, $configAmbiente = 'PRUEBAS')
    {
        $endpoint = $configAmbiente === 'PRODUCCION' ? config('app.sri.authorization_prod') : config('app.sri.authorization');
        $rp = Soap::to(endpoint: $endpoint)->call('autorizacionComprobante', [
            'claveAccesoComprobante' => $accessKey
        ]);

        $authorizationResponse = $rp->response->RespuestaAutorizacionComprobante;
        $estado = $authorizationResponse->autorizaciones->autorizacion->estado ?? null;
        $numeroComprobantes = intval($authorizationResponse->numeroComprobantes) ?? 0;

        if (!$facturaSri) {
            $facturaSri = FacturaEstadoSri::where('clave_acceso', $accessKey)->first();
        }

        if (!$facturaSri) {
            return ['estado' => 'PENDIENTE', 'error' => 'No existe una factura enviada al SRI.'];
        }

        if ($numeroComprobantes === 0) {
            $facturaSri->estado_autorizacion = 'PENDIENTE';

            $facturaSri->save();
            return ['estado' => 'PENDIENTE', 'error' => 'No se encontraron autorizaciones para la clave de acceso proporcionada'];
        }

        if ($estado !== 'AUTORIZADO') {
            $facturaSri->estado_autorizacion = 'RECHAZADO';

            $facturaSri->save();
            return ['estado' => 'RECHAZADO', 'mensaje' => $authorizationResponse->autorizaciones->autorizacion->mensajes];
        }

        if (!isset($authorizationResponse->claveAccesoConsultada)) {
            $facturaSri->estado_autorizacion = 'PENDIENTE';

            $facturaSri->save();
            return ['error' => 'Clave de acceso no encontrada en la respuesta'];
        }

        $keyLote = $authorizationResponse->claveAccesoConsultada;

        $xmlFromResponse = self::xmlFromResponse($authorizationResponse->autorizaciones->autorizacion);

        if (!$xmlFromResponse) {
            return ['error' => 'Error al generar el XML desde la respuesta'];
        }

        Storage::put("xml/authorized/{$keyLote}_AUTORIZADO.xml", $xmlFromResponse);
        $facturaSri->estado_autorizacion = 'AUTORIZADO';

        $facturaSri->save();

        self::getRenderedPDF($keyLote);

        return ['estado' => $facturaSri->estado_autorizacion, 'rp' => $authorizationResponse];
    }

    public static function sendInvoiceToSRI($xml)
    {
        return Soap::to(config('app.sri.reception'))->call('validarComprobante', [
            'xml' => $xml
        ]);
    }

    public static function getRenderedPDF($authorizationCode)
    {
        $invoice = self::objectFromXml($authorizationCode);
        $barcode = self::renderBarCode($invoice->numeroAutorizacion);

        // return view('pdf.invoice', ['invoice' => $invoice, 'barcode' => $barcode]);
        $config = Config::first();
        $logo = $config->logo_path ? Storage::get($config->logo_path) : '';
        $logoBase64 = null;

        if ($logo) {
            $logoBase64 = 'data:image/' . pathinfo($config->logo_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logo);
        }

        Pdf::loadView('pdf.email-invoice', ['invoice' => $invoice, 'barcode' => $barcode, 'logo' => $logoBase64])->setWarnings(false)->save("pdf/{$authorizationCode}.pdf", 'local');

        return [
            'success' => true,
            'message' => 'PDF created successfully'
        ];
    }

    private static function xmlFromResponse($response)
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->formatOutput = true;

        $root = $doc->createElement('autorizacion');

        $estado = $doc->createElement('estado', $response->estado);
        $root->appendChild($estado);

        $numeroAutorizacion = $doc->createElement('numeroAutorizacion', $response->numeroAutorizacion);
        $root->appendChild($numeroAutorizacion);

        $fechaAutorizacion = $doc->createElement('fechaAutorizacion', $response->fechaAutorizacion);
        $root->appendChild($fechaAutorizacion);

        $ambiente = $doc->createElement('ambiente', $response->ambiente);
        $root->appendChild($ambiente);

        $comprobanteNode = $doc->createElement('comprobante');

        $comprobanteDoc = new DOMDocument();
        $comprobanteDoc->loadXML($response->comprobante);

        $imported = $doc->importNode($comprobanteDoc->documentElement, true);
        $comprobanteNode->appendChild($imported);

        $root->appendChild($comprobanteNode);

        $doc->appendChild($root);

        return $doc->saveXML();
    }

    private static function objectFromXml($authorizedCode)
    {
        $xml = Storage::get("xml/authorized/{$authorizedCode}_AUTORIZADO.xml");
        return simplexml_load_string($xml);
    }

    private static function renderBarCode($code)
    {
        $barcode = new TypeCode128();
        $barcode = $barcode->getBarcode($code);

        $renderer = new SvgRenderer();
        $renderer->setSvgType($renderer::TYPE_SVG_INLINE);
        $rendered = $renderer->render($barcode);

        $barcode = base64_encode($rendered);

        return "data:image/svg+xml;base64,{$barcode}";
    }

    public static function sendEmail($accessKey, $toEmail, $toName, $numeroFactura)
    {
        $pdfPath = "pdf/{$accessKey}.pdf";
        $authorizedXmlPath = "xml/authorized/{$accessKey}_AUTORIZADO.xml";

        try {
            Mail::to(new Address(
                $toEmail,
                $toName
            ))->send(new SriInvoice($pdfPath, $authorizedXmlPath, $accessKey, $numeroFactura, $toName));
        } catch (\Exception $ex) {
            Log::error('Error al enviar el email con QR: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar el email: ' . $ex->getMessage(),
            ];
        }
    }

    public static function sendQrEmail($cliente, $numero_factura, $qr_code, $toEmail)
    {
        try {
            Mail::to(new Address(
                $toEmail,
                $cliente
            ))->send(new QrCode($cliente, $numero_factura, $qr_code));
        } catch (\Exception $ex) {
            Log::error('Error al enviar el email con QR: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error al enviar el email con QR: ' . $ex->getMessage(),
            ];
        }
    }
}
