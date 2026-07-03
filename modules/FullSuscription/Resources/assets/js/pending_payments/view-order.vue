<template>
  <div class="order-page">

    <!-- Empresa -->
    <div class="company-header">
      <img v-if="company.logo" :src="company.logo" :alt="company.name" class="company-logo" />
      <p class="company-name">{{ company.name }}</p>
      <p v-if="company.ruc" class="company-ruc">RUC {{ company.ruc }}</p>
    </div>

    <div class="order-card">

      <!-- Acento superior -->
      <div class="order-accent"></div>

      <!-- Encabezado -->
      <div class="order-header">
        <span class="order-badge">
          <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
            <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
          </svg>
          Recordatorio de pago · #{{ order.id }}
        </span>
        <h1 class="order-plan">{{ planName }}</h1>
      </div>

      <!-- Monto + estado -->
      <div class="order-amount-section">
        <div class="order-amount">
          <span class="order-amount__currency">S/</span>
          <span class="order-amount__value">{{ order.amount }}</span>
        </div>
        <span :class="['order-status', statusClass(order.status)]">
          <span class="order-status__dot"></span>
          {{ statusLabel(order.status) }}
        </span>
      </div>

      <!-- Detalles -->
      <dl class="order-details">
        <div class="order-details__row">
          <dt>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/>
              <path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/>
              <path d="M11 15h1"/><path d="M12 15v3"/>
            </svg>
            Vencimiento
          </dt>
          <dd :class="dueDateClass">{{ formattedDueDate }}</dd>
        </div>
        <div v-if="customerName" class="order-details__row">
          <dt>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
              <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
            </svg>
            Cliente
          </dt>
          <dd>{{ customerName }}</dd>
        </div>
        <div v-if="order.date_of_issue" class="order-details__row">
          <dt>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
              <path d="M12 7v5l3 3"/>
            </svg>
            Emitido
          </dt>
          <dd>{{ formatDate(order.date_of_issue) }}</dd>
        </div>
      </dl>

      <div class="order-divider"></div>

      <!-- Estado del pago -->
      <div class="order-checkout">
        <div class="order-alert order-alert--success" v-if="order.status === 'paid'">
          <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            style="flex-shrink:0;">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
            <path d="M9 12l2 2l4 -4"/>
          </svg>
          Este pago ya fue procesado correctamente
        </div>
        <template v-else>
            <checkout-tenant @submit="changeStatus" :form="form">
            </checkout-tenant>
        <div  class="order-alert order-alert--warning">

          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            style="flex-shrink:0;">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 9v4"/>
            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
            <path d="M12 16h.01"/>
          </svg>
          Pago pendiente de procesar
        </div>

        </template>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  props: ['order', 'company', 'suscription', 'person'],

  data() {
    return {
      resource: '/full-suscription/pending-payments',
    }
  },
  created() {
    
    this.form = {
      order_id: this.order.id + '-' + Date.now(),
      amount: this.order.amount * 100,
      currency: 'PEN',
      description: this.order.number, 
      customer: {
        email: this.person.email
      }

    }
  },

  computed: {
    planName() {
      return this.order.suscription && this.order.suscription.suscription_plan
        ? this.order.suscription.suscription_plan.name
        : 'Pago pendiente'
    },

    customerName() {
      const suscription = this.order.suscription
      if (!suscription) return null
      const c = suscription.customer
      if (!c) return null
      const parsed = typeof c === 'string' ? JSON.parse(c) : c
      return parsed && parsed.name ? parsed.name : null
    },

    formattedDueDate() {
      return this.formatDate(this.order.date_of_due)
    },

    dueDateClass() {
      if (!this.order.date_of_due) return ''
      const due  = new Date(this.order.date_of_due)
      const now  = new Date()
      const diff = Math.ceil((due - now) / (1000 * 60 * 60 * 24))
      if (diff < 0)  return 'dd--overdue'
      if (diff <= 3) return 'dd--soon'
      return ''
    },
  },

  methods: {
    formatDate(raw) {
      if (!raw) return '—'
      const d = new Date(raw)
      if (isNaN(d)) return raw
      return d.toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric' })
    },

    statusClass(status) {
      const map = {
        pending:   'status--pending',
        rejected:  'status--rejected',
        expired:   'status--expired',
        cancelled: 'status--expired',
        paid:      'status--paid',
      }
      return map[status] || 'status--default'
    },

    statusLabel(status) {
      const map = {
        pending:   'Pendiente',
        rejected:  'Rechazado',
        expired:   'Vencido',
        cancelled: 'Cancelado',
        paid:      'Pagado',
      }
      return map[status] || status
    },
    changeStatus(data) {
      this.$http.post(`${this.resource}/${this.order.id}/change-status-orders`, {
          status: data.status,
          order_id: this.form.order_id,
          customer: data.customer
      })
        .then(response => {})
    }
  },
}
</script>

