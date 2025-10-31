<!-- Modal flotante -->
<div    x-show="showForm" 
        x-cloak 
        x-transition.opacity.duration.200ms 
        x-on:keydown.esc.window="showForm = false; resetForm()"
        @notify.document="$event.detail.variant === 'success' ? (showForm = false, resetForm()) : null;"
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    <div
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Registrar Producto</h2>
        </div>

        <!-- Formulario -->
        <form id="formProductos" class="p-6 space-y-4">
            @csrf
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- id del producto --}}
                <input id="id" x-model="form.id" type="text" class="hidden">
                <!-- Nombre del producto -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre</label>
                    <input type="text" id="nombre" name="nombre" x-model="form.nombre"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                </div>

                <!-- Categoría -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Categoría</label>
                    <select id="categoriaSelect" 
                            name="categoria" 
                            x-model="form.categoria"
                            x-on:change="
                                form.categoriaTexto = $event.target.options[$event.target.selectedIndex].text;
                                if(form.categoriaTexto === 'Abonos') {
                                    form.esAbono = true;
                                    form.abonoLocked = true; // bloqueado
                                } else {
                                    form.esAbono = false;
                                    form.abonoLocked = true; // bloqueado también
                                }

                                $nextTick(() => {
                                    document.getElementById('esAbono').dispatchEvent(new Event('change'));
                                });
                            
                            "
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                        <option value="" disabled>Seleccione una categoría</option>
                    </select>
                </div>

                <input type="hidden" x-model="form.categoriaTexto" id="categoriaTexto">
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Costo -->
                <div class="w-full sm:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Costo</label>
                    <input 
                        type="number" 
                        id="costo" 
                        name="costo"
                        step="0.0001" 
                        x-model="form.costo"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                </div>

                <!-- Precios -->
                <div class="w-full sm:w-1/2 flex gap-4">
                    <!-- PVP sin IVA -->
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">PVP sin IVA</label>
                        <input 
                            type="number" 
                            id="precioSinIva" 
                            name="precioSinIva"
                            step="0.0001"
                            x-model="form.precioSinIva"
                            x-on:blur="calculateTax()"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                         <input 
                            type="hidden" 
                            name="precioSinIvaFormat" 
                            :value="form.precioSinIvaFormat"
                            id="precioSinIvaFormat" 
                            >
                    </div>
                    
                    <!-- PVP con IVA -->
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Precio Final</label>
                        <input 
                            type="number" 
                            id="precioConIva" 
                            name="precioConIva" 
                            step="0.0001"
                            x-model="form.precioConIva"
                            readonly
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                        <input 
                            type="hidden" 
                            name="precioConIvaFormat" 
                            id="precioConIvaFormat" 
                            :value="form.precioConIvaFormat">
                    </div>
                </div>
            </div>

            <!-- Categoría -->
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Impuesto -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Impuesto</label>
                    <select id="impuesto" name="impuesto" x-model="form.impuesto" x-on:change="calculateTax()"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                        <option value="0">Tarifa 0</option>
                        <option value="0.15">Tarifa 15%</option>
                    </select>
                </div>
                <!-- Cantidad -->
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Cantidad</label>
                    <input  type="number" 
                            id="cantidad" 
                            name="cantidad" 
                            x-model="form.cantidad"
                            x-effect="if (form.categoriaTexto === 'Ticket') { form.cantidad = '' }"
                            :disabled = "form.categoriaTexto === 'Ticket'"
                            :class="form.categoriaTexto === 'Ticket' 
                            ? 'cursor-not-allowed bg-gray-200 text-gray-500' 
                            : 'cursor-text bg-white text-black'"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                </div>
                
            </div>

            <!-- Sección de switches -->
            <div class="space-y-4">
                <!-- Switch desglosar IVA -->
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <label for="desglosarIva" class="text-sm font-medium text-gray-700 dark:text-white">
                            Modo de cálculo del IVA
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" 
                           x-text="form.desglosarIva ? 
                            'Modo Activo: Esto sirve para agregar el IVA al valor ingresado al PVP' : 
                            'Modo Inactivo: Esto sirve para desglozar el IVA del valor ingresado'"></p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="desglosarIva" name="desglosarIva" class="sr-only peer" x-model="form.desglosarIva" x-on:change="calculateTax()">
                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:bg-gray-700 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                        <div class="absolute left-0.5 top-0.5 h-5 w-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-5 transition-all"></div>
                    </label>
                </div>

                <!-- Switch abono -->
                <div 
                    class="flex flex-col gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg"
                    x-data="{ abonoLocked: false }"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="esAbono" class="text-sm font-medium text-gray-700 dark:text-white">
                                ¿Es un abono?
                            </label>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input  type="checkbox" id="esAbono" name="esAbono" class="sr-only peer" 
                                    x-model="form.esAbono"
                                    :disabled="form.abonoLocked"
                                    >
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 dark:bg-gray-700 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                            <div class="absolute left-0.5 top-0.5 h-5 w-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-5 transition-all"></div>
                        </label>
                    </div>
                    
                    <!-- Select abonos -->
                    <div x-show="form.esAbono" x-transition class="mt-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Seleccionar abono</label>
                        <select id="abonoSelect" name="abonoId" x-model="form.abonoId" 
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                            <option value="" disabled>Seleccione un abono</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button 
                    x-on:click="
                        resetForm();
                        showForm = false
                        " 
                    type="button"
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
</div>
