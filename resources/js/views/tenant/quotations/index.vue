<template>
    <div class="quotations">
        <div class="page-header pe-0">
            <h2>
                <a href="/quotations">
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
                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"
                        />
                        <path
                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"
                        />
                        <path d="M16 5l3 3" />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Cotizaciones</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    :href="`/${resource}/create`"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="col in orderedColumns" :key="col.key">
                            <el-checkbox v-model="columns[col.key].visible" @change="saveColumnVisibilityQuotations">
                                {{ col.title }}
                            </el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table :resource="resource" :state-types="state_types">
                    <tr slot="heading">
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">Fecha Emisión</th>
                            <th v-if="col.visible && col.key === 'delivery_date'" :key="col.key" class="text-center">T. Entrega</th>
                            <th v-if="col.visible && col.key === 'registered_by'" :key="col.key">Registrado por</th>
                            <th v-if="col.visible && col.key === 'seller'" :key="col.key">Vendedor</th>
                            <th v-if="col.visible && col.key === 'customer'" :key="col.key">Cliente</th>
                            <th v-if="col.visible && col.key === 'state_type'" :key="col.key">Estado</th>
                            <th v-if="col.visible && col.key === 'identifier'" :key="col.key">Cotización</th>
                            <th v-if="col.visible && col.key === 'documents'" :key="col.key">Comprobantes</th>
                            <th v-if="col.visible && col.key === 'sale_notes'" :key="col.key">Notas de venta</th>
                            <th v-if="col.visible && col.key === 'order_note'" :key="col.key">Pedido</th>
                            <th v-if="col.visible && col.key === 'sale_opportunity'" :key="col.key">Oportunidad Venta</th>
                            <th v-if="col.visible && col.key === 'referential_information'" :key="col.key">Inf.Referencial</th>
                            <th v-if="col.visible && col.key === 'contract'" :key="col.key">Contrato</th>
                            <th v-if="col.visible && col.key === 'exchange_rate_sale'" :key="col.key">T.C.</th>
                            <th v-if="col.visible && col.key === 'currency_type_id'" :key="col.key" class="text-center">Moneda</th>
                            <th v-if="col.visible && col.key === 'payments'" :key="col.key" class="text-end">Pagos</th>
                            <th v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end">T.Exportación</th>
                            <th v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end">T.Gratuito</th>
                            <th v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">T.Inafecta</th>
                            <th v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">T.Exonerado</th>
                            <th v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">T.Gravado</th>
                            <th v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">T.Igv</th>
                            <th v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">Total</th>
                            <th v-if="col.visible && col.key === 'pdf'" :key="col.key" class="text-center">PDF</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end"></th>
                        </template>
                    </tr>
                    <tr
                        slot-scope="{ index, row }"
                        :class="{ anulate_color: row.state_type_id == '11' }"
                    >
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">{{ row.date_of_issue | toDate }}</td>
                            <td v-if="col.visible && col.key === 'delivery_date'" :key="col.key" class="text-center">{{ row.delivery_date }}</td>
                            <td v-if="col.visible && col.key === 'registered_by'" :key="col.key">{{ row.user_name }}</td>
                            <td v-if="col.visible && col.key === 'seller'" :key="col.key">{{ row.seller_name }}</td>
                            <td v-if="col.visible && col.key === 'customer'" :key="col.key">{{ row.customer_name }}<br /><small v-text="row.customer_number"></small></td>
                            <td v-if="col.visible && col.key === 'state_type'" :key="col.key">
                                <template v-if="row.state_type_id == '11'">{{ row.state_type_description }}</template>
                                <template v-else>
                                    <el-select v-model="row.state_type_id" @change="changeStateType(row)" style="width:120px !important">
                                        <el-option v-for="option in state_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'identifier'" :key="col.key">{{ row.identifier }}</td>
                            <td v-if="col.visible && col.key === 'documents'" :key="col.key">
                                <template v-for="(document, i) in row.documents">
                                    <template v-if="document.is_voided_or_rejected">
                                        <label :key="i" class="d-block text-danger">{{ document.number_full }}</label>
                                    </template>
                                    <template v-else>
                                        <label :key="i" v-text="document.number_full" class="d-block"></label>
                                    </template>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'sale_notes'" :key="col.key">
                                <template v-for="(sale_note, i) in row.sale_notes">
                                    <label :key="i"v-text="sale_note.number_full" class="d-block"></label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'order_note'" :key="col.key">
                                <template v-if="row.order_note !== undefined && row.order_note.full_number !== undefined">
                                    <label class="d-block">{{ row.order_note.full_number }}</label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'sale_opportunity'" :key="col.key">
                                <el-popover placement="right" v-if="row.sale_opportunity" width="400" trigger="click">
                                    <div class="col-md-12 mt-4">
                                        <table>
                                            <tr><td><strong>O. Venta: </strong></td><td><strong>{{ row.sale_opportunity_number_full }}</strong></td></tr>
                                            <tr><td><strong>Detalle: </strong></td><td><strong>{{ row.sale_opportunity.detail }}</strong></td></tr>
                                            <tr class="mt-4 mb-4"><td><strong>F. Emisión:</strong></td><td><strong>{{ row.date_of_issue | toDate }}</strong></td></tr>
                                        </table>
                                        <div class="table-responsive mt-4">
                                            <table class="table">
                                                <thead><tr><th>#</th><th>Descripción</th><th>Cantidad</th><th>Total</th></tr></thead>
                                                <tbody>
                                                    <tr v-for="(row, index) in row.sale_opportunity.items" :key="index">
                                                        <td>{{ index + 1 }}</td><td>{{ row.item.description }}</td><td>{{ row.quantity }}</td><td>{{ row.total }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <el-button slot="reference"><i class="fa fa-eye"></i></el-button>
                                </el-popover>
                            </td>
                            <td v-if="col.visible && col.key === 'referential_information'" :key="col.key">{{ row.referential_information }}</td>
                            <td v-if="col.visible && col.key === 'contract'" :key="col.key">{{ row.contract_number_full }}</td>
                            <td v-if="col.visible && col.key === 'exchange_rate_sale'" :key="col.key">{{ row.exchange_rate_sale }}</td>
                            <td v-if="col.visible && col.key === 'currency_type_id'" :key="col.key" class="text-center">{{ row.currency_type_id }}</td>
                            <td v-if="col.visible && col.key === 'payments'" :key="col.key" class="text-end">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickPayment(row.id)">Pagos</button>
                            </td>
                            <td v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exportation) }}</td>
                            <td v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_free) }}</td>
                            <td v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_unaffected) }}</td>
                            <td v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exonerated) }}</td>
                            <td v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_taxed) }}</td>
                            <td v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_igv) }}</td>
                            <td v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total) }}</td>
                            <td v-if="col.visible && col.key === 'pdf'" :key="col.key" class="text-end">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickOptionsPdf(row.id)">PDF</button>
                            </td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                            <el-dropdown trigger="click" placement="bottom-end">
                                <el-button class="btn-dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                    <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item
                                      v-if="row.btn_options"
                                      @click.native="clickGenerateDocument(row.id)"
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
                                      v-if="row.btn_options"
                                      @click.native="clickOptions(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 4v17l2 -2l2 2l2 -2l2 2l2 -2l2 2l2 -2v-17z"/>
                                        <path d="M14 8h-4"/>
                                        <path d="M14 12h-4"/>
                                        <path d="M14 16h-4"/>
                                      </svg>
                                      Generar nota de venta
                                    </el-dropdown-item>

                                    <el-dropdown-item
                                      @click.native="clickSendQuotation(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-arrow-right me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15h6" /><path d="M12.5 17.5l2.5 -2.5l-2.5 -2.5" /></svg>
                                      Enviar cotización
                                    </el-dropdown-item>

                                    <el-dropdown-item
                                      v-if="canMakeOrderNote(row)"
                                      @click.native="makeOrder(row.id)"
                                    >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                      Generar Pedido
                                    </el-dropdown-item>

                                    <el-dropdown-item divided />

                                    <el-dropdown-item
                                      @click.native="goToDispatch(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck me-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                                      </svg>
                                      Guía
                                    </el-dropdown-item>

                                    <template
                                      v-if="row.btn_generate_cnt && row.state_type_id != '11'"
                                    >
                                      <el-dropdown-item
                                        @click.native="goToContract(row.id)"
                                      >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-contract me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 21h-2a3 3 0 0 1 -3 -3v-1h5.5" /><path d="M17 8.5v-3.5a2 2 0 1 1 2 2h-2" /><path d="M19 3h-11a3 3 0 0 0 -3 3v11" /><path d="M9 7h4" /><path d="M9 11h4" /><path d="M18.42 12.61a2.1 2.1 0 0 1 2.97 2.97l-6.39 6.42h-3v-3z" /></svg>
                                        Generar contrato
                                      </el-dropdown-item>
                                    </template>

                                    <template v-else>
                                      <el-dropdown-item
                                        @click.native="clickPrintContract(row.external_id_contract)"
                                      >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-check me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 15l2 2l4 -4" /></svg>
                                        Ver contrato
                                      </el-dropdown-item>
                                    </template>

                                    <el-dropdown-item divided />

                                    <el-dropdown-item
                                      v-if="row.documents.length == 0 && row.state_type_id != '11'"
                                      @click.native="goToEdit(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                      Editar
                                    </el-dropdown-item>

                                    <el-dropdown-item
                                      @click.native="duplicate(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                      Duplicar
                                    </el-dropdown-item>

                                    <el-dropdown-item v-if="row.documents.length == 0 && row.state_type_id != '11'" divided />

                                    <el-dropdown-item
                                      v-if="row.documents.length == 0 && row.state_type_id != '11'"
                                      @click.native="clickAnulate(row.id)"
                                      class="text-danger option-delete"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                                      Anular
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </td>
                        </template>
                    </tr>
                </data-table>
            </div>

            <quotation-options
                :showDialog.sync="showDialogOptions"
                :recordId="recordId"
                :showGenerate="true"
                :showClose="true"
            ></quotation-options>

            <quotation-options-pdf
                :showDialog.sync="showDialogOptionsPdf"
                :recordId="recordId"
                :showClose="true"
            ></quotation-options-pdf>

            <quotation-payments
                :showDialog.sync="showDialogPayments"
                :recordId="recordId"
            ></quotation-payments>

            <send-email-document
                :config="config"
                :showDialog.sync="showDialogSendEmailDocument"
                :recordId="recordId"
                :resource="resource"
            ></send-email-document>
        </div>
    </div>
