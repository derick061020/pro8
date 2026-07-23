<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Configuración de WhatsApp</span></li>
            </ol>
        </div>

        <div class="card card-dashboard border tab-content-default row-new row-mx-0">
            <div class="card-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class="mb-3" name="qr_api">
                        <span slot="label"><h3 class="m-0 mt-2">Envío de comprobantes</h3></span>
                        <div class="mt-3">
                            <tenant-qr-api></tenant-qr-api>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane class="mb-3" name="first">
                        <span slot="label"><h3 class="m-0 mt-2">Bot conversacional</h3></span>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <label class="control-label me-3 mb-0">Bot activo</label>
                                    <el-switch
                                        v-model="form.whatsapp_bot_enabled"
                                        active-text="Sí"
                                        inactive-text="No"
                                        @change="onToggleBotEnabled">
                                    </el-switch>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Cuando está desactivado, el bot deja de procesar mensajes entrantes. La instancia de WhatsApp sigue conectada y el envío de comprobantes no se ve afectado.
                                </small>
                            </div>
                        </div>
                        <hr>

                        <!-- Step 1: input de nombre de instancia -->
                        <div v-if="step === 'input'" class="row">
                            <div class="col-md-12 mt-3">
                                <p class="text-muted">
                                    Conecta tu WhatsApp escaneando un código QR. Asigna un nombre único para esta instancia
                                    (puedes usar letras, números, guiones y guión bajo).
                                </p>
                            </div>
                            <div class="col-md-6 mt-3 form-modern">
                                <label class="control-label">Nombre de la instancia</label>
                                <div class="form-group">
                                    <el-input
                                        v-model="instanceName"
                                        placeholder="ej. pro8-bot, miempresa-wa"
                                        @keyup.enter.native="startConnection">
                                    </el-input>
                                    <small class="text-muted">3 a 40 caracteres. Sin espacios ni símbolos especiales.</small>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3 text-end">
                                <el-button
                                    :loading="loading"
                                    :disabled="!canStart"
                                    class="btn-primary"
                                    @click="startConnection">
                                    Conectar
                                </el-button>
                            </div>
                        </div>

                        <!-- Step intermedio: instancia desconectada -->
                        <div v-else-if="step === 'disconnected'" class="row">
                            <div class="col-md-12 mt-3">
                                <el-alert type="warning" :closable="false" class="mb-3">
                                    <span slot="title">Instancia desconectada de WhatsApp</span>
                                    Tu instancia <strong>{{ form.evolution_instance }}</strong> está en estado
                                    <code>{{ lastState || 'desconocido' }}</code>. Vuelve a escanear el QR para reconectar,
                                    o renueva la instancia si reconectar no funciona.
                                </el-alert>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="bot-action-row">
                                    <el-button size="small" :loading="loading_check" icon="el-icon-view" @click="checkStateManual">
                                        Comprobar estado
                                    </el-button>
                                    <el-button size="small" type="primary" :loading="loading" icon="el-icon-link" @click="startReconnect">
                                        Reconectar instancia
                                    </el-button>
                                    <el-button size="small" type="danger" plain :loading="loading_renew" icon="el-icon-refresh-right" @click="confirmRenew">
                                        Renovar instancia
                                    </el-button>
                                    <el-button size="small" type="warning" :loading="loading_disconnect" @click="confirmDisconnect">
                                        Conectar nuevo número
                                    </el-button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: QR + polling -->
                        <div v-else-if="step === 'qr'" class="row">
                            <div class="col-md-12 mt-3 text-center">
                                <p class="text-muted">
                                    Escanea el código QR desde WhatsApp en tu teléfono:
                                    <strong>Configuración → Dispositivos vinculados → Vincular un dispositivo</strong>.
                                </p>
                                <div v-if="qrImage" class="my-3">
                                    <img :src="qrImage" alt="QR de WhatsApp" style="max-width: 320px; border: 1px solid #eee; padding: 8px; background: white;">
                                </div>
                                <div v-else class="my-3 py-5 text-muted">
                                    Generando código QR…
                                </div>
                                <p class="text-muted small">
                                    Esperando escaneo… <span v-if="lastState">({{ lastState }})</span>
                                </p>
                            </div>
                            <div class="col-md-12 mt-2 text-center">
                                <el-button @click="cancelReconnect">Volver</el-button>
                                <el-button :loading="loading" icon="el-icon-refresh" @click="refreshQr">
                                    Refrescar QR
                                </el-button>
                            </div>
                        </div>

                        <!-- Step 3: monitoreo persistente -->
                        <div v-else-if="step === 'connected'" class="row">
                            <div class="col-md-12 mt-3">
                                <el-alert type="success" :closable="false" class="mb-3">
                                    <span slot="title">WhatsApp conectado correctamente</span>
                                </el-alert>
                            </div>

                            <div class="col-md-6 mt-3 form-modern">
                                <label class="control-label">Instancia</label>
                                <div class="metric-value">{{ form.evolution_instance || '—' }}</div>
                            </div>
                            <div class="col-md-6 mt-3 form-modern">
                                <label class="control-label">Estado</label>
                                <div>
                                    <el-tag :type="lastState === 'open' ? 'success' : 'warning'" size="medium">
                                        {{ lastState || 'desconocido' }}
                                    </el-tag>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <p class="text-muted small">
                                    El bot está listo para recibir mensajes. Habilita el bot por usuario desde
                                    <strong>Usuarios</strong> → editar → tab "Datos personales".
                                </p>
                            </div>

                            <div class="col-md-12 mt-2">
                                <div class="bot-action-row">
                                    <el-button size="small" :loading="loading_check" icon="el-icon-view" @click="checkStateManual">
                                        Comprobar estado
                                    </el-button>
                                    <el-button size="small" :loading="loading_restart" icon="el-icon-refresh" @click="restart">
                                        Reiniciar conexión
                                    </el-button>
                                    <el-button size="small" type="danger" plain :loading="loading_renew" icon="el-icon-refresh-right" @click="confirmRenew">
                                        Renovar instancia
                                    </el-button>
                                    <el-button size="small" type="warning" :loading="loading_disconnect" @click="confirmDisconnect">
                                        Conectar nuevo número
                                    </el-button>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane class="mb-3" name="commands">
                        <span slot="label"><h3 class="m-0 mt-2">Comandos del bot</h3></span>
                        <div class="mt-3">
                            <tenant-whatsapp-bot-commands :configuration="configuration"></tenant-whatsapp-bot-commands>
                            <div class="mt-4 pt-3 border-top text-end">
                                <el-button type="info" plain icon="el-icon-document" @click="openBotManual">
                                    Abrir manual del bot
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
const POLL_INTERVAL_MS = 5000;

