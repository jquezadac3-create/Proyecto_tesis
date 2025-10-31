import * as SPINNER from "@utils/spinner.js";
import { toSnackeCase } from "@utils/utils.js";
import { phoneInput } from "@utils/phoneNumber.js";
import remove  from "@utils/deleteModal.js";
import { DataTable } from "simple-datatables";

let phoneIti = null;
let table = null;
const form = document.getElementById('client-form');
const dForm = document.getElementById('delete-form');

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/clientes');
        const data = await response.json();

        if (response.status !== 200) {
            toast.show('error', 'Error', data.message || 'Error al obtener los clientes.');
            return;
        }

        renderCategories(data.data);

    } catch(err) {
        toast.show('error', 'Error', 'Ocurrió un error al mostrar los clientes.');
        console.error(err);
    } finally {
        SPINNER.hideSpinner();
    }
}

function renderCategories(clients) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbClientes = document.getElementById('tbClientes');

    tbClientes.innerHTML = '';

    const clientes = clients.map(cl => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.nombres ?? ''} ${cl.apellidos ?? ''}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400 capitalize">${cl.tipo_identificacion}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.numero_identificacion}</span>
                </div>
            </td>
            <td hidden class="px-6 py-3 whitespace-nowrap hidden">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.direccion}</span>
                </div>
            </td>
            <td hidden class="px-6 py-3 whitespace-nowrap hidden">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.telefono}</span>
                </div>
            </td>
            <td hidden class="px-6 py-3 whitespace-nowrap hidden">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.email}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.estadisticas.tiene_abono || false ? 'Sí' : 'No'}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${cl.estadisticas.cantidad_total || 0}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2 uppercase">
                    <i x-on:click="setData('${cl.id}', '${cl.nombres}', '${cl.apellidos}', '${cl.tipo_identificacion}', '${cl.numero_identificacion}', '${cl.direccion}', '${cl.telefono}', '${cl.email}', '${cl.abono}', '${cl.entradas}'); setStats(${cl.estadisticas.tiene_abono}, ${cl.estadisticas.cantidad_usada}, ${cl.estadisticas.total_abono}, ${cl.estadisticas.entradas_normales}); showForm = true;" class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>
                    <i x-on:click="form.id = '${cl.id}'; dTitle = '${cl.nombres ?? ''} ${cl.apellidos ?? ''}'; showDeleteForm = true;" class="fa-solid fa-trash-can text-sm text-gray-600 hover:text-red-500 cursor-pointer dark:text-gray-400"></i>
                </div>
            </td>
        </tr>
        `
    )).join('');

    tbClientes.innerHTML = clientes;
    table = new DataTable('#tClientes', {
        searchable: true,
    })
}

async function store(e) {
    try {

        e.preventDefault();

        const proxyForm = e.detail.form;

        if (!proxyForm) {
            toast.show('error', 'Error', 'Ocurrió un error al enviar el formulario');
            return;
        }

        SPINNER.showSpinner();

        const csrf = form.querySelector('input[name="_token"]')?.getAttribute('value');

        if (!csrf) {
            toast.show('error', 'Error', 'Token CSRF no encontrado.');
            return;
        }

        const formData = {};

        const id = proxyForm.id || null;

        for (const [key, value] of Object.entries(proxyForm)) {
            const keyName = toSnackeCase(key);
            if (key === 'telefono') {
                const phoneNumber = phoneIti.getNumber();
                formData[keyName] = phoneNumber || value;
                continue;
            }
            formData[keyName] = value || '';
        }

        const response = await fetch(id ? `/actions/clientes/${id}` : '/actions/clientes', {
            method: id ? 'PUT' : 'POST',
            headers: {
                'X-CSRF-TOKEN' : csrf,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.status !== 200) {
            toast.show('error', 'Error', data.message || 'Ocurrió un error al intentar guardar el cliente');
            return;
        }

        if (!data.success) {
            toast.show('error', 'Error', data.message.title || 'Ocurrió un error al intentar guardar al cliente.', data.message.errors);
            return;
        }

        toast.show('success', 'Éxito', data.message);
        showAll();
    } catch (err) {
        toast.show('error', 'Error', 'Ocurrió un error al intentar guardar al cliente.');
    } finally {
        SPINNER.hideSpinner();
    }
}

function init(e) {
    e.preventDefault();

    phoneIti = phoneInput('phone', 'phoneError');

    showAll();

    dForm.addEventListener('submit', (e) => (e.preventDefault(), remove(e, dForm, SPINNER, '/actions/clientes/', showAll)));
}

document.addEventListener('save-client', store);
document.addEventListener('DOMContentLoaded', init);