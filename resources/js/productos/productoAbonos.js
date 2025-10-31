import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal";

let table = null;
const form = document.getElementById('abono-form');
const dForm = document.getElementById('delete-form')

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/abonos');
        const data = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener abonos');
            return;
        }
        renderAbonos(data.data);
    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
        console.error(err); 
    }
}

function renderAbonos(abonos) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbAbonos = document.getElementById('tbAbonos');

    tbAbonos.innerHTML = '';

    const escapeForAlpine = str => JSON.stringify(str).replace(/"/g, "'");

    const abonosHtml = abonos.map(abono => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${abono.nombre}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${abono.numero_entradas}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${abono.costo_total}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">
                    <i x-on:click="form.id = '${abono.id}'; form.nombre = '${abono.nombre}'; form.numero_entradas = ${abono.numero_entradas}; form.costo_total = ${abono.costo_total}; form.descripcion = ${escapeForAlpine(abono.descripcion)}; form.estado = ${abono.estado ? true : false}; form.mostrar_en_web = ${abono.mostrar_en_web ? true : false}; showForm = true;" class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>
                    <i x-on:click="${abono.productos.length > 0 ? "$dispatch('notify', { variant: 'warning', title: 'No se puede eliminar', message: 'El abono seleccionado tiene productos asignados, no se puede eliminar'}); showDeleteForm = false;" : `form.id = '${abono.id}'; dTitle ='${abono.nombre}'; showDeleteForm = true;`}" class="fa-solid fa-trash text-sm text-gray-600 hover:text-red-400 cursor-pointer dark:text-gray-400"></i>
                </div>
            </td>
        </tr>
        `
    )).join('');

    tbAbonos.innerHTML = abonosHtml;
    table = new DataTable('#tAbonos', {
        searchable: true,
    });
}

async function store(e) {
    try {
        e.preventDefault();

        SPINNER.showSpinner();

        const id = form.querySelector('#id').value.trim();
        const nombre = form.querySelector('#nombre').value.trim();
        const numero_entradas = form.querySelector('#numero_entradas').value.trim();
        const costo_total = form.querySelector('#costo_total').value.trim();
        const descripcion = form.querySelector('#descripcion').value.trim();
        const estado = form.querySelector('#estado').checked;
        const mostrar_en_web = form.querySelector('#mostrar_en_web').checked;

        const formData = { nombre, numero_entradas, costo_total, descripcion, estado, mostrar_en_web };

        const csrfToken = document.querySelector('[name="_token"]').getAttribute('value');

        const urlToFetch = id ? `/actions/abonos/${id}` : '/actions/abonos';

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
            toast.show('error', 'Error', data.message.title || 'Error al guardar', data.message.errors || []);
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
    dForm.addEventListener('submit', (e) => (e.preventDefault(), remove(e, dForm, SPINNER, '/actions/abonos/', showAll)));
};

document.addEventListener('DOMContentLoaded', init);