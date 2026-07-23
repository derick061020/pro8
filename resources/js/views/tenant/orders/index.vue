<template>
    <div class="orders" v-loading="loading_submit">
        <div class="page-header pe-0">
            <h2>
                <a href="/orders">
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
                        class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 17h-11v-14h-2" />
                        <path d="M6 5l14 1l-1 7h-13" />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Pedidos</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button
                    class="btn btn-custom btn-sm mt-2 me-4"
                    @click="showStatusModal = true"
                    title="Gestionar estados"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                    </svg>
                    Gestionar estados
                </button>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <data-table :resource="resource" :status-options="orderOptions">
                    <tr slot="heading" width="100%">
                        <!-- <th>#</th> -->
                        <th># Pedido</th>
                        <th>Cliente</th>
                        <th class="text-center">Detalle Productos</th>
                        <th class="text-end">Total</th>
                        <th>Fecha Emision</th>
                        <th>Medio Pago</th>
                        <th>Estado de pago</th>
                        <th>Estado de envío</th>
                        <th>Estado de pedido</th>
                        <th class="text-center">Documento</th>
                        <th class="text-end">Opciones</th>
                    </tr>
                    <tr></tr>
                    <tr slot-scope="{ index, row }">
                        <!-- <td>{{ index }}</td> -->
                        <td>
                            <a href="#" @click.prevent="openDetail(row)" class="text-primary">
                                {{ row.order_id }}
                            </a>
                        </td>
                        <td>{{ row.customer }}</td>
                        <td class="text-center">
                            <template>
                                <el-popover
                                    placement="right"
                                    width="540"
                                    trigger="click"
                                >
                                    <el-table
                                        style="width: 100%"
                                        :data="row.items"
                                    >
                                        <!--
                      En la edicion del item, el nombre es descripcion, por ello, aqui tambien debe ser descripcion
  <el-table-column width="150" property="name" label="Nombre"></el-table-column>
  @todo homologar campos en editar/crear item.
  -->
                                        <el-table-column
                                            width="150"
                                            property="description"
                                            label="Nombre"
                                        ></el-table-column>
                                        <el-table-column
                                            width="90"
                                            property="cantidad"
                                            label="Cant."
                                        ></el-table-column>
                                        <el-table-column
                                            width="90"
                                            label="Precio"
                                        >
                                            <template slot-scope="scope">
                                                <span
                                                    >{{
                                                        scope.row
                                                            .currency_type_id ===
                                                        "USD"
                                                            ? "$"
                                                            : "S/"
                                                    }}
                                                    {{
                                                        Number(
                                                            scope.row
                                                                .sale_unit_price
                                                        ).toFixed(2)
                                                    }}</span
                                                >
                                            </template>
                                        </el-table-column>
                                        <el-table-column
                                            width="90"
                                            property="exchange_rate_sale"
                                            label="T/C"
                                        ></el-table-column>
                                        <el-table-column
                                            width="90"
                                            label="Subtotal"
                                        >
                                            <template slot-scope="scope">
                                                <span
                                                    >S/
                                                    {{
                                                        subtotal(scope.row)
                                                    }}</span
                                                >
                                            </template>
                                        </el-table-column>
                                    </el-table>
                                    <table
                                        class="el-table--small el-table--fit el-table"
                                    >
                                        <thead class="has-gutter">
                                            <th colspan="2" class="text-center">
                                                Contacto
                                            </th>
                                        </thead>
                                        <tbody>
                                            <tr class="el-table tr">
                                                <td class="el-table--small td">
                                                    TELÉFONO:
                                                    {{ row.customer_telefono }}
                                                </td>
                                            </tr>
                                            <tr class="el-table tr">
                                                <td class="el-table--small td">
                                                    DIRECCIÓN:
                                                    {{ row.customer_direccion }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <el-button
                                        slot="reference"
                                        icon="el-icon-zoom-in"
                                    ></el-button>
                                </el-popover>
                            </template>
                        </td>
                        <td class="text-end">S/ {{ row.total }}</td>
                        <td>{{ formatDate(row.created_at) }}</td>
                        <td>{{ row.reference_payment }}</td>
                        <td>
                            <div
                                class="status-select-wrap"
                                :class="{ 'has-color': statusColor(row.payment_status_order_id) }"
                                :style="selectVars(row.payment_status_order_id)"
                            >
                                <span
                                    v-if="statusColor(row.payment_status_order_id)"
                                    class="status-dot status-dot--inside"
                                    :style="{ background: statusColor(row.payment_status_order_id) }"
                                ></span>
                                <el-select
                                    v-model="row.payment_status_order_id"
                                    placeholder="Estado de pago"
                                    :value="row.payment_status_order_id"
                                    @change="updateStatus(row, 'payment_status_order_id')"
                                >
                                    <el-option
                                        v-for="item in paymentOptions"
                                        :key="item.id"
                                        :label="item.description"
                                        :value="item.id"
                                    >
                                        <span class="status-dot" :style="{ background: item.color || '#909399' }"></span>
                                        <span>{{ item.description }}</span>
                                    </el-option>
                                </el-select>
                            </div>
                        </td>
                        <td>
                            <div
                                class="status-select-wrap"
                                :class="{ 'has-color': statusColor(row.shipping_status_order_id) }"
                                :style="selectVars(row.shipping_status_order_id)"
                            >
                                <span
                                    v-if="statusColor(row.shipping_status_order_id)"
                                    class="status-dot status-dot--inside"
                                    :style="{ background: statusColor(row.shipping_status_order_id) }"
                                ></span>
                                <el-select
                                    v-model="row.shipping_status_order_id"
                                    placeholder="Estado de envío"
                                    :value="row.shipping_status_order_id"
                                    @change="updateStatus(row, 'shipping_status_order_id')"
                                >
                                    <el-option
                                        v-for="item in shippingOptions"
                                        :key="item.id"
                                        :label="item.description"
                                        :value="item.id"
                                    >
                                        <span class="status-dot" :style="{ background: item.color || '#909399' }"></span>
                                        <span>{{ item.description }}</span>
                                    </el-option>
                                </el-select>
                            </div>
                        </td>
                        <td>
                            <div
                                class="status-select-wrap"
                                :class="{ 'has-color': statusColor(row.status_order_id) }"
                                :style="selectVars(row.status_order_id)"
                            >
                                <span
                                    v-if="statusColor(row.status_order_id)"
                                    class="status-dot status-dot--inside"
                                    :style="{ background: statusColor(row.status_order_id) }"
                                ></span>
                                <el-select
                                    v-model="row.status_order_id"
                                    placeholder="Estado de pedido"
                                    :value="row.status_order_id"
                                    @change="updateStatus(row, 'status_order_id')"
                                >
                                    <el-option
                                        v-for="item in orderOptions"
                                        :key="item.id"
                                        :label="item.description"
                                        :value="item.id"
                                    >
                                        <span class="status-dot" :style="{ background: item.color || '#909399' }"></span>
                                        <span>{{ item.description }}</span>
                                    </el-option>
                                </el-select>
                            </div>
                        </td>
                        <td class="text-center">
                            <template v-if="row.document_type_id == '80'">
                                {{ row.sale_note_number_full }}
                            </template>
                            <template v-else>
                                {{ row.number_document }}
                            </template>
                        </td>
                        <td class="text-end">
                            <template v-if="row.document_type_id == '80'">
                                <el-button
                                    v-if="row.sale_note_id"
                                    class="submit"
                                    type="success"
                                    icon="el-icon-tickets"
                                    @click.prevent="
                                        clickOptions(row.sale_note_id)
                                    "
                                ></el-button>
                            </template>
                            <template v-else>
                                <el-button
                                    v-if="row.document_external_id"
                                    class="submit"
                                    type="success"
                                    icon="el-icon-tickets"
                                    @click.prevent="
                                        clickDownload(row.document_external_id)
                                    "
                                ></el-button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>
        </div>

        <el-dialog
            title="Stock en almacén"
            width="40%"
            :visible="showDialog"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            append-to-body
            :show-close="false"
        >
            <div class="form-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 table-responsive">
                        <table width="100%" class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Almacén</th>
                                </tr>
                            </thead>
                            <tbody
                                v-for="(rowProduct,
                                indexProduct) in totalProduct"
                                :key="indexProduct"
                                width="100%"
                            >
                                <tr>
                                    <td>
                                        {{ record.items[indexProduct].name }}
                                    </td>
                                    <td>
                                        <el-select
                                            v-model="form[rowProduct]"
                                            placeholder="Almacenes"
                                        >
                                            <el-option
                                                v-if="
                                                    rowProduct === item.item_id
                                                "
                                                v-for="item in warehouses"
                                                :key="item.id"
                                                :label="
                                                    item.warehouse +
                                                        ' - ' +
                                                        'Stock -> ' +
                                                        Math.trunc(item.stock)
                                                "
                                                :value="item.id"
                                                :disabled="
                                                    optionDisable(
                                                        item.item_id,
                                                        item.stock
                                                    )
                                                "
                                            ></el-option>
                                        </el-select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end pt-2">
                <el-button class="second-buton" @click="close"
                    >Cerrar</el-button
                >
                <el-button type="primary" @click="save">Guardar</el-button>
            </div>
        </el-dialog>

        <options-form
            :showDialog.sync="showDialogOptions"
            :recordId="documentNewId"
            :statusDocument="statusDocument"
            :resource="resource_options"
        ></options-form>

        <document-form
            :order_id="order_id"
            :user="user"
            :document_types="document_types"
            ref="document_form"
        >
        </document-form>

        <sale-note-form
            :showDialog.sync="showDialogSaleNote"
            :orderId="order_id"
            :dataSaleNote="dataSaleNote"
        >
        </sale-note-form>
        <status-order-modal
            :showDialog.sync="showStatusModal"
            :options="options"
        ></status-order-modal>
        <order-detail
            :visible.sync="showOrderModal"
            :record="selectedOrder">
        </order-detail>
    </div>
