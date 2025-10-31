<!-- Modal flotante -->
<div x-show="showForm" x-cloak x-transition.opacity.duration.200ms x-on:keydown.esc.window="showForm = false; resetForm()"
    @notify.document="$event.detail.variant === 'success' ? (showForm = false, resetForm()) : null;"
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white" x-text="form.id ? 'Editar Jornada' : 'Registrar Jornada'"></h2>
        </div>

        <!-- Formulario -->
        <form id="jornada-form" class="p-6 space-y-4">
            @csrf

            <input id="id" x-model="form.id" type="text" class="hidden">

            <div class="w-full">
                <label for="nombre"
                    class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre</label>
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


            <div class="w-full">
                <label for="aforo" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Aforo</label>
                <input id="aforo" type="number" x-model="form.aforo"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
            </div>

            <label for="toggleSuccess" class="inline-flex items-center gap-3">
                <input id="toggleSuccess" type="checkbox" x-model="form.estado" class="peer sr-only" role="switch" />
                <span
                    class="trancking-wide text-sm font-medium text-on-surface peer-checked:text-on-surface-strong peer-disabled:cursor-not-allowed peer-disabled:opacity-70 dark:text-on-surface-dark dark:peer-checked:text-on-surface-dark-strong">Activo</span>
                <div class="relative h-6 w-11 after:h-5 after:w-5 peer-checked:after:translate-x-5 rounded-full border border-outline bg-surface-alt after:absolute after:bottom-0 after:left-[0.0625rem] after:top-0 after:my-auto after:rounded-full after:bg-on-surface after:transition-all after:content-[''] peer-checked:bg-success peer-checked:after:bg-on-success peer-focus:outline-2 peer-focus:outline-offset-2 peer-focus:outline-outline-strong peer-focus:peer-checked:outline-success peer-active:outline-offset-0 peer-disabled:cursor-not-allowed peer-disabled:opacity-70 dark:border-outline-dark dark:bg-surface-dark-alt dark:after:bg-on-surface-dark dark:peer-checked:bg-success dark:peer-checked:after:bg-on-success dark:peer-focus:outline-outline-dark-strong dark:peer-focus:peer-checked:outline-success"
                    aria-hidden="true"></div>
            </label>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button x-on:click="showForm = false; resetForm()" type="button"
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
                                timepicker: true,
                                timeFormat: 'HH:mm', 
                                minutesStep: 5,
                                autoClose: false,
                                minDate: new Date(),
                                locale: AirDatepickerEs,
                                buttons: ['clear'],
                                onSelect: ({ formattedDate, date }) => {
                                    this.form.fecha_inicio = formattedDate;
                                },
                                onShow: () => {
                                    this.form.fecha_inicio ? '' : this.datepickerInstance.clear()
                                },
                                onRenderCell: ({ date, cellType }) => {
                                    const now = new Date();
                                    if (cellType === 'time') {
                                        const selectedDate = new Date(date);
                                        // Bloquear solo horas del día actual que ya pasaron
                                        if (selectedDate.toDateString() === now.toDateString() &&
                                            selectedDate.getHours() < now.getHours()) {
                                            return { disabled: true };
                                        }
                                    }
                                }
                            });
                        });
                    },
                    setSelectedDates() {
                        if (!this.datepickerInstance) return;
                        if (this.form.fecha_inicio) {
                            this.datepickerInstance.selectDate(new Date(this.form.fecha_inicio));
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
                                timepicker: true,
                                timeFormat: 'HH:mm',             
                                autoClose: false,
                                minDate: new Date(),
                                locale: AirDatepickerEs,
                                buttons: ['clear'],
                                onSelect: ({ formattedDate }) => {
                                    this.form.fecha_fin = formattedDate;
                                },
                                onShow: () => {
                                    this.form.fecha_fin ? '' : this.datepickerInstance.clear()
                                },
                                onRenderCell: ({ date, cellType }) => {
                                const now = new Date();
                                if (cellType === 'time') {
                                    const selectedDate = new Date(date);
                                    // Bloquear solo horas del día actual que ya pasaron
                                    if (selectedDate.toDateString() === now.toDateString() &&
                                        selectedDate.getHours() < now.getHours()) {
                                        return { disabled: true };
                                    }
                                }
                            }
                            });
                        });
                    },
                    setSelectedDates() {
                        if (!this.datepickerInstance) return;
                        if (this.form.fecha_fin) {
                            this.datepickerInstance.selectDate(new Date(this.form.fecha_fin));
                        }
                    }
                }));
            });
        </script>
    </div>
</div>
