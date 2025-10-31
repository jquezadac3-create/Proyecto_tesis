<header x-data="{ menuToggle: false }"
    class="sticky top-0 z-99999 flex w-full bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
        <div
            class="flex w-full items-center justify-between gap-2 px-3 py-3 sm:gap-4 lg:justify-normal  lg:px-0 lg:py-4 dark:border-gray-800">
            <!-- Hamburger Toggle BTN -->
            <button
                :class="sidebarToggle ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800' : ''"
                class="z-99999 flex h-10 w-10 items-center justify-center rounded-lg  text-gray-500 lg:h-11 lg:w-11 lg:border dark:border-gray-800 dark:text-gray-400"
                @click.stop="sidebarToggle = !sidebarToggle">
                <!-- Ícono hamburguesa (desktop y mobile) -->
                <i :class="sidebarToggle ? 'hidden' : 'fas fa-bars block text-lg lg:text-base'"></i>

                <!-- Ícono cerrar (mobile) -->
                <i :class="sidebarToggle ? 'fas fa-times block text-lg lg:hidden' : 'hidden'"></i>
            </button>
            <!-- Hamburger Toggle BTN -->

            <a class="lg:hidden">
                <img class="dark:hidden w-full max-w-[80px] h-auto" src="{{ asset('assets/img/logo.png') }}"
                    alt="Logo" />
                <img class="hidden dark:block w-full max-w-[80px] h-auto" src="{{ asset('assets/img/logo.png') }}"
                    alt="Logo" />
            </a>

            <!-- Application nav menu button -->
            <button
                class="z-99999 flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
                :class="menuToggle ? 'bg-gray-100 dark:bg-gray-800' : ''" @click.stop="menuToggle = !menuToggle">
                <i class="fas fa-ellipsis-h text-xl"></i>
            </button>
            <!-- Application nav menu button -->
        </div>

        <div :class="menuToggle ? 'flex' : 'hidden'"
            class="shadow-theme-md w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0 lg:shadow-none">
            <div class="2xsm:gap-3 flex items-center gap-2">
                {{-- <!-- Dark Mode Toggler -->
                <button
                    class="hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    @click.prevent="darkMode = !darkMode">
                    <i class="fas fa-sun text-xl dark:hidden"></i>
                    <i class="fas fa-moon text-xl hidden dark:block"></i>
                </button>
                <!-- Dark Mode Toggler --> --}}
            </div>

            <!-- User Area -->
            <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                <a class="flex items-center text-gray-700 dark:text-gray-400" href="#"
                    @click.prevent="dropdownOpen = ! dropdownOpen">
                    <span class="mr-3 h-11 w-11 overflow-hidden rounded-full">
                        <img src="{{ asset('assets/img/logo_responsive.png') }}" alt="User" />
                    </span>

                    <span class="text-theme-sm capitalize mr-1 block font-medium"> {{ auth()->user()->name }} </span>

                    <svg :class="dropdownOpen && 'rotate-180'" class="stroke-gray-500 dark:stroke-gray-400" width="18"
                        height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.3125 8.65625L9 13.3437L13.6875 8.65625" stroke="" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>

                <!-- Dropdown Start -->
                <div x-show="dropdownOpen"
                    class="shadow-theme-lg dark:bg-gray-dark absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800">
                    <div>
                        <span class="text-theme-sm block font-medium text-gray-700 dark:text-gray-400 capitalize">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-theme-xs mt-0.5 block text-gray-500 dark:text-gray-400">
                            {{ auth()->user()->email }}
                        </span>
                    </div>

                    <ul class="flex flex-col gap-1 pt-4 pb-0 dark:border-gray-800 select-none">
                        <li>
                            <a x-on:click="toast.show('warning', 'Próximamente', 'Esta acción no se encuentra disponible en este momento')"
                                class="group text-theme-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                <i
                                    class="fas fa-user-edit text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300"></i>
                                Editar perfil
                            </a>
                        </li>
                    </ul>
                    <form id="logoutForm" x-data="{ submitting: false }" x-on:submit="submitting = true;" class="w-full" action="{{ route('auth.logout') }}" method="POST">
                        @csrf
                        <button type="submit" form="logoutForm" :disabled="submitting"
                            class="w-full group text-theme-sm mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                            <i
                            class="fas fa-sign-out-alt text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"></i>
                            Salir
                        </button>
                    </form>
                </div>
                <!-- Dropdown End -->
            </div>
            <!-- User Area -->
        </div>
    </div>
</header>