@extends('layout.main-layout')

@section('title', 'Detalle de Factura')

@section('assets')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('breadcrumb')
    <div>

        <body :class="{ 'overflow-hidden': showForm }">

            <div>
                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

                    <div x-data="{ pageName: `Detalle Factura` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">
                                Detalle de Factura
                            </h2>
                            <nav>
                                <ol class="flex items-center gap-1.5">
                                    <li>
                                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                            href="{{ route('sales.bills') }}">
                                            Listado
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </li>
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Detalle de
                                        Factura
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <div x-data="init()">
                        <!-- Invoice Mainbox Start -->
                        <div
                            class="w-full rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                            <div
                                class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                                <h3 class="text-theme-xl font-medium text-gray-800 dark:text-white/90">
                                    Factura
                                </h3>

                                <div>
                                    <h4 class="text-base font-medium text-gray-700 dark:text-gray-400">
                                        Secuencia : #{{ str_pad($factura->secuencia_factura, 9, '0', STR_PAD_LEFT) }}
                                    </h4>
                                    @if ($factura->status === 'anulada')
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                                            <i class="fa-solid fa-ban mr-2"></i>
                                            Anulada
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5 xl:p-8">
                                <div class="mb-9 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Datos del Cliente
                                        </span>

                                        <h5
                                            class="mb-2 text-base font-semibold text-gray-800 dark:text-white/90 capitalize">
                                            {{ $factura->cliente->nombres ?? '' }} {{ $factura->cliente->apellidos ?? '' }}
                                        </h5>

                                        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $factura->cliente->email ?? 'Sin correo registrado' }} <br>
                                            {{ $factura->cliente->direccion ?? 'Azogues | S/N' }}
                                        </p>

                                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Realizada:
                                        </span>

                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            {{ $factura->fecha ?? '' }}
                                        </span>
                                    </div>

                                    <div class="h-px w-full bg-gray-200 sm:h-[158px] sm:w-px dark:bg-gray-800"></div>

                                    <div class="sm:text-right">
                                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Datos de Facturación
                                        </span>

                                        <h5 class="mb-2 text-base font-semibold text-gray-800 dark:text-white/90">
                                            {{ $config->nombre_comercial }}
                                        </h5>

                                        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                                            Obligado a llevar contabilidad: {{ $config->obligado_contabilidad }} <br>
                                            Ruc: {{ $config->ruc }}
                                        </p>

                                        <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Dirección Matriz:
                                        </span>

                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            {{ $config->direccion_matriz }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Invoice Table Start -->
                                <div>
                                    <div
                                        class="overflow-x-auto rounded-xl custom-scrollbar border border-gray-100 dark:border-gray-800">
                                        <table class="min-w-full text-left text-gray-700 dark:text-gray-400">
                                            <thead class="bg-gray-50 dark:bg-gray-900">
                                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                                    <th
                                                        class="px-5 py-3 text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                        Código
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                        Descripción
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                        Cantidad
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                        Precio Unitario
                                                    </th>
                                                    <th
                                                        class="px-5 py-3 text-right text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">
                                                        Total
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                @forelse ($factura->detalles as $item)
                                                    <tr>
                                                        <td
                                                            class="px-5 py-3 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ $item->producto->id ?? '' }}
                                                        </td>
                                                        <td
                                                            class="px-5 py-3 text-sm font-medium whitespace-nowrap text-gray-800 dark:text-white/90">
                                                            {{ $item->producto->nombre }}
                                                        </td>
                                                        <td
                                                            class="px-5 py-3 text-center text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ $item->cantidad }}
                                                        </td>
                                                        <td
                                                            class="px-5 py-3 text-center text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ round($item->precio_unitario, 2) }}
                                                        </td>
                                                        <td
                                                            class="px-5 py-3 text-right text-sm text-gray-500 dark:text-gray-400">
                                                            {{ round($item->total, 2) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5"
                                                            class="px-5 py-3 text-center text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            No hay detalles para esta factura.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Invoice Table End -->

                                <div
                                    class="my-6 flex justify-end border-b border-gray-100 pb-6 text-right dark:border-gray-800">
                                    <div class="w-[220px]">
                                        <p class="mb-4 text-left text-sm font-medium text-gray-800 dark:text-white/90">
                                            Resumen del pedido
                                        </p>
                                        <ul class="space-y-2">
                                            <li class="flex justify-between gap-5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal
                                                    (15%):</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="subtotal15">${{ round($factura->subtotal15, 2) }}</span>
                                            </li>
                                            <li class="flex justify-between gap-5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal (5%):</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="subtotal5">${{ round($factura->subtotal5, 2) }}</span>
                                            </li>
                                            <li class="flex justify-between gap-5">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal (0%):</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="subtotal0">${{ round($factura->subtotal0, 2) }}</span>
                                            </li>
                                            <li class="flex justify-between gap-5 items-center">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Descuento:</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="descuento">${{ round($factura->descuento, 2) }}</span>
                                            </li>
                                            <li class="flex items-center justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">IVA (15%):</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="iva15">${{ round($factura->iva15, 2) }}</span>
                                            </li>
                                            <li class="flex items-center justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">IVA (5%):</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="iva5">${{ round($factura->iva5, 2) }}</span>
                                            </li>
                                            <li class="flex items-center justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">ICE:</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="ice">${{ round($factura->ice, 2) }}</span>
                                            </li>
                                            <li class="flex items-center justify-between">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">Adicional:</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                                    id="adicional">${{ round($factura->adicional, 2) }}</span>
                                            </li>
                                            <li class="flex items-center justify-between">
                                                <span class="font-medium text-gray-700 dark:text-gray-400">Total</span>
                                                <span class="text-lg font-semibold text-gray-800 dark:text-white/90"
                                                    id="total">${{ round($factura->total_factura, 2) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3">
                                    @if ($factura->status === 'valida')
                                        <button x-on:click="anular()"
                                            class="bg-red-500 shadow-theme-xs hover:bg-red-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
                                            <i class="fa-solid fa-ban"></i>
                                            Anular Factura
                                        </button>
                                    @endif

                                    @if (!$factura->facturaEstadoSri || ($factura->facturaEstadoSri->estado_autorizacion === 'PENDIENTE'))

                                        <button :disabled="sended" x-on:click="resendToSri()"
                                            class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                            Reenviar Factura al SRI
                                        </button>
                                    @else
                                        <button x-on:click="getPdfInvoice()"
                                            class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                            Ver PDF
                                        </button>
                                        @if (!$factura->cliente || !$factura->cliente->email)
                                            <script>
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    toast.show('warning', 'Alerta', 'El cliente no tiene un correo registrado, no se puede reenviar el correo.');
                                                });
                                            </script>
                                        @elseif ($factura->cliente && $factura->cliente->numero_identificacion === "9999999999999")
                                            <script>
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    toast.show('info', 'Alerta', 'La factura está registrada a CONSUMIDOR FINAL, no se puede reenviar el correo.');
                                                });
                                            </script>
                                        @else
                                            <button :disabled="sended" x-on:click="resendEmail()"
                                                title="Reenviar Factura al correo del cliente"
                                                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
                                                <i class="fa-solid fa-envelope"></i>
                                                Reenviar Factura
                                            </button>
                                            <button :disabled="qrsended" x-on:click="resendQrEmail()"
                                                title="Reenviar Código QR al correo del cliente"
                                                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
                                                <i class="fa-solid fa-qrcode"></i>
                                                Reenviar QR
                                            </button>
                                        @endif
                                    @endif

                                    <button x-on:click="print()"
                                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
                                        <i class="fa-solid fa-print"></i>
                                        Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Invoice Mainbox End -->
                    </div>
                </div>
            </div>
            <script>
                function init() {
                    return {
                        sended: false,
                        qrsended: false,
                        async print() {
                            let url = null;
                            let iframe = null;

                            try {
                                this.showSpinner();

                                const oldIframe = document.getElementById('factura-iframe');

                                if (oldIframe) {
                                    document.body.removeChild(oldIframe);
                                }

                                const response = await fetch("{{ route('facturas.facturaPdf', ['facturaId' => $factura->id], false) }}");
                                const blob = await response.blob();

                                url = window.URL.createObjectURL(blob);
                                iframe = document.createElement('iframe');
                                iframe.id = "factura-iframe";
                                iframe.style.display = 'none';
                                iframe.src = url;

                                document.body.appendChild(iframe);

                                iframe.onload = () => {
                                    iframe.contentWindow.focus();
                                    iframe.contentWindow.print();
                                    // document.body.removeChild(iframe);
                                    // window.URL.revokeObjectURL(url);
                                }
                            } catch (err) {
                                toast.show('error', 'Error al generar el PDF', 'Ocurrió un error inesperado');
                                console.error(err);
                                if (iframe) document.body.removeChild(iframe);
                                if (url) window.URL.revokeObjectURL(url);
                            } finally {
                                this.hideSpinner();
                            }
                        },
                        @if ($factura->status ===  'valida')
                            async anular() {
                                try {
                                    const result = await Swal.fire({
                                        title: '¿Estás seguro?',
                                        text: "Esta acción no se puede deshacer.",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Sí, anular',
                                        cancelButtonText: 'Cancelar'
                                    });

                                    if (!result.isConfirmed) {
                                        return;
                                    }

                                    this.showSpinner();

                                    const response = await fetch("{{ route('facturas.anular', ['facturaId' => $factura->id], false) }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    });

                                    const data = await response.json();

                                    if (!data.success) {
                                        toast.show('error', 'Error', data.message || 'Error al anular la factura');
                                        return;
                                    }

                                    toast.show('success', 'Éxito', data.message || 'Factura anulada correctamente');

                                    location.reload();
                                } catch (err) {
                                    toast.show('error', 'Error al anular la factura', 'Ocurrió un error inesperado');
                                    console.error(err);
                                } finally {
                                    this.hideSpinner();
                                }
                            },
                        @endif
                        @if (!$factura->facturaEstadoSri || ($factura->facturaEstadoSri->estado_autorizacion === 'PENDIENTE'))
                            async resendToSri() {
                                try {
                                    this.showSpinner();
                                    this.sended = true;

                                    const response = await fetch("{{ route('facturas.reenviarSri', ['facturaId' => $factura->id], false) }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    });

                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }

                                    const data = await response.json();

                                    if (!data.success) {
                                        this.sended = false;
                                        toast.show('error', 'Error', data.message || 'Error al reenviar la factura al SRI');
                                        return;
                                    }

                                    toast.show('success', 'Éxito', data.message || 'Factura reenviada al SRI correctamente');

                                    location.reload();
                                } catch (err) {
                                    toast.showToast('error', 'Error al reenviar la factura', 'Ocurrió un error inesperado');
                                    console.error(err);
                                } finally {
                                    this.hideSpinner();
                                }
                            },
                        @else
                                getPdfInvoice() {
                                    window.open("{{ route('facturas.obtenerPdfSri', ['facturaId' => $factura->id], false) }}", '_blank');
                                },
                                @if (!$factura->cliente || !$factura->cliente->email)
                                    // No se puede reenviar el correo, el cliente no tiene email
                                @elseif($factura->cliente && $factura->cliente->numero_identificacion === "9999999999999")
                                    // No se puede reenviar el correo, la factura está registrada a CONSUMIDOR FINAL
                                @elseif ($factura->cliente->email)
                                    async resendEmail() {
                                        this.sended = true;

                                        try {
                                            this.showSpinner();

                                            const response = await fetch("{{ route('facturas.reenviarSriEmail', ['facturaId' => $factura->id], false) }}", {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json'
                                                }
                                            });

                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }

                                            const data = await response.json();

                                            if (!data.success) {
                                                this.sended = false;
                                                toast.show('error', 'Error', data.message || 'Error al reenviar el correo');
                                                return;
                                            }

                                            toast.show('success', 'Éxito', data.message || 'Correo reenviado correctamente');
                                        } catch (err) {
                                            toast.show('error', 'Error al reenviar el correo', 'Ocurrió un error inesperado');
                                            console.error(err);
                                        } finally {
                                            this.hideSpinner();
                                        }
                                    },
                                    async resendQrEmail() {
                                        this.qrsended = true;

                                        try {
                                            this.showSpinner();

                                            const response = await fetch("{{ route('facturas.reenviarQrEmail', ['facturaId' => $factura->id], false) }}", {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json'
                                                }
                                            });

                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }

                                            const data = await response.json();

                                            if (!data.success) {
                                                this.qrsended = false;
                                                toast.show('error', 'Error', data.message || 'Error al reenviar el correo');
                                                return;
                                            }

                                            toast.show('success', 'Éxito', data.message || 'Correo reenviado correctamente');
                                        } catch (err) {
                                            toast.show('error', 'Error al reenviar el correo', 'Ocurrió un error inesperado');
                                            console.error(err);
                                        } finally {
                                            this.hideSpinner();
                                        }
                                    },
                                @endif
                            @endif
                    showSpinner() {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: true } }));
                    },
                    hideSpinner() {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                    }
                }
            }
            </script>
        </body>
    </div>
@endsection