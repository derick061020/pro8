<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Configuración de Google Maps</h3>
        </div>
        <form class="row card-body px-0" autocomplete="off" @submit.prevent="submit">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Api Key</label>
                    <el-input v-model="form.google_maps_api_key"></el-input>
                </div>
            </div>
            <div class="col-md-12 text-right pt-2 ">
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ['configuration'],
    created() {
        this.initForm();
    },
    data() {
        return {
            form: {
                google_maps_api_key: null,
            },
            resource: 'configurations',
            loading_submit: false,
        };
    },
    methods: {
        initForm() {
            if (this.configuration) {
                this.form.google_maps_api_key = this.configuration.google_maps_api_key;
            }
        },
        async submit() {
            this.loading_submit = true;
            try {
                await this.$http.post(`${this.resource}/google-maps`, this.form);
                this.$message({
                    message: 'Configuración guardada',
                    type: 'success',
                });
            } catch (e) {
                this.$message({
                    message: 'Error al guardar la configuración',
                    type: 'error',
                });
            } finally {
                this.loading_submit = false;
            }
        },
    },
}
</script>