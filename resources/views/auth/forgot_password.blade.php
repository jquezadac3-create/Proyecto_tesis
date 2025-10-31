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
                                class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                                ¿Has olvidado tu contraseña?
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Ingresa la dirección de correo electrónico vinculada a tu cuenta y te enviaremos
                                un enlace para restablecer tu contraseña.
                            </p>
                        </div>
                        <div>
                            <form>
                                <div class="space-y-5">
                                    <!-- Email -->
                                    <div>
                                        <label
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Email<span class="text-error-500">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" placeholder="Ingresa tu email"
                                            class="dark:bg-dark-900 font-noraml shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-left text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                    </div>

                                    <!-- Button -->
                                    <div>
                                        <button
                                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                            Enviar enlace de restablecimiento
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="mt-5">
                                <p
                                    class="text-center text-sm font-normal text-gray-700 sm:text-start dark:text-gray-400">
                                    Espera, recuerdo mi contraseña...
                                    <a href="{{ route('auth.login') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">Haz
                                        clic aquí</a>
                                </p>
                            </div>
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
            src: "{{ asset('assets/img/forgot_password.lottie') }}",
            mode: 'forward',
            speed: 1,
            backgroundColor: 'transparent'
        });
    </script>
</body>


</html>