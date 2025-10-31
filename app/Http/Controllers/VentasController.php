<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\FacturaCabecera;
use App\Models\Jornada;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Insoutt\EcValidator\EcValidator;

class VentasController extends Controller
{
    public function preSellItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'numero_identificacion' => 'required|string|max:20',
            'telefono' => 'required|phone',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        DB::beginTransaction();
        try {
            $clientData = $request->only([
                'nombres',
                'apellidos',
                'numero_identificacion',
                'telefono',
                'direccion',
                'email'
            ]);

            $noDocValidation = $this->validateNoDoc($clientData['numero_identificacion']);

            if (!$noDocValidation['success']) {
                return response()->json($noDocValidation);
            }

            $itemsData = $request->input('items');

            $resumeData = $request->input('resume');

            $invoiceData = [
                'client' => $clientData,
                'items' => $itemsData,
                'resume' => $resumeData
            ];

            $uniqueCode = $this->getUniqueCode();

            Transaccion::create([
                'clientTransactionId' => $uniqueCode['code'],
                'invoice_data' => json_encode($invoiceData),
            ]);

            collect($itemsData)->each(function ($item) use ($request, $uniqueCode) {
                $updateStock = $this->updateStockItem(
                    $item['cantidad'],
                    isset($item['jornada_id']) ? 'jornada' : 'abono',
                    $item['id'],
                    $item['jornada_id'] ?? $item['abono_id'],
                    $request->user()->id ?? User::first()->id,
                    $uniqueCode['code']
                );

                if (!$updateStock['success']) {
                    throw new \Exception($updateStock['message']);
                }
            });

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $uniqueCode['code'],
                    'reference' => $uniqueCode['reference']
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error al procesar la venta',
                    'errors' => [$e->getMessage()]
                ]
            ]);
        }
    }

    private function updateStockItem($quantity, $type, $productId, $itemId, $userId, $uniqueCode)
    {
        if ($type === 'jornada') {
            $queryJornada = DB::table('productos_jornada')
                ->join('jornadas', 'productos_jornada.id_jornada', 'jornadas.id')
                ->where('id_producto', $productId)
                ->where('id_jornada', $itemId);

            $prodJornada = $queryJornada->first();

            if ($prodJornada) {
                $stockAnterior = $prodJornada->stock_actual;

                if ($stockAnterior <= 0) {
                    return [
                        'success' => false,
                        'message' => "El producto {$prodJornada->jornada->nombre} no tiene stock disponible"
                    ];
                }

                if ($stockAnterior < $quantity) {
                    return [
                        'success' => false,
                        'message' => "Stock insuficiente para el producto {$prodJornada->jornada->nombre}"
                    ];
                }

                $queryJornada
                    ->update([
                        'stock_actual' => $stockAnterior - $quantity
                    ]);

                MovimientoStock::create([
                    'producto_id' => $productId,
                    'user_id' => $userId,
                    'jornada_id' => $itemId,
                    'tipo_movimiento' => 'egreso',
                    'stock_anterior' => $stockAnterior,
                    'stock_agregado' => -$quantity,
                    'stock_nuevo' => $stockAnterior - $quantity,
                    'motivo' => "Pre-venta en transacción online {$uniqueCode}",
                    'fecha' => now(),
                ]);
            }

            return [
                'success' => true
            ];
        }

        if ($type === 'abono') {
            $producto = Producto::find($productId);
            $stockAnterior = $producto->cantidad_actual;

            $producto->load('abonoRelacion');

            if ($stockAnterior <= 0) {
                return [
                    'success' => false,
                    'message' => "El producto {$producto->abonoRelacion->nombre} no tiene stock disponible"
                ];
            }

            if ($stockAnterior < $quantity) {
                return [
                    'success' => false,
                    'message' => "Stock insuficiente para el producto {$producto->abonoRelacion->nombre}"
                ];
            }

            $producto->cantidad_actual = $stockAnterior - $quantity;
            $producto->save();

            MovimientoStock::create([
                'producto_id' => $productId,
                'user_id' => $userId,
                'jornada_id' => null,
                'tipo_movimiento' => 'egreso',
                'stock_anterior' => $stockAnterior,
                'stock_agregado' => -$quantity,
                'stock_nuevo' => $stockAnterior - $quantity,
                'motivo' => "Pre-venta en transacción online {$uniqueCode}",
                'fecha' => now(),
            ]);

            return [
                'success' => true
            ];
        }
    }

    public function getUniqueCode()
    {
        $lastFactura = FacturaCabecera::orderByDesc('id')->first();

        $config = Config::first();

        $lastFactura ?
            $nextSecuencia = max(
                (int) $lastFactura->secuencia_factura + 1,
                (int) $config->numero_factura
            ) : $nextSecuencia = $config->numero_factura;

        return [
            'code' => str_pad(rand(0, 999999999999999), 15, '0', STR_PAD_LEFT),
            'reference' => "Pago por venta online"
        ];
    }

    private function validateNoDoc($numero_identificacion)
    {
        $validator = new EcValidator();

        $status = $validator->validateCedula($numero_identificacion);

        if (!$status) {
            return [
                'success' => false,
                'message' => "Cédula no válida: {$validator->getError()}"
            ];
        }

        return [
            'success' => true
        ];
    }
}
