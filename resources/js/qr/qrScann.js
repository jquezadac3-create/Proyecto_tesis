import { Html5QrcodeScanner, Html5QrcodeScanType } from "html5-qrcode";
import * as SPINNER from "@utils/spinner.js";

// Elements
const resultsList = document.getElementById('results-list');
const noResults = document.getElementById('no-results');
const searchForm = document.getElementById('form-search');
const searchFormAuto = document.getElementById('form-search-auto');
const startBtn = document.getElementById('start-btn');
const stopBtn = document.getElementById('stop-btn');
const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');
const cameraPlaceholder = document.getElementById('camera-placeholder');
const readerElement = document.getElementById('reader');
const clearSection = document.getElementById('clear-section');
const clearBtn = document.getElementById('clear-btn');
const searchInput = document.getElementById('search-input');

// Scanner configuration
const config = {
    fps: 10,
    // qrbox: { width: 350, height: 350 },
    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
    rememberLastUsedCamera: true,
    showTorchButtonIfSupported: true
}

let html5QrCode = null;
let isScanning = false;
let searchInProgress = false;
let hasAbono = false;
let timeout = null;

// Variables for hardware scanner detection
let scannerBuffer = '';
let scannerTimeout = null;
let lastKeyTime = 0;

function initQRScanner() {
    setupEventListeners();
    updateStatus('idle', 'Listo para escanear - Usa la cámara o scanner físico');
}

function setupEventListeners() {
    // Start scanner button
    startBtn?.addEventListener('click', startScanner);

    // Stop scanner button
    stopBtn?.addEventListener('click', stopScanner);

    // Manual search form
    searchForm?.addEventListener('submit', searchSubmit);

    // Auto search form (for scanner results)
    searchFormAuto?.addEventListener('submit', searchSubmit);

    // Clear results button
    clearBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        clearResults();
        document.dispatchEvent(new Event('resultscleared'));
    });

    // Hardware scanner detection
    setupHardwareScannerListeners();
}

async function startScanner() {
    try {
        updateStatus('active', 'Iniciando cámara...');
        startBtn.disabled = true;

        // Hide placeholder and show reader
        if (cameraPlaceholder) cameraPlaceholder.style.display = 'none';
        if (readerElement) readerElement.style.display = 'block';

        // Initialize scanner
        html5QrCode = new Html5QrcodeScanner('reader', config, false);
        html5QrCode.render(onScanSuccess, onScanError);

        // Update UI
        isScanning = true;
        startBtn.style.display = 'none';
        stopBtn.style.display = 'block';
        stopBtn.disabled = false;

        if (readerElement) readerElement.classList.add('scanning');
        updateStatus('active', 'Escáner activo - Apunta la cámara al código QR');

    } catch (error) {
        console.error('Error starting scanner:', error);
        updateStatus('error', 'Error al iniciar la cámara');
        resetButtons();
        toast.show('error', 'Error', 'No se pudo acceder a la cámara. Verifica los permisos.');
    }
}

async function stopScanner() {
    try {
        if (html5QrCode) {
            await html5QrCode.clear();
            html5QrCode = null;
        }

        isScanning = false;

        // Show placeholder and hide reader
        if (cameraPlaceholder) cameraPlaceholder.style.display = 'flex';
        if (readerElement) {
            readerElement.classList.remove('scanning');
            readerElement.style = 'min-height: 300px;';
        }

        resetButtons();
        updateStatus('idle', 'Escáner detenido');

    } catch (error) {
        console.error('Error stopping scanner:', error);
        updateStatus('error', 'Error al detener el escáner');
    }
}

function resetButtons() {
    startBtn.style.display = 'block';
    startBtn.disabled = false;
    stopBtn.style.display = 'none';
    stopBtn.disabled = true;
}

function updateStatus(type, message) {
    if (!statusIndicator || !statusText) return;

    // Remove all status classes
    statusIndicator.classList.remove('status-active', 'status-error', 'status-idle');

    // Add appropriate class
    statusIndicator.classList.add(`status-${type}`);
    statusText.textContent = message;
}

