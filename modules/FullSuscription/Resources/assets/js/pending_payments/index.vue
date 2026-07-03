<template>
  <div>

    <!-- Page Header -->
    <div class="page-header pe-0 d-flex justify-content-between align-items-start">
      <div>
        <h2>
          <a href="/full-suscription/receipts" title="Ver comprobantes">
            <svg xmlns="http://www.w3.org/2000/svg" style="margin-top:-5px" width="24" height="24"
              viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2v-12z"/>
              <path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/>
              <path d="M7 14h.013"/><path d="M10.01 14h.005"/><path d="M13.01 14h.005"/>
              <path d="M16.015 14h.005"/><path d="M13.015 17h.005"/>
              <path d="M7.01 17h.005"/><path d="M10.01 17h.005"/>
            </svg>
          </a>
        </h2>
        <ol class="breadcrumbs">
          <li class="active"><span>Pagos pendientes</span></li>
        </ol>
      </div>

      <div class="d-flex align-items-center pt-1" style="gap:8px;">
        <transition name="fade">
          <span v-if="isSelectedIds" class="text-muted small">
            {{ multipleSelection.length }} seleccionada{{ multipleSelection.length !== 1 ? 's' : '' }}
          </span>
        </transition>
        <el-tooltip v-if="!isSelectedIds" effect="dark" content="Selecciona órdenes para enviar links" placement="top-start">
          <span>
            <el-button type="primary" disabled>
              <i class="fas fa-link mr-1"></i> Link manual
            </el-button>
          </span>
        </el-tooltip>
        <el-button v-else type="primary" @click="showLinkManual = true">
          <i class="fas fa-link mr-1"></i> Link manual
        </el-button>
      </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-2">
      <div class="col-md-12 filter-container">
        <div class="btn-filter-content">
          <el-button
            type="secondary"
            class="btn-show-filter mb-2"
            :class="{ shift: filterVisible }"
            @click="filterVisible = !filterVisible"
          >
            {{ filterVisible ? 'Ocultar filtros' : 'Mostrar filtros' }}
          </el-button>
        </div>
        <div v-if="filterVisible" class="row mx-0">
          <div class="col-lg-4 col-md-6 col-sm-12 pb-2">
            <el-input
              v-model="filters.q"
              placeholder="Buscar cliente..."
              prefix-icon="el-icon-search"
              style="width:100%;"
              clearable
            ></el-input>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
            <el-select
              v-model="filters.status"
              placeholder="Estado"
              style="width:100%;"
              clearable
            >
              <el-option label="Todos" value=""></el-option>
              <el-option label="Pendiente" value="pending"></el-option>
              <el-option label="Rechazado" value="rejected"></el-option>
              <el-option label="Vencido"   value="expired"></el-option>
            </el-select>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
            <el-input
              v-model="filters.plan"
              placeholder="Buscar plan..."
              style="width:100%;"
              clearable
            ></el-input>
          </div>
          <div class="col-lg-2 col-md-6 col-sm-12 pb-2">
            <el-button @click="clearFilters" style="width:100%;">
              <i class="fa fa-times"></i> Limpiar
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- Card principal -->
    <div class="card shadow-sm mb-0">

      <!-- Barra de resumen -->
      <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between" style="background:#fafbfc;">
        <div class="d-flex align-items-center" style="gap:6px;">
          <i class="fas fa-clock text-warning" style="font-size:13px;"></i>
          <span class="text-muted small">
            <strong class="text-dark">{{ records.length }}</strong>
            orden{{ records.length !== 1 ? 'es' : '' }} pendiente{{ records.length !== 1 ? 's' : '' }}
          </span>
        </div>
        <div class="d-flex align-items-center" style="gap:6px;">
          <span class="status-pill pending">Pendiente</span>
          <span class="status-pill rejected">Rechazado</span>
          <span class="status-pill expired">Vencido</span>
        </div>
      </div>

      <!-- Tabla -->
      <el-table
        ref="multipleTable"
        :data="filteredRecords"
        style="width:100%"
        :row-class-name="rowClass"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="46"></el-table-column>

        <!-- N° Orden -->
        <el-table-column label="N° Orden" width="110" align="center">
          <template slot-scope="{ row }">
            <span v-if="row.order_number" class="order-number-badge">{{ row.order_number }}</span>
            <span v-else class="text-muted">—</span>
          </template>
        </el-table-column>

        <!-- Fecha Emisión -->
        <el-table-column label="Emisión" width="105">
          <template slot-scope="{ row }">
            <span class="cell-date">{{ row.date_of_issue }}</span>
          </template>
        </el-table-column>

        <!-- Cliente -->
        <el-table-column label="Cliente" min-width="175">
          <template slot-scope="{ row }">
            <div class="d-flex align-items-center" style="gap:9px;">
              <div class="avatar-initials">{{ initials(row.subscriptor_name) }}</div>
              <span class="client-name">{{ row.subscriptor_name }}</span>
            </div>
          </template>
        </el-table-column>

        <!-- Plan -->
        <el-table-column label="Plan" min-width="150">
          <template slot-scope="{ row }">
            <span class="plan-badge">{{ row.plan_name }}</span>
          </template>
        </el-table-column>

        <!-- Estado -->
        <el-table-column label="Estado" width="180">
          <template slot-scope="{ row }">
            <div class="d-flex align-items-center" style="gap:8px;">
              <span class="status-dot" :class="row.status_value"></span>
              <el-select v-model="row.status_value" size="small" style="flex:1;"
                @change="changeStatus(row)"
              >
                <el-option
                  v-for="(d, i) in status_types"
                  :key="i"
                  :label="d.description"
                  :value="d.value"
                ></el-option>
              </el-select>
            </div>
          </template>
        </el-table-column>

        <!-- Total -->
        <el-table-column label="Total" width="105" align="right">
          <template slot-scope="{ row }">
            <span class="total-amount">S/ {{ row.total }}</span>
          </template>
        </el-table-column>

        <!-- Notificaciones enviadas -->
        <el-table-column label="Notificaciones" width="130" align="center">
          <template slot-scope="{ row }">
            <div class="notif-cell">
              <div class="notif-count">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/></svg>
                <span>{{ row.count_notification || 0 }} envío{{ (row.count_notification || 0) !== 1 ? 's' : '' }}</span>
              </div>
              <div v-if="row.date_notification" class="notif-date">{{ row.date_notification }}</div>
              <div v-else class="notif-date notif-date--none">Sin envíos</div>
            </div>
          </template>
        </el-table-column>

        <!-- Link de pago -->
        <el-table-column label="Link de pago" width="120" align="center">
          <template slot-scope="{ row }">
            <button
              v-if="row.checkout_url"
              class="btn btn-sm btn-copy"
              :class="{ 'btn-copy--copied': copiedId === row.id }"
              :title="copiedId === row.id ? 'Copiado!' : 'Copiar link de pago'"
              @click="copyCheckoutUrl(row)"
            >
              <svg v-if="copiedId !== row.id" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
              {{ copiedId === row.id ? 'Copiado' : 'Copiar' }}
            </button>
            <span v-else class="text-muted">—</span>
          </template>
        </el-table-column>

      </el-table>

      <!-- Estado vacío -->
      <div v-if="filteredRecords.length === 0" class="text-center py-5">
        <div class="empty-icon-wrap mx-auto mb-3">
          <i class="far fa-check-circle fa-2x text-muted"></i>
        </div>
        <p class="font-weight-semibold mb-1">Sin órdenes pendientes</p>
        <p class="text-muted small mb-0">Todas las órdenes han sido procesadas.</p>
      </div>

    </div>

    <link-manual :multipleIds="multipleSelection" :show.sync="showLinkManual" />

  </div>
