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
                x-text="form.id ? 'Editar Cliente' : 'Registrar Cliente'"></h2>
        </div>

        <!-- Formulario -->
        <form id="usuario-form" class="p-6 space-y-4">
            @csrf

            <input name="id" type="text" x-model="form.id" hidden class="hidden">

            <!-- Primera fila: Nombre -->
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre *</label>
                <input type="text" x-model="form.name" name="name" id="name" required x-on:input="errors.name = ''"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                    placeholder="Ingrese el nombre del usuario">
                <template x-if="errors.name">
                    <p class="mt-1 text-sm text-red-600" x-text="errors.name"></p>
                </template>
            </div>

            <!-- Segunda fila: Número de entradas y Costo total -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Correo electrónico
                        *</label>
                    <input type="email" x-model="form.email" name="email" id="email" required x-on:input="errors.email = ''"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                        placeholder="Ej: usuario@ejemplo.com">
                    <template x-if="errors.email">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
                    </template>
                </div>

                <div class="w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1"
                        x-text="form.id ? 'Editar Contraseña' : 'Registrar Contraseña'"></label>
                    <div x-data="{ showPassword: false }" class="relative">
                        <input :type="showPassword ? 'text' : 'password'" placeholder="Ingresa tu Contraseña"
                            id="password" name="password"
                            x-model="form.password"
                            x-on:input="errors.password = ''"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white"
                            :required="!form.id" />
                        <span @click="showPassword = !showPassword"
                            class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                            <i x-show="!showPassword" class="fa-regular fa-eye"></i>
                            <i x-show="showPassword" class="fa-regular fa-eye-slash"></i>
                        </span>
                    </div>
                    <template x-if="errors.password">
                        <p class="mt-1 text-sm text-red-600" x-text="errors.password"></p>
                    </template>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                <button x-on:click="showForm = false; resetForm()" type="reset"
                    class="px-4 py-2 text-sm rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white hover:bg-gray-400 dark:hover:bg-gray-600 w-full sm:w-auto">
                    Cancelar
                </button>
                <button type="button" @click="sendForm"
                    class="px-4 py-2 text-sm rounded bg-blue-600 hover:bg-blue-700 text-white w-full sm:w-auto">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>