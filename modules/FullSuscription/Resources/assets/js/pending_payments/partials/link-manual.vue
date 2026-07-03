<template>
    <el-dialog
        :title="'Recordatorio manual de pago'"
        :visible.sync="show"
        @open="open"
        @close="close"
    >

        <div class="mt-2">
                <h4>
                    Plantilla de mensaje
                </h4>
                <p style="opacity: 0.7;">
                    Personaliza el mensaje que recibirán los clientes. Usa variables para datos dinámicos.
                </p>
                <div class="h-auto">
                            <vue-ckeditor
                                v-model="message"
                                :editors="editors"
                                type="classic"
                                @ready="onEditorReady"
                            ></vue-ckeditor>
                </div>
                <div class="mt-2">
                    <nav>
                        <small class="text-muted">Variables disponibles — haz clic para insertar en el mensaje:</small>
                        <div class="d-flex align-items-center justify-content-center flex-wrap gap-1 mt-1">
                            <el-tooltip v-for="v in variables" :key="v.key" :content="v.tooltip" placement="bottom" effect="light">
                                <el-button size="mini" @click="insertVariable(v.key)">{{ v.key }}</el-button>
                            </el-tooltip>
                        </div>
                    </nav>
                </div>

                <div class="col-12 row">
                    <div class="col-8">
                    </div>

                    <div class="col-4 d-flex justify-content-end">
                        <el-button type="primary" class="mt-2" @click="saveMessage" :disabled="sending">Guardar mensaje</el-button>
                        <el-button type="danger" class="mt-2" @click="sendNotify" :disabled="sending" :loading="sending">
                            {{ sending ? `Enviando ${sentCount}/${totalCount}` : 'Enviar links' }}
                        </el-button>
                    </div>
                </div>
        </div>
    </el-dialog>
</template>
<script>
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import VueCkeditor from "vue-ckeditor5";

export default {
    props: [
        'title',
        'show',
        'multipleIds',
    ],
    data() {
        return {
            message: '',
            resource: '/full-suscription/pending-payments',
            editors: {
                classic: ClassicEditor
            },
            sending: false,
            sentCount: 0,
            totalCount: 0,
            editorInstance: null,
            variables: [
                { key: '{{ plan_nombre }}',       tooltip: 'Nombre del plan de suscripción del cliente' },
                { key: '{{ cliente }}',            tooltip: 'Nombre completo del cliente destinatario' },
                { key: '{{ order_id }}',           tooltip: 'Número identificador de la orden de pago' },
                { key: '{{ order_total }}',        tooltip: 'Monto total a pagar de la orden' },
                { key: '{{ order_payment_url }}',  tooltip: 'Enlace directo para que el cliente realice el pago' },
            ],
        }
    },
    components: {
        "vue-ckeditor": VueCkeditor.component,
    },
    created() {

    },
    methods: {
        saveMessage() {
            this.$http.post(`${this.resource}/save-message`, {
                message: this.message,
            }).then( response => {
                this.$message.success({
                    type: 'success',
                    message: 'Mensaje guardado correctamente'
                });
                this.close();
            });
        },
        close() {
            this.$emit('update:show', false);
        },
        getTables() {
            this.$http.get(`${this.resource}/tables`).then( response => {
                this.status_types = response.data.status_types;
            });
        },
        loadConfiguration() {
            this.$http.get(`${this.resource}/get-message`).then( response => {
                this.message = response.data.message;
            });
        },
        async sendNotify(){
            let ids = this.multipleIds.map(row => parseInt(row.id));
            this.totalCount = ids.length;
            this.sentCount = 0;
            this.sending = true;

            for (const id of ids) {
                try {
                    await this.$http.post(`${this.resource}/${id}/send-notify`);
                    this.sentCount++;
                } catch (e) {
                    this.$message.error(`Error al enviar notificación de la orden #${id}`);
                }
            }

            this.sending = false;
            this.$message.success(`${this.sentCount} notificaciones enviadas correctamente`);
            this.$eventHub.$emit('refresh-records');
            this.close();
        },
        onEditorReady(editor) {
            this.editorInstance = editor;
        },
        insertVariable(variable) {
            if (this.editorInstance) {
                this.editorInstance.model.change(writer => {
                    const position = this.editorInstance.model.document.selection.getFirstPosition();
                    writer.insertText(` ${variable} `, position);
                });
                this.editorInstance.editing.view.focus();
            } else {
                this.message += ` ${variable} `;
            }
        },
        open() {
            this.getTables();
            this.loadConfiguration();
            this.sending = false;
            this.sentCount = 0;
            this.totalCount = 0;
        }
    }
}
</script>
