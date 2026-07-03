<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/full_suscription/payments">
                    <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M7 14h.013" /><path d="M10.01 14h.005" /><path d="M13.01 14h.005" /><path d="M16.015 14h.005" /><path d="M13.015 17h.005" /><path d="M7.01 17h.005" /><path d="M10.01 17h.005" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>
                        Suscripciones
                    </span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button class="btn btn-custom btn-sm  mt-2 me-2"
                        type="button"
                        @click.prevent="clickShowPlan()">
                    <i class="fa fa-plus-circle">
                    </i>
                    Nuevo
                </button>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <data-table :plans="plans">
                    <tr slot="heading">
                        <th class="text-start">Cliente</th>
                        <th class="text-start">Estado</th>
                        <th class="text-start">Plan</th>
                        <th class="text-center">Ciclos</th>
                        <th class="text-center">Últ. Vencimiento</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <!-- Cliente -->
                        <td class="text-start">
                            <div class="fw-bold">{{ row.parent_customer.name }}</div>
                            <div class="text-muted" style="font-size:12px;">
                                {{ row.parent_customer.document_type }}&nbsp;{{ row.parent_customer.number }}
                            </div>
                        </td>

                        <!-- Estado -->
                        <td class="text-start">
                            <span :class="statusClass(row.status)">
                                <span class="status-dot"></span>
                                {{ statusLabel(row.status) }}
                            </span>
                        </td>

                        <!-- Plan -->
                        <td class="text-start">
                            <div class="fw-bold">{{ row.plan.name }}</div>
                            <div class="text-muted" style="font-size:12px;">
                                {{ row.cat_period ? row.cat_period.name : '' }}
                            </div>
                        </td>

                        <!-- Ciclos -->
                        <td class="text-center">
                            <span v-if="row.plan.unlimited" class="text-muted" style="font-size:12px;">Ilimitado</span>
                            <span v-else class="ciclos-badge"> {{ row.orders_created }}/{{ row.quantity_period }}</span>
                        </td>

                        <!-- Últ. Vencimiento -->
                        <td class="text-center">
                            <span :class="{ 'text-danger fw-semibold': isDateExpired(row.end_date) }">
                                {{ formatDate(row.end_date) }}
                            </span>
                        </td>

                        <!-- Monto -->
                        <td class="text-end">
                            {{ formatAmount(row.total) }}
                        </td>

                        <!-- Acciones -->
                        <td class="text-end">
                            <el-dropdown v-if="row.status !== 'cancelada'" trigger="click" @command="handleCommand($event, row)">
                                <el-button class="btn-dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </el-button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item command="edit">
                                            Editar
                                        </el-dropdown-item>
                                        <el-dropdown-item command="authorized" v-if="row.status === 'pausada'">
                                            Activar
                                        </el-dropdown-item>
                                        <el-dropdown-item command="cancelled">
                                            Finalizar
                                        </el-dropdown-item>
                                        <el-dropdown-item divided></el-dropdown-item>
                                        <el-dropdown-item command="paused" v-if="row.status === 'activa'">
                                            Suspender
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </td>
                    </tr>
                </data-table>
            </div>
            <customers-form
                :showDialog.sync="showDialog"
                :suscriptionId="suscriptionId"
                @clearSuscriptionId="clearsuscriptionid">
            </customers-form>
        </div>
    </div>
</template>
<style>
@media only screen and (max-width: 485px) {
    .filter-container {
        margin-top: 0px;
        & .btn-filter-content, .btn-container-mobile {
            display: flex;
            align-items: center;
            justify-content: start;
        }
    }
}

