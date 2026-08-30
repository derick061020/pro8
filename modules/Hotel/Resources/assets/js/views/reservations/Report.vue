<template>
    <div class="hrep">
        <!-- Cabecera -->
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top:-5px" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M5 21v-14l8 -4v18"/><path d="M19 21v-10l-6 -4"/><path d="M9 9l0 .01"/><path d="M9 12l0 .01"/><path d="M9 15l0 .01"/><path d="M9 18l0 .01"/></svg>
                </a>
                Reporte de Reservas
            </h2>
            <ol class="breadcrumbs">
                <li><span><a href="/hotels">Hoteles</a></span></li>
                <li class="active"><span>Reporte de Reservas</span></li>
            </ol>
        </div>

        <!-- ================== FILTROS ================== -->
        <div class="hrep-filters">
            <!-- Atajos de periodo -->
            <div class="hrep-row hrep-row-top">
                <div class="hrep-presets">
                    <button
                        v-for="p in presets"
                        :key="p.key"
                        class="hrep-preset"
                        :class="{ active: activePreset === p.key }"
                        @click="applyPreset(p.key)"
                    >{{ p.label }}</button>
                </div>

                <div class="hrep-actions">
                    <button class="hrep-btn hrep-btn-ghost" @click="resetFilters" title="Quitar todos los filtros">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6l-.9 12.1a2 2 0 0 1-2 1.9H7.9a2 2 0 0 1-2-1.9L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                        Limpiar
                    </button>
                    <button class="hrep-btn hrep-btn-excel" :disabled="!records.length" @click="download('xlsx')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="19"/><line x1="15" y1="13" x2="9" y2="19"/></svg>
                        Excel
                    </button>
                    <button class="hrep-btn hrep-btn-pdf" :disabled="!records.length" @click="download('pdf')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                        Descargar PDF
                    </button>
                </div>
            </div>

            <!-- Campos -->
            <div class="hrep-row hrep-row-fields">
                <div class="hrep-field hrep-field-dates">
                    <label>Periodo</label>
                    <el-date-picker
                        v-model="range"
                        type="daterange"
                        size="small"
                        unlink-panels
                        range-separator="a"
                        start-placeholder="Desde"
                        end-placeholder="Hasta"
                        format="dd/MM/yyyy"
                        value-format="yyyy-MM-dd"
                        style="width:100%"
                        @change="onRangeChange"
                    ></el-date-picker>
                </div>

                <div class="hrep-field hrep-field-criterion">
                    <label>Criterio de fecha</label>
                    <el-select v-model="filters.date_field" size="small" style="width:100%" @change="fetchReport">
                        <el-option value="input" label="Fecha de ingreso"></el-option>
                        <el-option value="output" label="Fecha de salida"></el-option>
                        <el-option value="stay" label="Alojados en la fecha"></el-option>
                        <el-option value="created" label="Fecha de registro"></el-option>
                    </el-select>
                    <small class="hrep-hint">{{ criterionHint }}</small>
                </div>

                <div class="hrep-field">
                    <label>Tipo de habitación</label>
                    <el-select v-model="filters.category_id" size="small" style="width:100%" clearable
                               placeholder="Todos" @change="fetchReport">
                        <el-option v-for="c in categories" :key="c.id" :value="c.id" :label="c.description"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field">
                    <label>Habitación</label>
                    <el-select v-model="filters.room_id" size="small" style="width:100%" clearable filterable
                               placeholder="Todas" @change="fetchReport">
                        <el-option v-for="r in rooms" :key="r.id" :value="r.id" :label="r.name"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field">
                    <label>Estado</label>
                    <el-select v-model="filters.status" size="small" style="width:100%" clearable
                               placeholder="Todos" @change="fetchReport">
                        <el-option value="INICIADO" label="En curso"></el-option>
                        <el-option value="FINALIZADO" label="Finalizado"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field">
                    <label>Estado de pago</label>
                    <el-select v-model="filters.payment_state" size="small" style="width:100%" clearable
                               placeholder="Todos" @change="fetchReport">
                        <el-option value="paid" label="Pagado"></el-option>
                        <el-option value="partial" label="Adelanto / parcial"></el-option>
                        <el-option value="unpaid" label="Sin pagar"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field">
                    <label>Medio de reserva</label>
                    <el-select v-model="filters.origin" size="small" style="width:100%" clearable
                               placeholder="Todos" @change="fetchReport">
                        <el-option v-for="o in origins" :key="o.value" :value="o.value" :label="o.label"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field">
                    <label>Tipo</label>
                    <el-select v-model="filters.reservation_type" size="small" style="width:100%" clearable
                               placeholder="Todos" @change="fetchReport">
                        <el-option value="reserve" label="Reservas"></el-option>
                        <el-option value="direct" label="Check-in directo"></el-option>
                    </el-select>
                </div>

                <div class="hrep-field hrep-field-search">
                    <label>Buscar</label>
                    <el-input v-model="search" size="small" clearable placeholder="Huésped, documento, habitación o placa">
                        <i slot="prefix" class="el-input__icon el-icon-search"></i>
                    </el-input>
                </div>
            </div>
        </div>

        <!-- ================== RESUMEN ================== -->
        <div v-loading="loading" class="hrep-body">
            <div class="hrep-kpis">
                <div class="hrep-kpi">
                    <span class="hrep-kpi-label">Reservas</span>
                    <span class="hrep-kpi-value">{{ totals.count || 0 }}</span>
                    <span class="hrep-kpi-hint">registros en el periodo</span>
                </div>
                <div class="hrep-kpi hrep-kpi-accent">
                    <span class="hrep-kpi-label">Huéspedes</span>
                    <span class="hrep-kpi-value">{{ totals.guests || 0 }}</span>
                    <span class="hrep-kpi-hint">{{ totals.adults || 0 }} adultos · {{ totals.children || 0 }} niños</span>
                </div>
                <div class="hrep-kpi">
                    <span class="hrep-kpi-label">Habitaciones</span>
                    <span class="hrep-kpi-value">{{ totals.rooms || 0 }}</span>
                    <span class="hrep-kpi-hint">distintas ocupadas</span>
                </div>
                <div class="hrep-kpi">
                    <span class="hrep-kpi-label">Noches</span>
                    <span class="hrep-kpi-value">{{ totals.nights || 0 }}</span>
                    <span class="hrep-kpi-hint">suma de estadías</span>
                </div>
                <div class="hrep-kpi">
                    <span class="hrep-kpi-label">Importe</span>
                    <span class="hrep-kpi-value hrep-kpi-money">{{ money(totals.total) }}</span>
                    <span class="hrep-kpi-hint">total facturable</span>
                </div>
                <div class="hrep-kpi">
                    <span class="hrep-kpi-label">Pagado</span>
                    <span class="hrep-kpi-value hrep-kpi-money hrep-ok">{{ money(totals.paid) }}</span>
                    <span class="hrep-kpi-hint">{{ paidPct }}% cobrado</span>
                </div>
                <div class="hrep-kpi hrep-kpi-debt">
                    <span class="hrep-kpi-label">Por cobrar</span>
                    <span class="hrep-kpi-value hrep-kpi-money hrep-bad">{{ money(totals.debt) }}</span>
                    <span class="hrep-kpi-hint">saldo pendiente</span>
                </div>
            </div>

            <!-- Resúmenes -->
            <div class="hrep-panels" v-if="records.length">
                <div class="hrep-panel">
                    <div class="hrep-panel-title">
                        Ingresos por día
                        <small>cuánta gente entra cada fecha</small>
                    </div>
                    <div class="hrep-panel-body">
                        <table class="hrep-mini">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="ctr">Res.</th>
                                    <th class="ctr">Hab.</th>
                                    <th class="ctr">Huésp.</th>
                                    <th class="bar-col">Ocupación</th>
                                    <th class="num">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="d in byDay" :key="d.date" :class="{ peak: d.guests === peakGuests }">
                                    <td>
                                        <strong>{{ shortDate(d.date) }}</strong>
                                        <span class="hrep-dim">{{ weekDay(d.date) }}</span>
                                    </td>
                                    <td class="ctr">{{ d.count }}</td>
                                    <td class="ctr">{{ d.rooms }}</td>
                                    <td class="ctr"><strong>{{ d.guests }}</strong></td>
                                    <td class="bar-col">
                                        <div class="hrep-bar-track">
                                            <div class="hrep-bar-fill" :style="{ width: barWidth(d.guests) }"></div>
                                        </div>
                                    </td>
                                    <td class="num">{{ money(d.total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="hrep-panel">
                    <div class="hrep-panel-title">
                        Por tipo de habitación
                        <small>cómo se reparte el periodo</small>
                    </div>
                    <div class="hrep-panel-body">
                        <table class="hrep-mini">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th class="ctr">Res.</th>
                                    <th class="ctr">Huésp.</th>
                                    <th class="num">Importe</th>
                                    <th class="num">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in byCategory" :key="c.category">
                                    <td><strong>{{ c.category }}</strong></td>
                                    <td class="ctr">{{ c.count }}</td>
                                    <td class="ctr">{{ c.guests }}</td>
                                    <td class="num">{{ money(c.total) }}</td>
                                    <td class="num hrep-dim">{{ categoryPct(c) }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================== DETALLE ================== -->
            <div class="hrep-panel hrep-panel-wide">
                <div class="hrep-panel-title">
                    Detalle de reservas
                    <small v-if="search">{{ filteredRecords.length }} de {{ records.length }} registros</small>
                    <small v-else>{{ records.length }} {{ records.length === 1 ? 'registro' : 'registros' }}</small>
                </div>

                <el-table
                    :data="pagedRecords"
                    size="mini"
                    stripe
                    empty-text="No se encontraron reservas con los filtros seleccionados"
                    style="width:100%"
                >
                    <el-table-column label="Hab." width="88" sortable :sort-by="'room'">
                        <template slot-scope="s">
                            <strong>{{ s.row.room }}</strong>
                            <div class="hrep-dim">{{ s.row.category }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Huésped" min-width="190">
                        <template slot-scope="s">
                            <strong>{{ s.row.customer || '—' }}</strong>
                            <div class="hrep-dim" v-if="s.row.customer_number">Doc. {{ s.row.customer_number }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Teléfono" width="105" prop="customer_telephone">
                        <template slot-scope="s">{{ s.row.customer_telephone || '—' }}</template>
                    </el-table-column>

                    <el-table-column label="Pax" width="62" align="center">
                        <template slot-scope="s">
                            {{ (s.row.adults || 0) + (s.row.children || 0) }}
                            <div class="hrep-dim" v-if="s.row.children > 0">{{ s.row.adults }}A·{{ s.row.children }}N</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Ingreso" width="112" sortable :sort-by="'input_date'">
                        <template slot-scope="s">
                            <strong>{{ shortDate(s.row.input_date) }}</strong>
                            <div class="hrep-dim">{{ weekDay(s.row.input_date) }} {{ hhmm(s.row.input_time) }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Salida" width="112" sortable :sort-by="'output_date'">
                        <template slot-scope="s">
                            {{ shortDate(s.row.output_date) }}
                            <div class="hrep-dim">{{ weekDay(s.row.output_date) }} {{ hhmm(s.row.output_time) }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Nt" width="50" align="center" prop="duration" sortable></el-table-column>

                    <el-table-column label="Medio" width="100">
                        <template slot-scope="s">
                            {{ s.row.origin || '—' }}
                            <div><span class="hrep-pill" :class="statusClass(s.row.status)">{{ s.row.status }}</span></div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Pago" width="118">
                        <template slot-scope="s">
                            <span class="hrep-pill" :class="paymentClass(s.row.payment_key)">{{ s.row.payment_state }}</span>
                            <div class="hrep-dim" v-if="s.row.document_number">{{ s.row.document_number }}</div>
                        </template>
                    </el-table-column>

                    <el-table-column label="Total" width="88" align="right" prop="total" sortable>
                        <template slot-scope="s"><strong>{{ num(s.row.total) }}</strong></template>
                    </el-table-column>

                    <el-table-column label="Pagado" width="88" align="right" prop="paid" sortable>
                        <template slot-scope="s"><span class="hrep-ok">{{ num(s.row.paid) }}</span></template>
                    </el-table-column>

                    <el-table-column label="Deuda" width="88" align="right" prop="debt" sortable>
                        <template slot-scope="s">
                            <span :class="s.row.debt > 0 ? 'hrep-bad' : 'hrep-dim'">{{ num(s.row.debt) }}</span>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="hrep-tfoot" v-if="filteredRecords.length">
                    <div class="hrep-tfoot-label">
                        Totales{{ search ? ' (filtrados)' : '' }} · {{ filteredRecords.length }} reservas
                    </div>
                    <div class="hrep-tfoot-nums">
                        <span>Huéspedes <b>{{ sum('adults') + sum('children') }}</b></span>
                        <span>Noches <b>{{ sum('duration') }}</b></span>
                        <span>Importe <b>{{ money(sum('total')) }}</b></span>
                        <span>Pagado <b class="hrep-ok">{{ money(sum('paid')) }}</b></span>
                        <span>Deuda <b class="hrep-bad">{{ money(sum('debt')) }}</b></span>
                    </div>
                </div>

                <div class="hrep-pager" v-if="filteredRecords.length > pageSize">
                    <el-pagination
                        background
                        layout="prev, pager, next, sizes, total"
                        :page-sizes="[25, 50, 100, 200]"
                        :page-size.sync="pageSize"
                        :current-page.sync="page"
                        :total="filteredRecords.length"
                    ></el-pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    props: {
        establishment: { type: Object, default: null },
    },
    data() {
        return {
            loading: false,
            records: [],
            totals: {},
            byDay: [],
            byCategory: [],
            categories: [],
            rooms: [],
            origins: [
                { value: 'whatsapp',   label: 'WhatsApp' },
                { value: 'correo',     label: 'Correo' },
                { value: 'celular',    label: 'Celular' },
                { value: 'presencial', label: 'Presencial' },
                { value: 'web',        label: 'Web' },
            ],
            presets: [
                { key: 'today',     label: 'Hoy' },
                { key: 'tomorrow',  label: 'Mañana' },
                { key: 'week',      label: 'Esta semana' },
                { key: 'month',     label: 'Este mes' },
                { key: 'next7',     label: 'Próximos 7 días' },
            ],
            activePreset: 'today',
            range: [],
            search: '',
            page: 1,
            pageSize: 25,
            filters: {
                date_field: 'input',
                category_id: null,
                room_id: null,
                status: null,
                payment_state: null,
                origin: null,
                reservation_type: null,
            },
        };
    },
    computed: {
        criterionHint() {
            return {
                input:   'Reservas que INGRESAN en el periodo.',
                output:  'Reservas que SALEN en el periodo.',
                stay:    'Reservas alojadas durante el periodo (aunque entraran antes).',
                created: 'Reservas REGISTRADAS en el periodo.',
            }[this.filters.date_field] || '';
        },
        paidPct() {
            const total = parseFloat(this.totals.total) || 0;
            if (total <= 0) return 0;
            return Math.round(100 * (parseFloat(this.totals.paid) || 0) / total);
        },
        peakGuests() {
            return this.byDay.reduce((max, d) => Math.max(max, d.guests || 0), 0);
        },
        // La búsqueda filtra en el navegador sobre lo ya traído: es instantánea
        // y no vuelve a pegarle al servidor por cada tecla.
        filteredRecords() {
            const term = this.search.trim().toLowerCase();
            if (!term) return this.records;

            return this.records.filter(r => [
                r.customer, r.customer_number, r.room, r.category,
                r.license_plate, r.customer_telephone, r.document_number,
            ].some(v => (v || '').toString().toLowerCase().includes(term)));
        },
        pagedRecords() {
            const from = (this.page - 1) * this.pageSize;
            return this.filteredRecords.slice(from, from + this.pageSize);
        },
    },
    watch: {
        search() { this.page = 1; },
        pageSize() { this.page = 1; },
    },
    async mounted() {
        this.applyPreset('today', false);
        await Promise.all([this.loadCategories(), this.loadRooms()]);
        await this.fetchReport();
    },
    methods: {
        applyPreset(key, fetch = true) {
            const fmt = 'YYYY-MM-DD';
            let start = moment();
            let end = moment();

            if (key === 'tomorrow') {
                start = moment().add(1, 'day');
                end = start.clone();
            } else if (key === 'week') {
                start = moment().startOf('isoWeek');
                end = moment().endOf('isoWeek');
            } else if (key === 'month') {
                start = moment().startOf('month');
                end = moment().endOf('month');
            } else if (key === 'next7') {
                end = moment().add(6, 'days');
            }

            this.activePreset = key;
            this.range = [start.format(fmt), end.format(fmt)];

            if (fetch) this.fetchReport();
        },
        onRangeChange() {
            // Rango elegido a mano: deja de coincidir con ningún atajo.
            this.activePreset = null;
            this.fetchReport();
        },
        resetFilters() {
            this.filters = {
                date_field: 'input',
                category_id: null,
                room_id: null,
                status: null,
                payment_state: null,
                origin: null,
                reservation_type: null,
            };
            this.search = '';
            this.applyPreset('today');
        },
        queryParams() {
            const [start, end] = this.range && this.range.length === 2
                ? this.range
                : [moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD')];

            const params = new URLSearchParams({ start, end, date_field: this.filters.date_field });

            Object.keys(this.filters).forEach(key => {
                if (key === 'date_field') return;
                const value = this.filters[key];
                if (value !== null && value !== '' && value !== undefined) {
                    params.append(key, value);
                }
            });

            return params;
        },
        async fetchReport() {
            if (!this.range || this.range.length !== 2) return;

            this.loading = true;
            try {
                const { data } = await this.$http.get(
                    `/hotels/reservations/report/data?${this.queryParams().toString()}`
                );

                this.records    = data.records || [];
                this.totals     = data.totals || {};
                this.byDay      = data.by_day || [];
                this.byCategory = data.by_category || [];
                this.page       = 1;
            } catch (e) {
                this.$message.error('No se pudo cargar el reporte.');
            } finally {
                this.loading = false;
            }
        },
        async loadCategories() {
            try {
                const { data } = await this.$http.get('/hotels/reservations/calendar/categories');
                this.categories = data.data || [];
            } catch (e) { this.categories = []; }
        },
        async loadRooms() {
            try {
                const { data } = await this.$http.get('/hotels/reservations/calendar/rooms');
                this.rooms = data.data || [];
            } catch (e) { this.rooms = []; }
        },
        download(format) {
            const params = this.queryParams();
            if (format === 'pdf') params.append('format', 'pdf');

            window.open(`/hotels/reservations/calendar/export?${params.toString()}`, '_blank');
        },
        sum(field) {
            return Math.round(
                this.filteredRecords.reduce((acc, r) => acc + (parseFloat(r[field]) || 0), 0) * 100
            ) / 100;
        },
        barWidth(guests) {
            if (!this.peakGuests) return '2%';
            return `${Math.max(3, Math.round(100 * guests / this.peakGuests))}%`;
        },
        categoryPct(cat) {
            const total = parseFloat(this.totals.total) || 0;
            if (total <= 0) return 0;
            return Math.round(1000 * cat.total / total) / 10;
        },
        money(value) {
            const amount = parseFloat(value);
            return `S/ ${(isNaN(amount) ? 0 : amount).toFixed(2)}`;
        },
        num(value) {
            const amount = parseFloat(value);
            return (isNaN(amount) ? 0 : amount).toFixed(2);
        },
        shortDate(value) {
            if (!value) return '';
            const d = moment(String(value).substring(0, 10), 'YYYY-MM-DD');
            return d.isValid() ? d.format('DD/MM/YYYY') : value;
        },
        weekDay(value) {
            if (!value) return '';
            const d = moment(String(value).substring(0, 10), 'YYYY-MM-DD');
            return d.isValid() ? ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][d.day()] : '';
        },
        hhmm(value) {
            return value ? String(value).substring(0, 5) : '';
        },
        statusClass(status) {
            return {
                'Reserva':    'pill-blue',
                'En curso':   'pill-teal',
                'Finalizado': 'pill-slate',
            }[status] || 'pill-slate';
        },
        paymentClass(key) {
            return { paid: 'pill-ok', partial: 'pill-warn', unpaid: 'pill-bad' }[key] || 'pill-slate';
        },
    },
};
</script>

<style scoped>
.hrep { color: #333; }

/* ---------------- Filtros ---------------- */
.hrep-filters {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 14px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
}

.hrep-row-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f3f5;
}

.hrep-presets { display: flex; background: #f3f4f6; border-radius: 6px; padding: 2px; gap: 1px; }

.hrep-preset {
    border: 0; background: transparent; padding: 6px 12px; border-radius: 5px;
    font-size: 12px; font-weight: 600; color: #6b7280; cursor: pointer; transition: all .12s;
}
.hrep-preset:hover { background: #fff; color: #111; }
.hrep-preset.active { background: #4f46e5; color: #fff; box-shadow: 0 1px 3px rgba(79, 70, 229, .3); }

.hrep-actions { display: flex; gap: 8px; }

.hrep-btn {
    display: inline-flex; align-items: center; gap: 6px;
    border: 1px solid #dce0e6; background: #fff; color: #374151;
    padding: 7px 13px; border-radius: 6px; font-size: 12.5px; font-weight: 600;
    cursor: pointer; transition: all .15s;
}
.hrep-btn:hover:not(:disabled) { background: #f9fafb; border-color: #c7cbd1; }
.hrep-btn:disabled { opacity: .5; cursor: not-allowed; }
.hrep-btn-ghost { color: #6b7280; }
.hrep-btn-excel { border-color: #10b981; color: #047857; }
.hrep-btn-excel:hover:not(:disabled) { background: #ecfdf5; }
.hrep-btn-pdf { background: #4f46e5; border-color: #4f46e5; color: #fff; }
.hrep-btn-pdf:hover:not(:disabled) { background: #4338ca; border-color: #4338ca; }

.hrep-row-fields {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(158px, 1fr));
    gap: 10px 12px;
    padding-top: 12px;
}

.hrep-field-dates { grid-column: span 2; min-width: 250px; }
.hrep-field-search { grid-column: span 2; }

.hrep-field label {
    display: block; font-size: 11px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px;
}

.hrep-hint { display: block; font-size: 10.5px; color: #9ca3af; margin-top: 3px; line-height: 1.3; }

/* ---------------- Tarjetas de resumen ---------------- */
.hrep-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
}

.hrep-kpi {
    background: #fff; border: 1px solid #e5e7eb; border-top: 3px solid #4f46e5;
    border-radius: 8px; padding: 12px 14px; display: flex; flex-direction: column;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
}
.hrep-kpi-accent { border-top-color: #0d9488; }
.hrep-kpi-debt { border-top-color: #dc2626; }

.hrep-kpi-label {
    font-size: 10.5px; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: .7px;
}
.hrep-kpi-value { font-size: 26px; font-weight: 700; color: #111827; line-height: 1.15; margin-top: 4px; }
.hrep-kpi-money { font-size: 19px; }
.hrep-kpi-hint { font-size: 11px; color: #9ca3af; margin-top: 2px; }

/* ---------------- Paneles ---------------- */
.hrep-panels {
    display: grid;
    grid-template-columns: 1.35fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

@media (max-width: 1100px) {
    .hrep-panels { grid-template-columns: 1fr; }
}

.hrep-panel {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, .04); overflow: hidden;
}

.hrep-panel-title {
    padding: 11px 14px; border-bottom: 1px solid #f1f3f5;
    font-size: 12.5px; font-weight: 700; color: #111827;
    text-transform: uppercase; letter-spacing: .5px;
}
.hrep-panel-title small {
    text-transform: none; letter-spacing: 0; font-weight: 400;
    color: #9ca3af; margin-left: 8px; font-size: 11.5px;
}

.hrep-panel-body { max-height: 300px; overflow-y: auto; }

table.hrep-mini { width: 100%; border-collapse: collapse; font-size: 12.5px; }
table.hrep-mini th {
    position: sticky; top: 0; background: #fff; z-index: 1;
    text-align: left; padding: 8px 12px; font-size: 10.5px; font-weight: 700;
    color: #6b7280; text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid #e5e7eb;
}
table.hrep-mini td { padding: 8px 12px; border-bottom: 1px solid #f4f6f8; }
table.hrep-mini tr.peak td { background: #f0fdfa; }
table.hrep-mini .ctr, table.hrep-mini th.ctr { text-align: center; }
table.hrep-mini .num, table.hrep-mini th.num { text-align: right; }
.bar-col { width: 26%; }

.hrep-bar-track { background: #eef1f5; border-radius: 999px; height: 7px; overflow: hidden; }
.hrep-bar-fill { background: #0d9488; height: 7px; border-radius: 999px; transition: width .25s; }

/* ---------------- Detalle ---------------- */
.hrep-panel-wide { padding-bottom: 4px; }

.hrep-tfoot {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
    padding: 11px 14px; background: #f9fafb; border-top: 1px solid #e5e7eb;
    font-size: 12.5px; color: #374151;
}
.hrep-tfoot-label { font-weight: 700; color: #111827; }
.hrep-tfoot-nums { display: flex; gap: 18px; flex-wrap: wrap; color: #6b7280; }
.hrep-tfoot-nums b { color: #111827; margin-left: 4px; }

.hrep-pager { padding: 10px 14px; display: flex; justify-content: flex-end; }

/* ---------------- Utilidades ---------------- */
.hrep-dim { color: #9ca3af; font-size: 11px; }
.hrep-ok { color: #059669; }
.hrep-bad { color: #dc2626; font-weight: 600; }

.hrep-pill {
    display: inline-block; padding: 1px 7px; border-radius: 999px;
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
}
.pill-ok    { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.pill-warn  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.pill-bad   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.pill-blue  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.pill-teal  { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
.pill-slate { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
</style>
