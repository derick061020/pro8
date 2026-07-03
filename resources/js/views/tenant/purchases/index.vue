<template>
    <div class="purchases">
        <div class="page-header pe-0">
            <h2>
                <a href="/purchases">
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
                        class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z"
                        />
                        <path d="M9 11v-5a3 3 0 0 1 6 0v5" />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Compras</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    :href="`/${resource}/create`"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
                <button
                    @click.prevent="clickImport()"
                    type="button"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                >
                    <i class="fa fa-upload"></i> Importar
                </button>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i
                            class="el-icon-arrow-down el-icon--right"
                        ></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="col in orderedColumns" :key="col.key">
                            <el-checkbox v-model="columns[col.key].visible" @change="saveColumnVisibility">{{ col.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">F. Emisión</th>
                            <th v-if="col.visible && col.key === 'date_of_due'" :key="col.key" class="text-center">F. Vencimiento</th>
                            <th v-if="col.visible && col.key === 'supplier'" :key="col.key">Proveedor</th>
                            <th v-if="col.visible && col.key === 'state_type'" :key="col.key">Estado</th>
                            <th v-if="col.visible && col.key === 'payment_state'" :key="col.key">Estado de pago</th>
                            <th v-if="col.visible && col.key === 'number'" :key="col.key">Número</th>
                            <th v-if="col.visible && col.key === 'products'" :key="col.key">Productos</th>
                            <th v-if="col.visible && col.key === 'warehouse'" :key="col.key">Almacén</th>
                            <th v-if="col.visible && col.key === 'payments'" :key="col.key">Pagos</th>
                            <th v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">Moneda</th>
                            <th v-if="col.visible && col.key === 'guides'" :key="col.key" class="text-end">Guía</th>
                            <th v-if="col.visible && col.key === 'purchase_order'" :key="col.key" class="text-end">Orden de compra</th>
                            <th v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end">T.Gratuita</th>
                            <th v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">T.Inafecta</th>
                            <th v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">T.Exonerado</th>
                            <th v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">T.Gravado</th>
                            <th v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">T.Igv</th>
                            <th v-if="col.visible && col.key === 'total_perception'" :key="col.key">Percepcion</th>
                            <th v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">Total</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">Acciones</th>
                        </template>
                    </tr>
                    <tr slot-scope="{ index, row }" :class="{'anulate_color': row.state_type_id === '11'}">
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">{{ row.date_of_issue | toDate }}</td>
                            <td v-if="col.visible && col.key === 'date_of_due'" :key="col.key" class="text-center" :class="{ 'text-danger': row.state_type_payment_description != 'Pagado' && isDateWarning(row.date_of_due) }">{{ row.date_of_due | toDate }}</td>
                            <td v-if="col.visible && col.key === 'supplier'" :key="col.key">{{ row.supplier_name }}<br /><small v-text="row.supplier_number"></small></td>
                            <td v-if="col.visible && col.key === 'state_type'" :key="col.key">{{ row.state_type_description }}</td>
                            <td v-if="col.visible && col.key === 'payment_state'" :key="col.key" :class="row.state_type_payment_description == 'Pagado' ? 'text-success' : 'text-warning'">{{ row.state_type_payment_description }}</td>
                            <td v-if="col.visible && col.key === 'number'" :key="col.key">{{ row.number }}<br /><small v-text="row.document_type_description"></small><br /></td>
                            <td v-if="col.visible && col.key === 'products'" :key="col.key">
                                <el-popover placement="right" width="400" trigger="click">
                                    <el-table :data="row.items">
                                        <el-table-column width="80" property="key" label="#"></el-table-column>
                                        <el-table-column width="220" label="Nombre">
                                            <template slot-scope="scope">
                                                <template v-if="scope.row.name_product_pdf"><span v-html="scope.row.name_product_pdf"></span></template>
                                                <template v-else>{{ scope.row.description }}</template>
                                            </template>
                                        </el-table-column>
                                        <el-table-column width="90" property="quantity" label="Cantidad"></el-table-column>
                                    </el-table>
                                    <el-button class="second-buton" slot="reference"><i class="fa fa-eye"></i></el-button>
                                </el-popover>
                            </td>
                            <td v-if="col.visible && col.key === 'warehouse'" :key="col.key">
                                <template v-if="row.warehouses && row.warehouses.length">
                                    <el-tag v-for="warehouse in row.warehouses" :key="warehouse.id" size="small" type="success" class="me-1 mb-1">{{ warehouse.description }}</el-tag>
                                </template>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td v-if="col.visible && col.key === 'payments'" :key="col.key" class="text-end">
                                <button v-if="row.state_type_id != '11'" type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-info m-1__2" @click.prevent="clickPurchasePayment(row.id)">Pagos</button>
                            </td>
                            <td v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">{{ row.currency_type_id }}</td>
                            <td v-if="col.visible && col.key === 'guides'" :key="col.key" class="text-center">
                                <span v-for="(item, i) in row.guides" :key="i">{{ item.number }} <br /></span>
                            </td>
                            <td v-if="col.visible && col.key === 'purchase_order'" :key="col.key" class="text-end">
                                <span v-if="row.purchase_order">{{ row.purchase_order.prefix }}-{{ row.purchase_order.id }}</span>
                            </td>
                            <td v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_free) }}</td>
                            <td v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_unaffected) }}</td>
                            <td v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exonerated) }}</td>
                            <td v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_taxed) }}</td>
                            <td v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_igv) }}</td>
                            <td v-if="col.visible && col.key === 'total_perception'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ row.total_perception ? formatDecimal(row.total_perception) : formatDecimal(0) }}</td>
                            <td v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total) }}</td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                                <el-dropdown trigger="click" @command="handleCommand($event, row)">
                                    <el-button class="btn-dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                    </el-button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item v-if="permissions.edit_purchase && row.state_type_id != '11'" command="edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                                Editar
                                            </el-dropdown-item>
                                            <el-dropdown-item command="options">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                                Opciones
                                            </el-dropdown-item>
                                            <el-dropdown-item :disabled="disableGuideBtn" command="guide">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path></svg>
                                                Guía
                                            </el-dropdown-item>
                                            <el-dropdown-item v-if="permissions.annular_purchase && row.state_type_id != '11' || permissions.delete_purchase && row.state_type_id == '11'" divided></el-dropdown-item>
                                            <el-dropdown-item v-if="permissions.annular_purchase && row.state_type_id != '11'" command="anulate" class="option-delete text-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M10 10l4 4m0 -4l-4 4"></path></svg>
                                                Anular
                                            </el-dropdown-item>
                                            <el-dropdown-item v-if="permissions.delete_purchase && row.state_type_id == '11'" command="delete" class="option-delete text-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                Eliminar
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </td>
                        </template>
                    </tr>
                </data-table>
            </div>

            <!-- <documents-voided :showDialog.sync="showDialogVoided"
                            :recordId="recordId"></documents-voided>

            <document-options :showDialog.sync="showDialogOptions"
                              :recordId="recordId"
                              :showClose="true"></document-options> -->

            <purchase-import
                :showDialog.sync="showImportDialog"
            ></purchase-import>
        </div>

        <tenant-guides-modal
            :show.sync="showModalGuide"
            :id="recordId"
            :type="'purchases'"
        ></tenant-guides-modal>
        <purchase-payments
            :showDialog.sync="showDialogPurchasePayments"
            :purchaseId="recordId"
            :external="true"
        ></purchase-payments>

        <purchase-options
            :showDialog.sync="showDialogOptions"
            :recordId="recordId"
            :showClose="true"
        ></purchase-options>
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";

