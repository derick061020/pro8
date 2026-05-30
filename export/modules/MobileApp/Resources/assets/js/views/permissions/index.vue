<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h4>Gestionar permisos</h4>
                <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <tr>
                            <!-- <th>#</th> -->
                            <th>Email</th>
                            <th>Nombre</th>
                            <th>Perfil</th>
                            <th class="text-center">Permisos</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <!-- <td>{{ index + 1 }}</td> -->
                            <td>{{ row.email }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.type }}</td>
                            <td class="text-center">
                                <template v-if="row.id == 1">
                                    <span>Todos</span>
                                </template>
                                <template v-else>
                                    <button type="button" class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="clickShowPermissions(row.id)">
                                        <i class="fas fa-user-lock"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>                
            </div>
            <div class="card-body mt-4">
                <h4>Gestionar colores de la app</h4>
                <div class="col-md-12">
                        <el-select v-model="form.theme_color"
                                   filterable
                                   learable
                                   @change="submit"
                                   popper-class="el-select-currency"
                        >
                            <el-option v-for="option in [
                                {id: 'blue', description: 'Predeterminado'},
                                {id: 'dark', description: 'Oscuro'},
                                {id: 'red', description: 'Rojizo'},
                            ]"
                                       :key="option.id"
                                       :label="option.description"
                                       :value="option.id"></el-option>
                        </el-select>
                        <!-- <el-button :loading="loading_submit"
                                   class="btn btn-primary btn-submit-default me-3 mb-3 mt-2"
                                   type="primary"
                                   @click.prevent="submit">
                            Guardar 
                        </el-button> -->
                </div>                
            </div>

            <permission-form :showDialog.sync="showDialog"
                        :typeUser="typeUser"
                        :recordId="recordId"></permission-form>
        </div>
    </div>
</template>

<script>

    import PermissionForm from './partials/form.vue'

    export default {
        props: ['typeUser'],
        components: {PermissionForm},
        data() {
            return {
                showDialog: false,
                resource: 'users',
                recordId: null,
                records: [],
                form: {
                    theme_color: null,
                },
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
                this.$http.get(`/app-configurations/record`)
                    .then(response => {
                        this.form = response.data.data
                    })
            },
            clickShowPermissions(recordId) {

                if(recordId != 1)
                {
                    this.recordId = recordId
                    this.showDialog = true
                }
                else
                {
                    this.$message.warning('El usuario principal tiene todos los permisos asignados, no puede modificarlos.')
                }
            },
            submit()
            {
                this.loading_submit = true;

                this.$http.post('/app-configurations', this.form)
                .then(response => {
                    if (response.data.success) {
                        this.loading_submit = false;
                        this.$message.success('Configuración guardada correctamente');
                    }
                })
                .catch(error => {
                    this.loading_submit = false;
                    this.$message.error('Error al guardar la configuración');
                });
            }
        }
    }
</script>
