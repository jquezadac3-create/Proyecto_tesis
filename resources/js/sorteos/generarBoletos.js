import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';
import "../../css/custom-choices.css"
import * as SPINNER from "@utils/spinner.js";
import Swal from 'sweetalert2';
import confetti from 'canvas-confetti';
import { DataTable } from "simple-datatables";
// import "simple-datatables/dist/style.css";

let choicesPeriodo = null;
let choicesJornada = null;

// Variables globales corregidas
let totalPremios = 0;
let numIntentosPorPremio = 0;
let premiosSorteados = [];      // Boletos ganadores
let boletosSorteo = [];         // Boletos totales
let boletosYaSalieron = [];     // Boletos que ya participaron 
let sorteoIdActual = null;      // Guardar el id del sorteo
let tablaBoletos = null;

document.addEventListener('DOMContentLoaded', cargarPeriodos);

async function cargarPeriodos() {
    const selectPeriodo = document.getElementById('selectPeriodo');
    const selectJornada = document.getElementById('selectJornada');

    // Verificar que los elementos existan
    if (!selectPeriodo || !selectJornada) {
        console.error('No se encontraron los elementos select');
        return;
    }

    SPINNER.showSpinner();

    try {
        const response = await fetch('/actions/sorteos/obetenerPeriodos');
        if (!response.ok) throw new Error('Error al traer los periodos');

        const periodos = await response.json();

        // Opción por defecto
        selectPeriodo.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        defaultOption.textContent = 'Seleccione un periodo';
        selectPeriodo.appendChild(defaultOption);

        // Agregar periodos
        periodos.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.textContent = `${p.nombre} (${formatearFecha(p.fecha_inicio)} - ${formatearFecha(p.fecha_fin)})`;
            option.dataset.fechaInicio = p.fecha_inicio;
            option.dataset.fechaFin = p.fecha_fin;
            selectPeriodo.appendChild(option);
        });

        // Inicializar Choice de periodos
        if (choicesPeriodo) choicesPeriodo.destroy();
        choicesPeriodo = new Choices(selectPeriodo, {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Seleccione un periodo',
            classNames: { containerOuter: 'choices-periodos' },
        });

        // Inicializar Choice de jornadas vacío
        if (choicesJornada) choicesJornada.destroy();
        choicesJornada = new Choices(selectJornada, {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Seleccione jornadas',
            removeItemButton: true,
            classNames: { containerOuter: 'choices-jornadas' },
        });

        // Evento al cambiar el periodo
        selectPeriodo.addEventListener('change', manejarCambioPeriodo);

    } catch (error) {
        console.error('Error cargando periodos:', error);
        // Mostrar mensaje de error en el select de jornadas
        if (choicesJornada) {
            choicesJornada.setChoices([{
                value: '',
                label: 'Error cargando periodos',
                disabled: true
            }], 'value', 'label', true);
        }
    } finally {
        SPINNER.hideSpinner(); 
    }
}

async function manejarCambioPeriodo(event) {
    const selectPeriodo = document.getElementById('selectPeriodo');
    const selectJornada = document.getElementById('selectJornada');
    const selectedOption = selectPeriodo.options[selectPeriodo.selectedIndex];
    
    if (!selectedOption || !selectedOption.value) return;

    const fechaInicio = selectedOption.dataset.fechaInicio;
    const fechaFin = selectedOption.dataset.fechaFin;
    
    // Habilitar el select de jornadas
    selectJornada.disabled = false;
    choicesJornada.enable();

    SPINNER.showSpinner();

    try {
        const response = await fetch('/actions/sorteos/obtenerJornadas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin })
        });

        if (!response.ok) throw new Error('Error al obtener jornadas');

        const data = await response.json();

        if (data.jornadas && data.jornadas.length > 0) {
            const choices = data.jornadas.map(j => ({
                value: j.id,
                label: `${j.nombre}`,
                selected: false
            }));
            choicesJornada.setChoices(choices, 'value', 'label', true); 
        } else {
            choicesJornada.setChoices([{
                value: '',
                label: 'No hay jornadas disponibles',
                disabled: true
            }], 'value', 'label', true);
        }

    } catch (err) {
        console.error("Error obteniendo jornadas:", err);
        choicesJornada.setChoices([{
            value: '',
            label: 'Error cargando jornadas',
            disabled: true
        }], 'value', 'label', true);
    } finally {
        SPINNER.hideSpinner();
    }
}

