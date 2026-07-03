<template>
    <el-dialog width="60%" :title="title" :visible="showDialog" @close="close" @open="getData">
        <div class="form-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive" v-if="records.length > 0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Descripción</th>
                                <th>Fecha de vencimiento</th>
                                <th>Estado</th>
                                <th class="text-right">Monto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in records" :key="row.id">
                                <td>{{ row.order }}</td>
                                <td>{{ row.description }}</td>
                                <td>{{ row.due_date }}</td>
                                <td>
                                    <span class="badge"
                                          :class="row.order_state_id == 3 ? 'badge-danger' : 'badge-warning'">
                                        {{ row.order_state }}
                                    </span>
                                </td>
                                <td class="text-right">S/ {{ row.amount }}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" class="text-right">TOTAL PENDIENTE</td>
                                <td class="text-right">S/ {{ total_pending }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p v-else class="text-center text-muted pt-3">
                        No hay órdenes de pago pendientes.
                    </p>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>

    export default {
        props: ['showDialog', 'clientId'],

        data() {
            return {
                title: 'Órdenes de pago pendientes',
                resource: 'client_payments',
                records: [],
            }
        },
        computed: {
            total_pending() {
                return this.records
                    .reduce((sum, row) => sum + parseFloat(row.amount || 0), 0)
                    .toFixed(2);
            }
        },
        methods: {
            async getData() {
                this.records = [];
                await this.$http.get(`/${this.resource}/client/${this.clientId}`)
                    .then(response => {
                        this.title = 'Órdenes de pago pendientes del cliente: ' + response.data.name;
                    });
                await this.$http.get(`/${this.resource}/records/${this.clientId}`)
                    .then(response => {
                        this.records = response.data.data;
                    });
            },
            close() {
                this.$emit('update:showDialog', false);
            }
        }
    }
</script>
