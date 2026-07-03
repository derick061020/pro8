<template>
    <el-dialog
        title="Estados del libro de reclamaciones"
        :visible.sync="showDialog"
        width="680px"
        @open="getRecords"
        @close="close"
    >
        <!-- Subtítulo informativo -->
        <p class="sc-subtitle">
            El orden define el flujo mostrado al reclamante
        </p>

        <!-- Lista de estados via collapse -->
        <el-collapse v-model="activePanel" accordion class="sc-collapse">
            <div
                v-for="(status, index) in statuses"
                :key="status.id"
                class="sc-drag-wrapper"
                :class="{ 'sc-dragging-ghost': status.id === draggedId }"
                @dragover.prevent="onDragOver(index)"
                @drop.prevent="onDrop()"
                @dragend="onDragEnd"
            >
            <el-collapse-item
                :name="String(status.id)"
                class="sc-collapse-item"
            >
                <!-- Cabecera del ítem colapsado -->
                <template slot="title">
                    <div class="sc-item-header">
                        <span
                            class="sc-drag-handle"
                            draggable="true"
                            @dragstart="onDragStart(index, $event)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-grip-vertical"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                        </span>
                        <!-- Dot de color -->
                        <span
                            class="sc-color-dot"
                            :style="{ background: status.color || '#909399' }"
                        ></span>
                        <!-- Nombre del estado -->
                        <span class="sc-item-name">{{ status.description }}</span>
                        <!-- Chips de acciones y flags activos -->
                        <span class="sc-chips">
                            <el-tag
                                v-if="status.action_send_email"
                                size="mini"
                                :color="'#8B5CF6'"
                                class="sc-chip"
                            >Notificar cliente</el-tag>
                            <el-tag v-if="status.is_initial" size="mini" type="info" class="sc-chip sc-chip--gray">
                                Inicial
                            </el-tag>
                            <el-tag v-if="status.is_final" size="mini" type="danger" class="sc-chip sc-chip--gray">
                                Final
                            </el-tag>
                        </span>

                    </div>
                </template>

                <!-- Contenido expandido: formulario de edición -->
                <div class="sc-form px-3">
                    <!-- Nombre del estado -->
                    <div class="sc-field">
                        <label>Nombre del estado</label>
                        <el-input v-model="status.description" size="small"></el-input>
                    </div>

                    <div class="row">
                        <!-- Paleta de colores -->
                        <div class="sc-field col-6">
                            <label>Color del estado</label>
                            <div class="sc-color-palette">
                                <span
                                    v-for="c in colorPalette"
                                    :key="c"
                                    class="sc-palette-dot"
                                    :class="{ 'sc-palette-dot--active': status.color === c }"
                                    :style="{ background: c }"
                                    @click="status.color = c"
                                ></span>
                            </div>
                        </div>
                        <!-- select de usuario asignado por defecto -->
                        <div class="sc-field col-6" v-if="status.is_initial">
                            <label>Responsable por defecto</label>
                            <el-select v-model="status.assigned_user_id" placeholder="Seleccionar usuario" size="small">
                                <el-option
                                    v-for="user in activeUsers"
                                    :key="user.id"
                                    :label="user.name"
                                    :value="user.id"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>

                    <!-- Acciones automáticas -->
                    <div class="sc-field">
                        <label class="sc-actions-label">ACCIONES AUTOMÁTICAS DEL SISTEMA</label>
                        <div class="sc-actions-grid">
                            <div class="sc-action-item">
                                <el-checkbox v-model="status.action_send_email">
                                    <span class="sc-action-name">Notificar al cliente</span>
                                </el-checkbox>
                                <span class="sc-action-desc">Envía email al reclamante al entrar en este estado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Flags especiales: is_initial / is_final (mutuamente excluyentes) -->
                    <div class="sc-field sc-field--flags">
                        <el-checkbox
                            v-model="status.is_initial"
                            @change="onSetInitial(status)"
                        >
                            Usar como estado inicial (apertura)
                        </el-checkbox>
                        <el-checkbox
                            v-model="status.is_final"
                            @change="onSetFinal(status)"
                        >
                            Usar como estado final (cierre)
                        </el-checkbox>
                    </div>

                    <!-- Acciones del panel -->
                    <div class="sc-panel-actions">
                        <el-button
                            type="danger"
                            size="mini"
                            plain
                            @click="destroy(status)"
                        >
                            <i class="el-icon-delete"></i> Eliminar
                        </el-button>
                        <el-button
                            type="primary"
                            size="small"
                            :loading="saving === status.id"
                            @click="update(status)"
                        >
                            Listo
                        </el-button>
                    </div>
                </div>
            </el-collapse-item>
            </div>
        </el-collapse>

        <!-- Pie del dialog: agregar nuevo estado -->
        <div slot="footer" class="sc-footer">
            <el-input
                v-model="newDescription"
                placeholder="Nombre del nuevo estado..."
                size="small"
                style="width: 300px"
                @keyup.enter.native="store"
            ></el-input>
            <el-button
                type="primary"
                size="small"
                :loading="storing"
                @click="store"
            >
                <i class="el-icon-plus"></i> Nuevo estado
            </el-button>
        </div>
    </el-dialog>
