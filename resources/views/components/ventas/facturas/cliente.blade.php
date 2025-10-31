<!-- resources/views/components/ventas/factura/form.blade.php -->
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white">
            Datos del Cliente
        </h2>
    </div>
    <div class="px-4 pt-4 sm:p-6 dark:border-gray-800">
        <form id="datos-clientes-form-search" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <input type="hidden" id="customer-id" name="customer_id" value="">
                <div class="grid grid-cols-3 gap-3">
                    <!-- Contenedor relativo para input y dropdown -->
                    <div class="col-span-2 relative">
                        <!-- Input N° Identificación -->
                        <label for="factura-identificación"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            N° Identificación
                        </label>
                        <input type="text" id="factura-identificación"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            placeholder="WP3434434">

                        <!-- DropDown para mostrar los resultados -->
                        <div class="hidden text-left" id="dropdownContainerClientes">
                            <div id="dropdownClientes"
                                class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-900 rounded-lg shadow max-h-60 overflow-auto border border-indigo-500">
                                <!-- Cargar los resultados dinamicamente -->
                            </div>
                        </div>
                    </div>

                    <!-- Botón Buscar -->
                    <div class="col-span-1 flex items-end">
                        <button type="button" id="buscar-cliente-btn"
                            class="h-8 w-full rounded-lg bg-brand-500 text-white text-sm font-medium shadow hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2">
                            Buscar Cliente
                        </button>
                    </div>
                </div>

                <!-- Correo -->
                <div>
                    <label for="customer-correo"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Correo electrónico
                    </label>
                    <input type="text" id="customer-correo"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        placeholder="correo@ejemplo.com">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Nombres -->
                    <div>
                        <label for="customer-nombres"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Nombres
                        </label>
                        <input type="text" id="customer-nombres"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            placeholder="Jhon Deniyal">
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label for="customer-apellidos"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Apellidos
                        </label>
                        <input type="text" id="customer-apellidos"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            placeholder="Doe Smith">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <!-- Contacto -->
                    <div>
                        <label for="customer-contacto"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            N° Contacto
                        </label>
                        <input type="text" id="customer-contacto"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            placeholder="Ingrese N° contacto">
                    </div>
                    <!-- Dirección -->
                    <div>
                        <label for="customer-direccion"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Dirección
                        </label>
                        <input type="text" id="customer-direccion"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-8 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                            placeholder="Ingrese la dirección">
                    </div>
                </div>
                <!-- Btn Guardar -->
                <div class="col-span-1 md:col-span-2 flex justify-end">
                    <button type="submit"
                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
