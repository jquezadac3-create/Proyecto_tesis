import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal";

let table = null;
const form = document.getElementById('periodo-form');
const dForm = document.getElementById('delete-form');


async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/periodo-campeonato');
        const data = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener los periodos de las jornadas');
            return;
        }
        console.log(data.data)
        renderPeriodoCampeonato(data.data);

    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
    }
}


function renderPeriodoCampeonato(periodos){
    // Destruye la tabla si ya existe
    if (table) {
        table.destroy();
        table = null;
    }

    const tbPeriodos = document.getElementById('tbPeriodos');
    tbPeriodos.innerHTML = '';

    const colorMapStatus = {
        'activo': { bg: 'bg-emerald-400', text: 'text-white' }, 
        'inactivo': { bg: 'bg-red-500', text: 'text-white' }, 
        'finalizado': { bg: 'bg-gray-400', text: 'text-white' }      
    };

    const periodosMapped = periodos.map(p => {
        const color = colorMapStatus[p.status] || { bg: 'bg-gray-400', text: 'text-white' };

        return `
            <tr>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${p.id}</span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${p.nombre}</span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${p.fecha_inicio}</span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${p.fecha_fin}</span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium ${color.text} ${color.bg} px-4 py-1.5 rounded-full">
                            ${p.status}
                        </span>
                    </div>
                </td>

                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center gap-2">
                        <i x-on:click="
                            form.id = '${p.id}'; 
                            form.nombre = '${p.nombre}';
                            form.fecha_inicio = '${p.fecha_inicio}';
                            form.fecha_fin = '${p.fecha_fin}';
                            showFormPeriodo = true;
                            $dispatch('set-dates')
                        " class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>

                        <i x-on:click="
                            form.id = '${p.id}';
                            dTitle = 'Periodo';
                            showDeleteForm = true
                        " class="fa-solid fa-trash-can text-sm text-gray-600 hover:text-red-500 cursor-pointer dark:text-gray-400"></i>
                    </div>
                </td>
            </tr>
        `;
    });

    tbPeriodos.innerHTML = periodosMapped;

    // Inicializa DataTable
    table = new DataTable('#tPeriodos', {
        searchable: true
    });
}


async function storePeriodo(e) {
    try {
        e.preventDefault();

        SPINNER.showSpinner();

        // Obtener ID si es edición
        const id = document.querySelector('#id').value.trim();

        // Obtener valores del formulario
        const nombre = document.querySelector('#nombre').value.trim();
        const fecha_inicio = document.querySelector('#fecha_inicio')?.value?.trim();
        const fecha_fin = document.querySelector('#fecha_fin')?.value?.trim();

        const formData = { nombre, fecha_inicio, fecha_fin };

        const csrfToken = document.querySelector('[name="_token"]').value;

        // Definir URL según si es creación o edición
        const urlToFetch = id ? `/actions/periodo-campeonato/${id}` : '/actions/periodo-campeonato';

        const response = await fetch(urlToFetch, {
            method: id ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json;charset=UTF-8',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        SPINNER.hideSpinner();
        SPINNER.hideSpinner();

        if (response.status === 404) {
            toast.show('error', 'Error', data.message);
            return;
        }

        if (response.status == 400) {
            // Mostrar lista de errores de validación
            toast.show('error', 'Error', data.message.error, data.message.list);
            return;
        }

        if (!data.success) {
            toast.show('error', 'Error', data.message);
            return;
        }

        toast.show('success', 'Éxito', data.message);

        showAll();
    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Algo salió mal.');
        console.error(err);
    }
}



function init() {
    showAll();
    form.addEventListener('submit', storePeriodo);
    dForm.addEventListener('submit', (e) => remove(e, dForm, SPINNER, '/actions/periodo-campeonato/', showAll));
}


document.addEventListener('DOMContentLoaded', init);