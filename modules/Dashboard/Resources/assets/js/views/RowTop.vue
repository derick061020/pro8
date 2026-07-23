<template>
  <div class="row top px-2 kpi-row">
    <div class="kpi-col col">
      <div class="card card-dashboard">
        <div class="card-body card-kpi">
          <small class="text-muted">{{ salesTitle }}</small>
          <div class="kpi-main">
            <div class="kpi-values">
              <h3 class="font-weight-bold m-0 text-nowrap">S/ {{ monthly_sales | formatNumber }}</h3>
              <small
                v-if="changes.monthly_sales"
                class="kpi-change"
                :class="changes.monthly_sales.up ? 'is-up' : 'is-down'"
              >
                {{ changes.monthly_sales.up ? "▲" : "▼" }} {{ changes.monthly_sales.pct | formatNumber(1, 1) }}%
                <span class="kpi-change-label text-muted">vs mes anterior</span>
              </small>
            </div>
            <kpi-sparkline class="kpi-spark" :data="trend.monthly_sales" :labels="trend.labels" color="#0f766e"></kpi-sparkline>
          </div>
        </div>
      </div>
    </div>
    <div class="kpi-col col">
      <div class="card card-dashboard">
        <div class="card-body card-kpi">
          <small class="text-muted">Ticket promedio</small>
          <div class="kpi-main">
            <div class="kpi-values">
              <h3 class="font-weight-bold m-0 text-nowrap">S/ {{ average_ticket | formatNumber }}</h3>
              <small
                v-if="changes.average_ticket"
                class="kpi-change"
                :class="changes.average_ticket.up ? 'is-up' : 'is-down'"
              >
                {{ changes.average_ticket.up ? "▲" : "▼" }} {{ changes.average_ticket.pct | formatNumber(1, 1) }}%
                <span class="kpi-change-label text-muted">vs mes anterior</span>
              </small>
            </div>
            <kpi-sparkline class="kpi-spark" :data="trend.average_ticket" :labels="trend.labels" color="#0d9488"></kpi-sparkline>
          </div>
        </div>
      </div>
    </div>
    <div class="kpi-col col">
      <div class="card card-dashboard">
        <div class="card-body card-kpi">
          <small class="text-muted">Por cobrar</small>
          <div class="kpi-main">
            <div class="kpi-values">
              <h3 class="font-weight-bold m-0 text-nowrap">S/ {{ accounts_receivable | formatNumber }}</h3>
              <small
                v-if="changes.accounts_receivable"
                class="kpi-change"
                :class="changes.accounts_receivable.up ? 'is-up' : 'is-down'"
              >
                {{ changes.accounts_receivable.up ? "▲" : "▼" }} {{ changes.accounts_receivable.pct | formatNumber(1, 1) }}%
                <span class="kpi-change-label text-muted">vs mes anterior</span>
              </small>
            </div>
            <kpi-sparkline class="kpi-spark" :data="trend.accounts_receivable" :labels="trend.labels" color="#f59e0b"></kpi-sparkline>
          </div>
        </div>
      </div>
    </div>
    <div class="kpi-col col">
      <div class="card card-dashboard">
        <div class="card-body card-kpi">
          <small class="text-muted">Utilidad neta</small>
          <div class="kpi-main">
            <div class="kpi-values">
              <h3 class="font-weight-bold m-0 text-nowrap">S/ {{ net_utility | formatNumber }}</h3>
              <small
                v-if="changes.net_utility"
                class="kpi-change"
                :class="changes.net_utility.up ? 'is-up' : 'is-down'"
              >
                {{ changes.net_utility.up ? "▲" : "▼" }} {{ changes.net_utility.pct | formatNumber(1, 1) }}%
                <span class="kpi-change-label text-muted">vs mes anterior</span>
              </small>
            </div>
            <kpi-sparkline class="kpi-spark" :data="trend.net_utility" :labels="trend.labels" color="#16a34a"></kpi-sparkline>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import KpiSparkline from "./KpiSparkline.vue";

