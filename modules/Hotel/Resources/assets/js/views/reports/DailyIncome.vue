<template>
    <div class="dinc">
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels/reception">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top:-5px" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12"/><path d="M20 12v4h-4a2 2 0 0 1 0 -4h4"/></svg>
                </a>
                Ingresos diarios
            </h2>
            <ol class="breadcrumbs">
                <li><span><a href="/hotels/reception">Hoteles</a></span></li>
                <li class="active"><span>Ingresos diarios</span></li>
            </ol>
        </div>

        <div v-if="establishments.length > 1" class="dinc-branches">
            <button v-for="e in establishments" :key="e.id" class="dinc-branch" :class="{ active: establishmentId === e.id }" @click="selectBranch(e.id)">
                {{ e.description }}
            </button>
            <button class="dinc-branch" :class="{ active: establishmentId === 'all' }" @click="selectBranch('all')">Todas</button>
        </div>

        <div class="dinc-filters">
            <div class="dinc-presets">
                <button v-for="p in presets" :key="p.key" class="dinc-preset" :class="{ active: activePreset === p.key }" @click="applyPreset(p.key)">
                    {{ p.label }}
                </button>
            </div>
            <div class="dinc-range">
                <button class="dinc-export" :disabled="loading || !turns.length" @click="exportExcel">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="19"/><line x1="15" y1="13" x2="9" y2="19"/></svg>
                    Exportar Excel
                </button>
                <!-- Dos calendarios separados: el de rango confundía (el segundo panel
                     es sólo el mes siguiente, no la fecha final) -->
                <label class="dinc-date">
                    <span>Desde</span>
                    <el-date-picker v-model="dateFrom" type="date" size="small" format="dd/MM/yyyy" value-format="yyyy-MM-dd"
                        :clearable="false" placeholder="Desde" style="width:140px" @change="onFromChange"></el-date-picker>
                </label>
                <label class="dinc-date">
                    <span>Hasta</span>
                    <el-date-picker v-model="dateTo" type="date" size="small" format="dd/MM/yyyy" value-format="yyyy-MM-dd"
                        :clearable="false" placeholder="Hasta" style="width:140px" :picker-options="{ disabledDate: beforeFrom }"
                        @change="onToChange"></el-date-picker>
                </label>
            </div>
        </div>

        <p class="dinc-note">
            "Ocupadas" son las habitaciones con huésped ese día y "Cobradas" las que pagaron ese día (una estadía de varias noches pagada
            al entrar sólo se cobra el primer día). Dinero que entró a caja cada día y en cada turno, por método de pago. Un turno es una apertura de caja hasta su cierre
            (puede durar varios días); aquí sólo se suman sus pagos dentro del periodo elegido. No depende de los comprobantes: cuenta los pagos registrados
            (habitaciones, notas de venta, comprobantes y otros ingresos). No descuenta gastos.
        </p>

        <div v-loading="loading">
            <!-- Totales del periodo -->
            <div class="dinc-cards">
                <div class="dinc-card dinc-card-total">
                    <span class="dinc-card-label">Total del periodo</span>
                    <span class="dinc-card-value">S/ {{ money(totals.total) }}</span>
                </div>
                <div class="dinc-card dinc-card-click" :class="{ active: showRoomsNow }" title="Ver qué habitaciones están ocupadas ahora" @click="showRoomsNow = !showRoomsNow">
                    <span class="dinc-card-label">Ocupadas ahora <span class="dinc-caret" :class="{ open: showRoomsNow }">▸</span></span>
                    <span class="dinc-card-value">{{ roomsNow.occupied }} <small class="text-muted">de {{ roomsNow.total }}</small></span>
                </div>
                <div v-for="m in methods" :key="m.id" class="dinc-card">
                    <span class="dinc-card-label">{{ m.description }}</span>
                    <span class="dinc-card-value">S/ {{ money(totals.by_method[m.id]) }}</span>
                </div>
            </div>

            <div v-if="showRoomsNow" class="card dinc-block dinc-rooms-now">
                <div class="card-body py-2">
                    <b>Habitaciones ocupadas ahora ({{ roomsNow.occupied }})</b>
                    <div class="dinc-chips">
                        <span v-for="r in roomsNow.names || []" :key="r" class="dinc-chip">{{ r }}</span>
                        <span v-if="!(roomsNow.names || []).length" class="text-muted">Ninguna.</span>
                    </div>
                </div>
            </div>

            <!-- Por día -->
            <div class="card dinc-block">
                <div class="card-header"><b>Por día</b> <small class="text-muted">· haga clic en un día para ver qué habitaciones estuvieron ocupadas</small></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 dinc-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-center">Turnos</th>
                                <th class="text-center" title="Habitaciones con huésped ese día, hayan pagado ese día o no">Ocupadas</th>
                                <th class="text-center" title="Habitaciones que pagaron ese día">Cobradas</th>
                                <th v-for="m in methods" :key="m.id" class="text-end">{{ m.description }}</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="d in days">
                            <tr :key="d.date" class="dinc-turn" @click="toggleDay(d.date)">
                                <td class="text-nowrap"><span class="dinc-caret" :class="{ open: expandedDays[d.date] }">▸</span> {{ dayLabel(d.date) }}</td>
                                <td class="text-center">{{ d.turns }}</td>
                                <td class="text-center fw-bold" :title="(d.occupied || []).join(', ')">{{ (d.occupied || []).length || '—' }}</td>
                                <td class="text-center" :title="(d.rooms || []).join(', ')">{{ (d.rooms || []).length || '—' }}</td>
                                <td v-for="m in methods" :key="m.id" class="text-end">{{ money(d.by_method[m.id]) }}</td>
                                <td class="text-end fw-bold">{{ money(d.total) }}</td>
                            </tr>
                            <tr v-if="expandedDays[d.date]" :key="d.date + '-rooms'" class="dinc-detail-row">
                                <td :colspan="methods.length + 5">
                                    <div class="dinc-day-rooms">
                                        <b>Ocupadas ({{ (d.occupied || []).length }}):</b>
                                        <span v-for="r in d.occupied || []" :key="'o' + r" class="dinc-chip" :class="{ paid: (d.rooms || []).includes(r) }">{{ r }}</span>
                                        <span v-if="!(d.occupied || []).length" class="text-muted">ninguna</span>
                                    </div>
                                    <div class="dinc-day-rooms">
                                        <b>Cobradas ese día ({{ (d.rooms || []).length }}):</b>
                                        <span v-for="r in d.rooms || []" :key="'c' + r" class="dinc-chip paid">{{ r }}</span>
                                        <span v-if="!(d.rooms || []).length" class="text-muted">ninguna</span>
                                    </div>
                                </td>
                            </tr>
                            </template>
                            <tr v-if="!days.length">
                                <td :colspan="methods.length + 5" class="text-center text-muted py-4">No hay ingresos en este periodo.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Por turno de caja -->
            <div class="card dinc-block">
                <div class="card-header"><b>Por turno de caja</b></div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 dinc-table">
                        <thead>
                            <tr>
                                <th style="width:28px"></th>
                                <th>Cajero</th>
                                <th v-if="establishmentId === 'all'">Sucursal</th>
                                <th>Apertura</th>
                                <th>Cierre</th>
                                <th class="text-end">Abrió con</th>
                                <th v-for="m in methods" :key="m.id" class="text-end">{{ m.description }}</th>
                                <th class="text-end">Total ingresos</th>
                                <th class="text-end" :title="'Abrió con + ' + cashLabel">Efectivo en caja</th>
                                <th>Habitaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="t in turns">
                            <tr :key="t.id" class="dinc-turn" @click="toggle(t.id)">
                                <td class="text-center"><span class="dinc-caret" :class="{ open: expanded[t.id] }">▸</span></td>
                                <td>{{ t.cashier || '—' }}</td>
                                <td v-if="establishmentId === 'all'">{{ t.establishment || '—' }}</td>
                                <td class="text-nowrap">{{ dateTime(t.opened_at) }}</td>
                                <td class="text-nowrap">
                                    <span v-if="t.open" class="badge bg-success">Abierta</span>
                                    <span v-else>{{ dateTime(t.closed_at) }}</span>
                                </td>
                                <td class="text-end">{{ money(t.beginning_balance) }}</td>
                                <td v-for="m in methods" :key="m.id" class="text-end">{{ money(t.by_method[m.id]) }}</td>
                                <td class="text-end fw-bold">{{ money(t.total) }}</td>
                                <td class="text-end">{{ money(t.beginning_balance + (cashMethodId ? (t.by_method[cashMethodId] || 0) : 0)) }}</td>
                                <td>
                                    <span v-if="t.rooms.length">{{ t.rooms.length }} · {{ t.rooms.join(', ') }}</span>
                                    <span v-else class="text-muted">—</span>
                                </td>
                            </tr>
                            <tr v-if="expanded[t.id]" :key="t.id + '-detail'" class="dinc-detail-row">
                                <td></td>
                                <td :colspan="methods.length + 7">
                                    <table class="table table-sm mb-0 dinc-detail">
                                        <thead>
                                            <tr><th>Hora</th><th>Habitación</th><th>Concepto</th><th>Huésped / cliente</th><th>Método</th><th class="text-end">Monto</th><th></th></tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(d, i) in t.detail" :key="i">
                                                <td class="text-nowrap">{{ dateTime(d.created_at || d.time) }}</td>
                                                <td class="fw-bold">{{ d.room || '—' }}</td>
                                                <td>{{ d.concept }}</td>
                                                <td>{{ d.customer || '—' }}</td>
                                                <td>{{ methodName(d.method_id) }}</td>
                                                <td class="text-end">{{ money(d.amount) }}</td>
                                                <td class="text-end" style="width:70px">
                                                    <button v-if="d.removable" class="dinc-remove" title="Quitar este pago (registrado por error)" @click.stop="removePayment(d)">Quitar</button>
                                                </td>
                                            </tr>
                                            <tr v-if="!t.detail.length"><td colspan="7" class="text-muted text-center">Sin ingresos en este turno.</td></tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </template>
                            <tr v-if="!turns.length">
                                <td :colspan="methods.length + 8" class="text-center text-muted py-4">No hay turnos de caja en este periodo.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    data() {
        return {
            loading: false,
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            activePreset: 'today',
            presets: [
                { key: 'today', label: 'Hoy' },
                { key: 'yesterday', label: 'Ayer' },
                { key: 'week', label: 'Esta semana' },
                { key: 'month', label: 'Este mes' },
            ],
            methods: [],
            days: [],
            turns: [],
            totals: { by_method: {}, total: 0 },
            expanded: {},
            expandedDays: {},
            showRoomsNow: false,
            roomsNow: { occupied: 0, total: 0, names: [] },
            establishments: [],
            establishmentId: null,
        };
    },
    computed: {
        cashMethodId() {
            const cash = this.methods.find((m) => (m.description || '').toLowerCase().includes('efectivo'));
            return cash ? cash.id : null;
        },
        cashLabel() {
            const cash = this.methods.find((m) => m.id === this.cashMethodId);
            return cash ? cash.description : 'Efectivo';
        },
    },
    created() {
        this.load();
    },
    methods: {
        applyPreset(key) {
            this.activePreset = key;
            const today = moment();
            const ranges = {
                today: [today.clone(), today.clone()],
                yesterday: [today.clone().subtract(1, 'day'), today.clone().subtract(1, 'day')],
                week: [today.clone().startOf('isoWeek'), today.clone()],
                month: [today.clone().startOf('month'), today.clone()],
            };
            [this.dateFrom, this.dateTo] = ranges[key].map((d) => d.format('YYYY-MM-DD'));
            this.load();
        },
        beforeFrom(date) {
            return this.dateFrom ? moment(date).format('YYYY-MM-DD') < this.dateFrom : false;
        },
        onFromChange() {
            // Si la fecha inicial pasa a la final, la final la sigue (un solo día)
            if (this.dateTo < this.dateFrom) this.dateTo = this.dateFrom;
            this.activePreset = null;
            this.load();
        },
        onToChange() {
            if (this.dateTo < this.dateFrom) this.dateFrom = this.dateTo;
            this.activePreset = null;
            this.load();
        },
        removePayment(d) {
            const text = `¿Quitar el pago de S/ ${this.money(d.amount)} (${d.concept}${d.customer ? ' · ' + d.customer : ''})? `
                + 'Úselo sólo si se registró por error: se borra el pago y sale también de la caja. No se puede deshacer.';
            this.$confirm(text, 'Quitar pago', { confirmButtonText: 'Sí, quitar', cancelButtonText: 'Cancelar', type: 'warning' })
                .then(async () => {
                    try {
                        const { data } = await this.$http.post(`/hotels/income-report/payments/${d.id}/remove`);
                        this.$message.success(data.message || 'Pago quitado.');
                        this.load();
                    } catch (e) {
                        this.$message.error(e.response?.data?.message || 'No se pudo quitar el pago.');
                    }
                })
                .catch(() => {});
        },
        toggleDay(date) {
            this.$set(this.expandedDays, date, !this.expandedDays[date]);
        },
        async load() {
            if (!this.dateFrom || !this.dateTo) return;
            this.loading = true;
            try {
                const params = { from: this.dateFrom, to: this.dateTo };
                if (this.establishmentId !== null) params.establishment_id = this.establishmentId;
                const { data } = await this.$http.get('/hotels/income-report/data', { params });
                this.establishments = data.establishments || [];
                this.establishmentId = data.establishment_id;
                this.methods = data.methods || [];
                this.days = data.days || [];
                this.turns = data.turns || [];
                this.totals = data.totals || { by_method: {}, total: 0 };
                this.roomsNow = data.rooms_now || { occupied: 0, total: 0, names: [] };
                this.expandedDays = {};
                if (data.from && data.to && (data.from !== this.dateFrom || data.to !== this.dateTo)) {
                    this.dateFrom = data.from;
                    this.dateTo = data.to;
                    this.$message.info('El periodo se limitó a 2 meses.');
                }
            } catch (e) {
                this.$message.error(e.response?.status === 403 ? 'Sólo un administrador puede ver este reporte.' : 'No se pudo cargar el reporte.');
            } finally {
                this.loading = false;
            }
        },
        exportExcel() {
            if (!this.dateFrom || !this.dateTo) return;
            const q = new URLSearchParams({ from: this.dateFrom, to: this.dateTo, establishment_id: this.establishmentId ?? 'all' });
            window.open(`/hotels/income-report/export?${q.toString()}`, '_blank');
        },
        selectBranch(id) {
            if (this.establishmentId === id) return;
            this.establishmentId = id;
            this.expanded = {};
            this.load();
        },
        toggle(id) {
            this.$set(this.expanded, id, !this.expanded[id]);
        },
        methodName(id) {
            const m = this.methods.find((x) => String(x.id) === String(id));
            return m ? m.description : 'Otros';
        },
        money(value) {
            const n = Number(value) || 0;
            return n === 0 ? '—' : n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        dayLabel(date) {
            // Intl y no moment: moment no tiene cargado el idioma español y mostraba "Mon", "Tue"…
            const [y, m, d] = date.split('-').map(Number);
            return new Intl.DateTimeFormat('es-PE', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' })
                .format(new Date(y, m - 1, d));
        },
        dateTime(value) {
            if (!value) return '—';
            const m = moment(value, ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD HH:mm', 'YYYY-MM-DD']);
            return m.isValid() ? m.format('DD/MM HH:mm') : value;
        },
    },
};
</script>

<style scoped>
.dinc-branches { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
.dinc-branch { border: 1px solid #c9ced6; background: #fff; border-radius: 6px; padding: 6px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
.dinc-branch.active { background: #0f5132; border-color: #0f5132; color: #fff; }
.dinc-filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.dinc-presets { display: flex; flex-wrap: wrap; gap: 6px; }
.dinc-preset { border: 1px solid #d9d9d9; background: #fff; border-radius: 16px; padding: 4px 14px; font-size: 13px; cursor: pointer; }
.dinc-preset.active { background: #1f6feb; border-color: #1f6feb; color: #fff; }
.dinc-range { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.dinc-export { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #1d7a3e; color: #1d7a3e; background: #fff; border-radius: 6px; padding: 5px 12px; font-size: 13px; cursor: pointer; }
.dinc-export:disabled { opacity: .5; cursor: default; }
.dinc-note { color: #6c757d; font-size: 12.5px; margin: 4px 0 14px; }
.dinc-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 16px; }
.dinc-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; display: flex; flex-direction: column; }
.dinc-card-total { border-color: #1f6feb; }
.dinc-card-label { font-size: 12px; color: #6c757d; }
.dinc-card-value { font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums; }
.dinc-block { margin-bottom: 16px; }
.dinc-table td, .dinc-table th { font-variant-numeric: tabular-nums; vertical-align: middle; }
.dinc-table th { white-space: nowrap; font-size: 12.5px; }
.dinc-turn { cursor: pointer; }
.dinc-turn:hover { background: #f6f8fa; }
.dinc-caret { display: inline-block; transition: transform .15s; color: #6c757d; }
.dinc-caret.open { transform: rotate(90deg); }
.dinc-detail-row > td { background: #fafbfc; }
.dinc-detail { font-size: 12.5px; background: transparent; }
.dinc-remove { border: 1px solid #dc3545; color: #dc3545; background: #fff; border-radius: 4px; padding: 1px 8px; font-size: 11.5px; cursor: pointer; }
.dinc-remove:hover { background: #dc3545; color: #fff; }
.dinc-date { display: inline-flex; align-items: center; gap: 5px; margin: 0; font-size: 12.5px; color: #555; }
.dinc-card-click { cursor: pointer; }
.dinc-card-click:hover, .dinc-card-click.active { border-color: #0f5132; }
.dinc-chips, .dinc-day-rooms { display: flex; flex-wrap: wrap; gap: 5px; align-items: center; margin: 4px 0; font-size: 12.5px; }
.dinc-day-rooms > b { margin-right: 4px; }
.dinc-chip { display: inline-block; min-width: 30px; text-align: center; padding: 2px 8px; border-radius: 12px; background: #e9ecef; font-weight: 600; font-size: 12px; }
.dinc-chip.paid { background: #d1e7dd; color: #0f5132; }
</style>
