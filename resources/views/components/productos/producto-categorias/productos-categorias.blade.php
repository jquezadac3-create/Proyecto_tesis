@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/productos/productoCategorias.js', 'resources/js/utils/deleteModal.js'])
@endsection

@section('title', 'CategoriaProductos')

@section('breadcrumb')
    <div x-show="selected === 'products.categories'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                showForm: false,
                showDeleteForm: false,
                form: { id: '', nombre: '' },
                resetForm() {
                    this.form.id = '';
                    this.form.nombre = '';
                }
            }">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de Categorias` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Categorias
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
                                        de productos
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-productos.producto-categorias.table />

                    <x-productos.producto-categorias.create />

                    <x-utils.delete-modal />
                </div>
            </div>
        </body>
    </div>

@endsection
