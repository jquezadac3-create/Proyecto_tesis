<!-- resources/views/components/ventas/factura/actions.blade.php -->
<div class="p-4 sm:p-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
        <!-- Nueva Factura -->
        <button 
            id="nuevaFactura"
            type="button"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            <i class="fa-solid fa-file-circle-plus"></i>
            Nueva Factura
        </button>
        <!-- Guardar Factura -->
        <button 
            id="enviarFactura"  
            type="button"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            <i class="fa-solid fa-floppy-disk"></i>
            Guardar factura
        </button>
        <!-- Imprimir Factura -->
        <button 
            id="imprimirFactura"  
            type="button"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            <i class="fa-solid fa-print"></i>
            Imprimir Factura
        </button>
    </div>
</div>