<?php

namespace App\Http\Controllers;

use App\Models\FacturaCabecera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardReports extends Controller
{
    public function getDashboardData($AS_ARRAY = true)
    {
        $todaySales = $this->getDataTodaySales($AS_ARRAY);
        $todayPurchases = $this->getDataTodayPurchases($AS_ARRAY);
        $cashSales = $this->getDataTodayCashSales($AS_ARRAY);
        $otherSales = $this->getDataTodayOtherSales($AS_ARRAY);
        $weeklySales = $this->getDataWeeklySales($AS_ARRAY);
        $totalProductsSales = $this->getTotalProductsSales($AS_ARRAY);

        return $this->returnAsArrayOrJson([
            'todaySales' => $todaySales,
            'todayPurchases' => $todayPurchases,
            'cashSales' => $cashSales,
            'otherSales' => $otherSales,
            'weeklySales' => $weeklySales,
            'totalProductsSales' => $totalProductsSales
        ], $AS_ARRAY);
    }

    public function getDataTodaySales($AS_ARRAY = true)
    {
        $invoices = $this->getYesterdayAndTodayInvoices();
        $todayInvoices = $invoices['today'];
        $yesterdayInvoices = $invoices['yesterday'];

        $todayTotal = $todayInvoices->sum('total_factura');
        $yesterdayTotal = $yesterdayInvoices->sum('total_factura');

        $porcentualDifference = $this->getPorcentualDifference($todayTotal, $yesterdayTotal);

        return $this->returnAsArrayOrJson([
            'total' => $todayTotal,
            'diff' => $porcentualDifference
        ], $AS_ARRAY);
    }

    public function getDataTodayPurchases($AS_ARRAY = true)
    {
        $invoices = $this->getYesterdayAndTodayInvoices();
        $todayInvoices = $invoices['today'];
        $yesterdayInvoices = $invoices['yesterday'];

        $countToday = $todayInvoices->count();
        $countYesterday = $yesterdayInvoices->count();
        $porcentualDifference = $this->getPorcentualDifference($countToday, $countYesterday);

        return $this->returnAsArrayOrJson([
            'count' => $countToday,
            'diff' => $porcentualDifference
        ], $AS_ARRAY);
    }

    public function getDataTodayCashSales($AS_ARRAY = true)
    {
        $invoices = $this->getYesterdayAndTodayInvoices();
        $todayInvoices = $invoices['today']->load('formaPago')->filter(fn($invoice) => $invoice->formaPago && $invoice->formaPago->codigo === '01');
        $yesterdayInvoices = $invoices['yesterday']->load('formaPago')->filter(fn($invoice) => $invoice->formaPago && $invoice->formaPago->codigo === '01');

        $todayTotal = $todayInvoices->sum('total_factura');
        $yesterdayTotal = $yesterdayInvoices->sum('total_factura');

        $porcentualDifference = $this->getPorcentualDifference($todayTotal, $yesterdayTotal);

        return $this->returnAsArrayOrJson([
            'total' => $todayTotal,
            'diff' => $porcentualDifference
        ], $AS_ARRAY);
    }

    public function getDataTodayOtherSales($AS_ARRAY = true)
    {
        $invoices = $this->getYesterdayAndTodayInvoices();
        $todayInvoices = $invoices['today']->load('formaPago')->filter(fn($invoice) => $invoice->formaPago && $invoice->formaPago->codigo !== '01');
        $yesterdayInvoices = $invoices['yesterday']->load('formaPago')->filter(fn($invoice) => $invoice->formaPago && $invoice->formaPago->codigo !== '01');

        $todayTotal = $todayInvoices->sum('total_factura');
        $yesterdayTotal = $yesterdayInvoices->sum('total_factura');

        $porcentualDifference = $this->getPorcentualDifference($todayTotal, $yesterdayTotal);

        return $this->returnAsArrayOrJson([
            'total' => $todayTotal,
            'diff' => $porcentualDifference
        ], $AS_ARRAY);
    }

    public function getDataWeeklySales($AS_ARRAY = true)
    {
        $invoices = $this->getWeeklyInvoices()->select(DB::raw('DATE(fecha) as fecha'), 'total_factura', 'forma_pago', 'id')->get();
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $invoices = $invoices->groupBy('fecha')->map(function ($invoice) use ($dias) {
            $first = $invoice->first();
            $dayN = $dias[date_create($first->fecha)->format('w')];
            return [
                'fecha' => $dayN,
                'total_factura' => $invoice->sum('total_factura'),
                'forma_pago' => $first->forma_pago,
                'id' => $first->id
            ];
        })->toArray();

        return $this->returnAsArrayOrJson(['weekly' => $invoices], $AS_ARRAY);
    }

    public function getTotalProductsSales($AS_ARRAY = true)
    {
        $invoices = $this->getYesterdayAndTodayInvoices();
        $todayInvoices = $invoices['today'];

        $todayInvoices->load('detalles.producto');

        $productTotals = $todayInvoices
            ->flatMap(function ($invoice) {
                return $invoice->detalles;
            })
            ->groupBy(function ($detalle) {
                return $detalle->producto->nombre ?? 'Producto desconocido';
            })
            ->map(function ($group) {
                return $group->sum('cantidad');
            });


        return $this->returnAsArrayOrJson(['totalProducts' => $productTotals], $AS_ARRAY);
    }

    private function getYesterdayAndTodayInvoices()
    {
        $todayInvoices = FacturaCabecera::whereToday('fecha')->where('status', '!=', 'anulada')->get();
        $yesterdayInvoices = FacturaCabecera::whereDate('fecha', now()->subDay()->toDateString())->where('status', '!=', 'anulada')->get();

        return [
            'today' => $todayInvoices,
            'yesterday' => $yesterdayInvoices
        ];
    }

    private function getWeeklyInvoices()
    {
        $weeklyInvoices = FacturaCabecera::fromLastWeek();
        return $weeklyInvoices;
    }

    private function getPorcentualDifference($current, $previous)
    {
        if ($previous == 0) {
            if ($current > 0) return 100;

            return 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function returnAsArrayOrJson($data, $AS_ARRAY)
    {
        return !$AS_ARRAY ? response()->json([
            'success'  => true,
            'data' => $data
        ]) : $data;
    }
}
