<template>
    <el-dialog :visible="showDialog" @close="close" @open="onOpen" :close-on-click-modal="false" width="880px">
        <div slot="title" class="series-title">
            <span class="font-weight-bold">Series</span>
            <span class="text-muted" v-if="establishment" style="font-size:13px">
                Sucursal <b>{{ establishment.description }}</b> · {{ establishment.code }}
            </span>
        </div>

        <div class="row text-end">
            <span class="series-toggle">
                <el-tooltip content="Habilita la creación y gestión de series dedicadas por equipo" placement="top">
                    <span class="text-muted me-2" style="font-size:13px">Dedicado</span>
                </el-tooltip>
                <el-switch v-model="enableDedicatedSeries" @change="toggleDedicated" active-color="#0E8C8C"></el-switch>
            </span>
        </div>
        <!-- Filtros (estilo maqueta) + toggle Dedicado a la altura de los tabs -->
        <div class="series-filters d-flex align-items-center flex-wrap mb-3">
            <button type="button" class="chip" :class="{active: filter === 'all'}" @click="setFilter('all')">Todos</button>
            <button type="button" class="chip" :class="{active: filter === 'basic'}" @click="setFilter('basic')">Básico</button>
            <button type="button" class="chip" :class="{active: filter === 'advanced'}" @click="setFilter('advanced')">Avanzado</button>
            <button type="button" class="chip" :class="{active: filter === 'internal'}" @click="setFilter('internal')">Interno</button>
            <span class="fdiv" v-if="enableDedicatedSeries || hasContingency"></span>
            <button type="button" v-if="enableDedicatedSeries" class="chip ded" :class="{active: filter === 'dedicated'}" @click="setFilter('dedicated')"><span class="dot"></span>Dedicado</button>
            <button type="button" v-if="hasContingency" class="chip cont" :class="{active: filter === 'contingency'}" @click="setFilter('contingency')"><span class="dot"></span>Contingencia</button>
        </div>

        <!-- Grupos de dispositivo (solo en el tab Dedicado) -->
        <div v-if="filter === 'dedicated'" class="series-groups mb-3">
            <div class="d-flex align-items-center flex-wrap" style="gap:8px">
                <span class="text-muted font-weight-bold" style="font-size:12.5px">Grupos de dispositivo</span>
                <span v-for="group in groups" :key="group.id" class="series-group-chip">
                    <b>{{ group.name }}</b>
                    <span class="count">{{ group.series.length }}</span>
                    <!-- Asignación por módulo (restaurant/ecommerce/hoteles) oculta de momento; lógica intacta.
                    <span v-if="group.module_label" class="badge badge-light text-dark border ms-1">{{ group.module_label }}</span>
                    -->

                    <el-tooltip v-if="group.is_bound" :content="'En uso por ' + group.bound_device_name" placement="top">
                        <i class="el-icon-lock text-warning"></i>
                    </el-tooltip>
                    <i class="el-icon-edit-outline action" title="Editar" @click="editGroup(group)"></i>
                    <i v-if="group.is_bound" class="el-icon-unlock action" title="Desvincular equipo" @click="unbindGroup(group)"></i>
                    <i class="el-icon-close action" title="Eliminar" @click="deleteGroup(group)"></i>
                </span>
                <span v-if="!groups.length" class="text-muted" style="font-size:12.5px">aún no hay grupos de dispositivo</span>
                <el-button size="mini" icon="el-icon-plus" @click="openGroupForm">Crear grupo</el-button>
            </div>

            <!-- Form de grupo -->
            <div v-if="creatingGroup" class="series-create p-3 mt-2">
                <div class="d-flex flex-wrap align-items-center mb-2" style="gap:10px">
                    <el-input v-model="groupForm.name" size="small" placeholder="Nombre del grupo · ej. Caja 1" style="width:280px"></el-input>
                    <!-- Selector de módulo (restaurant/ecommerce/hoteles) oculto de momento; lógica intacta.
                    <el-select v-model="groupForm.module_value" size="small" clearable placeholder="Módulo (opcional)" style="width:220px">
                        <el-option v-for="module in modules" :key="module.value" :label="module.label" :value="module.value"></el-option>
                    </el-select>
                    -->

                </div>
                <small class="text-muted d-block mb-1">Series dedicadas disponibles</small>
                <el-checkbox-group v-if="selectableSeries.length" v-model="groupForm.series_ids">
                    <el-checkbox v-for="serie in selectableSeries" :key="serie.id" :label="serie.id" border size="small" class="mb-1 me-1">
                        <span class="series-number">{{ serie.number }}</span>
                    </el-checkbox>
                </el-checkbox-group>
                <small v-else class="text-muted">No hay series dedicadas sin agrupar. Crea series con emisión "Dedicado" y luego agrúpalas.</small>
                <div class="text-end mt-2">
                    <el-button size="small" type="primary" class="me-2" :disabled="savingGroup" @click="saveGroup">Guardar grupo</el-button>
                    <el-button size="small" @click="cancelGroupForm">Cancelar</el-button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:90px">Categoría</th>
                    <th>Tipo de documento</th>
                    <th>Número</th>
                    <th style="width:120px">Correlativo</th>
                    <th class="text-right"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in visibleRecords" :key="row.id">
                    <td><span class="badge badge-light text-dark border">{{ categoryLabel(row.category) }}</span></td>
                    <td>{{ row.document_type_description }}</td>
                    <td>
                        <span class="series-number">{{ row.number }}</span>
                        <span class="badge badge-success mx-1" v-if="row.dedicated">DEDICADO</span>
                        <span class="badge badge-warning mx-1" v-else-if="row.contingency">CONTINGENCIA</span>
                        <span class="badge badge-info mx-1" v-if="row.dedicated && row.group_name">{{ row.group_name }}</span>
                        <small v-else-if="row.dedicated" class="text-muted ml-1">sin grupo</small>
                    </td>
                    <td>
                        <el-input v-model.number="row.correlative" type="number" size="mini" :min="1"
                                  :disabled="row.in_use"
                                  :title="row.in_use ? 'La serie ya tiene comprobantes: correlativo bloqueado' : ''"
                                  @change="updateCorrelative(row)" style="width:90px"></el-input>
                    </td>
                    <td class="text-end">
                        <small v-if="row.in_use" class="text-muted" title="La serie ya tiene comprobantes">en uso</small>
                        <button v-else class="btn waves-effect waves-light btn-xs btn-danger ms-1" type="button" @click="clickDelete(row)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                        </button>
                    </td>
                </tr>
                <tr v-if="!visibleRecords.length && !creating">
                    <td colspan="5" class="text-center text-muted py-3">{{ emptyMessage }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Fila de creación -->
        <div v-if="creating" class="series-create p-2 mt-2">
            <!-- Línea 1: tipo de documento + tipo de serie -->
            <div class="row no-gutters mb-2">
                <div class="col-5">
                    <el-select v-model="form.seriesTypeKey" size="small" placeholder="Tipo de documento"
                               filterable @change="onTypeChange" style="min-width:280px">
                        <el-option-group v-for="group in groupedTypes" :key="group.value" :label="group.label">
                            <el-option v-for="type in group.items" :key="type.key" :label="type.label" :value="type.key"></el-option>
                        </el-option-group>
                    </el-select>
                </div>
                <div class="col-4">
                    <el-radio-group v-if="form.emission !== 'contingency'" v-model="form.mode" size="small" @change="refreshNumber" class="me-2">
                        <el-radio-button label="auto">Auto</el-radio-button>
                        <el-radio-button label="manual">Manual</el-radio-button>
                    </el-radio-group>
                    <el-input v-model="form.number" size="small" :maxlength="4" :placeholder="numberPlaceholder"
                          :disabled="form.emission !== 'contingency' && form.mode === 'auto'" style="width:130px"></el-input>
                </div>
                <div class="col-3 text-end">
                    <el-input v-model.number="form.correlative" size="small" type="number" :min="1" style="width:160px">
                        <template slot="prepend">Correlativo</template>
                    </el-input>
                </div>
            </div>

            <!-- Línea 2: número + auto/manual + correlativo + acciones -->
            <div class="row">
                <div class="col-4">
                    <el-radio-group v-model="form.emission" size="small" @change="onEmissionChange">
                        <el-radio-button label="normal">Normal</el-radio-button>
                        <el-radio-button v-if="enableDedicatedSeries" label="dedicated">Dedicado</el-radio-button>
                        <el-radio-button label="contingency">Contingencia</el-radio-button>
                    </el-radio-group>
                </div>
                <div class="col-8 text-end">
                    <button class="btn waves-effect waves-light btn-xs btn-primary" :disabled="saving" type="button" @click="confirmCreate">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </button>
                    <button class="btn waves-effect waves-light btn-xs btn-danger ms-2" type="button" @click="cancelCreate">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            <small class="text-danger d-block mt-2" v-if="form.error">{{ form.error }}</small>
        </div>

        <!-- Footer -->
        <div class="d-flex flex-wrap align-items-center mt-3" style="gap:14px">
            <el-button type="primary" icon="el-icon-plus" @click="clickNew">Nuevo</el-button>
            <small class="text-muted">El <b>correlativo</b> es el número desde el que continuará la serie. Útil al migrar de otro sistema; por defecto 1.</small>
        </div>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'establishmentId', 'establishment'],
        data() {
            return {
                resource: 'series',
                records: [],
                seriesTypes: [],
                enableDedicatedSeries: false,
                filter: 'all',
                creating: false,
                saving: false,
                form: this.emptyForm(),
                groups: [],
                availableSeries: [],
                modules: [],
                creatingGroup: false,
                savingGroup: false,
                groupForm: {id: null, name: '', module_value: null, series_ids: []},
                editingGroupSeries: [],
            }
        },
        computed: {
            hasContingency() {
                return this.records.some(row => row.contingency)
            },
            visibleRecords() {
                let items = this.records.slice()
                if (this.filter === 'dedicated') items = items.filter(row => row.dedicated)
                else if (this.filter === 'contingency') items = items.filter(row => row.contingency)
                else if (this.filter !== 'all') items = items.filter(row => row.category === this.filter && !row.dedicated)
                // 'all' incluye todo (también dedicadas); ordenado por número de serie
                return items.sort((a, b) => a.number.localeCompare(b.number))
            },
            groupedTypes() {
                const groups = [
                    {value: 'basic', label: 'Básico (SUNAT)'},
                    {value: 'advanced', label: 'Avanzado (SUNAT)'},
                    {value: 'internal', label: 'Interno'},
                ]
                return groups
                    .map(group => ({...group, items: this.optionsByCategory(group.value)}))
                    .filter(group => group.items.length)
            },
            selectedType() {
                return this.seriesTypes.find(type => type.key === this.form.seriesTypeKey) || null
            },
            numberPlaceholder() {
                if (this.form.emission === 'contingency') return '____'
                return this.selectedType ? this.selectedType.prefix + '__' : '____'
            },
            emptyMessage() {
                if (this.filter === 'contingency') return 'No hay series de contingencia.'
                if (this.filter === 'dedicated') return 'No hay series dedicadas.'
                if (this.filter === 'advanced') return 'Aún no agregaste series avanzadas.'
                return 'Sin series.'
            },
            selectableSeries() {
                // Series dedicadas sin agrupar + (al editar) las que ya pertenecen al grupo.
                const map = new Map()
                this.availableSeries.forEach(serie => map.set(serie.id, serie))
                this.editingGroupSeries.forEach(serie => map.set(serie.id, serie))
                return Array.from(map.values()).sort((a, b) => a.number.localeCompare(b.number))
            },
        },
        async created() {
            await this.getTables()
        },
        methods: {
            emptyForm() {
                return {seriesTypeKey: null, number: '', mode: 'auto', correlative: 1, emission: 'normal', error: ''}
            },
            async getTables() {
                const {data} = await this.$http.get(`/${this.resource}/tables`)
                this.seriesTypes = data.series_types || []
                this.enableDedicatedSeries = !!data.enable_dedicated_series
            },
            async getData() {
                if (!this.establishmentId) return
                const {data} = await this.$http.get(`/${this.resource}/records/${this.establishmentId}`)
                this.records = (data && data.data) ? data.data : []
            },
            onOpen() {
                this.creating = false
                this.filter = 'all'
                this.getData()
            },
            setFilter(key) {
                this.filter = key
                this.creating = false
                this.creatingGroup = false
                if (key === 'dedicated') {
                    this.getGroups()
                    this.getGroupTables()
                }
            },
            async getGroups() {
                if (!this.establishmentId) return
                const {data} = await this.$http.get(`/${this.resource}/groups/records/${this.establishmentId}`)
                this.groups = (data && data.data) ? data.data : []
            },
            async getGroupTables() {
                if (!this.establishmentId) return
                const {data} = await this.$http.get(`/${this.resource}/groups/tables/${this.establishmentId}`)
                this.availableSeries = data.available_series || []
                this.modules = data.modules || []
            },
            openGroupForm() {
                this.groupForm = {id: null, name: '', module_value: null, series_ids: []}
                this.editingGroupSeries = []
                this.creatingGroup = true
                this.getGroupTables()
            },
            editGroup(group) {
                this.groupForm = {
                    id: group.id,
                    name: group.name,
                    module_value: group.module_value,
                    series_ids: group.series.map(serie => serie.id),
                }
                this.editingGroupSeries = group.series.slice()
                this.creatingGroup = true
                this.getGroupTables()
            },
            cancelGroupForm() {
                this.creatingGroup = false
            },
            async saveGroup() {
                if (!this.groupForm.name.trim()) {
                    this.$message.warning('Ingresa un nombre para el grupo.')
                    return
                }
                this.savingGroup = true
                const payload = {
                    id: this.groupForm.id,
                    establishment_id: this.establishmentId,
                    name: this.groupForm.name.trim(),
                    module_value: this.groupForm.module_value,
                    series_ids: this.groupForm.series_ids,
                }
                try {
                    const {data} = await this.$http.post(`/${this.resource}/groups`, payload)
                    if (data.success) {
                        this.$message.success(data.message)
                        this.creatingGroup = false
                        await this.refreshGroups()
                    } else {
                        this.$message.error(data.message)
                    }
                } finally {
                    this.savingGroup = false
                }
            },
            unbindGroup(group) {
                this.$confirm('¿Desvincular el equipo "' + group.bound_device_name + '" del grupo ' + group.name + '?', 'Confirmar', {
                    confirmButtonText: 'Desvincular', cancelButtonText: 'Cancelar', type: 'warning',
                }).then(async () => {
                    const {data} = await this.$http.post(`/${this.resource}/groups/${group.id}/unbind`)
                    if (data.success) {
                        this.$message.success(data.message)
                        this.refreshGroups()
                    } else {
                        this.$message.error(data.message)
                    }
                }).catch(() => {})
            },
            deleteGroup(group) {
                this.$confirm('¿Eliminar el grupo ' + group.name + '? Sus series quedarán sin grupo.', 'Confirmar', {
                    confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', type: 'warning',
                }).then(async () => {
                    const {data} = await this.$http.delete(`/${this.resource}/groups/${group.id}`)
                    if (data.success) {
                        this.$message.success(data.message)
                        this.refreshGroups()
                    } else {
                        this.$message.error(data.message)
                    }
                }).catch(() => {})
            },
            async refreshGroups() {
                await Promise.all([this.getGroups(), this.getGroupTables(), this.getData()])
            },
            categoryLabel(category) {
                return {basic: 'Básico', advanced: 'Avanzado', internal: 'Interno'}[category] || category
            },
            optionsByCategory(category) {
                let list = this.seriesTypes.filter(type => type.category === category)
                if (this.form.emission === 'contingency') {
                    list = list.filter(type => ['01', '03', '07', '08', '09'].includes(type.document_type_id))
                }
                return list
            },
            async toggleDedicated(enable) {
                const {data} = await this.$http.post(`/${this.resource}/toggle-dedicated`, {enable})
                if (data.success) {
                    this.enableDedicatedSeries = data.enable_dedicated_series
                    if (!this.enableDedicatedSeries && this.filter === 'dedicated') this.filter = 'all'
                    this.$message.success(data.message)
                }
            },
            clickNew() {
                this.form = this.emptyForm()
                if (this.filter === 'dedicated') this.form.emission = 'dedicated'
                else if (this.filter === 'contingency') this.form.emission = 'contingency'
                this.creating = true
                this.$nextTick(() => this.pickFirstType())
            },
            firstAvailableType() {
                for (const category of ['basic', 'advanced', 'internal']) {
                    const list = this.optionsByCategory(category)
                    if (list.length) return list[0]
                }
                return null
            },
            pickFirstType() {
                const first = this.firstAvailableType()
                this.form.seriesTypeKey = first ? first.key : null
                this.refreshNumber()
            },
            onTypeChange() {
                this.refreshNumber()
            },
            onEmissionChange() {
                if (this.form.emission === 'contingency') {
                    this.form.mode = 'manual'
                    this.form.number = ''
                }
                const stillValid = this.selectedType &&
                    this.optionsByCategory(this.selectedType.category).some(type => type.key === this.form.seriesTypeKey)
                if (!stillValid) this.pickFirstType()
                else this.refreshNumber()
            },
            async refreshNumber() {
                if (this.form.emission === 'contingency') {
                    this.form.number = ''
                    return
                }
                if (this.form.mode === 'auto' && this.selectedType) {
                    const {data} = await this.$http.get(`/${this.resource}/next-code`, {params: {prefix: this.selectedType.prefix}})
                    this.form.number = data.number
                } else {
                    this.form.number = ''
                }
            },
            cancelCreate() {
                this.creating = false
                this.form = this.emptyForm()
            },
            async confirmCreate() {
                if (!this.selectedType) {
                    this.form.error = 'Selecciona un tipo de documento.'
                    return
                }
                this.form.error = ''
                this.saving = true
                const payload = {
                    establishment_id: this.establishmentId,
                    document_type_id: this.selectedType.document_type_id,
                    number: (this.form.number || '').toUpperCase(),
                    contingency: this.form.emission === 'contingency',
                    dedicated: this.form.emission === 'dedicated',
                    correlative: Math.max(1, parseInt(this.form.correlative) || 1),
                }
                try {
                    const {data} = await this.$http.post(`/${this.resource}`, payload)
                    if (data.success) {
                        this.$message.success(data.message)
                        this.creating = false
                        this.form = this.emptyForm()
                        await this.getData()
                    } else {
                        this.form.error = data.message
                    }
                } catch (error) {
                    const response = error.response
                    if (response && response.status === 422) {
                        const errors = response.data.errors || response.data
                        this.form.error = (errors.number ? errors.number[0] : null) || 'Datos inválidos.'
                    } else {
                        this.form.error = 'No se pudo registrar la serie.'
                    }
                } finally {
                    this.saving = false
                }
            },
            async updateCorrelative(row) {
                const correlative = Math.max(1, parseInt(row.correlative) || 1)
                row.correlative = correlative
                const {data} = await this.$http.post(`/${this.resource}/${row.id}/correlative`, {correlative})
                if (data.success) {
                    this.$message.success(data.message)
                } else {
                    this.$message.error(data.message)
                    this.getData()
                }
            },
            clickDelete(row) {
                if (row.in_use) {
                    this.$message.warning('La serie ya tiene comprobantes: no se puede eliminar.')
                    return
                }
                this.$confirm('¿Eliminar la serie ' + row.number + '?', 'Confirmar', {
                    confirmButtonText: 'Eliminar', cancelButtonText: 'Cancelar', type: 'warning',
                }).then(async () => {
                    const {data} = await this.$http.delete(`/${this.resource}/${row.id}`)
                    if (data.success) {
                        this.$message.success(data.message)
                        this.getData()
                    } else {
                        this.$message.error(data.message)
                    }
                }).catch(() => {})
            },
            close() {
                this.creating = false
                this.$emit('update:showDialog', false)
            },
        },
    }
