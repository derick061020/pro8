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
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-12 d-flex">
                        
                       <template  v-for="(option,ind) in filteredRecords">
                            <el-checkbox class="plan_documents d-block"  
                                v-model="option.active"  
                                :label="option.id"  
                                :key="ind"  
                                @change="submit(option.id)">
                                {{option.name}}
                            </el-checkbox>
                       </template>
                    </div>
                </div>
            </div>
        <template v-if="showConfigFilling">
            <TapConfiguration class="mt-2" :records="records" />
        </template>
 
        </div>
    </div>
</template>

<script> 

    import TapConfiguration from  './partials/tap.vue'

    export default {
        data() {
            return {
                title: null, 
                business_turns:[],
                resource: 'bussiness_turns',
                records: [],
                loading_submit: false,
                errors: {},
                form: {
                    is_pharmacy: false,
                }
            }
        },
        components: {
            TapConfiguration
        },
        computed: {
            filteredRecords() {
                return this.records.filter(record => record.id !== 2);
            },
            showConfigFilling()
            {
                return this.records.find(record => record.id === 4).active || this.records.find(record => record.id === 5).active;
            }
        },
        async created() {
            
            this.title = 'Giros de negocio'
            this.initForm()
            await this.getRecords()
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    is_pharmacy: false,
                }
            },
            submit(id) {
                this.loading_submit = true;
                
                this.$http.post(`/${this.resource}`,{id}).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getRecords()
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
            getRecords(){
                this.$http.get(`/${this.resource}/records`)
                    .then(response => { 
                        this.records = response.data
                        // Verificar si farmacia está activa
                        const pharmacyRecord = this.records.find(r => r.name === 'Farmacia')
                        if (pharmacyRecord) {
                            this.form.is_pharmacy = pharmacyRecord.active
                        }
                    }) 
            }
        }
    }
</script>
