<template>
  <div>
    <div class="page-header pe-0">
      <h2>
        <a href="/list-settings">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M9 15h-4.5a4.5 4.5 0 1 1 .81 -8.92a4 4 0 0 1 7.66 -.83a4.5 4.5 0 0 1 4.85 6.27" />
            <path d="M12 19a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
            <path d="M15 16v-6a2 2 0 0 1 2 -2h1" />
          </svg>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Webhooks</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <button v-if="activeTab === 'subscriptions'" class="btn btn-custom btn-sm mt-2 me-2" type="button"
          @click.prevent="clickCreate()">
          <i class="fa fa-plus-circle"></i> Nuevo
        </button>
      </div>
    </div>
    <div class="card mb-0 tab-content-default row-new">
      <div class="card-body">
        <el-tabs v-model="activeTab">
          <el-tab-pane label="Suscripciones" name="subscriptions">
            <div v-loading="loading_submit" class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>URL de destino</th>
                    <th>Eventos</th>
                    <th>Estado</th>
                    <th>Fallos consecutivos</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in records" :key="row.id">
                    <td>{{ row.name }}</td>
                    <td class="url-cell">{{ row.url }}</td>
                    <td>
                      <el-tag v-for="label in row.event_labels" :key="label" size="mini" class="me-1 mb-1">
                        {{ label }}
                      </el-tag>
                    </td>
                    <td>
                      <el-tooltip v-if="!row.is_active && row.disabled_at" :content="'Desactivado el ' + row.disabled_at" placement="top">
                        <el-tag size="mini" type="danger">Inactivo</el-tag>
                      </el-tooltip>
                      <el-tag v-else-if="!row.is_active" size="mini" type="danger">Inactivo</el-tag>
                      <el-tag v-else size="mini" type="success">Activo</el-tag>
                    </td>
                    <td>{{ row.consecutive_failures }}</td>
                    <td>
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-secondary" type="button" :title="row.is_active ? 'Desactivar' : 'Activar'"
                          @click.prevent="clickToggle(row.id)">
                          <i :class="row.is_active ? 'fa fa-pause' : 'fa fa-play'"></i>
                        </button>
                        <button class="btn btn-sm btn-info" type="button" title="Editar"
                          @click.prevent="clickEdit(row)">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" type="button" title="Eliminar"
                          @click.prevent="clickDelete(row.id)">
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="records.length === 0">
                    <td colspan="6" class="text-center text-muted">
                      No hay webhooks registrados
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
          </el-tab-pane>

          <el-tab-pane label="Entregas" name="deliveries" lazy>
            <webhook-deliveries :subscriptions="records"></webhook-deliveries>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>

    <webhook-form
      :showDialog.sync="showDialog"
      @reload-data="getRecords"
      @created-secret="showSecret"
    >
    </webhook-form>

    <!-- El secret se muestra una sola vez, al crear -->
    <el-dialog title="Secreto del webhook" :visible.sync="showSecretDialog" width="560px">
      <p class="mb-2">
        Guarda este secreto: se usa para verificar la firma <code>X-Webhook-Signature</code>
        de cada envío y <strong>no se volverá a mostrar completo</strong>.
      </p>
      <el-input :value="createdSecret" readonly>
        <template slot="append">
          <el-button @click="copySecret">Copiar</el-button>
        </template>
      </el-input>
      <div class="text-end mt-3">
        <button class="btn btn-sm btn-primary" type="button" @click="showSecretDialog = false">Entendido</button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import WebhookForm from './form.vue'
import WebhookDeliveries from './deliveries.vue'

export default {
  name: 'WebhooksIndex',
  components: {
    WebhookForm,
    WebhookDeliveries,
  },
  data() {
    return {
      loading_submit: false,
      showDialog: false,
      showSecretDialog: false,
      createdSecret: '',
      activeTab: 'subscriptions',
      records: [],
      current_page: 1,
      per_page: 20,
      total: 0,
      last_page: 1,
    }
  },
  mounted() {
    this.getRecords()
  },
  methods: {
    getRecords() {
      this.loading_submit = true
      this.$http.post('/configurations/webhooks/records', {
        value: ''
      }).then((response) => {
        this.records = response.data.data
        this.current_page = response.data.current_page
        this.per_page = response.data.per_page
        this.total = response.data.total
        this.last_page = response.data.last_page
        this.loading_submit = false
      }).catch(() => {
        this.loading_submit = false
      })
    },
    handleCurrentChange(page) {
      this.current_page = page
      this.getRecords()
    },
    clickCreate() {
      this.showDialog = true
    },
    clickEdit(row) {
      this.$root.$refs.WebhookForm.edit(row)
    },
    clickToggle(id) {
      this.$http.post(`/configurations/webhooks/toggle/${id}`)
        .then((response) => {
          this.$message.success(response.data.message)
          this.getRecords()
        })
        .catch(() => {
          this.$message.error('Error al cambiar el estado del webhook')
        })
    },
    clickDelete(id) {
      this.$confirm('¿Deseas eliminar este webhook? También se eliminará su historial de entregas.', 'Confirmación', {
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
      }).then(() => {
        this.$http.delete(`/configurations/webhooks/destroy/${id}`).then(() => {
          this.$message.success('Webhook eliminado con éxito')
          this.getRecords()
        })
      }).catch(() => {})
    },
    showSecret(secret) {
      this.createdSecret = secret
      this.showSecretDialog = true
    },
    copySecret() {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(this.createdSecret)
        this.$message.success('Secreto copiado al portapapeles')
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

.url-cell {
  max-width: 280px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.me-1 {
  margin-right: 0.25rem;
}

.mb-1 {
  margin-bottom: 0.25rem;
}
</style>
