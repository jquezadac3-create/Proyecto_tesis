<div class="space-y-5 sm:space-y-6" x-data="movimientoCajaData()">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6 dark:border-gray-800">

            <!-- Filtros superiores -->
            <div class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-5 px-6 mb-4">
                    <!-- Título -->
                    <div class="w-full">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Movimientos de Caja
                        </h3>
                    </div>

                    <!-- Filtros -->
                    <div class="w-full flex flex-col gap-5 sm:flex-row sm:items-center justify-center">
                        @csrf

                        <!-- Fecha Inicio -->
                        <div class="w-full sm:w-1/3" x-data="datepickerInicio">
                            <div class="relative">
                                <input x-ref="input" type="text" x-model="fechaInicio" id="fechaInicio" autocomplete="off" readonly
                                    class="font-sans block px-2 pb-2 pt-2.5 w-full text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label
                                    class="absolute text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2">
                                    Fecha Inicio
                                </label>
                            </div>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="w-full sm:w-1/3" x-data="datepickerFin">
                            <div class="relative">
                                <input x-ref="input" type="text" x-model="fechaFin" id="fechaFin" autocomplete="off" readonly
                                    class="font-sans block px-2 pb-2 pt-2.5 w-full text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label
                                    class="absolute text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2">
                                    Fecha Fin
                                </label>
                            </div>
                        </div>

                        <!-- Forma de Pago -->
                        <div class="w-full sm:w-1/3">
                            <div class="relative">
                                <select id="selectFormaPago"
                                    class="peer block w-full px-2 pb-2 pt-3 text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-blue-600">
                                    <option value="">Todas las formas de pago</option>
                                    <!-- Aquí se llenará dinámicamente -->
                                </select>
                                <label
                                    class="absolute text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:-translate-y-4 peer-focus:scale-75 peer-focus:text-blue-600 peer-focus:dark:text-blue-500">
                                    Forma de Pago
                                </label>
                            </div>
                        </div>

                        <!-- Botón Buscar -->
                        <div class="w-full sm:w-auto">
                            <button type="button" id="btnBuscar"
                                class="px-6 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 flex items-center justify-center w-full sm:w-auto">
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Totales por Forma de Pago -->
                <div id="totalesFormaPago" class="px-6 mb-6 hidden">
                    <!-- Título mejorado -->
                    <div class="w-full mb-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Total Ventas
                        </h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- Tarjeta de Débito -->
                        <div id="tarjetaDebito" class="hidden relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-gray-200 dark:bg-white/[0.05] dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-2">
                                    <!-- Texto: 3/4 -->
                                    <div class="flex-none w-3/4 max-w-full px-2">
                                        <div class="break-words">
                                            <p class="mb-1 font-sans font-bold leading-normal uppercase text-xs 
                                                    text-transparent bg-gradient-to-tl from-pink-500 to-rose-500 bg-clip-text">
                                                Tarjeta de Débito
                                            </p>
                                            <h5 class="mb-1 font-bold text-lg dark:text-white">
                                                <span class="text-transparent bg-gradient-to-tl from-pink-500 to-rose-500 bg-clip-text">$</span>
                                                <span id="totalTarjetaDebito" class="text-transparent bg-gradient-to-tl from-pink-500 to-rose-500 bg-clip-text">0.00</span>
                                            </h5>
                                        </div>
                                    </div>

                                    <!-- Icono: 1/4 -->
                                    <div class="px-2 text-right w-1/4 flex justify-end items-center">
                                        <div class="inline-block w-10 h-10 text-center rounded-lg bg-gradient-to-tl from-pink-500 to-rose-500">
                                            <i class="fa-solid fa-credit-card text-sm relative top-2.5 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Tarjeta de Crédito -->
                        <div id="tarjetaCredito" class="hidden relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-gray-200 dark:bg-white/[0.05] dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-2">
                                    <!-- Texto: 3/4 -->
                                    <div class="flex-none w-3/4 max-w-full px-2">
                                        <div class="break-words">
                                            <p class="mb-1 font-sans font-bold leading-normal uppercase text-xs 
                                                    text-transparent bg-gradient-to-tl from-purple-600 to-indigo-600 bg-clip-text">
                                                Tarjeta de Crédito
                                            </p>
                                            <h5 class="mb-1 font-bold text-lg dark:text-white">
                                                <span class="text-transparent bg-gradient-to-tl from-purple-500 to-indigo-500 bg-clip-text">$</span>
                                                <span id="totalTarjetaCredito" class="text-transparent bg-gradient-to-tl from-purple-500 to-indigo-500 bg-clip-text">0.00</span>
                                            </h5>
                                        </div>
                                    </div>

                                    <!-- Icono: 1/4 -->
                                    <div class="px-2 text-right w-1/4 flex justify-end items-center">
                                        <div class="inline-block w-10 h-10 text-center rounded-lg bg-gradient-to-tl from-purple-500 to-indigo-500">
                                            <i class="fa-solid fa-credit-card text-sm relative top-2.5 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <!-- Otros con utilización del sistema financiero -->
                        <div id="tarjetaOtrosFinanciero" class="hidden relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-gray-200 dark:bg-white/[0.05] dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-2">
                                    <!-- Texto: ahora ocupa 3/4 -->
                                    <div class="flex-none w-3/4 max-w-full px-2">
                                        <div class="break-words">
                                            <p class="mb-1 font-sans font-bold leading-normal uppercase text-xs 
                                                    text-transparent bg-gradient-to-tl from-blue-600 to-sky-600 bg-clip-text">
                                                Otros con utilización del sistema financiero
                                            </p>
                                            <h5 class="mb-1 font-bold text-lg dark:text-white">
                                                <span class="text-transparent bg-gradient-to-tl from-blue-500 to-sky-500 bg-clip-text">$</span>
                                                <span id="totalOtrosFinanciero" class="text-transparent bg-gradient-to-tl from-blue-500 to-sky-500 bg-clip-text">0.00</span>
                                            </h5>
                                        </div>
                                    </div>

                                    <!-- Icono: ahora ocupa 1/4 -->
                                    <div class="px-2 text-right w-1/4 flex justify-end items-center">
                                        <div class="inline-block w-10 h-10 text-center rounded-lg bg-gradient-to-tl from-blue-500 to-sky-500">
                                            <i class="fa-solid fa-dollar-sign text-sm relative top-2.5 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sin utilización del sistema financiero -->
                        <div id="tarjetaSinFinanciero" class=" hidden relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-gray-200 dark:bg-white/[0.05] dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-300 hover:shadow-xl">
                            <div class="flex-auto p-4">
                                <div class="flex flex-row -mx-2">
                                    <div class="flex-none w-3/4 max-w-full px-2">
                                        <div class="break-words">
                                             <p class=" mb-1 font-sans font-bold leading-normal uppercase text-xs 
                                                        text-transparent bg-gradient-to-tl from-emerald-600 to-green-600 bg-clip-text">
                                                Sin utilización del sistema financiero
                                            </p>
                                            <h5 class="mb-1 font-bold text-lg dark:text-white">
                                                <span class="text-transparent bg-gradient-to-tl from-emerald-500 to-green-500 bg-clip-text">$</span>
                                                <span id="totalSinFinanciero" class="text-transparent bg-gradient-to-tl from-emerald-500 to-green-500 bg-clip-text">0.00</span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="px-2 text-right w-1/4 flex justify-end items-center">
                                        <div class="inline-block w-10 h-10 text-center rounded-lg bg-gradient-to-tl from-emerald-500 to-green-500">
                                            <i class="fa-solid fa-money-bill text-sm relative top-2.5 text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <!-- Tabla -->
                    <table id="tMovCaja" class="min-w-full">
                        <thead class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Valor</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Detalle</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Forma de Pago</th>
                            </tr>
                        </thead>
                        <tbody id="tbMovCaja" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- dinámico -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('movimientoCajaData', () => ({
            fechaInicio: '',
            fechaFin: '',
            formaPago: '',
            formasPago: [],
        }));

        // Datepicker Inicio
        Alpine.data('datepickerInicio', () => ({
            datepickerInstance: null,
            init() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.datepickerInstance = new AirDatepicker(this.$refs.input, {
                        autoClose: true,
                        locale: AirDatepickerEs,
                        buttons: ['clear'],
                        maxDate: new Date(),
                        onSelect: ({ formattedDate }) => {
                            this.$root.fechaInicio = formattedDate;
                        },
                        onShow: () => {
                            if (!this.$root.fechaInicio) this.datepickerInstance.clear();
                        }
                    });
                });
            }
        }));

        // Datepicker Fin
        Alpine.data('datepickerFin', () => ({
            datepickerInstance: null,
            init() {
                document.addEventListener('DOMContentLoaded', () => {
                    this.datepickerInstance = new AirDatepicker(this.$refs.input, {
                        autoClose: true,
                        locale: AirDatepickerEs,
                        buttons: ['clear'],
                        maxDate: new Date(),
                        onSelect: ({ formattedDate }) => {
                            this.$root.fechaFin = formattedDate;
                        },
                        onShow: () => {
                            if (!this.$root.fechaFin) this.datepickerInstance.clear();
                        }
                    });
                });

            }
        }));
    });
</script>