<div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 sm:p-6">

            <!-- Título -->
            <div class="w-full text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Generación de Sorteos
                </h3>
            </div>

            <!-- Filtros centrados: Selects y Botón -->
            <div class="w-full flex flex-col gap-4 sm:flex-row sm:items-center justify-center mb-6">
                <!-- Periodos disponibles -->
                <div class="w-86 relative">
                    <select id="selectPeriodo" name="periodo" required
                        class="choices-select bg-white border border-blue-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option value="" disabled selected>Seleccione un periodo</option>
                    </select>
                    <label for="selectPeriodo"
                        class="ml-1 absolute font-sans text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2
                            peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500
                            peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2
                            peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                        Periodo Campeonato
                    </label>
                </div>

                <!-- Jornadas Disponibles -->
                <div class="w-86 relative">
                    <select id="selectJornada" name="jornada" multiple required disabled
                        class="choices-select bg-white border border-blue-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                    </select>
                    <label for="selectJornada"
                        class="ml-1 absolute font-sans text-sm text-blue-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white dark:bg-gray-900 px-2
                            peer-focus:px-2 peer-focus:text-blue-600 peer-focus:dark:text-blue-500
                            peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2
                            peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4">
                        Jornadas Disponibles
                    </label>
                </div>

                <!-- Botón Crear Sorteo -->
                <div class="mt-2 sm:mt-0 sm:mr-2">
                    <button type="button" id="btnCrearSorteo"
                        class="px-6 py-2.5 rounded-lg bg-green-500 text-white text-sm font-medium hover:bg-green-600 flex items-center justify-center">
                        Crear Sorteo
                    </button>
                </div>

                <!-- Botón Realizar Sorteo -->
                <div class="mt-2 sm:mt-0 hidden" id="containerRealizarSorteo">
                    <button type="button" id="btnRealizarSorteo"
                        class="px-6 py-2.5 rounded-lg bg-blue-500 text-white text-sm font-medium hover:bg-blue-600 flex items-center justify-center">
                        Realizar Sorteo
                    </button>
                </div>

                <!-- Botón Nuevo Sorteo -->
                <div class="mt-2 sm:mt-0 sm:ml-2 hidden" id="containerNuevoSorteo">
                    <button type="button" id="btnNuevoSorteo"
                        class="px-6 py-2.5 rounded-lg bg-yellow-500 text-white text-sm font-medium hover:bg-yellow-600 flex items-center justify-center">
                        Nuevo Sorteo
                    </button>
                </div>

            </div>
            <!-- Tarjetas de premios -->
            <div class="hidden flex flex-row w-full mb-6" id="tarjetasPremios">
                <!-- Espacio izquierdo -->
                <div class="flex-1"></div>

               <!-- Tarjeta Total Premios -->
                <div class="flex-1 max-w-[250px] relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-blue-400 transition-all duration-300 hover:shadow-xl p-4">
                    <div class="flex flex-row -mx-2 items-center"> <!-- items-center centra verticalmente -->
                        <!-- Texto -->
                        <div class="flex-1 px-2">
                            <div class="break-words">
                                <p class="mb-1 font-sans font-bold leading-normal uppercase text-xs text-blue-600">
                                    Total Premios
                                </p>
                                <h5 class="mb-1 font-bold text-lg text-blue-700">
                                    <span id="totalPremios">0</span>
                                </h5>
                            </div>
                        </div>

                        <!-- Icono -->
                        <div class="px-2 w-12 h-12 flex justify-center items-center">
                            <div class="w-10 h-10 rounded-lg bg-blue-600 flex justify-center items-center">
                                <i class="fa-solid fa-gift text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Espacio central -->
                <div class="flex-1"></div>

                <!-- Tarjeta Premios Sorteados -->
                <div class="flex-1 max-w-[250px] relative flex flex-col min-w-0 break-words bg-white shadow-lg rounded-xl border border-green-400 transition-all duration-300 hover:shadow-xl p-4">
                    <div class="flex flex-row -mx-2 items-center"> <!-- items-center centra verticalmente -->
                        <!-- Texto -->
                        <div class="flex-1 px-2">
                            <div class="break-words">
                                <p class="mb-1 font-sans font-bold leading-normal uppercase text-xs text-green-600">
                                    Premios Sorteados
                                </p>
                                <h5 class="mb-1 font-bold text-lg text-green-700">
                                    <span id="premiosSorteados">0</span>
                                </h5>
                            </div>
                        </div>

                        <!-- Icono -->
                        <div class="px-2 w-12 h-12 flex justify-center items-center">
                            <div class="w-10 h-10 rounded-lg bg-green-600 flex justify-center items-center">
                                <i class="fa-solid fa-trophy text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Espacio derecho -->
                <div class="flex-1"></div>
            </div>


            <!-- Contenedor Ganadores del Sorteo -->
            <div id="containerGanadores" class="hidden">
                <!-- Título tabla Ganadores -->
                <div class="mb-2 mt-6">
                    <h4 class="text-md font-semibold text-gray-800 dark:text-white">Ganadores del Sorteo</h4>
                </div>

                <!-- Tabla de ganadores -->
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tablaGanadores" class="min-w-full border border-gray-200 rounded-lg divide-y divide-gray-100 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Factura</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Boleto</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Cliente</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Premio</th>
                                {{-- <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Acciones</th> --}}
                            </tr>
                        </thead>
                        <tbody id="cuerpoGanadores">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

             <!-- Título tabla Boletos -->
            <div class="mb-2 mt-6">
                <h4 class="text-md font-semibold text-gray-800 dark:text-white">Boletos del Sorteo</h4>
            </div>

            <!-- Tabla de boletos -->
            <div class="max-w-full overflow-x-auto custom-scrollbar mt-6">
                <table id="tDatosBoleto" class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Factura</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Número Boleto</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Producto</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Jornada</th>
                            <th class="px-6 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Abono</th>
                        </tr>
                    </thead>
                    <tbody id="tbDatosBoleto" class="divide-y divide-gray-100 dark:divide-gray-800">
                        <!-- Aquí se llenará dinámicamente con JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
