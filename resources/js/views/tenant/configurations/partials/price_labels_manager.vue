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
                <div class="col-sm-6 col-lg-4 mb-3 mb-3">
                    <div
                        class="price-label-card border rounded p-3"
                    >
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <label class="control-label m-0">Precio Principal</label>
                                <svg
                                    v-if="isMainDefault"
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="icon icon-tabler icons-tabler-filled icon-tabler-star default-star is-default"
                                    style="margin-top: -2px;"
                                    title="Ya es el precio por defecto"
                                    @click="!loading && setMainAsDefault()"
                                ><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-star default-star"
                                    style="margin-top: -2px;"
                                    title="Marcar como por defecto"
                                    @click="!loading && setMainAsDefault()"
                                ><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" /></svg>
                            </div>
                            <el-tag type="primary" v-if="isMainDefault">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-star me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg> 
                                POR DEFECTO
                            </el-tag>
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
                    class="col-sm-6 col-lg-4 mb-3"
                >
                    <div
                        class="price-label-card border rounded p-3"
                    >
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <strong class="text-muted small">
                                    {{ label.position }}
                                </strong>
                                <svg
                                    v-if="label.is_default"
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="icon icon-tabler icons-tabler-filled icon-tabler-star default-star is-default"
                                    :class="{ 'is-disabled': !label.is_active }"
                                    style="margin-top: -2px;"
                                    :title="!label.is_active
                                        ? 'Activa la etiqueta para poder marcarla como por defecto'
                                        : 'Quitar como por defecto'"
                                    @click="!loading && label.is_active && toggleDefault(label)"
                                ><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-star default-star"
                                    :class="{ 'is-disabled': !label.is_active }"
                                    style="margin-top: -2px;"
                                    :title="!label.is_active
                                        ? 'Activa la etiqueta para poder marcarla como por defecto'
                                        : 'Marcar como por defecto'"
                                    @click="!loading && label.is_active && toggleDefault(label)"
                                ><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" /></svg>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <el-tag type="primary" v-if="label.is_default">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-star me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg> 
                                    POR DEFECTO
                                </el-tag>
                                <el-switch
                                    v-model="label.is_active"
                                    @change="updateLabel(label)"
                                    :disabled="loading"
                                ></el-switch>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <el-input
                                v-model="label.label"
                                placeholder="Ej: Precio Mayorista"
                                maxlength="50"
                                show-word-limit
                                @blur="updateLabel(label)"
                                :disabled="loading"
                            >
                            </el-input>

                            <button
                                v-if="!label.is_original"
                                class="btn btn-sm btn-danger ms-2"
                                @click="confirmDelete(label)"
                                :disabled="loading"
                                title="Eliminar etiqueta"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </div>

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
    computed: {
        isMainDefault() {
            return !this.labels.some(l => l.is_default);
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
                    if (response.data.data) {
                        label.is_default = !!response.data.data.is_default;
                    }
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

        async setMainAsDefault() {
            if (this.isMainDefault) return;

            this.loading = true;
            try {
                const response = await this.$http.post('/price-labels/clear-default');

                if (response.data.success) {
                    this.labels.forEach(l => { l.is_default = false; });
                    this.$message.success(response.data.message);
                    this.$emit('labels-updated');
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                this.$message.error('Error al actualizar la etiqueta por defecto');
                console.error(error);
            } finally {
                this.loading = false;
            }
        },

        async toggleDefault(label) {
            if (!label.id) return;

            this.loading = true;
            try {
                const response = await this.$http.post(`/price-labels/${label.id}/set-default`);

                if (response.data.success) {
                    const updated = response.data.data;
                    this.labels.forEach(l => {
                        l.is_default = (l.id === updated.id) ? updated.is_default : false;
                    });
                    this.$message.success(response.data.message);
                    this.$emit('labels-updated');
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                this.$message.error('Error al actualizar la etiqueta por defecto');
                console.error(error);
            } finally {
                this.loading = false;
            }
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

.default-star {
    cursor: pointer;
    color: #c0c4cc;
    transition: color 0.2s;
}
.default-star:hover {
    color: #f7ba2a;
}
.default-star.is-default {
    color: #f7ba2a;
}
.default-star.is-disabled {
    color: #e4e7ed;
    cursor: not-allowed;
}
.default-star.is-disabled:hover {
    color: #e4e7ed;
}
</style>
