<?php

namespace App\Http\Controllers\Routes;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardReports;
use App\Models\Config;
use App\Models\FacturaCabecera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardRoutesController extends Controller
{
    public function dashboard()
    {
        $dashboardData = new DashboardReports();
        $data = $dashboardData->getDashboardData();

        return view('components.dashboard', compact('data'));
    }

    public function config()
    {
        $config = Config::first();

        if (!$config) {
            return redirect()->route('configuracion.index');
        }

        $imgRoute = $config->logo_path ? Storage::temporaryUrl(
            $config->logo_path,
            now()->addMinutes(5)
        ) : null;

        $config->logo = $imgRoute;

        return view('components.configuracion.vconfig', compact('config'));
    }

    public function categories()
    {
        return view('components.productos.producto-categorias.productos-categorias');
    }

    public function abonos()
    {
        return view('components.productos.producto-abonos.productos-abonos');
    }

    public function days()
    {
        return view('components.productos.producto-jornadas.producto-jornadas');
    }

    public function productsList()
    {
        return view('components.productos.producto-list.productos-lista');
    }

    public function salesClient()
    {
        return view('components.ventas.clientes.ventas-clientes');
    }

    public function salesBill()
    {
        return view('components.ventas.facturas.ventas-factura');
    }

    public function salesListBill()
    {
        $pendingInvoices = FacturaCabecera::whereDoesntHave('facturaEstadoSri')
            ->orWhereHas('facturaEstadoSri', function ($query) {
                $query->where('estado_autorizacion', 'PENDIENTE');
            })
            ->count();

        return view('components.ventas.listado-facturas.listado-facturas', ['pendingInvoices' => $pendingInvoices]);
    }

    public function reportCardex()
    {
        return view('components.reportes.cardex.reporte-cardex');
    }
    public function reportMovimientoCaja()
    {
        return view('components.reportes.movimientoCaja.reporte-movimientoCaja');
    }

    public function reportVentasProductos()
    {
        return view('components.reportes.ventasProducto.reporte-ventasProducto');
    }

    public function reportVentas()
    {
        return view('components.reportes.ventasReport.reporte-ventas');
    }

    public function sorteoCargarDatosFactura()
    {
        return view('components.sorteos.cargarDatosFactura.cargarDatosFactura');
    }

    public function datosBoletos()
    {
        return view('components.sorteos.boletos.boletos');
    }

    public function periodoCampeonato()
    {
        return view('components.sorteos.periodoCampeonato.perdiodoCampeonato');
    }

    public function sorteosRealizados()
    {
        return view('components.sorteos.sorteosRealizados.sorteosRealizados');
    }

    public function usuarios()
    {
        return view('components.usuarios.usuarios');
    }

    public function qr()
    {
        return view('components.qr.scanner-view');
    }
}
