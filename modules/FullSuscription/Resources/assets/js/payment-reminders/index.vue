<template>
  <div class="card-body bg-white" style="border-radius: 12px;">

    <!-- Cabecera -->
    <div class="page-header pe-0">
      <h2>
        <a href="/full-suscription/payment-reminders">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active">
          <span>Recordatorios de Pago</span>
        </li>
      </ol>
      <div class="right-wrapper pull-right"></div>
    </div>

    <!-- Configuración general -->
    <div class="card border mb-4 card-body bg-white" style="border-color:#dde3ed !important;">
      <div class="">
        <div class="row">
          <div class="col-12 col-md-6">
            <div :class="{ 'has-danger': errors.before_day_creation_suscription_order }" class="form-group mb-0">
              <label class="control-label font-weight-semibold mb-1 h6">
                Días de anticipación para crear la orden
              </label>
              <p class="text-muted mb-3">
                Cuántos días antes del vencimiento se genera automáticamente la orden de pago.
              </p>
              <div class="d-flex align-items-center" style="gap:8px;">
                <el-input
                  v-model="form.before_day_creation_suscription_order"
                  placeholder="Ej: 3"
                  type="number"
                  min="1"
                  style="width:240px;"
                />
                <el-button type="primary" size="small" @click="submit">
                Guardar
                </el-button>
              </div>
              <small
                v-if="errors.before_day_creation_suscription_order"
                class="invalid-feedback d-block"
                v-text="errors.before_day_creation_suscription_order[0]"
              ></small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reglas -->
    <div class="card border mb-4">

          <!-- Header -->
          <div class="card-header classic-header d-flex justify-content-between align-items-center">
            <div>
              <span class="control-label font-weight-semibold mb-1 h6">Programación de notificaciones automáticas</span>
              <p class="text-muted mb-0" style="font-weight: normal;">
                Configura reglas para enviar recordatorios automáticos a tus clientes antes o después del vencimiento de su pago.
              </p>
            </div>
          </div>

          <div class="card-body p-0 bg-white">

            <el-table
              v-if="reminders.length > 0"
              :data="reminders"
              style="width:100%;"
            >
              <!-- # -->
              <el-table-column label="#" width="50" align="center">
                <template slot-scope="{ $index }">
                  <span class="row-number">{{ $index + 1 }}</span>
                </template>
              </el-table-column>

              <!-- Canal -->
              <el-table-column label="Canal" width="200">
                <template slot-scope="{ row }">
                  <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-white">
                        <i :class="channelIcon(row.channel)"></i>
                      </span>
                    </div>
                    <el-select v-model="row.channel" size="small" style="flex:1; min-width:0;">
                      <el-option value="email"    label="Correo"></el-option>
                      <el-option value="whatsapp" label="WhatsApp"></el-option>
                    </el-select>
                  </div>
                </template>
              </el-table-column>

              <!-- Días -->
              <el-table-column label="Días" width="90" align="center">
                <template slot-scope="{ row }">
                  <el-input
                    v-if="row.timingType !== 'same_day'"
                    v-model="row.days"
                    type="number"
                    min="1"
                    max="365"
                    size="small"
                  />
                  <el-input v-else value="—" size="small" disabled />
                </template>
              </el-table-column>

              <!-- Momento -->
              <el-table-column label="Momento de envío">
                <template slot-scope="{ row }">
                  <el-select
                    v-model="row.timingType"
                    size="small"
                    style="width:100%;"
                  >
                    <el-option value="before"   label="Días antes de vencer"></el-option>
                    <el-option value="same_day" label="El mismo día del vencimiento"></el-option>
                    <el-option value="after"    label="Días después de vencer"></el-option>
                  </el-select>
                </template>
              </el-table-column>

              <!-- Hora -->
              <el-table-column label="Hora" width="130" align="center">
                <template slot-scope="{ row }">
                  <el-input v-model="row.time" type="time" size="small" />
                </template>
              </el-table-column>

              <!-- Eliminar -->
              <el-table-column label="" width="55" align="center">
                <template slot-scope="{ $index }">
                  <button
                    class="btn btn-sm btn-link text-danger p-0"
                    title="Eliminar"
                    @click="removeReminder($index)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </template>
              </el-table-column>

            </el-table>

            <!-- Botón añadir -->
            <div class="p-3">
              <button
                class="btn btn-outline-secondary w-100 py-2"
                style="border-style:dashed;"
                @click="addReminder"
              >
                <i class="fas fa-plus mr-2"></i>
                Añadir regla
              </button>
            </div>

          </div>

          <!-- Footer -->
          <div class="card-footer d-flex justify-content-end align-items-center">
            <el-button type="primary" size="small" :loading="loading" @click="saveConfiguration">
              {{ loading ? 'Guardando...' : 'Guardar configuración' }}
            </el-button>
          </div>

        </div>
      </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      reminders: [],
      form: {
        before_day_creation_suscription_order: 1,
      },
      errors: {},
      loading: false,
      resource: '/full-suscription',
    }
  },

  created() {
    this.loadConfiguration()
    this.getRecords()
  },

  methods: {
    getRecords() {
      this.$http.get(`${this.resource}/payment-reminders/records`)
        .then(response => {
          this.reminders = response.data.map(r => ({
            id:         r.id,
            channel:    r.shipping_medium,
            timingType: r.reminder_type,
            days:       r.reminder_days,
            time:       r.reminder_time,
          }))
        })
    },

    channelIcon(channel) {
      const icons = {
        email:    'far fa-envelope text-secondary',
        whatsapp: 'fab fa-whatsapp text-success',
        sms:      'fas fa-comment text-primary',
      }
      return icons[channel] || 'far fa-bell text-muted'
    },

    addReminder() {
      this.reminders.push({ id: null, channel: 'email', timingType: 'before', days: 1, time: '09:00' })
    },

    async removeReminder(index) {
      const id = this.reminders[index].id
      if (!id) {
        this.reminders.splice(index, 1)
        return
      }
      await this.$http.delete(`${this.resource}/payment-reminders/${id}`)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message)
            this.getRecords()
          }
        })
    },

    updateTimingType(id, value) {
      const reminder = this.reminders.find(r => r.id === id)
      if (!reminder) return
      this.$set(reminder, 'timingType', value)
      if (value === 'same_day')      this.$set(reminder, 'days', 0)
      else if (reminder.days === 0)  this.$set(reminder, 'days', 1)
    },

    discardChanges() {
      this.reminders = []
    },

    loadConfiguration() {
      this.$http.get(`${this.resource}/payment-reminders/configuration`)
        .then(response => {
          this.form.before_day_creation_suscription_order = response.data.before_day_creation_suscription_order
        })
    },

    saveConfiguration() {
      this.loading = true
      const reminders = this.reminders.map(r => ({
        id:              r.id,
        shipping_medium: r.channel,
        reminder_type:   r.timingType,
        reminder_days:   r.days,
        reminder_time:   r.time,
      }))

      if (reminders.length === 0) {
        this.loading = false
        this.$message.info('No hay cambios para guardar')
        return
      }

      this.$http.post(`${this.resource}/payment-reminders`, { reminders })
        .then(response => { if (response.data.success) this.getRecords() })
        .catch(() => {})
        .finally(() => { this.loading = false })
    },

    submit() {
      this.$http.post(`${this.resource}/payment-reminders/save-configuration`, {
        before_day_creation_suscription_order: this.form.before_day_creation_suscription_order,
      }).then(response => {
        if (response.data.success) this.$message.success(response.data.message)
      }).catch(error => {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {}
        } else {
          this.$message.error('Ocurrió un error al guardar la configuración')
        }
      })
    },
  },
}
</script>

<style scoped>
.classic-header {
  background: #f7f8fa;
  border-bottom: 0px solid #dde3ed;
  font-size: 13px;
  font-weight: 600;
  color: #344563;
  padding: 10px 16px;
}

.row-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #eef1f7;
  color: #7a8aab;
  font-size: 11px;
  font-weight: 700;
}

.empty-icon {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: #f0f2f7;
  display: flex;
  align-items: center;
  justify-content: center;
}

::v-deep .el-table td.el-table__cell,
::v-deep .el-table th.el-table__cell.is-leaf {
    border-bottom: none !important;
}
::v-deep .el-table::before,
::v-deep .el-table::after {
    display: none !important;
}

.card-footer {
    border-top: 0px solid var(--black-highlight);
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
