<template>
  <div>
    <div class="page-header pe-0">
      <h2>
        <a href="/list-setting">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-medicine-syrup">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M8 21h8a1 1 0 0 0 1 -1v-10a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v10a1 1 0 0 0 1 1z" />
            <path d="M10 14h4" />
            <path d="M12 12v4" />
            <path d="M10 7v-3a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3" />
          </svg>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Campos Personalizados</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <button v-if="can_add_new_product" class="btn btn-custom btn-sm mt-2 me-2" type="button"
          @click.prevent="clickCreate()">
          <i class="fa fa-plus-circle"></i> Nuevo
        </button>
      </div>
    </div>
    <div class="card mb-0 tab-content-default row-new">
      <div class="card-body">
        <div v-loading="loading_submit" class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Requerido</th>
                <th>Factura/Boleta</th>
                <th>Nota de venta</th>
                <th>Guía de remisión</th>
                <th>Pedido</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in records" :key="row.id">
                <td>{{ row.name }}</td>
                <td>
                  {{ getTypeLabel(row.type) }}
                </td>
                <td>
                  {{ row.required ? 'Sí' : 'No' }}
                </td>
                <td>
                  <el-switch
                    v-model="row.enabled_for_documents"
                    @change="toggleDocumentStatus(row.id, 'documents', row.enabled_for_documents)">
                  </el-switch>
                </td>
                <td>
                  <el-switch
                    v-model="row.enabled_for_sale_notes"
                    @change="toggleDocumentStatus(row.id, 'sale_notes', row.enabled_for_sale_notes)">
                  </el-switch>
                </td>
                <td>
                  <el-switch
                    v-model="row.enabled_for_dispatches"
                    @change="toggleDocumentStatus(row.id, 'dispatches', row.enabled_for_dispatches)">
                  </el-switch>
                </td>
                <td>
                  <el-switch
                    v-model="row.enabled_for_order_notes"
                    @change="toggleDocumentStatus(row.id, 'order_notes', row.enabled_for_order_notes)">
                  </el-switch>
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button
                      class="btn btn-sm btn-info"
                      type="button"
                      @click.prevent="clickEdit(row)">
                      <i class="fa fa-edit"></i>
                    </button>
                    <button
                      class="btn btn-sm btn-danger"
                      type="button"
                      @click.prevent="clickDelete(row.id)">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="records.length === 0">
                <td colspan="8" class="text-center text-muted">
                  No hay campos personalizados registrados
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginación Element.io -->
        <div v-if="total > 0" class="">
          <el-pagination
            :current-page="current_page"
            :page-size="per_page"
            :page-sizes="[10, 20, 50, 100]"
            :total="total"
            layout="total, prev, pager, next"
            @current-change="handleCurrentChange"
            @size-change="handleSizeChange">
          </el-pagination>
        </div>
      </div>
    </div>

    <!-- Componente de formulario -->
    <custom-field-form
      :showDialog.sync="showDialog"
      @reload-data="getRecords"
    >
    </custom-field-form>
  </div>
</template>

<script>
import CustomFieldForm from './form.vue'
import { deletable } from '@mixins/deletable'

export default {
  name: 'CustomFieldsIndex',
  components: {
    CustomFieldForm,
  },
  mixins: [deletable],
  data() {
    return {
      loading_submit: false,
      showDialog: false,
      records: [],
      current_page: 1,
      per_page: 20,
      total: 0,
      last_page: 1,
      can_add_new_product: true,
      fieldTypes: {
        'text': 'Texto',
        'number': 'Número',
        'textarea': 'Área de texto',
        'select': 'Selección',
        'checkbox': 'Casilla de verificación',
        'date': 'Fecha',
      }
    }
  },
  computed: {
  },
  created() {
    this.loadConfiguration()
    this.$eventHub.$on('reloadData', () => {
      this.getRecords()
    })
  },
  mounted() {
    this.getRecords()
  },
  methods: {
    loadConfiguration() {
      this.$http.get('/configurations/record').then((response) => {
        this.$setStorage('configuration', response.data)
      })
    },
    getRecords() {
      this.loading_submit = true
      this.$http.post('/configurations/custom-fields/records', {
        column: 'name',
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
    goToPage(page) {
      if (page >= 1 && page <= this.last_page) {
        this.current_page = page
        this.getRecords()
      }
    },
    handleCurrentChange(page) {
      this.current_page = page
      this.getRecords()
    },
    handleSizeChange(size) {
      this.per_page = size
      this.current_page = 1
      this.getRecords()
    },
    clickCreate() {
      this.showDialog = true
    },
    clickEdit(row) {
      this.$root.$refs.CustomFieldForm.edit(row)
    },
    clickDelete(id) {
      this.$confirm('¿Deseas eliminar este campo personalizado?', 'Confirmación', {
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
      }).then(() => {
        this.destroy(`/configurations/custom-fields/destroy/${id}`).then(() => {
          this.$message.success('Campo personalizado eliminado con éxito')
          this.getRecords()
        })
      }).catch(() => {})
    },
    toggleDocumentStatus(id, document, currentStatus) {
      this.loading_submit = true
      this.$http.post('/configurations/custom-fields/update-document-status', {
        id: id,
        document: document,
        enabled: currentStatus
      }).then(() => {
        this.$message.success('Estado actualizado con éxito')
        this.loading_submit = false
      }).catch(() => {
        this.$message.error('Error al actualizar el estado')
        this.loading_submit = false
        // Revertir el cambio
        this.getRecords()
      })
    },
    getTypeLabel(type) {
      return this.fieldTypes[type] || type
    }
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

.badge {
  padding: 0.35em 0.65em;
  font-size: 0.85em;
}

.badge-info {
  background-color: #5bc0de;
  color: white;
}
</style>