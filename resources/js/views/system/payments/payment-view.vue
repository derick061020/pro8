<template>
    <div class="payment-page">
        <div class="payment-card">
            <!-- Encabezado -->
            <header class="payment-header">
                <div class="brand">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg>
                    <span>Orden de pago</span>
                </div>
                <span class="badge-state" :class="stateClass">{{ payment_order.state }}</span>
            </header>

            <!-- Monto destacado -->
            <section class="amount-section">
                <span class="amount-label">Total a pagar</span>
                <span class="amount-value">{{ payment_order.currency }} {{ formatMoney(payment_order.amount) }}</span>
                <span class="order-number">Orden #{{ payment_order.order }}</span>
            </section>

            <!-- Detalle del pago -->
            <section class="info-block">
                <h4 class="info-title">Detalle del pago</h4>
                <div class="info-row">
                    <span class="info-label">Concepto</span>
                    <span class="info-value">{{ payment_order.description }}</span>
                </div>
                <div class="info-row" v-if="payment_order.due_date">
                    <span class="info-label">Fecha de vencimiento</span>
                    <span class="info-value">{{ payment_order.due_date }}</span>
                </div>
                <div class="info-row" v-if="payment_order.is_paid && payment_order.date_of_payment">
                    <span class="info-label">Fecha de pago</span>
                    <span class="info-value">{{ payment_order.date_of_payment }}</span>
                </div>
            </section>

            <!-- Servicio / Plan -->
            <section class="info-block" v-if="plan">
                <h4 class="info-title">Servicio contratado</h4>
                <div class="info-row">
                    <span class="info-label">Plan</span>
                    <span class="info-value">{{ plan.name }}</span>
                </div>
                <div class="info-row" v-if="plan.period">
                    <span class="info-label">Periodo</span>
                    <span class="info-value">{{ plan.period }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Precio del plan</span>
                    <span class="info-value">{{ payment_order.currency }} {{ formatMoney(plan.price) }}</span>
                </div>
                <div class="info-row" v-if="plan.date_of_ending">
                    <span class="info-label">Vence el</span>
                    <span class="info-value">{{ plan.date_of_ending }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado del servicio</span>
                    <span class="info-value" :class="plan.active ? 'text-success' : 'text-danger'">
                        {{ plan.active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </section>

            <!-- Cliente -->
            <section class="info-block">
                <h4 class="info-title">Cliente</h4>
                <div class="info-row">
                    <span class="info-label">Empresa</span>
                    <span class="info-value">{{ client.name }}</span>
                </div>
                <div class="info-row" v-if="client.number">
                    <span class="info-label">RUC / Nº</span>
                    <span class="info-value">{{ client.number }}</span>
                </div>
                <div class="info-row" v-if="client.client_name">
                    <span class="info-label">Contacto</span>
                    <span class="info-value">{{ client.client_name }}</span>
                </div>
                <div class="info-row" v-if="client.contact_email">
                    <span class="info-label">Correo</span>
                    <span class="info-value">{{ client.contact_email }}</span>
                </div>
                <div class="info-row" v-if="client.phone_ws">
                    <span class="info-label">WhatsApp</span>
                    <span class="info-value">{{ client.phone_ws }}</span>
                </div>
            </section>

            <!-- Acción (placeholder del checkout) -->
            <footer class="payment-footer">
                <template v-if="payment_order.is_paid || paid">
                    <div class="paid-message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                        Esta orden ya fue pagada
                    </div>
                </template>
                <template v-else>
                    <checkout-admin @submit="submit" :form="form">
                    </checkout-admin>
                    <p class="footer-note">Pago seguro</p>
                </template>
            </footer>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        payment_order: {
            type: Object,
            required: true
        },
        client: {
            type: Object,
            required: true
        },
        plan: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            loading: false,
            paid: false,
            form: {
                amount: 0,
                currency: 'PEN',
                orderId: '',
                description: '',
                customer: {
                    name: '',
                    email: '',
                    phone: '',
                    lastName: ''
                }
            }
        }
    },
    computed: {
        stateClass() {
            switch (this.payment_order.state_id) {
                case 1: return 'state-warning'
                case 2: return 'state-success'
                case 3: return 'state-danger'
                case 4: return 'state-dark'
                default: return 'state-warning'
            }
        }
    },
    created() {
        this.form.amount = this.payment_order.amount * 100
        this.form.currency = "PEN" 
        this.form.orderId = this.payment_order.order
        this.form.description = this.payment_order.description
        this.form.customer.name = this.client.client_name || this.client.name
        this.form.customer.email = this.client.contact_email
        
    },
    methods: {
        formatMoney(value) {
            const val = parseFloat(value)
            return isNaN(val) ? '0.00' : val.toFixed(2)
        },
        async submit(data) {
            let response = await this.$http.post('/payment-orders/payment-view/pays', 
                {
                    uuid: this.payment_order.uuid,
                    status: data.status
                }
            )
            console.log(response);
            this.paid = response.data.paid
        }
    }
}
</script>

<style scoped>
.payment-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f4f5f7;
    padding: 40px 16px;
}
.payment-card {
    width: 100%;
    max-width: 480px;
    margin: auto;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}
.payment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid #eef0f2;
}
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #0088CC;
}
.badge-state {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    color: #fff;
    text-transform: uppercase;
}
.state-warning { background: #f0ad4e; }
.state-success { background: #28a745; }
.state-danger  { background: #dc3545; }
.state-dark    { background: #6c757d; }

.amount-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 28px 24px;
    border-bottom: 1px solid #eef0f2;
}
.amount-label {
    color: #8a929c;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.amount-value {
    font-size: 38px;
    font-weight: 700;
    color: #1f2937;
    margin: 6px 0;
}
.order-number {
    color: #8a929c;
    font-size: 13px;
}

.info-block {
    padding: 20px 24px;
    border-bottom: 1px solid #eef0f2;
}
.info-title {
    margin: 0 0 14px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #8a929c;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 6px 0;
}
.info-label {
    color: #6b7280;
    font-size: 14px;
}
.info-value {
    color: #1f2937;
    font-size: 14px;
    font-weight: 600;
    text-align: right;
    word-break: break-word;
}
.text-success { color: #28a745 !important; }
.text-danger  { color: #dc3545 !important; }

.payment-footer {
    padding: 24px;
}
.btn-pay {
    width: 100%;
    border: none;
    border-radius: 10px;
    background: #0088CC;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    padding: 14px;
    cursor: pointer;
    transition: background 0.2s ease;
}
.btn-pay:hover { background: #006fa8; }
.btn-pay:disabled { opacity: 0.6; cursor: not-allowed; }
.footer-note {
    text-align: center;
    color: #9ca3af;
    font-size: 12px;
    margin: 12px 0 0;
}
.paid-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #28a745;
    font-weight: 600;
    background: #eafaf0;
    border-radius: 10px;
    padding: 14px;
}
</style>
