export function showSpinner() {
    document.dispatchEvent(new CustomEvent('loading', { detail: { loading: true } }));
}

export function hideSpinner() {
    document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
}
