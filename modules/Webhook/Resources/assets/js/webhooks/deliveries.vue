<template>
  <div>
    <div class="row mb-3 filters-row">
      <div class="col-md-4">
        <el-select v-model="filters.subscription_id" placeholder="Todas las suscripciones" clearable @change="getRecords(1)">
          <el-option v-for="sub in subscriptions" :key="sub.id" :label="sub.name" :value="sub.id"></el-option>
        </el-select>
      </div>
      <div class="col-md-3">
        <el-select v-model="filters.status" placeholder="Todos los estados" clearable @change="getRecords(1)">
          <el-option label="Exitoso" value="success"></el-option>
          <el-option label="Pendiente" value="pending"></el-option>
          <el-option label="Fallido" value="failed"></el-option>
        </el-select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-sm btn-secondary" type="button" @click.prevent="getRecords()">
          <i class="fa fa-refresh"></i> Actualizar
        </button>
      </div>
    </div>

    <div v-loading="loading" class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Evento</th>
            <th>Suscripción</th>
            <th>Estado</th>
            <th>Intentos</th>
            <th>Código HTTP</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in records" :key="row.id">
            <td>{{ row.created_at }}</td>
            <td><code>{{ row.event }}</code></td>
            <td>{{ row.subscription_name }}</td>
            <td>
              <el-tag v-if="row.status === 'success'" size="mini" type="success">Exitoso</el-tag>
              <el-tag v-else-if="row.status === 'failed'" size="mini" type="danger">Fallido</el-tag>
              <el-tag v-else size="mini" type="warning">Pendiente</el-tag>
            </td>
            <td>{{ row.attempts }}</td>
            <td>{{ row.response_code || '-' }}</td>
            <td>
              <div class="btn-group" role="group">
                <button class="btn btn-sm btn-info" type="button" title="Ver detalle"
                  @click.prevent="clickDetail(row)">
                  <i class="fa fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning" type="button" title="Reenviar"
                  :disabled="row.status === 'pending'"
                  @click.prevent="clickResend(row.id)">
                  <i class="fa fa-paper-plane"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="records.length === 0">
            <td colspan="7" class="text-center text-muted">
              No hay entregas registradas
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="total > 0">
      <el-pagination
        :current-page="current_page"
        :page-size="per_page"
        :total="total"
        layout="total, prev, pager, next"
        @current-change="handleCurrentChange">
      </el-pagination>
    </div>

    <el-dialog title="Detalle de la entrega" :visible.sync="showDetailDialog" width="700px">
      <template v-if="detail">
        <p class="mb-1"><strong>Evento:</strong> <code>{{ detail.event }}</code></p>
        <p class="mb-1"><strong>ID del evento:</strong> <code>{{ detail.event_id }}</code></p>
        <p class="mb-1"><strong>URL:</strong> {{ detail.subscription_url }}</p>
        <p class="mb-1" v-if="detail.sent_at"><strong>Último envío:</strong> {{ detail.sent_at }}</p>
        <p class="mb-1" v-if="detail.error_message"><strong>Error:</strong> {{ detail.error_message }}</p>

        <label class="control-label mt-2"><strong>Payload enviado</strong></label>
        <pre class="payload-box">{{ formatJson(detail.payload) }}</pre>

        <template v-if="detail.response_body">
          <label class="control-label"><strong>Respuesta del receptor ({{ detail.response_code }})</strong></label>
          <pre class="payload-box">{{ detail.response_body }}</pre>
        </template>
      </template>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'WebhookDeliveries',
  props: {
    subscriptions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      loading: false,
      records: [],
      current_page: 1,
      per_page: 20,
      total: 0,
      filters: {
        subscription_id: null,
        status: null,
      },
      showDetailDialog: false,
      detail: null,
    }
  },
  mounted() {
    this.getRecords()
  },
  methods: {
    getRecords(page) {
      if (page) {
        this.current_page = page
      }
      this.loading = true
      this.$http.post('/configurations/webhooks/deliveries/records', {
        page: this.current_page,
        subscription_id: this.filters.subscription_id,
        status: this.filters.status,
      }).then((response) => {
        this.records = response.data.data
        this.current_page = response.data.current_page
        this.per_page = response.data.per_page
        this.total = response.data.total
        this.loading = false
      }).catch(() => {
        this.loading = false
      })
    },
    handleCurrentChange(page) {
      this.current_page = page
      this.getRecords()
    },
    clickDetail(row) {
      this.detail = row
      this.showDetailDialog = true
    },
    clickResend(id) {
      this.$http.post(`/configurations/webhooks/deliveries/${id}/resend`)
        .then((response) => {
          if (response.data.success) {
            this.$message.success(response.data.message)
          } else {
            this.$message.error(response.data.message)
          }
          this.getRecords()
        })
        .catch(() => {
          this.$message.error('Error al reenviar la entrega')
        })
    },
    formatJson(payload) {
      try {
        return JSON.stringify(payload, null, 2)
      } catch (e) {
        return payload
      }
    },
  }
}
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

.btn-group {
  display: flex;
  gap: 5px;
}

.filters-row {
  display: flex;
  gap: 10px;
  align-items: center;
}

.payload-box {
  background: #f6f8fa;
  border: 1px solid #e1e4e8;
  border-radius: 4px;
  padding: 10px;
  max-height: 280px;
  overflow: auto;
  font-size: 0.8rem;
  white-space: pre-wrap;
  word-break: break-all;
}

.mb-1 {
  margin-bottom: 0.25rem;
}
</style>
