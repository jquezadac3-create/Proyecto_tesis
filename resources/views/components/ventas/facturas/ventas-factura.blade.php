<!-- resources/views/ventas/factura.blade.php -->
@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/ventas/factura.js'])
@endsection

@section('title', 'Ventas Factura')

@section('breadcrumb')
    <div x-show="selected === 'sales.bill'">
        <div class="mx-auto max-w-(--breakpoint-2xl) pt-4 px-4 md:px-6 md:pt-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: 'Factura' }">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                    <h2 id="tituloFactura" class="text-xl font-semibold text-gray-800 dark:text-white/90">
                        Factura
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
                            <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Crear factura</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Breadcrumb End -->

            <!-- Incluir componentes -->
            <x-ventas.facturas.cliente />
            <x-ventas.facturas.table />
            <x-ventas.facturas.summary />
            <x-ventas.facturas.actions />
        </div>
    </div>
@endsection