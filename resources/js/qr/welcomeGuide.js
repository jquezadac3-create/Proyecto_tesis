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
            element: '#header-title-qr',
            popover: {
                title: 'Bienvenido al Escáner de Códigos QR',
                description: 'Aquí puedes puedes escanear códigos QR para buscar información rápidamente.',
                side: 'bottom',
                align: 'center'
            },

        },
        {
            element: '#control-buttons',
            popover: {
                title: 'Escanear con la Cámara',
                description: 'Usa estos botones para iniciar o detener el escaneo con la cámara de tu dispositivo. Deberás permitir el acceso a la cámara cuando se te solicite.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '#manual-input',
            popover: {
                title: 'Entrada QR',
                description: 'Alternativamente, puedes introducir el código QR manualmente aquí o utilizar un escáner físico. No necesitas activar la cámara para este proceso.',
                side: 'bottom',
                align: 'center'
            }
        },
        {
            element: '#manual-input',
            popover: {
                title: 'Instrucción Adicional',
                description: 'Enfoca el escáner en el código QR y este se ingresará automáticamente en el campo, pero, asegúrate que este campo esté seleccionado para poder escanear.',
            }
        },
    ],

    onDestroyed: async () => {
        const showGuide = localStorage.getItem('showWelcomeGuideQR');

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
                localStorage.setItem('showWelcomeGuideQR', 'true');
            } else if (result.isDismissed) {
                localStorage.setItem('showWelcomeGuideQR', 'false');
            }
        });

        driverObj.destroy();

        document.getElementById('search-input')?.focus();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const showGuide = localStorage.getItem('showWelcomeGuideQR');
    if (!showGuide || showGuide === 'true') {
        driverObj.drive();
    }
    document.getElementById('help-btn')?.addEventListener('click', showWelcomeGuide);
});

function showWelcomeGuide() {
    driverObj.refresh();
    driverObj.drive(1);
}