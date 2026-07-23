<template>
    <div>
        <header class="page-header">
            <h2>
                <a href="/dashboard">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard" style="margin-top: -5px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" /><path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" /><path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" /><path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Dashboard</span>
                </li>
            </ol>
        </header>
        <!-- ── KPI Strip ─────────────────────────────────────────── -->
        <div class="row px-2 mb-3">
            <!-- Total Clientes -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">Total Clientes</span>
                        <svg class="shad-kpi-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                    </div>
                    <div class="shad-kpi-value">{{ records.length }}</div>
                    <div class="shad-kpi-desc"><span class="shad-kpi-green">{{ kpiActivos }}</span> activos · <span :class="kpiBloqueados > 0 ? 'shad-kpi-red' : ''">{{ kpiBloqueados }}</span> bloqueados</div>
                </div>
            </div>
            <!-- Producción -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">En Producción</span>
                        <svg class="shad-kpi-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                    </div>
                    <div class="shad-kpi-value">{{ kpiProduccion }}</div>
                    <div class="shad-kpi-desc">{{ kpiPctProduccion }}% del total de clientes</div>
                </div>
            </div>
            <!-- Demo -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">En Demo</span>
                        <svg class="shad-kpi-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 3l-6 18" /><path d="M15 3l6 18" /><path d="M4 14l16 0" /><path d="M12 3c-1.333 4.667 -2 8 -2 10c0 3 .667 5.333 2 7" /><path d="M12 3c1.333 4.667 2 8 2 10c0 3 -.667 5.333 -2 7" /></svg>
                    </div>
                    <div class="shad-kpi-value">{{ kpiDemo }}</div>
                    <div class="shad-kpi-desc">{{ kpiInterno }} entorno interno</div>
                </div>
            </div>
            <!-- Total Docs -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">Total Comprobantes</span>
                        <svg class="shad-kpi-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                    </div>
                    <div class="shad-kpi-value">{{ formatNum(total_documents) }}</div>
                    <div class="shad-kpi-desc">Historial acumulado</div>
                </div>
            </div>
            <!-- Docs este Mes -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">Docs. este Mes</span>
                        <svg class="shad-kpi-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 17l0 -5" /><path d="M12 17l0 -3" /><path d="M15 17l0 -1" /><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                    </div>
                    <div class="shad-kpi-value">{{ formatNum(kpiDocsMes) }}</div>
                    <div class="shad-kpi-desc">Ciclo actual en curso</div>
                </div>
            </div>
            <!-- Alertas -->
            <div class="col-xl-2 col-md-4 col-6 mb-3">
                <div class="shad-kpi" :class="kpiAlertas > 0 ? 'shad-kpi-danger' : ''">
                    <div class="shad-kpi-header">
                        <span class="shad-kpi-label">Con Alertas</span>
                        <svg class="shad-kpi-icon" :style="kpiAlertas > 0 ? 'color:#ef4444' : ''" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                    </div>
                    <div class="shad-kpi-value" :style="kpiAlertas > 0 ? 'color:#ef4444' : ''">{{ kpiAlertas }}</div>
                    <div class="shad-kpi-desc">{{ kpiAlertas > 0 ? 'Clientes con docs pendientes' : 'Sin alertas activas' }}</div>
                </div>
            </div>
        </div>

        <!-- ── Main Row ───────────────────────────────────────────── -->
        <div class="row px-2 mb-md-4">

            <!-- Gráfico -->
            <div class="col-lg-8 d-flex flex-column mb-3">
                <div class="card flex-fill">
                    <div class="card-body p-0 m-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="chart-data-selector ready ps-3 pe-4 pt-4">
                                    <div class="chart-data-selector-items">
                                        <chart-line v-if="chartDataLoaded && dataChartLine.labels.length > 0"
                                                    :data="dataChartLine"></chart-line>
                                        <div v-else class="d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Cargando gráfico...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row px-4 mt-2 pb-3">
                            <div class="col-2 font-weight-bold text-primary-new">{{ year }}</div>
                            <div class="col-10 font-weight-semibold text-end">Comprobantes generados por mes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-flex flex-column mb-3">

                <!-- Card única: ambas donas -->
                <div class="card mb-0 flex-fill">
                    <div class="card-body p-4 m-0">

                        <!-- Donut: Entorno -->
                        <p class="donut-card-title">Distribución por Entorno</p>
                        <div class="donut-wrap">
                            <div class="donut-svg-wrap">
                                <svg width="100" height="100" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f1f5f9" stroke-width="3.8"/>
                                    <circle v-if="!records.length" cx="18" cy="18" r="15.9155" fill="none" stroke="#e2e8f0" stroke-width="3.8"/>
                                    <circle v-for="(seg, i) in envDonutSegments" :key="i"
                                        cx="18" cy="18" r="15.9155" fill="none"
                                        :stroke="seg.color" stroke-width="3.8"
                                        :stroke-dasharray="`${seg.pct} ${100 - seg.pct}`"
                                        :stroke-dashoffset="seg.offset"
                                        stroke-linecap="butt"
                                        transform="rotate(-90 18 18)"/>
                                </svg>
                                <div class="donut-center">
                                    <span class="donut-center-num">{{ records.length }}</span>
                                    <span class="donut-center-lbl">total</span>
                                </div>
                            </div>
                            <div class="donut-legend">
                                <div v-for="(seg, i) in envDonutSegments" :key="i" class="donut-legend-row">
                                    <span class="donut-legend-dot" :style="`background:${seg.color}`"></span>
                                    <span class="donut-legend-name">{{ seg.label }}</span>
                                    <span class="donut-legend-count" :style="`color:${seg.color}`">{{ seg.count }}</span>
                                    <span class="donut-legend-pct">{{ seg.pct }}%</span>
                                </div>
                                <div v-if="kpiBloqueados > 0" class="donut-legend-row" style="margin-top:5px;padding-top:5px;border-top:1px solid #f1f5f9">
                                    <span class="donut-legend-dot" style="background:#ef4444"></span>
                                    <span class="donut-legend-name">Bloqueados</span>
                                    <span class="donut-legend-count" style="color:#ef4444">{{ kpiBloqueados }}</span>
                                    <span class="donut-legend-pct">—</span>
                                </div>
                            </div>
                        </div>

                        <hr style="border-color:#f1f5f9;margin:12px 0">

                        <!-- Donut: Planes -->
                        <p class="donut-card-title">Planes más usados</p>
                        <div class="donut-wrap">
                            <div class="donut-svg-wrap">
                                <svg width="100" height="100" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f1f5f9" stroke-width="3.8"/>
                                    <circle v-if="!topPlanes.length" cx="18" cy="18" r="15.9155" fill="none" stroke="#e2e8f0" stroke-width="3.8"/>
                                    <circle v-for="(seg, i) in planDonutSegments" :key="i"
                                        cx="18" cy="18" r="15.9155" fill="none"
                                        :stroke="seg.color" stroke-width="3.8"
                                        :stroke-dasharray="`${seg.pct} ${100 - seg.pct}`"
                                        :stroke-dashoffset="seg.offset"
                                        stroke-linecap="butt"
                                        transform="rotate(-90 18 18)"/>
                                </svg>
                                <div class="donut-center">
                                    <span class="donut-center-num">{{ topPlanes.length }}</span>
                                    <span class="donut-center-lbl">planes</span>
                                </div>
                            </div>
                            <div class="donut-legend">
                                <div v-for="(seg, i) in planDonutSegments" :key="i" class="donut-legend-row">
                                    <span class="donut-legend-dot" :style="`background:${seg.color}`"></span>
                                    <span class="donut-legend-name" style="max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ seg.plan }}</span>
                                    <span class="donut-legend-count" :style="`color:${seg.color}`">{{ seg.count }}</span>
                                    <span class="donut-legend-pct">{{ seg.pct }}%</span>
                                </div>
                                <div v-if="!topPlanes.length" class="donut-legend-row" style="color:#94a3b8;font-size:11px">Cargando...</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- ── Sistema ────────────────────────────────────────────── -->
        <div class="row px-2 mb-1" style="display:none">
            <div class="col-md-3 col-6 mb-3">
                <div class="sys-card">
                    <div class="sys-icon" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 5m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M12 9v.01" /><path d="M9 12a3 3 0 1 0 6 0" /></svg>
                    </div>
                    <div class="sys-data">
                        <div class="sys-value">{{ discUsed }}</div>
                        <div class="sys-label">Disco Duro</div>
                        <a href="https://docs.google.com/document/d/1hpEQUs9OFha_35yyLb1cMKeluD-dEku5lQsQ3TJFib8/edit" target="_blank" class="sys-action">Incrementar</a>
                    </div>
                    <div class="sys-ring">
                        <svg viewBox="0 0 36 36" width="44" height="44">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#f0f0f8" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#667eea" stroke-width="3" stroke-dasharray="94.2" :stroke-dashoffset="94.2 - (94.2 * Math.min(parseFloat(discUsed)||0, 100) / 100)" stroke-linecap="round" transform="rotate(-90 18 18)"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="sys-card">
                    <div class="sys-icon" style="background:linear-gradient(135deg,#26c6da,#00838f)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M3 12m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M7 8l0 .01" /><path d="M7 17l0 .01" /></svg>
                    </div>
                    <div class="sys-data">
                        <div class="sys-value">{{ iUsed }}</div>
                        <div class="sys-label">Inodes</div>
                        <a href="https://drive.google.com/open?id=1foPKDI3V3Z9uKTjRc2SPSoztVSOBevPAluT2BqFbfxA" target="_blank" class="sys-action">Limpiar</a>
                    </div>
                    <div class="sys-ring">
                        <svg viewBox="0 0 36 36" width="44" height="44">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#f0f0f8" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#26c6da" stroke-width="3" stroke-dasharray="94.2" :stroke-dashoffset="94.2 - (94.2 * Math.min(parseFloat(iUsed)||0, 100) / 100)" stroke-linecap="round" transform="rotate(-90 18 18)"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="sys-card">
                    <div class="sys-icon" style="background:linear-gradient(135deg,#11c26d,#00966f)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                    </div>
                    <div class="sys-data">
                        <div class="sys-value">{{ storageSize }}</div>
                        <div class="sys-label">Archivos generados</div>
                        <span class="sys-action" style="cursor:default;color:#ccc">—</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="sys-card">
                    <div class="sys-icon" style="background:#292961">
                        <i class="fab fa-gitlab" style="font-size:16px"></i>
                    </div>
                    <div class="sys-data">
                        <div class="sys-value" style="font-size:15px">{{ version }}</div>
                        <div class="sys-label">Versión del sistema</div>
                        <span class="sys-action" style="cursor:default;color:#ccc">—</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-0 mt-3">
            <div class="col-12">
                <header class="page-header">
                    <h2>
                        <a href="/dashboard">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users" style="margin-top: -5px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                        </a>
                    </h2>
                    <ol class="breadcrumbs">
                        <li class="active">
                            <span>Listado de Clientes</span>
                        </li>
                    </ol>
                    <div class="right-wrapper pull-right">
                        <div v-if="canCreateClients !== false" class="btn-group flex-wrap">
                            <button
                                class="btn btn-custom btn-sm mt-2 me-2 mb-3 primary-buton pull-end"
                                type="button"
                                @click.prevent="clickCreate()"
                                v-bind:disabled="!checkLimitUsers || isLoading"
                            >
                                <i :class="{
                                    'fa-plus-circle': checkLimitUsers,
                                    'fa-exclamation-circle': !checkLimitUsers
                                    }"
                                    class="fa"
                                ></i>
                                {{ checkLimitUsers ? 'Nuevo' : 'Clientes máximos creados. Actualice su plan llamando al 944999965' }}
                            </button>
                        </div>
                    </div>
                </header>
            </div>
        </div>

        <div id="client-list"
             class="card">
            <div class="card-body mx-2">
                <!-- Botón para mostrar/ocultar filtros -->
                <div class="btn-filter-content mb-3 d-flex">
                    <el-button
                        type="secondary"
                        class="btn-show-filter"
                        :class="{ shift: isFiltersVisible }"
                        @click="toggleFilters"
                    >
                        {{ isFiltersVisible ? "Ocultar filtros" : "Mostrar filtros" }}
                    </el-button>
                    <el-button
                        v-if="hasActiveFilters"
                        type="secondary"
                        @click="clearFilters"
                    >
                        Limpiar Filtros
                    </el-button>
                </div>

                <!-- Filtros (ahora incluyen el buscador) -->
                <div v-if="isFiltersVisible" class="filter-section">
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-6 col-sm-12 mb-2">
                            <label class="control-label mb-1">Buscar:</label>
                            <el-input
                                v-model="searchQuery"
                                placeholder="Buscar por hostname, nombre, ruc o correo"
                                style="width: 100%;"
                                prefix-icon="el-icon-search"
                                @input="applyFilters">
                            </el-input>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-sm-12 mb-2">
                            <label class="control-label mb-1">Filtrar por Entorno:</label>
                            <el-select
                                v-model="filters.entorno"
                                placeholder="Todos los entornos"
                                style="width: 100%;"
                                @change="applyFilters">
                                <el-option label="Todos" value=""></el-option>
                                <el-option label="Demo" value="01"></el-option>
                                <el-option label="Producción" value="02"></el-option>
                                <el-option label="Interno" value="03"></el-option>
                            </el-select>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-sm-12 mb-2">
                            <label class="control-label mb-1">Filtrar por Plan:</label>
                            <el-select
                                v-model="filters.plan"
                                placeholder="Todos los planes"
                                style="width: 100%;"
                                @change="applyFilters">
                                <el-option label="Todos" value=""></el-option>
                                <el-option
                                    v-for="plan in availablePlans"
                                    :key="plan"
                                    :label="plan"
                                    :value="plan">
                                </el-option>
                            </el-select>
                        </div>
                        <div class="form-group col-lg-3 col-md-6 col-sm-12 mb-2">
                            <label class="control-label mb-1">Estado de Cuenta:</label>
                            <el-select
                                v-model="filters.bloqueo"
                                placeholder="Todos los estados"
                                style="width: 100%;"
                                @change="applyFilters">
                                <el-option label="Todos" value=""></el-option>
                                <el-option label="Activos" value="0"></el-option>
                                <el-option label="Bloqueados" value="1"></el-option>
                            </el-select>
                        </div>
                    </div>
                </div>

                <!-- Columnas mostrar/ocultar -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-end mt-2 me-2 data-table-visible-columns">
                            <el-dropdown :hide-on-click="false" placement="bottom-end">
                                <el-button type="secondary">
                                    Mostrar columnas<i class="el-icon-arrow-down el-icon--right"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown" class="clients-columns-dropdown-menu">
                                    <el-dropdown-item v-for="(column, index) in columnsComputed" :key="index">
                                        <el-checkbox
                                            v-if="column.title !== undefined && column.visible !== undefined"
                                            v-model="column.visible" @change="saveColumnVisibility"
                                        >{{ column.title }}
                                        </el-checkbox>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 position-relative mt-4">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th class="sticky-column">Hostname</th>
                            <th v-if="columns.bloquear_cuenta.visible" class="text-center">Bloquear <br>cuenta</th>
                            <th v-if="columns.nombre.visible" class="column-name">Nombre</th>
                            <th v-if="columns.ruc.visible">RUC</th>
                            <th v-if="columns.plan.visible">Plan</th>
                            <th v-if="columns.correo.visible">Correo</th>
                            <th v-if="columns.entorno.visible">Entorno</th>
                            <th v-if="columns.total_comprobantes.visible" class="text-center">Total de<br>Comprobantes</th>
                            <th v-if="columns.notificaciones.visible" class="text-center">Notificaciones</th>
                            <th v-if="columns.inicio_ciclo.visible" class="text-center">Inicio<br>Ciclo Facturación</th>
                            <th v-if="columns.comprobantes_ciclo.visible" class="text-center">Comprobantes<br>Ciclo Facturación</th>
                            <th v-if="columns.usuarios.visible" class="text-center">Usuarios</th>
                            <th v-if="columns.sucursales.visible" class="text-center">Sucursales</th>
                            <th v-if="columns.ventas_mes.visible" class="text-center">Ventas (Mes)</th>
                            <th v-if="columns.mensajes_whatsapp.visible" class="text-center">Mensajes<br>WhatsApp</th>
                            <th v-if="columns.fecha_creacion.visible" class="text-center">F.Creación</th>
                            <th v-if="columns.consultas_api.visible" class="text-center">Consultas <br>API Peru <br>(mes)</th>
                            <th v-if="columns.notas_venta.visible" class="text-center">Cant. <br>Notas de venta</th>
                            <th v-if="columns.total_mes.visible" class="text-center">Total<br><small>(Comprobantes <br>por mes)</small></th>
                            <th v-if="columns.total_pse.visible" class="text-center">Total<br><small>(Comprobantes <br>a PSE)</small></th>
                            <th v-if="columns.total_notas.visible" class="text-center">Total<br><small>(Comprobantes <br>notas de venta)</small></th>
                            <th v-if="columns.limitar_doc.visible" class="text-end">Limitar Doc.</th>
                            <th v-if="columns.limitar_usuarios.visible" class="text-center">Limitar <br>Usuarios</th>
                            <th v-if="columns.limitar_sucursales.visible" class="text-center">Limitar <br>Sucursales</th>
                            <th v-if="columns.limitar_ventas.visible" class="text-center">
                                <el-tooltip class="item"
                                    content="Límite de ventas mensual asociado al ciclo de facturación"
                                    effect="dark"
                                    placement="top">
                                    <label>Limitar <br>Ventas (Mes)</label>
                                </el-tooltip>
                            </th>
                            <th class="text-end">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in filteredRecords" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td class="sticky-column">
                                <!-- {{ row.hostname }} -->
                                <a :href="`http://${row.hostname}`"
                                   style="color:black"
                                   target="_blank">{{ row.hostname }}</a>
                            </td>
                            <td v-if="columns.bloquear_cuenta.visible" class="text-center">
                                <template v-if="!row.locked">
                                    <el-switch
                                        v-model="row.locked_tenant"
                                        style="display: block"
                                        @change="changeLockedTenant(row)"
                                    ></el-switch>
                                </template>
                            </td>
                            <td v-if="columns.nombre.visible" class="column-name table-cell">
                                {{ row.name }}
                                <span v-if="row.id === mainWebClientId" class="main-web-badge" title="Web principal del sistema">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                                    Web principal
                                </span>
                            </td>
                            <td v-if="columns.ruc.visible">{{ row.number }}</td>
                            <td v-if="columns.plan.visible">{{ row.plan }}</td>
                            <td v-if="columns.correo.visible">{{ row.email }}</td>
                            <td v-if="columns.entorno.visible">
                                <span v-if="row.soap_type == '01'"
                                      class="badge badge-default">Demo</span>
                                <span v-if="row.soap_type == '02'"
                                      class="badge badge-success">Producción</span>
                                <span v-if="row.soap_type == '03'"
                                      class="badge badge-info">Interno</span>
                            </td>
                            <td v-if="columns.total_comprobantes.visible" class="text-center">
                                <label>
                                    <strong>{{ row.count_doc }}</strong>
                                </label>
                            </td>

                            <td v-if="columns.notificaciones.visible" class="d-flex justify-content-center">
                                <template v-if="row.document_not_sent > 0 || row.document_to_be_regularized > 0 || row.document_to_be_canceled > 0">
                                    <el-tooltip
                                        class="item"
                                        content="Comprobantes enviados / por enviar"
                                        effect="dark"
                                        placement="top-start">
                                        <el-badge
                                            v-if="row.document_not_sent > 0"
                                            :value="row.document_not_sent"
                                            class="item mx-2"
                                            :type="row.document_not_sent == 0 ? 'primary' : 'danger'">
                                            <i class="far fa-bell text-secondary"></i>
                                        </el-badge>
                                    </el-tooltip>

                                    <el-tooltip
                                        class="item"
                                        content="Comprobantes pendientes de rectificación"
                                        effect="dark"
                                        placement="top-start">
                                        <el-badge
                                            v-if="row.document_to_be_regularized > 0"
                                            :value="row.document_regularize_shipping"
                                            class="item mx-2"
                                            :type="row.document_regularize_shipping == 0 ? 'primary' : 'danger'">
                                            <i class="fas fa-exclamation-triangle text-secondary"></i>
                                        </el-badge>
                                    </el-tooltip>

                                    <el-tooltip
                                        class="item"
                                        content="Comprobantes por anular"
                                        effect="dark"
                                        placement="top-start">
                                        <el-badge
                                            v-if="row.document_to_be_canceled > 0"
                                            :value="row.document_to_be_canceled"
                                            class="item mx-2"
                                            :type="row.document_to_be_canceled == 0 ? 'primary' : 'danger'">
                                            <i class="fas fa-exclamation-circle text-secondary"></i>
                                        </el-badge>
                                    </el-tooltip>

                                </template>

                                <template v-else>
                                    <span class="text-muted small d-flex align-items-center">
                                        Todo OK
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check ms-1 text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                                    </span>
                                </template>
                            </td>

                            <td v-if="columns.inicio_ciclo.visible" class="text-center">
                                <template v-if="row.start_billing_cycle">
                                    <span></span>
                                    <span>{{ row.start_billing_cycle }}</span>
                                </template>
                                <template v-else>
                                    <el-date-picker
                                        v-model="row.select_date_billing"
                                        placeholder="..."
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        @change="setStartBillingCycle($event, row.id)"
                                    ></el-date-picker>
                                </template>
                            </td>

                            <td v-if="columns.comprobantes_ciclo.visible" class="text-center">
                                <strong>
                                    <template v-if="row.sale_notes_quantity_if_include > 0">
                                        {{ row.count_doc_month ? (row.count_doc_month + row.sale_notes_quantity_if_include) : 0 }} /
                                    </template>
                                    <template v-else>
                                    {{ row.count_doc_month ? row.count_doc_month : 0 }} /
                                    </template>

                                    <template v-if="row.max_documents == 0">
                                        <i class="fas fa-infinity"></i>
                                    </template>
                                    <template v-else>
                                        <strong>{{ row.max_documents }}</strong>
                                    </template>
                                </strong>
                            </td>

                            <td v-if="columns.usuarios.visible" class="text-center">
                                <template v-if="row.max_users !== 0 && row.count_user > row.max_users">
                                    <el-popover
                                        :content="text_limit_users"
                                        placement="top-start"
                                        trigger="hover"
                                        width="220"
                                    >
                                        <label slot="reference"
                                               class="text-danger">
                                            <strong>{{ row.count_user }}</strong>
                                        </label>
                                    </el-popover>
                                </template>
                                <template v-else>
                                    <label>
                                        <strong>{{ row.count_user }}</strong>
                                    </label>
                                </template>
                                /
                                <template v-if="row.max_users == 0">
                                    <i class="fas fa-infinity"></i>
                                </template>
                                <template v-else>
                                    <strong>{{ row.max_users }}</strong>
                                </template>
                            </td>

                            <td v-if="columns.sucursales.visible" class="text-center">

                                <data-limit-notification
                                    entity_description="sucursales"
                                    :unlimited="row.establishments_unlimited"
                                    :quantity="row.quantity_establishments"
                                    :max_quantity="row.max_quantity_establishments"
                                    >
                                </data-limit-notification>

                            </td>

                            <td v-if="columns.ventas_mes.visible" class="text-center">

                                <data-limit-notification
                                    entity_description="ventas"
                                    style_div="width: 150px !important"
                                    :unlimited="row.sales_unlimited"
                                    :quantity="row.monthly_sales_total"
                                    :max_quantity="row.max_sales_limit"
                                    >
                                </data-limit-notification>

                            </td>

                            <td v-if="columns.mensajes_whatsapp.visible" class="text-center">

                                <data-limit-notification
                                    entity_description="mensajes de WhatsApp"
                                    :unlimited="row.whatsapp_messages_unlimited"
                                    :quantity="row.count_whatsapp_month"
                                    :max_quantity="row.max_whatsapp_messages"
                                    >
                                </data-limit-notification>

                            </td>

                            <td v-if="columns.fecha_creacion.visible" class="text-center">{{ row.created_at }}</td>
                            <td v-if="columns.consultas_api.visible">{{ row.queries_to_apiperu }}</td>

                            <td v-if="columns.notas_venta.visible" class="text-center"><strong>{{ row.count_sales_notes }}</strong></td>
                            <td v-if="columns.total_mes.visible" class="text-center"><strong>{{ row.current_count_doc_month }}</strong></td>
                            <td v-if="columns.total_pse.visible" class="text-center"><strong>{{ row.count_doc_pse }}</strong></td>
                            <td v-if="columns.total_notas.visible" class="text-center"><strong>{{ row.count_doc_month + row.count_sales_notes_month }}</strong></td>

                            <td v-if="columns.limitar_doc.visible" class="text-center">
                                <el-switch
                                    v-model="row.locked_emission"
                                    style="display: block"
                                    @change="changeLockedEmission(row)"
                                ></el-switch>
                            </td>

                            <td v-if="columns.limitar_usuarios.visible" class="text-center">
                                <el-switch
                                    v-model="row.locked_users"
                                    style="display: block"
                                    @change="changeLockedUser(row)"
                                ></el-switch>
                            </td>

                            <td v-if="columns.limitar_sucursales.visible" class="text-center">
                                <el-switch
                                    v-model="row.locked_create_establishments"
                                    style="display: block"
                                    @change="changeLockedByColumn(row, 'locked_create_establishments')"
                                ></el-switch>
                            </td>

                            <td v-if="columns.limitar_ventas.visible" class="text-center">
                                <el-switch
                                    v-model="row.restrict_sales_limit"
                                    style="display: block"
                                    @change="changeLockedByColumn(row, 'restrict_sales_limit')"
                                ></el-switch>
                            </td>

                            <td class="text-end">
                                <el-dropdown trigger="click" @command="handleCommand" placement="bottom-end">
                                    <el-button type="text" size="small" class="dropdown-trigger">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item :command="{action: 'edit', id: row.id}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                            Editar
                                        </el-dropdown-item>

                                        <el-dropdown-item divided></el-dropdown-item>

                                        <el-dropdown-item :command="{action: 'secretLogin', id: row.id}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-dual-screen me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4l8 3v15l-8 -3z" /><path d="M13 19h6v-15h-14" /></svg>
                                            Acceso Maestro
                                        </el-dropdown-item>

                                        <el-dropdown-item :command="{action: 'mainWeb', id: row.id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-star me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                                            {{ row.id === mainWebClientId ? 'Quitar web principal' : 'Marcar como web principal' }}
                                        </el-dropdown-item>

                                        <el-dropdown-item
                                            v-if="row.soap_type=='01'"
                                            :command="{action: 'demoConfig', id: row.id}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-settings me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                                            Configurar Demo
                                        </el-dropdown-item>

                                        <el-dropdown-item :command="{action: 'payments', id: row.id}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-cash-banknote me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M3 8a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M18 12h.01" /><path d="M6 12h.01" /></svg>
                                            Pagos
                                        </el-dropdown-item>

                                        <el-dropdown-item :command="{action: 'accountStatus', id: row.id}">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-line me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l16 0" /><path d="M4 15l4 -6l4 2l4 -5l4 4" /></svg>
                                            Estado de cuenta
                                        </el-dropdown-item>

                                        <template v-if="!row.locked">
                                            <el-dropdown-item :command="{action: 'password', id: row.id}">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-key me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-.175 .008h-1.172a1 1 0 0 1 -.993 -.883l-.007 -.117v-1.172a2 2 0 0 1 .467 -1.284l.119 -.13l.414 -.414h2v-2h2v-2l2.144 -2.144l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0z" /><path d="M15 9h.01" /></svg>
                                                Restablecer contraseña
                                            </el-dropdown-item>
                                            <el-dropdown-item v-if="deletePermission == true" divided></el-dropdown-item>
                                            <el-dropdown-item
                                                class="text-danger option-delete"
                                                v-if="deletePermission == true"
                                                :command="{action: 'delete', row: row}">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                Eliminar Cliente
                                            </el-dropdown-item>
                                        </template>

                                    </el-dropdown-menu>
                                </el-dropdown>
                            </td>
                            <!-- <td class="text-right">
                                <button
                                    class="btn waves-effect waves-light btn-xs btn-warning m-1__2"
                                    type="button"
                                    @click.prevent="clickPayments(row.id)"
                                >Pagos
                                </button>
                            </td>
                            <td class="text-right">
                                <button
                                    class="btn waves-effect waves-light btn-xs btn-primary m-1__2"
                                    type="button"
                                    @click.prevent="clickAccountStatus(row.id)"
                                >E. Cuenta
                                </button>
                            </td>
                            <td class="text-right">
                                <button
                                    class="btn waves-effect waves-light btn-xs btn-primary m-1__2"
                                    type="button"
                                    @click.prevent="clickEdit(row.id)"
                                >Editar
                                </button>
                            </td> -->

                        </tr>

                        <!-- Fila cuando no hay resultados -->
                        <tr v-if="filteredRecords.length === 0 && records.length > 0">
                            <td colspan="20" class="text-center py-4">
                                <div class="text-muted">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="30"  height="30" style="opacity: 0.5;"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    <p class="mb-0">No se encontraron clientes que coincidan con los filtros aplicados.</p>
                                    <small>
                                        <template v-if="searchQuery">
                                            Búsqueda: "{{ searchQuery }}"
                                        </template>
                                        <template v-if="filters.entorno">
                                            | Entorno: {{ getEntornoLabel(filters.entorno) }}
                                        </template>
                                        <template v-if="filters.plan">
                                            | Plan: {{ filters.plan }}
                                        </template>
                                        <template v-if="filters.bloqueo !== ''">
                                            | Estado: {{ filters.bloqueo === '1' ? 'Bloqueados' : 'Activos' }}
                                        </template>
                                    </small>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <system-clients-form :recordId="recordId"
                             :showDialog.sync="showDialog"></system-clients-form>

        <!--<system-clients-form-edit :showDialog.sync="showDialogEdit"
        :recordId="recordId"></system-clients-form-edit>-->

        <client-payments :clientId="recordId"
                         :showDialog.sync="showDialogPayments"></client-payments>

        <account-status :clientId="recordId"
                        :showDialog.sync="showDialogAccountStatus"></account-status>

        <client-delete :record="record"
                        :showDialog.sync="showDialogDelete"></client-delete>

        <demo-configuration :clientId="recordId"
                        :showDialog.sync="showDemoConfiguration"></demo-configuration>
    </div>
