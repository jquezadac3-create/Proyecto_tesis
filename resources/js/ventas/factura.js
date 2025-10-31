import * as SPINNER from "@utils/spinner.js";
import Swal from 'sweetalert2';


let initialData = {}; // Datos iniciales al cargar
let products = []; // Almacenar productos seleccionadso
const productsTbody = document.getElementById("products-tbody");
const imprimirFactura = document.getElementById("imprimirFactura");

document.addEventListener("DOMContentLoaded", () => {
    initFacturaForm();
    setupImprimirFacturaButton();
});

// Función principal para inicializar el formulario de factura
function initFacturaForm() {
    const defaultId = "9999999999999";
    const idInput = document.getElementById("factura-identificación");
    numFactura();
    setupIdChangeListener(idInput, defaultId);
    loadConsumidorFinal(); // Cargar el cosumidor final al iniciar la pg
    setupBuscarClienteButton(); // Buscador de clientes dentro de la base
    setupGuardarClienteButton(); // Guardar o modificar un cliente
    setupBuscarProductoInput(); // Buscador de productos
    setupGuardarFacturaButton(); // Guardar la factura
    initNuevaFactura(); // Recargar la pagina para una nueva factura
}

function numFactura() {
    fetch('/actions/facturas/numeroFactura')
        .then(response => response.json())
        .then(data => {
            const titulo = document.getElementById("tituloFactura");
            const breadcrumb = document.getElementById("breadcrumbFactura");

            if (titulo) {
                titulo.textContent = `Factura #${data.numero_factura}`;
            }
            if (breadcrumb) {
                breadcrumb.textContent = `Crear factura #${data.numero_factura}`;
            }
        })
        .catch(error => {
            console.error("Error al obtener el número de factura:", error);
        });
}

// region Clientes
// Configura el listener para limpiar campos si el RUC cambia
function setupIdChangeListener(idInput, defaultId) {
    const otherInputs = [
        "customer-nombres",
        "customer-apellidos",
        "customer-correo",
        "customer-contacto",
        "customer-direccion",
        "customer-id"
    ].map(id => document.getElementById(id));

    let alreadyCleared = false; // Almacenar el cambio 

    idInput.addEventListener("input", () => {
        if (!alreadyCleared && idInput.value !== defaultId) {
            otherInputs.forEach(input => input.value = "");
            alreadyCleared = true;
        }
    });
}

// Carga los datos del cliente Consumidor Final
async function loadConsumidorFinal() {
    SPINNER.showSpinner();
    try {
        const response = await fetch("/actions/clientes/consumidor-final", {
            method: "GET",
            headers: { "Accept": "application/json" }
        });

        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const data = await response.json();

        if (data.success) fillClienteFields(data.data);
        else console.warn("No se encontró el cliente Consumidor Final");
    } catch (error) {
        console.error("Error al cargar el cliente Consumidor Final:", error);
    } finally {
        SPINNER.hideSpinner();
    }
}

// Llena los campos del formulario con la info del cliente
function fillClienteFields(cliente) {
    const inputs = {
        identificacion: document.getElementById("factura-identificación"),
        nombres: document.getElementById("customer-nombres"),
        apellidos: document.getElementById("customer-apellidos"),
        correo: document.getElementById("customer-correo"),
        contacto: document.getElementById("customer-contacto"),
        direccion: document.getElementById("customer-direccion"),
        id: document.getElementById("customer-id")
    };

    // Transformar número si viene con +593
    let telefono = cliente.telefono ?? "";
    if (telefono.startsWith("+593") && telefono.length === 13) {
        telefono = "0" + telefono.substring(4);
    }

    inputs.identificacion.value = cliente.numero_identificacion;
    inputs.nombres.value = cliente.nombres ?? "";
    inputs.apellidos.value = cliente.apellidos ?? "";
    inputs.correo.value = cliente.email ?? "";
    inputs.contacto.value = telefono;
    inputs.direccion.value = cliente.direccion ?? "";
    inputs.id.value = cliente.id;

    // Guardar los valores iniciales después de llenar los campos
    initialData = {
        id: inputs.id.value,
        identificacion: inputs.identificacion.value,
        nombres: inputs.nombres.value,
        apellidos: inputs.apellidos.value,
        correo: inputs.correo.value,
        contacto: inputs.contacto.value,
        direccion: inputs.direccion.value
    };
}