</template>
<style>
/* Estado con color: se pinta el propio select (borde, fondo, texto) con el punto dentro */
.status-select-wrap {
    position: relative;
    width: 100%;
}
.status-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
/* Punto dentro del select, sobre el input, a la izquierda del texto */
.status-dot--inside {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    pointer-events: none;
}
/* Pinta el input del select con el color del estado. !important para ganarle al skin del tenant */
.status-select-wrap.has-color .el-input__inner {
    border: none !important;
    background-color: color-mix(in srgb, var(--st-color) 12%, #fff) !important;
    color: color-mix(in srgb, var(--st-color) 80%, #000) !important;
    font-weight: 600;
    padding-left: 30px !important; /* espacio para el punto interno */
}
.status-select-wrap.has-color .el-input__inner::placeholder {
    color: color-mix(in srgb, var(--st-color) 62%, #000) !important;
}
.status-select-wrap.has-color .el-select__caret {
    color: var(--st-color) !important;
}
/* Punto dentro de las opciones del desplegable (se teletransporta al body) */
.el-select-dropdown__item .status-dot {
    margin-right: 8px;
    vertical-align: middle;
}

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
import DataTable from "../../../components/DataTable.vue";
import queryString from "query-string";
import OptionsForm from "../pos/partials/options.vue";
import DocumentForm from "./partials/document_form.vue";
import SaleNoteForm from "./partials/sale_note_form.vue";
import StatusOrderModal from "./partials/status_order_modal.vue";
import OrderDetail from "./partials/OrderDetail.vue";

export default {
    props: ["user"],

    components: { DataTable, OptionsForm, DocumentForm, SaleNoteForm, StatusOrderModal, OrderDetail },
    data() {
        return {
            showStatusModal: false,
            showDialog: false,
            showImportDialog: false,
            showImageDetail: false,
            resource: "orders",
            recordId: null,
            options: [],
            warehouses: [],
            estableciment_id: "",
            totalProduct: [], // items_id
            showDialog: false,
            form: [],
            record: "", // record orders
            stocks: "",
            showDialogOptions: false,
            documentNewId: null,
            statusDocument: {},
            resource_options: null,
            loading_submit: false,
            document_types: [],
            order_id: null,
            dataSaleNote: {},
            showDialogSaleNote: false,
            statusFilter: null,
            statusField: 'status_order_id',
            showOrderModal: false,
            selectedOrder: null,
        };
    },
    async created() {
        this.$http.get(`/statusOrder/records`).then(response => {
            this.options = response.data;
        });
        this.events();
        this.$eventHub.$on('statusesUpdated', () => {
            this.loadStatuses()
        })
    },
    computed: {
        paymentOptions() {
            return this.options.filter(o => o.is_payment_status);
        },
        orderOptions() {
            return this.options.filter(o => o.is_order_status);
        },
        shippingOptions() {
            return this.options.filter(o => o.is_shipping_status);
        }
    },
    methods: {
        openDetail(row) {
            console.log(row)
            console.log(row.purchase)
            this.selectedOrder = row
            this.showOrderModal = true
        },
        loadStatuses() {
            this.$http.get(`/statusOrder/records`).then(response => {
                this.options = response.data;
            });
        },
        // Devuelve el color hex del estado con ese id (o '' si no tiene)
        statusColor(id) {
            if (!id) return '';
            const s = this.options.find(o => o.id === id);
            return s && s.color ? s.color : '';
        },
        // Variable CSS con el color del estado, para pintar el select (borde, fondo, texto)
        selectVars(id) {
            const c = this.statusColor(id);
            return c ? { '--st-color': c } : {};
        },
        formatDate(date) {
            if (!date) return null;
            const parsedDate = moment(date);
            return parsedDate.isValid()
                ? parsedDate.format("DD-MM-YYYY h:mmA")
                : null;
        },
        clickOptions(recordId) {
            this.documentNewId = recordId;
            this.statusDocument.send = "";
            this.resource_options = "sale-notes";
            this.showDialogOptions = true;
        },
        async clickDownload(row) {
            await this.$http
                .get(`/documents/search/externalId/${row}`)
                .then(response => {
                    this.documentNewId = response.data.id;
                });
            this.statusDocument.send = "";
            this.resource_options = "documents";
            this.showDialogOptions = true;
        },
        subtotal(item) {
            var subtotal;
            if (item.currency_type_id === "USD") {
                subtotal = Number(
                    item.cantidad *
                        item.exchange_rate_sale *
                        parseFloat(item.sale_unit_price)
                ).toFixed(2);
                if (isNaN(subtotal)) {
                    return "-";
                } else {
                    return subtotal;
                }
            } else {
                return parseFloat(item.cantidad * item.sale_unit_price);
            }
        },
        optionDisable(product, stock) {
            for (var i = 0; i < this.record.items.length; i++) {
                if (product === this.record.items[i].id) {
                    return stock >= this.record.items[i].cantidad
                        ? false
                        : true;
                }
            }
        },
        openDialogSaleNote(sale_note) {
            this.dataSaleNote = sale_note;
            this.showDialogSaleNote = true;
        },
        async updateStatus(record, field = 'status_order_id') {
            this.record = record;
            this.statusField = field;

            // Obtener el objeto de estado completo desde las opciones cargadas
            const selectedStatus = this.options.find(o => o.id === record[field])

            if (selectedStatus && selectedStatus.action_generate_document) {
                this.order_id = record.id;

                if (record.purchase.codigo_tipo_documento == "80") {
                    if (record.has_sale_note)
                        return this.$message.success(
                            "Ya existe una nota de venta"
                        );
                    this.openDialogSaleNote(record.purchase);
                } else {
                    if (record.document_external_id) {
                        return this.$message.success(
                            "Ya existe un comprobante."
                        );
                    }
                    this.$refs.document_form.sendPreview(record.purchase);
                }
            } else if (selectedStatus && selectedStatus.action_discount_stock) {
                // Si la orden ya tiene el flag de stock descontado, no continuar
                if (record.stock_discounted) {
                    this.$message.success('El stock ya fue descontado para esta orden');
                    return;
                }
                this.totalProduct = await this.products(record);
                await this.$http
                    .post(`/orders/warehouse`, { item_id: this.totalProduct })
                    .then(response => {
                        this.warehouses = response.data.data;
                        this.showDialog = true;
                    });
                return;
            } else {
                this.saveUpdateStatus();
            }
        },
        saveUpdateStatus() {
            this.$http
                .post(`/statusOrder/update`, { record: this.record, field: this.statusField })
                .then(response => {
                    this.$message.success(response.data.message);
                });
        },
        async save() {
            var save = [];

            for (var i = 0; i < this.record.items.length; i++) {
                if (this.totalProduct[i] === this.record.items[i].id) {
                    save.push({
                        id: this.form[this.totalProduct[i]],
                        cantidad: this.record.items[i].cantidad
                    });
                }
            }

            // Marcar en el front que el stock será descontado
            this.record.stock_discounted = true;

            await this.$http
                .post(`/statusOrder/update`, {
                    record: this.record,
                    discount: save,
                    field: this.statusField
                })
                .then(response => {
                    this.$message.success(response.data.message);
                    this.close();
                });
        },
        close() {
            this.form = [];
            this.showDialog = false;
            this.recoard = "";
        },
        products(products) {
            let listProduct = [];

            for (var i = 0; i <= products.items.length - 1; i++) {
                listProduct.push(products.items[i].id);
            }
            return listProduct;
        },
        async events() {
            await this.$eventHub.$on("cancelSale", () => {
                this.showDialogOptions = false;
            });
        },

        getHeaderConfig() {
            let token = this.user.api_token;
            let httpConfig = {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                }
            };
            return httpConfig;
        }
    }
};
</script>
