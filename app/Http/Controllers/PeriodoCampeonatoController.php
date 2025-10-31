<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodoCampeonato;
use App\Models\FacturaSorteo;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class PeriodoCampeonatoController extends Controller
{
    // Listar los periodos
    public function index(){
        $periodos = PeriodoCampeonato::all();
        return response()->json([
            'success' => true,
            'data' => $periodos
        ]);
    }

    // Crear un nuevo periodo
    // public function store(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'nombre' => 'required|string|max:255',
    //         'fecha_inicio' => 'required|date',
    //         'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => [
    //                 'error' => 'La validación de los campos falló',
    //                 'list' => $validator->errors()->all()
    //             ]
    //         ], 400);
    //     }

    //     // Crear el periodo
    //     PeriodoCampeonato::create($request->all());

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Se ha guardado correctamente.'
    //     ], 200);
    // }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
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

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        // Validar que no se superpongan con otros periodos
        $overlap = PeriodoCampeonato::where(function ($query) use ($fechaInicio, $fechaFin) {
            $query->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                    $q->where('fecha_inicio', '<=', $fechaInicio)
                        ->where('fecha_fin', '>=', $fechaFin);
                });
        })->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => [
                    'error' => 'El periodo seleccionado se superpone con otro periodo existente.'
                ]
            ], 400);
        }

        // Calcular el status según la fecha actual
        $hoy = now()->format('Y-m-d');
        if ($hoy < $fechaInicio) {
            $status = 'inactivo';
        } elseif ($hoy > $fechaFin) {
            $status = 'finalizado';
        } else {
            $status = 'activo';
        }

        // Crear el periodo
        PeriodoCampeonato::create([
            'nombre' => $request->nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Se ha guardado correctamente.'
        ], 200);
    }

    // Actualizar un periodo
    public function update(Request $request, $id){
        $periodo = PeriodoCampeonato::find($id);

        if (!$periodo) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el periodo especificado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
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

         // Actualizar los campos
        $periodo->fill($request->only(['nombre', 'fecha_inicio', 'fecha_fin']));

        // Determinar status automáticamente según la fecha actual
        $hoy = now();
        $fechaInicio = $periodo->fecha_inicio;
        $fechaFin = $periodo->fecha_fin;

        if ($hoy->between($fechaInicio, $fechaFin)) {
            $periodo->status = 'activo';
        } elseif ($hoy->gt($fechaFin)) {
            $periodo->status = 'finalizado';
        } else { // $hoy < $fechaInicio
            $periodo->status = 'inactivo';
        }

        $updated = $periodo->save();

        return response()->json([
            'success' => true,
            'message' => $updated ? 'Se ha actualizado el periodo correctamente.' : 'Algo salió mal al efectuar la actualización.'
        ], 200);
    }
    
    // Eliminar un periodo
    public function destroy($id){

        $existeFactura = FacturaSorteo::where('periodo_id', $id)->exists();

        if ($existeFactura) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar este periodo porque tiene facturas asociadas.'
            ], 400);
        }

        $periodo = PeriodoCampeonato::find($id);

        if (!$periodo) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el periodo especificado'
            ], 404);
        }

        $periodo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Periodo eliminado correctamente'
        ]);
    }


}
