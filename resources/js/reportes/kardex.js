import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import * as XLSX from "xlsx";
import ExcelJS from 'exceljs';
import { saveAs } from 'file-saver';

let table = null;
let datosCargados = false; //Verificar si existen datos en la tabla

document.addEventListener("DOMContentLoaded", () =>{
    const btnBuscar = document.querySelector("#btnBuscar");
    const fechaInicio = document.querySelector("#fechaInicio");
    const fechaFin = document.querySelector("#fechaFin");
    const btnDescargar = document.querySelector("#btnDescargarExcel");

    // Manejo del boton de buscar
    if (btnBuscar) {
        btnBuscar.addEventListener("click", () => {
            console.log("boton precionado")
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

            console.log("Buscando movimientos de stock entre:", inicio, "y", fin);
            SPINNER.showSpinner();
             // Hacemos el fetch POST
            fetch("/actions/movimientos/filtrar", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('[name="_token"]').getAttribute("value")
                },
                body: JSON.stringify({
                    fecha_inicio: inicio,
                    fecha_fin: fin
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("entro aqui")
                if (data.success) {
                    console.log("Movimientos recibidos:", data.data);
                    renderMovimientosCardex(data.data);
                } else {
                    toast.show('error', 'Error', 'No se encontraron movimientos en ese rango de fechas.');
                }
            })
            .catch(error => {
                console.error("Error en la petición:", error);
                toast.show('error', 'Error', 'Ocurrió un problema al obtener los movimientos.');
            })
            .finally(()=>{
                SPINNER.hideSpinner();               
            });


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

// Parsear la fecha para comparar
function parseDate(str) {
    const [day, month, year] = str.split("/");
    return new Date(`${year}-${month}-${day}T00:00:00`);
}



function renderMovimientosCardex(movimientos) {    
    // Inicializar DataTable
    if (table) {
        table.destroy();
        table = null;
    }
    
    // Ordenar los datos por fecha
    movimientos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
    
    const tbCardex = document.getElementById('tbCardex');
    tbCardex.innerHTML = '';

    let rows = '';

    // Mapa de colores por tipo de movimiento
    const colorMapTipo = {
        'ingreso': { bg: 'bg-emerald-200', text: 'text-emerald-700' },
        'egreso': { bg: 'bg-red-200', text: 'text-red-700' }
    };


     if (movimientos.length === 0) {
        datosCargados = false;
        // Mostrar mensaje si no hay registros
        rows = `
            <tr>
                <td class="px-6 py-3 text-center text-gray-500 dark:text-gray-400" colspan="9">
                    No se encontraron registros
                </td>
            </tr>
        `;
    } else {
        datosCargados = true;
        // Mapear los movimientos
        rows = movimientos.map(mov => {
            const tipo = mov.tipo || '';
            const colorTipo = colorMapTipo[tipo.toLowerCase()] || { bg: 'bg-gray-200', text: 'text-gray-700' };

            return `
            <tr>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.producto || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.usuario || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm font-medium py-1 px-3 rounded-full flex items-center justify-center space-x-1
                        ${mov.tipo_relacion === 'J' ? 
                            'bg-indigo-100 text-indigo-800 border border-indigo-300 shadow-sm' : 
                            mov.tipo_relacion === 'A' ? 
                            'bg-purple-100 text-purple-800 border border-purple-300 shadow-sm' : 
                            'bg-gray-100 text-gray-800 border border-gray-300'}">
                        
                        ${mov.tipo_relacion === 'J' ? 
                            '<i class="fas fa-ticket w-4 h-4"></i>' : 
                            mov.tipo_relacion === 'A' ? 
                            '<i class="fas fa-calendar w-4 h-4"></i>' : 
                            ''}

                        <span>${mov.relacion || ''} ${mov.tipo_relacion ? `(${mov.tipo_relacion})` : ''}</span>
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.fecha || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="py-1 px-3 text-xs rounded-full font-semibold ${colorTipo.bg} ${colorTipo.text} capitalize">
                            ${tipo}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.stock_anterior || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.stock_agregado || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.stock_nuevo || ''}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                            ${mov.motivo || ''}
                        </span>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }


    tbCardex.innerHTML = rows;
    if (movimientos.length > 0) {
        table = new DataTable('#tCardex', { searchable: true });
    }

}


// function exportarExcel() {
//     const table = document.getElementById("tCardex");
//     if (!table) return;

//     const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
//     const fechaFin = document.querySelector("#fechaFin")?.value || '';

//     // Formatear las fechas para el nombre del archivo
//     const fechaInicioFormatted = fechaInicio.replace(/\//g, '-');
//     const fechaFinFormatted = fechaFin.replace(/\//g, '-');

//     const fileName = `movimientosStock (${fechaInicioFormatted} ${fechaFinFormatted}).xlsx`;

//     // Convertir tabla HTML a hoja de Excel
//     const wb = XLSX.utils.book_new();
//     const ws = XLSX.utils.table_to_sheet(table);

//     XLSX.utils.book_append_sheet(wb, ws, "Movimientos");

//     // Descargar
//     XLSX.writeFile(wb, fileName);
// }

// Exportar datos a excel
async function exportarExcel() {
    const table = document.getElementById("tCardex");
    if (!table) return;

    const fechaInicio = document.querySelector("#fechaInicio")?.value || '';
    const fechaFin = document.querySelector("#fechaFin")?.value || '';

    // Crear workbook y worksheet
    const wb = new ExcelJS.Workbook();
    const ws = wb.addWorksheet("Movimientos");

    // Título (fila 1)
    ws.mergeCells('A1:I1'); // Ajusta según número de columnas
    const titleCell = ws.getCell('A1');
    titleCell.value = "REPORTE DE MOVIMIENTOS";
    // Estilos copiados de la versión de ventas
    const headerFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2563EB' } }; 
    const centerAlignment = { vertical: 'middle', horizontal: 'center' };
    titleCell.fill = headerFill;
    titleCell.alignment = centerAlignment;
    titleCell.font = { bold: true, size: 16, color: { argb: 'FFFFFFFF' } };

    // Fila 2: fechas
    ws.mergeCells('A2:I2');
    const dateCell = ws.getCell('A2');
    dateCell.value = (fechaInicio === fechaFin) 
        ? `${fechaInicio}` 
        : `Desde: ${fechaInicio} Hasta: ${fechaFin}`;
    dateCell.alignment = { vertical: 'middle', horizontal: 'center' };

    // Fila 3 vacía
    ws.addRow([]);

    // Encabezados (fila 4)
    const headers = [];
    table.querySelectorAll('thead tr th').forEach(th => {
        headers.push(th.innerText);
    });
    const headerRow = ws.addRow(headers);

    // Estilo encabezados
    headerRow.eachCell(cell => {
        cell.font = { bold: true, color: { argb: 'FFFFFFFF' } }; // blanco
        cell.fill = {
            type: 'pattern',
            pattern: 'solid',
            fgColor: { argb: 'FF2563EB' } // azul
        };
        cell.alignment = { vertical: 'middle', horizontal: 'center' };
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

    // Ajustar ancho de columnas
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
    const fileName = `movimientosStock (${fechaInicio.replace(/\//g, '-')}_${fechaFin.replace(/\//g, '-')}).xlsx`;
    saveAs(new Blob([buffer]), fileName);
}