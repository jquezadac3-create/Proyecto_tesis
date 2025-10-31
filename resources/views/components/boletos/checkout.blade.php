@extends('layout.simple-layout')

@section('title', 'Verificación de transacción')

@section('content')
    @if (isset($response['errorCode']))
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-2xl">
                <div class="rounded-2xl bg-white p-8 text-center shadow-xl">
                    <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900">Error en la transacción</h2>
                    <p class="text-gray-600">Código de error: {{ $response['errorCode'] }}</p>
                    <p class="text-gray-600">{{ $response['message'] }}</p>
                    <div class="mt-6 space-y-2">
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                            <div class="h-2 w-2 animate-pulse rounded-full bg-red-600"></div>
                            <span>Por favor intenta nuevamente o contacta soporte.</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('boletos-venta') }}"
                            class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-blue-700">Volver
                            a la compra</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-2xl">

                @if (isset($response['statusCode']) && $response['statusCode'] === 3)
                    <!-- Success State -->
                    <div x-transition class="rounded-2xl bg-white p-8 shadow-xl">
                        <div class="mb-8 text-center">
                            <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h1 class="mb-2 text-3xl font-bold text-gray-900">¡Pago Exitoso!</h1>
                            <p class="text-gray-600">Tu transacción ha sido procesada correctamente</p>
                        </div>

                        <div class="mb-6 rounded-xl bg-gray-50 p-6">
                            <h3 class="mb-4 font-semibold text-gray-900">Detalles de la Transacción</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">ID de Transacción</span>
                                    <span
                                        class="font-mono text-sm font-semibold text-gray-900">{{ $response['transactionId'] }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">Método de Pago</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ $response['cardType'] === 'Credit' ? 'Tarjeta de Crédito' : 'Tarjeta de Débito' }}
                                    </span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">Fecha y Hora</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ date_create($response['date'])->format('d/m/Y H:i') }} </span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">Estado</span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        Aprobado </span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="font-semibold text-gray-600">Monto Total</span>
                                    <span class="text-2xl font-bold text-green-600"> ${{ $response['amount'] / 100 }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="mb-1 font-semibold text-blue-900">¿Qué sigue?</h4>
                                    <p class="text-sm text-blue-800">Recibirás un correo de confirmación de tu pedido
                                        próximamente.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif (isset($response['statusCode']) && $response['statusCode'] === 2)
                    <!-- Failed State -->
                    <div x-show="status === 'failed'" x-transition class="rounded-2xl bg-white p-8 shadow-xl">
                        <div class="mb-8 text-center">
                            <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </div>
                            <h1 class="mb-2 text-3xl font-bold text-gray-900">Pago Rechazado</h1>
                            <p class="text-gray-600">No pudimos procesar tu transacción</p>
                        </div>

                        {{-- <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-6">
                            <h3 class="mb-3 font-semibold text-red-900">Motivo del Rechazo</h3>
                            <p class="text-red-800" x-text="errorMessage"></p>
                        </div> --}}

                        <div class="mb-6 rounded-xl bg-gray-50 p-6">
                            <h3 class="mb-4 font-semibold text-gray-900">Detalles del Intento</h3>
                            <div class="space-y-3">
                                {{-- <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">ID de Transacción</span>
                                    <span class="font-mono text-sm font-semibold text-gray-900" x-text="transactionId"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">Método de Pago</span>
                                    <span class="font-semibold text-gray-900" x-text="paymentMethod"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">Fecha y Hora</span>
                                    <span class="font-semibold text-gray-900" x-text="paymentDate"></span>
                                </div> --}}
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600">Estado</span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                                        Transacción Cancelada </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="mb-1 font-semibold text-blue-900">Sugerencias</h4>
                                    <ul class="space-y-1 text-sm text-blue-800">
                                        <li>• Verifica que tu tarjeta tenga fondos suficientes</li>
                                        <li>• Confirma que los datos ingresados sean correctos</li>
                                        <li>• Contacta a tu banco si el problema persiste</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div x-transition class="rounded-2xl bg-white p-8 shadow-xl">
                        <div class="mb-8 text-center">
                            <div class="mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full bg-yellow-100">
                                <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h1 class="mb-2 text-3xl font-bold text-gray-900">Pago Pendiente</h1>
                            <p class="text-gray-600">Tu transacción está siendo procesada</p>
                        </div>

                        <div class="mb-6 rounded-xl border border-yellow-200 bg-yellow-50 p-6">
                            <h3 class="mb-3 font-semibold text-yellow-900">Estado de la Transacción</h3>
                            <p class="mb-4 text-yellow-800">Tu pago está siendo procesado. Este proceso puede tomar
                                algunos minutos.</p>
                            <div class="flex items-center justify-center gap-2 text-sm text-yellow-700">
                                <div class="h-2 w-2 animate-pulse rounded-full bg-yellow-600"></div>
                                <span>Actualiza esta ventana para ver el estado</span>
                            </div>
                        </div>

                        <div class="mb-6 rounded-xl bg-gray-50 p-6">
                            <h3 class="mb-4 font-semibold text-gray-900">Detalles de la Transacción</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-gray-200 py-2">
                                    <span class="text-gray-600">ID de Transacción</span>
                                    {{-- Obtener el query param id --}}
                                    <span class="font-mono text-sm font-semibold text-gray-900"> {{ request()->query('id') ?? 'No encontrado' }} </span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600">Estado</span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-800">
                                        En Proceso </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="mb-1 font-semibold text-blue-900">Información Importante</h4>
                                    <p class="text-sm text-blue-800">Te enviaremos un correo electrónico cuando tu pago sea
                                        confirmado. Recarga esta página para consultar si el estado ha cambiado.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('boletos-venta') }}"
                        class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-blue-700">Volver
                        al inicio</a>
                </div>
            </div>
        </div>
    @endif
@endsection