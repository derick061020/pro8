<template>
    <div class="dashboard" v-if="typeUser == 'admin'">
        <header
        class="page-header"
        style="display: flex; justify-content: space-between; align-items: center"
        >
            <div>
                <h2>Dashboard</h2>
            </div>
            <div class="d-flex align-items-center h-100">
              {{ filterLabel }}
              <el-button
                type="primary"
                size="small"
                class="mx-2 p-2 btn-dashboard-filter"
                style="padding: 8px !important;"
                @click="toggleFilters"
                :title="showFilters ? 'Ocultar filtros' : 'Mostrar filtros'"
              >
                <svg v-if="showFilters" xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"
                  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-alt">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8h4v4h-4z" />
                  <path d="M6 4l0 4" /><path d="M6 12l0 8" />
                  <path d="M10 14h4v4h-4z" /><path d="M12 4l0 10" />
                  <path d="M12 18l0 2" /><path d="M16 5h4v4h-4z" />
                  <path d="M18 4l0 1" /><path d="M18 9l0 11" />
                </svg>

                <svg v-else xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"
                  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-off">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                  <path d="M6 6v2" /><path d="M6 12v8" /><path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                  <path d="M12 4v4m0 4v2" /><path d="M12 18v2" />
                  <path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M18 4v1" />
                  <path d="M18 9v5m0 4v2" /><path d="M3 3l18 18" />
                </svg>
              </el-button>
            </div>
        </header>

        <hr v-if="showWelcomePanel">

        <new-dashboard v-if="!showLegacyDashboard"></new-dashboard>

        <div v-if="showLegacyDashboard" class="card mb-0 row-new bg-transparent dashboard-cards mt-0">
            <div class="row px-2" v-show="showFilters">
                <div class="col-12">
                    <section class="card card-dashboard">
                        <div class="card-body pt-2 pb-0">
                            <div class="row border-bottom mb-2 no-gutters">
                                <small class="col-12 text-center txt-dark">Filtrar datos históricos</small>
                            </div>
                            <div class="row">
                                <div :class="filterColumnClass" class="form-group">
                                    <label class="control-label">Sucursal</label>
                                    <el-select v-model="form.establishment_id" @change="loadAll">
                                        <el-option
                                        v-for="option in establishments"
                                        :key="option.id"
                                        :value="option.id"
                                        :label="option.name"
                                        ></el-option>
                                    </el-select>
                                </div>
                                <div :class="filterColumnClass" class="form-period form-group">
                                    <label class="control-label">Periodo</label>
                                    <el-select v-model="form.period" @change="changePeriod">
                                        <el-option key="all" value="all" label="Todos"></el-option>
                                        <el-option key="last_week" value="last_week" label="Última semana"></el-option>
                                        <el-option key="month" value="month" label="Por mes"></el-option>
                                        <el-option key="between_months" value="between_months" label="Entre meses"></el-option>
                                        <el-option key="date" value="date" label="Por fecha"></el-option>
                                        <el-option key="between_dates" value="between_dates" label="Entre fechas"></el-option>
                                    </el-select>
                                </div>
                                <template v-if="form.period === 'month' || form.period === 'between_months'">
                                    <div :class="filterColumnClass" class="form-group">
                                        <label class="control-label">Mes de</label>
                                        <el-date-picker
                                            v-model="form.month_start"
                                            type="month"
                                            @change="changeDisabledMonths"
                                            value-format="yyyy-MM"
                                            format="MM/yyyy"
                                            :clearable="false"
                                        ></el-date-picker>
                                    </div>
                                </template>
                                <template v-if="form.period === 'between_months'">
                                    <div :class="filterColumnClass" class="form-group">
                                        <label class="control-label">Mes al</label>
                                        <el-date-picker
                                            v-model="form.month_end"
                                            type="month"
                                            :picker-options="pickerOptionsMonths"
                                            @change="loadAll"
                                            value-format="yyyy-MM"
                                            format="MM/yyyy"
                                            :clearable="false"
                                        ></el-date-picker>
                                    </div>
                                </template>
                                <template v-if="form.period === 'date' || form.period === 'between_dates'">
                                    <div :class="filterColumnClass" class="form-group">
                                        <label class="control-label">Fecha del</label>
                                        <el-date-picker
                                            v-model="form.date_start"
                                            type="date"
                                            @change="changeDisabledDates"
                                            value-format="yyyy-MM-dd"
                                            format="dd/MM/yyyy"
                                            :clearable="false"
                                        ></el-date-picker>
                                    </div>
                                </template>
                                <template v-if="form.period === 'between_dates'">
                                    <div :class="filterColumnClass" class="form-group">
                                        <label class="control-label">Fecha al</label>
                                        <el-date-picker
                                            v-model="form.date_end"
                                            type="date"
                                            :picker-options="pickerOptionsDates"
                                            @change="loadAll"
                                            value-format="yyyy-MM-dd"
                                            format="dd/MM/yyyy"
                                            :clearable="false"
                                        ></el-date-picker>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <RowTop :company="company" :utilities="utilities" :filters="form"></RowTop>

            <div class="row mx-0 px-1">
                <div class="col-xl-12 card-dashboard-section">
                    <div class="row">
                    <template v-if="configuration.dashboard_sales">
                    <div class="col-12 col-md-6 col-xl-3 mb-2">
                        <section class="card card-dashboard sn-panel">
                            <div class="card-body" v-if="loaders.sale_note">
                                <template >
                                    <loader-graph :rows="4" :columns="1" :radius="50"></loader-graph>
                                </template>
                            </div>
                            <div class="card-body card-body-border-radius" v-if="!loaders.sale_note">
                                <div class="sn-head">
                                    <div>
                                        <h5 class="sn-title m-0">Notas de venta</h5>
                                        <small class="text-muted">Cobros y pendientes</small>
                                    </div>
                                </div>

                                <div v-if="saleNoteHasData" class="sn-body">
                                    <div class="sn-chart">
                                        <apexchart type="donut" height="210" :options="saleNoteChartOptions" :series="saleNoteValues"></apexchart>
                                    </div>
                                    <ul class="sn-legend">
                                        <li v-for="(item, index) in saleNoteLegendItems" :key="index" class="sn-legend-item">
                                            <span class="sn-dot" :style="{ background: item.color }"></span>
                                            <span class="sn-label text-truncate">{{ item.label }}</span>
                                            <span class="sn-value">S/ {{ item.value | saleNoteMoney }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <div v-else class="sn-empty text-muted text-center">Sin notas de venta en el periodo.</div>
                            </div>
                        </section>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 mb-2" v-if="soapCompany != '03'">
                        <section class="card card-dashboard sn-panel">
                            <div class="card-body" v-if="loaders.document">
                                <template >
                                    <loader-graph :rows="4" :columns="1" :radius="50"></loader-graph>
                                </template>
                            </div>
                            <div class="card-body card-body-border-radius" v-if="!loaders.document">
                                <div class="sn-head">
                                    <div>
                                        <h5 class="sn-title m-0">Comprobantes</h5>
                                        <small class="text-muted">Cobros y pendientes</small>
                                    </div>
                                </div>

                                <div v-if="documentHasData" class="sn-body">
                                    <div class="sn-chart">
                                        <apexchart type="donut" height="210" :options="documentChartOptions" :series="documentValues"></apexchart>
                                    </div>
                                    <ul class="sn-legend">
                                        <li v-for="(item, index) in documentLegendItems" :key="index" class="sn-legend-item">
                                            <span class="sn-dot" :style="{ background: item.color }"></span>
                                            <span class="sn-label text-truncate">{{ item.label }}</span>
                                            <span class="sn-value">S/ {{ item.value | saleNoteMoney }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <div v-else class="sn-empty text-muted text-center">Sin comprobantes en el periodo.</div>
                            </div>
                        </section>
                    </div>
                    <div class="col-12 col-xl-6 mb-2">
                        <section class="card card-dashboard gt-panel">
                            <div class="card-body" v-if="loaders.general">
                                <template >
                                    <loader-graph :rows="2" :columns="3" :radius="100"></loader-graph>
                                </template>
                            </div>
                            <div class="card-body card-body-border-radius" v-if="!loaders.general">
                                <div class="gt-head">
                                    <div>
                                        <h5 class="gt-title m-0">Totales</h5>
                                        <small class="text-muted">Notas de venta y comprobantes</small>
                                    </div>
                                    <div class="gt-legend">
                                        <span v-for="item in generalLegendItems" :key="item.label" class="gt-legend-item">
                                            <i class="gt-dot" :style="{ background: item.color }"></i>{{ item.label }}
                                        </span>
                                    </div>
                                </div>

                                <div class="gt-metrics">
                                    <div v-for="item in generalLegendItems" :key="`metric-${item.label}`" class="gt-metric">
                                        <small class="text-muted">{{ item.label }}</small>
                                        <strong>S/ {{ item.value | saleNoteMoney }}</strong>
                                    </div>
                                </div>

                                <apexchart
                                    v-if="generalHasData"
                                    type="area"
                                    height="260"
                                    :options="generalChartOptions"
                                    :series="generalSeries"
                                ></apexchart>

                                <div v-else class="gt-empty text-muted text-center">Sin totales para graficar en el periodo.</div>
                            </div>
                        </section>
                    </div>
                    </template>
                    <div class="col-12 mb-2" :class="{ 'col-xl-8': configuration.dashboard_goal_enabled }">
                        <weekly-sales-chart :filters="form"></weekly-sales-chart>
                    </div>
                    <div v-if="configuration.dashboard_goal_enabled" class="col-12 col-xl-4 mb-2">
                        <month-goal></month-goal>
                    </div>
                    <div class="col-12 col-md-6 mb-2" :class="configuration.dashboard_products ? 'col-xl-4' : 'col-xl-6'">
                        <debtors :filters="form"></debtors>
                    </div>
                    <template v-if="configuration.dashboard_products">
                        <div class="col-12 col-md-6 col-xl-4 mb-2">
                            <div v-if="loaders.items_by_sales" class="card card-dashboard">
                                <div class="card-body">
                                    <loader-graph :rows="4" :columns="1" :radius="100" :hideCircle="true"></loader-graph>
                                </div>
                            </div>
                            <top-products
                                v-else
                                :items="items_by_sales"
                                v-model="form.enabled_move_item"
                                @order-change="loadDataAditional"
                            ></top-products>
                        </div>
                    </template>
                    <div class="col-12 col-md-6 mb-2" :class="configuration.dashboard_products ? 'col-xl-4' : 'col-xl-6'">
                        <payment-methods :filters="form"></payment-methods>
                    </div>
                    <div class="col-12 col-xl-8 mb-2">
                        <cash-flow-chart :filters="form"></cash-flow-chart>
                    </div>
                    <div class="col-12 col-xl-4 mb-2 d-flex flex-column">
                        <sunat-status :filters="form"></sunat-status>
                        <template v-if="configuration.dashboard_products">
                            <low-stock :filters="form"></low-stock>
                        </template>
                    </div>
                    
                    <template v-if="showLegacyCards && configuration.dashboard_general">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <section class="card card-dashboard">
                                <div class="card-body" v-if="loaders.balance">
                                    <template>
                                        <loader-graph :rows="4" :columns="1" :radius="50"></loader-graph>
                                    </template>
                                </div>
                                <div class="card-body card-body-border-radius" v-if="!loaders.balance">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col" v-if="document">
                                            <div class="row no-gutters">
                                                <div class="col-md-12 m-b-10">
                                                    <label>
                                                        Balance
                                                        <el-tooltip
                                                            class="item"
                                                            effect="dark"
                                                            content="Ventas - Compras - Gastos"
                                                            placement="top-start"
                                                            >
                                                                <i class="fa fa-info-circle"></i>
                                                            </el-tooltip>
                                                        </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <x-graph type="doughnut" :all-data="balance.graph"></x-graph>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                    <div class="mt-3" v-show="!loaders.balance">
                                        <table class="table-dashboard mb-0 table-sm">
                                            <tbody class="card-default">
                                                <tr class="text-info text-bold">
                                                    <td class="d-flex align-items-center">
                                                        <el-popover placement="right" width="100%" trigger="hover">
                                                            <p><span class="custom-badge">T. Ventas - T. Compras/Gastos</span></p>
                                                            <p>Total comprobantes:<span class="custom-badge pull-right">S/ {{ balance.totals.total_document }}</span></p>
                                                            <p>Total notas de venta:<span class="custom-badge pull-right">S/ {{ balance.totals.total_sale_note }}</span></p>
                                                            <p>Total compras:<span class="custom-badge pull-right">- S/ {{ balance.totals.total_purchase }}</span></p>
                                                            <p>Total gastos:<span class="custom-badge pull-right">- S/ {{ balance.totals.total_expense }}</span></p>
                                                            <el-button class="me-1" icon="el-icon-view" type="primary" size="mini" slot="reference" circle></el-button>
                                                        </el-popover>
                                                        Totales
                                                    </td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ balance.totals.all_totals_payment }}</td>
                                                </tr>
                                                <tr class="text-danger text-bold td-total">
                                                    <td class="d-flex align-items-center">
                                                        <el-popover placement="right" width="100%" trigger="hover">
                                                        <p><span class="custom-badge">T. Pagos Ventas - T. Pagos Compras/Gastos</span></p>
                                                        <p>Total pagos comprobantes:<span class="custom-badge pull-right">S/ {{ balance.totals.total_payment_document }}</span></p>
                                                        <p>Total pagos notas de venta:<span class="custom-badge pull-right">S/ {{ balance.totals.total_payment_sale_note }}</span></p>
                                                        <p>Total pagos compras:<span class="custom-badge pull-right">- S/ {{ balance.totals.total_payment_purchase }}</span></p>
                                                        <p>Total pagos gastos:<span class="custom-badge pull-right">- S/ {{ balance.totals.total_payment_expense }}</span></p>
                                                        <el-button class="me-1" icon="el-icon-view" type="danger" size="mini" slot="reference" circle></el-button>
                                                        </el-popover>
                                                        Total pagos
                                                    </td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ balance.totals.all_totals_payment }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <section class="card card-dashboard">
                                <div class="card-body" v-if="loaders.utility">
                                    <template>
                                        <loader-graph :rows="4" :columns="1" :radius="50"></loader-graph>
                                    </template>
                                </div>
                                <div class="card-body card-body-border-radius" v-if="!loaders.utility">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col" v-if="utilities">
                                            <div class="row no-gutters">
                                                <div class="col-md-12 m-b-10">
                                                    <label>Utilidades/Ganancias</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <x-graph type="doughnut" :all-data="utilities.graph"></x-graph>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                
                                    <div class="mt-3" v-show="!loaders.utility">
                                        <table class="table-dashboard mb-0 table-sm">
                                            <tbody class="card-default">
                                                <tr>
                                                    <td colspan="2">
                                                        <el-checkbox  v-model="form.enabled_expense" @change="loadDataUtilities">Considerar gastos</el-checkbox><br>
                                                        <el-checkbox  v-model="filter_item" @change="changeFilterItem">Filtrar por producto</el-checkbox>
                                                    </td>
                                                </tr>
                                                <tr v-if="filter_item">
                                                    <td colspan="2">
                                                        <div class="form-group">
                                                            <el-select v-model="form.item_id" filterable remote  popper-class="el-select-customers"  clearable
                                                                placeholder="Buscar producto"
                                                                :remote-method="searchRemoteItems"
                                                                :loading="loading_search"
                                                                @change="loadDataUtilities">
                                                                <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                                            </el-select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="text-info text-bold">
                                                    <td>Ingreso</td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ utilities.totals.total_income }}</td>
                                                </tr>
                                                <tr class="text-danger text-bold">
                                                    <td>Egreso</td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ utilities.totals.total_egress }}</td>
                                                </tr>
                                                <tr class="text-bold td-total">
                                                    <td class="">Utilidad</td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ utilities.totals.utility }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="col-xl-6 col-md-6">
                            <section class="card card-dashboard">
                                <div class="card-body" v-if="loaders.purchase">
                                    <template>
                                        <loader-graph :rows="2" :columns="3" :radius="100"></loader-graph>
                                    </template>
                                </div>
                                <div class="card-body card-body-border-radius" v-if="!loaders.purchase">
                                    <div class="widget-summary">
                                        <div class="widget-summary-col" v-if="general">
                                            <div class="summary">
                                                <div class="row no-gutters">
                                                    <div class="col-md-12 m-b-10">
                                                        <label>Compras
                                                            <el-tooltip
                                                            class="item"
                                                            effect="dark"
                                                            content="Aplica filtro por sucursal"
                                                            placement="top-start"
                                                            >
                                                                <i class="fa fa-info-circle"></i>
                                                            </el-tooltip>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="row m-t-20">
                                                    <div class="col-md-12">
                                                        <x-graph-line :all-data="purchase.graph"></x-graph-line>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3" v-show="!loaders.purchase">
                                        <table class="table-dashboard mb-0 table-sm">
                                            <tbody class="card-default">
                                                <tr class="text-info text-bold">
                                                    <td>Total percepciones</td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ purchase.totals.purchases_total_perception }}</td>
                                                </tr>
                                                <tr class="text-danger text-bold">
                                                    <td>Total compras</td>
                                                    <td class="text-end right font-weight-bold">S/&nbsp;{{ purchase.totals.purchases_total }}</td>
                                                </tr>
                                                <tr class="text-bold td-total">
                                                    <td class="">Total</td>
                                                    <td class="text-end font-weight-bold">S/&nbsp;{{ purchase.totals.total }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </template>
                    <template v-if="showLegacyCards && configuration.dashboard_clients">
                        <div class="col-xl-3 col-md-6">
                            <section class="card card-dashboard">
                                <div class="card-body" v-if="loaders.top_customers">
                                    <template>
                                        <loader-graph :rows="4" :columns="1" :radius="100" :hideCircle="true"></loader-graph>
                                    </template>
                                </div>
                                <div class="card-body card-body-border-radius" v-show="!loaders.top_customers">
                                    <label>Top clientes</label>
                                    <div class="mt-3">
                                        <el-checkbox  v-model="form.enabled_transaction_customer" @change="loadDataAditional">Ordenar por transacciones</el-checkbox><br>
                                    </div>                                
                                    <div class="mt-3" v-show="!loaders.top_customers">
                                        <div class="table-responsive table-default">
                                            <table class="table table-default">
                                                <thead class="card-default">
                                                    <tr>
                                                        <!-- <th>#</th> -->
                                                        <th>Cliente</th>
                                                        <th class="text-end">
                                                            Trans.
                                                            <el-tooltip
                                                                class="item"
                                                                effect="dark"
                                                                content="Transacciones (Cantidad de ventas realizadas)"
                                                                placement="top-start"
                                                            >
                                                                <i class="fa fa-info-circle"></i>
                                                            </el-tooltip>
                                                        </th>
                                                        <th class="text-end">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tbody-default">
                                                    <template v-for="(row, index) in top_customers" class="template-default">
                                                        <tr :key="index" class="tr-default">
                                                            <!-- <td>{{ index + 1 }}</td> -->
                                                            <td>{{ row.name }}<br /><small v-text="row.number"></small></td>
                                                            <td class="text-end">{{ row.transaction_quantity }}</td>
                                                            <td class="text-end">{{ row.total }}</td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </template>
                    <template v-if="showLegacyCards && configuration.dashboard_products">
                        <div class="col-xl-6 col-md-12 col-lg-12">
                            <dashboard-inventory></dashboard-inventory>
                        </div>
                    </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.widget-summary .summary {
  min-height: inherit;
}
.custom-badge {
  font-size: 15px;
  font-weight: bold;
}
.card.card-dashboard .table td,
.card.card-dashboard .table th {
    font-size: 0.9rem;
    font-weight: 500;
}
.sn-panel {
  height: 100%;
}
.sn-head {
  align-items: flex-start;
  display: flex;
  gap: 1rem;
  justify-content: space-between;
  margin-bottom: 1rem;
}
.sn-body {
  align-items: center;
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}
.sn-chart {
  flex: 1 1 170px;
  min-width: 160px;
}
.sn-legend {
  flex: 1 1 155px;
  list-style: none;
  margin: 0;
  padding: 0;
}
.sn-legend-item {
  align-items: center;
  display: flex;
  gap: 0.5rem;
  padding: 0.35rem 0;
}
.sn-dot {
  border-radius: 3px;
  flex-shrink: 0;
  height: 10px;
  width: 10px;
}
.sn-label {
  flex: 1 1 auto;
  font-size: 0.85rem;
  min-width: 0;
}
.sn-value {
  flex-shrink: 0;
  font-size: 0.85rem;
  font-weight: 700;
}
.sn-empty {
  padding: 2rem 0 1.5rem;
}
.sn-panel .apexcharts-tooltip,
.sn-panel .apexcharts-tooltip *,
.sn-panel .apexcharts-tooltip-title {
  color: #fff !important;
}
.gt-panel {
  height: 100%;
}
.gt-head {
  align-items: flex-start;
  display: flex;
  gap: 1rem;
  justify-content: space-between;
  margin-bottom: 1rem;
}
.gt-title {
  font-size: 1.05rem;
  font-weight: 600;
}
.gt-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 0.85rem;
  justify-content: flex-end;
}
.gt-legend-item {
  align-items: center;
  color: #6b7280;
  display: flex;
  font-size: 0.78rem;
  font-weight: 700;
  gap: 0.4rem;
}
.gt-dot {
  border-radius: 50%;
  display: inline-block;
  height: 9px;
  width: 9px;
}
.gt-metrics {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  margin-bottom: 0.75rem;
}
.gt-metric {
  background: color-mix(in srgb,var(--primary) 4%,transparent);
  border-radius: 8px;
  padding: 0.7rem 0.8rem;
}
.gt-metric small {
  display: block;
  font-size: 0.72rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  text-transform: uppercase;
}
.gt-metric strong {
  display: block;
  font-size: 0.95rem;
  overflow-wrap: anywhere;
}
.gt-empty {
  padding: 2.5rem 0;
}
.sn-title {
    font-weight: 600;
}
@media (max-width: 767.98px) {
  .gt-head {
    flex-direction: column;
  }
  .gt-legend {
    justify-content: flex-start;
  }
  .gt-metrics {
    grid-template-columns: 1fr;
  }
}
</style>
<script>
import DashboardStock from "./partials/dashboard_stock.vue";
import queryString from "query-string";
import LoaderGraph from "../components/loaders/l-graph.vue";
import RowTop from "./RowTop.vue";
import DashboardInventory from "./partials/dashboard_inventory.vue";
import NewDashboard from "./partials/new_dashboard/NewDashboard.vue";
import TopProducts from "./partials/TopProducts.vue";
import CashFlowChart from "./partials/CashFlowChart.vue";
import LowStock from "./partials/LowStock.vue";
import WeeklySalesChart from "./partials/WeeklySalesChart.vue";
import PaymentMethods from "./partials/PaymentMethods.vue";
import SunatStatus from "./partials/SunatStatus.vue";
import Debtors from "./partials/Debtors.vue";
import MonthGoal from "./partials/MonthGoal.vue";
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

