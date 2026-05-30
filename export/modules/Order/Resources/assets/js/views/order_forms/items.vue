<template>
    <el-dialog :title="titleDialog" :visible="dialogVisible" @open="create" :close-on-click-modal="false" :close-on-press-escape="false" @close="close" top="8vh">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" :class="{'has-danger': errors.items}">
                        <label class="control-label">
                            Producto
                            <a href="#" @click.prevent="showDialogNewItem = true">[+ Nuevo]</a>
                        </label>
                        <el-select :disabled="recordItem != null" v-model="form.item" filterable>
                            <el-option v-for="option in items" :key="option.id" :value="option.id" :label="option.full_description"></el-option>
                        </el-select>
                        <div class="product-model position-relative">
                            <div class="tooltips-container" style="top: 46px;" v-show="hasSelectedItem">
                                <el-tooltip
                                    slot="append"
                                    :disabled="isUpdateItem"
                                    class="item"
                                    content="Ver Stock del Producto"
                                    effect="dark"
                                    placement="bottom"
                                >
                                    <el-button
                                        :disabled="isUpdateItem"
                                        class="d-flex align-items-center"
                                        @click.prevent="clickWarehouseDetail"
                                    >
                                        <i class="fa fa-search"></i>
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip
                                    slot="append"
                                    :disabled="isUpdateItem || !hasSelectedItem"
                                    class="item"
                                    content="Historial de ventas"
                                    effect="dark"
                                    placement="bottom"
                                >
                                    <el-button
                                        :disabled="isUpdateItem || !hasSelectedItem"
                                        class="d-flex align-items-center"
                                        @click.prevent="clickHistorySales"
                                    >
                                        <i class="fa fa-list"></i>
                                    </el-button>
                                </el-tooltip>
                            </div>
                            <el-select
                                :disabled="isUpdateItem"
                                v-model="form.item"
                                class="w-100"
                                filterable
                            >
                                <el-option
                                    v-for="option in items"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.full_description"
                                ></el-option>
                            </el-select>
                        </div>
                        <small class="form-control-feedback" v-if="errors.items" v-text="errors.items[0]"></small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group" :class="{'has-danger': errors.quantity}">
                        <label class="control-label">Cantidad</label>
                        <el-input-number v-model="form.quantity" :precision="4" :step="1" :min="0" :max="99999999"></el-input-number>
                        <small class="form-control-feedback" v-if="errors.quantity" v-text="errors.quantity[0]"></small>
                    </div>
                </div>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button class="second-buton" @click.prevent="close">Cerrar</el-button>
            <el-button type="primary" @click="clickAddItem">Guardar</el-button>
        </span>

        <item-form :showDialog.sync="showDialogNewItem" :external="true"></item-form>
        <warehouses-detail
            :showDialog.sync="showWarehousesDetail"
            :warehouses="warehousesDetail"
            :isUpdateWarehouseId="null"
        ></warehouses-detail>
        <history-sales-form
            :showDialog.sync="showDialogHistorySales"
            :item_id="history_item_id"
            :customer_id="customerId"
            :type="true"
        ></history-sales-form>
    </el-dialog>
</template>

<script>
    import itemForm from '@views/items/form.vue';
    import WarehousesDetail from '@views/documents/partials/select_warehouses.vue';
    import HistorySalesForm from '../../../../../../Pos/Resources/assets/js/views/history/sales.vue';

    export default {
        components: {itemForm, WarehousesDetail , HistorySalesForm},
        props: ['dialogVisible', 'recordItem', 'customerId'],
        data() {
            return {
                titleDialog: 'Agregar Producto',
                showDialogNewItem: false,
                resource: 'order-forms',
                errors: {},
                items: [],
                form: {},
                showWarehousesDetail: false,
                warehousesDetail: [],
                showDialogHistorySales: false,
                history_item_id: null
            }
        },
        computed: {
            isUpdateItem() {
                return !!this.recordItem;
            },
            selectedItem() {
                if (!this.form || !this.form.item) return null;
                return this.items.find(item => item.id === this.form.item) || null;
            },
            hasSelectedItem() {
                return !!this.selectedItem;
            }
        },
        methods: {
            async create() {

                await  this.$http.post(`/${this.resource}/tables`).then(response => {
                    this.items = response.data.items;
                });

                if(this.recordItem)
                {
                    this.form = { quantity: this.recordItem.quantity, item: this.recordItem.item_id };
                }
                else{
                    this.form = { quantity:0 };
                }

            },
            close() {
                this.showWarehousesDetail = false;
                this.warehousesDetail = [];
                this.showDialogHistorySales = false;
                this.history_item_id = null;
                this.$emit('update:dialogVisible', false);
            },
            clickAddItem() {
                this.errors = {};


                if(this.recordItem)
                {
                    this.recordItem.quantity = this.form.quantity
                    this.form = { quantity: 0, item: null }
                    this.$emit('update:dialogVisible', false);

                }else{

                    if ((this.form.item != null) && (this.form.quantity != null)) {
                        this.$emit('addItem', {
                            item: this.items.find((item) => item.id == this.form.item),
                            quantity: this.form.quantity
                        });

                        this.form = { quantity: 0, item: null }

                        return;
                    }

                    if (this.form.item == null) this.$set(this.errors, 'items', ['Seleccione el producto']);

                    if (this.form.quantity == null) this.$set(this.errors, 'quantity', ['Digite la cantidad']);
                }


            },
            clickWarehouseDetail() {
                if (!this.hasSelectedItem) {
                    return this.$message.error('Seleccione un item');
                }

                const item = this.selectedItem;

                if (!item || !item.warehouses || item.warehouses.length === 0) {
                    return this.$message.warning('El producto no tiene almacenes disponibles');
                }

                this.warehousesDetail = item.warehouses;
                this.showWarehousesDetail = true;
            },
            clickHistorySales() {
                if (!this.hasSelectedItem) {
                    return this.$message.error('Seleccione un item');
                }

                const item = this.selectedItem;

                if (!item) {
                    return this.$message.error('Producto no encontrado');
                }

                this.history_item_id = item.id;
                this.showDialogHistorySales = true;
            }
            
        }
    }
</script>
