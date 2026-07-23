<template>
    <div class="card card-config">
        <div class="card-header bg-info">
            <h3 class="my-0">Envío de mensajes a través de QR Api</h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Cuando QR Api está activo, los comprobantes (boletas/facturas) se envían como PDF
                al cliente vía WhatsApp después de emitir. Es independiente del bot conversacional.
            </p>

            <div class="form-group">
                <label class="control-label me-3">Habilitar QR Api</label>
                <el-switch
                    v-model="form.qr_api_enable_ws"
                    active-text="Sí"
                    inactive-text="No"
                    @change="updateEnable">
                </el-switch>
            </div>

            <div v-if="form.qr_api_enable_ws">
                <hr>
                <div class="form-group">
                    <label class="control-label me-3 d-block mb-2">Mensajes de WhatsApp — ciclo actual</label>
                    <div v-if="form.whatsapp_messages_unlimited" class="whatsapp-usage-text">
                        <span class="whatsapp-usage-main text-success"><i class="fas fa-infinity"></i></span>
                        <small class="text-muted d-block">Mensajes ilimitados en tu plan</small>
                    </div>
                    <div v-else class="whatsapp-gauge-wrap">
                        <div class="whatsapp-gauge">
                            <svg width="84" height="84" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#eef0f2" stroke-width="3.5"/>
                                <circle
                                    cx="18" cy="18" r="15.9155" fill="none"
                                    :stroke="whatsappUsageColor" stroke-width="3.5"
                                    stroke-linecap="round"
                                    :stroke-dasharray="`${whatsappUsagePct} ${100 - whatsappUsagePct}`"
                                    transform="rotate(-90 18 18)">
                                </circle>
                            </svg>
                        </div>
                        <div class="whatsapp-usage-text">
                            <span class="whatsapp-usage-main" :style="{ color: whatsappUsageColor }">
                                {{ form.whatsapp_messages_used }} / {{ form.whatsapp_messages_limit }}
                            </span>
                            <small class="text-muted d-block">mensajes enviados este ciclo</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label me-3">Formato del PDF enviado</label>
                    <el-radio-group
                        v-model="form.qr_api_pdf_format"
                        class="pdf-format-group"
                        @change="updateEnable">
                        <el-radio-button label="ticket">Ticket</el-radio-button>
                        <el-radio-button label="a4">A4</el-radio-button>
                    </el-radio-group>
                </div>
                <div class="form-group">
                    <label class="control-label me-3">Usar el mismo número del bot</label>
                    <el-switch
                        :value="form.qr_api_use_bot_instance"
                        active-text="Sí"
                        inactive-text="No"
                        @change="onToggleUseBot">
                    </el-switch>
                    <small v-if="form.qr_api_use_bot_instance" class="text-muted d-block mt-2">
                        Se usará la instancia <strong>{{ form.evolution_instance || '(bot no conectado)' }}</strong> del bot.
                        Asegúrate de conectar el bot primero desde su sección.
                    </small>
                    <small v-else class="text-muted d-block mt-2">
                        Conecta un número distinto al del bot escaneando un nuevo QR más abajo.
                    </small>
                </div>

                <!-- Conectar número propio -->
                <div v-if="!form.qr_api_use_bot_instance">
                    <!-- Estado: input de nombre -->
                    <div v-if="step === 'input'">
                        <p class="text-muted">
                            Conecta un WhatsApp escaneando un código QR. Asigna un nombre único para esta instancia.
                        </p>
                        <div class="form-group">
                            <label class="control-label">Nombre de la instancia</label>
                            <el-input
                                v-model="instanceName"
                                placeholder="ej. pro8-qrapi, miempresa-pdf"
                                @keyup.enter.native="startConnection">
                            </el-input>
                            <small class="text-muted">3 a 40 caracteres. Sin espacios ni símbolos especiales.</small>
                        </div>
                        <div class="text-end">
                            <el-button
                                type="primary"
                                size="small"
                                :loading="loading"
                                :disabled="!canStart"
                                @click="startConnection">
                                Conectar
                            </el-button>
                        </div>
                    </div>

                    <!-- Estado: disconnected -->
                    <div v-else-if="step === 'disconnected'">
                        <el-alert type="warning" :closable="false" class="mb-3">
                            <span slot="title">Instancia desconectada de WhatsApp</span>
                            Tu instancia <strong>{{ form.qr_api_instance }}</strong> está en estado
                            <code>{{ lastState || 'desconocido' }}</code>.
                        </el-alert>
                        <div class="qr-action-row">
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

                    <!-- Estado: QR -->
                    <div v-else-if="step === 'qr'" class="text-center">
                        <p class="text-muted">
                            Escanea el código QR desde WhatsApp en tu teléfono:
                            <strong>Configuración → Dispositivos vinculados → Vincular un dispositivo</strong>.
                        </p>
                        <div v-if="qrImage" class="my-3">
                            <img :src="qrImage" alt="QR de WhatsApp" style="max-width: 320px; border: 1px solid #eee; padding: 8px; background: white;">
                        </div>
                        <div v-else class="my-3 py-4 text-muted">Generando código QR…</div>
                        <p class="text-muted small">
                            Esperando escaneo… <span v-if="lastState">({{ lastState }})</span>
                        </p>
                        <div>
                            <el-button @click="cancelReconnect">Volver</el-button>
                            <el-button :loading="loading" icon="el-icon-refresh" @click="refreshQr">
                                Refrescar QR
                            </el-button>
                        </div>
                    </div>

                    <!-- Estado: connected -->
                    <div v-else-if="step === 'connected'">
                        <el-alert type="success" :closable="false" class="mb-3">
                            <span slot="title">WhatsApp conectado correctamente</span>
                        </el-alert>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Instancia</small>
                                <div>{{ form.qr_api_instance || '—' }}</div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Estado</small>
                                <div>
                                    <el-tag :type="lastState === 'open' ? 'success' : 'warning'" size="medium">
                                        {{ lastState || 'desconocido' }}
                                    </el-tag>
                                </div>
                            </div>
                        </div>
                        <div class="qr-action-row mt-3">
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
            </div>
        </div>
    </div>
