<template>
  <section class="card card-dashboard ls-panel mt-2">
    <div class="card-body mt-0">
      <div class="ls-head">
        <h5 class="ls-title m-0">Stock por agotarse</h5>
        <small class="text-muted">{{ total }} {{ total === 1 ? "producto" : "productos" }} con 10 o menos en stock</small>
      </div>

      <ul class="ls-list stock">
        <li v-for="(row, index) in items" :key="index" class="ls-item">
          <span class="ls-name text-truncate">{{ row.product }}</span>
          <span class="ls-ratio">{{ row.stock | lsNum }}</span>
        </li>
      </ul>

      <div v-if="!items.length" class="ls-empty text-muted text-center">Sin productos con 10 o menos en stock.</div>
    </div>
  </section>
</template>

<script>
export default {
  name: "LowStock",
  props: {
    filters: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      items: [],
      total: 0,
    };
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
      this.$http.get("/dashboard/low-stock", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.items = data.items || [];
        this.total = data.total || 0;
      });
    },
  },
  filters: {
    lsNum(value) {
      const num = Number(value) || 0;
      return Number.isInteger(num) ? num : num.toLocaleString("es-PE", { maximumFractionDigits: 2 });
    },
  },
};
</script>

<style scoped>
.ls-panel {
  height: 100%;
  margin-bottom: 0 !important;
}
.ls-head {
  margin-bottom: 1rem;
}
.ls-title {
  font-size: 1.05rem;
  font-weight: 600;
}
.ls-list {
  list-style: none;
  margin: 0;
  max-height: 100px;
  overflow-y: auto;
  padding: 0 4px 0 0;
  scrollbar-color: rgba(155, 161, 173, 0.32) transparent;
  scrollbar-width: thin;
}
.ls-list::-webkit-scrollbar {
  width: 5px;
}
.ls-list::-webkit-scrollbar-track {
  background: transparent;
}
.ls-list::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.28);
  border-radius: 999px;
}
.ls-list:hover::-webkit-scrollbar-thumb {
  background: rgba(155, 161, 173, 0.46);
}
.ls-item {
  align-items: center;
  border-bottom: 1px solid #f1f3f5;
  display: flex;
  gap: 0.75rem;
  justify-content: space-between;
  padding: 0.1rem 0;
  font-size: 14px;
}
.ls-item:last-child {
  border-bottom: 0;
}
.ls-name {
  font-weight: 500;
  min-width: 0;
}
.ls-ratio {
  color: var(--danger);
  flex-shrink: 0;
  font-weight: 700;
}
.ls-empty {
  padding: 2rem 0;
}
</style>
