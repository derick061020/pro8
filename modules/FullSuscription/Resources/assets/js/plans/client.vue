<!-- Vista pública de suscripción al plan -->
<template>
  <div class="sub-page">

    <!-- Loading inicial -->
    <div v-if="isChecking" class="sub-loading">
      <div class="sub-spinner"></div>
      <p class="sub-loading__text">Cargando información del plan...</p>
    </div>

    <!-- Tarjeta de precios centrada -->
    <div v-else class="sub-center">

      <!-- Empresa -->
      <div class="company-header">
        <img v-if="company.logo" :src="company.logo" :alt="company.name" class="company-logo" />
        <p class="company-name">{{ company.name }}</p>
        <p v-if="company.ruc" class="company-ruc">RUC {{ company.ruc }}</p>
      </div>

      <div class="pricing-card">

        <!-- Acento superior -->
        <div class="pricing-accent"></div>

        <!-- Encabezado -->
        <div class="pricing-header">
          <span class="pricing-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/>
              <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/>
            </svg>
            Nueva suscripción
          </span>
          <h1 class="pricing-name">{{ plan.name }}</h1>
          <p v-if="plan.description" class="pricing-desc">{{ plan.description }}</p>
        </div>

        <!-- Precio -->
        <div class="pricing-price">
          <div class="pricing-amount-row">
            <span class="pricing-currency">S/</span>
            <span class="pricing-amount">{{ plan.total }}</span>
          </div>
          <span class="pricing-period">
            por
            {{ plan.quantity_period > 1 ? plan.quantity_period + ' ' : '' }}{{ plan.cat_period ? plan.cat_period.name : 'período' }}
          </span>
        </div>

        <!-- Periodo de prueba -->
        <div v-if="plan.trial_days && plan.trial_days > 0" class="pricing-trial">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            style="flex-shrink:0;">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
            <path d="M12 7v5l3 3"/>
          </svg>
          {{ plan.trial_days }} días de prueba gratis
        </div>

        <!-- Items del plan (dinámicos) -->
        <ul v-if="plan.items && plan.items.length" class="pricing-items">
          <li v-for="(item, i) in plan.items" :key="i">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="item-check">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M5 12l5 5l10 -10"/>
            </svg>
            <span>{{ item.description || item.name || item }}</span>
          </li>
        </ul>

        <!-- Características fijas -->
        <ul class="pricing-features">
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="feature-icon">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M5 12l5 5l10 -10"/>
            </svg>
            Acceso completo al plan
          </li>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="feature-icon">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
              <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
            </svg>
            Renovación automática
          </li>
          <li>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="feature-icon">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"/>
              <path d="M3 10h18"/>
              <path d="M7 15h.01"/><path d="M11 15h2"/>
            </svg>
            Pago seguro con MercadoPago
          </li>
        </ul>

        <!-- Divider -->
        <div class="pricing-divider"></div>

        <!-- Resultado del pago -->
        <div v-if="paymentResult !== null" class="payment-result">
          <template v-if="paymentResult.paid">
            <div class="result-icon result-icon--success">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M5 12l5 5l10 -10"/>
              </svg>
            </div>
            <h2 class="result-title result-title--success">¡Pago exitoso!</h2>
            <p class="result-desc">Tu suscripción al plan <strong>{{ plan.name }}</strong> ha sido creada correctamente. En breve recibirás la confirmación en tu correo.</p>
          </template>
          <template v-else>
            <div class="result-icon result-icon--pending">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                <path d="M12 7v5l3 3"/>
              </svg>
            </div>
            <h2 class="result-title result-title--pending">Pago en proceso</h2>
            <p class="result-desc">Tu pago está siendo procesado por la entidad financiera. Una vez confirmado, tu suscripción quedará activa. Revisa tu correo para más detalles.</p>
          </template>
        </div>

        <!-- Checkout -->
        <template v-else>
          <div class="pricing-checkout">

            <!-- Datos del cliente -->
            <div class="row">
              <div class="form-group col-12">
                <label class="control-label">Correo electrónico</label>
                <el-input v-model="form.customer.email" type="email" placeholder="tu@correo.com" />
              </div>
              <div class="form-group col-5">
                <label class="control-label">Tipo doc.</label>
                <el-select v-model="form.customer.identity_document_type_id" style="width:100%">
                  <el-option label="DNI" value="1" />
                  <el-option label="RUC" value="6" />
                </el-select>
              </div>
              <div class="form-group col-7">
                <label class="control-label">N.° de documento</label>
                <el-input v-model="form.customer.number" placeholder="12345678" />
              </div>
              <div class="form-group col-12">
                <label class="control-label">Teléfono</label>
                <el-input v-model="form.customer.phone" type="tel" placeholder="999 999 999" />
              </div>
            </div>

            <checkout-tenant @submit="submit" :form="form" :disabled="!customerComplete || isSubmitting">
            </checkout-tenant>
          </div>

          <!-- Nota de seguridad -->
          <p class="pricing-secure">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              style="flex-shrink:0;">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/>
            </svg>
            Transacción 100% segura y encriptada
          </p>
        </template>

      </div>
    </div>

  </div>
</template>

<script>

