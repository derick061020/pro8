<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="open"
        :close-on-click-modal="false"
        width="560px"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">

                <!-- Código del cupón -->
                <div class="form-group" :class="{'has-danger': errors.code}">
                    <label class="control-label">CÓDIGO DEL CUPÓN</label>
                    <div class="input-group">
                        <el-input
                            v-model="form.code"
                            placeholder="Ej: VERANO20"
                            style="flex: 1;"
                            :maxlength="20"
                        ></el-input>
                        <el-button
                            type="warning"
                            plain
                            class="ms-2"
                            style="white-space: nowrap;"
                            @click.prevent="generateCode"
                        >
                            <i class="fa fa-bolt me-1"></i> Generar
                        </el-button>
                    </div>
                    <small class="form-control-feedback" v-if="errors.code" v-text="errors.code[0]"></small>
                </div>

                <div class="row py-0 align-items-center">
                  <!-- Tipo y valor del descuento -->
                  <div class="form-group col-9 px-0" :class="{'has-danger': errors.amount || errors.type}">
                      <label class="control-label">TIPO Y VALOR DEL DESCUENTO</label>
                      <div class="d-flex align-items-center gap-2">
                          <el-input
                              v-model.number="form.amount"
                              type="number"
                              min="0"
                              step="0.01"
                              :placeholder="form.type === 'percentage' ? '% 0' : 'S/ 0'"
                              style="width: 130px;"
                          ></el-input>
                          <el-radio-group v-model="form.type" size="small">
                              <el-radio-button label="percentage">% Porcentaje</el-radio-button>
                              <el-radio-button label="fixed">S/ Fijo</el-radio-button>
                          </el-radio-group>
                      </div>
                      <small class="form-control-feedback" v-if="errors.amount" v-text="errors.amount[0]"></small>
                      <small class="form-control-feedback" v-if="errors.type" v-text="errors.type[0]"></small>
                  </div>

                  <!-- Envío gratis -->
                  <div class="form-group mb-0 col-3">
                      <el-checkbox v-model="form.free_shipping">
                          <span class="fw-semibold">Envío gratis</span>
                      </el-checkbox>
                  </div>
                </div>

                <!-- Límites de compra (collapse) -->
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="mb-0 d-flex align-items-center gap-2" style="cursor:pointer;">
                            <el-checkbox v-model="form.has_purchase_limits">Límites de compra</el-checkbox>
                        </label>
                        <small class="text-muted">Sin límite si desactivado</small>
                    </div>
                    <div v-if="form.has_purchase_limits" class="row mt-3">
                        <div class="col-6">
                            <div class="form-group mb-0" :class="{'has-danger': errors.min_amount}">
                                <label class="control-label">MONTO MÍNIMO</label>
                                <el-input
                                    v-model.number="form.min_amount"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="S/0.00"
                                ></el-input>
                                <small class="form-control-feedback" v-if="errors.min_amount" v-text="errors.min_amount[0]"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0" :class="{'has-danger': errors.max_amount}">
                                <label class="control-label">MONTO MÁXIMO</label>
                                <el-input
                                    v-model.number="form.max_amount"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="S/0.00"
                                ></el-input>
                                <small class="form-control-feedback" v-if="errors.max_amount" v-text="errors.max_amount[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Límites de uso y vigencia (collapse) -->
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="mb-0 d-flex align-items-center gap-2" style="cursor:pointer;">
                            <el-checkbox v-model="form.has_usage_limits">Límites de uso y vigencia</el-checkbox>
                        </label>
                        <small class="text-muted">Ilimitado si desactivado</small>
                    </div>
                    <div v-if="form.has_usage_limits" class="row mt-3">
                        <div class="col-4">
                            <div class="form-group mb-0" :class="{'has-danger': errors.max_total_uses}">
                                <label class="control-label">USOS TOTALES</label>
                                <el-input
                                    v-model.number="form.max_total_uses"
                                    type="number"
                                    min="1"
                                    placeholder="100"
                                ></el-input>
                                <small class="form-control-feedback" v-if="errors.max_total_uses" v-text="errors.max_total_uses[0]"></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group mb-0" :class="{'has-danger': errors.max_uses_per_customer}">
                                <label class="control-label">USOS POR CLIENTE</label>
                                <el-input
                                    v-model.number="form.max_uses_per_customer"
                                    type="number"
                                    min="1"
                                    placeholder="1"
                                ></el-input>
                                <small class="form-control-feedback" v-if="errors.max_uses_per_customer" v-text="errors.max_uses_per_customer[0]"></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group mb-0" :class="{'has-danger': errors.expires_at}">
                                <label class="control-label">FECHA DE VENCIMIENTO</label>
                                <el-date-picker
                                    v-model="form.expires_at"
                                    type="date"
                                    format="dd/MM/yyyy"
                                    value-format="yyyy-MM-dd"
                                    placeholder="dd/mm/aaaa"
                                    style="width: 100%;"
                                ></el-date-picker>
                                <small class="form-control-feedback" v-if="errors.expires_at" v-text="errors.expires_at[0]"></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Acciones -->
            <div class="form-actions text-end mt-4">
                <el-button @click.prevent="close">Cancelar</el-button>
                <el-button
                    native-type="submit"
                    type="primary"
                    :loading="loading"
                    class="ms-1"
                >
                    <i class="fa fa-check me-1"></i> Guardar Cupón
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: {
        showDialog: {
            type: Boolean,
            default: false,
        },
        couponRecord: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            loading: false,
            errors: {},
            form: this.initFormData(),
        };
    },
    computed: {
        titleDialog() {
            return this.form.id ? 'Editar Cupón' : 'Crear Cupón';
        },
    },
    methods: {
        /**
         * Retorna la estructura base del formulario con valores por defecto.
         */
        initFormData() {
            return {
                id:                    null,
                code:                  '',
                type:                  'percentage',
                amount:                0,
                has_purchase_limits:   false,
                min_amount:            null,
                max_amount:            null,
                has_usage_limits:      false,
                max_total_uses:        null,
                max_uses_per_customer: null,
                expires_at:            null,
                free_shipping:         false,
            };
        },
        /**
         * Al abrir el diálogo, carga el registro si es edición o limpia el formulario.
         */
        open() {
            this.errors = {};
            if (this.couponRecord && this.couponRecord.id) {
                this.form = Object.assign(this.initFormData(), this.couponRecord);
            } else {
                this.form = this.initFormData();
                this.generateCode();
            }
        },
        /**
         * Genera un código aleatorio alfanumérico en mayúsculas de 8 caracteres.
         */
        generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            this.form.code = code;
        },
        submit() {
            this.loading = true;
            this.errors  = {};

            this.$http.post('/ecommerce/discount-coupons', this.form)
                .then(() => {
                    this.$message.success(
                        this.form.id ? 'Cupón actualizado correctamente' : 'Cupón creado correctamente'
                    );
                    this.$eventHub.$emit('reloadData');
                    this.close();
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error('Error al guardar el cupón');
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        close() {
            this.errors = {};
            this.$emit('update:showDialog', false);
            this.$emit('update:couponRecord', null);
        },
    },
};
</script>
