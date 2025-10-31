import * as SPINNER from "@utils/spinner.js";
import { DataTable } from "simple-datatables";
import remove from "@utils/deleteModal";

let table = null;

const dForm = document.getElementById('delete-form');
const form = document.getElementById('formProductos');
const boletoForm = document.getElementById('formBoleto');
const stockForm = document.getElementById('formStock');
const stockTicketsForm = document.getElementById('formStockTickets');

async function showAll() {
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/productos');
        const data = await response.json();

        console.log("datos obtenidos de productos:",data)
        if (response.status !== 200 || !data.success) {
            toast.show('error', 'Error', data.message || 'Error al obtener los productos');
            return;
        }
        
        renderProductos(data.data)

    } catch (err) {
        toast.show('error', 'Error', 'Error inesperado al cargar los datos');
        console.error('Error al obtener productos:', err);
    }finally{
        SPINNER.hideSpinner();
    }
}

function renderProductos(productos){
    const csrfToken = boletoForm.querySelector('[name="_token"]').getAttribute('value');
    console.log(productos)
    if (table) {
        table.destroy();
        table = null;
    }

    const tbProductos = document.getElementById('tbProductos');
    tbProductos.innerHTML = '';
    console.log(productos)
    const productosMapped = productos.map(prod => `
        <tr>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.nombre}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.cantidad}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.precio_venta_final}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                       ${ Math.round(prod.impuesto * 100) }%
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap hidden">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.fecha_creacion}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.categoria?.nombre || ''}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">
                <div class="flex items-center justify-center">
                    <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">
                        ${prod.abono ? prod.abono_relacion?.nombre : ''}
                    </span>
                </div>
            </td>
            <td class="px-6 py-3 whitespace-nowrap">

            
                <!-- Botón asignar cantidad boleto -->
                    <!--Btn Tickets -->
                    ${prod.categoria?.nombre === 'Ticket' ? `
                        <!--Btn Agregar/Disminuir Stock ticket -->
                        <i 
                            class="fa-solid fa-plus text-sm text-gray-600 hover:text-blue-500 cursor-pointer"
                            title="Control de stock de Tickets"
                            x-on:click="
                            handleTicketStockClick(${prod.id}, '${csrfToken}')              
                            ">
                        </i>

                        <!-- Botón vincular jornada con ticket -->
                        <i 
                            class="fa-solid fa-ticket text-sm text-gray-600 hover:text-green-500 cursor-pointer"
                            title="Asignar boletos a una jornada"
                            x-on:click="
                                boletoForm.idProducto = ${prod.id};
                                $nextTick(() => cargarJornadas(${prod.id},'${csrfToken}'));
                                showBoletoModal = true;
                            ">
                        </i>
                    ` : ''}

                    <!--Btn Abonos -->
                    ${prod.categoria?.nombre === 'Abonos' ? `
                        <!-- Botón actualizar stock -->
                        <i 
                            class="fa-solid fa-plus text-sm text-gray-600 hover:text-blue-500 cursor-pointer"
                            title="Control de stock de Abonos"
                            x-on:click="
                                abrirModalStock(${prod.id}, ${prod.cantidad_actual})
                            ">
                        </i>
                    ` : ''}

                <!-- Botón editar -->
                <i  class="fa-solid fa-pen-to-square text-sm text-gray-600 hover:text-amber-400 cursor-pointer"
                    title="Editar Producto"
                    x-on:click="
                        form.id = '${prod.id}';
                        form.nombre = '${prod.nombre}';
                        form.cantidad = '${prod.cantidad}';
                        form.costo = '${prod.costo}';
                        form.precioSinIva = '${parseFloat(prod.precio_venta_sin_iva).toFixed(2)}';
                        form.precioSinIvaFormat = '${prod.precio_venta_sin_iva}';
                        form.precioConIva = '${parseFloat(prod.precio_venta_final).toFixed(2)}';
                        form.precioConIvaFormat = '${prod.precio_venta_final}';
                        form.impuesto = '${prod.impuesto}';
                        form.categoria = '${prod.categoria_id}';
                        form.categoriaTexto = '${prod.categoria?.nombre || ''}';
                        form.desglosarIva = false;
                        form.abonoId = ${prod.id_abono || 0};
                        showForm = true;

                        // Obtener elementos
                        const esAbonoSwitch = document.getElementById('esAbono');
                        const abonoSelect = document.getElementById('abonoSelect');
                        const precioSinIvaInput = document.getElementById('precioSinIva');
                        const precioConIvaInput = document.getElementById('precioConIva');
                        let impuestoNum = parseFloat(document.getElementById('impuesto').value) || 0;

                        if(${prod.abono}) {
                            esAbonoSwitch.checked = true;
                            esAbonoSwitch.dispatchEvent(new Event('change'));

                            $nextTick(() => {
                                form.abonoId = '${prod.id_abono || ''}';
                                abonoSelect.value = '${prod.id_abono || ''}';
                                const selectedOption = abonoSelect.options[abonoSelect.selectedIndex];
                                if(selectedOption && selectedOption.dataset.costo) {
                                    const costo = parseFloat(selectedOption.dataset.costo);
                                    if(!isNaN(costo)) {
                                        precioConIvaInput.value = costo.toFixed(2);
                                        precioSinIvaInput.value = (costo / (1 + impuestoNum)).toFixed(2);
                                    }
                                }
                            });
                        } else {
                            esAbonoSwitch.checked = false;
                            esAbonoSwitch.dispatchEvent(new Event('change'));

                            // Si no es abono, asignar valores originales del producto
                            precioSinIvaInput.value = form.precioSinIva;
                            precioConIvaInput.value = form.precioConIva;
                        }

                    ">
                </i>

                <!-- Botón eliminar -->

                <i class="fa-solid fa-trash-can text-sm text-gray-600 hover:text-red-500 cursor-pointer"
                    x-on:click="form.id = '${prod.id}'; dTitle = '${prod.nombre}'; showDeleteForm = true;">
                </i>
            </td>
        </tr>
    `).join('');

    tbProductos.innerHTML = productosMapped;
    
    // Inicializar DataTable
    try {
        table = new DataTable('#tProductos', {
            searchable: true
        });
    } catch(err) {
        console.error("Error al inicializar DataTable:", err);
    }

}

