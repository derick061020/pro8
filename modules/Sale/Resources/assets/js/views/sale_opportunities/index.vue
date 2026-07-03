<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/sale-opportunities">
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
                <li class="active"><span>Oportunidad de venta</span></li>
            </ol>

            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm mt-2 me-2">
                    <i class="fa fa-plus-circle"></i> Nuevo
                </a>
            </div>
        </div>

        <div class="card tab-content-default row-new mb-0">

            <!-- Mostrar columnas -->
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="col in orderedColumns" :key="col.key">
                            <el-checkbox v-model="columns[col.key].visible" @change="saveColumnVisibility">
                                {{ col.title }}
                            </el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>

            <div class="card-body">
                <data-table
                    ref="dt"
                    :resource="resource"
                    @success="onTableLoaded"
                >
                    <!-- ENCABEZADOS -->
                    <tr slot="heading">
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-left">Fecha Emisión</th>
                            <th v-if="col.visible && col.key === 'sale'" :key="col.key">Vendedor</th>
                            <th v-if="col.visible && col.key === 'customer'" :key="col.key">Cliente</th>
                            <th v-if="col.visible && col.key === 'state_type'" :key="col.key">Estado</th>
                            <th v-if="col.visible && col.key === 'number'" :key="col.key">O. Venta</th>
                            <th v-if="col.visible && col.key === 'quotation'" :key="col.key">Cotización</th>
                            <th v-if="col.visible && col.key === 'purchase_order'" :key="col.key">O. Compra</th>
                            <th v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">Moneda</th>
                            <th v-if="col.visible && col.key === 'files'" :key="col.key" class="text-center">Archivos</th>
                            <th v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end">T.Exportación</th>
                            <th v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">T.Inafecta</th>
                            <th v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">T.Exonerado</th>
                            <th v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">T.Gravado</th>
                            <th v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">T.Igv</th>
                            <th v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">Total</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">Acciones</th>
                        </template>
                    </tr>

                    <!-- FILAS -->
                    <tr slot-scope="{ index, row }" :class="{ anulate_color : row.state_type_id == '11' }">
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-start">{{ row.date_of_issue | toDate }}</td>
                            <td v-if="col.visible && col.key === 'sale'" :key="col.key">{{ row.user_name }}</td>
                            <td v-if="col.visible && col.key === 'customer'" :key="col.key">{{ row.customer_name }}<br/><small>{{ row.customer_number }}</small></td>
                            <td v-if="col.visible && col.key === 'state_type'" :key="col.key">
                                <template v-if="row.state_type_id == '11'">
                                    {{ row.state_type_description }}
                                </template>
                                <template v-else>
                                    <el-select
                                        v-model="row.state_type_id"
                                        @change="changeStateType(row)"
                                        style="width:120px !important"
                                    >
                                        <el-option
                                            v-for="option in state_types"
                                            :key="option.id"
                                            :value="option.id"
                                            :label="option.description"
                                        ></el-option>
                                    </el-select>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'number'" :key="col.key">{{ row.number_full }}</td>
                            <td v-if="col.visible && col.key === 'quotation'" :key="col.key">{{ row.quotation_number_full }}</td>
                            <td v-if="col.visible && col.key === 'purchase_order'" :key="col.key">{{ row.purchase_order_number_full }}</td>
                            <td v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">{{ row.currency_type_id }}</td>
                            <td v-if="col.visible && col.key === 'files'" :key="col.key" class="text-center">
                                <el-popover placement="right" width="400" trigger="click">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nombre</th>
                                                        <th>Descarga</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(rowFile, i) in row.files" :key="i">
                                                        <td>{{ i + 1 }}</td>
                                                        <td>{{ rowFile.filename }}</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-xs btn-primary"
                                                                @click.prevent="clickDownloadFile(rowFile.filename)">
                                                                <i class="fas fa-file-download"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <el-button slot="reference" class="second-buton">
                                        <i class="fa fa-eye"></i>
                                    </el-button>
                                </el-popover>
                            </td>
                            <td v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exportation) }}</td>
                            <td v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_unaffected) }}</td>
                            <td v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exonerated) }}</td>
                            <td v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }}{{ formatDecimal(row.total_taxed) }}</td>
                            <td v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_igv) }}</td>
                            <td v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total) }}</td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <el-button class="btn-dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                        <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item
                                            v-if="row.btn_generate_oc && canGenerarte"
                                            :command="{action: 'generateOC', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                            Generar O. Compra
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="row.btn_generate && canGenerarte"
                                            :command="{action: 'generateQuotation', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                            Generar Cotización
                                        </el-dropdown-item>
                                        <el-dropdown-item divided />
                                        <el-dropdown-item
                                            v-if="row.state_type_id != '11' && (row.btn_generate && row.btn_generate_oc)"
                                            :command="{action: 'edit', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                            Editar
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            :command="{action: 'options', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                            Opciones
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </td>
                        </template>
                    </tr>

                </data-table>
            </div>

            <sale-opportunities-options
                :showDialog.sync="showDialogOptions"
                :recordId="recordId"
                :showGenerate="true"
                :showClose="true"
            />
        </div>

    </div>
