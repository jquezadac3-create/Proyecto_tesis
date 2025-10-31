<div class="space-y-5 sm:space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="p-5 sm:p-6 dark:border-gray-800">
            <!-- Table Periodos -->
            <div class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-5 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Listado de Periodos
                        </h3>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Botón Nuevo Periodo -->
                        <button type="button" @click="showFormPeriodo = true;  resetFormPeriodo()"
                            class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-blue-400 border border-info dark:border-info px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-info active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-info dark:text-on-info dark:focus-visible:outline-info">
                            + Nuevo
                        </button>
                    </div>
                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tPeriodos" class="min-w-full">
                        <!-- table header start -->
                        <thead class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        ID
                                    </span>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        Nombre
                                    </span>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        Fecha Inicio
                                    </span>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        Fecha Fin
                                    </span>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        Estado
                                    </span>
                                </th>
                                <th class="px-6 py-3 text-center">
                                    <span class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                        Acción
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <!-- table header end -->

                        <!-- table body start -->
                        <tbody id="tbPeriodos" class="divide-y divide-gray-100 dark:divide-gray-800">
                        </tbody>
                        <!-- table body end -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
