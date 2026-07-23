<template>
    <div class="new-dashboard row-new bg-transparent mt-0">
        <!-- <div class="nd-tabs">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                type="button"
                class="nd-tab"
                :class="{ 'is-active': activeTab === tab.key }"
                @click="activeTab = tab.key"
            >
                {{ tab.label }}
            </button>
        </div> -->

        <template v-if="activeTab === 'income_expense'">
            <div class="row">
                <div class="col-12">
                    <div class="card-body">
                        <div class="row mx-0 px-0">
                            <div class="col-6 col-md-3">
                                <label class="control-label">Período</label>
                                <el-select
                                    v-model="form.period"
                                    size="small"
                                    class="nd-filter-select"
                                    @change="onPeriodChange"
                                >
                                    <el-option value="today" label="Hoy"></el-option>
                                    <el-option value="week" label="Semana"></el-option>
                                    <el-option value="month" label="Mes"></el-option>
                                    <el-option value="year" label="Año"></el-option>
                                </el-select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="control-label">Fecha</label>
                                <el-date-picker
                                    v-model="form.date_pick"
                                    :type="pickerConfig.type"
                                    size="small"
                                    class="nd-filter-select"
                                    :value-format="pickerConfig.valueFormat"
                                    :format="pickerConfig.format"
                                    :clearable="false"
                                    @change="onPickChange"
                                ></el-date-picker>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="control-label">Sucursal</label>
                                <el-select
                                    v-model="form.establishment_id"
                                    size="small"
                                    class="nd-filter-select"
                                    @change="loadAll"
                                >
                                    <el-option :value="null" label="Todas las sucursales"></el-option>
                                    <el-option
                                        v-for="option in establishments"
                                        :key="option.id"
                                        :value="option.id"
                                        :label="option.name"
                                    ></el-option>
                                </el-select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="control-label">Comparar con</label>
                                <el-select
                                    v-model="form.compare"
                                    size="small"
                                    class="nd-filter-select"
                                    @change="loadAll"
                                >
                                    <el-option value="none" label="Sin comparación"></el-option>
                                    <el-option value="previous_period" label="Período anterior"></el-option>
                                    <el-option value="previous_year" label="Año anterior"></el-option>
                                </el-select>
                            </div>
                        </div>

                        <div class="nd-active-filters">
                            <span class="nd-filter-label text-muted text-uppercase">Filtros activos</span>
                            <span
                                v-for="chip in activeFilters"
                                :key="chip.key"
                                class="nd-chip"
                                :class="chip.variant"
                            >
                                {{ chip.label }}
                                <i
                                    v-if="chip.removable"
                                    class="el-icon-close nd-chip-close"
                                    @click="removeFilter(chip.key)"
                                ></i>
                            </span>
                            <button type="button" class="nd-clear-all text-muted" @click="clearFilters">Limpiar todo</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row nd-kpi-row">
                <div class="col-12 col-sm-6 col-md-4 mt-3">
                    <div class="nd-card card-body">
                        <div class="nd-card-head">
                            <span class="nd-card-title text-muted text-uppercase">Ventas {{ periodLabel }}</span>
                            <span v-if="form.compare !== 'none'" class="nd-badge" :class="badgeClass(kpi.monthSalesVariation)">{{ formatPercent(kpi.monthSalesVariation) }}</span>
                        </div>
                        <div class="nd-card-value">S/ {{ kpi.monthSales }}</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4 mt-3">
                    <div class="nd-card card-body">
                        <div class="nd-card-head">
                            <span class="nd-card-title text-muted text-uppercase">Compras {{ periodLabel }}</span>
                            <span v-if="form.compare !== 'none'" class="nd-badge" :class="badgeClass(kpi.monthPurchasesVariation)">{{ formatPercent(kpi.monthPurchasesVariation) }}</span>
                        </div>
                        <div class="nd-card-value">S/ {{ kpi.monthPurchases }}</div>
                    </div>
                </div>

                <!-- <div class="col-12 col-sm-6 col-xl-3">
                    <div class="nd-card nd-card--dark">
                        <div class="nd-card-head">
                            <span class="nd-card-title">MARGEN OPERATIVO</span>
                            <i class="fa fa-chart-line nd-card-icon"></i>
                        </div>
                        <div class="nd-card-value nd-card-value--accent">{{ kpi.operatingMargin }}%</div>
                    </div>
                </div> -->

                <div class="col-12 col-sm-12 col-md-4 mt-3">
                    <div class="nd-card nd-card--outline card-body">
                        <div class="nd-card-head">
                            <span class="nd-card-title text-muted text-uppercase">Ventas Acumuladas</span>
                            <small class="nd-card-note text-muted text-end">Anual<br />{{ kpi.accumulatedYear }}</small>
                        </div>
                        <div class="nd-card-value">S/ {{ kpi.accumulatedSales }}</div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-12 col-lg-6 mt-3">
                    <div class="nd-panel card-body">
                        <div class="nd-panel-head mb-4">
                            <h3 class="fw-semibold m-0">{{ comparisonTitle }}</h3>
                            <div class="nd-legend">
                                <span class="nd-legend-item"><i class="nd-dot nd-dot--sales text-muted"></i>VENTAS</span>
                                <span class="nd-legend-item"><i class="nd-dot nd-dot--purchases text-muted"></i>COMPRAS</span>
                            </div>
                        </div>
                        <monthly-comparison-chart
                            :categories="monthlyComparison.categories"
                            :sales="monthlyComparison.sales"
                            :purchases="monthlyComparison.purchases"
                        ></monthly-comparison-chart>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mt-3">
                    <div class="nd-panel card-body">
                        <div class="nd-panel-head mb-4">
                            <h3 class="fw-semibold m-0">Crecimiento de Ventas</h3>
                            <div class="nd-legend">
                                <span class="nd-legend-item"><i class="nd-line nd-line--current text-muted"></i>{{ salesGrowth.currentLabel }}</span>
                                <span class="nd-legend-item"><i class="nd-line nd-line--previous text-muted"></i>{{ salesGrowth.previousLabel }}</span>
                            </div>
                        </div>
                        <sales-growth-chart
                            :categories="salesGrowth.categories"
                            :current="salesGrowth.current"
                            :previous="salesGrowth.previous"
                            :current-label="salesGrowth.currentLabel"
                            :previous-label="salesGrowth.previousLabel"
                        ></sales-growth-chart>
                    </div>
                </div>
            </div>
        </template>

        <div v-else class="nd-empty text-muted text-center">
            <p>Sección en construcción.</p>
        </div>
    </div>
