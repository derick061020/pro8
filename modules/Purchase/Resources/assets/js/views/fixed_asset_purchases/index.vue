<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/fixed-asset/purchases">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Compras - Activos fijos</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm  mt-2 me-2"><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="(column, index) in columns" :key="index">
                            <el-checkbox v-model="column.visible" @change="saveColumnVisibility">{{ column.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th class="text-start">F. Emisión</th>
                        <th class="text-center" v-if="columns.date_of_due.visible" >F. Vencimiento</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Número</th>
                        <th>Productos</th> 
                        <th class="text-center">Moneda</th>
                        <th v-if="columns.total_free.visible"  class="text-end">T.Gratuita</th>
                        <th v-if="columns.total_unaffected.visible" class="text-end">T.Inafecta</th>
                        <th v-if="columns.total_exonerated.visible" class="text-end">T.Exonerado</th>
                        <th v-if="columns.total_taxed.visible" class="text-end">T.Gravado</th>
                        <th v-if="columns.total_igv.visible" class="text-end">T.Igv</th>
                        <!-- <th v-if="columns.total_perception.visible" >Percepcion</th> -->
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <!-- <td>{{ index }}</td> -->
                        <td class="text-start">{{ row.date_of_issue }}</td>
                        <td v-if="columns.date_of_due.visible" class="text-center">{{ row.date_of_due }}</td>
                        <td>{{ row.supplier_name }}<br/><small v-text="row.supplier_number"></small></td>
                        <td>{{row.state_type_description}}</td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.document_type_description"></small><br/>
                        </td>
                        <td>

                            <el-popover
                                placement="right"
                                width="400"
                                trigger="click">
                                <el-table :data="row.items">
                                    <el-table-column width="80" property="key" label="#"></el-table-column>
                                    <el-table-column width="220" property="description" label="Nombre"></el-table-column>
                                    <el-table-column width="90" property="quantity" label="Cantidad"></el-table-column>
                                </el-table>
                                <el-button slot="reference" class="btn btn-sm"> <i class="fa fa-eye"></i></el-button>
                            </el-popover>

                        </td>

                        <td class="text-center">{{ row.currency_type_id }}</td>
                        <td v-if="columns.total_free.visible" class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total_free) }}</td>
                        <td v-if="columns.total_unaffected.visible" class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total_unaffected) }}</td>
                        <td v-if="columns.total_exonerated.visible" class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total_exonerated) }}</td>
                        <td v-if="columns.total_taxed.visible" class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total_taxed) }}</td>
                        <td v-if="columns.total_igv.visible" class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total_igv) }}</td>
                        <!-- <td v-if="columns.total_perception.visible" class="text-end">{{ row.total_perception ? row.total_perception : 0 }}</td> -->
                        <td class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total) }}</td>
                        <td class="text-end">

                            <a v-if="row.state_type_id != '11'" :href="`/${resource}/create/${row.id}`" type="button" class="btn btn-xs btn-info btn-shad me-1" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                            </a>
                            <button v-if="row.state_type_id != '11'" type="button" class="btn btn-xs btn-danger btn-shad me-1" title="Anular" @click.prevent="clickVoided(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                            </button>
                            <button v-if="row.state_type_id == '11'" type="button" class="btn btn-xs btn-danger btn-shad me-1" title="Eliminar" @click.prevent="clickDelete(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </td>

                    </tr>
                </data-table>
            </div>
 
        </div>

         
    </div>
</template>

<script>

    import DataTable from '../../components/DataTable.vue'
    import {deletable} from '@mixins/deletable'


    export default {
        mixins: [deletable],
        components: {DataTable},
        data() {
            return {
                showDialogVoided: false,
                resource: 'fixed-asset/purchases',
                recordId: null,
                showDialogOptions: false,
                showDialogPurchasePayments: false,
                showImportDialog: false,
                decimal_quantity: 2,
                columns: {
                    date_of_due: {
                        title: 'F. Vencimiento',
                        visible: false
                    },
                    total_free: {
                        title: 'T.Gratuita',
                        visible: false
                    },
                    total_unaffected: {
                        title: 'T.Inafecta',
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
                        title: 'T.Igv',
                        visible: false
                    },
                    // total_perception:{
                    //     title: 'Percepcion',
                    //     visible: false
                    // }

                }
            }
        },
        created() {
            this.loadDecimalQuantity();
            this.loadColumnVisibility();
        },
        methods: { 
            async loadDecimalQuantity() {
                try {
                    const response = await this.$http.get('/configurations/record')
                    const decimalQuantity = response.data.data.decimal_quantity

                    this.decimal_quantity = parseInt(decimalQuantity || 2)
                } catch (error) {
                    this.decimal_quantity = 2
                }
            },
            formatDecimal(value) {
                const number = parseFloat(value || 0)

                if (isNaN(number)) {
                    return Number(0).toFixed(this.decimal_quantity)
                }

                return number.toFixed(this.decimal_quantity)
            },
            saveColumnVisibility() {
                localStorage.setItem('columnVisibilityFixedpurchases', JSON.stringify(this.columns));
            },
            loadColumnVisibility() {
                const savedColumns = localStorage.getItem('columnVisibilityFixedpurchases');
                if (savedColumns) {
                    this.columns = JSON.parse(savedColumns);
                }
            },
            clickDownload(download) {
                window.open(download, '_blank');
            },
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickVoided(id)
            {
                this.anular(`/${this.resource}/voided/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            clickDelete(id)
            {
                this.destroy(`/${this.resource}/delete/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>
