<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
        </div>
        <div class="card tab-content-default row-new mb-0 mx-0 bg-transparent">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0"> {{ title }}</h3>
            </div> -->
            <el-tabs v-model="activeName" type="border-card" class="rounded">
                <el-tab-pane 
                    class="mb-3" 
                    v-for="(option, index) in filteredRecords" 
                    :key="option.id"
                    :label="option.name"
                    :name="'tab_' + option.id">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">                                
                                <el-switch
                                    v-model="option.active"
                                    @change="submit(option.id)">
                                </el-switch>
                                <label class="ms-2 mb-0">{{ getDescriptionById(option.id, option.name) }}</label>
                            </div>
                            
                            <TapConfiguration v-if="option.id === 4" :records="records"></TapConfiguration>
                            <PharmacyConfiguration v-if="option.id === 5" :records="records"></PharmacyConfiguration>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
 
        </div>
    </div>
</template>

<script> 

    import TapConfiguration from './partials/tap.vue'
    import PharmacyConfiguration from './partials/pharmacy.vue'

    export default {
        data() {
            return {
                title: null, 
                business_turns:[],
                resource: 'bussiness_turns',
                records: [],
                loading_submit: false,
                errors: {},
                activeName: 'tab_1',
                form: {
                    is_pharmacy: false,
                }
            }
        },
        components: {
            TapConfiguration,
            PharmacyConfiguration
        },
        computed: {
            filteredRecords() {
                return this.records.filter(record => record.id !== 2);
            },
        },
        async created() {
            
            this.title = 'Giros de negocio'
            this.initForm()
            await this.getRecords()
        },
        methods: {
            getDescriptionById(id, name) {
                const descriptions = {
                    1: 'Activa el giro de Hoteles. Se habilitan funciones de hospedaje como gestión de huéspedes, habitaciones y pisos, además de reportes de habitaciones y hospedaje en Reportes → General.',
                            
                    3: 'Activa el giro de Restaurante. Permite gestionar consumos por mesas o pedidos y habilita configuraciones y campos específicos para operaciones de restaurante.',
                            
                    4: 'Activa el giro de Grifo. Habilita configuraciones para la venta de combustibles, gestión de productos de combustible y reportes relacionados con operaciones de grifos.',
                            
                    5: 'Activa el giro de Farmacia. Habilita una tabla especial para medicamentos y campos adicionales en Configuración de Empresa, como el código DIGEMID del establecimiento farmacéutico.',
                }
                return descriptions[id] || `Activar ${name}`
            },
            initForm() {
                this.errors = {}
                this.form = {
                    is_pharmacy: false,
                }
            },
            submit(id) {
                this.loading_submit = true;
                this.activeName = 'tab_' + id;
                
                this.$http.post(`/${this.resource}`,{id}).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getRecords(this.activeName)
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            getRecords(activeTab = null){
                this.$http.get(`/${this.resource}/records`)
                    .then(response => { 
                        this.records = response.data
                        // Verificar si farmacia está activa
                        const pharmacyRecord = this.records.find(r => r.name === 'Farmacia')
                        if (pharmacyRecord) {
                            this.form.is_pharmacy = pharmacyRecord.active
                        }
                        if (activeTab) {
                            this.activeName = activeTab
                            return
                        }

                        // Inicializar el tab activo con el primer registro filtrado
                        const firstRecord = this.records.find(record => record.id !== 2)
                        if (firstRecord) {
                            this.activeName = 'tab_' + firstRecord.id
                        }
                    }) 
            }
        }
    }
</script>
