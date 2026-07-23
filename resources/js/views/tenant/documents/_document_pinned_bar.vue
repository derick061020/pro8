<template>
    <div class="item-form-pinned-bar document-form-pinned-bar mb-2" :class="{ 'is-editing': editing }">

        <div v-if="editing" class="d-flex flex-column align-items-start gap-1 mb-2">
            <span class="ifb-badge">
                <i class="el-icon-set-up"></i> PERSONALIZANDO DATOS GENERALES
            </span>
            <small class="text-muted">
                Arrastra para reordenar. Mueve el borde derecho para cambiar el ancho. Se guarda para todo el tenant.
            </small>
        </div>

        <div v-if="!editing" class="row g-2 mx-0">
            <pinned-slot v-for="pin in visiblePins"
                         :key="pin.field_key"
                         :slot-name="pin.field_key"
                         :slot-scope-data="{ field: catalogByKey[pin.field_key], width: pin.width }"
                         :wrapper-class="columnClass(pin.width)" />
        </div>

        <div v-if="editing" class="pinned-bar-editing">
            <draggable v-model="draftPins"
                       :animation="200"
                       handle=".ifb-drag-handle"
                       class="row g-0"
                       @end="onDragEnd">
                <div v-for="(pin, idx) in draftPins"
                     :key="pin.field_key"
                     ref="pinCells"
                     :class="[
                        'pinned-cell-edit',
                        columnClass(pin.width),
                        { 'pinned-cell-edit--resizing': resizingIdx === idx },
                        { 'pinned-cell-edit--spacer': isSpacer(pin) },
                        { 'pinned-cell-edit--conditional': !!conditionHint(pin) },
                     ]">
                    <div class="pinned-cell-edit-inner">
                        <div class="pinned-cell-edit-toolbar">
                            <span class="ifb-drag-handle" title="Arrastrar para reordenar">
                                <i class="el-icon-rank"></i>
                            </span>
                            <span class="ifb-pin-label" :title="pinTitle(pin)">
                                {{ pinTitle(pin) }}<span v-if="catalogByKey[pin.field_key] && catalogByKey[pin.field_key].required" class="ifb-required">*</span>
                            </span>
                            <el-tooltip v-if="conditionHint(pin)"
                                        :content="conditionHint(pin)"
                                        placement="top"
                                        effect="dark">
                                <i class="el-icon-info ifb-cond-info"></i>
                            </el-tooltip>
                            <button type="button"
                                    class="ifb-pin-remove"
                                    title="Quitar de la barra"
                                    @click="removePin(idx)">
                                <i class="el-icon-close"></i>
                            </button>
                        </div>
                        <div class="pinned-cell-edit-preview">
                            <div v-if="isSpacer(pin)" class="ifb-spacer-preview">
                                Espacio en blanco
                            </div>
                            <pinned-slot v-else
                                         :slot-name="pin.field_key"
                                         :slot-scope-data="{ field: catalogByKey[pin.field_key], width: pin.width }" />
                        </div>
                        <div class="ifb-resize-handle"
                             :class="{ 'ifb-resize-handle--active': resizingIdx === idx }"
                             title="Arrastra para cambiar el ancho"
                             @mousedown.prevent="onResizeStart($event, idx)"></div>
                    </div>
                </div>
            </draggable>

            <div class="d-flex gap-2">
                <button v-if="!addPanelOpen"
                        type="button"
                        class="ifb-add-trigger"
                        @click="openAddPanel">
                    <i class="el-icon-plus"></i> Agregar campo a datos generales
                </button>

                <button v-else type="button" class="ifb-add-trigger" @click="closeAddPanel">
                    <i class="el-icon-close"></i> Cerrar
                </button>

                <button type="button" class="btn second-buton mt-2" style="min-width: 195px;" @click="addSpacer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-space me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 10v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1 -1v-3" /></svg>
                    Agregar espaciado
                </button>
            </div>

            <div v-if="addPanelOpen" class="ifb-add-panel">
                <div class="ifb-add-panel-body">
                    <div v-for="group in filteredAddGroups" :key="group.tab" class="ifb-add-group">
                        <div class="ifb-add-group-title">{{ group.label }}</div>
                        <div v-for="field in group.fields"
                             :key="field.key"
                             :class="['ifb-add-row', { 'is-disabled': isFieldPinned(field.key) }]"
                             @click="addPinFromPanel(field.key)">
                            <span class="ifb-add-row-icon">
                                <i :class="isFieldPinned(field.key) ? 'el-icon-check' : 'el-icon-plus'"></i>
                            </span>
                            <span class="ifb-add-row-label">
                                {{ field.label }}
                                <el-tooltip v-if="fieldConditionHint(field)"
                                            :content="fieldConditionHint(field)"
                                            placement="top"
                                            effect="dark">
                                    <i class="el-icon-info ifb-cond-info ifb-add-row-info" @click.stop></i>
                                </el-tooltip>
                            </span>
                            <span class="ifb-add-row-type">{{ inputLabel(field.type) }}</span>
                        </div>
                    </div>
                    <div v-if="filteredAddGroups.length === 0" class="text-center text-muted p-3 small">
                        No se encontraron campos que coincidan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import '../items/_pinned_bar.scss';
