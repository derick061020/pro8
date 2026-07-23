<template>
    <el-dialog
        title="Estados del pedido"
        :visible.sync="showDialog"
        width="680px"
        @open="getRecords"
        @close="close"
    >
        <!-- Subtítulo informativo -->
        <p class="so-subtitle">
            El orden define el flujo visible al cliente
        </p>

        <!-- Grupos colapsables por tipo -->
        <el-collapse v-model="activeGroups" class="so-groups-collapse">
            <el-collapse-item
                v-for="group in statusGroups"
                :key="group.key"
                :name="group.key"
                class="so-group-collapse"
            >
                <template slot="title">
                    <div class="so-group-header">
                        <span class="so-group-title-wrap">
                            <span class="so-group-icon" aria-hidden="true">
                                <svg v-if="group.key === 'payment'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"/><path d="M3 10h18"/><path d="M7 15h.01"/><path d="M11 15h2"/></svg>
                                <svg v-else-if="group.key === 'order'" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck-delivery"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"/><path d="M3 9l4 0"/></svg>
                            </span>
                            <span class="so-group-title">{{ group.title }}</span>
                        </span>
                        <span class="so-group-count">{{ group.items.length }}</span>
                    </div>
                </template>

                <div class="so-group-body">
                    <el-collapse v-model="activePanel" accordion class="so-collapse so-status-collapse">
                        <div
                            v-for="(status, index) in group.items"
                            :key="status.id"
                            class="so-drag-wrapper"
                            :class="{
                                'so-dragging-ghost': status.id === draggedId,
                                'so-drag-wrapper--expanded': activePanel === String(status.id),
                            }"
                            @dragover.prevent="onDragOver(index, group.key)"
                            @drop.prevent="onDrop(group.key)"
                            @dragend="onDragEnd"
                        >
                        <el-collapse-item
                            :name="String(status.id)"
                            class="so-collapse-item"
                        >
                <!-- Cabecera del ítem colapsado -->
                <template slot="title">
                    <div class="so-item-header">
                        <!-- Handle de arrastre -->
                        <span
                            class="so-drag-handle"
                            draggable="true"
                            @dragstart="onDragStart(index, group.key, $event)"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-grip-vertical"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                        </span>
                        <!-- Dot de color -->
                        <span
                            class="so-color-dot"
                            :style="{ background: status.color || '#909399' }"
                        ></span>
                        <!-- Nombre del estado -->
                        <span class="so-item-name">{{ status.description }}</span>
                        <!-- Chips de acciones activas -->
                        <span class="so-chips">
                            <el-tag
                                v-for="action in activeActions(status)"
                                :key="action.key"
                                size="mini"
                                :color="action.color"
                                class="so-chip"
                            >{{ action.label }}</el-tag>
                            <el-tag v-if="status.is_initial" size="mini" type="info" class="so-chip text-gray">
                                Estado inicial
                            </el-tag>
                            <el-tag v-if="status.is_final" size="mini" type="danger" class="so-chip text-gray">
                                Estado final
                            </el-tag>
                        </span>
                    </div>
                </template>

                <!-- Contenido expandido: formulario de edición -->
                <div class="so-form px-2">
                    <!-- Nombre -->
                    <div class="so-field">
                        <label>Nombre del estado</label>
                        <el-input v-model="status.description" size="small"></el-input>
                    </div>

                    <!-- Paleta de colores -->
                    <div class="so-field">
                        <label>Color del estado</label>
                        <div class="so-color-palette">
                            <span
                                v-for="c in colorPalette"
                                :key="c"
                                class="so-palette-dot"
                                :class="{ 'so-palette-dot--active': status.color === c }"
                                :style="{ background: c }"
                                @click="status.color = c"
                            ></span>
                        </div>
                    </div>

                    <div class="so-field">
                        <label>Tipo de estado</label>
                        <div class="so-type-selector">
                            <el-button
                                v-for="opt in typeOptions"
                                :key="opt.key"
                                size="small"
                                :type="isTypeActive(status, opt.key) ? 'primary' : ''"
                                plain
                                class="so-type-btn"
                                @click="selectType(status, opt.key)"
                            >
                                <span class="so-type-btn__inner">
                                    <span class="so-type-btn__icon" aria-hidden="true">
                                        <svg v-if="opt.key === 'payment'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"/><path d="M3 10h18"/><path d="M7 15h.01"/><path d="M11 15h2"/></svg>
                                        <svg v-else-if="opt.key === 'order'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17h-11v-14h-2"/><path d="M6 5l14 1l-1 7h-13"/></svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck-delivery"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"/><path d="M3 9l4 0"/></svg>
                                    </span>
                                    <span>{{ opt.label }}</span>
                                </span>
                            </el-button>
                        </div>
                        <span class="so-action-desc" style="padding-left: 0">
                            Define en qué columna del listado de pedidos aparece este estado.
                        </span>
                    </div>

                    <!-- Acciones automáticas -->
                    <div class="so-field">
                        <label class="so-actions-label">ACCIONES AUTOMÁTICAS DEL SISTEMA</label>
                        <div class="so-actions-grid">
                            <div
                                v-for="action in actionDefs"
                                :key="action.key"
                                class="so-action-item"
                                :class="{ 'so-action-item--disabled': action.disabled }"
                            >
                                <el-tooltip
                                    :content="action.desc"
                                    placement="top"
                                    :open-delay="300"
                                >
                                    <span class="so-action-tooltip-trigger">
                                        <el-checkbox
                                            v-model="status[action.key]"
                                            :disabled="action.disabled"
                                        >
                                            <span class="so-action-name">{{ action.label }}</span>
                                        </el-checkbox>
                                    </span>
                                </el-tooltip>
                            </div>
                        </div>
                    </div>

                    <!-- Estado inicial / final: solo para el grupo de pedido, únicos -->
                    <div v-if="status.is_order_status" class="so-field so-flags-stack">
                        <el-checkbox
                            v-model="status.is_initial"
                            @change="setInitial(status)"
                        >
                            Usar como estado inicial
                        </el-checkbox>
                        <el-checkbox
                            v-model="status.is_final"
                            @change="setFinal(status)"
                        >
                            Usar como estado final
                        </el-checkbox>
                    </div>

                    <!-- Acciones del panel -->
                    <div class="so-panel-actions">
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

                    <p v-if="!group.items.length" class="so-group-empty">
                        No hay estados en este grupo
                    </p>
                </div>
            </el-collapse-item>
        </el-collapse>

        <!-- Pie del dialog: agregar nuevo estado -->
        <div slot="footer" class="so-footer">
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
.so-subtitle {
    font-size: 12px;
    color: #909399;
    margin: -10px 0 12px;
}
.so-groups-collapse {
    border: none;
}
.so-groups-collapse :deep(.el-collapse-item__header),
.so-groups-collapse >>> .el-collapse-item__header {
    height: auto;
    min-height: 44px;
    padding: 0 12px;
    line-height: 1.4;
    background: #f5f7fa;
    border: 1px solid #ebeef5;
}
.so-groups-collapse :deep(.el-collapse-item__wrap),
.so-groups-collapse >>> .el-collapse-item__wrap {
    border-bottom: none;
}
.so-group-collapse {
    margin-bottom: 12px;
}
.so-group-collapse:last-child {
    margin-bottom: 0;
}
.so-group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1;
    padding-right: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #303133;
}
.so-group-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.so-group-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #606266;
}
.so-group-title {
    letter-spacing: 0.02em;
}
.so-group-count {
    font-size: 11px;
    font-weight: 500;
    color: #909399;
    background: #fff;
    border: 1px solid #dcdfe6;
    border-radius: 10px;
    padding: 1px 8px;
    min-width: 22px;
    text-align: center;
}
.so-group-body {
    border: 1px solid #ebeef5;
    border-top: none;
    overflow: hidden;
    background: #fff;
    border-radius: 0 0 13px 13px;
}
.so-status-collapse {
    border: none;
}
.so-group-empty {
    margin: 0;
    padding: 14px;
    font-size: 12px;
    color: #909399;
    text-align: center;
    background: #fafafa;
}
/* Forzar que el título del collapse ocupe todo el ancho */
.so-collapse :deep(.el-collapse-item__header),
.so-collapse >>> .el-collapse-item__header {
    height: auto;
    min-height: 48px;
    padding: 6px 12px;
    line-height: 1.4;
    margin: 5px;
    border-radius: 8px;
}
.so-item-header {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
    padding-right: 8px;
}
.so-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}
.so-item-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.so-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    flex: 1;
    min-width: 0;
}
.so-chip {
    border: none !important;
    color: #fff !important;
    font-size: 11px;
}
.so-drag-wrapper {
    position: relative;
    border-top-color: #ebeef5;
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
}
.so-group .so-drag-wrapper:first-of-type {
    border-top-color: transparent;
}
.so-drag-wrapper--expanded {
    border-radius: 0;
    margin: 0;
    background: #fff;
}
.so-dragging-ghost {
    opacity: 0.4;
}
.so-drag-handle {
    cursor: move;
    color: #909399;
    user-select: none;
}
.so-drag-handle:active {
    cursor: grabbing;
}
/* Formulario expandido */
.so-form {
    padding: 4px 0 0;
}
.so-field {
    margin-bottom: 16px;
}
.so-field > label {
    display: block;
    font-size: 12px;
    color: #606266;
    margin-bottom: 6px;
}
.so-flags-stack {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.so-flags-stack .el-checkbox {
    margin-left: 0;
}
.so-type-selector {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}
.so-type-selector .so-type-btn {
    flex: 1;
    min-width: 0;
    margin: 0 !important;
    padding: 8px 12px;
}
.so-type-btn__inner {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.so-type-btn__icon {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    line-height: 0;
}
/* Paleta de colores */
.so-color-palette {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.so-palette-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform .15s, border-color .15s;
}
.so-palette-dot:hover {
    transform: scale(1.15);
}
.so-palette-dot--active {
    border-color: #303133;
    transform: scale(1.15);
}
/* Grid de acciones */
.so-actions-label {
    font-size: 11px !important;
    color: #909399 !important;
    letter-spacing: .5px;
    text-transform: uppercase;
}
.so-actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 10px 16px;
    margin-top: 4px;
}
.so-action-item {
    border: 1px solid #ebeef5;
    border-radius: 4px;
    padding: 6px 10px;
    background: #fafafa;
}
.so-action-item--disabled {
    opacity: .5;
    cursor: not-allowed;
}
.so-action-name {
    font-weight: 500;
    font-size: 13px;
}
.so-action-tooltip-trigger {
    display: inline-block;
    width: 100%;
}
.so-action-desc {
    display: block;
    font-size: 11px;
    color: #909399;
    margin-top: 2px;
}
/* Acciones del panel (footer interno) */
.so-panel-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 8px;
    border-top: 1px solid #ebeef5;
    margin-top: 4px;
}
/* Footer del dialog */
.so-footer {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
}
.el-collapse.so-collapse {
    border: none !important;
}
</style>
<style>
.so-collapse-item .el-collapse-item__header{
    border: none !important;
}
.so-collapse-item .el-collapse-item__content {
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
            activeGroups: ['payment', 'shipping', 'order'],
            activePanel: null,
            newDescription: '',
            storing: false,
            saving: null,
            draggedId: null,
            dragGroupKey: null,
            originalStatuses: null,
            dropped: false,

            // Paleta de 15 colores vibrantes, distribuidos en todo el espectro
            colorPalette: [
                '#64748B', // gris pizarra
                '#EF4444', // rojo
                '#F97316', // naranja
                '#F59E0B', // ámbar
                '#EAB308', // amarillo
                '#84CC16', // lima
                '#22C55E', // verde
                '#10B981', // esmeralda
                '#14B8A6', // teal
                '#06B6D4', // cian
                '#3B82F6', // azul
                '#6366F1', // índigo
                '#8B5CF6', // violeta
                '#D946EF', // fucsia
                '#EC4899', // rosa
            ],

            // Definición de acciones: label, descripción, clave del modelo y si está deshabilitada
            actionDefs: [
                { key: 'action_discount_stock',     label: 'Descontar stock',       desc: 'Reduce el inventario disponible',      disabled: false, color: '#E6A23C' },
                { key: 'action_mark_payment',       label: 'Marcar pago recibido',  desc: 'Registra el pago como confirmado',     disabled: true, color: '#67C23A' },
                { key: 'action_generate_document',  label: 'Generar comprobante',   desc: 'Emite factura o boleta electrónica',   disabled: false, color: '#409EFF' },
                { key: 'action_send_email',         label: 'Enviar email al cliente', desc: 'Notifica el cambio de estado',       disabled: false, color: '#8B5CF6' },
                { key: 'action_notify_dispatch',    label: 'Notificar despacho',    desc: 'Alerta al área logística',             disabled: true, color: '#409EFF' },
                { key: 'action_generate_remission', label: 'Generar guía de remisión', desc: 'Crea el documento de envío',       disabled: true,  color: '#10B981' },
                { key: 'action_free_reserved_stock',label: 'Liberar stock reservado', desc: 'Devuelve unidades al inventario',   disabled: true,  color: '#E6A23C' },
                { key: 'action_block_returns',      label: 'Bloquear devoluciones', desc: 'Cierra el periodo de cambio',          disabled: true,  color: '#F56C6C' },
                { key: 'action_void_order',         label: 'Anular pedido',         desc: 'Cancela y revierte el pedido',         disabled: true,  color: '#F56C6C' },
            ],

            typeOptions: [
                { key: 'payment', label: 'Pago' },
                { key: 'shipping', label: 'Envío' },
                { key: 'order', label: 'Pedido' },
            ],
        }
    },

    computed: {
        statusGroups() {
            return [
                {
                    key: 'payment',
                    title: 'Estados de pago',
                    items: this.statuses.filter(s => s.is_payment_status),
                },
                {
                    key: 'shipping',
                    title: 'Estados de envío',
                    items: this.statuses.filter(s => s.is_shipping_status),
                },
                {
                    key: 'order',
                    title: 'Estados de pedido',
                    items: this.statuses.filter(s => s.is_order_status),
                },
            ]
        },

        // Campos enviados en store/update (todos los editables)
        formFields() {
            return [
                'description', 'color', 'is_initial', 'is_final',
                'is_payment_status', 'is_order_status', 'is_shipping_status',
                'action_discount_stock', 'action_mark_payment',
                'action_generate_document', 'action_send_email',
                'action_notify_dispatch', 'action_generate_remission',
                'action_free_reserved_stock', 'action_block_returns',
                'action_void_order',
            ]
        },
    },

    methods: {
        // Devuelve las acciones activas de un estado para mostrar como chips
        activeActions(status) {
            return this.actionDefs.filter(a => status[a.key])
        },

        getRecords() {
            this.$http.get('/statusOrder/records').then(response => {
                // Normalizar booleanos que pueden llegar como 0/1 desde el backend
                this.statuses = response.data.map(s => this.normalize(s))
                this.activeGroups = ['payment', 'shipping', 'order']
                this.activePanel = null
            })
        },

        // Garantiza que los booleanos sean boolean (no 0/1)
        normalize(status) {
            const booleans = [
                'is_initial', 'is_final', 'is_payment_status', 'is_order_status', 'is_shipping_status',
                'action_discount_stock', 'action_mark_payment',
                'action_generate_document', 'action_send_email', 'action_notify_dispatch',
                'action_generate_remission', 'action_free_reserved_stock',
                'action_block_returns', 'action_void_order',
            ]
            const s = { ...status }
            booleans.forEach(k => { s[k] = Boolean(s[k]) })
            return s
        },

        store() {
            if (!this.newDescription.trim())
                return this.$message.error('Ingrese un nombre para el estado')

            this.storing = true
            this.$http.post('/statusOrder/store', { description: this.newDescription })
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
            const payload = {}
            this.formFields.forEach(f => { payload[f] = status[f] })

            this.$http.put(`/statusOrder/update/${status.id}`, payload)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.activePanel = null
                        this.getRecords()
                    }
                })
                .finally(() => { this.saving = null })
        },

        // Grupo de un estado: 'payment' | 'shipping' | 'order'
        typeOf(s) {
            if (s.is_payment_status) return 'payment'
            if (s.is_shipping_status) return 'shipping'
            return 'order'
        },

        isTypeActive(status, type) {
            return this.typeOf(status) === type
        },

        selectType(status, type) {
            status.is_payment_status = type === 'payment'
            status.is_order_status = type === 'order'
            status.is_shipping_status = type === 'shipping'
        },

        // Marca un estado como inicial. Único dentro del grupo de pedido.
        setInitial(status) {
            if (!status.is_initial) return
            this.statuses.forEach(s => {
                if (s.id !== status.id && s.is_order_status) {
                    s.is_initial = false
                }
            })
        },

        // Marca un estado como final. Único dentro del grupo de pedido.
        setFinal(status) {
            if (!status.is_final) return
            this.statuses.forEach(s => {
                if (s.id !== status.id && s.is_order_status) {
                    s.is_final = false
                }
            })
        },

        destroy(status) {
            this.$confirm(
                `¿Desea eliminar el estado "${status.description}"?`,
                'Eliminar estado',
                { confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => {
                this.$http.delete(`/statusOrder/destroy/${status.id}`)
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

        groupItems(groupKey) {
            const group = this.statusGroups.find(g => g.key === groupKey)
            return group ? group.items : []
        },

        rebuildStatuses(reorderedGroup, changedGroupKey) {
            return this.statusGroups.flatMap(g =>
                g.key === changedGroupKey ? reorderedGroup : g.items
            )
        },

        onDragStart(index, groupKey, event) {
            const groupItems = this.groupItems(groupKey)
            const id = groupItems[index].id
            this.dragGroupKey = groupKey
            this.originalStatuses = [...this.statuses]
            this.dropped = false
            event.dataTransfer.effectAllowed = 'move'
            event.dataTransfer.setData('text/plain', String(index))
            const wrapper = event.target.closest('.so-drag-wrapper')
            if (wrapper) event.dataTransfer.setDragImage(wrapper, 20, 20)
            requestAnimationFrame(() => { this.draggedId = id })
        },

        onDragOver(index, groupKey) {
            if (this.draggedId === null || this.dragGroupKey !== groupKey) return
            const groupItems = this.groupItems(groupKey)
            const currentIndex = groupItems.findIndex(s => s.id === this.draggedId)
            if (currentIndex === -1 || currentIndex === index) return
            const reordered = [...groupItems]
            const [moved] = reordered.splice(currentIndex, 1)
            reordered.splice(index, 0, moved)
            this.statuses = this.rebuildStatuses(reordered, groupKey)
        },

        onDrop(groupKey) {
            if (this.draggedId === null || this.dragGroupKey !== groupKey) return
            this.dropped = true
            this.draggedId = null
            this.dragGroupKey = null
            this.originalStatuses = null
            this.persistOrder()
        },

        onDragEnd() {
            if (!this.dropped && this.originalStatuses) {
                this.statuses = [...this.originalStatuses]
            }
            this.draggedId = null
            this.dragGroupKey = null
            this.originalStatuses = null
            this.dropped = false
        },

        // Envía el nuevo sort_order de todos los estados al backend
        persistOrder() {
            const order = this.statuses.map((s, i) => ({ id: s.id, sort_order: i }))
            this.$http.post('/statusOrder/reorder', { order })
                .then(response => {
                    if (!response.data.success) {
                        this.$message.error('Error al guardar el orden')
                        this.getRecords() // revertir al orden de DB si falla
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
            this.$eventHub.$emit('statusesUpdated')
        }
    }
}
</script>
