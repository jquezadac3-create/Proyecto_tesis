<style>
    .loader-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    /* Spinner con animaciones */
    .soccer-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .ball {
        width: 120px;
        /* más grande */
        height: 120px;
        background-image: url('https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball.svg');
        background-size: cover;
        background-position: center;
        animation: pulse 1s infinite ease-in-out, rotate 2s linear infinite, bounce 1.5s infinite ease-in-out;
    }

    /* Animación de pulso */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    /* Animación de rotación */
    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Animación de rebote */
    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-15px);
        }
    }
</style>

<div x-data="{ loading: false }" @loading.document="loading = $event.detail.loading" :class="{ 'hidden': !loading }" class="h-full w-full items-center justify-center">
    <div class="loader-container">
        <div class="soccer-loader">
            <div class="ball"></div>
            <p class="mt-5 text-[20px]">Cargando...</p>
        </div>
    </div>
</div>
