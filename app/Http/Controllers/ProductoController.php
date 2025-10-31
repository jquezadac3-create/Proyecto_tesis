<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'abonoRelacion'])->get();

        return response()->json([
            'success' => true,
            'data' => $productos
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:0',
            'tipo_producto' => 'required|in:producto,servicio',
            'impuesto' => 'required|numeric|min:0',
            'precio_venta_sin_iva' => 'required|numeric|min:0',
            'precio_venta_final' => 'required|numeric|min:0',
            'costo' => 'nullable|numeric|min:0',
            'categoria_id' => 'required|exists:categoria_productos,id',
            'abono' => 'boolean',
            'id_abono' => 'nullable|exists:abonos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'error' => 'La validación de los campos falló',
                    'list' => $validator->errors()->all()
                ]
            ], 400);
        }

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'cantidad_actual' => $request->cantidad,
            'tipo_producto' => $request->tipo_producto,
            'impuesto' => $request->impuesto,
            'precio_venta_sin_iva' => $request->precio_venta_sin_iva,
            'precio_venta_final' => $request->precio_venta_final,
            'costo' => $request->costo,
            'categoria_id' => $request->categoria_id,
            'abono' => $request->abono,
            'id_abono' => $request->id_abono
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Se ha guardado correctamente.'
        ], 200);
    }

    public function show($id)
    {
        $producto = Producto::with(['categoria', 'abonoRelacion'])->findOrFail($id);
        return response()->json($producto);
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el objeto especificado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'cantidad' => 'sometimes|required|integer|min:0',
            'tipo_producto' => 'sometimes|required|in:producto,servicio',
            'impuesto' => 'sometimes|required|numeric|min:0',
            'precio_venta_sin_iva' => 'sometimes|required|numeric|min:0',
            'precio_venta_final' => 'sometimes|required|numeric|min:0',
            'costo' => 'nullable|numeric|min:0',
            'categoria_id' => 'sometimes|required|exists:categoria_productos,id',
            'abono' => 'sometimes|boolean',
            'id_abono' => 'nullable|exists:abonos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'error' => 'La validación de los campos falló.',
                    'list' => $validator->errors()->all()
                ]
            ], 400);
        }

        // Verificar cambio de categoría
        $categoriaNueva = $request->input('categoria_id', $producto->categoria_id);

        // Validar si la categoria es Ticket y ademas       
        if ($producto->categoria_id != $categoriaNueva) {
            $categoriaTicketId = CategoriaProducto::where('nombre', 'Ticket')->value('id');

            if ($producto->categoria_id == $categoriaTicketId) {
                $tieneJornadas = DB::table('productos_jornada')
                    ->where('id_producto', $producto->id)
                    ->exists();

                if ($tieneJornadas) {
                    return response()->json([
                        'success' => false,
                        'message' => [
                            'error' => 'Este ticket ya tiene al menos una jornada asignada. No se puede cambiar la categoría',
                        ]

                    ], 400);
                }
            }
        }

        $updated = $producto->update($request->all());

        return response()->json([
            'success' => true,
            'message' => $updated
                ? 'Se ha actualizado el registro.'
                : 'Algo salió mal al efectuar la actualización.'
        ], 200);
    }

    // Aumentar o disminuir el stock de abonos
    public function agregarStock(Request $request, $id)
    {
        // Buscar el producto
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el producto especificado.'
            ], 404);
        }

        // Validar datos
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|not_in:0',
            'motivo'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'error' => 'La validación de los campos falló.',
                    'list'  => $validator->errors()->all()
                ]
            ], 400);
        }

        $cantidad      = $request->cantidad;
        $stockAnterior = $producto->cantidad;
        $stockAnteriorActual = $producto->cantidad_actual;
        $stockActualNuevo = $stockAnteriorActual + $cantidad;
        $stockNuevo    = $stockAnterior + $cantidad;

        // Validar que no quede en negativo
        if ($stockNuevo < 0) {
            return response()->json([
                'success' => false,
                'message' => 'El stock principal no puede quedar negativo.'
            ], 422);
        }

        // VAlidar que no quede en negativo el stock de ventas
        if ($stockActualNuevo < 0) {
            return response()->json([
                'success' => false,
                'message' => 'El stock actual no puede quedar negativo.'
            ], 422);
        }

        // Actualizar stock en producto
        $producto->update([
            'cantidad' => $stockNuevo,
            'cantidad_actual' => $stockActualNuevo
        ]);

        // Registrar movimiento
        MovimientoStock::create([
            'producto_id'     => $producto->id,
            'user_id'         => 1, // luego se reemplaza por auth()->id()
            'tipo_movimiento' => $cantidad > 0 ? 'ingreso' : 'egreso',
            'stock_anterior'  => $stockAnteriorActual,
            'stock_agregado'  => $cantidad,
            'stock_nuevo'     => $stockActualNuevo,
            'motivo'          => $request->motivo,
            'fecha'           => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado y movimiento registrado correctamente.',
            'cantidad_actual' => $stockNuevo
        ], 200);
    }

    // Aumentar o disminuir el stock de Tickets
    public function agregarStockTickets(Request $request)
    {
        // Validar datos recibidos
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|integer|exists:productos,id',
            'jornada_id'  => 'required|integer|exists:jornadas,id',
            'cantidad'    => 'required|integer|not_in:0',
            'motivo'      => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'error' => 'La validación de los campos falló.',
                    'list'  => $validator->errors()->all()
                ]
            ], 400);
        }

        $producto_id = $request->producto_id;
        $jornada_id  = $request->jornada_id;
        $cantidad    = $request->cantidad;
        $motivo      = $request->motivo;

        // Buscar registro en productos_jornada
        $registro = DB::table('productos_jornada')
            ->where('id_producto', $producto_id)
            ->where('id_jornada', $jornada_id)
            ->first();

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la jornada vinculada a este producto.'
            ], 404);
        }

        $stockAnterior = $registro->stock;
        $stockActualAntedior = $registro->stock_actual;
        $stockNuevo    = $stockAnterior + $cantidad;
        $stockActualNuevo = $registro->stock_actual + $cantidad;

        // Obtener aforo de la jornada
        $jornada = DB::table('jornadas')->where('id', $jornada_id)->first();
        if (!$jornada) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la jornada especificada.'
            ], 404);
        }

        // Validar que no exceda el aforo ni quede negativo
        if ($stockNuevo < 0) {
            return response()->json([
                'success' => false,
                'message' => 'El stock principal no puede quedar negativo.'
            ], 422);
        }

        if ($stockActualNuevo < 0) {
            return response()->json([
                'success' => false,
                'message' => 'El stock actual no puede quedar negativo.'
            ], 422);
        }

        if ($stockNuevo > $jornada->cantidad_aforo) {
            return response()->json([
                'success' => false,
                'message' => 'El stock no puede superar el aforo de la jornada.'
            ], 422);
        }

        // Actualizar stock en productos_jornada
        DB::table('productos_jornada')
            ->where('id_producto', $producto_id)
            ->where('id_jornada', $jornada_id)
            ->update([
                'stock'        => $stockNuevo,
                'stock_actual' => $stockActualNuevo,
            ]);

        // Registrar movimiento (tabla MovimientosTickets)
        DB::table('movimientos_stock')->insert([
            'producto_id'     => $producto_id,
            'jornada_id'      => $jornada_id,
            'user_id'         => 1, // cambiar luego cuando haya usuarios
            'tipo_movimiento' => $cantidad > 0 ? 'ingreso' : 'egreso',
            'stock_anterior'  => $stockActualAntedior,
            'stock_agregado'  => $cantidad,
            'stock_nuevo'     => $stockActualNuevo,
            'motivo'          => $motivo,
            'fecha'           => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock de tickets actualizado correctamente.',
            'stock_actual' => $stockNuevo
        ], 200);
    }


    // Obtener las jornadas vinculadas a un producto(Ticket), y el stock disponible de la jornada
    public function obtenerJornadasDisponibles($idTicketStock)
    {
        // Buscar el producto
        $producto = Producto::find($idTicketStock);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el producto especificado.'
            ], 404);
        }

        // Obtener jornadas activas vinculadas al producto
        $jornadas = DB::table('productos_jornada')
            ->join('jornadas', 'productos_jornada.id_jornada', '=', 'jornadas.id')
            ->where('productos_jornada.id_producto', $idTicketStock)
            ->where('jornadas.estado', 'activa')
            ->select(
                'jornadas.id',
                'jornadas.nombre',
                'jornadas.cantidad_aforo'
            )
            ->get();

        // Validar que existan jornadas
        if ($jornadas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay jornadas vinculadas a este producto.'
            ], 404);
        }

        // Calcular aforo disponible
        $resultado = $jornadas->map(function ($jornada) use ($idTicketStock) {
            // Stock asignado a ESTE producto en ESTA jornada
            $stockAsignado = DB::table('productos_jornada')
                ->where('id_producto', $idTicketStock)
                ->where('id_jornada', $jornada->id)
                ->sum('stock_actual');

            // Stock total asignado a la jornada (todos los productos) para calcular aforo disponible
            $totalStockJornada = DB::table('productos_jornada')
                ->where('id_jornada', $jornada->id)
                ->sum('stock');

            $aforoDisponible = $jornada->cantidad_aforo - $totalStockJornada;

            return [
                'id' => $jornada->id,
                'nombre' => $jornada->nombre,
                'cantidad_aforo' => $jornada->cantidad_aforo,
                'stock_asignado' => $stockAsignado,
                'aforo_disponible' => $aforoDisponible,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $resultado
        ], 200);
    }

    // Eliminar un producto
    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la jornada especificada'
            ], 404);
        }
        // Verificar si el producto está asociado en producto_jornada
        $existeRelacion = DB::table('productos_jornada')
            ->where('id_producto', $id)
            ->exists();

        if ($existeRelacion) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar ticket que este relacionado a una jornada'
            ], 400);
        }

        $producto->delete();
        return response()->json(['success' => true, 'message' => 'Producto eliminado correctamente']);
    }

    // Verificar si al menos tiene una jornada el producto
    public function tieneJornadas($idProducto)
    {
        // Revisar si existe al menos una fila con este idProducto
        $existe = DB::table('productos_jornada')
            ->where('id_producto', $idProducto)
            ->exists(); // devuelve true si hay al menos una fila, false si no

        return response()->json([
            'success' => true,
            'tieneJornadas' => $existe
        ], 200);
    }

    // Buscar productos por nombre
    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
            'customerId' => 'nullable',
            'customerIdentification' => 'nullable'
        ]);

        $query = $request->input('query');
        $customerId = $request->input('customerId');
        $customerIdentification = $request->input('customerIdentification');

        $cliente = Cliente::where('id', $customerId)->first();

        // Buscar productos que coincidan por nombre
        $productos = Producto::with(['categoria', 'abonoRelacion'])
            ->where('nombre', 'LIKE', "%{$query}%")
            ->get();

        $resultado = [];

        foreach ($productos as $producto) {
            if ($producto->abono == 1) {
                // ---- ABONO ----
                if ($producto->cantidad_actual > 0) {
                    $resultado[] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'tipo' => 'abono',
                        'cantidad_actual' => $producto->cantidad_actual,
                        'precio_venta' => $producto->precio_venta_final,
                        'precio_sin_iva' => $producto->precio_venta_sin_iva,
                        'impuesto' => $producto->impuesto,
                        'costo' => $producto->costo,
                        'selectable' => $cliente->numero_identificacion === '9999999999999' || $customerIdentification === '9999999999999' ? false : true,
                        'abono' => $producto->abonoRelacion,
                        'categoria' => $producto->categoria,
                    ];
                }
            } else {
                // ---- TICKET ----
                // Buscar jornadas activas con stock > 0
                $jornadasActivas = DB::table('productos_jornada as pj')
                    ->join('jornadas as j', 'pj.id_jornada', '=', 'j.id')
                    ->where('pj.id_producto', $producto->id)
                    ->where('pj.stock_actual', '>', 0)
                    ->where('j.estado', 'activa')
                    ->select(
                        'j.id',
                        'j.nombre',
                        'j.fecha_inicio',
                        'j.fecha_fin',
                        'j.cantidad_aforo',
                        'j.estado',
                        'pj.stock_actual'
                    )
                    ->get();

                if ($jornadasActivas->isNotEmpty()) {
                    $resultado[] = [
                        'id' => $producto->id,
                        'nombre' => $producto->nombre,
                        'tipo' => 'ticket',
                        'precio_venta' => $producto->precio_venta_final,
                        'precio_sin_iva' => $producto->precio_venta_sin_iva,
                        'costo' => $producto->costo,
                        'selectable' => true,
                        'impuesto' => $producto->impuesto,
                        'jornadas' => $jornadasActivas,
                        'categoria' => $producto->categoria,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'productos' => $resultado
        ], 200);
    }
}
