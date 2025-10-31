@extends('layout.main-layout')

@section('title', 'Configuración')

@section('breadcrumb')
    <div class="mx-auto max-w-7xl p-4 md:p-6">
        <!-- Header con iconos y acciones -->
        <div x-data="{ pageName: `Configuración Sistema` }">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Configuración
                    Sistema
                </h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="index.html">
                                Home
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Configuración Sistema
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Sección: Empresa -->
            <div
                class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <div class="flex justify-between items-center space-x-3">
                        <div class="w-full flex items-center space-x-3">
                            <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/30">
                                <i class="fas fa-building text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h2 class="text-lg font-medium text-gray-800 dark:text-white">Datos de la empresa</h2>
                        </div>
                        <a href="{{ route('configuracion.index') }}" class="cursor-pointer flex justify-between items-center">
                            <button aria-label="create something epic" type="button" class="inline-flex justify-center items-center aspect-square whitespace-nowrap rounded-full border border-blue-600 bg-blue-500 p-2 text-sm font-medium tracking-wide text-on-primary transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:border-primary-dark dark:bg-primary-dark dark:text-on-primary-dark dark:focus-visible:outline-primary-dark"><i class="fa-regular fa-pen-to-square  text-white"></i></button>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-building text-xs"></i>
                                <span>Razón social</span>
                            </dt>
                            <dd class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $config->razon_social ?? 'No especificado' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-store text-xs"></i>
                                <span>Nombre comercial</span>
                            </dt>
                            <dd class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $config->nombre_comercial ?? 'No especificado' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-id-card text-xs"></i>
                                <span>RUC</span>
                            </dt>
                            <dd class="mt-2 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                {{ $config->ruc ?? 'No especificado' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-code text-xs"></i>
                                <span>Código establecimiento</span>
                            </dt>
                            <dd class="mt-2 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                {{ $config->codigo_establecimiento ?? 'No especificado' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-hashtag text-xs"></i>
                                <span>Serie del RUC</span>
                            </dt>
                            <dd class="mt-2 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                {{ $config->serie_ruc ?? 'No especificado' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-user-tag text-xs"></i>
                                <span>Tipo contribuyente</span>
                            </dt>
                            <dd class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $config->tipo_contribuyente ?? 'No especificado' }}</dd>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-map-marker-alt text-xs"></i>
                                <span>Dirección matriz</span>
                            </dt>
                            <dd class="mt-2 text-gray-900 dark:text-white">
                                {{ $config->direccion_matriz ?? 'No especificada' }}</dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-building text-xs"></i>
                                <span>Dirección establecimiento</span>
                            </dt>
                            <dd class="mt-2 text-gray-900 dark:text-white">
                                {{ $config->direccion_establecimiento ?? 'No especificada' }}</dd>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sección: Tributaria -->
            <div
                class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900/30">
                            <i class="fas fa-calculator text-green-600 dark:text-green-400"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-white">Configuración tributaria</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-balance-scale text-xs"></i>
                                <span>Obligado a llevar contabilidad</span>
                            </dt>
                            <dd class="mt-2 flex items-center space-x-2">
                                @if($config->obligado_contabilidad == 'SI')
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-check mr-1"></i>Sí
                                    </span>
                                @elseif($config->obligado_contabilidad == 'NO')
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fas fa-times mr-1"></i>No
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No especificado
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-cloud text-xs"></i>
                                <span>Ambiente</span>
                            </dt>
                            <dd class="mt-2">
                                @if($config->ambiente == 'PRODUCCION')
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fas fa-globe mr-1"></i>Producción
                                    </span>
                                @elseif($config->ambiente == 'PRUEBAS')
                                    <span
                                        class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        <i class="fas fa-flask mr-1"></i>Pruebas
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No especificado
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-digital-tachograph text-xs"></i>
                                <span>Estado electrónica</span>
                            </dt>
                            <dd class="mt-2">
                                @if($config->estado_electronica)
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-circle text-xs mr-2"></i>Activa
                                    </span>
                                @elseif(!$config->estado_electronica == 'Inactiva')
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        <i class="fas fa-circle text-xs mr-2"></i>Inactiva
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No especificado
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-receipt text-xs"></i>
                                <span>Número de factura</span>
                            </dt>
                            <dd class="mt-2">
                                @if($config->numero_factura)
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center rounded-full py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            <i class="fas fa-hashtag mr-1"></i>
                                        </span>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ str_pad($config->numero_factura, 9, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>No configurado
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sección: Archivos y seguridad -->
            <div
                class="group rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <div class="flex items-center space-x-3">
                        <div class="rounded-lg bg-purple-100 p-2 dark:bg-purple-900/30">
                            <i class="fas fa-shield-alt text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h2 class="text-lg font-medium text-gray-800 dark:text-white">Archivos y seguridad</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div
                            class="group/item rounded-lg border border-gray-100 p-6 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt
                                class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">
                                <i class="fas fa-certificate text-xs"></i>
                                <span>Firma electrónica</span>
                            </dt>
                            <dd class="flex items-center space-x-3">
                                @if(isset($config->firma_path))
                                    <div class="flex items-center space-x-3">
                                        <div class="rounded-full bg-green-100 p-2 dark:bg-green-900/30">
                                            <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-medium text-green-800 dark:text-green-300">Archivo
                                                configurado</span>
                                            <span class="text-xs text-gray-500">Certificado .p12 cargado</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-3">
                                        <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                                            <i class="fas fa-exclamation-triangle text-gray-500"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-800 dark:text-gray-300">No
                                                configurado</span>
                                            <span class="text-xs text-gray-500">Certificado digital requerido</span>
                                        </div>
                                    </div>
                                @endif
                            </dd>
                        </div>
                        <div
                            class="group/item rounded-lg border border-gray-100 p-6 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50">
                            <dt
                                class="flex items-center space-x-2 text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">
                                <i class="fas fa-image text-xs"></i>
                                <span>Logo empresarial</span>
                            </dt>
                            <dd>
                                @if(isset($config->logo))
                                    <div class="flex items-start space-x-4">
                                        <img src="{{ $config->logo }}" alt="Logo"
                                            class="h-20 w-20 rounded-lg border border-gray-200 object-contain shadow-sm dark:border-gray-700">
                                        <div class="flex-1">
                                            <span class="block text-sm font-medium text-gray-800 dark:text-gray-300">Logo
                                                configurado</span>
                                            <span class="text-xs text-gray-500">Imagen corporativa activa</span>
                                            <div class="mt-2">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    <i class="fas fa-check mr-1"></i>Activo
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="flex h-20 w-20 items-center justify-center rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                                            <i class="fas fa-image text-2xl text-gray-400"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-800 dark:text-gray-300">Sin
                                                logo</span>
                                            <span class="text-xs text-gray-500">No se ha configurado una imagen</span>
                                            <div class="mt-2">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Pendiente
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection