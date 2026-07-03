<template>
    <div class="ag-notification-wrapper">
        <el-dropdown trigger="click" @command="handleCommand">
            <span class="el-dropdown-link notification-icon text-secondary">
                <el-badge :value="totalCount" :hidden="totalCount === 0" class="ag-bell-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>
                </el-badge>
            </span>
            <el-dropdown-menu slot="dropdown" class="ag-notification-menu">
                <el-dropdown-item command="redirect">
                    <template v-if="totalCount > 0">
                        Comprobantes sin enviar: <strong>{{ totalCount }}</strong>
                    </template>
                    <template v-else>
                        ¡Todo al día! No hay pendientes
                    </template>
                </el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>

<script>
export default {
    props: {
        initialCount: {
            type: Number,
            default: 0
        },
        redirectUrl: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            totalCount: this.initialCount,
            polling: null
        }
    },
    mounted() {
        this.startPolling();
    },
    beforeDestroy() {
        this.stopPolling();
    },
    methods: {
        handleCommand(command) {
            if (command === 'redirect') {
                window.location.href = this.redirectUrl;
            }
        },
        async fetchCount() {
            try {
                // Endpoint ligero que devuelve {"total": X}
                const response = await axios.get('/documents/not-sent/count');
                if (response.data && typeof response.data.total !== 'undefined') {
                    this.totalCount = response.data.total;
                }
            } catch (error) {
                // Fallo silencioso para evitar ruidos en la consola del usuario
                console.log("No se pudo actualizar el contador de notificaciones.");
            }
        },
        startPolling() {
            // Actualización cada 60 segundos
            this.polling = setInterval(() => {
                this.fetchCount();
            }, 60000);
        },
        stopPolling() {
            if (this.polling) {
                clearInterval(this.polling);
                this.polling = null;
            }
        }
    }
}
</script>

<style scoped>
.ag-notification-wrapper {
    display: inline-block;
    vertical-align: middle;
}

.ag-bell-container {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffffff;
    padding: 6px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.ag-bell-container:hover {
    background-color: #f8f9fa;
}

.ag-bell-svg {
    color: #67757c !important;
    width: 18px;
    height: 18px;
}

.ag-bell-badge :deep(.el-badge__content) {
    background-color: #ff4d4d !important; /* Rojo vibrante */
    top: 0px !important;
    right: 2px !important;
    border: none;
    padding: 0 5px;
    font-size: 10px;
    height: 16px;
    line-height: 16px;
    min-width: 18px; /* Un poco más ancho para calcar el del carrito */
    text-align: center;
    border-radius: 3px !important; /* Cuadrado redondeado como el carrito */
    font-weight: bold;
}

.ag-notification-menu {
    background-color: #ffffff !important;
    z-index: 5000 !important;
}
</style>