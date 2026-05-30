<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        append-to-body
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-12 form-group" :class="{'has-danger': errors.file}">
                        <el-upload
                                ref="upload"
                                :headers="headers"
                                action="/transfers/import"
                                :show-file-list="true"
                                :auto-upload="false"
                                :multiple="false"
                                :on-error="errorUpload"
                                :before-upload="onBeforeUpload"
                                :limit="1"
                                :data="form"
                                :on-success="successUpload">
                            <el-button slot="trigger" type="primary">Seleccione un archivo (xlsx)</el-button>
                        </el-upload>
                        <small class="form-control-feedback" v-if="errors.file" v-text="errors.file[0]"></small>
                    </div>
                    <div v-if="!stock_establishments" class="col-12 mt-4 mb-2">
                        <a class="text-dark me-auto" href="/transfers/download/import" target="_new">
                            <span class="me-2">Descargar formato de ejemplo para importar</span>
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-4">
                <el-button class="second-buton me-2" @click.prevent="close()"
                    >Cancelar</el-button
                >
                <el-button
                    type="primary"
                    native-type="submit"
                    :loading="loading_submit"
                    >Aceptar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
import OutputLotsGroupForm from "@views/documents/partials/lots_group.vue";
import OutputLotsForm from "@views/documents/partials/lots.vue";
//import OutputLotsForm from './partials/lots.vue';

export default {
    components: { OutputLotsForm, OutputLotsGroupForm },
    props: ["showDialog", "recordId"],
    data() {
        return {
            titleDialog: null,
            loading_submit: false,
            headers: headers_token,
            resource: "inventory",
            errors: {},
            form: {},
        };
    },
    async created() {
        this.initForm();
    },
    methods: {
        initForm() {
            this.errors = {};
        },
        async create() {
            this.titleDialog = "Traslados Masivos de Inventario";
        },
        async submit() {

            this.loading_submit = true
            await this.$refs.upload.submit()
            this.loading_submit = false

        },
        successUpload(response, file, fileList) {
            if (response.success) {
                    this.$message.success(response.message)
                    this.$refs.upload.clearFiles()
                    this.close()
                } else {
                    this.$message({message:response.message, type: 'error'})
                }
            },
        close() {
            this.$emit("update:showDialog", false);
            this.initForm();
        },
    }
};
</script>
