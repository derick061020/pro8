<style>
#orderDetailModal .modal-content {
    border: none;
    border-radius: 14px;
    overflow: hidden;
}

#orderDetailModal .modal-header {
    background: #fafbfc;
    border-bottom: 1px solid #eef0f2;
    padding: 20px 28px;
}

#orderDetailModal .modal-title {
    font-weight: 700;
    font-size: 18px;
    color: #1a1a1a;
}

#orderDetailModal .modal-body {
    padding: 28px;
}

.info-card {
    background: #fafbfc;
    border: 1px solid #eef0f2;
    border-radius: 10px;
    padding: 18px 20px;
    height: 100%;
}

.info-card h6 {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #8a8f98;
    margin-bottom: 14px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 14px;
    border-bottom: 1px solid #f0f1f3;
}
.info-row:last-child { border-bottom: none; }
.info-row .label { color: #6b7280; }
.info-row .value { font-weight: 600; color: #1a1a1a; text-align: right; }

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.products-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #8a8f98;
    margin: 28px 0 14px;
}

.product-table {
    border: 1px solid #eef0f2;
    border-radius: 10px;
    overflow: hidden;
}
.product-table thead th {
    background: #fafbfc;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #8a8f98;
    border-bottom: 1px solid #eef0f2;
    padding: 12px 16px;
}
.product-table td {
    padding: 12px 16px;
    vertical-align: middle;
    border-top: 1px solid #f5f6f8;
}
.product-thumb {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #eef0f2;
}
.product-name {
    font-weight: 600;
    font-size: 14px;
    color: #1a1a1a;
}

.total-box {
    margin-top: 20px;
    padding: 16px 20px;
    background: #fafbfc;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.total-box .label {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #6b7280;
}
.total-box .amount {
    font-size: 22px;
    font-weight: 800;
    color: #15803d;
}
</style>
<script type="text/x-template" id="order-detail-template">
<div class="modal fade" id="orderDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" v-if="record">

            <div class="modal-header">
                <h5 class="modal-title">
                    Pedido #@{{ record.order_id }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" @click="close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="info-card">
                            <h6>Cliente</h6>
                            <div class="info-row">
                                <span class="label">Nombre</span>
                                <span class="value">@{{ record.customer }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Teléfono</span>
                                <span class="value">@{{ record.customer_telefono }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Dirección</span>
                                <span class="value">@{{ record.customer_direccion }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h6>Pedido</h6>
                            <div class="info-row">
                                <span class="label">Fecha</span>
                                <span class="value">@{{ record.created_at }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Pago</span>
                                <span class="value">@{{ record.reference_payment }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Estado</span>
                                <span class="value">
                                    <span class="status-badge" :class="'status-' + record.status_order_id">
                                        @{{ record.status_order_description }}
                                    </span>
                                </span>
                            </div>
                            <div class="info-row" v-if="record.shipping_status_order_description">
                                <span class="label">Envío</span>
                                <span class="value">@{{ record.shipping_status_order_description }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="products-title">Productos</div>

                <table class="table product-table mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th width="70" class="text-center">Cant.</th>
                            <th width="110" class="text-right">Precio</th>
                            <th width="110" class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in record.items">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img :src="'/storage/uploads/items/' + item.image" class="product-thumb mr-3">
                                    <span class="product-name">@{{ item.description }}</span>
                                </div>
                            </td>
                            <td class="text-center">@{{ item.cantidad }}</td>
                            <td class="text-right">
                                @{{ item.currency_type.symbol }} @{{ Number(item.sale_unit_price).toFixed(2) }}
                            </td>
                            <td class="text-right">
                                @{{ item.currency_type.symbol }} @{{ Number(item.sub_total).toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="total-box">
                    <span class="label">Total</span>
                    <span class="amount">
                        @{{ record.items[0].currency_type.symbol }} @{{ Number(record.total).toFixed(2) }}
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>
</script>