// import DocumentsVoided from './partials/voided.vue'
// import DocumentOptions from './partials/options.vue'
import DataTable from "../../../components/DataTable.vue";
import { deletable } from "../../../mixins/deletable";
import PurchaseImport from "./import.vue";
import PurchasePayments from "@viewsModulePurchase/purchase_payments/payments.vue";
import PurchaseOptions from "./partials/options.vue";

export default {
    mixins: [deletable],
    // components: {DocumentsVoided, DocumentOptions, DataTable},
    components: {
        DataTable,
        PurchaseImport,
        PurchasePayments,
        PurchaseOptions
    },
    props: ["typeUser", "configuration"],
    data() {
        return {
            disableGuideBtn: true,
            showModalGuide: false,
            showDialogVoided: false,
            resource: "purchases",
            recordId: null,
            showDialogOptions: false,
            showDialogPurchasePayments: false,
            showImportDialog: false,
            columns: {
                date_of_issue:    { title: "F. Emisión",     visible: true,  order: 0  },
                date_of_due:      { title: "F. Vencimiento", visible: false, order: 1  },
                supplier:         { title: "Proveedor",      visible: true,  order: 2  },
                state_type:       { title: "Estado",         visible: true,  order: 3  },
                payment_state:    { title: "Estado de pago", visible: true,  order: 4  },
                number:           { title: "Número",         visible: true,  order: 5  },
                products:         { title: "Productos",      visible: true,  order: 6  },
                warehouse:        { title: "Almacén",        visible: true,  order: 7  },
                payments:         { title: "Pagos",          visible: true,  order: 8  },
                currency_type:    { title: "Moneda",         visible: true,  order: 9  },
                guides:           { title: "Guias",          visible: false, order: 10 },
                purchase_order:   { title: "Orden de Compra",visible: false, order: 11 },
                total_free:       { title: "T.Gratuita",     visible: false, order: 12 },
                total_unaffected: { title: "T.Inafecta",     visible: false, order: 13 },
                total_exonerated: { title: "T.Exonerado",    visible: false, order: 14 },
                total_taxed:      { title: "T.Gravado",      visible: false, order: 15 },
                total_igv:        { title: "T.Igv",          visible: false, order: 16 },
                total_perception: { title: "Percepcion",     visible: false, order: 17 },
                total:            { title: "Total",          visible: true,  order: 18 },
                actions:          { title: "Acciones",       visible: true,  order: 19 },
            },
            permissions: {},
            decimal_quantity: 2
        };
    },
    computed: {
        ...mapState(["document_types_guide", "warehouses"]),
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order);
        },
    },
    async created() {
        this.loadColumnVisibility();
        this.$store.commit("setConfiguration", this.configuration);
        this.$store.commit("setTypeUser", this.typeUser);
        await this.$http.get(`/${this.resource}/tables`).then(response => {
            this.permissions = response.data.permissions;
        });
        this.getDocumentTypes();
        this.loadDecimalQuantity();
    },
    methods: {
        loadDecimalQuantity() {
            // Obtener la configuracion general para los decimales
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
        saveColumnVisibility() {
            const columns = {};
            Object.keys(this.columns).forEach(key => {
                columns[key] = { title: this.columns[key].title, visible: this.columns[key].visible, order: this.columns[key].order };
            });
            this.$http.post('/column-visibility/purchases_index', { columns }).catch(() => {});
        },
        loadColumnVisibility() {
            this.$http.get('/column-visibility/purchases_index').then(response => {
                if (response.data.success && response.data.data) {
                    const data = response.data.data;
                    Object.keys(data).forEach(key => {
                        if (this.columns[key] !== undefined) {
                            this.columns[key].visible = data[key].visible;
                            if (data[key].order !== undefined) {
                                this.columns[key].order = data[key].order;
                            }
                        }
                    });
                }
            }).catch(() => {});
        },
        getItemDescription(scope) {
            return scope.row.name_product_pdf
                ? scope.row.name_product_pdf
                : scope.row.description;
        },
        ...mapActions(["loadConfiguration"]),
        clickPurchasePayment(recordId) {
            this.recordId = recordId;
            this.showDialogPurchasePayments = true;
        },
        clickVoided(recordId = null) {
            this.recordId = recordId;
            this.showDialogVoided = true;
        },
        clickDownload(download) {
            window.open(download, "_blank");
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        handleCommand(command, row) {
            switch (command) {
                case "edit":
                    if (row.state_type_id != "11")
                        window.location.href = `/${this.resource}/edit/${row.id}`;
                    break;
                case "anulate":
                    if (row.state_type_id != "11") this.clickAnulate(row.id);
                    break;
                case "delete":
                    if (row.state_type_id == "11") this.clickDelete(row.id);
                    break;
                case "options":
                    this.clickOptions(row.id);
                    break;
                case "guide":
                    if (!this.disableGuideBtn) this.clickGuide(row.id);
                    break;
                case "payments":
                    if (row.state_type_id != "11") this.clickPurchasePayment(row.id);
                    break;
            }
        },
        clickGuide(recordId = null) {
            this.recordId = recordId;
            this.showModalGuide = true;
        },
        clickAnulate(id) {
            this.anular(`/${this.resource}/anular/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickDelete(id) {
            this.delete(`/${this.resource}/delete/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickImport() {
            this.showImportDialog = true;
        },
        getDocumentTypes() {
            this.$http
                .post("/dispatches/getDocumentType")
                .then(result => {
                    this.$store.commit("setDocumentTypesGuide", result.data);
                })
                .then(() => {
                    this.disableGuideBtn = !this.disableGuideBtn;
                });
        },
        isDateWarning(date_due) {
            let today = Date.now();
            return moment(date_due).isBefore(today);
        }
    }
};
</script>