export default {
    props: ['configuration'],
    data() {
        return {
            activeName: 'qr_api',
            form: {
                evolution_instance: this.configuration?.evolution_instance || null,
                evolution_connection_state: this.configuration?.evolution_connection_state || 'disconnected',
                connected_phone: this.configuration?.evolution_connected_phone || null,
                profile_name: this.configuration?.evolution_profile_name || null,
                whatsapp_bot_enabled: this.configuration?.whatsapp_bot_enabled ?? true,
            },
            instanceName: '',
            qrImage: null,
            reconnecting: false,
            lastState: this.configuration?.evolution_connection_state || null,
            loading: false,
            loading_restart: false,
            loading_disconnect: false,
            loading_check: false,
            loading_renew: false,
            pollTimer: null,
        };
    },
    computed: {
        step() {
            if (!this.form.evolution_instance) return 'input';
            if (this.form.evolution_connection_state === 'open') return 'connected';
            if (this.reconnecting) return 'qr';
            return 'disconnected';
        },
        canStart() {
            return /^[A-Za-z0-9_\-]{3,40}$/.test(this.instanceName);
        },
        connectedPhoneFormatted() {
            const p = this.form.connected_phone;
            return p ? `+${p}` : '—';
        },
    },
    mounted() {
        if (this.step === 'qr') {
            this.refreshQr();
            this.startPolling();
        } else if (this.step === 'connected') {
            this.checkState();
            this.startPolling(15000);
        }
    },
    beforeDestroy() {
        this.stopPolling();
    },
    methods: {
        openBotManual() {
            this.$eventHub.$emit('openHelpDrawer', 'whatsapp-bot/manual');
        },
        async onToggleBotEnabled(value) {
            try {
                const { data } = await this.$http.post('/whatsapp-bot/toggle-enabled', { enabled: value });
                this.form.whatsapp_bot_enabled = data.enabled;
                this.$message({ message: data.message, type: data.enabled ? 'success' : 'warning' });
            } catch (e) {
                this.form.whatsapp_bot_enabled = !value;
                this.$message({ message: 'No se pudo cambiar el estado del bot', type: 'error' });
            }
        },
        async startConnection() {
            this.loading = true;
            try {
                const { data } = await this.$http.post('/whatsapp-bot/connect', {
                    instance_name: this.instanceName,
                });
                if (data.success) {
                    this.form.evolution_instance = data.instance_name;
                    this.form.evolution_connection_state = 'connecting';
                    this.reconnecting = true;
                    await this.$nextTick();
                    this.refreshQr();
                    this.startPolling();
                } else {
                    this.$message({ message: data.message || 'No se pudo iniciar', type: 'error' });
                }
            } catch (e) {
                this.$message({ message: 'Error al iniciar la conexión', type: 'error' });
            } finally {
                this.loading = false;
            }
        },
        async refreshQr(attempts = 4) {
            this.loading = true;
            try {
                for (let i = 0; i < attempts; i++) {
                    const { data } = await this.$http.get('/whatsapp-bot/qr');
                    if (data.success && data.qr) {
                        this.qrImage = data.qr;
                        return;
                    }
                    if (i < attempts - 1) {
                        await new Promise(r => setTimeout(r, 1500));
                    }
                }
                this.$message({ message: 'Evolution no devolvió un QR todavía. Pulsa "Refrescar QR" en unos segundos.', type: 'warning' });
            } catch (e) {
                this.$message({ message: 'Error al refrescar QR', type: 'error' });
            } finally {
                this.loading = false;
            }
        },
        startPolling(interval) {
            this.stopPolling();
            this.pollTimer = setInterval(() => this.checkState(), interval || POLL_INTERVAL_MS);
        },
        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },
        async checkState() {
            try {
                const { data } = await this.$http.get('/whatsapp-bot/state');
                if (!data.success) return;
                this.lastState = data.state;
                if (data.connected_phone) this.form.connected_phone = data.connected_phone;
                if (data.profile_name) this.form.profile_name = data.profile_name;
                if (data.connected && this.form.evolution_connection_state !== 'open') {
                    this.form.evolution_connection_state = 'open';
                    this.reconnecting = false;
                    this.qrImage = null;
                    this.stopPolling();
                    this.startPolling(15000);
                    this.$message({ message: 'WhatsApp conectado correctamente', type: 'success' });
                } else if (!data.connected && this.form.evolution_connection_state === 'open') {
                    this.form.evolution_connection_state = data.state || 'close';
                }
            } catch (e) {
                // silent retry
            }
        },
        async startReconnect() {
            this.reconnecting = true;
            this.qrImage = null;
            await this.$nextTick();
            await this.refreshQr();
            this.startPolling();
        },
        cancelReconnect() {
            this.reconnecting = false;
            this.qrImage = null;
            this.stopPolling();
        },
        async checkStateManual() {
            this.loading_check = true;
            try {
                const { data } = await this.$http.get('/whatsapp-bot/state');
                if (data.success) {
                    this.lastState = data.state;
                    if (data.connected_phone) this.form.connected_phone = data.connected_phone;
                    if (data.profile_name) this.form.profile_name = data.profile_name;
                    this.$message({
                        message: data.connected ? `Conectado (${data.state})` : `Estado: ${data.state}`,
                        type: data.connected ? 'success' : 'warning',
                    });
                } else {
                    this.$message({ message: data.message || 'No se pudo consultar estado', type: 'error' });
                }
            } catch (e) {
                this.$message({ message: 'Error al consultar estado', type: 'error' });
            } finally {
                this.loading_check = false;
            }
        },
        confirmRenew() {
            this.$confirm(
                'Renovar la instancia elimina la actual y crea una nueva con el mismo nombre y configuración. Tendrás que volver a escanear el QR. ¿Continuar?',
                'Renovar instancia',
                { confirmButtonText: 'Sí, renovar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => this.renew()).catch(() => {});
        },
        async renew() {
            this.loading_renew = true;
            try {
                const { data } = await this.$http.post('/whatsapp-bot/renew');
                if (data.success) {
                    this.form.evolution_connection_state = 'connecting';
                    this.form.connected_phone = null;
                    this.form.profile_name = null;
                    this.qrImage = null;
                    this.lastState = 'connecting';
                    this.reconnecting = true;
                    this.$message({ message: data.message, type: 'success' });
                    await this.$nextTick();
                    this.refreshQr();
                    this.startPolling();
                } else {
                    this.$message({ message: data.message || 'No se pudo renovar', type: 'error' });
                }
            } catch (e) {
                this.$message({ message: 'Error al renovar instancia', type: 'error' });
            } finally {
                this.loading_renew = false;
            }
        },
        async restart() {
            this.loading_restart = true;
            try {
                const { data } = await this.$http.post('/whatsapp-bot/restart');
                this.$message({
                    message: data.message || 'Reinicio solicitado',
                    type: data.success ? 'success' : 'warning',
                });
            } catch (e) {
                this.$message({ message: 'Error al reiniciar', type: 'error' });
            } finally {
                this.loading_restart = false;
            }
        },
        confirmDisconnect() {
            this.$confirm(
                'Esto desconectará el WhatsApp actual y eliminará la instancia en Evolution. Tendrás que escanear un nuevo QR para reconectar. ¿Continuar?',
                'Conectar nuevo número',
                { confirmButtonText: 'Sí, desconectar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => this.disconnect()).catch(() => {});
        },
        async disconnect() {
            this.loading_disconnect = true;
            try {
                const { data } = await this.$http.post('/whatsapp-bot/disconnect');
                if (data.success) {
                    this.form.evolution_instance = null;
                    this.form.evolution_connection_state = 'disconnected';
                    this.form.connected_phone = null;
                    this.form.profile_name = null;
                    this.instanceName = '';
                    this.qrImage = null;
                    this.lastState = null;
                    this.stopPolling();
                    this.$message({ message: data.message, type: 'success' });
                } else {
                    this.$message({ message: data.message || 'No se pudo desconectar', type: 'error' });
                }
            } catch (e) {
                this.$message({ message: 'Error al desconectar', type: 'error' });
            } finally {
                this.loading_disconnect = false;
            }
        },
    },
};
</script>

<style scoped>
.metric-value {
    font-size: 1.05rem;
    color: #303133;
}
.bot-action-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-end;
    align-items: center;
}
.bot-action-row > .el-button {
    margin-left: 0 !important;
}
</style>