</template>

<script>
const POLL_INTERVAL_MS = 5000;

export default {
    data() {
        return {
            form: {
                qr_api_enable_ws: false,
                qr_api_use_bot_instance: false,
                qr_api_pdf_format: 'ticket',
                qr_api_instance: null,
                qr_api_connected_phone: null,
                qr_api_profile_name: null,
                qr_api_connection_state: 'disconnected',
                evolution_instance: null,
                evolution_connected_phone: null,
                whatsapp_messages_used: 0,
                whatsapp_messages_limit: null,
                whatsapp_messages_unlimited: false,
            },
            instanceName: '',
            qrImage: null,
            reconnecting: false,
            lastState: null,
            loading: false,
            loading_check: false,
            loading_restart: false,
            loading_renew: false,
            loading_disconnect: false,
            pollTimer: null,
            resource: 'qrapi',
        };
    },
    computed: {
        step() {
            if (!this.form.qr_api_instance) return 'input';
            if (this.form.qr_api_connection_state === 'open') return 'connected';
            if (this.reconnecting) return 'qr';
            return 'disconnected';
        },
        canStart() {
            return /^[A-Za-z0-9_\-]{3,40}$/.test(this.instanceName);
        },
        whatsappUsagePct() {
            if (this.form.whatsapp_messages_unlimited || !this.form.whatsapp_messages_limit) return 0;
            const pct = (this.form.whatsapp_messages_used / this.form.whatsapp_messages_limit) * 100;
            return Math.min(100, Math.round(pct));
        },
        whatsappUsageColor() {
            const pct = this.whatsappUsagePct;
            if (pct >= 100) return '#ef4444';
            if (pct >= 80) return '#f59e0b';
            return '#16a34a';
        },
    },
    created() {
        this.getConfig();
    },
    beforeDestroy() {
        this.stopPolling();
    },
    methods: {
        async getConfig() {
            try {
                const { data } = await this.$http.get(`/${this.resource}/configuration`);
                this.form = { ...this.form, ...data };
                this.lastState = data.qr_api_connection_state;
                if (this.step === 'connected') {
                    this.startPolling(15000);
                }
            } catch (e) {
                this.$message({ message: 'No se pudo cargar la configuración', type: 'error' });
            }
        },
        async updateEnable() {
            try {
                await this.$http.post(`/${this.resource}/configuration/update`, {
                    qr_api_enable_ws: this.form.qr_api_enable_ws,
                    qr_api_use_bot_instance: this.form.qr_api_use_bot_instance,
                    qr_api_pdf_format: this.form.qr_api_pdf_format,
                });
                this.$message({ message: 'Configuración actualizada', type: 'success' });
            } catch (e) {
                this.$message({ message: 'Error al guardar', type: 'error' });
            }
        },
        async onToggleUseBot(value) {
            // Si se activa el toggle y ya hay un número conectado, confirmar la desconexión
            if (value && this.form.qr_api_instance) {
                try {
                    await this.$confirm(
                        `Tu número actual (${this.form.qr_api_instance}) se desconectará y se eliminará. ` +
                        `Si quieres volver a usarlo, tendrás que escanear un QR nuevo. ¿Continuar?`,
                        'Usar el número del Bot',
                        { confirmButtonText: 'Sí, desconectar y usar el del Bot', cancelButtonText: 'Cancelar', type: 'warning' }
                    );
                } catch {
                    return;
                }
            }

            // Avisar si el bot no tiene número conectado todavía
            if (value && !this.form.evolution_instance) {
                this.$message({
                    message: 'El bot no tiene un número conectado. Conéctalo primero desde su sección.',
                    type: 'warning',
                });
            }

            this.form.qr_api_use_bot_instance = value;
            await this.updateEnable();

            // Si activamos el toggle y se desconectó el número propio, reflejarlo en la UI
            if (value) {
                this.form.qr_api_instance = null;
                this.form.qr_api_connected_phone = null;
                this.form.qr_api_profile_name = null;
                this.form.qr_api_connection_state = 'disconnected';
                this.lastState = null;
                this.qrImage = null;
                this.stopPolling();
            }
        },
        async startConnection() {
            this.loading = true;
            try {
                const { data } = await this.$http.post(`/${this.resource}/connect`, {
                    instance_name: this.instanceName,
                });
                if (data.success) {
                    this.form.qr_api_instance = data.instance_name;
                    this.form.qr_api_connection_state = 'connecting';
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
                    const { data } = await this.$http.get(`/${this.resource}/qr`);
                    if (data.success && data.qr) {
                        this.qrImage = data.qr;
                        return;
                    }
                    if (i < attempts - 1) {
                        await new Promise(r => setTimeout(r, 1500));
                    }
                }
                this.$message({ message: 'No se obtuvo QR todavía. Vuelve a intentar en unos segundos.', type: 'warning' });
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
                const { data } = await this.$http.get(`/${this.resource}/state`);
                if (!data.success) return;
                this.lastState = data.state;
                if (data.connected_phone) this.form.qr_api_connected_phone = data.connected_phone;
                if (data.profile_name) this.form.qr_api_profile_name = data.profile_name;
                if (data.connected && this.form.qr_api_connection_state !== 'open') {
                    this.form.qr_api_connection_state = 'open';
                    this.reconnecting = false;
                    this.qrImage = null;
                    this.stopPolling();
                    this.startPolling(15000);
                    this.$message({ message: 'WhatsApp conectado correctamente', type: 'success' });
                } else if (!data.connected && this.form.qr_api_connection_state === 'open') {
                    this.form.qr_api_connection_state = data.state || 'close';
                }
            } catch (e) {
                // silent
            }
        },
        async checkStateManual() {
            this.loading_check = true;
            try {
                const { data } = await this.$http.get(`/${this.resource}/state`);
                if (data.success) {
                    this.lastState = data.state;
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
        async restart() {
            this.loading_restart = true;
            try {
                const { data } = await this.$http.post(`/${this.resource}/restart`);
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
        confirmRenew() {
            this.$confirm(
                'Renovar la instancia elimina la actual y crea una nueva con el mismo nombre. Tendrás que escanear el QR. ¿Continuar?',
                'Renovar instancia',
                { confirmButtonText: 'Sí, renovar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => this.renew()).catch(() => {});
        },
        async renew() {
            this.loading_renew = true;
            try {
                const { data } = await this.$http.post(`/${this.resource}/renew`);
                if (data.success) {
                    this.form.qr_api_connection_state = 'connecting';
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
                this.$message({ message: 'Error al renovar', type: 'error' });
            } finally {
                this.loading_renew = false;
            }
        },
        confirmDisconnect() {
            this.$confirm(
                'Esto desconectará el WhatsApp actual y eliminará la instancia. Tendrás que escanear un nuevo QR. ¿Continuar?',
                'Conectar nuevo número',
                { confirmButtonText: 'Sí, desconectar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => this.disconnect()).catch(() => {});
        },
        async disconnect() {
            this.loading_disconnect = true;
            try {
                const { data } = await this.$http.post(`/${this.resource}/disconnect`);
                if (data.success) {
                    this.form.qr_api_instance = null;
                    this.form.qr_api_connection_state = 'disconnected';
                    this.form.qr_api_connected_phone = null;
                    this.form.qr_api_profile_name = null;
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
.whatsapp-gauge-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.whatsapp-gauge {
    flex-shrink: 0;
    line-height: 0;
}
.whatsapp-gauge circle {
    transition: stroke-dasharray 0.4s ease, stroke 0.4s ease;
}
.whatsapp-usage-main {
    font-size: 20px;
    font-weight: 700;
}
.pdf-format-group ::v-deep .el-radio-button__inner {
    font-size: 16px;
    padding: 10px 20px;
}
.qr-action-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-end;
    align-items: center;
}
.qr-action-row > .el-button {
    margin-left: 0 !important;
}
</style>
