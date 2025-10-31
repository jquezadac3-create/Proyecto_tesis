<?php

namespace App\Http\Controllers;

use App\Models\ProductoJornada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoJornadaController extends Controller
{

    public function index(){
        $relaciones = ProductoJornada::with(['producto', 'jornada'])->get();
        return response()->json($relaciones);
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|exists:productos,id',
            'id_jornada' => 'required|exists:jornadas,id',
            'stock' => 'required|integer|min:0',
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

        $stock = $request->stock;

        $productoJornada = ProductoJornada::create([
            'id_producto'  => $request->id_producto,
            'id_jornada'   => $request->id_jornada,
            'stock'        => $stock,
            'stock_actual' => $stock,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto asignado a jornada correctamente',
            'data'    => $productoJornada
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id){
        $relacion = ProductoJornada::with(['producto', 'jornada'])->findOrFail($id);
        return response()->json($relacion);
    }


    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $id){
        $relacion = ProductoJornada::findOrFail($id);

        $validated = $request->validate([
            'id_producto' => 'sometimes|required|exists:productos,id',
            'id_jornada' => 'sometimes|required|exists:jornadas,id',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $relacion->update($validated);
        return response()->json($relacion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $relacion = ProductoJornada::findOrFail($id);
        $relacion->delete();
        return response()->json(['message' => 'Relación producto-jornada eliminada correctamente']);
    }
}
