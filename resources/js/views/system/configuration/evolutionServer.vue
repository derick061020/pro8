<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Servidor Evolution (bot)</h3>
        </div>
        <form class="row card-body px-0" autocomplete="off" @submit.prevent="submit">
            <div class="col-md-12">
                <small class="text-muted d-block mb-2">
                    Servidor Evolution API que todos los tenants usarán para conectar sus WhatsApp.
                </small>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">URL del servidor</label>
                    <el-input
                        v-model="form.evolution_server_url"
                        placeholder="https://evolution.midominio.com">
                    </el-input>
                    <small class="text-muted">Sin barra al final.</small>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">API key</label>
                    <el-input
                        v-model="form.evolution_server_apikey"
                        :type="show_key ? 'text' : 'password'"
                        placeholder="API key del servidor de Evolution">
                        <i slot="suffix"
                           :class="['el-input__icon', show_key ? 'el-icon-view' : 'el-icon-warning-outline']"
                           style="cursor: pointer; padding-right: 8px;"
                           @click="show_key = !show_key"></i>
                    </el-input>
                </div>
            </div>
            <div class="col-md-12 text-right pt-2">
                <el-button type="primary" native-type="submit" :loading="loading">
                    Guardar
                </el-button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ['configuration'],
    created() {
        if (this.configuration) {
            this.form.evolution_server_url = this.configuration.evolution_server_url;
            this.form.evolution_server_apikey = this.configuration.evolution_server_apikey;
        }
    },
    data() {
        return {
            form: {
                evolution_server_url: null,
                evolution_server_apikey: null,
            },
            show_key: false,
            loading: false,
        };
    },
    methods: {
        async submit() {
            this.loading = true;
            try {
                const { data } = await this.$http.post('/configurations/evolution-server', this.form);
                this.$message({
                    message: data.message || 'Configuración guardada',
                    type: data.success ? 'success' : 'error',
                });
            } catch (e) {
                this.$message({ message: 'Error al guardar la configuración', type: 'error' });
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
