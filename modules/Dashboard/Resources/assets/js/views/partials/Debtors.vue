<template>
  <section class="card card-dashboard db-panel">
    <div class="card-body">
      <div class="db-head">
        <h5 class="db-title m-0">¿Quién me debe?</h5>
        <small class="text-muted">
          S/ {{ total | dbMoney }} por cobrar · {{ count }} {{ count === 1 ? "cliente" : "clientes" }}
        </small>

        <div v-if="urgentDebts.length" class="db-urgent">
          <span class="db-dot"></span>
          <span>S/ {{ urgentTotal | dbMoney }} para cobrar ya · {{ urgentDebts.length }} {{ urgentDebts.length === 1 ? "deuda" : "deudas" }}</span>
        </div>
      </div>

      <div class="db-seg" :class="{ 'is-date': viewMode === 'date' }">
        <span class="db-thumb"></span>
        <button type="button" :class="{ active: viewMode === 'client' }" @click="viewMode = 'client'">Por cliente</button>
        <button type="button" :class="{ active: viewMode === 'date' }" @click="viewMode = 'date'">Por fecha</button>
      </div>

      <div v-if="viewMode === 'client'" class="db-list">
        <div
          v-for="(row, index) in items"
          :key="index"
          class="db-client"
          :class="{ open: row.open, expandable: row.debts_count > 1 }"
        >
          <div class="db-client-head" @click="toggleOpen(row)">
            <div class="db-info">
              <div class="db-name text-truncate">{{ row.customer }}</div>
              <div class="db-sub text-muted">
                <span v-if="row.debts_count > 1" class="db-count text-muted">{{ row.debts_count }} deudas</span>
                <span v-if="row.debts_count > 1">
                  <b>S/ {{ row.urgent_amount | dbMoney }}</b> {{ row.due_text }}
                </span>
                <span v-else>{{ row.due_text }}</span>
              </div>
            </div>

            <div class="db-right">
              <span class="db-amount">S/ {{ row.total_to_pay | dbMoney }}</span>
              <span class="db-badge" :class="badgeClass(row.status)">{{ badgeText(row) }}</span>
            </div>

            <span v-if="row.debts_count > 1" class="db-chevron text-muted">
              <svg v-if="!open" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" /></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-up"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 15l6 -6l6 6" /></svg>
            </span>
          </div>

          <div v-if="row.debts_count > 1" class="db-detail">
            <div class="db-detail-inner">
              <div v-for="debt in row.debts" :key="`${debt.id}-${debt.number}`" class="db-debt" :class="debtClass(debt.status)">
                <div class="db-debt-main">
                  <strong>S/ {{ debt.total_to_pay | dbMoney }}</strong>
                  <small class="text-muted">{{ debt.number }} · {{ debt.due_text }}</small>
                </div>
                <span class="db-badge" :class="badgeClass(debt.status)">{{ badgeText(debt) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="db-list">
        <div v-for="group in dateGroups" :key="group.status" class="db-group">
          <div class="db-group-head text-muted">
            <span><i :class="badgeClass(group.status)"></i>{{ group.label }} · {{ group.items.length }}</span>
            <strong>S/ {{ group.total | dbMoney }}</strong>
          </div>

          <div v-for="debt in group.items" :key="`${debt.customer}-${debt.id}-${debt.number}`" class="db-date-row">
            <div class="db-info">
              <span class="db-name text-truncate">{{ debt.customer }}</span>
              <small class="text-muted">{{ debt.number }} · {{ debt.due_text }}</small>
            </div>
            <strong>S/ {{ debt.total_to_pay | dbMoney }}</strong>
          </div>
        </div>
      </div>

      <div v-if="!items.length" class="db-empty text-muted text-center">No hay saldos por cobrar.</div>
    </div>
  </section>
</template>

<script>
export default {
  name: "Debtors",
  props: {
    filters: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      items: [],
      total: 0,
      count: 0,
      viewMode: "client",
    };
  },
  computed: {
    visibleDebts() {
      return this.items
        .reduce((debts, row) => {
          (row.debts || []).forEach((debt) => {
            debts.push({ ...debt, customer: row.customer });
          });
          return debts;
        }, [])
        .sort((a, b) => this.sortDueDays(a) - this.sortDueDays(b));
    },
    urgentDebts() {
      return this.visibleDebts.filter((debt) => debt.due_days !== null && debt.due_days !== undefined && Number(debt.due_days) <= 0);
    },
    urgentTotal() {
      return this.urgentDebts.reduce((sum, debt) => sum + Number(debt.total_to_pay || 0), 0);
    },
    dateGroups() {
      const groups = [
        { status: "vencido", label: "Vencidas", items: [] },
        { status: "por_vencer", label: "Por vencer", items: [] },
        { status: "al_dia", label: "Al día", items: [] },
      ];

      groups.forEach((group) => {
        group.items = this.visibleDebts.filter((debt) => debt.status === group.status);
        group.total = group.items.reduce((sum, debt) => sum + Number(debt.total_to_pay || 0), 0);
      });

      return groups.filter((group) => group.items.length);
    },
  },
  mounted() {
    this.fetchData();
  },
  watch: {
    filters: {
      deep: true,
      handler() {
        this.fetchData();
      },
    },
  },
  methods: {
    fetchData() {
      this.$http.get("/dashboard/debtors", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.items = (data.items || []).map((row) => ({ ...row, open: false }));
        this.total = data.total || 0;
        this.count = data.count || 0;
      });
    },
    toggleOpen(row) {
      if (row.debts_count > 1) {
        row.open = !row.open;
      }
    },
    sortDueDays(debt) {
      return debt.due_days === null || debt.due_days === undefined ? 999999 : Number(debt.due_days);
    },
    badgeClass(status) {
      return {
        "is-vencido": status === "vencido",
        "is-por_vencer": status === "por_vencer",
        "is-al_dia": status === "al_dia",
      };
    },
    debtClass(status) {
      return {
        "is-vencido": status === "vencido",
        "is-por_vencer": status === "por_vencer",
        "is-al_dia": status === "al_dia",
      };
    },
    badgeText(row) {
      if (row.due_days !== null && row.due_days !== undefined && Number(row.due_days) === 0) return "vence hoy";
      const labels = { vencido: "vencido", por_vencer: "por vencer", al_dia: "al día" };
      return labels[row.status] || row.status;
    },
  },
  filters: {
    dbMoney(value) {
      return (Number(value) || 0).toLocaleString("es-PE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
};
</script>

<style scoped>
.db-panel {
  height: 100%;
}
.db-head {
  margin-bottom: 1rem;
}
.db-title {
  font-size: 1.15rem;
  font-weight: 800;
}
.db-urgent {
  align-items: center;
  background: color-mix(in srgb, var(--danger) 15%, #ffffff00);
  border-radius: 14px;
  color: var(--danger);
  display: inline-flex;
  font-size: 0.82rem;
  font-weight: 700;
  gap: 0.5rem;
  margin-top: 0.85rem;
  padding: 0.45rem 0.75rem;
}
.db-dot {
  animation: dbPulse 1.8s ease-in-out infinite;
  background: var(--danger);
  border-radius: 50%;
  height: 8px;
  width: 8px;
}
@keyframes dbPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(0.75); }
}
.db-seg {
  background: #f1f2f5;
  border-radius: 14px;
  display: flex;
  margin-bottom: 0.75rem;
  padding: 4px;
  position: relative;
}
.db-seg button {
  background: transparent;
  border: 0;
  border-radius: 11px;
  color: #9ba1ad;
  flex: 1;
  font-size: 0.82rem;
  font-weight: 700;
  padding: 0.48rem 0;
  position: relative;
  z-index: 1;
}
.db-seg button.active {
  color: #1b1c26;
}
.db-thumb {
  background: #fff;
  border-radius: 11px;
  bottom: 4px;
  box-shadow: 0 2px 6px rgba(22, 25, 45, 0.1);
  left: 4px;
  position: absolute;
  top: 4px;
  transition: transform 0.24s ease;
  width: calc(50% - 4px);
}
.db-seg.is-date .db-thumb {
  transform: translateX(100%);
}
.db-list {
  list-style: none;
  margin: 0;
  padding: 0 4px 0 0;
  max-height: 350px;
  overflow-y: auto;
  scrollbar-color: rgba(155, 161, 173, 0.32) transparent;
  scrollbar-width: thin;
}
.db-list::-webkit-scrollbar {
  width: 5px;
}
.db-list::-webkit-scrollbar-track {
  background: transparent;
}
.db-list::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.28);
  border-radius: 999px;
}
.db-list:hover::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.46);
}
.db-client {
  border-bottom: 1px solid #eef0f3;
}
.db-client:last-child {
  border-bottom: 0;
}
.db-client-head,
.db-date-row {
  align-items: center;
  display: flex;
  gap: 0.75rem;
  padding: 0.85rem 0.25rem;
}
.db-client.expandable .db-client-head {
  cursor: pointer;
}
.db-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}
.db-name {
  font-size: 0.98rem;
  font-weight: 700;
}
.db-sub {
  align-items: center;
  display: flex;
  flex-wrap: wrap;
  font-size: 0.82rem;
  font-weight: 500;
  gap: 0.35rem;
  margin-top: 0.2rem;
}
.db-count {
  background: #f1f2f5;
  border-radius: 999px;
  font-size: 0.68rem;
  font-weight: 800;
  padding: 0.1rem 0.45rem;
}
.db-right {
  flex-shrink: 0;
  text-align: right;
}
.db-amount {
  display: block;
  font-size: 1rem;
  font-weight: 800;
}
.db-chevron {
  font-size: 1.15rem;
  transition: transform 0.2s ease;
}
.db-client.open .db-chevron {
  transform: rotate(180deg);
}
.db-badge {
  border-radius: 999px;
  display: inline-block;
  font-size: 0.7rem;
  font-weight: 800;
  margin-top: 0.25rem;
  padding: 0.18rem 0.55rem;
  white-space: nowrap;
}
.db-badge.is-vencido {
  background: color-mix(in srgb, var(--danger) 15%, #ffffff00);
  color: var(--danger);
}
.db-group-head i.is-vencido {
  background: var(--danger);
  color: var(--danger);
}
.db-badge.is-por_vencer {
  background: color-mix(in srgb, var(--warning) 15%, #ffffff00);
  color: var(--warning);
}
.db-group-head i.is-por_vencer {
  background: var(--warning);
  color: var(--warning);
}
.db-badge.is-al_dia {
  background: color-mix(in srgb, var(--success) 15%, #ffffff00);
  color: var(--success);
}
.db-group-head i.is-al_dia {
  background: var(--success);
  color: var(--success);
}
.db-detail {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.24s ease;
}
.db-client.open .db-detail {
  grid-template-rows: 1fr;
}
.db-detail-inner {
  overflow: hidden;
}
.db-debt {
  align-items: center;
  border-radius: 10px;
  display: flex;
  gap: 0.75rem;
  margin: 0 0.2rem 0.5rem;
  padding: 0.65rem 0.75rem 0.65rem 0.9rem;
  position: relative;
}
.db-debt::before {
  border-radius: 3px;
  bottom: 10px;
  content: "";
  left: 0;
  position: absolute;
  top: 10px;
  width: 3px;
}
.db-debt.is-vencido::before {
  background: var(--danger);
}
.db-debt.is-por_vencer::before {
  background: var(--warning);
}
.db-debt.is-al_dia::before {
  background: var(--success);
}
.db-debt-main {
  display: flex;
  flex: 1;
  flex-direction: column;
  min-width: 0;
}
.db-debt-main small {
  font-weight: 500;
}
.db-group {
  margin-bottom: 0.5rem;
}
.db-group-head {
  align-items: center;
  display: flex;
  font-size: 0.78rem;
  font-weight: 800;
  justify-content: space-between;
  letter-spacing: 0.02em;
  padding: 0.8rem 0.25rem 0.35rem;
  text-transform: uppercase;
}
.db-group-head span {
  align-items: center;
  display: flex;
  gap: 0.45rem;
}
.db-group-head i {
  border-radius: 3px;
  display: inline-block;
  height: 9px;
  width: 9px;
}
.db-date-row {
  border-bottom: 1px solid #eef0f3;
}
.db-date-row:last-child {
  border-bottom: 0;
}
.db-date-row strong {
  flex-shrink: 0;
}
.db-empty {
  padding: 2rem 0;
}
</style>
