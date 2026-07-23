<template>
  <section class="card card-dashboard tp-panel">
    <div class="card-body">
      <div class="tp-head">
        <div>
          <h5 class="tp-title m-0">Productos más vendidos</h5>
          <small class="text-muted">Por ingreso</small>
        </div>
        <el-checkbox v-model="orderByMove" @change="onOrderChange">
          Por movimientos
        </el-checkbox>
      </div>

      <ul class="tp-list">
        <li v-for="(row, index) in items" :key="index" class="tp-item">
          <span class="tp-rank text-muted">{{ index + 1 }}</span>
          <div class="tp-body">
            <div class="tp-row">
              <span class="tp-name text-truncate">{{ row.description }}</span>
              <span class="tp-total">S/ {{ row.total | tpMoney }}</span>
            </div>
            <div class="tp-bar">
              <span class="tp-bar-fill" :style="{ width: barWidth(row.total) }"></span>
            </div>
            <small class="tp-meta text-muted">{{ row.move_quantity | tpUnits }} uds</small>
          </div>
        </li>
      </ul>

      <div v-if="!items.length" class="tp-empty text-muted text-center">Sin ventas en el periodo.</div>

      <div class="d-flex justify-content-end">
        <a class="btn btn-sm second-buton" href="dashboard/sales-by-product">Ver todo</a>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "TopProducts",
  props: {
    items: { type: Array, default: () => [] },
    value: { type: Boolean, default: false },
  },
  data() {
    return {
      orderByMove: this.value,
    };
  },
  watch: {
    value(val) {
      this.orderByMove = val;
    },
  },
  computed: {
    maxTotal() {
      return this.items.reduce((max, row) => Math.max(max, Number(row.total) || 0), 0);
    },
  },
  methods: {
    onOrderChange(val) {
      this.$emit("input", val);
      this.$emit("order-change", val);
    },
    barWidth(total) {
      if (!this.maxTotal) return "0%";
      return Math.max((Number(total) / this.maxTotal) * 100, 3) + "%";
    },
  },
  filters: {
    tpMoney(value) {
      const num = Number(value);
      if (!Number.isFinite(num)) return "0.00";
      if (Math.abs(num) >= 1000000) return (num / 1000000).toFixed(1).replace(/\.0$/, "") + "M";
      if (Math.abs(num) >= 1000) return (num / 1000).toFixed(1).replace(/\.0$/, "") + "K";
      return num.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    },
    tpUnits(value) {
      const num = Number(value) || 0;
      return num.toLocaleString("es-PE", { maximumFractionDigits: 2 });
    },
  },
};
</script>

<style scoped>
.tp-panel {
  height: 100%;
}
.tp-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}
.tp-title {
  font-size: 1.05rem;
  font-weight: 600;
}
.tp-list {
  list-style: none;
  margin: 0;
  padding: 0;
  max-height: 300px;
  overflow-y: auto;
  scrollbar-color: rgba(155, 161, 173, 0.32) transparent;
  scrollbar-width: thin;
}
.tp-list::-webkit-scrollbar {
  width: 5px;
}
.tp-list::-webkit-scrollbar-track {
  background: transparent;
}
.tp-list::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.28);
  border-radius: 999px;
}
.tp-list:hover::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.46);
}
.tp-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0;
  border-bottom: 1px solid #f1f3f5;
}
.tp-item:last-child {
  border-bottom: 0;
}
.tp-rank {
  flex: 0 0 auto;
  width: 20px;
  font-weight: 600;
  text-align: center;
  font-size: 0.85rem;
}
.tp-body {
  flex: 1 1 auto;
  min-width: 0;
}
.tp-row {
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
}
.tp-name {
  font-weight: 500;
  min-width: 0;
}
.tp-total {
  flex-shrink: 0;
  font-weight: 700;
}
.tp-bar {
  height: 5px;
  border-radius: 3px;
  background: #f1f3f5;
  margin: 6px 0 4px;
  overflow: hidden;
}
.tp-bar-fill {
  display: block;
  height: 100%;
  border-radius: 3px;
  background: var(--primary);
}
.tp-meta {
  font-size: 0.72rem;
}
.tp-empty {
  padding: 2rem 0;
}
.tp-link {
  margin-top: 0.75rem;
}
</style>
