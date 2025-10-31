<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaProductoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProducto::get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:categoria_productos,nombre',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        CategoriaProducto::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada correctamente.'
        ], 200);
    }

    public function show($id)
    {
        $categoria = CategoriaProducto::with('productos')->findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        try {

            $categoria = CategoriaProducto::findOrFail($id);

            $validated = Validator::make($request->all(), [
                'nombre' => "required|string|max:255|unique:categoria_productos,nombre,{$id}",
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validated->errors()->first(),
                ]);
            }

            $categoria->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $categoria = CategoriaProducto::find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.'
            ], 404);
        }

        $categoria->delete();

        return response()->json(['success' => true, 'message' => 'Categoría eliminada correctamente']);
    }
}
