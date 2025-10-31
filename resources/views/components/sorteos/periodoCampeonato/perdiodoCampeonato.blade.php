@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/sorteos/periodoCampeonato.js', 'resources/js/app.js', 'resources/js/utils/deleteModal.js'])

@endsection

@section('title', 'Periodo Campeonato')

@section('breadcrumb')
    <div x-show="selected === 'sorteo.periodoCampeonato'">

        <body :class="">
            <div x-data="{
                showFormPeriodo: false,
                showDeleteForm: false,
                form: { id: '', nombre: '', fecha_inicio: '', fecha_fin: '' },
                resetFormPeriodo() {
                    this.form.id = '';
                    this.form.nombre = '';
                    this.form.fecha_inicio = '';
                    this.form.fecha_fin = '';
            
                },
            }">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Periodo Campeonato` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">
                                Periodo Campeonato
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
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->
                    <x-sorteos.periodoCampeonato.table />
                    <x-sorteos.periodoCampeonato.create />
                    <x-utils.delete-modal />

                </div>
            </div>
        </body>
    </div>
@endsection
