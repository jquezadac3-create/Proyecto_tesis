@extends('layout.main-layout')

@section('title', 'Usuarios')

@section('assets')
    @vite(['resources/js/usuarios/users.js'])
@endsection

@section('breadcrumb')
    <div x-show="selected === 'users.index'">

        <body :class="{ 'overflow-hidden': showForm }">
            <div x-data="{
                        showForm: false,
                        showDeleteForm: false,
                        form: { id: '', name: '', email: '', password: '' },
                        errors: {name: '', email: '', password: '',},
                        resetForm() {
                            this.form = { id: '', name: '', email: '', password: '' };
                            this.errors = {name: '', email: '', password: '',};
                        },
                        validForm(){
                            if (!this.form.name.trim()) this.errors.name = 'El nombre es obligatorio.';
                            if (!this.form.email.trim()) this.errors.email = 'El correo electrónico es obligatorio.';
                            if (!this.form.password.trim()) this.errors.password = 'La contraseña es obligatoria.';
                            if (this.form.password.trim().length < 8) this.errors.password = 'La contraseña debe tener al menos 8 caracteres.';
                        },
                        sendForm() {
                            this.validForm();
                            if (Object.values(this.errors).some(error => error)) {
                                toast.show('error', 'Error', 'Por favor, corrige los errores en el formulario.');
                                return;
                            }
                            document.dispatchEvent(new CustomEvent('submit-user-form', { detail: this.form }));
                        }
                    }">

                <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                    <!-- Breadcrumb Start -->
                    <div x-data="{ pageName: `Registro de Usuarios` }">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Registro de
                                Usuarios
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
                                        de Usuarios
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- Breadcrumb End -->

                    <x-usuarios.list />

                    @can('create user')
                        <x-usuarios.form />
                    @endcan

                    @can('delete user')
                        <x-utils.delete-modal />
                    @endcan
                </div>
            </div>
        </body>
    </div>
@endsection