<template>
    <el-dialog :title="titleDialog" :visible="showDialog"   @close="close" @open="onDialogOpen"   append-to-body destroy-on-close top="7vh">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" >
                        <div class="form-group" >
                            <vue-ckeditor v-if="showDialog" ref="editor" type="classic" v-model="form.terms_condition" :editors="editors"></vue-ckeditor>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end pt-2 mt-2">
                <el-button class="second-buton" @click.prevent="close()">Cerrar</el-button>
                <el-button type="primary" @click.prevent="clickSubmit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

    import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
    import VueCkeditor from 'vue-ckeditor5'

    export default {
        components: {'vue-ckeditor': VueCkeditor.component},
        props:['showDialog', 'form'],
        data() {
            return {
                showImportDialog: false,
                resource: 'items',
                recordId: null,
                titleDialog: 'Términos y condiciones',
                editors: {
                  classic: ClassicEditor
                },
            }
        },
        created() {
        },
        methods: {
            onDialogOpen() {
                this.applyInitialEditorContent();
            },
            applyInitialEditorContent(retries = 40) {
                if (retries <= 0) return;
                const editor = this.$refs.editor;
                if (!editor || !editor.instance) {
                    setTimeout(() => this.applyInitialEditorContent(retries - 1), 25);
                    return;
                }
                const value = (this.form && this.form.terms_condition) || '';
                if (editor.instance.getData() !== value) {
                    editor.instance.setData(value);
                }
            },
            clickSubmit(){
                this.$eventHub.$emit('submitFormConfigurations', this.form)
                this.close()
            },
            close() {
                this.$emit('update:showDialog', false)
            },
        }
    }
</script>
