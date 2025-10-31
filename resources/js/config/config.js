import * as SPINNER from "@utils/spinner.js";
import { toSnackeCase } from "@utils/utils.js";

async function store(e) {
    try {
        e.preventDefault();

        const proxyForm = e.detail.form;

        if (!proxyForm) {
            toast.show('error', 'Error', 'Error al enviar el formulario');
            return;
        }

        SPINNER.showSpinner();

        const csrf = document.querySelector('input[name="_token"]')?.getAttribute('value');

        if (!csrf) {
            toast.show('error', 'Error', 'Token CSRF no encontrado.');
            return;
        }

        const formData = new FormData();

        const id = proxyForm.id || null;

        for (const [key, value] of Object.entries(proxyForm)) {
            if (key !== 'firmaElectronica' && key !== 'logo') {
                const keyName = toSnackeCase(key);
                formData.append(keyName, value || '');
            }
        }

        if (proxyForm.firmaElectronica) {
            formData.append('firma_electronica', proxyForm.firmaElectronica);
        }

        if (proxyForm.logo) {
            formData.append('logo', proxyForm.logo);
        }

        const response = await fetch(id ? `/actions/configuracion/${id}` : '/actions/configuracion', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            },
            body: formData
        });

        const data = await response.json();

        if (response.status === 400) {
            const message = data.message.title || data.message || 'Existió un error al guardar la configuración.';
            toast.show('error', 'Error', message, data.message.errors || []);
            return;
        }

        if (!response.ok || !data.success) {
            const message = data.message.title || data.message || 'Existió un error al guardar la configuración.';
            toast.show('error', 'Error', message, data.message.errors || []);
            return;
        }

        toast.show('success', 'Éxito', data.message || 'Configuración guardada correctamente');

    } catch (err) {
        toast.show('error', 'Error', err);
    } finally {
        SPINNER.hideSpinner();
    }
}

document.addEventListener('valid-form-submitted', store);