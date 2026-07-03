<template>
<div class="card">
    <div class="card-header bg-info bg-info-customer-admin">
        <h3 class="my-0">Temas del sistema</h3>
    </div>
    <div class="card-body">

        <div class="fw-bold text-muted mb-3">
            <span>Temas disponibles</span>
            <el-tag type="primary" class="ms-1">{{ skins.length }}</el-tag>
        </div>

        <div v-if="!skins || skins.length === 0"
             class="text-center py-4 px-2 text-muted"
             style="border: 1px dashed #dcdfe6; border-radius: 4px;">
            <i class="el-icon-picture-outline" style="font-size: 24px; margin-bottom: 10px;"></i>
            <p style="margin: 0; font-size: 14px;">No hay temas cargados aún</p>
        </div>

        <div v-else>
            <div class="border rounded table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th class="text-muted fw-normal ps-3 py-2 text-center" style="font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase; width: 110px;">Activo</th>
                            <th class="text-muted fw-normal py-2" style="font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase;">Tema</th>
                            <th class="text-muted fw-normal py-2" style="font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase; width: 140px;">Estado</th>
                            <th class="text-muted fw-normal text-end pe-3 py-2" style="font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="skin in skins" :key="skin.id"
                            class="skin-row"
                            :class="skin.is_tenant_default ? 'table-background' : ''">
                            <td class="align-middle ps-3 py-2 text-center">
                                <el-tooltip :content="skin.is_visible_to_clients ? 'Desactivar plantilla para clientes' : 'Activar plantilla para clientes'" placement="top">
                                    <el-switch
                                        :value="skin.is_visible_to_clients"
                                        :loading="loading_toggle_visibility === skin.id"
                                        @change="toggleSkinVisibility(skin)">
                                    </el-switch>
                                </el-tooltip>
                            </td>
                            <td class="align-middle py-2">
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="fw-bold" style="font-size: 13px;">{{ skin.name }}</span>
                                    <small class="text-muted" style="font-size: 11px;">{{ skin.is_default ? 'sistema' : 'personalizado' }}</small>
                                    <el-tag v-if="skin.is_replaced" size="mini" type="warning">reemplazado</el-tag>
                                </div>
                                <div class="mt-1 text-muted d-flex align-items-center gap-1" style="font-size: 11px;">
                                    <i class="el-icon-document"></i>
                                    <template v-if="skin.is_replaced">
                                        <span style="text-decoration: line-through;">{{ skin.filename }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#e6a23c"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M15 16l4 -4"/><path d="M15 8l4 4"/></svg>
                                        <span class="fw-medium text-warning">{{ skin.active_filename }}</span>
                                    </template>
                                    <template v-else>
                                        <span>{{ skin.filename }}</span>
                                    </template>
                                </div>
                            </td>
                            <td class="align-middle py-2">
                                <div class="d-flex gap-1 flex-wrap align-items-center">
                                    <el-tooltip
                                        :content="skin.is_tenant_default ? 'Tema predeterminado · Click para forzar a empresas existentes' : 'Aplicar tema (predeterminado o forzar)'"
                                        placement="top">
                                        <button
                                            class="apply-skin-btn"
                                            :class="{ 'apply-skin-btn--active': skin.is_tenant_default }"
                                            @click="openApplyDialog(skin)">
                                            <svg v-if="skin.is_tenant_default" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="margin-top:-1px;display:inline-block"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z"/></svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245"/></svg>
                                            {{ skin.is_tenant_default ? 'Default' : 'Aplicar' }}
                                        </button>
                                    </el-tooltip>
                                </div>
                            </td>
                            <td class="align-middle py-2 pe-3">
                                <div class="d-flex justify-content-end">
                                    <el-tooltip content="Descargar" placement="top">
                                        <el-button size="mini" plain @click="downloadSkin(skin)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                        </el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="skin.is_default" content="Reemplazar" placement="top">
                                        <el-button size="mini" plain @click.prevent="openReplaceDialog(skin)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-upload"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                        </el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="skin.is_replaced" content="Restaurar original" placement="top">
                                        <el-button size="mini" plain :loading="loading_revert" @click.prevent="confirmRevert(skin)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-back-up"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 14l-4 -4l4 -4" /><path d="M5 10h11a4 4 0 1 1 0 8h-1" /></svg>
                                        </el-button>
                                    </el-tooltip>
                                    <el-tooltip v-if="!skin.is_default" content="Eliminar" placement="top">
                                        <el-button size="mini" plain type="danger" :loading="loading_delete" @click.prevent="confirmDelete(skin)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash text-danger"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </el-button>
                                    </el-tooltip>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="bg-light px-3 py-2 text-muted d-flex flex-wrap gap-3 mt-2 rounded">
                <span class="d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245"/></svg>
                    Aplicar
                    <small style="opacity:.7">(predeterminado o forzar a existentes)</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 11l5 5l5 -5"/><path d="M12 4l0 12"/></svg>
                    Descargar
                </span>
                <span class="d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg>
                    Reemplazar
                    <small style="opacity:.7">(solo sistema)</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4 -4l4 -4"/><path d="M5 10h11a4 4 0 1 1 0 8h-1"/></svg>
                    Restaurar
                    <small style="opacity:.7">(si fue reemplazado)</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                    Eliminar
                    <small style="opacity:.7">(solo personalizados)</small>
                </span>
            </div>
        </div>

        <el-divider>Subir nuevo tema</el-divider>

        <div>
            <p style="font-size: 12px;" class="mb-2">
                <i class="el-icon-warning-outline"></i> Solo se aceptan archivos <strong>.css</strong>. Los temas subidos se propagan a todos los tenants.
            </p>
            <el-upload
                ref="upload"
                :auto-upload="false"
                :multiple="false"
                :on-change="onFileChange"
                :on-remove="onFileRemove"
                :limit="1"
                drag
                accept=".css"
                action=""
                style="width: 100%;">
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">Arrastra tu archivo CSS aquí o <em>haz clic para subir</em></div>
            </el-upload>
        </div>

        <!-- Diálogo de conflicto de nombre -->
        <el-dialog
            title="Nombre de archivo en uso"
            :visible.sync="showRenameDialog"
            width="420px"
            :close-on-click-modal="false"
            @close="onRenameDialogClose">
            <div>
                <el-alert
                    :title="`El archivo &quot;${conflictFilename}&quot; ya existe en el sistema.`"
                    type="warning"
                    :closable="false"
                    show-icon
                    class="mb-3">
                </el-alert>
                <p style="font-size: 13px;" class="mb-2">Puedes subir el archivo con un nombre diferente:</p>
                <el-input
                    v-model="renameValue"
                    placeholder="Nuevo nombre (sin extensión)"
                    @keyup.enter.native="confirmRename">
                    <template slot="append">.css</template>
                </el-input>
                <p v-if="renameError" style="font-size: 12px; color: #f56c6c;" class="mt-1">{{ renameError }}</p>
            </div>
            <span slot="footer">
                <el-button @click="onRenameDialogClose">Cancelar</el-button>
                <el-button type="primary" :loading="loading_upload" @click="confirmRename">Subir con este nombre</el-button>
            </span>
        </el-dialog>

        <!-- Diálogo de aplicación de tema (default + forzar) -->
        <el-dialog
            :title="applyingSkin ? `Aplicar tema: ${applyingSkin.name}` : ''"
            :visible.sync="showApplyDialog"
            width="540px"
            :close-on-click-modal="false"
            @close="closeApplyDialog">
            <div v-if="applyingSkin">
                <p class="text-muted mb-3" style="font-size: 13px;">
                    Elige cómo aplicar este tema. Las dos opciones son independientes; puedes usar una, la otra o ambas.
                </p>

                <!-- Opción 1: Hacer predeterminado -->
                <div class="apply-option" :class="{ 'apply-option--done': applyingSkin.is_tenant_default }">
                    <div class="d-flex align-items-start">
                        <div class="apply-option__icon apply-option__icon--default">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z"/></svg>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="apply-option__title">Establecer como predeterminado</div>
                            <div class="apply-option__desc text-muted">
                                Las <strong>empresas nuevas</strong> que se creen a partir de ahora usarán este tema por defecto.
                                <span class="d-block text-muted mt-1" style="font-size: 11px;">No afecta a las empresas ya existentes.</span>
                            </div>
                        </div>
                    </div>
                    <div class="apply-option__action text-end mt-2">
                        <el-tag v-if="applyingSkin.is_tenant_default" type="success" size="mini">
                            <i class="el-icon-check"></i> Ya es el predeterminado
                        </el-tag>
                        <el-button
                            v-else
                            type="warning"
                            size="small"
                            :loading="loading_set_default"
                            :disabled="loading_force"
                            @click="executeSetDefault">
                            Hacer predeterminado
                        </el-button>
                    </div>
                </div>

                <!-- Opción 2: Forzar -->
                <div class="apply-option apply-option--danger mt-3">
                    <div class="d-flex align-items-start">
                        <div class="apply-option__icon apply-option__icon--force">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 3l0 7l6 0l-8 11l0 -7l-6 0l8 -11"/></svg>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="apply-option__title">Forzar en todas las empresas</div>
                            <div class="apply-option__desc text-muted">
                                Reemplaza el tema activo en <strong>todas las empresas existentes</strong>.
                                <span class="d-block text-muted mt-1" style="font-size: 11px;">Acción inmediata: los usuarios verán el cambio al recargar.</span>
                            </div>
                        </div>
                    </div>
                    <div class="apply-option__action text-end mt-2">
                        <el-button
                            type="danger"
                            plain
                            size="small"
                            :loading="loading_force"
                            :disabled="loading_set_default"
                            @click="executeForce">
                            Forzar tema
                        </el-button>
                    </div>
                </div>
            </div>
            <span slot="footer">
                <el-button @click="closeApplyDialog">Cerrar</el-button>
            </span>
        </el-dialog>

        <!-- Diálogo de reemplazo de tema por defecto -->
        <el-dialog
            :title="`Reemplazar tema: ${replacingSkin ? replacingSkin.name : ''}`"
            :visible.sync="showReplaceDialog"
            width="460px"
            :close-on-click-modal="false"
            @close="onReplaceDialogClose">
            <div v-if="replacingSkin">
                <el-alert
                    type="info"
                    :closable="false"
                    show-icon
                    class="mb-3">
                    <template slot="title">
                        El archivo original <strong>{{ replacingSkin.filename }}</strong> no se modificará.
                        Los tenants usarán el nuevo CSS con el mismo nombre <strong>"{{ replacingSkin.name }}"</strong>.
                        Para volver al original usa <em>Restaurar original</em>.
                    </template>
                </el-alert>
                <el-upload
                    ref="uploadReplace"
                    :auto-upload="false"
                    :multiple="false"
                    :on-change="onReplaceFileChange"
                    :on-remove="onReplaceFileRemove"
                    :limit="1"
                    drag
                    accept=".css"
                    action=""
                    style="width: 100%;">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">Arrastra el nuevo CSS aquí o <em>haz clic para seleccionar</em></div>
                </el-upload>
            </div>
            <span slot="footer">
                <el-button @click="onReplaceDialogClose">Cancelar</el-button>
                <el-button
                    type="primary"
                    :loading="loading_replace"
                    :disabled="!pendingReplaceFile"
                    @click="confirmReplace">
                    Reemplazar
                </el-button>
            </span>
        </el-dialog>

    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            skins: [],
            loading_delete: false,
            loading_upload: false,
            loading_replace: false,
            loading_revert: false,
            loading_sync: null,
            loading_set_default: false,
            loading_toggle_visibility: null,
            headers: headers_token,
            pendingFile: null,
            showRenameDialog: false,
            conflictFilename: '',
            renameValue: '',
            renameError: '',
            showReplaceDialog: false,
            replacingSkin: null,
            pendingReplaceFile: null,
            loading_force: false,
            showApplyDialog: false,
            applyingSkin: null,
        };
    },
    created() {
        this.loadSkins();
    },
    methods: {
        loadSkins(skins = null) {
            if (skins) {
                this.skins = skins;
                return;
            }
            this.$http.get('configurations/system-skins').then(response => {
                if (response.data.success) {
                    this.skins = response.data.skins;
                }
            });
        },

        downloadSkin(skin) {
            const a = document.createElement('a');
            a.href = '/storage/skins/' + skin.filename;
            a.download = skin.filename;
            a.click();
        },

        openApplyDialog(skin) {
            this.applyingSkin = skin;
            this.showApplyDialog = true;
        },
        closeApplyDialog() {
            if (this.loading_set_default || this.loading_force) return;
            this.showApplyDialog = false;
            this.applyingSkin = null;
        },

        executeSetDefault() {
            if (!this.applyingSkin) return;
            this.loading_set_default = true;
            this.$http.post('configurations/system-skins/set-tenant-default', { skin_id: this.applyingSkin.id }).then(response => {
                this.loading_set_default = false;
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.skins = response.data.skins;
                    const refreshed = this.skins.find(s => s.id === this.applyingSkin.id);
                    if (refreshed) this.applyingSkin = refreshed;
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(() => {
                this.loading_set_default = false;
                this.$message.error('Error al actualizar el tema por defecto');
            });
        },

        executeForce() {
            if (!this.applyingSkin) return;
            this.loading_force = true;
            this.$http.post('configurations/system-skins/force', { skin_id: this.applyingSkin.id }).then(response => {
                this.loading_force = false;
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.skins = response.data.skins;
                    const refreshed = this.skins.find(s => s.id === this.applyingSkin.id);
                    if (refreshed) this.applyingSkin = refreshed;
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(() => {
                this.loading_force = false;
                this.$message.error('Error al forzar el tema');
            });
        },

        onFileChange(file) {
            if (!file) return;
            this.pendingFile = file.raw;
            this.$http.get('configurations/system-skins/check', { params: { filename: file.name } }).then(response => {
                if (response.data.exists) {
                    this.conflictFilename = file.name;
                    this.renameValue = response.data.suggested;
                    this.renameError = '';
                    this.showRenameDialog = true;
                } else {
                    this.submitFile();
                }
            });
        },
        onFileRemove() {
            this.pendingFile = null;
        },
        submitFile(renameTo = null) {
            this.loading_upload = true;
            const formData = new FormData();
            formData.append('file', this.pendingFile);
            if (renameTo) formData.append('rename_to', renameTo);

            this.$http.post('configurations/system-skins/upload', formData, {
                headers: { ...this.headers, 'Content-Type': 'multipart/form-data' },
            }).then(response => {
                this.loading_upload = false;
                const data = response.data;
                if (data.success) {
                    this.$message.success(data.message);
                    this.skins = data.skins;
                } else {
                    this.$message.error(data.message);
                }
                this.$refs.upload.clearFiles();
                this.pendingFile = null;
            }).catch(() => {
                this.loading_upload = false;
                this.$refs.upload.clearFiles();
                this.pendingFile = null;
                this.$message.error('Error al subir el archivo');
            });
        },
        confirmRename() {
            const name = this.renameValue.trim();
            if (!name) {
                this.renameError = 'El nombre no puede estar vacío.';
                return;
            }
            if (!/^[a-zA-Z0-9_\-]+$/.test(name)) {
                this.renameError = 'Solo letras, números, guiones y guiones bajos.';
                return;
            }
            this.renameError = '';
            this.showRenameDialog = false;
            this.submitFile(name);
        },
        onRenameDialogClose() {
            this.showRenameDialog = false;
            this.renameError = '';
            this.$refs.upload.clearFiles();
            this.pendingFile = null;
        },

        openReplaceDialog(skin) {
            this.replacingSkin = skin;
            this.pendingReplaceFile = null;
            this.showReplaceDialog = true;
        },
        onReplaceFileChange(file) {
            this.pendingReplaceFile = file ? file.raw : null;
        },
        onReplaceFileRemove() {
            this.pendingReplaceFile = null;
        },
        confirmReplace() {
            if (!this.pendingReplaceFile) return;
            this.loading_replace = true;
            const formData = new FormData();
            formData.append('file', this.pendingReplaceFile);
            formData.append('skin_id', this.replacingSkin.id);

            this.$http.post('configurations/system-skins/replace', formData, {
                headers: { ...this.headers, 'Content-Type': 'multipart/form-data' },
            }).then(response => {
                this.loading_replace = false;
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.skins = response.data.skins;
                    this.showReplaceDialog = false;
                    this.$refs.uploadReplace.clearFiles();
                    this.pendingReplaceFile = null;
                    this.replacingSkin = null;
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(() => {
                this.loading_replace = false;
                this.$message.error('Error al reemplazar el tema');
            });
        },
        onReplaceDialogClose() {
            this.showReplaceDialog = false;
            this.pendingReplaceFile = null;
            this.replacingSkin = null;
            if (this.$refs.uploadReplace) {
                this.$refs.uploadReplace.clearFiles();
            }
        },

        confirmRevert(skin) {
            this.$confirm(
                `¿Restaurar "${skin.name}" al CSS original? Los tenants volverán a usar "${skin.filename}".`,
                'Restaurar original',
                { confirmButtonText: 'Restaurar', cancelButtonText: 'Cancelar', type: 'info' }
            ).then(() => {
                this.loading_revert = true;
                this.$http.post('configurations/system-skins/revert', { skin_id: skin.id }).then(response => {
                    this.loading_revert = false;
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.skins = response.data.skins;
                    } else {
                        this.$message.error(response.data.message);
                    }
                }).catch(() => {
                    this.loading_revert = false;
                    this.$message.error('Error al restaurar el tema');
                });
            }).catch(() => {});
        },

        // --- Reparar tema por defecto con estado inconsistente ---
        confirmSync(skin) {
            this.$confirm(
                `¿Forzar a todos los tenants a usar el archivo original "${skin.filename}" para el tema "${skin.name}"? Esto repara estados inconsistentes.`,
                'Reparar tema',
                { confirmButtonText: 'Reparar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => {
                this.loading_sync = skin.id;
                this.$http.post('configurations/system-skins/sync', { skin_id: skin.id }).then(response => {
                    this.loading_sync = null;
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.skins = response.data.skins;
                    } else {
                        this.$message.error(response.data.message);
                    }
                }).catch(() => {
                    this.loading_sync = null;
                    this.$message.error('Error al reparar el tema');
                });
            }).catch(() => {});
        },

        toggleSkinVisibility(skin) {
            this.loading_toggle_visibility = skin.id;
            this.$http.post('configurations/system-skins/toggle-visibility', { skin_id: skin.id }).then(response => {
                this.loading_toggle_visibility = null;
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.skins = response.data.skins;
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(() => {
                this.loading_toggle_visibility = null;
                this.$message.error('Error al cambiar la visibilidad del tema');
            });
        },

        confirmDelete(skin) {
            this.$confirm(`¿Estás seguro de eliminar el tema "${skin.name}"? Se eliminará de todos los tenants.`, 'Confirmar', {
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
            }).then(() => {
                this.deleteSkin(skin);
            }).catch(() => {});
        },
        deleteSkin(skin) {
            this.loading_delete = true;
            this.$http.post('configurations/system-skins/delete', { id: skin.id }).then(response => {
                this.loading_delete = false;
                if (response.data.success) {
                    this.$message.success(response.data.message);
                    this.skins = response.data.skins;
                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(() => {
                this.loading_delete = false;
                this.$message.error('Error al eliminar el tema');
            });
        },
    },
};
</script>

<style scoped>
.el-upload {
    width: 100%;
}
::v-deep .el-upload-dragger {
    width: 100%;
}

.apply-skin-btn {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 0 7px;
    height: 22px;
    font-size: 11px;
    font-weight: 500;
    color: #909399;
    background: transparent;
    border: 1px dashed #d3d4d6;
    border-radius: 4px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.apply-skin-btn:hover {
    color: #e6a23c;
    border-color: #e6a23c;
    border-style: solid;
    background: #fdf6ec;
}
.apply-skin-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.apply-skin-btn--active {
    color: #b88230;
    background: #fdf6ec;
    border: 1px solid #f5dab1;
}
.apply-skin-btn--active:hover {
    color: #b88230;
    background: #faecd8;
    border-color: #e6a23c;
}

.apply-option {
    border: 1px solid #ebeef5;
    border-radius: 6px;
    padding: 14px 16px;
    transition: border-color 0.15s ease, background 0.15s ease;
}
.apply-option--done {
    border-color: color-mix(in srgb, var(--success) 30%, #ffffff);
}
.apply-option--danger {
    border-color: color-mix(in srgb, var(--danger) 30%, #ffffff);
}
.apply-option__icon {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.apply-option__icon--default {
    color: var(--warning);
    background: color-mix(in srgb, var(--warning) 20%, #ffffff);
}
.apply-option__icon--force {
    color: var(--danger);
    background: color-mix(in srgb, var(--danger) 20%, #ffffff);
}
.apply-option__title {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 2px;
}
</style>
