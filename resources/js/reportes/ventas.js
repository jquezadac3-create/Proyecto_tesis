import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import * as XLSX from "xlsx";
import ExcelJS from 'exceljs';
import { saveAs } from "file-saver";

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

            fetch("/actions/facturas/reporteVentas", {
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

                const tbVentas = document.getElementById("tbVentas");

                // Limpiar tbody
                tbVentas.innerHTML = '';

                if (!data || data.length === 0) {
                    datosCargados = false;
                    tbVentas.innerHTML = `
                        <tr>
                            <td colspan="19" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                                No se encontraron registros
                            </td>
                        </tr>`;
                } else {
                    datosCargados = true;
                    window.ventasData = data;
                    renderVentasTabla(data); 
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

// Renderizar la tabla de reporte de ventas
function renderVentasTabla(ventas) {
    const tbVentas = document.getElementById("tbVentas");

    // Destruir DataTable si ya existe
    if (table) {
        table.destroy();
        table = null;
    }

    // Limpiar tbody
    tbVentas.innerHTML = '';

    // Si no hay datos
    if (!ventas || ventas.length === 0) {
        datosCargados = false;
        tbVentas.innerHTML = `
            <tr>
                <td colspan="19" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron registros
                </td>
            </tr>`;
        return;
    }

    // Si hay datos
    datosCargados = true;

    // Construir filas
    let rows = ventas.map(v => `
        <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
            <td class="px-6 py-3 text-center">${v.documento || ''}</td>
            <td class="px-6 py-3 text-center">${v.fecha || ''}</td>
            <td class="px-6 py-3 text-center">${v.emitido_a || ''}</td>
            <td class="px-6 py-3 text-center">${v.secuencia_factura || ''}</td>
            <td class="px-6 py-3 text-center">${v.subtotal15 || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.subtotal0 || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.iva15 || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.descuento || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.total_factura || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.ruc_cliente || ''}</td>
            <td class="px-6 py-3 text-center">${v.estado_factura || ''}</td>
            <td class="px-6 py-3 text-center">${v.descuento_iva15 || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.descuento_iva0 || '0.00'}</td>
            <td class="px-6 py-3 text-center">${v.serie || ''}</td>
            <td class="px-6 py-3 text-center">${v.autorizacion || ''}</td>
            <td class="px-6 py-3 text-center">${v.caducidad || ''}</td>
            <td class="px-6 py-3 text-center">${v.forma_pago || ''}</td>
            <td class="px-6 py-3 text-center">${v.vendedor || ''}</td>
            <td class="px-6 py-3 text-center">${v.codigo_sustento || ''}</td>
        </tr>
    `).join('');

    // Renderizar filas
    tbVentas.innerHTML = rows;

    // Inicializar DataTable si hay datos
    if (datosCargados) {
        table = new DataTable('#tVentas', { searchable: true, paging: true, info: true });
    }
}

// Exportar los datos a Excel
// function exportarExcel() {
//     const table = document.getElementById("tVentas");
//     if (!table) {
//         console.error("No se encontró la tabla con id 'tVentas'");
//         return;
//     }

//     // Obtener fechas del filtro para el nombre del archivo
//     const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
//     const fechaFin = document.querySelector("#fechaFin")?.value || '';

//     const fechaInicioFormatted = fechaInicio.replace(/\//g, '-');
//     const fechaFinFormatted = fechaFin.replace(/\//g, '-');

//     const fileName = `Ventas (${fechaInicioFormatted} - ${fechaFinFormatted}).xlsx`;

//     // Crear libro y hoja de Excel
//     const wb = XLSX.utils.book_new();
//     const ws = XLSX.utils.table_to_sheet(table);
//     XLSX.utils.book_append_sheet(wb, ws, "Ventas");

//     // Guardar archivo
//     XLSX.writeFile(wb, fileName);
// }


async function exportarExcel() {
    const ventas = window.ventasData; 
    if (!ventas || ventas.length === 0) {
        console.error("No hay datos para exportar.");
        return;
    }

    const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
    const fechaFin = document.querySelector("#fechaFin")?.value || '';

    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Ventas");

    const headerFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2563EB' } }; // azul claro
    const centerAlignment = { vertical: 'middle', horizontal: 'center' };
    const totalFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFF99' } }; // amarillo claro

    // Fila 1: título
    ws.mergeCells('A1:S1');
    const titleCell = ws.getCell('A1');
    titleCell.value = 'REPORTE DE VENTAS';
    titleCell.fill = headerFill;
    titleCell.alignment = centerAlignment;
    titleCell.font = { bold: true, size: 20, color: { argb: 'FFFFFFFF' } };

    // Fila 2: fechas
    ws.mergeCells('A2:S2');
    const dateCell = ws.getCell('A2');
    dateCell.value = (fechaInicio === fechaFin) ? `${fechaInicio}` : `Desde: ${fechaInicio}  Hasta: ${fechaFin}`;
    dateCell.alignment = centerAlignment;

    // Fila 3 vacía
    ws.addRow([]);

    // Fila 4: encabezados
    const headers = [
        "Documento","Fecha","Emitido A","# Factura","Sub. 15%","Sub. 0%","Iva 15%",
        "Descuento","Total","Ruc","Estado Factura","Descuento Iva 15",
        "Descuento Iva 0","Serie","Autorización","Caducidad","Forma Pago",
        "Vendedor","Código Sustento"
    ];
    const headerRow = ws.addRow(headers);
    headerRow.eachCell(cell => {
        cell.fill = headerFill;
        cell.font = { bold: true , color: { argb: 'FFFFFFFF' }};
        cell.alignment = centerAlignment;
        cell.border = { top: {style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
    });

    // Filas de datos
    ventas.forEach(v => {
        ws.addRow([
            v.documento || '',
            v.fecha || '',
            v.emitido_a || '',
            v.secuencia_factura || '',
            parseFloat(v.subtotal15 || 0),
            parseFloat(v.subtotal0 || 0),
            parseFloat(v.iva15 || 0),
            parseFloat(v.descuento || 0),
            parseFloat(v.total_factura || 0),
            v.ruc_cliente || '',
            v.estado_factura || '',
            parseFloat(v.descuento_iva15 || 0),
            parseFloat(v.descuento_iva0 || 0),
            v.serie || '',
            v.autorizacion || '',
            v.caducidad || '',
            v.forma_pago || '',
            v.vendedor || '',
            v.codigo_sustento || ''
        ]);
    });

    // Calcular totales
    const totalSub15 = ventas.reduce((sum, v) => sum + parseFloat(v.subtotal15 || 0), 0);
    const totalSub0 = ventas.reduce((sum, v) => sum + parseFloat(v.subtotal0 || 0), 0);
    const totalIva15 = ventas.reduce((sum, v) => sum + parseFloat(v.iva15 || 0), 0);
    const totalDescuento = ventas.reduce((sum, v) => sum + parseFloat(v.descuento || 0), 0);
    const totalTotal = ventas.reduce((sum, v) => sum + parseFloat(v.total_factura || 0), 0);
    const totalDescuentoIva15 = ventas.reduce((sum, v) => sum + parseFloat(v.descuento_iva15 || 0), 0);
    const totalDescuentoIva0 = ventas.reduce((sum, v) => sum + parseFloat(v.descuento_iva0 || 0), 0);
    const totalFacturas = ventas.length;
    
    // Saltar dos filas 
    ws.addRow([]);
    ws.addRow([]);

    // Fila de subtotales con # de Facturas
    const filaTotalesHeader = ws.addRow(['', '', '', '', 'Sub. 15%', 'Sub. 0%', 'Iva 15%', 'Descuento', 'Total', '', '', 'Descuento Iva 15', 'Descuento Iva 0']);
    ws.mergeCells(`B${filaTotalesHeader.number}:C${filaTotalesHeader.number}`);
    filaTotalesHeader.getCell(2).value = `# de Facturas: ${totalFacturas} - # de Facturas Anuladas: 0`;
    filaTotalesHeader.getCell(2).alignment = centerAlignment;
    [5,6,7,8,9,12,13].forEach(i => {
        filaTotalesHeader.getCell(i).alignment = centerAlignment;
        filaTotalesHeader.getCell(i).font = { bold: true };
    });

    // Fila TOTAL
    const filaTotal = ws.addRow(['TOTAL', '', '', '', totalSub15, totalSub0, totalIva15, totalDescuento, totalTotal, '', '', totalDescuentoIva15, totalDescuentoIva0]);
    ws.mergeCells(`A${filaTotal.number}:D${filaTotal.number}`);
    filaTotal.getCell(1).alignment = centerAlignment;

    // Aplicar fondo amarillo claro y estilo a toda la fila TOTAL
    filaTotal.eachCell(cell => {
        cell.fill = totalFill;
        cell.font = { bold: true };
        cell.alignment = centerAlignment;
        cell.border = { top: {style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
    });

    // Ajustar anchos de columna automáticamente
    ws.columns.forEach(col => {
        let maxLength = 10;
        col.eachCell({ includeEmpty: true }, cell => {
            const len = cell.value ? cell.value.toString().length : 0;
            if (len > maxLength) maxLength = len;
        });
        col.width = maxLength + 5;
    });

    // Descargar Excel
    const buffer = await wb.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: "application/octet-stream" });
    const fechaInicioFormatted = fechaInicio.replace(/\//g, '-');
    const fechaFinFormatted = fechaFin.replace(/\//g, '-');
    saveAs(blob, `REPORTE_VENTAS (${fechaInicioFormatted} - ${fechaFinFormatted}).xlsx`);
}