function formatearFecha(fechaString) {
    if (!fechaString) return '';
    
    try {
        const fecha = new Date(fechaString);
        
        if (isNaN(fecha.getTime())) {
            return fechaString; 
        }
        
        const dia = String(fecha.getDate()).padStart(2, '0');
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const año = fecha.getFullYear();
        
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        console.error('Error formateando fecha:', error);
        return fechaString;
    }
}


document.getElementById('btnCrearSorteo').addEventListener('click', async () => {
    const selectPeriodo = document.getElementById('selectPeriodo');
    const selectJornada = document.getElementById('selectJornada');

    if (!selectPeriodo.value) {
        return Swal.fire('Error', 'Seleccione un periodo', 'error');
    }
    if (!selectJornada.value || selectJornada.selectedOptions.length === 0) {
        return Swal.fire('Error', 'Seleccione al menos una jornada', 'error');
    }

    const jornadasSeleccionadas = Array.from(selectJornada.selectedOptions).map(o => o.value);
    

    // SweetAlert para obtener los datos del sorteo
    const { value: formValues } = await Swal.fire({
        title: '<h3 class="text-xl font-bold text-gray-800">Crear Nuevo Sorteo</h3>',
        html:
            '<div class="space-y-4">' +
            '<div>' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Nombre del sorteo</label>' +
            '<input id="swal-sorteo-nombre" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Ingrese el nombre"  autocomplete="off">' +
            '</div>' +
            '<div class="grid grid-cols-2 gap-4">' +
            '<div>' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Número de premios</label>' +
            '<input id="swal-num-premios" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Ej: 5">' +
            '</div>' +
            '<div>' +
            '<label class="block text-sm font-medium text-gray-700 mb-1">Posición del boleto ganador</label>' +
            '<input id="swal-posicion-ganador" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" placeholder="Ej: 1">' +
            '</div>' +
            '</div>' +
            '</div>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Crear Sorteo',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'rounded-xl shadow-2xl',
            confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50',
            cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors duration-200 focus:ring-2 focus:ring-gray-300'
        },
        buttonsStyling: false,
        preConfirm: () => {
            const nombre = document.getElementById('swal-sorteo-nombre').value.trim();
            const numPremios = parseInt(document.getElementById('swal-num-premios').value);
            const posicionGanador = parseInt(document.getElementById('swal-posicion-ganador').value);

            if (!nombre) {
                Swal.showValidationMessage('Ingrese el nombre del sorteo');
                return false;
            }
            if (isNaN(numPremios) || numPremios <= 0) {
                Swal.showValidationMessage('Ingrese un número válido de premios');
                return false;
            }
            if (isNaN(posicionGanador) || posicionGanador <= 0) {
                Swal.showValidationMessage('Ingrese una posición válida para el ganador');
                return false;
            }

            return { nombre, numPremios, posicionGanador };
        }
    });

    if (!formValues) return;

    console.log('Datos del sorteo:', formValues);
    console.log('Periodo:', selectPeriodo.value);
    console.log('Jornadas seleccionadas:', jornadasSeleccionadas);
    console.log(selectPeriodo.value)
    console.log(jornadasSeleccionadas)
    try {
        SPINNER.showSpinner();

        const response = await fetch('/actions/sorteos/crearSorteo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                periodo_id: selectPeriodo.value,
                jornadas: jornadasSeleccionadas,
                nombre: formValues.nombre,
                numPremios: formValues.numPremios,
                posicionGanador: formValues.posicionGanador
            })
        });

        const result = await response.json();
        SPINNER.hideSpinner();

        if (!response.ok || !result.ok) {
            return console.error('Error creando sorteo:', result.msg);
        }

         // Guardar datos importantes
        sorteoIdActual = result.sorteo.id;
        boletosSorteo = mezclarArray(result.boletos); // Mezclar boletos para randomizar
        totalPremios = formValues.numPremios;
        numIntentosPorPremio = formValues.posicionGanador;
        premiosSorteados = [];
        boletosYaSalieron = []; // Reiniciar array

        // Actualizar las tarjetas
        document.getElementById('totalPremios').textContent = totalPremios;
        document.getElementById('premiosSorteados').textContent = premiosSorteados.length;

        // Mostrar elementos
        document.getElementById('tarjetasPremios').classList.remove('hidden');
        document.getElementById('containerGanadores').classList.remove('hidden');

        // Renderizar tablas
        renderTablaBoletos(boletosSorteo);
        renderTablaGanadores();

        Swal.fire('Éxito', 'Sorteo creado correctamente', 'success');

    } catch (err) {
        SPINNER.hideSpinner();
        console.error('Error creando sorteo:', err);
    }
});


