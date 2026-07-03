<template>
    <div>
        <!-- Basado en resources/js/views/tenant/sale_notes/index.vue -->
        <div class="page-header pe-0">
            <h2>
                <a href="/full-suscription/receipts">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        style="margin-top: -5px;"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"
                        />
                        <path d="M16 3v4" />
                        <path d="M8 3v4" />
                        <path d="M4 11h16" />
                        <path d="M7 14h.013" />
                        <path d="M10.01 14h.005" />
                        <path d="M13.01 14h.005" />
                        <path d="M16.015 14h.005" />
                        <path d="M13.015 17h.005" />
                        <path d="M7.01 17h.005" />
                        <path d="M10.01 17h.005" />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Recibos de pago</span>
                </li>
            </ol>
            <!--
            <div class="right-wrapper pull-right">
                <a class="btn btn-custom btn-sm  mt-2 mr-2"
                   href="#"
                   @click.prevent="clickCreate()">
                    <i class="fa fa-plus-circle">
                    </i> Nuevo</a>
                <a class="btn btn-custom btn-sm  mt-2 mr-2"
                   href="#"
                   @click.prevent="onOpenModalGenerateCPE">Generar comprobante desde múltiples Notas</a>
                <a v-if="config.send_data_to_other_server === true"
                   class="btn btn-custom btn-sm  mt-2 mr-2"
                   href="#"
                   @click.prevent="onOpenModalMigrateNv">Migrar Datos</a>
            </div>
            -->
        </div>

        <!-- Tarjetas de resumen -->
        <div class="row mb-3">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-card--collected">
                    <div class="stat-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1" /><path d="M12 7v10" /></svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">Total cobrado</span>
                        <span class="stat-card__value">S/ {{ formatAmount(statistics.total_collected) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-card--approved">
                    <div class="stat-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">Pagos aprobados</span>
                        <span class="stat-card__value">{{ statistics.total_approved || 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-card--rejected">
                    <div class="stat-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">Pagos rechazados</span>
                        <span class="stat-card__value">{{ statistics.total_rejected || 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card stat-card--pending">
                    <div class="stat-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                    </div>
                    <div class="stat-card__body">
                        <span class="stat-card__label">Pagos pendientes</span>
                        <span class="stat-card__value">{{ statistics.total_pending || 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i
                            class="el-icon-arrow-down el-icon--right"
                        >
                        </i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                            v-for="(column, index) in columns"
                            :key="index"
                        >
                            <el-checkbox
                                v-model="column.visible"
                                @change="saveColumnVisibility"
                                >{{ column.title }}</el-checkbox
                            >
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table-payment-receipt-order
                    :resource="resource"
                    :extraquery="{ onlyFullSuscription: 1 }"
                >
                <tr slot="heading">
                        <th class="text-center">N° Orden</th>
                        <th class="text-start">Fecha Emisión</th>
                        <th>Cliente</th>
                        <th>Plan de suscripción</th>
                        <th>Estado de la orden</th>
                        <th class="text-end">F. del pago</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Comprobante</th>
                    </tr>

                    <tr></tr>
                    <tr slot-scope="{ index, row }">
                        <!-- N° Orden -->
                        <td class="text-center">
                            <span v-if="row.order_number" class="order-number-badge">{{ row.order_number }}</span>
                            <span v-else class="text-muted">—</span>
                        </td>
                        <!-- Fecha Emisión -->
                        <td class="text-start">
                            {{ formatDate(row.date_of_issue) }}
                        </td>
                        <!-- Cliente -->
                        <td>
                            {{ row.subscriptor_name }}
                        </td>
                        <!-- Plan -->
                        <td>{{ row.plan_name }}</td>
                        <!-- Estado -->
                        <td>
                            <span
                                class="badge"
                                :class="{
                                    'badge-status--paid':     row.status_value === 'paid',
                                    'badge-status--pending':  row.status_value === 'pending',
                                    'badge-status--rejected': row.status_value === 'canceled' || row.status_value === 'rejected',
                                    'badge-status--expired':  row.status_value === 'expired',
                                }"
                            >{{ row.status }}</span>
                        </td>
                        <!-- Fecha de pago -->
                        <td class="text-end">{{ row.date_of_payment }}</td>
                        <!-- Total -->
                        <td class="text-end font-weight-bold">{{ row.total }}</td>
                        <!-- Comprobante -->
                        <td class="text-center">
                            <div v-if="row.number_full" class="receipt-cell">
                                <span class="receipt-number">{{ row.number_full }}</span>
                                <a
                                    v-if="row.download_pdf_a4"
                                    :href="row.download_pdf_a4"
                                    target="_blank"
                                    class="receipt-pdf-link"
                                    title="Descargar PDF"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-1px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                    Descargar PDF
                                </a>
                            </div>
                            <el-button
                                :loading="generatingIds.includes(row.id)"
                                v-else-if="row.status_value !== 'canceled'"
                                class="btn-generate"
                                @click="clickGenerate(row.id)"
                                title="Generar comprobante"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-1px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 11v6" /><path d="M9 14h6" /></svg>
                                Generar
                            </el-button>
                        </td>
                    </tr>

                </data-table-payment-receipt-order>
            </div>
        </div>

        <sale-note-payments
            :documentId="recordId"
            :showDialog.sync="showDialogPayments"
        >
        </sale-note-payments>

        <sale-notes-options
            :configuration="config"
            :recordId="saleNotesNewId"
            :showClose="true"
            :showDialog.sync="showDialogOptions"
        >
        </sale-notes-options>

        <sale-note-generate
            :recordId="recordId"
            :show.sync="showDialogGenerate"
            :showClose="false"
            :showGenerate="true"
        >
        </sale-note-generate>
        <ModalGenerateCPE :show.sync="showModalGenerateCPE"> </ModalGenerateCPE>
        <!--
        <UploadToOtherServer
            :configuration="config"
            :showMigrate.sync="showMigrateNv"
        >
        </UploadToOtherServer>
        -->
    </div>
</template>

<script>
// import DataTable from '../../../components/DataTableSaleNote.vue'
// import UploadToOtherServer from './partials/upload_other_server_group.vue'
import SaleNotePayments from "./partials/payments.vue";
import SaleNotesOptions from "./partials/options.vue";
import SaleNoteGenerate from "./partials/option_documents.vue";
import ModalGenerateCPE from "./ModalGenerateCPE.vue";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import { deletable } from "../../../../../../resources/js/mixins/deletable";

export default {
    // tenant-index-payment-receipt
    props: [
        "soapCompany",
        // 'typeUser',
        "configuration"
    ],
    mixins: [deletable],
    components: {
        //  DataTable,
        SaleNotePayments,
        SaleNotesOptions,
        SaleNoteGenerate,
        ModalGenerateCPE
        // UploadToOtherServer
    },
    computed: {
        ...mapState([
            'config',
        ])
    },
    data() {
        return {
            showModalGenerateCPE: false,
            generatingIds: [],
            statistics: {
                total_collected: 0,
                total_approved: 0,
                total_rejected: 0,
                total_pending: 0,
            },
            showMigrateNv: false,
            resource: "sale-notes",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            saleNotesNewId: null,
            recordId: null,
            columns: {
                total_paid: {
                    title: "Pagado",
                    visible: false
                },

                total_pending_paid: {
                    title: "Por pagar",
                    visible: false
                }
                /*
                paid: {
                    title: 'Estado de Pago',
                    visible: false
                },
                /*

                due_date: {
                    title: 'Fecha de Vencimiento',
                    visible: false
                },
                total_free: {
                    title: 'T.Gratuito',
                    visible: false
                },
                total_exportation: {
                    title: 'T.Exportación',
                    visible: false
                },
                total_unaffected: {
                    title: 'T.Inafecto',
                    visible: false
                },
                total_exonerated: {
                    title: 'T.Exonerado',
                    visible: false
                },
                total_taxed: {
                    title: 'T.Gravado',
                    visible: false
                },
                total_igv: {
                    title: 'T.IGV',
                    visible: false
                },
                type_period: {
                    title: 'Tipo Periodo',
                    visible: true
                },
                quantity_period: {
                    title: 'Cantidad Periodo',
                    visible: true
                },
                license_plate: {
                    title: 'Placa',
                    visible: true
                },
                */
            }
        };
    },
    created() {
        this.loadColumnVisibility();
        this.getStatistics();
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
    },
    filters: {
        period(name) {
            let res = "";
            switch (name) {
                case "month":
                    res = "Mensual";
                    break;
                case "year":
                    res = "Anual";
                    break;
                default:
                    break;
            }

            return res;
        }
    },
    mounted() {
    },
    methods: {
        formatDate(date) {
            if (!date) return null;
            const parsedDate = moment(date);
            return parsedDate.isValid()
                ? parsedDate.format("DD-MM-YYYY")
                : null;
        },
        saveColumnVisibility() {
            localStorage.setItem(
                "columnVisibilityPaymentreceipt",
                JSON.stringify(this.columns)
            );
        },
        loadColumnVisibility() {
            const savedColumns = localStorage.getItem(
                "columnVisibilityPaymentreceipt"
            );
            if (savedColumns) {
                this.columns = JSON.parse(savedColumns);
            }
        },
        ...mapActions(["loadConfiguration"]),
        getStatistics() {
            this.$http.get('/full-suscription/receipts/statistics').then(response => {
                if (response.data.success) {
                    this.statistics = response.data.statistics;
                }
            });
        },
        formatAmount(value) {
            const num = parseFloat(value) || 0;
            return num.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },
        duplicate(id) {
            this.$http
                .post(`${this.resource}/duplicate`, { id })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se guardaron los cambios correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch(() => {});
            this.$eventHub.$emit("reloadData");
        },
        onOpenModalGenerateCPE() {
            this.showModalGenerateCPE = true;
        },
        onOpenModalMigrateNv() {
            this.showMigrateNv = true;
        },
        clickDownload(external_id) {
            window.open(
                `/sale-notes/downloadExternal/${external_id}`,
                "_blank"
            );
        },
        clickOptions(recordId) {
            this.saleNotesNewId = recordId;
            this.showDialogOptions = true;
        },
        sendToServer(recordId) {
            this.$http
                .post("/sale-notes/UpToOther", { sale_note_id: recordId })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (
                        error.response !== undefined &&
                        error.response.status !== undefined &&
                        error.response.status.errors !== undefined &&
                        error.response.status === 422
                    ) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickGenerate(recordId) {
            this.generatingIds = [...this.generatingIds, recordId];
            this.$http.post(`/full-suscription/receipts/${recordId}/generate-document`).then((response) => {
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.$eventHub.$emit("reloadData");
                    this.getStatistics();
                } else {
                    this.$message.error(response.data.message || 'Error al generar el comprobante. Por favor, intente nuevamente o contacte con soporte.');
                }
                this.generatingIds = this.generatingIds.filter(id => id !== recordId);
            }).catch(() => {
                this.$message.error('Error al generar el comprobante. Por favor, intente nuevamente o contacte con soporte.');
                this.generatingIds = this.generatingIds.filter(id => id !== recordId);
            });
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        clickCreate(id = "") {
            location.href = `/${this.resource}/create/${id}`;
        },

        changeConcurrency(row) {
            this.$http
                .post(`/${this.resource}/enabled-concurrency`, row)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickVoided(id) {
            this.anular(`/${this.resource}/anulate/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        }
    }
};
</script>

<style scoped>
.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 1rem 1.25rem;
  border-radius: 10px;
  background: #fff;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 4px #0000000a;
}

.stat-card__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border-radius: 8px;
  flex-shrink: 0;
}

.stat-card__body {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-card__label {
  font-size: .75rem;
  color: #71717a;
  font-weight: 500;
}

.stat-card__value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #18181b;
  line-height: 1.2;
}

/* ── Badges de estado de orden ── */
.badge {
  display: inline-block;
  padding: .25rem .65rem;
  border-radius: 9999px;
  font-size: .7rem;
  font-weight: 600;
  letter-spacing: .03em;
  text-transform: uppercase;
  line-height: 1;
  white-space: nowrap;
}

.badge-status--paid {
  background: #dcfce7;
  color: #15803d;
}

.badge-status--pending {
  background: #fef9c3;
  color: #92400e;
}

.badge-status--rejected {
  background: #fee2e2;
  color: #b91c1c;
}

.badge-status--expired {
  background: #f1f5f9;
  color: #475569;
}

/* Variantes de color */
.stat-card--collected .stat-card__icon {
  background: #eff6ff;
  color: #2563eb;
}

.stat-card--approved .stat-card__icon {
  background: #f0fdf4;
  color: #16a34a;
}

.stat-card--rejected .stat-card__icon {
  background: #fff1f2;
  color: #e11d48;
}

.stat-card--pending .stat-card__icon {
  background: #fffbeb;
  color: #d97706;
}
</style>
