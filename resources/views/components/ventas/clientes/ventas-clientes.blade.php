@extends('layout.main-layout')

@section('title', 'Clientes')

@section('assets')
    @vite(['resources/js/ventas/clientes.js'])
@endsection

@section('breadcrumb')
    <div x-show="selected === 'sales.client'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                showDeleteForm: false,
                errors: {},
                form: { id: '', nombres: '', apellidos: '', tipo_identificacion: '', numero_identificacion: '', direccion: '', telefono: '', email: '', abono: '', entradas: '' },
                stats: {},
                setData(id, nombres, apellidos, tipo_identificacion, numero_identificacion, direccion, telefono, email, abono, entradas) {
                    this.form = { id, nombres, apellidos, tipo_identificacion, numero_identificacion, direccion, telefono, email, abono, entradas };
                },
                setStats(tiene_abono, cantidad_usada, cantidad_total, entradas_normales) {
                    this.stats = {
                        tiene_abono,
                        cantidad_usada,
                        cantidad_total,
                        entradas_normales
                    };
                },
                resetForm() {
                    this.showForm = false;
                    this.errors = {};
                    this.stats = {};
                    this.form = { id: '', nombres: '', apellidos: '', tipo_identificacion: '', numero_identificacion: '', direccion: '', telefono: '', email: '', abono: '', entradas: '' };
                }
            }">
                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de clientes` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                clientes
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
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                        clientes
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-ventas.clientes.table />

                    <x-ventas.clientes.form />

                    <x-utils.delete-modal />
                </div>
            </div>
        </body>
    </div>

@endsection