// Función para renderizar los boletos en la tabla #tDatosBoleto
function renderTablaBoletos(boletos) {

    if (tablaBoletos) {
        tablaBoletos.destroy();
        tablaBoletos = null;
    }


    const tb = document.getElementById('tbDatosBoleto');

    // Limpiar tabla
    tb.innerHTML = '';

    if (!boletos || boletos.length === 0) {
        tb.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-3 text-center text-gray-500 dark:text-gray-400">
                    No se encontraron boletos
                </td>
            </tr>
        `;
        return;
    } 
    // Llenar tabla con los boletos
    else {
        boletos.forEach(b => {
            tb.innerHTML += `
                <tr>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.numero_factura}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.numero_boleto}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.nombre_cliente}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.nombre_producto ?? ''}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.nombre_jornada ?? '-'}</td>
                    <td class="px-6 py-3 whitespace-nowrap text-center">${b.nombre_abono ?? '-'}</td>
                </tr>
            `;
        });

        // Inicializar DataTable
        tablaBoletos = new DataTable('#tDatosBoleto', { searchable: true });
    }

    // Ocultar botón Crear Sorteo
    const btnCrear = document.getElementById('btnCrearSorteo');
    if (btnCrear) btnCrear.style.display = 'none';

    // Mostrar botones Realizar Sorteo y Nuevo Sorteo
    const containerRealizar = document.getElementById('containerRealizarSorteo');
    if (containerRealizar) containerRealizar.classList.remove('hidden');

    // Poner los selects de Choice.js en modo solo lectura
    if (choicesPeriodo) choicesPeriodo.disable();
    if (choicesJornada) choicesJornada.disable();
}

// Función para mezclar array (algoritmo Fisher-Yates)
function mezclarArray(array) {
    const nuevoArray = [...array];
    for (let i = nuevoArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [nuevoArray[i], nuevoArray[j]] = [nuevoArray[j], nuevoArray[i]];
    }
    return nuevoArray;
}

// Botón Realizar Sorteo
// document.getElementById('btnRealizarSorteo').addEventListener('click', realizarSorteo);
document.getElementById('btnRealizarSorteo').addEventListener('click', () => {
    // Debug
    mostrarEstadoBoletos();
    realizarSorteo();
});

// Función para mostrar animación de selección
function mostrarAnimacionSeleccion() {
    return Swal.fire({
        title: '🎰 Seleccionando boletos...',
        html: `
            <div class="text-center">
                <div class="inline-block animate-pulse">
                    <i class="fa-solid fa-shuffle text-4xl text-blue-500 mb-4"></i>
                </div>
                <p class="text-lg font-semibold text-gray-700">Preparando el sorteo</p>
                <p class="text-sm text-gray-500 mt-2">Por favor espere...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: 2000, // 2 segundos de suspenso
        didOpen: () => {
            // Agregar más efectos visuales
            Swal.showLoading();
        }
    });
}

