<template>
    <div :class="colClass?colClass:'col-12'">
        <el-input v-model="wsPhone">
            <template slot="prepend">+51</template>
            <template slot="append">
                <el-tooltip class="item"
                    :content="disabledReason"
                    effect="dark"
                    placement="top-start"
                    :disabled="!canSend ? false : true">
                    <el-button
                        @click="sendQrChat"
                        :disabled="!canSend"
                        :loading="loading_submit">
                        Enviar <i class="fab fa-whatsapp"></i>
                    </el-button>
                </el-tooltip>
            </template>
        </el-input>
        <small v-if="errors.customer_telephone"
            class="form-control-feedback"
            v-text="errors.customer_telephone[0]"></small>
    </div>
</template>

<script>

import {mapState} from "vuex/dist/vuex.mjs";
export default {
    props: ['colClass','wsPhone','wsFile','wsFileA4','wsDocument','wsMessage', 'wsData', 'wsConfig'],
    data() {
        return {
            form: {},
            errors: {},
            loading_submit: false,
            resource: 'qrapi'
        }
    },
    computed: {
        ...mapState(['config']),
        effectiveConfig() {
            return (this.wsConfig && Object.keys(this.wsConfig).length) ? this.wsConfig : this.config;
        },
        effectiveInstance() {
            const c = this.effectiveConfig;
            return c.qr_api_use_bot_instance ? c.evolution_instance : c.qr_api_instance;
        },
        canSend() {
            return !!this.effectiveConfig.qr_api_enable_ws && !!this.effectiveInstance;
        },
        resolvedWsFile() {
            if (this.effectiveConfig.qr_api_pdf_format === 'a4' && this.wsFileA4) {
                return this.wsFileA4;
            }
            return this.wsFile;
        },
        disabledReason() {
            if (!this.effectiveConfig.qr_api_enable_ws) {
                return 'El envío por QR Api está deshabilitado. Actívalo en Configuración → WhatsApp.';
            }
            if (!this.effectiveInstance) {
                return 'No hay un WhatsApp conectado para QR Api. Conecta uno en Configuración → WhatsApp.';
            }
            return 'Enviar comprobante por WhatsApp';
        },
    },

    methods: {
        async sendQrChat() {
            this.loading_submit = true
            if (this.wsPhone == '') {
                return this.$message.error('El número es obligatorio')
            }

            const {extension_only, filename_only} = this.wsData;
            this.convertFileToBase64(this.resolvedWsFile)
                .then(file_encode64 => {

                    this.setForm(file_encode64, `${filename_only}.${extension_only}`)
                    return this.$http
                        .post('/qrapi/send-message', this.form)
                        .then(response => {
                            if(response.status == 200) {
                                return this.$message.success('Documento enviado con exito')
                            }
                        })
                        .catch(error => {
                            const message = error.response?.data?.message || 'No se puede enviar'
                            return this.$message.error(message)
                        })
                        .finally(() => {
                            this.loading_submit = false
                        });
                })

        },
        setForm(base64file, full_filename) {
            this.form = {
                file: base64file,
                number: `51${this.wsPhone}`,
                message: this.wsMessage,
                filename: full_filename
            }
        },
        async convertFileToBase64(url) {
            try {
                const response = await fetch(url);
                const blob = await response.blob();
                return await this.blobToBase64(blob);
            } catch (error) {
                console.error(error);
                return this.$message.error('Error al convertir el archivo a base64:')
            }
        },
        blobToBase64(blob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => {
                    resolve(reader.result.split(',')[1]); // Quitar la parte 'data:*/*;base64,'
                };
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        },

    }
}

</script>