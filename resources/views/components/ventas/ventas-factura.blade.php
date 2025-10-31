@extends('layout.main-layout')

@section('title','VentasFactura')

@section('breadcrumb')
<div x-show="selected === 'sales.bill">
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `Factura` }">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Factura</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="index.html">
                                Home
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke=""
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Crear factura</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- Content Start -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-xl font-medium text-gray-800 dark:text-white">
                    Crear factura
                </h2>
            </div>
            <div class="border-b border-gray-200 p-4 sm:p-8 dark:border-gray-800">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="factura-identificación"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">N°
                                Identificación</label>
                            <input type="text" id="factura-identificación"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="WP3434434">
                        </div>
                        <div>
                            <label for="customer-nombres"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombres</label>
                            <input type="text" id="customer-nombres"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Jhon Deniyal">
                        </div>
                        <div>
                            <label for="customer-correo"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Correo
                                electrónico
                            </label>
                            <input type="text" id="customer-correo"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Ingrese el correo el ectrónico">
                        </div>
                        <div>
                            <label for="customer-contacto"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">N° Contacto
                            </label>
                            <input type="text" id="customer-contacto"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Ingrese el correo el N° contacto">
                        </div>
                        <div>
                            <label for="customer-direccion"
                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dirección
                            </label>
                            <input type="text" id="customer-direccion"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                placeholder="Ingrese el correo el direccion">
                        </div>
                        <div class="col-span-1 md:col-span-2 flex justify-end">
                            <button type="submit"
                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                Guardar 
                            </button>
                        </div>
                        
                    </div>
                </form>
            </div>
            <div class="overflow-hidden">
                <div class="custom-scrollbar overflow-x-auto">
                    <div class="space-y-5 sm:space-y-6">

                        <div class="bg-white dark:bg-white/[0.03]">

                            <div class="p-5 sm:p-6 dark:border-gray-800">
                                <!-- Table Four -->
                                <div class="overflow-hidden bg-white py-4 dark:bg-white/[0.03]">
                                    <div class="flex flex-col justify-end gap-3 sm:flex-row sm:items-center">
                                        <form>
                                            <div class="relative">
                                                <span
                                                    class="absolute -translate-y-1/2 pointer-events-none top-1/2 left-4">
                                                    <svg class="fill-gray-500 dark:fill-gray-400" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
                                                            fill=""></path>
                                                    </svg>
                                                </span>
                                                <input type="text" placeholder="Search..."
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-[42px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[300px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="max-w-full overflow-x-auto custom-scrollbar">
                                    <table class="min-w-full">
                                        <!-- table header start -->
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div x-data="{ checked: false }" class="flex items-center gap-3">
                                                            <div>
                                                                <span
                                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                    Código
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div x-data="{ checked: false }" class="flex items-center gap-3">
                                                            <div>
                                                                <span
                                                                    class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                    Descripción
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Cantidad
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Precio unitario
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Precio total
                                                        </p>
                                                    </div>
                                                </th>
                                                <th class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center justify-center">
                                                        <p
                                                            class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                            Action
                                                        </p>
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <!-- table header end -->

                                        <!-- table body start -->
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            <tr>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex items-center gap-3">
                                                            <div>
                                                                <span
                                                                    class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                                                                    0582
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex items-center gap-3">
                                                            <div>
                                                                <span
                                                                    class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                                                                    Entrada vip
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <input type="number"
                                                            class="w-14 rounded border border-blue-200 bg-white px-2 py-1 text-sm text-gray-700 outline-none ring-0 transition focus:border-blue-500 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-blue-500 dark:focus:ring-blue-400/40"
                                                            min="1" value="1" />
                                                    </div>
                                                </td>

                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">
                                                            25
                                                        </p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <p class="text-gray-700 text-theme-sm dark:text-gray-400">
                                                            25
                                                        </p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <i
                                                            class="fa-solid fa-trash-can text-sm text-gray-600 cursor-pointer dark:text-gray-400"></i>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!-- table body end -->
                                    </table>
                                </div>
                            </div>
                            <!-- Table Four -->
                        </div>
                    </div>

                </div>
                <template x-if="products.length === 0">
                    <div class="px-5 py-4 text-center text-gray-400">
                        No products added.
                    </div>
                </template>
            </div>

            <!-- Total Summary -->
            <div class="flex flex-wrap justify-between sm:justify-end">
                <div class="mt-6 w-full space-y-1 text-right sm:w-[220px]">
                    <p class="mb-4 text-left text-sm font-medium text-gray-800 dark:text-white/90">
                        Resumen del pedido
                    </p>
                    <ul class="space-y-2">
                        <li class="flex justify-between gap-5">
                            <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal (15%):</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + subtotal15">$30.00</span>
                        </li>
                        <li class="flex justify-between gap-5">
                            <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal (5%):</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + subtotal5">$0.00</span>
                        </li>
                        <li class="flex justify-between gap-5">
                            <span class="text-sm text-gray-500 dark:text-gray-400">SubTotal (0%):</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + subtotal0">$0.00</span>
                        </li>
                        <li class="flex justify-between gap-5">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Descuento:</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + descuento">$0.00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">IVA (15%):</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + iva15">$0.00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">IVA (5%):</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + iva5">$0.00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">ICE:</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + ice">$0.00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Adicional:</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400"
                                x-text="'$' + adicional">$0.00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="font-medium text-gray-700 dark:text-gray-400">Total</span>
                            <span class="text-lg font-semibold text-gray-800 dark:text-white/90"
                                x-text="'$' + total">$4235.00</span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="p-4 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button @click="isModalOpen = !isModalOpen"
                    class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    <i class="fa-solid fa-eye"></i>
                    Ver factura
                </button>
                <button
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Guardar factura
                </button>
            </div>
        </div>
    </div>
    <!-- Content End -->
</div>
@endsection