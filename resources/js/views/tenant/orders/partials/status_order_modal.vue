<template>
    <el-dialog
        title="Gestionar Estados de Pedido"
        :visible.sync="showDialog"
        width="500px"
        @close="close"
    >
        <!-- Agregar nuevo estado -->
        <div class="d-flex mb-3">
            <el-input 
                v-model="newDescription" 
                placeholder="Nuevo estado..."
                class="me-2"
            ></el-input>
            <el-button 
                type="primary" 
                @click="store"
                :loading="loading"
            >
                <i class="fa fa-plus"></i> Agregar
            </el-button>
        </div>

        <!-- Lista de estados -->
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="status in statuses" :key="status.id">
                    <td>
                        <template v-if="editingId === status.id">
                            <el-input 
                                v-model="editDescription" 
                                size="small"
                            ></el-input>
                        </template>
                        <template v-else>
                            {{ status.description }}
                        </template>
                    </td>
                    <td class="text-end">
                        <template v-if="editingId === status.id">
                            <el-button 
                                type="success" 
                                size="mini"
                                @click="update(status)"
                            >
                                <i class="fa fa-check"></i>
                            </el-button>
                            <el-button 
                                size="mini"
                                @click="cancelEdit"
                            >
                                <i class="fa fa-times"></i>
                            </el-button>
                        </template>
                        <template v-else>
                            <el-button 
                                type="warning" 
                                size="mini"
                                @click="startEdit(status)"
                                :disabled="isCritical(status.id)"
                                :class="{ 'is-disabled-custom': isCritical(status.id) }"
                            >
                                <i class="fa fa-edit"></i>
                            </el-button>
                            <el-button 
                                type="danger" 
                                size="mini"
                                @click="destroy(status.id)"
                                :disabled="isCritical(status.id)"
                                :class="{ 'is-disabled-custom': isCritical(status.id) }"
                            >
                                <i class="fa fa-trash"></i>
                            </el-button>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </el-dialog>
</template>
<style scoped>
.is-disabled-custom {
    background-color: #c0c4cc !important;
    border-color: #c0c4cc !important;
    color: #fff !important;
}
</style>
<script>
export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            statuses: [],
            newDescription: '',
            editingId: null,
            editDescription: '',
            loading: false
        }
    },
    watch: {
        showDialog(val) {
            if (val) this.getRecords()
        }
    },
    methods: {
        isCritical(id) {
            return [2, 3].includes(id)  // IDs de Pago verificado y Despachado
        },
        getRecords() {
            this.$http.get('/statusOrder/records').then(response => {
                this.statuses = response.data
            })
        },
        store() {
            if (!this.newDescription.trim()) 
                return this.$message.error('Ingrese una descripción')
            
            this.loading = true
            this.$http.post('/statusOrder/store', { 
                description: this.newDescription 
            }).then(response => {
                if (response.data.success) {
                    this.$message.success(response.data.message)
                    this.newDescription = ''
                    this.getRecords()
                }
            }).finally(() => {
                this.loading = false
            })
        },
        startEdit(status) {
            this.editingId = status.id
            this.editDescription = status.description
        },
        cancelEdit() {
            this.editingId = null
            this.editDescription = ''
        },
        update(status) {
            if (!this.editDescription.trim()) 
                return this.$message.error('Ingrese una descripción')

            this.$http.put(`/statusOrder/update/${status.id}`, { 
                description: this.editDescription 
            }).then(response => {
                if (response.data.success) {
                    this.$message.success(response.data.message)
                    this.cancelEdit()
                    this.getRecords()
                }
            })
        },
        destroy(id) {
            this.$confirm('¿Desea eliminar este estado?', 'Eliminar', {
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning'
            }).then(() => {
                this.$http.delete(`/statusOrder/destroy/${id}`).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.getRecords()
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
            }).catch(() => {})
        },
        close() {
            this.cancelEdit()
            this.$emit('update:showDialog', false)
            this.$eventHub.$emit('statusesUpdated')
        }
    }
}
</script>