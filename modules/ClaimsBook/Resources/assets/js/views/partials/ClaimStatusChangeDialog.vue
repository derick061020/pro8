<template>
    <!-- Dialog unificado para resolución final y reapertura de reclamo -->
    <el-dialog
        :title="dialogTitle"
        :visible.sync="internalVisible"
        :width="mode === 'resolution' ? '520px' : '420px'"
        :close-on-click-modal="false"
        append-to-body
        @close="$emit('cancel')"
    >
        <!-- Modo resolución: requiere texto obligatorio para cerrar el reclamo -->
        <template v-if="mode === 'resolution'">
            <p class="csd-hint">
                Este estado cierra el reclamo. Ingrese la resolución o respuesta al reclamante.
            </p>
            <vue-ckeditor
                :editors="editors"
                type="classic"
                v-model="resolution"
                :config="editorConfig"
            />

            <!-- Adjuntos de respuesta (opcional) -->
            <div class="csd-upload-section">
                <p class="csd-hint">Adjuntar archivos de respuesta (opcional)</p>
                <el-upload
                    ref="uploadRef"
                    action="#"
                    :auto-upload="false"
                    :on-change="handleFileChange"
                    :on-remove="handleFileRemove"
                    :limit="5"
                    :on-exceed="() => $message.warning('Máximo 5 archivos permitidos')"
                    multiple
                    accept=".pdf,.png,.jpg,.jpeg,.mp4"
                >
                    <el-button size="small" icon="el-icon-upload2">Adjuntar archivos</el-button>
                    <div slot="tip" class="el-upload__tip">PDF, PNG, JPG, MP4 · máx. 32 MB por archivo</div>
                </el-upload>
            </div>
        </template>

        <!-- Modo reapertura: advertencia de que el reclamo ya fue cerrado -->
        <template v-if="mode === 'reopen'">
            <el-alert
                title="El reclamo ya fue cerrado. ¿Desea reabrirlo cambiando el estado?"
                type="warning"
                :closable="false"
                show-icon
            ></el-alert>
        </template>

        <div slot="footer" class="d-flex justify-content-end">
            <el-button size="small" @click="cancel">Cancelar</el-button>
            <el-button
                size="small"
                :type="mode === 'reopen' ? 'warning' : 'primary'"
                :loading="loading"
                class="ms-2"
                @click="confirm"
            >
                {{ confirmLabel }}
            </el-button>
        </div>
    </el-dialog>
</template>
<style>

</style>
<script>
import 'ckeditor5/ckeditor5.css';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import CKEditor from 'vue-ckeditor5';

export default {
    name: 'ClaimStatusChangeDialog',

    components: {
        'vue-ckeditor': CKEditor.component
    },

    props: {
        // Controla la visibilidad del dialog (.sync)
        visible: {
            type: Boolean,
            default: false
        },
        // 'resolution' = cerrado con texto obligatorio | 'reopen' = advertencia de reapertura
        mode: {
            type: String,
            default: 'resolution',
            validator: v => ['resolution', 'reopen'].includes(v)
        },
        // Estado de carga del botón confirmar
        loading: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            resolution: '',
            editors: {
                classic: ClassicEditor
            },
            editorConfig: {
                licenseKey: 'GPL',
                toolbar: [
                    'heading',
                    '|',
                    'bold', 'italic', 'link',
                    'bulletedList', 'numberedList',
                    '|',
                    'blockQuote',
                    'undo', 'redo',
                    '|',
                    'sourceEditing'
                ]
            }
        }
    },

    computed: {
        internalVisible: {
            get() { return this.visible },
            set(val) { this.$emit('update:visible', val) }
        },
        dialogTitle() {
            return this.mode === 'resolution' ? 'Registrar resolución final' : 'Advertencia'
        },
        confirmLabel() {
            return this.mode === 'resolution' ? 'Confirmar y cerrar' : 'Sí, reabrir'
        }
    },

    watch: {
        // Limpiar campos al cerrar el dialog
        visible(val) {
            if (!val) {
                this.resolution = ''
                this.$nextTick(() => {
                    this.$refs.uploadRef && this.$refs.uploadRef.clearFiles()
                })
            }
        }
    },

    methods: {
        confirm() {
            // Recopilar archivos del uploader (solo disponibles en modo resolución)
            const files = (this.$refs.uploadRef ? this.$refs.uploadRef.uploadFiles : [])
            this.$emit('confirm', { resolution: this.resolution, files })
        },

        cancel() {
            this.$emit('update:visible', false)
            this.$emit('cancel')
        },

        // Valida tamaño máximo al agregar un archivo
        handleFileChange(file, fileList) {
            const maxSize = 32 * 1024 * 1024
            if (file.raw && file.raw.size > maxSize) {
                this.$message.error(`"${file.name}" supera el tamaño máximo de 32 MB`)
                const idx = fileList.findIndex(f => f.uid === file.uid)
                if (idx !== -1) fileList.splice(idx, 1)
            }
        },

        handleFileRemove() {
            // El uploader gestiona la lista internamente
        },
    }
}
</script>

<style scoped>
.csd-hint {
    font-size: 13px;
    color: #606266;
    margin-bottom: 12px;
}
.csd-upload-section {
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid #ebeef5;
}
</style>

