<template>
    <div class="" v-loading="loading">
        <!-- Encabezado de página -->
        <div class="page-header pe-0">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-book-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12" /><path d="M19 16h-12a2 2 0 0 0 -2 2" /><path d="M9 8h6" /></svg>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Libro de Reclamaciones</span></li>
            </ol>
            <div class="right-wrapper pull-right d-flex gap-2 h-100 py-2 me-4">
                <el-button
                    class="btn btn-custom btn-sm"
                    @click="showStatusModal = true"
                    title="Gestionar estados"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37c1 .608 2.296.07 2.572-1.065z"/>
                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0-6 0"/>
                    </svg>
                    Gestionar estados
                </el-button>
                <el-button
                    class="btn btn-custom btn-sm"
                    @click="showChannelsModal = true"
                    title="Canales de atención"
                >
                    <i class="el-icon-connection"></i> Canales
                </el-button>
                <el-button
                    class="btn btn-custom btn-sm"
                    @click="showEmbedModal = true"
                    title="Código de integración (embed)"
                >
                    <i class="el-icon-share"></i> Integrar en mi web
                </el-button>
            </div>
        </div>

        <!-- Métricas -->
        <claims-metrics ref="metrics"></claims-metrics>

        <!-- Panel principal -->
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <div class="btn-filter-content" style="display:flex; align-items:center; justify-content:space-between;">
                    <el-button
                        type="secondary"
                        class="btn-show-filter mb-3"
                        :class="{ shift: isVisible }"
                        @click="toggleInformation"
                    >
                        {{ isVisible ? "Ocultar filtros" : "Mostrar filtros" }}
                    </el-button>
                    <el-button
                        :type="highlightRows ? 'primary' : 'secondary'"
                        class="mb-3 ms-auto me-2"
                        @click="highlightRows = !highlightRows"
                    >
                        <i :class="highlightRows ? 'el-icon-magic-stick' : 'el-icon-magic-stick'"></i>
                        {{ highlightRows ? 'Quitar resaltado' : 'Resaltar filas' }}
                    </el-button>
                    <el-dropdown :hide-on-click="false" class="mb-3">
                        <el-button type="secondary">
                            Mostrar columnas <i class="el-icon-arrow-down el-icon--right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item v-for="(col, key) in tableColumns" :key="key">
                                <el-checkbox v-model="col.visible" @change="saveTableColumns">
                                    {{ col.title }}
                                </el-checkbox>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
                <div class="row mx-0 g-2" v-if="applyFilter && isVisible">
                    <!-- Fila 1: Código · Nombre/doc · Fecha -->
                    <div class="col-lg-2 col-md-3 col-sm-6 col-12">
                        <el-input
                            v-model="filters.code"
                            placeholder="Código"
                            size="small"
                            clearable
                            @input="onFilterChange"
                            @clear="onFilterChange"
                        ></el-input>
                    </div>
                    <div class="col-lg-4 col-md-5 col-sm-6 col-12">
                        <el-input
                            v-model="filters.search"
                            placeholder="Nombre o N° documento"
                            size="small"
                            clearable
                            @input="onFilterChange"
                            @clear="onFilterChange"
                        ></el-input>
                    </div>
                    <div class="col-lg-6 col-md-4 col-sm-12 col-12">
                        <el-date-picker
                            v-model="filters.dateRange"
                            type="daterange"
                            start-placeholder="Fecha inicio"
                            end-placeholder="Fecha fin"
                            value-format="yyyy-MM-dd"
                            size="small"
                            style="width:100%"
                            @change="onFilterChange"
                        ></el-date-picker>
                    </div>
                    <!-- Fila 2: Estado · Tipo · Canal · Registros -->
                    <div class="col-lg-3 col-md-4 col-sm-12 col-12">
                        <el-select
                            v-model="filters.status_claim_id"
                            placeholder="Estado"
                            size="small"
                            clearable
                            style="width:100%"
                            @change="onFilterChange"
                        >
                            <el-option
                                v-for="s in tables.status_claims"
                                :key="s.id"
                                :label="s.description"
                                :value="s.id"
                            ></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <el-select
                            v-model="filters.claim_type"
                            placeholder="Tipo"
                            size="small"
                            clearable
                            style="width:100%"
                            @change="onFilterChange"
                        >
                            <el-option value="queja" label="Queja"></el-option>
                            <el-option value="reclamo" label="Reclamo"></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <el-select
                            v-model="filters.channel"
                            placeholder="Canal"
                            size="small"
                            clearable
                            style="width:100%"
                            @change="onFilterChange"
                        >
                            <el-option
                                v-for="c in tables.claim_channels"
                                :key="c.id"
                                :label="c.name"
                                :value="c.name"
                            ></el-option>
                        </el-select>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                        <el-select
                            v-model="showClosed"
                            placeholder="Registros"
                            size="small"
                            style="width:100%"
                            @change="onFilterChange"
                        >
                            <el-option :value="false" label="Ocultar cerrados"></el-option>
                            <el-option :value="true" label="Mostrar cerrados"></el-option>
                        </el-select>
                    </div>
                </div>
                <!-- Tabla de reclamos — componente desacoplado -->
                <claims-data-table
                    :records="records"
                    :status-claims="tables.status_claims"
                    :loading="loading"
                    :pagination="pagination"
                    :columns="tableColumns"
                    :highlight-rows="highlightRows"
                    @status-change="onStatusChange"
                    @assign-change="onAssignChange"
                    @view="viewDetail"
                    @page-change="onPageChange"
                ></claims-data-table>
            </div>
        </div>

        <!-- Dialog unificado de cambio de estado (resolución / reapertura) -->
        <claim-status-change-dialog
            :visible.sync="showStatusChangeDialog"
            :mode="statusChangeMode"
            :loading="savingStatus"
            @confirm="confirmStatusChange"
            @cancel="cancelStatusChange"
        ></claim-status-change-dialog>

        <!-- Modal de estados -->
        <status-claim-modal
            :showDialog.sync="showStatusModal"
        ></status-claim-modal>

        <!-- Modal de canales -->
        <claim-channels-modal
            :showDialog.sync="showChannelsModal"
        ></claim-channels-modal>

        <!-- Modal de detalle -->
        <claim-detail-modal
            :showDialog.sync="showDetailModal"
            :claimId="selectedClaimId"
            :status-claims="tables.status_claims"
            @updated="loadRecords"
        ></claim-detail-modal>

        <!-- Modal de código de integración (widget embed) -->
        <claim-embed-modal
            :visible.sync="showEmbedModal"
        ></claim-embed-modal>
    </div>
