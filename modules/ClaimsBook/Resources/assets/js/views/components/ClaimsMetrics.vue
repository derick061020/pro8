<template>
    <div class="cbm-wrap" v-if="metrics">

        <!-- KPIs Primarios -->
        <div class="cbm-primary-grid">

            <!-- Total -->
            <div class="cbm-card">
                <div class="cbm-card__icon cbm-card__icon--neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12"/>
                        <path d="M19 16h-12a2 2 0 0 0 -2 2"/>
                        <path d="M9 8h6"/>
                    </svg>
                </div>
                <div class="cbm-card__body">
                    <span class="cbm-card__value">{{ metrics.total }}</span>
                    <span class="cbm-card__label">Total registros</span>
                </div>
            </div>

            <!-- Abiertos -->
            <div class="cbm-card">
                <div class="cbm-card__icon cbm-card__icon--warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9"/>
                        <polyline points="12 7 12 12 15 15"/>
                    </svg>
                </div>
                <div class="cbm-card__body">
                    <span class="cbm-card__value cbm-card__value--warning">{{ metrics.open }}</span>
                    <span class="cbm-card__label">En proceso</span>
                </div>
            </div>

            <!-- Cerrados -->
            <div class="cbm-card">
                <div class="cbm-card__icon cbm-card__icon--success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12l2 2l4 -4"/>
                    </svg>
                </div>
                <div class="cbm-card__body">
                    <span class="cbm-card__value cbm-card__value--success">{{ metrics.closed }}</span>
                    <span class="cbm-card__label">Resueltos</span>
                </div>
            </div>

            <!-- Vencidos -->
            <div class="cbm-card">
                <div class="cbm-card__icon cbm-card__icon--danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v4"/>
                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                        <path d="M12 16h.01"/>
                    </svg>
                </div>
                <div class="cbm-card__body">
                    <span class="cbm-card__value cbm-card__value--danger">{{ metrics.overdue }}</span>
                    <span class="cbm-card__label">Vencidos</span>
                </div>
            </div>

            <!-- Tasa de resolución -->
            <div class="cbm-card cbm-card--highlight">
                <div class="cbm-card__icon cbm-card__icon--success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="17" cy="17" r="3"/>
                        <circle cx="7" cy="7" r="3"/>
                        <path d="M10 7l8.5 8.5"/>
                        <path d="M7 10v7.5a2.5 2.5 0 0 0 2.5 2.5h1.5"/>
                    </svg>
                </div>
                <div class="cbm-card__body">
                    <span class="cbm-card__value cbm-card__value--success">{{ metrics.resolution_rate }}%</span>
                    <span class="cbm-card__label">Tasa de resolución</span>
                    <div class="cbm-progress">
                        <div class="cbm-progress__bar" :style="{ width: metrics.resolution_rate + '%' }"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- KPIs Secundarios -->
        <div class="cbm-secondary-grid">

            <div class="cbm-stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cbm-stat__icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                    <path d="M9 12l2 2l4 -4"/>
                </svg>
                <span class="cbm-stat__label">Reclamos</span>
                <span class="cbm-stat__value">{{ metrics.reclamos }}</span>
            </div>

            <div class="cbm-stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cbm-stat__icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                    <path d="M12 8v4"/>
                    <path d="M12 16v.01"/>
                </svg>
                <span class="cbm-stat__label">Quejas</span>
                <span class="cbm-stat__value">{{ metrics.quejas }}</span>
            </div>

            <div class="cbm-stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cbm-stat__icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="12 7 12 12 15 15"/>
                    <circle cx="12" cy="12" r="9"/>
                </svg>
                <span class="cbm-stat__label">Tiempo promedio de resolución</span>
                <span class="cbm-stat__value">{{ metrics.avg_resolution_days !== null ? metrics.avg_resolution_days + ' días' : '—' }}</span>
            </div>

            <div class="cbm-stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cbm-stat__icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <rect x="3" y="12" width="6" height="8" rx="1"/>
                    <rect x="9" y="8" width="6" height="12" rx="1"/>
                    <rect x="15" y="4" width="6" height="16" rx="1"/>
                </svg>
                <span class="cbm-stat__label">Pendientes abiertos</span>
                <span class="cbm-stat__value">{{ metrics.open }}</span>
            </div>

            <div class="cbm-stat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cbm-stat__icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                    <path d="M16 5l3 3"/>
                </svg>
                <span class="cbm-stat__label">Atendidos vs. registrados</span>
                <span class="cbm-stat__value">{{ metrics.closed }}/{{ metrics.total }}</span>
            </div>

        </div>

    </div>
</template>

<style scoped>
.cbm-wrap {
    margin-bottom: 20px;
}

/* ── Primary grid ── */
.cbm-primary-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 10px;
}
@media (max-width: 1100px) { .cbm-primary-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px)  { .cbm-primary-grid { grid-template-columns: repeat(2, 1fr); } }

.cbm-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: box-shadow 0.15s;
}
.cbm-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
}
.cbm-card--highlight {
    border-color: #d1fae5;
    background: #f0fdf4;
}

/* Icon badge */
.cbm-card__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    flex-shrink: 0;
}
.cbm-card__icon--neutral { background: #f4f4f5; color: #52525b; }
.cbm-card__icon--warning { background: #fffbeb; color: #d97706; }
.cbm-card__icon--success { background: #f0fdf4; color: #16a34a; }
.cbm-card__icon--danger  { background: #fef2f2; color: #dc2626; }

/* Text */
.cbm-card__body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.cbm-card__value {
    font-size: 24px;
    font-weight: 700;
    color: #18181b;
    line-height: 1.1;
}
.cbm-card__value--warning { color: #d97706; }
.cbm-card__value--success { color: #16a34a; }
.cbm-card__value--danger  { color: #dc2626; }
.cbm-card__label {
    font-size: 12px;
    color: #71717a;
    white-space: nowrap;
}

/* Progress bar */
.cbm-progress {
    margin-top: 6px;
    height: 4px;
    background: #d1fae5;
    border-radius: 2px;
    overflow: hidden;
    width: 100%;
}
.cbm-progress__bar {
    height: 100%;
    background: #16a34a;
    border-radius: 2px;
    transition: width 0.4s ease;
}

/* ── Secondary grid ── */
.cbm-secondary-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 10px;
    overflow: hidden;
}
.cbm-stat {
    flex: 1 1 160px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-right: 1px solid #f4f4f5;
}
.cbm-stat:last-child { border-right: none; }
@media (max-width: 860px) {
    .cbm-stat { border-right: none; border-bottom: 1px solid #f4f4f5; }
    .cbm-stat:last-child { border-bottom: none; }
}
.cbm-stat__icon { color: #a1a1aa; flex-shrink: 0; }
.cbm-stat__label {
    font-size: 12px;
    color: #71717a;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cbm-stat__value {
    font-size: 13px;
    font-weight: 600;
    color: #18181b;
    white-space: nowrap;
}
</style>

<script>
export default {
    name: 'ClaimsMetrics',
    data() {
        return {
            metrics: null,
        }
    },
    mounted() {
        this.$http.get('/claims/metrics').then(response => {
            this.metrics = response.data
        }).catch(() => {})
    },
    methods: {
        reload() {
            this.$http.get('/claims/metrics').then(response => {
                this.metrics = response.data
            }).catch(() => {})
        },
    },
}
</script>