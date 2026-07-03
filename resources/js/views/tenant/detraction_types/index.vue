<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Listado de tipos de detracciones </span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="card tab-content-default row-new">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de tipos de detracciones</h3>
            </div> -->
            <div class="card-body">                
                <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>T. Operación</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Porcentaje</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.operation_type_id }}</td>
                            <td>{{ row.id }}</td>
                            <td>{{ row.description }}</td>
                            <td>{{ row.percentage }}</td>
                            <td class="text-center">{{ row.active }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-info me-1" @click.prevent="clickCreate(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                </button>
    
                                <template v-if="typeUser === 'admin'">
                                   <button type="button" class="btn btn-xs btn-danger"  @click.prevent="clickDelete(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                   </button>
                                </template>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>                
            </div>
            <detraction-types-form :showDialog.sync="showDialog"
                             :recordId="recordId"></detraction-types-form>
        </div>
    </div>
</template>

<script>

    import DetractionTypesForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        props: ['typeUser'],
        components: {DetractionTypesForm},
        data() {
            return {
                showDialog: false,
                resource: 'detraction_types',
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
                        this.records = response.data.data
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
            }
        }
    }
</script>
