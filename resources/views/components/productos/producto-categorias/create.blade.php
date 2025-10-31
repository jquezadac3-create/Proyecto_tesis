<!-- Modal flotante -->
<div x-show="showForm" x-cloak x-on:keydown.esc.window="showForm = false; resetForm()"
    x-on:click.self="modalIsOpen = false"
    @notify.document="$event.detail.variant === 'success' ? (showForm = false, resetForm()) : (showForm = true);"
    x-transition.opacity.duration.200ms
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white"
                x-text="form.id ? 'Editar Categoria' : 'Registrar Categoria'"></h2>
        </div>

        <!-- Formulario -->
        <form id="categoria-form" class="p-6 space-y-4">
            @csrf

            <input id="id" type="text" x-model="form.id" class="hidden">

            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre</label>
                <input type="text" x-model="form.nombre" name="nombre" id="nombre"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
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
</div>