</template>

<style scoped>
.sc-subtitle {
    font-size: 12px;
    color: #909399;
    margin: -10px 0 12px;
}
.sc-collapse {
    border-top: 1px solid #ebeef5;
}
.sc-collapse >>> .el-collapse-item__header {
    height: auto;
    min-height: 48px;
    padding: 6px 12px;
    line-height: 1.4;
}
.sc-item-header {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    padding-right: 8px;
}
.sc-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}
.sc-item-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sc-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    flex: 1;
    min-width: 0;
}
.sc-chip {
    border: none !important;
    color: #fff !important;
    font-size: 11px;
}
.sc-chip--gray {
    color: #909399 !important;
}
.sc-drag-wrapper {
    position: relative;
}
.sc-dragging-ghost {
    opacity: 0.4;
}
.sc-form {
    padding: 4px 0 0;
}
.sc-field {
    margin-bottom: 16px;
}
.sc-field > label {
    display: block;
    font-size: 12px;
    color: #606266;
    margin-bottom: 6px;
}
.sc-field--flags {
    display: flex;
    gap: 24px;
}
.sc-color-palette {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.sc-palette-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform .15s, border-color .15s;
}
.sc-palette-dot:hover {
    transform: scale(1.15);
}
.sc-palette-dot--active {
    border-color: #303133;
    transform: scale(1.15);
}
.sc-actions-label {
    font-size: 11px !important;
    color: #909399 !important;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.sc-actions-grid {
    margin-top: 4px;
}
.sc-action-item {
    border: 1px solid #ebeef5;
    border-radius: 6px;
    padding: 8px 10px;
    background: #fafafa;
}
.sc-action-name {
    font-weight: 500;
    font-size: 13px;
}
.sc-action-desc {
    display: block;
    font-size: 11px;
    color: #909399;
    margin-top: 2px;
    padding-left: 22px;
}
.sc-panel-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0 16px 0;
    border-bottom: 1px solid #ebeef5;
    margin-top: 4px;
}
.sc-footer {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
}
.sc-drag-handle {
    cursor: move;
    color: #909399;
    user-select: none;
}
.sc-drag-handle:active {
    cursor: grabbing;
}
.el-collapse.sc-collapse {
    border: none !important;
}
</style>
<style>
.sc-collapse-item .el-collapse-item__header{
    border: none !important;
}
.sc-collapse-item .el-collapse-item__content {
    padding-bottom: 5px !important;
}
</style>
<script>
export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            statuses: [],
            activePanel: null,
            newDescription: '',
            storing: false,
            saving: null,
            users: [],
            draggedId: null,
            originalStatuses: null,
            dropped: false,

            // Paleta de colores predefinidos
            colorPalette: [
                '#909399',
                '#409EFF',
                '#67C23A',
                '#F56C6C',
                '#E6A23C',
                '#8B5CF6',
                '#10B981',
                '#EF4444',
                '#EC4899',
            ],
        }
    },

    computed: {
        activeUsers() {
            return this.users.filter(u => u.active)
        }
    },

    methods: {
        getRecords() {
            this.$http.get('/statusClaim/records').then(response => {
                this.statuses = response.data.map(s => this.normalize(s))
                this.activePanel = null
            })
            this.$http.get('/users/records').then(response => {
                this.users = response.data.data
            })
        },

        // Normaliza valores 0/1 del backend a boolean
        normalize(status) {
            const booleans = ['is_initial', 'is_final', 'action_send_email']
            const s = { ...status }
            booleans.forEach(k => { s[k] = Boolean(s[k]) })
            return s
        },

        store() {
            if (!this.newDescription.trim())
                return this.$message.error('Ingrese un nombre para el estado')

            this.storing = true
            this.$http.post('/statusClaim/store', { description: this.newDescription })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.newDescription = ''
                        this.getRecords()
                    }
                })
                .finally(() => { this.storing = false })
        },

        update(status) {
            if (!status.description.trim())
                return this.$message.error('Ingrese un nombre para el estado')

            this.saving = status.id
            const payload = {
                description: status.description,
                color: status.color,
                is_initial: status.is_initial,
                is_final: status.is_final,
                action_send_email: status.action_send_email,
                assigned_user_id: status.is_initial ? status.assigned_user_id : null
            }

            this.$http.put(`/statusClaim/update/${status.id}`, payload)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.activePanel = null
                        this.getRecords()
                    }
                })
                .finally(() => { this.saving = null })
        },

        // Marca un estado como inicial y quita el flag en los demás localmente
        onSetInitial(status) {
            if (status.is_initial) {
                this.statuses.forEach(s => {
                    if (s.id !== status.id) s.is_initial = false
                })
                // Un estado no puede ser inicial y final a la vez
                status.is_final = false
            }
        },

        // Marca un estado como final y quita el flag en los demás localmente
        onSetFinal(status) {
            if (status.is_final) {
                this.statuses.forEach(s => {
                    if (s.id !== status.id) s.is_final = false
                })
                // Un estado no puede ser final e inicial a la vez
                status.is_initial = false
            }
        },

        destroy(status) {
            this.$confirm(
                `¿Desea eliminar el estado "${status.description}"?`,
                'Eliminar estado',
                { confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => {
                this.$http.delete(`/statusClaim/destroy/${status.id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.getRecords()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
            }).catch(() => {})
        },

        onDragStart(index, event) {
            const id = this.statuses[index].id
            this.originalStatuses = [...this.statuses]
            this.dropped = false
            event.dataTransfer.effectAllowed = 'move'
            event.dataTransfer.setData('text/plain', String(index))
            const wrapper = event.target.closest('.sc-drag-wrapper')
            if (wrapper) event.dataTransfer.setDragImage(wrapper, 20, 20)
            // Set after ghost is captured so the native drag image looks fully opaque
            requestAnimationFrame(() => { this.draggedId = id })
        },

        onDragOver(index) {
            if (this.draggedId === null) return
            const currentIndex = this.statuses.findIndex(s => s.id === this.draggedId)
            if (currentIndex === -1 || currentIndex === index) return
            const list = [...this.statuses]
            const [moved] = list.splice(currentIndex, 1)
            list.splice(index, 0, moved)
            this.statuses = list
        },

        onDrop() {
            if (this.draggedId === null) return
            this.dropped = true
            this.draggedId = null
            this.originalStatuses = null
            this.persistOrder()
        },

        onDragEnd() {
            if (!this.dropped && this.originalStatuses) {
                this.statuses = [...this.originalStatuses]
            }
            this.draggedId = null
            this.originalStatuses = null
            this.dropped = false
        },

        // Persiste el nuevo sort_order en el backend
        persistOrder() {
            const order = this.statuses.map((s, i) => ({ id: s.id, sort_order: i }))
            this.$http.post('/statusClaim/reorder', { order })
                .then(response => {
                    if (!response.data.success) {
                        this.$message.error('Error al guardar el orden')
                        this.getRecords()
                    }
                })
                .catch(() => {
                    this.$message.error('Error al guardar el orden')
                    this.getRecords()
                })
        },

        close() {
            this.activePanel = null
            this.$emit('update:showDialog', false)
            this.$eventHub.$emit('statusClaimsUpdated')
        }
    }
}
</script>