</template>

<script>
import MonthlyComparisonChart from "./MonthlyComparisonChart.vue";
import SalesGrowthChart from "./SalesGrowthChart.vue";

export default {
    name: "NewDashboard",
    components: { MonthlyComparisonChart, SalesGrowthChart },
    data() {
        return {
            activeTab: "income_expense",
            tabs: [
                { key: "income_expense", label: "Ingresos y Gastos" },
                { key: "tax", label: "Tributario" },
                { key: "business_health", label: "Salud del Negocio" },
            ],
            loadingKpi: false,
            establishments: [],
            form: {
                establishment_id: null,
                period: "month",
                date_pick: moment().format("YYYY-MM"),
                date_range: [
                    moment().startOf("month").format("YYYY-MM-DD"),
                    moment().endOf("month").format("YYYY-MM-DD"),
                ],
                compare: "previous_period",
            },
            kpi: {
                monthSales: "0.00",
                monthPurchases: "0.00",
                operatingMargin: "0",
                accumulatedSales: "0.00",
                accumulatedYear: "",
                monthSalesVariation: 0,
                monthPurchasesVariation: 0,
            },
            monthlyComparison: {
                categories: [],
                sales: [],
                purchases: [],
            },
            salesGrowth: {
                categories: [],
                current: [],
                previous: [],
                currentLabel: "",
                previousLabel: "",
            },
        };
    },
    created() {
        this.loadEstablishments();
        this.loadAll();
    },
    computed: {
        pickerConfig() {
            const map = {
                today: { type: "date", valueFormat: "yyyy-MM-dd", format: "dd/MM/yyyy" },
                week: { type: "week", valueFormat: "yyyy-MM-dd", format: "Sem. WW · yyyy" },
                month: { type: "month", valueFormat: "yyyy-MM", format: "MM/yyyy" },
                year: { type: "year", valueFormat: "yyyy", format: "yyyy" },
            };
            return map[this.form.period] || map.month;
        },
        periodLabel() {
            const labels = {
                today: "de hoy",
                week: "de la semana",
                month: "del mes",
                year: "del año",
            };
            return labels[this.form.period] || "del periodo";
        },
        comparisonTitle() {
            const titles = {
                today: "Comparativa por hora",
                week: "Comparativa diaria",
                month: "Comparativa semanal",
                year: "Comparativa mensual",
            };
            return titles[this.form.period] || "Comparativa";
        },
        periodChipLabel() {
            const [start, end] = this.form.date_range || [];
            if (!start || !end) return "";
            const m = moment(start);
            if (this.form.period === "today") return m.format("DD MMM YYYY");
            if (this.form.period === "month") {
                const label = m.format("MMMM YYYY");
                return label.charAt(0).toUpperCase() + label.slice(1);
            }
            if (this.form.period === "year") return m.format("YYYY");
            // week
            return `${m.format("DD MMM")} – ${moment(end).format("DD MMM YYYY")}`;
        },
        compareLabel() {
            const labels = {
                previous_period: "vs Período anterior",
                previous_year: "vs Año anterior",
            };
            return labels[this.form.compare] || "";
        },
        establishmentLabel() {
            if (!this.form.establishment_id) return "";
            const found = this.establishments.find(
                (e) => e.id === this.form.establishment_id
            );
            return found ? found.name : "";
        },
        activeFilters() {
            const chips = [];
            chips.push({
                key: "period",
                label: this.periodChipLabel,
                variant: "nd-chip--green",
                removable: false,
            });
            if (this.form.establishment_id) {
                chips.push({
                    key: "establishment",
                    label: this.establishmentLabel,
                    variant: "nd-chip--teal",
                    removable: true,
                });
            }
            if (this.form.compare !== "none") {
                chips.push({
                    key: "compare",
                    label: this.compareLabel,
                    variant: "nd-chip--purple",
                    removable: true,
                });
            }
            return chips;
        },
    },
    methods: {
        loadEstablishments() {
            this.$http.get("/dashboard/filter").then((response) => {
                this.establishments = response.data.establishments;
            });
        },
        onPeriodChange() {
            const pickFormats = {
                today: "YYYY-MM-DD",
                week: "YYYY-MM-DD",
                month: "YYYY-MM",
                year: "YYYY",
            };
            this.form.date_pick = moment().format(pickFormats[this.form.period] || "YYYY-MM-DD");
            this.syncRange();
            this.loadAll();
        },
        onPickChange() {
            this.syncRange();
            this.loadAll();
        },
        syncRange() {
            const m = this.form.date_pick
                ? moment(this.form.date_pick, ["YYYY-MM-DD", "YYYY-MM", "YYYY"])
                : moment();
            const units = {
                today: "day",
                week: "isoWeek",
                month: "month",
                year: "year",
            };
            const unit = units[this.form.period] || "month";
            this.form.date_range = [
                m.clone().startOf(unit).format("YYYY-MM-DD"),
                m.clone().endOf(unit).format("YYYY-MM-DD"),
            ];
        },
        removeFilter(key) {
            if (key === "establishment") {
                this.form.establishment_id = null;
            } else if (key === "compare") {
                this.form.compare = "none";
            } else if (key === "period") {
                this.form.period = "month";
                this.onPeriodChange();
                return;
            }
            this.loadAll();
        },
        clearFilters() {
            this.form.establishment_id = null;
            this.form.compare = "none";
            this.form.period = "month";
            this.onPeriodChange();
        },
        loadAll() {
            this.loadKpi();
            this.loadMonthlyComparison();
            this.loadSalesGrowth();
        },
        requestParams() {
            const [date_start, date_end] = this.form.date_range || [];
            return {
                params: {
                    establishment_id: this.form.establishment_id,
                    period: this.form.period,
                    date_start,
                    date_end,
                    compare: this.form.compare,
                },
            };
        },
        loadKpi() {
            this.loadingKpi = true;
            this.$http
                .get("/dashboard/kpi", this.requestParams())
                .then((response) => {
                    const data = response.data.data;
                    this.kpi = {
                        monthSales: this.formatMoney(data.month_sales),
                        monthPurchases: this.formatMoney(data.month_purchases),
                        operatingMargin: data.operating_margin || "0",
                        accumulatedSales: this.formatMoney(data.accumulated_sales),
                        accumulatedYear: data.accumulated_year,
                        monthSalesVariation: data.month_sales_variation,
                        monthPurchasesVariation: data.month_purchases_variation,
                    };
                })
                .finally(() => {
                    this.loadingKpi = false;
                });
        },
        loadMonthlyComparison() {
            this.$http
                .get("/dashboard/monthly-comparison", this.requestParams())
                .then((response) => {
                    const data = response.data.data;
                    this.monthlyComparison = {
                        categories: data.categories,
                        sales: data.sales,
                        purchases: data.purchases,
                    };
                });
        },
        loadSalesGrowth() {
            this.$http
                .get("/dashboard/sales-growth", this.requestParams())
                .then((response) => {
                    const data = response.data.data;
                    this.salesGrowth = {
                        categories: data.categories,
                        current: data.current,
                        previous: data.previous,
                        currentLabel: data.current_label,
                        previousLabel: data.previous_label,
                    };
                });
        },
        formatMoney(value) {
            return Number(value).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        formatPercent(value) {
            const number = Number(value) || 0;
            const sign = number >= 0 ? "+" : "";
            return sign + number.toFixed(1) + "%";
        },
        badgeClass(value) {
            return Number(value) >= 0 ? "nd-badge--up" : "nd-badge--down";
        },
    },
};
</script>

<style scoped>
.new-dashboard {
    padding: 0 0.5rem;
}

/* Filter card */
.nd-filter-label {
    font-size: 0.7rem;
    letter-spacing: 0.05em;
}

/* Active filters */
.nd-active-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eef0f3;
}
.nd-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
}
.nd-chip--green {
    color: #0d9488;
    background: #ccfbf1;
}
.nd-chip--teal {
    color: #0f766e;
    background: #d1fae5;
}
.nd-chip--purple {
    color: #6d28d9;
    background: #ede9fe;
}
.nd-chip-close {
    cursor: pointer;
    font-size: 0.7rem;
    opacity: 0.7;
}
.nd-chip-close:hover {
    opacity: 1;
}
.nd-clear-all {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0;
}
.nd-clear-all:hover {
    text-decoration: underline;
}

