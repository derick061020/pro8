<template>
    <div>
        <div class="card">
            <div class="card-header bg-info bg-info-customer-admin d-flex justify-content-between align-items-center">
                <h3 class="my-0">Configuración de correo</h3>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    @click="openMailManual"
                >
                    Ver manual
                </button>
            </div>
            <form class="row card-body px-0" autocomplete="off" @submit.prevent="submit">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="mail_host">Dirección del host de correo</label>
                        <el-input id="mail_host" v-model="form.mail_host"></el-input>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="mail_port">Puerto del host de correo</label>
                        <el-input id="mail_port" v-model="form.mail_port"></el-input>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="mail_username">Nombre de usuario de correo</label>
                        <el-input id="mail_username" v-model="form.mail_username"></el-input>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="mail_password">Contraseña del usuario de correo</label>
                        <el-input id="mail_password" v-model="form.mail_password" type="password"></el-input>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="mail_encryption">Encriptación de correo</label>
                        <el-select id="mail_encryption" v-model="form.mail_encryption" style="width: 100%">
                            <el-option label="SSL" value="ssl"></el-option>
                            <el-option label="TLS" value="tls"></el-option>
                            <el-option label="Ninguna" value=""></el-option>
                        </el-select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group pt-3 d-flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success"
                            :disabled="loading_submit"
                            @click="testEmail"
                        >
                            <span v-if="loading_test">Probando...</span>
                            <span v-else>Hacer prueba</span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12 text-end pt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
            </form>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-info bg-info-customer-admin">
                <h3 class="my-0">Buscador de documentos embebido</h3>
            </div>
            <div class="card-body">
                    <div class="mb-4 pb-3 border-bottom">
                        <system-public-search-configuration
                            load-url="/configurations/public-search"
                            save-url="/configurations/public-search"
                        ></system-public-search-configuration>
                    </div>

                    <p class="text-muted mb-3">
                        Copia este script y pégalo en la web del cliente para mostrar el buscador de comprobantes.
                    </p>

                    <div class="form-group mb-3">
                        <label class="control-label" for="consultation_embed_code">Script de integración</label>
                        <div class="bg-dark text-light rounded p-3 position-relative border" style="border-color: #111827 !important; box-shadow: 0 6px 16px rgba(2,6,23,.24);">
                            <pre id="consultation_embed_code" class="mb-0 text-break" style="white-space: pre-wrap; word-break: break-word; font-size: 12px; color: #f8fafc;">{{ consultationEmbedCode }}</pre>
                            <el-button
                                size="mini"
                                type="primary"
                                icon="el-icon-document-copy"
                                class="position-absolute"
                                style="top: 10px; right: 10px;"
                                @click="copyConsultationCode(consultationEmbedCode)"
                            >Copiar</el-button>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Este es el código que el cliente debe pegar en su web para mostrar el buscador embebido.
                        </small>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-group mb-0 position-relative">
                                <label class="control-label" for="consultation_url">URL pública</label>
                                <el-input class="url-input" id="consultation_url" v-model="consultationUrl" readonly>
                                </el-input>
                                <span class="url-copy-icon" @click="copyConsultationCode(consultationUrl)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                </span>
                                <small class="text-muted d-block mt-1">
                                    URL por cliente (tenant): incluye el dominio del cliente en la ruta del widget.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0 position-relative">
                                <label class="control-label" for="consultation_api_url">API pública</label>
                                <el-input class="url-input" id="consultation_api_url" v-model="consultationApiUrl" readonly>
                                </el-input>
                                <span class="url-copy-icon" @click="copyConsultationCode(consultationApiUrl)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                </span>
                                <small class="text-muted d-block mt-1">
                                    Consumir con <strong>POST</strong> enviando los datos en JSON.
                                </small>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">
                        El script detecta automáticamente el dominio principal y muestra el formulario dentro de un iframe.
                    </small>
            </div>
        </div>
    </div>
</template>
<style>
.url-input input {
    padding-right: 34px !important;
}
.url-copy-icon{
    position: absolute;
    top: 29px;
    right: 10px;
    cursor: pointer;
}
</style>
<script>
export default {
    props: ['configuration'],
    created() {
        this.initForm();
        this.buildConsultationIntegration();
    },
    data() {
        return {
            loading_submit: false,
            loading_test: false,
            resource: 'configurations',
            errors: {},
            consultationUrl: '',
            consultationApiUrl: '',
            consultationEmbedCode: '',
            form: {
                mail_host: '',
                mail_port: '',
                mail_username: '',
                mail_password: '',
                mail_encryption: ''
            }
        }
    },
    methods: {
        initForm() {
            this.form = {
                mail_host: this.configuration.mail_host || '',
                mail_port: this.configuration.mail_port || '',
                mail_username: this.configuration.mail_username || '',
                mail_password: this.configuration.mail_password || '',
                mail_encryption: this.configuration.mail_encryption || ''
            };
        },
        buildConsultationIntegration() {
            const origin = globalThis.location.origin;
            const slug = globalThis.location.hostname;
            this.consultationUrl = `${origin}/consultas/widget/${slug}`;
            this.consultationApiUrl = `${origin}/api/consultas/search`;
            this.consultationEmbedCode = [
                '<!-- Consulta de comprobantes — copie y pegue esta línea donde quiera mostrar el formulario -->',
                '<script src="' + origin + '/consultas/embed.js?tenant=' + slug + '"><' + '/script>',
            ].join('\n');
        },
        openMailManual() {
            globalThis.open('https://manual.pro8.uio.la/guias-adicionales/Configuracion/configuracion-smtp-segura', '_blank', 'noopener');
        },
        copyConsultationCode(text) {
            if (navigator.clipboard && globalThis.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(() => this.$message.success('Código copiado al portapapeles'))
                    .catch(() => this.fallbackCopy(text));
            } else {
                this.fallbackCopy(text);
            }
        },
        fallbackCopy(text) {
            const el = document.createElement('textarea');
            el.value = text;
            el.style.position = 'fixed';
            el.style.opacity = '0';
            document.body.appendChild(el);
            el.select();
            try {
                document.execCommand('copy');
                this.$message.success('Código copiado al portapapeles');
            } catch (error) {
                console.error(error);
                this.$message.error('No se pudo copiar automáticamente. Seleccione el texto manualmente.');
            }
            el.remove();
        },
        submit() {
            this.loading_submit = true;
            // Logic to submit the form data
            // After submission, reset loading state
            this.$http.post(`${this.resource}/emails`, this.form)
                .then(response => {
                    if (response.data.success) {
                        return this.$message.success('Configuración guardada correctamente');
                    }
                })
                .catch(error => {
                    this.errors = error.response.data.errors || {};
                    this.$message.error('Error al guardar la configuración');
                })
                .finally(() => {
                    this.loading_submit = false;
                });

        },
        testEmail() {
            this.loading_test = true;
            this.$http.post(`${this.resource}/emails/test`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    } else {
                        this.$message.error(response.data.message || 'Error al enviar correo de prueba');
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Error al enviar correo de prueba';
                    this.$message.error(message);
                })
                .finally(() => {
                    this.loading_test = false;
                });
        },
    }
}
</script>
