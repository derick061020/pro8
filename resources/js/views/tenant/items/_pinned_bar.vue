<template>
    <div class="item-form-pinned-bar mb-2" :class="{ 'is-editing': editing }">

        <!-- Edición: solo badge + helper (todos los botones viven en el header del el-dialog) -->
        <div v-if="editing" class="d-flex flex-column align-items-start gap-1 mb-2">
            <span class="ifb-badge">
                <i class="el-icon-set-up"></i> PERSONALIZANDO BARRA FIJADA
            </span>
            <small class="text-muted">
                Arrastra para reordenar. Mueve el borde derecho para cambiar el ancho. Se guarda para todo el tenant.
            </small>
        </div>

        <!-- Vista normal: grid de slots fijados -->
        <div v-if="!editing" class="row g-2 mb-3">
            <pinned-slot v-for="pin in sortedPins"
                         :key="pin.field_key"
                         :slot-name="pin.field_key"
                         :slot-scope-data="{ field: catalogByKey[pin.field_key], width: pin.width }"
                         :wrapper-class="columnClass(pin.width)" />
        </div>

        <!-- Edición: grid de pines con drag, resize, remove -->
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
                     ]">
                    <div class="pinned-cell-edit-inner">
                        <div class="pinned-cell-edit-toolbar">
                            <span class="ifb-drag-handle" title="Arrastrar para reordenar">
                                <i class="el-icon-rank"></i>
                            </span>
                            <span class="ifb-pin-label" :title="pinTitle(pin)">
                                {{ pinTitle(pin) }}<span v-if="catalogByKey[pin.field_key] && catalogByKey[pin.field_key].required" class="ifb-required">*</span>
                            </span>
                            <button type="button"
                                    class="ifb-pin-remove"
                                    title="Quitar de la barra"
                                    @click="removePin(idx)">
                                <i class="el-icon-close"></i>
                            </button>
                        </div>
                        <div class="pinned-cell-edit-preview">
                            <pinned-slot :slot-name="pin.field_key"
                                         :slot-scope-data="{ field: catalogByKey[pin.field_key], width: pin.width }" />
                        </div>
                        <div class="ifb-resize-handle"
                             :class="{ 'ifb-resize-handle--active': resizingIdx === idx }"
                             title="Arrastra para cambiar el ancho"
                             @mousedown.prevent="onResizeStart($event, idx)"></div>
                    </div>
                </div>
            </draggable>

            <!-- Selector de campos full-width agrupado por pestaña -->
            <button v-if="!addPanelOpen"
                    type="button"
                    class="ifb-add-trigger"
                    @click="openAddPanel">
                <i class="el-icon-plus"></i> Agregar campo a la barra fijada
            </button>

            <div v-else class="ifb-add-panel">
                <div class="d-flex align-items-center gap-2 p-2 border-bottom bg-light">
                    <el-input v-model="addSearch"
                              ref="addSearchInput"
                              size="small"
                              placeholder="Buscar campo..."
                              prefix-icon="el-icon-search"
                              clearable
                              class="flex-grow-1" />
                    <button type="button" class="btn btn-sm btn-link text-muted px-2" @click="closeAddPanel">
                        <i class="el-icon-close"></i>
                    </button>
                </div>
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
                            <span class="ifb-add-row-label">{{ field.label }}</span>
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
import './_pinned_bar.scss';
import {
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getFieldByKey,
    getInputTypeLabel,
} from './_form_fields_catalog';

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
    name: 'ItemFormPinnedBar',
    components: { draggable, PinnedSlot },
    props: {
        variant: { type: String, required: true },
        pinnedFields: { type: Array, default: () => [] },
        saving: { type: Boolean, default: false },
    },
    data() {
        return {
            editing: false,
            draftPins: [],
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
                .filter(p => this.catalogByKey[p.field_key])
                .sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
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
            this.editing = true;
            this.addPanelOpen = false;
        },
        exitEditMode() {
            this.editing = false;
            this.addPanelOpen = false;
            this.addSearch = '';
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
         * Pública: usada desde form.vue via $refs cuando el usuario hace clic
         * en "Fijar arriba" en un wrapper del form.
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
        pinTitle(pin) {
            const field = this.catalogByKey[pin.field_key];
            return field && field.label ? field.label : pin.field_key;
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
