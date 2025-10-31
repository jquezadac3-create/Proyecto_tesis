<!-- Modal actualizar stock -->
<div x-show="showStockModal" 
     x-cloak  
     x-transition.opacity.duration.200ms 
     x-on:keydown.esc.window="showStockModal = false; resetStockForm()"
     @notify.document="$event.detail.variant === 'success' ? (showStockModal = false, resetStockForm()) : null;"
     class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md mx-auto overflow-y-auto max-h-[80vh]">
        
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Actualizar Stock</h2>
        </div>

        <!-- Formulario -->
        <form id="formStock" class="p-6 space-y-4">
            @csrf
            <!-- Input oculto para id del producto -->
            <input type="hidden" 
                   id="idProductoStock" 
                   x-model="stockForm.idProducto"
                   value="">

             <!-- Mostrar stock actual -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                    Stock Actual
                </label>
                <input type="text"
                       x-model="stockForm.stockActual"
                       disabled
                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700">
            </div>

            <!-- Input para cantidad de stock -->
            <label class="block text-sm text-gray-700 dark:text-white mb-1">
                Cantidad (ingrese un número positivo para aumentar stock, negativo para disminuir)
            </label>

            <!-- Contenedor centrado para el input con botones -->
            <div class="w-full flex justify-center mt-2">
                <div class="w-1/2"> <!-- Ajusta ancho del input y botones -->
                    <div class="flex items-center gap-1">
                        <!-- Botón restar -->
                        <button 
                            type="button"
                            x-on:click="currentVal = Math.max(minVal, currentVal - incrementAmount); stockForm.cantidad = currentVal"
                            class="flex h-10 items-center justify-center rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 px-4 py-2 text-gray-800 dark:text-white hover:opacity-75"
                        >
                            <i class="fa-solid fa-minus"></i>
                        </button>

                        <!-- Input editable -->
                        <input 
                            type="number"
                            id="cantidadStock"
                            name="cantidadStock"
                            x-model.number="currentVal"
                            x-on:input="stockForm.cantidad = currentVal"
                            class="h-10 w-full text-center border-t border-b border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-white rounded-none p-2 focus:outline-none"
                        />

                        <!-- Botón sumar -->
                        <button 
                            type="button"
                            x-on:click="currentVal = Math.min(maxVal, currentVal + incrementAmount); stockForm.cantidad = currentVal"
                            class="flex h-10 items-center justify-center rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 px-4 py-2 text-gray-800 dark:text-white hover:opacity-75"
                        >
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Input para motivo / razón -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                    Motivo / Razón
                </label>
                <textarea
                    id="motivoStock"
                    name="motivoStock"
                    x-model="stockForm.motivo"
                    placeholder="Ingrese el motivo por el cual se agrega stock"
                    rows="3"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"></textarea>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button type="button"
                        x-on:click="resetStockForm(); showStockModal = false"
                        class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 
                               text-gray-800 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 
                               text-white w-full sm:w-auto">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
