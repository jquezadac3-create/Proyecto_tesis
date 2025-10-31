@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/productos/productosLista.js', 'resources/js/utils/deleteModal.js'])
@endsection

@section('title', 'Productos')

@section('breadcrumb')
    <div x-show="selected === 'products.list'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                showDeleteForm: false,
                showBoletoModal: false,
                showStockModal: false,
                showStockModalTickets: false,
                form: {
                    id: '',
                    nombre: '',
                    cantidad: '',
                    tipo_producto: 'producto',
                    impuesto: 0.15,
                    precioSinIva: '',          
                    precioSinIvaFormatted: '', 
                    precioConIva: '',          
                    precioConIvaFormatted: '', 
                    costo: '',
                    categoria: '',
                    categoriaTexto: '',
                    abono: '',
                    abonoLocked: false,
                    abonoId: '',
                    desglosarIva: false
                },
                <!-- formulario de boletos -->
                boletoForm: { 
                    idProducto: '',
                    jornada: '',
                    cantidad: ''
                },
                {{-- formulario de abonos stock --}}
                stockForm: { 
                    idProducto: '',
                    stockActual: '',
                    cantidad: '',
                    motivo: ''  
                },

                stockFormTickets: {
                    idTicket: '',
                    ticketSeleccionado: '',
                    stockActual: '',
                    stockDisponible: '',
                    cantidad: 0,
                    motivo: ''
                },

                // Variables controladores de cantidad stock productos
                currentVal: 0,
                minVal: -9999,
                maxVal: 9999,
                incrementAmount: 1,

                // Variables controladores de cantidad stock tickets
                currentValTickets: 0,
                minValTickets: -9999,
                maxValTickets: 9999,
                incrementAmountTickets: 1,

                resetForm() {
                    this.form.id = '';
                    this.form.nombre = '';
                    this.form.cantidad = '';
                    this.form.tipo_producto = 'producto';
                    this.form.impuesto = 0.15;
                    this.form.precioSinIva = '';
                    this.form.precioSinIvaFormat = '';
                    this.form.precioConIva = '';
                    this.form.precioConIvaFormat = '';
                    this.form.precioSinIvaFormatted = '';
                    this.form.precioConIvaFormatted = '';
                    this.form.costo = '';
                    this.form.categoria = '';
                    this.form.categoriaTexto = '';
                    this.form.abono = '';
                    this.form.abonoId = '';
                    this.form.desglosarIva = false;
                    this.form.esAbono = false;
                    this.$nextTick(() => {
                        document.getElementById('precioSinIvaFormat').value = '';
                        document.getElementById('precioSinIva').value = '';
                        document.getElementById('precioConIvaFormat').value = '';
                        document.getElementById('precioConIva').value = '';
                    });
                },
                resetBoletoForm() { 
                    this.boletoForm.idProducto = '';
                    this.boletoForm.jornada = '';
                    this.boletoForm.cantidad = '';
                    this.jornadas = [];
                },
                resetStockForm() {
                    this.stockForm.idProducto = '';
                    this.stockForm.stockActual = '';
                    this.stockForm.cantidad = 0;
                    this.stockForm.motivo = '';
                    this.currentVal = 0; 
                },

                resetStockFormTickets() {
                    this.stockFormTickets.idTicket = '';
                    this.stockFormTickets.ticketSeleccionado = '';
                    this.stockFormTickets.stockActual = '';
                    this.stockFormTickets.stockDisponible = '';
                    this.stockFormTickets.cantidad = 0;
                    this.stockFormTickets.motivo = '';
                    this.currentValTickets = 0;
                },

                jornadas: [],
                ticketsDisponibles: [],
                
                cargarJornadas(idProducto, crsfToken) {
                    
                    this.jornadas = [];

                    fetch('/actions/jornadas/disponibles', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': crsfToken,
                        },
                        body: JSON.stringify({ idProducto })
                    })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data)
                            if (data.success && data.data.length > 0) {
                                // Solo actualizar el array, Alpine se encarga del select
                                this.jornadas = data.data.map(j => ({
                                    id: j.id,
                                    nombre: `${j.nombre} (Aforo disponible: ${j.aforo_restante})`,
                                    aforo_restante: j.aforo_restante
                                }));
                                this.boletoForm.jornada = '';
                                this.boletoForm.cantidad = ''; 
                            }
                        })
                    .catch(err => console.error('Error cargando jornadas:', err));
                },
                abrirModalStock(idProducto, stockActual) {
                    this.stockForm.idProducto = idProducto;
                    this.stockForm.stockActual = stockActual;
                    this.stockForm.cantidad = 0;
                    this.currentVal = 0; 
                    this.stockForm.motivo = '';
                    this.showStockModal = true;
                },

                abrirModalStockTickets(idTicket, crsfToken) {
                    this.stockFormTickets.idTicket = idTicket;
                    this.stockFormTickets.ticketSeleccionado = '';
                    this.stockFormTickets.stockActual = '';
                    this.stockFormTickets.stockDisponible = '';
                    this.stockFormTickets.cantidad = 0;
                    this.currentValTickets = 0; 
                    this.stockFormTickets.motivo = '';

                    document.dispatchEvent(new CustomEvent('loading', { detail: { loading: true } }));
                    fetch(`/actions/productos/${idTicket}/jornadas-disponibles`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': crsfToken,
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                        console.log(data)
                        if (data.success && data.data.length > 0) {
                            this.ticketsDisponibles = data.data.map(j => ({
                                id: j.id,
                                nombre: `${j.nombre} (Stock: ${j.stock_asignado}, Disp: ${j.aforo_disponible})`,
                                stockActual: j.stock_asignado,
                                stockDisponible: j.aforo_disponible
                            }));

                            // Limpiar selección inicial
                            this.stockFormTickets.ticketSeleccionado = '';
                            this.stockFormTickets.stockActual = '';
                            this.stockFormTickets.stockDisponible = '';
                        }
                    })
                    .catch(err => {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                        console.error('Error cargando jornadas:', err);
            
                    });

                    this.showStockModalTickets = true;
                },
                calculateTax() {
                    const sinIva = parseFloat(this.form.precioSinIva);
                    const impuestoNum = parseFloat(this.form.impuesto) || 0;
                    
                    if (isNaN(sinIva)) {
                        this.form.precioConIva = '';
                        this.form.precioSinIva = '';
                        this.form.precioSinIvaFormat = '';
                        this.form.precioConIvaFormat = '';
                        return;
                    }

                    // Caso: usuario ingresa precio base (sin IVA)
                    if (this.form.desglosarIva) {
                        const precioConIvaCalculado = sinIva * (1 + impuestoNum);
                        
                        // Para campos visibles (2 decimales)
                        this.form.precioConIva = this.roundToDecimalPlaces(precioConIvaCalculado, 2);
                        
                        // Para campos ocultos (4 decimales)
                        this.form.precioSinIvaFormat = this.roundToDecimalPlaces(sinIva, 4);
                        this.form.precioConIvaFormat = this.roundToDecimalPlaces(precioConIvaCalculado, 4);
                    
                    // Caso: usuario ingresa precio final (con IVA) en el campo sin IVA
                    } else {
                        const precioConIvaNum = sinIva;
                        const precioSinIvaCalculado = precioConIvaNum / (1 + impuestoNum);
                        
                        // Para campos visibles (2 decimales)
                        this.form.precioConIva = this.roundToDecimalPlaces(precioConIvaNum, 2);
                        this.form.precioSinIva = this.roundToDecimalPlaces(precioSinIvaCalculado, 2);
                        
                        // Para campos ocultos (4 decimales)
                        this.form.precioSinIvaFormat = this.roundToDecimalPlaces(precioSinIvaCalculado, 4);
                        this.form.precioConIvaFormat = this.roundToDecimalPlaces(precioConIvaNum, 4);
                    }
                    
                },
                roundToDecimalPlaces(num, places) {
                    const multiplier = 10 ** places;
                    return Math.round(num * multiplier) / multiplier;
                },
                // Verificar si un ticket tiene asignada una jornada
                checkTicketTieneJornadas(idTicket, csrfToken) {
                    //iniciar el spinner
                    document.dispatchEvent(new CustomEvent('loading', { detail: { loading: true } }));
                    return fetch(`/actions/productos/${idTicket}/tiene-jornadas`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        //cerrar el spinner
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                        return data.tieneJornadas === true;
                    })
                    .catch(err => {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                        console.error('Error verificando jornadas:', err);
                        return false;
                    });
                },
                handleTicketStockClick(prodId, csrfToken) {
                    this.checkTicketTieneJornadas(prodId, csrfToken).then(tiene => {
                        if (tiene) {
                            this.stockFormTickets.idTicket = prodId;
                            this.abrirModalStockTickets(prodId, csrfToken);
                        } else {
                            // Usar Alpine.js $nextTick para asegurar que el toast se muestre correctamente
                            this.$nextTick(() => {
                                toast.show('error', 'Error', 'No se puede agregar stock a tickets que no tienen jornadas asignadas. Por favor asigne al menos una jornada a este ticket.');
                            });
                        }
                    });
                },
            }"
            >

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de productos` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Productos
                            </h2>
                            <nav>
                                <ol class="flex items-center gap-1.5">
                                    <li>
                                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                            href="{{ route('dashboard.index') }}">
                                            Home
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </li>
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">
                                        Registro de productos
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->
                    <x-productos.producto-list.table />

                    <x-productos.producto-list.create />
                    
                    <x-productos.producto-list.cantidadTickets />

                    <x-productos.producto-list.cantidadAbonos />

                    <x-productos.producto-list.stockTickets />
                    
                    <x-utils.delete-modal />
                </div>
        </body>
    </div>

@endsection
