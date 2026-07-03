<template>
    <el-dialog
        :close-on-click-modal="false"
        title="Editar Producto o Servicio"
        :visible.sync="showDialog"
        top="7vh"
        @close="close"
    >
        <form autocomplete="off" @submit.prevent="clickAddItem">
            <div class="form-body">
                <div class="row">
                    <!-- Producto -->
                    <div class="col-md-7 col-lg-7 col-xl-7">
                        <div class="form-group">
                            <label class="control-label">Producto/Servicio</label>
                            <el-select
                                v-model="form.item_id"
                                filterable
                                :disabled="recordItem !== null"
                                @change="changeItem"
                            >
                                <el-option
                                    v-for="option in items"
                                    :key="option.id"
                                    :value="option.id"
                                    :label="option.full_description"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>

                    <!-- Afectación IGV -->
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="control-label">Afectación IGV</label>
                            <el-select
                                v-model="form.affectation_igv_type_id"
                                filterable
                            >
                                <el-option
                                    v-for="option in affectation_igv_types"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label class="control-label">Cantidad</label>
                            <el-input-number
                                v-model="form.quantity"
                                :min="0.01"
                            ></el-input-number>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="col-6 col-lg-3">
                        <div class="form-group">
                            <label class="control-label">Precio Unitario</label>
                            <el-input
                                v-model="form.unit_price"
                                class="currency-container"
                            >
                                <template
                                    v-if="form.item.currency_type_symbol"
                                    slot="prepend"
                                >
                                    {{ form.item.currency_type_symbol }}
                                </template>
                            </el-input>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions pt-2 d-flex justify-content-end gap-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button
                    v-if="form.item_id"
                    native-type="submit"
                    type="primary"
                >
                    Actualizar
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
// import WarehousesDetail from './warehouses.vue'
import ItemForm from "@views/items/form.vue";
import LotsGroup from "@views/sale_notes/partials/lots_group.vue";

import { calculateRowItem } from "@helpers/functions";
import WarehousesDetail from "@views/documents/partials/select_warehouses.vue";
import SelectLotsForm from "./lots.vue";

import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import VueCkeditor from "vue-ckeditor5";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import {
    ItemOptionDescription,
    ItemSlotTooltip
} from "@helpers/modal_item";
import { checkPermissionEditPrices } from "@mixins/check-permission-edit-prices";
import HistorySalesForm from "../../../../../../../Pos/Resources/assets/js/views/history/sales.vue";

