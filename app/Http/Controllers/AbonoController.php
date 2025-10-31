<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AbonoController extends Controller
{
    public function index()
    {
        $abonos = Abono::with('productos')->get();

        return response()->json([
            'success' => true,
            'data' => $abonos
        ]);
    }

    public function getActiveAbonos(){
        // Obtener todos los abonos con estado = 1
        $abonos = Abono::where('estado', 1)->whereDoesntHave('productos')->get();

        return response()->json([
            'success' => true,
            'data' => $abonos
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:abonos,nombre',
            'descripcion' => 'required|string|max:500',
            'numero_entradas' => 'nullable|integer|min:0',
            'costo_total' => 'required|numeric|min:0',
            'estado' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Ocurrió un error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $abono = Abono::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Abono creado correctamente'
        ]);
    }

    public function show($id)
    {
        $abono = Abono::with('productos')->findOrFail($id);
        return response()->json($abono);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255|unique:abonos,nombre,' . $id,
            'descripcion' => 'sometimes|required|string|max:500',
            'numero_entradas' => 'sometimes|required|integer|min:0',
            'costo_total' => 'sometimes|required|numeric|min:0',
            'estado' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Ocurrió un error de validación',
                    'errors' => $validator->errors()->all()
                ]
            ]);
        }

        $abono = Abono::find($id);

        if (!$abono) {
            return response()->json([
                'success' => false,
                'message' => 'Abono no encontrado'
            ], 404);
        }

        $abono->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Abono actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $abono = Abono::findOrFail($id);
        $abono->delete();
        return response()->json([
            'success' => true,
            'message' => 'Abono eliminado correctamente'
        ]);
    }
}
