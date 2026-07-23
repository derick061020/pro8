<template>
<div class="card">
    <div class="card-header bg-info bg-info-customer-admin">
        <h3 class="my-0">Otras configuraciones</h3>
    </div>
    <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label">
                        Habilitar Registro de invitados
                        <el-tooltip class="item"
                                    content="Permitir a los clientes registrarse sin necesidad de un usuario administrador que los cree (se necesita configurar el correo previamente)."
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    <div :class="{'has-danger': errors.enable_guest_register}"
                         class="form-group">
                        <el-switch v-model="form.enable_guest_register"
                                   active-text="Si"
                                   inactive-text="No"
                                   @change="submit"></el-switch>
                        <small v-if="errors.enable_guest_register"
                               class="form-control-feedback"
                               v-text="errors.enable_guest_register[0]"></small>
                    </div>
                </div>

                <div class="col-md-6" v-if="form.enable_guest_register">
                    <label class="control-label">
                        Validar RUC en el registro
                        <el-tooltip class="item"
                                    content="Si está activo, el RUC deberá existir en SUNAT (validación obligatoria). Si está desactivado, se permite registrar con un RUC ficticio."
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    <div :class="{'has-danger': errors.validate_ruc_register}"
                         class="form-group">
                        <el-switch v-model="form.validate_ruc_register"
                                   active-text="Si"
                                   inactive-text="No"
                                   @change="submit"></el-switch>
                        <small v-if="errors.validate_ruc_register"
                               class="form-control-feedback"
                               v-text="errors.validate_ruc_register[0]"></small>
                    </div>
                </div>
                
                <!-- URL de registro simplificada (visible solo cuando está habilitado) -->
                <div class="col-md-6" v-if="form.enable_guest_register">
                    <div class="form-group mb-0 position-relative">
                        <label class="control-label">
                            URL de registro
                            <el-tooltip class="item"
                                        content="Enlace simplificado para compartir con potenciales clientes. Redirige al formulario de registro."
                                        effect="dark"
                                        placement="top-start">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                        </label>
                        <el-input v-model="registerUrl" readonly>
                        </el-input>
                        <span class="url-copy-icon" @click="copyUrl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                        </span>
                        <small class="text-success" v-if="urlCopied">¡URL copiada al portapapeles!</small>
                    </div>
                </div>

                <!-- Selector de plan para nuevos registrados -->
                <div class="col-md-6" v-if="form.enable_guest_register">
                    <div :class="{'has-danger': errors.guest_register_plan_id}" class="form-group">
                        <label class="control-label">
                            Plan por defecto para nuevos registrados
                            <el-tooltip class="item"
                                        content="Selecciona qué plan se asignará automáticamente a los nuevos usuarios que se registren."
                                        effect="dark"
                                        placement="top-start">
                                <i class="fa fa-info-circle"></i>
                            </el-tooltip>
                        </label>
                        <el-select v-model="form.guest_register_plan_id" @change="submit" style="width: 100%;" placeholder="Seleccione un plan">
                            <el-option
                                v-for="plan in allPlans"
                                :key="plan.id"
                                :label="plan.name + ' - S/' + plan.pricing + ' (' + plan.limit_documents + ' docs / ' + plan.limit_users + ' users)'"
                                :value="plan.id">
                            </el-option>
                        </el-select>
                        <small v-if="errors.guest_register_plan_id"
                               class="form-control-feedback"
                               v-text="errors.guest_register_plan_id[0]"></small>
                    </div>
                </div>

                <div class="col-md-6">

                    <label class="control-label">
                        Mostrar publicidad
                        <el-tooltip class="item"
                                    content="Se visualizará la imágen cargada en el header de todos los clientes."
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    
                    <div :class="{'has-danger': errors.tenant_show_ads}"
                            class="form-group">
                        <el-switch v-model="form.tenant_show_ads"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.tenant_show_ads"
                                class="form-control-feedback"
                                v-text="errors.tenant_show_ads[0]"></small>
                    </div>
                </div>

                <div class="col-md-6" v-if="form.tenant_show_ads">
                    <div class="form-group">
                        <label class="control-label">Imágen para publicidad</label>
                        <el-input v-model="form.tenant_image_ads" :readonly="true">
                            <el-upload
                                slot="append"
                                :headers="headers"
                                :on-success="successUpload"
                                :on-error="errorUpload"
                                :show-file-list="false"
                                action="/configurations/upload-tenant-ads">
                                <el-button icon="el-icon-upload" type="primary"></el-button>
                            </el-upload>
                        </el-input>
                        <div class="sub-title text-danger"><small>Se recomienda resoluciones 500x50</small>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-6">
                    
                    <label class="control-label">
                        Habilitar contraseña segura
                        <el-tooltip class="item"
                                    content="Se solicitará una contraseña segura (cumplir patrón) al registrar cliente"
                                    effect="dark"
                                    placement="top-start">
                            <i class="fa fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    
                    <div :class="{'has-danger': errors.regex_password_client}"
                            class="form-group">
                        <el-switch v-model="form.regex_password_client"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.regex_password_client"
                                class="form-control-feedback"
                                v-text="errors.regex_password_client[0]"></small>
                    </div>
                </div>
            </div>
            <!-- <div class="form-actions text-right pt-2">
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div> -->
        </form>
    </div>
