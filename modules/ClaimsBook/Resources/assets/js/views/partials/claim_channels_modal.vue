<template>
    <el-dialog
        title="Canales de atención"
        :visible.sync="showDialog"
        width="600px"
        @open="loadEstablishments"
        @close="close"
    >
        <!-- Nota informativa -->
        <el-alert
            title="Los canales de atención se generan a partir de los establecimientos del sistema."
            type="info"
            :closable="false"
            show-icon
            class="ch-info-alert"
        ></el-alert>

        <!-- Establecimientos del sistema con indicador de sincronización -->
        <div class="ch-section-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold">Sucursales del sistema</h5>
            <el-button
                class="btn btn-sm"
                type="primary"
                plain
                :loading="syncing"
                @click="syncChannels"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                Sincronizar sucursales
            </el-button>
        </div>
        <div class="table-responsive ch-table">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="text-center" style="width:45px">#</th>
                        <th style="width:80px">Código</th>
                        <th>Establecimiento</th>
                        <th class="text-center" style="width:115px">Sincronizado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="4" class="text-center py-3">
                            <i class="el-icon-loading"></i> Cargando...
                        </td>
                    </tr>
                    <tr v-else-if="establishments.length === 0">
                        <td colspan="4" class="text-center py-3 text-muted">Sin registros</td>
                    </tr>
                    <tr v-for="(row, index) in establishments" :key="row.id" v-else>
                        <td class="text-center">{{ index + 1 }}</td>
                        <td>{{ row.code }}</td>
                        <td>{{ row.description }}</td>
                        <td class="text-center">
                            <span v-if="isSynced(row)" class="badge bg-success-lt">
                                <i class="el-icon-check"></i> Activo
                            </span>
                            <span v-else class="ch-pending">—</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Canales registrados -->
        <div class="ch-section-header mt-2">
            <h5 class="fw-bold">Canales registrados</h5>
            <el-tag type="info" size="mini" effect="plain">{{ channels.length }}</el-tag>
        </div>
        <div class="table-responsive ch-table">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th style="width:160px">Canal</th>
                        <th>Descripción</th>
                        <th class="text-end" style="width:60px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="channels.length === 0">
                        <td colspan="3" class="text-center py-3 text-muted">Sin canales registrados</td>
                    </tr>
                    <tr v-for="row in channels" :key="row.id" v-else>
                        <td>{{ row.name }}</td>
                        <td>
                            <span v-if="channelContactInfo(row)" class="text-muted">{{ channelContactInfo(row) }}</span>
                            <span v-else-if="row.description" class="text-muted">{{ row.description }}</span>
                            <span v-else class="ch-pending">—</span>
                        </td>
                        <td class="text-end">
                            <el-button
                                size="mini"
                                type="danger"
                                class="btn btn-sm"
                                plain
                                @click="destroy(row)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </el-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Agregar canal extra manualmente -->
        <div class="ch-section-header">
            <h5 class="fw-bold">Agregar canal manualmente</h5>
        </div>
        <div class="row mx-0">
            <div class="col-md-5">
                <label class="control-label">Nombre <span class="text-danger">*</span></label>
                <el-input
                    v-model="newName"
                    placeholder="Nombre del canal"
                    size="small"
                    @keyup.enter.native="store"
                ></el-input>
            </div>
            <div class="col-md-7">
                <label class="control-label">Descripción (opcional)</label>
                <el-input
                    v-model="newDescription"
                    placeholder="Descripción del canal"
                    size="small"
                    @keyup.enter.native="store"
                ></el-input>
            </div>
        </div>
        <el-button
            type="primary"
            class="mt-2 ms-auto me-1 btn btn-sm"
            :loading="storing"
            @click="store"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Agregar
        </el-button>

        <div slot="footer" class="ch-footer ">
            <el-button size="small" @click="close">Cerrar</el-button>
        </div>
    </el-dialog>
</template>

<style scoped>
.ch-info-alert {
    margin-bottom: 14px;
}
.ch-table {
    margin-top: 4px;
    margin-bottom: 0;
}
.ch-pending {
    color: #c0c4cc;
    font-size: 13px;
}
.ch-desc-text {
    color: #909399;
    font-size: 12px;
}
.ch-divider {
    margin: 16px 0 10px;
}
.ch-section-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}
.ch-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #606266;
}
.ch-add-box {
    margin-top: 16px;
    padding: 12px 14px;
    border: 1px solid #ebeef5;
    border-radius: 4px;
    background: #fafafa;
}
.ch-add-header {
    margin-bottom: 10px;
}
.ch-add-fields {
    display: flex;
    gap: 10px;
}
.ch-add-fields .el-input {
    flex: 1;
}
.ch-add-action {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}
.ch-footer {
    display: flex;
    justify-content: end;
    align-items: center;
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
            // Establecimientos cargados desde /establishments/records
            establishments: [],
            // Canales ya registrados en claim_channels
            channels: [],
            // Nombres en minúsculas para comparación rápida de sincronización
            syncedNames: [],
            loading: false,
            syncing: false,
            storing: false,
            newName: '',
            newDescription: '',
        }
    },

    computed: {
        estByName() {
            const map = {}
            this.establishments.forEach(e => {
                map[e.description.toLowerCase()] = e
            })
            return map
        },
    },

    methods: {
        // Carga establecimientos y canales registrados al abrir el modal
        loadEstablishments() {
            this.loading = true
            Promise.all([
                this.$http.get('/establishments/records'),
                this.$http.get('/claimChannels/records'),
            ]).then(([estResp, chanResp]) => {
                this.establishments = estResp.data.data ?? estResp.data
                this.channels       = chanResp.data ?? []
                this.syncedNames    = this.channels.map(c => c.name.toLowerCase())
            }).finally(() => { this.loading = false })
        },

        // Comprueba si un establecimiento ya está registrado como canal
        isSynced(establishment) {
            return this.syncedNames.includes(establishment.description.toLowerCase())
        },

        channelContactInfo(channel) {
            const est = this.estByName[channel.name.toLowerCase()]
            if (!est) return null
            const parts = [est.address, est.telephone, est.email].filter(Boolean)
            return parts.length ? parts.join(' · ') : null
        },

        // Agrega un canal extra manualmente
        store() {
            if (!this.newName.trim())
                return this.$message.error('Ingrese un nombre para el canal')

            this.storing = true
            this.$http.post('/claimChannels/store', { name: this.newName, description: this.newDescription })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.newName = ''
                        this.newDescription = ''
                        this.refreshChannels()
                    }
                })
                .finally(() => { this.storing = false })
        },

        // Elimina un canal registrado
        destroy(channel) {
            this.$confirm(
                `¿Desea eliminar el canal "${channel.name}"?`,
                'Eliminar canal',
                { confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', type: 'warning' }
            ).then(() => {
                this.$http.delete(`/claimChannels/destroy/${channel.id}`)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.refreshChannels()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
            }).catch(() => {})
        },

        // Llama al backend para sincronizar los establecimientos como canales
        syncChannels() {
            this.syncing = true
            this.$http.post('/claimChannels/sync')
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.refreshChannels()
                    }
                })
                .catch(() => {
                    this.$message.error('Error al sincronizar los canales')
                })
                .finally(() => { this.syncing = false })
        },

        // Refresca la lista de canales y actualiza el índice de sincronizados
        refreshChannels() {
            this.$http.get('/claimChannels/records').then(res => {
                this.channels    = res.data ?? []
                this.syncedNames = this.channels.map(c => c.name.toLowerCase())
            })
        },

        close() {
            this.$emit('update:showDialog', false)
        }
    }
}
</script>
