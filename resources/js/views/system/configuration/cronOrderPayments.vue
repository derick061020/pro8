<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Configuración de tareas automáticas </h3>
        </div>
            <form class="row card-body px-0" autocomplete="off" @submit.prevent="submit">
                <div class="col-md-12">
                    <div class="form-group">
                        <el-checkbox @change="submit" v-model="form.active_cron">
                            Activar tareas automáticas CRON
                        </el-checkbox>
                        <div class="ms-2" >
                        <span style="opacity: 0.4;">
                            Habilita el sistema para generar y enviar recordatorios de pagos de pago automáticamente.
                        </span>
                        </div>
                    </div>

                    <div v-if="form.active_cron" class="col-md-12 container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group position-relative form-days-cron">
                                    <label class="control-label">
                                        Dias antes del vencimiento
                                    </label>
                                    <el-input type="number" :min="1" :max="25"  placeholder="Ingrese su dia" v-model="form.day_before_due">                                        
                                    </el-input>
                                    <span class="day-span-cron">Dias</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">
                                        Hora para generar la orden
                                    </label>
                                    <el-time-select
                                        v-model="form.hour_generate_payment_order"
                                        class="w-100"
                                        :picker-options="{
                                            start: '00:00',
                                            end: '23:59',
                                            step: '01:00'   // ← salto de 1 hora
                                        }"
                                        placeholder="Ingrese su hora"
                                        >
                                    </el-time-select>

                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <el-switch v-model="form.send_notification_cron" @change="submit">
                                </el-switch>
                                <span>
                                    Habilitar notificaciones:
                                </span>
                            </div>
                            <div v-if="form.send_notification_cron">
                                <span style="opacity: 0.4;">
                                    Luego de haberse generado las ordenes, las notificaciones se enviarán automáticamente por WhatsApp y Email (debe configurar previamente ambos servicios).
                                </span>

                                <div class="mt-2">
                                    <h4>
                                        Plantilla de mensaje
                                    </h4>
                                    <p style="opacity: 0.7;"><span style="border-bottom: 2px solid #409eff !important; opacity: 1 !important; padding-bottom: 1px; font-weight: 500;">Personaliza el mensaje que recibirán los clientes.</span> Usa variables para datos dinámicos.</p>
                                    
                                    <div class="editor-container message-editor-container">
                                        <!-- Editor Único (ContentEditable) -->
                                        <div
                                            ref="editor"
                                            class="atomic-editor"
                                            contenteditable="true"
                                            spellcheck="false"
                                            @input="onInput"
                                            @paste.prevent="onPaste"
                                            placeholder="Escribe aquí tu mensaje. Las variables se resaltarán automáticamente..."
                                        ></div>
                                    </div>

                                    <div class="mt-3">
                                        <nav style="opacity: 1;">
                                            <span class="text-secondary small d-block mb-2">Variables disponibles (haz clic para insertar):</span>
                                                <div class="d-flex align-items-center flex-wrap gap-2 variables-container">
                                                    <el-tooltip
                                                        v-for="(label, key) in variablesDisponibles"
                                                        :key="key"
                                                        :content="label"
                                                        placement="bottom"
                                                        effect="light"
                                                    >
                                                        <el-button
                                                            size="mini"
                                                            type="primary"
                                                            plain
                                                            class="variable-btn"
                                                            @click="insertarVariable(key)"
                                                        >
                                                            <span v-text="'{{' + key + '}}'"></span>
                                                            <span
                                                                v-if="conteoVariables[key] > 0"
                                                                class="variable-badge"
                                                            >
                                                                {{ conteoVariables[key] }}
                                                            </span>
                                                        </el-button>
                                                    </el-tooltip>
                                                </div>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-end pt-2">
                                <el-button type="primary" @click.prevent="manualSubmit" :loading="loading_submit">Guardar Configuración</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</template>

