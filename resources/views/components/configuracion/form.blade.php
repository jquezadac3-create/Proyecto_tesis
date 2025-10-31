@extends('layout.main-layout')

@section('assets')
    @vite(['resources/js/config/config.js'])
@endsection

@section('title', 'Configuración')

@section('breadcrumb')
    <div x-show="selected === 'configuracion.index'">
        <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
            <!-- Breadcrumb Start -->
            <div x-data="{ pageName: `Configuración Sistema` }">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Configuración
                        Sistema
                    </h2>
                </div>
            </div>
            <!-- Breadcrumb End -->

            <!-- Content Start -->
            <div x-data="multiStepForm()" class="space-y-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                <!-- Stepper Visual -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                    <nav aria-label="Progreso">
                        <ol class="flex items-center justify-center space-x-4 sm:space-x-8">
                            <!-- Paso 1 -->
                            <li class="flex items-center">
                                <div class="flex items-center">
                                    <div :class="{
                                        'bg-brand-600 border-brand-600': step >= 1,
                                        'border-gray-300 bg-white': step < 1
                                    }" 
                                    class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-colors duration-200 cursor-pointer"
                                    @click="goToStep(1)">
                                        <template x-if="step > 1">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </template>
                                        <template x-if="step === 1">
                                            <i class="fas fa-location-pin text-white text-sm"></i>
                                        </template>
                                        <template x-if="step < 1">
                                            <span class="text-gray-500 font-semibold">1</span>
                                        </template>
                                    </div>
                                    <div class="ml-3 hidden sm:block">
                                        <p :class="{
                                            'text-brand-600 font-medium': step === 1,
                                            'text-gray-900 dark:text-white': step > 1,
                                            'text-gray-500': step < 1
                                        }" class="text-sm transition-colors duration-200">
                                            Datos de la empresa
                                        </p>
                                        <p class="text-xs text-gray-500">Información básica</p>
                                    </div>
                                </div>
                            </li>

                            <!-- Separador -->
                            <li class="hidden sm:block">
                                <div :class="{
                                    'bg-brand-600': step > 1,
                                    'bg-gray-300': step <= 1
                                }" class="h-0.5 w-16 transition-colors duration-200"></div>
                            </li>

                            <!-- Paso 2 -->
                            <li class="flex items-center">
                                <div class="flex items-center">
                                    <div :class="{
                                        'bg-brand-600 border-brand-600': step >= 2,
                                        'border-gray-300 bg-white': step < 2
                                    }" 
                                    class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-colors duration-200 cursor-pointer"
                                    @click="goToStep(2)">
                                        <template x-if="step > 2">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </template>
                                        <template x-if="step === 2">
                                            <i class="fas fa-location-pin text-white text-sm"></i>
                                        </template>
                                        <template x-if="step < 2">
                                            <span class="text-gray-500 font-semibold">2</span>
                                        </template>
                                    </div>
                                    <div class="ml-3 hidden sm:block">
                                        <p :class="{
                                            'text-brand-600 font-medium': step === 2,
                                            'text-gray-900 dark:text-white': step > 2,
                                            'text-gray-500': step < 2
                                        }" class="text-sm transition-colors duration-200">
                                            Configuración tributaria
                                        </p>
                                        <p class="text-xs text-gray-500">Datos fiscales</p>
                                    </div>
                                </div>
                            </li>

                            <!-- Separador -->
                            <li class="hidden sm:block">
                                <div :class="{
                                    'bg-brand-600': step > 2,
                                    'bg-gray-300': step <= 2
                                }" class="h-0.5 w-16 transition-colors duration-200"></div>
                            </li>

                            <!-- Paso 3 -->
                            <li class="flex items-center">
                                <div class="flex items-center">
                                    <div :class="{
                                        'bg-brand-600 border-brand-600': step >= 3,
                                        'border-gray-300 bg-white': step < 3
                                    }" 
                                    class="flex h-10 w-10 items-center justify-center rounded-full border-2 transition-colors duration-200 cursor-pointer"
                                    @click="goToStep(3)">
                                        <template x-if="step === 3">
                                            <i class="fas fa-location-pin text-white text-sm"></i>
                                        </template>
                                        <template x-if="step < 3">
                                            <span class="text-gray-500 font-semibold">3</span>
                                        </template>
                                    </div>
                                    <div class="ml-3 hidden sm:block">
                                        <p :class="{
                                            'text-brand-600 font-medium': step === 3,
                                            'text-gray-500': step < 3
                                        }" class="text-sm transition-colors duration-200">
                                            Archivos y seguridad
                                        </p>
                                        <p class="text-xs text-gray-500">Certificados y logo</p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <!-- Indicador de progreso mobile -->
                    <div class="mt-4 block sm:hidden">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Paso <span x-text="step"></span> de 3</span>
                            <span class="text-gray-600 dark:text-gray-400" x-text="Math.round((step / 3) * 100) + '%'"></span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-brand-600 h-2 rounded-full transition-all duration-300" 
                                 :style="`width: ${(step / 3) * 100}%`"></div>
                        </div>
                    </div>
                </div>

                <div @notify.document="$event.detail.variant === 'success' ? redirectOnSave() : null"
                    class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                        <h2 class="text-lg font-medium text-gray-800 dark:text-white">
                            @if ($config)
                                Editar Configuración
                            @else
                                Crear Confiuración
                            @endif
                        </h2>
                        <!-- Título dinámico del paso actual -->
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <span x-show="step === 1">Complete los datos básicos de su empresa</span>
                            <span x-show="step === 2">Configure los parámetros tributarios</span>
                            <span x-show="step === 3">Suba los archivos necesarios para la facturación</span>
                        </p>
                    </div>
                    <div class="p-4 sm:p-6 dark:border-gray-800">
                        <form id="config-form" @submit.prevent="submitForm()">
                            @csrf
                            <!-- Paso 1: Datos de la empresa -->
                            @if ($config)
                                <input type="text" name="id" id="id" x-model="form.id" class="hidden">
                            @endif
                            <div x-show="step === 1" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <template x-for="(field, idx) in fieldsEmpresa" :key="idx">
                                    <div>
                                        <label :for="field.id"
                                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                            x-text="field.label"></label>
                                        <input :type="field.type" :id="field.id" :name="field.id"
                                            x-model="form[field.model]" :placeholder="field.placeholder"
                                            :maxlength="field.id === 'ruc' ? 13 : null"
                                            x-on:input="if (field.id === 'ruc' && form[field.model].length > 13) form[field.model] = form[field.model].slice(0,13)"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <template x-if="errors[field.model] && !form[field.model]">
                                            <span class="text-xs text-red-500" x-text="errors[field.model]"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <!-- Paso 2: Configuración tributaria -->
                            <div x-show="step === 2" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Obligado a
                                        llevar contabilidad</label>
                                    <select id="obligado_contabilidad" name="obligado_contabilidad"
                                        x-model="form.obligadoContabilidad"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <option value="">Seleccione</option>
                                        <option value="SI">Si</option>
                                        <option value="NO">No</option>
                                    </select>
                                    <template x-if="errors.obligadoContabilidad && !form.obligadoContabilidad">
                                        <span class="text-xs text-red-500" x-text="errors.obligadoContabilidad"></span>
                                    </template>
                                </div>
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ambiente</label>
                                    <select id="ambiente" name="ambiente" x-model="form.ambiente"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <option value="">Seleccione</option>
                                        <option value="PRODUCCION">Producción</option>
                                        <option value="PRUEBAS">Pruebas</option>
                                    </select>
                                    <template x-if="errors.ambiente && !form.ambiente">
                                        <span class="text-xs text-red-500" x-text="errors.ambiente"></span>
                                    </template>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Estado
                                        electrónica</label>
                                    <select id="estado_electronica" name="estado_electronica"
                                        x-model="form.estadoElectronica"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <option value="">Seleccione</option>
                                        <option value="1">Activa</option>
                                        <option value="0">Inactiva</option>
                                    </select>
                                    <template x-if="errors.estadoElectronica && !form.estadoElectronica">
                                        <span class="text-xs text-red-500" x-text="errors.estadoElectronica"></span>
                                    </template>
                                </div>
                                <div>
                                    <label for="numero_factura" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ingrese el número de factura</label>
                                    <input type="text" id="numero_factura" name="numero_factura" x-model="form.numero_factura" placeholder="Ingrese el número de factura"
                                        :maxlength="9"
                                            x-on:input="form.numero_factura = form.numero_factura.replace(/[^0-9]/g, '').slice(0,13);"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <template x-if="errors.numero_factura && !form.numero_factura.trim()">
                                            <span class="text-xs text-red-500" x-text="errors.numero_factura"></span>
                                        </template>
                                    </div>
                            </div>
                            <!-- Paso 3: Archivos y seguridad -->
                            <div x-show="step === 3" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label for="firma"
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Firma electrónica
                                    </label>
                                    <input id="firma" type="file" @change="handleFile($event, 'firmaElectronica')"
                                        class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400">
                                    @if($config->firma_path ?? '')
                                        <div class="relative w-fit">
                                            <i class="fa fa-check text-emerald-600"></i><span
                                                class="text-xs peer rounded-radius px-4 py-2 font-medium tracking-wide text-on-surface focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-primary dark:text-on-surface-dark dark:focus-visible:outline-primary-dark">Certificado
                                                .p12 cargado</span>
                                            <div id="tooltipExample"
                                                class="absolute -top-9 left-1/2 -translate-x-1/2 sm:top-1/2 sm:left-full sm:-translate-y-1/2 ml-2 z-10 whitespace-nowrap rounded-sm bg-surface-dark px-2 py-1 text-center text-sm text-on-surface-dark-strong opacity-0 transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100 dark:bg-surface dark:text-on-surface-strong"
                                                role="tooltip">Puedes cambiar tu certificado si lo deseas.</div>
                                        </div>
                                    @endif
                                    <template x-if="errors.firmaElectronica">
                                        <span class="text-xs text-red-500" x-text="errors.firmaElectronica"></span>
                                    </template>
                                </div>
                                <div x-data="{ show_password: false }">
                                    <label for="config-contraseña-firma-electronica"
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Contraseña firma electrónica
                                    </label>

                                    <div class="relative">
                                        <input :type="show_password ? 'text' : 'password'"
                                            id="config-contraseña-firma-electronica" x-model="form.contraseniaFirma"
                                            placeholder="Ingrese la contraseña de la firma electrónica"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">

                                        <button type="button" @click="show_password = !show_password"
                                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 dark:text-white/50 dark:hover:text-white">
                                            <span class="fa" :class="show_password ? 'fa-eye-slash' : 'fa-eye'"></span>
                                        </button>
                                    </div>

                                    <template x-if="errors.contraseniaFirma">
                                        <span class="text-xs text-red-500" x-text="errors.contraseniaFirma"></span>
                                    </template>
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Logo</label>
                                    <div :class="{ 'border-gray-900/50': dropping, 'border-gray-900/25': !dropping }"
                                        x-data="{dropping: false}" x-on:dragover.prevent="dropping = true"
                                        x-on:dragleave.prevent="dropping = false" x-on:drop="dropping = false"
                                        x-on:drop.prevent="
                                                                        if ($event.dataTransfer.files.length > 1) return;
                                                                        $refs.logoInput.files = $event.dataTransfer.files;
                                                                        $refs.logoInput.dispatchEvent(new Event('change', { bubbles: true }));
                                                                    "
                                        class="flex w-full items-center justify-center border-gray-900/25">
                                        <label for="dropzone-file"
                                            class="flex h-64 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:border-gray-500 dark:hover:bg-gray-600 relative overflow-hidden">
                                            <template x-if="logoPreview">
                                                <img :src="logoPreview"
                                                    class="absolute inset-0 object-contain h-full w-full z-10"
                                                    alt="Preview logo">
                                            </template>
                                            <template x-if="!logoPreview">
                                                <div id="dropzone-content" class="z-20">
                                                    <svg class="mb-4 h-8 w-8 text-gray-500 dark:text-gray-400"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2">
                                                        </path>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                            class="font-semibold">Click para subir</span> o arrastra y
                                                        suelta</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG (MAX.
                                                        800x400px AND 3MB)</p>
                                                </div>
                                            </template>
                                            <input id="dropzone-file" type="file" class="hidden"
                                                @change="handleLogoPreview($event)"
                                                accept="image/png, image/jpg, image/jpeg" x-ref="logoInput">
                                        </label>
                                    </div>
                                    <template x-if="errors.logo">
                                        <span class="text-xs text-red-500" x-text="errors.logo"></span>
                                    </template>
                                </div>
                            </div>
                            <!-- Navegación de pasos -->
                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end mt-6">
                                <button type="button" x-show="step > 1" @click="prevStep"
                                    class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                                    Anterior
                                </button>
                                <button type="button" x-show="step < 3" @click="nextStep"
                                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                    Siguiente
                                </button>
                                <button type="submit" x-show="step === 3"
                                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                function multiStepForm() {
                    return {
                        step: 1,
                        form: {
                            id: "{{ $config->id ?? '' }}", razonSocial: "{{ $config->razon_social ?? '' }}", nombreComercial: "{{ $config->nombre_comercial ?? '' }}",
                            ruc: "{{ $config->ruc ?? '' }}", codigo_establecimiento: "{{ $config->codigo_establecimiento ?? '' }}", serie_ruc: "{{ $config->serie_ruc ?? '' }}",
                            direccionMatriz: "{{ $config->direccion_matriz ?? '' }}", direccionEstablecimiento: "{{ $config->direccion_establecimiento ?? '' }}", numero_factura: "{{ $config->numero_factura ?? '' }}",
                            tipoContribuyente: "{{ $config->tipo_contribuyente ?? '' }}", obligadoContabilidad: "{{ $config->obligado_contabilidad ?? '' }}", ambiente: "{{ $config->ambiente ?? '' }}",
                            estadoElectronica: "{{ $config->estado_electronica ?? '' }}", firmaElectronica: null, contraseniaFirma: "{{ $config->firma ?? '' }}", logo: ''
                        },
                        logoPreview: "{!! $config->logo ?? '' !!}",
                        errors: {},
                        fieldsEmpresa: [
                            { id: 'razon_social', label: 'Razón social', model: 'razonSocial', type: 'text', placeholder: 'Ingrese la razón social' },
                            { id: 'nombre_comercial', label: 'Nombre comercial', model: 'nombreComercial', type: 'text', placeholder: 'Ingrese el nombre comercial' },
                            { id: 'ruc', label: 'Ruc', model: 'ruc', type: 'number', placeholder: 'Ingrese la RUC' },
                            { id: 'codigo_establecimiento', label: 'Código establecimiento', model: 'codigo_establecimiento', type: 'number', placeholder: 'Ingrese el código establecimiento' },
                            { id: 'serie_ruc', label: 'Serie del Ruc', model: 'serie_ruc', type: 'number', placeholder: 'Ingrese la serie del Ruc' },
                            { id: 'direccion_matriz', label: 'Dirección Matriz', model: 'direccionMatriz', type: 'text', placeholder: 'Ingrese la dirección matriz' },
                            { id: 'direccion_establecimiento', label: 'Dirección del establecimiento', model: 'direccionEstablecimiento', type: 'text', placeholder: 'Ingrese la dirección del establecimiento' },
                            { id: 'tipo_contribuyente', label: 'Tipo del contribuyente', model: 'tipoContribuyente', type: 'text', placeholder: 'Ingrese el tipo de contribuyente' },
                        ],
                        nextStep() {
                            this.errors = {};
                            if (this.step === 1) {
                                this.fieldsEmpresa.forEach(f => {
                                    if (!this.form[f.model]) {
                                        this.errors[f.model] = 'Este campo es obligatorio';
                                    }
                                });
                                if (Object.keys(this.errors).length === 0) this.step++;
                            } else if (this.step === 2) {
                                if (!this.form.obligadoContabilidad) this.errors.obligadoContabilidad = 'Seleccione una opción';
                                if (!this.form.ambiente) this.errors.ambiente = 'Seleccione una opción';
                                if (!this.form.estadoElectronica) this.errors.estadoElectronica = 'Seleccione una opción';
                                if (!this.form.numero_factura.trim()) this.errors.numero_factura = 'Ingrese el número de factura';
                                if (Object.keys(this.errors).length === 0) this.step++;
                            }
                        },
                        prevStep() {
                            if (this.step > 1) this.step--;
                        },
                        goToStep(targetStep) {
                            // Solo permitir navegar hacia atrás o al paso actual
                            if (targetStep <= this.step || targetStep === this.step + 1) {
                                // Si intentamos ir hacia adelante, validamos el paso actual
                                if (targetStep > this.step) {
                                    if (this.step === 1) {
                                        this.nextStep();
                                    } else if (this.step === 2) {
                                        this.nextStep();
                                    }
                                } else {
                                    // Navegación hacia atrás, permitida siempre
                                    this.step = targetStep;
                                }
                            }
                        },
                        handleFile(e, field) {
                            this.form[field] = e.target.files[0];
                        },
                        handleLogoPreview(e) {
                            const file = e.target.files[0];
                            this.form.logo = file;
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (ev) => {
                                    this.logoPreview = ev.target.result;
                                };
                                reader.readAsDataURL(file);
                            } else {
                                this.logoPreview = '';
                            }
                            if (this.errors.logo && file) delete this.errors.logo;
                        },
                        submitForm() {
                            this.errors = {};
                            if (!this.form.firmaElectronica && !this.form.id) this.errors.firmaElectronica = 'Seleccione el archivo .p12';
                            if (!this.form.contraseniaFirma) this.errors.contraseniaFirma = 'Ingrese la contraseña de la firma electrónica';
                            if (!this.form.logo && !this.form.id) this.errors.logo = 'Seleccione el logo';
                            if (Object.keys(this.errors).length === 0) {
                                document.dispatchEvent(new CustomEvent('valid-form-submitted', { detail: { form: this.form } }));
                            }
                        },
                        redirectOnSave() {
                            window.location.href = "{!! route('dashboard.config') !!}";
                        }
                    }
                }
            </script>

            <!-- Content End -->
        </div>

    </div>
@endsection