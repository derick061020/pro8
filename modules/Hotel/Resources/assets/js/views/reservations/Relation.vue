<template>
    <div class="hrel">
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels/reservations/calendar">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top:-5px" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 12l.01 0"/><path d="M13 12l2 0"/><path d="M9 16l.01 0"/><path d="M13 16l2 0"/></svg>
                </a>
                Relación de reservas
            </h2>
            <ol class="breadcrumbs">
                <li><span><a href="/hotels/reservations/calendar">Reservas</a></span></li>
                <li class="active"><span>Relación de reservas</span></li>
            </ol>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="hrel-help">
                    Marque las reservas que quiere juntar (por ejemplo, las habitaciones de un mismo grupo) y descargue
                    una sola lista con el titular y los acompañantes de cada habitación para enviarla al cliente.
                </p>

                <div class="hrel-filters">
                    <div class="hrel-field">
                        <label>Ingreso desde</label>
                        <el-date-picker v-model="from" type="date" size="small" format="dd/MM/yyyy" value-format="yyyy-MM-dd" :clearable="false" @change="load"></el-date-picker>
                    </div>
                    <div class="hrel-field">
                        <label>Ingreso hasta</label>
                        <el-date-picker v-model="to" type="date" size="small" format="dd/MM/yyyy" value-format="yyyy-MM-dd" :clearable="false" :picker-options="{ disabledDate: d => from && d < new Date(from + 'T00:00:00') }" @change="load"></el-date-picker>
                    </div>
                    <div class="hrel-field hrel-field-grow">
                        <label>Buscar (cliente, documento, habitación o código)</label>
                        <el-input v-model="search" size="small" clearable placeholder="Ej. Ureña, RD8582415, 36, R-003292" @input="onSearch"></el-input>
                    </div>
                </div>

                <div class="hrel-toolbar">
                    <span class="hrel-count">
                        <b>{{ selected.length }}</b> de {{ records.length }} reserva(s) marcada(s)
                        <template v-if="selected.length"> · {{ selectedGuests }} huésped(es)</template>
                    </span>
                    <div class="hrel-actions">
                        <el-input v-model="title" size="small" class="hrel-title" placeholder="Título (opcional): Relación de reservas"></el-input>
                        <el-button size="small" type="success" icon="el-icon-document" :disabled="!selected.length" @click="download('xlsx')">Excel</el-button>
                        <el-button size="small" type="primary" icon="el-icon-download" :disabled="!selected.length" @click="download('pdf')">PDF</el-button>
                    </div>
                </div>

                <el-table
                    ref="table"
                    v-loading="loading"
                    :data="records"
                    size="small"
                    border
                    row-key="id"
                    empty-text="No hay reservas con ingreso en esas fechas"
                    @selection-change="rows => selected = rows"
                >
                    <el-table-column type="selection" width="42" reserve-selection></el-table-column>
                    <el-table-column prop="code" label="Código" width="100"></el-table-column>
                    <el-table-column label="Titular" min-width="200">
                        <template slot-scope="{ row }">
                            <div class="hrel-name">{{ row.customer }}</div>
                            <small class="text-muted">{{ row.number }}</small>
                        </template>
                    </el-table-column>
                    <el-table-column label="Habitación" min-width="120">
                        <template slot-scope="{ row }">{{ row.room }} <small class="text-muted">{{ row.category }}</small></template>
                    </el-table-column>
                    <el-table-column label="Ingreso" width="125">
                        <template slot-scope="{ row }">{{ fmt(row.input_date) }} {{ row.input_time }}</template>
                    </el-table-column>
                    <el-table-column label="Salida" width="125">
                        <template slot-scope="{ row }">{{ fmt(row.output_date) }} {{ row.output_time }}</template>
                    </el-table-column>
                    <el-table-column prop="nights" label="Noches" width="75" align="center"></el-table-column>
                    <el-table-column prop="guests" label="Huéspedes" width="90" align="center"></el-table-column>
                    <el-table-column label="Estado" width="100" align="center">
                        <template slot-scope="{ row }">
                            <el-tag size="mini" :type="row.is_reserve ? 'warning' : 'success'">{{ row.is_reserve ? 'Reserva' : 'Hospedado' }}</el-tag>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    name: 'HotelReservationRelation',
    props: ['establishment'],
    data() {
        return {
            from: moment().format('YYYY-MM-DD'),
            to: moment().add(30, 'days').format('YYYY-MM-DD'),
            search: '',
            title: '',
            records: [],
            selected: [],
            loading: false,
            searchTimer: null,
        }
    },
    computed: {
        selectedGuests() {
            return this.selected.reduce((s, r) => s + (r.guests || 0), 0)
        },
    },
    mounted() {
        this.load()
    },
    methods: {
        fmt(date) {
            return date ? moment(date).format('DD/MM/YYYY') : ''
        },
        onSearch() {
            clearTimeout(this.searchTimer)
            this.searchTimer = setTimeout(this.load, 350)
        },
        async load() {
            if (this.to < this.from) this.to = this.from
            this.loading = true
            try {
                const { data } = await this.$http.get('/hotels/reservations/relation/records', {
                    params: { from: this.from, to: this.to, search: this.search },
                })
                this.records = data.records || []
            } catch (e) {
                this.$message.error('No se pudieron cargar las reservas.')
            } finally {
                this.loading = false
            }
        },
        download(format) {
            if (!this.selected.length) return
            const params = new URLSearchParams({
                ids: this.selected.map(r => r.id).join(','),
                format,
            })
            if (this.title.trim()) params.set('title', this.title.trim().toUpperCase())
            window.open(`/hotels/reservations/relation/download?${params.toString()}`, '_blank')
        },
    },
}
</script>

<style scoped>
.hrel-help { color: #6b7280; margin-bottom: 12px; }
.hrel-filters { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; }
.hrel-field { display: flex; flex-direction: column; gap: 4px; }
.hrel-field label { font-size: 12px; color: #6b7280; margin: 0; }
.hrel-field-grow { flex: 1 1 260px; }
.hrel-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
.hrel-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.hrel-title { width: 280px; max-width: 100%; }
.hrel-name { font-weight: 600; }
@media (max-width: 576px) {
    .hrel-title { width: 100%; }
}
</style>
