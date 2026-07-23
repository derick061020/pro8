<template>
    <el-drawer
        :visible.sync="visible"
        :with-header="false"
        size="400px"
        direction="rtl"
        custom-class="help-drawer-custom"
    >
        <div class="help-drawer-content" v-loading="loading">
            <div class="help-header">
                <div class="d-flex align-items-center">
                    <el-button 
                        v-if="history.length > 0"
                        icon="el-icon-back" 
                        circle 
                        size="mini" 
                        @click="goBack"
                        class="mr-2"
                    ></el-button>
                    <h3>Centro de Ayuda</h3>
                </div>
                <el-button icon="el-icon-close" circle size="small" @click="visible = false"></el-button>
            </div>
            
            <div class="help-body">
                <!-- Overlay de Zoom de Imagen -->
                <div v-if="previewImage" class="help-image-preview-overlay" @click="previewImage = null">
                    <img :src="previewImage" alt="Preview" @click.stop />
                    <el-button class="help-close-preview" icon="el-icon-close" circle @click="previewImage = null"></el-button>
                </div>
                <!-- Buscador para directorios -->
                <div v-if="contentType === 'directory'" class="mb-3">
                    <el-input
                        placeholder="Buscar manual..."
                        v-model="searchQuery"
                        prefix-icon="el-icon-search"
                        clearable
                        size="small"
                    ></el-input>
                </div>

                <!-- Listado de Manuales -->
                <div v-if="contentType === 'directory'" class="manual-list">
                    <p class="text-muted small mb-2">Manuales disponibles para esta sección:</p>
                    <div 
                        v-for="manual in filteredManuals" 
                        :key="manual.topic"
                        class="manual-item"
                        @click="openManual(manual.topic)"
                    >
                        <i class="el-icon-document mr-2"></i>
                        <span>{{ manual.label }}</span>
                        <i class="el-icon-arrow-right ml-auto"></i>
                    </div>
                    <div v-if="filteredManuals.length === 0" class="text-center py-4 text-muted">
                        No se encontraron manuales con "{{ searchQuery }}"
                    </div>
                </div>

                <!-- Contenido del Manual -->
                <div v-else-if="htmlContent" class="markdown-body" v-html="htmlContent"></div>
                
                <!-- Error o Vacío -->
                <div v-else-if="!loading" class="help-error">
                    No se pudo cargar la documentación.
                </div>
            </div>
        </div>
    </el-drawer>
</template>

<script>
export default {
    name: 'HelpDrawer',
    data() {
        return {
            visible: false,
            loading: false,
            htmlContent: null,
            currentTopic: null,
            targetHash: null,
            contentType: 'file', // 'file' o 'directory'
            manuals: [],
            searchQuery: '',
            history: [], // Historial de temas para botón volver
            previewImage: null // Estado para hacer zoom en la imagen
        }
    },
    computed: {
        filteredManuals() {
            if (!this.searchQuery) return this.manuals;
            
            const normalize = (str) => {
                return str ? str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase() : '';
            };
            
            const query = normalize(this.searchQuery);
            return this.manuals.filter(m => 
                normalize(m.label).includes(query) || 
                normalize(m.title).includes(query)
            );
        }
    },
    mounted() {
        this.$eventHub.$on('openHelpDrawer', (topic) => {
            if (this.currentTopic && this.currentTopic !== topic) {
                this.history.push(this.currentTopic);
            }
            
            const parts = topic.split('#');
            this.currentTopic = parts[0];
            this.targetHash = parts[1] || null;
            
            this.visible = true;
            this.fetchDocumentation();
        });
    },
    beforeDestroy() {
        this.$eventHub.$off('openHelpDrawer');
    },
    methods: {
        goBack() {
            if (this.history.length > 0) {
                const prev = this.history.pop();
                this.currentTopic = prev;
                this.fetchDocumentation();
            }
        },
        openManual(topic) {
            this.history.push(this.currentTopic);
            this.currentTopic = topic;
            this.fetchDocumentation();
        },
        async fetchDocumentation() {
            if (!this.currentTopic) return;
            
            this.loading = true;
            this.htmlContent = null;
            this.manuals = [];
            this.searchQuery = '';
            
            try {
                const response = await this.$http.get(`/api/help-center/${this.currentTopic}`);
                if (response.data && response.data.success) {
                    if (response.data.type === 'directory') {
                        this.contentType = 'directory';
                        this.manuals = response.data.manuals;
                    } else {
                        this.contentType = 'file';
                        this.htmlContent = response.data.html;
                        
                        this.$nextTick(() => {
                            // Implementar zoom en imágenes
                            const images = this.$el.querySelectorAll('.markdown-body img');
                            images.forEach(img => {
                                img.addEventListener('click', (e) => {
                                    this.previewImage = e.target.src;
                                });
                            });

                            if (this.targetHash) {
                                this.scrollToSection(this.targetHash);
                            }
                        });
                    }
                }
            } catch (error) {
                console.error("Error cargando ayuda:", error);
                if (error.response && error.response.status === 403) {
                    this.$message.warning("El Centro de Ayuda está deshabilitado.");
                    this.visible = false;
                } else {
                    this.htmlContent = "<p>Ocurrió un error al cargar la documentación.</p>";
                }
            } finally {
                this.loading = false;
            }
        },
        scrollToSection(hash) {
            const container = this.$el.querySelector('.help-body');
            if (!container) return;

            // Función para normalizar texto (quitar acentos y caracteres especiales)
            const normalize = (str) => {
                return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            };

            const normalizedHash = normalize(hash.replace(/-/g, ' '));
            const searchTerms = normalizedHash.split(' ').filter(t => t.length > 2);
            
            // Buscar en todos los encabezados
            const headers = container.querySelectorAll('h1, h2, h3, h4, h5, h6');
            let element = null;

            // Primero intentar coincidencia exacta por ID si el parser los genera
            element = container.querySelector(`#${hash}`);

            // Si no, buscar por contenido de texto normalizado
            if (!element) {
                element = Array.from(headers).find(h => {
                    const text = normalize(h.textContent);
                    return searchTerms.every(term => text.includes(term));
                });
            }

            if (element) {
                // Hacer scroll
                setTimeout(() => {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Resaltar visualmente
                    element.classList.add('highlight-help-section');
                    setTimeout(() => {
                        element.classList.remove('highlight-help-section');
                    }, 3000);
                }, 300);
            }
        }
    }
}
</script>