</template>

<script>
import SaleOpportunitiesOptions from './partials/options.vue'
import DataTable from '@components/DataTable.vue'
import { deletable } from '@mixins/deletable'
import axios from 'axios'

export default {
    props: ['typeUser', 'canGenerate'],
    mixins: [deletable],
    components: { DataTable, SaleOpportunitiesOptions },

    data() {
        return {
            resource: 'sale-opportunities',
            recordId: null,
            showDialogOptions: false,
            state_types: [],
            columns: {
                date_of_issue:    { title: 'Fecha Emisión', visible: true,  order: 0  },
                sale:             { title: 'Vendedor',      visible: false, order: 1  },
                customer:         { title: 'Cliente',       visible: true,  order: 2  },
                state_type:       { title: 'Estado',        visible: true,  order: 3  },
                number:           { title: 'O. Venta',      visible: true,  order: 4  },
                quotation:        { title: 'Cotización',    visible: true,  order: 5  },
                purchase_order:   { title: 'O. Compra',     visible: true,  order: 6  },
                currency_type:    { title: 'Moneda',        visible: true,  order: 7  },
                files:            { title: 'Archivos',      visible: true,  order: 8  },
                total_exportation:{ title: 'T.Exportación', visible: false, order: 9  },
                total_unaffected: { title: 'T.Inafecto',    visible: false, order: 10 },
                total_exonerated: { title: 'T.Exonerado',   visible: false, order: 11 },
                total_taxed:      { title: 'T.Gravado',     visible: false, order: 12 },
                total_igv:        { title: 'T.IGV',         visible: false, order: 13 },
                total:            { title: 'Total',         visible: true,  order: 14 },
                actions:          { title: 'Acciones',      visible: true,  order: 15 },
            },
            decimal_quantity: 2,
        }
    },

    computed: {
        canGenerarte() {
            return this.typeUser == 'admin' || this.canGenerate == true
        },
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order)
        },
    },

    created() {
        this.$store.dispatch('loadConfiguration')
        this.loadColumnVisibility()
        this.filter()
    },
    mounted() {
        axios.get('/configurations/record').then(response => {
            if (response.data && response.data.data && response.data.data.decimal_quantity) {
                this.decimal_quantity = response.data.data.decimal_quantity;
            }
        });
    },
    methods: {
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
        money(row) {
            return row.currency_type_id === 'PEN' ? 'S/' : '$'
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
                columns[key] = {
                    title:   this.columns[key].title,
                    visible: this.columns[key].visible,
                    order:   this.columns[key].order,
                };
            });
            this.$http.post('/column-visibility/sale_opportunities_index', { columns }).catch(() => {});
        },

        loadColumnVisibility() {
            this.$http.get('/column-visibility/sale_opportunities_index').then(response => {
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

        async clickDownloadFile(filename) {
            try {
                const response = await axios.head(`/${this.resource}/download-file/${filename}`)
                if (response.status === 200) {
                    window.open(`/${this.resource}/download-file/${filename}`, '_blank')
                } else {
                    this.$message.warning('El archivo no está disponible.')
                }
            } catch (error) {
                this.$message.warning('El archivo no está disponible.')
            }
        },
        clickDownload(external_id) {
            window.open(`/${this.resource}/download/${external_id}`, '_blank');
        },
        clickEdit(id) {
            this.recordId = id
            this.showDialogFormEdit = true
        },
        clickOptions(recordId = null) {
            this.recordId = recordId
            this.showDialogOptions = true
        },
        handleCommand(command) {
            switch(command.action) {
                case 'generateOC':
                    window.location.href = `/purchase-orders/sale-opportunity/${command.id}`;
                    break;
                case 'generateQuotation':
                    window.location.href = `/quotations/create/${command.id}/sale_opportunity`;
                    break;
                case 'edit':
                    window.location.href = `/${this.resource}/create/${command.id}`;
                    break;
                case 'options':
                    this.clickOptions(command.id);
                    break;
            }
        }
    }
}
</script>

<style scoped>
.anulate_color {
    color: red;
}
</style>
