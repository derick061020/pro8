<template>
    <el-dialog
        :visible="showDialog"
        @close="close"
        title="Gestionar Etiquetas de Precios"
        width="70%"
        append-to-body
        top="5vh"
    >
        <div class="price-labels-manager">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="price-label-card border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="control-label m-0">Etiqueta Precio Principal</label>                            
                        </div>                        
                        <el-input
                            v-model="localPrice1Label"
                            @change="updateMainLabel"
                            placeholder="Precio principal"
                            :disabled="loading"
                        ></el-input> 
                    </div>
                </div>
                <div
                    v-for="(label, index) in labels"
                    :key="label.id || `new-${index}`"
                    class="col-md-4 mb-3"
                >
                    <div class="price-label-card border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-muted small">
                                {{ label.position }}
                            </strong>
                            <el-switch
                                v-model="label.is_active"
                                @change="updateLabel(label)"
                                :disabled="loading"
                            ></el-switch>
                        </div>

                        <el-input
                            v-model="label.label"
                            placeholder="Ej: Precio Mayorista"
                            maxlength="50"
                            show-word-limit
                            @blur="updateLabel(label)"
                            :disabled="loading"
                        >
                            <template #append>
                                <el-button
                                    v-if="!label.is_original"
                                    @click="confirmDelete(label)"
                                    type="danger"
                                    icon="el-icon-delete"
                                    :disabled="loading"
                                    title="Eliminar etiqueta"
                                ></el-button>
                            </template>
                        </el-input>

                        <div v-if="!label.is_active" class="mt-1">
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Inactivo (no se mostrará en ventas)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Botón añadir nueva etiqueta -->
                <div class="col-md-4 mb-3">
                    <div class="price-label-card border rounded p-3 border-dashed d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <el-button
                            type="primary"
                            icon="el-icon-plus"
                            @click="addNewLabel"
                            :disabled="loading"
                        >
                            Añadir Etiqueta
                        </el-button>
                    </div>
                </div>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button class="me-2" @click="close" :disabled="loading">Cerrar</el-button>
            <!-- <el-button type="primary" @click="updateMainLabel" :loading="loading">Actualizar</el-button> -->
        </span>
    </el-dialog>
</template>

<script>
export default {
    name: 'PriceLabelsManager',
    props: {
        showDialog: {
            type: Boolean,
            default: false
        },
        configuration: {
            type: Object,
            required: false,
            default: null
        }
    },
    data() {
        return {
            labels: [],
            loading: false,
            localPrice1Label: '',
        }
    },
    watch: {
        showDialog(newVal) {
            if (newVal) {
                this.loadLabels();
            }
        }
    },
    methods: {
        async loadLabels() {
            this.loading = true;
            try {
                const response = await this.$http.get('/price-labels');
                this.labels = response.data.data;
                // initialize localPrice1Label from passed configuration if available
                if (this.configuration && this.configuration.price1_label !== undefined) {
                    this.localPrice1Label = this.configuration.price1_label || '';
                }
            } catch (error) {
                this.$message.error('Error al cargar las etiquetas de precios');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async addNewLabel() {
            this.loading = true;
            try {
                const nextPosition = this.labels.length + 1;
                const response = await this.$http.post('/price-labels', {
                    label: `Precio ${nextPosition}`,
                    is_active: true
                });

                if (response.data.success) {
                    this.labels.push(response.data.data);
                    this.$message.success('Etiqueta creada correctamente');
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                this.$message.error('Error al crear la etiqueta');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async updateLabel(label) {
            if (!label.id) return; // Es una etiqueta nueva sin guardar

            this.loading = true;
            try {
                const response = await this.$http.put(`/price-labels/${label.id}`, {
                    label: label.label,
                    is_active: label.is_active
                });

                if (response.data.success) {
                    this.$message.success('Etiqueta actualizada correctamente');
                    // Emitir evento para actualizar configuración en el padre
                    this.$emit('labels-updated');
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                this.$message.error('Error al actualizar la etiqueta');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async updateMainLabel() {
            // Guardar en la configuración el nuevo label para 'Precio principal'
            this.loading = true;
            try {
                // El endpoint de configuraciones espera el objeto completo de configuración.
                let payload;
                if (this.configuration && typeof this.configuration === 'object') {
                    payload = Object.assign({}, this.configuration, { price1_label: this.localPrice1Label });
                } else {
                    // Fallback: enviar solo el campo si no se pasó la configuración completa
                    payload = { price1_label: this.localPrice1Label };
                }

                const response = await this.$http.post('/configurations', payload);
                if (response.data && response.data.success) {
                    this.$message.success('Etiqueta principal actualizada');
                    this.$emit('labels-updated');
                } else {
                    this.$message.error(response.data.message || 'Error al actualizar');
                }
            } catch (error) {
                this.$message.error('Error al actualizar la etiqueta principal');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        confirmDelete(label) {
            this.$confirm(
                `¿Desea eliminar la etiqueta "${label.label}"?`,
                'Eliminar etiqueta',
                {
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }
            ).then(() => {
                this.deleteLabel(label);
            }).catch(() => {
                // Cancelado
            });
        },

        async deleteLabel(label) {
            this.loading = true;
            try {
                const response = await this.$http.delete(`/price-labels/${label.id}`);

                if (response.data.success) {
                    const index = this.labels.findIndex(l => l.id === label.id);
                    if (index > -1) {
                        this.labels.splice(index, 1);
                    }
                    // Reorganizar positions
                    this.labels.forEach((l, idx) => {
                        l.position = idx + 1;
                    });
                    this.$message.success('Etiqueta eliminada correctamente');
                    this.$emit('labels-updated');
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                const message = error.response?.data?.message || 'Error al eliminar la etiqueta';
                this.$message.error(message);
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        close() {
            this.$emit('update:showDialog', false);
        }
    }
}
</script>

<style scoped>
.price-labels-manager {
    min-height: 200px;
}

.price-label-card {
    background-color: #f9f9f9;
    transition: all 0.3s;
}

.price-label-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.border-dashed {
    border-style: dashed !important;
    border-color: #d3d3d3 !important;
    background-color: #fafafa;
    cursor: pointer;
}

.border-dashed:hover {
    border-color: #409EFF !important;
    background-color: #f0f7ff;
}

.price-label-card.border-primary {
    border-color: #409EFF !important;
    box-shadow: 0 0 0 0.12rem rgba(64,158,255,0.12);
    background-color: #ffffff;
}
</style>
