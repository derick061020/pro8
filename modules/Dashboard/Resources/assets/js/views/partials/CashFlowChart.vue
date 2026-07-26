<template>
  <section class="card card-dashboard cf-panel">
    <div class="card-body">
      <div class="cf-head">
        <div>
          <h5 class="cf-title m-0">Flujo de caja</h5>
          <small class="text-muted">{{ subtitle }}</small>
        </div>
        <div class="cf-legend">
          <span class="cf-legend-item"><i class="cf-dot cf-dot--income"></i>Ingresos</span>
          <span class="cf-legend-item"><i class="cf-dot cf-dot--egress"></i>Egresos</span>
        </div>
      </div>
      <apexchart type="area" height="260" :options="chartOptions" :series="series"></apexchart>
    </div>
  </section>
</template>

<script>
export default {
  name: "CashFlowChart",
  props: {
    filters: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      labels: [],
      income: [],
      egress: [],
      subtitle: "Ingresos vs egresos - ultimos 6 meses",
      incomeColor: "#00c666",
      egressColor: "#ff8400",
    };
  },
  mounted() {
    this.resolveColors();
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
    series() {
      return [
        { name: "Ingresos", data: this.income },
        { name: "Egresos", data: this.egress },
      ];
    },
    chartOptions() {
      return {
        chart: { toolbar: { show: false }, fontFamily: "inherit", zoom: { enabled: false } },
        colors: [this.incomeColor, this.egressColor],
        stroke: { curve: "smooth", width: 2 },
        fill: {
          type: "gradient",
          gradient: { opacityFrom: 0.25, opacityTo: 0, stops: [0, 100] },
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: { borderColor: "#eef0f3", strokeDashArray: 4, padding: { left: 8, right: 8 } },
        markers: { size: 0, hover: { size: 5 } },
        xaxis: {
          categories: this.labels,
          axisBorder: { show: false },
          axisTicks: { show: false },
          labels: { style: { colors: "#9ca3af", fontSize: "12px" } },
        },
        yaxis: {
          labels: {
            style: { colors: "#9ca3af", fontSize: "12px" },
            formatter: (val) => this.formatK(val),
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
    resolveColors() {
      const styles = getComputedStyle(document.documentElement);
      const success = styles.getPropertyValue("--success").trim();
      const warning = styles.getPropertyValue("--warning").trim();
      if (success) this.incomeColor = success;
      if (warning) this.egressColor = warning;
    },
    fetchData() {
      this.$http.get("/dashboard/cash-flow", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.labels = data.labels || [];
        this.income = data.income || [];
        this.egress = data.egress || [];
        this.subtitle = data.subtitle || this.subtitle;
      });
    },
    formatK(val) {
      const num = Number(val) || 0;
      if (Math.abs(num) >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
      return num.toFixed(0);
    },
  },
};
</script>

<style scoped>
.cf-panel {
  height: 100%;
}
.cf-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}
.cf-title {
  font-size: 1.05rem;
  font-weight: 600;
}
.cf-legend {
  display: flex;
  gap: 1rem;
}
.cf-legend-item {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.78rem;
  color: #6b7280;
}
.cf-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  display: inline-block;
}
.cf-dot--income {
  background: var(--success);
}
.cf-dot--egress {
  background: var(--warning);
}
</style>
