<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Columnas por defecto</h3>
        </div>
        <div class="card-body px-3 pt-3 pb-2">
            <p class="text-muted mb-3" style="font-size:0.85rem;">
                Configure qué columnas se mostrarán y en qué orden aparecerán por defecto en cada listado. Esta configuración se aplica a usuarios nuevos o que aún no hayan personalizado su vista.
            </p>

            <el-collapse v-model="openSections" class="vc-collapse">
                <el-collapse-item
                    v-for="section in sections"
                    :key="section.key"
                    :name="section.key"
                    class="vc-collapse-item"
                >
                    <template slot="title">
                        <div class="vc-section__header">
                            <div class="d-flex align-items-center" style="gap:10px;">
                                <div class="vc-section__icon" :style="{ background: section.color + '18', color: section.color }" v-html="section.svg"></div>
                                <span class="vc-section__title">{{ section.label }}</span>
                            </div>
                            <span class="vc-section__count">
                                {{ section.modules.length }} {{ section.modules.length === 1 ? 'listado' : 'listados' }}
                            </span>
                        </div>
                    </template>

                    <div
                        v-for="(moduleKey, idx) in section.modules"
                        :key="moduleKey"
                        class="vc-module-item"
                        :class="{ 'vc-module-item--border': idx > 0 }"
                    >
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <span class="vc-module-item__dot" :style="{ background: section.color }"></span>
                            <span class="vc-module-item__label">{{ moduleLabel(moduleKey) }}</span>
                        </div>
                        <el-button size="small" @click="openDialog(moduleKey)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;vertical-align:-2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                            Editar columnas
                        </el-button>
                    </div>
                </el-collapse-item>
            </el-collapse>

            <el-dialog
                :title="'Columnas — ' + editingModuleLabel"
                :visible.sync="dialogVisible"
                width="780px"
                :close-on-click-modal="false"
                custom-class="col-dialog"
            >
                <p class="text-muted" style="margin:-8px 0 14px;">
                    Selecciona y ordena las columnas visibles por defecto.
                </p>

                <!-- Wizard tabs (indicadores) -->
                <div class="wz-tabs">
                    <span class="wz-tab"><span class="wz-num">1</span> Elige columnas</span>
                    <span class="wz-sep"></span>
                    <span class="wz-tab"><span class="wz-num">2</span> Ordena</span>
                    <span class="wz-sep"></span>
                    <span class="wz-tab"><span class="wz-num">3</span> Vista previa</span>
                </div>

                <!-- Dual panel -->
                <div class="dp-wrap">
                    <!-- Izquierda: Todas las columnas con checkbox -->
                    <div class="dp-panel">
                        <div class="dp-panel__head">
                            <span class="dp-panel__label">COLUMNAS</span>
                            <span class="dp-panel__count">{{ activeColumnsComputed.length }} / {{ allColumns.length }}</span>
                        </div>
                        <div class="dp-panel__search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input v-model="searchAvailable" placeholder="Buscar..." class="dp-search-input" />
                        </div>
                        <div class="dp-panel__search bg-light">
                            <small class="text-muted" style="word-break: keep-all;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-checkbox"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                                Haz clic en una columna para agregarla al listado activo
                            </small>
                        </div>
                        <div class="dp-panel__list">
                            <div
                                v-for="col in filteredAllColumns"
                                :key="col.key"
                                class="dp-item dp-item--toggle"
                                :class="{ 'dp-item--checked': isActive(col.key) }"
                                @click="toggleColumn(col.key)"
                            >
                                <span class="dp-item__title">
                                    {{ col.title }}
                                    <el-tooltip
                                        v-if="col.key === 'personalized'"
                                        effect="dark"
                                        placement="top"
                                        content="Controla la posición y visibilidad por defecto del grupo de campos personalizados de este listado. Su orden define dónde se insertarán todos los campos personalizados, y su visibilidad establece el estado inicial con que los verán los usuarios."
                                    >
                                        <span class="dp-item__info" @click.stop>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: -2px;"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                        </span>
                                    </el-tooltip>
                                </span>
                                <span class="dp-item__check" :class="{ 'dp-item__check--on': isActive(col.key) }">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                            </div>
                            <p v-if="filteredAllColumns.length === 0" class="dp-panel__empty">Sin resultados</p>
                        </div>
                    </div>

                    <!-- Derecha: Activas -->
                    <div class="dp-panel">
                        <div class="dp-panel__head">
                            <span class="dp-panel__label">COLUMNAS ACTIVAS</span>
                            <span class="dp-panel__count">{{ activeColumnsComputed.length }}</span>
                        </div>
                        <div class="dp-panel__search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input v-model="searchActive" placeholder="Buscar..." class="dp-search-input" />
                        </div>
                        <div class="dp-panel__search bg-light">
                            <small class="text-muted" style="word-break: keep-all;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-drag-drop"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19 11v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" /><path d="M13 13l9 3l-4 2l-2 4l-3 -9" /><path d="M3 3l0 .01" /><path d="M7 3l0 .01" /><path d="M11 3l0 .01" /><path d="M15 3l0 .01" /><path d="M3 7l0 .01" /><path d="M3 11l0 .01" /><path d="M3 15l0 .01" /></svg>
                                Estas columnas se mostrarán en este orden. Arrastra
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-grip-vertical"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M8 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 12a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M14 19a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                                para reordenar.
                            </small>
                        </div>
                        <div class="dp-panel__list">
                            <draggable
                                v-model="activeColumnsComputed"
                                handle=".drag-handle"
                                animation="150"
                                ghost-class="dp-item--ghost"
                            >
                                <div
                                    v-for="col in activeColumnsComputed"
                                    v-show="!searchActive || col.title.toLowerCase().includes(searchActive.toLowerCase())"
                                    :key="col.key"
                                    class="dp-item dp-item--active"
                                >
                                    <span class="drag-handle">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="5" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="19" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="5" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="19" r="1" fill="currentColor" stroke="none"/></svg>
                                    </span>
                                    <span class="dp-item__title">{{ col.title }}</span>
                                    <button class="dp-item__remove" @click.stop="deactivateColumn(col.key)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                            </draggable>
                            <p v-if="activeColumnsComputed.length === 0" class="dp-panel__empty">Sin columnas activas</p>
                        </div>
                    </div>
                </div>

                <!-- Vista previa -->
                <div class="pv-wrap">
                    <span class="pv-label d-flex align-items-center justify-content-between">
                        <p class="m-0">VISTA PREVIA</p>
                        <small class="text-muted d-flex align-items-center fw-normal">Así verán los usuarios el listado con esta configuración</small>
                    </span>
                    <div class="pv-scroll">
                        <table class="pv-table" v-if="activeColumnsComputed.length > 0">
                            <thead>
                                <tr>
                                    <th v-for="col in activeColumnsComputed" :key="col.key">{{ col.title }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, i) in sampleRows" :key="i">
                                    <td v-for="col in activeColumnsComputed" :key="col.key">
                                        <span v-if="col.type === 'status'" :class="['pv-badge', 'pv-badge--' + i]">{{ row[col.key] }}</span>
                                        <span v-else>{{ row[col.key] }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-else class="pv-empty">Sin columnas activas</p>
                    </div>
                </div>

                <span slot="footer" class="col-dialog__footer">
                    <span class="col-dialog__count">{{ activeColumnsComputed.length }} columnas activas</span>
                    <div>
                        <el-button @click="resetToDefault">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;vertical-align:-2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/></svg>
                            Restablecer
                        </el-button>
                        <el-button @click="dialogVisible = false">Cancelar</el-button>
                        <el-button type="primary" :loading="saving" @click="saveModule">Guardar</el-button>
                    </div>
                </span>
            </el-dialog>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'

const SAMPLE_BY_TYPE = {
    id:       [1, 2, 3],
    code:     ['P001', 'P002', 'S001'],
    unit:     ['NIU', 'NIU', 'ZZ'],
    image:    ['🖼', '🖼', '🖼'],
    text:     ['Ejemplo 1', 'Ejemplo 2', 'Ejemplo 3'],
    longtext: ['Descripción del producto A', 'Descripción del producto B', 'Descripción del servicio'],
    number:   [24, 80, 0],
    price:    ['2,500.00', '45.00', '120.00'],
    boolean:  ['Sí', 'Sí', 'No'],
    date:     ['01/05/2026', '05/05/2026', '10/05/2026'],
    document: ['F001-00001', 'B001-00002', 'F002-00003'],
    customer: ['Juan Pérez García', 'Empresa SAC', 'María López'],
    sunat:    ['43211503', '43211706', '81112100'],
    status:   ['Pendiente', 'En proceso', 'Cerrado'],
    currency: ['PEN', 'USD', 'PEN'],
    exchange: ['3.75', '3.80', '3.72'],
    action:   ['···', '···', '···'],
};

const MODULES = {
    order_notes_index: {
        label: 'Pedidos',
        columns: {
            date_of_issue:    { title: 'Fecha Emisión',       visible: true,  type: 'date'     },
            delivery_date:    { title: 'F.Entrega',           visible: true,  type: 'date'     },
            seller:           { title: 'Vendedor',            visible: true,  type: 'text'     },
            customer:         { title: 'Cliente',             visible: true,  type: 'customer' },
            state_type:       { title: 'Estado',              visible: true,  type: 'status'   },
            personalized:     { title: 'Personalizados',      visible: true,  type: 'text'     },
            identifier:       { title: 'Pedido',              visible: true,  type: 'document' },
            documents:        { title: 'Comprobantes',        visible: true,  type: 'document' },
            sale_notes:       { title: 'Notas de venta',      visible: true,  type: 'document' },
            quotation:        { title: 'Cotización',          visible: false, type: 'document' },
            dispatches:       { title: 'Guías de Remisión',   visible: false, type: 'document' },
            mi_tienda_pe:     { title: 'Pedido MiTienda.Pe',  visible: false, type: 'text'     },
            currency_type:    { title: 'Moneda',              visible: true,  type: 'currency' },
            total_exportation:{ title: 'T.Exportación',       visible: false, type: 'price'    },
            total_unaffected: { title: 'T.Inafecto',          visible: false, type: 'price'    },
            total_exonerated: { title: 'T.Exonerado',         visible: false, type: 'price'    },
            total_taxed:      { title: 'T.Gravado',           visible: true,  type: 'price'    },
            total_igv:        { title: 'T.IGV',               visible: true,  type: 'price'    },
            balance:          { title: 'Saldo',               visible: true,  type: 'price'    },
            total:            { title: 'Total',               visible: true,  type: 'price'    },
            pdf:              { title: 'PDF',                 visible: true,  type: 'action'   },
            actions:          { title: 'Acciones',            visible: true,  type: 'action'   },
        },
    },
    contracts_index: {
        label: 'Contratos',
        columns: {
            date_of_issue:   { title: 'Fecha Emisión', visible: true,  type: 'date'     },
            delivery_date:   { title: 'F.Entrega',     visible: false, type: 'date'     },
            seller:          { title: 'Vendedor',      visible: true,  type: 'text'     },
            customer:        { title: 'Cliente',       visible: true,  type: 'customer' },
            state_type:      { title: 'Estado',        visible: true,  type: 'status'   },
            number:          { title: 'Contrato',      visible: true,  type: 'document' },
            quotation:       { title: 'Cotización',    visible: true,  type: 'document' },
            currency_type:   { title: 'Moneda',        visible: true,  type: 'currency' },
            total_exportation:{ title: 'T.Exportación',visible: false, type: 'price'    },
            total_free:      { title: 'T.Gratuito',    visible: false, type: 'price'    },
            total_unaffected:{ title: 'T.Inafecto',    visible: false, type: 'price'    },
            total_exonerated:{ title: 'T.Exonerado',   visible: false, type: 'price'    },
            total_taxed:     { title: 'T.Gravado',     visible: true,  type: 'price'    },
            total_igv:       { title: 'T.Igv',         visible: true,  type: 'price'    },
            total:           { title: 'Total',         visible: true,  type: 'price'    },
            actions:         { title: 'Acciones',      visible: true,  type: 'action'   },
        },
    },
    quotations_index: {
        label: 'Cotizaciones',
        columns: {
            date_of_issue:           { title: 'Fecha Emisión',      visible: true,  type: 'date'     },
            delivery_date:           { title: 'T.Entrega',          visible: false, type: 'date'     },
            registered_by:           { title: 'Registrado por',     visible: false, type: 'text'     },
            seller:                  { title: 'Vendedor',           visible: false, type: 'text'     },
            customer:                { title: 'Cliente',            visible: true,  type: 'customer' },
            state_type:              { title: 'Estado',             visible: true,  type: 'status'   },
            identifier:              { title: 'Cotización',         visible: true,  type: 'document' },
            documents:               { title: 'Comprobantes',       visible: false, type: 'document' },
            sale_notes:              { title: 'Notas de venta',     visible: false, type: 'document' },
            order_note:              { title: 'Pedidos',            visible: false, type: 'document' },
            sale_opportunity:        { title: 'Oportunidad Venta',  visible: false, type: 'document' },
            referential_information: { title: 'Inf.Referencial',    visible: false, type: 'text'     },
            contract:                { title: 'Contrato',           visible: false, type: 'text'     },
            exchange_rate_sale:      { title: 'T. de cambio',       visible: false, type: 'exchange' },
            currency_type_id:        { title: 'Moneda',             visible: false, type: 'currency' },
            payments:                { title: 'Pagos',              visible: true,  type: 'price'    },
            total_exportation:       { title: 'T.Exportación',      visible: false, type: 'price'    },
            total_free:              { title: 'T.Gratuito',         visible: false, type: 'price'    },
            total_unaffected:        { title: 'T.Inafecto',         visible: false, type: 'price'    },
            total_exonerated:        { title: 'T.Exonerado',        visible: false, type: 'price'    },
            total_taxed:             { title: 'T.Gravado',          visible: true,  type: 'price'    },
            total_igv:               { title: 'T.Igv',              visible: true,  type: 'price'    },
            total:                   { title: 'Total',              visible: true,  type: 'price'    },
            pdf:                     { title: 'PDF',                visible: true,  type: 'action'   },
            actions:                 { title: 'Acciones',           visible: true,  type: 'action'   },
        },
    },
    purchases_index: {
        label: 'Listado de compras',
        columns: {
            date_of_issue:    { title: 'F. Emisión',       visible: true,  type: 'date'     },
            date_of_due:      { title: 'F. Vencimiento',   visible: false, type: 'date'     },
            supplier:         { title: 'Proveedor',        visible: true,  type: 'customer' },
            state_type:       { title: 'Estado',           visible: true,  type: 'status'   },
            payment_state:    { title: 'Estado de pago',   visible: true,  type: 'status'   },
            number:           { title: 'Número',           visible: true,  type: 'document' },
            products:         { title: 'Productos',        visible: true,  type: 'text'     },
            warehouse:        { title: 'Almacén',          visible: true,  type: 'text'     },
            payments:         { title: 'Pagos',            visible: true,  type: 'action'   },
            currency_type:    { title: 'Moneda',           visible: true,  type: 'currency' },
            guides:           { title: 'Guias',            visible: false, type: 'document' },
            purchase_order:   { title: 'Orden de Compra',  visible: false, type: 'document' },
            total_free:       { title: 'T.Gratuita',       visible: false, type: 'price'    },
            total_unaffected: { title: 'T.Inafecta',       visible: false, type: 'price'    },
            total_exonerated: { title: 'T.Exonerado',      visible: false, type: 'price'    },
            total_taxed:      { title: 'T.Gravado',        visible: false, type: 'price'    },
            total_igv:        { title: 'T.Igv',            visible: false, type: 'price'    },
            total_perception: { title: 'Percepción',       visible: false, type: 'price'    },
            total:            { title: 'Total',            visible: true,  type: 'price'    },
            actions:          { title: 'Acciones',         visible: true,  type: 'action'   },
        },
    },
    sale_notes_index: {
        label: 'Notas de venta',
        columns: {
            seller_name:        { title: 'Vendedor',             visible: false, type: 'text'     },
            date_of_issue:      { title: 'Fecha Emisión',        visible: true,  type: 'date'     },
            date_payment:       { title: 'Fecha de pago',        visible: false, type: 'date'     },
            customer:           { title: 'Cliente',              visible: true,  type: 'customer' },
            full_number:        { title: 'Nota de Venta',        visible: true,  type: 'document' },
            state_type:         { title: 'Estado',               visible: true,  type: 'status'   },
            exchange_rate_sale: { title: 'Tipo de cambio',       visible: false, type: 'exchange' },
            currency_type:      { title: 'Moneda',               visible: true,  type: 'currency' },
            due_date:           { title: 'Fecha de Vencimiento', visible: false, type: 'date'     },
            total_exportation:  { title: 'T.Exportación',        visible: false, type: 'price'    },
            total_free:         { title: 'T.Gratuito',           visible: false, type: 'price'    },
            total_unaffected:   { title: 'T.Inafecto',           visible: false, type: 'price'    },
            total_exonerated:   { title: 'T.Exonerado',          visible: false, type: 'price'    },
            total_taxed:        { title: 'T.Gravado',            visible: false, type: 'price'    },
            total_igv:          { title: 'T.IGV',                visible: false, type: 'price'    },
            total:              { title: 'Total',                visible: true,  type: 'price'    },
            total_paid:         { title: 'Pagado',               visible: false, type: 'price'    },
            total_pending_paid: { title: 'Por pagar',            visible: false, type: 'price'    },
            documents:          { title: 'Comprobantes',         visible: true,  type: 'document' },
            payment_status:     { title: 'Estado pago',          visible: true,  type: 'status'   },
            purchase_order:     { title: 'Orden de compra',      visible: true,  type: 'document' },
            payments:           { title: 'Pagos',                visible: true,  type: 'action'   },
            download:           { title: 'Descarga',             visible: true,  type: 'action'   },
            personalized:       { title: 'Personalizados',       visible: true,  type: 'text'     },
            recurrence:         { title: 'Recurrencia',          visible: false, type: 'text'     },
            region:             { title: 'Región',               visible: false, type: 'text'     },
            dispatch_status:    { title: 'Estado de despacho',   visible: false, type: 'status'   },
            type_period:        { title: 'Tipo Periodo',         visible: true,  type: 'text'     },
            quantity_period:    { title: 'Cantidad Periodo',     visible: true,  type: 'number'   },
            paid:               { title: 'Estado de Pago',       visible: false, type: 'status'   },
            license_plate:      { title: 'Placa',                visible: true,  type: 'text'     },
            actions:            { title: 'Acciones',             visible: true,  type: 'action'   },
        },
    },
    document_index: {
        label: 'Boleta / Factura',
        columns: {
            soap_type:         { title: 'Soap',                       visible: false, type: 'text'     },
            date_of_issue:     { title: 'Emisión',                    visible: true,  type: 'date'     },
            date_payment:      { title: 'Fecha de pago',              visible: false, type: 'date'     },
            date_of_due:       { title: 'F. Vencimiento',             visible: false, type: 'date'     },
            customer:          { title: 'Cliente',                    visible: true,  type: 'customer' },
            number:            { title: 'Número',                     visible: true,  type: 'document' },
            notes:             { title: 'Notas C/D',                  visible: false, type: 'document' },
            dispatch:          { title: 'Guía de Remisión',           visible: false, type: 'document' },
            sales_note:        { title: 'Nota de ventas',             visible: false, type: 'document' },
            order_note:        { title: 'Pedidos',                    visible: false, type: 'document' },
            send_it:           { title: 'Correo enviado',             visible: false, type: 'boolean'  },
            state_type:        { title: 'Estado',                     visible: true,  type: 'status'   },
            personalized:      { title: 'Personalizados',             visible: true,  type: 'text'     },
            user_name:         { title: 'Usuario',                    visible: false, type: 'text'     },
            exchange_rate_sale:{ title: 'Tipo de cambio',             visible: false, type: 'exchange' },
            currency_type_id:  { title: 'Moneda',                     visible: false, type: 'currency' },
            guides:            { title: 'Guías',                      visible: false, type: 'document' },
            plate_numbers:     { title: 'Placa',                      visible: false, type: 'text'     },
            total_exportation: { title: 'T.Exportación',              visible: false, type: 'price'    },
            total_free:        { title: 'T.Gratuito',                 visible: false, type: 'price'    },
            total_unaffected:  { title: 'T.Inafecto',                 visible: false, type: 'price'    },
            total_exonerated:  { title: 'T.Exonerado',                visible: false, type: 'price'    },
            total_charge:      { title: 'T.Cargos',                   visible: false, type: 'price'    },
            total_taxed:       { title: 'T.Gravado',                  visible: true,  type: 'price'    },
            total_igv:         { title: 'T.Igv',                      visible: true,  type: 'price'    },
            total:             { title: 'Total',                      visible: false, type: 'price'    },
            balance:           { title: 'Saldo',                      visible: true,  type: 'price'    },
            purchase_order:    { title: 'Orden de Compra',            visible: false, type: 'document' },
            downloads:         { title: 'Descargas (XML/PDF/CDR)',     visible: true,  type: 'action'   },
            actions:           { title: 'Acciones',                   visible: true,  type: 'action'   },
        },
    },
    sale_opportunities_index: {
        label: 'Oportunidad de venta',
        columns: {
            date_of_issue:    { title: 'Fecha Emisión', visible: true,  type: 'date'     },
            sale:             { title: 'Vendedor',      visible: false, type: 'text'     },
            customer:         { title: 'Cliente',       visible: true,  type: 'customer' },
            state_type:       { title: 'Estado',        visible: true,  type: 'status'   },
            number:           { title: 'O. Venta',      visible: true,  type: 'document' },
            quotation:        { title: 'Cotización',    visible: true,  type: 'document' },
            purchase_order:   { title: 'O. Compra',     visible: true,  type: 'document' },
            currency_type:    { title: 'Moneda',        visible: true,  type: 'currency' },
            files:            { title: 'Archivos',      visible: true,  type: 'action'   },
            total_exportation:{ title: 'T.Exportación', visible: false, type: 'price'    },
            total_unaffected: { title: 'T.Inafecto',    visible: false, type: 'price'    },
            total_exonerated: { title: 'T.Exonerado',   visible: false, type: 'price'    },
            total_taxed:      { title: 'T.Gravado',     visible: false, type: 'price'    },
            total_igv:        { title: 'T.IGV',         visible: false, type: 'price'    },
            total:            { title: 'Total',         visible: true,  type: 'price'    },
            actions:          { title: 'Acciones',      visible: true,  type: 'action'   },
        },
    },
    items_index: {
        label: 'Productos',
        columns: {
            id:                          { title: 'ID',                       visible: true,  type: 'id'       },
            internal_id:                 { title: 'Cód. Interno',             visible: true,  type: 'code'     },
            unit_type:                   { title: 'Unidad',                   visible: true,  type: 'unit'     },
            image:                       { title: 'Imagen',                   visible: true,  type: 'image'    },
            name:                        { title: 'Nombre',                   visible: true,  type: 'text'     },
            description:                 { title: 'Descripción',              visible: false, type: 'longtext' },
            model:                       { title: 'Modelo',                   visible: false, type: 'text'     },
            brand:                       { title: 'Marca',                    visible: false, type: 'text'     },
            item_code:                   { title: 'Cód. SUNAT',               visible: false, type: 'sunat'    },
            history:                     { title: 'Historial',                visible: true,  type: 'action'   },
            stock:                       { title: 'Stock',                    visible: true,  type: 'number'   },
            sale_unit_price:             { title: 'P. Unitario (Venta)',      visible: true,  type: 'price'    },
            purchase_unit_price:         { title: 'P. Unitario (Compra)',     visible: false, type: 'price'    },
            real_unit_price:             { title: 'P. Venta total (con IGV)', visible: false, type: 'price'    },
            has_igv:                     { title: 'Tiene IGV (Venta)',        visible: true,  type: 'boolean'  },
            purchase_has_igv_description:{ title: 'Tiene IGV (Compra)',       visible: false, type: 'boolean'  },
            actions:                     { title: 'Acciones',                 visible: true,  type: 'action'   },
        },
    },
};

export default {
    components: { draggable },
    computed: {
        editingModuleLabel() {
            const mod = MODULES[this.editingModuleKey];
            return mod ? mod.label : '';
        },
        // Computed con getter+setter para el draggable
        activeColumnsComputed: {
            get() {
                return this.allColumns.filter(c => c.visible);
            },
            set(newActiveOrder) {
                // Reinserta el nuevo orden de activas manteniendo las inactivas en su posición relativa
                const result = [];
                let activeIdx = 0;
                for (const col of this.allColumns) {
                    if (col.visible) {
                        result.push(newActiveOrder[activeIdx++]);
                    } else {
                        result.push(col);
                    }
                }
                this.allColumns = result;
            },
        },
        filteredAllColumns() {
            const q = this.searchAvailable.toLowerCase();
            if (!q) return this.allColumns;
            return this.allColumns.filter(c => c.title.toLowerCase().includes(q));
        },
        sampleRows() {
            if (!this.editingModuleKey) return [];
            return [0, 1, 2].map(i => {
                const row = {};
                this.activeColumnsComputed.forEach(col => {
                    row[col.key] = (SAMPLE_BY_TYPE[col.type] ?? ['—', '—', '—'])[i];
                });
                return row;
            });
        },
    },
    data() {
        return {
            dialogVisible: false,
            saving: false,
            editingModuleKey: null,
            allColumns: [],
            searchAvailable: '',
            searchActive: '',
            savedConfigs: {},
            openSections: null,
            sections: [
                {
                    key: 'presale',
                    label: 'Preventa',
                    color: '#8b5cf6',
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>',
                    modules: ['sale_opportunities_index', 'quotations_index', 'contracts_index', 'order_notes_index'],
                },
                {
                    key: 'sales',
                    label: 'Ventas',
                    color: '#10b981',
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-receipt-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M14 8h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5m2 0v1.5m0 -9v1.5" /></svg>',
                    modules: ['document_index', 'sale_notes_index'],
                }, 
                {
                    key: 'purchases',
                    label: 'Compras',
                    color: '#f59e0b',
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>',
                    modules: ['purchases_index'],
                },               
                {
                    key: 'products',
                    label: 'Productos / Servicios',
                    color: '#ef4444',
                    svg: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 4h6v6h-6l0 -6" /><path d="M14 4h6v6h-6l0 -6" /><path d="M4 14h6v6h-6l0 -6" /><path d="M14 17a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>',
                    modules: ['items_index'],
                },
            ],
        };
    },
    created() {
        this.loadConfigs();
    },
    methods: {
        moduleLabel(key) {
            return MODULES[key] ? MODULES[key].label : key;
        },
        async loadConfigs() {
            try {
                const res = await this.$http.get('/configurations/column-visibility');
                if (res.data.success) {
                    this.savedConfigs = res.data.data;
                }
            } catch (_) {}
        },
        openDialog(moduleKey) {
            const mod = MODULES[moduleKey];
            if (!mod) return;

            this.editingModuleKey = moduleKey;
            this.searchAvailable = '';
            this.searchActive = '';

            // Construir array con order y defaultOrder (posición fija del MODULES, nunca cambia)
            const allCols = Object.entries(mod.columns).map(([key, col], idx) => ({
                key,
                title: col.title,
                type: col.type,
                visible: col.visible,
                order: idx,
                defaultOrder: idx,
            }));

            // Aplicar configuración guardada (visible + order, pero NO defaultOrder)
            const saved = this.savedConfigs[moduleKey];
            if (saved && saved.columns) {
                allCols.forEach(col => {
                    const s = saved.columns[col.key];
                    if (s !== undefined) {
                        col.visible = s.visible;
                        if (s.order !== undefined) col.order = s.order;
                    }
                });
            }

            allCols.sort((a, b) => a.order - b.order);
            this.allColumns = allCols;

            this.dialogVisible = true;
        },
        isActive(key) {
            const col = this.allColumns.find(c => c.key === key);
            return col ? col.visible : false;
        },
        toggleColumn(key) {
            const col = this.allColumns.find(c => c.key === key);
            if (col) this.$set(col, 'visible', !col.visible);
        },
        deactivateColumn(key) {
            const col = this.allColumns.find(c => c.key === key);
            if (col) this.$set(col, 'visible', false);
        },
        resetToDefault() {
            const mod = MODULES[this.editingModuleKey];
            if (!mod) return;
            this.allColumns = Object.entries(mod.columns).map(([key, col], idx) => ({
                key,
                title: col.title,
                type: col.type,
                visible: col.visible,
                order: idx,
                defaultOrder: idx,
            }));
        },
        async saveModule() {
            try {
                await this.$confirm(
                    'Esta acción reemplazará la configuración de columnas de <strong>todos los usuarios</strong> que tienen su propia configuración guardada para este listado. ¿Deseas continuar?',
                    'Advertencia',
                    {
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar',
                        type: 'warning',
                        dangerouslyUseHTMLString: true,
                    }
                );
            } catch (_) {
                return;
            }

            this.saving = true;

            const columns = {};
            // Guardar con el order de su posición en allColumns
            this.allColumns.forEach((col, idx) => {
                columns[col.key] = { title: col.title, visible: col.visible, order: idx };
            });

            try {
                const res = await this.$http.post(
                    `/configurations/column-visibility/${this.editingModuleKey}`,
                    { columns }
                );
                if (res.data.success) {
                    this.$message.success(res.data.message);
                    await this.loadConfigs();
                    this.dialogVisible = false;
                } else {
                    this.$message.error(res.data.message);
                }
            } catch (_) {
                this.$message.error('Error al guardar la configuración');
            } finally {
                this.saving = false;
            }
        },
    },
};
</script>

<style scoped>
/* ===== Collapse ===== */
.vc-collapse {
    border: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.vc-collapse >>> .el-collapse-item {
    border: 1px solid var(--accent-color);
    border-radius: 10px;
    overflow: hidden;
    margin: 0;
}
.vc-collapse >>> .el-collapse-item__header {
    height: 56px;
    padding: 0 16px;
    border-bottom: none;
    border-radius: 10px;
    font-size: 0.9rem;
}
.vc-collapse >>> .el-collapse-item__header.is-active {
    border-radius: 10px 10px 0 0;
    border-bottom: none;
    background-color: var(--light-color);
}
.vc-collapse >>> .el-collapse-item__arrow { margin-left: 8px; }
.vc-collapse >>> .el-collapse-item__wrap {
    border-bottom: none;
    border-radius: 0 0 10px 10px;
}
.vc-collapse >>> .el-collapse-item__content { padding: 0; }

/* ===== Section header ===== */
.vc-section__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1;
    padding-right: 4px;
}
.vc-section__icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.vc-section__title { font-weight: 600; }
.vc-section__count { font-size: 0.78rem; }

/* ===== Module items ===== */
.vc-module-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 10px 10px 15px;
    border: 1px solid var(--accent-color);
    margin: 10px;
    border-radius: 6px;
    background-color: #fff;
}
.vc-module-item:hover { background-color: #fff; }
.vc-module-item__dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
    opacity: 0.3;
}
.vc-module-item:hover .vc-module-item__dot { opacity: 1; }
.vc-module-item__label { font-size: 0.875rem; }