// Funcion para el boton de buscar cliente
function setupBuscarClienteButton() {
    const buscarBtn = document.getElementById("buscar-cliente-btn");
    const idInput = document.getElementById("factura-identificación");
    const dropdownContainer = document.getElementById("dropdownContainerClientes");
    const dropdownClientes = document.getElementById("dropdownClientes");

    buscarBtn.addEventListener("click", async () => {
        const query = idInput.value.trim();
        if (!query || query === "9999999999999") return;
        SPINNER.showSpinner();

        try {
            const response = await fetch("/actions/clientes/buscar", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('[name="_token"]').getAttribute("value")
                },
                body: JSON.stringify({ query })
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const data = await response.json();

            if (data.success && data.data.length) {
                renderDropdownClientes(data.data, dropdownClientes, dropdownContainer);
                // Agregar opción "Agregar nuevo cliente" al final
                const agregarDiv = document.createElement("div");
                agregarDiv.className = "px-4 py-2 cursor-pointer hover:bg-green-100 text-green-700 font-medium";
                agregarDiv.textContent = "Agregar un cliente nuevo";
                agregarDiv.addEventListener("click", () => {
                    dropdownContainer.classList.add("hidden");
                });
                dropdownClientes.appendChild(agregarDiv);
            } else {
                dropdownClientes.innerHTML = `
                    <div class="px-4 py-2 text-gray-500">No se encontraron clientes</div>
                    <div id="agregar-cliente-btn" class="px-4 py-2 cursor-pointer hover:bg-green-100 text-green-700 font-medium">
                        Agregar un cliente nuevo
                    </div>
                `;
                dropdownContainer.classList.remove("hidden");

                // Listener para agregar cliente
                const agregarBtn = document.getElementById("agregar-cliente-btn");
                agregarBtn.addEventListener("click", () => {
                    dropdownContainer.classList.add("hidden");
                });
            }
        } catch (error) {
            console.error("Error al buscar clientes:", error);
        } finally {
            SPINNER.hideSpinner();
        }
    });
}


// Función para renderizar los clientes en el dropdown
function renderDropdownClientes(clientes, container, wrapper) {
    container.innerHTML = ""; // limpiar resultados previos

    // Input num identificacion
    const input = document.getElementById("factura-identificación");

    // Ajustar el wrapper al input No Identificacion
    wrapper.style.width = `${input.offsetWidth}px`;
    wrapper.style.position = "absolute";
    wrapper.style.top = `${input.offsetHeight + 10}px`;  //10px mas abajo de donde empieza el input
    wrapper.style.left = "0px";
    wrapper.style.zIndex = 9999;

    clientes.forEach(cliente => {
        const div = document.createElement("div");
        div.className = "px-4 py-2 cursor-pointer hover:bg-blue-100";

        // Nombre en la primera línea
        const nombreDiv = document.createElement("div");
        nombreDiv.className = "font-medium text-gray-800 dark:text-white";
        nombreDiv.textContent = `${cliente.nombres} ${cliente.apellidos}`;

        // Identificación en la segunda línea, más pequeña
        const idDiv = document.createElement("div");
        idDiv.className = "text-xs text-gray-500 dark:text-gray-400";
        idDiv.textContent = `Identificación: ${cliente.numero_identificacion}`;

        div.appendChild(nombreDiv);
        div.appendChild(idDiv);

        div.addEventListener("click", () => {
            fillClienteFields(cliente);
            wrapper.classList.add("hidden");
        });

        container.appendChild(div);
    });

    wrapper.classList.remove("hidden");
}


