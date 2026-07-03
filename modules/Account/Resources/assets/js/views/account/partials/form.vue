<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit" v-loading="loading">
            <div class="form-body">
                <div class="text-right mb-2">
                    <el-button
                        type="primary"
                        size="small"
                        icon="el-icon-plus"
                        :disabled="!canAddRow"
                        @click.prevent="addRow"
                    >
                        Agregar
                    </el-button>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descripción tipo documento</th>
                            <th>Cuenta Soles</th>
                            <th>Cuenta Dolares</th>
                            <th class="text-center" width="60">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!form.records.length">
                            <td colspan="5" class="text-center text-muted">
                                Sin registros. Use «Agregar» para crear uno.
                            </td>
                        </tr>
                        <tr v-for="(row, index) in form.records" :key="index">
                            <td>
                                <span>{{ documentTypeShort(row.document_type_id) }}</span>
                            </td>
                            <td>
                                <el-select v-model="row.document_type_id" filterable class="w-100">
                                    <el-option
                                        v-for="option in availableDocumentTypes(index)"
                                        :key="option.id"
                                        :label="option.description"
                                        :value="option.id"
                                    ></el-option>
                                </el-select>
                            </td>
                            <td>
                                <el-select v-model="row.account_soles_id" filterable clearable class="w-100">
                                    <el-option
                                        v-for="option in bank_accounts_pen"
                                        :key="option.id"
                                        :label="option.description"
                                        :value="option.id"
                                    ></el-option>
                                </el-select>
                            </td>
                            <td>
                                <el-select v-model="row.account_dolares_id" filterable clearable class="w-100">
                                    <el-option
                                        v-for="option in bank_accounts_usd"
                                        :key="option.id"
                                        :label="option.description"
                                        :value="option.id"
                                    ></el-option>
                                </el-select>
                            </td>
                            <td class="text-center">
                                <el-button
                                    type="danger"
                                    size="mini"
                                    icon="el-icon-delete"
                                    @click.prevent="removeRow(index)"
                                ></el-button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button class="second-buton" @click.prevent="close()">Cancelar</el-button>
                <el-button :loading="loading_submit" native-type="submit" type="primary">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: ['showDialog', 'recordId'],
        data() {
            return {
                loading: false,
                loading_submit: false,
                titleDialog: null,
                resource: 'account',
                errors: {},
                form: {},
                bank_accounts: [],
                document_types: []
            }
        },
        created() {
            this.initForm()
        },
        computed: {
            bank_accounts_usd() {
                return this.bank_accounts.filter(el => el.currency_type_id === 'USD')
            },
            bank_accounts_pen() {
                return this.bank_accounts.filter(el => el.currency_type_id === 'PEN')
            },
            canAddRow() {
                return this.form.records && this.form.records.length < this.document_types.length
            },
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    records: []
                }
            },
            async create() {
                this.titleDialog = this.recordId ? 'Editar Configuración de EJB' : 'Nueva Configuración de EJB'

                this.loading = true
                await this.getRecords()

                this.loading = false
            },
            async getRecords() {
                await this.$http.get('/account/tables-ejb')
                    .then(response => {
                        this.bank_accounts = response.data.banks
                        this.document_types = response.data.document_types

                        this.form.records = (response.data.records || []).map(record => ({
                            document_type_id: record.document_type_id,
                            account_soles_id: record.bank_account_pen_id,
                            account_dolares_id: record.bank_account_usd_id
                        }))
                    })
            },
            availableDocumentTypes(index) {
                const selected = this.form.records
                    .filter((row, i) => i !== index && row.document_type_id)
                    .map(row => row.document_type_id)

                return this.document_types.filter(option => !selected.includes(option.id))
            },
            documentTypeShort(document_type_id) {
                const documentType = this.document_types.find(el => el.id === document_type_id)
                return documentType ? documentType.short : ''
            },
            addRow() {
                if (!this.canAddRow) {
                    this.$message.warning(`Solo se pueden registrar ${this.document_types.length} tipos de documento.`)
                    return
                }
                this.form.records.push({
                    document_type_id: null,
                    account_soles_id: null,
                    account_dolares_id: null
                })
            },
            removeRow(index) {
                this.form.records.splice(index, 1)
            },
            submit() {
                this.loading_submit = true
                this.$http.post(`/${this.resource}/ejb`, this.form)
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
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            }
        }
    }
</script>
