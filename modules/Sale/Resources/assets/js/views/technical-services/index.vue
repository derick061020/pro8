<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/technical-services"><svg
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
                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"
                        />
                        <path
                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"
                        />
                        <path d="M16 5l3 3" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>{{ title }}</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button
                    type="button"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    @click.prevent="clickCreate()"
                >
                    <i class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div> -->
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th>Cliente</th>
                        <th class="text-end">Celular</th>
                        <th class="text-end">Número</th>
                        <th>F. Emisión</th>
                        <th>N° Serie</th>
                        <th>Costo S.</th>
                        <th>Costo P.</th>
                        <th>Total</th>
                        <th class="text-center">Documento</th>
                        <!-- <th>Pago adelantado</th> -->
                        <th></th>
                        <th>Saldo</th>
                        <th class="text-center">Ver</th>
                        <th class="text-end">Acciones</th>
                    </tr>

                    <tr></tr>
                    <tr slot-scope="{ index, row }">
                        <!-- <td>{{ index }}</td> -->
                        <td>
                            {{ row.customer_name }}<br /><small
                                v-text="row.customer_number"
                            ></small>
                        </td>
                        <td class="text-end">{{ row.cellphone }}</td>
                        <td class="text-end">{{ row.id }}</td>
                        <td class="text-start">
                            {{ row.date_of_issue | toDate }}
                        </td>
                        <td class="text-center">{{ row.serial_number }}</td>
                        <td class="text-center">{{ formatDecimal(row.cost) }}</td>
                        <td class="text-center">{{ formatDecimal(row.total) }}</td>
                        <td class="text-center">{{ formatDecimal(row.sum_total) }}</td>
                        <td class="text-center">
                            {{ row.number_document_sale_note }}
                        </td>
                        <!-- <td class="text-center">{{ row.prepayment }}</td> -->
                        <td class="text-end">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                @click.prevent="clickPayment(row.id)"
                            >
                                Pagos
                            </button>
                        </td>

                        <td class="text-center">{{ formatDecimal(row.balance) }}</td>

                        <td class="text-center">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info"
                                @click.prevent="clickPrint(row.id)"
                            >
                                PDF
                            </button>
                        </td>

                        <td class="text-end">
                            <el-dropdown
                                trigger="click"
                                @command="(command) => handleRowAction(command, row)"
                            >
                                <el-button class="btn-dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                    <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item
                                        v-if="!row.has_document_sale_note"
                                        command="generate"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16"
                                          viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                          <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                          <path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"/>
                                        </svg>
                                        Generar comprobante
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="!row.has_document_sale_note"
                                        command="edit"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                        Editar
                                    </el-dropdown-item>
                                    <el-dropdown-item v-if="!row.has_document_sale_note || typeUser === 'admin'" divided></el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="typeUser === 'admin'"
                                        command="delete"
                                        class="text-danger option-delete"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        Eliminar
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </td>
                    </tr>
                </data-table>
            </div>

            <technical-service-options
                :showDialog.sync="showDialogOptions"
                :recordId="recordId"
                :showGenerate="true"
                :showClose="true"
            ></technical-service-options>

            <technical-services-form
                :showDialog.sync="showDialog"
                :configuration="configuration"
                :recordId="recordId"
            ></technical-services-form>

            <technical-service-payments
                :showDialog.sync="showDialogPayments"
                :recordId="recordId"
                :external="true"
            ></technical-service-payments>
        </div>
    </div>
</template>
<style>
@media only screen and (max-width: 485px) {
    .filter-container {
        margin-top: 0px;
        & .btn-filter-content,
        .btn-container-mobile {
            display: flex;
            align-items: center;
            justify-content: start;
        }
    }
}
</style>
<script>
import TechnicalServicesForm from "./form.vue";
import DataTable from "@components/DataTable.vue";
import { deletable } from "@mixins/deletable";
import TechnicalServicePayments from "./partials/payments.vue";
import TechnicalServiceOptions from "./partials/options.vue";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";

export default {
    mixins: [deletable],
    props: ["typeUser", "configuration"],
    computed: {
        ...mapState(["exchange_rate", "config", "currency_types"])
    },
    components: {
        TechnicalServicesForm,
        DataTable,
        TechnicalServicePayments,
        TechnicalServiceOptions
    },
    data() {
        return {
            title: null,
            showDialog: false,
            showDialogOptions: false,
            resource: "technical-services",
            recordId: null,
            showDialogPayments: false,
            decimal_quantity: 2
        };
    },
    created() {
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
        this.loadCurrencyTypes();
        this.loadExchangeRate();
        this.title = "Servicios de soporte técnico";
        this.loadDecimalQuantity();
    },
    methods: {
        loadDecimalQuantity() {
            // Obtener la configuración general para los decimales
            this.$http ? this.$http.get('/configurations/record').then(response => {
                if (response.data && response.data.data && response.data.data.decimal_quantity) {
                    this.decimal_quantity = response.data.data.decimal_quantity;
                }
            }) :
            (window.axios && window.axios.get('/configurations/record').then(response => {
                if (response.data && response.data.data && response.data.data.decimal_quantity) {
                    this.decimal_quantity = response.data.data.decimal_quantity;
                }
            }));
        },
        formatDecimal(value) {
            if (value === undefined || value === null || isNaN(value)) return '';
            return Number(value).toLocaleString('en-US', { minimumFractionDigits: this.decimal_quantity, maximumFractionDigits: this.decimal_quantity });
        },
        formatDate(date) {
            if (!date) return null;
            return moment(date).format("DD-MM-YYYY");
        },
        ...mapActions([
            "loadConfiguration",
            "loadExchangeRate",
            "loadCurrencyTypes"
        ]),
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        clickPrint(recordId) {
            window.open(`/${this.resource}/print/${recordId}/a4`, "_blank");
        },
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        handleRowAction(command, row) {
            if (command === "generate") {
                this.clickOptions(row.id);
                return;
            }

            if (command === "edit") {
                this.clickCreate(row.id);
                return;
            }

            if (command === "delete") {
                this.clickDelete(row.id);
            }
        }
    }
};
</script>
