<!-- Modal flotante -->
<div x-show="showForm" x-transition.opacity.duration.200ms x-cloak
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4"
    x-on:notify.document="$event.detail.variant === 'success' && showForm ? (showForm = false, resetForm()) : null;">

    <!-- Contenido del modal -->
    <div x-data="initData()"
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md sm:max-w-xl lg:max-w-2xl mx-auto max-h-[90vh] overflow-y-auto">

        <!-- Tu contenido aquí -->
        <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-blue-200">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Nuevo Cliente</h2>
        </div>

        <!-- Formulario -->
        <form id="client-form" @submit.prevent="submitForm" class="space-y-4 p-6">
            @csrf
            <input type="text" x-model="form.id" class="hidden">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Tipo Identificación -->
                <div class="relative flex w-full flex-col gap-1 text-gray-700 dark:text-gray-100">
                    <label for="os" class="pl-0.5 text-sm">Tipo Identificación</label>
                    <i
                        class="fas fa-chevron-down absolute right-4 top-9 pointer-events-none text-gray-500 dark:text-gray-400"></i>
                    <select id="os" name="os" x-model="form.tipo_identificacion"
                        @change="errors.tipo_identificacion = ''"
                        :class="errors.tipo_identificacion ? 'border-red-500' : 'border-gray-300'"
                        class="w-full appearance-none rounded-md border bg-white px-4 py-2 pr-10 text-sm text-gray-800 shadow-sm focus-visible:outline-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:ring-blue-500">
                        <option value="">Seleccionar el tipo</option>
                        <option :disabled="form.numero_identificacion.length > 10" value="cedula">Cédula</option>
                        <option :disabled="form.numero_identificacion.length > 13" value="ruc">RUC</option>
                        <option value="pasaporte">Pasaporte</option>
                    </select>
                    <span x-show="errors.tipo_identificacion" x-text="errors.tipo_identificacion"
                        class="text-xs text-red-500"></span>
                </div>

                <!-- Número de Identificación -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">N°
                        Identificación</label>
                    <input type="text" x-model="form.numero_identificacion" @input="validateIdentificacion()"
                        @blur="validateIdentificacion()"
                        :class="errors.numero_identificacion ? 'border-red-500' : 'border-gray-300'"
                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                    <span x-show="errors.numero_identificacion" x-text="errors.numero_identificacion"
                        class="text-xs text-red-500"></span>
                </div>
            </div>

            <!-- Nombres -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombres</label>
                    <input type="text" x-model="form.nombres" @input="errors.nombres = ''"
                        :class="errors.nombres ? 'border-red-500' : 'border-gray-300'"
                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                    <span x-show="errors.nombres" x-text="errors.nombres" class="text-xs text-red-500"></span>
                </div>

                <div class="w-full sm:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Apellidos</label>
                    <input type="text" x-model="form.apellidos" @input="errors.apellidos = ''"
                        :class="errors.apellidos ? 'border-red-500' : 'border-gray-300'"
                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                    <span x-show="errors.apellidos" x-text="errors.apellidos" class="text-xs text-red-500"></span>
                </div>
            </div>

            <!-- Resto de campos con validación similar... -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Dirección</label>
                <input type="text" x-model="form.direccion" @input="errors.direccion = ''"
                    :class="errors.direccion ? 'border-red-500' : 'border-gray-300'"
                    class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                <span x-show="errors.direccion" x-text="errors.direccion" class="text-xs text-red-500"></span>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-full sm:w-1/2">
                    <div class="w-full">
                        <label for="phone"
                            class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Teléfono</label>
                        <input id="phone" type="text" x-model="form.telefono" @input="errors.telefono = ''"
                            :class="errors.telefono ? 'border-red-500' : 'border-gray-300'"
                            class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                    </div>
                    <span id="phoneError" x-text="errors.telefono" class="text-xs text-red-500"></span>
                </div>

                <div class="w-full sm:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Correo
                        electrónico</label>
                    <input type="email" x-model="form.email" @input="errors.email = ''"
                        :class="errors.email ? 'border-red-500' : 'border-gray-300'"
                        class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                    <span x-show="errors.email" x-text="errors.email" class="text-xs text-red-500"></span>
                </div>
            </div>

            <!-- Abono y Entradas -->

            <template x-if="form.id">
                <details class="group">
                    <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="fa fa-chart-simple mr-2"></i>
                        Detalles de Abonos y Entradas
                        <i class="fa fa-chevron-down ml-auto transition-transform group-open:rotate-180"></i>
                    </summary>
                    <div class="flex gap-4">
                        <div class="bg-white shadow-sm border rounded-lg p-6 flex flex-col items-center">
                            <h2 class="text-gray-500 font-medium text-sm">Abono Adquirido</h2>
                            <p class="text-2xl font-bold" x-text="stats.tiene_abono ? 'Sí' : 'No'"></p>
                            <span class="text-gray-400 text-center text-xs mt-1">Cuenta con algún abono adquirido.</span>
                        </div>

                        <div class="bg-white shadow-sm border rounded-lg p-6 flex flex-col items-center">
                            <h2 class="text-gray-500 font-medium text-sm">Total Tickets Adquiridos</h2>
                            <p class="text-2xl font-bold" x-text="stats.entradas_normales"></p>
                            <span class="text-gray-400 text-center text-xs mt-1">Número total de tickets adquiridas.</span>
                        </div>

                        <div class="bg-white shadow-sm border rounded-lg p-6 flex flex-col items-center">
                            <h2 class="text-gray-500 font-medium text-sm">Total Abonos</h2>
                            <p class="text-2xl font-bold" x-text="`${stats.cantidad_usada} / ${stats.cantidad_total}`"></p>
                            <span class="text-gray-400 text-center text-xs mt-1">Total abonos usados.</span>
                        </div>
                    </div>
                </details>    
                <div hidden class="flex flex-col sm:flex-row gap-4" x-show="form.id" :class="form.id || 'hidden'">
                    <div class="flex items-center gap-3" x-show="form.id" :class="form.id || 'hidden select-none'">
                        <label for="abono" class="text-sm font-medium text-gray-700 dark:text-white">Cuenta con
                            Abono</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="abono" x-model="form.abono" class="sr-only peer" readonly>
                            <div
                                class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:bg-gray-700 rounded-full peer peer-checked:bg-blue-600 transition-all">
                            </div>
                            <div
                                class="absolute left-0.5 top-0.5 h-5 w-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-5 transition-all">
                            </div>
                        </label>
                    </div>

                    <div class="w-full" x-show="form.id" :class="form.id || 'hidden'">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">N° Total Entradas
                            Adquiridas</label>
                        <input type="number" x-model="form.entradas" @input="errors.entradas = ''" readonly
                            :class="errors.entradas ? 'border-red-500' : 'border-gray-300'"
                            class="w-full rounded-md border dark:border-gray-700 p-2 text-sm dark:bg-gray-800 dark:text-white focus-visible:outline-blue-500">
                        <span x-show="errors.entradas" x-text="errors.entradas" class="text-xs text-red-500"></span>
                    </div>
                </div>
            </template>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 mt-4">
                <button x-on:click="resetForm()" type="button"
                    class="px-4 py-2 text-sm rounded bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 w-full sm:w-auto">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto">Guardar</button>
            </div>
        </form>
    </div>
    <script>
        function initData() {
            return {
                validate() {
                    let nErrors = 0;

                    if (!this.form.nombres) (this.errors.nombres = "El campo nombre es obligatorio.", nErrors++);
                    if (!this.form.apellidos) (this.errors.apellidos = "El campo apellidos es obligatorio.", nErrors++);
                    if (!this.form.tipo_identificacion) (this.errors.tipo_identificacion = "El tipo de identificación es obligatorio.", nErrors++);
                    if (!this.form.numero_identificacion) (this.errors.numero_identificacion = "El número de identificación es obligatorio.", nErrors++);
                    if (!this.form.email) (this.errors.email = "El campo email es obligatorio.", nErrors++);

                    return nErrors;
                },
                validateIdentificacion() {
                    if (!this.form.numero_identificacion) {
                        this.errors.numero_identificacion = "El Número de identificación es obligatorio.";
                        return;
                    }

                    this.errors.numero_identificacion = "";

                    if (this.form.numero_identificacion.length === 10) {
                        this.form.tipo_identificacion = 'cedula';

                        return;
                    }

                    if (this.form.numero_identificacion.length === 13) {
                        this.form.tipo_identificacion = 'ruc';

                        return;
                    }

                    this.form.tipo_identificacion = '';
                },
                submitForm() {
                    const errors = this.validate();

                    if (errors > 0) {
                        toast.show('error', 'Error', 'Por favor corrija los errores antes de continuar.');
                        return;
                    }

                    document.dispatchEvent(new CustomEvent('save-client', {
                        detail: { form: this.form }
                    }));
                },
            }
        }
    </script>
</div>