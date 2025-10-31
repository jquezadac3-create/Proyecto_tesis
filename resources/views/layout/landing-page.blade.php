<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        @yield('title', config('app.name'))
    </title>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/fontawesome.min.css') }}">

    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css">
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script>

    <script defer src="{{ asset('assets/js/toast.js') }}"></script>

    @yield('assets')

    <style>
        :root {
            --amarillo: #FFD700;
            --amarillo-claro: #FFF9C4;
            --amarillo-oscuro: #E6C300;
            --negro: #1A1A1A;
            --negro-suave: #333333;
            --gris: #78909C;
            --gris-claro: #ECEFF1;
            --gris-oscuro: #546E7A;
            --acento: #FF6D00;
            --sombra: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .fade-in {
            animation: fadeIn 0.3s;
        }

        .slide-up {
            animation: slideUp 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .ticket-counter {
            display: flex;
            align-items: center;
            margin: 8px 0;
        }

        .ticket-counter button {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--gris-claro);
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }

        .ticket-counter button:hover {
            background: var(--amarillo);
        }

        .ticket-counter span {
            margin: 0 10px;
            min-width: 30px;
            text-align: center;
        }

        .day-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: var(--amarillo);
            color: var(--negro);
            font-size: 0.8rem;
            margin-right: 8px;
            font-weight: bold;
        }

        /* ESTILOS GENERALES */
        body {
            background-color: var(--gris-claro);
            color: var(--negro-suave);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* HEADER */

        .logo h1 {
            color: var(--amarillo);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* BOTONES */
        .btn {
            background-color: var(--amarillo);
            color: var(--negro);
            transition: all 0.3s;
            font-weight: bold;
        }

        .btn:hover {
            background-color: var(--amarillo-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* TARJETAS */
        .feature-card,
        .tournament-card {
            background: white;
            border-top: 4px solid var(--amarillo);
            transition: all 0.3s;
        }

        .feature-card:hover,
        .tournament-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            background-color: var(--amarillo-claro);
            color: var(--negro);
        }

        /* MODALES */
        .modal-content {
            background: white;
            border-top: 5px solid var(--amarillo);
        }

        input:focus {
            border-color: var(--amarillo);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }

        /* ESTADOS */
        .status-open {
            background-color: #E8F5E9;
            color: #2E7D32;
        }

        .status-closed {
            background-color: #FFEBEE;
            color: #C62828;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 font-sans">
    <!-- HEADER -->
    <header class="bg-white shadow-md sticky top-0 z-10">
        <div class="container mx-auto flex items-center justify-between p-2">
            <h1 class="flex gap-2 text-2xl font-bold text-yellow-600"><img class="w-10 "
                    src="{{ asset('assets/img/logo_responsive.png') }}" alt=""> BARRABAS CLUB</h1>

            <nav class="hidden space-x-2 md:flex">
                <a href="https://www.instagram.com/club_barrabas?igsh=MXVjam12YmQwMTg3dA=="
                    class="text-2xl bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 text-transparent bg-clip-text hover:opacity-80">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.facebook.com/profile.php?id=100057305835749&mibextid=wwXIfr&mibextid=wwXIfr" class="text-2xl text-blue-600 hover:text-blue-800">
                    <i class="fab fa-facebook"></i>
                </a>
                {{-- <a href="#" class="text-2xl text-green-500 hover:text-green-700">
                    <i class="fab fa-whatsapp"></i>
                </a> --}}
            </nav>
        </div>
    </header>

    @yield('content')

    <!-- FOOTER -->
    <footer class="bg-gray-900 py-6 text-center text-gray-300 mt-10">
        <p>© 2025 Barrabas Club - Todos los derechos reservados</p>
    </footer>
</body>

</html>