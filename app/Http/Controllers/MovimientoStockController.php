<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovimientoStockController extends Controller
{
    // Obtener los datos de la tabla movimientos por rango de fecha 
   public function filtrarPorFechas(Request $request){
        // Validar fechas
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date_format:d/m/Y',
            'fecha_fin'    => 'required|date_format:d/m/Y|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Convertir fechas a formato Y-m-d H:i:s
        $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_inicio)->startOfDay()->format('Y-m-d H:i:s');
        $fechaFin    = \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_fin)->endOfDay()->format('Y-m-d H:i:s');

        // SQL puro
        $movimientos = DB::select("
            SELECT 
                ms.id,
                p.nombre AS producto,
                u.name AS usuario,
                ms.stock_anterior,
                ms.stock_agregado,
                ms.stock_nuevo,
                ms.tipo_movimiento AS tipo,
                ms.fecha,
                ms.motivo,
                -- Nombre de jornada o abono
                CASE 
                    WHEN ms.jornada_id IS NOT NULL THEN j.nombre
                    ELSE a.nombre
                END AS relacion,
                -- Tipo: T si es jornada/ticket, A si es abono
                CASE 
                    WHEN ms.jornada_id IS NOT NULL THEN 'J'
                    ELSE 'A'
                END AS tipo_relacion
            FROM movimientos_stock ms
            LEFT JOIN productos p ON ms.producto_id = p.id
            LEFT JOIN users u ON ms.user_id = u.id
            LEFT JOIN jornadas j ON ms.jornada_id = j.id
            LEFT JOIN abonos a ON p.id_abono = a.id
            WHERE ms.fecha BETWEEN ? AND ?
            ORDER BY ms.fecha ASC
        ", [$fechaInicio, $fechaFin]);

        return response()->json([
            'success' => true,
            'data' => $movimientos
        ]);
    }
}
