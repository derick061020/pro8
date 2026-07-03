<template>
    <div v-loading="loading_submit">
        <div class="row ">
            <div class="col-md-12 col-lg-12 col-xl-12 filter-container" v-if="applyFilter && !hideFilter">
                <div class="btn-filter-content d-flex">
                    <el-button
                        type="secondary"
                        class="btn-show-filter mb-2 me-2"
                        :class="{ shift: isVisible }"
                        @click="toggleInformation"
                    >
                        {{ isVisible ? "Ocultar filtros" : "Mostrar filtros" }}
                    </el-button>
                    <el-button
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="mb-2"
                        type="secondary"
                        icon="el-icon-refresh"
                    >
                        Limpiar filtros
                    </el-button>
                </div>
                <div v-if="isVisible" class="row mx-0">
                    <!-- Búsqueda de nombre/descripción -->
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-input
                            v-model="planFilters.q"
                            placeholder="Buscar plan..."
                            prefix-icon="el-icon-search"
                            style="width: 100%;"
                            @input="onSearchInput"
                            clearable
                        ></el-input>
                    </div>

                    <!-- Filtro por frecuencia/período -->
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.frequency"
                            placeholder="Frecuencia"
                            style="width: 100%;"
                            @change="applyFilters"
                            clearable
                        >
                            <el-option label="Todas" value=""></el-option>
                            <el-option
                                v-for="period in periods"
                                :key="period.id"
                                :label="period.name"
                                :value="period.id"
                            ></el-option>
                        </el-select>
                    </div>

                    <!-- Filtro por estado -->
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.status"
                            placeholder="Estado"
                            style="width: 100%;"
                            @change="applyFilters"
                            clearable
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option label="Activos" :value="true"></el-option>
                            <el-option label="Inactivos" :value="false"></el-option>
                        </el-select>
                    </div>

                    <!-- Filtro por período de prueba -->
                    <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                        <el-select
                            v-model="planFilters.trial"
                            placeholder="Período de Prueba"
                            style="width: 100%;"
                            @change="applyFilters"
                            clearable
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option label="Con Prueba" value="with"></el-option>
                            <el-option label="Sin Prueba" value="without"></el-option>
                        </el-select>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <slot name="heading"></slot>
                        </thead>
                        <tbody>
                        <slot
                            v-for="(row, index) in table_data"
                            :index="customIndex(index)"
                            :row="row"
                        ></slot>
                        </tbody>
                    </table>
                    <div>
                        <el-pagination
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                            :total="pagination.total"
                            layout="total, prev, pager, next"
                            @current-change="getRecords"
                        >
                        </el-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

export default {
    props: {
        extraquery: {
            type: Object,
            default: () => ({}),
            required: false
        },
        periods: {
            type: Array,
            default: () => [],
            required: false
        },
        productType: {
            type: String,
            required: false,
            default: ''
        },
        applyFilter: {
            type: Boolean,
            default: true,
            required: false
        },
        pharmacy: Boolean,
        hideFilter: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            planFilters: {
                q: null,           // búsqueda por nombre/descripción
                frequency: null,   // período/frecuencia
                status: null,      // estado (true/false)
                trial: null        // período de prueba (with/without)
            },
            originalFilters: {
                q: null,
                frequency: null,
                status: null,
                trial: null
            },
            records: [],
            pagination: {},
            isVisible: false,
            loading_submit: false,
            fromPharmacy: false,
            showLeftShadow: false,
            showRightShadow: false,
            searchTimeout: null,  // para el debounce
        };
    },
    created() {
        this.loadConfiguration();
        if (this.pharmacy !== undefined && this.pharmacy === true) {
            this.fromPharmacy = true;
        }
        this.$eventHub.$on("reloadData", () => {
            //  console.log('Dispara reloadData')
            this.getRecords();
        });
        this.$root.$refs.DataTable = this;
    },
    async mounted() {
        await this.getRecords();

        this.$nextTick(() => {
            const el = this.$refs.scrollContainer;
            if (el) {
                el.addEventListener('scroll', this.checkScrollShadows);
                this.checkScrollShadows();
            }
        });
    },
    methods: {
        checkScrollShadows() {
            const el = this.$refs.scrollContainer;
            if (!el) return;

            const scrollLeft = el.scrollLeft;
            const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;

            this.showLeftShadow = scrollLeft > 1;
            this.showRightShadow = scrollRight > 1;
        },
        toggleInformation() {
            this.isVisible = !this.isVisible;
        },
        ...mapActions([
            'loadConfiguration',
        ]),
        customIndex(index) {
            return ( this.pagination.per_page * (this.pagination.current_page - 1) + index + 1 );
        },
        getRecords() {
            this.loading_submit = true;
            let url = `/full_suscription/${this.resource}/records`;
            return this.$http
                .post(url,this.getQueryParameters())
                .then(response => {
                    this.records = response.data.data;
                    this.$store.commit('setTableData',this.records)
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                })
                .catch(() => {
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        getQueryParameters() {
            // Construir objeto de parámetros de consulta con los filtros
            const queryParams = {
                page: this.pagination.current_page,
                limit: this.limit,
                isPharmacy: this.fromPharmacy,
            };

            // Agregar filtros de planes si tienen valor
            if (this.planFilters.q && this.planFilters.q.trim().length > 0) {
                queryParams.q = this.planFilters.q.trim();
            }
            if (this.planFilters.frequency && this.planFilters.frequency !== '') {
                queryParams.frequency = this.planFilters.frequency;
            }
            if (this.planFilters.status !== null && this.planFilters.status !== '') {
                queryParams.status = this.planFilters.status;
            }
            if (this.planFilters.trial && this.planFilters.trial !== '') {
                queryParams.trial = this.planFilters.trial;
            }

            return queryParams;
        },

        // Búsqueda con debounce (300ms)
        onSearchInput() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 300);
        },

        // Aplicar filtros y recargar datos
        applyFilters() {
            // Resetear a página 1 cuando se aplican filtros
            this.pagination.current_page = 1;
            this.getRecords();
        },

        // Limpiar todos los filtros
        clearFilters() {
            this.planFilters = {
                q: null,
                frequency: null,
                status: null,
                trial: null
            };
            this.originalFilters = { ...this.planFilters };
            this.pagination.current_page = 1;
            this.getRecords();
        },
    },

    computed: {
        ...mapState([
            'config',
            'table_data',
            'resource',
        ]),

        hasActiveFilters() {
            return JSON.stringify(this.planFilters) !== JSON.stringify(this.originalFilters);
        },
    },
};
</script>
