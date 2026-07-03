<template>
    <div class="plans-page">

        <div class="page-header pe-0">
            <h2>
                <a href="/full_suscription/plans">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Planes</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button class="btn btn-custom btn-sm mt-2 me-2" type="button" @click.prevent="clickShowPlan()">
                    <i class="fa fa-plus-circle"></i>
                    Nuevo plan
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-2">
            <div class="col-md-12 filter-container">
                <div class="btn-filter-content">
                    <el-button
                        type="secondary"
                        class="btn-show-filter mb-2"
                        :class="{ shift: filterVisible }"
                        @click="filterVisible = !filterVisible"
                    >
                        {{ filterVisible ? 'Ocultar filtros' : 'Mostrar filtros' }}
                    </el-button>
                </div>
                <div v-if="filterVisible" class="row mx-0">
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-input
                            v-model="planFilters.q"
                            placeholder="Buscar plan..."
                            prefix-icon="el-icon-search"
                            style="width:100%;"
                            clearable
                            @input="onFilterInput"
                        ></el-input>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.frequency"
                            placeholder="Frecuencia"
                            style="width:100%;"
                            clearable
                            @change="applyPlanFilters"
                        >
                            <el-option label="Todas" value=""></el-option>
                            <el-option
                                v-for="period in periods"
                                :key="period.id"
                                :label="period.name"
                                :value="period.id"
                            ></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.status"
                            placeholder="Estado"
                            style="width:100%;"
                            clearable
                            @change="applyPlanFilters"
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option label="Activos" :value="true"></el-option>
                            <el-option label="Inactivos" :value="false"></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.trial"
                            placeholder="Período de Prueba"
                            style="width:100%;"
                            clearable
                            @change="applyPlanFilters"
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option label="Con Prueba" value="with"></el-option>
                            <el-option label="Sin Prueba" value="without"></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-12 pb-2">
                        <el-button @click="clearPlanFilters" style="width:100%;">
                            <i class="fa fa-times"></i> Limpiar
                        </el-button>
                    </div>
                </div>
            </div>
        </div>

        <div class="plans-card">
            <data-table ref="dataTable" :periods="periods" :hide-filter="true">
                <tr slot="heading">
                    <th class="th-cell">Nombre</th>
                    <th class="th-cell">Estado</th>
                    <th class="th-cell">Frecuencia</th>
                    <th class="th-cell text-right">Periodos</th>
                    <th class="th-cell text-right">Dias de prueba</th>
                    <th class="th-cell text-right">Ítems</th>
                    <th class="th-cell text-right">Monto del plan</th>
                    <th class="th-cell text-right">Suscriptores</th>
                    <th class="th-cell text-right">Acciones</th>
                </tr>
                <tr slot-scope="{ index, row }" class="td-row">
                    <td class="td-cell">
                        <span class="plan-name">{{ row.name }}</span>
                    </td>
                    <td class="td-cell">
                        <el-switch
                            v-model="row.status"
                            active-color="#18181b"
                            inactive-color="#e4e4e7"
                            @change="toggleStatus(row)"
                        ></el-switch>
                    </td>
                    <td class="td-cell">
                        <span class="badge-outline">{{ row.period }}</span>
                    </td>
                    <td class="td-cell text-right">
                        <span v-if="row.unlimited" class="badge-green">Ilimitado</span>
                        <span v-else class="text-muted-sm">{{ row.quantity_period }}</span>
                    </td>
                    <td class="td-cell text-right">
                        <span v-if="row.trial_days > 0" class="badge-amber">{{ row.trial_days }}d</span>
                        <span v-else class="text-muted-sm">—</span>
                    </td>
                    <td class="td-cell text-right">
                        <span class="text-muted-sm">{{ row.items.length }}</span>
                    </td>
                    <td class="td-cell text-right">
                        <span class="plan-total">{{ row.currency.symbol }} {{ row.total }}</span>
                    </td>
                    <td class="td-cell text-right">
                        <span class="text-muted-sm">{{ row.subscribers }}</span>
                    </td>
                    <td class="td-cell text-right">
                        <div class="action-group">
                            <button
                                class="icon-btn"
                                type="button"
                                title="Editar"
                                @click.prevent="clickShowPlan(row)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button
                                class="icon-btn"
                                type="button"
                                title="Ver suscripciones"
                                @click.prevent="viewPlanSubscriptions(row)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button
                                v-if="row.status"
                                class="icon-btn"
                                type="button"
                                title="Copiar enlace de suscripción"
                                @click.prevent="copySubscriptionLink(row.url_client)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                </svg>
                            </button>
                            <button
                                v-if="row.subscribers === 0"
                                class="icon-btn icon-btn--danger"
                                type="button"
                                title="Eliminar"
                                @click.prevent="clickDelete(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            </data-table>
        </div>

        <plans-form
            :showDialog.sync="showDialog"
            @reload-data="sendReload"
        ></plans-form>
    </div>
</template>

<style scoped>
.plans-page { padding: 0; }

.plans-card {
    background: #fff;
    border: 1px solid #e4e4e7;
    border-radius: .75rem;
    overflow: hidden;
}

