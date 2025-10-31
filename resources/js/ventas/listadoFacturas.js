import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";

let table = null;

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/facturas');

        const json = await response.json();

        renderTable(json.data);

    } catch(err) {
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
        console.error(err);
    } finally {
        SPINNER.hideSpinner();
    }
}

function renderTable(facturas) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbFacturas = document.getElementById('tbFacturas');

    tbFacturas.innerHTML = '';

    const facturasMapped = facturas.map(fc => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${fc.secuencia_factura.padStart(9, 0)}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${fc.fecha}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${parseFloat(fc.total_factura).toFixed(2)}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${fc.nombres ?? ''} ${fc.apellidos ?? ''}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="inline-flex items-center rounded-full ${fc.estado_autorizacion === 'AUTORIZADO' ? 'bg-green-100 text-green-800' : fc.estado_autorizacion === 'PENDIENTE' ? 'bg-yellow-100 text-yellow-800' :'bg-red-100 text-red-800'} px-3 py-1 text-sm font-medium">${fc.estado_autorizacion ?? 'PENDIENTE'}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">
                    <a href="/ventas/factura/${fc.id}">
                        <i class="fa-solid fa-eye text-sm text-gray-600 hover:text-blue-400 cursor-pointer dark:text-gray-400"></i>
                    </a>
                </div>
            </td>
        </tr>
        `
    ));

    tbFacturas.innerHTML = facturasMapped;
    table = new DataTable('#tFacturas', {
        searchable: true
    });
}

async function init() {
    showAll();
}

document.addEventListener('DOMContentLoaded', init);