/* Tabs */
.nd-tabs {
    display: flex;
    gap: 1.75rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}
.nd-tab {
    background: none;
    border: none;
    padding: 0.5rem 0 0.85rem;
    font-size: 1rem;
    color: #9ca3af;
    cursor: pointer;
    position: relative;
    transition: color 0.2s ease;
}
.nd-tab.is-active {
    color: #6d28d9;
    font-weight: 600;
}
.nd-tab.is-active::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 3px;
    border-radius: 3px;
    background: #6d28d9;
}

/* KPI cards */
.nd-card {
    height: 100%;
}
.nd-card--dark {
    background: #1f2023;
}
.nd-card--outline {
    border-left: 4px solid #6d28d9;
}
.nd-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}
.nd-card-title {
    letter-spacing: 0.05em;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.nd-card-head .nd-badge,
.nd-card-head .nd-card-note {
    flex-shrink: 0;
    margin-left: 0.5rem;
}
.nd-card-value {
    font-size: 1.9rem;
    font-weight: 700;
}
.nd-card--dark .nd-card-value {
    color: #fff;
}
.nd-card-value--accent {
    color: #2dd4bf;
}
.nd-card-icon {
    color: #2dd4bf;
}
.nd-card-note {
    line-height: 1.1;
}
.nd-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
}
.nd-badge--up {
    color: #0d9488;
    background: #ccfbf1;
}
.nd-badge--down {
    color: #4f46e5;
    background: #e0e7ff;
}

/* Chart panels */
.nd-panel {
    height: 100%;
}
.nd-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.nd-legend {
    display: flex;
    gap: 1rem;
}
.nd-legend-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
}
.nd-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block;
}
.nd-dot--sales {
    background: #2dd4bf;
}
.nd-dot--purchases {
    background: #4f46e5;
}
.nd-line {
    width: 16px;
    height: 3px;
    border-radius: 3px;
    display: inline-block;
}
.nd-line--current {
    background: #2dd4bf;
}
.nd-line--previous {
    background: #cbd5e1;
}

.nd-empty {
    padding: 3rem;
}
</style>
