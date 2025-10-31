<!-- PARTIDOS DISPONIBLES -->
<section id="partidos" class="container mx-auto py-10 px-4">
    <h3 class="mb-6 text-2xl font-bold">🎟️ Jornadas Disponibles</h3>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($jornadas as $items)
            @php
                $jornada = $items->first()->jornada;
            @endphp
            <div class="overflow-hidden rounded-lg bg-white shadow-lg slide-up flex flex-col h-full">
                <div class="p-6 flex flex-col flex-grow">
                    <div class="mb-4 flex items-end">
                        <div class="h-16 w-16 flex-shrink-0 rounded-lg day-badge text-center"></div>
                        <div class="ml-4 flex-1">
                            <h4 class="mb-1 text-lg font-semibold text-gray-800 capitalize">{{ $jornada->nombre }}</h4>
                            <div class="flex items-center text-sm text-gray-500">
                                <span
                                    class="mr-2 rounded day-badge px-2 py-1 text-xs font-medium text-blue-700">{{ date_create($jornada->fecha_inicio)->format('l') }}</span>
                                <span>{{ date_create($jornada->fecha_inicio)->format('d M, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow">
                        <div class="mb-4">
                            <div class="mb-3 h-2 w-3/4 rounded-full bg-yellow-400"></div>
                            <p class="text-sm text-gray-600">{{ date_create($jornada->fecha_inicio)->format('H:i') }} -
                                {{ date_create($jornada->fecha_fin)->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="mb-6 space-y-2">
                            <div class="space-y-1">
                                @foreach ($items as $precio)
                                    @php
                                        $price = round($precio->producto->precio_venta_final * 1.1, 2);  
                                        $id = "tooltip-jornada-{$loop->index}";
                                    @endphp

                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 capitalize">{{ $precio->producto->nombre }}</span>
                                        <span class="font-semibold text-yellow-600">${{ $price }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button @click="selectJornada(
                                        {
                                            id: {{ $jornada->id }}, nombre: '{{ $jornada->nombre }}',
                                            fecha_inicio: '{{ date_create($jornada->fecha_inicio)->format('d M, Y H:i') }}',
                                            fecha_fin: '{{ $jornada->fecha_fin }}',
                                            cantidad_aforo: '{{ $jornada->cantidad_aforo }}',
                                            estado: '{{ $jornada->estado }}',
                                            total: 0,
                                            prices: [
                                                @foreach ($items as $precio)
                                                    { id: {{ $precio->producto->id }}, nombre: '{{ $precio->producto->nombre }}', nombre_factura: '{{ $precio->producto->nombre }} - {{ $jornada->nombre }}',
                                                    precio_venta_final: {{ round($precio->producto->precio_venta_final * 1.1, 2) }}, precio_sin_iva: {{ round($precio->producto->precio_venta_sin_iva * 1.1, 4) }}, stock: {{ $precio->stock_actual }}, cantidad: 0, impuesto: {{ $precio->producto->impuesto }} },
                                                @endforeach
                                            ]
                                        })"
                            class="w-full rounded-lg font-bold bg-slate-900 px-4 py-2 text-yellow-400 hover:bg-slate-700">
                            Comprar entradas
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center col-span-3">
                <p class="text-center text-gray-500">No hay jornadas disponibles en este momento. Por favor, vuelva más
                    tarde.</p>
            </div>
        @endforelse
    </div>
</section>