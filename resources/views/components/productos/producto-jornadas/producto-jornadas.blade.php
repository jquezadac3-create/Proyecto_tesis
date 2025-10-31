@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/productos/productoJornadas.js', 'resources/js/utils/deleteModal.js', 'resources/js/app.js'])
@endsection

@section('title', 'JornadasProductos')

@section('breadcrumb')
    <div x-show="selected === 'products.days'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                showDeleteForm: false,
                form: { id: '', nombre: '', fecha: '', aforo: '', estado: true },
                resetForm() {
                    this.form.id = '';
                    this.form.nombre = '';
                    this.form.fecha_inicio = '';
                    this.form.fecha_fin = '';
                    this.form.aforo = '';
                    this.form.estado = true;
                }
            }">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de Jornadas` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Jornadas
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
                                        Jornadas
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-productos.producto-jornadas.table />

                    <x-productos.producto-jornadas.form />

                    <x-utils.delete-modal />
                </div>
            </div>
        </body>
    </div>
@endsection
