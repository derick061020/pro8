<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Comandos del Bot</span></li>
            </ol>
        </div>
        <div class="card card-dashboard border tab-content-default row-new row-mx-0">
            <div class="card-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class="mb-3" name="first">
                        <span slot="label"><h3 class="m-0 mt-2">Comandos del Bot</h3></span>

                        <p class="text-muted mb-2 mt-2">
                            Palabras que el vendedor escribirá en WhatsApp para controlar al bot.
                            Pueden ser cualquier cosa: <code>/bot</code>, <code>qwe</code>, <code>b+</code>, etc.
                            Sin espacios, máximo 20 caracteres y deben ser distintos entre sí.
                        </p>

                        <div class="row">
                            <div class="col-md-4 mt-4 form-modern">
                                <label class="control-label">Iniciar / retomar</label>
                                <div class="form-group">
                                    <el-input v-model="form.bot_trigger_command" placeholder="/bot"></el-input>
                                    <small class="text-muted">El vendedor lo usa para abrir una sesión.</small>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4 form-modern">
                                <label class="control-label">Pausar</label>
                                <div class="form-group">
                                    <el-input v-model="form.bot_pause_command" placeholder="/p"></el-input>
                                    <small class="text-muted">Detiene al bot cuando el dueño quiere intervenir.</small>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4 form-modern">
                                <label class="control-label">Cerrar sesión</label>
                                <div class="form-group">
                                    <el-input v-model="form.bot_exit_command" placeholder="/fin"></el-input>
                                    <small class="text-muted">Termina la sesión actual.</small>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <el-button
                                    :loading="loading"
                                    class="pull-right btn-primary"
                                    @click="submit">
                                    Guardar
                                </el-button>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['configuration'],
    created() {
        if (this.configuration) {
            this.form.bot_trigger_command = this.configuration.bot_trigger_command || '/bot';
            this.form.bot_pause_command = this.configuration.bot_pause_command || '/p';
            this.form.bot_exit_command = this.configuration.bot_exit_command || '/fin';
        }
    },
    data() {
        return {
            activeName: 'first',
            form: {
                bot_trigger_command: '/bot',
                bot_pause_command: '/p',
                bot_exit_command: '/fin',
            },
            loading: false,
        };
    },
    methods: {
        async submit() {
            this.loading = true;
            try {
                const { data } = await this.$http.post('/whatsapp-bot/commands', this.form);
                this.$message({
                    message: data.message || 'Comandos actualizados',
                    type: data.success ? 'success' : 'error',
                });
            } catch (e) {
                this.$message({ message: 'Error al guardar los comandos', type: 'error' });
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
