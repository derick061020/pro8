<template>
    <div v-if="isPharmacyActive">
        <hr>
        <h4 class="mt-0 mb-2">Datos de farmacia</h4>
        <div class="row mx-0">
            <div class="col-12 col-sm-4 ps-0">
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
        </div>
        <div class="form-actions text-end">
            <el-button type="primary"
                       @click="saveCompanyData"
                       :loading="loading_submit_pharmacy">
                Guardar
            </el-button>
        </div>
    </div>
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
            formPharmacy: {
                cod_digemid: null,
            },
            errorsPharmacy: {},
            loading_submit_pharmacy: false,
        };
    },
    computed: {
        isPharmacyActive() {
            const farmacia = this.records.find(record => record.id === 5);
            return farmacia ? farmacia.active : false;
        }
    },
    created() {
        this.getCompanyData();
    },
    methods: {
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
