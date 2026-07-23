<template>
    <el-tooltip class="item" effect="light" popper-class="modern-help-tooltip" :placement="placement">
        <div slot="content" class="help-tooltip-content">
            <div v-if="loading"><i class="el-icon-loading"></i> Cargando...</div>
            <div v-else v-html="content"></div>
            <template v-if="enableHelp">
                <div class="mt-2 text-right border-top pt-1">
                    <a href="javascript:void(0)" @click.prevent="openManual" class="help-link">
                        Ver manual completo <i class="fas fa-external-link-alt ml-1"></i>
                    </a>
                </div>
            </template>
        </div>
        <i class="fa fa-info-circle help-tooltip-icon"></i>
    </el-tooltip>
</template>

<script>
export default {
    name: 'HelpTooltip',
    props: {
        topic: { type: String, required: true },
        section: { type: String, required: true },
        placement: { type: String, default: 'top-end' },
        defaultText: { type: String, default: '' }
    },
    data() {
        return {
            content: this.defaultText || 'Cargando información...',
            loading: false,
            enableHelp: false
        }
    },
    mounted() {
        // Intentar detectar la configuración de ayuda de varias fuentes comunes en este proyecto
        const config = this.$root.config || window.config || {};
        this.enableHelp = config.enable_help_center !== undefined ? config.enable_help_center : true;
        
        if (this.enableHelp) {
            this.fetchContent();
        }
    },
    methods: {
        async fetchContent() {
            this.loading = true;
            try {
                // El endpoint que acabamos de crear en HelpController
                const response = await this.$http.get(`/api/help-center/${this.topic}?section=${this.section}`);
                if (response.data.success) {
                    this.content = response.data.html;
                }
            } catch (e) {
                console.error("Error al cargar el tooltip de ayuda:", e);
            } finally {
                this.loading = false;
            }
        },
        openManual() {
            this.$eventHub.$emit('openHelpDrawer', `${this.topic}#${this.section}`);
        }
    }
}
</script>

<style scoped>
.help-tooltip-content {
    max-width: 200px;
    line-height: 1.2;
    font-size: 0.8rem;
}
.help-tooltip-icon {
    cursor: help;
    margin-left: 2px;
}
.help-link {
    color: #409eff; 
    font-size: 0.7rem;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.2s;
}
.help-link i {
    font-size: 0.6rem;
}
.help-link:hover {
    color: #66b1ff;
    text-decoration: underline;
}
</style>

<style>
.modern-help-tooltip {
    border-radius: 8px !important;
    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2) !important;
    border: 1px solid #ebeef5 !important;
    padding: 12px 15px !important;
    color: #2c3e50 !important;
    font-family: inherit !important;
}
/* Arrow styling overrides for light theme */
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=top] .popper__arrow {
    border-top-color: #ebeef5 !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=top] .popper__arrow::after {
    border-top-color: #fff !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=bottom] .popper__arrow {
    border-bottom-color: #ebeef5 !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=bottom] .popper__arrow::after {
    border-bottom-color: #fff !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=right] .popper__arrow {
    border-right-color: #ebeef5 !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=right] .popper__arrow::after {
    border-right-color: #fff !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=left] .popper__arrow {
    border-left-color: #ebeef5 !important;
}
.modern-help-tooltip.el-tooltip__popper.is-light[x-placement^=left] .popper__arrow::after {
    border-left-color: #fff !important;
}
</style>
