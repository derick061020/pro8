<template>
  <apexchart
    type="area"
    :height="height"
    :width="width"
    :options="chartOptions"
    :series="series"
  ></apexchart>
</template>

<script>
export default {
  name: "KpiSparkline",
  props: {
    data: { type: Array, default: () => [] },
    labels: { type: Array, default: () => [] },
    color: { type: String, default: "#0f766e" },
    height: { type: [Number, String], default: 46 },
    width: { type: [Number, String], default: "100%" },
  },
  computed: {
    series() {
      return [{ name: "", data: this.data }];
    },
    chartOptions() {
      return {
        chart: { sparkline: { enabled: true }, animations: { enabled: false } },
        colors: [this.color],
        stroke: { curve: "smooth", width: 2 },
        fill: {
          type: "gradient",
          gradient: { opacityFrom: 0.35, opacityTo: 0, stops: [0, 100] },
        },
        markers: { size: 0, hover: { size: 4 } },
        tooltip: {
          x: { show: this.labels.length > 0 },
          y: {
            formatter: (val) =>
              "S/ " +
              Number(val).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }),
            title: { formatter: () => "" },
          },
          marker: { show: false },
        },
        xaxis: { categories: this.labels },
      };
    },
  },
};
</script>
