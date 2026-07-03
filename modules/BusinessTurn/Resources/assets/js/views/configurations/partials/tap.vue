<template>
    <div v-if="isTapActive">
        <hr>
        <h4 class="mt-0 mb-3">Configuración de grifos</h4>
        <div class="switch-configuration-container">
            <div class="d-flex align-items-center">
                <el-switch v-model="form.save_plates_client" @change="submit"></el-switch>
                <label class="control-label ms-2 my-0">Guardar placas respecto a un cliente</label>                
            </div>
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
            form: {
                save_plates_client: false,
            },
            errors: {},
            typeUser: '',
        };
    },
    computed: {
        isTapActive() {
            const grifos = this.records.find(record => record.id === 4);
            return grifos ? grifos.active : false;
        }
    },
    created() {
        this.getRecord();
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
        }
    },
}
</script>