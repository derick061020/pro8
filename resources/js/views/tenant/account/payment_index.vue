<template>
  <div>
    <div class="page-header pe-0">
      <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Pagos</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <a v-if="canConfigure" type="button" class="btn btn-custom btn-sm mt-2 me-2" href="/cuenta/configuration">
          <i class="fas fa-cogs"></i> Configuración
        </a>
      </div>
    </div>

    <div class="col-md-12 container px-0 mb-3">
      <div class="row">
        <template v-for="pay in sortedPayments">
          <div class="col-12 col-sm-6 col-lg-3 mb-2" :key="pay.id">
            <div
              class="status-container p-3 d-flex align-items-center justify-content-between"
              :style="{ backgroundColor: getStateColor(pay.id) }"
            >
              <div>
                <span class="status-price">S/ {{ parseFloat(pay.total).toFixed(2) }}</span>
                <p class="m-0">{{ pay.name }}</p>
              </div>
              <div v-html="getStateIcon(pay.id)"></div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div class="card tab-content-default row-new">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th class="text-start">ID de orden</th>
                <th>Descripción</th>
                <th class="text-center">Monto</th>
                <th class="text-center">Fecha de vencimiento</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="record in paginatedRecords" :key="record.id">
                <td class="text-start fw-bold">
                  <span>#{{ record.order }}</span>
                  <br>
                  <span
                    class="badge badge-pill"
                    :class="record.created_by === 'Manual' ? 'badge-info' : 'badge-secondary'"
                  >
                    {{ record.created_by }}
                  </span>
                </td>
                <td>
                  <span class="text-muted">{{ record.description }}</span>
                </td>
                <td class="text-center">S/ {{ parseFloat(record.amount).toFixed(2) }}</td>
                <td class="text-center">{{ record.due_date }}</td>
                <td class="text-center">
                  <span class="badge badge-pill" :class="getStateBadgeClass(record.order_state_id)">
                    {{ record.order_state }}
                  </span>
                  <br>
                  <span class="text-muted small" v-if="record.date_of_payment">
                    {{ record.date_of_payment }}
                  </span>
                </td>
                <td class="text-center">
                  <button
                    v-if="canPay(record)"
                    type="button"
                    class="btn btn-info btn-xs"
                    @click.prevent="clickPayment(record)"
                  >
                    Pagar
                  </button>
                  <span v-else class="text-muted small">Sin acciones</span>
                </td>
              </tr>
              <tr v-if="!records.length">
                <td colspan="6" class="text-center text-muted py-4">
                  No tienes órdenes de pago registradas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="records.length > pageSize" class="mt-3">
          <el-pagination
            layout="total, prev, pager, next"
            :total="records.length"
            :page-size="pageSize"
            :current-page.sync="currentPage"
          ></el-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    canConfigure: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      resource: 'cuenta',
      records: [],
      payments_records: [],
      currentPage: 1,
      pageSize: 10,
      gateway_enabled: false,
    };
  },
  computed: {
    sortedPayments() {
      const order = [2, 1, 3, 4]; // Pagado, Pendiente, Vencido, Anulado
      return this.payments_records.slice().sort((a, b) => order.indexOf(a.id) - order.indexOf(b.id));
    },
    paginatedRecords() {
      const start = (this.currentPage - 1) * this.pageSize;
      return this.records.slice(start, start + this.pageSize);
    },
  },
  created() {
    this.getData();
  },
  watch: {
    records() {
      this.currentPage = 1;
    },
  },
  methods: {
    getData() {
      this.$http.get(`/${this.resource}/payment_records`).then(response => {
        this.records = response.data.data;
        this.payments_records = response.data.pays || [];
        this.gateway_enabled = !!response.data.gateway_enabled;
      });
    },
    canPay(record) {
      const isPayable = record.order_state_id === 1 || record.order_state_id === 3;
      return isPayable && !!record.payment_url && this.gateway_enabled;
    },
    clickPayment(record) {
      window.open(record.payment_url, '_blank', 'noopener');
    },
    getStateColor(stateId) {
      switch (stateId) {
        case 1: return 'var(--warning, #ff8400)';
        case 2: return 'var(--success, #00c666)';
        case 3: return 'var(--danger, #ff0066)';
        case 4: return 'var(--muted, #99abb4)';
      }
    },
    getStateBadgeClass(stateId) {
      switch (stateId) {
        case 1: return 'badge-warning';
        case 2: return 'badge-success';
        case 3: return 'badge-danger';
        case 4: return 'badge-dark';
      }
    },
    getStateIcon(stateId) {
      switch (stateId) {
        case 1:
          return `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor" class="icon-transparent"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -15 8.66l.005 -.324a10 10 0 0 1 14.995 -8.336m-5 2.66a1 1 0 0 0 -1 1v5.026l.009 .105l.02 .107l.04 .129l.048 .102l.046 .078l.042 .06l.069 .08l.088 .083l.083 .062l3 2a1 1 0 1 0 1.11 -1.664l-2.555 -1.704v-4.464a1 1 0 0 0 -.883 -.993z" /></svg>`;
        case 2:
          return `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor" class="icon-transparent"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>`;
        case 3:
          return `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor" class="icon-transparent"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" /></svg>`;
        case 4:
          return `<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-transparent-ban"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M5.7 5.7l12.6 12.6" /></svg>`;
        default:
          return '';
      }
    },
  },
};
</script>

<style scoped>
.status-container,
.status-container .status-price,
.status-container p {
  color: #fff !important;
}
.status-container {
  border-radius: 12px;
}
.status-container .status-price {
  font-size: 22px;
  font-weight: 600;
}
.icon-transparent {
  stroke: rgba(255, 255, 255, 0);
  fill: rgba(255, 255, 255, 0.516);
}
.icon-transparent-ban {
  stroke: rgba(255, 255, 255, 0.516);
  fill: rgba(255, 255, 255, 0);
}
</style>
