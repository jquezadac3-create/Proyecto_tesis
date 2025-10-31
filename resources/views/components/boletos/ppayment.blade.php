<div x-show="showPaymentModal" x-data="initPayments()" x-on:processpayment.document="ejecutarPagos()"
    x-transition:enter.opacity.duration.200ms    
    class="fixed inset-0 z-40 flex items-center justify-center modal-backdrop">
    <!-- Life is available only in the present moment. - Thich Nhat Hanh -->
    <div id="pp-button"></div>
</div>
<script>
    function initPayments() {
        return {
            showPaymentModal: false,
            ejecutarPagos() {
                this.showPaymentModal = true;

                try {
                    ppb = new PPaymentButtonBox({
                        token: 'gMbIMdMXBmA2Dg_SEhg0vMqkOyjLO16Tt17fPTV_NpVWNnXnLjQaBW-gbKdkkAkWnQI6XZQNdFfeIgkBbZmOpbyAg53pzFTzlv_JzjGmQaAYYInG3bEjMwqj5N2tcd9UMNfjmZCEqstJ22J3d0SonkuSDpFmxBfGBu9aWqovZAN2lRRaZkywxU9XZj3d6LjouCRAG34E_QzUl6W3U0eJ9STinSHRsj16lySgWKi_wiLha2GK-nm4Qgbb2RB-cDMHtBfrGey-d1UI8bG26cYU6FfAtzBcvhEut-vI6I9biTl0XqGj3gEFrjF1HryVKCnVbWhKgw',
                        clientTransactionId: this.uniqueCode,
                        amount: this.amountToPay,
                        amountWithTax: this.amountWithTax,
                        tax: this.tax,
                        currency: "USD",
                        storeId: "076e2af2-163b-451d-9fe1-926a1b694ab4",
                        reference: this.reference,
                        phoneNumber: `+593${this.customerInfo.celular.trim().slice(1,10)}`,
                        email: this.customerInfo.correo.trim(),
                        documentId: this.customerInfo.cedula,
                        identificationType: this.identificationType,
                    }).render('pp-button');

                    toast.show('info', 'Info', 'No cierres ni recargues esta ventana hasta completar el pago. Si el pago se completa correctamente, serás redirigido automáticamente.');
                } catch (error) {
                    console.error('Error al inicializar PPaymentButtonBox:', error);
                    setTimeout(() => {
                        this.showPaymentModal = false;
                    }, 3000);
                }
            }
        }
    }
</script>