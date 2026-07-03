<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/fixed-asset/items">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Ítems</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <template v-if="typeUser === 'admin'">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
                </template>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de Ítems</h3>
            </div> -->
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading" width="100%">
                        <!-- <th>#</th> -->
                        <th>Cód. Interno</th>
                        <th>Unidad</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th v-if="typeUser != 'seller'" class="text-end">P.Unitario (Compra)</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }" >
                        <!-- <td>{{ index }}</td> -->
                        <td>{{ row.internal_id }}</td>
                        <td>{{ row.unit_type_id }}</td>
                        <td>{{ row.name }}</td>
                        <td>{{ row.description }}</td>
                        <td v-if="typeUser != 'seller'" class="text-end">{{ formatDecimal(row.purchase_unit_price) }}</td>
                        <td class="text-end">
                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn btn-xs btn-info btn-shad me-1" title="Editar" @click.prevent="clickCreate(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger btn-shad" title="Eliminar" @click.prevent="clickDelete(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <items-form :showDialog.sync="showDialog"
                        :recordId="recordId"></items-form>


        </div>
    </div>
</template>
<style>
@media only screen and (max-width: 485px){
    .filter-container{
      margin-top: 0px;
      & .btn-filter-content, .btn-container-mobile{
        display: flex;
        align-items: center;
        justify-content: start;
      }
    }
}
</style>
<script>

    import ItemsForm from './form.vue'
    import DataTable from '../../components/DataTable.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        props:['typeUser'],
        mixins: [deletable],
        components: {ItemsForm, DataTable},
        data() {
            return {
                showDialog: false,
                showImportDialog: false,
                showWarehousesDetail: false,
                resource: 'fixed-asset/items',
                recordId: null,
                warehousesDetail:[],
                decimal_quantity: 2
            }
        },
        created() {
            this.loadDecimalQuantity()
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

                const stringValue = value.toString();
                const prefix = stringValue.replace(/[0-9.,\-\s]/g, '');
                const cleanValue = stringValue.replace(/[^0-9.\-]/g, '');
                const number = Number(cleanValue);

                if (isNaN(number)) return value;

                return `${prefix ? prefix + ' ' : ''}${number.toLocaleString('en-US', { minimumFractionDigits: this.decimal_quantity, maximumFractionDigits: this.decimal_quantity })}`;
            },  
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            }, 
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }, 
        }
    }
</script>
