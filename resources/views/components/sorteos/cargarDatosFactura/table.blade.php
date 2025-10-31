<div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6 dark:border-gray-800">
            <!-- Contenedor superior-->
            <div class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                
                
                <div class="flex flex-col gap-5 px-6 mb-4">
                    <!-- Título -->
                    <div class="w-full">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Facturas Ingresadas al Sistema
                        </h3>
                    </div>

                    <!-- Botón Importar Excel -->
                    <div class="w-full flex flex-col gap-3 sm:flex-row sm:items-center justify-end">
                        <!-- Botón Generar Boletos -->
                        <button 
                            id="btnGenerarBoletos" 
                            class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-purple-500 border border-purple-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center cursor-pointer"
                        >
                            <i class="fa-solid fa-ticket"></i>
                            Generar Boletos
                        </button>
                        <!-- Botón Actualizar Datos -->
                        <button 
                            id="btnActualizarDatos" 
                            class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-green-500 border border-green-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center cursor-pointer"
                        >
                            <i class="fa-solid fa-sync"></i>
                            Actualizar Datos
                        </button>
                        <form id="formImportarExcel"> 
                         @csrf
                        <input 
                            type="file" 
                            id="archivo" 
                            name="archivo" 
                            accept=".xlsx,.xls" 
                            class="hidden"
                        >

                        <label 
                            for="archivo"
                            class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-blue-500 border border-blue-600 px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center cursor-pointer"
                        >
                            <i class="fa-solid fa-file-excel"></i>
                            Importar Datos
                        </label>
                        </form>

                    </div>
                </div>

                <!-- Tabla DatosFactura -->
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tDatosFactura" class="min-w-full">
                        <thead class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Factura</th>
                                <th class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</th>
                                <th class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">Cantidad Entradas</th>
                                <th class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">Periodo Campeonato</th>
                                <th class="px-6 py-3 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">Producto</th>
                            </tr>
                        </thead>
                        <tbody id="tbDatosFactura" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- Aquí se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- Fin Contenedor -->
        </div>
    </div>
</div>