// Guardar el cliente en caso de no existir / actualizar el cliente en caso de existir
function setupGuardarClienteButton() {
    const form = document.querySelector("#datos-clientes-form-search");

    // Obtener inputs y guardar sus valores iniciales
    const inputs = {
        id: document.getElementById("customer-id"),
        identificacion: document.getElementById("factura-identificación"),
        nombres: document.getElementById("customer-nombres"),
        apellidos: document.getElementById("customer-apellidos"),
        correo: document.getElementById("customer-correo"),
        contacto: document.getElementById("customer-contacto"),
        direccion: document.getElementById("customer-direccion")
    };

    console.log(initialData)
    form.addEventListener("submit", (e) => {
        e.preventDefault(); // evitar que el formulario se envíe realmente

        let contacto = inputs.contacto.value.trim();

        // Si empieza con "0" y tiene al menos 9 dígitos
        if (/^0\d{8,9}$/.test(contacto)) {
            contacto = "+593" + contacto.substring(1);
        } else {
            toast.show('error', 'Número inválido', 'El número de contacto debe tener 10 dígitos y empezar con 0.');
            return;
        }

        const clienteData = {
            id: inputs.id.value.trim(),
            identificacion: inputs.identificacion.value.trim(),
            nombres: inputs.nombres.value.trim(),
            apellidos: inputs.apellidos.value.trim(),
            correo: inputs.correo.value.trim(),
            contacto: contacto,
            direccion: inputs.direccion.value.trim()
        };

        // Caso 1: No hay cambios
        const isUnchanged = Object.keys(clienteData).every(
            key => clienteData[key] === (initialData[key] ?? "")
        );
        if (isUnchanged) {
            toast.show('error', 'Error', 'No se han realizado cambios en la información del cliente.');
            return;
        }

        // Caso 2: Crear nuevo cliente
        if (!clienteData.id) {
            console.log("Creando nuevo cliente:", clienteData);
            SPINNER.showSpinner();
            let tipoIdentificacion = "pasaporte";
            const idLength = clienteData.identificacion.length;
            if (idLength === 10) {
                tipoIdentificacion = "cedula";
            } else if (idLength === 13) {
                tipoIdentificacion = "ruc";
            }
            // Preparar datos para el backend
            const payload = {
                nombres: clienteData.nombres,
                apellidos: clienteData.apellidos,
                numero_identificacion: clienteData.identificacion,
                tipo_identificacion: tipoIdentificacion,
                email: clienteData.correo,
                telefono: clienteData.contacto,
                direccion: clienteData.direccion
            };
            fetch("/actions/clientes", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        toast.show('success', 'Éxito', data.message || 'Cliente creado correctamente.');
                        // Asignar el ID que devuelve el backend al input hidden
                        if (data.data?.id) {
                            inputs.id.value = data.data.id;
                        }
                        // Actualizar initialData para futuras comparaciones
                        initialData = { ...clienteData, id: data.data?.id ?? "" };
                    } else {
                        if (data.message?.errors && Array.isArray(data.message.errors)) {
                            // Concatenar todos los errores en un solo string
                            const errores = data.message.errors.join("\n");
                            toast.show('error', 'Error', `${data.message.title}\n${errores}`);
                            console.warn(errores);
                        } else {
                            // Mensaje general si no hay array de errores
                            toast.show('error', 'Error', data.message || 'Error al crear el cliente.');
                            console.warn(data.message);
                        }
                    }
                })
                .catch(err => {
                    console.error("Error al crear cliente:", err);
                    toast.show('error', 'Error', 'No se pudo crear el cliente.');
                })
                .finally(() => SPINNER.hideSpinner());
        } else {
            if (clienteData.identificacion === "9999999999999") {
                toast.show('error', 'Error', 'No se puede modificar la información del Consumidor Final.');
                return; // Salimos sin actualizar nada
            }
            // Caso 3: Actualizar cliente existente
            console.log("Actualizando cliente existente:", clienteData);
            SPINNER.showSpinner();

            // Preparar solo los campos que se van a actualizar
            const payload = {
                nombres: clienteData.nombres,
                apellidos: clienteData.apellidos,
                email: clienteData.correo,
                telefono: clienteData.contacto,
                direccion: clienteData.direccion,
            };

            // Determinar tipo de identificación si quieres (opcional)
            if (clienteData.identificacion.length === 10) payload.tipo_identificacion = "cedula";
            else if (clienteData.identificacion.length === 13) payload.tipo_identificacion = "ruc";
            else payload.tipo_identificacion = "pasaporte";

            fetch(`/actions/clientes/${clienteData.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        toast.show('success', 'Éxito', data.message || 'Cliente actualizado correctamente.');
                        // Actualizar initialData para futuras comparaciones
                        initialData = { ...clienteData };
                    } else {
                        // Si el backend devuelve un objeto de validación
                        if (data.message?.title && data.message?.errors) {
                            const errores = data.message.errors.join('<br>');
                            toast.show('error', data.message.title, errores);
                        } else {
                            toast.show('error', 'Error', data.message || 'Error al actualizar el cliente.');
                        }
                        console.warn(data.message);
                    }
                })
                .catch(err => {
                    toast.show('error', 'Error', 'No se pudo actualizar el cliente.');
                    console.error(err);
                })
                .finally(() => SPINNER.hideSpinner());
        }

        // Actualizar initialData después de guardar para futuras comparaciones
        initialData = { ...clienteData };
    });
}
// endregion

// region Productos

function setupBuscarProductoInput() {
    const searchInput = document.getElementById("search-input");
    let timeout = null;

    searchInput.addEventListener("input", () => {
        const dataFacturaId = imprimirFactura.dataset.factura_id;

        if (dataFacturaId) {
            searchInput.disabled = true;

            Swal.fire({
                icon: 'warning',
                title: 'Factura ya guardada',
                text: 'No se pueden agregar más productos a una factura ya guardada.',
                confirmButtonText: 'Aceptar'
            });
            return;
        }

        const query = searchInput.value.trim();

        // Limpiar dropdown al escribir
        clearDropdown();

        if (!query) return;

        clearTimeout(timeout);
        timeout = setTimeout(async () => {
            try {
                const customerId = document.getElementById('customer-id').value.trim();
                const customerIdentification = document.getElementById('factura-identificación').value.trim();

                if (!customerId) {
                    toast.show('warning', 'Alerta', 'Por favor, ingrese o seleccione un cliente antes de buscar productos.');
                    return;
                }

                SPINNER.showSpinner();
                const response = await fetch("/actions/productos/buscar", {
                    method: "POST",
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value
                    },
                    body: JSON.stringify({ query, customerIdentification, customerId })
                });

                if (!response.ok) throw new Error(`HTTP error! ${response.status}`);
                const data = await response.json();

                if (data.success) {
                    renderDropdownProductos(data.productos || []);
                    console.log(data.productos)
                } else {
                    renderNoResults();
                }
            } catch (err) {
                console.error("Error buscando productos:", err);
                renderNoResults();
            } finally {
                SPINNER.hideSpinner();
            }
        }, 700); // debounce 700ms
    });

    // Cerrar dropdown al hacer clic fuera
    // document.addEventListener('click', (e) => {
    //     if (!searchInput.contains(e.target) && !document.getElementById('dropdownContainerProductos').contains(e.target)) {
    //         hideDropdown();
    //         searchInput.value = "";
    //     }
    // });

    // Cerrar dropdown y limpiar input con ESC
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            hideDropdown();
            searchInput.value = "";
        }
    });
}

// Función para limpiar el dropdown
function clearDropdown() {
    const container = document.getElementById("dropdownProductos");
    const wrapper = document.getElementById("dropdownContainerProductos");

    container.innerHTML = "";
    wrapper.classList.add("hidden");
}

// Función para ocultar el dropdown
function hideDropdown() {
    const wrapper = document.getElementById("dropdownContainerProductos");
    wrapper.classList.add("hidden");
}

// Función para mostrar mensaje de no resultados
function renderNoResults() {
    const container = document.getElementById("dropdownProductos");
    const wrapper = document.getElementById("dropdownContainerProductos");

    container.innerHTML = `
        <div class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-search mb-1"></i>
            <p class="text-sm">No se encontraron productos</p>
        </div>
    `;

    positionDropdown();
    wrapper.classList.remove("hidden");
}

function renderDropdownProductos(productos) {
    const inputElement = document.getElementById("search-input");
    const wrapper = document.getElementById("dropdownContainerProductos");
    const container = document.getElementById("dropdownProductos");

    // Limpiar resultados anteriores
    container.innerHTML = "";

    if (!productos.length) {
        renderNoResults();
        return;
    }

    // Insertar productos
    productos.forEach(prod => {

        const option = document.createElement("div");
        option.className = "px-4 py-3 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0";

        option.dataset.selectable = prod.selectable;

        // Texto a poner en la busqueda
        option.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">${prod.nombre}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">${prod.categoria?.nombre || "Sin categoría"}</div>
                </div>
                <div class="text-xs font-semibold text-blue-600 dark:text-blue-400 ml-2">$${parseFloat(prod.precio_venta || 0).toFixed(2)}</div>
            </div>
            <div class="text-[11px] text-gray-500 dark:text-gray-500 mt-1">
                ${prod.tipo === 'ticket'
                ? `Jornadas: ${prod.jornadas?.length || 0}`
                : prod.tipo === 'abono'
                    ? `Entradas del abono: ${prod.abono.numero_entradas || 'N/A'}`
                    : `Código: ${prod.id || "N/A"}`
            }
            </div>
        `;

        option.addEventListener("click", () => {
            // inputElement.value = prod.nombre;
            hideDropdown();
            const customerIdentification = document.getElementById('factura-identificación').value.trim();
            if (option.dataset.selectable === "false" && customerIdentification === "9999999999999") {
                toast.show('warning', 'Alerta', prod.message || 'No se puede seleccionar abono cuando el cliente es CONSUMIDOR FINAL.');
                return;
            }
            // Mostrar dropdown de jornadas
            if (prod.tipo === "ticket" && Array.isArray(prod.jornadas)) {
                renderDropdownJornadas(prod, prod.jornadas);
            } else {
                hideDropdownOpciones();
                addProductToTable(prod);
                document.getElementById("search-input").value = "";
            };
        });

        container.appendChild(option);
    });

    // Posicionar y mostrar dropdown
    positionDropdown();
    wrapper.classList.remove("hidden");
}