.th-cell {
    padding: .65rem 1rem;
    font-size: .75rem;
    font-weight: 500;
    color: #71717a;
    background: #fafafa;
    border-bottom: 1px solid #e4e4e7;
    white-space: nowrap;
    text-align: left;
}

.td-row { transition: background .1s; }
.td-row:hover { background: #fafafa; }
.td-row:not(:last-child) .td-cell { border-bottom: 1px solid #f4f4f5; }

.td-cell {
    padding: .75rem 1rem;
    font-size: .8125rem;
    color: #18181b;
    vertical-align: middle;
    text-align: left;
}

.text-right { text-align: right !important; }

.plan-name { font-weight: 500; }
.plan-total { font-weight: 600; color: #18181b; }
.text-muted-sm { color: #a1a1aa; font-size: .8125rem; }

.badge-outline, .badge-green, .badge-amber {
    display: inline-flex;
    align-items: center;
    padding: .1rem .45rem;
    border-radius: .35rem;
    font-size: .7rem;
    font-weight: 500;
    line-height: 1.4;
}
.badge-outline { background: transparent; color: #52525b; border: 1px solid #d4d4d8; }
.badge-green   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.badge-amber   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

.action-group {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    justify-content: flex-end;
}

.icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.75rem;
    height: 1.75rem;
    border-radius: .4rem;
    border: 1px solid #e4e4e7;
    background: transparent;
    color: #52525b;
    cursor: pointer;
    transition: background .12s, color .12s, border-color .12s;
}
.icon-btn:hover { background: #f4f4f5; color: #18181b; border-color: #d4d4d8; }
.icon-btn--danger { color: #dc2626; border-color: #fecaca; }
.icon-btn--danger:hover { background: #fef2f2; color: #b91c1c; border-color: #fca5a5; }
</style>

<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

import PlansForm from './form.vue'
import DataTable from '../components/SuscriptionsDataTable.vue'
import {deletable} from '../../../../../../resources/js/mixins/deletable'
import {exchangeRate} from "../../../../../../resources/js/mixins/functions";

export default {
    props: ['configuration', 'date'],
    mixins: [deletable, exchangeRate],
    components: { PlansForm, DataTable },
    data() {
        return {
            showDialog: false,
            filterVisible: false,
            filterTimeout: null,
            planFilters: {
                q: null,
                frequency: null,
                status: null,
                trial: null,
            },
        }
    },
    computed: {
        ...mapState([
            'config', 'resource', 'form_data', 'exchange_rate', 'periods',
            'affectation_igv_types', 'item_search_extra_parameters',
            'unit_types', 'payment_method_types',
        ]),
    },
    created() {
        this.loadConfiguration()
        this.$store.commit('setItemSearchExtraParameters', {'only_service': 0});
        this.$store.commit('setConfiguration', this.configuration)
        this.$store.commit('setResource', 'plans')
        this.$store.commit('setFormData', { periods: 'M', quantity_period: 12 })
        this.searchExchangeRateByDate(this.date).then(response => {
            this.$store.commit('setExchangeRate', response)
        });
        this.getCommonData();
    },
    methods: {
        ...mapActions(['loadConfiguration', 'clearFormData']),
        getCommonData() {
            this.$http.post('CommonData', {})
                .then((response) => {
                    this.$store.commit('setCurrencyTypes', response.data.currency_types)
                    this.$store.commit('setAffectationIgvTypes', response.data.affectation_igv_types)
                    this.$store.commit('setUnitTypes', response.data.unit_types)
                    this.$store.commit('setPaymentMethodTypes', response.data.payments_credit)
                })
        },
        sendReload() {
            this.$eventHub.$emit('reloadData')
        },
        clickShowPlan(row) {
            this.clearFormData();
            if (row === undefined) row = {};
            if (row.quantity_period === undefined) row.quantity_period = 12;
            if (row.periods === undefined) row.periods = 'M';
            this.$store.commit('setFormData', row)
            this.showDialog = true
        },
        clickDelete(id) {
            this.destroy(`/full_suscription/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit('reloadData')
            )
        },
        toggleStatus(row) {
            this.$http.post(`/full_suscription/${this.resource}/${row.id}/status`, {status: row.status})
                .then(() => { this.$message.success('Estado actualizado correctamente'); })
                .catch(() => { row.status = !row.status; this.$message.error('Error al actualizar el estado'); })
        },
        copySubscriptionLink(url) {
            if (!url) return;
            navigator.clipboard.writeText(url).then(() => {
                this.$message.success('Enlace copiado al portapapeles');
            }).catch(() => {
                this.$message.error('No se pudo copiar el enlace');
            });
        },
        onFilterInput() {
            clearTimeout(this.filterTimeout);
            this.filterTimeout = setTimeout(() => this.applyPlanFilters(), 300);
        },
        applyPlanFilters() {
            const dt = this.$refs.dataTable;
            if (!dt) return;
            dt.planFilters = { ...this.planFilters };
            dt.pagination.current_page = 1;
            dt.getRecords();
        },
        clearPlanFilters() {
            this.planFilters = { q: null, frequency: null, status: null, trial: null };
            this.applyPlanFilters();
        },
    },
};
</script>