import {
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getFieldByKey,
    getInputTypeLabel,
} from './_document_form_fields_catalog';

// Prefijo de las celdas de espaciado (columnas vacías y transparentes)
const SPACER_PREFIX = '__spacer__';

const PinnedSlot = {
    name: 'PinnedSlot',
    functional: true,
    props: {
        slotName: { type: String, required: true },
        slotScopeData: { type: Object, default: () => ({}) },
        wrapperClass: { type: String, default: '' },
    },
    render(h, ctx) {
        const parent = ctx.parent;
        const slot = parent.$scopedSlots[ctx.props.slotName] || parent.$slots[ctx.props.slotName];
        const content = typeof slot === 'function'
            ? slot(ctx.props.slotScopeData)
            : slot;
        return h('div', {
            class: [ctx.props.wrapperClass].filter(Boolean),
        }, content || []);
    },
};

export default {
    name: 'DocumentFormPinnedBar',
    components: { draggable, PinnedSlot },
    props: {
        variant: { type: String, default: 'invoice' },
        pinnedFields: { type: Array, default: () => [] },
        // Claves a omitir en la vista normal cuando su condición no aplica (evita columnas vacías).
        hiddenFields: { type: Array, default: () => [] },
        saving: { type: Boolean, default: false },
    },
    data() {
        return {
            editing: false,
            draftPins: [],
            originalDraftSignature: null,
            addPanelOpen: false,
            addSearch: '',
            resizingIdx: null,
            resizeState: null,
        };
    },
    computed: {
        catalogByKey() {
            const available = getAvailableFields(this.variant);
            return available.reduce((acc, f) => {
                acc[f.key] = f;
                return acc;
            }, {});
        },
        sortedPins() {
            return [...this.pinnedFields]
                .filter(p => this.isSpacer(p) || this.catalogByKey[p.field_key])
                .sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
        },
        hiddenSet() {
            return new Set(this.hiddenFields || []);
        },
        visiblePins() {
            return this.sortedPins.filter(p => !this.hiddenSet.has(p.field_key));
        },
        groupedAvailable() {
            return getAvailableFieldsGrouped(this.variant);
        },
        filteredAddGroups() {
            const q = (this.addSearch || '').trim().toLowerCase();
            return this.groupedAvailable
                .map(group => ({
                    ...group,
                    fields: group.fields.filter(f => !q || f.label.toLowerCase().includes(q)),
                }))
                .filter(group => group.fields.length > 0);
        },
    },
    watch: {
        editing(value) {
            this.$emit('editing-changed', value);
        },
    },
    methods: {
        enterEditMode() {
            this.draftPins = this.sortedPins.map(p => ({ ...p }));
            this.originalDraftSignature = this.layoutSignature(this.draftPins);
            this.editing = true;
            this.addPanelOpen = false;
        },
        exitEditMode() {
            this.editing = false;
            this.addPanelOpen = false;
            this.addSearch = '';
            this.originalDraftSignature = null;
        },
        cancelEdit() {
            if (!this.hasUnsavedLayoutChanges()) {
                this.exitEditMode();
                return;
            }

            this.$confirm('Hay cambios sin guardar en el editor. Si cierras ahora, se perderán.', 'Cerrar sin guardar', {
                confirmButtonText: 'Cerrar sin guardar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
            }).then(() => {
                this.exitEditMode();
            }).catch(() => {});
        },
        hasUnsavedLayoutChanges() {
            return this.layoutSignature(this.draftPins) !== this.originalDraftSignature;
        },
        layoutSignature(pins) {
            return JSON.stringify((pins || []).map((p, idx) => ({
                field_key: p.field_key,
                width: parseInt(p.width, 10) || 3,
                order: idx,
            })));
        },
        onDragEnd() {
            this.draftPins = this.draftPins.map((p, idx) => ({ ...p, order: idx }));
        },
        openAddPanel() {
            this.addPanelOpen = true;
            this.$nextTick(() => {
                const el = this.$refs.addSearchInput;
                if (el && typeof el.focus === 'function') el.focus();
            });
        },
        closeAddPanel() {
            this.addPanelOpen = false;
            this.addSearch = '';
        },
        addPinFromPanel(fieldKey) {
            if (this.isFieldPinned(fieldKey)) return;
            this.pinField(fieldKey);
        },
        isFieldPinned(fieldKey) {
            return this.draftPins.some(p => p.field_key === fieldKey);
        },
        /**
         * Pública: usada desde invoice_generate.vue via $refs cuando el usuario hace clic
         * en "Fijar arriba" en un campo de Información Adicional.
         */
        pinField(fieldKey) {
            if (!this.editing) this.enterEditMode();
            if (this.isFieldPinned(fieldKey)) return;
            const field = getFieldByKey(fieldKey);
            if (!field) return;
            this.draftPins.push({
                field_key: fieldKey,
                width: field.defaultWidth || 3,
                order: this.draftPins.length,
            });
        },
        isSpacer(pin) {
            const key = typeof pin === 'string' ? pin : (pin && pin.field_key);
            return typeof key === 'string' && key.startsWith(SPACER_PREFIX);
        },
        addSpacer() {
            if (!this.editing) this.enterEditMode();
            const usedIndexes = this.draftPins
                .filter(p => this.isSpacer(p))
                .map(p => parseInt(p.field_key.slice(SPACER_PREFIX.length), 10))
                .filter(n => !Number.isNaN(n));
            const next = (usedIndexes.length ? Math.max(...usedIndexes) : 0) + 1;
            this.draftPins.push({
                field_key: `${SPACER_PREFIX}${next}`,
                width: 3,
                order: this.draftPins.length,
            });
        },
        removePin(idx) {
            this.draftPins.splice(idx, 1);
            this.draftPins = this.draftPins.map((p, i) => ({ ...p, order: i }));
        },
        resetLayout() {
            this.draftPins = getDefaultLayout(this.variant);
        },
        confirmEdit() {
            const payload = this.draftPins.map((p, idx) => ({
                field_key: p.field_key,
                width: parseInt(p.width, 10) || 3,
                order: idx,
            }));
            this.$emit('save', payload, () => {
                this.exitEditMode();
            });
        },
        columnClass(width) {
            const w = Math.max(1, Math.min(12, parseInt(width, 10) || 3));
            return `col-md-${w}`;
        },
        inputLabel(type) {
            return getInputTypeLabel(type);
        },
        fieldConditionHint(field) {
            if (!field || !this.hiddenSet.has(field.key)) return null;
            return field.conditionHint
                || 'Este campo depende de una condición o configuración para mostrarse.';
        },
        pinTitle(pin) {
            if (this.isSpacer(pin)) return 'Espaciado';
            const field = this.catalogByKey[pin.field_key];
            return field && field.label ? field.label : pin.field_key;
        },
        conditionHint(pin) {
            if (this.isSpacer(pin)) return null;
            if (!this.hiddenSet.has(pin.field_key)) return null;
            const field = this.catalogByKey[pin.field_key];
            if (!field) return null;
            return field.conditionHint
                || 'Este campo depende de una condición o configuración para mostrarse.';
        },

        /* ─── Resize por borde derecho ─── */

        onResizeStart(event, idx) {
            const cell = this.$refs.pinCells && this.$refs.pinCells[idx];
            if (!cell) return;
            const container = cell.parentElement;
            if (!container) return;
            const containerStyle = window.getComputedStyle(container);
            const paddingX = parseFloat(containerStyle.paddingLeft || '0')
                + parseFloat(containerStyle.paddingRight || '0');
            const colWidth = Math.max(1, (container.clientWidth - paddingX) / 12);
            this.resizingIdx = idx;
            this.resizeState = {
                idx,
                startX: event.clientX,
                startWidth: this.draftPins[idx].width,
                colWidth,
            };
            window.addEventListener('mousemove', this.onResizeMove);
            window.addEventListener('mouseup', this.onResizeEnd);
        },
        onResizeMove(event) {
            if (!this.resizeState) return;
            const deltaPx = event.clientX - this.resizeState.startX;
            const deltaCols = Math.round(deltaPx / this.resizeState.colWidth);
            const newWidth = Math.max(1, Math.min(12, this.resizeState.startWidth + deltaCols));
            if (this.draftPins[this.resizeState.idx].width !== newWidth) {
                this.$set(this.draftPins[this.resizeState.idx], 'width', newWidth);
            }
        },
        onResizeEnd() {
            this.resizingIdx = null;
            this.resizeState = null;
            window.removeEventListener('mousemove', this.onResizeMove);
            window.removeEventListener('mouseup', this.onResizeEnd);
        },
    },
    beforeDestroy() {
        window.removeEventListener('mousemove', this.onResizeMove);
        window.removeEventListener('mouseup', this.onResizeEnd);
    },
};
</script>
<style>
.ifb-spacer-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    border: 1px dashed #c9ced6;
    border-radius: 6px;
    background:
        repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.03),
            rgba(0, 0, 0, 0.03) 6px,
            transparent 6px,
            transparent 12px
        );
    color: #9aa1ab;
    font-size: 12px;
    font-style: italic;
    user-select: none;
}
</style>
<style scoped>
.pinned-cell-edit--conditional .pinned-cell-edit-inner {
    border-color: var(--warning);
    border-style: dashed;
    background: color-mix(in srgb, var(--warning) 10%, #ffffff00);
}
.ifb-cond-info {
    color: var(--warning);
    cursor: help;
    font-size: 15px;
}
.ifb-add-row-info {
    margin-left: 4px;
    vertical-align: middle;
}
</style>
