<div class="space-y-5 sm:space-y-6">

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        <div class="p-5 sm:p-6 dark:border-gray-800">
            <!-- Table Four -->
            <div class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-5 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Facturas
                        </h3>
                    </div>

                    @if (isset($pendingInvoices) && $pendingInvoices > 0)
                        <div x-data="{ submitted: false }" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <form action="{{ route('facturas.listadoReenvio') }}" method="POST" @submit="submitted = true">
                                @csrf
                                <!-- info Button with Icon -->
                                <button :disabled="submitted" type="submit"
                                    class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-blue-400 border border-info dark:border-info px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-info active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-info dark:text-on-info dark:focus-visible:outline-info">
                                    Reenviar Facturas Pendientes
                                </button>
                            </form>
                        </div>
                    @endif
                    @if (session('success'))
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                toast.show('success', 'Éxito', "{{ session('success') }}")
                            })
                        </script>
                    @endif

                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table id="tFacturas" class="min-w-full">
                        <!-- table header start -->
                        <thead class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                    Secuencia Factura
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-3 whitespace-nowrap" data-type="date" data-format="YYYY-MM-DD">
                                    <div class="flex items-center">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                    Fecha Emisión
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                    Valor
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                    Cliente
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <span
                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                    Estado
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            Acción
                                        </p>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <!-- table header end -->

                        <!-- table body start -->
                        <tbody id="tbFacturas" class="divide-y divide-gray-100 dark:divide-gray-800">
                        </tbody>
                        <!-- table body end -->
                    </table>
                </div>
            </div>
            <!-- Table Four -->
        </div>
    </div>

</div>