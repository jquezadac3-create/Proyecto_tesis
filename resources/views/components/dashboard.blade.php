{{-- @extends('layout.main-layout')

@section('title', 'Dashboard')

@section('breadcrumb')

<div x-show="selected === 'dashboard.index'">
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12">
                <!-- Metric Group Two -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">
                    <!-- Metric Item Start -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                            Unique Visitors
                        </p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    24.7K
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +20%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                            Total Pageviews
                        </p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    55.9K
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +4%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">Bounce Rate</p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">54%</h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                    -1.59%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->

                    <!-- Metric Item Start -->
                    <div
                        class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">Visit Duration</p>

                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                                    2m 56s
                                </h4>
                            </div>

                            <div class="flex items-center gap-1">
                                <span
                                    class="flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    +7%
                                </span>

                                <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                    Vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Metric Item End -->
                </div>
                <!-- Metric Group Two -->
            </div>

            <div class="col-span-12">
                <!-- ====== Chart Four Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                    <div class="flex flex-wrap items-start justify-between gap-5">
                        <div>
                            <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                Analytics
                            </h3>
                            <span class="block text-theme-sm text-gray-500 dark:text-gray-400">
                                Visitor analytics of last 30 days
                            </span>
                        </div>

                        <div x-data="{ selected: 'optionOne' }"
                            class="flex items-center gap-0.5 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
                            <button @click="selected = 'optionOne'" :class="selected === 'optionOne' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class=":hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800">
                                12 months
                            </button>

                            <button @click="selected = 'optionTwo'" :class="selected === 'optionTwo' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="hover:text-gray-900dark:hover:text-white rounded-md px-3 py-2 text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                30 days
                            </button>

                            <button @click="selected = 'optionThree'" :class="selected === 'optionThree' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
                                7 days
                            </button>

                            <button @click="selected = 'optionFour'" :class="selected === 'optionFour' ?
                                    'shadow-theme-xs text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                    'text-gray-500 dark:text-gray-400'"
                                class="rounded-md px-3 py-2 text-theme-sm font-medium hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
                                24 hours
                            </button>
                        </div>
                    </div>
                </div>
                <!-- ====== Chart Four End -->
            </div>
        </div>
    </div>
</div>
@endsection --}}
@extends('layout.main-layout')

@section('title', 'Dashboard')

