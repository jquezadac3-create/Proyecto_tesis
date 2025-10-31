<!-- Modal flotante -->
<div x-show="showForm" x-cloak x-on:keydown.esc.window="showForm = false; resetForm()"
    x-on:click.self="modalIsOpen = false"
    @notify.document="$event.detail.variant === 'success' ? (showForm = false, resetForm()) : null;"
    x-transition:enter.opacity.duration.200ms
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white"
                x-text="form.id ? 'Editar Abono' : 'Registrar Abono'"></h2>
        </div>

        <!-- Formulario -->
        <form id="abono-form" class="p-6 space-y-4">
            @csrf

            <input id="id" type="text" x-model="form.id" class="hidden">

            <!-- Primera fila: Nombre -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre *</label>
                <input type="text" x-model="form.nombre" name="nombre" id="nombre" required
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                    placeholder="Ej: Abono inicial, Cuota mensual, etc.">
            </div>

            <!-- Segunda fila: Número de entradas y Costo total -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Número de Entradas *</label>
                    <input type="number" x-model="form.numero_entradas" name="numero_entradas" id="numero_entradas" 
                        min="1" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                        placeholder="Ej: 1, 12, 24">
                </div>

                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Costo Total *</label>
                    <input type="number" x-model="form.costo_total" name="costo_total" id="costo_total" 
                        step="0.01" min="0" required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                        placeholder="0.00">
                </div>
            </div>

            <!-- Tercera fila: Descripción -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Descripción</label>
                <textarea x-model="form.descripcion" name="descripcion" id="descripcion" rows="3"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                    placeholder="Detalles del plan de abono, términos y condiciones..."></textarea>
            </div>

            <!-- Estados y Configuración -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-3">Configuración del Abono</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Estado Activo -->
                    <div class="relative">
                        <div class="flex items-center justify-between p-4 rounded-lg border-2 transition-all duration-200"
                            :class="form.estado ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50'">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                        :class="form.estado ? 'bg-green-500' : 'bg-gray-400'">
                                        <i class="fas fa-check text-white text-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Estado Activo</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        <span x-show="form.estado">Abono habilitado</span>
                                        <span x-show="!form.estado">Abono deshabilitado</span>
                                    </p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.estado" name="estado" id="estado" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Mostrar en Web -->
                    <div class="relative">
                        <div class="flex items-center justify-between p-4 rounded-lg border-2 transition-all duration-200"
                            :class="form.mostrar_en_web ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50'">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-colors"
                                        :class="form.mostrar_en_web ? 'bg-blue-500' : 'bg-gray-400'">
                                        <i class="fas fa-globe text-white text-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Mostrar en Web</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        <span x-show="form.mostrar_en_web">Visible públicamente</span>
                                        <span x-show="!form.mostrar_en_web">Solo interno</span>
                                    </p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.mostrar_en_web" name="mostrar_en_web" id="mostrar_en_web" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button x-on:click="showForm = false; resetForm()" type="reset"
                    class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto">
                    Guardar
                </button>
            </div>
        </form>
    </div>
    <script>
        function init() {
            return {
                sendForm() {
                    
                }
            }
        }
    </script>
</div>