<template>
  <section class="card card-dashboard ss-panel">
    <div class="card-body">
      <h5 class="ss-title m-0">Estado SUNAT</h5>

      <div class="ss-tiles mt-3">
        <div class="ss-tile">
          <span class="ss-value is-accepted">{{ accepted | ssNum }}</span>
          <small class="text-muted">Aceptados</small>
        </div>
        <div class="ss-tile">
          <span class="ss-value is-pending">{{ pending | ssNum }}</span>
          <small class="text-muted">Pendientes</small>
        </div>
        <div class="ss-tile">
          <span class="ss-value is-rejected">{{ rejected | ssNum }}</span>
          <small class="text-muted">Rechazados</small>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "SunatStatus",
  props: {
    filters: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      accepted: 0,
      pending: 0,
      rejected: 0,
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
      this.$http.get("/dashboard/sunat-status", { params: this.filters || {} }).then((response) => {
        const data = response.data;
        this.accepted = data.accepted || 0;
        this.pending = data.pending || 0;
        this.rejected = data.rejected || 0;
      });
    },
  },
  filters: {
    ssNum(value) {
      return (Number(value) || 0).toLocaleString("es-PE");
    },
  },
};
</script>

<style scoped>
.ss-panel {
  height: 100%;
  margin-bottom: 0 !important;
}
.ss-title {
  font-size: 1.05rem;
  font-weight: 600;
  margin-bottom: 1rem;
}
.ss-tiles {
  display: flex;
  gap: 0.75rem;
}
.ss-tile {
  flex: 1 1 0;
  text-align: center;
  padding: 1rem 0.5rem;
  border-radius: 0.5rem;
  background: color-mix(in srgb, var(--primary) 4%, transparent);
}
.ss-value {
  display: block;
  font-size: 1.7rem;
  font-weight: 700;
  line-height: 1.1;
}
.ss-value.is-accepted {
  color: var(--success);
}
.ss-value.is-pending {
  color: var(--warning);
}
.ss-value.is-rejected {
  color: var(--danger);
}
</style>