</template>

<script>
import LinkManual from './partials/link-manual.vue';

export default {
  components: { LinkManual },

  data() {
    return {
      copiedId: null,
      resource: '/full-suscription/receipts',
      records: [],
      status_types: [],
      showLinkManual: false,
      multipleSelection: [],
      filterVisible: false,
      filters: {
        q: '',
        status: '',
        plan: '',
      },
    }
  },

  computed: {
    isSelectedIds() {
      return this.multipleSelection.length > 0;
    },
    filteredRecords() {
      return this.records.filter(row => {
        if (this.filters.q && !row.subscriptor_name.toLowerCase().includes(this.filters.q.toLowerCase())) return false;
        if (this.filters.status && row.status_value !== this.filters.status) return false;
        if (this.filters.plan && !row.plan_name.toLowerCase().includes(this.filters.plan.toLowerCase())) return false;
        return true;
      });
    },
  },

  created() {
    this.getTables();
    this.$eventHub.$on('refresh-records', this.getRecord);
    this.getRecord();
  },

  methods: {
    getRecord() {
      this.$http.get(`${this.resource}/records?column=status&value[]=pending&value[]=rejected&value[]=expired`)
        .then(response => { this.records = response.data.data; });
    },
    getTables() {
      this.$http.get(`/full-suscription/pending-payments/tables`)
        .then(response => { this.status_types = response.data.status_types; });
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    initials(name) {
      if (!name) return '?';
      return name.trim().split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
    },
    rowClass({ row }) {
      const map = { rejected: 'row-rejected', expired: 'row-expired' };
      return map[row.status_value] || '';
    },
    clearFilters() {
      this.filters = { q: '', status: '', plan: '' };
    },
    copyCheckoutUrl(row) {
      if (!row.checkout_url) return;
      navigator.clipboard.writeText(row.checkout_url).then(() => {
        this.copiedId = row.id;
        setTimeout(() => { this.copiedId = null; }, 2000);
      });
    },
    changeStatus(row) {
      this.$http.post(`/full-suscription/pending-payments/${row.id}/change-status`, { status: row.status_value })
        .then(() => {
          this.getRecord();
          this.$message.success('Estado actualizado');
        })
        .catch(() => {
          this.$message.error('Error al actualizar estado');
          this.getRecord();
        });
    }
  },
}
</script>

<style scoped>
.avatar-initials {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e8f4fd;
  color: #0d7fc7;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  letter-spacing: 0.5px;
}

.client-name {
  font-size: 13px;
  font-weight: 600;
  color: #1a1a2e;
}

.cell-date {
  font-size: 13px;
  color: #6b7280;
}

.plan-badge {
  display: inline-block;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  padding: 2px 10px;
  font-size: 12px;
  font-weight: 500;
  color: #374151;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}
.status-dot.pending  { background: #f59e0b; }
.status-dot.rejected { background: #ef4444; }
.status-dot.expired  { background: #9ca3af; }

.total-amount {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
}

.status-pill {
  display: inline-block;
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}
.status-pill.pending  { background: #fef3c7; color: #92400e; }
.status-pill.rejected { background: #fee2e2; color: #991b1b; }
.status-pill.expired  { background: #f3f4f6; color: #6b7280; }

.row-rejected td { background: #fff8f8 !important; }
.row-expired  td { background: #fafafa !important; }

.empty-icon-wrap {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter, .fade-leave-to           { opacity: 0; }

.notif-cell {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  line-height: 1.3;
}
.notif-count {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 20px;
  padding: 2px 8px;
  font-size: 11px;
  font-weight: 700;
  color: #1d4ed8;
  white-space: nowrap;
}
.notif-date {
  font-size: 10px;
  color: #6b7280;
  white-space: nowrap;
}
.notif-date--none {
  color: #d1d5db;
  font-style: italic;
}

.btn-copy {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  font-weight: 600;
  color: #fff;
  background: #7c3aed;
  border: none;
  border-radius: 6px;
  padding: 4px 10px;
  transition: background .15s;
  cursor: pointer;
}
.btn-copy:hover { background: #6d28d9; color: #fff; }
.btn-copy--copied { background: #059669 !important; }

.order-number-badge {
  display: inline-block;
  font-family: monospace;
  font-size: 12px;
  font-weight: 700;
  color: #6d28d9;
  background: #ede9fe;
  border: 1px solid #c4b5fd;
  border-radius: 6px;
  padding: 2px 8px;
  letter-spacing: .5px;
  white-space: nowrap;
}
</style>