</template>
<style>
.table th,
.table td {
    white-space: nowrap;
}
.main-web-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-left: 6px;
    padding: 1px 8px;
    border-radius: 20px;
    background: #eafaf4;
    color: #16a085;
    font-size: 11px;
    font-weight: 600;
    vertical-align: middle;
}
.main-web-badge svg { color: #f1c40f; }
th.sticky-column,
td.sticky-column {
    position: sticky;
    left: 0;
    background-color: #fff !important;
    z-index: 1;
}
th.sticky-column.scroll-active::after,
td.sticky-column.scroll-active::after {
  opacity: 1;
  transition: opacity 0.2s ease;
}
th.sticky-column::after,
td.sticky-column::after {
    content: "";
    position: absolute;
    top: 0;
    right: -6px;
    width: 6px;
    height: 100%;
    background: linear-gradient(to right, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0));
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}
th.sticky-column {
    z-index: 2;
}
.dropdown-trigger {
    color: #6c757d;
    border: none !important;
    background: none !important;
    box-shadow: none !important;
    padding: 4px 8px;
}
.dropdown-trigger:hover {
    color: #495057;
    background-color: #f8f9fa !important;
}
.shadow-right {
    right: 0px;
}
.shadow-left {
    left: 0px;
    z-index: 3;
}
.page-header {
    position: relative;
}
.clients-columns-dropdown-menu {
    max-height: 80vh;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
}