<script>
export default {
    props: ['configuration'],
    data() {
        return {
            form: {
                active_cron: false,
                hour_generate_payment_order: '',
                day_before_due: '',
                send_notification_cron: false,
                qr_api_msg: ''
            },
            resource : 'configurations',
            loading_submit: false,
            variablesDisponibles: {
                nombre: 'Nombre del cliente',
                plan: 'Nombre del plan',
                precios: 'Precio del plan',
                fecha_vencimiento: 'Fecha de vencimiento'
            },
        };
    },

    computed: {
        conteoVariables() {
            const counts = {};
            const texto = this.form.qr_api_msg || '';

            Object.keys(this.variablesDisponibles).forEach(key => {
                const token = '{{' + key + '}}';
                const regex = new RegExp(this.escapeRegExp(token), 'g');
                const matches = texto.match(regex);
                counts[key] = matches ? matches.length : 0;
            });

            return counts;
        }
    },

    methods: {
        /**
         * Carga el mensaje plano y lo convierte en HTML con tags atómicos.
         */
        loadMessage(text) {
            if (!text) return '';
            const allowedVariables = Object.keys(this.variablesDisponibles);

            // Normaliza formato viejo @variable_x → {{x}}
            text = text.replace(/@variable_(\w+)/g, '{{$1}}');

            // (lo que ya tenías) convierte {{x}} en chips
            let html = text.replace(/\{\{\s*([^}]+?)\s*\}\}/g, (match, variable) => {
                const v = variable.trim();
                if (allowedVariables.includes(v)) {
                    return `<span class="variable-tag" contenteditable="false">{{${v}}}</span>`;
                }
                return match;
            });

            return html.replace(/\n/g, '<br>');
        },

        /**
         * Sincroniza el contenido del editor HTML hacia el modelo de datos plano.
         */
        syncMessage() {
            const el = this.$refs.editor;
            if (!el) return;
            
            // Usamos un div temporal para procesar el contenido sin afectar el DOM real
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = el.innerHTML;
            
            // Los saltos de línea en contenteditable pueden ser <br>, <div> o <p>
            // innerText unifica esto a texto plano con saltos de línea \n
            this.form.qr_api_msg = tempDiv.innerText;
        },

        onInput() {
            this.syncMessage();
        },

        /**
         * Maneja el pegado de texto para asegurar que solo entre texto plano.
         */
        onPaste(e) {
            const text = (e.originalEvent || e).clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
            this.syncMessage();
        },

        /**
         * Inserta una variable como un span atómico en la posición actual del cursor.
         */
        insertarVariable(key) {
            const el = this.$refs.editor;
            if (!el) return;
            
            el.focus();
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            
            const range = selection.getRangeAt(0);
            range.deleteContents();
            
            // Crear el token atómico
            const span = document.createElement('span');
            span.className = 'variable-tag';
            span.contentEditable = 'false';
            span.innerText = `{{${key}}}`;
            
            // Insertar y posicionar cursor
            range.insertNode(span);
            range.setStartAfter(span);
            range.setEndAfter(span);
            selection.removeAllRanges();
            selection.addRange(range);
            
            this.syncMessage();
        },

        submit(){
            this.syncMessage();
            this.loading_submit = true;
            this.$http.post(`${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        return this.$message.success('Configuración guardada correctamente');
                    }
                })
                .catch(error => {
                    this.$message.error('Error al guardar la configuración');
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },

        manualSubmit() {
            try {
                this.syncMessage();
                this.loading_submit = true;
                const payload = { ...this.form };
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
                const axiosConfig = csrfToken ? { headers: { 'X-CSRF-TOKEN': csrfToken.content } } : {};

                this.$http.post(`${this.resource}`, payload, axiosConfig)
                    .then(response => {
                        if (response && response.data && response.data.success) {
                            this.$message.success('Configuración de CRON guardada correctamente');
                            setTimeout(() => { window.location.reload(); }, 1200);
                        } else {
                            const msg = (response && response.data) ? response.data.message : 'Error al guardar';
                            this.$message.error(msg);
                        }
                    })
                    .catch(err => {
                        console.error("Error al guardar:", err);
                        this.$message.error('Error al guardar la configuración');
                    })
                    .finally(() => {
                        this.loading_submit = false;
                    });
            } catch (e) {
                console.error(e);
                this.loading_submit = false;
            }
        },

        escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    },

    mounted() {
        this.$nextTick(() => {
            if (this.$refs.editor) {
                // Cargar el mensaje inicial convertido a HTML
                this.$refs.editor.innerHTML = this.loadMessage(this.form.qr_api_msg);
            }
            document.execCommand('defaultParagraphSeparator', false, 'br');
        });
    },

    created() {
        if (this.configuration) {
            this.form.day_before_due = this.configuration.day_before_due || 3;
            this.form.hour_generate_payment_order = this.configuration.hour_generate_payment_order || '09:00:00';
            this.form.active_cron = Boolean(this.configuration.active_cron);
            this.form.send_notification_cron = Boolean(this.configuration.send_notification_cron);
            this.form.qr_api_msg = this.configuration.qr_api_msg || '';
        }
    }
}
</script>

<style scoped>
.editor-container {
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    background-color: #fff;
    transition: border-color 0.2s;
}

.editor-container:focus-within {
    border-color: #409eff;
}

.atomic-editor {
    min-height: 150px;
    max-height: 300px;
    padding: 12px;
    overflow-y: auto;
    outline: none;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.6;
    color: #212529;
    white-space: pre-wrap;
    word-break: break-word;
}

.atomic-editor:empty:before {
    content: attr(placeholder);
    color: #909399;
    pointer-events: none;
    display: block;
}

.variable-btn {
    margin-bottom: 5px;
}

.variable-badge {
    background: #409eff;
    color: white;
    border-radius: 999px;
    padding: 0 6px;
    font-size: 0.7rem;
    margin-left: 6px;
}

.variables-container {
    padding: 10px 0;
}
</style>

<style>
/* Estilos Globales para el Editor (No Scoped para permitir renderizado de tags dinámicos) */
.message-editor-container .variable-tag {
    background-color: #e2e8f0 !important;
    border: 1px solid #cbd5e1 !important;
    color: #1e293b !important;
    padding: 2px 8px !important;
    border-radius: 6px !important;
    font-weight: 600 !important;
    display: inline-block !important;
    margin: 0 2px !important;
    line-height: normal !important;
    user-select: all !important;
    opacity: 1 !important;
}
</style>