<template>
  <el-dialog
    :title="titleDialog"
    :visible="showDialog"
    @close="close"
    @open="create"
    width="600px">
    <form
      autocomplete="off"
      @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <div class="col-md-12">
            <div
              :class="{'has-danger': errors.name}"
              class="form-group">
              <label class="control-label">
                Nombre <span class="text-danger">*</span>
              </label>
              <el-input
                v-model="form_data.name"
                placeholder="Ej: Mi ERP, n8n producción..."
              ></el-input>
              <small
                v-if="errors.name"
                class="form-control-feedback"
                v-text="errors.name[0]">
              </small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div
              :class="{'has-danger': errors.url}"
              class="form-group">
              <label class="control-label">
                URL de destino <span class="text-danger">*</span>
              </label>
              <el-input
                v-model="form_data.url"
                placeholder="https://midominio.com/webhooks/facturacion"
              ></el-input>
              <small class="form-text text-muted">
                Recibirá un POST con JSON por cada evento seleccionado. Se recomienda HTTPS.
              </small>
              <small
                v-if="errors.url"
                class="form-control-feedback"
                v-text="errors.url[0]">
              </small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div
              :class="{'has-danger': errors.events}"
              class="form-group">
              <label class="control-label">
                Eventos a escuchar <span class="text-danger">*</span>
              </label>
              <el-checkbox-group v-model="form_data.events" class="events-group">
                <el-checkbox
                  v-for="event in availableEvents"
                  :key="event.value"
                  :label="event.value">
                  {{ event.label }} <code>{{ event.value }}</code>
                </el-checkbox>
              </el-checkbox-group>
              <small
                v-if="errors.events"
                class="form-control-feedback"
                v-text="errors.events[0]">
              </small>
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions text-end mt-4">
        <button
          :disabled="loading_submit"
          class="btn btn-sm btn-secondary"
          type="button"
          @click="close">
          Cancelar
        </button>
        <button
          :disabled="loading_submit"
          class="btn btn-sm btn-primary ms-2"
          type="submit">
          {{ loading_submit ? 'Guardando...' : 'Guardar' }}
        </button>
      </div>
    </form>
  </el-dialog>
</template>

<script>
export default {
  props: {
    showDialog: Boolean,
  },
  data() {
    return {
      loading_submit: false,
      titleDialog: 'Nuevo webhook',
      errors: {},
      availableEvents: [],
      form_data: {
        id: null,
        name: '',
        url: '',
        events: [],
      },
    }
  },
  created() {
    this.$root.$refs.WebhookForm = this
    this.getAvailableEvents()
  },
  methods: {
    getAvailableEvents() {
      this.$http.get('/configurations/webhooks/events').then((response) => {
        this.availableEvents = response.data.data
      })
    },
    create() {
      this.errors = {}
    },
    close() {
      this.$emit('update:showDialog', false)
      this.clearForm()
    },
    clearForm() {
      this.form_data = {
        id: null,
        name: '',
        url: '',
        events: [],
      }
      this.titleDialog = 'Nuevo webhook'
      this.errors = {}
    },
    edit(row) {
      this.form_data = {
        id: row.id,
        name: row.name,
        url: row.url,
        events: [...row.events],
      }
      this.titleDialog = 'Editar webhook'
      this.$emit('update:showDialog', true)
    },
    submit() {
      this.loading_submit = true
      this.errors = {}

      const isNew = !this.form_data.id

      this.$http.post('/configurations/webhooks/store', this.form_data)
        .then((response) => {
          this.$message.success(response.data.message)
          this.loading_submit = false
          this.close()
          this.$emit('reload-data')
          if (isNew && response.data.secret) {
            this.$emit('created-secret', response.data.secret)
          }
        })
        .catch((error) => {
          this.loading_submit = false
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors
            this.$message.error('Por favor verifica los errores en el formulario')
          } else {
            this.$message.error('Error al guardar el webhook')
          }
        })
    }
  }
}
</script>

<style scoped>
.form-body {
  padding: 20px 0;
}

.text-danger {
  color: #dc3545;
}

.has-danger .el-input__inner {
  border-color: #dc3545 !important;
}

.form-control-feedback {
  display: block;
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 5px;
}

.form-text {
  display: block;
  margin-top: 5px;
  font-size: 0.875rem;
}

.text-muted {
  color: #6c757d;
}

.events-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 8px;
}

.events-group code {
  font-size: 0.8em;
  color: #6c757d;
}

.ms-2 {
  margin-left: 0.5rem;
}
</style>
