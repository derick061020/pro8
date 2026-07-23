<template>
    <div class="card">
        <div class="card-body" v-loading="loadingConfig || loadingSubmit">
            <h4>Gestionar color de la app</h4>
            <div class="col-12">
                <label class="control-label d-block mb-1">Tema de color</label>
                <el-select v-model="form.theme_color"
                    filterable
                    clearable
                    :disabled="loadingSubmit"
                    @change="submit"
                    popper-class="el-select-currency">
                    <el-option v-for="option in [
                            {id: 'blue', description: 'Predeterminado'},
                            {id: 'dark', description: 'Oscuro'},
                            {id: 'red', description: 'Rojizo'},
                        ]"
                        :key="option.id"
                        :label="option.description"
                        :value="option.id"></el-option>
                </el-select>
            </div>
            <div class="col-12 pt-3">
                <label class="control-label d-block mb-1">Color principal <small class="badge badge-info">Nueva versión</small></label>
                <el-color-picker v-model="form.primary_color" size="medium" class="w-100" :disabled="loadingSubmit" @change="submit"></el-color-picker>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    theme_color: null,
                    primary_color: null,
                },
                loadingConfig: false,
                loadingSubmit: false,
            }
        },
        created() {
            this.getData()
        },
        methods: {
            getData() {
                this.loadingConfig = true;
                this.$http.get('/app/configurations')
                    .then(response => {
                        this.form = response.data.data
                    })
                    .finally(() => {
                        this.loadingConfig = false;
                    })
            },
            submit() {
                this.loadingSubmit = true;
                this.$http.post('/app/configurations', this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success('Configuración guardada correctamente');
                        }
                    })
                    .catch(() => {
                        this.$message.error('Error al guardar la configuración');
                    })
                    .finally(() => {
                        this.loadingSubmit = false;
                    });
            }
        }
    }
</script>
