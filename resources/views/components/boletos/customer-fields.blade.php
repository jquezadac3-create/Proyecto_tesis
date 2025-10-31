<div>
    <div>
        <label class="mb-1 block font-semibold">Nombre/s <span class="text-red-600">*</span></label>
        <input type="text" class="w-full rounded border p-2" x-model="customerInfo.nombres" @input="errors.nombres = ''"
            :class="{ 'border-red-400': errors.nombres.trim(), 'border-gray-300': !errors.nombres.trim() }"
            placeholder="Ingresa tus nombres">
        <span x-show="errors.nombres" x-text="errors.nombres" class="text-xs text-red-500"></span>
    </div>

    <div>
        <label class="mb-1 block font-semibold">Apellido/s <span class="text-red-600">*</span></label>
        <input type="text" class="w-full rounded border p-2" x-model="customerInfo.apellidos"
            @input="errors.apellidos = ''"
            :class="{ 'border-red-400': errors.apellidos.trim(), 'border-gray-300': !errors.apellidos.trim() }"
            placeholder="Ingresa tus apellidos">
        <span x-show="errors.apellidos" x-text="errors.apellidos" class="text-xs text-red-500"></span>
    </div>

    <div>
        <label class="mb-1 block font-semibold">Cédula <span class="text-red-600">*</span></label>
        <input type="text" class="w-full rounded border p-2" x-model="customerInfo.cedula" @input="errors.cedula = ''; customerInfo.cedula = customerInfo.cedula.replace(/[^0-9]/g, '').slice(0,10);"
            :class="{ 'border-red-400': errors.cedula.trim(), 'border-gray-300': !errors.cedula.trim() }"
            placeholder="Ingresa tu cédula">
        <span x-show="errors.cedula" x-text="errors.cedula" class="text-xs text-red-500"></span>
    </div>

    <div>
        <label class="mb-1 block font-semibold">Correo electrónico <span class="text-red-600">*</span></label>
        <input type="email" class="w-full rounded border p-2" x-model="customerInfo.correo" @input="errors.email = ''"
            :class="{ 'border-red-400': errors.email.trim(), 'border-gray-300': !errors.email.trim() }"
            placeholder="Ingresa tu correo electrónico">
        <span x-show="errors.email" x-text="errors.email" class="text-xs text-red-500"></span>
    </div>

    <div>
        <label class="mb-1 block font-semibold">Número de celular <span class="text-red-600">*</span></label>
        <input type="tel" class="w-full rounded border p-2" x-model="customerInfo.celular" @input="errors.telefono = ''; customerInfo.celular = customerInfo.celular.replace(/[^0-9]/g, '').slice(0,10);"
            :class="{ 'border-red-400': errors.telefono.trim(), 'border-gray-300': !errors.telefono.trim() }"
            placeholder="Ingresa tu número de celular">
        <span x-show="errors.telefono" x-text="errors.telefono" class="text-xs text-red-500"></span>
    </div>

    <div class="flex justify-end space-x-2 pt-2">
        <button type="button" @click="closeModal"
            class="rounded-lg bg-gray-300 px-4 py-2 hover:bg-gray-400">Cancelar</button>
        <button type="submit"
            class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700 disabled:opacity-75 disabled:cursor-not-allowed"
            :disabled="selectedJornada.total <= 0 && selectedAbono.total <= 0">Continuar</button>
    </div>
</div>