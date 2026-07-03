<template>
    <div class="table-responsive mt-3">
        <table class="table table-claims">
            <thead>
                <tr>
                    <th class="text-start">Código</th>
                    <th class="text-start">Cliente</th>
                    <th v-if="columns.tipo.visible"         class="text-start">Tipo</th>
                    <th v-if="columns.fecha.visible"        class="text-start">Fecha</th>
                    <th v-if="columns.vencimiento.visible"  class="text-start">Vencimiento</th>
                    <th v-if="columns.comprobante.visible"  class="text-start">Comprobante</th>
                    <th v-if="columns.monto.visible"        class="text-start">Monto</th>
                    <th v-if="columns.canal.visible"        class="text-start">Canal</th>
                    <th class="text-start">Estado</th>
                    <th v-if="columns.responsable.visible"  class="text-start">Responsable</th>
                    <th class="text-end">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td :colspan="visibleColspan" class="text-center py-4">
                        <i class="el-icon-loading"></i> Cargando...
                    </td>
                </tr>
                <tr v-else-if="records.length === 0">
                    <td :colspan="visibleColspan" class="text-center py-4 text-muted">
                        No se encontraron registros
                    </td>
                </tr>
                <tr v-for="row in records" :key="row.id" v-else :style="highlightRows ? getRowHighlight(row) : {}">
                    <td>
                        <div class="cb-code">{{ row.code }}</div>
                        <small class="text-muted">{{ row.public_code }}</small>
                    </td>
                    <td>
                        <div class="cb-customer-name">{{ row.name }}</div>
                        <small class="text-muted">
                            {{ row.identity_document_type_label }}
                            {{ row.identity_document_number }}
                        </small>
                    </td>
                    <td v-if="columns.tipo.visible" class="text-start">
                        <span
                            class="badge"
                            :class="row.claim_type === 'reclamo' ? 'bg-danger' : 'bg-warning'"
                            style="text-transform: capitalize"
                        >
                            {{ row.claim_type }}
                        </span>
                    </td>
                    <td v-if="columns.fecha.visible" class="text-start">
                        {{ formatDate(row.created_at) }}
                    </td>
                    <td v-if="columns.vencimiento.visible" class="text-start" style="white-space:nowrap;">
                        <span v-if="row.is_closed && row.days_to_resolve !== null" class="text-success">
                            Resuelto en {{ row.days_to_resolve }} {{ row.days_to_resolve === 1 ? 'día' : 'días' }}
                        </span>
                        <span v-else-if="!row.is_closed && row.remaining_business_days" class="text-danger">
                            {{ row.remaining_business_days }} días
                        </span>
                        <span v-else class="text-muted">—</span>
                    </td>
                    <td v-if="columns.comprobante.visible" class="text-start">
                        <span v-if="row.receipt_series">
                            {{ row.receipt_series }}-{{ row.receipt_number }}
                        </span>
                        <span v-else class="text-muted">—</span>
                    </td>
                    <td v-if="columns.monto.visible" class="text-start">
                        <span v-if="row.has_receipt">
                            {{ row.receipt_currency }} {{ row.receipt_amount }}
                        </span>
                        <span v-else class="text-muted">—</span>
                    </td>
                    <td v-if="columns.canal.visible" class="text-start">
                        <span v-if="row.channel">{{ row.channel }}</span>
                        <span v-else class="text-muted">—</span>
                    </td>
                    <td class="text-start">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span
                                :style="{ background: getStatusColor(row.status_claim_id), flexShrink: 0 }"
                                style="display:inline-block; width:8px; height:8px; border-radius:50%;"
                            ></span>
                            <el-select
                                :value="row.status_claim_id"
                                size="mini"
                                style="width: 100%; min-width: 150px"
                                :class="{ 'select-white': highlightRows }"
                                @change="newVal => $emit('status-change', row, newVal)"
                            >
                                <el-option
                                    v-for="s in statusClaims"
                                    :key="s.id"
                                    :label="s.description"
                                    :value="s.id"
                                >
                                    <span :style="{ color: s.color || '#909399' }">● </span>
                                    {{ s.description }}
                                </el-option>
                            </el-select>
                        </div>
                    </td>
                    <td v-if="columns.responsable.visible" class="text-start">
                        <el-select
                            v-model="row.assigned_user_id"
                            size="mini"
                            style="width: 100%; min-width: 150px"
                            :class="{ 'select-white': highlightRows }"
                            @change="newVal => onAssign(row, newVal)"
                        >
                            <el-option
                                v-for="user in activeUsers"
                                :key="user.id"
                                :label="user.name"
                                :value="user.id"
                            ></el-option>
                        </el-select>
                    </td>
                    <td class="text-end">
                        <button
                            type="button"
                            class="btn btn-xs btn-primary"
                            @click="$emit('view', row)"
                            title="Abrir detalle del reclamo"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                style="vertical-align:-2px; margin-right:3px;">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                                <path d="M9 17h6"/><path d="M9 13h6"/><path d="M9 9h1"/>
                            </svg>
                            Abrir
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="cb-pagination">
            <el-pagination
                    @current-change="onPageChange"
                    layout="total, prev, pager, next"
                    :total="pagination.total"
                    :current-page="pagination.current_page"
                    :page-size="pagination.per_page">
            </el-pagination>
        </div>
    </div>
</template>

<style scoped>
.select-white >>> .el-input__inner {
    background-color: #fff !important;
}
</style>

<script>
export default {
    name: 'ClaimsDataTable',

    data() {
        return {
            users: [],
        }
    },

    props: {
        records: {
            type: Array,
            default: () => []
        },
        statusClaims: {
            type: Array,
            default: () => []
        },
        loading: {
            type: Boolean,
            default: false
        },
        pagination: {
            type: Object,
            default: () => ({ total: 0, per_page: 20, current_page: 1 })
        },
        columns: {
            type: Object,
            default: () => ({})
        },
        highlightRows: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        visibleColspan() {
            const fixed = 4 // Código, Cliente, Estado, Opciones
            const dynamic = Object.values(this.columns).filter(c => c.visible).length
            return fixed + dynamic
        },
        activeUsers() {
            return this.users.filter(u => u.active)
        }
    },

    methods: {
        getStatusColor(statusId) {
            const s = this.statusClaims.find(s => s.id === statusId)
            return (s && s.color) ? s.color : '#909399'
        },

        getRowHighlight(row) {
            const hex = this.getStatusColor(row.status_claim_id)
            // Convierte hex a rgba con opacidad baja para un color suave
            let r = 200, g = 200, b = 200
            const clean = hex.replace('#', '')
            if (clean.length === 6) {
                r = parseInt(clean.slice(0, 2), 16)
                g = parseInt(clean.slice(2, 4), 16)
                b = parseInt(clean.slice(4, 6), 16)
            } else if (clean.length === 3) {
                r = parseInt(clean[0] + clean[0], 16)
                g = parseInt(clean[1] + clean[1], 16)
                b = parseInt(clean[2] + clean[2], 16)
            }
            return { backgroundColor: `rgba(${r}, ${g}, ${b}, 0.15)` }
        },

        onPageChange(page) {
            this.$emit('page-change', page)
        },

        formatDate(dateStr) {
            if (!dateStr) return ''
            return moment ? moment(dateStr).format('DD/MM/YYYY') : dateStr.slice(0, 10)
        },

        onAssign(row, userId) {
            this.$emit('assign-change', row, userId)
        }
    },

    mounted() {
        this.$http.get('/users/records').then(response => {
            this.users = response.data.data || []
        }).catch(() => { this.users = [] })
    }
}
</script>