<style scoped>
.help-drawer-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #fcfcfc;
}

.help-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #ebeef5;
    background-color: #fff;
}

.help-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #303133;
    font-weight: 600;
}

.help-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
}

/* Estilos básicos para el Markdown renderizado (GitHub-like) */
.markdown-body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
    font-size: 14px;
    line-height: 1.6;
    color: #24292e;
}

.markdown-body :deep(h1),
.markdown-body :deep(h2),
.markdown-body :deep(h3) {
    margin-top: 24px;
    margin-bottom: 16px;
    font-weight: 600;
    line-height: 1.25;
}

.markdown-body :deep(h1) { font-size: 1.5em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
.markdown-body :deep(h2) { font-size: 1.3em; border-bottom: 1px solid #eaecef; padding-bottom: 0.3em; }
.markdown-body :deep(h3) { font-size: 1.1em; }

.markdown-body :deep(p),
.markdown-body :deep(blockquote),
.markdown-body :deep(ul),
.markdown-body :deep(ol) {
    margin-top: 0;
    margin-bottom: 16px;
}

.markdown-body :deep(ul),
.markdown-body :deep(ol) {
    padding-left: 2em;
}

.markdown-body :deep(blockquote) {
    padding: 0 1em;
    color: #6a737d;
    border-left: 0.25em solid #dfe2e5;
}

.markdown-body :deep(code) {
    padding: 0.2em 0.4em;
    margin: 0;
    font-size: 85%;
    background-color: rgba(27,31,35,0.05);
    border-radius: 3px;
}

.manual-list {
    display: flex;
    flex-direction: column;
}

.manual-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 8px;
    background-color: #fff;
    border: 1px solid #ebeef5;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #606266;
}

.manual-item:hover {
    border-color: #409eff;
    color: #409eff;
    background-color: #f0f7ff;
    transform: translateX(5px);
}

.manual-item i {
    font-size: 1.1rem;
}

.manual-item span {
    font-size: 0.95rem;
    font-weight: 500;
}

:deep(.highlight-help-section) {
    animation: highlight-fade 3s ease-out;
    border-radius: 4px;
    padding: 2px 5px;
}

@keyframes highlight-fade {
    0% {
        background-color: #28a74533;
        box-shadow: 0 0 0 4px #28a74533;
    }
    100% {
        background-color: transparent;
        box-shadow: 0 0 0 0 transparent;
    }
}

.help-image-preview-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0,0,0,0.8);
    z-index: 99999;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: zoom-out;
}

.help-image-preview-overlay img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    cursor: default;
}

.help-close-preview {
    position: absolute;
    top: 20px;
    right: 20px;
}

.markdown-body :deep(img) {
    cursor: zoom-in;
    max-width: 100%;
    transition: transform 0.2s;
    border-radius: 4px;
    border: 1px solid #ebeef5;
}

.markdown-body :deep(img:hover) {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Estilos para Admonitions */
.markdown-body :deep(.admonition) {
    margin: 1.5em 0;
    padding: 12px 16px;
    border-left: 4px solid #ccc;
    border-radius: 4px;
    background-color: #f8f9fa;
}

.markdown-body :deep(.admonition-title) {
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    text-transform: uppercase;
}

.markdown-body :deep(.admonition-info) {
    border-left-color: #3578e5;
    background-color: #ebf2ff;
}
.markdown-body :deep(.admonition-info .admonition-title) { color: #3578e5; }

.markdown-body :deep(.admonition-tip) {
    border-left-color: #00a400;
    background-color: #e6ffed;
}
.markdown-body :deep(.admonition-tip .admonition-title) { color: #00a400; }

.markdown-body :deep(.admonition-warning) {
    border-left-color: #e7c000;
    background-color: #fff8e1;
}
.markdown-body :deep(.admonition-warning .admonition-title) { color: #b08d00; }

.markdown-body :deep(.admonition-danger) {
    border-left-color: #fa383e;
    background-color: #ffeef0;
}
.markdown-body :deep(.admonition-danger .admonition-title) { color: #fa383e; }

.markdown-body :deep(.admonition-note) {
    border-left-color: #606770;
    background-color: #f6f7f8;
}
.markdown-body :deep(.admonition-note .admonition-title) { color: #606770; }

.markdown-body :deep(.admonition p:last-child) {
    margin-bottom: 0;
}
</style>
