<!-- ABONOS DISPONIBLES -->
<section id="abonos" class="container mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold">🎫 Abonos Disponibles</h3>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($abonos as $abono)
            <div class="overflow-hidden rounded-lg bg-white shadow-lg flex flex-col h-full slide-up">
                <div class="p-4 flex flex-col flex-grow">
                    <!-- Header -->

                    <div class="mb-4 flex items-end">
                        <div class="ml-4 flex-1">
                            <h4 class="mb-1 text-lg font-semibold text-gray-800 capitalize">{{ $abono->nombre }}</h4>
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="mr-2 rounded text-sm font-medium text-blue-700">{{ $abono->productos->first()->cantidad_actual }}
                                    disponibles</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 h-2 w-3/4 rounded-full bg-violet-400"></div>

                    <!-- Contenido que debe crecer -->
                    <div class="flex-grow">
                        <p class="text-gray-600 mb-2">
                            {!! nl2br(e($abono->descripcion ?? 'Asegura tu lugar en cada jornada y vive la emoción del deporte como un verdadero fan. ¡Este abono es tu pase directo a la pasión en cada partido!')) !!}
                        </p>
                    </div>

                    <!-- Precio y botón alineados abajo -->
                    <div class="mt-auto">
                        @forelse ($abono->productos as $producto)
                            <div class="flex justify-between mb-3">
                                <span class="font-semibold">Número de entradas:
                                    <span>{{ $abono->numero_entradas }}</span></span>
                                <span class="font-semibold text-yellow-600">$<span> {{ $abono->costo_total }} </span></span>
                            </div>
                        @empty
                        @endforelse

                        @php
                            $abonoData = [
                                'id' => $abono->id,
                                'nombre' => $abono->nombre,
                                'nombre_factura' => ($abono->productos->first()?->nombre ?? '') . " - " . ($abono->nombre ?? ''),
                                'descripcion' => $abono->descripcion ?? 'Asegura tu lugar en cada jornada y vive la emoción del deporte como un verdadero fan. ¡Este abono es tu pase directo a la pasión en cada partido!',
                                'numero_entradas' => $abono->numero_entradas,
                                'stock' => $abono->productos->first()?->cantidad_actual ?? 0,
                                'precio' => $abono->costo_total,
                                'precio_sin_iva' => $abono->costo_sin_iva,
                                'prod_id' => $abono->productos->first()?->id ?? 0,
                                'total' => 0,
                                'total_sin_iva' => 0,
                                'cantidad' => 0,
                                'impuesto' => $abono->productos->first()?->impuesto ?? 0.15,
                                'prices' => $abono->productos->map(fn($p) => [
                                    'id' => $p->id,
                                    'nombre' => $p->nombre,
                                    'precio_venta_final' => $abono->costo_total,
                                    'precio_sin_iva' => $abono->costo_sin_iva,
                                    'cantidad' => 0
                                ])
                            ];
                        @endphp

                        <button @click="selectAbono({{ json_encode($abonoData) }})"
                            class="w-full rounded-lg font-bold bg-slate-900 px-4 py-2 text-yellow-400 hover:bg-slate-700">
                            Comprar abono
                        </button>
                    </div>
                </div>
            </div>
        @empty

        @endforelse
    </div>
</section>