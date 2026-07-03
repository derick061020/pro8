<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        append-to-body
        top="7vh"
        @close="close"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div v-if="warehouses" class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ubicación</th>
                                    <th class="text-right">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, index) in warehouses"
                                    :key="index"
                                >
                                    <th>{{ row.warehouse_description }}</th>
                                    <th
                                        class="text-right"
                                        :class="{
                                            'text-danger': Number(row.stock) <= 0
                                        }"
                                    >
                                        {{ (row.stock == null) ? '-' : Number(row.stock).toFixed(2) }}
                                    </th>
                                </tr>
                            </tbody>
                        </table>

                        <template v-if="item_unit_types.length > 0">
                            <h5>Lista de Precios Creados</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Unidad</th>
                                            <th>Description</th>
                                            <th>Factor</th>

                                            <template v-for="pl in price_labels">
                                                <th v-if="pl.is_active !== false">{{ pl.label }}</th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(row, index) in item_unit_types"
                                            :key="index"
                                        >
                                            <th>{{ row.unit_type_id }}</th>
                                            <th>{{ row.description }}</th>
                                            <th>{{ row.quantity_unit }}</th>
                                            <template v-for="(price, priceIndex) in row.prices">
                                                <th v-if="price_labels[priceIndex] && price_labels[priceIndex].is_active !== false">
                                                    {{ price.price }}
                                                </th>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right pt-2">
                <el-button class="second-buton" @click.prevent="close()"
                    >Cerrar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "warehouses", "item_unit_types", 'config', 'price_labels'],
    data() {
        return {
            showImportDialog: false,
            resource: "items",
            recordId: null,
            titleDialog: "Stock de producto"
        };
    },
    created() {
        //console.log(this.typeUser)
    },
    methods: {
        close() {
            console.log(this.price_labels);
            this.$emit("update:showDialog", false);
        }
    }
};
</script>