.clients-columns-dropdown-menu::-webkit-scrollbar {
    width: 6px;
}
.page-header-actions {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
}
@media (max-width: 768px) {
    .page-header-actions {
        position: static;
        transform: none;
        margin-top: 1rem;
    }
}

/* ── shadcn KPI Cards ── */
.shad-kpi {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px 14px;
    height: 100%;
    transition: box-shadow .15s;
}
.shad-kpi:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.07); }
.shad-kpi-danger { border-left: 3px solid #ef4444; }
.shad-kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.shad-kpi-label { font-size: 12px; font-weight: 500; color: #64748b; }
.shad-kpi-icon { color: #94a3b8; flex-shrink: 0; }
.shad-kpi-value { font-size: 26px; font-weight: 700; color: #0f172a; line-height: 1; margin-bottom: 4px; }
.shad-kpi-desc { font-size: 11px; color: #94a3b8; }
.shad-kpi-green { color: #16a34a; font-weight: 600; }
.shad-kpi-red { color: #ef4444; font-weight: 600; }

/* ── Donut Charts ── */
.donut-card-title { font-size: 13px; font-weight: 600; color: #0f172a; margin-bottom: 12px; }
.donut-wrap { display: flex; align-items: center; gap: 16px; }
.donut-svg-wrap { position: relative; flex-shrink: 0; }
.donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
}
.donut-center-num { font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1; }
.donut-center-lbl { font-size: 9px; color: #94a3b8; margin-top: 2px; text-transform: uppercase; letter-spacing: .04em; }
.donut-legend { flex: 1; min-width: 0; }
.donut-legend-row { display: flex; align-items: center; gap: 6px; margin-bottom: 7px; }
.donut-legend-row:last-child { margin-bottom: 0; }
.donut-legend-dot { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }
.donut-legend-name { font-size: 11.5px; color: #475569; flex: 1; min-width: 0; }
.donut-legend-count { font-size: 11.5px; font-weight: 700; }
.donut-legend-pct { font-size: 10.5px; color: #94a3b8; min-width: 34px; text-align: right; }

/* env-dot kept for backwards compat */
.env-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Sistema cards */
.sys-card {
    background: #fff;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
}
.sys-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
}
.sys-data { flex: 1; min-width: 0; }
.sys-value { font-size: 17px; font-weight: 700; color: #2d2d4a; line-height: 1; }
.sys-label { font-size: 10.5px; color: #999; margin-top: 2px; }
.sys-action { font-size: 10px; color: #667eea; text-decoration: none; margin-top: 2px; display: block; }
.sys-action:hover { text-decoration: underline; }
.sys-ring { position: absolute; right: 10px; opacity: .5; }

/* Onboarding */
.onb-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.18);
}
.onb-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 18px 16px 14px;
}
.onb-header-top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.onb-header-icon {
    width: 38px;
    height: 38px;
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    flex-shrink: 0;
}
.onb-title {
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    line-height: 1.2;
}
.onb-subtitle {
    color: rgba(255,255,255,0.75);
    font-size: 11px;
    margin-top: 2px;
}
.onb-progress-wrap {
    background: rgba(255,255,255,0.12);
    border-radius: 8px;
    padding: 8px 10px;
}
.onb-progress-labels {
    display: flex;
    justify-content: space-between;
    color: rgba(255,255,255,0.8);
    font-size: 11px;
    margin-bottom: 5px;
}
.onb-progress-count {
    color: #fff;
    font-weight: 600;
}
.onb-progress-bar {
    background: rgba(255,255,255,0.25);
    border-radius: 10px;
    height: 5px;
}
.onb-progress-fill {
    background: #fff;
    height: 100%;
    border-radius: 10px;
    transition: width 0.4s ease;
}
.onb-body {
    padding: 16px 14px 12px;
    background: #fff;
}
.onb-step {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.onb-step-last {
    margin-bottom: 0;
}
.onb-step-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}
.onb-step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.3s;
}
.onb-pending {
    background: #f0f0f8;
    color: #9b9bc4;
    border: 2px solid #e0e0f0;
}
.onb-active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: 2px solid transparent;
    box-shadow: 0 3px 10px rgba(102,126,234,0.4);
}
.onb-done {
    background: #e6f9f0;
    color: #2ecc71;
    border: 2px solid #b8f0d8;
}
.onb-step-line {
    width: 2px;
    height: 28px;
    background: linear-gradient(to bottom, #e0e0f0, #ebebf8);
    margin: 3px 0;
    border-radius: 2px;
}
.onb-step-content {
    padding-bottom: 12px;
    padding-top: 4px;
}
.onb-step-last .onb-step-content {
    padding-bottom: 0;
}
.onb-step-title {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: #3d3d6e;
    line-height: 1.3;
}
.onb-step-desc {
    display: block;
    font-size: 10.5px;
    color: #9999bb;
    margin-top: 2px;
    line-height: 1.4;
}
</style>
<script>
// import CompaniesForm from "./form.vue";
//   import CompaniesFormEdit from './form_edit.vue'
import {deletable} from "../../../mixins/deletable";
import {changeable} from "../../../mixins/changeable";
import ChartLine from "./charts/Line.vue";
import ClientPayments from "./partials/payments.vue";
import DemoConfiguration from "./partials/demo_configuration.vue";
import AccountStatus from "./partials/account_status.vue";
import ClientDelete from "./partials/delete.vue";
import DataLimitNotification from "./partials/DataLimitNotification.vue";

export default {
    mixins: [
        deletable,
        changeable
    ],
    props: [
        'deletePermission',
        'discUsed',
        'iUsed',
        'storageSize',
        'version',
        'canCreateClients',
    ],
    components: {
        // CompaniesForm,
        ChartLine,
        ClientPayments,
        AccountStatus,
        ClientDelete,
        DataLimitNotification,
        DemoConfiguration
    },
    data() {
        return {
            searchQuery: "",
            selectBillingDate: "",
            showDialogEdit: false,
            showDialog: false,
            showDialogPayments: false,
            showDialogAccountStatus: false,
            resource: "clients",
            recordId: null,
            records: [],
            mainWebClientId: null,
            isLoading: true,
            checkLimitUsers: true,
            text_limit_doc: null,
            text_limit_users: null,
            loaded: false,
            chartDataLoaded: false,
            year: moment().format('YYYY'),
            total_documents: 0,
            filters: {
                entorno: "",
                plan: "",
                bloqueo: ""
            },
            dataChartLine: {
                labels: [],
                datasets: [
                    {
                        // label: 'Data One',
                        // backgroundColor: '#f87979',
                        data: []
                    }
                ]
            },
            showDialogDelete: false,
            record: {},
            showDemoConfiguration: false,
            planColors: ['#667eea','#11c26d','#ffa726','#26c6da','#ff7043'],
            showLeftShadow: false,
            showRightShadow: false,
            isFiltersVisible: false,
            columns: {
                nombre: {
                    title: 'Nombre',
                    visible: true
                },
                ruc: {
                    title: 'RUC',
                    visible: true
                },
                plan: {
                    title: 'Plan',
                    visible: true
                },
                correo: {
                    title: 'Correo',
                    visible: true
                },
                entorno: {
                    title: 'Entorno',
                    visible: true
                },
                total_comprobantes: {
                    title: 'Total de Comprobantes',
                    visible: false
                },
                notificaciones: {
                    title: 'Notificaciones',
                    visible: true
                },
                inicio_ciclo: {
                    title: 'Inicio Ciclo Facturación',
                    visible: false
                },
                comprobantes_ciclo: {
                    title: 'Comprobantes Ciclo Facturación',
                    visible: true
                },
                usuarios: {
                    title: 'Usuarios',
                    visible: true
                },
                sucursales: {
                    title: 'Sucursales',
                    visible: false
                },
                ventas_mes: {
                    title: 'Ventas (Mes)',
                    visible: false
                },
                mensajes_whatsapp: {
                    title: 'Mensajes WhatsApp',
                    visible: true
                },
                fecha_creacion: {
                    title: 'F.Creación',
                    visible: true
                },
                consultas_api: {
                    title: 'Consultas API Peru (mes)',
                    visible: false
                },
                notas_venta: {
                    title: 'Cant.Notas de venta',
                    visible: false
                },
                total_mes: {
                    title: 'Total (Comprobantes por mes)',
                    visible: false
                },
                total_pse: {
                    title: 'Total (Comprobantes a PSE)',
                    visible: false
                },
                total_notas: {
                    title: 'Total (Comprobantes notas de venta)',
                    visible: false
                },
                bloquear_cuenta: {
                    title: 'Bloquear cuenta',
                    visible: true
                },
                limitar_doc: {
                    title: 'Limitar Doc.',
                    visible: false
                },
                limitar_usuarios: {
                    title: 'Limitar Usuarios',
                    visible: false
                },
                limitar_sucursales: {
                    title: 'Limitar Sucursales',
                    visible: false
                },
                limitar_ventas: {
                    title: 'Limitar Ventas (Mes)',
                    visible: false
                }
            }
        };
    },
    async mounted() {
        this.loaded = false;
        this.chartDataLoaded = false;

        try {
            const response = await this.$http.get(`/${this.resource}/charts`);
            let line = response.data.line;

            if (line && line.labels && line.data) {
                this.dataChartLine.labels = line.labels;
                this.dataChartLine.datasets[0].data = line.data;
                this.total_documents = response.data.total_documents || 0;

                this.$nextTick(() => {
                    this.chartDataLoaded = true;
                });
            } else {
                // Si no hay datos, inicializar con valores vacíos
                this.dataChartLine.labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];
                this.dataChartLine.datasets[0].data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                this.total_documents = 0;
                this.chartDataLoaded = true;
            }
        } catch (error) {
            console.error('Error loading chart data:', error);
            this.dataChartLine.labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];
            this.dataChartLine.datasets[0].data = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            this.total_documents = 0;
            this.chartDataLoaded = true;
        }

        this.loaded = true;
        this.initScrollShadow();
        this.$nextTick(() => {
            const el = this.$refs.scrollContainer;
            if (el) {
                el.addEventListener('scroll', this.checkScrollShadows);
                this.checkScrollShadows();
            }
        });
    },
    created() {
        this.$eventHub.$on("reloadData", () => {
            this.getData();
        });
        this.getData();
        this.getMainWeb();
        this.checkLimit();

        this.text_limit_doc = "El límite de comprobantes fue superado";
        this.text_limit_users = "El límite de usuarios fue superado";
        this.loadColumnVisibility();
    },
    computed: {
        kpiActivos() { return this.records.filter(r => !r.locked_tenant).length; },
        kpiBloqueados() { return this.records.filter(r => r.locked_tenant).length; },
        kpiProduccion() { return this.records.filter(r => r.soap_type == '02').length; },
        kpiDemo() { return this.records.filter(r => r.soap_type == '01').length; },
        kpiInterno() { return this.records.filter(r => r.soap_type == '03').length; },
        kpiPctProduccion() {
            if (!this.records.length) return 0;
            return Math.round(this.kpiProduccion / this.records.length * 100);
        },
        kpiDocsMes() {
            return this.records.reduce((s, r) => s + (r.current_count_doc_month || 0), 0);
        },
        kpiAlertas() {
            return this.records.filter(r => r.document_not_sent > 0 || r.document_to_be_regularized > 0 || r.document_to_be_canceled > 0).length;
        },
        topPlanes() {
            const counts = {};
            this.records.forEach(r => {
                const p = r.plan || 'Sin plan';
                counts[p] = (counts[p] || 0) + 1;
            });
            return Object.entries(counts)
                .map(([plan, count]) => ({ plan, count }))
                .sort((a, b) => b.count - a.count)
                .slice(0, 5);
        },
        envDonutSegments() {
            const total = this.records.length;
            if (!total) return [];
            const segs = [
                { label: 'Producción', count: this.kpiProduccion, color: '#3b82f6' },
                { label: 'Demo',       count: this.kpiDemo,       color: '#f59e0b' },
                { label: 'Interno',    count: this.kpiInterno,    color: '#06b6d4' },
            ];
            let acc = 0;
            return segs.filter(s => s.count > 0).map(s => {
                const pct = parseFloat(((s.count / total) * 100).toFixed(2));
                const offset = -(acc);
                acc += pct;
                return { ...s, pct, offset };
            });
        },
        planDonutSegments() {
            const total = this.records.length;
            if (!total || !this.topPlanes.length) return [];
            let acc = 0;
            return this.topPlanes.map((item, idx) => {
                const pct = parseFloat(((item.count / total) * 100).toFixed(2));
                const offset = -(acc);
                acc += pct;
                return { ...item, pct, offset, color: this.planColors[idx % this.planColors.length] };
            });
        },
        filteredRecords() {
            let filtered = this.records;

            // Filtro por búsqueda de texto
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter((row) => {
                    return (
                        (row.name && row.name.toLowerCase().includes(query)) ||
                        (row.hostname && row.hostname.toLowerCase().includes(query)) ||
                        (row.email && row.email.toLowerCase().includes(query)) ||
                        (row.number && row.number.toLowerCase().includes(query))
                    );
                });
            }

            // Filtro por entorno
            if (this.filters.entorno) {
                filtered = filtered.filter((row) => {
                    return row.soap_type === this.filters.entorno;
                });
            }

            // Filtro por plan
            if (this.filters.plan) {
                filtered = filtered.filter((row) => {
                    return row.plan === this.filters.plan;
                });
            }

            // Filtro por estado de bloqueo
            if (this.filters.bloqueo !== "") {
                filtered = filtered.filter((row) => {
                    const isBlocked = Boolean(row.locked_tenant);

                    if (this.filters.bloqueo === "1") {
                        return isBlocked; // Bloqueados
                    } else if (this.filters.bloqueo === "0") {
                        return !isBlocked; // Activos
                    }
                    return true;
                });
            }

            return filtered;
        },
        availablePlans() {
            const plans = [...new Set(this.records.map(record => record.plan).filter(plan => plan))];
            return plans.sort();
        },
        columnsComputed: function () {
            return this.columns;
        },
        hasActiveFilters() {
            return !!(
                this.searchQuery ||
                this.filters.entorno ||
                this.filters.plan ||
                this.filters.bloqueo !== ""
            );
        }
    },
    methods: {
        formatNum(n) {
            return (n || 0).toLocaleString('es-PE');
        },
        checkLimit() {
            this.isLoading = true;
            this.$http.get('clients/confirm-limit-reseller')
            .then(response => {
                if (response.data.success) {
                    this.checkLimitUsers = true;
                } else {
                    this.checkLimitUsers = false;
                }
            })
            .finally(() => {
                this.isLoading = false;
            })
            .catch(error => {
                console.error('Error al verificar el límite:', error);
                this.checkLimitUsers = true;
            });
        },
        saveColumnVisibility() {
            localStorage.setItem('columnVisibilityClients', JSON.stringify(this.columns));
        },
        loadColumnVisibility() {
            const savedColumns = localStorage.getItem('columnVisibilityClients');
            if (savedColumns) {
                const parsed = JSON.parse(savedColumns);
                Object.keys(this.columns).forEach((key) => {
                    if (parsed[key] && typeof parsed[key].visible === 'boolean') {
                        this.columns[key].visible = parsed[key].visible;
                    }
                });
            }
        },
        applyFilters() {
            console.log('Filtros aplicados:', this.filters);

            this.$nextTick(() => {
                const totalRecords = this.records.length;
                const filteredCount = this.filteredRecords.length;

                if (this.filters.bloqueo !== "" && filteredCount === 0) {
                    const filterType = this.filters.bloqueo === "1" ? "bloqueados" : "activos";
                    console.log(`No se encontraron clientes ${filterType} de un total de ${totalRecords} registros`);
                }
            });
        },
        clearFilters() {
            this.filters.entorno = "";
            this.filters.plan = "";
            this.filters.bloqueo = "";
            this.searchQuery = "";
        },
        toggleFilters() {
            this.isFiltersVisible = !this.isFiltersVisible;
        },
        getEntornoLabel(value) {
            const entornos = {
                '01': 'Demo',
                '02': 'Producción',
                '03': 'Interno'
            };
            return entornos[value] || value;
        },
        checkScrollShadows() {
            const el = this.$refs.scrollContainer;
            if (!el) return;

            const scrollLeft = el.scrollLeft;
            const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;

            this.showLeftShadow = scrollLeft > 1;
            this.showRightShadow = scrollRight > 1;
        },
        handleCommand(command) {
            switch (command.action) {
                case 'password':
                    this.clickPassword(command.id);
                    break;
                case 'delete':
                    this.clickDelete(command.row);
                    break;
                case 'edit':
                    this.clickEdit(command.id);
                    break;
                case 'demoConfig':
                    this.clickDemoConfiguration(command.id);
                    break;
                case 'secretLogin':
                    this.clickSecretLogin(command.id);
                    break;
                case 'payments':
                    this.clickPayments(command.id);
                    break;
                case 'accountStatus':
                    this.clickAccountStatus(command.id);
                    break;
                case 'mainWeb':
                    this.setMainWeb(command.id);
                    break;
            }
        },
        getMainWeb() {
            this.$http.get(`/${this.resource}/main-web`).then(response => {
                this.mainWebClientId = response.data.main_web_client_id;
            }).catch(() => {});
        },
        setMainWeb(id) {
            this.$http.post(`/${this.resource}/set-main-web`, { client_id: id }).then(response => {
                if (response.data.success) {
                    this.mainWebClientId = response.data.main_web_client_id;
                    this.$message.success(response.data.message);
                } else {
                    this.$message.error(response.data.message || 'No se pudo actualizar la web principal.');
                }
            }).catch(error => {
                const msg = (error.response && error.response.data && error.response.data.message)
                    ? error.response.data.message
                    : 'No se pudo actualizar la web principal.';
                this.$message.error(msg);
            });
        },
        initScrollShadow() {
          this.$nextTick(() => {
            const container = this.$refs.scrollContainer;
            if (!container) return;

            container.addEventListener('scroll', () => {
              const isScrolled = container.scrollLeft > 0;

              const thSticky = container.querySelector('th.sticky-column');
              const tdStickies = container.querySelectorAll('tbody td.sticky-column');

              if (thSticky) {
                if (isScrolled) thSticky.classList.add('scroll-active');
                else thSticky.classList.remove('scroll-active');
              }

              tdStickies.forEach(td => {
                if (isScrolled) td.classList.add('scroll-active');
                else td.classList.remove('scroll-active');
              });
            });

            container.dispatchEvent(new Event('scroll'));
          });
        },
        changeLockedTenant(row) {
            this.$http
                .post(`${this.resource}/locked_tenant`, row)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 500) {
                        this.$message.error(error.response.data.message);
                    } else {
                        console.log(error.response);
                    }
                })
                .then(() => {
                });
        },

        changeLockedUser(row) {
            this.$http
                .post(`${this.resource}/locked_user`, row)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 500) {
                        this.$message.error(error.response.data.message);
                    } else {
                        console.log(error.response);
                    }
                })
                .then(() => {
                });
        },
        changeLockedByColumn(row, column)
        {
            const params = { ...row }
            params.column = column

            this.$http
                .post(`${this.resource}/locked-by-column`, params)
                .then(response => {

                    if (response.data.success)
                    {
                        this.$message.success(response.data.message)
                        this.$eventHub.$emit("reloadData")
                    }
                    else
                    {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 500)
                    {
                        this.$message.error(error.response.data.message)
                    }
                    else
                    {
                        console.log(error.response)
                    }
                })
                .then(() => {
                })
        },
        setStartBillingCycle(event, id) {
            this.$http
                .post(`${this.resource}/set_billing_cycle`, {
                    id: id,
                    start_billing_cycle: event
                })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 500) {
                        this.$message.error(error.response.data.message);
                    } else {
                        console.log(error.response);
                    }
                })
                .then(() => {
                    this.$eventHub.$emit("reloadData");
                });
        },
        changeLockedEmission(row) {
            this.$http
                .post(`${this.resource}/locked_emission`, row)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 500) {
                        this.$message.error(error.response.data.message);
                    } else {
                        console.log(error.response);
                    }
                })
                .then(() => {
                });
        },
        getData() {
            this.$http.get(`/${this.resource}/records`).then(response => {
                this.records = response.data.data;
            });
        },
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickPayments(recordId = null) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        clickAccountStatus(recordId = null) {
            this.recordId = recordId;
            this.showDialogAccountStatus = true;
        },
        clickSecretLogin(clientId) {
            // Crear un formulario dinámico para enviar la solicitud POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/secret-login`;
            form.target = '_blank';
            form.style.display = 'none';

            // Agregar el token CSRF
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Agregar el ID del cliente
            const clientIdInput = document.createElement('input');
            clientIdInput.type = 'hidden';
            clientIdInput.name = 'client_id';
            clientIdInput.value = clientId;
            form.appendChild(clientIdInput);

            // Agregar el formulario al documento y enviarlo
            document.body.appendChild(form);
            form.submit();
        },

        clickPassword(id) {
            this.change(`/${this.resource}/password/${id}`);
        },
        clickDelete(record) {

            this.record = record
            this.showDialogDelete = true
            // this.destroy(`/${this.resource}/${id}`).then(() =>
            //     this.$eventHub.$emit("reloadData")
            // );
        },
        clickEdit(recordId) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickDemoConfiguration(recordId = null) {
            this.recordId = recordId;
            this.showDemoConfiguration = true;
        },
    }
};
</script>
