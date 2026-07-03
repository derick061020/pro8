<template>
    <div class="checkout-guest">
        <div v-if="loading" class="checkout-guest__loading">
            Cargando opciones de pago…
        </div>

        <div v-else-if="error" class="checkout-guest__error">
            {{ error }}
        </div>

        <div v-else-if="!type" class="checkout-guest__error">
            No hay una pasarela de pago configurada. Por favor contacta al administrador.
        </div>

        <template v-else>
            <system-checkout-culqi
                v-if="type === 'culqi'"
                :form="culqiForm"
                :endpoint-prefix="`${baseEndpoint}/culqi`"
                @submit="onSubmit"
            />
            <system-checkout-izipay
                v-else-if="type === 'izipay'"
                :form="izipayForm"
                :endpoint-prefix="`${baseEndpoint}/izipay`"
                @submit="onSubmit"
            />
        </template>
    </div>
</template>

<script>
export default {
    props: {
        paymentUuid: {
            type: String,
            required: true
        },
        amount: {
            type: Number,
            required: true
        },
        description: {
            type: String,
            default: 'Pago de servicio'
        },
        currency: {
            type: String,
            default: 'PEN'
        },
        orderNumber: {
            type: String,
            default: ''
        },
        customerEmail: {
            type: String,
            default: ''
        },
        customerName: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            type: null,
            loading: true,
            error: null
        }
    },
    computed: {
        baseEndpoint() {
            return `/pago/${this.paymentUuid}`;
        },
        amountInCents() {
            return Math.round(Number(this.amount) * 100);
        },
        culqiForm() {
            return {
                amount: this.amountInCents,
                currency: this.currency,
                title: this.description,
                description: this.description,
                email: this.customerEmail,
                order: this.orderNumber
            };
        },
        izipayForm() {
            const [firstName, ...rest] = (this.customerName || '').split(' ');
            return {
                amount: this.amountInCents,
                currency: this.currency,
                orderId: this.orderNumber,
                customer: {
                    email: this.customerEmail,
                    billingDetails: {
                        firstName: firstName || this.customerName,
                        lastName: rest.join(' ') || '-',
                        phoneNumber: ''
                    }
                }
            };
        }
    },
    created() {
        this.loadCheckout();
    },
    methods: {
        loadCheckout() {
            this.loading = true;
            this.$http.get(`${this.baseEndpoint}/checkout-config`)
                .then(response => {
                    this.type = response.data.checkout || null;
                })
                .catch(err => {
                    console.error(err);
                    this.error = 'No se pudo cargar el checkout. Intenta nuevamente.';
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        onSubmit(payload) {
            if (payload && payload.status === 'venta_exitosa') {
                this.$message.success('Pago exitoso');
                window.location.href = `${this.baseEndpoint}/success`;
            }
        }
    }
}
</script>

<style scoped>
.checkout-guest__loading,
.checkout-guest__error {
    text-align: center;
    color: #8a96a3;
    font-size: 0.9rem;
    padding: 1rem 0;
}
.checkout-guest__error {
    color: #c53030;
}
</style>
