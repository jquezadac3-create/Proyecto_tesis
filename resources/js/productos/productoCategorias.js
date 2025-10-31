import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal";

let table = null;
const form = document.getElementById('categoria-form');
const dForm = document.getElementById('delete-form')

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/categoria-productos');
        const data = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener categorías');
            return;
        }
        renderCategories(data.data);
    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
    }
}

function renderCategories(categories) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbCategorias = document.getElementById('tbCategorias');

    tbCategorias.innerHTML = '';

    const categorias = categories.map(ct => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${ct.nombre}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">
                    <i x-on:click="form.id = '${ct.id}'; form.nombre = '${ct.nombre}'; showForm = true;" class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>
                    <i x-on:click="form.id = '${ct.id}'; dTitle = '${ct.nombre}'; showDeleteForm = true;" class="fa-solid fa-trash-can text-sm text-gray-600 hover:text-red-500 cursor-pointer dark:text-gray-400"></i>
                </div>
            </td>
        </tr>
        `
    )).join('');

    tbCategorias.innerHTML = categorias;
    table = new DataTable('#tCategorias', {
        searchable: true,
    });
}

async function store(e) {
    try {
        e.preventDefault();

        SPINNER.showSpinner();

        const id = form.querySelector('#id').value.trim();

        const nombre = form.querySelector('#nombre').value.trim();
        const formData = { nombre };

        const csrfToken = document.querySelector('[name="_token"]').getAttribute('value');

        const urlToFetch = id ? `/actions/categoria-productos/${id}` : '/actions/categoria-productos';

        const response = await fetch(urlToFetch, {
            method: id ? 'PATCH' : 'POST',
            headers: {
                'Content-Type': 'application/json; charset=UTF-8',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al guardar');
            return;
        }

        toast.show('success', 'Éxito', data.message);

        showAll();
    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Error inesperado');
    }
}

function init() {
    showAll();
    form.addEventListener('submit', store);
    dForm.addEventListener('submit', (e) => (e.preventDefault(), remove(e, dForm, SPINNER, '/actions/categoria-productos/', showAll)));
};

document.addEventListener('DOMContentLoaded', init);