function onScanSuccess(decodedText, decodedResult) {
    if (!searchFormAuto) {
        toast.show('error', 'Alerta', 'Ocurrió un error inesperado. Inténtalo nuevamente.');
        return;
    }

    const inputField = searchFormAuto.querySelector('#search-input-auto');

    if (!inputField) {
        toast.show('error', 'Alerta', 'Ocurrió un error inesperado. Inténtalo nuevamente.');
        return;
    }

    // Update status
    updateStatus('active', 'Código QR detectado - Procesando...');

    // Add visual feedback
    if (readerElement) {
        readerElement.style.borderColor = '#10b981';
        setTimeout(() => {
            if (readerElement) readerElement.style.borderColor = '#e5e7eb';
        }, 1000);
    }

    if (inputField.value) {
        updateStatus('active', 'Si necesitas escanear un nuevo código QR, limpia los resultados anteriores');
        return;
    }

    inputField.value = decodedText;
    searchFormAuto.requestSubmit();
}

function onScanError(error) {
    // Don't show error for routine scanning attempts
    // Only log to console for debugging
    if (error && !error.includes('NotFoundException')) {
        console.warn('QR scan error:', error);
    }
}

function clearResults(cleanTimeout = false) {
    if (cleanTimeout && timeout) {
        clearTimeout(timeout);
        timeout = null;
    }
    if (resultsList) resultsList.innerHTML = '';
    if (noResults) noResults.classList.remove('hidden');
    if (resultsList) resultsList.classList.add('hidden');
    if (clearSection) clearSection.classList.add('hidden');

    updateStatus('idle', isScanning ? 'Escáner activo - Apunta la cámara al código QR' : 'Listo para escanear - Usa la cámara o scanner físico');
}

async function searchSubmit(event) {
    event.preventDefault();

    if (searchInProgress) {
        return;
    }

    const inputField = document.getElementById('search-input');
    inputField.readOnly = true;
    try {

        clearResults(true);
        document.dispatchEvent(new Event('resultscleared'));

        searchInProgress = true;
        SPINNER.showSpinner();
        updateStatus('active', 'Buscando información...');

        const form = event.target;
        const csrf = form.querySelector('input[name="_token"]')?.getAttribute('value');

        if (!csrf) {
            toast.show('error', 'Error', 'Token CSRF no encontrado.');
            return;
        }

        const formData = {};

        // Get input value from either form
        const inputField = form.querySelector('#search-input') || form.querySelector('#search-input-auto');
        formData['search'] = inputField?.value || '';

        if (!formData['search'].trim()) {
            toast.show('error', 'Error', 'Por favor introduce un código QR válido.', null, true);
            return;
        }

        const response = await fetch(`/actions/qr/${formData.search}`, {
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
            },
        });

        const data = await response.json();

        if (response.status !== 200) {
            toast.show('error', 'Error', data.message || 'Ocurrió un error al intentar buscar el código QR.', null, true);
            updateStatus('error', 'Error en la búsqueda');
            return;
        }

        if (!data.success) {
            toast.show('error', 'Error', data.message || 'Ocurrió un error al intentar buscar el código QR.', null, true);
            updateStatus('error', 'Código QR no válido');
            return;
        }

        if (data.modified) {
            toast.show('success', 'Éxito', 'El código QR ha sido procesado correctamente.', null, true);
        }

        const success = data.discounted ?? false;
        const reason = data.reason ?? null;
        renderQrDetails(data.data, success, reason);
        hasAbono = data.abonoDiscounted ?? false;
        updateStatus('active', 'Información encontrada');
        toast.show(success ? 'success' : 'error', success ? 'Éxito' : 'Error', data.message || (success ? 'Código QR válido' : 'Código QR no válido'), null, true);

        // Show clear button
        if (clearSection) clearSection.classList.remove('hidden');

        SPINNER.hideSpinner();

        /**
         * Esperar minimamente de 1 - 2 segundos para que pueda buscar otro codigo
         */
        await new Promise(resolve => setTimeout(resolve, 1500));

        /**
         * Permitir la búsqueda de un nuevo código QR casi enseguida solo si el resultado actual es un ticket normal,
         * caso contrario (abono) debe esperar mas tiempo o limpiar manualmente (hacer click en aceptar)
         */
        if (!hasAbono) {
            searchInput.readOnly = false;
            inputField.value = '';
            searchInProgress = false;
            searchInput.focus();
            timeout = setTimeout(() => {
                clearResults();
                document.dispatchEvent(new Event('resultscleared'));
            }, 5000);
            return;
        }

        /**
         * El resultado obtenido dentro de 30 segundos se removerá automáticamente
         */
        await new Promise(resolve => setTimeout(resolve, 30000));

        clearResults();
        document.dispatchEvent(new Event('resultscleared'));
        searchInput.focus();
    } catch (err) {
        toast.show('error', 'Error', 'Ocurrió un error inesperado. Inténtalo nuevamente.', null, true);
        console.error(err);
        if (inputField) inputField.value = '';
        hasAbono = false;
        updateStatus('error', 'Error inesperado');
    } finally {
        SPINNER.hideSpinner();
        searchInProgress = false;
        inputField.value = '';
    }
}

