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
        <!-- Nombre -->
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
                placeholder="Ej: Color, Talla, Modelo..."
              ></el-input>
              <small
                v-if="errors.name"
                class="form-control-feedback"
                v-text="errors.name[0]">
              </small>
            </div>
          </div>
        </div>

        <!-- Tipo de campo -->
        <div class="row">
          <div class="col-md-6">
            <div
              :class="{'has-danger': errors.type}"
              class="form-group">
              <label class="control-label">
                Tipo de Campo <span class="text-danger">*</span>
              </label>
              <el-select
                v-model="form_data.type"
                placeholder="Selecciona un tipo">
                <el-option label="Texto" value="text"></el-option>
                <el-option label="Número" value="number"></el-option>
                <el-option label="Área de texto" value="textarea"></el-option>
                <el-option label="Selección" value="select"></el-option>
                <el-option label="Casilla de verificación" value="checkbox"></el-option>
                <el-option label="Fecha" value="date"></el-option>
              </el-select>
              <small
                v-if="errors.type"
                class="form-control-feedback"
                v-text="errors.type[0]">
              </small>
            </div>
          </div>

          <!-- Requerido -->
          <div class="col-md-6">
            <div class="form-group">
              <label class="mt-3">
                Requerido
              </label>
              <div class="mt-2">
                <el-switch
                  v-model="form_data.required">
                </el-switch>
                <span class="ms-2">{{ form_data.required ? 'Sí' : 'No' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Opciones para select/checkbox -->
        <div v-if="form_data.type === 'select' || form_data.type === 'checkbox'" class="row">
          <div class="col-md-12">
            <div
              :class="{'has-danger': errors.options}"
              class="form-group">
              <label class="control-label">
                Opciones <span class="text-danger">*</span>
              </label>
              <el-input
                v-model="optionsText"
                type="textarea"
                placeholder="Opción 1&#10;Opción 2&#10;Opción 3&#10;&#10;O con comas: Opción 1, Opción 2, Opción 3"
                rows="4"
              ></el-input>
              <small class="form-text text-muted">
                Ingresa opciones separadas por líneas nuevas o comas
              </small>
              <small
                v-if="errors.options"
                class="form-control-feedback"
                v-text="errors.options[0]">
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
          <span v-if="loading_submit" class="spinner-border spinner-border-sm me-2"></span>
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
      titleDialog: 'Nuevo campo personalizado',
      errors: {},
      form_data: {
        id: null,
        name: '',
        type: 'text',
        required: false,
        options: null,
      },
      optionsText: '',
    }
  },
  created() {
    this.$root.$refs.CustomFieldForm = this
  },
  methods: {
    create() {
      // Resetear errores cuando se abre el diálogo
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
        type: 'text',
        required: false,
        options: null,
      }
      this.optionsText = ''
      this.errors = {}
    },
    edit(row) {
      this.form_data = {
        id: row.id,
        name: row.name,
        type: row.type,
        required: row.required,
        options: row.options,
      }

      // Convertir opciones a texto para el formulario
      if (row.options && Array.isArray(row.options)) {
        // Si el array tiene un solo elemento con comas, es el formato antiguo
        if (row.options.length === 1 && row.options[0].includes(',')) {
          this.optionsText = row.options[0]
            .split(',')
            .map(opt => opt.trim())
            .join('\n')
        } else {
          this.optionsText = row.options.join('\n')
        }
      } else {
        this.optionsText = ''
      }

      this.titleDialog = 'Editar campo personalizado'
      this.$emit('update:showDialog', true)
    },
    submit() {
      this.loading_submit = true
      this.errors = {}

      // Preparar datos
      const data = {
        ...this.form_data
      }

      // Convertir opciones de texto a array si es necesario
      if (this.form_data.type === 'select' || this.form_data.type === 'checkbox') {
        if (this.optionsText.trim()) {
          // Dividir por newline o coma (más flexible)
          data.options = this.optionsText
            .split(/[\n,]/)
            .map(option => option.trim())
            .filter(option => option.length > 0)
        } else {
          data.options = null
        }
      }

      this.$http.post('/configurations/custom-fields/store', data)
        .then((response) => {
          this.$message.success(response.data.message)
          this.loading_submit = false
          this.close()
          this.$emit('reload-data')
        })
        .catch((error) => {
          this.loading_submit = false
          if (error.response && error.response.data && error.response.data.errors) {
            this.errors = error.response.data.errors
            this.$message.error('Por favor verifica los errores en el formulario')
          } else {
            this.$message.error('Error al guardar el campo personalizado')
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

.has-danger .el-input__inner,
.has-danger .el-textarea__inner {
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

.ms-2 {
  margin-left: 0.5rem;
}

.spinner-border {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  vertical-align: text-bottom;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner-border 0.75s linear infinite;
}

.spinner-border-sm {
  width: 0.875rem;
  height: 0.875rem;
  border-width: 0.2em;
}

@keyframes spinner-border {
  to {
    transform: rotate(360deg);
  }
}
</style>
