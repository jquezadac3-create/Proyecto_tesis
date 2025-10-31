<!-- MODAL DE COMPRA -->
<div x-show="showJornadaModal" x-transition:enter.opacity.duration.200ms x-cloak
    class="fixed inset-0 z-30 flex items-center justify-center modal-backdrop">
    <div class="w-11/12 max-w-2xl rounded-lg bg-white p-6 max-h-[90vh] overflow-y-auto" x-data="data()">
        <h3 class="mb-4 text-xl font-bold" x-text="'Comprar entradas: ' + selectedJornada.nombre"></h3>

        <form @submit.prevent="completePurchase" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna izquierda: Selección de entradas -->
                <div>
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 mb-2" x-text="`${selectedJornada.fecha_inicio}`"></p>

                        <div class="space-y-4">
                            <template x-for="item in selectedJornada.prices">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="font-semibold" x-text="item.nombre"></label>
                                        <p class="text-sm"
                                            :class="item.stock < 5 ? 'text-red-500 font-semibold' : 'text-gray-600'"
                                            x-text="item.stock + ' disponibles'"></p>
                                        <p class="text-sm text-gray-600" x-text="'$' + item.precio_venta_final"></p>
                                    </div>
                                    <div class="flex items-center">
                                        <button type="button" @click="decrementItem(item)"
                                            class="rounded-full bg-gray-200 w-8 h-8 flex items-center justify-center hover:bg-gray-300">-</button>
                                        <span class="bg-white px-3 py-1 w-10 text-center" x-text="item.cantidad"></span>
                                        <button type="button" @click="incrementItem(item)"
                                            class="rounded-full bg-gray-200 w-8 h-8 flex items-center justify-center hover:bg-gray-300">+</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 pt-2 border-t font-bold flex justify-between">
                            <span>Total:</span>
                            <span x-text="'$' + roundTwoDecimals(selectedJornada.total)"></span>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Formulario de información -->
                <x-boletos.customer-fields />
            </div>
        </form>
    </div>
    <script>
        function data() {
            return {
                roundTwoDecimals(num) {
                    return Math.round(num * 100) / 100;
                },
                incrementItem(item) {
                    if (item.cantidad < item.stock && item.cantidad < this.maxItemsToBuy && this.currentItemsInCart < this
                        .maxItemsToBuy) {
                        item.cantidad++;
                        this.currentItemsInCart++;
                        this.calculateSubtotal();
                        this.updateItemsCart();
                        this.updateResumeData();
                    }
                },
                decrementItem(item) {
                    if (item.cantidad > 0) {
                        item.cantidad--;
                        this.currentItemsInCart--;
                        this.calculateSubtotal();
                        this.updateItemsCart();
                        this.updateResumeData();
                    }
                },
                calculateSubtotal() {
                    const sumTotal = this.selectedJornada.prices.reduce((total, item) => total + (item.precio_venta_final *
                        item.cantidad), 0);
                    const sumTotalSinIVA = this.selectedJornada.prices.reduce((total, item) => total + (item
                        .precio_sin_iva * item.cantidad), 0);
                    const sumTotalImpuesto = this.selectedJornada.prices.reduce((total, item) => total + ((item
                        .precio_sin_iva * item.impuesto) * item.cantidad), 0);
                    this.selectedJornada.total = sumTotal;
                    this.selectedJornada.total_sin_iva = sumTotalSinIVA;
                    this.selectedJornada.impuesto = sumTotalImpuesto;
                },
                validate() {
                    let nErrors = 0;
                    if (this.selectedJornada.total === 0)(toast.show('error', 'Error',
                        'Debes seleccionar al menos una entrada.'), nErrors++);
                    if (!this.customerInfo.nombres.trim())(this.errors.nombres = "El campo nombres es obligatorio.",
                        nErrors++);
                    if (!this.customerInfo.apellidos.trim())(this.errors.apellidos = "El campo apellidos es obligatorio.",
                        nErrors++);
                    if (!this.customerInfo.cedula.trim())(this.errors.cedula = "El campo cédula es obligatorio", nErrors++);
                    if (!this.customerInfo.correo.trim())(this.errors.email = "El campo correo es obligatorio", nErrors++);
                    if (!this.customerInfo.celular.trim())(this.errors.telefono = "El campo teléfono es obligatorio",
                        nErrors++);
                    if (this.customerInfo.cedula.trim() && this.customerInfo.cedula.trim().length > 10)(this.errors.cedula =
                        "La cédula debe tener 10 dígitos.", nErrors++);
                    if (this.customerInfo.celular.trim() && this.customerInfo.celular.trim().length > 10)(this.errors
                        .telefono = "El número de teléfono debe tener 10 dígitos.", nErrors++);
                    return nErrors;
                },
                async completePurchase() {
                    if (this.validate() > 0) {
                        toast.show('error', 'Error', 'Por favor corrige los errores en el formulario.');
                        return;
                    }

                    await this.fetchUniqueCode();

                    if (!this.uniqueCode || !this.reference) {
                        return;
                    }

                    this.amountToPay = this.decimalToInt(this.selectedJornada.total);
                    this.amountWithTax = this.decimalToInt(this.selectedJornada.total_sin_iva);
                    this.tax = this.decimalToInt(this.selectedJornada.impuesto);

                    document.dispatchEvent(new CustomEvent('processpayment', ));
                },
                updateItemsCart() {
                    this.cartItems = [];
                    for (const item of this.selectedJornada.prices) {
                        if (item.cantidad > 0) {
                            this.cartItems.push({
                                id: item.id,
                                codigo: item.id,
                                jornada_id: this.selectedJornada.id,
                                nombre: item.nombre_factura,
                                cantidad: item.cantidad,
                                precio_unitario: item.precio_sin_iva,
                                precio_unitario_iva: item.precio_venta_final,
                                stock_actual: item.stock,
                                impuesto: item.impuesto,
                                total: item.precio_sin_iva * item.cantidad,
                                total_iva: item.precio_venta_final * item.cantidad,
                            });
                        }
                    }
                },
                updateResumeData() {
                    const sumTotalIva15 = this.selectedJornada.prices.reduce((total, item) => {
                        return total + (item.impuesto == '0.15' ? (item.precio_sin_iva * item.impuesto) * item
                            .cantidad : 0);
                    }, 0);

                    const sumTotalIva5 = this.selectedJornada.prices.reduce((total, item) => {
                        return total + (item.impuesto == '0.05' ? (item.precio_sin_iva * item.impuesto) * item
                            .cantidad : 0);
                    }, 0);

                    const sumSubtotal15 = this.selectedJornada.prices.reduce((total, item) => total + (item.impuesto ==
                        '0.15' ? (item.precio_sin_iva * item.cantidad) : 0), 0);
                    const sumSubtotal5 = this.selectedJornada.prices.reduce((total, item) => total + (item.impuesto ==
                        '0.05' ? (item.precio_sin_iva * item.cantidad) : 0), 0);

                    this.resumeData = {
                        subtotal15: sumSubtotal15,
                        subtotal5: sumSubtotal5,
                        subtotal0: 0.0000,
                        iva15: this.roundTwoDecimals(sumTotalIva15),
                        iva5: this.roundTwoDecimals(sumTotalIva5),
                        iva0: 0.0000,
                        total: this.selectedJornada.total,
                        ice: 0.0000,
                        adicional: 0.0000,
                        descuento: 0.0000,
                        forma_pago: 2 // This can be 2 (credit) or 3 (debit)
                    }
                }
            }
        }
    </script>
</div>
