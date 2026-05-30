<template>
    <el-dialog :visible="showDialog"
               class="dialog-import"
               title="Generar Etiquetas de Productos"
               @open="open"
               @close="close">
        <form autocomplete="off"
              @submit.prevent="submit">
            <div class="form-body">
                <div class="row">


                    <!-- Minimo -->
                    <div class="col-6">
                        <div class="form-group">
                            <label class="control-label w-100 d-flex justify-content-between">
                                Tipo de etiqueta                                
                            </label>
                            <div>
                                <el-select v-model="form.template_id">
                                    <el-option
                                        v-for="option in templates"
                                        :key="option.id"
                                        :label="option.name"
                                        :value="option.id"
                                    ></el-option>
                                </el-select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 tag-preview">
                        <div ref="tagPreview" class="preview-canvas-wrap mt-2">
                        </div>
                        <div class="text-center mt-1">
                            <el-button type="primary" class="btn btn-sm col-12" @click.prevent="showDialogEditor = true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tags"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8v4.172a2 2 0 0 0 .586 1.414l5.71 5.71a2.41 2.41 0 0 0 3.408 0l3.592 -3.592a2.41 2.41 0 0 0 0 -3.408l-5.71 -5.71a2 2 0 0 0 -1.414 -.586h-4.172a2 2 0 0 0 -2 2" /><path d="M18 19l1.592 -1.592a4.82 4.82 0 0 0 0 -6.816l-4.592 -4.592" /><path d="M7 10h-.01" /></svg>
                                Editor de etiquetas
                            </el-button>
                        </div>
                    </div>
                    <el-tabs class="mt-4" type="card" @tab-click="handClick">
                        <el-tab-pane label="Selección individual" >
                        <div class="col-12">
                            <div class="row">
                                <div class="col-8">
                                <label class="control-label">
                                    Seleccionar producto
                                </label>
                                        <el-select
                                            id="select-width"
                                            ref="selectBarcode"
                                            slot="prepend"
                                            multiple
                                            v-model="form.items"
                                            :loading="loading_search"
                                            :remote-method="searchRemoteItems"
                                            filterable
                                            placeholder="Buscar"
                                            popper-class="el-select-items"
                                            remote
                                            value-key="id"
                                        >
                                            <el-option
                                                v-for="option in all_items"
                                                :key="option.id"
                                                :label="option.full_description"
                                                :value="option.id"
                                            ></el-option>
                                        </el-select>
                                </div>
                                <div class="col-4 col-sm-4">
                                    <div
                                        class="form-group"
                                    >
                                        <label class="control-label">Etiquetas por producto</label>
                                        <el-input-number
                                            ref="inputQuantity"
                                            v-model="form.quantity"
                                            :min="0.01"
                                        ></el-input-number>
                                    </div>
                                </div>

                            </div>
                        </div>

                        </el-tab-pane>
                        <el-tab-pane label="Todo los productos">
                                <div class="col-12">
                                    <div
                                        class="form-group"
                                    >
                                        <label class="control-label">Etiquetas por producto</label>
                                        <el-input-number
                                            ref="inputQuantity"
                                            v-model="form.quantity_per_item"
                                            :min="0.01"
                                        ></el-input-number>
                                    </div>
                                </div>

                        </el-tab-pane>
                    </el-tabs>
                </div>
                <div class="row text-end mt-4">
                    <span>
                        Total: {{ total_records }} etiquetas 
                    </span>
                </div>
                <div class="form-actions text-end mt-4">
                    <el-button class="second-buton me-2" @click.prevent="close()">Cancelar</el-button>
                    <el-button :loading="loading_submit"
                            native-type="submit"
                            type="primary">Procesar
                    </el-button>
                </div>
            </div>
        </form>
        <el-dialog
            :visible.sync="showDialogEditor"
            width="80%"
            custom-class="no-top tag-editor-dialog"
            title="Editor de Etiquetas"
            :close-on-click-modal="false"
        >
            <iframe src="/item-editor-tag" width="100%" height="800px"></iframe>
        </el-dialog>
    </el-dialog>
</template>

