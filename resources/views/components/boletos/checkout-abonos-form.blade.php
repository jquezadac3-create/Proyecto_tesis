<!-- MODAL DE COMPRA PARA ABONOS -->
<div x-show="showAbonoModal" x-transition:enter.opacity.duration.200ms x-cloak
    class="fixed inset-0 z-30 flex items-center justify-center modal-backdrop">
    <div x-data="initAbonoMethods()" class="w-11/12 max-w-2xl rounded-lg bg-white p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="mb-4 text-xl font-bold" x-text="'Comprar abono: ' + selectedAbono.nombre"></h3>

        <form @submit.prevent="completePurchase" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna izquierda: Información del abono -->
                <div>
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600 mb-2" x-text="selectedAbono.descripcion"></p>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="font-semibold">Precio unitario</label>
                                </div>
                                <div>
                                    <span class="font-bold text-blue-600">$<span
                                            x-text="selectedAbono.precio"></span></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="font-semibold">Cantidad</label>
                                </div>
                                <div class="flex items-center">
                                    <button type="button" @click="decrementItem"
                                        class="rounded-full bg-gray-200 w-8 h-8 flex items-center justify-center hover:bg-gray-300">-</button>
                                    <span class="bg-white px-3 py-1 w-10 text-center"
                                        x-text="selectedAbono.cantidad"></span>
                                    <button type="button" @click="incrementItem"
                                        class="rounded-full bg-gray-200 w-8 h-8 flex items-center justify-center hover:bg-gray-300">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-t font-bold flex justify-between">
                            <span>Total:</span>
                            <span x-text="'$' + selectedAbono.total"></span>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Formulario de información -->
                <x-boletos.customer-fields />
            </div>
            <script>
                function initAbonoMethods() {
                    return {
                        roundTwoDecimals(num) {
                            return Math.round(num * 100) / 100;
                        },
                        incrementItem() {
                            if (this.selectedAbono.cantidad < this.selectedAbono.stock && this.selectedAbono.cantidad < this
                                .maxItemsToBuy && this.currentItemsInCart < this.maxItemsToBuy) {
                                this.selectedAbono.cantidad++;
                                this.currentItemsInCart++;
                                this.calculateSubtotal();
                                this.updateItemsCart();
                                this.updateResumeData();
                            }
                        },
                        decrementItem() {
                            if (this.selectedAbono.cantidad > 0) {
                                this.currentItemsInCart--;
                                this.selectedAbono.cantidad--;
                            }
                            this.calculateSubtotal();
                            this.updateItemsCart();
                            this.updateResumeData();
                        },
                        calculateSubtotal() {
                            this.selectedAbono.total = this.roundTwoDecimals(this.selectedAbono.precio * this.selectedAbono
                                .cantidad);
                            this.selectedAbono.total_sin_iva = this.selectedAbono.precio_sin_iva * this.selectedAbono.cantidad;
                        },
                        validate() {
                            let nErrors = 0;
                            if (this.selectedAbono.total === 0)(toast.show('error', 'Error',
                                'Debes seleccionar al menos una entrada.'), nErrors++);
                            if (!this.customerInfo.nombres.trim())(this.errors.nombres = "El campo nombres es obligatorio.",
                                nErrors++);
                            if (!this.customerInfo.apellidos.trim())(this.errors.apellidos = "El campo apellidos es obligatorio.",
                                nErrors++);
                            if (!this.customerInfo.cedula.trim())(this.errors.cedula = "El campo cédula es obligatorio", nErrors++);
                            if (!this.customerInfo.correo.trim())(this.errors.email = "El campo correo es obligatorio", nErrors++);
                            if (!this.customerInfo.celular.trim())(this.errors.telefono = "El campo teléfono es obligatorio",
                                nErrors++);

                            return nErrors;
                        },
                        async completePurchase() {
                            if (this.validate() > 0) {
                                toast.show('error', 'Error', 'Por favor corrige los errores antes de continuar.');
                                return;
                            }

                            await this.fetchUniqueCode();

                            if (!this.uniqueCode || !this.reference) {
                                return;
                            }

                            this.amountToPay = this.decimalToInt(this.selectedAbono.total);
                            this.amountWithTax = this.decimalToInt(this.selectedAbono.total_sin_iva);
                            this.tax = this.decimalToInt(this.selectedAbono.total_sin_iva * this.selectedAbono.impuesto);

                            document.dispatchEvent(new CustomEvent('processpayment', ));
                        },
                        updateItemsCart() {
                            if (this.selectedAbono.cantidad === 0) {
                                this.cartItems = [];
                                return;
                            }

                            this.cartItems = [{
                                id: this.selectedAbono.prod_id,
                                codigo: this.selectedAbono.prod_id,
                                abono_id: this.selectedAbono.id,
                                nombre: this.selectedAbono.nombre_factura,
                                cantidad: this.selectedAbono.cantidad,
                                precio_unitario: this.selectedAbono.precio_sin_iva,
                                precio_unitario_iva: this.selectedAbono.precio,
                                stock_actual: this.selectedAbono.stock,
                                total: this.selectedAbono.total_sin_iva,
                                total_iva: this.selectedAbono.total,
                                impuesto: this.selectedAbono.impuesto
                            }];
                        },
                        updateResumeData()
                        {
                            const isIva15 = this.selectedAbono.impuesto === '0.15';
                            const isIva5 = this.selectedAbono.impuesto === '0.05';
                            const isIva0 = this.selectedAbono.impuesto === '0';

                            this.resumeData = {
                                subtotal15: isIva15 ? this.selectedAbono.total_sin_iva : 0.0000,
                                subtotal5: isIva5 ? this.selectedAbono.total_sin_iva : 0.0000,
                                subtotal0: isIva0 ? this.selectedAbono.total_sin_iva : 0.0000,
                                iva15: isIva15 ? this.roundTwoDecimals(this.selectedAbono.total_sin_iva * this.selectedAbono.impuesto) : 0.0000,
                                iva5: isIva5 ? this.roundTwoDecimals(this.selectedAbono.total_sin_iva * this.selectedAbono.impuesto) : 0.0000,
                                ice: 0.0000,
                                adicional: 0.0000,
                                descuento: 0.0000,
                                total: this.selectedAbono.total,
                                forma_pago: 2 // This can be 2 (credit) or 3 (debit)
                            };
                        }
                    }
                }
            </script>
        </form>
    </div>
</div>
