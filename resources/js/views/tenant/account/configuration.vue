<template>
  <div>
    <div class="page-header pr-0">
      <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Pagos</span></li>
      </ol>
      <div class="right-wrapper pull-right"></div>
    </div>

    <div class="card">
      <div class="card-header bg-info">
        <h3 class="my-0">Tipo de Plan de la Empresa</h3>
      </div>
      <div class="card-body">
        <p class="plans-intro">Selecciona un plan</p>

        <div v-if="loading_plans" class="text-center py-4">
          <i class="el-icon-loading"></i> Cargando planes...
        </div>

        <div v-else class="plans-grid">
          <div
            v-for="plan in plans"
            :key="plan.id"
            class="plan-card"
            :class="{
              'plan-card--current': plan.id === current_plan_id,
              'plan-card--popular': isPopular(plan)
            }"
          >
            <span v-if="isPopular(plan)" class="plan-card__badge">Popular</span>

            <div class="plan-card__head">
              <h5 class="plan-card__name">{{ plan.name }}</h5>
              <div class="plan-card__price">
                <span class="plan-card__currency">S/.</span>
                <span class="plan-card__amount">{{ plan.pricing }}</span>
                <span class="plan-card__period">/mes</span>
              </div>
            </div>

            <ul class="plan-card__features">
              <li>
                <span>Establecimientos</span>
                <strong>{{ plan.establishments_unlimited ? 'Ilimitado' : plan.establishments_limit }}</strong>
              </li>
              <li>
                <span>Documentos</span>
                <strong>{{ plan.limit_documents == 0 ? 'Ilimitado' : plan.limit_documents }}</strong>
              </li>
              <li>
                <span>Usuarios</span>
                <strong>{{ plan.limit_users == 0 ? 'Ilimitado' : plan.limit_users }}</strong>
              </li>
              <li>
                <span>Ventas mensuales</span>
                <strong>{{ plan.sales_unlimited ? 'Ilimitado' : 'S/. ' + plan.sales_limit }}</strong>
              </li>
            </ul>

            <button
              v-if="plan.id === current_plan_id"
              class="plan-card__btn plan-card__btn--current"
              disabled
            >Plan actual</button>
            <button
              v-else-if="isDowngrade(plan)"
              class="plan-card__btn plan-card__btn--current"
              disabled
              title="Para bajar de plan, contacta a soporte"
            >No disponible</button>
            <button
              v-else
              class="plan-card__btn"
              :class="{ 'plan-card__btn--filled': isPopular(plan) }"
              :disabled="loading_change"
              @click="selectPlan(plan)"
            >{{ loading_change && selected_plan_id === plan.id ? 'Procesando...' : 'Seleccionar' }}</button>
            <small v-if="isDowngrade(plan)" class="plan-card__hint">
              Contacta a soporte para bajar de plan
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      resource: 'cuenta',
      plans: [],
      current_plan_id: null,
      loading_plans: true,
      loading_change: false,
      selected_plan_id: null,
    };
  },
  async created() {
    await this.loadPlans();
  },
  computed: {
    currentPlan() {
      return this.plans.find(p => p.id === this.current_plan_id) || null;
    },
  },
  methods: {
    async loadPlans() {
      this.loading_plans = true;
      try {
        const { data } = await this.$http.get(`/${this.resource}/plan_change/tables`);
        this.plans = data.plans || [];
        this.current_plan_id = data.current_plan_id;
      } catch (err) {
        this.$message.error('No se pudieron cargar los planes');
      } finally {
        this.loading_plans = false;
      }
    },
    isPopular(plan) {
      return !!plan.is_popular;
    },
    isDowngrade(plan) {
      if (!this.currentPlan) return false;
      if (plan.id === this.current_plan_id) return false;
      return Number(plan.pricing) < Number(this.currentPlan.pricing);
    },
    async selectPlan(plan) {
      const confirmed = await this.$confirm(
        `¿Confirmas el cambio al plan ${plan.name} por S/. ${plan.pricing}/mes? Serás redirigido al checkout para completar el pago.`,
        'Confirmar cambio de plan',
        {
          confirmButtonText: 'Continuar al pago',
          cancelButtonText: 'Cancelar',
          type: 'info',
        }
      ).catch(() => false);

      if (!confirmed) return;

      this.loading_change = true;
      this.selected_plan_id = plan.id;
      try {
        const { data } = await this.$http.post(`/${this.resource}/plan_change/order`, {
          plan_id: plan.id,
        });
        if (data.success && data.payment_url) {
          window.location.href = data.payment_url;
        } else {
          this.$message.error(data.message || 'No se pudo crear la orden de pago');
        }
      } catch (err) {
        const msg = err.response && err.response.data && err.response.data.message
          ? err.response.data.message
          : 'No se pudo crear la orden de pago';
        this.$message.error(msg);
      } finally {
        this.loading_change = false;
        this.selected_plan_id = null;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@use "sass:color";
@import "../../../../sass/auth.scss";

.plans-intro {
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.05em;
  color: #8a96a3;
  margin-bottom: 1rem;
}

.plans-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

@media (max-width: 992px) {
  .plans-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
  .plans-grid { grid-template-columns: 1fr; }
}

.plan-card {
  position: relative;
  display: flex;
  flex-direction: column;
  border: 1px solid #ebebeb;
  border-radius: 12px;
  padding: 1.25rem;
  background-color: #fff;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.plan-card:hover {
  border-color: $blue;
}

.plan-card--popular {
  border-color: $blue;
  box-shadow: 0 4px 16px rgba(4, 42, 66, 0.08);
}

.plan-card--current {
  background-color: #f6f6f6;
  border-color: #ebebeb;
  opacity: 0.85;
}

.plan-card__badge {
  position: absolute;
  top: -10px;
  right: 12px;
  background-color: $blue;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  letter-spacing: 0.03em;
}

.plan-card__head {
  text-align: left;
  border-bottom: 1px solid #ebebeb;
  padding-bottom: 0.75rem;
  margin-bottom: 0.75rem;
}

.plan-card__name {
  font-size: 0.85rem;
  font-weight: 700;
  color: #8a96a3;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0 0 0.5rem 0;
}

.plan-card__price {
  display: flex;
  align-items: baseline;
  color: $blue;
}

.plan-card__currency {
  font-size: 1rem;
  font-weight: 600;
  margin-right: 0.25rem;
}

.plan-card__amount {
  font-size: 2.25rem;
  font-weight: 700;
  line-height: 1;
}

.plan-card__period {
  font-size: 0.85rem;
  color: #8a96a3;
  margin-left: 0.25rem;
}

.plan-card__features {
  list-style: none;
  padding: 0;
  margin: 0 0 1rem 0;
  flex: 1;
}

.plan-card__features li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.4rem 0;
  font-size: 0.85rem;
  color: $blue;
  border-bottom: 1px dashed #f0f0f0;
}

.plan-card__features li:last-child {
  border-bottom: none;
}

.plan-card__features li strong {
  font-weight: 600;
}

.plan-card__btn {
  width: 100%;
  padding: 0.65rem 0.75rem;
  font-size: 0.9rem;
  font-weight: 600;
  border: 1px solid $blue;
  border-radius: 8px;
  background-color: #fff;
  color: $blue;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
}

.plan-card__btn:hover:not(:disabled) {
  background-color: $blue;
  color: #fff;
}

.plan-card__btn--filled {
  background-color: $blue;
  color: #fff;
}

.plan-card__btn--filled:hover:not(:disabled) {
  background-color: color.adjust($blue, $lightness: -8%);
}

.plan-card__btn--current {
  background-color: #ebebeb;
  border-color: #ebebeb;
  color: #8a96a3;
  cursor: default;
}

.plan-card__btn:disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

.plan-card__hint {
  display: block;
  margin-top: 0.5rem;
  font-size: 0.75rem;
  color: #8a96a3;
  text-align: center;
  font-style: italic;
}
</style>
