<!-- Modal flotante Periodo Campeonato -->
<div x-show="showFormPeriodo" x-on:keydown.esc.window="showFormPeriodo = false; resetFormPeriodo()"
     x-on:keydown.esc.window="showFormPeriodo = false; resetFormPeriodo()"
     @notify.document="$event.detail.variant === 'success' ? (showFormPeriodo = false, resetFormPeriodo()) : null;"
     x-cloak x-transition.opacity.duration.200ms 
     class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white"
                x-text="form.id ? 'Editar Periodo' : 'Registrar Periodo'"></h2>
        </div>

        <!-- Formulario -->
        <form id="periodo-form" class="p-6 space-y-4">
            @csrf

            <input id="id" x-model="form.id" type="text" class="hidden">

            <div class="w-full">
                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre</label>
                <input id="nombre" type="text" x-model="form.nombre"
                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
            </div>

            <!-- Contenedor de fechas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Fecha Inicio -->
                <div x-data="datepickerInicio" class="w-full">
                    <label for="fecha_inicio" 
                           class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Fecha Inicio</label>
                    <input x-ref="input" id="fecha_inicio" type="text" x-model="form.fecha_inicio" readonly
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                </div>

                <!-- Fecha Fin -->
                <div x-data="datepickerFin" class="w-full">
                    <label for="fecha_fin"
                           class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Fecha Fin</label>
                    <input x-ref="input" id="fecha_fin" type="text" x-model="form.fecha_fin" readonly
                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button type="button" @click="showFormPeriodo = false; resetFormPeriodo()"
                        class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto">
                    Guardar
                </button>
            </div>
        </form>

        <script>
            document.addEventListener('alpine:init', () => {
                // Datepicker Inicio
                Alpine.data('datepickerInicio', () => ({
                    datepickerInstance: null,
                    init() {
                        document.addEventListener('DOMContentLoaded', () => {
                            this.datepickerInstance = new AirDatepicker(this.$refs.input, {
                                dateFormat: 'yyyy-MM-dd',
                                autoClose: true,
                                locale: AirDatepickerEs,
                                buttons: ['clear'],
                                onSelect: ({ formattedDate, date }) => {
                                    this.form.fecha_inicio = formattedDate;;
                                },
                                onShow: () => {
                                    this.form.fecha_inicio ? '' : this.datepickerInstance.clear()
                                },
                            });
                        });
                    },
                    setSelectedDates() {
                        if (!this.datepickerInstance) return;
                        if (this.form.fecha_inicio) {
                            this.datepickerInstance.selectDate(new Date(form.fecha_inicio));
                        }
                    }
                }));

                // Datepicker Fin
                Alpine.data('datepickerFin', () => ({
                    datepickerInstance: null,
                    init() {
                        document.addEventListener('DOMContentLoaded', () => {
                            this.datepickerInstance = new AirDatepicker(this.$refs.input, {
                                dateFormat: 'yyyy-MM-dd',
                                autoClose: true,
                                locale: AirDatepickerEs,
                                buttons: ['clear'],
                                onSelect: ({ formattedDate, date}) => {
                                    this.form.fecha_fin = formattedDate;
                                },
                                onShow: () => {
                                    this.form.fecha_fin ? '' : this.datepickerInstance.clear()
                                },
                            });
                        });
                    },
                    setSelectedDates() {
                        if (!this.datepickerInstance) return;
                        if (this.form.fecha_fin) {
                            this.datepickerInstance.selectDate(new Date(form.fecha_fin));
                        }
                    }
                }));
            });
        </script>
    </div>
</div>