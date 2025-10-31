@extends('layout.landing-page')

@section('title', 'Compra de Boletos')

@section('assets')
    @vite(['resources/js/boletos/boletos.js', 'resources/js/app.js'])
@endsection

@section('content')
    <main x-data="initData()">
        <!-- HERO -->
        <section id="mainContent" class="relative h-[400px]">
            <img class="w-full h-[400px]" src="{{ asset('assets/img/banner.png') }}" alt="Estadio de fútbol">
            <div class="absolute inset-0 flex items-center justify-center bg-black/50">
                <div class="text-center text-white">
                    <h2 class="mb-4 text-4xl font-bold">Compra tus entradas oficiales</h2>
                    <p class="mb-6 text-lg">Vive la emoción del fútbol</p>
                    {{-- <a href="#partidos" class="rounded-lg bg-yellow-600 px-6 py-3 font-semibold hover:bg-yellow-700">
                        <i class="fab fa-whatsapp"></i> Revisa nuestras jornadas
                    </a> --}}
                </div>
            </div>
        </section>

        <section id="buyContent">
            <x-boletos.jornadas-list :jornadas="$jornadas" />

            <x-boletos.abonos-list :abonos="$abonos" />
        </section>


        <x-boletos.checkout-jornada-form />

        <x-boletos.checkout-abonos-form />

        <script>
            function initData() {
                return {
                    showJornadaModal: false,
                    showAbonoModal: false,
                    maxItemsToBuy: 2,
                    currentItemsInCart: 0,
                    uniqueCode: '',
                    reference: '',
                    amountToPay: 0,
                    amountWithTax: 0,
                    tax: 0,
                    phoneNumber: 123,
                    email: '',
                    documentId: '',
                    identificationType: 1,
                    selectedJornada: {
                        id: 0,
                        prod_id: 0,
                        nombre: "",
                        fecha_inicio: "",
                        fecha_fin: "",
                        cantidad_aforo: "",
                        estado: "",
                        total: 0,
                        prices: [
                            {
                                id: 0,
                                nombre: "",
                                precio_venta_final: 0,
                                cantidad: 0,
                                stock: 0,
                            }
                        ]
                    },
                    selectedAbono: {
                        id: 0,
                        prod_id: 0,
                        nombre: "",
                        numero_entradas: 0,
                        descripcion: "",
                        stock: 0,
                        precio: 0,
                        precio_sin_iva: 0,
                        total: 0,
                        cantidad: 0,
                        prices: [
                            {
                                id: 0,
                                nombre: "",
                                precio_venta_final: 0,
                                precio_sin_iva: 0,
                                cantidad: 0,
                            }
                        ]
                    },
                    cartItems: [],
                    resumeData: {
                        subtotal15: 0,
                        subtotal5: 0,
                        subtotal0: 0,
                        iva15: 0,
                        iva5: 0,
                        ice: 0,
                        adicional: 0,
                        total: 0,
                        forma_pago: 2 // This can be 2 (credit) or 3 (debit)
                    },
                    customerInfo: {
                        nombres: "",
                        apellidos: "",
                        cedula: "",
                        correo: "",
                        celular: ""
                    },
                    errors: {
                        nombres: "",
                        apellidos: "",
                        cedula: "",
                        email: "",
                        telefono: "",
                    },
                    selectJornada(jornada) {
                        this.showJornadaModal = true;
                        this.selectedJornada = { ...jornada };
                    },
                    selectAbono(abono) {
                        this.showAbonoModal = true;
                        this.selectedAbono = { ...abono };
                    },
                    closeModal() {
                        this.showJornadaModal = false;
                        this.showAbonoModal = false;
                        this.currentItemsInCart = 0;
                        this.selectedJornada = {
                            id: 0,
                            nombre: "",
                            fecha_inicio: "",
                            fecha_fin: "",
                            cantidad_aforo: "",
                            estado: "",
                            total: 0,
                            prices: [
                                {
                                    id: 0,
                                    nombre: "",
                                    precio_venta_final: 0,
                                    stock: 0,
                                    cantidad: 0,
                                }
                            ]
                        };
                        this.selectedAbono = {
                            id: 0,
                            nombre: "",
                            descripcion: "",
                            numero_entradas: 0,
                            stock: 0,
                            precio: 0,
                            total: 0,
                            cantidad: 0,
                        };
                        this.customerInfo = {
                            nombres: "",
                            apellidos: "",
                            cedula: "",
                            correo: "",
                            celular: ""
                        };
                        this.errors = {
                            nombres: "",
                            apellidos: "",
                            cedula: "",
                            email: "",
                            telefono: "",
                        }
                    },
                    async fetchUniqueCode() {
                        await Swal.fire({
                            title: '¿Desea continuar?',
                            text: 'Una vez que inicie el proceso de pago, no podrá cancelarlo.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, continuar',
                            cancelButtonText: 'Cancelar',
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                this.showSpinner();
                                try {
                                    const response = await fetch("{{ route('ventas.preSellItem') }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            nombres: this.customerInfo.nombres,
                                            apellidos: this.customerInfo.apellidos,
                                            numero_identificacion: this.customerInfo.cedula,
                                            email: this.customerInfo.correo,
                                            telefono: `+593${this.customerInfo.celular.slice(1, 10)}`,
                                            items: this.cartItems,
                                            resume: this.resumeData
                                        })
                                    });

                                    const data = await response.json();

                                    if (!data.success) {
                                        const messageObj = data.message;

                                        if (Object.prototype.toString.call(messageObj) === '[object Object]') {
                                            toast.show('error', 'Error', messageObj?.title || 'Error al obtener el código único.', messageObj?.errors || []);
                                        } else {
                                            toast.show('error', 'Error', data.message || 'Error al obtener el código único.');
                                        }

                                        return;
                                    }

                                    this.uniqueCode = data.data.code;
                                    this.reference = data.data.reference;
                                } catch (error) {
                                    this.uniqueCode = '';
                                    this.reference = '';
                                    toast.show('error', 'Error', error.message || 'Ocurrió un error al obtener el código único.');
                                } finally {
                                    this.hideSpinner();
                                }
                            }
                        });
                    },
                    showSpinner() {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: true } }));
                    },
                    hideSpinner() {
                        document.dispatchEvent(new CustomEvent('loading', { detail: { loading: false } }));
                    },
                    roundToTwoDecimals(num) {
                        return Math.round(num * 100) / 100;
                    },
                    decimalToInt(num) {
                        return Math.round(num * 100);
                    }
                }
            }
        </script>

        <x-boletos.ppayment />

    </main>

    <x-utils.toasts />

    <x-utils.soccer-spinner />
@endsection