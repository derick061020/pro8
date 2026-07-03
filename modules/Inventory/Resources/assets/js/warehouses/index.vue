<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <!--<button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>-->
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Descripción</th>
                        <th>Sucursal</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.description }}</td>
                        <td>{{ row.establishment_description }}</td>
                        <td class="text-right">
                            <button type="button" class="btn btn-xs btn-info btn-shad" @click.prevent="clickCreate(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                            </button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <warehouses-form :showDialog.sync="showDialog"
                             :recordId="recordId"></warehouses-form>
        </div>
    </div>
</template>

<script>

    import WarehousesForm from './form.vue'
    import DataTable from '../../../../../../resources/js/components/DataTable.vue'

    export default {
        props: ['type'],
        components: {DataTable, WarehousesForm},
        data() {
            return {
                title: null,
                showDialog: false,
                resource: 'warehouses',
                recordId: null,
            }
        },
        created() {
            this.title = 'Listado de almacenes'
        },
        methods: {
            clickCreate(recordId) {
                this.recordId = recordId
                this.showDialog = true
            }
        }
    }
</script>
