@extends('layout.main-layout')

@section('title', 'Escáner QR')

@section('assets')
    @vite(['resources/js/qr/qrScann.js', 'resources/js/qr/welcomeGuide.js'])
@endsection

@section('breadcrumb')
    <section class="container mx-auto px-4 py-8">
        <div class="flex justify-end">
            <button id="help-btn"
                title="Mostrar guía de uso"
                class="inline-flex items-center rounded-full bg-gray-100 p-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <i class="fa fa-question"></i>
            </button>
        </div>
        <!-- Header -->
        <div id="header-title-qr" class="mb-8 text-center">
            <div
                class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600">
                <i class="fa fa-qrcode text-3xl text-white"></i>
            </div>
            <h1 class="mb-2 text-3xl font-bold text-gray-800 sm:text-4xl">Escáner QR</h1>
            <p class="text-lg text-gray-600">Escanea códigos QR con tu cámara web o scanner físico y obtén la información al instante</p>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-[28rem]">
            <div class="sm:grid sm:grid-cols-1 gap-8">
                <!-- Scanner Section -->
                <div class="rounded-2xl bg-white p-6 shadow-xl">
                    <div class="mb-6 text-center">
                        <h2 class="mb-2 text-2xl font-semibold text-gray-800">Cámara</h2>
                        <div id="status" class="text-sm text-gray-600">
                            <span class="inline-flex items-center">
                                <span id="status-indicator" class="mr-2 h-2 w-2 rounded-full bg-gray-400"></span>
                                <span id="status-text">Listo para escanear - Usa la cámara o scanner físico</span>
                            </span>
                        </div>
                    </div>

                    <div class="scanner-container mb-6 relative">
                        <div id="reader" class="w-full h-[300px] rounded-xl bg-gray-100 overflow-hidden"></div>

                        <!-- Placeholder when camera is off -->
                        <div id="camera-placeholder"
                            class="absolute inset-0 flex h-full w-full items-center justify-center rounded-xl bg-gray-100">
                            <div class="text-center">
                                <i class="fa fa-camera text-6xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg font-medium">Múltiples opciones disponibles</p>
                                <p class="text-gray-400 text-sm mt-2">Cámara web, scanner físico o entrada manual</p>
                            </div>
                        </div>
                    </div>

                    <!-- Control Buttons -->
                    <div id="control-buttons" class="flex flex-col gap-3 sm:flex-row">
                        <button id="start-btn"
                            class="flex-1 transform rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3 font-semibold text-white shadow-lg transition duration-200 hover:scale-105 hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                            <span class="inline-flex items-center justify-center">
                                <i id="start-icon" class="fa-solid fa-play mr-2"></i>
                                <span id="start-text">Iniciar Escáner</span>
                            </span>
                        </button>
                        <button id="stop-btn"
                            class="flex-1 transform rounded-xl bg-gradient-to-r from-red-600 to-pink-600 px-6 py-3 font-semibold text-white shadow-lg transition duration-200 hover:scale-105 hover:from-red-700 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            style="display: none;" disabled>
                            <span class="inline-flex items-center justify-center">
                                <i class="fa fa-stop mr-2"></i>
                                <span>Detener</span>
                            </span>
                        </button>
                    </div>

                    <!-- Manual Input Section -->
                    <div id="manual-input" class="mt-4 border-t border-gray-200 pt-4">
                        <details class="group" open>
                            <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-800 flex items-center">
                                <i class="fa fa-keyboard mr-2"></i>
                                Introducir código manualmente
                                <i class="fa fa-chevron-down ml-auto transition-transform group-open:rotate-180"></i>
                            </summary>
                            <div class="mt-3" x-data x-init="$nextTick(() => $refs.searchInput.focus())">
                                <form id="form-search" class="flex gap-2">
                                    @csrf
                                    <input type="text" id="search-input" x-ref="searchInput"
                                        class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        placeholder="Introduce el código QR...">
                                    <button type="submit"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>

                    <!-- Hidden form for automatic submission -->
                    <form class="hidden" id="form-search-auto">
                        @csrf
                        <input type="text" id="search-input-auto" class="hidden">
                        <button type="submit" hidden class="hidden" id="submit-search"></button>
                    </form>
                </div>
            </div>
        </div>

        <div x-data="{modalIsOpen: false}">
            <div x-on:result.document="modalIsOpen = true" x-cloak x-show="modalIsOpen" x-transition:opacity.duration.200ms
                x-on:keydown.esc.window="modalIsOpen = false" x-on:click.self="modalIsOpen = false"
                class="fixed inset-0 z-99999 flex items-center justify-center bg-black/20 p-4 pb-4 backdrop-blur-md overflow-y-auto custom-scrollbar"
                role="dialog" aria-modal="true" aria-labelledby="defaultModalTitle">
                <!-- Modal Dialog -->
                <div x-show="modalIsOpen"
                    x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                    x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                    class="flex max-w-lg flex-col gap-4 overflow-hidden rounded-radius bg-surface text-on-surface dark:bg-surface-dark-alt dark:text-on-surface-dark">
                    <!-- Dialog Header -->
                    <div class="flex items-center justify-between bg-surface-alt/60 p-4 dark:bg-surface-dark/20">
                        <h4 class="text-gray-600 font-bold">Qr info:</h4>
                        <button x-on:click="modalIsOpen = false" ><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <!-- Dialog Body -->
                    <div class="flex-1 overflow-y-auto px-4 pb-4 max-h-[70vh] custom-scrollbar">
                        <div id="results-container">
                            <!-- No results state -->
                            <div id="no-results" class="py-12 text-center">
                                <i class="fa-regular fa-file text-7xl text-gray-200"></i>
                                <p class="text-gray-500">Aún no hay resultados</p>
                                <p class="mt-1 text-sm text-gray-400">Escanea un código QR para ver su contenido</p>
                            </div>

                            <!-- Results list -->
                            <div id="results-list" class="space-y-4">
                            </div>
                        </div>
                    </div>
                    <!-- Dialog Footer -->
                    <div
                        class="flex flex-col-reverse justify-between gap-2 bg-surface-alt/60 p-4 dark:bg-surface-dark/20 sm:flex-row sm:items-center md:justify-end">
                        <!-- Clear Results Button -->
                        <div id="clear-section" class="border-t  border-gray-200 hidden">
                            <button id="clear-btn" x-on:resultscleared.document="modalIsOpen = false"
                                class="w-full rounded-lg bg-gray-600 px-4 py-2 font-semibold text-white transition duration-200 hover:bg-gray-700">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        {{-- <div class="mx-auto mt-8 max-w-4xl">
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                <h3 class="mb-3 text-lg font-semibold text-blue-800">Instrucciones de uso</h3>
                <div class="grid grid-cols-1 gap-4 text-sm text-blue-700 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-blue-200 text-center text-xs leading-6 font-bold text-blue-800">1</span>
                        <span>Permite el acceso a tu cámara cuando el navegador lo solicite</span>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-blue-200 text-center text-xs leading-6 font-bold text-blue-800">2</span>
                        <span>Apunta la cámara hacia el código QR que deseas escanear</span>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-blue-200 text-center text-xs leading-6 font-bold text-blue-800">3</span>
                        <span>El resultado aparecerá automáticamente en la sección de resultados</span>
                    </div>
                </div>
            </div>

            <!-- Hardware Scanner Instructions -->
            <div class="rounded-xl border border-green-200 bg-green-50 p-6 mt-4">
                <h3 class="mb-3 text-lg font-semibold text-green-800 flex items-center">
                    <i class="fas fa-barcode mr-2"></i>
                    Scanner Físico Compatible
                </h3>
                <div class="grid grid-cols-1 gap-4 text-sm text-green-700 sm:grid-cols-2">
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-green-200 text-center text-xs leading-6 font-bold text-green-800">1</span>
                        <span>Conecta tu scanner HH492 o similar al dispositivo</span>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-green-200 text-center text-xs leading-6 font-bold text-green-800">2</span>
                        <span>Mantén el cursor en esta página (sin hacer clic en campos de texto)</span>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-green-200 text-center text-xs leading-6 font-bold text-green-800">3</span>
                        <span>Escanea directamente con el scanner - se procesará automáticamente</span>
                    </div>
                    <div class="flex items-start">
                        <span
                            class="mr-3 inline-block h-6 w-6 flex-shrink-0 rounded-full bg-green-200 text-center text-xs leading-6 font-bold text-green-800">4</span>
                        <span>No necesitas activar la cámara para usar el scanner físico</span>
                    </div>
                </div>
            </div>
        </div> --}}
    </section>

    <!-- CSS Styles -->
    <style>
        .slide-in-up {
            animation: slideInUp 0.6s ease-out;
        }

        .slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .product-card {
            transition: transform 0.2s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        /* QR Scanner specific styles */
        #reader {
            border: 2px solid #e5e7eb;
            transition: border-color 0.3s ease;
        }

        #reader.scanning {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .status-active {
            background-color: #10b981 !important;
        }

        .status-error {
            background-color: #ef4444 !important;
        }

        .status-idle {
            background-color: #6b7280 !important;
        }

        .driver-overlay {
            z-index: 100000 !important;
        }
    </style>
@endsection