<template>
    <div class="col-12" v-loading="loading">
        <div class="table-responsive w-100">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Ruta</th>
                        <th class="text-center">Convertir a Soles</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in records" :key="index">

                        <td>{{ index + 1 }}</td>
                        <td>{{ row.name }}</td>
                        <td>
                            <div>
                                <span class="route-path-reports">
                                    {{ row.route_path }}
                                    <a :href="row.route_path" target="_blank" rel="noopener" :title="`Ir a ${row.route_path}`" style="cursor: pointer; text-decoration: none; color: inherit;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link ms-2" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            <el-switch v-model="row.convert_pen"
                                 @change="submit(row)">
                            </el-switch>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>


export default {
    props: [ 
    ],
    data() {
        return {
            resource: 'report_configurations',
            records: [],
            loading: false,
            errors: {}
        }
    },
    created() {
        this.getData()
    },
    methods: {
        getData() {
            this.$http.get(`/${this.resource}/records`)
                .then(response => {
                    this.records = response.data.data
                })
        },
        submit(row) {

            this.loading = true

            this.$http.post(`/${this.resource}`, row).then(response => {
                let data = response.data

                if (data.success) {
                    this.$message.success(data.message)
                } else {
                    this.$message.error(data.message)
                }

            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors
                } else {
                    console.log(error)
                }
            }).then(() => {
                this.loading = false
            })
        },
    }
}
</script>
