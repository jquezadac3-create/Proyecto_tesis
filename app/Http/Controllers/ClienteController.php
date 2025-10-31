<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Qr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Insoutt\EcValidator\EcValidator;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::all();

        $estadisticas = $this->getStats();

        $clientes = $clientes->map(function ($cliente) use ($estadisticas) {
            $stats = $estadisticas[$cliente->id] ?? [
                'cantidad_total' => 0,
                'cantidad_restante' => 0,
                'cantidad_usada' => 0,
                'entradas_normales' => 0,
                'tiene_abono' => false,
            ];

            return array_merge($cliente->toArray(), ['estadisticas' => $stats]);
        });

        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'tipo_identificacion' => 'required|in:cedula,ruc,pasaporte,sas',
            'numero_identificacion' => 'required|string|max:20|unique:cliente,numero_identificacion',
            'email' => 'required|email:rfc,dns',
            'telefono' => 'nullable|string|phone|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error en la validación.',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $data = $request->all();

        $validator = new EcValidator();

        $info = [
            'tipo' => $data['tipo_identificacion'],
            'numero' => $data['numero_identificacion'],
            'valid' => $data['tipo_identificacion'] === 'ruc' || $data['tipo_identificacion'] === 'cedula' ? false : true,
        ];

        if ($info['tipo'] === 'cedula') {
            $info['valid'] = $validator->validateCedula($info['numero']);
        }

        if ($info['tipo'] === 'ruc') {
            $info['valid'] = $validator->validateRuc($info['numero']);
        }

        if (!$info['valid']) {
            return response()->json([
                'success' => false,
                'message' => "Identificación no válida: {$validator->getError()}"
            ], 400);
        }

        // Crear el cliente y capturar el modelo creado
        $cliente = Cliente::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado correctamente.',
            'data' => [
                'id' => $cliente->id, // devolver el id del cliente
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return response()->json($cliente);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'tipo_identificacion' => 'sometimes|required|in:cedula,ruc,pasaporte',
            'numero_identificacion' => "sometimes|required|string|max:20|unique:cliente,numero_identificacion,{$id}",
            'email' => "sometimes|required|email:rfc,dns",
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error en la validación de campos',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'El cliente no existe'
            ], 404);
        }

        $cliente->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado con éxito'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el cliente especificado'
            ], 404);
        }

        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado correctamente'
        ]);
    }

    // Obtener consumidor final
    public function consumidorFinal()
    {
        $cliente = Cliente::firstOrCreate(
            ['numero_identificacion' => '9999999999999'],
            [
                'nombres' => 'Consumidor',
                'apellidos' => 'Final',
                'tipo_identificacion' => 'ruc',
                'email' => null,
                'telefono' => null,
                'direccion' => null
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $cliente
        ]);
    }

    // Obtener las coincidencias de los clientes
    public function buscarClientes(Request $request)
    {
        // Validar que el parámetro 'query' sea una cadena y obligatorio
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:13',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error en la validación de campos',
                    'errors' => $validator->errors()->all()
                ]
            ], 422);
        }

        $query = $request->input('query');

        // Buscar coincidencias en numero_identificacion excluyendo Consumidor Final
        $clientes = Cliente::where('numero_identificacion', 'LIKE', "{$query}%")
            ->where('numero_identificacion', '!=', '9999999999999')
            ->orderBy('nombres')
            ->limit(10)
            ->get(['id', 'nombres', 'apellidos', 'numero_identificacion', 'email', 'telefono', 'direccion']);

        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }

    private function getStats()
    {
        // $qrs = Qr::with('factura')->whereHas('factura', function ($query) use ($clientIds) {
        //     $query->whereIn('cliente_id', $clientIds);
        // })
        //     ->get()
        //     ->groupBy(fn($item) => json_decode($item->qr_code_data, true)['cliente']);

        // Log::info('QR Codes Stats', $qrs->toArray());

        // $qrs->map(function($qrs) {
        //     $abonos = 0;
        //     $total = 0;

        //     $qrs->each(function($qr) use (&$abonos, &$total) {

        //     });

        //     return [
        //         'count' => $qrs->count(),
        //         // 'abonos' => $qrs->sum(fn($qr) => $qr->factura->abonos()->sum('monto')),
        //         'abonos' => $abonos,
        //         'total' => $total,
        //     ];
        // });

        $estadisticas = [];

        Qr::with('factura')->whereHas('factura', function($query) {
            $query->where('status', '=', 'valida');
        })->cursor()->each(function ($qr) use (&$estadisticas) {
            $data = json_decode($qr->qr_code_data, true);

            $clienteId = $data['cliente'];

            $items = $data['items'] ?? [];

            if (!isset($estadisticas[$clienteId])) {
                $estadisticas[$clienteId] = [
                    'total_abono' => 0,
                    'cantidad_total' => 0,
                    'cantidad_restante' => 0,
                    'cantidad_usada' => 0,
                    'entradas_normales' => 0,
                    'tiene_abono' => false,
                ];
            }

            foreach ($items as $item) {
                $inicial = (int) $item['cantidad_inicial'];
                $restante = (int) $item['cantidad_restante'];
                $usada = $inicial - $restante;

                $estadisticas[$clienteId]['cantidad_total'] += $inicial;
                
                if (!$item['abono_id']) {
                    $estadisticas[$clienteId]['entradas_normales'] += $inicial;
                }
                
                if ($item['abono_id']) {
                    $estadisticas[$clienteId]['cantidad_usada'] += $usada;
                    $estadisticas[$clienteId]['cantidad_restante'] += $restante;
                    $estadisticas[$clienteId]['total_abono'] += $inicial;
                    $estadisticas[$clienteId]['tiene_abono'] = true;
                }
            }
        });

        return $estadisticas;
    }
}