export default {
  props: ["company", "utilities", "filters"],
  components: { KpiSparkline },
  data() {
    return {
      document_total_global: 0,
      total_cpe: 0,
      sale_note_total_global: 0,
      total: 0,
      monthly_sales: 0,
      average_ticket: 0,
      accounts_receivable: 0,
      net_utility: 0,
      trend: {
        labels: [],
        monthly_sales: [],
        average_ticket: [],
        accounts_receivable: [],
        net_utility: [],
      },
    };
  },
  mounted() {
    this.onFetchData();
  },
  watch: {
    filters: {
      deep: true,
      handler() {
        this.onFetchData();
      },
    },
  },
  computed: {
    changes() {
      const keys = ["monthly_sales", "average_ticket", "accounts_receivable", "net_utility"];
      const result = {};
      keys.forEach((key) => {
        const serie = this.trend[key] || [];
        if (serie.length < 2) {
          result[key] = null;
          return;
        }
        const current = Number(serie[serie.length - 1]);
        const previous = Number(serie[serie.length - 2]);
        if (!previous) {
          result[key] = null;
          return;
        }
        const pct = ((current - previous) / Math.abs(previous)) * 100;
        result[key] = { pct: Math.abs(pct), up: pct >= 0 };
      });
      return result;
    },
    salesTitle() {
      const period = this.filters && this.filters.period ? this.filters.period : "month";
      const titles = {
        all: "Ventas totales",
        last_week: "Ventas de la semana",
        month: "Ventas del mes",
        between_months: "Ventas entre meses",
        date: "Venta del dia",
        between_dates: "Ventas entre fechas",
      };

      return titles[period] || "Ventas";
    },
    isDueWarning() {
      if (this.company.certificate_due) {
        const dueDate = moment(this.company.certificate_due);

        const now = moment();
        const diffInDays = dueDate.diff(now, 'days')
        return diffInDays <= 15;
      }
      return false;
    },
  },
  methods: {
    onFetchData() {
      this.$http.get("/dashboard/global-data", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.document_total_global = Number(data.document_total_global) || 0;
        this.total_cpe = Number(data.total_cpe) || 0;
        this.sale_note_total_global = Number(data.sale_note_total_global) || 0;
        this.total = this.document_total_global + this.sale_note_total_global;
        this.monthly_sales = Number(data.monthly_sales) || 0;
        this.average_ticket = Number(data.average_ticket) || 0;
        this.accounts_receivable = Number(data.accounts_receivable) || 0;
        this.net_utility = Number(data.net_utility) || 0;
        if (data.trend) {
          this.trend = data.trend;
        }
      });
    },
  },
  filters: {
    formatNumber(value, baseDecimals = 2, suffixDecimals = 1) {
      const numericValue = Number(value);
      const defaultString = (0).toLocaleString("en-US", {
        minimumFractionDigits: baseDecimals,
        maximumFractionDigits: baseDecimals,
      });

      if (!Number.isFinite(numericValue)) {
        return defaultString;
      }

      if (Math.abs(numericValue) >= 1000000) {
        const millions = (numericValue / 1000000)
          .toFixed(suffixDecimals)
          .replace(/\.0+$/, "");
        return `${millions}M`;
      }

      if (Math.abs(numericValue) >= 1000) {
        const thousands = (numericValue / 1000)
          .toFixed(suffixDecimals)
          .replace(/\.0+$/, "");
        return `${thousands}K`;
      }

      return numericValue.toLocaleString("en-US", {
        minimumFractionDigits: baseDecimals,
        maximumFractionDigits: baseDecimals,
      });
    },
  },
};
</script>
<style>
.card-green {
  background-color: var(--success);
  color: white;
}
.is-due-warning {
  background-color: var(--danger);
}
.card-green .card-title {
  color: white;
}
.kpi-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(285px, 1fr));
}
.card-kpi {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.kpi-row .card-kpi small {
  display: block;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  font-size: 0.72rem;
}
.kpi-row .card-kpi h3 {
  font-size: 1.6rem;
}
.kpi-row .kpi-main {
  display: flex;
  align-items: center;
  gap: 8px;
}
.kpi-row .kpi-values {
  flex: 1 1 auto;
  min-width: 0;
  margin-top: auto;
}
.kpi-row .kpi-spark {
  flex: 0 0 96px;
  width: 96px;
  max-width: 96px;
}
.kpi-row .card-kpi .kpi-change {
  display: block;
  text-transform: none;
  letter-spacing: normal;
  font-size: 0.74rem;
  font-weight: 600;
  margin: 4px 0 0;
}
.kpi-row .card-kpi .kpi-change.is-up {
  color: var(--success);
}
.kpi-row .card-kpi .kpi-change.is-down {
  color: var(--danger);
}
.kpi-row .card-kpi .kpi-change .kpi-change-label {
  font-weight: 400;
  margin-left: 4px;
}
</style>
