<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        @yield('title', config('app.name'))
    </title>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/fontawesome.min.css') }}">

    <script defer src="{{ asset('assets/js/toast.js') }}"></script>

    @yield('assets')

</head>

<body x-data="{ selected: '{{ Route::currentRouteName() }}', sidebarToggle: false, darkMode: false, dTitle: '' }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')) ?? false;
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode }">
    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden bg-white dark:bg-[#101828]">
        <!-- ===== Sidebar Start ===== -->
        <x-shared.sidebar />
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 bg-white overflow-x-hidden overflow-y-auto custom-scrollbar">
            <!-- Small Device Overlay Start -->
            {{-- <include src="./partials/overlay.html" /> --}}
            <!-- Small Device Overlay End -->

            <!-- ===== Header Start ===== -->
            <x-shared.header />
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main class="flex-1 bg-slate-100 rounded-tl-2xl overflow-hidden">
                <div class="h-full overflow-y-auto p-4 custom-scrollbar">
                    @yield('breadcrumb')

                    {{-- <div x-show="selected === 'dashboard'">
                    @include('components.dashboard')
                </div>
                <div x-show="selected === 'config'" x-cloak>
                    @include('components.configuracion.configuracion')
                </div>

                <div x-show="selected === 'productos-categorias'" x-cloak>
                    @include('components.productos.productos-categorias')
                </div>
                <div x-show="selected === 'productos-lista'" x-cloak>
                    @include('components.productos.productos-lista')
                </div>
                <div x-show="selected === 'productos-jornadas'" x-cloak>
                    @include('components.productos.productos-jornadas')
                </div>

                <div x-show="selected === 'ventas-cliente'" x-cloak>
                    @include('components.ventas.ventas-cliente')
                </div>
                <div x-show="selected === 'ventas-factura'" x-cloak>
                    @include('components.ventas.ventas-factura')
                </div> --}}
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
    @include('components.utils.toasts')
    @include('components.utils.soccer-spinner')
</body>

</html>