document.addEventListener('DOMContentLoaded', function() {
    
    init();
    
    const select = document.getElementById('categoriaSelect');
    
    fetch('/actions/categoria-productos')
        .then(response => response.json())
        .then(data => {
            console.log('Categorías cargadas:', data);
            if(data.success) {
                
                data.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error cargando categorías:', error));

    // Obtener elementos del switch y select de abonos
    const esAbonoSwitch = document.getElementById('esAbono');
    const abonoSelect = document.getElementById('abonoSelect');
    const precioSinIvaInput = document.getElementById('precioSinIva');
    const precioSinIvaFormat = document.getElementById('precioSinIvaFormat');
    const precioConIvaInput = document.getElementById('precioConIva');
    const precioConIvaFormat = document.getElementById('precioConIvaFormat');
    const desglosarIvaCheckbox = document.getElementById('desglosarIva');
    const impuestoSelect = document.getElementById('impuesto');

    let abonosCargados = false;
    let impuestoNum = parseFloat(document.querySelector('[name="impuesto"]').value) || 0;

    // Control del switch de abono
    esAbonoSwitch.addEventListener('change', function () {
        if (this.checked) {
            // Bloquear campos
            precioSinIvaInput.readOnly = true;
            desglosarIvaCheckbox.disabled = true;
            desglosarIvaCheckbox.checked = false;

            // Cargar abonos si aún no se cargaron
            if (!abonosCargados) {
                console.log('Cargando abonos...');
                SPINNER.showSpinner();
                fetch('/actions/abonos/activos')
                    .then(response => response.json())
                    .then(data => {
                        SPINNER.hideSpinner();

                        if (!data.success) {
                            toast.show('error', 'Error', data.message || 'Error al cargar los abonos');
                            return;
                        }

                        data.data.forEach(abono => {
                            const option = document.createElement('option');
                            option.value = abono.id;
                            option.textContent = `${abono.nombre} - $${abono.costo_total}`;
                            option.dataset.costo = abono.costo_total;
                            abonoSelect.appendChild(option);
                        });
                        abonosCargados = true;
                    })
                    .catch(error => console.error('Error cargando abonos:', error));
            }
        } else {
            // Si se desactiva el switch, desbloqueamos todo
            precioSinIvaInput.readOnly = false;
            desglosarIvaCheckbox.disabled = false;
            precioSinIvaInput.value = '';
            precioConIvaInput.value = '';
            abonoSelect.value = '';
        }
    });

    // Selecciona un Abono
    abonoSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const costo = parseFloat(selectedOption.dataset.costo);

        if (!isNaN(costo)) {
            // Poner costo en precioConIva
            precioConIvaInput.value = costo.toFixed(2);
            precioConIvaFormat.value = costo.toFixed(4);
            // Calcular precioSinIva
            const precioIva = costo / (1 + impuestoNum);
            precioSinIvaInput.value = precioIva.toFixed(2);
            precioSinIvaFormat.value = precioIva.toFixed(4);
        }
    });

    // Cambio de impuesto
    impuestoSelect.addEventListener('change', function () {
        impuestoNum = parseFloat(this.value) || 0;

        // Recalcular cuando se selecciona un abono
        const selectedOption = abonoSelect.options[abonoSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.costo) {
            const costo = parseFloat(selectedOption.dataset.costo);
            precioConIvaInput.value = costo.toFixed(2);
            precioSinIvaInput.value = (costo / (1 + impuestoNum)).toFixed(2);
        }
    });

});

dForm.addEventListener('submit', (e) => remove(e, dForm, SPINNER, '/actions/productos/', showAll));

boletoForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    SPINNER.showSpinner();
    // Obtener los valores de los inputs del formulario
    const jornada = boletoForm.querySelector('[x-model="boletoForm.jornada"]')?.value.trim();
    const cantidad = boletoForm.querySelector('[x-model="boletoForm.cantidad"]')?.value.trim();
    const idProducto = boletoForm.querySelector('[x-model="boletoForm.idProducto"]')?.value.trim();

      // Validaciones
    if (!jornada || jornada === '') {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Debe seleccionar una jornada o grupo');
        return;
    }

    const cantidadNum = parseInt(cantidad, 10);
    if (isNaN(cantidadNum) || cantidadNum <= 0) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'La cantidad de boletos debe ser mayor que 0');
        return;
    }

    // Preparar datos para enviar al backend
    const formData = {
        id_producto: idProducto,
        id_jornada: jornada,
        stock: cantidadNum
    };

    console.log("Datos a enviar para asignar boletos:", formData);

     try {
        const csrfToken = boletoForm.querySelector('[name="_token"]').getAttribute('value');

        const response = await fetch('/actions/productos-jornada', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!data.success) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', data.message || 'Error al guardar boletos');
            return;
        }

        toast.show('success', 'Éxito', data.message || 'Boletos guardados correctamente');

        // Limpiar el formulario si es necesario
        boletoForm.querySelector('[x-model="boletoForm.jornada"]').value = '';
        boletoForm.querySelector('[x-model="boletoForm.cantidad"]').value = '';
        boletoForm.querySelector('[x-model="boletoForm.idProducto"]').value = '';
        showAll();

    } catch (err) {
        SPINNER.hideSpinner();

        console.error('Error al enviar boletos:', err);
        toast.show('error', 'Error', 'No se pudo conectar con el servidor');
    } 

});

stockForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    SPINNER.showSpinner();

    // Obtener valores del formulario
    const idProducto = stockForm.querySelector('[x-model="stockForm.idProducto"]')?.value.trim();
    const cantidad = parseInt(stockForm.querySelector('#cantidadStock').value, 10);
    const motivo = stockForm.querySelector('[x-model="stockForm.motivo"]')?.value.trim();

    console.log("esto es cantidad:",cantidad)
    // Validaciones
    if (!idProducto || idProducto === '') {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'No se especificó el producto');
        return;
    }

    const cantidadNum = parseInt(cantidad, 10);
    if (isNaN(cantidadNum) || cantidadNum === 0) {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'La cantidad no puede ser igual a 0');
        return;
    }

    if (!motivo || motivo === '') {
        SPINNER.hideSpinner();
        toast.show('error', 'Error', 'Debe especificar un motivo o razón');
        return;
    }

    // Despues se tiene que pasar el userId 

    // Datos para el back 
    const formData = { 
        producto_id: idProducto,
        cantidad: cantidadNum, 
        motivo: motivo,
    };

    try {
        const csrfToken = stockForm.querySelector('[name="_token"]').getAttribute('value');

        const response = await fetch(`/actions/productos/${idProducto}/agregar-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!data.success) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', data.message || 'Error al actualizar stock');
            return;
        }

        toast.show('success', 'Éxito', data.message || 'Stock actualizado correctamente');

        // Limpiar formulario
        // resetStockForm();
        showAll();

    } catch (err) {
        SPINNER.hideSpinner();
        console.error('Error al actualizar stock:', err);
        toast.show('error', 'Error', 'No se pudo conectar con el servidor');
    } 
});

stockTicketsForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    SPINNER.showSpinner();

    try {
        // Obtener valores del formulario
        const idTicket = stockTicketsForm.querySelector('[x-model="stockFormTickets.idTicket"]')?.value.trim();
        const idJornada = stockTicketsForm.querySelector('[x-model="stockFormTickets.ticketSeleccionado"]')?.value.trim();
        const cantidad = parseInt(stockTicketsForm.querySelector('#cantidadStockTickets')?.value, 10);
        const motivo = stockTicketsForm.querySelector('[x-model="stockFormTickets.motivo"]')?.value.trim();
        const stockAnterior = parseInt(stockTicketsForm.querySelector('[x-model="stockFormTickets.stockActual"]')?.value, 10);
        const stockDisponible = parseInt(stockTicketsForm.querySelector('[x-model="stockFormTickets.stockDisponible"]')?.value, 10);

        // Validaciones
        if (!idTicket) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'No se especificó el ticket');
            return;
        }
        if (!idJornada) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'Debe seleccionar una jornada asociada al ticket');
            return;
        }
        if (isNaN(cantidad) || cantidad === 0) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'La cantidad no puede ser 0');
            return;
        }
        if (cantidad>stockDisponible){
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'La cantidad ingresada no puede ser mayor al stock disponible');
            return;
        }

        if (!motivo || motivo === '') {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'Debe especificar un motivo o razón');
            return;
        }

        // Datos a enviar al backend
        const formData = {
            producto_id: idTicket,
            jornada_id: idJornada,
            tipo_movimiento: cantidad > 0 ? 'ingreso' : 'egreso',
            stock_anterior: stockAnterior,
            cantidad: cantidad,
            motivo: motivo
        };

        console.log(formData)

        const csrfToken = stockTicketsForm.querySelector('[name="_token"]').getAttribute('value');

        const response = await fetch(`/actions/productos/${idTicket}/agregar-stock-ticket`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!data.success) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', data.message || 'Error al actualizar stock');
            return;
        }

        toast.show('success', 'Éxito', data.message || 'Stock actualizado correctamente');
        showAll();

    } catch (err) {
        SPINNER.hideSpinner();
        console.error('Error al actualizar stock tickets:', err);
        toast.show('error', 'Error', 'No se pudo conectar con el servidor');
    }
});

form.addEventListener("submit", async (e) => {
        e.preventDefault();
        SPINNER.showSpinner();
        console.log("Evento submit capturado ✅");

        const form = e.target;

        // Obtener la fecha y hora actual del sistema
        const now = new Date();
        const fechaHoraActual = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + ' ' +
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');

        // Obtener los datos del formulario
        const id = form.querySelector('[x-model="form.id"]')?.value.trim();
        const nombre = form.querySelector('[x-model="form.nombre"]')?.value.trim();
        let cantidad = form.querySelector('[x-model="form.cantidad"]')?.value.trim();
        const costo = form.querySelector('[x-model="form.costo"]')?.value.trim();
        const precioSinIva = form.querySelector('input[name="precioSinIvaFormat"]')?.value.trim();
        const precioConIva = form.querySelector('input[name="precioConIvaFormat"]')?.value.trim();
        const impuesto = form.querySelector('[x-model="form.impuesto"]')?.value.trim();
        const categoria = form.querySelector('[x-model="form.categoria"]')?.value.trim();
        const categoriaTexto = form.querySelector('[x-model="form.categoriaTexto"]')?.value.trim();
        const esAbono = form.querySelector('[x-model="form.esAbono"]')?.checked;
        const abonoId = form.querySelector('[x-model="form.abonoId"]')?.value.trim();

        // Validaciones 
        // Validacion de nombre 
        if (!nombre) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'El nombre del producto es obligatorio');
            return;
        }
        // Validaicon de categoria
        if (!categoria) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'Debe seleccionar una categoría');
            return;
        }
        console.log("Esta es la categoria:",categoriaTexto)
       

        // Validar precios
        const precioSinIvaNum = parseFloat(precioSinIva);
        const costoNum = parseFloat(costo);
        
        // Validacion costo
        if (isNaN(costoNum) || costoNum < 0) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'El costo debe ser un número válido mayor o igual a 0');
            return;
        }

        // Validar que el costo sea menor que el precio sin IVA
        if (costoNum >= precioSinIvaNum) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'El costo debe ser menor que el precio de venta sin IVA');
            return;
        }

        // Validacion precio sin iva
        if (isNaN(precioSinIvaNum) || precioSinIvaNum < 0) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'El precio sin IVA debe ser un número válido mayor o igual a 0');
            return;
        }

        // validacion de cantidad
        if (categoriaTexto && categoriaTexto.toLowerCase() === 'ticket') {
            cantidad = '0';
        } else {
            const cantidadNum = parseFloat(cantidad);
            if (isNaN(cantidadNum) || cantidadNum < 0) {
                SPINNER.hideSpinner();
                toast.show('error', 'Error', 'La cantidad debe ser un número válido mayor o igual a 0');
                return;
            }
        }

        // Validar que si es abono, tenga un abono seleccionado
        if (esAbono && (!abonoId || abonoId === '')) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'Debe seleccionar un abono cuando el producto es de tipo abono');
            return;
        }

        const formData = {
            fechaCreacion: fechaHoraActual,
            nombre,
            cantidad,
            tipo_producto: 'producto',
            precio_venta_sin_iva: precioSinIva,
            precio_venta_final: precioConIva,
            categoria_id: categoria,
            costo,
            abono: esAbono,
            id_abono: esAbono ? abonoId : null,
            impuesto
        };
       
        const csrfToken = form.querySelector('[name="_token"]').getAttribute('value');

        console.log("Datos a enviar:", formData);
        console.log(id)
        try {
            const urlToFetch = id 
                ? `/actions/productos/${id}` 
                : '/actions/productos';

            const response = await fetch(urlToFetch, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });

            // Si el servidor respondió, tratamos de parsear el JSON
            const data = await response.json();

            if (response.status === 404) {
                toast.show('error', 'Error', data.message || 'Recurso no encontrado');
                return;
            }

            if (response.status === 400) {
                toast.show('error', 'Error', data.message?.error || 'Error de validación', data.message?.list);
                return;
            }

            if (!data.success) {
                SPINNER.hideSpinner();

                toast.show('error', 'Error', data.message || 'Ocurrió un error desconocido');
                return;
            }

            toast.show('success', 'Éxito', data.message);
            showAll();
            

        } catch (error) {
            SPINNER.hideSpinner(); 
            console.error('Error en la petición fetch:', error);
            toast.show('error', 'Error', 'No se pudo conectar con el servidor. Intente nuevamente.');
        }finally{
            // SPINNER.hideSpinner();

        }
    
});

function init(){
    showAll() 
}