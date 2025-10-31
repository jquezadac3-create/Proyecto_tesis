@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/sorteos/cargarDatosExcel.js'])

@endsection

@section('title', 'Importar Datos Factura')

@section('breadcrumb')
    <div x-show="selected === 'sorteo.cargarDatosFactura'">

        <body :class="">
            <div x-data="{}">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Importar Datos Factura` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">
                                Importar Datos Factura
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
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->
                    <x-sorteos.cargarDatosFactura.table />

                </div>
            </div>
        </body>
    </div>
@endsection
