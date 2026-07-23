<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Configuración de IA (compartida con clientes)</h3>
        </div>
        <form class="row card-body px-0" autocomplete="off" @submit.prevent="submit">
            <div class="col-md-12">
                <p class="text-muted">
                    El proveedor de IA se ofrece como servicio del reseller. Los tenants no configuran su propia key —
                    todos los bots de WhatsApp usan estos valores.
                </p>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="control-label">OpenAI API Key</label>
                    <el-input v-model="form.openai_api_key" placeholder="sk-..." show-password
                        @blur="onApiKeyBlur"></el-input>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">
                        Modelo
                        <el-button v-if="!loading_models" type="text" size="mini" @click="loadModels"
                            style="padding: 0 0 0 6px;">refrescar</el-button>
                        <span v-else style="margin-left: 6px; font-size: 11px;" class="text-muted">cargando…</span>
                    </label>
                    <el-select v-model="form.openai_model" filterable allow-create default-first-option
                        placeholder="gpt-4o-mini" style="width: 100%;">
                        <el-option v-for="m in models" :key="m" :label="m" :value="m"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="col-md-12 text-right pt-2">
                <el-button :loading="loading_test" @click="testConnection">Probar conexión</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </div>
</template>

<script>
const DEFAULT_MODELS = ['gpt-4o-mini', 'gpt-4o', 'gpt-4-turbo', 'gpt-3.5-turbo'];

export default {
    created() {
        this.fetch();
    },
    data() {
        return {
            form: {
                openai_api_key: null,
                openai_model: 'gpt-4o-mini',
            },
            models: DEFAULT_MODELS,
            resource: 'configurations',
            loading_submit: false,
            loading_test: false,
            loading_models: false,
        };
    },
    methods: {
        async fetch() {
            try {
                const { data } = await this.$http.get(`${this.resource}/openai`);
                this.form.openai_api_key = data.openai_api_key;
                this.form.openai_model = data.openai_model || 'gpt-4o-mini';
                if (this.form.openai_api_key) this.loadModels();
            } catch (e) {
                // silent
            }
        },
        async loadModels() {
            if (!this.form.openai_api_key) return;
            this.loading_models = true;
            try {
                const { data } = await this.$http.post(`${this.resource}/openai/models`, {
                    openai_api_key: this.form.openai_api_key,
                });
                if (data.success && data.models && data.models.length) {
                    this.models = data.models;
                } else if (!data.success) {
                    this.$message({ message: data.message || 'No se pudieron cargar los modelos', type: 'warning' });
                }
            } catch (e) {
                // silent
            } finally {
                this.loading_models = false;
            }
        },
        onApiKeyBlur() {
            if (this.form.openai_api_key) this.loadModels();
        },
        async submit() {
            this.loading_submit = true;
            try {
                const { data } = await this.$http.post(`${this.resource}/openai`, this.form);
                this.$message({ message: data.message || 'Guardado', type: data.success ? 'success' : 'error' });
            } catch (e) {
                this.$message({ message: 'Error al guardar', type: 'error' });
            } finally {
                this.loading_submit = false;
            }
        },
        async testConnection() {
            this.loading_test = true;
            try {
                const { data } = await this.$http.post(`${this.resource}/openai/test`, this.form);
                this.$message({ message: data.message, type: data.success ? 'success' : 'error', duration: 6000 });
            } catch (e) {
                this.$message({ message: 'Error al probar la conexión', type: 'error' });
            } finally {
                this.loading_test = false;
            }
        },
    },
}
</script>
