<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body"> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" :class="{'has-danger': errors.description}">
                            <label class="control-label">Nombre <span class="text-danger">*</span></label>
                            <el-input v-model="form.description" dusk="description"></el-input>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div> 
                    <div class="col-md-6">
                            <div class="form-group d-flex align-items-center mb-0 mt-3">
                                <label class="control-label m-0">Habilitar descripción del cliente</label>
                                <div class="ms-2" :class="{'has-danger': errors.is_client}">
                                    <el-switch v-model="form.enabled_description_person_type"></el-switch>
                                    <small class="form-control-feedback" v-if="errors.is_client" v-text="errors.is_client[0]"></small>
                                </div>
                            </div>
                            <div class="form-group" :class="{'has-danger': errors.description}" v-if="form.enabled_description_person_type">
                                <label class="control-label">Descripción: <span class="text-danger">*</span></label>
                                    <el-input
                                        type="textarea"
                                        v-model="form.description_person_type"></el-input>
                                <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                            </div>
                    </div> 
                    <div class="col-md-6">
                        <div class="form-group d-flex align-items-center mb-0 mt-3">
                            <label class="control-label m-0">Habilitar precio</label>
                            <div class="ms-2">
                                <el-switch v-model="form.enabled_price" @change="onTogglePrice"></el-switch>
                            </div>
                        </div>
                        <div class="form-group" :class="{'has-danger': errors.description}" v-if="form.enabled_price">
                            <label class="control-label">Seleccionar Precio: <span class="text-danger">*</span></label>
                                    <el-select v-model="form.price_label_id"
                                            placeholder="Precio por cliente"
                                            popper-class="el-select-currency"
                                    >
                                        <el-option v-for="option in price_labels"
                                                :key="option.id"
                                                :label="option.label"
                                                :value="option.id"></el-option>
                                    </el-select>
                            <small class="form-control-feedback" v-if="errors.description" v-text="errors.description[0]"></small>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="form-actions text-end mt-4">
                <el-button class="second-buton me-2" @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>


    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                titleDialog: null,
                loading_submit: false,
                resource: 'person-types',
                price_labels: [],
                errors: {},
                form: {}, 
            }
        },
        created() {
            this.initForm() 
        }, 
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    description: null,
                    price_label_id: null,
                    enabled_price: false,
                }
            },
            create() { 
                this.titleDialog = (this.recordId)? 'Editar tipo de cliente':'Nuevo tipo de cliente'

                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`)
                        .then(response => {
                            this.form = {
                                ...response.data,
                                enabled_price: !!response.data.price_label_id
                            }
                        })
                }

                this.$http.get("/price-labels")
                    .then(response => {
                        this.price_labels = response.data.data
                    })
            }, 
            submit() {
                if (this.form.enabled_price && !this.form.price_label_id) {
                    this.$message.error('Debe seleccionar un precio.')
                    return
                }
                this.loading_submit = true
                this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message) 
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            }, 
            onTogglePrice(val) {
                if (!val) {
                    this.form.price_label_id = null
                }
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            }, 
        }
    }
</script>