function renderQrDetails(data, discounted = false, reason = null) {
    const qrData = data.qr_code_data;
    const invoiceDate = data.invoiceDate;

    if (!qrData || !invoiceDate) {
        noResults.classList.remove('hidden');
        resultsList.classList.add('hidden');
        return;
    }

    noResults.classList.add('hidden');
    resultsList.classList.remove('hidden');
    resultsList.innerHTML = '';

    const fecha = new Date(invoiceDate.fecha ?? Date.now());
    const formateada = new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(fecha);

    const htmlItems = qrData.items.map(item => `<div class="product-card bg-gray-50 rounded-xl p-3 mb-4 border border-gray-100">
            <div class="flex items-center space-x-6">
                <div class="hidden sm:block sm:flex-shrink-0">
                    <div class="w-15 h-15 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas text-2xl text-white ${item.jornada_id ? 'fa-ticket' : 'fa-layer-group'}"></i>
                    </div>
                </div>
                <div class="flex-grow capitalize">
                    <h3 class="text-md font-semibold text-gray-800 mb-2">${item.factura_nombre}</h3>
                    ${item.fecha_str ? `<p class="text-gray-600 mb-2 text-sm">${item.fecha_str}</p>` : ''}
                    ${item.abono_id ? `<p class="text-gray-600 mb-2 text-sm">Último uso: ${item.last_updated}</p>` : ''}
                    <p class="text-gray-600 mb-2 text-sm">${item.producto}</p>
                    <div class="flex items-center space-x-4">
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-2xl text-xs font-medium text-center">
                            Cantidad adquirida: ${item.cantidad_inicial}
                        </span>
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-2xl text-xs font-medium text-center">
                            Cantidad restante: ${item.cantidad_restante}
                        </span>
                    </div>
                </div>
            </div>
        </div>`).join('');

    const { title, subtitle } = renderBanner(discounted, reason);

    const htmlContent = `
    <section class="gradient-bg">
        <!-- Success Header -->
            <div class="text-center mb-4 slide-in-up">
                <h1 class="text-xl font-bold mb-2">${title}</h1>
                <p class="text-md ${!discounted ? 'text-red-400 font-bold' : ''}">${subtitle}</p>
            </div>

        <!-- Main Content Container -->
        <div class="max-w-6xl mx-auto">
            <!-- Quick Info Cards -->
            <div class="flex flex-col gap-4 mb-4">
                <div class="border-2 rounded-2xl p-6 slide-in-left" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                        <h4 class="text-md font-semibold">${invoiceDate.cliente.nombres ?? 'CONSUMIDOR'} ${invoiceDate.cliente.apellidos ?? 'FINAL'}</h4>
                    </div>
                    <div class="space-y-2 text-sm">
                        <p><i class="fas fa-id-card mr-2"></i>${invoiceDate.cliente.numero_identificacion ?? '9999999999999'} </p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>${invoiceDate.cliente.direccion ?? 'N/A'}</p>
                    </div>
                </div>

                <!-- <div class="border-2 rounded-2xl p-6 slide-in-left" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-white/20 p-3 rounded-xl">
                            <i class="fas fa-receipt text-2xl"></i>
                        </div>
                        <h3 class="text-md font-semibold">Factura # ${invoiceDate.secuencia_factura.padStart(9, '0')}</h3>
                    </div>
                    <p class="text-sm">${formateada}</p>
                </div> -->
            </div>

            <!-- Products Section -->
            <div class="bg-white rounded-2xl border-2 overflow-hidden slide-in-up" style="animation-delay: 0.4s;">
                <div class="bg-gradient-to-r p-4">
                    <h2 class="text-xl font-bold flex items-center">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        Productos Adquiridos
                    </h2>
                    <p class="mt-1">${qrData.items.length} artículo/s en tu pedido</p>
                </div>

                <div class="px-3 sm:px-6">
                    ${htmlItems ?? '<p class="text-gray-500">No se encontraron productos.</p>'}
                </div>
            </div>
        </div>
    </section>
    `;

    document.dispatchEvent(new Event('result'));

    resultsList.innerHTML = htmlContent;
}

