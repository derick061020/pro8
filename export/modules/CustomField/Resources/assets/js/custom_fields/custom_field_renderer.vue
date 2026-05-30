<template>
  <div v-if="customFields.length > 0" class="row mx-0 px-0">
    <div class="col-12">
      <hr>
      <h5 class="mb-0">Campos Personalizados</h5>
    </div>

    <div v-for="field in customFields" :key="field.id" class="col-md-4">
      <!-- Campo de Texto -->
      <div v-if="field.type === 'text'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-input
          v-model="data[field.slug]"
          :placeholder="`Ingresa ${field.name}`"
          :required="field.required"
        ></el-input>
      </div>

      <!-- Campo de Número -->
      <div v-else-if="field.type === 'number'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-input
          v-model.number="data[field.slug]"
          type="number"
          :placeholder="`Ingresa ${field.name}`"
          :required="field.required"
        ></el-input>
      </div>

      <!-- Campo de Área de Texto -->
      <div v-else-if="field.type === 'textarea'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-input
          v-model="data[field.slug]"
          type="textarea"
          :rows="3"
          :placeholder="`Ingresa ${field.name}`"
          :required="field.required"
        ></el-input>
      </div>

      <!-- Campo de Selección -->
      <div v-else-if="field.type === 'select'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-select
          v-model="data[field.slug]"
          :placeholder="`Selecciona ${field.name}`"
          :required="field.required"
          clearable>
          <el-option
            v-for="option in normalizeOptions(field.options)"
            :key="option"
            :label="option"
            :value="option">
          </el-option>
        </el-select>
      </div>

      <!-- Campo de Casilla de Verificación -->
      <div v-else-if="field.type === 'checkbox'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-checkbox-group
          v-model="data[field.slug]"
          :required="field.required">
          <el-checkbox
            v-for="option in normalizeOptions(field.options)"
            :key="option"
            :label="option"
            :value="option">
          </el-checkbox>
        </el-checkbox-group>
      </div>

      <!-- Campo de Fecha -->
      <div v-else-if="field.type === 'date'" class="form-group">
        <label class="control-label">
          {{ field.name }}
          <span v-if="field.required" class="text-danger">*</span>
        </label>
        <el-date-picker
          v-model="data[field.slug]"
          type="date"
          :placeholder="`Selecciona ${field.name}`"
          :required="field.required"
          format="yyyy-MM-dd"
          value-format="yyyy-MM-dd">
        </el-date-picker>
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Componente para renderizar campos personalizados en documentos
 *
 * Uso:
 * <custom-fields-renderer
 *   :document-type="'documents'"
 *   :form-data.sync="document_data"
 * ></custom-fields-renderer>
 *
 * Props:
 * - documentType: 'documents' | 'sale_notes' | 'dispatches' | 'order_notes'
 * - formData: objeto reactivo donde se guardan los valores
 */
export default {
  name: 'CustomFieldsRenderer',
  props: {
    // Tipo de documento para filtrar campos habilitados
    documentType: {
      type: String,
      required: true,
      validator: function(value) {
        return ['documents', 'sale_notes', 'dispatches', 'order_notes'].includes(value)
      }
    },
    // Datos del formulario (v-model.sync)
    formData: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      customFields: [],
      loading: false,
      validationErrors: [], // Almacena errores de validación
    }
  },
  computed: {
    // Alias para facilitar el acceso a formData
    data: {
      get() {
        return this.formData
      },
      set(value) {
        this.$emit('update:formData', value)
      }
    }
  },
  created() {
    this.loadCustomFields()
  },
  methods: {
    /**
     * Cargar campos personalizados habilitados para este tipo de documento
     */
    loadCustomFields() {
      this.loading = true

      this.$http.post('/configurations/custom-fields/records', {
        column: 'name',
        value: ''
      }).then((response) => {
        // Filtrar solo los campos habilitados para este tipo de documento
        const enabledField = this.getEnabledFieldName()
        this.customFields = response.data.data
          .filter(field => field[enabledField])
          .sort((a, b) => a.order - b.order)

        // Inicializar valores en formData si no existen
        this.customFields.forEach(field => {
          if (!(field.slug in this.formData)) {
            // Inicializar con valor vacío según el tipo
            if (field.type === 'checkbox') {
              this.$set(this.formData, field.slug, [])
            } else {
              this.$set(this.formData, field.slug, null)
            }
          }
        })

        this.loading = false
      }).catch((error) => {
        console.error('Error cargando campos personalizados:', error)
        this.loading = false
      })
    },

    /**
     * Obtener el nombre del campo booleano según el tipo de documento
     * @returns {string}
     */
    getEnabledFieldName() {
      const map = {
        'documents': 'enabled_for_documents',
        'sale_notes': 'enabled_for_sale_notes',
        'dispatches': 'enabled_for_dispatches',
        'order_notes': 'enabled_for_order_notes'
      }
      return map[this.documentType] || 'enabled_for_documents'
    },

    /**
     * Normalizar opciones: soporta tanto array como string con comas/newlines
     * Útil para datos guardados antes de la actualización
     * @param {Array|String} options
     * @returns {Array}
     */
    normalizeOptions(options) {
      if (!options) return []

      // Si es un array
      if (Array.isArray(options)) {
        // Si contiene un solo elemento con comas, dividirlo
        if (options.length === 1 && typeof options[0] === 'string' && options[0].includes(',')) {
          return options[0]
            .split(',')
            .map(opt => opt.trim())
            .filter(opt => opt.length > 0)
        }
        return options
      }

      // Si es un string, dividir por coma o newline
      if (typeof options === 'string') {
        return options
          .split(/[\n,]/)
          .map(opt => opt.trim())
          .filter(opt => opt.length > 0)
      }

      return []
    },

    /**
     * Validar que todos los campos requeridos estén rellenos
     * @returns {Object} { valid: boolean, errors: Array }
     */
    validateRequiredFields() {
      const errors = []

      this.customFields.forEach(field => {
        if (field.required) {
          const value = this.formData[field.slug]

          // Validar si está vacío
          if (value === null || value === undefined || value === '') {
            errors.push(`${field.name} es requerido`)
          }

          // Validar arrays (checkbox)
          if (Array.isArray(value) && value.length === 0) {
            errors.push(`${field.name} es requerido`)
          }
        }
      })

      this.validationErrors = errors
      return {
        valid: errors.length === 0,
        errors: errors
      }
    }
  }
}
</script>