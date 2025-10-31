<!-- Modal flotante -->
<div x-show="showDeleteForm" x-cloak x-transition.opacity.duration.200ms x-on:keydown.esc.window="modalIsOpen = false"
    x-on:click.self="modalIsOpen = false"
    @notify.document="$event.detail.variant === 'success' && showDeleteForm ? (showDeleteForm = false, resetForm(), dTitle = '') : '';"
    class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50 dark:bg-white/10 px-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg w-full max-w-2xl mx-auto overflow-y-auto max-h-[90vh]">
        <!-- Encabezado -->
        <div
            class="flex items-center justify-between border-b border-outline bg-surface-alt/60 px-4 py-2 dark:border-outline-dark dark:bg-surface-dark/20">
            <div class="flex items-center justify-center rounded-full bg-danger/20 text-danger p-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <button x-on:click="showDeleteForm = false; form.id = ''" aria-label="close modal">
                <i class="fa-solid fa-xmark dark:text-on-surface-dark-strong"></i>
            </button>
        </div>

        <!-- Formulario -->
        <form id="delete-form" class="p-6 space-y-4">
            @csrf

            <input required id="id" x-model="form.id" type="text" class="hidden">

            <div class="px-4 text-center">
                <h3 id="dangerModalTitle"
                    class="mb-2 font-semibold tracking-wide text-on-surface-strong dark:text-on-surface-dark-strong">
                    Confirmar eliminación
                </h3>
                <p class="text-on-surface-strong dark:text-on-surface-dark-strong">Estás a punto de eliminar
                    <span class="font-bold text-on-surface-strong dark:text-on-surface-dark-strong"
                        x-text="dTitle"></span>. Esta acción no se puede deshacer. ¿Estás seguro de que deseas
                    continuar?
                </p>
            </div>
            <!-- Dialog Footer -->
            <div class="flex items-center justify-center border-outline p-4 dark:border-outline-dark">
                <button x-on:click="dangerModalIsOpen = false" type="submit"
                    class="w-full whitespace-nowrap rounded-radius border border-danger bg-danger px-4 py-2 text-center text-sm font-semibold tracking-wide text-on-danger transition hover:opacity-75 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-danger active:opacity-100 active:outline-offset-0">¡Sí,
                    eliminar!</button>
            </div>
        </form>
    </div>
</div>