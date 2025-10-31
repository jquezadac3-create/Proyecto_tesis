import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal";

let table = null;
const form = document.getElementById('jornada-form');
const dForm = document.getElementById('delete-form');

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/jornadas');
        const data = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener las jornadas');
            return;
        }

        renderJornadas(data.data);

    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
    }
}

function renderJornadas(jornadas) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbJornadas = document.getElementById('tbJornadas');

    tbJornadas.innerHTML = '';

    const jornadasMapped = jornadas.map(jr => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${jr.nombre}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${jr.fecha_inicio}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${jr.fecha_fin}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${jr.cantidad_aforo}</span>
                </div>

            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">

                    <i x-on:click="form.id = '${jr.id}'; form.nombre = '${jr.nombre}'; form.fecha_inicio = '${jr.fecha_inicio}'; form.fecha_fin = '${jr.fecha_fin}'; form.aforo = '${jr.cantidad_aforo}'; form.estado = ${jr.estado === 'activa'}; showForm = true; $dispatch('set-dates')" class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>
                    <i x-on:click="form.id = '${jr.id}'; dTitle = 'Jornada'; showDeleteForm = true" class="fa-solid fa-trash-can text-sm text-gray-600 hover:text-red-500 cursor-pointer dark:text-gray-400"></i>

                </div>
            </td>
        </tr>
        `
    )).join('');

    tbJornadas.innerHTML = jornadasMapped;
    table = new DataTable('#tJornadas', {
        searchable: true
    });
}

async function store(e) {
    try {
        e.preventDefault();

        SPINNER.showSpinner();

        const id = form.querySelector('#id').value.trim();

        const nombre = form.querySelector('#nombre').value.trim();
        const fecha_inicio = form.querySelector('#fecha_inicio')?.value?.trim();
        const fecha_fin = form.querySelector('#fecha_fin')?.value?.trim();
        const cantidad_aforo = parseInt(form.querySelector('#aforo').value.trim());
        const estado = form.querySelector('#toggleSuccess').checked ? 'activa' : 'inactiva';

        const formData = {
            nombre, fecha_inicio, fecha_fin, cantidad_aforo, estado
        }
        console.log(formData)
        const csrfToken = form.querySelector('[name="_token"]').getAttribute('value');

        const urlToFetch = id ? `/actions/jornadas/${id}` : '/actions/jornadas';

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

        if (response.status === 404) {
            toast.show('error', 'Error', data.message);
            return;
        }

        if (response.status == 400) {
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
        console.log(err);
    }
}

function init() {
    showAll();
    form.addEventListener('submit', store);
    dForm.addEventListener('submit', (e) => remove(e, dForm, SPINNER, '/actions/jornadas/', showAll));
}

document.addEventListener('DOMContentLoaded', init);