<template>
    <div v-if="enabled" class="dedicated-group-selector w-100">
        <div class="d-flex align-items-center mb-1">
            <i class="el-icon-monitor me-2"></i>
            <span class="font-weight-bold" style="font-size:13px">Grupo dedicado</span>
        </div>

        <!-- Vinculado a este equipo -->
        <template v-if="boundGroup">
            <div class="text-muted" style="font-size:12px">Activo en este equipo:</div>
            <div class="d-flex align-items-center justify-content-between">
                <span><b>{{ boundGroup.name }}</b> <small class="text-muted">({{ boundGroup.series.length }} series)</small></span>
                <el-button size="mini" type="danger" plain @click="unbind">Desvincular</el-button>
            </div>
        </template>

        <!-- Selector para vincular -->
        <template v-else>
            <el-input v-model="deviceName" size="mini" placeholder="Nombre del equipo · ej. Caja 1" class="mb-1"></el-input>
            <el-select v-model="selectedGroupId" size="mini" placeholder="Selecciona un grupo" class="mb-1" style="width:100%">
                <el-option v-for="group in availableGroups" :key="group.id"
                           :label="group.name + ' (' + group.series.length + ')'" :value="group.id"></el-option>
            </el-select>
            <small v-if="!availableGroups.length" class="text-muted d-block mb-1">No hay grupos disponibles para este equipo.</small>
            <small v-if="hasPredefinedSeries" class="text-warning d-block mb-1">
                Tu usuario tiene series predefinidas; al activar un grupo se eliminarán.
            </small>
            <el-button size="mini" type="primary" style="width:100%"
                       :disabled="!selectedGroupId || !deviceName.trim() || saving" @click="activate">Activar</el-button>
        </template>
    </div>
</template>

<script>
    const STORAGE_KEY = 'series_device_name'

    export default {
        data() {
            return {
                enabled: false,
                hasPredefinedSeries: false,
                boundGroup: null,
                availableGroups: [],
                deviceName: '',
                selectedGroupId: null,
                saving: false,
            }
        },
        created() {
            this.deviceName = localStorage.getItem(STORAGE_KEY) || ''
            this.loadState()
        },
        methods: {
            async loadState() {
                const {data} = await this.$http.get('/series/profile/dedicated-group', {params: {device_name: this.deviceName}})
                this.enabled = !!data.enabled
                if (!this.enabled) return
                this.hasPredefinedSeries = !!data.has_predefined_series
                this.boundGroup = data.bound_group || null
                this.availableGroups = data.available_groups || []
                if (this.boundGroup) this.deviceName = this.boundGroup.bound_device_name
            },
            activate() {
                const deviceName = this.deviceName.trim()
                if (!this.selectedGroupId || !deviceName) return
                if (this.hasPredefinedSeries) {
                    this.$confirm('Tu usuario tiene series predefinidas. Al activar un grupo dedicado se eliminarán esas relaciones. ¿Continuar?', 'Confirmar', {
                        confirmButtonText: 'Eliminar y activar', cancelButtonText: 'Cancelar', type: 'warning',
                    }).then(() => this.bind(true)).catch(() => {})
                } else {
                    this.bind(false)
                }
            },
            async bind(removePredefined) {
                this.saving = true
                try {
                    const {data} = await this.$http.post('/series/profile/dedicated-group/bind', {
                        group_id: this.selectedGroupId,
                        device_name: this.deviceName.trim(),
                        remove_predefined: removePredefined,
                    })
                    if (data.success) {
                        localStorage.setItem(STORAGE_KEY, this.deviceName.trim())
                        this.$message.success(data.message)
                        this.boundGroup = data.bound_group
                        this.hasPredefinedSeries = false
                    } else if (data.requires_confirmation) {
                        this.$confirm(data.message + ' ¿Continuar?', 'Confirmar', {
                            confirmButtonText: 'Eliminar y activar', cancelButtonText: 'Cancelar', type: 'warning',
                        }).then(() => this.bind(true)).catch(() => {})
                    } else {
                        this.$message.error(data.message)
                    }
                } finally {
                    this.saving = false
                }
            },
            unbind() {
                this.$confirm('¿Desvincular este equipo del grupo ' + this.boundGroup.name + '?', 'Confirmar', {
                    confirmButtonText: 'Desvincular', cancelButtonText: 'Cancelar', type: 'warning',
                }).then(async () => {
                    const {data} = await this.$http.post('/series/groups/' + this.boundGroup.id + '/unbind')
                    if (data.success) {
                        this.$message.success(data.message)
                        this.boundGroup = null
                        this.selectedGroupId = null
                        this.loadState()
                    } else {
                        this.$message.error(data.message)
                    }
                }).catch(() => {})
            },
        },
    }
</script>
