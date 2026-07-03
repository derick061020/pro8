<template>
    <div v-loading="loading_submit">
        <div class="row">
            <!-- Panel de filtros -->
            <div class="col-md-12 col-lg-12 col-xl-12 filter-container">
                <div class="btn-filter-content mb-1 d-flex">
                    <el-button
                        type="secondary"
                        class="btn-show-filter mb-2 me-2"
                        :class="{ shift: isVisible }"
                        @click="toggleFilters"
                    >
                        {{ isVisible ? 'Ocultar filtros' : 'Mostrar filtros' }}
                    </el-button>
                    <el-button v-if="hasActiveFilters" @click="clearFilters" class="mb-2" type="secondary" icon="el-icon-refresh">
                        Limpiar filtros
                    </el-button>
                </div>

                <div v-if="isVisible" class="row mx-0 mb-2">
                    <!-- Búsqueda por cliente o documento -->
                    <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
                        <el-input
                            v-model="filters.q"
                            placeholder="Cliente o documento..."
                            prefix-icon="el-icon-search"
                            style="width: 100%;"
                            clearable
                            @input="onSearchInput"
                        ></el-input>
                    </div>

                    <!-- Filtro por estado -->
                    <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
                        <el-select
                            v-model="filters.status"
                            placeholder="Estado"
                            style="width: 100%;"
                            clearable
                            @change="applyFilters"
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option label="Activa"              value="activa"></el-option>
                            <el-option label="En prueba"           value="en_prueba"></el-option>
                            <el-option label="Pendiente de pago"   value="pendiente_pago"></el-option>
                            <el-option label="Vencida"             value="vencida"></el-option>
                        </el-select>
                    </div>

                    <!-- Filtro por tipo de plan -->
                    <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
                        <el-select
                            v-model="filters.plan_id"
                            placeholder="Tipo de plan"
                            style="width: 100%;"
                            clearable
                            @change="applyFilters"
                        >
                            <el-option label="Todos" value=""></el-option>
                            <el-option
                                v-for="plan in plans"
                                :key="plan.id"
                                :label="plan.name"
                                :value="plan.id"
                            ></el-option>
                        </el-select>
                    </div>

                    <!-- Filtro por vencimiento -->
                    <div class="col-lg-3 col-md-6 col-sm-12 pb-2">
                        <el-select
                            v-model="filters.vencimiento"
                            placeholder="Vencimiento"
                            style="width: 100%;"
                            clearable
                            @change="applyFilters"
                        >
                            <el-option label="Cualquier fecha"  value=""></el-option>
                            <el-option label="Vence hoy"        value="vence_hoy"></el-option>
                            <el-option label="Próximos 7 días"  value="proximos_7"></el-option>
                            <el-option label="Próximos 30 días" value="proximos_30"></el-option>
                            <el-option label="Ya vencidos"      value="ya_vencidos"></el-option>
                        </el-select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="col-md-12">
                <div class="scroll-shadow shadow-left"  v-show="showLeftShadow"></div>
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
                        ></el-pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapState } from 'vuex/dist/vuex.mjs';

export default {
    props: {
        plans: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            filters: {
                q:           null,
                status:      null,
                plan_id:     null,
                vencimiento: null,
            },
            originalFilters: {
                q:           null,
                status:      null,
                plan_id:     null,
                vencimiento: null,
            },
            pagination: {
                current_page: 1,
                per_page:     20,
                total:        0,
            },
            isVisible:       false,
            loading_submit:  false,
            showLeftShadow:  false,
            showRightShadow: false,
            searchTimeout:   null,
        };
    },
    created() {
        this.loadConfiguration();

        this.$eventHub.$on('reloadData', () => {
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
        ...mapActions(['loadConfiguration']),

        checkScrollShadows() {
            const el = this.$refs.scrollContainer;
            if (!el) return;
            const scrollLeft  = el.scrollLeft;
            const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;
            this.showLeftShadow  = scrollLeft > 1;
            this.showRightShadow = scrollRight > 1;
        },

        toggleFilters() {
            this.isVisible = !this.isVisible;
        },

        customIndex(index) {
            return this.pagination.per_page * (this.pagination.current_page - 1) + index + 1;
        },

        getQueryParameters() {
            const params = {
                page: this.pagination.current_page,
            };
            if (this.filters.q && this.filters.q.trim().length > 0) {
                params.q = this.filters.q.trim();
            }
            if (this.filters.status) {
                params.status = this.filters.status;
            }
            if (this.filters.plan_id) {
                params.plan_id = this.filters.plan_id;
            }
            if (this.filters.vencimiento) {
                params.vencimiento = this.filters.vencimiento;
            }
            return params;
        },

        getRecords() {
            this.loading_submit = true;
            return this.$http
                .post(`/full_suscription/${this.resource}/records`, this.getQueryParameters())
                .then(response => {
                    this.$store.commit('setTableData', response.data.data);
                    this.pagination          = response.data.meta;
                    this.pagination.per_page = parseInt(response.data.meta.per_page);
                })
                .catch(() => {})
                .finally(() => {
                    this.loading_submit = false;
                });
        },

        // Búsqueda con debounce de 350 ms
        onSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 350);
        },

        applyFilters() {
            this.pagination.current_page = 1;
            this.getRecords();
        },

        clearFilters() {
            this.filters = { q: null, status: null, plan_id: null, vencimiento: null };
            this.originalFilters = { ...this.filters };
            this.pagination.current_page = 1;
            this.getRecords();
        },
    },
    computed: {
        ...mapState(['config', 'table_data', 'resource']),

        hasActiveFilters() {
            return JSON.stringify(this.filters) !== JSON.stringify(this.originalFilters);
        },
    },
};
</script>
