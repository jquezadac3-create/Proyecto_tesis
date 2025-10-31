@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/productos/productoAbonos.js', 'resources/js/utils/deleteModal.js'])
@endsection

@section('title', 'Abono Productos')

@section('breadcrumb')
    <div x-show="selected === 'products.abonos'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                showDeleteForm: false,
                form: { id: '', nombre: '', descripcion: '', numero_entradas: '', costo_total: '', estado: true, mostrar_en_web: true },
                resetForm() {
                    this.form.id = '';
                    this.form.nombre = '';
                    this.form.descripcion = '';
                    this.form.numero_entradas = '';
                    this.form.costo_total = '';
                    this.form.estado = true;
                    this.form.mostrar_en_web = true;
                }
            }">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de Abonos` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Abonos
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
                                    <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Registro de lista
                                        de abonos
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-productos.producto-abonos.table />

                    <x-productos.producto-abonos.form />

                    <x-utils.delete-modal />
                </div>
            </div>
        </body>
    </div>

@endsection
