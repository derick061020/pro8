<template>
    <div class="card">
        <div class="card-body" v-loading="loadingRecord || loadingUpload || loadingDelete">
            <h4>Logo de la App</h4>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="image-container image-container-fluid">
                        <div v-if="appLogo">
                            <img
                                :src="previewUrl"
                                alt="Logo APP"
                                class="img-fluid img-small img-thumbnail w-100"
                                @error="previewUrl = PLACEHOLDER"
                            />
                            <div class="overlay">
                                <el-button
                                    class="me-2 btn btn-sm"
                                    @click="openFilePicker"
                                    :loading="loadingUpload"
                                    :disabled="loadingUpload"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                    Cambiar
                                </el-button>
                                <el-button
                                    @click="deleteLogo"
                                    :loading="loadingDelete"
                                    size="mini"
                                    type="danger"
                                    class="btn btn-sm"
                                    plain
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </el-button>
                            </div>
                        </div>
                        <div
                            v-else
                            class="d-flex flex-column justify-content-center align-items-center gap-2 p-2 drop-zone"
                            :class="{'drop-zone-active': isDragging}"
                            @dragover="onDragOver"
                            @dragleave="onDragLeave"
                            @drop="onDrop"
                        >
                            <div class="p-2 bg-light rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 8h.01"></path><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path></svg>
                            </div>
                            <div>
                                <p class="text-center">Arrastra una imagen aquí o haz clic para seleccionar</p>
                                <p class="text-muted text-center">PNG, JPG, GIF o SVG · Máx. 2 MB</p>
                            </div>
                            <el-button
                                @click="openFilePicker"
                                :loading="loadingUpload"
                                :disabled="loadingUpload"
                                class="btn btn-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:-2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                Seleccionar imagen
                            </el-button>
                        </div>
                    </div>
                    <input
                        type="file"
                        ref="fileInput"
                        @change="onFileChange"
                        class="hidden"
                        accept="image/*"
                    />
                    <div class="sub-title text-muted mt-2"><small>Se recomienda imagen con fondo transparente o blanco</small></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const PLACEHOLDER_SVG = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 12l3 -3c.928 -.893 2.072 -.893 3 0l4 4" /><path d="M13 12l2 -2c.928 -.893 2.072 -.893 3 0l2 2" /><path d="M14 7l.01 0" /></svg>`
    const PLACEHOLDER = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(PLACEHOLDER_SVG)}`

    export default {
        data() {
            return {
                PLACEHOLDER,
                appLogo: null,
                previewUrl: PLACEHOLDER,
                loadingRecord: false,
                loadingUpload: false,
                loadingDelete: false,
                isDragging: false,
                headers: headers_token,
            }
        },
        created() {
            this.getData()
        },
        methods: {
            getData() {
                this.loadingRecord = true
                this.$http.get('/companies/record')
                    .then(response => {
                        if (response.data !== '') {
                            this.appLogo = response.data.data.app_logo || null
                            this.previewUrl = this.resolveUrl(this.appLogo)
                        }
                    })
                    .finally(() => {
                        this.loadingRecord = false
                    })
            },
            resolveUrl(value) {
                if (!value || typeof value !== 'string') return PLACEHOLDER
                if (value.startsWith('http://') || value.startsWith('https://')) return value
                if (value.startsWith('/')) return value
                if (value.startsWith('storage/')) return `/${value}`
                return `/storage/uploads/logos/${value}`
            },
            openFilePicker() {
                this.$refs.fileInput && this.$refs.fileInput.click()
            },
            onFileChange(event) {
                const file = event?.target?.files?.[0]
                if (!file) return
                this.upload(file)
                if (event.target) event.target.value = ''
            },
            upload(file) {
                if (file.size > 2 * 1024 * 1024) {
                    this.$message.error('El archivo no debe superar los 2 MB')
                    return
                }

                const previousPreviewUrl = this.previewUrl

                const reader = new FileReader()
                reader.addEventListener('load', () => {
                    this.previewUrl = reader.result
                })
                reader.readAsDataURL(file)

                const payload = new FormData()
                payload.append('file', file)
                payload.append('type', 'app_logo')

                this.loadingUpload = true
                this.$http.post('/companies/uploads', payload, { headers: this.headers })
                    .then(response => {
                        const data = response?.data
                        if (data?.success) {
                            this.$message.success(data.message)
                            this.appLogo = data.name
                            // previewUrl ya tiene el data URL del FileReader — no cambiar
                        } else {
                            this.$message.error(data?.message || 'Error al subir la imagen')
                            this.previewUrl = previousPreviewUrl
                        }
                    })
                    .catch(error => {
                        this.$message.error(error?.response?.data?.message || 'Error al subir la imagen')
                        this.previewUrl = previousPreviewUrl
                    })
                    .finally(() => {
                        this.loadingUpload = false
                    })
            },
            deleteLogo() {
                this.$confirm('¿Está seguro de eliminar el Logo APP?', 'Confirmar eliminación', {
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning',
                }).then(() => {
                    this.loadingDelete = true
                    this.$http.delete('/companies/delete-logo', { data: { type: 'app_logo' } })
                        .then(response => {
                            if (response.data?.success) {
                                this.$message.success(response.data.message)
                                this.appLogo = null
                                this.previewUrl = PLACEHOLDER
                            } else {
                                this.$message.error(response.data?.message || 'Error al eliminar')
                            }
                        })
                        .catch(error => {
                            this.$message.error(error?.response?.data?.message || 'Error al eliminar el logo')
                        })
                        .finally(() => {
                            this.loadingDelete = false
                        })
                }).catch(() => {})
            },
            onDragOver(event) {
                event.preventDefault()
                event.stopPropagation()
                this.isDragging = true
            },
            onDragLeave(event) {
                event.preventDefault()
                event.stopPropagation()
                this.isDragging = false
            },
            onDrop(event) {
                event.preventDefault()
                event.stopPropagation()
                this.isDragging = false

                const file = event.dataTransfer?.files?.[0]
                if (!file) return
                if (!file.type.startsWith('image/')) {
                    this.$message.error('Solo se permiten archivos de imagen')
                    return
                }
                this.upload(file)
            },
        }
    }
</script>

<style scoped>
.image-container {
    position: relative;
    display: inline-block;
    width: 100%;
}
.overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(255, 255, 255, 0.908);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.image-container:hover .overlay {
    opacity: 1;
}
.img-small {
    height: auto;
    max-height: 80px;
    object-fit: contain;
}
.drop-zone {
    transition: all 0.3s ease;
    border: 2px dashed transparent;
    border-radius: 0.5rem;
}
.drop-zone-active {
    border-color: #409eff;
}
</style>
