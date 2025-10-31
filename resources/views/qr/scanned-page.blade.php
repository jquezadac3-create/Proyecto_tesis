@extends('layout.simple-layout')

@section('title', 'Detalle del Código QR Escaneado')

@section('content')
<section class="gradient-bg min-h-screen p-4">
    @php
        $qrData = $savedQr->qr_code_data;
        $qrDataDecoded = json_decode($qrData, true);
    @endphp
    <!-- Success Header -->
    <div class="text-center mb-8 slide-in-up">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4 success-pulse">
            <i class="fas fa-check-circle text-green-500 text-4xl"></i>
        </div>
        <h1 class="text-3xl font-bold mb-2">¡Compra Verificada!</h1>
        <p class="text-lg">Tu código QR ha sido escaneado exitosamente</p>
    </div>

    <!-- Main Content Container -->
    <div class="max-w-6xl mx-auto">
        <!-- Quick Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="border-2 rounded-2xl p-6 slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-user text-2xl"></i>
                    </div>
                    <div class="status-badge bg-blue-500 text-xs px-3 py-1 rounded-full font-semibold">
                        VERIFICADO
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-3">{{ $cliente->nombres ?? 'CONSUMIDOR' }} {{ $cliente->apellidos ?? 'FINAL' }}</h3>
                <div class="space-y-2 text-sm">
                    <p><i class="fas fa-id-card mr-2"></i>{{ $cliente->dni ?? '9999999999999' }}</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i>{{ $cliente->direccion ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="border-2 rounded-2xl p-6 slide-in-left" style="animation-delay: 0.1s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-2">Factura #{{ str_pad($invoiceDate->id, 9, '0', STR_PAD_LEFT) }}</h3>
                <p class="text-sm">{{ date_create($invoiceDate->fecha)->format('d M, Y - H:i') }}</p>
            </div>

            <div class="border-2 rounded-2xl p-6 slide-in-left" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold">${{ number_format($qrDataDecoded['total'], 2) }}</span>
                        <br>
                        <span class="text-xs"><span class="font-bold">IVA aplicado:</span> ${{ number_format($qrDataDecoded['iva'], 2) }}</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-2">Total Pagado</h3>
                <p class="text-sm">Incluye impuestos y servicios</p>
            </div>
        </div>

        <!-- Products Section -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden slide-in-up" style="animation-delay: 0.4s;">
            <div class="bg-gradient-to-r p-6">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-shopping-bag mr-3"></i>
                    Productos Adquiridos
                </h2>
                <p class="mt-1">{{ count($qrDataDecoded['items']) }} artículos en tu pedido</p>
            </div>

            <div class="p-6">
                @forelse ($qrDataDecoded['items'] as $item)
                    <div class="product-card bg-gray-50 rounded-xl p-6 mb-4 border border-gray-100">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i @class(["fas", "text-2xl", "text-white", "fa-ticket" => $item['jornada_id'], "fa-layer-group" => $item['abono_id']])></i>
                                </div>
                            </div>
                            <div class="flex-grow capitalize">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $item['factura_nombre'] }}</h3>
                                <p class="text-gray-600 mb-2">{{ $item['producto'] }}</p>
                                <div class="flex items-center space-x-4">
                                    <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                        Cantidad total adquirida: {{ $item['cantidad_inicial'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Cantidad Restante: {{ $item['cantidad_restante'] }} </p>
                                <p class="text-2xl font-bold text-gray-800">${{ number_format($item['precio_total'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No se encontraron productos.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection