<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden  bg-white dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0">
    <!-- SIDEBAR HEADER -->
    <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
        class="flex items-center gap-2 pt-8 px-5 sidebar-header pb-7">
        <a>
            <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
                <img class="hidden lg:block dark:hidden w-40 max-w-[160px] h-auto"
                    src="{{ asset('assets/img/logo.png') }}" alt="Logo" />
                <img class="hidden dark:block w-40 max-w-[120px] h-auto" src="{{ asset('assets/img/logo.png') }}"
                    alt="Logo" />
            </span>

            <img class="logo-icon w-8 " :class="sidebarToggle ? 'lg:block' : 'hidden'"
                src="{{ asset('assets/img/logo_responsive.png') }}" alt="Logo" />
        </a>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar pl-5">
        <!-- Sidebar Menu -->
        <nav x-data="{ selected: $persist('Dashboard') }">
            <a href="{{ route('dashboard.index') }}" @class([
                'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                    'dashboard.index'),
                'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                    'dashboard.index'),
            ])>
                <!-- Icono siempre visible -->
                <i class="fas fa-home {{ request()->routeIs('dashboard.index') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                <span class="menu-item-text" :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                    Dashboard
                </span>
            </a>

            <a href="{{ route('dashboard.config') }}" @class([
                'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                    'dashboard.config'),
                'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                    'dashboard.config'),
            ])>
                <i class="fa-solid fa-gears {{ request()->routeIs('dashboard.config') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                <span class="menu-item-text"
                    :class="[
                        sidebarToggle ? 'xl:hidden' : 'inline-block',
                        'block xl:block'
                    ]">
                    Configuración
                </span>
            </a>

            @can('view users')
                <a href="{{ route('users.index') }}" @class([
                    'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                    'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                        'users.index'),
                        'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                            'users.index'),
                            ])>
                    <i class="fa-solid fa-users {{ request()->routeIs('users.index') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                    <span class="menu-item-text"
                    :class="[
                        sidebarToggle ? 'xl:hidden' : 'inline-block',
                            'block xl:block'
                            ]">
                        Usuarios
                    </span>
                </a>
            @endcan

            <div>
                <h3 class="my-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        Qr
                    </span>
                </h3>

                <ul class="flex flex-col gap-2 mb-2">
                    <!-- Menu Item Qr -->
                    <li>
                        <a href="{{route('qr.home')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'qr.home'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'qr.home'),
                        ])>
                            <i class="fa-solid fa-qrcode {{ request()->routeIs('qr.home') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Escanear Qr</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Productos Group -->
            <div>
                <h3 class="my-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        Productos
                    </span>
                </h3>

                <ul class="flex flex-col gap-4 mb-6">
                    <!-- Menu Item categoria -->
                    <li>
                        <a href="{{ route('products.categories') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'products.categories'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'products.categories'),
                        ])>
                            <i class="fa fa-tags {{ request()->routeIs('products.categories') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Categorías</span>
                        </a>
                    </li>

                    <!-- Menu Item abonos -->
                    <li>
                        <a href="{{ route('products.abonos') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'products.abonos'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'products.abonos'),
                        ])>
                            <i class="fa fa-tags {{ request()->routeIs('products.abonos') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Abonos</span>
                        </a>
                    </li>

                    <!-- Menu Item Jornadas -->
                    <li>
                        <a href="{{ route('products.days') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'products.days'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'products.days'),
                        ])>
                            <i class="fa-solid fa-calendar-days {{ request()->routeIs('products.days') ? 'menu-item-icon-active' : 'menu-item-icon-inactive'}}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Jornadas</span>
                        </a>
                    </li>
                    <!-- Menu Item Jornadas -->

                    <!-- Menu Item Productos -->
                    <li>
                        <a href="{{ route('products.list') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'products.list'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'products.list'),
                        ])>
                            <i class="fa fa-box {{ request()->routeIs('products.list') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Productos</span>
                        </a>
                    </li>
                    <!-- Menu Item Productos -->
                </ul>
            </div>

            <div>
                <h3 class="my-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        Ventas
                    </span>
                </h3>

                <ul class="flex flex-col gap-2 mb-2">
                    <li>
                        <a href="{{ route('sales.client') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sales.client'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sales.client'),
                        ])>
                            <i class="fa-solid fa-users {{ request()->routeIs('sales.client') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Clientes</span>
                        </a>
                    </li>
                    <!-- Menu Item Clientes -->

                    <!-- Menu Item Factura -->
                    <li>
                        <a href="{{ route('sales.bill') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sales.bill'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sales.bill'),
                        ])>
                            <i class="fa-solid fa-receipt {{ request()->routeIs('sales.bill') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Factura</span>
                        </a>
                    </li>

                    <!-- Menu Item Listado de Factura -->
                    <li>
                        <a href="{{ route('sales.bills') }}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sales.bills'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sales.bills'),
                        ])>
                            <i class="fa-solid fa-list-ul {{ request()->routeIs('sales.bills') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Listado de Facturas</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="my-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        Reportes
                    </span>
                </h3>

                <ul class="flex flex-col gap-2 mb-2">
                    <!-- Menu Item Cardex -->
                    <li>
                        <a href="{{route('reporte.reportMovimientoCaja')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'reporte.reportMovimientoCaja'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'reporte.reportMovimientoCaja'),
                        ])>
                            <i class="fa-solid fa-cash-register {{ request()->routeIs('reporte.reportMovimientoCaja') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Movimiento de caja</span>
                        </a>
                    </li>
                    <!-- Menu Item Kardex -->
                    <li>
                        <a href="{{route('reporte.cardex')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'reporte.cardex'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'reporte.cardex'),
                        ])>
                            <i class="fa-solid fa-clipboard-list 
                            {{ request()->routeIs('reporte.cardex')  ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Kardex</span>
                        </a>
                    </li>

                    <!-- Menu Item Productos Vendidos -->
                    <li>
                        <a href="{{route('reporte.ventasProductos')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'reporte.ventasProductos'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'reporte.ventasProductos'),
                        ])>
                            <i class="fa-solid fa-box-open
                                {{ request()->routeIs('reporte.ventasProductos')  ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Productos vendidos</span>
                        </a>
                    </li>
                    <!-- Menu Item Reporte de Ventas -->
                     <li>
                        <a href="{{route('reporte.ventas')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'reporte.ventas'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'reporte.ventas'),
                        ])>
                            <i class="fa-solid fa-chart-line
                                {{ request()->routeIs('reporte.ventas')  ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Reporte de Facturas</span>
                        </a>
                    </li>
                </ul>
            </div>

             <div>
                <h3 class="my-4 text-xs uppercase leading-[20px] text-gray-400">
                    <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
                        Sorteos
                    </span>
                </h3>

                <ul class="flex flex-col gap-2 mb-2">
                    <!-- Menu Item Periodo Campeonato -->
                    <li>
                        <a href="{{route('sorteo.periodoCampeonato')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sorteo.periodoCampeonato'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sorteo.periodoCampeonato'),
                        ])>
                            <i class="fa-solid fa-clock {{ request()->routeIs('sorteo.periodoCampeonato') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Periodo Campeonato</span>
                        </a>
                    </li>
                </ul>

                <ul class="flex flex-col gap-2 mb-2 hidden">
                    <!-- Menu Item Cardex -->
                    <li>
                        <a href="{{route('sorteo.cargarDatosFactura')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sorteo.cargarDatosFactura'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sorteo.cargarDatosFactura'),
                        ])>
                            <i class="fa-solid fa-file-excel {{ request()->routeIs('sorteo.cargarDatosFactura') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Importar Datos Factura</span>
                        </a>
                    </li>
                   
                </ul>

                <ul class="flex flex-col gap-2 mb-2">
                    <!-- Menu Item Boletos -->
                    <li>
                        <a href="{{route('sorteo.boletos')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sorteo.boletos'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sorteo.boletos'),
                        ])>
                            <i class="fa-solid fa-ticket {{ request()->routeIs('sorteo.boletos') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Sorteos del Sistema</span>
                        </a>
                    </li>
                   
                </ul>

                <ul class="flex flex-col gap-2 mb-2">
                    <!-- Menu Item Periodo Campeonato -->
                    <li>
                        <a href="{{route('sorteo.sorteosRealizados')}}" @class([
                            'flex items-center gap-3 px-4 py-2 rounded-l-full menu-item',
                            'bg-slate-100 dark:bg-gray-800 text-blue-600 shadow-md font-semibold menu-item-active' => request()->routeIs(
                                'sorteo.sorteosRealizados'),
                            'text-gray-700 rounded-br-xl dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 menu-item-inactive' => !request()->routeIs(
                                'sorteo.sorteosRealizados'),
                        ])>
                            <i class="fa-solid fa-gift {{ request()->routeIs('sorteo.sorteosRealizados') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}"></i>
                            <span class="menu-item-text"
                                :class="[sidebarToggle ? 'xl:hidden' : 'inline-block', 'block xl:block']">
                                Sorteos Realizados</span>
                        </a>
                    </li>
                </ul>


            </div>

        </nav>
        <!-- Sidebar Menu -->
    </div>
</aside>
