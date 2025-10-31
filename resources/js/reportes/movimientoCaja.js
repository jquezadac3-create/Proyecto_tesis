import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";

let table = null;
let datosCargados = false;

document.addEventListener("DOMContentLoaded", () => {
    const selectFormaPago = document.getElementById("selectFormaPago");
    const btnBuscar = document.getElementById("btnBuscar");
    const fechaInicio = document.querySelector("#fechaInicio");
    const fechaFin = document.querySelector("#fechaFin");

    // Cargar formas de pago al inicio
    if (selectFormaPago) {
        SPINNER.showSpinner();
        fetch("/actions/facturas/formasPago")
            .then(res => res.json())
            .then(data => {
                selectFormaPago.innerHTML = '<option value="">Todas las formas de pago</option>';
                data.forEach(fp => {
                    const option = document.createElement("option");
                    option.value = fp.codigo;
                    option.textContent = fp.forma_pago;
                    selectFormaPago.appendChild(option);
                });
            })
            .catch(err => console.error("Error cargando formas de pago:", err))
            .finally(() => SPINNER.hideSpinner());
    }

    // Buscar mis ingresos
    if (btnBuscar) {
        btnBuscar.addEventListener("click", () => {
            const inicio = fechaInicio.value;
            const fin = fechaFin.value;
            const formaPago = selectFormaPago.value;

            if (!inicio) {
                toast.show('error', 'Error', 'Debe seleccionar la fecha de inicio.');
                return;
            }
            if (!fin) {
                toast.show('error', 'Error', 'Debe seleccionar la fecha de fin.');
                return;
            }
            if (parseDate(inicio) > parseDate(fin)) {
                toast.show('error', 'Error', 'La fecha de inicio no puede ser mayor que la fecha fin.');
                return;
            }

            SPINNER.showSpinner();

            fetch("/actions/facturas/movimientosCaja", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify({
                    fechaInicio: inicio,
                    fechaFin: fin,
                    formaPago: formaPago
                })
            })
            .then(res => res.json())
            .then(data => {
                if (table) {
                    table.destroy();
                    table = null;
                }

                const tbMovCaja = document.getElementById("tbMovCaja");
                const totalesContainer = document.getElementById("totalesFormaPago");
                const tarjetas = {
                    "16": document.getElementById("tarjetaDebito"),
                    "19": document.getElementById("tarjetaCredito"),
                    "01": document.getElementById("tarjetaSinFinanciero"),
                    "20": document.getElementById("tarjetaOtrosFinanciero")
                };
                // Limpiar tbody
                tbMovCaja.innerHTML = '';

                if (!data || data.length === 0) {
                    datosCargados = false;
                    tbMovCaja.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron registros
                            </td>
                        </tr>`;
                    Object.values(tarjetas).forEach(t => t.classList.add("hidden"));
                    Object.values(tarjetas).forEach(t => t.querySelector("span[id^='total']").textContent = "0.00");
                    totalesContainer.classList.add("hidden");
                } else {
                    datosCargados = true;
                    renderMovimientosCaja(data, formaPago); // Aquí se volverá a crear la tabla si hay datos
                }
            })
            .catch(err => {
                console.error("Error cargando movimientos de caja:", err);
                toast.show('error', 'Error', 'Ocurrió un problema al obtener los movimientos.');
            })
            .finally(() => SPINNER.hideSpinner());
        });
    }
});

// Parsear fecha para comparar
function parseDate(str) {
    const [day, month, year] = str.split("/");
    return new Date(`${year}-${month}-${day}T00:00:00`);
}

function renderMovimientosCaja(movimientos, formaPagoSeleccionada) {
    const tbMovCaja = document.querySelector("#tbMovCaja");
    const totalesContainer = document.getElementById("totalesFormaPago");

    // Destruir la tabla si existe
    if (table) {
        table.destroy();
        table = null;
    }

    // Limpiar el tbody antes de agregar nuevas filas
    tbMovCaja.innerHTML = '';

    let rows = '';
    const datosCargadosTabla = movimientos && movimientos.length > 0;

    // Mapeo de tarjetas
    const tarjetas = {
        "16": document.getElementById("tarjetaDebito"),
        "19": document.getElementById("tarjetaCredito"),
        "01": document.getElementById("tarjetaSinFinanciero"),
        "20": document.getElementById("tarjetaOtrosFinanciero")
    };

    // Inicializar totales
    const totales = { "16": 0, "19": 0, "01": 0, "20": 0 };

    if (!datosCargadosTabla) {
        rows = `
            <tr>
                <td colspan="7" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron registros
                </td>
            </tr>
        `;
    } else {
        // Ordenar por fecha descendente
        movimientos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

        // Generar filas de la tabla
        rows = movimientos.map(mov => `
            <tr>
                <td class="px-6 py-3">${mov.usuario || ''}</td>
                <td class="px-6 py-3">${mov.fecha || ''}</td>
                <td class="px-6 py-3">${mov.tipo || 'Ingreso'}</td>
                <td class="px-6 py-3">${mov.valor || '0.00'}</td>
                <td class="px-6 py-3">${mov.detalle || ''}</td>
                <td class="px-6 py-3">${mov.cliente || ''}</td>
                <td class="px-6 py-3">${mov.forma_pago || ''}</td>
            </tr>
        `).join('');

        // Calcular totales por forma de pago
        movimientos.forEach(mov => {
            if(totales.hasOwnProperty(mov.codigo_forma_pago)){
                if (mov.status != 'anulada') {
                    totales[mov.codigo_forma_pago] += parseFloat(mov.valor) || 0;
                }
            }
        });
    }

    // Renderizar filas en la tabla
    tbMovCaja.innerHTML = rows;

    // Mostrar tarjetas y actualizar valores
    let mostrarTotales = false;
    Object.keys(tarjetas).forEach(codigo => {
        if (formaPagoSeleccionada === "" || formaPagoSeleccionada === codigo) {
            if (totales[codigo] > 0) {
                mostrarTotales = true;
            }
            tarjetas[codigo].classList.remove("hidden");
            tarjetas[codigo].querySelector("span[id^='total']").textContent = totales[codigo].toFixed(2);
        } else {
            tarjetas[codigo].classList.add("hidden");
            tarjetas[codigo].querySelector("span[id^='total']").textContent = "0.00";
        }
    });

    // Mostrar u ocultar contenedor de totales según si hay datos
    if(mostrarTotales){
        totalesContainer.classList.remove("hidden");
    } else {
        totalesContainer.classList.add("hidden");
    }

    // Inicializar DataTable solo si hay datos
    if (datosCargadosTabla) {
        table = new DataTable('#tMovCaja', { searchable: true });
    }
}

