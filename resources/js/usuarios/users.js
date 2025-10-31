import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal.js";

let table = null;
const form = document.getElementById('usuario-form');
const dForm = document.getElementById('delete-form');

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/usuarios');
        const data = await response.json();

        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener usuarios');
            return;
        }

        renderTable(data.data);

    } catch (err) {
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
        console.log(err);
    } finally {
        SPINNER.hideSpinner();
    }
}

function renderTable(users) {
    if (table) {
        table.destroy();
        table = null;
    }

    const tbUsuarios = document.getElementById('tbUsuarios');
    tbUsuarios.innerHTML = '';

    const usuariosHtml = users.map(user => (
        `<tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${user.name}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${user.email}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${user.created_at}</span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center gap-2">
                    <i x-on:click="form.id = '${user.id}'; form.name = '${user.name}'; form.email = '${user.email}'; showForm = true;" class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer dark:text-gray-400"></i>
                    <i x-on:click="${user.facturas_count > 0 ? "$dispatch('notify', { variant: 'warning', title: 'No se puede eliminar', message: 'El usuario seleccionado tiene facturas creadas, no se puede eliminar'}); showDeleteForm = false;" : `form.id = '${user.id}'; dTitle ='${user.name}'; showDeleteForm = true;`}" class="fa-solid fa-trash text-sm text-gray-600 hover:text-red-400 cursor-pointer dark:text-gray-400"></i>
                </div>
            </td>
        </tr>
        `
    )).join('');

    tbUsuarios.innerHTML = usuariosHtml;
    table = new DataTable('#tUsuarios', {
        searchable: true,
    });
}

async function submitForm(e) {
    try {
        e.preventDefault();

        SPINNER.showSpinner();

        const proxyForm = e.detail;

        if(!proxyForm) {
            toast.show('error', 'Error', 'Error al enviar el formulario');
            console.error(e.detail);
            return;
        }

        const csrf = document.querySelector('input[name="_token"]')?.getAttribute('value');

        if (!csrf) {
            toast.show('error', 'Error', 'Token CSRF no encontrado.');
            return;
        }

        const formData = {};

        for (const [key, value] of Object.entries(proxyForm)) {
            formData[key] = value || '';
        }

        const id = formData.id || null;

        const response = await fetch(id ? `/actions/usuarios/${id}` : '/actions/usuarios', {
            method: id ? 'PUT' : 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json; charset=UTF-8'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (response.status === 400) {
            const message = data.message.title || data.message || 'Existió un error al guardar el usuario.';
            toast.show('error', 'Error', message, data.message.errors || []);
            return;
        }

        if (response.status !== 200 || !data.success) {
            const message = data.message.title || data.message || 'Existió un error al guardar el usuario.';
            toast.show('error', 'Error', message, data.message.errors || []);
            return;
        }

        toast.show('success', 'Éxito', data.message);

        showAll();

    } catch (err) {
        toast.show('error', 'Error', 'Error inesperado al procesar el formulario');
        console.log(err);
    } finally {
        SPINNER.hideSpinner();
    }
}

function init() {
    showAll();

    if (form) {
        document.addEventListener('submit-user-form', submitForm);
    }

    if (dForm) {
        dForm.addEventListener('submit', (e) => (e.preventDefault(), remove(e, dForm, SPINNER, '/actions/usuarios/', showAll)));
    }
}

document.addEventListener('DOMContentLoaded', init);