<!-- Modal flotante -->
<div x-show="showBoletoModal" 
     x-cloak  
     x-transition.opacity.duration.200ms 
     x-on:keydown.esc.window="showBoletoModal = false; 
     resetBoletoForm()"
     @notify.document="$event.detail.variant === 'success' ? (showBoletoModal = false,  resetBoletoForm()) : null;"

     class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
     
    <div
        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-md mx-auto overflow-y-auto max-h-[80vh]">
        
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Asignar Boletos</h2>
        </div>

        <!-- Formulario -->
        <form id="formBoleto" class="p-6 space-y-4">
            @csrf
            <input type="hidden" 
                id="idProductoBoleto" 
                x-model="boletoForm.idProducto"
                value="">
            <!-- Select Jornada / Grupo -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                    Seleccione Jornada o Grupo
                </label>
                <select id="jornadaSelect" 
                        name="jornada" 
                        x-model="boletoForm.jornada"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm 
                            dark:bg-gray-800 dark:text-white">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <template x-for="j in jornadas" :key="j.id">
                        <option :value="j.id" x-text="j.nombre"></option>
                    </template>
                </select>
            </div>

            <!-- Cantidad de boletos -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">
                    Ingrese la cantidad de boletos
                </label>
                <input  type="number" 
                        id="cantidadBoletos" 
                        name="cantidadBoletos" 
                        x-model="boletoForm.cantidad"
                        x-on:change="
                           const jornada = jornadas.find(j => j.id == boletoForm.jornada);
                            if (!jornada) {
                                toast.show('error', 'Error', 'Debe seleccionar una jornada primero');
                                boletoForm.cantidad = '';
                                return;
                            }
                            const cantidad = parseInt(boletoForm.cantidad) || 0;
                            const aforo = parseInt(jornada.aforo_restante) || 0;
                            if (cantidad > aforo) {
                                toast.show('error', 'Error', `El aforo máximo disponible es ${aforo}`);
                                boletoForm.cantidad = aforo;
                            }
                        "
                        min="1"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm 
                               dark:bg-gray-800 dark:text-white">
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button 
                    type="button"
                    x-on:click="resetBoletoForm(); showBoletoModal = false"
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