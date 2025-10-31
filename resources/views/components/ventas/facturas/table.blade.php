<!-- resources/views/components/ventas/factura/table.blade.php -->
<div class="custom-scrollbar mt-5 rounded-t-2xl border-t border-x border-gray-200 overflow-x-auto">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800 bg-white">
        <h2 class="text-xl font-medium text-gray-800 dark:text-white">
            Lista de Productos
        </h2>
    </div>
    <div class="space-y-6">
        <div class="bg-white dark:bg-white/[0.03]">
            <div class="p-5 sm:p-6 dark:border-gray-800">
                <!-- Buscador -->
                <div class="bg-white py-4 dark:bg-white/[0.03]">
                    <form class="grid grid-cols-3 items-center w-full gap-3" @submit.prevent>
                        <!-- Input izquierdo -->
                        <div class="flex justify-center">
                            <div class="relative w-48">
                                <input id="left-input" type="text" autocomplete="off" list="opciones-list"
                                    class="hidden" />
                                <datalist id="opciones-list"></datalist>

                                <!-- Dropdown opciones -->
                                <div class="hidden absolute left-0 mt-1 z-[9999] text-left"
                                    id="dropdownContainerOpciones">
                                    <div id="dropdownOpciones"
                                        class="bg-white dark:bg-gray-900 rounded-lg shadow max-h-60 overflow-auto border border-indigo-500 min-w-[20rem]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna vacía -->
                        <div></div>

                        <!-- Input derecho -->
                        <div class="flex justify-end">
                            <div class="relative w-72">
                                <span class="absolute -translate-y-1/2 pointer-events-none top-1/2 left-4">
                                    <i class="fa-solid fa-magnifying-glass" style="color: #888;"></i>
                                </span>
                                <input id="search-input" type="text" placeholder="Buscar Producto..."
                                    autocomplete="off" list="productos-list"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                       dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 
                       bg-transparent py-2.5 pr-4 pl-[42px] text-sm text-gray-800 
                       placeholder:text-gray-400 focus:ring-3 focus:outline-hidden 
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">

                                <datalist id="productos-list"></datalist>

                                <!-- Dropdown productos -->
                                <div class="hidden absolute right-0 mt-1 w-full z-[9999] text-left"
                                    id="dropdownContainerProductos">
                                    <div id="dropdownProductos"
                                        class="bg-white dark:bg-gray-900 rounded-lg shadow max-h-60 overflow-auto border border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabla de productos -->
                <div class="max-w-full overflow-x-auto custom-scrollbar mt-6">
                    <div class="max-h-96 overflow-y-auto border custom-scrollbar border-gray-200 rounded-lg dark:border-gray-700">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 whitespace-nowrap">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Código</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Descripción</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Stock
                                            Actual</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cantidad</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Precio
                                            unitario</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Precio
                                            total</span>
                                    </th>
                                    <th class="px-6 py-3 whitespace-nowrap text-center">
                                        <span
                                            class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">Action</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody" class="divide-y divide-gray-200 dark:divide-gray-800">
                                <!-- Filas dinámicas -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
