import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";

let tableGanadores = null;
let tablePerdedores = null;


// Carga de la pag
document.addEventListener('DOMContentLoaded', () => {
    cargarSorteos();

    const btnBuscar = document.getElementById('btnBuscarBoletos');
    btnBuscar.addEventListener('click', buscarSorteo);
});


// Cargar los sorteos realizados
async function cargarSorteos() {
     SPINNER.showSpinner(); 
    try {
        const response = await fetch('/actions/sorteos/obtenerSorteos');
        const sorteos = await response.json();

        const select = document.getElementById('selectPeriodo');

        sorteos.forEach(s => {
            const option = document.createElement('option');
            option.value = s.id;
            option.textContent = `${s.nombre} - ${s.periodo_nombre}`;
            select.appendChild(option);
        });

    } catch (error) {
        console.error('Error cargando los sorteos:', error);
    } finally {
        SPINNER.hideSpinner(); 
    }
}


async function buscarSorteo() {
    const select = document.getElementById('selectPeriodo');
    const sorteoId = select.value;

    if (!sorteoId) {
        toast.show('error', 'Error', 'Por favor seleccione un sorteo');
        return;
    }

    SPINNER.showSpinner();

    try {
        // Llamamos al endpoint que devuelve los datos del sorteo
        const response = await fetch(`/actions/sorteos/datosSorteo/${sorteoId}`);
        const data = await response.json();

        if (!data.success) {
            console.error("Error al obtener sorteo:", data.message);
            return;
        }

        // Mostrar panel y tablas ocultas
        document.getElementById('panelSorteo').classList.remove('hidden');
        document.getElementById('tablaGanadores').classList.remove('hidden');
        document.getElementById('tablaPerdedores').classList.remove('hidden');

        // Actualizar panel del sorteo
        const s = data.sorteo;
        document.getElementById('sorteoPeriodo').textContent = s.periodo_nombre;
        document.getElementById('sorteoNombre').textContent = s.nombre;
        document.getElementById('sorteoPremios').textContent = s.num_premios;
        document.getElementById('sorteoPosicion').textContent = s.posicion_ganadora;
        document.getElementById('sorteoFecha').textContent = new Date(s.created_at).toLocaleDateString();

        // --- Render boletos ganadores ---
        if (tableGanadores) {
            tableGanadores.destroy();
            tableGanadores = null;
        }

        const tbGanadores = document.getElementById('tbBoletosGanadores');
        tbGanadores.innerHTML = '';

        const ganadoresMapped = data.boletos_ganadores.map(b => {
            return `
                <tr>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.numero_factura}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.numero_boleto}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.nombre_cliente}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.premio_ganado}</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbGanadores.innerHTML = ganadoresMapped.join('');
        tableGanadores = new DataTable('#tBoletosGanadores', { searchable: true, perPage: 10 });

        // --- Render boletos perdedores ---
        if (tablePerdedores) {
            tablePerdedores.destroy();
            tablePerdedores = null;
        }

        const tbPerdedores = document.getElementById('tbBoletosPerdedores');
        tbPerdedores.innerHTML = '';

        const perdedoresMapped = data.boletos_perdedores.map(b => {
            return `
                <tr>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.numero_factura}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.numero_boleto}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.nombre_cliente}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        <div class="flex items-center justify-center">
                            <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${b.nombre_producto || '-'}</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbPerdedores.innerHTML = perdedoresMapped.join('');
        tablePerdedores = new DataTable('#tBoletosPerdedores', { searchable: true, perPage: 10 });

    } catch (error) {
        console.error('Error consultando el sorteo:', error);
    } finally {
        SPINNER.hideSpinner();
    }
}