export default {
  props: {
    publicKey:   { type: String, required: true },
    planId:      { type: String, required: true },
    initialPlan: { type: Object, required: true },
    company:     { type: Object, required: true },
  },

  data() {
    return {
      isChecking: false,
      plan: this.initialPlan,
      form: {
        amount: this.initialPlan.total * 100,
        order_id: this.initialPlan.id + '-' + Date.now(),
        currency: 'PEN',
        description: this.initialPlan.name,
        customer: {
          name: '',
          lastName: '',
          email: '',
          phone: '',
          identity_document_type_id: '',
          number: '',
        }
      },
      ordenId: '',
      paymentResult: null,
      isSubmitting: false,
    }
  },
  computed: {
    customerComplete() {
      const c = this.form.customer
      return !!(c.email && c.identity_document_type_id && c.number && c.phone)
    }
  },
  methods: {
    /**
     * 
     * @param data -> {
     *  status: 0,
     * }
     */
    async submit(data) {
      this.isSubmitting = true
      try {
        await this.$http.post(`/full_suscription/plans/create-suscription`, {
          status: data.status,
          plan_id: this.planId,
          customer: data.customer
        })
        this.paymentResult = { paid: data.paid }
      } catch (e) {
        this.paymentResult = { paid: false }
      } finally {
        this.isSubmitting = false
      }
    }
  }
}
</script>

<style scoped>

html {
    background: #f8f8f9;
}
/* ── Base ── */
.sub-page {
  min-height: 100vh;
  font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

/* ── Loading ── */
.sub-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  gap: 14px;
  width: 100%;
}

.sub-spinner {
  width: 28px;
  height: 28px;
  border: 2px solid #e4e4e7;
  border-top-color: #18181b;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.sub-loading__text {
  font-size: .8125rem;
  color: #71717a;
  margin: 0;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Center wrapper ── */
.sub-center {
  width: 100%;
  max-width: 460px;
}

/* ── Company header ── */
.company-header {
  text-align: center;
  margin-bottom: 1.25rem;
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

/* ── Pricing card ── */
.pricing-card {
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  box-shadow: 0 0 25px #0000000a;
}

/* Acento morado — identifica suscripción nueva */
.pricing-accent {
  height: 8px;
  background: linear-gradient(90deg, #4f46e5, #7c3aed);
}

/* ── Header ── */
.pricing-header {
  padding: 2rem 2rem 1.5rem;
  border-bottom: 1px solid #f4f4f5;
  text-align: center;
}

.pricing-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: .2rem .75rem;
  border: 1px solid #ddd6fe;
  border-radius: 9999px;
  font-size: .6875rem;
  font-weight: 600;
  letter-spacing: .05em;
  text-transform: uppercase;
  color: #6d28d9;
  background: #f5f3ff;
  margin-bottom: .85rem;
}

.pricing-name {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -.03em;
  color: #09090b;
  margin: 0 0 .5rem;
}

.pricing-desc {
  font-size: .875rem;
  color: #71717a;
  line-height: 1.6;
  margin: 0;
}

/* ── Price ── */
.pricing-price {
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f4f4f5;
  display: flex;
  align-items: flex-end;
  gap: .5rem;
}

.pricing-amount-row {
  display: flex;
  align-items: flex-end;
  gap: .15rem;
}

.pricing-currency {
  font-size: 1.125rem;
  font-weight: 600;
  color: #52525b;
  padding-bottom: .5rem;
}

.pricing-amount {
  font-size: 3rem;
  font-weight: 700;
  letter-spacing: -.05em;
  color: #09090b;
  line-height: 1;
}

.pricing-period {
  font-size: .875rem;
  color: #a1a1aa;
  padding-bottom: .4rem;
}

/* ── Trial ── */
.pricing-trial {
  display: flex;
  align-items: center;
  gap: .45rem;
  padding: .75rem 2rem;
  background: #f0fdf4;
  border-bottom: 1px solid #dcfce7;
  font-size: .8125rem;
  color: #15803d;
  font-weight: 500;
}

/* ── Items ── */
.pricing-items {
  list-style: none;
  margin: 0;
  padding: 1.25rem 2rem;
  display: flex;
  flex-direction: column;
  gap: .7rem;
  border-bottom: 1px solid #f4f4f5;
}

.pricing-items li {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .875rem;
  color: #3f3f46;
}

.item-check {
  color: #16a34a;
  flex-shrink: 0;
}

/* ── Características fijas ── */
.pricing-features {
  list-style: none;
  margin: 0;
  padding: 1.25rem 2rem;
  display: flex;
  flex-direction: column;
  gap: .7rem;
  border-bottom: 1px solid #f4f4f5;
}

.pricing-features li {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .875rem;
  color: #3f3f46;
}

.feature-icon {
  color: #16a34a;
  flex-shrink: 0;
}

/* ── Divider ── */
.pricing-divider {
  height: 1px;
  background: #f4f4f5;
}

/* ── Checkout area ── */
.pricing-checkout {
  padding: 1.5rem 2rem;
}

/* ── Secure note ── */
.pricing-secure {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
  padding: 0 2rem 1.25rem;
  font-size: .75rem;
  color: #059669;
  font-weight: 500;
  margin: 0;
}

/* ── Override CheckoutPro button to full-width ── */
.pricing-checkout :deep(.btn-pagar),
.pricing-checkout .btn-pagar {
  width: 100%;
  padding: .9rem 1rem;
  font-size: .9375rem;
  border-radius: 8px;
}

/* ── Payment result ── */
.payment-result {
  padding: 2.5rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: .85rem;
}

.result-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.result-icon--success {
  background: #dcfce7;
  color: #16a34a;
}

.result-icon--pending {
  background: #fef9c3;
  color: #ca8a04;
}

.result-title {
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: -.02em;
  margin: 0;
}

.result-title--success { color: #15803d; }
.result-title--pending { color: #92400e; }

.result-desc {
  font-size: .875rem;
  color: #52525b;
  line-height: 1.65;
  margin: 0;
  max-width: 340px;
}
</style>
