<template>
    <div>
        <el-dialog
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :title="title"
            :visible="show"
            @close="onClose"
            @open="getRecordGuide"
        >
            <!--
            <template v-if="!is_client">
                        </template>

            -->
            <div class="form-group">
                <label class="control-label">
                    Guías
                </label>
                <table style="width: 100%">
                    <tr v-for="(guide,index) in form.guides">
                        <td>
                            <el-select v-model="guide.document_type_id">
                                <el-option v-for="option in document_types_guide"
                                           :key="option.id"
                                           :label="option.description"
                                           :value="option.id"></el-option>
                            </el-select>
                        </td>
                        <td>
                            <el-input v-model="guide.number"></el-input>
                        </td>
                        <td>
                            <template v-if="guide.filename">
                                <button
                                    type="button"
                                    v-if="guide.filename && guide.live === undefined"
                                    class="btn waves-effect waves-light btn-xs btn-primary"
                                    @click.prevent="clickDownloadFile(guide.filename)">
                                    <i class="fas fa-file-download"></i>
                                </button>
                                <div
                                    v-if="guide.filename && guide.live == 1"
                                >
                                <span>
                                    {{guide.filename}}
                                </span>
                                </div>
                            </template>
                            <template v-else>
                                <el-upload

                                    :action="`/${type}/guide-file/upload`"
                                    :data="{'index': index}"
                                    :file-list="fileList"
                                    :headers="headers"
                                    :limit="form.guides.length"
                                    :multiple="false"
                                    :on-remove="handleRemove"
                                    :on-success="onSuccess"
                                    :show-file-list="false"
                                >
                                    <el-button
                                        class="btn btn-sm"
                                        slot="trigger"
                                        type="primary"
                                        @click="changeIndexFile(index)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" /></svg>
                                        Seleccione un archivo
                                    </el-button>

                                </el-upload>
                            </template>
                        </td>
                        <td align="right">
                            <button class="btn waves-effect waves-light btn-sm btn-danger"
                                    type="button"
                                    @click.prevent="clickRemoveGuide(index)">
                                <svg data-v-d812ec56="" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path data-v-d812ec56="" stroke="none" d="M0 0h24v24H0z" fill="none"></path><path data-v-d812ec56="" d="M4 7l16 0"></path><path data-v-d812ec56="" d="M10 11l0 6"></path><path data-v-d812ec56="" d="M14 11l0 6"></path><path data-v-d812ec56="" d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path data-v-d812ec56="" d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div v-if="!loading" class="col add-row-table mx-0" @click.prevent="clickAddGuide">
                                <svg data-v-d812ec56="" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-plus"><path data-v-d812ec56="" stroke="none" d="M0 0h24v24H0z" fill="none"></path><path data-v-d812ec56="" d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path data-v-d812ec56="" d="M9 12h6"></path><path data-v-d812ec56="" d="M12 9v6"></path></svg>
                                Agregar guía
                            </div>
                        </td>
                    </tr>
                </table>
            </div>


            <div class="form-actions text-end pt-2 mt-2">
                <el-button
                    class="me-2"
                    :disabled="loading"
                    @click="onClose"
                >Cerrar
                </el-button>
                <el-button
                    v-if="form.guides.length > 0"
                    :disabled="loading"
                    type="primary"                    
                    @click="saveGuides"
                >Guardar Guia
                </el-button>                
            </div>

        </el-dialog>
    </div>
</template>
<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

export default {
    props: {
        'establishment': {
            required: false,
            default: ''
        },
        id: {
            required: false,
            type: Number,
            default: 0

        },
        show: {
            required: false,
            type: Boolean,
            default: false

        },
        type: {
            required: false,
            type: String,
            default: ''

        },

    },
    computed: {
        ...mapState([
            'config',
            'document_types_guide',
        ]),
    },
    data() {
        return {
            // document_types_guide: [],
            headers: headers_token,
            index_file: null,
            loading: true,
            title: '',
            fileList: [],
            form: {
                guides: [{}],
                id: null,
                number: '',
                document_type_description: '',
            }

        }
    },
    created() {
        this.loadConfiguration()

    },
    methods: {

        ...mapActions([
            'loadConfiguration',
            'loadDocumentTypesGuide',
        ]),
        getRecordGuide() {
            this.fileList = [];
            this.form.guides = [];
            this.loading = true;
            this.$http.post(`/${this.type}/guide/${this.id}`)
                .then((result) => {
                    this.form = result.data
                    this.title = 'Guia para Documento: ' + this.form.number + "";
                    if (this.form.guides === undefined) {
                        this.form.guides = [];
                    }

                })
                .finally(() => {
                    this.loading = false;
                })
        },
        clickAddInitGuides() {
            this.form.guides.push({
                document_type_id: '09',
                number: null
            }, {
                document_type_id: '31',
                number: null
            })
        },
        clickRemoveGuide(index) {
            this.form.guides.splice(index, 1)
            this.fileList.splice(index, 1)
        },
        cleanFileList() {
            this.fileList = []
        },
        saveGuides() {
            this.loading = true;
             this.form.updateGuide = 1;


            this.$http.post(`/${this.type}/guide/${this.id}`,  this.form)
                .then((result) => {
                    this.onClose()

                })
                .catch(error => {
                    if (error.response.status === 500) {
                        console.log(error)
                        this.$message.error(error.response.data.message)
                    }
                })
                .finally(() => {
                    this.loading = false;
                })

        },
        clickAddGuide() {
            this.form.guides.push({
                document_type_id: null,
                number: null
            })
        },
        onClose() {
            this.title = 'Guias';
            this.loading = false;
            this.form = {};
            this.form.guides = [];
            this.fileList = [];
            this.$emit("update:show", false);

        },
        handleRemove(file, fileList) {

            this.form.guides[this.index_file].filename = null
            this.form.guides[this.index_file].temp_path = null
            this.fileList = []
            this.index_file = null

        },
        hasFile(index) {
            if (this.fileList[index] !== undefined) {
                if (this.fileList[index] !== null) {
                    return true
                }
            }
            return false
        },
        changeIndexFile(index) {
            this.index_file = index
        },
        onSuccess(response, file, fileList) {

            // console.log(response, file, fileList)
            // this.fileList = fileList

            if (this.index_file == null) this.index_file = this.fileList.length;
            if (response.success) {
                this.index_file = response.data.index
                for (let i = 0; i < this.index_file; i++) {
                    if (this.fileList[i] === undefined) this.fileList[i] = null;
                }
                this.fileList[this.index_file] = file;
                let t = this.form.guides[this.index_file];
                t.filename = response.data.filename
                t.temp_path = response.data.temp_path
                t.live = 1

                this.form.guides[this.index_file] = t;
                let guides = this.form.guides;
                this.form.guides = [];
                this.form.guides = guides;
                this.hasFile(this.index_file)
            } else {
                this.cleanFileList()
                this.$message.error(response.message)
            }

            // console.log(this.form.guides)

        },
        clickDownloadFile(filename) {
            window.open(
                `/${this.type}/guides-file/download-file/${this.id}/${filename}`,
                "_blank"
            );
        },
        onSubmit() {
            console.error('onSubmit')

        },
        handleExceed() {
            console.error('handleExceed')

        }
    }
}
</script>