// Función para posicionar el dropdown correctamente
function positionDropdown() {
    const inputElement = document.getElementById("search-input");
    const wrapper = document.getElementById("dropdownContainerProductos");

    // Ajustar ancho/posición al input
    wrapper.style.width = `${inputElement.offsetWidth}px`;
    wrapper.style.position = "absolute";
    wrapper.style.top = `${inputElement.offsetTop + inputElement.offsetHeight + 2}px`;
    wrapper.style.left = `${inputElement.offsetLeft}px`;
    wrapper.style.zIndex = 9999;
}

// dropdown de jornadas
function renderDropdownJornadas(product, jornadas) {
    const wrapper = document.getElementById("dropdownContainerOpciones");
    const container = document.getElementById("dropdownOpciones");
    const searchInput = document.getElementById("search-input");

    // Limpiar antes de insertar
    container.innerHTML = "";

    if (!jornadas.length) {
        container.innerHTML = `
            <div class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-calendar-times mb-1"></i>
                <p class="text-sm">No hay jornadas disponibles</p>
            </div>
        `;
        wrapper.classList.remove("hidden");
        return;
    }

    jornadas.forEach(jornada => {
        const option = document.createElement("div");
        option.className = "px-4 py-3 cursor-pointer hover:bg-green-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0";

        option.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-1">${jornada.nombre}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <div>Inicio: ${formatFecha(jornada.fecha_inicio)}</div>
                        <div>Fin: ${formatFecha(jornada.fecha_fin)}</div>
                    </div>
                </div>
                <div class="text-xs font-semibold text-green-600 dark:text-green-400 ml-2 whitespace-nowrap">Stock: ${jornada.stock_actual}</div>
            </div>
        `;


        option.addEventListener("click", () => {
            addProductToTable(product, jornada);
            console.log("Jornada seleccionada:", jornada);
            document.getElementById("search-input").value = "";
            wrapper.classList.add("hidden");
        });

        container.appendChild(option);
    });

    wrapper.classList.remove("hidden");

    // Listener para cerrar con ESC
    const escListener = (e) => {
        if (e.key === "Escape") {
            wrapper.classList.add("hidden");
            searchInput.value = "";
            document.removeEventListener("keydown", escListener);
        }
    };

    // Agregar listener
    document.addEventListener("keydown", escListener);
}

// Formatear fecha
function formatFecha(fechaStr) {
    if (!fechaStr) return "";

    // Separar fecha y hora
    const [fecha, hora] = fechaStr.split(" ");
    const [anio, mes, dia] = fecha.split("-");
    const [hh, mm] = hora.split(":");

    return `${dia}/${mes}/${anio} ${hh}:${mm}`;
}

// Oculatar el dropdown de jornadas
function hideDropdownOpciones() {
    const wrapper = document.getElementById("dropdownContainerOpciones");
    wrapper.classList.add("hidden");
}

// endregion

// region Add Productos Table
function addProductToTable(product, jornada = null) {
    const existingProd = products.find(p =>
        p.id === product.id && p.jornada_id === (jornada?.id || null)
    );

    if (existingProd) {
        // Si ya existe, aumentar cantidad
        existingProd.cantidad += 1;
        renderProductsTable();
        return;
    }

    // Si no existe, crear nuevo registro
    const newProd = {
        id: product.id,
        codigo: product.id, // puedes cambiar si tienes un código real
        nombre: jornada
            ? `${product.nombre} - ${jornada.nombre}`
            : `${product.nombre} - ${product.abono?.nombre || ''}`,
        cantidad: 1,
        precio_unitario: parseFloat(product.precio_sin_iva),
        precio_unitario_iva: parseFloat(product.precio_venta),
        impuesto: parseFloat(product.impuesto),
        abono_id: product.tipo === 'abono' ? product.abono.id : '', // si es abono
        jornada_id: product.tipo === 'ticket' ? jornada?.id || '' : '', // si es ticket
        stock_actual: product.tipo === 'ticket'
            ? jornada?.stock_actual
            : (product.tipo === 'abono' ? product.cantidad_actual : '')
    };

    products.push(newProd);
    renderProductsTable();
}

// Función para renderizar la tabla
function renderProductsTable() {
    productsTbody.innerHTML = "";

    products.forEach((prod, index) => {
        const tr = document.createElement("tr");
        console.log("Esto es producto ", prod)
        tr.innerHTML = `
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${prod.codigo}</span>
                <input type="hidden" name="products[${index}][producto_id]" value="${prod.id}">
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <span class="text-theme-sm mb-0.5 block font-medium text-gray-700 dark:text-gray-400">${prod.nombre}</span>
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <span class="text-gray-700 text-theme-sm dark:text-gray-400">${prod.stock_actual !== undefined ? prod.stock_actual : ''}</span>
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <input type="number" min="1" value="${prod.cantidad}" class="w-14 rounded border border-blue-200 bg-white px-2 py-1 text-sm text-gray-700 outline-none ring-0 transition focus:border-blue-500 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-blue-500 dark:focus:ring-blue-400/40 cantidad-input">
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <!-- Campos ocultos -->
                <input type="hidden" name="products[${index}][precio_unitario]" value="${prod.precio_unitario}">
                <input type="hidden" name="products[${index}][precio_unitario_iva]" value="${prod.precio_unitario_iva}">
                <input type="hidden" name="products[${index}][impuesto]" value="${prod.impuesto}">

                <!-- Input visible toma precio_unitario -->
                <input type="number" step="0.01" value="${prod.precio_unitario}" class="w-20 rounded border border-blue-200 bg-white px-2 py-1 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring focus:ring-blue-200/50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-blue-500 dark:focus:ring-blue-400/40 precio-input">
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <span class="text-gray-700 text-theme-sm dark:text-gray-400 total-cell">${(prod.cantidad * prod.precio_unitario).toFixed(4)}</span>
            </td>
            <td class="px-6 py-3 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-2">
                    <button type="button" class="text-gray-600 dark:text-gray-400 eliminar-btn">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </td>

            <!-- Campos ocultos adicionales -->
            <input type="hidden" name="products[${index}][abono_id]" value="${prod.abono_id}">
            <input type="hidden" name="products[${index}][jornada_id]" value="${prod.jornada_id}">
        `;

        productsTbody.appendChild(tr);

        // Capturar input de cantidad y agregar listener
        const inputCantidad = tr.querySelector('input[type="number"]');
        inputCantidad.addEventListener('change', (e) => {
            const cantidad = parseInt(e.target.value);
            if (cantidad < 1) return;
            products[index].cantidad = cantidad;
            renderProductsTable();
        });

        // Capturar input de precio
        const inputPrecio = tr.querySelector('.precio-input');
        inputPrecio.addEventListener('change', (e) => {
            let precioIva = parseFloat(e.target.value);
            if (isNaN(precioIva) || precioIva < 0) precioIva = products[index].precio_unitario_iva;

            products[index].precio_unitario_iva = precioIva;
            // Calcular precio_unitario base: precio sin impuesto
            products[index].precio_unitario = (precioIva / (1 + products[index].impuesto)).toFixed(4);

            renderProductsTable(); // recalcula la tabla
        });

        // Capturar botón eliminar y agregar listener
        const btnEliminar = tr.querySelector('button');
        btnEliminar.addEventListener('click', () => {
            products.splice(index, 1);
            renderProductsTable();
        });
    });
    updateResumenPedido();
}

// Llamar esta función cada vez que cambie la tabla o el input de Descuento/Adicional
document.getElementById('descuento').addEventListener('input', updateResumenPedido);
document.getElementById('adicional').addEventListener('input', updateResumenPedido);

// Calcular los valores de mi factura
function updateResumenPedido() {
    let subtotal15 = 0;
    let subtotal5 = 0; // siempre 0
    let subtotal0 = 0;
    let iva15 = 0;
    let iva5 = 0; // siempre 0
    let ice = 0; // siempre 0
    let adicional = parseFloat(document.getElementById('adicional').value) || 0;
    let descuento = parseFloat(document.getElementById('descuento').value) || 0;

    products.forEach(prod => {
        const cantidad = prod.cantidad || 1;
        const precioUnitario = prod.precio_unitario || 0;
        const precioUnitarioIva = prod.precio_unitario_iva || 0;
        const impuesto = prod.impuesto || 0;

        if (impuesto === 0.15) {
            subtotal15 += precioUnitario * cantidad;
            iva15 += (precioUnitarioIva - precioUnitario) * cantidad; // cálculo IVA
        } else if (impuesto === 0.05) {
            subtotal5 += precioUnitario * cantidad;
            iva5 += (precioUnitarioIva - precioUnitario) * cantidad;
        } else if (impuesto === 0) {
            subtotal0 += precioUnitario * cantidad;
        }

        // ICE si existiera, por ahora dejamos 0
    });

    const total = subtotal15 + subtotal5 + subtotal0 + iva15 + iva5 + ice + adicional - descuento;

    // Actualizar los campos en el HTML
    document.getElementById('subtotal15').textContent = `$${subtotal15.toFixed(2)}`;
    document.getElementById('subtotal5').textContent = `$${subtotal5.toFixed(2)}`;
    document.getElementById('subtotal0').textContent = `$${subtotal0.toFixed(2)}`;
    document.getElementById('iva15').textContent = `$${iva15.toFixed(2)}`;
    document.getElementById('iva5').textContent = `$${iva5.toFixed(2)}`;
    document.getElementById('ice').textContent = `$${ice.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

// endregion

// region factura

function setupGuardarFacturaButton() {
    const guardarFacturaBtn = document.getElementById('enviarFactura');

    guardarFacturaBtn.addEventListener('click', (e) => {
        e.preventDefault();
        // Verificar que haya al menos un producto
        if (products.length === 0) {
            toast.show('error', 'Error', 'Debe agregar al menos un producto a la factura.');
            return;
        }
        // Mostrar spinner
        SPINNER.showSpinner();
        // Obtener datos del cliente
        const clienteData = {
            id: document.getElementById('customer-id').value,
            identificacion: document.getElementById('factura-identificación').value,
            nombres: document.getElementById('customer-nombres').value,
            apellidos: document.getElementById('customer-apellidos').value,
            correo: document.getElementById('customer-correo').value,
            contacto: document.getElementById('customer-contacto').value,
            direccion: document.getElementById('customer-direccion').value
        };

        // Obtener productos de la tabla
        const productosData = products.map((prod, index) => ({
            id: prod.id,
            codigo: prod.codigo,
            nombre: prod.nombre,
            cantidad: prod.cantidad,
            precio_unitario: prod.precio_unitario,
            precio_unitario_iva: prod.precio_unitario_iva,
            impuesto: prod.impuesto,
            abono_id: prod.abono_id ? parseInt(prod.abono_id) : null,
            jornada_id: prod.jornada_id ? parseInt(prod.jornada_id) : null,
            stock_actual: prod.stock_actual,
            total: (prod.cantidad * prod.precio_unitario).toFixed(4)
        }));

        // Obtener resumen de la compra
        const resumenData = {
            subtotal15: parseDecimal(document.getElementById('subtotal15').textContent),
            subtotal5: parseDecimal(document.getElementById('subtotal5').textContent),
            subtotal0: parseDecimal(document.getElementById('subtotal0').textContent),
            descuento: parseDecimal(document.getElementById('descuento').textContent),
            iva15: parseDecimal(document.getElementById('iva15').textContent),
            iva5: parseDecimal(document.getElementById('iva5').textContent),
            ice: parseDecimal(document.getElementById('ice').textContent),
            adicional: parseDecimal(document.getElementById('adicional').textContent),
            total: parseDecimal(document.getElementById('total').textContent),
            forma_pago: 1 // Ajustar despues
        };

        if (resumenData.total < 0) {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'El total de la factura no puede ser cero o un valor negativo.');
            return;
        }


        if (resumenData.total > 50 && clienteData.identificacion === "9999999999999") {
            SPINNER.hideSpinner();
            toast.show('error', 'Error', 'No se puede facturar más de $50 al Consumidor Final. Por favor, seleccione un cliente diferente a CONSUMIDOR FINAL que tiene el numero de identificacion 9999999999999.');
            return;
        }

        console.log(resumenData)
        // Armar payload
        const payload = {
            cliente: clienteData,
            productos: productosData,
            resumen: resumenData
        };

        console.log("📤 Enviando payload al backend:", payload);

        fetch('/actions/facturas/guardarFactura', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                SPINNER.hideSpinner();
                if (data.success) {
                    // toast.show('success', 'Factura guardada', 'La factura se guardó correctamente.');
                    Swal.fire({
                        icon: 'success',
                        title: 'Factura almacenada con éxito',
                        text: 'La factura se guardó correctamente.',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // location.reload();
                    });
                    imprimirFactura.dataset.factura_id = data.factura_id;
                    guardarFacturaBtn.disabled = true;
                    setTimeout(() => {
                        imprimirFactura.click();
                    }, 1500);
                } else {
                    toast.show('error', 'Error', data.message || 'No se pudo guardar la factura.');
                }
            })
            .catch(error => {
                SPINNER.hideSpinner();
                console.error("❌ Error en la petición:", error);
                toast.show('error', 'Error', 'Hubo un problema al enviar la factura.');
            });
    });
}

function parseDecimal(str) {
    return parseFloat(str.replace(/[^0-9.-]+/g, "")) || 0;
}

// endregion

// region btn Nueva Factura
function initNuevaFactura() {
    const btnNuevaFactura = document.getElementById("nuevaFactura");
    if (!btnNuevaFactura) return;

    btnNuevaFactura.addEventListener("click", () => {
        Swal.fire({
            title: 'Crear nueva factura',
            text: 'Si no ha guardado la factura actual, los datos se perderán. ¿Desea continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Factura Nueva',
            cancelButtonText: 'Cancelar',
            reverseButtons: false, // false para que Cancelar quede a la izquierda y Confirmar a la derecha
            buttonsStyling: false,
            customClass: {
                confirmButton: 'bg-brand-500 hover:bg-brand-600 text-white font-medium px-4 py-2 rounded-lg shadow-theme-xs transition ml-3', // ml-3 añade separación izquierda
                cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded-lg shadow-theme-xs transition'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    });
}

function setupImprimirFacturaButton() {
    const imprimirFactura = document.getElementById("imprimirFactura");
    if (!imprimirFactura) return;

    imprimirFactura.addEventListener("click", (e) => getPdfInvoice(imprimirFactura));
}

async function getPdfInvoice(printButton) {
    const facturaId = printButton.dataset.factura_id;
    if (!facturaId) {
        toast.show('error', 'Error', 'No hay factura para imprimir. Por favor, guarde la factura primero.');
        return;
    }

    try {
        SPINNER.showSpinner();
        const urlToFetch = `/actions/facturas/factura-pdf/${facturaId}`;

        const response = await fetch(urlToFetch, {
            method: 'GET',
            headers: {
                'Accept': 'application/pdf'
            }
        });

        const blob = await response.blob();

        const url = URL.createObjectURL(blob);
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = url;
        document.body.appendChild(iframe);
        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    } catch (err) {
        toast.show('error', 'Error', 'No se pudo generar el PDF de la factura.');
    } finally {
        SPINNER.hideSpinner();
    }
}
// endregion