// Función para lanzar confeti
function lanzarConfeti() {
    
    // Configuración del confeti
    const duration = 3 * 1000; // 3 segundos
    const animationEnd = Date.now() + duration;
    const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

    function randomInRange(min, max) {
        return Math.random() * (max - min) + min;
    }

    const interval = setInterval(function() {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
            return clearInterval(interval);
        }

        const particleCount = 50 * (timeLeft / duration);

        // Confeti desde la izquierda
        confetti({
            ...defaults,
            particleCount,
            origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }
        });

        // Confeti desde la derecha
        confetti({
            ...defaults,
            particleCount,
            origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }
        });
    }, 250);

    // También un burst inicial
    confetti({
        particleCount: 150,
        spread: 100,
        origin: { y: 0.6 },
        zIndex: 999999
    });
}


// Función principal para realizar el sorteo - MODIFICADA (SIN TIMERS)
async function realizarSorteo() {
    if (!boletosSorteo || boletosSorteo.length === 0) {
        return Swal.fire('Error', 'No hay boletos para realizar el sorteo', 'error');
    }
    
    if (premiosSorteados.length >= totalPremios) {
        return Swal.fire('Información', 'No quedan premios por sortear', 'info');
    }

    // Mostrar animación de selección al inicio
    await mostrarAnimacionSeleccion();

    // Filtrar boletos que aún no han participado
    const boletosDisponibles = boletosSorteo.filter(b => 
        !boletosYaSalieron.some(ya => ya.id === b.id)
    );

    console.log(`Boletos disponibles: ${boletosDisponibles.length}`);
    console.log(`Boletos que ya salieron: ${boletosYaSalieron.length}`);

    if (boletosDisponibles.length === 0) {
        return Swal.fire('Error', 'No hay más boletos disponibles para sortear', 'error');
    }

    // Verificar que hay suficientes boletos para este premio
    if (boletosDisponibles.length < numIntentosPorPremio) {
        return Swal.fire('Error', `No hay suficientes boletos disponibles. Se necesitan ${numIntentosPorPremio} pero solo hay ${boletosDisponibles.length}`, 'error');
    }

    // Tomar los boletos para este premio
    const boletosParaEstePremio = boletosDisponibles.slice(0, numIntentosPorPremio);
    const ganador = boletosParaEstePremio[boletosParaEstePremio.length - 1];

    console.log("Boletos para este premio:", boletosParaEstePremio.map(b => b.numero_boleto));

    // Mostrar boletos uno por uno (SIN TIMER - control manual completo)
    for (let i = 0; i < boletosParaEstePremio.length; i++) {
        const boleto = boletosParaEstePremio[i];
        const esGanador = (i === boletosParaEstePremio.length - 1);
        const numeroEliminado = i + 1;
        
        if (esGanador) {
            // Para el ganador, pedir el premio
            const { value: premio } = await Swal.fire({
                title: '🎉 ¡BOLETO GANADOR! 🎉',
                html: `
                    <div class="text-center">
                        <div class="mb-4 animate-bounce">
                            <i class="fa-solid fa-trophy text-6xl text-yellow-500"></i>
                        </div>
                        <p class="text-xl font-bold text-green-600 mb-3">¡FELICIDADES!</p>
                        <p class="text-lg font-semibold mb-2">${boleto.nombre_cliente}</p>
                        <p><strong>Número Boleto:</strong> ${boleto.numero_boleto}</p>
                        <p><strong>Factura:</strong> ${boleto.numero_factura}</p>
                        <p><strong>Producto:</strong> ${boleto.nombre_producto ?? '-'}</p>
                        <p class="text-sm text-green-600 mt-3">🎊 ¡Este es el boleto ganador! 🎊</p>
                    </div>
                `,
                input: 'text',
                inputLabel: 'Premio para el ganador:',
                inputPlaceholder: 'Ingrese el nombre del premio',
                showCancelButton: false,
                confirmButtonText: '🎁 Asignar Premio y Finalizar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe ingresar un premio';
                    }
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    // LANZAR CONFETI CUANDO SE MUESTRA EL GANADOR
                    lanzarConfeti();
                }
            });

            if (!premio) return; 

            // Preparar datos para enviar al backend
            const boletosParaActualizar = boletosParaEstePremio.map((boleto, index) => ({
                id: boleto.id,
                es_ganador: index === boletosParaEstePremio.length - 1 ? 1 : 0,
                premio_ganado: index === boletosParaEstePremio.length - 1 ? premio : null,
                ya_participo: 1,
                sorteo_id: sorteoIdActual
            }));

            // Guardar localmente
            const ganadorConPremio = {
                ...ganador,
                premio_ganado: premio,
                fecha_sorteo: new Date().toISOString()
            };

            premiosSorteados.push(ganadorConPremio);
            
            // AGREGAR TODOS LOS BOLETOS A boletosYaSalieron
            boletosYaSalieron.push(...boletosParaEstePremio.map(b => ({
                ...b,
                ya_participo: 1,
                es_ganador: b.id === ganador.id ? 1 : 0,
                premio_ganado: b.id === ganador.id ? premio : null
            })));

            console.log("Boletos que ya salieron actualizados:", boletosYaSalieron.length);
            console.log("Boletos del sorteo para backend:", boletosParaActualizar);

            // Actualizar backend con TODOS los boletos de este grupo
            await actualizarBoletosSorteo(boletosParaActualizar);

        } else {
            // Para boletos no ganadores 
            await Swal.fire({
                title: `🎯 Boleto Eliminado #${numeroEliminado}`,
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-user-slash text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold mb-2">${boleto.nombre_cliente}</p>
                        <p><strong>Número Boleto:</strong> ${boleto.numero_boleto}</p>
                        <p><strong>Factura:</strong> ${boleto.numero_factura}</p>
                        <p><strong>Posición:</strong> ${numeroEliminado} de ${numIntentosPorPremio}</p>
                        <p class="text-sm text-gray-500 mt-2">❌ Este boleto ya no participará en sorteos futuros</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: '⏭️ Siguiente Boleto',
                showCancelButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        }
    }

    // Actualizar interfaz
    actualizarInterfazDespuesSorteo();

    // Verificar si ya se sortearon todos los premios
    if (premiosSorteados.length >= totalPremios) {
        await Swal.fire({
            title: '🎊 ¡Sorteo Completado! 🎊',
            html: `
                <div class="text-center">
                    <div class="mb-4 animate-pulse">
                        <i class="fa-solid fa-flag-checkered text-6xl text-green-500"></i>
                    </div>
                    <p class="text-xl font-bold text-green-600 mb-2">¡FELICITACIONES!</p>
                    <p class="text-lg">Todos los premios han sido sorteados exitosamente</p>
                    <p class="text-sm text-gray-500 mt-3">Puede iniciar un nuevo sorteo cuando desee</p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: '👌 Entendido',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                // Última ráfaga de confeti al completar todo
                confetti({
                    particleCount: 200,
                    spread: 120,
                    origin: { y: 0.6 }
                });
            }
        });
        
        // Ocultar botón realizar sorteo y mostrar nuevo sorteo
        document.getElementById('btnRealizarSorteo').classList.add('hidden');
        document.getElementById('containerRealizarSorteo').classList.add('hidden');
        document.getElementById('containerNuevoSorteo').classList.remove('hidden');
    }
}


// Función para debug (opcional)
function mostrarEstadoBoletos() {
    console.log("=== ESTADO ACTUAL DE BOLETOS ===");
    console.log(`Total boletos: ${boletosSorteo.length}`);
    console.log(`Boletos que ya salieron: ${boletosYaSalieron.length}`);
    console.log(`Boletos disponibles: ${boletosSorteo.length - boletosYaSalieron.length}`);
    console.log("Boletos que ya salieron:", boletosYaSalieron.map(b => b.numero_boleto));
}

// Función para actualizar boletos en el backend - CORREGIDA
async function actualizarBoletosSorteo(boletos) {
    SPINNER.showSpinner();
    try {
        const response = await fetch('/actions/sorteos/actualizarBoletosSorteo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                boletos: boletos,
                sorteo_id: sorteoIdActual
            })
        });

        const result = await response.json();

        if (!response.ok || !result.ok) {
            console.error('Error actualizando boletos en backend:', result.msg);
            Swal.fire('Error', 'Error al guardar los resultados del sorteo', 'error');
        } else {
            console.log('Boletos actualizados correctamente en backend');
        }
    } catch (error) {
        console.error('Error actualizando boletos:', error);
        Swal.fire('Error', 'Error de conexión al guardar resultados', 'error');
    } finally{
        SPINNER.hideSpinner();
    }
}

// Función para actualizar interfaz después del sorteo
function actualizarInterfazDespuesSorteo() {
    // Actualizar tarjeta de premios sorteados
    document.getElementById('premiosSorteados').textContent = premiosSorteados.length;
    
    // Actualizar tabla de ganadores
    renderTablaGanadores();
}

// Función corregida para renderizar tabla de ganadores
function renderTablaGanadores() {
    const tbody = document.getElementById('cuerpoGanadores');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (premiosSorteados.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No hay ganadores aún
                </td>
            </tr>
        `;
        return;
    }

    // Usar esto para btn rehacer sorteo
    // premiosSorteados.forEach((ganador, index) => {
    //     tbody.innerHTML += `
    //         <tr class="border-b border-gray-200 dark:border-gray-700">
    //             <td class="px-6 py-4 text-center">${ganador.numero_factura}</td>
    //             <td class="px-6 py-4 text-center">${ganador.numero_boleto}</td>
    //             <td class="px-6 py-4 text-center">${ganador.nombre_cliente}</td>
    //             <td class="px-6 py-4 text-center font-semibold text-green-600">${ganador.premio_ganado}</td>
    //             <td class="px-6 py-4 text-center">
    //                 <button class="text-red-500 hover:text-red-700" onclick="eliminarGanador(${index})">
    //                     <i class="fa-solid fa-trash"></i>
    //                 </button>
    //             </td>
    //         </tr>
    //     `;
    // });

    premiosSorteados.forEach((ganador, index) => {
        tbody.innerHTML += `
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <td class="px-6 py-4 text-center">${ganador.numero_factura}</td>
                <td class="px-6 py-4 text-center">${ganador.numero_boleto}</td>
                <td class="px-6 py-4 text-center">${ganador.nombre_cliente}</td>
                <td class="px-6 py-4 text-center font-semibold text-green-600">${ganador.premio_ganado}</td>
            </tr>
        `;
    });
}

// Modificar la función del botón Nuevo Sorteo
document.getElementById('btnNuevoSorteo').addEventListener('click', () => {
    Swal.fire({
        title: 'Sorteo finalizado 🎉',
        html: `
            <p>Todos los resultados del sorteo han sido guardados correctamente.</p>
            <p class="mt-2">¿Desea iniciar un nuevo sorteo?</p>
        `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Sí, nuevo sorteo',
        cancelButtonText: 'No, quedarme aquí',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Recargar la página para nuevo sorteo
            window.location.reload();
        }
    });
});