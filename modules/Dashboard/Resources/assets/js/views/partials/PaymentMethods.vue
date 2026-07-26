<template>
  <section class="card card-dashboard pm-panel">
    <div class="card-body">
      <div class="pm-head">
        <h5 class="pm-title m-0">¿Cómo me pagan?</h5>
        <small class="text-muted">{{ subtitle }}</small>
      </div>

      <div v-if="labels.length" class="pm-body">
        <div class="pm-chart">
          <apexchart type="donut" height="220" :options="chartOptions" :series="values"></apexchart>
        </div>
        <ul class="pm-legend">
          <li v-for="(label, index) in labels" :key="index" class="pm-legend-item">
            <span class="pm-dot" :style="{ background: palette[index % palette.length] }"></span>
            <span class="pm-label text-truncate">{{ label }}</span>
            <span class="pm-value">S/ {{ values[index] | pmMoney }}</span>
          </li>
        </ul>
      </div>

      <div v-else class="pm-empty text-muted text-center">{{ emptyText }}</div>
    </div>
  </section>
</template>

<script>
export default {
  name: "PaymentMethods",
  props: {
    filters: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      labels: [],
      values: [],
      total: 0,
      subtitle: "Distribucion de cobros - mes actual",
      palette: ["var(--primary)", "var(--danger)", "var(--success)", "var(--info)", "var(--warning)", "#ffdd00"],
    };
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    filters: {
      deep: true,
      handler() {
        this.fetchData();
      },
    },
  },
  computed: {
    emptyText() {
      return this.subtitle ? `Sin cobros registrados (${this.subtitle.toLowerCase()}).` : "Sin cobros registrados.";
    },
    chartOptions() {
      return {
        chart: { fontFamily: "inherit" },
        labels: this.labels,
        colors: this.palette,
        stroke: { width: 0 },
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
          pie: {
            donut: {
              size: "72%",
              labels: {
                show: true,
                value: {
                  fontSize: "18px",
                  fontWeight: 700,
                  formatter: (val) => "S/ " + this.formatK(val),
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: "COBRADO",
                  color: "#9ca3af",
                  fontSize: "11px",
                  formatter: () => "S/ " + this.formatK(this.total),
                },
              },
            },
          },
        },
        tooltip: {
          y: {
            formatter: (val) =>
              "S/ " +
              Number(val).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }),
          },
        },
      };
    },
  },
  methods: {
    fetchData() {
      this.$http.get("/dashboard/payment-methods", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.labels = data.labels || [];
        this.values = data.values || [];
        this.total = data.total || 0;
        this.subtitle = data.subtitle || this.subtitle;
      });
    },
    formatK(val) {
      const num = Number(val) || 0;
      if (Math.abs(num) >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
      return num.toFixed(0);
    },
  },
  filters: {
    pmMoney(value) {
      const num = Number(value) || 0;
      if (Math.abs(num) >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, "") + "M";
      if (Math.abs(num) >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
      return num.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
  },
};
</script>

<style scoped>
.pm-panel {
  height: 100%;
}
.pm-head {
  margin-bottom: 1rem;
}
.pm-title {
  font-size: 1.05rem;
  font-weight: 600;
}
.pm-body {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}
.pm-chart {
  flex: 1 1 180px;
  min-width: 160px;
}
.pm-legend {
  flex: 1 1 160px;
  list-style: none;
  margin: 0;
  padding: 0;
}
.pm-legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0;
}
.pm-dot {
  flex-shrink: 0;
  width: 10px;
  height: 10px;
  border-radius: 3px;
}
.pm-label {
  flex: 1 1 auto;
  min-width: 0;
  font-size: 0.85rem;
}
.pm-value {
  flex-shrink: 0;
  font-weight: 700;
  font-size: 0.85rem;
}
.pm-empty {
  padding: 2rem 0;
}
.pm-panel ::v-deep .apexcharts-tooltip,
.pm-panel ::v-deep .apexcharts-tooltip *,
.pm-panel ::v-deep .apexcharts-tooltip-title {
  color: #fff !important;
}
</style>