</div>
</template>

<script>
export default {
    props: {
        plans: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            headers: headers_token,
            loading_submit: false,
            resource: 'configurations',
            errors: {},
            urlCopied: false,
            loadedPlans: [],
            form: {
                enable_guest_register: true,
                validate_ruc_register: true,
                regex_password_client: false,
                tenant_show_ads: false,
                tenant_image_ads: null,
                guest_register_plan_id: null,
            }
        }
    },
    computed: {
        registerUrl() {
            return window.location.origin + '/register';
        },
        allPlans() {
            // Usar planes pasados desde PHP (prop) o los cargados desde API
            return this.plans.length > 0 ? this.plans : this.loadedPlans;
        }
    },
    async created() {
        this.initForm()
        this.loadPlans()
        this.loadConfiguration()
    },
    methods: {
        copyUrl() {
            const urlToCopy = this.registerUrl;
            
            // Intentar copiar usando navigator.clipboard
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(urlToCopy)
                    .then(() => {
                        this.handleCopySuccess();
                    })
                    .catch((err) => {
                        console.error('Error al copiar:', err);
                        this.fallbackCopy(urlToCopy);
                    });
            } else {
                // Fallback para navegadores sin soporte o contexto no seguro
                this.fallbackCopy(urlToCopy);
            }
        },
        
        fallbackCopy(text) {
            // Crear un textarea temporal
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    this.handleCopySuccess();
                } else {
                    this.$message.error('No se pudo copiar la URL');
                }
            } catch (err) {
                console.error('Error en fallback copy:', err);
                this.$message.error('Error al copiar al portapapeles');
            }
            
            document.body.removeChild(textArea);
        },
        
        handleCopySuccess() {
            this.urlCopied = true;
            this.$message.success('¡URL copiada al portapapeles!');
            setTimeout(() => {
                this.urlCopied = false;
            }, 3000);
        },
        loadPlans() {
            // Solo cargar desde API si no se recibieron planes desde PHP
            if (this.plans.length === 0) {
                this.$http.get('/plans/records')
                    .then(response => {
                        this.loadedPlans = response.data.data || [];
                    })
                    .catch(error => {
                        console.error('Error loading plans:', error);
                        this.loadedPlans = [];
                    });
            }
        },
        loadConfiguration() {
            this.$http.get(`/${this.resource}/get-other-configuration`)
                .then(response => {
                    const data = response.data;
                    this.form.enable_guest_register = data.enable_guest_register;
                    this.form.validate_ruc_register = data.validate_ruc_register;
                    this.form.regex_password_client = data.regex_password_client;
                    this.form.tenant_show_ads = data.tenant_show_ads;
                    this.form.tenant_image_ads = data.tenant_image_ads;
                    this.form.guest_register_plan_id = data.guest_register_plan_id;
                })
                .catch(error => {
                    console.error('Error loading configuration:', error);
                });
        },
        successUpload(response, file, fileList) 
        { 
            if (response.success) {
                this.$message.success(response.message)
                this.form.tenant_image_ads = response.name
            } else {
                this.$message({message: 'Error al subir el archivo', type: 'error'})
            }
        },
        errorUpload(error)
        {
            this.$message({message: 'Error al subir el archivo', type: 'error'})
        },
        initForm() {
            this.errors = {}
            this.form = {
                enable_guest_register: true,
                validate_ruc_register: true,
                regex_password_client: false,
                tenant_show_ads: false,
                tenant_image_ads: null,
                guest_register_plan_id: null,
            }
        },
        submit() {
            this.loading_submit = true
            this.$http
                .post(`/${this.resource}/other-configuration`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        console.log(error)
                    }
                })
                .then(() => {
                    this.loading_submit = false
                })
        },
    }
}
</script>
