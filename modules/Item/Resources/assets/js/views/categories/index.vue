<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/categories">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-category-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>

            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div> -->
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th>ID</th>
                        <th class="text-center">Imagen</th>
                        <th>Nombre</th>
                        <th>Fecha creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <!-- <td>{{ index }}</td> -->
                        <td>{{ row.id }}</td>
                        <td class="text-center">
                            <img v-if="row.image" :src="row.image_url" alt width="32" height="32" style="object-fit: contain;" />
                        </td>
                        <td>{{ row.name }}</td>
                        <td>{{ row.created_at }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-xs btn-info btn-shad me-1" title="Editar" @click.prevent="clickCreate(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger btn-shad me-1" title="Eliminar" @click.prevent="clickDelete(row.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <category-form 
                :showDialog.sync="showDialog"
                :recordId="recordId"
                    ></category-form> 
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

    import CategoryForm from './form.vue' 
    import DataTable from '../../../../../../../resources/js/components/DataTable.vue'
    import {deletable} from '../../../../../../../resources/js/mixins/deletable'

    export default {
        mixins: [deletable],
        components: {DataTable, CategoryForm},
        data() {
            return {
                title: null,
                showDialog: false, 
                resource: 'categories',
                recordId: null,
            }
        },
        created() {
            this.title = 'Categorías'
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