export default {
  props: ["typeUser", "soapCompany",'configuration'],
  components: { DashboardStock, LoaderGraph, RowTop, DashboardInventory, NewDashboard, TopProducts, CashFlowChart, LowStock, WeeklySalesChart, PaymentMethods, SunatStatus, Debtors, MonthGoal },
  data() {
    return {
            showWelcomePanel: true,
      showLegacyDashboard: true,
      showLegacyCards: false,
      showFilters: false,
      loading_search: false,
      records_base: [],
      selected_customer: null,
      customers: [],
      resource: "dashboard",
      establishments: [],
      balance: {
        totals: {},
        graph: [],
      },
      document: {
        totals: {},
        graph: [],
      },
      sale_note: {
        totals: {},
        graph: [],
      },
      general: {
        totals: {},
        graph: [],
      },
      purchase: {
        totals: {},
        graph: [],
      },
      utilities: {
        totals: {},
        graph: [],
      },
      form: {},
      pickerOptionsDates: {
        disabledDate: (time) => {
          time = moment(time).format("YYYY-MM-DD");
          return this.form.date_start > time;
        },
      },
      pickerOptionsMonths: {
        disabledDate: (time) => {
          time = moment(time).format("YYYY-MM");
          return this.form.month_start > time;
        },
      },
      records: [],
      items_by_sales: [],
      top_customers: [],
      recordId: null,
      showDialogDocumentPayments: false,
      showDialogSaleNotePayments: false,
      filter_item: false,
      all_items: [],
      items: [],
      company: {},
      loaders: {},
      welcomeObserver: null,
      dashboardThemeObserver: null,
      dashboardThemeChangeHandler: null,
      dashboardThemeRefreshFrame: null,
      themeColors: {
        primary: "#2447e8",
        danger: "#fe006c",
        success: "#00c666",
        warning: "#ff8400",
        info: "#00cfe8",
      },
    };
  },
  async created() {
    this.loadConfiguration()
    this.$store.commit('setConfiguration', this.configuration)
    this.initForm();
    this.initLoaders();
    await this.$http.get(`/${this.resource}/filter`).then((response) => {
      this.establishments = response.data.establishments;
      this.form.establishment_id =
        this.establishments.length > 0 ? this.establishments[0].id : null;
    });

    this.$eventHub.$on('establishmentChanged', this.updateEstablishment);

    await this.loadAll();
    await this.filterItems();
  },
    mounted() {
        this.initWelcomeVisibility();
        this.initDashboardThemeColors();
    },
    beforeDestroy() {
        if (this.welcomeObserver) {
            this.welcomeObserver.disconnect();
            this.welcomeObserver = null;
        }
        this.teardownDashboardThemeObserver();
    },

  computed: {
    filterLabel() {
      switch(this.form.period) {
        case 'all':
          return 'Filtrado: Todos los registros';
        case 'last_week':
          return 'Filtrado por: Última semana';
        case 'month':
          return this.form.month_start ? `Filtrado por mes: ${moment(this.form.month_start).format('MM/YYYY')}` : 'Filtrado por: Por mes';
        case 'between_months':
          return this.form.month_start && this.form.month_end 
            ? `Filtrado entre meses: ${moment(this.form.month_start).format('MM/YYYY')} - ${moment(this.form.month_end).format('MM/YYYY')}` 
            : 'Filtrado por: Entre meses';
        case 'date':
          return this.form.date_start ? `Filtrado por fecha: ${moment(this.form.date_start).format('DD/MM/YYYY')}` : 'Filtrado por: Por fecha';
        case 'between_dates':
          return this.form.date_start && this.form.date_end 
            ? `Filtrado entre fechas: ${moment(this.form.date_start).format('DD/MM/YYYY')} - ${moment(this.form.date_end).format('DD/MM/YYYY')}` 
            : 'Filtrado por: Entre fechas';
        default:
          return 'Filtrado por: Última semana';
      }
    },
    visibleFiltersCount() {
      let count = 2; // Siempre: Sucursal + Periodo
      
      if (this.form.period === 'month') {
        count += 1; // + Mes de
      } else if (this.form.period === 'between_months') {
        count += 2; // + Mes de + Mes al
      } else if (this.form.period === 'date') {
        count += 1; // + Fecha del
      } else if (this.form.period === 'between_dates') {
        count += 2; // + Fecha del + Fecha al
      }
      
      return count;
    },
    filterColumnClass() {
      const count = this.visibleFiltersCount;
      const colMap = {
        2: 'col-md-6 col-12',      // 2 inputs = 50% cada uno
        3: 'col-md-4 col-12',      // 3 inputs = 33.33% cada uno
        4: 'col-md-3 col-12'       // 4 inputs = 25% cada uno
      };
      return colMap[count] || 'col-md-3 col-12';
    },
    saleNoteDataset() {
      const graph = this.sale_note && this.sale_note.graph ? this.sale_note.graph : {};
      const datasets = Array.isArray(graph.datasets) ? graph.datasets : [];
      return datasets.length ? datasets[0] : {};
    },
    saleNoteLabels() {
      const graph = this.sale_note && this.sale_note.graph ? this.sale_note.graph : {};
      const labels = Array.isArray(graph.labels) ? graph.labels : [];
      return labels.length ? labels : ["Total cobrado", "Pendiente de cobro"];
    },
    saleNoteValues() {
      const data = Array.isArray(this.saleNoteDataset.data) ? this.saleNoteDataset.data : [];
      return this.saleNoteLabels.map((label, index) => Number(data[index]) || 0);
    },
    saleNoteColors() {
      return [this.themeColors.primary, this.themeColors.danger];
    },
    saleNoteLegendItems() {
      const items = this.saleNoteLabels.map((label, index) => ({
        label,
        value: this.saleNoteValues[index] || 0,
        color: this.saleNoteColors[index % this.saleNoteColors.length],
      }));

      items.push({
        label: "Total",
        value: this.saleNoteTotal,
        color: this.themeColors.success,
      });

      return items;
    },
    saleNotePaid() {
      return Number(this.sale_note && this.sale_note.totals ? this.sale_note.totals.total_payment : 0) || 0;
    },
    saleNotePending() {
      return Number(this.sale_note && this.sale_note.totals ? this.sale_note.totals.total_to_pay : 0) || 0;
    },
    saleNoteTotal() {
      return Number(this.sale_note && this.sale_note.totals ? this.sale_note.totals.total : 0) || 0;
    },
    saleNoteHasData() {
      return this.saleNoteValues.some((value) => Number(value) > 0);
    },
    saleNoteChartOptions() {
      return {
        chart: { fontFamily: "inherit" },
        labels: this.saleNoteLabels,
        colors: this.saleNoteColors,
        stroke: { width: 0 },
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
          pie: {
            donut: {
              size: "72%",
              labels: {
                show: true,
                value: {
                  fontSize: "18px",
                  fontWeight: 700,
                  formatter: (val) => "S/ " + this.formatSaleNoteK(val),
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: "TOTAL",
                  color: "#9ca3af",
                  fontSize: "11px",
                  formatter: () => "S/ " + this.formatSaleNoteK(this.saleNoteTotal),
                },
              },
            },
          },
        },
        tooltip: {
          y: {
            formatter: (val) =>
              "S/ " +
              Number(val).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }),
          },
        },
      };
    },
    documentDataset() {
      const graph = this.document && this.document.graph ? this.document.graph : {};
      const datasets = Array.isArray(graph.datasets) ? graph.datasets : [];
      return datasets.length ? datasets[0] : {};
    },
    documentLabels() {
      const graph = this.document && this.document.graph ? this.document.graph : {};
      const labels = Array.isArray(graph.labels) ? graph.labels : [];
      return labels.length ? labels : ["Total cobrado", "Pendiente de cobro"];
    },
    documentValues() {
      const data = Array.isArray(this.documentDataset.data) ? this.documentDataset.data : [];
      return this.documentLabels.map((label, index) => Number(data[index]) || 0);
    },
    documentColors() {
      return [this.themeColors.primary, this.themeColors.danger];
    },
    documentLegendItems() {
      const items = this.documentLabels.map((label, index) => ({
        label,
        value: this.documentValues[index] || 0,
        color: this.documentColors[index % this.documentColors.length],
      }));

      items.push({
        label: "Total",
        value: this.documentTotal,
        color: this.themeColors.success,
      });

      return items;
    },
    documentPaid() {
      return Number(this.document && this.document.totals ? this.document.totals.total_payment : 0) || 0;
    },
    documentPending() {
      return Number(this.document && this.document.totals ? this.document.totals.total_to_pay : 0) || 0;
    },
    documentTotal() {
      return Number(this.document && this.document.totals ? this.document.totals.total : 0) || 0;
    },
    documentHasData() {
      return this.documentValues.some((value) => Number(value) > 0);
    },
    documentChartOptions() {
      return {
        chart: { fontFamily: "inherit" },
        labels: this.documentLabels,
        colors: this.documentColors,
        stroke: { width: 0 },
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
          pie: {
            donut: {
              size: "72%",
              labels: {
                show: true,
                value: {
                  fontSize: "18px",
                  fontWeight: 700,
                  formatter: (val) => "S/ " + this.formatSaleNoteK(val),
                },
                total: {
                  show: true,
                  showAlways: true,
                  label: "TOTAL",
                  color: "#9ca3af",
                  fontSize: "11px",
                  formatter: () => "S/ " + this.formatSaleNoteK(this.documentTotal),
                },
              },
            },
          },
        },
        tooltip: {
          y: {
            formatter: (val) =>
              "S/ " +
              Number(val).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }),
          },
        },
      };
    },
    generalDatasets() {
      const graph = this.general && this.general.graph ? this.general.graph : {};
      return Array.isArray(graph.datasets) ? graph.datasets : [];
    },
    generalLabels() {
      const graph = this.general && this.general.graph ? this.general.graph : {};
      const labels = Array.isArray(graph.labels) ? graph.labels : [];
      return labels;
    },
    generalFormattedLabels() {
      return this.generalLabels.map((label, index) => this.formatGeneralChartLabel(label, index));
    },
    generalColors() {
      return this.generalDatasets.map((dataset, index) => {
        const label = (dataset.label || "").toLowerCase();
        if (label.includes("nota")) return this.themeColors.danger;
        if (label.includes("comprobante")) return this.themeColors.primary;
        if (label.includes("total")) return this.themeColors.success;

        const fallback = [this.themeColors.danger, this.themeColors.primary, this.themeColors.success];
        return fallback[index % fallback.length];
      });
    },
    generalSeries() {
      return this.generalDatasets.map((dataset) => ({
        name: dataset.label || "Total",
        data: Array.isArray(dataset.data) ? dataset.data.map((value) => Number(value) || 0) : [],
      }));
    },
    generalLegendItems() {
      return [
        {
          label: "Notas de venta",
          value: this.generalSaleNotesTotal,
          color: this.generalColors[0] || this.themeColors.danger,
        },
        {
          label: "Comprobantes",
          value: this.generalDocumentsTotal,
          color: this.generalColors[1] || this.themeColors.primary,
        },
        {
          label: "Total",
          value: this.generalTotal,
          color: this.themeColors.success,
        },
      ];
    },
    generalSaleNotesTotal() {
      return Number(this.general && this.general.totals ? this.general.totals.total_sale_notes : 0) || 0;
    },
    generalDocumentsTotal() {
      return Number(this.general && this.general.totals ? this.general.totals.total_documents : 0) || 0;
    },
    generalTotal() {
      return Number(this.general && this.general.totals ? this.general.totals.total : 0) || 0;
    },
    generalHasData() {
      return this.generalSeries.some((serie) => serie.data.some((value) => Number(value) > 0));
    },
    generalChartOptions() {
      return {
        chart: { toolbar: { show: false }, fontFamily: "inherit", zoom: { enabled: false } },
        colors: this.generalColors,
        stroke: { curve: "smooth", width: 2 },
        fill: {
          type: "gradient",
          gradient: { opacityFrom: 0.18, opacityTo: 0, stops: [0, 100] },
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        grid: { borderColor: "#eef0f3", strokeDashArray: 4, padding: { left: 8, right: 8 } },
        markers: { size: 0, hover: { size: 5 } },
        xaxis: {
          categories: this.generalFormattedLabels,
          axisBorder: { show: false },
          axisTicks: { show: false },
          labels: { style: { colors: "#9ca3af", fontSize: "12px" } },
        },
        yaxis: {
          labels: {
            style: { colors: "#9ca3af", fontSize: "12px" },
            formatter: (val) => this.formatSaleNoteK(val),
          },
        },
        tooltip: {
          y: {
            formatter: (val) =>
              "S/ " +
              Number(val).toLocaleString("es-PE", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }),
          },
        },
      };
    }
  },
  methods: {
    ...mapActions([
            'loadConfiguration',
        ]),
        initDashboardThemeColors() {
            this.refreshDashboardThemeColors();
            this.setupDashboardThemeObserver();
        },
        getDashboardCssColor(names, fallback) {
            const styles = getComputedStyle(document.documentElement);
            const colorNames = Array.isArray(names) ? names : [names];

            for (const name of colorNames) {
                const value = styles.getPropertyValue(name).trim();
                if (value) {
                    return value;
                }
            }

            return fallback;
        },
        refreshDashboardThemeColors() {
            this.themeColors = {
                primary: this.getDashboardCssColor(['--primary', '--primary-color'], this.themeColors.primary),
                danger: this.getDashboardCssColor('--danger', this.themeColors.danger),
                success: this.getDashboardCssColor('--success', this.themeColors.success),
                warning: this.getDashboardCssColor('--warning', this.themeColors.warning),
                info: this.getDashboardCssColor('--info', this.themeColors.info),
            };
        },
        scheduleDashboardThemeRefresh() {
            if (this.dashboardThemeRefreshFrame) {
                window.cancelAnimationFrame(this.dashboardThemeRefreshFrame);
            }

            this.dashboardThemeRefreshFrame = window.requestAnimationFrame(() => {
                this.dashboardThemeRefreshFrame = null;
                this.refreshDashboardThemeColors();
            });
        },
        setupDashboardThemeObserver() {
            this.teardownDashboardThemeObserver();

            this.dashboardThemeChangeHandler = () => {
                this.scheduleDashboardThemeRefresh();
            };

            window.addEventListener('theme:change', this.dashboardThemeChangeHandler);

            if (typeof MutationObserver !== 'undefined') {
                this.dashboardThemeObserver = new MutationObserver(() => {
                    this.scheduleDashboardThemeRefresh();
                });

                this.dashboardThemeObserver.observe(document.head, {
                    childList: true,
                    subtree: true,
                    characterData: true,
                });

                this.dashboardThemeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class', 'style'],
                });
            }
        },
        teardownDashboardThemeObserver() {
            if (this.dashboardThemeObserver) {
                this.dashboardThemeObserver.disconnect();
                this.dashboardThemeObserver = null;
            }

            if (this.dashboardThemeChangeHandler) {
                window.removeEventListener('theme:change', this.dashboardThemeChangeHandler);
                this.dashboardThemeChangeHandler = null;
            }

            if (this.dashboardThemeRefreshFrame) {
                window.cancelAnimationFrame(this.dashboardThemeRefreshFrame);
                this.dashboardThemeRefreshFrame = null;
            }
        },
        initWelcomeVisibility() {
            this.showWelcomePanel = this.isWelcomeVisible();

            const welcomeElement = document.querySelector('.welcome-component');
            if (!welcomeElement || typeof MutationObserver === 'undefined') {
                return;
            }

            this.welcomeObserver = new MutationObserver(() => {
                this.showWelcomePanel = this.isWelcomeVisible();
            });

            this.welcomeObserver.observe(welcomeElement, {
                attributes: true,
                attributeFilter: ['style', 'class'],
            });
        },
        isWelcomeVisible() {
            const welcomeElement = document.querySelector('.welcome-component');
            if (!welcomeElement) {
                return this.configuration?.visual?.show_welcome_panel !== false;
            }

            const style = window.getComputedStyle(welcomeElement);
            return style.display !== 'none' && style.visibility !== 'hidden';
        },
    toggleFilters() {
      this.showFilters = !this.showFilters;
    },
    updateEstablishment(establishmentId) {
      console.log('Cambiando a sucursal ID:', establishmentId);

      this.form.establishment_id = establishmentId;

      this.loadAll();
    },
    changeFilterItem() {
      this.form.item_id = null;
      this.loadDataUtilities();
    },
    searchRemoteItems(input) {
      if (input.length > 1) {
        this.loading_search = true;
        let parameters = `input=${input}`;
        this.$http
          .get(`/reports/data-table/items/?${parameters}`)
          .then((response) => {
            this.items = response.data.items;
            this.loading_search = false;

            if (this.items.length == 0) {
              this.filterItems();
            }
          });
      } else {
        this.filterItems();
      }
    },
    filterItems() {
      this.items = this.all_items;
    },
    calculateTotalCurrency(currency_type_id, exchange_rate_sale, total) {
      if (currency_type_id == "USD") {
        return parseFloat(total) * exchange_rate_sale;
      } else {
        return parseFloat(total);
      }
    },
    clickDownloadDispatch(download) {
      window.open(download, "_blank");
    },
    clickDownload(type) {
      let query = queryString.stringify({
        ...this.form,
      });
      window.open(`/reports/no_paid/${type}/?${query}`, "_blank");
    },
    initForm() {
      this.form = {
        item_id: null,
        establishment_id: null,
        enabled_expense: null,
        enabled_move_item: false,
        enabled_transaction_customer: false,
        period: "last_week",
        date_start: moment().subtract(7, 'days').format("YYYY-MM-DD"),
        date_end: moment().format("YYYY-MM-DD"),
        month_start: moment().format("YYYY-MM"),
        month_end: moment().format("YYYY-MM"),
        customer_id: null,
      };
    },
    changeDisabledDates() {
      if (this.form.date_end < this.form.date_start) {
        this.form.date_end = this.form.date_start;
      }
      this.loadAll();
    },
    changeDisabledMonths() {
      if (this.form.month_end < this.form.month_start) {
        this.form.month_end = this.form.month_start;
      }
      this.loadAll();
    },
    changePeriod() {
      if (this.form.period === "month") {
        this.form.month_start = moment().format("YYYY-MM");
        this.form.month_end = moment().format("YYYY-MM");
      }
      if (this.form.period === "between_months") {
        this.form.month_start = moment().startOf("year").format("YYYY-MM"); //'2019-01';
        this.form.month_end = moment().endOf("year").format("YYYY-MM");
      }
      if (this.form.period === "date") {
        this.form.date_start = moment().format("YYYY-MM-DD");
        this.form.date_end = moment().format("YYYY-MM-DD");
      }
      if (this.form.period === "between_dates") {
        this.form.date_start = moment().startOf("month").format("YYYY-MM-DD");
        this.form.date_end = moment().endOf("month").format("YYYY-MM-DD");
      }
      this.loadAll();
    },
    loadAll() {
      this.loadData();
      // this.loadUnpaid();
      this.loadDataAditional();
      this.loadDataUtilities();
      //this.loadCustomer();
      this.loadCompany();
      this.changeStock();
    },
    changeStock() {
      this.$eventHub.$emit("changeStock", this.form.establishment_id);
    },
    loadCompany() {
      this.$http.get(`/companies/record`).then((response) => {
        this.company = response.data.data;
      });
    },
    initLoaders() {
      this.loaders = {
        document: true,
        sale_note: true,
        general: true,
        balance: true,
        utility: true,
        purchase: true,
        items_by_sales: true,
        top_customers: true,
      };
    },
    showLoadersLoadData() {
      this.loaders.document = true;
      this.loaders.sale_note = true;
      this.loaders.general = true;
      this.loaders.balance = true;
    },
    hideLoadersLoadData() {
      this.loaders.document = false;
      this.loaders.sale_note = false;
      this.loaders.general = false;
      this.loaders.balance = false;
    },
    loadData() {
      this.showLoadersLoadData();

      this.$http.post(`/${this.resource}/data`, this.form).then((response) => {
        this.document = response.data.data.document;
        // this.documents_quantity = response.data.data.quantity;
        this.balance = response.data.data.balance;
        this.sale_note = response.data.data.sale_note;
        this.general = response.data.data.general;
        this.customers = response.data.data.customers;
        this.items = response.data.data.items;
        this.hideLoadersLoadData();
      });
    },
    loadDataAditional() {
      this.showLoadersLoadDataAditional();

      this.$http
        .post(`/${this.resource}/data_aditional`, this.form)
        .then((response) => {
          this.purchase = response.data.data.purchase;
          this.items_by_sales = response.data.data.items_by_sales;
          this.top_customers = response.data.data.top_customers;
          this.hideLoadersLoadDataAditional();
        });
    },
    loadDataUtilities() {
      this.loaders.utility = true;

      this.$http
        .post(`/${this.resource}/utilities`, this.form)
        .then((response) => {
          this.utilities = response.data.data.utilities;
          this.loaders.utility = false;
        });
    },
    showLoadersLoadDataAditional() {
      this.loaders.purchase = true;
      this.loaders.items_by_sales = true;
      this.loaders.top_customers = true;
    },
    hideLoadersLoadDataAditional() {
      this.loaders.purchase = false;
      this.loaders.items_by_sales = false;
      this.loaders.top_customers = false;
    },
    formatSaleNoteK(val) {
      const num = Number(val) || 0;
      if (Math.abs(num) >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, "") + "M";
      if (Math.abs(num) >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
      return num.toFixed(0);
    },
    formatGeneralChartLabel(label, index) {
      const value = String(label || "");

      if (moment(value, "YYYY-MM-DD", true).isValid()) {
        return moment(value, "YYYY-MM-DD").format("DD/MM");
      }

      if (/^\d{1,2}d$/.test(value)) {
        const startDate = this.getGeneralChartStartDate();

        if (startDate && startDate.isValid()) {
          return startDate.clone().add(index, "days").format("DD/MM");
        }

        return value.replace("d", "");
      }

      return value;
    },
    getGeneralChartStartDate() {
      if (["last_week", "date", "between_dates"].includes(this.form.period) && this.form.date_start) {
        return moment(this.form.date_start, "YYYY-MM-DD", true);
      }

      if (["month", "between_months"].includes(this.form.period) && this.form.month_start) {
        return moment(this.form.month_start, "YYYY-MM", true).startOf("month");
      }

      return null;
    },
  },
  filters: {
    saleNoteMoney(value) {
      return (Number(value) || 0).toLocaleString("es-PE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
};
</script>
