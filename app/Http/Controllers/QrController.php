<?php

namespace App\Http\Controllers;

use App\Models\FacturaCabecera;
use App\Models\Qr;
use App\Models\User;
use HeroQR\Core\QRCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QrController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware for the controller.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage qr codes', ['show']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $qr = Qr::find($id);

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no encontrado.',
            ]);
        }

        if ($qr->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'El código QR no se encuentra activo, posiblemente la factura fue anulada.',
            ]);
        }

        $modifiedVal = $this->discountQuantity($qr);

        $qr_code_data = json_decode($modifiedVal['qr']->qr_code_data);

        $invoiceDate = FacturaCabecera::find($qr->factura_id);

        if ($invoiceDate) {
            $invoiceDate->load('cliente');
        }

        $invoiceDate = [
            'fecha' => $invoiceDate->fecha->format('Y-m-d H:i:s'),
            'secuencia_factura' => $invoiceDate->secuencia_factura,
            'cliente' => [
                'nombres' => $invoiceDate->cliente->nombres,
                'apellidos' => $invoiceDate->cliente->apellidos,
                'numero_identificacion' => $invoiceDate->cliente->numero_identificacion,
                'direccion' => $invoiceDate->cliente->direccion,
            ],
        ];

        return response()->json([
            'success' => true,
            'discounted' => $modifiedVal['discounted'],
            'reason' => $modifiedVal['reason'],
            'abonoDiscounted' => $modifiedVal['abonoDiscounted'],
            'message' => $modifiedVal['discounted'] ? 'Cantidad descontada exitosamente.' : 'No se descontó el producto.',
            'data' => compact('qr_code_data', 'invoiceDate')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Este método generará la data que contendrá el código QR
     * La data está contenida en una URL firmada que contendrá información de la factura y mediante a esta podremos validar su autenticidad y vigencia.
     * @param mixed $facturaId
     * @return string
     */
    private function generateQrData($facturaId)
    {
        $currentUser = Auth::user()->id ?? User::first()->id;

        $invoiceWithItems = FacturaCabecera::with('cliente', 'detalles', 'detalles.producto', 'detalles.producto.abonoRelacion')->find($facturaId);

        $qrData = [
            'factura_id' => $invoiceWithItems->id,
            'cliente' => $invoiceWithItems->cliente->id,
            'total' => $invoiceWithItems->total_factura,
            'iva' => $invoiceWithItems->iva15 + $invoiceWithItems->iva5,
            'items' => $invoiceWithItems->detalles->map(function ($item) {
                $cantidad = $item->producto->abonoRelacion ? ($item->producto->abonoRelacion->numero_entradas * $item->cantidad) : $item->cantidad;

                return [
                    'detalle' => $item->id,
                    'abono_id' => $item->abono_id,
                    'jornada_id' => $item->jornada_id,
                    'factura_nombre' => $item->nombre_producto,
                    'producto' => $item->producto->nombre,
                    'cantidad_inicial' => $cantidad,
                    'cantidad_restante' => $cantidad,
                    'precio_total' => $item->total,
                    'last_updated' => null,
                ];
            }),
        ];

        $qr = Qr::create([
            'factura_id' => $facturaId,
            'authorized_by' => $currentUser,
            'qr_code_data' => json_encode($qrData),
        ]);

        return $qr->id;
    }

    public function generateQrCode($facturaId)
    {
        $qrData = Qr::where('factura_id', $facturaId)->first();

        if (!$qrData) {
            $this->generateQrData($facturaId);
            $qrData = Qr::where('factura_id', $facturaId)->first();
        }

        $qrCodeManager = new QRCodeGenerator();

        $qrCode = $qrCodeManager->setData($qrData->id)->generate();

        return $qrCode->getDataUri();
    }

    /**
     * Try to discount the quantity of the first item in the invoice
     * @param \App\Models\Qr $qr
     * @return array ['qr' => Qr, 'discounted' => bool]
     */
    private function discountQuantity(Qr $qr): array
    {
        $discounted = false;
        $abonoDiscounted = false;
        $qr_data_decoded = json_decode($qr->qr_code_data, true);
        $reasons = [];
        $localTZ = new \DateTimeZone('America/Guayaquil');

        $jornadaItems = array_filter($qr_data_decoded['items'], fn($item) => $item['jornada_id'] !== null);
        $abonoItems = array_filter($qr_data_decoded['items'], fn($item) => $item['abono_id'] !== null);

        $jornadaIds = array_map(fn($item) => $item['jornada_id'], $jornadaItems);
        $abonoIds = array_map(fn($item) => $item['abono_id'], $abonoItems);

        $jornadas = DB::table('jornadas')->whereIn('id', $jornadaIds)->get()->keyBy('id');
        $abonos = DB::table('abonos')->whereIn('id', $abonoIds)->get()->keyBy('id');

        $items_modified = array_map(function ($item) use (&$discounted, &$reasons, &$abonoDiscounted, $jornadas, $abonos, $localTZ) {
            $estadoValido = true;
            $motivo = null;

            if ($item['jornada_id'] !== null) {
                $jornada = $jornadas[$item['jornada_id']] ?? null;

                /**
                 * Validar que la jornada esté activa y que la fecha actual esté dentro del rango de fechas de la jornada
                 * TODO: Preguntar si se debe verificar que la fecha actual sea dentro solo el día de rango de fechas o solo con fecha y hora validar
                 */
                if ($jornada) {
                    $now = new \DateTime();
                    $fecha_inicio = new \DateTime($jornada->fecha_inicio);
                    $fecha_fin = new \DateTime($jornada->fecha_fin);

                    /**
                     * Validación de rango de fechas
                     * Si la fecha actual es menor a la fecha de inicio o mayor a la fecha final, no se puede descontar
                     * Ex: Si la jornada es del 30-09-2025 18:05 al 30-09-2025 19:30, y hoy es 30-09-2025 18:00, no se puede descontar
                     */
                    // if ($now < $fecha_inicio || $now > $fecha_fin) {
                    //     $estadoValido = false;
                    //     $motivo = 'jornada-fuera-de-fecha';
                    // }

                    /**
                     * Validacion por día
                     * Si el día está dentro del rango de fechas especificados, se puede descontar
                     * Ex: Si la jornada es del 30-09-2025 18:05 al 30-09-2025 19:30, y hoy es 30-09-2025, se puede descontar
                     */
                    $inicio_dia = (clone $fecha_inicio)->setTime(0, 0, 0);
                    $fin_dia = (clone $fecha_fin)->setTime(23, 59, 59);
                    if ($now < $inicio_dia || $now > $fin_dia) {
                        $estadoValido = false;
                        $motivo = 'jornada-fuera-de-fecha';
                    }
                }

                if (!$jornada || $jornada->estado === 'inactiva') {
                    $estadoValido = false;
                    $motivo = 'jornada-inactiva';
                }
            }

            if ($item['abono_id'] !== null) {
                $abono = $abonos[$item['abono_id']] ?? null;

                if (!$abono || $abono->estado === 0) {
                    $estadoValido = false;
                    $motivo = 'abono-inactivo';
                }

                if ($item['last_updated'] !== null) {
                    if (date_create($item['last_updated'])->setTimezone($localTZ)->format('Y-m-d') === date('Y-m-d')) {
                        $estadoValido = false;
                        $motivo = 'abono-hoy-usado';
                    }
                } else {
                    $item['last_updated'] = now();
                }
            }

            if ($item['cantidad_restante'] <= 0 && $motivo === null) {
                $estadoValido = false;
                $motivo = 'sin-cantidad';
            }

            if ($estadoValido && !$discounted) {
                $item['cantidad_restante'] -= 1;
                $item['last_updated'] = now();
                $abonoDiscounted = isset($item['abono_id']);
                $discounted = true;
            } else {
                $reasons[] = $motivo;
            }

            return $item;
        }, $qr_data_decoded['items']);

        if ($discounted) {
            $qr_data_decoded['items'] = $items_modified;
            $qr->qr_code_data = json_encode($qr_data_decoded);
            $qr->save();
        }

        $qr_data_decoded['items'] = $this->assignExtraData($items_modified, $jornadas, $abonos);

        $qr->qr_code_data = json_encode($qr_data_decoded);

        $reason = $discounted ? null : ($reasons[0] ?? 'desconocido');

        return [
            'qr' => $qr,
            'discounted' => $discounted,
            'reason' => $reason,
            'abonoDiscounted' => $abonoDiscounted
        ];
    }

    private function assignExtraData($items, $jornadas, $abonos)
    {
        return array_map(function ($item) use ($jornadas, $abonos) {
            if (isset($item['jornada_id'])) {
                $jornada = $jornadas[$item['jornada_id']] ?? null;
                if ($jornada) {
                    $format = 'd M, Y';
                    $inicio = date_create($jornada->fecha_inicio);
                    $fin = date_create($jornada->fecha_fin);
                    $item['fecha_str'] = $inicio->format($format) . ' - ' . $fin->format($format);
                }
            }

            if (isset($item['abono_id'])) {
                $item['last_updated'] = date_create($item['last_updated'])->format('d M, Y H:i');
            }
            return $item;
        }, $items);
    }
}
