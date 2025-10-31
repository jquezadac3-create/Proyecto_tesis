import Swal from 'sweetalert2';
import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";

let table = null;

// Btn importar datos excel
document.getElementById("archivo").addEventListener("change", async function() {
    const form = document.getElementById("formImportarExcel");
    const archivo = this.files[0];

    if (!archivo) return;
    console.log(archivo)
    // Preparar datos
    let formData = new FormData(form);
    formData.append("archivo", archivo);
    formData.append("_token", document.querySelector('input[name="_token"]').value);
    try {
        SPINNER.showSpinner();

        console.log("Subiendo archivo...");

        let response = await fetch('/actions/sorteos/exportarDatos/facturas', {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            }
        });

        let result = await response.json();
        console.log("Respuesta del backend:", result);

        if (response.status !== 200 || !result.success) {
            SPINNER.hideSpinner();

            // Construir mensaje
            let mensaje = '';
            if (result.message && result.message.errors) {
                mensaje = result.message.errors.join('<br>'); // separa errores con <br> para SweetAlert
            } else if (typeof result.message === 'string') {
                mensaje = result.message;
            } else {
                mensaje = 'Error al importar el archivo';
            }

            await Swal.fire({
                icon: 'error',
                title: result.message.title,
                html: mensaje,  // usa html para soportar <br>
                confirmButtonText: 'Cerrar'
            });

            return;
        }
        
        // Éxito
        SPINNER.hideSpinner();
        await Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: result.message || 'Archivo importado correctamente ✅',
            confirmButtonText: 'Aceptar'
        });
        cargarFacturas();


    } catch (error) {
        SPINNER.hideSpinner();
        console.error("Error en la petición:", error);
        await Swal.fire({
            icon: 'error',
            title: 'Error inesperado',
            text: 'Ocurrió un error inesperado al importar el archivo',
            confirmButtonText: 'Cerrar'
        });
    }
});


// Función para cargar las facturas desde el backend
async function cargarFacturas() {
    SPINNER.showSpinner();

    try {
        const response = await fetch('/actions/sorteos/obtenerDatosFacturas'); // endpoint del backend
        const result = await response.json();

        if (response.status !== 200 || !result.success) {
            let mensaje = result.message || 'Error al obtener las facturas';
            if (Array.isArray(mensaje)) {
                mensaje.forEach(m => toast.show('error', 'Error', m));
            } else {
                toast.show('error', 'Error', mensaje);
            }
            return;
        }

        // Renderizar datos en la tabla
        renderTablaFacturas(result.data);

    } catch (error) {
        console.error("Error al cargar las facturas:", error);
        toast.show('error', 'Error', 'Ocurrió un error al cargar las facturas');
    } finally {
        SPINNER.hideSpinner();
    }
}

// Función para renderizar datos en la tabla #tDatosFactura
function renderTablaFacturas(facturas) {
    console.log(facturas)
    if (table) {
        table.destroy();
        table = null;
    }

    const tb = document.getElementById('tbDatosFactura');
    tb.innerHTML = ''; // limpiar tabla

    if (!facturas || facturas.length === 0) {
        tb.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron registros
                </td>
            </tr>
        `;
        return;
    }

    // Llenar tabla con los datos
    console.log(facturas)
    facturas.forEach(f => {
        tb.innerHTML += `
            <tr>
                <td class="px-6 py-3 whitespace-nowrap text-center">${f.numero_factura}</td>
                <td class="px-6 py-3 whitespace-nowrap text-center">${f.nombre}</td>
                <td class="px-6 py-3 whitespace-nowrap text-center">${f.cantidad}</td>
                <td class="px-6 py-3 whitespace-nowrap text-center">${f.nombre_periodo ?? f.periodo?.nombre ?? ''}</td>
                <td class="px-6 py-3 whitespace-nowrap text-center">${f.nombre_producto ?? ''}</td>
            </tr>
        `;
    });
    // nombre_producto
    // nombre_jornada
    // nombre_abono
    // Inicializar DataTable
   
    table = new DataTable('#tDatosFactura', { searchable: true });
}


// Ejecutar al cargar la ventana
document.addEventListener('DOMContentLoaded', () => {
    cargarFacturas();
});

// Comentar luego de actualizar los datos
const btnGenerarBoletos = document.getElementById('btnGenerarBoletos');
const btnActualizarDatos = document.getElementById('btnActualizarDatos');

if (btnGenerarBoletos) btnGenerarBoletos.style.display = 'none';
if (btnActualizarDatos) btnActualizarDatos.style.display = 'none';

// Btn Actualizar Datos
document.getElementById('btnActualizarDatos').addEventListener('click', async function() {
    const confirmar = await Swal.fire({
        icon: 'warning',
        title: 'Confirmar actualización',
        text: '¿Deseas actualizar los datos de facturas y generar los boletos?',
        showCancelButton: true,
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar',
    });

    if (!confirmar.isConfirmed) return; // Si canceló, no hacer nada

    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/sorteos/actualizarDatos', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !result.ok) {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.msg || 'Ocurrió un error al actualizar los datos'
            });
            return;
        }

        await Swal.fire({
            icon: 'success',
            title: 'Actualización completada',
            text: result.msg || 'Los datos se actualizaron correctamente ✅'
        });

        // Recargar tabla de facturas
        cargarFacturas();

    } catch (error) {
        SPINNER.hideSpinner();
        console.error("Error al actualizar datos:", error);
        await Swal.fire({
            icon: 'error',
            title: 'Error inesperado',
            text: 'Ocurrió un error al ejecutar la actualización'
        });
    }
});



// Btn Generar Boletos
document.getElementById('btnGenerarBoletos').addEventListener('click', async function() {
    const confirmar = await Swal.fire({
        icon: 'warning',
        title: 'Confirmar generación',
        text: '¿Deseas generar los boletos del sorteo?',
        showCancelButton: true,
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar',
    });

    if (!confirmar.isConfirmed) return; // Si canceló, no hacer nada

    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/sorteos/generarBoletos', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        SPINNER.hideSpinner();

        if (response.status !== 200 || !result.ok) {
            await Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.msg || 'Ocurrió un error al generar los boletos'
            });
            return;
        }

        await Swal.fire({
            icon: 'success',
            title: 'Boletos generados',
            text: result.msg || 'Los boletos se generaron correctamente 🎟️'
        });

        // Si quieres recargar la tabla de facturas después
        cargarFacturas();

    } catch (error) {
        SPINNER.hideSpinner();
        console.error("Error al generar boletos:", error);
        await Swal.fire({
            icon: 'error',
            title: 'Error inesperado',
            text: 'Ocurrió un error al generar los boletos'
        });
    }
});