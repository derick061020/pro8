<template>
    <div>
        <!-- Barra de filtros -->
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <el-input
                v-model="filters.q"
                placeholder="Buscar por nombre..."
                size="small"
                clearable
                style="width: 220px;"
                @input="onSearchInput"
                @clear="applyFilters"
            ></el-input>
            <el-select
                v-model="filters.active"
                placeholder="Estado"
                size="small"
                clearable
                style="width: 140px;"
                @change="applyFilters"
            >
                <el-option label="Activo"   value="1"></el-option>
                <el-option label="Inactivo" value="0"></el-option>
            </el-select>
            <el-button
                v-if="hasActiveFilters"
                size="small"
                @click="clearFilters"
            >Limpiar</el-button>
        </div>

        <!-- Tabla -->
        <div style="overflow-x: auto;">
            <table class="table table-hover table-sm">
                <thead>
                    <slot name="heading"></slot>
                </thead>
                <tbody v-if="!loading">
                    <slot v-for="(row, index) in records" :row="row" :index="index"></slot>
                    <tr v-if="records.length === 0">
                        <td colspan="6" class="text-center text-muted py-4">No se encontraron zonas de delivery.</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fa fa-spinner fa-spin"></i> Cargando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <el-pagination
            v-if="pagination.total > pagination.per_page"
            @current-change="onPageChange"
            :current-page.sync="pagination.current_page"
            :page-size="pagination.per_page"
            :total="pagination.total"
            layout="total, prev, pager, next"
            background
            small
        ></el-pagination>
    </div>
</template>

<script>
export default {
    name: 'DeliveryZonesDataTable',
    data() {
        return {
            records: [],
            loading: false,
            pagination: {
                current_page: 1,
                per_page: 15,
                total: 0,
            },
            filters: {
                q: '',
                active: '',
            },
            originalFilters: { q: '', active: '' },
            searchTimeout: null,
        };
    },
    computed: {
        hasActiveFilters() {
            return JSON.stringify(this.filters) !== JSON.stringify(this.originalFilters);
        },
    },
    mounted() {
        this.getRecords();
        // Escucha el evento global para refrescar tras crear/editar/eliminar
        this.$eventHub.$on('reloadData', this.getRecords);
    },
    beforeDestroy() {
        this.$eventHub.$off('reloadData', this.getRecords);
    },
    methods: {
        getRecords() {
            this.loading = true;
            this.$http.post('/ecommerce/delivery-zones/records', {
                ...this.filters,
                page: this.pagination.current_page,
            }).then(response => {
                const data = response.data;
                this.records = data.data || [];
                if (data.meta) {
                    this.pagination.current_page = data.meta.current_page;
                    this.pagination.per_page     = data.meta.per_page;
                    this.pagination.total        = data.meta.total;
                }
            }).finally(() => {
                this.loading = false;
            });
        },
        applyFilters() {
            this.pagination.current_page = 1;
            this.getRecords();
        },
        onSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.applyFilters(), 350);
        },
        clearFilters() {
            this.filters = { ...this.originalFilters };
            this.applyFilters();
        },
        onPageChange(page) {
            this.pagination.current_page = page;
            this.getRecords();
        },
    },
};
</script>
