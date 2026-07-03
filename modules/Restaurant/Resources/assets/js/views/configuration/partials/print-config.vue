<template>
  <div class="card-body h-100 buhoprinter-config">
    <div class="d-flex align-items-center">
        <div class="tag-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" /></svg>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-2 ms-3 w-100">
            <div class="d-flex flex-column">
                <h3 class="text-primary fw-bold m-0">Integración con BuhoPrinter</h3>
                <div class="pt-1">
                  <el-tag type="primary" class="mx-0">Servicio externo de impresión</el-tag>
                </div>
            </div>
        </div>
    </div>
    <!-- Encabezado y switch principal -->
    <div class="row py-3">
      <div class="col-12 info-text">
        <span class="text-muted">
          Activa esta opción para conectar la empresa con el servicio de impresión local BuhoPrinter.
          Al activar, el sistema detectará automáticamente el agente en esta red e intentará registrar
          las impresoras disponibles.
        </span>
      </div>
    </div>

    <div class="row info-text mb-3" v-loading="loading">
      <!-- Switch de activación -->
      <div class="col-md-3">
        <label class="control-label d-block">Activar impresión con BuhoPrinter</label>
        <el-switch
          v-model="form.printer_enabled"
          active-text="Activo"
          inactive-text="Inactivo"
          @change="onTogglePrinterEnabled">
        </el-switch>
      </div>

      <!-- Switch de impresión local -->
      <div class="col-md-3" v-if="form.printer_enabled">
        <label class="control-label d-block">
          Impresión local
          <el-tooltip
            content="Al activar, solo se podrán enviar órdenes de impresión desde la misma red pública donde está instalado BuhoPrinter."
            effect="dark"
            placement="top">
            <i class="fa fa-info-circle text-muted ml-1"></i>
          </el-tooltip>
        </label>
        <el-switch
          v-model="form.print_local_enabled"
          active-text="Activo"
          inactive-text="Inactivo">
        </el-switch>
      </div>

      <!-- Switch de dirección -->
      <div class="col-md-3" v-if="form.printer_enabled">
        <label class="control-label d-block">
          Destino de impresión
          <el-tooltip
            content="Directo: El sistema enviará las órdenes de impresión directamente a BuhoPrinter, debe estar instalado en el dispositivo actual. Centralizado: Las órdenes de impresión se enviarán a través del api, lo que permite imprimir desde cualquier dispositivo, solo un equipo con BuhoPrinter instalado se encargará de recibir y procesar las órdenes."
            effect="dark"
            placement="top">
            <i class="fa fa-info-circle text-muted ml-1"></i>
          </el-tooltip>
        </label>
        <el-switch
          v-model="form.print_destination"
          active-text="Centralizado"
          inactive-text="Directo"
          @change="onTogglePrintDestination">
        </el-switch>
      </div>

      <!-- Selector de impresora para modo Directo -->
      <template  v-if="form.printer_enabled">
        <div class="col-md-3" v-if="!form.print_destination">
          <label class="control-label d-block">
            Impresora para impresión directa
            <el-tooltip
              content="Cada equipo puede elegir su propia impresora. La selección se guarda localmente en este navegador."
              effect="dark"
              placement="top">
              <i class="fa fa-info-circle text-muted ml-1"></i>
            </el-tooltip>
          </label>
          <template v-if="printers.length > 0">
            <el-select
              v-model="directPrinterName"
              placeholder="Seleccione una impresora"
              @change="saveDirectPrinter"
              class="w-100">
              <el-option
                v-for="p in printers"
                :key="p.name"
                :value="p.name"
                :label="p.name">
                <span>{{ p.name }}</span>
              </el-option>
            </el-select>
            <small class="text-muted d-block mt-1">
              Guardada en este equipo/navegador.
            </small>
          </template>
          <div v-else class="alert alert-warning mt-1 mb-0 py-2">
            <i class="fa fa-exclamation-triangle mr-1"></i>
            No hay impresoras sincronizadas. Usa <b>"Verificar y actualizar"</b> para detectarlas.
          </div>
        </div>
      </template>
    </div>

    <!-- Indicador de estado (solo informativo) -->
    <div class="row mb-3" v-if="form.printer_enabled">
      <div class="col-12 px-0">
        <div class="alert m-0" :class="statusAlertClass" role="alert">
          <i :class="statusIconClass" class="me-2"></i>
          <b>Estado de última verificación:</b>
          <span v-if="checking"> Verificando conexión con BuhoPrinter...</span>
          <span v-else-if="liveStatus === 'connected'"> BuhoPrinter respondió correctamente en la última verificación.</span>
          <span v-else-if="liveStatus === 'disconnected'"> No se pudo contactar a BuhoPrinter en la última verificación.</span>
          <span v-else-if="form.printer_status === 'connected'"> Última verificación guardada: conexión exitosa.</span>
          <span v-else-if="form.printer_status === 'disconnected'"> Última verificación guardada: sin conexión.</span>
          <span v-else> Sin verificación previa registrada.</span>
          <br>
          <small class="text-muted">
            Este indicador refleja el resultado de la última vez que se verificó la conexión desde este panel,
            no el estado en tiempo real del servicio de impresión.
          </small>
        </div>
      </div>
    </div>

    <!-- Lista de impresoras registradas -->
    <template v-if="form.printer_enabled && printers.length > 0">
      <div class="row mb-3">
        <div class="col-12 px-0">
          <h5 class="text-muted mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" /></svg>
            Impresoras registradas ({{ printers.length }})
          </h5>
          <div class="table-responsive">
            <table class="table table-sm table-hover">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Predeterminada</th>
                  <th>Última sincronización</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in printers" :key="p.name">
                  <td>{{ p.name }}</td>
                  <td>
                    <el-tag v-if="p.is_default" type="success" size="mini">Sí</el-tag>
                    <span v-else class="text-muted">—</span>
                  </td>
                  <td class="text-muted">{{ p.last_seen_at | formatDate }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>

    <!-- Mensaje cuando no hay impresoras aún -->
    <div class="row mb-3" v-else-if="form.printer_enabled && printers.length === 0 && !checking">
      <div class="col-12 px-0">
        <div class="alert alert-info m-0">
          <i class="fa fa-info-circle mr-1"></i>
          No hay impresoras registradas. Activa la opción y usa
          <b>"Verificar y actualizar"</b> para detectar las impresoras disponibles.
        </div>
      </div>
    </div>

    <!-- Botones de acción -->
    <div class="row">
      <div class="col-12 px-0 d-flex">
        <el-button
          type="success"
          :loading="saving"
          :disabled="loading"
          @click="saveConfig">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
          Guardar configuración
        </el-button>
        <el-button
          type="primary"
          class="ms-2"
          :loading="checking"
          :disabled="!form.printer_enabled || loading"
          @click="checkAndSync">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
          Verificar y actualizar
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
import { buhoprinter } from '@mixins/buhoprinter'

export default {
  name: 'PrintConfig',

  mixins: [buhoprinter],

  filters: {
    formatDate(value) {
      if (!value) return '—'
      return value.toString().replace('T', ' ').substring(0, 16)
    }
  },

  data() {
    return {
      resource: 'restaurant',
      form: {
        printer_enabled:         false,
        printer_status:          null,
        printer_public_ip:       null,
        print_local_enabled:     false,
        print_destination:       false,
      },
      printers: [],
      directPrinterName: null,
      liveStatus: null,
      checking: false,
      saving: false,
      loading: false,
    }
  },

  computed: {
    statusAlertClass() {
      const status = this.liveStatus || this.form.printer_status
      if (status === 'connected')    return 'alert-success'
      if (status === 'disconnected') return 'alert-danger'
      return 'alert-secondary'
    },

    statusIconClass() {
      const status = this.liveStatus || this.form.printer_status
      if (status === 'connected')    return 'fa fa-check-circle text-success'
      if (status === 'disconnected') return 'fa fa-times-circle text-danger'
      return 'fa fa-question-circle text-muted'
    },
  },

  mounted() {
    this.loadConfig()
  },

  methods: {
    async loadConfig() {
      this.loading = true

      try {
        const { data } = await this.$http.get(`/${this.resource}/printers/config`)
        if (data.success) {
          const d = data.data
          this.form.printer_enabled     = d.printer_enabled
          this.form.printer_status      = d.printer_status
          this.form.printer_public_ip   = d.printer_public_ip
          this.form.print_local_enabled = d.print_local_enabled
          this.form.print_destination   = d.print_destination
          this.printers                 = d.printers || []
          this.initDirectPrinter()
        }
      } catch (error) {
        console.error('Error al cargar config de impresión:', error)
      } finally {
        this.loading = false
      }
    },

    async onTogglePrintDestination() {
      if (!this.form.print_destination) {
        this.initDirectPrinter()
      }
      await this.saveConfig(false)
    },

    async onTogglePrinterEnabled(value) {
      if (value) {
        await this.checkAndSync()
      } else {
        await this.saveConfig(false)
      }
    },

    async checkAndSync(silent = false) {
      this.checking = true
      this.liveStatus = null

      try {
        await this.startConnectionBuho()

        if (!this.isBuhoActive) {
          throw new Error('BuhoPrinter no está disponible en esta red')
        }

        this.liveStatus = 'connected'

        const clientPublicIp = await this.fetchClientPublicIp()
        await this.$http.post(`/${this.resource}/printers/status`, {
          printer_status:    'connected',
          printer_public_ip: clientPublicIp || null,
        })
        this.form.printer_status    = 'connected'
        this.form.printer_public_ip = clientPublicIp || null

        const buhoUrl = this.getBuhoBaseUrl()
        const configRes = await this.$http.post(`/${this.resource}/printers/configure`, {
          buhoprinter_host: buhoUrl,
        })

        if (configRes.data.success) {
          await fetch(`${buhoUrl}/configure`, {
            method : 'POST',
            headers: { 'Content-Type': 'text/plain' },
            body   : configRes.data.payload,
          })
        }

        const rawPrinters = await this.getBuhoPrintersWithDefaults()

        const syncRes = await this.$http.post(`/${this.resource}/printers/sync`, {
          printers: rawPrinters
        })

        if (syncRes.data.success) {
          this.printers = syncRes.data.printers
          this.initDirectPrinter()
          if (!silent) {
            this.$message.success('BuhoPrinter conectado. Impresoras actualizadas.')
          }
        }

      } catch (error) {
        this.liveStatus = 'disconnected'

        await this.$http.post(`/${this.resource}/printers/status`, { printer_status: 'disconnected' })
          .catch(() => {})
        this.form.printer_status = 'disconnected'

        if (!silent) {
          this.$message.error('No se pudo conectar con BuhoPrinter. Verifica que el servicio esté corriendo en esta PC.')
        }
        console.error('Error al verificar BuhoPrinter:', error)
      } finally {
        this.checking = false
      }
    },

    async saveConfig(showMessage = true) {
      this.saving = true
      try {
        const res = await this.$http.post(`/${this.resource}/printers/config`, {
          printer_enabled:     this.form.printer_enabled,
          print_local_enabled: this.form.print_local_enabled,
          print_destination:   this.form.print_destination,
        })

        if (res.data.success && showMessage) {
          this.$message.success(res.data.message)
        }
      } catch (error) {
        this.$message.error('Error al guardar la configuración de impresión.')
        console.error(error)
      } finally {
        this.saving = false
      }
    },

    /**
     * Inicializa la impresora para modo Directo.
     * Prioridad: localStorage → impresora predeterminada → primera de la lista.
     */
    initDirectPrinter() {
      const LS_KEY = 'buho_direct_printer_name'
      const saved = localStorage.getItem(LS_KEY)

      if (saved && this.printers.find(p => p.name === saved)) {
        this.directPrinterName = saved
        return
      }

      const defaultPrinter = this.printers.find(p => p.is_default)
      this.directPrinterName = defaultPrinter?.name || this.printers[0]?.name || null

      if (this.directPrinterName) {
        localStorage.setItem(LS_KEY, this.directPrinterName)
      }
    },

    saveDirectPrinter() {
      if (this.directPrinterName) {
        localStorage.setItem('buho_direct_printer_name', this.directPrinterName)
      }
    },
  }
}
</script>
