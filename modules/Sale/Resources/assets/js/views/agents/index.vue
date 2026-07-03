<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Agentes</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="card mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Agentes</h3>
            </div> -->
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading" width="100%">
                        <th>#</th>
                        <th>Código</th> 
                        <th>Nombre</th> 
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.internal_id }}</td>
                        <td>{{ row.name }}</td> 
                        <td>{{ row.email }}</td>
                        <td>{{ row.telephone }}</td>
                        <td class="text-right">
                            <template v-if="typeUser === 'admin'">
                                <button type="button" class="btn btn-xs btn-info btn-shad" @click.prevent="clickCreate(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger btn-shad ms-1" @click.prevent="clickDelete(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </button>
                            </template>
                        </td>
                    </tr>
                </data-table>
            </div>

            <agents-form :showDialog.sync="showDialog"
                        :recordId="recordId"></agents-form>
 
        </div>
    </div>
</template>
<script>

    import AgentsForm from './form.vue'
    import DataTable from '@components/DataTable.vue'
    import {deletable} from '@mixins/deletable'

    export default {
        mixins: [deletable],
        props:['typeUser'],
        components: {AgentsForm,  DataTable},
        data() {
            return {
                showDialog: false,
                showImportDialog: false,
                showWarehousesDetail: false,
                resource: 'agents',
                recordId: null,
            }
        },
        created() {
        },
        methods: { 
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            }, 
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>
