<template>
    <div class="checkout-pay">
        <button
            type="button"
            class="checkout-pay__btn checkout-pay__btn--mercadopago"
            :disabled="disabled || !isReady"
            @click.prevent="openDialog"
        >
            <span class="checkout-pay__label">
                <span v-if="!isReady" class="checkout-pay__spinner"></span>
                {{ isReady ? 'Pagar con Mercado Pago' : 'Cargando...' }}
            </span>
            <span v-if="isReady" class="checkout-pay__amount">{{ formattedAmount }}</span>
        </button>

        <el-dialog
            title="Pagar con Mercado Pago"
            :visible.sync="dialogVisible"
            :close-on-click-modal="false"
            :append-to-body="true"
            width="480px"
            custom-class="checkout-mp-dialog"
            @opened="mountBrick"
            @closed="unmountBrick"
        >
            <div v-loading="brickLoading" class="checkout-mp-brick">
                <div :id="containerId"></div>
            </div>
        </el-dialog>
    </div>
</template>
<script>

/**
 * MercadoPago Payment Brick (formulario embebido en un modal, estilo Culqi)
 *
 * Flujo:
 *  1. El usuario presiona el botón y se abre un modal con el formulario de pago.
 *  2. El Payment Brick recolecta los datos de la tarjeta y los tokeniza en el
 *     navegador (onSubmit -> formData con token, issuer_id, payment_method_id,
 *     installments y payer).
 *  3. Se envía el formData al backend para crear el pago.
 *  4. Al confirmarse, se emite `submit` y se cierra el modal sin salir de la página.
 *
 * form -> {
 *  amount: Monto a cobrar (en centavos)
 *  currency: Moneda (PEN o USD)
 *  description: Descripción del pago
 *  orderId: Referencia externa (opcional)
 *  customer: { name, email }
 * }
 */

export default {
    props: {
        form: {
            type: Object,
            required: true
        },
        isTenant: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        endpointPrefix: {
            type: String,
            default: '/payment-gateway/mercadopago'
        },
    },
    data() {
        return {
            publicKey: null,
            scriptLoaded: false,
            dialogVisible: false,
            brickLoading: true,
            mp: null,
            brickController: null,
        }
    },
    created() {
        this.loadConfiguration();
        this.loadScript();
    },
    computed: {
        resource() {
            return this.endpointPrefix;
        },
        isReady() {
            return this.scriptLoaded && !!this.publicKey;
        },
        containerId() {
            return `mp-brick-container-${this._uid}`;
        },
        amountInUnits() {
            return Number(this.form.amount || 0) / 100;
        },
        formattedAmount() {
            const symbol = this.form.currency === 'USD' ? '$' : 'S/';
            return `${symbol} ${this.amountInUnits.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        }
    },
    beforeDestroy() {
        this.unmountBrick();
    },
    methods: {
        loadConfiguration() {
            this.$http.get(`${this.resource}/record?isTenant=${this.isTenant}`)
                .then(response => {
                    this.publicKey = response.data.public_key_mp;
                });
        },
        loadScript() {
            if (window.MercadoPago) {
                this.scriptLoaded = true;
                return;
            }

            const existing = document.querySelector('script[src="https://sdk.mercadopago.com/js/v2"]');
            if (existing) {
                existing.addEventListener('load', () => { this.scriptLoaded = true; });
                if (window.MercadoPago) this.scriptLoaded = true;
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://sdk.mercadopago.com/js/v2';
            script.async = true;
            script.addEventListener('load', () => { this.scriptLoaded = true; });
            script.addEventListener('error', () => {
                this.$message.error('No se pudo cargar el procesador de pagos. Verifique su conexión.');
            });
            document.head.appendChild(script);
        },
        openDialog() {
            if (!this.isReady) {
                this.$message.warning('El procesador de pagos aún se está cargando, espere un momento.');
                return;
            }
            this.brickLoading = true;
            this.dialogVisible = true;
        },
        async mountBrick() {
            if (!this.mp) {
                this.mp = new window.MercadoPago(this.publicKey, { locale: 'es-PE' });
            }

            const bricksBuilder = this.mp.bricks();
            const payerEmail = this.form.customer ? this.form.customer.email : '';

            const settings = {
                initialization: {
                    amount: this.amountInUnits,
                    payer: {
                        email: payerEmail || '',
                    },
                },
                customization: {
                    visual: {
                        style: { theme: 'default' },
                    },
                    paymentMethods: {
                        creditCard: 'all',
                        debitCard: 'all',
                    },
                },
                callbacks: {
                    onReady: () => {
                        this.brickLoading = false;
                    },
                    onSubmit: ({ formData }) => {
                        return this.createPayment(formData);
                    },
                    onError: (error) => {
                        console.error('MercadoPago Brick error', error);
                        this.$message.error('Ocurrió un error con el formulario de pago.');
                    },
                },
            };

            try {
                this.brickController = await bricksBuilder.create('payment', this.containerId, settings);
            } catch (error) {
                console.error('MercadoPago Brick create error', error);
                this.brickLoading = false;
                this.$message.error('No se pudo iniciar el formulario de pago.');
            }
        },
        createPayment(formData) {
            return new Promise((resolve, reject) => {
                this.$http.post(`${this.resource}/payment`, {
                    isTenant: this.isTenant,
                    order_id: this.form.orderId,
                    email: this.form.customer ? this.form.customer.email : null,
                    form_data: formData,
                }).then(response => {
                    const data = response.data;

                    if (!data.success) {
                        const msg = data.details ? 'El pago fue rechazado.' : (data.message || 'No se pudo procesar el pago.');
                        this.$message.error(msg);
                        reject();
                        return;
                    }

                    this.$emit('submit', {
                        status: data.result ? data.result.status : (data.paid ? 'approved' : 'rejected'),
                        customer: this.form._customer,
                        data: data,
                    });

                    if (data.paid) {
                        this.$message.success('Pago realizado con éxito');
                    } else {
                        this.$message.warning('El pago no pudo ser confirmado.');
                    }

                    resolve();
                    this.dialogVisible = false;
                }).catch(() => {
                    this.$message.error('Ocurrió un error al procesar el pago. Intente nuevamente.');
                    reject();
                });
            });
        },
        unmountBrick() {
            if (this.brickController) {
                try {
                    this.brickController.unmount();
                } catch (e) {
                    // noop
                }
                this.brickController = null;
            }
        },
    }
}
</script>

<style scoped>
.checkout-pay {
    width: 100%;
}
.checkout-pay__btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    padding: 14px 20px;
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.checkout-pay__btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
}
.checkout-pay__btn:active:not(:disabled) {
    transform: translateY(0);
}
.checkout-pay__btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.checkout-pay__btn--mercadopago {
    background: linear-gradient(135deg, #00b1ea 0%, #009ee3 100%);
    box-shadow: 0 4px 12px rgba(0, 158, 227, 0.25);
}
.checkout-pay__label {
    display: flex;
    align-items: center;
    gap: 8px;
}
.checkout-pay__spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: checkout-pay-spin 0.7s linear infinite;
}
@keyframes checkout-pay-spin {
    to { transform: rotate(360deg); }
}
.checkout-pay__amount {
    font-size: 16px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 6px;
    white-space: nowrap;
}
.checkout-mp-brick {
    min-height: 200px;
}
</style>