<style>
    .no-top {
      margin-top: 2vh !important;
    }
    .tag-editor-dialog .el-dialog__body {
        padding: 0;
    }
    .tag-preview .preview-canvas-wrap{
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
    }
    .tag-preview .preview-canvas{
        position: relative;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .tag-preview .preview-field{
        position: absolute;
        box-sizing: border-box;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>

<script>
import { param } from 'jquery';
import queryString from 'query-string';


export default {
    props: [
        'showDialog',
    ],
    data() {
        return {
            loading_submit: false,
            headers: headers_token,
            showDialogEditor: false,
            loading_search: false,
            resource: 'item-editor-tag',
            errors: {},
            form: {},
            templates: [],
            items: [],
            all_items: [],
            count_items: 0,
        }
    },

    created() {
        if (this.pharmacy !== undefined && this.pharmacy === true) {
            this.fromPharmacy = true;
        }
    },
    computed: {
        total_records() {
            if (this.form.type === 'individual' && this.form.items) {
                return this.form.items.length * this.form.quantity
            } else if (this.form.type === 'all' && this.items) {
                return this.count_items * this.form.quantity_per_item
            }
            return 0
        }
    },
    methods: {
        open() {
            this.initForm()
            this.getTables()
        },
        handClick(tab, event) {
            this.form.type = tab.paneName === "0" ? 'individual' : 'all';
        },
        initForm() {
            this.errors = {}
            this.form = {
                template_id: null,
                items: null,
                quantity: 1,
                quantity_per_item: 1,
                type: 'individual',
            }
        },
        close() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
        submit() {
            let form

            if (this.form.template_id == null) {
                this.$message.warning('Debe seleccionar una plantilla de etiqueta')
                return
            }

            if (this.form.type === 'individual') {

                if (this.form.items == null || this.form.items.length === 0) {
                    this.$message.warning('Debe seleccionar al menos un producto')
                    return
                }
                form = {
                    template_id: this.form.template_id,
                    items: this.form.items,
                    quantity_per_item: this.form.quantity,
                    type : this.form.type,
                }
                
            } else if (this.form.type === 'all') {
                form = {
                    template_id: this.form.template_id,
                    type : this.form.type,
                    quantity_per_item: this.form.quantity_per_item,
                }
            }

            let params =  queryString.stringify(form, { arrayFormat: 'bracket' })
            window.open(`${this.resource}/export?${params}` , '_blank')

            // this.$http.get(`${this.resource}/export`, {
            //     params: form
            // })
            //     .then(response => {
            //         this.loading_submit = false
            //         window.open(response.data.file, '_blank')
            //         this.$emit('update:showDialog', false)
            //         this.initForm()
            //     })
            //     .catch(error => {
            //         this.loading_submit = false
            //         if (error.response.status === 422) {
            //             this.errors = error.response.data.errors
            //         }
            //     })
            

            // this.$emit('update:showDialog', false)
            // this.initForm()
        },
        getTables() {
            this.$http.get(`${this.resource}/records`)
                .then(response => {
                    this.templates = response.data.templates;
                    this.all_items = response.data.items;
                    this.count_items = response.data.count_items;
                    this.form.template_id = this.templates.length > 0 ? this.templates.filter( el => el.is_default)[0].id : null;
                    this.$nextTick(() => this.renderTagPreview())
                })
        },
        async searchRemoteItems(input) {
            if (input.length > 2) {
                this.loading_search = true;
                const params = {
                    input: input,
                };
                await this.$http
                    .get(`/documents/search-items/`, { params })
                    .then(response => {
                        this.all_items = response.data.items;
                        this.loading_search = false;
                        if (this.items.length == 0) {
                            this.items = [];
                        }
                    });
            } 
        },
        renderTagPreview() {
            const wrap = this.$refs.tagPreview
            if (!wrap) return
            wrap.innerHTML = ''

            const tpl = this.templates.find(t => t.id === this.form.template_id)
            if (!tpl) return

            const mmToPx = 3.7795275591
            const canvasWmm = tpl.canvas && tpl.canvas.width ? parseFloat(tpl.canvas.width) : 100
            const canvasHmm = tpl.canvas && tpl.canvas.height ? parseFloat(tpl.canvas.height) : 60
            const canvasPixelW = canvasWmm * mmToPx
            const canvasPixelH = canvasHmm * mmToPx

            const maxPreviewW = 220
            const maxPreviewH = 140
            const scale = Math.min(maxPreviewW / canvasPixelW, maxPreviewH / canvasPixelH, 1)

            const preview = document.createElement('div')
            preview.className = 'preview-canvas'
            preview.style.width = (canvasPixelW * scale) + 'px'
            preview.style.height = (canvasPixelH * scale) + 'px'
            preview.style.border = '1px solid #eee'
            preview.style.background = '#fff'

            const fields = tpl.fields || []
            fields.forEach((f, idx) => {
                try {
                    const el = document.createElement('div')
                    el.className = 'preview-field'

                    const pos = f.position || {}
                    const contentObj = (f.content && typeof f.content === 'string') ? (() => {
                        try { return JSON.parse(f.content) } catch (e) { return {} }
                    })() : (f.content || {})
                    const barcodeObj = (f.barcode && typeof f.barcode === 'string') ? (() => {
                        try { return JSON.parse(f.barcode) } catch (e) { return {} }
                    })() : (f.barcode || {})
                    const parseVal = v => {
                        if (v === undefined || v === null) return 0
                        if (typeof v === 'number') return v
                        const n = parseFloat(String(v))
                        return isNaN(n) ? 0 : n
                    }

                    const leftRaw = pos.left !== undefined ? pos.left : (f.left !== undefined ? f.left : (f.x || 0))
                    const topRaw = pos.top !== undefined ? pos.top : (f.top !== undefined ? f.top : (f.y || 0))
                    const widthRaw = pos.width !== undefined ? pos.width : (f.width !== undefined ? f.width : (f.w || 50))
                    const heightRaw = pos.height !== undefined ? pos.height : (f.height !== undefined ? f.height : (f.h || 16))

                    const left = parseVal(leftRaw) * scale
                    const top = parseVal(topRaw) * scale
                    const width = parseVal(widthRaw) * scale
                    const height = parseVal(heightRaw) * scale

                    el.style.left = left + 'px'
                    el.style.top = top + 'px'
                    el.style.width = Math.max(1, width) + 'px'
                    el.style.height = Math.max(1, height) + 'px'
                    el.style.fontSize = ((contentObj && contentObj.fontSize) ? parseVal(contentObj.fontSize) : (f.fontSize || 12)) * scale + 'px'
                    el.style.lineHeight = '1'
                    el.style.color = (contentObj && contentObj.color) ? contentObj.color : (f.color || '#222')

                    if (f.type === 'image') {
                        const img = document.createElement('img')
                        img.src = (f.image) || (contentObj && contentObj.src) || f.path || f.src || f.value || ''
                        img.style.width = '100%'
                        img.style.height = '100%'
                        img.style.objectFit = 'contain'
                        el.appendChild(img)
                    } else if (f.type === 'barcode') {
                        const text = (barcodeObj && barcodeObj.value) || (contentObj && contentObj.text) || f.value || f.text || ''
                        el.textContent = text
                        el.style.fontFamily = 'monospace'
                    } else {
                        let text = (contentObj && contentObj.text) || f.value || f.text || ''
                        if ((!text || String(text).trim() === '') && f.systemData) {
                            text = String(f.systemData)
                        }
                        el.textContent = text
                        el.style.textAlign = (contentObj && contentObj.textAlign) ? contentObj.textAlign : (f.textAlign || 'left')
                        if ((contentObj && contentObj.fontWeight === 'bold') || f.fontWeight === 'bold' || f.bold) el.style.fontWeight = 'bold'
                    }
                    preview.appendChild(el)
                } catch (err) {
                    
                }
            })

            wrap.appendChild(preview)
        },
        clearTagPreview() {
            const wrap = this.$refs.tagPreview
            if (!wrap) return
            wrap.innerHTML = ''
        }
    }
    ,
    watch: {
        'form.template_id': function () {
            this.renderTagPreview()
        }
    },
}
</script>
