@extends('layout.main-layout')

@section('title', 'Listado de Facturas')

@section('assets')
    @vite(['resources/js/ventas/listadoFacturas.js'])
@endsection

@section('breadcrumb')
    <div x-show="selected === 'sales.bills'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div>

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Listado de Facturas` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">
                                Listado de Facturas
                            </h2>
                            <nav>
                                <ol class="flex items-center gap-1.5">
                                    <li>
                                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                            href="{{ route('dashboard.index') }}">
                                            Home
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </li>
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Listado de Facturas
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-ventas.listado-facturas.table :pendingInvoices="$pendingInvoices"/>
                </div>
            </div>
        </body>
    </div>
@endsection