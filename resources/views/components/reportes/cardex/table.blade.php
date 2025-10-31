<div class="space-y-5 sm:space-y-6" x-data="cardexData()">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6 dark:border-gray-800">
            <!-- Contenedor superior: título, filtros y botón -->
            <div class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                
                <!-- Contenedor de 3 filas -->
                <div class="flex flex-col gap-5 px-6 mb-4">
                    <!-- Título -->
                    <div class="w-full">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Movimientos de stock
                        </h3>
                    </div>

                    <!-- Filtros de fechas + botón Buscar -->
                    <div class="w-full flex flex-col gap-5 sm:flex-row sm:items-center justify-center">
                        @csrf
                        <!-- Input Fecha Inicio -->
                        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4" x-data="datepickerInicio">
                            <div class="relative">
                                <input x-ref="input" type="text" x-model="fechaInicio" id = "fechaInicio" autocomplete="off" readonly
                                    class="font-sans block px-2 pb-2 pt-2.5 w-full text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label
                                    class="absolute font-sans text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                                    Fecha Inicio
                                </label>
                            </div>
                        </div>
                        
                        <!-- Input Fecha Fin -->
                        <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4" x-data="datepickerFin">
                            <div class="relative">
                                <input x-ref="input" type="text" x-model="fechaFin" id = "fechaFin" autocomplete="off" readonly
                                    class="font-sans block px-2 pb-2 pt-2.5 w-full text-sm text-gray-900 bg-transparent rounded-xl border border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                <label
                                    class="absolute font-sans text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                                    Fecha Fin
                                </label>
                            </div>
                        </div>
                        
                        <!-- Botón Buscar -->
                        <div class="w-full sm:w-auto mt-2 sm:mt-0">
                            <button type="button" id = "btnBuscar"
                                class="px-6 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 flex items-center justify-center w-full sm:w-auto">
                                Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Botón Descargar Excel -->
                    <div class="w-full flex flex-col gap-3 sm:flex-row sm:items-center justify-end">
                        <button 
                            type="button" 
                            id="btnDescargarExcel"
                            class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-green-500 border border-green-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed">
                            Descargar Excel
                        </button>
                    </div>
                </div>

                <!-- Tabla Cardex -->
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tCardex" class="min-w-full">
                        <thead class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Producto</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Abono/Jornada</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Fecha y Hora</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tipo Movimiento</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Stock Anterior</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Cantidad</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Stock Actual</th>
                                <th class="px-6 py-3 whitespace-nowrap text-left text-sm font-medium text-gray-500 dark:text-gray-400">Descripcion</th>
                            </tr>
                        </thead>
                        <tbody id="tbCardex" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- Aquí se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- Fin Contenedor superior + tabla -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cardexData', () => ({
            fechaInicio: '',
            fechaFin: '',
            mostrarBotonDescargar: false 
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