/* ===== Dialog override ===== */
.col-dialog >>> .el-dialog__body { padding: 16px 20px 8px; }
.col-dialog >>> .el-dialog__footer { padding: 12px 20px 16px; border-top: 1px solid var(--accent-color); }

/* ===== Wizard tabs ===== */
.wz-tabs {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 16px;
}
.wz-tab {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 500;
}
.wz-tab--active { font-weight: 600; }
.wz-num {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e5e7eb;
    font-size: 0.72rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.wz-tab--active .wz-num { background: var(--primary-color); color: #fff; }
.wz-sep {
    flex: 1;
    height: 1px;
    background: #e5e7eb;
    max-width: 40px;
}

/* ===== Dual panel ===== */
.dp-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}
.dp-panel {
    border: 1px solid var(--accent-color);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.dp-panel__head {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: var(--light-color);
    border-bottom: 1px solid var(--accent-color);
}
.dp-panel__label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
}
.dp-panel__count {
    font-size: 0.7rem;
    font-weight: 700;
    background: #e5e7eb;
    border-radius: 10px;
    padding: 1px 7px;
}
.dp-panel__search {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-bottom: 1px solid var(--accent-color);
}
.dp-search-input {
    border: none;
    outline: none;
    font-size: 0.8rem;
    width: 100%;
    background: transparent;
}
.dp-panel__list {
    flex: 1;
    overflow-y: auto;
    max-height: 220px;
    padding: 4px 0;
}
.dp-panel__list::-webkit-scrollbar { width: 4px; }
.dp-panel__list::-webkit-scrollbar-thumb { background: var(--accent-color); border-radius: 4px; }

.pv-scroll::-webkit-scrollbar { height: 4px; }
.pv-scroll::-webkit-scrollbar-thumb { background: var(--accent-color); border-radius: 4px; }

.dp-panel__empty {
    text-align: center;
    font-size: 0.78rem;
    padding: 16px;
    margin: 0;
}

/* ===== Items ===== */
.dp-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    font-size: 0.82rem;
    transition: background 0.1s;
}
.dp-item--avail {
    cursor: pointer;
}
.dp-item--avail:hover { background: var(--accent-color); }
.dp-item--active {
    cursor: default;
}
.dp-item--active:hover { background: var(--light-color); }
.dp-item--ghost { opacity: 0.4; background: #f0f4ff !important; }

/* Toggle items (panel izquierdo con todas las columnas) */
.dp-item--toggle { cursor: pointer; }
.dp-item--toggle:hover { background: var(--light-color); }

.dp-item__title { flex: 1; }

.dp-item__info {
    color: var(--muted);
    cursor: help;
    margin-right: 6px;
    transition: color 0.15s;
}
.dp-item__info:hover { color: var(--primary-color); }

.dp-item__check {
    width: 18px;
    height: 18px;
    border: 1.5px solid var(--accent-color);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: transparent;
    transition: all 0.15s;
    background-color: #fff;
}
.dp-item--avail:hover .dp-item__check {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: #fff;
}
/* Checkbox activo (columna activa) */
.dp-item__check--on {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: #fff;
}

.drag-handle {
    cursor: grab;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    padding: 2px;
    border-radius: 3px;
}
.drag-handle:hover { background: #f3f4f6; }
.drag-handle:active { cursor: grabbing; }

.dp-item__remove {
    border: none;
    background: none;
    padding: 2px;
    cursor: pointer;
    color: var(--muted);
    display: flex;
    align-items: center;
    border-radius: 3px;
    flex-shrink: 0;
    line-height: 1;
}
.dp-item__remove:hover { color: var(--danger); background: #fef2f2; }

/* ===== Preview ===== */
.pv-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
}
.pv-scroll {
    overflow-x: auto;
    border: 1px solid var(--accent-color);
    border-radius: 6px;
}
.pv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.75rem;
    white-space: nowrap;
}
.pv-table thead th {
    background: var(--light-color);
    padding: 6px 10px;
    border-bottom: 1px solid var(--accent-color);
    font-weight: 600;
    text-align: left;
}
.pv-table tbody td {
    padding: 5px 10px;
    border-bottom: 1px solid var(--accent-color);
}
.pv-table tbody tr:last-child td { border-bottom: none; }
.pv-empty {
    text-align: center;
    color: #bbb;
    font-size: 0.8rem;
    padding: 14px;
    margin: 0;
}

/* Status badges en preview */
.pv-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.72rem;
    font-weight: 600;
}
.pv-badge--0 { background: #fef9c3; color: #a16207; }
.pv-badge--1 { background: #dbeafe; color: #1d4ed8; }
.pv-badge--2 { background: #f3f4f6; color: #6b7280; }

/* ===== Footer ===== */
.col-dialog__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}
.col-dialog__count {
    font-size: 0.8rem;
    color: #888;
}
</style>
