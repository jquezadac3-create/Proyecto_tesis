import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import * as XLSX from "xlsx";

let table = null;
let datosCargados = false;

document.addEventListener("DOMContentLoaded", () => {
    const btnBuscar = document.getElementById("btnBuscarVentas");
    const fechaInicio = document.querySelector("#fechaInicio");
    const fechaFin = document.querySelector("#fechaFin");
    const btnDescargar = document.getElementById("btnDescargarExcel");

    // Buscar mis ingresos
    if (btnBuscar) {
        btnBuscar.addEventListener("click", () => {
            console.log("click");
            const inicio = fechaInicio.value;
            const fin = fechaFin.value;

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

            fetch("/actions/facturas/ventasProductos", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify({
                    fechaInicio: inicio,
                    fechaFin: fin
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);

                if (table) {
                    table.destroy();
                    table = null;
                }

                const tbVentaProductos = document.getElementById("tbVentaProductos");

                // Limpiar tbody
                tbVentaProductos.innerHTML = '';

                if (!data || data.length === 0) {
                    datosCargados = false;
                    tbVentaProductos.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron registros
                            </td>
                        </tr>`;
                } else {
                    datosCargados = true;
                    renderVentaProductos(data); 
                }
            })
            .catch(err => {
                console.error("Error cargando movimientos de caja:", err);
                toast.show('error', 'Error', 'Ocurrió un problema al obtener los movimientos.');
            })
            .finally(() => SPINNER.hideSpinner());
        });
    }

    // Manejo del boton descargar excel
    if (btnDescargar) {
        btnDescargar.addEventListener("click", () => {
            if (!datosCargados) {
                toast.show('error', 'Error', 'No hay datos para exportar. Primero realice una búsqueda.');
                return;
            }
            // Exportar dato excel
            exportarExcel();
        });
    }
});


// Parsear fecha para comparar
function parseDate(str) {
    const [day, month, year] = str.split("/");
    return new Date(`${year}-${month}-${day}T00:00:00`);
}


function renderVentaProductos(ventas) {
    const tbVentaProductos = document.querySelector("#tbVentaProductos");

    // Destruir DataTable si ya existe
    if (table) {
        table.destroy();
        table = null;
    }

    // Limpiar el tbody antes de agregar nuevas filas
    tbVentaProductos.innerHTML = '';

    let rows = '';

    if (!ventas || ventas.length === 0) {
        // Si no hay datos
        datosCargados = false;
        rows = `
            <tr>
                <td colspan="6" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron registros
                </td>
            </tr>
        `;
    } else {
        // Si hay datos
        datosCargados = true;

        rows = ventas.map(v => `
            <tr>
                <td class="px-6 py-3">${v.producto_id || ''}</td>
                <td class="px-6 py-3">${v.categoria || ''}</td>
                <td class="px-6 py-3">${v.nombre || ''}</td>
                <td class="px-6 py-3">${v.nombre_extra || ''}</td>
                <td class="px-6 py-3">${v.cantidad || 0}</td>
                <td class="px-6 py-3">${v.precio_unitario || '0.00'}</td>
                <td class="px-6 py-3">${v.total || '0.00'}</td>
            </tr>
        `).join('');
    }

    // Renderizar filas
    tbVentaProductos.innerHTML = rows;

    // Inicializar DataTable solo si hay datos
    if (datosCargados) {
        table = new DataTable('#tVentaProductos', { searchable: true });
    }
}

// Exportar los datos a Excel
// function exportarExcel() {
//     const table = document.getElementById("tVentaProductos"); 
//     if (!table) {
//         console.error("No se encontró la tabla con id 'tVentaProductos'");
//         return;
//     }

//     const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
//     const fechaFin = document.querySelector("#fechaFin")?.value || '';

//     const fechaInicioFormatted = fechaInicio.replace(/\//g, '-');
//     const fechaFinFormatted = fechaFin.replace(/\//g, '-');

//     const fileName = `ventaProductos (${fechaInicioFormatted} / ${fechaFinFormatted}).xlsx`;

//     const wb = XLSX.utils.book_new();
//     const ws = XLSX.utils.table_to_sheet(table); 
//     XLSX.utils.book_append_sheet(wb, ws, "Ventas");

//     XLSX.writeFile(wb, fileName);
    
// }

import ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';

async function exportarExcel() {
    const table = document.getElementById("tVentaProductos");
    if (!table) {
        console.error("No se encontró la tabla con id 'tVentaProductos'");
        return;
    }

    const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
    const fechaFin = document.querySelector("#fechaFin")?.value || '';

    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Ventas");

    // Estilos
    const headerFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2563EB' } }; // azul
    const centerAlignment = { vertical: 'middle', horizontal: 'center' };

    // Fila 1: Título
    ws.mergeCells('A1:G1'); // Ajusta según número de columnas
    const titleCell = ws.getCell('A1');
    titleCell.value = "REPORTE DE VENTAS";
    titleCell.fill = headerFill;
    titleCell.alignment = centerAlignment;
    titleCell.font = { bold: true, size: 16, color: { argb: 'FFFFFFFF' } }; // letras blancas

    // Fila 2: Fechas
    ws.mergeCells('A2:G2');
    const dateCell = ws.getCell('A2');
    dateCell.value = (fechaInicio === fechaFin) ? `${fechaInicio}` : `Desde: ${fechaInicio}  Hasta: ${fechaFin}`;
    dateCell.alignment = centerAlignment;

    // Fila 3 vacía
    ws.addRow([]);

    // Fila 4: Encabezados
    const headers = [];
    table.querySelectorAll('thead tr th').forEach(th => {
        headers.push(th.innerText);
    });
    const headerRow = ws.addRow(headers);

    headerRow.eachCell(cell => {
        cell.fill = headerFill;
        cell.font = { bold: true, color: { argb: 'FFFFFFFF' } }; // letras blancas
        cell.alignment = centerAlignment;
        cell.border = {
            top: { style: 'thin' },
            bottom: { style: 'thin' },
            left: { style: 'thin' },
            right: { style: 'thin' }
        };
    });

    // Filas de datos
    table.querySelectorAll('tbody tr').forEach(tr => {
        const rowData = [];
        tr.querySelectorAll('td').forEach(td => {
            rowData.push(td.innerText);
        });
        ws.addRow(rowData);
    });

    // Ajustar ancho de columnas automáticamente
    ws.columns.forEach(column => {
        let maxLength = 10;
        column.eachCell({ includeEmpty: true }, cell => {
            const val = cell.value ? cell.value.toString() : '';
            if (val.length > maxLength) maxLength = val.length;
        });
        column.width = maxLength + 5;
    });

    // Descargar archivo
    const buffer = await wb.xlsx.writeBuffer();
    const fileName = `ventaProductos (${fechaInicio.replace(/\//g, '-')}_${fechaFin.replace(/\//g, '-')}).xlsx`;
    saveAs(new Blob([buffer]), fileName);
}

