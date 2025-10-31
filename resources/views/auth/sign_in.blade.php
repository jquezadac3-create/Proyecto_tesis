<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        Iniciar Sesión - {{ config('app.name') }}
    </title>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/fontawesome.min.css') }}">

    <script defer src="{{ asset('assets/js/toast.js') }}"></script>
</head>

<body
    x-data="{ page: 'comingSoon', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}">

    <!-- ===== Page Wrapper Start ===== -->
    <div class="relative p-6 bg-white z-1 dark:bg-gray-900 sm:p-0">

        @error('title')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    toast.show('error', 'Error', '{{ $message }}');
                })
            </script>
        @enderror

        <div class="relative flex flex-col justify-center w-full h-screen dark:bg-gray-900 sm:p-0 lg:flex-row">
            <!-- Form -->
            <div class="flex flex-col flex-1 w-full lg:w-1/2">
                <div class="flex flex-col justify-center flex-1 w-full max-w-md mx-auto">
                    <div>
                        <div class="mb-5 sm:mb-8">
                            <h1
                                class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                                Iniciar Sesión
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Ingresa tu nombre de usuario y contraseña para iniciar sesión.
                            </p>
                        </div>
                        <div>
                            <form x-data="{ submitting: false }" x-on:submit="submitting = true;" id="loginForm" method="POST" action="{{ route('auth.authenticate') }}">
                                @csrf
                                <div class="space-y-5">
                                    <!-- Email -->
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Usuario<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" placeholder="Ingresa tu Usuario"
                                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                            required />
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <!-- Password -->
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Contraseña<span class="text-red-500">*</span>
                                        </label>
                                        <div x-data="{ showPassword: false }" class="relative">
                                            <input :type="showPassword ? 'text' : 'password'"
                                                placeholder="Ingresa tu Contraseña" id="password" name="password"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                                                required />
                                            <span @click="showPassword = !showPassword"
                                                class="absolute z-30 text-gray-500 -translate-y-1/2 cursor-pointer right-4 top-1/2 dark:text-gray-400">
                                                <i x-show="!showPassword" class="fa-regular fa-eye"></i>
                                                <i x-show="showPassword" class="fa-regular fa-eye-slash"></i>
                                            </span>
                                        </div>
                                        @error('password')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                    <!-- Checkbox -->
                                    <div class="flex items-center justify-between">
                                        <div x-data="{ checkboxToggle: false }">
                                            <label for="remember"
                                                class="flex items-center text-sm font-normal text-gray-700 cursor-pointer select-none dark:text-gray-400">
                                                <div class="relative">
                                                    <input type="checkbox" id="remember" name="remember" class="sr-only"
                                                        @change="checkboxToggle = !checkboxToggle" />
                                                    <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                                                        class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                                        <span :class="checkboxToggle ? '' : 'opacity-0'">
                                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                                                                    stroke="white" stroke-width="1.94437"
                                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                                Mantener iniciada la sesión
                                            </label>
                                        </div>
                                        {{-- <a href="{{ route('auth.forgotPassword') }}"
                                            class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">¿Olvidaste
                                            tu contraseña?</a> --}}
                                    </div>
                                    <!-- Button -->
                                    <div>
                                        <button type="submit" form="loginForm" :disabled="submitting"
                                            class="flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600">
                                            Iniciar Sesión
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative items-center hidden w-full h-full dark:bg-white/5 lg:grid lg:w-1/2">
                <div class="flex items-center justify-center z-1">
                    <div class="flex flex-col items-center max-w-lg">
                        <canvas id="dotlottie-canvas" width="400px" height="400px"></canvas>
                        <h1 class="text-center text-gray-400 dark:text-white/60">
                            Sistema de Venta de Boletos Barrabas Club.
                        </h1>
                    </div>
                </div>
            </div>
            <!-- Toggler -->
            <div class="fixed z-50 hidden bottom-6 right-6 sm:block">
                <button
                    class="inline-flex items-center justify-center text-white transition-colors rounded-full size-14 bg-brand-500 hover:bg-brand-600"
                    @click.prevent="darkMode = !darkMode">
                    <div class="hidden fill-current dark:block"><i class="fa-regular fa-moon"></i></div>
                    <div class="fill-current dark:hidden"><i class="fa-regular fa-sun"></i></div>
                </button>
            </div>
        </div>
    </div>
    @include('components.utils.toasts')
    <script type="module">
        import { DotLottie } from "https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-web/+esm";

        const canvas = document.getElementById('dotlottie-canvas');

        new DotLottie({
            autoplay: true,
            loop: true,
            canvas: canvas,
            src: "{{ asset('assets/img/login.lottie') }}",
            mode: 'forward',
            speed: 1,
            backgroundColor: 'transparent'
        });
    </script>
</body>


</html>