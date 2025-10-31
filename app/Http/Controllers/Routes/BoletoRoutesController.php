<?php

namespace App\Http\Controllers\Routes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\QrController;
use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Config;
use App\Models\FacturaCabecera;
use App\Models\FacturaDetalle;
use App\Models\FormaPago;
use App\Models\Jornada;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\ProductoJornada;
use App\Models\Transaccion;
use App\Models\User;
use App\Services\InvoiceGenerateXML;
use App\Services\SriService;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoletoRoutesController extends Controller
{
    /**
     * Se muestra la vista de venta de boletos.
     * Se obtienen los abonos activos con sus productos asociados y las jornadas activas con sus productos.
     * Apunta a la vista 'components.boletos.boletos-venta' los datos obtenidos.
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function boletos()
    {
        $abonos = Abono::where('estado', 1)->where('mostrar_en_web', true)->has('productos')->with('productos')->get()->map(function ($item) {
            $item->costo_total = round(floatval($item->costo_total) * 1.1, 2);
            $item->costo_sin_iva = round($item->costo_total / 1.15, 4);
            return $item;
        });

        $jornadas = ProductoJornada::with(['producto', 'jornada'])
            ->whereHas('jornada', function ($query) {
                $query->where('estado', 1);
            })
            ->get()
            ->sortBy('jornada.fecha_inicio')
            ->groupBy('jornada');

        return view('components.boletos.boletos-venta', ['abonos' => $abonos, 'jornadas' => $jornadas]);
    }

    public function validarCompra(Request $request)
    {
        $id = $request->query('id');
        $clientTransactionId = $request->query('clientTransactionId');

        $rp = $this->validateTransaction($id, $clientTransactionId, $request);

        return view('components.boletos.checkout', [
            'response' => $rp
        ]);
    }

    private function validateTransaction($id, $clientTransactionId, Request $request)
    {
        $token = config('app.payphone.token');
        $response = null;

        try {
            $response = Http::withToken($token)->withHeader('Content-Type', 'application/json')->post(
                config('app.payphone.confirm_url'),
                [
                    'id' => $id,
                    'clientTxId' => $clientTransactionId
                ]
            );
        } catch (\Exception $e) {
            return null;
        }

        $status = null;
        $rp = $response->json();

        $status = isset($rp['errorCode']) ? 2 : ($rp['statusCode'] ?? 2);

        $transaction = Transaccion::where('clientTransactionId', $clientTransactionId);
        $transactionData = $transaction->first();

        if ($status === 2) {
            $this->revertStockMovements($clientTransactionId);
        }

        if ($status === 3 && $transactionData->status === '-1') {

            $data = json_decode($transactionData->invoice_data, true);
            $authUser = $request->user()->id ?? User::first()->id;

            $this->completeInvoice($authUser, $data['client'], $data['items'], $data['resume'], $rp['cardType']);
        }

        $transaction->update([
            'status' => $status,
            'response_data' => $rp
        ]);

        return $rp;
    }

    private function completeInvoice($authUser, $clientData, $itemsData, $resumeData, $cardType)
    {
        try {
            return DB::transaction(function () use ($authUser, $clientData, $itemsData, $resumeData, $cardType) {
                $lastFactura = FacturaCabecera::orderByDesc('id')->first();

                $config = Config::first();

                $lastFactura ?
                    $nextSecuencia = max(
                        (int) $lastFactura->secuencia_factura + 1,
                        (int) $config->numero_factura
                    ) : $nextSecuencia = $config->numero_factura;

                $cliente = Cliente::updateOrCreate(
                    ['numero_identificacion' => $clientData['numero_identificacion']],
                    [
                        'nombres' => $clientData['nombres'],
                        'apellidos' => $clientData['apellidos'],
                        'telefono' => $clientData['telefono'],
                        'direccion' => $clientData['direccion'] ?? 'AZOGUES',
                        'email' => $clientData['email'],
                    ]
                );

                $codeToSearch = strtolower($cardType) === 'credit' ? '19' : '16';
                $formaPago = FormaPago::where('codigo', $codeToSearch)->first();

                /**
                 * Crear la cabecera de la factura
                 */
                $cabecera = FacturaCabecera::create([
                    'user_id' => $authUser,
                    'secuencia_factura' => $nextSecuencia,
                    'fecha' => now(),
                    'cliente_id' => $cliente->id,
                    'forma_pago' => $formaPago->id,
                    'subtotal15' => $resumeData['subtotal15'],
                    'subtotal5' => $resumeData['subtotal5'],
                    'subtotal0' => $resumeData['subtotal0'],
                    'descuento' => $resumeData['descuento'],
                    'iva15' => $resumeData['iva15'],
                    'iva5' => $resumeData['iva5'],
                    'ice' => $resumeData['ice'],
                    'adicional' => $resumeData['adicional'],
                    'total_factura' => $resumeData['total'],
                ]);

                $total_boletos = 0;

                /**
                 * Guardar detalles y actualizar stock
                 */
                foreach ($itemsData as $item) {

                    FacturaDetalle::create([
                        'factura_id' => $cabecera->id,
                        'producto_id' => $item['id'],
                        'abono_id' => $item['abono_id'] ?? null,
                        'jornada_id' => $item['jornada_id'] ?? null,
                        'nombre_producto' => $item['nombre'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'total' => $item['total'],
                    ]);

                    $cantidad_boletos = 0;

                    if (isset($item['abono_id'])) {
                        $abono = Abono::find($item['abono_id']);
                        $cantidad_boletos = $abono->numero_entradas * $item['cantidad'];
                    } elseif (isset($item['jornada_id'])) {
                        $cantidad_boletos = $item['cantidad'];
                    }

                    $total_boletos += $cantidad_boletos;
                }

                /**
                 * Registrar boletos en la tabla de facturas_sorteo
                 */
                if ($total_boletos > 0) {
                    $periodo = DB::table('periodo_campeonato')
                        ->where('status', 'activo')
                        ->first();

                    if ($periodo) {
                        foreach ($itemsData as $prod) {
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
                                    'nombre' => trim("{$cliente->nombres} {$cliente->apellidos}"),
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
                                        'nombre_cliente' => trim("{$cliente->nombres} {$cliente->apellidos}"),
                                        'periodo_id'     => $periodo->id,
                                        'producto_id'    => $producto_id,
                                        'nombre_producto' => $nombre_producto,
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
                }

                $cabecera->fecha->setTimezone(new DateTimeZone('America/Guayaquil'));

                $xmlGenerated = $this->generateXML($cabecera, $itemsData, $cliente);

                $reception = SriService::signXML($xmlGenerated['xml'], $xmlGenerated['accessKey'], $cabecera->id);

                if ($reception['estado'] && $reception['estado'] !== 'RECIBIDA') {

                    return response()->json([
                        'success' => false,
                        'message' => $reception['mensaje'] ?: 'Error en la recepción del comprobante por el SRI.',
                        'reception' => $reception
                    ]);
                }

                $validateAuthorization = SriService::validateAuthorization($xmlGenerated['accessKey'], $reception['factura_sri'], $config->ambiente);

                if ($cliente->numero_identificacion !== '9999999999999') {
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
                    // 'authorization' => $validateAuthorization,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error al completar la factura: ' . $e->getMessage());
            return false;
        }
    }

    public function revertStockMovements($uniqueCode)
    {
        MovimientoStock::orderBy('fecha', 'desc')
            ->where('motivo', 'LIKE', "%{$uniqueCode}%")
            ->get()
            ->each(function ($movement): void {
                $possibleType = $movement->jornada_id ?? null;

                if ($possibleType) {
                    $queryJornada = DB::table('productos_jornada')
                        ->where('id_producto', $movement->producto_id)
                        ->where('id_jornada', $movement->jornada_id);

                    $prodJornada = $queryJornada->first();

                    if ($prodJornada) {
                        $stockAnterior = $prodJornada->stock_actual;

                        $queryJornada
                            ->update([
                                'stock_actual' => $stockAnterior + abs($movement->stock_agregado)
                            ]);

                        MovimientoStock::create([
                            'producto_id' => $movement->producto_id,
                            'user_id' => $movement->user_id,
                            'jornada_id' => $movement->jornada_id,
                            'tipo_movimiento' => 'ingreso',
                            'stock_anterior' => $stockAnterior,
                            'stock_agregado' => abs($movement->stock_agregado),
                            'stock_nuevo' => $stockAnterior + abs($movement->stock_agregado),
                            'motivo' => "Reversión de movimiento por motivo: {$movement->motivo}",
                            'fecha' => now(),
                        ]);
                    }
                } else {
                    $producto = Producto::find($movement->producto_id);
                    if ($producto) {
                        $producto->cantidad_actual += abs($movement->stock_agregado);
                        $producto->save();

                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'user_id' => $movement->user_id,
                            'jornada_id' => null,
                            'tipo_movimiento' => 'ingreso',
                            'stock_anterior' => $producto->cantidad_actual - abs($movement->stock_agregado),
                            'stock_agregado' => abs($movement->stock_agregado),
                            'stock_nuevo' => $producto->cantidad_actual,
                            'motivo' => "Reversión de movimiento por motivo: {$movement->motivo}",
                            'fecha' => now(),
                        ]);
                    }
                }
            });
    }

    private function generateXML($cabecera, $detalles, $cliente)
    {
        $invoiceGenerateXML = new InvoiceGenerateXML();
        return $invoiceGenerateXML->generateXML($cabecera, $cliente, $detalles);
    }
}