</template>

<style scoped>
.cb-index {
    min-height: 400px;
}
.cb-filters-collapse {
    border: none;
}
.cb-filters-collapse >>> .el-collapse-item__header {
    border-bottom: 1px solid #ebeef5;
    font-size: 13px;
    color: #606266;
}
.cb-filter-title {
    font-size: 13px;
    color: #409EFF;
}
.cb-filters-grid {
    padding: 12px 0;
    gap: 8px 0;
}
.cb-table {
    border-top: 0;
}
.cb-code {
    font-family: monospace;
    font-size: 12px;
    font-weight: 600;
    color: #409EFF;
}
.cb-customer-name {
    font-size: 13px;
    color: #303133;
}
.cb-customer-doc {
    font-size: 11px;
    color: #909399;
}
.cb-none {
    color: #c0c4cc;
}
.cb-pagination {
    display: flex;
    justify-content: flex-end;
    margin-top: 16px;
}
</style>

<script>
import StatusClaimModal        from './partials/status_claim_modal.vue'
import ClaimChannelsModal      from './partials/claim_channels_modal.vue'
import ClaimDetailModal        from './partials/claim_detail_modal.vue'
import ClaimsDataTable         from './components/ClaimsDataTable.vue'
import ClaimStatusChangeDialog from './partials/ClaimStatusChangeDialog.vue'
import ClaimEmbedModal         from './partials/ClaimEmbedModal.vue'
import ClaimsMetrics           from './components/ClaimsMetrics.vue'

const CLAIMS_COLS_KEY = 'claims_table_columns'
const defaultTableColumns = () => ({
    tipo:        { title: 'Tipo',        visible: true  },
    fecha:       { title: 'Fecha',       visible: true  },
    vencimiento: { title: 'Vencimiento', visible: true  },
    comprobante: { title: 'Comprobante', visible: true  },
    monto:       { title: 'Monto',       visible: false },
    canal:       { title: 'Canal',       visible: true  },
    responsable: { title: 'Responsable', visible: true  },
})

