<?php

namespace App\Http\Controllers;

use App\Models\Jornada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JornadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $jornadas = Jornada::all();

        return response()->json([
            'success' => true,
            'data' => $jornadas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'cantidad_aforo' => 'required|integer|min:1',
            'estado' => 'required|in:activa,inactiva',
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

        Jornada::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Se ha guardado correctamente.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jornada $jornada){
        $jornada = Jornada::findOrFail($id);
        return response()->json($jornada);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
        $jornada = Jornada::find($id);

        if (!$jornada) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el objeto especificado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'cantidad_aforo' => 'sometimes|required|integer|min:1',
            'estado' => 'sometimes|required|in:activa,inactiva',
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

        $updated = $jornada->update($request->all());

        return response()->json([
            'success' => true,
            'message' => $updated ? 'Se ha actualizado el registro.' : 'Algo salió mal al efectuar la actualización.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $jornada = Jornada::find($id);

        if (!$jornada) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la jornada especificada'
            ], 404);
        }

        $existeRelacion = DB::table('productos_jornada')
        ->where('id_jornada', $id)
        ->exists();

        if ($existeRelacion) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar esta jornada porque está asociada a un Boleto'
            ], 400);
        }

        $jornada->delete();
        return response()->json(['success' => true, 'message' => 'Jornada eliminada correctamente']);
    }

    // Traer las jornadas con el aforo correspondiente
    public function getJornadasDisponibles(Request $request){
        $idProducto = $request->input('idProducto');

        if (!$idProducto) {
            return response()->json([
                'success' => false,
                'message' => 'Debe enviar un idProducto'
            ], 400);
        }

        // Subquery: total stock asignado a cada jornada (sumando todos los productos)
        $subquery = DB::table('productos_jornada')
            ->select('id_jornada', DB::raw('SUM(stock) as total_stock'))
            ->groupBy('id_jornada');

        $jornadas = DB::table('jornadas')
            ->leftJoinSub($subquery, 'pj', function ($join) {
                $join->on('jornadas.id', '=', 'pj.id_jornada');
            })
            ->where('jornadas.estado', 'activa')
            ->whereNotIn('jornadas.id', function ($query) use ($idProducto) {
                $query->select('id_jornada')
                    ->from('productos_jornada')
                    ->where('id_producto', $idProducto);
            })
            ->select(
                'jornadas.id',
                'jornadas.nombre',
                'jornadas.fecha_inicio',
                'jornadas.fecha_fin',
                'jornadas.cantidad_aforo',
                DB::raw('jornadas.cantidad_aforo - IFNULL(pj.total_stock, 0) as aforo_restante')
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jornadas
        ]);
    }
}
