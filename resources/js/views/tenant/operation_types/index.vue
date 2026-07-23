<template>
    <div>
        <div class="page-header pe-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Listado de tipos de operacion </span></li>
            </ol>
        </div>
        <div class="card tab-content-default row-new">
            <div class="card-body">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!-- <th width="15%">Codigo</th> -->
                                    <th class="text-start">Activo</th>
                                    <th>Descripcion</th>
                                    <!-- <th width="15%">Exportacion</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in records" :key="row.id">
                                    <td>{{ index + 1 }}</td>
                                    <!-- <td>{{ row.id }}</td> -->
                                    <td class="text-start">
                                        <el-switch
                                            v-model="row.active"
                                            @change="changeActive(index)"
                                        ></el-switch>
                                    </td>
                                    <td>{{ row.description }}</td>
                                    <!-- <td>{{ row.exportation ? 'Si' : 'No' }}</td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            resource: 'operation-types',
            records: [],
        }
    },
    created() {
        this.getRecords()
    },
    methods: {
        getRecords() {
            this.$http.get(`${this.resource}/records`).then(response => {
                this.records = response.data
            }).catch(error => {
                console.error(error)
            })
        },
        changeActive(index) {
            const record = this.records[index]
            const active = Number(record.active)

            this.$http.get(`${this.resource}/active/${record.id}/${active}`).then(response => {
                if (response.data.success) {
                    this.$message.success(response.data.message)
                } else {
                    this.$message.error(response.data.message)
                    record.active = !record.active
                }
            }).catch(error => {
                console.error(error)
                record.active = !record.active
            })
        },
    },
}
</script>
