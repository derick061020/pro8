<template>
    <div class="coupon-list">

        <!-- Tarjeta principal -->
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <coupons-data-table>
                    <tr slot="heading">
                        <th class="text-start">Código</th>
                        <th class="text-start">Tipo</th>
                        <th class="text-end">Descuento</th>
                        <th class="text-end">Compra Mín.</th>
                        <th class="text-end">Compra Máx.</th>
                        <th class="text-start">Usos</th>
                        <th class="text-start">Vigencia</th>
                        <th class="text-center">Activo</th>
                        <th class="text-end">Opciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <!-- Código -->
                        <td class="text-start">
                            <span class="badge bg-dark text-white fw-bold" style="letter-spacing: 1px; font-size: 0.8rem;">
                                {{ row.code }}
                            </span>
                        </td>
                        <!-- Tipo -->
                        <td class="text-start">
                            <el-tag :type="row.type === 'percentage' ? 'primary' : 'warning'" size="small">
                                {{ row.type_label }}
                            </el-tag>
                        </td>
                        <!-- Descuento -->
                        <td class="text-end fw-bold">{{ row.amount_formatted }}</td>
                        <!-- Compra mínima -->
                        <td class="text-end">
                            <template v-if="row.has_purchase_limits && row.min_amount">
                                S/ {{ row.min_amount | toDecimals(2) }}
                            </template>
                            <template v-else>&mdash;</template>
                        </td>
                        <!-- Compra máxima -->
                        <td class="text-end">
                            <template v-if="row.has_purchase_limits && row.max_amount">
                                S/ {{ row.max_amount | toDecimals(2) }}
                            </template>
                            <template v-else>&mdash;</template>
                        </td>
                        <!-- Usos -->
                        <td class="text-start">
                            <template v-if="row.has_usage_limits && row.max_total_uses">
                                <div class="usage-wrapper">
                                    <div class="usage-track">
                                        <div class="usage-fill" :style="{ width: ((row.total_uses || 0) / row.max_total_uses * 100) + '%' }"></div>
                                    </div>
                                    <div class="usage-text">
                                        <small>{{ row.total_uses || 0 }}/{{ row.max_total_uses }}</small>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <span class="text-muted">&#8734; ilimitado</span>
                            </template>
                        </td>
                        <!-- Vigencia -->
                        <td class="text-start">
                            <template v-if="row.expires_at_formatted">
                                <el-tag v-if="row.is_expired" type="danger" size="small">
                                    {{ row.expires_at_formatted }}
                                </el-tag>
                                <span v-else>{{ row.expires_at_formatted }}</span>
                            </template>
                            <template v-else>&mdash;</template>
                        </td>
                        <!-- Estado activo/inactivo -->
                        <td class="text-center">
                            <el-switch
                                v-model="row.active"
                                active-color="#13ce66"
                                inactive-color="#ff4949"
                                @change="toggleStatus(row)"
                            ></el-switch>
                        </td>
                        <!-- Acciones -->
                        <td class="text-end">
                            <button
                                class="btn btn-xs btn-info btn-shad me-1"
                                type="button"
                                @click.prevent="clickEdit(row)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" />
                                    <path d="M16 5l3 3" />
                                </svg>
                            </button>
                            <button
                                class="btn btn-xs btn-danger btn-shad"
                                type="button"
                                @click.prevent="clickDelete(row.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </coupons-data-table>
            </div>

            <!-- Modal de creación / edición -->
            <coupon-form
                :showDialog.sync="showDialog"
                :couponRecord.sync="selectedCoupon"
            ></coupon-form>
        </div>
    </div>
</template>

<script>
import CouponsDataTable from './CouponsDataTable.vue';
import CouponForm       from './form.vue';
import { deletable } from '@mixins/deletable';

export default {
    mixins: [deletable],
    components: {
        CouponsDataTable,
        CouponForm,
    },
    data() {
        return {
            showDialog:     false,
            selectedCoupon: null,
        };
    },
    methods: {
        clickNew() {
            this.selectedCoupon = null;
            this.showDialog     = true;
        },
        clickEdit(row) {
            // Clona el objeto para no mutar la fila reactiva de la tabla
            this.selectedCoupon = Object.assign({}, row);
            this.showDialog     = true;
        },
        clickDelete(id) {
            this.destroy(`/ecommerce/discount-coupons/${id}`)
                .then(() => {
                    this.$eventHub.$emit('reloadData');
                });
        },
        toggleStatus(row) {
            this.$http.post(`/ecommerce/discount-coupons/${row.id}/status`, { active: row.active })
                .then(() => {
                    this.$message.success('Estado actualizado correctamente');
                })
                .catch(() => {
                    // Revertir el cambio visual si falla
                    row.active = !row.active;
                    this.$message.error('Error al actualizar el estado');
                });
        },
    },
};
</script>

<style scoped>
.usage-wrapper{display:flex;align-items:center;gap:8px}
.usage-track{width:120px;height:8px;background:#e9ecef;border-radius:4px;overflow:hidden}
.usage-fill{height:100%;background:#0b5ed7;width:0%;transition:width .3s ease}
.usage-text{font-size:0.85rem;color:#6c757d;margin-left:6px}
</style>
