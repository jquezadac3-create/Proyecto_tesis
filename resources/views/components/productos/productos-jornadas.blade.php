@extends('layout.main-layout')

@section('title','JornadasProductos')

@section('breadcrumb')
<div x-show="selected === 'products.days'">
        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                form: { nombre: '', fecha:'', aforo:'' },
                guardarProducto() {
                    console.log(this.form);
                    this.showForm = false;
                }
            }">
    
                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de Jornadas` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Jornadas
                            </h2>
                            <nav>
                                <ol class="flex items-center gap-1.5">
                                    <li>
                                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                            href="{{ route('configuracion.index') }}">
                                            Home
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </li>
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Registro de Jornadas
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->
    
                    <div class="space-y-5 sm:space-y-6">
    
                        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    
                            <div class="p-5 sm:p-6 dark:border-gray-800">
                                <!-- Table Four -->
                                <div
                                    class="overflow-hidden rounded-2xl bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                                    <div
                                        class="flex flex-col gap-5 px-6 mb-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                                Jornadas
                                            </h3>
                                        </div>
    
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
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
                                            <div>
                                                <button
                                                    class="text-theme-sm shadow-theme-xs inline-flex h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                                    <svg class="stroke-current fill-white dark:fill-gray-800" width="20"
                                                        height="20" viewBox="0 0 20 20" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.29004 5.90393H17.7067" stroke="" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M17.7075 14.0961H2.29085" stroke="" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path
                                                            d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z"
                                                            fill="" stroke="" stroke-width="1.5"></path>
                                                        <path
                                                            d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z"
                                                            fill="" stroke="" stroke-width="1.5"></path>
                                                    </svg>
    
                                                    Filter
                                                </button>
                                            </div>
                                            <!-- info Button with Icon -->
                                            <button type="button" @click="showForm = true"
                                                class="inline-flex justify-center items-center gap-2 whitespace-nowrap rounded-lg bg-blue-400 border border-info dark:border-info px-4 py-2 text-sm font-medium tracking-wide text-white transition hover:opacity-75 text-center focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-info active:opacity-100 active:outline-offset-0 disabled:opacity-75 disabled:cursor-not-allowed dark:bg-info dark:text-on-info dark:focus-visible:outline-info">
                                                + Nuevo
                                            </button>
                                        </div>
                                    </div>
    
                                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                                        <table class="min-w-full">
                                            <!-- table header start -->
                                            <thead
                                                class="border-gray-100 border-y bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
                                                <tr>
                                                    <th class="px-6 py-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div x-data="{ checked: false }" class="flex items-center gap-3">
                                                                <div>
                                                                    <span
                                                                        class="block font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                        Nombre
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
                                                                        Fecha Jornada
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
                                                                        Aforo
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th class="px-6 py-3 whitespace-nowrap">
                                                        <div class="flex items-center justify-center">
                                                            <p
                                                                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                                                Acción
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
                                                                        Partido 1
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
                                                                        05/08/2025
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
                                                                        100
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-3 whitespace-nowrap">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <i
                                                                class="fa-solid fa-eye text-sm text-gray-600 cursor-pointer dark:text-gray-400"></i>
                                                            <i
                                                                class="fa-solid fa-pen-to-square text-sm text-gray-600 cursor-pointer dark:text-gray-400"></i>
                                                            <i
                                                                class="fa-solid fa-trash-can text-sm text-gray-600 cursor-pointer dark:text-gray-400"></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <!-- table body end -->
                                        </table>
                                    </div>
                                    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
                                        <div class="flex items-center justify-between">
                                            <button
                                                class="text-theme-sm shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2 py-2 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 sm:px-3.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
                                                        fill=""></path>
                                                </svg>
    
                                                <span class="hidden sm:inline"> Previous </span>
                                            </button>
    
                                            <span
                                                class="block text-sm font-medium text-gray-700 sm:hidden dark:text-gray-400">
                                                Page 1 of 10
                                            </span>
    
                                            <ul class="hidden items-center gap-0.5 sm:flex">
                                                <li>
                                                    <a href="#"
                                                        class="bg-brand-500/[0.08] text-theme-sm text-brand-500 hover:bg-brand-500/[0.08] hover:text-brand-500 dark:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium">
                                                        1
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        2
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        3
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        ...
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        8
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        9
                                                    </a>
                                                </li>
    
                                                <li>
                                                    <a href="#"
                                                        class="text-theme-sm hover:bg-brand-500/[0.08] hover:text-brand-500 dark:hover:text-brand-500 flex h-10 w-10 items-center justify-center rounded-lg font-medium text-gray-700 dark:text-gray-400">
                                                        10
                                                    </a>
                                                </li>
                                            </ul>
    
                                            <button
                                                class="text-theme-sm shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-2 py-2 font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 sm:px-3.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                                <span class="hidden sm:inline"> Next </span>
    
                                                <svg class="fill-current" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                                        fill=""></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Table Four -->
                            </div>
                        </div>
    
                    </div>
                </div>
    
                <!-- Modal flotante -->
                <div x-show="showForm" x-transition
                    class="fixed inset-0 z-600 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
                        <!-- Encabezado -->
                        <div class="px-5 py-4 border-b border-blue-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Registrar jornada</h2>
                        </div>
    
                        <!-- Formulario -->
                        <form @submit.prevent="guardarProducto" class="p-6 space-y-4">
                            
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Nombre</label>
                                <input type="text" x-model="form.nombre"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Fecha</label>
                                <input type="date" x-model="form.fecha"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                            </div>
                            <div class="w-full">
                                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Aforo</label>
                                <input type="number" x-model="form.aforo"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 p-2 text-sm dark:bg-gray-800 dark:text-white">
                            </div>
    
    
    
                            <!-- Botones -->
                            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4">
                                <button @click="showForm = false" type="button"
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
            </div>
        </body>
    
</div>
@endsection