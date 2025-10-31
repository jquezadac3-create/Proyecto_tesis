<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacturaSorteo;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\HeadingRowImport;
use Carbon\Carbon;
use App\Models\PeriodoCampeonato;
use App\Models\Jornada;
use App\Models\Cliente;
use App\Models\FacturaDetalle;
use App\Models\Abono;
use App\Models\FacturaCabecera;
use App\Models\Sorteo;
use App\Models\SorteoJornada;
use App\Models\BoletoSorteo;
use Illuminate\Support\Facades\DB;

class FacturaSorteoController extends Controller
{
    public function importarExcel(Request $request){
        // Validación básica del archivo
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error en la validación.',
                    'errors' => $validator->errors()->all()
                ]
            ], 422);
        }

        // Leer datos del Excel
        $data = Excel::toArray([], $request->file('archivo'));
        $rows = $data[0];

        $skipHeader = true;
        $numeroFacturas = []; 
        $facturasAInsertar = [];
        $erroresExcel = [];
        $linea = 1; 

        foreach ($rows as $row) {
            if ($skipHeader) {
                $skipHeader = false;
                continue;
            }

            $linea++;

            if (count($row) < 3) {
                $erroresExcel[] = "Línea {$linea}: Fila incompleta.";
                continue;
            }

            [$numero_factura, $nombre, $cantidad] = $row;

            if (!$numero_factura) {
                $erroresExcel[] = "Línea {$linea}: Falta número de factura.";
            }

            if (!$nombre) {
                $erroresExcel[] = "Línea {$linea}: Falta nombre del cliente.";
            }

            if ($cantidad === null || $cantidad === '' || (int)$cantidad <= 0) {
                $erroresExcel[] = "Línea {$linea}: Cantidad inválida (debe ser mayor a 0).";
            }

            if (isset($numero_factura) && $nombre && (int)$cantidad > 0) {
                if (isset($numeroFacturas[$numero_factura])) {
                    $erroresExcel[] = "Línea {$linea}: Número de factura duplicado ({$numero_factura})";
                }

                $numeroFacturas[$numero_factura] = $linea;

                $facturasAInsertar[] = [
                    'numero_factura' => trim($numero_factura),
                    'nombre' => $nombre,
                    'cantidad' => (int) $cantidad,
                ];
            }
        }

        if (!empty($erroresExcel)) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Errores en los datos del Excel.',
                    'errors' => $erroresExcel
                ]
            ], 422);
        }

        // 🔹 Buscar periodo vigente con la fecha de hoy
        $hoy = Carbon::today()->toDateString();
        $periodo = PeriodoCampeonato::where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy)
                    ->where('status', 'activo') // opcional: si quieres que esté activo
                    ->first();

        if (!$periodo) {
            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error: No existe un periodo válido.',
                    'errors' => ["No existe un periodo para la fecha actual ($hoy). Cree un periodo para poder importar los datos."]
                ]
            ], 422);
        }

        // Verificar duplicados en la base de datos
        $duplicadosEnBD = FacturaSorteo::whereIn('numero_factura', array_keys($numeroFacturas))
            ->pluck('numero_factura')
            ->toArray();

        if (!empty($duplicadosEnBD)) {
            $erroresBD = [];
            foreach ($duplicadosEnBD as $num) {
                $linea = $numeroFacturas[$num] ?? '?';
                $erroresBD[] = "Línea {$linea}: Número de factura registrado en BD ({$num})";
            }

            return response()->json([
                'success' => false,
                'message' => [
                    'title' => 'Error: Facturas ya existen en la base de datos.',
                    'errors' => $erroresBD
                ]
            ], 422);
        }

        // Insertar facturas con el periodo_id encontrado
        foreach ($facturasAInsertar as &$factura) {
            $factura['periodo_id'] = $periodo->id;
            FacturaSorteo::create($factura);
        }

        return response()->json([
            'success' => true,
            'message' => 'Importación completada correctamente.'
        ]);
    }

    public function obtenerFacturas(){
        try {
            $facturas = FacturaSorteo::with('periodo')->get();

            return response()->json([
                'success' => true,
                'data' => $facturas,
                'message' => 'Facturas obtenidas correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al recuperar las facturas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerPeriodosCampeonato(){
        $hoy = now()->toDateString(); // fecha actual en formato 'YYYY-MM-DD'

        $periodos = PeriodoCampeonato::where('fecha_fin', '>=', $hoy) // solo periodos que no han finalizado
                        ->orderBy('fecha_inicio', 'asc')
                        ->get();

        return response()->json($periodos);
    }

    public function getJornadasPeriodo(Request  $request){
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFinPeriodo = Carbon::parse($request->input('fecha_fin'))->endOfDay();
        $hoy = Carbon::now();

        // Si la fecha de fin del periodo es mayor a hoy, usamos hoy
        $fechaFin = $fechaFinPeriodo->greaterThan($hoy) ? $hoy : $fechaFinPeriodo;

        $jornadas = Jornada::whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
            ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
            ->get();

        return response()->json([
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_fin' => $fechaFin->toDateString(),
            'jornadas' => $jornadas,
        ]);
    }

    // Actualizar los datos de la tabla facturas_sorteo y generar los boletos de las facturas actuales
    public function actualizarFacturasSorteo() {
        try {
            DB::transaction(function () {
                // Traemos los registros existentes en facturas_sorteo
                $facturasSorteo = DB::table('facturas_sorteo')->get();

                // Traemos facturas con detalles y clientes
                $facturas = FacturaCabecera::with(['detalles', 'cliente'])->get();
                $periodos = PeriodoCampeonato::all()->keyBy('id');
                $jornadas = Jornada::all()->keyBy('id');
                $abonos = Abono::all()->keyBy('id');

                foreach ($facturasSorteo as $fs) {
                    // Buscar la factura correspondiente
                    $factura = $facturas->firstWhere('secuencia_factura', $fs->numero_factura);
                    if (!$factura) continue;

                    $nombreCliente = $factura->cliente ? trim($factura->cliente->nombres . ' ' . $factura->cliente->apellidos) : 'Consumidor Final';
                    $periodo = $periodos[$factura->periodo_id] ?? null;
                    $nombrePeriodo = $periodo ? $periodo->nombre : null;

                    // Buscamos un detalle que coincida con la fila
                    foreach ($factura->detalles as $detalle) {

                        // Si la fila ya tiene producto/jornada o abono actualizado, saltamos
                        // Esto previene duplicar la actualización
                        if ($fs->producto_id || $fs->abono_id) continue;

                        $producto_id = $detalle->producto_id;
                        $nombre_producto = $detalle->nombre_producto;
                        $jornada_id = $detalle->jornada_id;
                        $nombre_jornada = $jornada_id ? ($jornadas[$jornada_id]->nombre ?? null) : null;
                        $abono_id = $detalle->abono_id;
                        $nombre_abono = $abono_id ? ($abonos[$abono_id]->nombre ?? null) : null;

                        $cantidad_boletos = $detalle->cantidad;

                        // Si es abono, multiplicar cantidad por número de entradas y quitar jornada
                        if ($abono_id) {
                            $cantidad_boletos = ($abonos[$abono_id]->numero_entradas ?? 0) * $detalle->cantidad;
                            $jornada_id = null;
                            $nombre_jornada = null;
                        }

                        // Actualizamos la fila existente
                        DB::table('facturas_sorteo')
                            ->where('id', $fs->id)
                            ->update([
                                'nombre'          => $nombreCliente,
                                'cantidad'        => $cantidad_boletos,
                                'periodo_id'      => $factura->periodo_id,
                                'nombre_periodo'  => $nombrePeriodo,
                                'producto_id'     => $producto_id,
                                'nombre_producto' => $nombre_producto,
                                'jornada_id'      => $jornada_id,
                                'nombre_jornada'  => $nombre_jornada,
                                'abono_id'        => $abono_id,
                                'nombre_abono'    => $nombre_abono,
                            ]);

                        // Una vez actualizada la fila, pasamos a la siguiente fila
                        break;
                    }
                }
            });

            return response()->json(['ok' => true, 'msg' => 'Facturas_sorteo actualizadas correctamente']);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al actualizar facturas_sorteo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Generar boletos desde facturas_sorteo ya actualizadas
    public function generarBoletosSorteo() {
        try {
            DB::transaction(function () {
                $facturasSorteo = DB::table('facturas_sorteo')->get();

                foreach ($facturasSorteo as $fs) {
                    // Buscar el id real de la factura en factura_cabecera
                    $cabecera = DB::table('factura_cabecera')
                        ->where('secuencia_factura', $fs->numero_factura)
                        ->where('periodo_id', $fs->periodo_id)
                        ->first();

                    if (!$cabecera) {
                        continue; // Si no hay factura, saltamos
                    }

                    // Obtener el último número de boleto en este periodo
                    $lastNumero = DB::table('boletos_sorteo')
                        ->where('periodo_id', $fs->periodo_id)
                        ->max('numero_boleto') ?? 0;

                    // Generar TODOS los boletos de esta fila sin validaciones
                    for ($i = 1; $i <= $fs->cantidad; $i++) {
                        $lastNumero++;

                        DB::table('boletos_sorteo')->insert([
                            'factura_id'      => $cabecera->id,
                            'numero_factura'  => $fs->numero_factura,
                            'nombre_cliente'  => $fs->nombre,
                            'periodo_id'      => $fs->periodo_id,
                            'producto_id'     => $fs->producto_id,
                            'nombre_producto' => $fs->nombre_producto,
                            'jornada_id'      => $fs->jornada_id,
                            'nombre_jornada'  => $fs->nombre_jornada,
                            'abono_id'        => $fs->abono_id,
                            'nombre_abono'    => $fs->nombre_abono,
                            'numero_boleto'   => $lastNumero,
                            'es_ganador'      => 0,
                            'premio_ganado'   => null,
                            'ya_participo'    => 0,
                        ]);
                    }
                }
            });

            return response()->json(['ok' => true, 'msg' => 'Boletos generados correctamente']);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'msg' => 'Error al generar boletos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Crear el sorteo
    public function crearSorteo(Request $request){
        $request->validate([
            'nombre' => 'required|string',
            'numPremios' => 'required|integer|min:1',
            'posicionGanador' => 'required|integer|min:1',
            'periodo_id' => 'required|integer|exists:periodo_campeonato,id',
            'jornadas' => 'required|array|min:1',
            'jornadas.*' => 'integer|exists:jornadas,id',
        ]);

        try {
            DB::beginTransaction();

            // Crear el sorteo
            $sorteo = Sorteo::create([
                'periodo_id' => $request->periodo_id,
                'nombre' => $request->nombre,
                'num_premios' => $request->numPremios,
                'posicion_ganadora' => $request->posicionGanador,
                'created_at' => Carbon::now('America/Guayaquil'),
                'updated_at' => Carbon::now('America/Guayaquil'),
            ]);

          // Guardar jornadas seleccionadas en la tabla sorteos_jornadas
                foreach ($request->jornadas as $jornadaId) {
                    SorteoJornada::create([
                        'sorteo_id' => $sorteo->id,
                        'jornada_id' => $jornadaId,
                        'created_at' => Carbon::now('America/Guayaquil'),
                        'updated_at' => Carbon::now('America/Guayaquil'),
                    ]);
                }

            // Traer boletos del sorteo
            $boletos = BoletoSorteo::where('periodo_id', $request->periodo_id)
                ->where('ya_participo', 0)
                ->where(function ($q) use ($request) {
                    $q->whereIn('jornada_id', $request->jornadas)
                    ->orWhereNotNull('abono_id'); //Traer todos los abonoes
                })
                ->get();

            DB::commit();

            return response()->json([
                'ok' => true,
                'msg' => 'Sorteo creado correctamente',
                'sorteo' => $sorteo,
                'jornadas' => $request->jornadas,
                'boletos' => $boletos
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'ok' => false,
                'msg' => 'Error al crear el sorteo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar boletos obtenidos en el sorteo 
    public function actualizarBoletos(Request $request){
        $data = $request->all();

        // Validar que recibimos un arreglo de boletos
        if (!isset($data['boletos']) || !is_array($data['boletos'])) {
            return response()->json([
                'ok' => false,
                'msg' => 'Datos inválidos: se esperaba un array de boletos'
            ], 422);
        }

        $actualizados = [];

        foreach ($data['boletos'] as $boleto) {
            if (!isset($boleto['id'])) {
                continue; // si no hay id, no se puede actualizar
            }

            $model = BoletoSorteo::find($boleto['id']);
            if (!$model) {
                continue; // boleto no existe
            }

            // Solo actualizamos los campos que llegan (para no sobreescribir nada innecesario)
            $updateData = array_intersect_key($boleto, array_flip([
                'es_ganador',
                'premio_ganado',
                'ya_participo',
                'sorteo_id',
            ]));

            $model->update($updateData);
            $actualizados[] = $model;
        }

        return response()->json([
            'ok' => true,
            'msg' => 'Boletos actualizados correctamente',
            'actualizados' => $actualizados
        ]);
    }

    // Obtener los sorteos realizados
    public function getSorteos()
    {
        // Obtenemos los sorteos con el nombre del periodo campeonato
        $sorteos = DB::table('sorteos as s')
            ->join('periodo_campeonato as p', 's.periodo_id', '=', 'p.id')
            ->select('s.id', 's.nombre', 'p.nombre as periodo_nombre')
            ->orderBy('s.created_at', 'desc')
            ->get();

        return response()->json($sorteos);
    }

    // Obtener los datos del sorteo
    public function obtenerSorteo($id){
        // Datos del sorteo
        $sorteo = DB::table('sorteos as s')
            ->join('periodo_campeonato as p', 's.periodo_id', '=', 'p.id')
            ->select(
                's.id',
                's.nombre',
                's.num_premios',
                's.posicion_ganadora',
                's.created_at',
                'p.nombre as periodo_nombre'
            )
            ->where('s.id', $id)
            ->first();

        if (!$sorteo) {
            return response()->json([
                'success' => false,
                'message' => 'Sorteo no encontrado'
            ], 404);
        }

        // Boletos ganadores
        $boletosGanadores = DB::table('boletos_sorteo')
            ->where('sorteo_id', $id)
            ->where('es_ganador', 1)
            ->select('numero_factura', 'numero_boleto', 'nombre_cliente', 'premio_ganado')
            ->get();

        // Boletos perdedores
        $boletosPerdedores = DB::table('boletos_sorteo')
            ->where('sorteo_id', $id)
            ->where('ya_participo', 1)
            ->where('es_ganador', 0)
            ->select('numero_factura', 'numero_boleto', 'nombre_cliente', 'nombre_producto')
            ->get();

        return response()->json([
            'success' => true,
            'sorteo' => $sorteo,
            'boletos_ganadores' => $boletosGanadores,
            'boletos_perdedores' => $boletosPerdedores
        ]);
    }

}
