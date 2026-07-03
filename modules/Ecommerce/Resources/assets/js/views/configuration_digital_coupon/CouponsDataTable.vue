<template>
    <div v-loading="loading">
        <!-- Botón toggle filtros -->
        <div class="btn-filter-content d-flex">
            <el-button
                type="secondary"
                class="btn-show-filter mb-3 me-3"
                :class="{ shift: isVisible }"
                @click="isVisible = !isVisible"
            >
                {{ isVisible ? "Ocultar filtros" : "Mostrar filtros" }}
            </el-button>
            <el-button v-if="hasActiveFilters" @click="clearFilters" class="mb-3" type="secondary" icon="el-icon-refresh">
                Limpiar filtros
            </el-button>
        </div>

        <!-- Filtros -->
        <div class="row mb-3" v-if="isVisible">
            <div class="col-lg-4 col-md-5 col-sm-12 pb-2">
                <el-input
                    v-model="filters.q"
                    placeholder="Buscar cupón..."
                    prefix-icon="el-icon-search"
                    style="width: 100%;"
                    @input="onSearchInput"
                    clearable
                ></el-input>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-12 pb-2">
                <el-select
                    v-model="filters.status"
                    placeholder="Todos los estados"
                    style="width: 100%;"
                    @change="applyFilters"
                    clearable
                >
                    <el-option label="Todos los estados" value=""></el-option>
                    <el-option label="Activos" value="active"></el-option>
                    <el-option label="Inactivos" value="inactive"></el-option>
                    <el-option label="Vencidos" value="expired"></el-option>
                </el-select>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-12 pb-2">
                <el-select
                    v-model="filters.type"
                    placeholder="Todos los tipos"
                    style="width: 100%;"
                    @change="applyFilters"
                    clearable
                >
                    <el-option label="Todos los tipos" value=""></el-option>
                    <el-option label="% Porcentaje" value="percentage"></el-option>
                    <el-option label="S/ Fijo" value="fixed"></el-option>
                </el-select>
            </div>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <slot name="heading"></slot>
                </thead>
                <tbody>
                    <slot
                        v-for="(row, index) in records"
                        :index="customIndex(index)"
                        :row="row"
                    ></slot>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <el-pagination
            :current-page.sync="pagination.current_page"
            :page-size="pagination.per_page"
            :total="pagination.total"
            layout="total, prev, pager, next"
            @current-change="getRecords"
        ></el-pagination>
    </div>
</template>

<script>
export default {
    data() {
        return {
            records: [],
            pagination: {
                current_page: 1,
                per_page: 15,
                total: 0,
            },
            filters: {
                q:      '',
                status: '',
                type:   '',
            },
            originalFilters: {
                q:      '',
                status: '',
                type:   '',
            },
            loading: false,
            searchTimeout: null,
            isVisible: false,
        };
    },
    created() {
        // Escucha eventos globales de recarga emitidos por el formulario
        this.$eventHub.$on('reloadData', () => {
            this.getRecords();
        });
    },
    mounted() {
        this.getRecords();
    },
    methods: {
        customIndex(index) {
            return (this.pagination.per_page * (this.pagination.current_page - 1)) + index + 1;
        },
        getRecords() {
            this.loading = true;

            const params = { page: this.pagination.current_page };

            if (this.filters.q)      params.q      = this.filters.q.trim();
            if (this.filters.status) params.status  = this.filters.status;
            if (this.filters.type)   params.type    = this.filters.type;

            this.$http.post('/ecommerce/discount-coupons/records', params)
                .then(response => {
                    this.records    = response.data.data;
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(response.data.meta.per_page);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        applyFilters() {
            this.pagination.current_page = 1;
            this.getRecords();
        },
        onSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 350);
        },
        clearFilters() {
            this.filters = { q: '', status: '', type: '' };
            this.originalFilters = { ...this.filters };
            this.pagination.current_page = 1;
            this.getRecords();
        },
    },
    computed: {
        hasActiveFilters() {
            return JSON.stringify(this.filters) !== JSON.stringify(this.originalFilters);
        },
    },
};
</script>