export default {
    props: [
        "recordItem",
        "showDialog",
        "currencyTypeIdActive",
        "exchangeRateSale",
        "typeUser",
        "configuration",
        "percentageIgv",
        "permissionEditItemPrices",
        "customerId"
    ],
    components: {
        ItemForm,
        WarehousesDetail,
        LotsGroup,
        SelectLotsForm,
        HistorySalesForm,
        "vue-ckeditor": VueCkeditor.component
    },
    mixins: [checkPermissionEditPrices],
    data() {
        return {
            can_add_new_product: false,
            loading_search: false,
            titleAction: "",
            is_client: false,
            titleDialog: "Agregar Producto o Servicio",
            resource: "order-notes",
            showDialogNewItem: false,
            has_list_prices: false,
            errors: {},
            form: {},
            all_items: [],
            items: [],
            operation_types: [],
            all_affectation_igv_types: [],
            aux_items: [],
            affectation_igv_types: [],
            system_isc_types: [],
            discount_types: [],
            charge_types: [],
            attribute_types: [],
            use_price: 1,
            change_affectation_igv_type_id: false,
            activePanel: 0,
            total_item: 0,
            item_unit_types: [],
            item_unit_type: {},
            showWarehousesDetail: false,
            warehousesDetail: [],
            showListStock: false,
            search_item_by_barcode: false,
            isUpdateWarehouseId: null,
            showDialogLots: false,
            showDialogSelectLots: false,
            lots: [],
            editors: {
                classic: ClassicEditor
            },
            loading_dialog: false,
            readonly_total: 0,
            itemSearchTerm: '',
            showDialogHistorySales: false,
            history_item_id: null
        };
    },
    watch: {
        showDialog(newVal) {
            if (newVal && this.recordItem) {
                this.itemSearchTerm = '';
                this.create();
            }
        }
    },
    mounted() {
        this.getTables();
        this.initForm();

        this.$eventHub.$on("reloadDataItems", item_id => {
            this.reloadDataItems(item_id);
        });

        this.$eventHub.$on("selectWarehouseId", warehouse_id => {
            this.form.warehouse_id = warehouse_id;
        });
        this.canCreateProduct();
    },
    computed: {
        ...mapState(["config"]),

        showLots() {
            if (this.form.item_id && this.form.item.lots_enabled) {
                return true;
            }

            return false;
        },
        showSeries() {
            if (this.form.item_id && this.form.item.series_enabled) {
                return true;
            }
            return false;
        },
        canEditPrice: function() {
            if (
                this.typeUser === "admin" ||
                (this.config !== undefined &&
                    this.config.allow_edit_unit_price_to_seller !== undefined &&
                    this.config.allow_edit_unit_price_to_seller === true)
            ) {
                return false;
            }
            return true;
        },
        documentItem() {
            if (
                this.recordItem !== undefined &&
                this.recordItem !== null &&
                this.recordItem.id !== undefined &&
                this.recordItem.id !== 0
            ) {
                this.form.document_item_id = this.recordItem.id;
                return this.recordItem.id;
            }
            return this.form.document_item_id;
        },
        edit_unit_price() {
            if (this.typeUser === "admin") {
                return true;
            }
            if (this.typeUser === "seller") {
                return this.config.allow_edit_unit_price_to_seller;
            }
            return false;
        },
        isUpdateItem() {
            return !_.isEmpty(this.recordItem);
        },
        hasSelectedItem() {
            return this.form.item_id && !_.isEmpty(this.form.item);
        }
    },
    methods: {
        ...mapActions(["loadConfiguration"]),
        hasAttributes() {
            if (
                this.form.item !== undefined &&
                this.form.item.attributes !== undefined &&
                this.form.item.attributes !== null &&
                this.form.item.attributes.length > 0
            ) {
                return true;
            }

            return false;
        },
        ItemSlotTooltipView(item) {
            return ItemSlotTooltip(item);
        },
        ItemOptionDescriptionView(item) {
            return ItemOptionDescription(item);
        },
        getTables() {
            this.$http.get(`/${this.resource}/item/tables`).then(response => {
                this.items = response.data.items;
                this.affectation_igv_types =
                    response.data.affectation_igv_types;
                this.system_isc_types = response.data.system_isc_types;
                this.discount_types = response.data.discount_types;
                this.charge_types = response.data.charge_types;
                this.attribute_types = response.data.attribute_types;
                // this.filterItems()
            });
        },
        canCreateProduct() {
            if (this.typeUser === "admin") {
                this.can_add_new_product = true;
            } else if (this.typeUser === "seller") {
                if (
                    this.config !== undefined &&
                    this.config.seller_can_create_product !== undefined
                ) {
                    this.can_add_new_product = this.config.seller_can_create_product;
                }
            }
            return this.can_add_new_product;
        },

        validateQuantity() {
            if (!this.form.quantity) {
                this.setMinQuantity();
            }

            if (isNaN(Number(this.form.quantity))) {
                this.setMinQuantity();
            }

            if (typeof parseFloat(this.form.quantity) !== "number") {
                this.setMinQuantity();
            }

            if (this.form.quantity <= this.getMinQuantity()) {
                this.setMinQuantity();
            }

            this.calculateTotal();
        },
        changeValidateQuantity(event) {
            this.calculateTotal();
        },
        getMinQuantity() {
            return 0.01;
        },
        setMinQuantity() {
            this.form.quantity = this.getMinQuantity();
        },
        clickDecrease() {
            this.form.quantity = parseInt(this.form.quantity - 1);

            if (this.form.quantity <= this.getMinQuantity()) {
                this.setMinQuantity();
                return;
            }

            this.calculateTotal();
        },
        clickIncrease() {
            this.form.quantity = parseInt(this.form.quantity + 1);
            this.calculateTotal();
        },
        async searchRemoteItems(input) {
            this.itemSearchTerm = input;

            if (input.length > 2) {
                this.loading_search = true;
                const params = {
                    input: input,
                    search_by_barcode: this.search_item_by_barcode ? 1 : 0
                };
                await this.$http
                    .get(`/${this.resource}/search-items/`, { params })
                    .then(response => {
                        this.items = response.data.items;
                        this.loading_search = false;
                        this.enabledSearchItemsBarcode();
                        this.enabledSearchItemBySeries();
                        if (this.items.length == 0) {
                            this.filterItems();
                        }
                    });
            } else {
                await this.filterItems();
            }
        },
        filterItems() {
            this.items = this.all_items;
        },

        enabledSearchItemsBarcode() {
            if (this.search_item_by_barcode) {
                this.$refs.selectBarcode.$data.selectedLabel = "";
                if (this.items.length == 1) {
                    this.form.item_id = this.items[0].id;
                    this.$refs.selectBarcode.blur();
                    this.changeItem();
                }
            }
        },
        async enabledSearchItemBySeries() {
            if (this.config.search_item_by_series && this.items.length == 1) {
                this.$notify({
                    title: "Serie ubicada",
                    message: "Producto añadido!",
                    type: "success",
                    duration: 1200
                });
                this.form.item_id = this.items[0].id;
                this.$refs.selectSearchNormal.$data.selectedLabel = "";

                await this.changeItem();

                this.lots = await this.form.item.lots.map(lot => {
                    lot.has_sale = true;
                });

                await this.clickAddItem();

                this.$refs.selectSearchNormal.$data.selectedLabel = "";
            }

            if (this.config.search_item_by_series && this.items.length == 0) {
                this.$notify({
                    title: "Serie no ubicada",
                    message: "",
                    type: "warning",
                    duration: 1200
                });
            }
        },

        filterMethod(query) {
            let item = _.find(this.items, { internal_id: query });

            if (item) {
                this.form.item_id = item.id;
                this.changeItem();
            }
        },
        clickWarehouseDetail() {
            if (!this.form.item_id) {
                return this.$message.error("Seleccione un item");
            }

            let item = _.find(this.items, { id: this.form.item_id });

            this.warehousesDetail = item.warehouses;
            this.showWarehousesDetail = true;
        },
        //filterItems() {
        // this.items = this.items.filter(item => item.warehouses.length >0)
        // },
        initForm() {
            this.errors = {};

            this.form = {
                // category_id: [1],
                // edit: false,
                item_id: null,
                item: {},
                affectation_igv_type_id: null,
                affectation_igv_type: {},
                has_isc: false,
                system_isc_type_id: null,
                percentage_isc: 0,
                suggested_price: 0,
                quantity: 1,
                unit_price: 0,
                unit_price_value: 0,
                input_unit_price: 0,
                input_unit_price_value: 0,
                charges: [],
                discounts: [],
                attributes: [],
                has_igv: null,
                item_unit_type_id: null,
                unit_type_id: null,
                is_set: false,
                item_unit_types: [],
                has_plastic_bag_taxes: false,
                series_enabled: false,
                warehouse_id: null,
                lots_group: [],
                IdLoteSelected: null,
                document_item_id: null,
                name_product_pdf: "",
                calculate_quantity: false
            };

            this.activePanel = 0;
            this.total_item = 0;
            this.item_unit_type = {};
            this.lots = [];
            this.has_list_prices = false;
        },
        // initializeFields() {
        //     this.form.affectation_igv_type_id = this.affectation_igv_types[0].id
        // },
        async create() {
            this.titleDialog = this.recordItem
                ? " Editar Producto o Servicio"
                : " Agregar Producto o Servicio";
            this.titleAction = this.recordItem ? " Editar" : " Agregar";


            if (this.recordItem) {
                await this.reloadDataItems(this.recordItem.item_id);
                this.form.item_id = this.recordItem.item_id;
                await this.changeItem();


                this.form.quantity = this.recordItem.quantity;
                // El campo form.unit_price es el valor que ingresa el usuario (sin IGV)
                // input_unit_price_value es el valor original ingresado
                this.form.unit_price = this.recordItem.input_unit_price_value;
                this.form.unit_price_value = this.recordItem.input_unit_price_value;
                this.form.has_plastic_bag_taxes =
                    this.recordItem.total_plastic_bag_taxes > 0 ? true : false;
                this.form.warehouse_id = this.recordItem.warehouse_id;
                if (this.recordItem.item && this.recordItem.item.name_product_pdf) {
                    this.form.name_product_pdf = this.recordItem.item.name_product_pdf;
                }


                if (this.recordItem.item && this.recordItem.item.change_free_affectation_igv) {
                    this.form.affectation_igv_type_id = "15";
                    this.form.item.change_free_affectation_igv = true;
                } else {
                    if (this.recordItem.item && this.recordItem.item.original_affectation_igv_type_id) {
                        this.form.affectation_igv_type_id = this.recordItem.item.original_affectation_igv_type_id;
                    }
                }

                this.calculateQuantity();
                
            }

            /* Migrado de resources/js/views/tenant/sale_notes/partials/item.vue*/
            /*

            this.titleDialog = (this.recordItem) ? ' Editar Producto o Servicio' : ' Agregar Producto o Servicio';
            this.titleAction = (this.recordItem) ? ' Editar' : ' Agregar';
            if(this.operation_types !== undefined) {
                let operation_type = await _.find(this.operation_types, {id: this.operationTypeId})
                if(operation_type !== undefined) {
                    this.affectation_igv_types = await _.filter(this.all_affectation_igv_types, {exportation: operation_type.exportation})
                }
            }

            if (this.recordItem) {
                await this.reloadDataItems(this.recordItem.item_id)
                this.form.item_id = await this.recordItem.item_id
                await this.changeItem()
                this.form.quantity = this.recordItem.quantity
                this.form.unit_price_value = this.recordItem.input_unit_price_value
                this.form.has_plastic_bag_taxes = (this.recordItem.total_plastic_bag_taxes > 0) ? true : false
                this.form.warehouse_id = this.recordItem.warehouse_id
                this.isUpdateWarehouseId = this.recordItem.warehouse_id

                if (this.isEditItemNote) {
                    this.form.item.currency_type_id = this.currencyTypeIdActive
                    this.form.item.currency_type_symbol = (this.currencyTypeIdActive == 'PEN') ? 'S/' : '$'

                    if (this.documentTypeId == '07' && this.noteCreditOrDebitTypeId == '07') {

                        this.form.document_item_id = this.recordItem.id ? this.recordItem.id : this.recordItem.document_item_id
                        this.form.item.lots = this.recordItem.item.lots
                        await this.regularizeLots()
                        this.lots = this.form.item.lots
                    }

                }

                if (this.recordItem.item.name_product_pdf) {
                    this.form.name_product_pdf = this.recordItem.item.name_product_pdf
                }
                // if(this.recordItem.name_product_pdf){
                //     this.form.name_product_pdf = this.recordItem.name_product_pdf
                // }

                if(this.recordItem.item.change_free_affectation_igv){

                    this.form.affectation_igv_type_id = '15'
                    this.form.item.change_free_affectation_igv = true

                }else{
                    if(this.recordItem.item.original_affectation_igv_type_id){
                        this.form.affectation_igv_type_id = this.recordItem.item.original_affectation_igv_type_id
                    }
                }
                this.calculateQuantity()
            } else {
                this.isUpdateWarehouseId = null
            }

            */
            //     this.initializeFields()
        },
        async regularizeLots() {
            if (this.form.document_item_id && this.form.item.lots.length > 0) {
                await this.$http
                    .get(
                        `/${this.resource}/regularize-lots/${
                            this.form.document_item_id
                        }`
                    )
                    .then(response => {
                        let all_lots = this.form.item.lots;
                        let available_lots = response.data;

                        all_lots.forEach((lot, index) => {
                            let exist_lot = _.find(available_lots, it => {
                                return it.id == lot.id;
                            });

                            if (!exist_lot) {
                                this.form.item.lots.splice(index, 1);
                            }
                        });
                    })
                    .catch(error => {})
                    .then(() => {});
            }
        },
        clickAddDiscount() {
            this.form.discounts.push({
                discount_type_id: null,
                discount_type: null,
                description: null,
                percentage: 0,
                factor: 0,
                amount: 0,
                base: 0,
                is_amount: false
            });
        },
        clickRemoveDiscount(index) {
            this.form.discounts.splice(index, 1);
        },
        changeDiscountType(index) {
            let discount_type_id = this.form.discounts[index].discount_type_id;
            this.form.discounts[index].discount_type = _.find(
                this.discount_types,
                { id: discount_type_id }
            );
        },
        clickAddCharge() {
            this.form.charges.push({
                charge_type_id: null,
                charge_type: null,
                description: null,
                percentage: 0,
                factor: 0,
                amount: 0,
                base: 0
            });
        },
        clickRemoveCharge(index) {
            this.form.charges.splice(index, 1);
        },
        changeChargeType(index) {
            let charge_type_id = this.form.charges[index].charge_type_id;
            this.form.charges[index].charge_type = _.find(this.charge_types, {
                id: charge_type_id
            });
        },
        clickAddAttribute() {
            this.form.attributes.push({
                attribute_type_id: null,
                description: null,
                value: null,
                start_date: null,
                end_date: null,
                duration: null
            });
        },
        clickRemoveAttribute(index) {
            this.form.attributes.splice(index, 1);
        },
        changeAttributeType(index) {
            let attribute_type_id = this.form.attributes[index]
                .attribute_type_id;
            let attribute_type = _.find(this.attribute_types, {
                id: attribute_type_id
            });
            this.form.attributes[index].description =
                attribute_type.description;
        },
        close() {
            this.initForm();
            this.$emit("update:showDialog", false);
        },
        async changeItem() {
            this.getItems();

            this.form.item = _.find(this.items, { id: this.form.item_id });
            this.form.unit_price = this.form.item.sale_unit_price;
            this.form.unit_price_value = this.form.item.sale_unit_price;
            this.lots = this.form.item.lots;
            this.form.has_igv = this.form.item.has_igv;
            this.form.affectation_igv_type_id = this.form.item.sale_affectation_igv_type_id;
            if (!this.recordItem) {
                this.form.quantity = 1;
            }
            this.item_unit_types = this.form.item.item_unit_types;
            this.item_unit_types.length > 0
                ? (this.has_list_prices = true)
                : (this.has_list_prices = false);
            this.form.lots_group = this.form.item.lots_group;

            this.setDefaultAttributes();
            this.cleanTotalItem();
            this.calculateTotal();
        },
        setDefaultAttributes() {
            this.form.attributes = [];

            if (this.hasAttributes()) {
                this.form.item.item_attributes.forEach(row => {
                    this.form.attributes.push({
                        attribute_type_id: row.attribute_type_id,
                        description: row.description,
                        duration: row.duration,
                        end_date: row.end_date,
                        start_date: row.start_date,
                        value: row.value
                    });
                });
            }
        },
        hasAttributes() {
            return (
                this.form.item != undefined &&
                this.form.item.item_attributes &&
                Array.isArray(this.form.item.item_attributes) &&
                this.form.item.item_attributes.length > 0
            );
        },
        focusTotalItem(change) {
            if (!change && this.form.item.calculate_quantity) {
                this.$refs.total_item.$el
                    .getElementsByTagName("input")[0]
                    .focus();
                this.total_item = this.form.unit_price_value;
            }
        },
        reloadDataItems(item_id) {
            this.$http.get(`/${this.resource}/table/items`).then(response => {
                this.items = response.data;
                this.form.item_id = item_id;
                if (item_id) {
                    this.changeItem();
                }
                // this.filterItems()
            });
        },

        calculateTotal() {
            this.readonly_total = _.round(
                this.form.quantity * this.form.unit_price_value,
                4
            );
            console.log(this.readonly_total);
        },
        calculateQuantity() {
            if (this.form.item.calculate_quantity) {
                this.form.quantity = _.round(
                    this.total_item / this.form.unit_price,
                    4
                );
            }
            this.calculateTotal();
        },
        cleanTotalItem() {
            this.total_item = null;
        },
        async clickAddItem() {
            this.validateQuantity();

            if (this.form.item.lots_enabled) {
                if (!this.form.IdLoteSelected)
                    return this.$message.error("Debe seleccionar un lote.");
            }

            let select_lots = await _.filter(this.form.item.lots, {
                has_sale: true
            });

            if (this.form.item.series_enabled) {
                if (select_lots.length != this.form.quantity)
                    return this.$message.error(
                        "La cantidad de series seleccionadas son diferentes a la cantidad a vender"
                    );
            }

            if (this.validateTotalItem().total_item) return;

            // this.form.item.unit_price = this.form.unit_price;
            let unit_price = this.form.has_igv
                ? this.form.unit_price
                : this.form.unit_price * (1 + this.percentageIgv);

            // this.form.item.unit_price = this.form.unit_price
            this.form.unit_price = unit_price;
            this.form.item.unit_price = unit_price;

            this.form.item.presentation = this.item_unit_type;
            this.form.affectation_igv_type = _.find(
                this.affectation_igv_types,
                { id: this.form.affectation_igv_type_id }
            );
            let IdLoteSelected = this.form.IdLoteSelected;
            this.row = calculateRowItem(
                this.form,
                this.currencyTypeIdActive,
                this.exchangeRateSale,
                this.percentageIgv
            );
            this.row.IdLoteSelected = IdLoteSelected;

            if (this.recordItem && this.recordItem.id) {
                this.row.id = this.recordItem.id;
            }
            
            this.initForm();

            // this.initializeFields()
            this.$emit("update", this.row);
            this.setFocusSelectItem();
        },
        cleanItems() {
            this.items = [];
            this.$refs.selectBarcode.$el
                .getElementsByTagName("input")[0]
                .focus();
            // console.log("add cart barcode")
        },
        validateTotalItem() {
            this.errors = {};

            if (this.form.item.calculate_quantity) {
                if (this.total_item < 0.01)
                    this.$set(this.errors, "total_item", [
                        "total venta producto debe ser mayor a 0"
                    ]);
            }

            return this.errors;
        },
        changePresentation() {
            let price = 0;

            this.item_unit_type = _.find(this.form.item.item_unit_types, {
                id: this.form.item_unit_type_id
            });

            switch (this.item_unit_type.price_default) {
                case 1:
                    price = this.item_unit_type.price1;
                    break;
                case 2:
                    price = this.item_unit_type.price2;
                    break;
                case 3:
                    price = this.item_unit_type.price3;
                    break;
            }

            this.form.unit_price = price;
            this.form.item.unit_type_id = this.item_unit_type.unit_type_id;
        },
        selectedPrice(row, amount = false) {
            if (this.isSelectedPrice(row) && !amount) {
                this.form.item_unit_type_id = null;
                this.item_unit_type = {};
                this.form.unit_price = this.form.item.sale_unit_price;
                this.form.unit_price_value = this.form.item.sale_unit_price;
                this.form.item.unit_type_id = this.form.item.original_unit_type_id;
            } else {
                let value = 0;
                if (amount) {
                    value = amount;
                } else {
                    switch (row.price_default) {
                        case 1:
                            value = row.price1;
                            break;
                        case 2:
                            value = row.price2;
                            break;
                        case 3:
                            value = row.price3;
                            break;
                    }
                }

                this.form.item_unit_type_id = row.id;
                this.item_unit_type = row;
                this.form.unit_price = value;
                this.form.unit_price_value = value;
                this.form.item.unit_type_id = row.unit_type_id;
            }

            this.calculateQuantity();
        },
        async getItems() {
            this.loading_dialog = true;

            await this.$http
                .get(`/${this.resource}/item/tables`)
                .then(response => {
                    this.items = response.data.items;
                })
                .then(() => {
                    this.loading_dialog = false;
                });
        },
        addRowLotGroup(id) {
            this.form.IdLoteSelected = id;
        },
        clickLotGroup() {
            this.showDialogLots = true;
        },
        clickSelectLots() {
            this.showDialogSelectLots = true;
        },
        addRowSelectLot(lots) {
            this.lots = lots;
        },
        focusSelectItem() {
            this.$refs.selectSearchNormal.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        setFocusSelectItem() {
            this.$refs.selectSearchNormal.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        validateQuantity() {
            if (!this.form.quantity) {
                this.setMinQuantity();
            }

            if (isNaN(Number(this.form.quantity))) {
                this.setMinQuantity();
            }

            if (typeof parseFloat(this.form.quantity) !== "number") {
                this.setMinQuantity();
            }

            if (this.form.quantity <= this.getMinQuantity()) {
                this.setMinQuantity();
            }

            this.calculateTotal();
        },
        setMinQuantity() {
            this.form.quantity = this.getMinQuantity();
        },
        getMinQuantity() {
            return 0.01;
        },
        getSelectedClass(row) {
            if (this.isSelectedPrice(row)) return "btn-success";
            return "btn-secondary";
        },
        isSelectedPrice(item_unit_type) {
            if (!_.isEmpty(this.item_unit_type)) {
                return this.item_unit_type.id === item_unit_type.id;
            }
            return false;
        },
        openNewItemDialog() {
            this.showDialogNewItem = true;
        },
        clickHistorySales() {
            if (!this.form.item_id) {
                return this.$message.error("Seleccione un item");
            }

            const item = _.find(this.items, { id: this.form.item_id });

            if (!item) {
                return this.$message.error("Producto no encontrado");
            }

            this.history_item_id = item.id;
            this.showDialogHistorySales = true;
        },
    }
};
</script>
