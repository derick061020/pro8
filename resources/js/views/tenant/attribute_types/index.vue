<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Listado de Atributos </span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="card tab-content-default row-new">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de Atributos</h3>
            </div> -->
            <div class="card-body">                
                <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <tr width="100%">
                            <!-- <th width="5%">#</th> -->                            
                            <th>Código</th>
                            <th class="text-start">Activo</th>
                            <th>Descripción</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <!-- <td>{{ index + 1 }}</td> -->
                            <td>{{ row.id }}</td>
                            <td class="text-start">
                                <el-switch
                                    v-model="row.active"
                                    :disabled="row.loading_active === true"
                                    @change="changeActive(row, $event)"></el-switch>
                            </td>
                            <td>{{ row.description }}</td>                            
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-info btn-shad me-1" title="Editar" @click.prevent="clickCreate(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                </button>
                                  <template v-if="typeUser === 'admin'">
                                    <button type="button" class="btn btn-xs btn-danger btn-shad" title="Eliminar" @click.prevent="clickDelete(row.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                  </template>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>                
                <!-- <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
                    </div>
                </div> -->
            </div>
    
    
            <tribute-concept-types-form :showDialog.sync="showDialog"
                                        :recordId="recordId"></tribute-concept-types-form>
        </div>
    </div>
</template>
<script>


    import TributeConceptTypesForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        props: ['typeUser'],
        components: {TributeConceptTypesForm},
        data() {
            return {

                showDialog: false,
                resource: 'tribute_concept_types',
                recordId: null,
                records: [],
                showLeftShadow: false,
                showRightShadow: false,
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        mounted() {
            this.$nextTick(() => {
                const el = this.$refs.scrollContainer;
                if (el) {
                    el.addEventListener('scroll', this.checkScrollShadows);
                    this.checkScrollShadows();
                }
            });
        },
        methods: {
            checkScrollShadows() {
                const el = this.$refs.scrollContainer;
                if (!el) return;
                
                const scrollLeft = el.scrollLeft;
                const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;
                
                this.showLeftShadow = scrollLeft > 1;
                this.showRightShadow = scrollRight > 1;
            },
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data.map(row => ({
                            ...row,
                            active: row.active === true || row.active === 1 || row.active === '1',
                        }))
                    })
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
            changeActive(row, value) {
                const previousValue = value === true ? false : true
                this.$set(row, 'loading_active', true)

                this.$http.post(`/${this.resource}`, {
                    id: row.id,
                    description: row.description,
                    active: row.active,
                })
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            row.active = previousValue
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        row.active = previousValue
                        if (error.response && error.response.status === 422) {
                            this.$message.error('No se pudo actualizar el estado del atributo')
                        } else {
                            this.$message.error('Error inesperado al actualizar el estado')
                        }
                    })
                    .then(() => {
                        this.$set(row, 'loading_active', false)
                    })
            }
        }
    }
</script>
