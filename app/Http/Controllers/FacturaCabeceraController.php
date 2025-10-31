<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Cliente;
use App\Services\SriService;
use App\Services\XadesBesSriSigner;
use App\Services\InvoiceGenerateXML;
use App\Models\FormaPago;
use DateTimeZone;
use DOMDocument;
use Illuminate\Http\Request;
use App\Models\FacturaDetalle;
use App\Models\Config;
use App\Models\FacturaCabecera;
use App\Models\FacturaEstadoSri;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\User;
use App\Models\Jornada;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use Carbon\Carbon;

class FacturaCabeceraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facturas = FacturaCabecera::with(['detalles', 'cliente', 'formaPago'])->get();
        return response()->json($facturas);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $factura = FacturaCabecera::create($request->all());

        // si vienen detalles
        if ($request->has('detalles')) {
            foreach ($request->detalles as $detalle) {
                $factura->detalles()->create($detalle);
            }
        }

        return response()->json($factura->load('detalles'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $factura = FacturaCabecera::with(['detalles', 'cliente', 'formaPago'])->findOrFail($id);
        return response()->json($factura);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $factura = FacturaCabecera::findOrFail($id);
        $factura->update($request->all());
        return response()->json($factura);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $factura = FacturaCabecera::findOrFail($id);
        $factura->delete();
        return response()->json(['message' => 'Factura eliminada correctamente']);
    }


    public function almacenarFactura(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $total = $request->resumen['total'];
                $clienteId = $request->cliente['id'];

                $cliente = Cliente::find($clienteId);

                if (!$cliente) {
                    throw new \Exception('Cliente no encontrado.');
                }

                if ($cliente->numero_identificacion === "9999999999999" && floatval($total) > 50) {
                    throw new \Exception('El total de la factura no puede exceder los $50 cuando el cliente es CONSUMIDOR FINAL.');
                }

                // 1. Determinar la secuencia de factura
                $lastFactura = FacturaCabecera::orderByDesc('id')->first();
                // Traer el primer dato de config
                $config = Config::first();

                if ($lastFactura) {
                    $nextSecuencia = max(
                        (int) $lastFactura->secuencia_factura + 1,
                        (int) $config->numero_factura
                    );
                } else {
                    $nextSecuencia = $config->numero_factura;
                }

                $authenticatedUser = $request->user()->id;

                // We set a default user if no authenticated user is found. But, this should not happen in production.
                if (!$authenticatedUser) {
                    $authenticatedUser = User::first()->id;
                }

                // 2. Guardar cabecera
                $cabecera = FacturaCabecera::create([
                    'user_id' => $authenticatedUser,
                    'secuencia_factura' => $nextSecuencia,
                    'fecha' => now(),
                    'cliente_id' => $request->cliente['id'],
                    'forma_pago' => $request->resumen['forma_pago'],
                    'subtotal15' => $request->resumen['subtotal15'],
                    'subtotal5' => $request->resumen['subtotal5'],
                    'subtotal0' => $request->resumen['subtotal0'],
                    'descuento' => $request->resumen['descuento'],
                    'iva15' => $request->resumen['iva15'],
                    'iva5' => $request->resumen['iva5'],
                    'ice' => $request->resumen['ice'],
                    'adicional' => $request->resumen['adicional'],
                    'total_factura' => $request->resumen['total'],
                ]);

                // Inicializar total de boletos para facturas_sorteo
                $total_boletos = 0;

                // 3. Guardar detalles y actualizar stock
                foreach ($request->productos as $prod) {

                    // Crear detalle
                    FacturaDetalle::create([
                        'factura_id' => $cabecera->id,
                        'producto_id' => $prod['id'],
                        'abono_id' => $prod['abono_id'] ?: null,
                        'jornada_id' => $prod['jornada_id'] ?: null,
                        'nombre_producto' => $prod['nombre'],
                        'cantidad' => $prod['cantidad'],
                        'precio_unitario' => $prod['precio_unitario'],
                        'total' => $prod['total'],
                    ]);

                    $cantidad_boletos = 0;

                    // Abono -> tabla productos
                    if (!empty($prod['abono_id'])) {
                        $producto = Producto::find($prod['id']);
                        $stockAnterior = $producto->cantidad_actual;
                        $producto->cantidad_actual = $stockAnterior - $prod['cantidad'];
                        $producto->save();

                        MovimientoStock::create([
                            'producto_id' => $prod['id'],
                            'user_id' => 1,
                            'jornada_id' => null,
                            'tipo_movimiento' => 'egreso',
                            'stock_anterior' => $stockAnterior,
                            'stock_agregado' => -$prod['cantidad'],
                            'stock_nuevo' => $producto->cantidad_actual,
                            'motivo' => 'Fact #' . $cabecera->secuencia_factura,
                            'fecha' => now()->setTimezone('America/Guayaquil')
                        ]);

                        // Calcular boletos según abono
                        $abono = Abono::find($prod['abono_id']);
                        $cantidad_boletos = $abono->numero_entradas * $prod['cantidad'];
                    }
                    // Ticket -> tabla productos_jornada
                    elseif (!empty($prod['jornada_id'])) {
                        $prodJornada = DB::table('productos_jornada')
                            ->where('id_producto', $prod['id'])
                            ->where('id_jornada', $prod['jornada_id'])
                            ->first();

                        if ($prodJornada) {
                            $stockAnterior = $prodJornada->stock_actual;
                            DB::table('productos_jornada')
                                ->where('id_producto', $prod['id'])
                                ->where('id_jornada', $prod['jornada_id'])
                                ->update([
                                    'stock_actual' => $stockAnterior - $prod['cantidad']
                                ]);

                            MovimientoStock::create([
                                'producto_id' => $prod['id'],
                                'user_id' => 1,
                                'jornada_id' => $prod['jornada_id'],
                                'tipo_movimiento' => 'egreso',
                                'stock_anterior' => $stockAnterior,
                                'stock_agregado' => -$prod['cantidad'],
                                'stock_nuevo' => $stockAnterior - $prod['cantidad'],
                                'motivo' => 'Fact #' . $cabecera->secuencia_factura,
                                'fecha' => now()->setTimezone('America/Guayaquil')
                            ]);

                            // Cantidad de boletos = cantidad de tickets comprados
                            $cantidad_boletos = $prod['cantidad'];
                        }
                    }

                    // Acumular boletos para la factura
                    $total_boletos += $cantidad_boletos;
                }

               // 4. Registrar en facturas_sorteo por cada item
                $periodo = DB::table('periodo_campeonato')
                    ->where('status', 'activo')
                    ->first();

                if ($periodo) {
                    foreach ($request->productos as $prod) {
                        $cantidad_boletos = 0;
                        $jornada_id = null;
                        $nombre_jornada = null;
                        $abono_id = null;
                        $nombre_abono = null;

                        // Producto
                        $producto_id = $prod['id'] ?? null;
                        $nombre_producto = $prod['nombre'] ?? null;

                        // Caso: abono
                        if (!empty($prod['abono_id'])) {
                            $abono = Abono::find($prod['abono_id']);
                            if ($abono) {
                                $abono_id = $abono->id;
                                $nombre_abono = $abono->nombre ?? $prod['nombre'];
                                $cantidad_boletos = $abono->numero_entradas * $prod['cantidad'];
                            }
                        }

                        // Caso: jornada
                        if (!empty($prod['jornada_id'])) {
                            $jornada = Jornada::find($prod['jornada_id']);
                            if ($jornada) {
                                $jornada_id = $jornada->id;
                                $nombre_jornada = $jornada->nombre ?? $prod['nombre'];
                                $cantidad_boletos = $prod['cantidad'];
                            }
                        }

                        if ($cantidad_boletos > 0) {
                            // Almacenar resumen en facturas_sorteo
                            DB::table('facturas_sorteo')->insertGetId([
                                'numero_factura'  => $cabecera->secuencia_factura,
                                'nombre' => trim($request->cliente['nombres'] . ' ' . $request->cliente['apellidos']),
                                'cantidad'        => $cantidad_boletos,
                                'periodo_id'      => $periodo->id,
                                'nombre_periodo'  => $periodo->nombre ?? null,
                                'jornada_id'      => $jornada_id,
                                'nombre_jornada'  => $nombre_jornada,
                                'abono_id'        => $abono_id,
                                'nombre_abono'    => $nombre_abono,
                                'producto_id'     => $producto_id,
                                'nombre_producto' => $nombre_producto,
                            ]);
                            
                            // Generar boletos individuales
                            $lastNumero = DB::table('boletos_sorteo')
                                ->where('periodo_id', $periodo->id)
                                ->max('numero_boleto') ?? 0;

                            for ($i = 1; $i <= $cantidad_boletos; $i++) {
                                $lastNumero++;
                                DB::table('boletos_sorteo')->insert([
                                    'factura_id'     => $cabecera->id,
                                    'numero_factura' => $cabecera->secuencia_factura,
                                    'nombre_cliente' => trim($request->cliente['nombres'] . ' ' . $request->cliente['apellidos']),
                                    'periodo_id'     => $periodo->id,
                                    'producto_id'    => $producto_id,
                                    'nombre_producto'=> $nombre_producto,
                                    'jornada_id'     => $jornada_id,
                                    'nombre_jornada' => $nombre_jornada,
                                    'abono_id'       => $abono_id,
                                    'nombre_abono'   => $nombre_abono,
                                    'numero_boleto'  => $lastNumero,
                                ]);
                            }
                        }
                    }
                }

                $cabecera->fecha->setTimezone(new DateTimeZone('America/Guayaquil'));

                $cliente = Cliente::find($request->cliente['id']);

                $xmlGenerated = $this->generateXML($cabecera, $request->productos, $cliente);

                $reception = SriService::signXML($xmlGenerated['xml'], $xmlGenerated['accessKey'], $cabecera->id);

                // $reception = $this->signXML($xmlGenerated['xml'], $xmlGenerated['accessKey'], $cabecera->id);

                if ($reception['estado'] && $reception['estado'] !== 'RECIBIDA') {
                    // TODO: Manejar una posible cola de reintentos

                    return response()->json([
                        'success' => false,
                        'message' => $reception['mensaje'] ?: 'Error en la recepción del comprobante por el SRI.',
                        'reception' => $reception
                    ]);
                }

                $validateAuthorization = SriService::validateAuthorization($xmlGenerated['accessKey'], $reception['factura_sri'], $config->ambiente);
                // $validateAuthorization = $this->validateAuthorization($xmlGenerated['accessKey'], $reception['factura_sri']);

                if ($cliente->numero_identificacion !== "9999999999999") {
                    $nombreCliente = "{$cliente->nombres} {$cliente->apellidos}";
                    $numeroFactura = str_pad($cabecera->secuencia_factura, 9, '0', STR_PAD_LEFT);
                    $qr = new QrController();
                    $qrCode = $qr->generateQrCode($cabecera->id);

                    SriService::sendEmail($xmlGenerated['accessKey'], $cliente->email, $nombreCliente, $numeroFactura);
                    SriService::sendQrEmail($nombreCliente, $numeroFactura, $qrCode, $cliente->email);
                }

                return response()->json([
                    'success' => true,
                    'factura_id' => $cabecera->id,
                    'secuencia' => $cabecera->secuencia_factura,
                    'total_boletos' => $total_boletos,
                    // 'authorization' => $validateAuthorization,
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al almacenar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enviarRecepcion($claveAcceso)
    {
        $xml = Storage::get("xml/signed/{$claveAcceso}_SIGNED.xml");
        $reception = $this->sendInvoiceToSRI($xml);
        return response()->json($reception);
    }

    public function verResultado($claveAcceso)
    {
        $authorization = Soap::to(endpoint: config('app.sri.authorization_prod'))->call('autorizacionComprobante', [
            'claveAccesoComprobante' => $claveAcceso
        ]);;
        return response()->json($authorization);
    }

    // Obtener el numero de factura
    public function obtenerNumeroFactura()
    {
        $lastFactura = FacturaCabecera::orderByDesc('id')->first();
        $config = Config::first();

        if ($lastFactura) {
            // Comparar último número usado vs configuración
            $nextSecuencia = max(
                (int) $lastFactura->secuencia_factura + 1,
                (int) $config->numero_factura
            );
        } else {
            // Si no hay facturas aún, iniciar con la configuración
            $nextSecuencia = $config->numero_factura;
        }

        return response()->json([
            'numero_factura' => $nextSecuencia
        ]);
    }

    public function getInvoicePdf($idFactura)
    {
        $factura = FacturaCabecera::with(['cliente', 'detalles', 'detalles.producto'])->findOrFail($idFactura);
        $qr = new QrController();
        $qrCode = $qr->generateQrCode($idFactura);
        $facturaSri = FacturaEstadoSri::where('factura_cabecera_id', $idFactura)->first();
        $config = Config::first();
        $ruc = $config->ruc;
        $logo_path = $config->logo_path;
        $logoBase64 = base64_encode(Storage::get($logo_path));

        $establecimiento = str_pad($config->codigo_establecimiento, 3, '0', STR_PAD_LEFT);
        $puntoEmision = str_pad($config->punto_emision ?? '001', 3, '0', STR_PAD_LEFT);
        $secuenciaFactura = str_pad($factura->secuencia_factura, 9, '0', STR_PAD_LEFT);
        $secuencia = "{$establecimiento}-{$puntoEmision}-{$secuenciaFactura}";

        $pdf = Pdf::loadView('pdf.print-invoice', [
            'ruc' => $ruc,
            'factura' => $secuencia,
            'config' => $config,
            'logo' => $logoBase64,
            'cliente' => ($factura->cliente->nombres ?? '') . ' ' . ($factura->cliente->apellidos ?? ''),
            'identificacion' => $factura->cliente->numero_identificacion,
            'claveAcceso' => $facturaSri ? $facturaSri->clave_acceso : 'N/A',
            'fecha' => date_create($factura->fecha)->format('d/m/Y H:i:s'),
            'total' => $factura->total_factura,
            'subtotal' => $factura->subtotal15,
            'iva' => $factura->iva15,
            'items' => $factura->detalles,
            'qrCode' => $qrCode
        ]);

        $pdf->setPaper([0, 0, 200.77, 800]); // Tamaño personalizado para tickets (80mm x 140mm)

        return $pdf->stream("factura_{$factura->secuencia_factura}.pdf");
    }

    private function generateXML($cabecera, $detalles, $cliente)
    {
        $invoiceGenerateXML = new InvoiceGenerateXML();
        return $invoiceGenerateXML->generateXML($cabecera, $cliente, $detalles);
    }

    private function signXML($result, $accessKey, $facturaId)
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

        $validatedXml = Soap::to(endpoint: config('app.sri.reception'))->call('validarComprobante', [
            'xml' => $signedXml['xml']
        ]);

        $receptionResponse = $validatedXml->response->RespuestaRecepcionComprobante;
        $message = null;

        if ($receptionResponse->estado !== 'RECIBIDA') {
            $message = $receptionResponse->comprobantes->comprobante->mensajes->mensaje->mensaje ?? null;
        }

        $facturaSri = FacturaEstadoSri::create([
            'factura_cabecera_id' => $facturaId,
            'clave_acceso' => $accessKey,
            'estado_recepcion' => strtoupper($receptionResponse->estado) ?? 'PENDIENTE',
        ]);

        return [
            'estado' => $receptionResponse->estado,
            'mensaje' => $message,
            'obj' => $receptionResponse,
            'factura_sri' => $facturaSri,
        ];
    }

    private function validateAuthorization($accessKey, FacturaEstadoSri $facturaSri)
    {
        $rp = Soap::to(endpoint: config('app.sri.authorization'))->call('autorizacionComprobante', [
            'claveAccesoComprobante' => $accessKey
        ]);

        // TODO: To remove this line
        // Log::info('Respuesta de la autorización SRI:', ['response' => $rp]);

        $authorizationResponse = $rp->response->RespuestaAutorizacionComprobante;
        $estado = $authorizationResponse->autorizaciones->autorizacion->estado ?? null;
        $numeroComprobantes = intval($authorizationResponse->numeroComprobantes) ?? 0;

        if ($numeroComprobantes === 0) {
            $facturaSri->estado_autorizacion = 'PENDIENTE';

            $facturaSri->save();
            return ['error' => 'No se encontraron autorizaciones para la clave de acceso proporcionada'];
        }

        if ($estado !== 'AUTORIZADO') {
            $facturaSri->estado_autorizacion = 'RECHAZADO';

            $facturaSri->save();
            return ['estado' => 'rechazado', 'mensaje' => $authorizationResponse->autorizaciones->autorizacion->mensajes];
        }

        if (!isset($authorizationResponse->claveAccesoConsultada)) {
            $facturaSri->estado_autorizacion = 'PENDIENTE';

            $facturaSri->save();
            return ['error' => 'Clave de acceso no encontrada en la respuesta'];
        }

        $keyLote = $authorizationResponse->claveAccesoConsultada;

        $xmlFromResponse = $this->xmlFromResponse($authorizationResponse->autorizaciones->autorizacion);

        if (!$xmlFromResponse) {
            return ['error' => 'Error al generar el XML desde la respuesta'];
        }

        Storage::put("xml/authorized/{$keyLote}_AUTORIZADO.xml", $xmlFromResponse);
        $facturaSri->estado_autorizacion = 'AUTORIZADO';

        $facturaSri->save();

        return ['estado' => 'procesado', 'rp' => $authorizationResponse];
    }

    private function xmlFromResponse($response)
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

    private function sendInvoiceToSRI($xml)
    {
        return Soap::to(config('app.sri.reception_prod'))->call('validarComprobante', [
            'xml' => $xml
        ]);
    }

    // Traer las formas de pago 
    public function formasPago()
    {
        // Trae todas las formas de pago
        $formasPago = FormaPago::orderBy('forma_pago')->get();

        // Devuelve como JSON
        return response()->json($formasPago);
    }

    public function movimientosCaja(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio'); // dd/mm/yyyy
        $fechaFin = $request->input('fechaFin');       // dd/mm/yyyy
        $formaPago = $request->input('formaPago');

        $query = FacturaCabecera::with(['usuario', 'cliente', 'formaPago']);

        // Parsear fechas al formato Y-m-d
        if ($fechaInicio && $fechaFin) {
            try {
                $inicio = Carbon::createFromFormat('d/m/Y', $fechaInicio)->startOfDay();
                $fin = Carbon::createFromFormat('d/m/Y', $fechaFin)->endOfDay();
                $query->whereBetween('fecha', [$inicio, $fin]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Formato de fecha inválido'], 422);
            }
        }

        // Filtro por forma de pago solo si no es vacío
        if (!empty($formaPago)) {
            $query->whereHas('formaPago', function ($q) use ($formaPago) {
                $q->where('codigo', $formaPago);
            });
        }

        $movimientos = $query->get()->map(function ($factura) {
            return [
                'usuario' => $factura->usuario->name ?? '',
                'fecha' => Carbon::parse($factura->fecha)->format('d/m/Y H:i:s'),
                'tipo' => 'Ingreso',
                'status' => $factura->status,
                'valor' => $factura->total_factura,
                'detalle' => 'Fact #' . $factura->secuencia_factura,
                'cliente' => trim(($factura->cliente->nombres ?? '') . ' ' . ($factura->cliente->apellidos ?? '')),
                'forma_pago' => $factura->formaPago->forma_pago ?? '',
                'codigo_forma_pago' => $factura->formaPago->codigo ?? '',
            ];
        });

        return response()->json($movimientos);
    }

    public function reporteProductos(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio'); // dd/mm/yyyy
        $fechaFin = $request->input('fechaFin');       // dd/mm/yyyy

        // Validar fechas
        try {
            $inicio = Carbon::createFromFormat('d/m/Y', $fechaInicio)->startOfDay();
            $fin = Carbon::createFromFormat('d/m/Y', $fechaFin)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido'], 422);
        }

        // Traer detalles con joins
        $detalles = FacturaDetalle::select(
            'factura_detalle.producto_id',
            'factura_detalle.abono_id',
            'factura_detalle.jornada_id',
            'productos.nombre as nombre_producto',
            'categorias.nombre as categoria',
            'productos.precio_venta_sin_iva',
            'productos.precio_venta_final as precio_unitario',
            DB::raw('SUM(factura_detalle.cantidad) as total_cantidad'),
            DB::raw('SUM(factura_detalle.cantidad * productos.precio_venta_final) as total')
        )
            ->join('factura_cabecera', 'factura_cabecera.id', '=', 'factura_detalle.factura_id')
            ->join('productos', 'productos.id', '=', 'factura_detalle.producto_id')
            ->leftJoin('categoria_productos as categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->whereBetween('factura_cabecera.fecha', [$inicio, $fin])
            ->where('factura_cabecera.status', '!=', 'anulada')
            ->groupBy(
                'factura_detalle.producto_id',
                'factura_detalle.abono_id',
                'factura_detalle.jornada_id',
                'productos.nombre',
                'categorias.nombre',
                'productos.precio_venta_sin_iva',
                'productos.precio_venta_final'
            )
            ->get();

        // Mapear datos para mostrar abono/jornada
        $resultados = $detalles->map(function ($item) {
            $tipo = 'Producto';
            $nombreExtra = null;

            if ($item->jornada_id) {
                $tipo = 'Jornada';
                $nombreExtra = \App\Models\Jornada::find($item->jornada_id)->nombre ?? '';
            } elseif ($item->abono_id) {
                $tipo = 'Abono';
                $nombreExtra = \App\Models\Abono::find($item->abono_id)->nombre ?? '';
            }

            return [
                'producto_id'   => $item->producto_id,
                'nombre'        => $item->nombre_producto,
                'categoria'     => $item->categoria,
                'tipo'          => $tipo,
                'nombre_extra'  => $nombreExtra,
                'cantidad'      => $item->total_cantidad,
                'precio_unitario' => number_format($item->precio_unitario, 2),
                'total'      => number_format($item->total, 2),
            ];
        });

        return response()->json($resultados);
    }

    public function reporteFacturas(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio'); // dd/mm/yyyy
        $fechaFin = $request->input('fechaFin');       // dd/mm/yyyy

        // Validar fechas
        try {
            $inicio = Carbon::createFromFormat('d/m/Y', $fechaInicio)->startOfDay();
            $fin = Carbon::createFromFormat('d/m/Y', $fechaFin)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido'], 422);
        }

        $facturas = FacturaCabecera::with(['cliente', 'usuario', 'detalles', 'facturaEstadoSri'])
            ->whereBetween('fecha', [$inicio, $fin])
            ->get();

        $resultados = $facturas->map(function ($factura) {

            $config = Config::first(); // Primera fila de config
            $serie = ($config->codigo_establecimiento ?? '001') . ($config->serie_ruc ?? '001');
            $estadoFactura = $factura->facturaEstadoSri->estado_autorizacion ?? 'No autorizado';
            $autorizacion = $factura->facturaEstadoSri->clave_acceso ?? '';
            $caducidad = $factura->fecha->format('d/m/Y'); // Fecha de factura

            return [
                'documento'        => 'Factura',
                'fecha'            => $factura->fecha->format('d/m/Y'),
                'emitido_a'        => $factura->cliente->nombre_completo ?? '',
                'secuencia_factura' => $factura->secuencia_factura,
                'subtotal15'       => number_format($factura->subtotal15, 2),
                'subtotal0'        => number_format($factura->subtotal0, 2),
                'iva15'            => number_format($factura->iva15, 2),
                'descuento'        => number_format($factura->descuento, 2),
                'total_factura'    => number_format($factura->total_factura, 2),
                'ruc_cliente'      => $factura->cliente->numero_identificacion ?? '',
                'estado_factura'   => $estadoFactura,
                'descuento_iva15'  => 0,
                'descuento_iva0'   => 0,
                'serie'            => $serie,
                'autorizacion'     => $autorizacion,
                'caducidad'        => $caducidad,
                'forma_pago'       => $factura->formaPago->forma_pago ?? '',
                'vendedor'         => $factura->usuario->name ?? '',
                'codigo_sustento'  => 18,
            ];
        });

        return response()->json($resultados);
    }
}