</template>
<style scoped>
.anulate_color {
    color: red;
}
</style>
<script>
import QuotationOptions from "./partials/options.vue";
import QuotationOptionsPdf from "./partials/options_pdf.vue";
import DataTable from "../../../components/DataTableQuotation.vue";
import { deletable } from "../../../mixins/deletable";
import QuotationPayments from "./partials/payments.vue";
import { mapActions, mapState } from "vuex";
import SendEmailDocument from "@components/secondary/SendEmailDocument.vue";

export default {
    props: ["typeUser", "soapCompany", "generateOrderNoteFromQuotation"],
    mixins: [deletable],
    components: {
        DataTable,
        QuotationOptions,
        QuotationOptionsPdf,
        QuotationPayments,
        SendEmailDocument
    },
    computed: {
        ...mapState(["config"]),
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order);
        },
    },
    data() {
        return {
            resource: "quotations",
            showDialogSendEmailDocument: false,
            recordId: null,
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogOptionsPdf: false,
            state_types: [],
            columns: {
                date_of_issue:           { title: "Fecha Emisión",    visible: true,  order: 0  },
                delivery_date:           { title: "T.Entrega",        visible: false, order: 1  },
                registered_by:           { title: "Registrado por",   visible: false, order: 2  },
                seller:                  { title: "Vendedor",         visible: false, order: 3  },
                customer:                { title: "Cliente",          visible: true,  order: 4  },
                state_type:              { title: "Estado",           visible: true,  order: 5  },
                identifier:              { title: "Cotización",       visible: true,  order: 6  },
                documents:               { title: "Comprobantes",     visible: false, order: 7  },
                sale_notes:              { title: "Notas de venta",   visible: false, order: 8  },
                order_note:              { title: "Pedidos",          visible: false, order: 9  },
                sale_opportunity:        { title: "Oportunidad Venta",visible: false, order: 10 },
                referential_information: { title: "Inf.Referencial",  visible: false, order: 11 },
                contract:                { title: "Contrato",         visible: false, order: 12 },
                exchange_rate_sale:      { title: "Tipo de cambio",   visible: false, order: 13 },
                currency_type_id:        { title: "Moneda",           visible: false, order: 14 },
                payments:                { title: "Pagos",            visible: true,  order: 15 },
                total_exportation:       { title: "T.Exportación",    visible: false, order: 16 },
                total_free:              { title: "T.Gratuito",       visible: false, order: 17 },
                total_unaffected:        { title: "T.Inafecto",       visible: false, order: 18 },
                total_exonerated:        { title: "T.Exonerado",      visible: false, order: 19 },
                total_taxed:             { title: "T.Gravado",        visible: true,  order: 20 },
                total_igv:               { title: "T.Igv",            visible: true,  order: 21 },
                total:                   { title: "Total",            visible: true,  order: 22 },
                pdf:                     { title: "PDF",              visible: true,  order: 23 },
                actions:                 { title: "Acciones",         visible: true,  order: 24 },
            },
            decimal_quantity: 2,
        };
    },
    async created() {
        this.loadColumnVisibilityQuotations();
        await this.filter();
        this.loadDecimalQuantity();
    },
    mounted() {
        this.loadConfiguration();
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
            if (value === undefined || value === null || value === '') return '';
            let cleanValue = value;
            if (typeof cleanValue === 'string') {
                cleanValue = cleanValue.replace(/,/g, '').trim();
            }
            if (isNaN(Number(cleanValue))) return '';
            const num = Number(cleanValue);
            return num.toLocaleString('en-US', { minimumFractionDigits: this.decimal_quantity, maximumFractionDigits: this.decimal_quantity });
        },
        formatDate(date) {
            if (!date) return null;
            return moment(date).format("DD-MM-YYYY");
        },
        saveColumnVisibilityQuotations() {
            const columns = {};
            Object.keys(this.columns).forEach(key => {
                columns[key] = { title: this.columns[key].title, visible: this.columns[key].visible, order: this.columns[key].order };
            });
            this.$http.post('/column-visibility/quotations_index', { columns }).catch(() => {});
        },
        loadColumnVisibilityQuotations() {
            this.$http.get('/column-visibility/quotations_index').then(response => {
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
        clickSendQuotation(id) {
            this.recordId = id;
            this.showDialogSendEmailDocument = true;
        },
        ...mapActions(["loadConfiguration"]),
        canMakeOrderNote(row) {
            let permission = true;

            // Si ya tiene Pedidos, no se genera uno nuevo
            if (row.order_note.full_number) {
                permission = false;
            } else {
                if (this.typeUser !== "admin") {
                    permission = this.generateOrderNoteFromQuotation;
                }
            }

            return permission;
        },
        clickPrintContract(external_id) {
            window.open(`/contracts/print/${external_id}/a4`, "_blank");
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        async changeStateType(row) {
            await this.updateStateType(
                `/${this.resource}/state-type/${row.state_type_id}/${row.id}`
            ).then(() => this.$eventHub.$emit("reloadData"));
        },
        async filter() {
            await this.$http.get(`/${this.resource}/filter`).then(response => {
                this.state_types = response.data.state_types;
            });
        },
        clickEdit(id) {
            this.recordId = id;
            this.showDialogFormEdit = true;
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        clickOptionsPdf(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptionsPdf = true;
        },
        clickAnulate(id) {
            this.anular(`/${this.resource}/anular/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        makeOrder(quotation) {
            let tos = parseInt(quotation);
            localStorage.setItem("Quotation", tos);
            localStorage.setItem("FromQuotation", true);
            window.location.href = "/order-notes/create";
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
                .catch(error => {});
            this.$eventHub.$emit("reloadData");
        },
        clickGenerateDocument(recordId) {
            window.location.href = `/documents/create/quotations/${recordId}`;
        },
        // Métodos auxiliares para redirecciones en dropdown
        goToEdit(id) {
            window.location.href = `/${this.resource}/create/${id}`;
        },
        goToDispatch(id) {
            window.location.href = `/dispatches/create_new/quotation/${id}`;
        },
        goToContract(id) {
            window.location.href = `/contracts/generate-quotation/${id}`;
        },
        go(url) {
          window.location.href = url;
        }
    }
};
</script>
