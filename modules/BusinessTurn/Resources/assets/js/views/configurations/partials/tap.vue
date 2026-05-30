<template>
    <el-tabs v-model="activeName" type="border-card" class="rounded">
        <el-tab-pane class="mb-3" name="first" v-if="isTapActive">
            <span slot="label">Configuración de grifos</span>
            <div class="row switch-configuration-container">
                <div class="col-md-6 mt-4">
                    <label class="control-label">Guardar placas respecto a un cliente</label>
                    <div :class="{ 'has-danger': errors.save_plates_client }" class="form-group">
                        <el-switch v-model="form.save_plates_client"
                            @change="submit"></el-switch>
                        <small v-if="errors.save_plates_client" class="form-control-feedback"
                            v-text="errors.save_plates_client[0]"></small>
                    </div>
                </div>
            </div>
        </el-tab-pane>
        
        <el-tab-pane class="mb-3" name="second" v-if="isPharmacyActive">
            <span slot="label">Datos de farmacia</span>
            <div class="row">
                <div class="col-md-8">
                    <div :class="{'has-danger': errorsPharmacy.cod_digemid}"
                         class="form-group">
                        <label class="control-label">Código de observación DIGEMID</label>
                        <el-input v-model="formPharmacy.cod_digemid" 
                                  placeholder="Ingrese el código DIGEMID de la empresa"></el-input>
                        <small v-if="errorsPharmacy.cod_digemid"
                               class="form-control-feedback d-block"
                               v-text="errorsPharmacy.cod_digemid[0]"></small>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-group mb-0">
                        <el-button type="primary" 
                                   @click="saveCompanyData" 
                                   :loading="loading_submit_pharmacy">
                            Guardar
                        </el-button>
                    </div>
                </div>
            </div>
        </el-tab-pane>
    </el-tabs>
    

</template>
<script>

export default {
    props: {
        records: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            activeName: 'first',
            form: {
                save_plates_client: false,
            },
            errors: {},
            typeUser: '',
            formPharmacy: {
                cod_digemid: null,
            },
            errorsPharmacy: {},
            loading_submit_pharmacy: false,
        };
    },
    computed: {
        isTapActive() {
            const grifos = this.records.find(record => record.id === 4);
            return grifos ? grifos.active : false;
        },
        isPharmacyActive() {
            const farmacia = this.records.find(record => record.id === 5);
            return farmacia ? farmacia.active : false;
        }
    },
    created() {
        // Establecer la pestaña activa inicial basándose en qué está habilitado
        if (this.isTapActive) {
            this.activeName = 'first';
        } else if (this.isPharmacyActive) {
            this.activeName = 'second';
        }
        this.getRecord();
        this.getCompanyData();
    },
    methods: {
        getRecord() {
            this.$http
                .get('/bussiness_turns/configuration/tap')
                .then((response) => {
                    this.form = response.data;
                });
        },
        submit() {
            this.errors = {};
            this.$http
                .post('/bussiness_turns/configuration/tap', this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                });
        },
        async getCompanyData() {
            try {
                const response = await this.$http.get('/companies/record')
                if (response.data && response.data.data) {
                    this.formPharmacy.cod_digemid = response.data.data.cod_digemid || null
                }
            } catch (error) {
                console.error('Error al cargar datos de empresa:', error)
            }
        },
        async saveCompanyData() {
            this.loading_submit_pharmacy = true
            this.errorsPharmacy = {}
            
            try {
                const companyResponse = await this.$http.get('/companies/record')
                if (!companyResponse.data || !companyResponse.data.data) {
                    this.$message.error('No se pudo obtener la información de la empresa')
                    return
                }
                
                const companyData = companyResponse.data.data
                
                const response = await this.$http.post('/companies', {
                    id: companyData.id,
                    cod_digemid: this.formPharmacy.cod_digemid,
                    number: companyData.number,
                    name: companyData.name,
                    trade_name: companyData.trade_name,
                    soap_type_id: companyData.soap_type_id,
                    soap_send_id: companyData.soap_send_id,
                    soap_username: companyData.soap_username,
                    soap_password: companyData.soap_password,
                    certificate: companyData.certificate,
                    identity_document_type_id: companyData.identity_document_type_id,
                    country_id: companyData.country_id,
                    department_id: companyData.department_id,
                    province_id: companyData.province_id,
                    district_id: companyData.district_id,
                    address: companyData.address,
                    email: companyData.email,
                    telephone: companyData.telephone,
                })
                
                if (response.data.success) {
                    this.$message.success('Código DIGEMID actualizado correctamente')
                    this.errorsPharmacy = {}
                } else {
                    this.$message.error(response.data.message || 'Error al guardar')
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errorsPharmacy = error.response.data.errors
                } else {
                    this.$message.error('Error al guardar los datos')
                    console.error(error)
                }
            } finally {
                this.loading_submit_pharmacy = false
            }
        },
    },
}
</script>