<style scoped>
.order-page {
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.company-header {
  text-align: center;
  margin-bottom: 1.25rem;
  width: 100%;
  max-width: 460px;
}

.company-logo {
  height: 48px;
  object-fit: contain;
  margin-bottom: .5rem;
}

.company-name {
  font-size: .875rem;
  font-weight: 600;
  color: #18181b;
  margin: 0 0 .15rem;
}

.company-ruc {
  font-size: .75rem;
  color: #71717a;
  margin: 0;
}

.order-card {
  width: 100%;
  max-width: 460px;
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 0 25px #0000000a
}

.order-accent {
  height: 8px;
  background: linear-gradient(90deg, #d97706, #f59e0b);
}

.order-header {
  padding: 1.75rem 2rem 1.5rem;
  border-bottom: 1px solid #f4f4f5;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: .65rem;
}

.order-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: .2rem .75rem;
  border: 1px solid #fde68a;
  border-radius: 9999px;
  font-size: .6875rem;
  font-weight: 600;
  letter-spacing: .05em;
  text-transform: uppercase;
  color: #92400e;
  background: #fffbeb;
}

.order-plan {
  font-size: 1.375rem;
  font-weight: 700;
  letter-spacing: -.03em;
  color: #09090b;
  margin: 0;
}

.order-amount-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f4f4f5;
  gap: 1rem;
}

.order-amount {
  display: flex;
  align-items: flex-end;
  gap: .2rem;
}

.order-amount__currency {
  font-size: 1rem;
  font-weight: 600;
  color: #52525b;
  padding-bottom: .45rem;
}

.order-amount__value {
  font-size: 2.5rem;
  font-weight: 700;
  letter-spacing: -.05em;
  color: #09090b;
  line-height: 1;
}

.order-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: .75rem;
  font-weight: 600;
  padding: .3rem .85rem;
  border-radius: 9999px;
  white-space: nowrap;
}

.order-status__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status--pending  { background: #fef9c3; color: #854d0e; }
.status--pending  .order-status__dot { background: #ca8a04; }
.status--rejected { background: #fee2e2; color: #991b1b; }
.status--rejected .order-status__dot { background: #dc2626; }
.status--expired  { background: #f3f4f6; color: #6b7280; }
.status--expired  .order-status__dot { background: #9ca3af; }
.status--paid     { background: #dcfce7; color: #166534; }
.status--paid     .order-status__dot { background: #16a34a; }
.status--default  { background: #f3f4f6; color: #6b7280; }
.status--default  .order-status__dot { background: #9ca3af; }

.order-details {
  margin: 0;
  padding: 1.25rem 2rem;
  border-bottom: 1px solid #f4f4f5;
  display: flex;
  flex-direction: column;
  gap: .75rem;
}

.order-details__row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: .875rem;
}

.order-details__row dt {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #71717a;
  font-weight: 400;
}

.order-details__row dd {
  color: #18181b;
  font-weight: 500;
  margin: 0;
}

.dd--soon    { color: #d97706; font-weight: 600; }
.dd--overdue { color: #dc2626; font-weight: 600; }

.order-divider { height: 1px; background: #f4f4f5; }

.order-checkout { padding: 1.5rem 2rem; }

.order-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 8px;
  font-size: .875rem;
  font-weight: 500;
}

.order-alert--warning {
  background: #fef9c3;
  border: 1px solid #fde047;
  color: #854d0e;
}

.order-alert--success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
}
</style>
<style>
.body-web {
  background: #f2f5f7;
}
</style>
