<div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6">

            <!-- Título -->
            <div class="w-full text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Datos del sorteo
                </h3>
            </div>

            <!-- Filtro: Periodo Campeonato + Botón Buscar -->
            <div class="w-full flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-6">
                <div class="w-80 relative">
                    <select id="selectPeriodo" name="periodo" required
                        class="choices-select bg-white border border-blue-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled selected>Seleccione un sorteo</option>
                    </select>
                    <label for="selectPeriodo"
                        class="ml-1 absolute font-sans text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2
                        peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500
                        peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2
                        peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                        Sorteos Realizados
                    </label>
                </div>
                <div class="mt-2 sm:mt-0">
                    <button type="button" id="btnBuscarBoletos"
                        class="px-6 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 flex items-center justify-center">
                        Buscar
                    </button>
                </div>
            </div>


            <!-- Panel de resumen del sorteo seleccionado -->
           <div id="panelSorteo" class="mt-4 mb-6 p-4 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm flex flex-col sm:flex-row gap-4 hidden">

                <!-- Cada bloque ocupa igual espacio -->
                <div class="flex-1 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Periodo Campeonato</p>
                    <h4 id="sorteoPeriodo" class="text-md font-semibold text-gray-800 dark:text-white">-</h4>
                </div>

                <div class="flex-1 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nombre del Sorteo</p>
                    <h4 id="sorteoNombre" class="text-md font-semibold text-gray-800 dark:text-white">-</h4>
                </div>

                <div class="flex-1 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Número de Premios</p>
                    <h4 id="sorteoPremios" class="text-md font-semibold text-gray-800 dark:text-white">-</h4>
                </div>

                <div class="flex-1 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Posición Ganadora</p>
                    <h4 id="sorteoPosicion" class="text-md font-semibold text-gray-800 dark:text-white">-</h4>
                </div>

                <div class="flex-1 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fecha del Sorteo</p>
                    <h4 id="sorteoFecha" class="text-md font-semibold text-gray-800 dark:text-white">-</h4>
                </div>
            </div>
            
            <!-- Tabla Boletos Ganadores -->
            <div id="tablaGanadores" class="mb-6 hidden">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Boletos Ganadores</h4>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tBoletosGanadores" class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-green-50 dark:bg-green-900 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Factura</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Boleto</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Premio</th>
                            </tr>
                        </thead>
                        <tbody id="tbBoletosGanadores" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- JS llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla Boletos Perdedoras -->
            <div id="tablaPerdedores" class="mb-6 hidden">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Boletos Eliminados</h4>
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tBoletosPerdedores" class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-red-50 dark:bg-red-900 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Factura</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Boleto</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Producto</th>
                            </tr>
                        </thead>
                        <tbody id="tbBoletosPerdedores" class="divide-y divide-gray-100 dark:divide-gray-800">
                            <!-- JS llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>