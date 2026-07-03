<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Listado de unidades </span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm mt-2 me-2" @click.prevent="clickCreate()">
                    <i class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>

        <div class="card tab-content-default row-new">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <el-select v-model="filter.active" placeholder="Estado" @change="getData">
                            <el-option label="Todos" value="all"></el-option>
                            <el-option label="Activos" value="1"></el-option>
                            <el-option label="Inactivos" value="0"></el-option>
                        </el-select>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                    <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>

                    <div class="table-responsive" ref="scrollContainer">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th class="text-center">Activo</th>
                                    <th>Descripción</th>
                                    <th>Símbolo</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in records" :key="index" :class="{ disable_color: isUnitInactive(row) }">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ row.id }}</td>
                                    <td class="text-center">
                                        <el-switch v-model="row.active" @change="clickActive(row)"></el-switch>
                                    </td>
                                    <td>{{ row.description }}</td>
                                    <td>{{ row.symbol }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-info btn-xs me-2" @click.prevent="clickCreate(row.id)">Editar</button>
                                        <template v-if="typeUser === 'admin'">
                                            <button type="button" class="btn btn-danger btn-xs" @click.prevent="clickDelete(row.id)">Eliminar</button>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <unit-types-form
            :showDialog.sync="showDialog"
            :recordId="recordId">
        </unit-types-form>
    </div>
</template>

<script>
    import UnitTypesForm from './form.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        mixins: [deletable],
        props: ['typeUser'],
        components: {UnitTypesForm},
        data() {
            return {
                showDialog: false,
                resource: 'unit_types',
                recordId: null,
                records: [],
                filter: { active: '1' },
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
            isUnitInactive(row) {
                if (row && typeof row.active_value === 'boolean') return !row.active_value
                const value = row?.active
                return value === 0 || value === '0' || value === false
            },
            checkScrollShadows() {
                const el = this.$refs.scrollContainer;
                if (!el) return;
                const scrollLeft = el.scrollLeft;
                const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;
                this.showLeftShadow = scrollLeft > 1;
                this.showRightShadow = scrollRight > 1;
            },
            getData() {
                this.$http.get(`/${this.resource}/records`, {
                    params: this.filter
                })
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
            },
            clickActive(row) {
                this.$http.post(`/${this.resource}/active`, row)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                            row.active = !row.active
                        }
                    })
                    .catch(error => {
                        this.$message.error('Error al actualizar')
                    })
            }
        }
    }
</script>