export default {
    components: {
        StatusClaimModal,
        ClaimChannelsModal,
        ClaimDetailModal,
        ClaimsDataTable,
        ClaimStatusChangeDialog,
        ClaimEmbedModal,
        ClaimsMetrics,
    },

    props: {
        applyFilter: {
            type: Boolean,
            default: true,
            required: false
        },
    },
    data() {
        return {
            loading: false,
            records: [],
            showFilters: ['filters'],

            // Filtros del listado
            filters: {
                code: '',
                search: '',
                dateRange: this.defaultDateRange(),
                status_claim_id: '',
                claim_type: '',
                channel: '',
            },

            // Paginación
            pagination: {
                total: 0,
                per_page: 20,
                current_page: 1,
            },

            // Tablas auxiliares
            tables: {
                status_claims: [],
                claim_channels: [],
            },

            // Columnas visibles de la tabla
            tableColumns: (() => {
                try {
                    const saved = localStorage.getItem(CLAIMS_COLS_KEY)
                    if (saved) {
                        const parsed = JSON.parse(saved)
                        const cols = defaultTableColumns()
                        Object.keys(cols).forEach(k => { if (parsed[k] !== undefined) cols[k].visible = parsed[k].visible })
                        return cols
                    }
                } catch (e) { /* */ }
                return defaultTableColumns()
            })(),

            // Control de modales
            showStatusModal:   false,
            showChannelsModal: false,
            showDetailModal:   false,
            showEmbedModal:    false,
            selectedClaimId:   null,
            showClosed:        false,
            highlightRows:     false,

            // Cambio de estado
            pendingStatusChange:    null,  // { row, newStatusId, oldStatusId }
            showStatusChangeDialog: false, // dialog unificado resolución/reapertura
            statusChangeMode:       'resolution', // 'resolution' | 'reopen'
            savingStatus:           false,
            isVisible: false,
        }
    },

    created() {
        this.loadTables()
        this.loadRecords()

        // Recargar estados cuando el modal de estados lo emita
        this.$eventHub.$on('statusClaimsUpdated', () => {
            this.loadTables()
        })
    },

    beforeDestroy() {
        this.$eventHub.$off('statusClaimsUpdated')
    },

    methods: {
        // ── Carga de datos ────────────────────────────────────────────

        toggleInformation() {
            this.isVisible = !this.isVisible;
        },

        saveTableColumns() {
            try {
                localStorage.setItem(CLAIMS_COLS_KEY, JSON.stringify(this.tableColumns))
            } catch (e) { /* */ }
        },

        onFilterChange() {
            this.pagination.current_page = 1
            this.loadRecords()
        },

        loadTables() {
            this.$http.get('/claims/tables').then(response => {
                this.tables.status_claims   = response.data.status_claims
                this.tables.claim_channels  = response.data.claim_channels
            })
        },

        loadRecords() {
            this.loading = true
            const params = this.buildParams()
            this.$http.get('/claims/records', { params })
                .then(response => {
                    this.records          = response.data.data
                    this.pagination.total        = response.data.meta.total
                    this.pagination.per_page     = response.data.meta.per_page
                    this.pagination.current_page = response.data.meta.current_page
                })
                .finally(() => { this.loading = false })
        },

        toggleShowClosed() {
            this.showClosed = !this.showClosed
            this.pagination.current_page = 1
            this.loadRecords()
        },

        buildParams() {
            const p = { page: this.pagination.current_page }
            if (this.showClosed)             p.show_closed    = 1
            if (this.filters.code)           p.code           = this.filters.code
            if (this.filters.search)         p.search         = this.filters.search
            if (this.filters.status_claim_id) p.status_claim_id = this.filters.status_claim_id
            if (this.filters.claim_type)     p.claim_type     = this.filters.claim_type
            if (this.filters.channel)        p.channel        = this.filters.channel
            if (this.filters.dateRange && this.filters.dateRange.length === 2) {
                p.date_from = this.filters.dateRange[0]
                p.date_to   = this.filters.dateRange[1]
            }
            return p
        },

        // ── Paginación ────────────────────────────────────────────────

        onPageChange(page) {
            this.pagination.current_page = page
            this.loadRecords()
        },

        // ── Detalle ───────────────────────────────────────────────────

        viewDetail(row) {
            this.selectedClaimId = row.id
            this.showDetailModal = true
        },

        // ── Cambio de estado ──────────────────────────────────────────

        onStatusChange(row, newStatusId) {
            // Como usamos :value (no v-model), row.status_claim_id es aún el valor anterior
            const oldStatusId = row.status_claim_id
            const newStatus   = this.tables.status_claims.find(s => s.id === newStatusId)

            if (!newStatus) return

            this.pendingStatusChange = { row, newStatusId, oldStatusId }

            if (row.is_closed) {
                // Reclamo cerrado: confirmar reapertura
                this.statusChangeMode       = 'reopen'
                this.showStatusChangeDialog = true
                return
            }

            if (newStatus.is_final) {
                // Estado final: pedir resolución obligatoria
                this.statusChangeMode       = 'resolution'
                this.showStatusChangeDialog = true
                return
            }

            // Cambio simple: aplicar inmediatamente
            this.saveStatusChange(row, newStatusId, null)
        },

        cancelStatusChange() {
            if (this.pendingStatusChange) {
                // Revertir el select al valor anterior
                this.pendingStatusChange.row.status_claim_id = this.pendingStatusChange.oldStatusId
            }
            this.pendingStatusChange    = null
            this.showStatusChangeDialog = false
        },

        confirmStatusChange({ resolution, files }) {
            if (!this.pendingStatusChange) return
            const { row, newStatusId } = this.pendingStatusChange

            if (this.statusChangeMode === 'resolution' && (!resolution || !resolution.trim())) {
                this.$message.error('Ingrese la resolución para cerrar el reclamo')
                return
            }

            this.saveStatusChange(row, newStatusId, resolution || null, files || [])
            this.showStatusChangeDialog = false
        },

        saveStatusChange(row, statusId, resolution, files = []) {
            this.savingStatus = true

            const fd = new FormData()
            fd.append('status_claim_id', statusId)
            if (resolution) fd.append('resolution', resolution)
            files.forEach((f, i) => {
                if (f.raw) fd.append(`response_attachments[${i}]`, f.raw)
            })

            this.$http.post(`/claims/${row.id}/update`, fd)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        // Actualizar la fila con el nuevo estado tras confirmación del backend
                        row.status_claim_id = statusId
                        const newStatus = this.tables.status_claims.find(s => s.id === statusId)
                        row.is_closed = !!(newStatus && newStatus.is_final)
                    }
                })
                .catch(() => {
                    // El select sigue mostrando el valor anterior (no se mudó porque usamos :value)
                    this.$message.error('Error al actualizar el estado')
                    this.loadRecords()
                })
                .finally(() => {
                    this.savingStatus        = false
                    this.pendingStatusChange = null
                    this.loadRecords()
                    this.$refs.metrics && this.$refs.metrics.reload()
                })
        },

        // Maneja el cambio de responsable desde la tabla (emitted by ClaimsDataTable)
        onAssignChange(row, userId) {
            // En caso de fallo, recargamos los registros para sincronizar el estado
            this.$http.put(`/claims/${row.id}/assign`, { assigned_user_id: userId })
                .then(response => {
                    if (response.data && response.data.success) {
                        this.$message.success(response.data.message || 'Responsable asignado correctamente')
                        // Actualizar la fila localmente si backend devolvió datos
                        row.assigned_user_id = userId
                    } else {
                        this.$message.error((response.data && response.data.message) || 'Error al asignar responsable')
                    }
                })
                .catch(() => {
                    this.$message.error('Error al asignar responsable')
                    this.loadRecords()
                })
                .finally(() => {
                    this.loadRecords()
                })
        },

        // ── Helpers ───────────────────────────────────────────────────

        formatDate(dateStr) {
            if (!dateStr) return ''
            const d = moment ? moment(dateStr).format('DD/MM/YYYY') : dateStr.slice(0, 10)
            return d
        },

        // Rango de fechas por defecto: primer y último día del mes actual
        defaultDateRange() {
            const now   = new Date()
            const year  = now.getFullYear()
            const month = now.getMonth()
            const first = `${year}-${String(month + 1).padStart(2, '0')}-01`
            const last  = new Date(year, month + 1, 0)
            const lastStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(last.getDate()).padStart(2, '0')}`
            return [first, lastStr]
        },
    }
}
</script>
