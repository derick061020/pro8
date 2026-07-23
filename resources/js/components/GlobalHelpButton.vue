<template>
    <div v-if="shouldShowHelp" class="d-inline-block ml-3">
        <!-- Mostrar popover si hay Tour Guiado Y Centro de Ayuda -->
        <el-popover
            v-if="hasGuidedTours && hasHelpCenter"
            placement="bottom-end"
            title=""
            width="350"
            trigger="click"
            popper-class="help-popover-custom"
            ref="helpPopover"
        >
            <div class="p-2">
                <!-- Método 4: Tour Guiado (Driver.js) -->
                <div class="text-center mb-2 mt-2">
                    <button type="button" class="btn btn-sm btn-info text-white w-100 rounded-pill" @click="startTourAndClosePopover">
                        <i class="fas fa-play-circle mr-1"></i> Iniciar Tour Guiado
                    </button>
                </div>

                <!-- Método 1: Panel Lateral -->
                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-link text-info p-0" @click="openFullHelpAndClosePopover">
                        Ver manual completo <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </div>

            <button
                slot="reference"
                type="button"
                class="btn btn-sm btn-outline-info rounded-pill"
                title="Ayuda y Tours"
            >
                <i class="fas fa-question-circle"></i> Ayuda
            </button>
        </el-popover>

        <!-- Mostrar botón directo al manual si SOLO hay Centro de Ayuda -->
        <button
            v-else-if="hasHelpCenter"
            type="button"
            class="btn btn-sm btn-outline-info rounded-pill"
            title="Centro de Ayuda"
            @click="openFullHelp"
        >
            <i class="fas fa-question-circle"></i> Ayuda
        </button>

        <!-- Mostrar botón directo al tour si SOLO hay Tour Guiado -->
        <button
            v-else-if="hasGuidedTours"
            type="button"
            class="btn btn-sm btn-outline-info rounded-pill"
            title="Iniciar Tour Guiado"
            @click="startTour"
        >
            <i class="fas fa-play-circle"></i> Iniciar Tour
        </button>
    </div>
</template>

<script>
import helpSummaries from '../helpers/help_summaries.json';
import tourDefinitions from '../helpers/tours.js';
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

export default {
    props: {
        configuration: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    data() {
        return {
            currentPath: '',
            summaries: helpSummaries,
            tours: tourDefinitions
        };
    },
    computed: {
        currentSummary() {
            return this.summaries[this.currentPath] || null;
        },
        currentTour() {
            return this.tours[this.currentPath] || null;
        },
        hasHelpCenter() {
            return this.configuration && this.configuration.enable_help_center;
        },
        hasGuidedTours() {
            return this.configuration && this.configuration.enable_guided_tours && this.currentTour !== null;
        },
        shouldShowHelp() {
            return this.hasHelpCenter || this.hasGuidedTours;
        }
    },
    created() {
        this.currentPath = window.location.pathname;
    },
    methods: {
        openFullHelpAndClosePopover() {
            this.openFullHelp();
            this.closePopover();
        },
        startTourAndClosePopover() {
            this.startTour();
            this.closePopover();
        },
        closePopover() {
            if (this.$refs.helpPopover) {
                this.$refs.helpPopover.showPopper = false;
            }
        },
        openFullHelp() {
            let topic = this.currentPath;
            if (topic.startsWith('/')) {
                topic = topic.substring(1);
            }
            this.$eventHub.$emit('openHelpDrawer', topic);
        },
        startTour() {
            if (!this.currentTour) return;
            // Ocultamos el popover haciendo un click forzado en el body o referenciando, 
            // pero Driver.js usualmente se superpone encima de todo.
            document.body.click(); 

            const driverObj = driver({
                showProgress: true,
                doneBtnText: 'Entendido',
                closeBtnText: 'Cerrar',
                nextBtnText: 'Siguiente',
                prevBtnText: 'Anterior',
                steps: this.currentTour
            });
            driverObj.drive();
        }
    }
};
</script>

<style>
.help-popover-custom {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
.ml-3 {
    margin-left: 1rem !important;
}
.mr-1 {
    margin-right: 0.25rem !important;
}

/* Solución para fondos transparentes en los Tours Guiados (Driver.js) */
body .driver-active-element {
    background-color: #ffffff !important;
    border-radius: 6px !important;
    padding: 6px !important; 
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5) !important;
}
</style>