</script>

<style scoped>
    .series-title { display: flex; align-items: center; gap: 10px; }
    .series-number { font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-weight: 700; letter-spacing: .04em; }
    .series-create { background: #F6F8FA; border: 1px solid #EAEDF0; border-radius: 8px; }

    /* Tabs estilo maqueta */
    .series-filters { gap: 8px; }
    .series-filters .chip { border: 1px solid #DEE3E8; background: #fff; color: #7A8794; font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 99px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
    .series-filters .chip:hover { border-color: #1B2430; }
    .series-filters .chip.active { background: #1B2430; color: #fff; border-color: #1B2430; }
    .series-filters .chip .dot { width: 7px; height: 7px; border-radius: 50%; }
    .series-filters .chip.ded .dot { background: #0E8C8C; }
    .series-filters .chip.cont .dot { background: #F2682C; }
    .series-filters .chip.ded.active { background: #0E8C8C; border-color: #0E8C8C; }
    .series-filters .chip.cont.active { background: #F2682C; border-color: #F2682C; }
    .series-filters .chip.ded.active .dot, .series-filters .chip.cont.active .dot { background: #fff; }
    .series-filters .fdiv { width: 1px; height: 20px; background: #DEE3E8; margin: 0 4px; }
    .series-filters .series-toggle { display: inline-flex; align-items: center; }

    /* Grupos de dispositivo */
    .series-group-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; background: #ECECFB; border: 1px solid #C9C7F4; color: #4F46E5; border-radius: 99px; padding: 4px 10px; }
    .series-group-chip .count { background: #4F46E5; color: #fff; border-radius: 99px; padding: 0 7px; font-size: 11px; }
    .series-group-chip .action { cursor: pointer; opacity: .65; }
    .series-group-chip .action:hover { opacity: 1; }

    /* Radio-group del formulario de creación: color principal del sistema, plano y sin salto.
       El "salto" venía de `border: none` en el estado activo: al quitar el borde, el botón
       activo medía 1px menos que los inactivos (que sí tienen borde) y desplazaba la fila.
       Se mantiene el borde con el mismo color del fondo (mismo tamaño, sigue viéndose plano)
       y se rellena el borde izquierdo compartido con un box-shadow de 1px (no es sombra visual). */
    .series-create ::v-deep .el-radio-button__inner {
        -webkit-transition: none;
        transition: none;
    }
    .series-create ::v-deep .el-radio-button__original-radio:checked + .el-radio-button__inner,
    .series-create ::v-deep .el-radio-button__orig-radio:checked + .el-radio-button__inner {
        background-color: var(--black-primary) !important;
        border-color: var(--black-primary) !important;
        box-shadow: -1px 0 0 0 var(--black-primary) !important;
    }
</style>
