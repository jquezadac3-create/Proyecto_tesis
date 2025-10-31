async function remove(event, dForm, SPINNER, urlToFetch, showAll) {
    if (!dForm) return;

    try {
        event.preventDefault();

        SPINNER.showSpinner();

        const id = dForm.querySelector('#id').value.trim();

        if (!id) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'ID no válido');
            return;
        }

        const csrfToken = dForm.querySelector('[name="_token"]').getAttribute('value');

        const response = await fetch(`${urlToFetch}${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json; charset=UTF-8',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (response.status !== 200 || !data.success) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', data.message || 'Ocurrió un error al realizar al intentar eliminar.');
            return;
        }

        SPINNER.hideSpinner();
        toast.show('success', 'Éxito', data.message);

        showAll();
    } catch (err) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Ocurrió un error inesperado al eliminar.');
    }
}


export default remove;