@section('assets')
    {{-- Chart.js desde CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- ApexCharts para el gráfico de donut --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('breadcrumb')
    {{-- cards --}}
    <div class="grid grid-cols-1 p-5 gap-4 sm:grid-cols-2 md:gap-6 xl:grid-cols-4">

        @php
            $todaySales = $data['todaySales']['total'];
            $todaySalesDiff = $data['todaySales']['diff'];
            $todayPurchases = $data['todayPurchases']['count'];
            $todayPurchasesDiff = $data['todayPurchases']['diff'];
            $cashSales = $data['cashSales']['total'];
            $cashSalesDiff = $data['cashSales']['diff'];
            $otherSales = $data['otherSales']['total'];
            $otherSalesDiff = $data['otherSales']['diff'];
        @endphp

        <!-- Ventas Totales -->
        <div @click="showChart('ventasTotales')"
            class="cursor-pointer rounded-2xl border border-gray-200 bg-white px-6 pb-5 pt-6 hover:shadow-lg transition dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="mb-6 flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center text-white bg-blue-500 rounded-full">
                    <i class="fa fa-dollar-sign"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Ventas Totales</h3>
                </div>
            </div>

            <div class="flex items-end justify-between">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">${{ $todaySales }}</h4>
                <span @class([
                    'flex',
                    'items-center',
                    'gap-1',
                    'rounded-full',
                    'py-0.5',
                    'pl-2',
                    'pr-2.5',
                    'text-sm',
                    'font-medium',

                    'bg-success-50' => $todaySalesDiff > 15,
                    'text-success-600' => $todaySalesDiff > 15,
                    'dark:bg-success-500/15' => $todaySalesDiff > 15,
                    'dark:text-success-500' => $todaySalesDiff > 15,

                    'bg-warning-50' => $todaySalesDiff <= 15 && $todaySalesDiff > 0,
                    'text-warning-600' => $todaySalesDiff <= 15 && $todaySalesDiff > 0,
                    'dark:bg-warning-500/15' => $todaySalesDiff <= 15 && $todaySalesDiff > 0,
                    'dark:text-warning-500' => $todaySalesDiff <= 15 && $todaySalesDiff > 0,

                    'bg-error-50' => $todaySalesDiff <= 0,
                    'text-error-600' => $todaySalesDiff <= 0,
                    'dark:bg-error-500/15' => $todaySalesDiff <= 0,
                    'dark:text-error-500' => $todaySalesDiff <= 0,
                ])>
                    <i @class(['fa', 'fa-arrow-down' => ($todaySalesDiff <= 0), 'fa-arrow-up' => ($todaySalesDiff > 0)])
                        class="fa fa-arrow-down"></i>
                    {{ $todaySalesDiff }}%
                </span>
            </div>
        </div>

        <!-- Número de Facturas -->
        <div @click="showChart('facturas')"
            class="cursor-pointer rounded-2xl border border-gray-200 bg-white px-6 pb-5 pt-6 hover:shadow-lg transition dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="mb-6 flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center text-white bg-yellow-500 rounded-full">
                    <i class="fa fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90"># Facturas</h3>
                </div>
            </div>

            <div class="flex items-end justify-between">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $todayPurchases }}</h4>
                <span @class([
                    'flex',
                    'items-center',
                    'gap-1',
                    'rounded-full',
                    'py-0.5',
                    'pl-2',
                    'pr-2.5',
                    'text-sm',
                    'font-medium',

                    'bg-success-50' => $todayPurchasesDiff > 15,
                    'text-success-600' => $todayPurchasesDiff > 15,
                    'dark:bg-success-500/15' => $todayPurchasesDiff > 15,
                    'dark:text-success-500' => $todayPurchasesDiff > 15,

                    'bg-warning-50' => $todayPurchasesDiff <= 15 && $todayPurchasesDiff > 0,
                    'text-warning-600' => $todayPurchasesDiff <= 15 && $todayPurchasesDiff > 0,
                    'dark:bg-warning-500/15' => $todayPurchasesDiff <= 15 && $todayPurchasesDiff > 0,
                    'dark:text-warning-500' => $todayPurchasesDiff <= 15 && $todayPurchasesDiff > 0,

                    'bg-error-50' => $todayPurchasesDiff <= 0,
                    'text-error-600' => $todayPurchasesDiff <= 0,
                    'dark:bg-error-500/15' => $todayPurchasesDiff <= 0,
                    'dark:text-error-500' => $todayPurchasesDiff <= 0,
                ])>
                    <i @class(['fa', 'fa-arrow-down' => ($todayPurchasesDiff <= 0), 'fa-arrow-up' => ($todayPurchasesDiff > 0)]) class="fa fa-arrow-down"></i>
                    {{ $todayPurchasesDiff }}%
                </span>
            </div>
        </div>

        <!-- Ventas en Efectivo -->
        <div @click="showChart('efectivo')"
            class="cursor-pointer rounded-2xl border border-gray-200 bg-white px-6 pb-5 pt-6 hover:shadow-lg transition dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="mb-6 flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center text-white bg-green-500 rounded-full">
                    <i class="fa fa-wallet"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Ventas en Efectivo</h3>
                </div>
            </div>

            <div class="flex items-end justify-between">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">${{ $cashSales }}</h4>
                <span @class([
                    'flex',
                    'items-center',
                    'gap-1',
                    'rounded-full',
                    'py-0.5',
                    'pl-2',
                    'pr-2.5',
                    'text-sm',
                    'font-medium',

                    'bg-success-50' => $cashSalesDiff > 15,
                    'text-success-600' => $cashSalesDiff > 15,
                    'dark:bg-success-500/15' => $cashSalesDiff > 15,
                    'dark:text-success-500' => $cashSalesDiff > 15,

                    'bg-warning-50' => $cashSalesDiff <= 15 && $cashSalesDiff > 0,
                    'text-warning-600' => $cashSalesDiff <= 15 && $cashSalesDiff > 0,
                    'dark:bg-warning-500/15' => $cashSalesDiff <= 15 && $cashSalesDiff > 0,
                    'dark:text-warning-500' => $cashSalesDiff <= 15 && $cashSalesDiff > 0,

                    'bg-error-50' => $cashSalesDiff <= 0,
                    'text-error-600' => $cashSalesDiff <= 0,
                    'dark:bg-error-500/15' => $cashSalesDiff <= 0,
                    'dark:text-error-500' => $cashSalesDiff <= 0,
                ])>
                    <i @class(['fa', 'fa-arrow-down' => ($cashSalesDiff <= 0), 'fa-arrow-up' => ($cashSalesDiff > 0)])
                        class="fa fa-arrow-down"></i>
                    {{ $cashSalesDiff }}%
                </span>
            </div>
        </div>

        <!-- Otras Formas de Pago -->
        <div @click="showChart('otrosPagos')"
            class="cursor-pointer rounded-2xl border border-gray-200 bg-white px-6 pb-5 pt-6 hover:shadow-lg transition dark:border-gray-800 dark:bg-white/[0.03]">

            <div class="mb-6 flex items-center gap-3">
                <div class="h-10 w-10 flex items-center justify-center text-white bg-red-500 rounded-full">
                    <i class="fa fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Otras Formas de Pago</h3>
                </div>
            </div>

            <div class="flex items-end justify-between">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">${{ $otherSales }}</h4>
                <span @class([
                    'flex',
                    'items-center',
                    'gap-1',
                    'rounded-full',
                    'py-0.5',
                    'pl-2',
                    'pr-2.5',
                    'text-sm',
                    'font-medium',

                    'bg-success-50' => $otherSalesDiff > 15,
                    'text-success-600' => $otherSalesDiff > 15,
                    'dark:bg-success-500/15' => $otherSalesDiff > 15,
                    'dark:text-success-500' => $otherSalesDiff > 15,

                    'bg-warning-50' => $otherSalesDiff <= 15 && $otherSalesDiff > 0,
                    'text-warning-600' => $otherSalesDiff <= 15 && $otherSalesDiff > 0,
                    'dark:bg-warning-500/15' => $otherSalesDiff <= 15 && $otherSalesDiff > 0,
                    'dark:text-warning-500' => $otherSalesDiff <= 15 && $otherSalesDiff > 0,

                    'bg-error-50' => $otherSalesDiff <= 0,
                    'text-error-600' => $otherSalesDiff <= 0,
                    'dark:bg-error-500/15' => $otherSalesDiff <= 0,
                    'dark:text-error-500' => $otherSalesDiff <= 0,
                ])>
                    <i @class(['fa', 'fa-arrow-down' => ($otherSalesDiff <= 0), 'fa-arrow-up' => ($otherSalesDiff > 0)])
                        class="fa fa-arrow-down"></i>
                    {{ $otherSalesDiff }}%
                </span>
            </div>
        </div>
    </div>
    {{-- charts --}}
    <div class="grid grid-cols-1 px-5 gap-2 sm:grid-cols-2 md:gap-6 xl:grid-cols-2">

        <!-- Gráfico de Ventas Totales -->
        <div class="mt-6 px-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Ventas Totales Semanales
                        </h3>
                    </div>
                </div>
                <div class="custom-scrollbar max-w-full overflow-x-auto">
                    <div id="chartVentasTotales" style="min-height: 330px;"></div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Donut (Reemplazo del Funnel) -->
        <div class="mt-6 px-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 flex justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Productos Vendidos
                        </h3>
                    </div>
                    <div x-data="{ openDropDown: false }" class="relative h-fit">
                        <button @click="openDropDown = !openDropDown" :class="openDropDown ? 'text-gray-700 dark:text-white' :
                                                        'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                    fill=""></path>
                            </svg>
                        </button>
                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                            class="shadow-theme-lg dark:bg-gray-dark absolute top-full right-0 z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 dark:border-gray-800"
                            style="display: none;">
                            <button
                                class="text-theme-xs flex w-full rounded-lg px-3 py-2 text-left font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                View More
                            </button>
                            <button
                                class="text-theme-xs flex w-full rounded-lg px-3 py-2 text-left font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div class="custom-scrollbar max-w-full overflow-x-auto">
                    <div id="chartDonut" style="min-height: 330px;"></div>
                </div>
            </div>
        </div>

    </div>
    {{-- link --}}
    <div class="p-10">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-6">
            <!-- Encabezado con icono -->
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-xl">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Enlace público de ventas</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Comparte este link para que los clientes compren entradas en línea.
                    </p>
                </div>
            </div>

            <!-- Caja del link con botón copiar -->
            <div x-data="{ link: '{{ route('boletos-venta') }}', copied: false }" class="flex items-center gap-3">
                <input type="text" x-model="link" readonly
                    class="w-full sm:w-96 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                <button
                    @click="navigator.clipboard.writeText(link).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                    class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <i class="fas fa-copy"></i>
                    <span x-show="!copied">Copiar</span>
                    <span x-show="copied" class="text-green-200">¡Copiado!</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar gráfico de Donut
            initDonutChart();

            // Inicializar gráfico de Ventas Totales
            initVentasTotalesChart();
        });

        function initDonutChart() {
            const productData = @json($data['totalProductsSales']);

            if (Object.keys(productData.totalProducts).length === 0) {
                document.getElementById('chartDonut').innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
                return;
            }

            const labels = Object.keys(productData.totalProducts);
            const options = {
                series: Object.values(productData.totalProducts),
                chart: {
                    type: 'donut',
                    height: 330,
                },
                labels: labels,
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontFamily: 'Outfit, sans-serif',
                    markers: {
                        width: 10,
                        height: 10,
                        radius: 10,
                    },
                    itemMargin: {
                        horizontal: 10,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '16px',
                                    fontFamily: 'Outfit, sans-serif',
                                    fontWeight: 400,
                                    color: '#373d3f',
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontFamily: 'Outfit, sans-serif',
                                    fontWeight: 600,
                                    color: '#373d3f',
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#373d3f',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                    }
                                }
                            }
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#chartDonut"), options);
            chart.render();
        }

        function generateSimilarColors(baseHue = 235, count = 5) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const saturation = 75 + i * 5;
                const lightness = 60 + i * 3;
                colors.push(`hsl(${baseHue}, ${saturation}%, ${lightness}%)`);
            }
            return colors;
        }

        function initVentasTotalesChart() {
            const ventasSemanales = @json($data['weeklySales']);
            const ventasValores = Object.values(ventasSemanales.weekly).map(item => item.total_factura);
            const ventasLabels = Object.values(ventasSemanales.weekly).map(item => item.fecha);

            if (ventasLabels.length === 0 || ventasValores.length === 0) {
                document.getElementById('chartVentasTotales').innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400">No hay datos disponibles</p>';
                return;
            }

            // Configuración del gráfico
            const options = {
                series: [{
                    name: 'Ventas Totales',
                    data: ventasValores,
                }],
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        columnWidth: '50%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 0
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 0,
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: ventasLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#373d3f',
                            fontSize: '12px',
                            fontFamily: 'Outfit, sans-serif',
                            fontWeight: 400,
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return '$' + value.toLocaleString();
                        },
                        style: {
                            colors: '#373d3f',
                            fontSize: '12px',
                            fontFamily: 'Outfit, sans-serif',
                            fontWeight: 400,
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                fill: {
                    opacity: 1,
                    colors: ['#2a31d8']
                },
                colors: ['#2a31d8'],
            };

            // Renderizar el gráfico
            const chart = new ApexCharts(document.querySelector("#chartVentasTotales"), options);
            chart.render();
        }

        // Función para mostrar el gráfico (simulada)
        function showChart(chartType) {
            console.log('Mostrando gráfico:', chartType);
        }
    </script>
@endsection