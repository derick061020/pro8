<template>
  <el-dialog
    title="Añadir documento relacionado"
    :close-on-click-modal="false"
    :visible="showDialog"
    @close="close"
    @open="create">
    <el-form 
      :model="form"
      ref="ReferenceDocumentForm"
      class="row"
      @submit.prevent="submit">
      
      <div class="col-12">
        <div v-if="dispatch_type_id == '09'" class="form-group position-relative" :class="{ 'has-error': !!errors.name }">
          <label class="control-label mb-0">Nombre de Empresa<span class="text-danger"> *</span></label>
          <el-input
            v-model="form.name"
            @input="clearError('name')"
            @blur="validateField('name')">
          </el-input>
          <div v-if="errors.name" class="el-form-item__error">{{ errors.name }}</div>
        </div>
      </div>
      
      <div class="col-6">
        <div class="form-group position-relative" :class="{ 'has-error': !!errors.document_type_id }">
          <label class="control-label mb-0">Tipo de documento<span class="text-danger"> *</span></label>
          <el-select
            v-model="form.document_type_id"
            placeholder="Tipo de documento"
            @change="validateField('document_type_id')">
            <el-option
              v-for="(row, index) in document_types"
              :key="index"
              :label="row.description"
              :value="row.id">
            </el-option>
          </el-select>
          <div v-if="errors.document_type_id" class="el-form-item__error">{{ errors.document_type_id }}</div>
        </div>
      </div>
      
      <div class="col-6">
        <div class="form-group position-relative" :class="{ 'has-error': !!errors.ruc }">
          <label class="control-label mb-0">Número RUC<span class="text-danger"> *</span></label>
          <el-input
            v-model="form.ruc"
            :maxlength="11"
            @input="clearError('ruc')"
            @blur="validateField('ruc')"></el-input>
          <div v-if="errors.ruc" class="el-form-item__error">{{ errors.ruc }}</div>
        </div>
      </div>
      
      <div class="col-6">
        <div class="form-group position-relative" :class="{ 'has-error': !!errors.serie }">
          <label class="control-label mb-0">Serie de documento<span class="text-danger"> *</span></label>
          <el-input
            v-model="form.serie"
            :maxlength="4"
            @input="clearError('serie')"
            @blur="validateField('serie')"></el-input>
          <div v-if="errors.serie" class="el-form-item__error">{{ errors.serie }}</div>
        </div>
      </div>
      
      <div class="col-6">
        <div class="form-group position-relative" :class="{ 'has-error': !!errors.number }">
          <label class="control-label mb-0">Numero de documento<span class="text-danger"> *</span></label>
          <el-input
            v-model="form.number"
            @input="clearError('number')"
            @blur="validateField('number')"></el-input>
          <div v-if="errors.number" class="el-form-item__error">{{ errors.number }}</div>
        </div>
      </div>
      
    </el-form>
    <span slot="footer" class="dialog-footer">
      <el-button class="second-buton" @click="close">Cancelar</el-button>
      <el-button type="primary" @click="submit">Agregar</el-button>
    </span>
  </el-dialog>
</template>
<style>
.el-form-item__content {
  margin-left: 0px !important;
}

.has-error .el-input__inner,
.has-error .el-textarea__inner,
.has-error .el-select .el-input__inner {
  border-color: #f56c6c;
}
</style>
<script>
export default {
  props: ['showDialog', 'dispatch_type_id', 'document_data', 'supplierData'],
  data() {
    return {
      loading_dialog: false,
      errors: {},
      form: {
        document_type_id: '09',
        serie: '',
        number: '',
        ruc: '',
        name: null,
      },
      document_types: [],
    }
  },
  mounted() {
    if (this.dispatch_type_id == '09') {
      this.document_types = [
        { id: '01', description: 'Factura' },
        { id: '03', description: 'Boleta' },
      ]
    } else {
      this.document_types = [
        { id: '09', description: 'Guía de remisión remitente' }
      ]
    }
    if (Object.keys(this.$props.document_data).length > 0) {
      this.setFirstDocumentReference()
    }
  },
  watch: {
    showDialog: function(data) {
      this.initSupplierDataDocument();
    },
  },
  methods: {
    clearError(field) {
      if (this.errors && Object.prototype.hasOwnProperty.call(this.errors, field)) {
        this.$delete(this.errors, field)
      }
    },
    validateField(field) {
      const requiredMessage = 'Completar este campo'

      const value = (this.form[field] ?? '').toString().trim()
      const setError = (message) => this.$set(this.errors, field, message)

      this.clearError(field)

      if (field === 'name') {
        if (this.dispatch_type_id === '09' && value.length === 0) {
          setError(requiredMessage)
          return false
        }
        return true
      }

      if (field === 'document_type_id') {
        if (value.length === 0) {
          setError(requiredMessage)
          return false
        }
        return true
      }

      if (field === 'serie') {
        if (value.length === 0) {
          setError(requiredMessage)
          return false
        }
        if (value.length !== 4) {
          setError('La serie está compuesta por 4 dígitos')
          return false
        }
        return true
      }

      if (field === 'number') {
        if (value.length === 0) {
          setError(requiredMessage)
          return false
        }
        return true
      }

      if (field === 'ruc') {
        if (value.length === 0) {
          setError(requiredMessage)
          return false
        }
        if (value.length !== 11) {
          setError('El RUC debe contener 11 dígitos')
          return false
        }
        return true
      }

      return true
    },
    validateAll() {
      const fields = ['document_type_id', 'ruc', 'serie', 'number']
      if (this.dispatch_type_id === '09') fields.unshift('name')
      return fields.map((f) => this.validateField(f)).every(Boolean)
    },
    close() {
      this.initForm()
      this.$emit('update:showDialog', false)
    },
    create() {
      this.initForm()
    },
    submit() {
      if (!this.validateAll()) return

      // formato listo para xml
      let row = {
        document_type: this.document_types.find(e => e.id === this.form.document_type_id),
        number: this.form.serie + '-' + this.form.number,
        customer: this.form.ruc,
        name: this.form.name
      }
      this.$emit('addReferenceDocument', row)
      this.close()
    },
    initForm() {
      this.form = {
        document_type_id: '',
        serie: '',
        number: '',
        ruc: '',
        name: null,
      }
      this.errors = {}
      this.initSupplierDataDocument()
    },
    setFirstDocumentReference() {
      if (this.document_data.document_type_id == '01' || this.document_data.document_type_id == '03') {
        let row = {
          document_type: this.document_types.find(e => e.id === this.document_data.document_type_id),
          number: this.document_data.serie + '-' + this.document_data.number,
          customer: this.document_data.customer,
          name: this.document_data.name ? this.document_data.name : null,
        }
        this.$emit('addReferenceDocument', row)
      }
    },
    async initSupplierDataDocument() {
      if (this.supplierData && this.supplierData.identity_document_type_id) {
        const { name, number } = this.supplierData;
        this.form.ruc = number;
        this.form.name = name

        this.clearError('ruc')
        this.clearError('name')
      }
    },
  }
}
</script>