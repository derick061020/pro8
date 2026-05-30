<template>
    <div>
        <Keypress key-event="keyup" :key-code="40" @success="handle40" />
        <Keypress key-event="keyup" :key-code="38" @success="handle38" />
        <Keypress key-event="keyup" :key-code="13" @success="handle13" />
        <Keypress key-event="keyup" :key-code="113" @success="openTableListPrices113" />

        <el-table
            ref="singleTable"
            :data="records"
            highlight-current-row
            :cell-style="changeColor"
            :header-cell-class-name="headerFont"
            :row-class-name="boldFont"
            @current-change="handleCurrentChange"
            @cell-mouse-enter="enterChangeColor"
            @cell-mouse-leave="leaveChangeColor"
            style="width: 100%"
        >
            <el-table-column type="index" width="50"> </el-table-column>
            <el-table-column property="description" label="Nombre" width="180">
            </el-table-column>
            <el-table-column property="internal_id" label="Código" width="120">
            </el-table-column>
            <el-table-column property="brand" label="Marca" width="120">
                <!-- <template slot-scope="{ row }">
                    {{ row }}
                </template> -->
            </el-table-column>
            <!-- <el-table-column property="currency_type_id" label="Moneda" width="80">
                </el-table-column> -->
            <el-table-column label="Precio" width="100">
                <template slot-scope="{ row }">
                    {{ row.currency_type_symbol }} {{ row.sale_unit_price }}
                </template>
            </el-table-column>

            <el-table-column label="Pack" width="120">
                <template slot-scope="{ row }">
                    <br />
                    <small> {{ row.sets.join("-") }} </small>
                </template>
            </el-table-column>
            <el-table-column label="Stock">
                <template slot-scope="{ row }">
                    <!-- <button type="button" class="btn btn-xs btn-primary-pos" @click="clickWarehouseDetail(row)">
                            <i class="fa fa-search"></i>
                        </button> -->
                    <div v-if="config.product_only_location == true">
                        {{ row.stock }}
                    </div>
                    <div v-else>
                        <template
                            v-if="
                                typeUser == 'seller' && row.unit_type_id != 'ZZ'
                            "
                            >{{ row.stock }}</template
                        >
                        <template
                            v-else-if="
                                typeUser != 'seller' && row.unit_type_id != 'ZZ'
                            "
                        >
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info"
                                @click.prevent="clickWarehouseDetail(row)"
                            >
                                <i class="fa fa-search"></i>
                            </button>
                        </template>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="Lista precios" width="120">
                <template slot-scope="{ row, $index }"> 
                    <template v-if="row.item_unit_types">
                        <el-popover
                            placement="top"
                            width="280"
                            trigger="click"
                        >

                                                    <div class="el-popover__title d-flex justify-content-between">
                                                        Precios
                                                        <el-tag v-if="priceOptionsCount(row) > 0">
                                                            {{ priceOptionsCount(row) }} OPCIONES
                                                        </el-tag>
                                                        <el-tag v-else>
                                                            SIN REGISTROS
                                                        </el-tag>
                                                    </div>
                                                    <table
                                                        v-if="row.item_unit_types"
                                                        class="table table-sm mb-0 table-prices-popover">
                                                        <thead>
                                                            <tr>
                                                                <td class="text-start">Precio</td>
                                                                <td class="text-start">Unidad</td>
                                                                <td class="text-start">Descripción</td>
                                                                <td class="text-end"></td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <template v-if="row.item_unit_types.length == 1">
                                                                <template v-for="(price, _index) in row.item_unit_types[0].prices">
                                                                    <tr v-if="Number(price.price) > 0">
                                                                        <td class="text-start font-weight-semibold">
                                                                            <!-- {{ currency_type.symbol }} -->
                                                                            {{ price.price }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            {{ row.item_unit_types[0].unit_type_id }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            {{ row.item_unit_types[0].description }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            <button
                                                                                @click="
                                                                                    setPriceItem(
                                                                                        price,
                                                                                        $index
                                                                                    )
                                                                                "
                                                                                type="button"
                                                                                class="btn btn-sm btn-custom"
                                                                                :class="{'btn-success': price.selected}"
                                                                            >
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                </template>
                                                            </template>
                                                            <template v-else-if="row.item_unit_types.length == 0">
                                                                <tr>
                                                                    <td colspan="4" class="text-center">
                                                                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                                                            <div class="circle-container p-2">
                                                                                <div class="circle-child p-2">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card text-muted svg-bounce"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg>
                                                                                </div>
                                                                            </div>
                                                                            <div>
                                                                                <span class="small text-muted">
                                                                                    Aún no hay precios disponibles para este artículo.
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                            <template v-else>
                                                                <template v-for="(item_unit_type, _index) in row.item_unit_types">
                                                                    <template v-for="(price, _index_price) in item_unit_type.prices">
                                                                        <tr v-if="Number(price.price) > 0">
                                                                            <td class="text-start font-weight-semibold">
                                                                                <!-- {{ currency_type.symbol }} -->
                                                                                {{ price.price }}
                                                                            </td>
                                                                            <td class="text-start">
                                                                                {{ item_unit_type.unit_type_id }}
                                                                            </td>
                                                                            <td class="text-start">
                                                                                {{ item_unit_type.description }}
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <button
                                                                                    @click="
                                                                                        setPriceItem(
                                                                                            price,
                                                                                            $index
                                                                                        )
                                                                                    "
                                                                                    type="button"
                                                                                    class="btn btn-custom btn-sm"
                                                                                    :class="{'btn-success': price.selected}"
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                                                </button>
                                                                            </td>
                                                                        </tr>

                                                                    </template>
                                                                </template>



                                                            </template>


                                                        </tbody>
                                                    </table>                                                    
                            <button
                                slot="reference"
                                type="button"
                                style="width:100%" 
                                class="btn btn-xs btn-primary-pos"
                            >
                                <i
                                    class="fa fa-money-bill-alt"
                                ></i>
                            </button> 
                        </el-popover> 
                    </template>

                </template>
            </el-table-column>


            <el-table-column label="Historial ventas">
                <template slot-scope="{ row }">
                    <button
                        type="button"
                        class="btn btn-xs btn-primary-pos"
                        @click="clickHistorySales(row.item_id)"
                    >
                        <i class="fa fa-list"></i>
                    </button>
                </template>
            </el-table-column>

            <!-- <el-table-column label="Historial compras">
                    <template slot-scope="{row}">
                        <button type="button" class="btn btn-xs btn-primary-pos" @click="clickHistoryPurchases(row.item_id)"><i class="fas fa-cart-plus"></i></button>
                    </template>
                </el-table-column> -->
        </el-table>
        <item-unit-types-table
            :showDialog.sync="showDialogItemUnitTypes"
            :itemUnitTypes="itemUnitTypes"
        >
        </item-unit-types-table>
    </div>
</template>

<script>
import Keypress from "vue-keypress";
import ItemUnitTypesTable from "./item_unit_types_table.vue";

export default {
    components: { Keypress, ItemUnitTypesTable },
    props: {
        typeUser: String,
        records: {
            type: Array,
            default: [],
            required: false
        },
        visibleTagsCustomer: {
            type: Boolean,
            default: false
        },
        searchFromBarcode: {
            type: Boolean,
            default: false
        },
        originIsGarage: {
            type: Boolean,
            required: false,
            default: false
        },
    },
    data() {
        return {
            currentIndex: 0,
            showDialogItemUnitTypes: false,
            currentRow: null,
            selectedIndex:null,
            hoverIndex:null,
            changeMouse:false,
            itemUnitTypes: [],
            config: {}
        };
    },
    created() {
        this.$http.get(`/configurations/record`).then(response => {
            this.config = response.data.data;
        });

        this.events()

    },
    methods: {
        events(){
            
            this.$eventHub.$on("selectedItemUnitTypeTable", (unit_type) => {
                this.setPriceItem(unit_type)
            })

        },
        setPriceItem(price, index) {
            
            const item = this.records[index];
            if (item && item.item_unit_types && Array.isArray(item.item_unit_types)) {
                item.item_unit_types.forEach(iut => {
                    if (iut && iut.prices && Array.isArray(iut.prices)) {
                        iut.prices.forEach(p => {
                            if (p && p.selected) {
                                this.$set(p, 'selected', false);
                            }
                        });
                    }
                });
            }

            if (price) {
                this.$set(price, 'selected', true);
                this.addItemFromPriceSelected(item)
            }

            this.records[index].sale_unit_price = price.price;
            this.records[index].unit_type_id = price.unit_type_id;
            this.$message.success("Precio seleccionado");
            
        },
        async addItemFromPriceSelected(item)
        {
            if(this.originIsGarage && this.config.price_selected_add_product)
            {
                await this.handle13()
                item.apply_price_selected_add_product = true
            }
        },
        openTableListPrices113(){
            
            if(this.config.select_available_price_list){

                if (this.records.length == 1) {
                    // console.log(this.records[0].description)

                    if(this.records[0].unit_type.length > 0){

                        this.itemUnitTypes = this.records[0].unit_type
                        this.showDialogItemUnitTypes = true
                    
                    }


                } else {

                    if (this.currentRow) {

                        if(this.currentRow.unit_type.length > 0){

                            this.itemUnitTypes = this.currentRow.unit_type
                            this.showDialogItemUnitTypes = true
                            // console.log(this.currentRow.description)

                        }

                    }
                }
            }

        },
        priceOptionsCount(item) {
            let count = 0;
            if (!item) return count;
            const iuts = item.item_unit_types;
            if (!iuts || !Array.isArray(iuts)) return count;

            iuts.forEach(iut => {
                if (!iut || !Array.isArray(iut.prices)) return;
                iut.prices.forEach(p => {
                    if (p && !isNaN(Number(p.price)) && Number(p.price) > 0) {
                        count++;
                    }
                });
            });

            return count;
        },
        handle13() {
            if(this.searchFromBarcode) return

            if (this.visibleTagsCustomer) {
                return false;
            }

            if (this.records.length == 1) {
                this.$emit("clickAddItem", this.records[0]);
            } else {
                if (this.currentRow) {
                    this.$emit("clickAddItem", this.currentRow);
                }
            }
        },
        handle40() {
            if(this.searchFromBarcode) return

            if (this.visibleTagsCustomer) {
                return;
            }
            this.currentIndex += 1;

            if (this.records[this.currentIndex]) {
                this.setCurrent(this.records[this.currentIndex]);
            } else {
                this.currentIndex = 0;
                this.setCurrent(this.records[0]);
            }
        },
        handle38() {
            if (this.visibleTagsCustomer) {
                return;
            }

            if (this.currentIndex == 0) {
                return;
            }
            this.currentIndex -= 1;
            this.setCurrent(this.records[this.currentIndex]);
        },
        setCurrent(row) {
            this.$refs.singleTable.setCurrentRow(row);
        },
        handleCurrentChange(val) {
            this.currentRow = val;
            this.selectedIndex = val.index;
        },
        clickWarehouseDetail(id) {
            this.$emit("clickWarehouseDetail", id);
        },
        clickHistorySales(id) {
            this.$emit("clickHistorySales", id);
        },
        clickHistoryPurchases(id) {
            this.$emit("clickHistoryPurchases", id);
        },
        reset() {
            this.currentIndex = 0;
            this.setCurrent(this.records[this.currentIndex]);
        },
        changeColor({row, rowIndex}){
            if((this.selectedIndex) === rowIndex){
                if(this.selectedIndex === this.hoverIndex){
                    return {"background-color": "#ACE1F6"}
                }
                return {"background-color": "#A9E6FF"}
            }
            if(this.changeMouse){
                if((this.hoverIndex) === rowIndex){
                    return {"background-color": "#ACE1F6"}
                }
            }else{
                return {"background-color": "#ffff"}
            }
        },
        enterChangeColor(val){
            this.hoverIndex=val.index;
            this.changeMouse=true;
        },
        leaveChangeColor({row, column, cell, event}){
            this.changeMouse=false;
        },
        boldFont({row, rowIndex}){
            row.index=rowIndex;
            return 'font-weight-semibold';
        },
        headerFont(){
            return 'font-weight-semibold';
        }

    }
};
</script>

<style></style>
