import { driver } from "driver.js";
import "driver.js/dist/driver.css";
import Swal from "sweetalert2";

const driverObj = driver({
    doneBtnText: 'Hecho',
    closeBtnText: 'Cerrar',
    nextBtnText: 'Siguiente',
    prevBtnText: 'Anterior',
    showProgress: true,
    progressText: "{{current}} de {{total}}",
    steps: [
        {
            element: '#mainContent',
            popover: {
                title: 'Bienvenido a nuestra Página para la compra de Boletos',
                description: 'Esta guía te ayudará a familiarizarte con las funcionalidades principales de esta sección.',
                side: 'bottom',
                align: 'center'
            },

        },
        {
            element: '#partidos',
            popover: {
                title: 'Jornadas Disponibles',
                description: 'Aquí puedes explorar las diferentes jornadas disponibles para comprar tus boletos. Selecciona la jornada que te interese para ver los partidos programados.',
                align: 'center'
            }
        },
        {
            element: '#abonos',
            popover: {
                title: 'Abonos Disponibles',
                description: 'Si estás interesado en asistir a múltiples jornadas, considera adquirir un abono. Los abonos ofrecen acceso a varios partidos a un precio reducido.',
                align: 'center'
            }
        },
        {
            element: '#buyContent',
            popover: {
                title: 'Instrucción Adicional',
                description: 'Ten en cuenta que solo puedes comprar un máximo de 2 entradas por persona. Asegúrate de completar correctamente tus datos personales al finalizar la compra.',
                side: 'top',
                align: 'center'
            }
        },
    ],

    onDestroyed: async () => {
        await Swal.fire({
            text: '¿Deseas ver esta guía de manera automática al ingresar en esta página en el futuro?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, mostrar',
            cancelButtonText: 'No, gracias',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-lg shadow-lg p-6 border border-gray-200 bg-white',
                title: 'text-lg font-semibold text-gray-800',
                htmlContainer: 'text-sm text-gray-600',
                confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-400',
                cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-gray-300',
                actions: 'flex justify-end gap-3 mt-4',
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.setItem('showWelcomeBuyGuide', 'true');
            } else if (result.isDismissed) {
                localStorage.setItem('showWelcomeBuyGuide', 'false');
            }
        });

        driverObj.destroy();

        // Removed focus on 'search-input' as the element may not exist on this page.
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const showGuide = localStorage.getItem('showWelcomeBuyGuide');
    if (!showGuide || showGuide === 'true') {
        driverObj.drive();
    }
    document.getElementById('help-btn')?.addEventListener('click', showWelcomeGuide);
});

function showWelcomeGuide() {
    driverObj.refresh();
    driverObj.drive();
}