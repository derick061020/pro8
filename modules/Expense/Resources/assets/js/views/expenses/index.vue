<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/expenses">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Gastos diversos</span></li>
            </ol>
            <div class="right-wrapper pull-right pt-2 me-2">
                <!--<el-button class="submit" type="success" @click.prevent="clickDownload('excel')"><i class="fa fa-file-excel"></i> Exportar Excel </el-button>-->
                <a :href="`/${resource}/create`" class="btn btn-custom btn-sm "><i class="fa fa-plus-circle"></i> Nuevo</a>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th class="text-start">Fecha Emisión</th>
                        <th>Proveedor</th>
                        <th>Número</th>
                        <th>Motivo</th>
                        <th class="text-center">Pagos</th>
                        <th class="text-center">Moneda</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Dist. Gasto</th>
                    </tr>
                    <tr slot-scope="{ index, row }" :class="{'text-danger': (row.state_type_id === '11'), 'text-warning': (row.state_type_id === '13'), 'border-light': (row.state_type_id === '01'), 'border-left border-info': (row.state_type_id === '03'), 'border-left border-success': (row.state_type_id === '05'), 'border-left border-secondary': (row.state_type_id === '07'), 'border-left border-dark': (row.state_type_id === '09'), 'border-left border-danger': (row.state_type_id === '11'), 'border-left border-warning': (row.state_type_id === '13')}">
                        <!-- <td>{{ index }}</td> -->
                        <td class="text-start">{{ row.date_of_issue }}</td>
                        <td>{{ row.supplier_name }}<br/><small v-text="row.supplier_number"></small></td>
                        <td>{{ row.number }}<br/>
                            <small v-text="row.expense_type_description"></small><br/>
                        </td>
                        <td class="">{{ row.expense_reason_description }}</td>
                        <td class="text-center">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-xs btn-info m-1__2"
                                @click.prevent="clickExpensePayment(row.id)"
                            >Pagos</button>
                        </td>
                        <td class="text-center">{{ row.currency_type_id }}</td>
                        <td class="text-end">{{row.currency_type_id === 'PEN' ? 'S/' : '$'}} {{ formatDecimal(row.total) }}</td>

                        <td class="text-end">

                            <button type="button" class="btn btn-xs btn-success btn-shad me-1"
                                    @click.prevent="clickPrint(row.external_id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4" /></svg>
                            </button>

                            <button type="button" v-if="row.state_type_id != '11'" class="btn btn-xs btn-primary btn-shad me-1"
                                    @click.prevent="clickCreate(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                            </button>

                            <button type="button" class="btn btn-xs btn-info btn-shad me-1"
                                    @click.prevent="clickPayment(row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger btn-shad me-1"
                                    @click.prevent="clickVoided(row.id)"
                                    v-if="row.state_type_id === '05'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </td>

                    </tr>
                </data-table>
            </div>


            <document-payments :showDialog.sync="showDialogPayments"
                               :expenseId="recordId"></document-payments>
            <expense-voided :showDialog.sync="showDialogVoided"
                               :expenseId="recordId"></expense-voided>

            <expense-payments
                :showDialog.sync="showDialogExpensePayments"
                :expenseId="recordId"
                :external="true"
                ></expense-payments>
        </div>
    </div>

</template>

<script>

    import DataTable from '../../components/DataTableExpenses.vue'
    import DocumentPayments from './partials/payments.vue'
    import ExpenseVoided from './partials/voided.vue'
    import ExpensePayments from '@viewsModuleExpense/expense_payments/payments.vue'
    import queryString from 'query-string'

    export default {
        components: {DataTable, DocumentPayments, ExpenseVoided, ExpensePayments},
        data() {
            return {
                showDialogVoided: false,
                resource: 'expenses',
                showDialogPayments: false,
                showDialogExpensePayments: false,
                recordId: null,
                showDialogOptions: false,
                decimal_quantity: 2
            }
        },
        created() {
            this.loadDecimalQuantity()
        },
        methods: {
            async loadDecimalQuantity() {
                try {
                    const response = await this.$http.get('/configurations/record')
                    const decimalQuantity = response.data.data.decimal_quantity

                    this.decimal_quantity = parseInt(decimalQuantity || 2)
                } catch (error) {
                    this.decimal_quantity = 2
                }
            },
            formatDecimal(value) {
                const number = parseFloat(value || 0)

                if (isNaN(number)) {
                    return Number(0).toFixed(this.decimal_quantity)
                }

                return number.toFixed(this.decimal_quantity)
            },
            clickPrint(external_id){
                window.open(`/${this.resource}/print/${external_id}`, '_blank');
            },
            clickCreate(id = '') {
                location.href = `/${this.resource}/create/${id}`
            },
            clickExpensePayment(recordId) {
                this.recordId = recordId;
                this.showDialogExpensePayments = true
            },
            clickVoided(recordId) {
                this.recordId = recordId;
                this.showDialogVoided = true;
            },
            /*clickDownload(download) {
                let data = this.$root.$refs.DataTable.getSearch();
                let query = queryString.stringify({
                    'column': data.column,
                    'value': data.value
                });

                window.open(`/${this.resource}/report/excel/?${query}`, '_blank');
            },*/
            clickOptions(recordId = null) {
                this.recordId = recordId
                this.showDialogOptions = true
            },
            clickPayment(recordId) {
                this.recordId = recordId;
                this.showDialogPayments = true;
            },
        }
    }
</script>