function renderBanner(discounted = false, reason = null) {
    const title = discounted ? '¡Acceso Permitido!' : 'Entrada No Disponible';

    let subtitle = 'Ya no tienes entradas disponibles';

    if (discounted) {
        subtitle = 'Tu entrada ha sido validada y se ha descontado correctamente. ¡Buen viaje!';
    } else {
        switch (reason) {
            case 'jornada-inactiva':
                subtitle = 'La jornada asociada está inactiva. No se puede validar tu entrada.';
                break;
            case 'abono-inactivo':
                subtitle = 'El abono está inactivo. No se puede aplicar el descuento.';
                break;
            case 'abono-fuera-de-fecha':
                subtitle = 'El abono no es válido en esta fecha. No se puede aplicar el descuento.';
                break;
            case 'abono-hoy-usado':
                subtitle = 'El abono ya fue utilizado hoy. No se puede aplicar el descuento.';
                break;
            case 'sin-cantidad':
            case 'cantidad':
                subtitle = 'Ya no tienes entradas disponibles para esta jornada o abono.';
                break;
            case 'desconocido':
                subtitle = 'No se pudo validar tu entrada por un motivo desconocido.';
                break;
        }
    }

    return { title, subtitle };
}

// Hardware Scanner Support Functions
function setupHardwareScannerListeners() {
    // Listen for keydown events to detect scanner input
    document.addEventListener('keydown', handleScannerInput);

    // Listen for paste events (some scanners simulate paste)
    document.addEventListener('paste', handlePasteInput);
}

function handleScannerInput(event) {
    // If user is typing in an input field, don't interfere
    if (document.activeElement &&
        (document.activeElement.tagName === 'INPUT' ||
            document.activeElement.tagName === 'TEXTAREA')) {
        return;
    }

    // If a search is already in progress, ignore
    if (searchInProgress) {
        return;
    }

    const currentTime = Date.now();

    // Check if this is rapid input (typical of scanners)
    const timeBetweenKeys = currentTime - lastKeyTime;
    lastKeyTime = currentTime;

    // Handle special keys
    if (event.key === 'Enter') {
        // Enter key often signals end of scanner input
        if (scannerBuffer.length > 5) { // QR codes are typically longer
            processScannerInput(scannerBuffer.trim());
        }
        scannerBuffer = '';
        return;
    }

    // Ignore special keys that aren't printable characters
    if (event.key.length > 1 && event.key !== 'Enter') {
        return;
    }

    // Add character to buffer
    scannerBuffer += event.key;

    // Clear existing timeout
    clearTimeout(scannerTimeout);

    // Set timeout to process buffer if no more input comes
    scannerTimeout = setTimeout(() => {
        if (scannerBuffer.length > 5) { // Minimum length for QR codes
            processScannerInput(scannerBuffer.trim());
        }
        scannerBuffer = '';
    }, 100); // 100ms pause indicates end of scanner input
}

function handlePasteInput(event) {
    // If user is pasting in an input field, don't interfere
    if (document.activeElement &&
        (document.activeElement.tagName === 'INPUT' ||
            document.activeElement.tagName === 'TEXTAREA')) {
        return;
    }

    // If a search is already in progress, ignore
    if (searchInProgress) {
        return;
    }

    // Prevent default paste behavior
    event.preventDefault();

    // Get pasted text
    const pastedText = (event.clipboardData || window.clipboardData).getData('text');

    if (pastedText && pastedText.length > 5) {
        processScannerInput(pastedText.trim());
    }
}

function processScannerInput(scannedCode) {
    // Validate the scanned code
    if (!scannedCode || scannedCode.length < 6) {
        return;
    }

    // Update status to show scanner detection
    updateStatus('active', 'Scanner físico detectado - Procesando código...');

    // Visual feedback
    if (readerElement) {
        readerElement.style.borderColor = '#10b981';
        setTimeout(() => {
            if (readerElement) readerElement.style.borderColor = '#e5e7eb';
        }, 1000);
    }

    // Use the auto form to process the scanned code
    const inputField = searchFormAuto?.querySelector('#search-input-auto');
    if (inputField && searchFormAuto) {
        // Clear any previous value
        inputField.value = '';

        // Set the scanned code
        inputField.value = scannedCode;

        // Submit the form
        searchFormAuto.requestSubmit();
    } else {
        toast.show('error', 'Error', 'Error interno del sistema. Inténtalo nuevamente.');
    }
}


// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initQRScanner();
});