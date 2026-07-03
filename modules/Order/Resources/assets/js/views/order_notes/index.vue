<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/order-notes"
                    ><svg
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
                        <path d="M16 5l3 3" /></svg>
                    </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Pedidos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    :href="`/${resource}/create`"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
            </div>

            <div
                class="btn-group flex-wrap pull-right dropdown"
                v-if="config.mi_tienda_pe === true"
            >
                <button
                    aria-expanded="false"
                    class="btn btn-custom btn-sm mt-2 me-2 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    type="button"
                >
                    <i class="fa fa-upload"></i>
                    Importar
                    <span class="caret"></span>
                </button>
                <div
                    class="dropdown-menu"
                    role="menu"
                    style="
                                position: absolute;
                                will-change: transform;
                                top: 0px;
                                left: 0px;
                                transform: translate3d(0px, 42px, 0px);
                            "
                    x-placement="bottom-start"
                >
                    <a
                        class="dropdown-item text-1"
                        href="#"
                        @click.prevent="clickImport()"
                    >
                        MiTienda.Pe
                    </a>
                </div>
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
                    <el-dropdown-menu slot="dropdown" style="min-width: 220px;">
                            <div style="max-height: 520px; overflow-y: auto;">
                                <el-dropdown-item divided disabled v-if=" customFieldColumns.length > 0 ">
                                    <strong>Campos personalizados</strong>
                                </el-dropdown-item>
                                <el-dropdown-item
                                    v-for="field in customFieldColumns"
                                    :key="`custom-field-${field.id}`"

                                >
                                    <el-checkbox
                                        @change="updateCustomFieldColumns()"
                                        v-model="field.visible"
                                        >{{ field.name }}</el-checkbox
                                    >
                                </el-dropdown-item>
                                <el-dropdown-item divided v-if=" customFieldColumns.length > 0 "></el-dropdown-item>
                                <el-dropdown-item disabled>
                                    <strong>Seleccionar columnas</strong>
                                </el-dropdown-item>
                                <el-dropdown-item v-for="col in tenantSelectableColumns" :key="col.key">
                                    <el-checkbox @change="saveColumnVisibility" v-model="columns[col.key].visible">{{ col.title }}</el-checkbox>
                                </el-dropdown-item>
                            </div>
                        </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table
                    :resource="resource"
                    :typeUser="typeUser"
                    :soapCompany="soapCompany"
                >
                    <tr slot="heading">
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">Fecha Emisión</th>
                            <th v-if="col.visible && col.key === 'delivery_date'" :key="col.key" class="text-center">Fecha Entrega</th>
                            <th v-if="col.visible && col.key === 'seller'" :key="col.key">Vendedor</th>
                            <th v-if="col.visible && col.key === 'customer'" :key="col.key">Cliente</th>
                            <th v-if="col.visible && col.key === 'state_type'" :key="col.key">Estado</th>
                            <!-- Campos personalizados: posición configurable vía columna virtual `personalized` (visibilidad la dicta cada field) -->
                            <template v-if="col.key === 'personalized'">
                                <template v-for="field in customFieldColumns">
                                    <th v-if="field.visible" :key="`cf-head-${field.id}`" class="text-start" style="min-width: 120px;">{{ field.name }}</th>
                                </template>
                            </template>
                            <th v-if="col.visible && col.key === 'identifier'" :key="col.key">Pedido</th>
                            <th v-if="col.visible && col.key === 'documents'" :key="col.key">Comprobantes</th>
                            <th v-if="col.visible && col.key === 'sale_notes'" :key="col.key">Notas de venta</th>
                            <th v-if="col.visible && col.key === 'quotation'" :key="col.key">Cotizacion</th>
                            <th v-if="col.visible && col.key === 'dispatches'" :key="col.key">Guías</th>
                            <th v-if="col.visible && col.key === 'mi_tienda_pe'" :key="col.key">#Pedido MiTienda.Pe</th>
                            <th v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">Moneda</th>
                            <th v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end">T.Exportación</th>
                            <th v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">T.Inafecta</th>
                            <th v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">T.Exonerado</th>
                            <th v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">T.Gravado</th>
                            <th v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">T.Igv</th>
                            <th v-if="col.visible && col.key === 'balance'" :key="col.key" class="text-end">Saldo</th>
                            <th v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">Total</th>
                            <th v-if="col.visible && col.key === 'pdf'" :key="col.key" class="text-center">PDF</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">Acciones</th>
                        </template>
                    </tr>

                    <tr></tr>
                    <tr
                        slot-scope="{ index, row }"
                        :class="{ anulate_color: row.state_type_id == '11' }"
                    >
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">{{ row.date_of_issue | toDate }}</td>
                            <td v-if="col.visible && col.key === 'delivery_date'" :key="col.key" class="text-center">{{ row.delivery_date | toDate }}</td>
                            <td v-if="col.visible && col.key === 'seller'" :key="col.key">{{ row.user_name }}</td>
                            <td v-if="col.visible && col.key === 'customer'" :key="col.key">{{ row.customer_name }}<br /><small v-text="row.customer_number"></small></td>
                            <td v-if="col.visible && col.key === 'state_type'" :key="col.key">
                                <template v-if="row.state_type_id == '11'">{{ row.state_type_description }}</template>
                                <template v-else>
                                    <el-select v-model="row.state_type_id" @change="changeStateType(row)" style="width:120px !important">
                                        <el-option v-for="option in state_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                </template>
                            </td>
                            <!-- Campos personalizados: posición configurable vía columna virtual `personalized` (visibilidad la dicta cada field) -->
                            <template v-if="col.key === 'personalized'">
                                <template v-for="field in customFieldColumns">
                                    <td v-if="field.visible" :key="`cf-data-${field.id}`" class="text-start">
                                        <template v-if="isEditableCustomField(field)">
                                            <template v-if="field.type === 'text'"><el-input v-model="row.custom_fields_data[field.slug]" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'number'"><el-input v-model.number="row.custom_fields_data[field.slug]" type="number" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'textarea'"><el-input v-model="row.custom_fields_data[field.slug]" type="textarea" :rows="2" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'select'">
                                                <el-select v-model="row.custom_fields_data[field.slug]" @change="saveCustomFieldValue(row, field)" size="small" clearable :placeholder="field.name">
                                                    <el-option v-for="option in normalizeOptions(field.options)" :key="option" :label="option" :value="option"></el-option>
                                                </el-select>
                                            </template>
                                            <template v-else-if="field.type === 'checkbox'">
                                                <el-checkbox-group v-model="row.custom_fields_data[field.slug]" @change="saveCustomFieldValue(row, field)">
                                                    <el-checkbox v-for="option in normalizeOptions(field.options)" :key="option" :label="option" :value="option">{{ option }}</el-checkbox>
                                                </el-checkbox-group>
                                            </template>
                                            <template v-else-if="field.type === 'date'"><el-date-picker v-model="row.custom_fields_data[field.slug]" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" @change="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-date-picker></template>
                                            <template v-else>{{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}</template>
                                        </template>
                                        <template v-else>{{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}</template>
                                    </td>
                                </template>
                            </template>
                            <td v-if="col.visible && col.key === 'identifier'" :key="col.key">{{ row.identifier }}</td>
                            <td v-if="col.visible && col.key === 'documents'" :key="col.key">
                                <template v-for="(document, i) in row.documents">
                                    <label :key="i" v-text="showAnulateDoc(document)" class="d-block"></label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'sale_notes'" :key="col.key">
                                <template v-for="(sale_note, i) in row.sale_notes">
                                    <label :key="i" text="sale_note.number_full" class="d-block"></label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'quotation'" :key="col.key">
                                <template v-if="row.quotation !== undefined && row.quotation.full_number !== undefined">
                                    <label class="d-block">{{ row.quotation.full_number }}</label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'dispatches'" :key="col.key">
                                <template v-for="(dispach, i) in row.dispatches">
                                    <label :key="i" v-text="dispach.number" class="d-block"></label>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'mi_tienda_pe'" :key="col.key">
                                <template v-if="row.mi_tienda_pe && row.mi_tienda_pe.order_number">{{ row.mi_tienda_pe.order_number }}</template>
                            </td>
                            <td v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">{{ row.currency_type_id }}</td>
                            <td v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exportation) }}</td>
                            <td v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_unaffected) }}</td>
                            <td v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exonerated) }}</td>
                            <td v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_taxed) }}</td>
                            <td v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_igv) }}</td>
                            <td v-if="col.visible && col.key === 'balance'" :key="col.key" class="text-end text-nowrap">
                                <label v-if="row.documents.length > 0" :key="'doc_payment_' + index" v-text="calculatePayments(row.documents)"></label>
                                <label v-if="row.sale_notes.length > 0" :key="'sale_note_payment_' + index" v-text="calculatePayments(row.sale_notes)"></label>
                            </td>
                            <td v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total) }}</td>
                            <td v-if="col.visible && col.key === 'pdf'" :key="col.key" class="text-end">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickOptionsPdf(row.id)">PDF</button>
                            </td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <el-button class="btn-dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item v-if="row.state_type_id != '11' && row.btn_generate && seller_can_generate_cpe === true && soapCompany != '03'" :command="{action: 'generateDocument', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
                                            Generar comprobante
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="cantEdited(row)" :command="{action: 'edit', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                            Editar
                                        </el-dropdown-item>
                                        <el-dropdown-item divided v-if="(row.state_type_id != '11' && row.btn_generate && seller_can_generate_cpe === true && soapCompany != '03') || cantEdited(row)"></el-dropdown-item>
                                        <el-dropdown-item :command="{action: 'duplicate', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                            Duplicar
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="{action: 'createGuide', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path></svg>
                                            Crear Guía
                                        </el-dropdown-item>
                                        <el-dropdown-item divided v-if="canAnulate(row)"></el-dropdown-item>
                                        <el-dropdown-item v-if="canAnulate(row)" :command="{action: 'anulate', id: row.id}" class="text-danger option-delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                                            Anular
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                                <ChangeStateType :key="'change_state_type_' + row.id" :state="row.state_type_id" :id="row.id" v-if="config.order_node_advanced" class="ms-2" />
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
                :configuration="configuration"
            ></quotation-options>

            <quotation-options-pdf
                :showDialog.sync="showDialogOptionsPdf"
                :recordId="recordId"
                :showClose="true"
                :configuration="configuration"
            ></quotation-options-pdf>

            <mi-tienda-pe
                :showDialog.sync="showMiTiendaPeDialog"
            ></mi-tienda-pe>
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
import MiTiendaPe from "./mi_tienda_pe.vue";
import DataTable from "../../components/DataTable.vue";
import { deletable } from "@mixins/deletable";
import { mapActions, mapState } from "vuex";

import StateType from "../../../../../../OrderNote/Resources/assets/js/components/StateType.vue";
import ChangeStateType from "../../../../../../OrderNote/Resources/assets/js/components/ChangeStateType.vue";

export default {
    props: ["typeUser", "soapCompany", "configuration"],
    mixins: [deletable],
    components: {
        DataTable,
        QuotationOptions,
        MiTiendaPe,
        QuotationOptionsPdf,
        StateType,
        ChangeStateType
    },
    async created() {
        this.$store.commit("setConfiguration", this.configuration);
        this.loadConfiguration();
        this.filter();
        if (this.config.mi_tienda_pe === true) {
            this.getMiTiendaDataData();
        }
        this.loadDecimalQuantity();
        await this.loadColumnVisibility();
        this.loadCustomFieldsColumns();
    },
    data() {
        return {
            resource: "order-notes",
            recordId: null,
            showMiTiendaPeDialog: false,
            showDialogOptions: false,
            showDialogOptionsPdf: false,
            state_types: [],
            columns: {
                date_of_issue:     { title: "Fecha Emisión",      visible: true,  order: 0  },
                delivery_date:     { title: "F.Entrega",           visible: true,  order: 1  },
                seller:            { title: "Vendedor",            visible: true,  order: 2  },
                customer:          { title: "Cliente",             visible: true,  order: 3  },
                state_type:        { title: "Estado",              visible: true,  order: 4  },
                personalized:      { title: "Personalizados",      visible: true,  order: 5  },
                identifier:        { title: "Pedido",              visible: true,  order: 6  },
                documents:         { title: "Comprobantes",        visible: true,  order: 7  },
                sale_notes:        { title: "Notas de venta",      visible: true,  order: 8  },
                quotation:         { title: "Cotizacion",          visible: false, order: 9  },
                dispatches:        { title: "Guías de Remisión",   visible: false, order: 10 },
                mi_tienda_pe:      { title: "Pedido MiTienda.Pe",  visible: false, order: 11 },
                currency_type:     { title: "Moneda",              visible: true,  order: 12 },
                total_exportation: { title: "T.Exportación",       visible: false, order: 13 },
                total_unaffected:  { title: "T.Inafecto",          visible: false, order: 14 },
                total_exonerated:  { title: "T.Exonerado",         visible: false, order: 15 },
                total_taxed:       { title: "T.Gravado",           visible: true,  order: 16 },
                total_igv:         { title: "T.IGV",               visible: true,  order: 17 },
                balance:           { title: "Saldo",               visible: true,  order: 18 },
                total:             { title: "Total",               visible: true,  order: 19 },
                pdf:               { title: "PDF",                 visible: true,  order: 20 },
                actions:           { title: "Acciones",            visible: true,  order: 21 },
            },
            state_type_accepted: ["01", "03", "05", "07", "13"],
            customFieldColumns: [],
            savedCustomFieldVisibilities: {},
            decimal_quantity: 2,
        };
    },
    computed: {
        ...mapState(["config", "mi_tienda_pe"]),
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order);
        },
        tenantSelectableColumns() {
            return this.orderedColumns.filter(col => col.key !== 'personalized');
        },
        seller_can_generate_cpe() {
            if (
                this.typeUser === "admin" ||
                (this.configuration !== undefined &&
                    this.configuration !== null &&
                    this.configuration
                        .seller_can_generate_sale_opportunities === true)
            ) {
                return true;
            }
            return false;
        }
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
        async changeStateType(row) {
            await this.updateStateType(
                `/${this.resource}/state-type/${row.state_type_id}/${row.id}`
            ).then(() => this.$eventHub.$emit("reloadData"));
        },
        filter() {
            this.$http.get(`/${this.resource}/filter`).then(response => {
                this.state_types = response.data.state_types;
            });
        },
        saveColumnVisibility() {
            const columns = {};
            Object.keys(this.columns).forEach(key => {
                columns[key] = { title: this.columns[key].title, visible: this.columns[key].visible, order: this.columns[key].order };
            });
            if (columns.personalized) {
                const fields = {};
                this.customFieldColumns.forEach(field => {
                    fields[field.slug] = field.visible;
                });
                columns.personalized.fields = fields;
            }
            this.$http.post('/column-visibility/order_notes_index', { columns }).catch(() => {});
        },
        loadColumnVisibility() {
            return this.$http.get('/column-visibility/order_notes_index').then(response => {
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
                    if (data.personalized && data.personalized.fields) {
                        this.savedCustomFieldVisibilities = data.personalized.fields;
                    }
                }
            }).catch(() => {});
        },
        ...mapActions(["loadConfiguration"]),

        clickImport() {
            this.showMiTiendaPeDialog = true;
        },
        cantEdited(row) {
            if (
                row &&
                row.documents &&
                row.documents.length == 0 &&
                row.state_type_id != "11"
            )
                return true;
            return false;
        },
        showAnulateDoc(document) {
            if (document.state_type_id == "11")
                return document.number_full + " (Anulado)";
            return document.number_full;
        },
        canAnulate(row) {
            if (row.state_type_id === "11") return false;

            if (row && row.documents) {
                if (row.documents.length == 0 && row.state_type_id != "11")
                    return true;

                if (row.documents.length > 0) {
                    let can_anulate = false;

                    const exist_accepted_document = row.documents.find(
                        document => {
                            return this.state_type_accepted.includes(
                                document.state_type_id
                            );
                        }
                    );

                    if (!exist_accepted_document) can_anulate = true;

                    return can_anulate;
                }
            }

            return false;

            // if(
            //     row &&
            //     row.documents
            // ) {
            //     if (
            //         row.documents.length == 0 &&
            //         row.state_type_id != '11'
            //     ) return true;
            //     if (
            //         row.documents.length > 0
            //     ) {
            //         let sal = false;
            //         row.documents.forEach(function(doc){
            //             sal = doc.state_type_id === '11';
            //         })
            //         return sal;
            //     }
            // }
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
            this.voided(`/${this.resource}/voided/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
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
        getMiTiendaDataData() {
            this.$http.post(`/mi_tienda_pe/getdata`).then(response => {
                let data = response.data;
                this.$store.commit("setMiTiendaPe", data.configurationMiTienda);
            });
        },
        calculatePayments(documents) {
            let payments = 0;
            documents.forEach(doc => {
                payments = payments + doc.total_payments;
            });

            return payments.toFixed(2);
        },
        handleCommand(command) {
            switch(command.action) {
                case 'generateDocument':
                    this.clickOptions(command.id);
                    break;
                case 'edit':
                    window.location.href = `/${this.resource}/edit/${command.id}`;
                    break;
                case 'duplicate':
                    this.duplicate(command.id);
                    break;
                case 'createGuide':
                    window.location.href = `/dispatches/create_new/order_note/${command.id}`;
                    break;
                case 'anulate':
                    this.clickAnulate(command.id);
                    break;
            }
        },
        async loadCustomFieldsColumns() {
            try {
                const response = await this.$http.get(
                    "/configurations/custom-fields/order-notes"
                );
                const defaultVisible = this.columns.personalized
                    ? this.columns.personalized.visible
                    : true;
                this.customFieldColumns = (response.data.data || []).map(field => {
                    const savedVisible = this.savedCustomFieldVisibilities[field.slug];
                    return {
                        ...field,
                        visible: savedVisible !== undefined ? savedVisible : defaultVisible
                    };
                });
            } catch (error) {
                console.error("Error cargando columnas de campos personalizados:", error);
                this.customFieldColumns = [];
            }
        },
        updateCustomFieldColumns() {
            this.saveColumnVisibility();
        },
        isEditableCustomField(field) {
            return [
                'text',
                'number',
                'textarea',
                'select',
                'checkbox',
                'date'
            ].includes(field.type);
        },
        normalizeOptions(options) {
            if (!options) return [];
            if (Array.isArray(options)) {
                if (options.length === 1 && typeof options[0] === 'string' && options[0].includes(',')) {
                    return options[0]
                        .split(',')
                        .map(opt => opt.trim())
                        .filter(opt => opt.length > 0);
                }
                return options;
            }
            if (typeof options === 'string') {
                return options
                    .split(/[,\n]/)
                    .map(opt => opt.trim())
                    .filter(opt => opt.length > 0);
            }
            return [];
        },
        ensureCustomFieldsData(row, field = null) {
            if (!row.custom_fields_data || typeof row.custom_fields_data !== 'object') {
                this.$set(row, 'custom_fields_data', {});
            }
            if (field && field.type === 'checkbox' && row.custom_fields_data[field.slug] === undefined) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            return row.custom_fields_data;
        },
        saveCustomFieldValue(row, field) {
            this.ensureCustomFieldsData(row, field);
            if (field.type === 'checkbox' && !Array.isArray(row.custom_fields_data[field.slug])) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            this.$http
                .post('/order-notes/custom-fields/update', {
                    id: row.id,
                    custom_fields_data: row.custom_fields_data
                })
                .then(response => {
                    if (response.data.success) {
                        if (response.data.data !== undefined) {
                            this.$set(row, 'custom_fields_data', response.data.data);
                        }
                        this.$message.success('Campo personalizado actualizado correctamente.');
                    }
                })
                .catch(error => {
                    console.error('Error guardando campo personalizado:', error);
                    this.$message.error('No se pudo actualizar el campo personalizado.');
                });
        },
        formatCustomFieldValue(value) {
            if (value === null || value === undefined || value === "") {
                return "";
            }
            if (Array.isArray(value)) {
                return value.join(", ");
            }
            if (typeof value === "object") {
                return JSON.stringify(value);
            }
            return value;
        },
    }
};
</script>
