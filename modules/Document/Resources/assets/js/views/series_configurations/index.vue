<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Numeración de facturación
                    <el-tooltip class="item" effect="dark" content="La serie iniciará desde un número correlativo personalizado" placement="top-start">
                        <i class="fa fa-info-circle"></i>
                    </el-tooltip>  
                </span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
            </div>
        </div>
        <div class="tab-content-default row-new">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Numeración de facturación
                    <el-tooltip class="item" effect="dark" content="La serie iniciará desde un número correlativo personalizado" placement="top-start">
                        <i class="fa fa-info-circle"></i>
                    </el-tooltip>    
                </h3> 
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
                            <th>Tipo comprobante</th>
                            <th>Serie</th>
                            <th class="text-center">Número a iniciar</th>
                            <th class="text-center">Emisión inicializada</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.document_type_description }}</td>
                            <td>{{ row.series }}</td>
                            <td class="text-center">{{ row.number }}</td>
                            <td class="text-center">{{ row.initialized_description }}</td>
                            <td class="text-end" >
                                <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button> -->
                                <template v-if="row.btn_delete">
                                    <button type="button" class="btn btn-xs btn-danger btn-shad"  @click.prevent="clickDelete(row.id)">
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
            <series-configurations-form :showDialog.sync="showDialog"
                             :recordId="recordId"></series-configurations-form>
        </div>
    </div>
</template>

<script>

    import SeriesConfigurationsForm from './form.vue'
    import {deletable} from '../../../../../../../resources/js/mixins/deletable'

    export default {
        mixins: [deletable],
        components: {SeriesConfigurationsForm},
        data() {
            return {
                showDialog: false,
                resource: 'series-configurations',
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
                        this.records = response.data
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