/* Badges de estado */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}
.status-badge .status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.status-activa         { background: #d1fae5; color: #065f46; }
.status-activa         .status-dot { background: #10b981; }
.status-en-prueba      { background: #dbeafe; color: #1e40af; }
.status-en-prueba      .status-dot { background: #3b82f6; }
.status-pendiente      { background: #fef9c3; color: #92400e; }
.status-pendiente      .status-dot { background: #f59e0b; }
.status-vencida        { background: #fee2e2; color: #991b1b; }
.status-vencida        .status-dot { background: #ef4444; }
.status-pausada        { background: #ede9fe; color: #5b21b6; }
.status-pausada        .status-dot { background: #8b5cf6; }
.status-cancelada      { background: #f3f4f6; color: #374151; }
.status-cancelada      .status-dot { background: #9ca3af; }

/* Chip circular de ciclos */
.ciclos-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #d1d5db;
    font-size: 13px;
    font-weight: 500;
    color: #374151;
}
</style>
<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

import CustomersForm from './form.vue'
import DataTable from '../components/PaymentsDataTable.vue'
import {deletable} from '../../../../../../resources/js/mixins/deletable'
import {exchangeRate} from "../../../../../../resources/js/mixins/functions";

export default {
    props: [
        'configuration',
        'date'
    ],
    mixins: [
        deletable,
        exchangeRate
    ],
    components: {
        CustomersForm,
        DataTable
    },
    data() {
        return {
            suscriptionId: null,
            showDialog: false,
        }
    },
    computed: {
        ...mapState([
            'config',
            'resource',
            'form_data',
            'exchange_rate',
            'periods',
            'plans',
            'affectation_igv_types',
            'item_search_extra_parameters',
            'unit_types',
            'payment_method_types',
            'customers',
            'customer_addresses',
            'all_customers',
        ]),
    },
    created() {
        this.loadConfiguration()

        this.$store.commit('setItemSearchExtraParameters', {'only_service': 1});
        this.$store.commit('setConfiguration', this.configuration)
        this.$store.commit('setResource', 'payments')
        this.$store.commit('setFormData', {})
        this.$store.commit('setCustomers', [])
        this.getCommonData();
        this.getSuscriptionData();
        this.searchExchangeRateByDate(this.date).then(response => {
            this.$store.commit('setExchangeRate', response)
            // this.form.exchange_rate_sale = this.exchange_rate

        });


    },
    methods: {
        ...mapActions([
            'loadConfiguration',
            'clearFormData',
        ]),
        getCommonData() {
            this.$http.post('CommonData', {})
                .then((response) => {
                    this.$store.commit('setCurrencyTypes', response.data.currency_types)
                    this.$store.commit('setAffectationIgvTypes', response.data.affectation_igv_types)
                    this.$store.commit('setUnitTypes', response.data.unit_types)
                    this.$store.commit('setPaymentMethodTypes', response.data.payments_credit)
                })
        },

        getSuscriptionData() {
            this.$http
                .post(`/full_suscription/${this.resource}/tables`, {})
                .then(response => {
                    let customers = response.data.customers;
                    this.$store.commit('setPeriods', response.data.periods)
                    this.$store.commit('setCustomers', customers)
                    this.$store.commit('setAllCustomers', customers)
                    this.$store.commit('setPlans', response.data.plans)
                })
        },


        clickShowPlan(row) {

            this.clearFormData();
            this.suscriptionId = null
            if (row !== undefined && row.id !== null) {
                this.suscriptionId = row.id
            }

            this.$store.commit('setFormData', row)
            this.showDialog = true

        },

        changeStatus(id, status) {
            this.$http.post(`/full_suscription/payments/${id}/change-status`, { status })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit('reloadData');
                    }
                })
        },
        handleCommand(command, row) {
            if (command === 'edit') {
                this.clickShowPlan(row);
                return;
            }
            if (command === 'cancelled') {
                this.$confirm(
                    '¿Estás seguro de que deseas finalizar esta suscripción? Esta acción no se puede deshacer.',
                    'Confirmar finalización',
                    {
                        confirmButtonText: 'Sí, finalizar',
                        cancelButtonText: 'Cancelar',
                        type: 'warning',
                    }
                ).then(() => {
                    this.changeStatus(row.id, command);
                }).catch(() => {});
                return;
            }
            if (command === 'paused') {
                this.$confirm(
                    'Al suspender la suscripción se seguirán generando nuevos cobros. Si no deseas más cobros, debes finalizarla.',
                    'Suspender suscripción',
                    {
                        confirmButtonText: 'Solo suspender',
                        cancelButtonText: 'Finalizar',
                        distinguishCancelAndClose: true,
                        type: 'warning',
                    }
                ).then(() => {
                    this.changeStatus(row.id, 'paused');
                }).catch(action => {
                    if (action === 'cancel') this.changeStatus(row.id, 'cancelled');
                });
                return;
            }
            this.changeStatus(row.id, command);
        },
        clearsuscriptionid() {
            this.suscriptionId = null;
        },

        // Retorna la clase CSS del badge de estado
        statusClass(status) {
            const map = {
                activa:         'status-badge status-activa',
                en_prueba:      'status-badge status-en-prueba',
                pendiente_pago: 'status-badge status-pendiente',
                vencida:        'status-badge status-vencida',
                pausada:        'status-badge status-pausada',
                cancelada:      'status-badge status-cancelada',
            };
            return map[status] || 'status-badge';
        },

        // Retorna la etiqueta legible del estado
        statusLabel(status) {
            const map = {
                activa:         'Activa',
                en_prueba:      'En prueba',
                pendiente_pago: 'Pendiente de pago',
                vencida:        'Vencida',
                pausada:        'Pausada',
                cancelada:      'Cancelada',
            };
            return map[status] || status;
        },

        // Formatea fecha Y-m-d → DD/MM/YYYY
        formatDate(dateStr) {
            if (!dateStr) return '–';
            const [y, m, d] = dateStr.split('-');
            return `${d}/${m}/${y}`;
        },

        // Devuelve true si la fecha ya pasó respecto a hoy
        isDateExpired(dateStr) {
            if (!dateStr) return false;
            const today = new Date().toISOString().slice(0, 10);
            return dateStr < today;
        },

        // Formatea el monto como "S/ X,XXX.XX" o "–"
        formatAmount(total) {
            if (!total || parseFloat(total) === 0) return '–';
            return 'S/ ' + parseFloat(total)
                .toFixed(2)
                .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
    }
}
</script>
