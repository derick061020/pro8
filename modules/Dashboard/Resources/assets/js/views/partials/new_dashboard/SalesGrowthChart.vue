<template>
    <apexchart
        type="line"
        height="260"
        :options="chartOptions"
        :series="series"
    ></apexchart>
</template>

<script>
export default {
    name: "SalesGrowthChart",
    props: {
        categories: {
            type: Array,
            default: () => [],
        },
        current: {
            type: Array,
            default: () => [],
        },
        previous: {
            type: Array,
            default: () => [],
        },
        currentLabel: { type: String, default: "" },
        previousLabel: { type: String, default: "" },
    },
    computed: {
        series() {
            return [
                { name: this.currentLabel, data: this.current },
                { name: this.previousLabel, data: this.previous },
            ];
        },
        chartOptions() {
            return {
                chart: {
                    toolbar: { show: false },
                    fontFamily: "inherit",
                    zoom: { enabled: false },
                },
                colors: ["#2DD4BF", "#cbd5e1"],
                stroke: {
                    curve: "smooth",
                    width: [4, 3],
                    dashArray: [0, 8],
                },
                dataLabels: { enabled: false },
                legend: { show: false },
                grid: { show: false, padding: { left: 8, right: 8 } },
                markers: { size: 0, hover: { size: 5 } },
                xaxis: {
                    categories: this.categories,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: "#9ca3af", fontSize: "12px" },
                    },
                },
                yaxis: { show: false },
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
};
</script>
