<template>
    <div class="card">
        <div class="card-header bg-info bg-info-customer-admin">
            <h3 class="my-0">Términos y Condiciones</h3>
        </div>
        <div class="card-body">
            <form autocomplete="off" @submit.prevent="submit">
                <div class="form-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" :class="{'has-danger': errors.terms_mode}">
                                <el-radio-group v-model="form.terms_mode">
                                    <el-radio label="disabled">Desactivado</el-radio>
                                    <el-radio label="content">Contenido propio</el-radio>
                                    <el-radio label="url">URL externa</el-radio>
                                </el-radio-group>
                                <small class="form-control-feedback d-block">
                                    Si está <strong>desactivado</strong>, la página pública no muestra nada (redirige al login).
                                    Solo puede usarse una fuente a la vez.
                                </small>
                                <small class="form-control-feedback" v-if="errors.terms_mode" v-text="errors.terms_mode[0]"></small>
                            </div>
                        </div>

                        <div class="col-12" v-if="form.terms_mode === 'content'">
                            <div class="form-group" :class="{'has-danger': errors.terms_content}">
                                <label class="control-label d-block">Contenido</label>
                                <vue-ckeditor
                                    :editors="editors"
                                    type="classic"
                                    v-model="form.terms_content"
                                    :config="editorConfig"
                                />
                                <small class="form-control-feedback">Redacta aquí los términos y condiciones que verán los usuarios.</small>
                                <small class="form-control-feedback" v-if="errors.terms_content" v-text="errors.terms_content[0]"></small>
                            </div>
                        </div>

                        <div class="col-12" v-if="form.terms_mode === 'url'">
                            <div class="form-group" :class="{'has-danger': errors.terms_url}">
                                <label class="control-label">URL externa</label>
                                <el-input v-model="form.terms_url" placeholder="https://miempresa.com/terminos"></el-input>
                                <small class="form-control-feedback text-muted">La página pública redirigirá a esta dirección.</small>
                                <small class="form-control-feedback" v-if="errors.terms_url" v-text="errors.terms_url[0]"></small>
                            </div>
                        </div>

                        <div class="col-12" v-if="form.terms_mode !== 'disabled'">
                            <small class="form-control-feedback">
                                Página pública: <a :href="publicUrl" target="_blank" v-text="publicUrl"></a>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="form-actions text-end pt-2">
                    <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import 'ckeditor5/ckeditor5.css';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import CKEditor from 'vue-ckeditor5';

export default {
    components: {
        'vue-ckeditor': CKEditor.component
    },
    data() {
        return {
            loading_submit: false,
            resource: 'configurations',
            errors: {},
            editors: {
                classic: ClassicEditor
            },
            editorConfig: {
                licenseKey: 'GPL',
                toolbar: [
                    'heading',
                    '|',
                    'bold', 'italic', 'link',
                    'bulletedList', 'numberedList',
                    '|',
                    'blockQuote',
                    'undo', 'redo',
                    '|',
                    'sourceEditing'
                ]
            },
            form: {
                terms_mode: 'disabled',
                terms_content: '',
                terms_url: null
            }
        }
    },
    computed: {
        publicUrl() {
            return `${window.location.origin}/terminos-y-condiciones`;
        }
    },
    created() {
        this.$http.get(`/${this.resource}/terms`)
            .then(response => {
                const data = response.data || {};
                this.form = {
                    terms_mode: data.terms_mode || 'disabled',
                    terms_content: data.terms_content || '',
                    terms_url: data.terms_url || null
                };
            });
    },
    methods: {
        submit() {
            this.loading_submit = true;
            this.errors = {};
            this.$http.post(`/${this.resource}/terms`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.log(error);
                    }
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        }
    }
}
</script>
