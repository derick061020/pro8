<template>
    <div class="garage container-fluid p-0">
        <span class="module-title-marker" data-page-title="Venta Rápida"></span>
        <div class="row page-header pr-0 no-gutters" style="min-height:48px">
            <Keypress
                key-event="keyup"
                :key-code="112"
                @success="handleFn112"
            />

            <!-- F4 -->
            <Keypress
                :key-code="115"
                key-event="keyup"
                @success="handleFn115"
            />
            <!-- F4 -->

            <div class="col-md-4">
                <h2>
                    <el-switch
                        v-model="search_item_by_barcode"
                        active-text="Buscar con escaner de código de barras"
                        @change="changeSearchItemBarcode"
                    ></el-switch>
                </h2>
            </div>
            <div class="col-md-4 d-flex justify-content-center align-items-center gap-2">
                <h2 class="px-0">
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Todas las categorías"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            @click="back()"
                            class="btn btn-custom btn-sm"
                        >
                            <i class="fa fa-border-all"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2 class="px-0">
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Categorías y productos"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat2'"
                            @click="setView('cat2')"
                            class="btn btn-custom btn-sm"
                        >
                            <i class="fa fa-bars"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2 class="px-0">
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Listado de todos los productos"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat3'"
                            @click="setView('cat3')"
                            class="btn btn-custom btn-sm"
                        >
                            <i class="fas fa-list-ul"></i>
                        </button>
                    </el-tooltip>
                </h2>
                <h2 class="px-0">
                    <el-tooltip
                        class="item"
                        effect="dark"
                        content="Regresar"
                        placement="top-start"
                    >
                        <button
                            type="button"
                            :disabled="place == 'cat'"
                            @click="back()"
                            class="btn btn-custom btn-sm"
                        >
                            <i class="fa fa-undo"></i>
                        </button>
                    </el-tooltip>
                </h2>
            </div>
            <div class="col-md-4" v-if="currency_types.length > 1">
                <div class="pull-right h-100 d-flex align-items-center">
                    <p class="pr-3 m-0 exchange-currency">
                        T.C.
                        <span>S/ {{ form.exchange_rate_sale }}</span> Cambiar
                        Moneda
                        <a
                            class="btn btn-sm btn-default"
                            @click="selectCurrencyType"
                        >
                            <template v-if="form.currency_type_id == 'PEN'">
                                <strong>S/</strong>
                            </template>
                            <template v-else>
                                <strong>$</strong>
                            </template>
                            <!-- <i class="fa fa-usd" aria-hidden="true"></i> -->
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="!is_payment"
            class="row col-lg-12 m-0 p-0"
            v-loading="loading"
        >
            <div class="col-lg-8 col-md-6 px-4 hyo">
                <template v-if="!search_item_by_barcode">
                    <el-input
                        v-show="
                            place == 'prod' ||
                                place == 'cat2' ||
                                place == 'cat3'
                        "
                        placeholder="Buscar productos"
                        size="medium"
                        v-model="input_item"
                        @input="searchItems"
                        @keyup.native="keyupTabCustomer"
                        @keyup.enter.native="keyupEnterAddItem"
                        class="m-bottom mt-3 input-search-pos"
                        ref="ref_search_items"
                    >
                        <template v-if="validteCreateProduct">
                            <el-button
                                slot="append"
                                @click.prevent="showDialogNewItem = true"
                                class="btn-add-product-pos"
                            >Nuevo Producto</el-button>
                        </template>
                    </el-input>
                </template>

                <template v-else>
                    <el-input
                        v-show="
                            place == 'prod' ||
                                place == 'cat2' ||
                                place == 'cat3'
                        "
                        placeholder="Buscar productos"
                        size="medium"
                        v-model="input_item"
                        @change="searchItemsBarcode"
                        @keyup.native="keyupTabCustomer"
                        ref="ref_search_items"
                        class="m-bottom mt-3"
                        @focus="searchFromBarcode = true"
                        @blur="searchFromBarcode = false"
                    >
                        <template v-if="validteCreateProduct">
                            <el-button
                                slot="append"
                                icon="el-icon-plus"
                                @click.prevent="showDialogNewItem = true"
                            ></el-button>
                        </template>
                    </el-input>
                </template>

                <div v-if="place == 'cat2'" class="container testimonial-group">
                    <div class="row text-center flex-nowrap">
                        <div
                            v-for="(item, index) in categories"
                            @click="filterCategorie(item.id, true)"
                            :key="index"
                            class="btn btn-primary w-auto mx-1 fw-semibold"
                        >
                            {{ item.name }}
                        </div>
                    </div>
                </div>
                <br />

                <div v-if="place == 'cat'" class="row no-gutters">
                    <template v-for="(item, index) in categories">
                        <div class="col" :key="index">
                            <div
                                @click="filterCategorie(item.id)"
                                class="card p-0 m-0 mb-1 mr-1 text-center"
                            >
                                <div
                                    class="btn btn-primary pointer"
                                    style="font-weight: bold;color: white;font-size: 18px;"
                                >
                                    {{ item.name }}
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div
                    v-if="place == 'prod' || place == 'cat2'"
                    class="product-pos-container"
                    :class="layout_mode"
                >
                    <template v-for="(item, index) in items">
                        <div :key="index" class="px-0">
                            <section class="card product-item mb-0 h-100">
                                <div
                                    class="card-body product pointer px-2 pt-2 m-0 pb-0 bg-transparent"
                                    @click="clickAddItem(item, index)"
                                >
                                    <img
                                        :src="item.image_url"
                                        class="img-thumbail img-custom"
                                    />
                                    <p
                                        class="text-muted font-weight-lighter mb-0"
                                        style="display: flex; justify-content: space-between; align-items: center;"
                                    >
                                        <small class="text-primary internal-code-small" :title="item.internal_id">{{ item.internal_id }}</small>
                                        <el-tooltip
                                            class="item text-center"
                                            effect="dark"
                                            :content="
                                                item.sets.flat().join(',\n')
                                            "
                                            placement="bottom"
                                            style="width: 10%;"
                                        >
                                            <i
                                                v-if="item.sets.length > 0"
                                                class="fas fa-box-open ms-2"
                                                style="cursor: pointer;"
                                            >
                                            </i>
                                        </el-tooltip>

                                        <small class="measuring-unit text-end" style="width: 45%;">
                                            <el-tag
                                                type="primary"
                                                size="mini"
                                                >{{ item.unit_type_id }}
                                            </el-tag>
                                        </small>
                                    </p>
                                    <span
                                        class="font-weight-semibold mb-0 product-name-description"
                                        v-if="DescriptionLength(item) > 50"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        :title="item.description"
                                    >
                                        {{ item.description.substring(0, 50) }}
                                    </span>
                                    <span
                                        class="font-weight-semibold mb-0 product-name-description"
                                        v-if="DescriptionLength(item) <= 50"
                                    >
                                        {{ item.description }}
                                    </span>
                                </div>
                                <div
                                    class="card-footer card-footer-fast-payment pointer text-center mb-1 position-relative px-0 py-1 bg-transparent"
                                    style="border-radius: 0px;"
                                >
                                    <!-- <button type="button" class="btn waves-effect waves-light btn-xs btn-danger m-1__2" @click="clickHistorySales(item.item_id)"><i class="fa fa-list"></i></button>
                  <button type="button" class="btn waves-effect waves-light btn-xs btn-success m-1__2" @click="clickHistoryPurchases(item.item_id)"><i class="fas fa-cart-plus"></i></button> -->
                                    <template v-if="!item.edit_unit_price">
                                        <h5
                                            class="font-weight-semibold text-start m-0 price-item-container px-2 m-0 pb-1"
                                        >
                                            {{ item.currency_type_symbol }}
                                            {{ item.sale_unit_price }}
                                            <button
                                                v-if="
                                                    configuration.options_pos &&
                                                        edit_unit_price
                                                "
                                                type="button"
                                                class="btn btn-xs btn-primary-pos edit-price"
                                                @click="
                                                    clickOpenInputEditUP(index)
                                                "
                                            >
                                                <i data-v-8ca351fc="" class="fas fa-pen"></i>
                                            </button>
                                        </h5>
                                    </template>
                                    <template v-else>
                                        <el-input
                                            min="0"
                                            v-model="item.edit_sale_unit_price"
                                            class="mt-1 mb-2 px-2"
                                            size="mini"
                                        >
                                        </el-input>
                                        <div class="btn-edit-price-container d-flex">
                                            <button
                                            class="btn btn-primary d-flex justify-content-center align-items-center p-0"
                                                @click="
                                                    clickEditUnitPriceItem(
                                                        index
                                                    )
                                                "
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                            </button>
                                            <button
                                                class="btn second-buton btn-close-pos d-flex justify-content-center align-items-center p-0"
                                                @click="
                                                    clickCancelUnitPriceItem(
                                                        index
                                                    )
                                                "
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <div
                                    v-if="configuration.options_pos"
                                    class=" card-footer configuration-options gap btn-group flex-wrap"
                                    style="width:100% !important; padding:0 !important; "
                                >
                                    <!-- <el-popover v-if="item.warehouses" placement="right" width="280"  trigger="hover">
                    <el-table  :data="item.warehouses">
                      <el-table-column width="150" property="warehouse_description" label="Ubicación"></el-table-column>
                      <el-table-column width="100" property="stock" label="Stock"></el-table-column>
                    </el-table>
                    <button type="button" style="width:100% !important;" slot="reference" class="btn btn-xs btn-default " @click="clickHistorySales(item.item_id)"><i class="fa fa-search"></i></button>
                  </el-popover> -->
                                    <!--<el-tooltip class="item" effect="dark" content="Visualizar stock" placement="bottom-end">
                    <button type="button" style="width:25% !important;"   class="btn btn-xs btn-primary-pos" @click="clickWarehouseDetail(item)">
                      <i class="fa fa-search"></i>
                    </button>
                  </el-tooltip>

                  <el-tooltip class="item" effect="dark" content="Visualizar historial de ventas del producto (precio venta) y cliente" placement="bottom-end">
                    <button type="button" style="width:25% !important;"   class="btn btn-xs btn-primary-pos" @click="clickHistorySales(item.item_id)"><i class="fa fa-list"></i></button>
                  </el-tooltip>

                  <el-tooltip class="item" effect="dark" content="Visualizar historial de compras del producto (precio compra)" placement="bottom-end">
                    <button type="button" style="width:25% !important;"  class="btn btn-xs btn-primary-pos" @click="clickHistoryPurchases(item.item_id)"><i class="fas fa-cart-plus"></i></button>
                  </el-tooltip>

                  <el-popover
                    placement="top-start"
                    title="Title"
                    width="400"
                    trigger="hover"
                    content="this is content, this is content, this is content">
                    <el-button slot="reference">Hov</el-button>
                </el-popover>-->

                                    <el-row class="mt-1" style="width:100%">
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar stock"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    style="width:100%"
                                                    type="button"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickWarehouseDetail(
                                                            item
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar historial de ventas del producto (precio venta) y cliente"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    type="button"
                                                    style="width:100%;"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickHistorySales(
                                                            item.item_id
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar historial de compras del producto (precio compra)"
                                                placement="bottom-end"
                                            >
                                                <button
                                                    type="button"
                                                    style="width:100%"
                                                    class="btn btn-xs btn-primary-pos"
                                                    @click="
                                                        clickHistoryPurchases(
                                                            item.item_id
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fas fa-cart-plus"
                                                    ></i>
                                                </button>
                                            </el-tooltip>
                                        </el-col>
                                        <el-col :span="6">
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Visualizar precios disponibles"
                                                placement="bottom-end"
                                            >
                                                <el-popover
                                                    placement="top"
                                                    width="370"
                                                    trigger="click"
                                                >

                                                    <div class="el-popover__title d-flex justify-content-between">
                                                        Precios
                                                        <el-tag v-if="priceOptionsCount(item) > 0">
                                                            {{ priceOptionsCount(item) }} OPCIONES
                                                        </el-tag>
                                                        <el-tag v-else>
                                                            SIN REGISTROS
                                                        </el-tag>
                                                    </div>
                                                    <table
                                                        v-if="item.item_unit_types"
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
                                                            <template v-if="item.item_unit_types.length == 1">
                                                                <template v-for="(price, _index) in item.item_unit_types[0].prices">
                                                                    <tr v-if="Number(price.price) > 0">
                                                                        <td class="text-start font-weight-semibold">
                                                                            {{ currency_type.symbol }}
                                                                            {{ price.price }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            {{ item.item_unit_types[0].unit_type_id }}
                                                                        </td>
                                                                        <td class="text-start">
                                                                            {{ item.item_unit_types[0].description }}
                                                                        </td>
                                                                        <td class="text-end">
                                                                            <button
                                                                                @click="
                                                                                    setPriceItem(
                                                                                        price,
                                                                                        index
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
                                                            <template v-else-if="item.item_unit_types.length == 0">
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
                                                                <template v-for="(item_unit_type, _index) in item.item_unit_types">
                                                                    <template v-for="(price, _index_price) in item_unit_type.prices">
                                                                        <tr v-if="Number(price.price) > 0">
                                                                            <td class="text-start font-weight-semibold">
                                                                                {{ currency_type.symbol }}
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
                                                                                            index
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
                                            </el-tooltip>
                                        </el-col>

                                        <!-- <el-col
                                            :span="6"
                                            v-if="
                                                allowedChangeAffectationExoneratedIgv(
                                                    item.sale_affectation_igv_type_id
                                                )
                                            "
                                        >
                                            <el-tooltip
                                                class="item"
                                                effect="dark"
                                                content="Modificar el tipo de afectación"
                                                placement="bottom-end"
                                            >
                                                <el-popover
                                                    placement="top"
                                                    title="Seleccionar tipo de afectación"
                                                    width="330"
                                                    trigger="click"
                                                >
                                                    <div
                                                        v-for="(row,
                                                        index) in getAffectationExoneratedIgv"
                                                        class="pt-1 mt-1 pb-1"
                                                    >
                                                        <el-radio
                                                            v-model="
                                                                item.sale_affectation_igv_type_id
                                                            "
                                                            :label="row.id"
                                                            @change="
                                                                changeAffectationExoneratedIgv(
                                                                    row,
                                                                    item
                                                                )
                                                            "
                                                            >{{
                                                                row.description
                                                            }}</el-radio
                                                        >
                                                    </div>

                                                    <button
                                                        slot="reference"
                                                        style="width:100%"
                                                        type="button"
                                                        class="btn btn-xs btn-primary-pos"
                                                    >
                                                        <i
                                                            class="fas fa-sync-alt"
                                                        ></i>
                                                    </button>
                                                </el-popover>
                                            </el-tooltip>
                                        </el-col> -->
                                    </el-row>
                                </div>
                            </section>
                        </div>
                    </template>
                </div>

                <table-items
                    ref="table_items"
                    @clickAddItem="clickAddItem"
                    @clickWarehouseDetail="clickWarehouseDetail"
                    @clickHistorySales="clickHistorySales"
                    @clickHistoryPurchases="clickHistoryPurchases"
                    v-if="place == 'cat3'"
                    :records="items"
                    :typeUser="typeUser"
                    :visibleTagsCustomer="focusClienteSelect"
                    :searchFromBarcode="searchFromBarcode"
                    :originIsGarage="true"
                ></table-items>

                <div v-if="place == 'prod' || place == 'cat2'" class="row">
                    <div class="col-md-12 text-center">
                        <el-pagination
                            @current-change="getRecords"
                            layout="total, prev, pager, next"
                            :total="pagination.total"
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                        >
                        </el-pagination>
                    </div>
                </div>
            </div>
            <div
                class="col-lg-4 col-md-6 bg-white m-0 p-0 pos-order-panel d-flex flex-column px-3 pt-2"
                style="height: calc(100vh - 110px); overflow-y: auto;"
            >
                <!-- ── TOP: Tipo documento + cliente ── -->
                <div class="fp-top-header">
                    <div class="fp-doc-tabs d-flex align-items-start px-1 py-1">
                        <div
                            v-for="tab in docTypeTabsAvailable"
                            :key="tab.id"
                            class="fp-doc-tab-wrap"
                            :class="{'active': form.document_type_id === tab.id}"
                        >
                            <button
                                class="fp-doc-tab w-100"
                                :class="{'active': form.document_type_id === tab.id}"
                                @click="setDocType(tab.id)"
                            >{{ tab.label }}</button>
                            <el-select
                                v-if="form.document_type_id === tab.id && show_fast_payment_garage && current_series_count > 1"
                                v-model="form.series_id"
                                size="mini"
                                class="fp-series-inline w-100 mt-1"
                                @click.native.stop
                            >
                                <el-option
                                    v-for="s in ($refs.componentFastPaymentGarage ? $refs.componentFastPaymentGarage.series : [])"
                                    :key="s.id"
                                    :label="s.number"
                                    :value="s.id"
                                ></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="fp-customer-row d-flex align-items-center py-2 px-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-search fa-user-circle fp-customer-icon me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h1.5" /><path d="M15 18a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M20.2 20.2l1.8 1.8" /></svg>
                        <el-select
                            ref="select_person"
                            v-model="form.customer_id"
                            filterable
                            placeholder="Seleccionar Cliente"
                            @change="changeCustomer"
                            @keyup.native="keyupCustomer"
                            @keyup.enter.native="keyupEnterCustomer"
                            @focus="focusClienteSelect = true"
                            @blur="focusClienteSelect = false"
                            class="fp-customer-select flex-grow-1"
                            :class="{ 'customer-error': customerError }"
                        >
                            <el-option
                                v-for="option in all_customers"
                                :key="option.id"
                                :label="option.description"
                                :value="option.id"
                            ></el-option>
                        </el-select>
                        <a class="fp-add-customer" @click.prevent="showDialogNewPerson = true" title="Agregar cliente">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                        </a>
                    </div>
                </div>

                <!-- ── MIDDLE: Lista de items (scrollable) ── -->
                <div class="pos-order-top flex-grow-1" style="overflow-y: auto; min-height: 200px;">

                    <!-- Empty state -->
                    <div v-if="!form.items.length" class="pos-empty d-flex flex-column align-items-center justify-content-center py-4">
                        <i class="fas fa-shopping-cart pos-empty-icon"></i>
                        <p class="pos-empty-text mt-2 mb-0">Sin productos</p>
                    </div>
                    <!-- Items list -->
                    <div class="px-2 py-1">
                        <template v-for="(item, index) in form.items">
                            <div :key="index" class="pos-cart-row d-flex align-items-center py-2">
                                <!-- Thumbnail -->
                                <img
                                    v-if="item.item && item.item.image_url"
                                    :src="item.item.image_url"
                                    class="pos-cart-thumb mr-2"
                                />
                                <div v-else class="pos-cart-thumb-ph mr-2">
                                    <i class="fas fa-cube"></i>
                                </div>
                                <!-- Info -->
                                <div class="pos-cart-info flex-grow-1 mr-2 min-w-0">
                                    <p class="pos-cart-name mb-0">
                                        {{ item.item.description }}
                                        <template v-if="item.item.presentation && item.item.presentation.hasOwnProperty('description')">
                                            {{ item.item.presentation.description }}
                                        </template>
                                    </p>
                                    <template v-if="edit_unit_price">
                                        <el-input
                                            v-model="item.total"
                                            size="mini"
                                            @input="calculateQuantity(index)"
                                            @blur="blurCalculateQuantity(index)"
                                            class="pos-total-input"
                                        />
                                    </template>
                                    <span v-else class="pos-cart-price">{{ currency_type.symbol }} {{ item.total }}</span>
                                </div>
                                <!-- Qty controls + trash (trash visible on row hover, a la izquierda del -) -->
                                <div class="pos-qty-wrap d-flex align-items-center">
                                    <a class="pos-cart-del" @click="clickDeleteItem(index)" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0"/>
                                            <path d="M10 11l0 6"/>
                                            <path d="M14 11l0 6"/>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                        </svg>
                                    </a>
                                    <button class="pos-qty-btn" @click="decrementItem(item, index)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 12l14 0" /></svg>
                                    </button>
                                    <el-input
                                        v-model="item.item.aux_quantity"
                                        @input="clickAddItem(item, index, true)"
                                        @keyup.enter.native="keyupEnterQuantity"
                                        class="pos-qty-field"
                                    />
                                    <button class="pos-qty-btn" @click="clickAddItem(item, index)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ── BOTTOM: Sección de pago ── -->
                <div class="payment-container-pos border-0">
                    <template v-if="show_fast_payment_garage">
                        <fast-payment
                            :is_payment.sync="is_payment"
                            :form="form"
                            :currency-type-id-active="form.currency_type_id"
                            :currency-type-active="currency_type"
                            :exchange-rate-sale="form.exchange_rate_sale"
                            :customer.sync="customer"
                            :soapCompany="soapCompany"
                            :businessTurns="businessTurns"
                            :is-print="isPrint"
                            :rows-items="form.items.length"
                            ref="componentFastPaymentGarage"
                            :configuration="configuration"
                            :type-user="typeUser"
                            @series-filtered="current_series_count = $event"
                            @customer-required="customerError = true"
                        ></fast-payment>
                    </template>
                </div>
            </div>

            <person-form
                :showDialog.sync="showDialogNewPerson"
                type="customers"
                :input_person="input_person"
                :external="true"
                :document_type_id="form.document_type_id"
            ></person-form>

            <item-form
                :showDialog.sync="showDialogNewItem"
                :external="true"
            ></item-form>
        </div>

        <history-sales-form
            :showDialog.sync="showDialogHistorySales"
            :item_id="history_item_id"
            :customer_id="form.customer_id"
        ></history-sales-form>

        <history-purchases-form
            :showDialog.sync="showDialogHistoryPurchases"
            :item_id="history_item_id"
        ></history-purchases-form>

        <warehouses-detail
            :showDialog.sync="showWarehousesDetail"
            :warehouses="warehousesDetail"
            :unit_type="unittypeDetail"
            :item_unit_types="[]"
        >
        </warehouses-detail>

        <item-unit-types
            :showDialog.sync="showDialogItemUnitTypes"
            :itemUnitTypes="itemUnitTypes"
        >
        </item-unit-types>
    </div>
</template>
<style>
.el-select-dropdown__item.hover {
    /* background-color: red; */
    background-color: var(--tq-accent-color-muted);
}

/* The heart of the matter */
.testimonial-group > .row {
    overflow-x: auto;
    white-space: nowrap;
    overflow-y: hidden;
}

.testimonial-group > .row > .col-sm-3-name {
    display: inline-block;
    float: none;
}

/* Decorations */
.col-sm-3-name {
    height: 70px;
    margin-right: 0.5%;
    color: white;
    font-size: 18px;
    padding-bottom: 20px;
    padding-top: 18px;
    font-weight: bold;
}

.card-block {
    min-height: 220px;
}

.ex1 {
    overflow-x: scroll;
}

.cat_c {
    width: 100px;
    margin: 1%;
    padding: 3px;
    font-weight: bold;
    color: white;
    min-height: 90px;
}

.cat_c p {
    color: white;
}

.c-width {
    width: 80px !important;
    padding: 0 !important;
    margin-right: 0 !important;
}

.el-select-dropdown {
    max-width: 80% !important;
    margin-right: 1% !important;
}

.el-input-group__append {
    padding: 0 10px !important;
}

.ws-flotante {
    display: none;
}
.product-pos-container {
    display: grid;
}

.product-pos-container.default {
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr));
    gap: 1rem;
}

.product-pos-container.comfortable {
    grid-template-columns: repeat(auto-fit, minmax(185px, 1fr));
    gap: 0.9rem;
}

.product-pos-container.compact {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.5rem;
}

.product-pos-container.stacked {
    grid-template-columns: repeat(auto-fit, minmax(135px, 1fr));
    gap: 0.25rem;
}

.product-pos-container > * {
    max-width: 300px;
    width: 100%;
}
.el-tooltip__popper {
    white-space: pre-line;
}
.internal-code-small {
    width: 45%; 
    white-space: nowrap;
    overflow: hidden; 
    text-overflow: ellipsis;
}
/* ── POS · Venta Rápida — Panel lateral ──────────────────────
   Los estilos del panel derecho (tabs documento, cliente,
   items del carrito, totales, botón Pagar y Cancelar) viven en
   storage/app/public/skins/tu-quipu.css
   No duplicar aquí. */
</style>

<script>
import Keypress from "vue-keypress";
import { calculateRowItem } from "../../../helpers/functions";
import FastPayment from "./partials/fast_payment_garage.vue";
import ItemForm from "./partials/form.vue";
import { functions, exchangeRate } from "../../../mixins/functions";
import HistorySalesForm from "../../../../../modules/Pos/Resources/assets/js/views/history/sales.vue";
import HistoryPurchasesForm from "../../../../../modules/Pos/Resources/assets/js/views/history/purchases.vue";
import PersonForm from "../persons/form.vue";
import WarehousesDetail from "../items/partials/warehouses.vue";
import queryString from "query-string";
import TableItems from "./partials/table.vue";
import ItemUnitTypes from "./partials/item_unit_types.vue";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";

export default {
    props: [
        "configuration",
        "soapCompany",
        "businessTurns",
        "typeUser",
        "isPrint"
    ],

    components: {
        FastPayment,
        ItemForm,
        HistorySalesForm,
        HistoryPurchasesForm,
        PersonForm,
        WarehousesDetail,
        ItemUnitTypes,
        Keypress,
        TableItems
    },
    mixins: [functions, exchangeRate],

    data() {
        return {
            place: "cat",
            showDialogItemUnitTypes: false,
            history_item_id: null,
            search_item_by_barcode: false,
            warehousesDetail: [],
            unittypeDetail: [],
            input_person: {},
            showDialogHistoryPurchases: false,
            showDialogHistorySales: false,
            showDialogNewPerson: false,
            showDialogNewItem: false,
            loading: false,
            is_payment: false, //aq
            // is_payment: true,//aq
            showWarehousesDetail: false,
            resource: "pos",
            recordId: null,
            input_item: "",
            items: [],
            all_items: [],
            customers: [],
            affectation_igv_types: [],
            all_customers: [],
            establishment: null,
            currency_types: [],
            currency_type: {},
            form_item: {},
            customer: {},
            row: {},
            user: {},
            form: {},
            categories: [],
            colors: ["#1cb973", "#bf7ae6", "#fc6304", "#9b4db4", "#77c1f3"],
            pagination: {},
            category_selected: "",
            focusClienteSelect: false,
            show_fast_payment_garage: false,
            itemUnitTypes: [],
            affectations_exonerated_igv: ["10", "20"],
            searchFromBarcode: false,
            current_series_count: 0,
            doc_type_tabs: [
                { id: '03', label: 'Boleta' },
                { id: '01', label: 'Factura' },
                { id: '80', label: 'N. Venta' },
            ],
            customerError: false,
        };
    },
    async created() {
        this.loadConfiguration();

        this.show_fast_payment_garage = false;
        await this.initForm();
        await this.getTables();
        await this.getPercentageIgv();
        this.events();

        await this.getFormPosLocalStorage();
        await this.initCurrencyType();
        this.customer = await this.getLocalStorageIndex("customer");

        await this.selectDefaultCustomer();

        this.show_fast_payment_garage = true;

        this.form.establishment_id = this.establishment.id;

        this.enabledSearchItemByBarcode();
    },

    computed: {
        layout_mode() {
            const cols = parseInt(this.configuration.colums_grid_item, 10);
            switch (cols) {
                case 2:
                    return "default";
                case 3:
                    return "comfortable";
                case 4:
                    return "compact";
                case 5:
                case 6:
                    return "stacked";
                default:
                    return "default";
            }
        },
        getAffectationExoneratedIgv() {
            return _.filter(this.affectation_igv_types, row => {
                return this.isExoneratedIgv(row.id);
            });
        },
        ...mapState(["config"]),
        isNrus() {
            return !!((this.configuration && this.configuration.is_nrus) || (this.config && this.config.is_nrus));
        },
        docTypeTabsAvailable() {
            // NRUS no emite Factura (01)
            if (this.isNrus) {
                return this.doc_type_tabs.filter(tab => tab.id !== '01');
            }
            return this.doc_type_tabs;
        },
        validteCreateProduct() {
            if (this.config) {
                return (
                    this.config.typeUser == "admin" ||
                    (this.config.typeUser == "seller" &&
                        this.config.seller_can_create_product)
                );
            }

            return false;
        },

        classObjectCol() {
            let cols = this.configuration.colums_grid_item;

            let clase = "c3";
            switch (cols) {
                case 2:
                    clase = "50%";

                    break;
                case 3:
                    clase = "33.33%";

                    break;
                case 4:
                    clase = "25%";

                    break;
                case 5:
                    clase = "20%";

                    break;
                case 6:
                    clase = "16.66%";
                    break;
                default:
            }
            return {
                width: `${clase}`,
                padding: "5px"
            };
        },
        edit_unit_price() {
            return this.user && this.user.permission_edit_item_prices;
        },
        customerEmail() {
            const customer = _.find(this.all_customers, c => String(c.id) === String(this.form.customer_id));
            return customer ? customer.email : null;
        }
    },
    methods: {
        changeRowTotalGarage(index) {
            const item = this.form.items[index];

            if (item.item.calculate_quantity) {
                this.blurCalculateQuantity(index);
                return;
            }

            const quantity = parseFloat(item.quantity);
            const newTotal = parseFloat(item.total);

            if (isNaN(newTotal) || isNaN(quantity) || quantity <= 0) {
                this.blurCalculateQuantity(index);
                return;
            }

            // Precio unitario bruto (con IGV) desde el total
            const newUnitPrice = newTotal / quantity;
            item.item.unit_price = newUnitPrice;

            // Guardar también en sale_unit_price para que cambiar la cantidad NO lo revierta
            item.item.sale_unit_price = item.item.has_igv
                ? newUnitPrice
                : newUnitPrice / (1 + this.percentage_igv);

            this.row = calculateRowItem(item, this.form.currency_type_id, 1, this.percentage_igv);
            this.row["unit_type_id"] = item.unit_type_id;
            this.form.items[index] = this.row;

            this.calculateTotal();
            this.setFormPosLocalStorage();
        },
        enabledSearchItemByBarcode() {
            if (this.configuration.search_item_by_barcode) {
                this.search_item_by_barcode = true;
            }
        },
        isExoneratedIgv(affectation_igv_type_id) {
            return this.affectations_exonerated_igv.includes(
                affectation_igv_type_id
            );
        },
        allowedChangeAffectationExoneratedIgv(affectation_igv_type_id) {
            if (this.configuration) {
                return (
                    this.configuration.change_affectation_exonerated_igv &&
                    this.isExoneratedIgv(affectation_igv_type_id)
                );
            }

            return false;
        },
        changeAffectationExoneratedIgv(affectation_igv_type, item) {
            const exist_item = _.find(this.form.items, {
                item_id: item.item_id
            });

            if (exist_item) {
                if (
                    exist_item.affectation_igv_type_id !=
                    affectation_igv_type.id
                )
                    this.$message.warning(
                        "Ya agregó el producto con otro tipo de afectación, para aplicar el cambio debe eliminarlo y agregarlo nuevamente."
                    );
            }

            item.change_affectation_exonerated_igv = true;
        },
        setOriginalAffectationToItems() {
            if (
                this.configuration !== undefined &&
                this.configuration.change_affectation_exonerated_igv
            ) {
                this.items.forEach(row => {
                    if (
                        row.change_affectation_exonerated_igv !== undefined &&
                        row.change_affectation_exonerated_igv &&
                        row.sale_affectation_igv_type_id !=
                            row.original_affectation_igv_type_id
                    ) {
                        row.sale_affectation_igv_type_id =
                            row.original_affectation_igv_type_id;
                    }
                });
            }
        },
        ...mapActions(["loadConfiguration"]),
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
        keyupEnterQuantity() {
            this.initFocus();
        },
        decrementItem(item, index) {
            const qty = parseInt(item.item.aux_quantity);
            if (qty <= 1) {
                this.clickDeleteItem(index);
            } else {
                item.item.aux_quantity = qty - 1;
                this.clickAddItem(item, index, true);
            }
        },
        setDocType(typeId) {
            this.form.document_type_id = typeId;
            if (this.$refs.componentFastPaymentGarage)
                this.$refs.componentFastPaymentGarage.filterSeries();
        },
        handleFn112(response) {
            this.search_item_by_barcode = !this.search_item_by_barcode;
        },
        handleFn113() {
            this.setView("cat3");
        },
        handleFn115() {
            this.openDialogNewPerson();
        },
        initFocus() {
            this.$refs.ref_search_items.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        keyupTabCustomer(e) {
            // console.log(e.keyCode)
            if (e.keyCode === 9) {
                this.$refs.select_person.$el
                    .getElementsByTagName("input")[0]
                    .focus();
            }
        },
        keyupEnterAddItem() {
            if (this.place == "cat3") {
                return false;
            }

            if (this.items.length == 1) {
                if (
                    this.items[0].unit_type.length > 0 &&
                    this.configuration.select_available_price_list
                ) {
                    // console.log(this.configuration.select_available_price_list)
                    this.itemUnitTypes = this.items[0].unit_type;
                    this.showDialogItemUnitTypes = true;
                } else {
                    this.clickAddItem(this.items[0], 0);
                    this.filterItems();
                    this.cleanInput();
                }
            } else {
                this.$message.warning(
                    "No puede añadir directamente el producto al listado, hay más de uno ubicado en la búsqueda"
                );
            }
        },
        filterCategorie(id, mod = false) {
            if (id) {
                this.category_selected = id;
                this.getRecords();
            } else {
                this.category_selected = "";
                this.getRecords();
            }

            if (mod) {
                this.place = "cat2";
            } else {
                this.place = "prod";
            }
        },
        getRecords() {
            this.loading = true;
            return this.$http
                .get(`/${this.resource}/items?${this.getQueryParameters()}`)
                .then(response => {
                    this.all_items = response.data.data;
                    this.filterItems();
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                    this.loading = false;
                    if (response.data.meta.total > 0) {
                        this.pagination.total = response.data.meta.total;
                    } else {
                        this.pagination.total = 0;
                    }
                    this.fixItems();
                });
        },
        getQueryParameters() {
            let p = {
                page: this.pagination.current_page
                    ? this.pagination.current_page
                    : 1,
                input_item: this.input_item,
                cat: this.category_selected,
                limit: this.limit
            };
            
            if (this.businessTurns && [true, 1, "1"].includes(this.businessTurns.active)) {
                p.garage = 1;
            }
            
            return queryString.stringify(p);
        },
        getColor(i) {
            return this.colors[i % this.colors.length];
        },
        initCurrencyType() {
            const exists = _.find(this.currency_types, {
                id: this.form.currency_type_id
            });
            if (!exists && this.currency_types.length > 0) {
                this.form.currency_type_id = this.currency_types[0].id;
                this.changeCurrencyType();
                return;
            }
            this.currency_type = exists;
        },
        getFormPosLocalStorage() {
            let form_pos = localStorage.getItem("form_pos_garage");
            form_pos = JSON.parse(form_pos);
            if (form_pos) {
                this.form = form_pos;
                this.initDateTimeIssue();
                // this.calculateTotal()
            }
            let amount = localStorage.setItem(
                "amount_garage",
                JSON.stringify(this.form.total)
            );
        },
        initDateTimeIssue() {
            this.form.date_of_issue = moment().format("YYYY-MM-DD");
            this.form.time_of_issue = moment().format("HH:mm:ss");
            this.form.date_of_due = moment().format("YYYY-MM-DD");
        },
        setFormPosLocalStorage(form_param = null) {
            if (form_param) {
                localStorage.setItem(
                    "form_pos_garage",
                    JSON.stringify(form_param)
                );
            } else {
                localStorage.setItem(
                    "form_pos_garage",
                    JSON.stringify(this.form)
                );
            }
        },
        cancelFormPosLocalStorage() {
            localStorage.setItem("form_pos_garage", JSON.stringify(null));
            this.setLocalStorageIndex("customer_garage", null);
        },
        clickOpenInputEditUP(index) {
            this.items[index].edit_unit_price = true;
        },
        clickEditUnitPriceItem(index) {
            // console.log(index)
            let item_search = this.items[index];
            this.items[index].sale_unit_price = this.items[
                index
            ].edit_sale_unit_price;
            this.items[index].edit_unit_price = false;

            // console.log(item_search)
        },
        clickCancelUnitPriceItem(index) {
            // console.log(index)
            this.items[index].edit_unit_price = false;
        },
        setPriceItem(price, index) {
            const item = this.items[index];
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
            }

            this.items[index].sale_unit_price = price.price;
            this.items[index].unit_type_id = price.unit_type_id;
            this.items[index].presentation = price

            this.$message.success("Precio seleccionado");
        },
        clickWarehouseDetail(item) {
            this.unittypeDetail = item.unit_type;
            this.warehousesDetail = item.warehouses;
            this.showWarehousesDetail = true;
        },
        clickHistoryPurchases(item_id) {
            this.history_item_id = item_id;
            this.showDialogHistoryPurchases = true;
            // console.log(item)
        },
        clickHistorySales(item_id) {
            if (!this.form.customer_id)
                return this.$message.error("Debe seleccionar el cliente");

            this.history_item_id = item_id;
            this.showDialogHistorySales = true;
            // console.log(item)
        },
        keyupEnterCustomer() {
            if (this.place == "cat3") {
                return false;
            }

            if (this.form.customer_id) {
                this.clickPayment();
                return;
            }

            this.openDialogNewPerson();

            // if (this.input_person.number) {
            //     if (!isNaN(parseInt(this.input_person.number))) {
            //         switch (this.input_person.number.length) {
            //             case 8:
            //                 this.input_person.identity_document_type_id = "1";
            //                 this.showDialogNewPerson = true;
            //                 break;

            //             case 11:
            //                 this.input_person.identity_document_type_id = "6";
            //                 this.showDialogNewPerson = true;
            //                 break;
            //             default:
            //                 this.input_person.identity_document_type_id = "6";
            //                 this.showDialogNewPerson = true;
            //                 break;
            //         }
            //     }
            // }
        },
        openDialogNewPerson() {
            if (this.input_person.number) {
                if (!isNaN(parseInt(this.input_person.number))) {
                    switch (this.input_person.number.length) {
                        case 8:
                            this.input_person.identity_document_type_id = "1";
                            this.showDialogNewPerson = true;
                            break;

                        case 11:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                        default:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                    }
                }
            }
        },
        keyupCustomer(e) {
            if (this.place == "cat3") {
                return false;
            }

            if (e.key !== "Enter") {
                this.input_person.number = this.$refs.select_person.$el.getElementsByTagName(
                    "input"
                )[0].value;
                let exist_persons = this.all_customers.filter(customer => {
                    let pos = customer.description.search(
                        this.input_person.number
                    );
                    return pos > -1;
                });

                this.input_person.number =
                    exist_persons.length == 0 ? this.input_person.number : null;
            }
        },
        calculateQuantity(index) {
            // console.log(this.form.items[index])
            if (this.form.items[index].item.calculate_quantity) {
                let quantity = _.round(
                    parseFloat(this.form.items[index].total) /
                        parseFloat(this.form.items[index].unit_price),
                    4
                );

                if (quantity) {
                    this.form.items[index].quantity = quantity;
                    this.form.items[index].item.aux_quantity = quantity;
                } else {
                    this.form.items[index].quantity = 0;
                    this.form.items[index].item.aux_quantity = 0;
                }
                // this.calculateTotal()
            }

            //  this.clickAddItem(this.form.items[index],index, true)
        },
        blurCalculateQuantity(index) {
            const current = this.form.items[index];
            if (!current.item.calculate_quantity) {
                const quantity = parseFloat(current.quantity);
                const newTotal = parseFloat(current.total);
                if (quantity > 0 && !isNaN(newTotal) && newTotal >= 0) {
                    const newUnitPrice = _.round(newTotal / quantity, 6);
                    current.item.unit_price = newUnitPrice;
                    current.unit_price = newUnitPrice;
                }
            }

            this.row = calculateRowItem(
                this.form.items[index],
                this.form.currency_type_id,
                1,
                this.percentage_igv
            );

            // console.log(this.form.items[index])

            this.row["unit_type_id"] = this.form.items[index].unit_type_id;

            this.form.items[index] = this.row;
            this.calculateTotal();
            this.setFormPosLocalStorage();
        },
        blurCalculateQuantity2(index) {
            this.row = calculateRowItem(
                this.form.items[index],
                this.form.currency_type_id,
                1,
                this.percentage_igv
            );
            this.form.items[index] = this.row;
            this.calculateTotal();
        },
        changeCustomer() {
            // console.log('clien 13')

            this.customerError = false;

            let customer = _.find(this.all_customers, {
                id: this.form.customer_id
            });
            this.customer = customer;

            if (this.configuration.default_document_type_80) {
                this.form.document_type_id = "80";
            } else if (this.configuration.default_document_type_03) {
                this.form.document_type_id = "03";
            } else if (this.isNrus) {
                // NRUS no emite Factura, siempre Boleta
                this.form.document_type_id = "03";
            } else {
                this.form.document_type_id =
                    customer.identity_document_type_id == "6" ? "01" : "03";
            }

            // console.log(this.customer);

            if (this.$refs.componentFastPaymentGarage)
                this.$refs.componentFastPaymentGarage.filterSeries();

            this.setLocalStorageIndex("customer", this.customer);
            this.setFormPosLocalStorage();
        },

        getLocalStorageIndex(key, re_default = null) {
            let ls_obj = localStorage.getItem(key);
            ls_obj = JSON.parse(ls_obj);

            if (ls_obj) {
                return ls_obj;
            }

            return re_default;
        },
        setLocalStorageIndex(key, obj) {
            localStorage.setItem(key, JSON.stringify(obj));
        },
        async events() {
            await this.$eventHub.$on("initInputPerson", () => {
                this.initInputPerson();
            });

            await this.$eventHub.$on(
                "eventSetFormPosLocalStorageGarage",
                form_param => {
                    this.setFormPosLocalStorage(form_param);
                }
            );

            await this.$eventHub.$on("cancelSaleGarage", () => {
                this.is_payment = false;
                this.initForm();
                this.changeExchangeRate();
                this.cancelFormPosLocalStorage();
                this.selectDefaultCustomer();

                this.$nextTick(() => {
                    this.initFocus();
                    if (
                        this.$refs.componentFastPaymentGarage &&
                        !this.form.series_id
                    )
                        this.$refs.componentFastPaymentGarage.filterSeries();
                });

                this.form.establishment_id = this.establishment.id;
            });

            // await this.$eventHub.$on("indexInitFocus", () => {
            //   if(!this.is_payment) this.initFocus()
            // });

            await this.$eventHub.$on("reloadDataPersons", customer_id => {
                this.reloadDataCustomers(customer_id);
                this.setFormPosLocalStorage();
            });

            await this.$eventHub.$on("reloadDataItems", item_id => {
                this.reloadDataItems(item_id);
            });

            await this.$eventHub.$on("saleSuccess", () => {
                // this.is_payment = false
                this.initForm();
                this.getTables();
                this.setFormPosLocalStorage();
                this.setOriginalAffectationToItems();
            });

            await this.$eventHub.$on("enterSelectItemUnitType", unit_type => {
                this.selectItemUnitType(unit_type);
            });

            this.form.establishment_id = this.establishment.id;
        },
        selectItemUnitType(unit_type) {
            this.setPriceItem(unit_type, 0);
            this.clickAddItem(this.items[0], 0);
            this.filterItems();
            this.cleanInput();
            this.initFocus();
        },
        initForm() {
            this.form = {
                establishment_id: null,
                document_type_id: "03",
                series_id: null,
                prefix: null,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: moment().format("HH:mm:ss"),
                customer_id: null,
                currency_type_id: "PEN",
                purchase_order: null,
                exchange_rate_sale: 1,
                total_prepayment: 0,
                total_charge: 0,
                total_discount: 0,
                total_exportation: 0,
                total_free: 0,
                total_taxed: 0,
                total_unaffected: 0,
                total_exonerated: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_plastic_bag_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                total: 0,
                subtotal: 0,
                operation_type_id: "0101",
                date_of_due: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                payments: [],
                hotel: {},
                additional_information: null,
                actions: {
                    format_pdf: "a4"
                },
                reference_data: null
            };

            this.initFormItem();
            this.changeDateOfIssue();
            this.initInputPerson();
        },
        initInputPerson() {
            this.input_person = {
                number: "",
                identity_document_type_id: ""
            };
        },
        initFormItem() {
            this.form_item = {
                item_id: null,
                item: {},
                affectation_igv_type_id: null,
                affectation_igv_type: {},
                has_isc: false,
                system_isc_type_id: null,
                calculate_quantity: false,
                percentage_isc: 0,
                suggested_price: 0,
                quantity: 1,
                aux_quantity: 1,
                unit_price_value: 0,
                unit_price: 0,
                charges: [],
                discounts: [],
                attributes: [],
                has_igv: false,
                has_plastic_bag_taxes: false
            };
        },
        async clickPayment() {
            if (!this.form.subtotal) {
                //fix para agregar subtotal si no existe prop en json almacenado en local storage
                this.form.subtotal = this.form.total;
            }

            let flag = 0;
            this.form.items.forEach(row => {
                if (row.aux_quantity < 0 || row.total < 0 || isNaN(row.total)) {
                    flag++;
                }
            });

            if (flag > 0)
                return this.$message.error("Cantidad negativa o incorrecta");
            if (!this.form.customer_id)
                return this.$message.error("Seleccione un cliente");
            if (!this.form.items[0])
                return this.$message.error("Seleccione un producto");
            this.form.establishment_id = this.establishment.id;
            this.loading = true;
            await this.sleep(800);
            this.is_payment = true;
            this.loading = false;
        },
        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },
        clickDeleteCustomer() {
            this.form.customer_id = null;
            this.customer = null;
            this.setLocalStorageIndex("customer", null);
            this.setFormPosLocalStorage();
        },
        async clickAddItem(item, index, input = false) {
            //Validar precio mínimo

            if (parseFloat(item.sale_unit_price) < 0.1) {
                this.$message.error(
                    "El precio del producto debe ser mayor a 0.1"
                );
                this.loading = false;
                return;
            }

            let addingNotification = this.$notify({
                title: "",
                message: "Agregando...",
                type: "info",
                duration: 0
            });

            let exchangeRateSale = this.form.exchange_rate_sale;

            // console.log(item.unit_type_id)
            // console.log(exist_item)
            // console.log(item)
            let presentation = item.presentation

            let exist_item =  false
            if (presentation === undefined) {
                exist_item = _.find(this.form.items, {
                    item_id: item.item_id,
                    unit_type_id: item.unit_type_id
                });
            }
            else {
                // Se evalua si existe presentation de item
                exist_item = _.find(this.form.items, {
                    item_id: item.item_id,
                    presentation: presentation,
                    unit_type_id: item.unit_type_id
                });
            }
            // console.log(exist_item)

            let pos = this.form.items.indexOf(exist_item);
            let response = null;

            if (exist_item) {
                if (input) {
                    response = await this.getStatusStock(
                        item.item_id,
                        exist_item.item.aux_quantity
                    );
                    if (!response.success) {
                        item.item.aux_quantity = item.quantity;
                        this.loading = false;
                        addingNotification.close();
                        return this.$message.error(response.message);
                    }

                    exist_item.quantity = exist_item.item.aux_quantity;
                } else {
                    response = await this.getStatusStock(
                        item.item_id,
                        parseFloat(exist_item.item.aux_quantity) + 1
                    );
                    if (!response.success) {
                        this.loading = false;
                        addingNotification.close();
                        return this.$message.error(response.message);
                    }

                    exist_item.quantity++;
                    exist_item.item.aux_quantity++;
                }

                // console.log(exist_item)
                let search_item_bd = await _.find(this.items, {
                    item_id: item.item_id
                });

                if (search_item_bd) {
                    exist_item.item.unit_price = parseFloat(
                        search_item_bd.sale_unit_price
                    );
                }

                let unit_price = exist_item.item.has_igv
                    ? exist_item.item.sale_unit_price
                    : exist_item.item.sale_unit_price *
                      (1 + this.percentage_igv);
                // exist_item.unit_price = unit_price
                exist_item.item.unit_price = unit_price;

                exist_item.has_plastic_bag_taxes =
                    exist_item.item.has_plastic_bag_taxes;

                this.row = calculateRowItem(
                    exist_item,
                    this.form.currency_type_id,
                    exchangeRateSale,
                    this.percentage_igv
                );

                console.log(this.row);


                this.row["unit_type_id"] = item.unit_type_id;

                this.form.items[pos] = this.row;
            } else {
                response = await this.getStatusStock(
                    item.item_id,
                    item.presentation
                        ? parseInt(item.presentation.quantity_unit)
                        : 1
                );
                if (!response.success) {
                    this.loading = false;
                    addingNotification.close();
                    return this.$message.error(response.message);
                }

                // this.form_item.item = item;
                this.form_item.item = { ...item };

                this.form_item.unit_price_value = this.form_item.item.sale_unit_price;
                this.form_item.has_igv = this.form_item.item.has_igv;
                this.form_item.has_plastic_bag_taxes = this.form_item.item.has_plastic_bag_taxes;
                this.form_item.affectation_igv_type_id = this.form_item.item.sale_affectation_igv_type_id;
                this.form_item.quantity = 1;
                this.form_item.aux_quantity = 1;

                let unit_price = this.form_item.has_igv
                    ? this.form_item.unit_price_value
                    : this.form_item.unit_price_value *
                      (1 + this.percentage_igv);

                this.form_item.unit_price = unit_price;
                this.form_item.item.unit_price = unit_price;
                this.form_item.item.presentation = item.presentation
                    ? item.presentation
                    : null;

                this.form_item.charges = [];
                this.form_item.discounts = [];
                this.form_item.attributes = [];
                this.form_item.affectation_igv_type = _.find(
                    this.affectation_igv_types,
                    { id: this.form_item.affectation_igv_type_id }
                );

                // console.log(this.form_item)
                this.row = calculateRowItem(
                    this.form_item,
                    this.form.currency_type_id,
                    exchangeRateSale,
                    this.percentage_igv
                );
                console.log(this.row)

                // this.row['unit_type_id'] = item.presentation ? item.presentation.unit_type_id : 'NIU';

                this.row["unit_type_id"] = item.presentation
                    ? item.presentation.unit_type_id
                    : this.form_item.item.unit_type_id;

                this.form.items.unshift(this.row);
                item.aux_quantity = 1;
            }

            // console.log("pos", this.row);

            addingNotification.close();

            this.$notify({
                title: "",
                message: "Producto añadido!",
                type: "success",
                duration: 1000
            });

            this.cleanInput();

            if (!input) {
                this.initFocus();
            }

            // console.log(this.row)
            // console.log(this.form.items)
            await this.calculateTotal();

            await this.setFormPosLocalStorage();

            await this.setDefaultDataPriceSelected(item);
        },
        setDefaultDataPriceSelected(item) {
            if (
                item.apply_price_selected_add_product != undefined &&
                item.apply_price_selected_add_product &&
                this.configuration.price_selected_add_product
            ) {
                item.sale_unit_price = parseFloat(item.aux_sale_unit_price);
                item.unit_type_id = item.aux_unit_type_id;
                item.presentation = null;

                item.apply_price_selected_add_product = false;
            }
        },
        async getStatusStock(item_id, quantity) {
            let data = {};
            if (!quantity) quantity = 0;
            await this.$http
                .get(`/${this.resource}/validate_stock/${item_id}/${quantity}`)
                .then(response => {
                    data = response.data;
                });
            return data;
        },
        async clickDeleteItem(index) {
            this.form.items.splice(index, 1);

            this.calculateTotal();

            await this.setFormPosLocalStorage();
        },

        calculateTotal() {
            let total_discount = 0;
            let total_charge = 0;
            let total_exportation = 0;
            let total_taxed = 0;
            let total_exonerated = 0;
            let total_unaffected = 0;
            let total_free = 0;
            let total_igv = 0;
            let total_value = 0;
            let total = 0;
            let total_plastic_bag_taxes = 0;

            this.form.items.forEach(row => {
                total_discount += parseFloat(row.total_discount);
                total_charge += parseFloat(row.total_charge);

                if (row.affectation_igv_type_id === "10") {
                    total_taxed += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "20") {
                    total_exonerated += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "30") {
                    total_unaffected += parseFloat(row.total_value);
                }
                if (row.affectation_igv_type_id === "40") {
                    total_exportation += parseFloat(row.total_value);
                }
                if (
                    ["10", "20", "30", "40"].indexOf(
                        row.affectation_igv_type_id
                    ) < 0
                ) {
                    total_free += parseFloat(row.total_value);
                }
                if (
                    ["10", "20", "30", "40"].indexOf(
                        row.affectation_igv_type_id
                    ) > -1
                ) {
                    total_igv += parseFloat(row.total_igv);
                    total += parseFloat(row.total);
                }
                total_value += parseFloat(row.total_value);
                total_plastic_bag_taxes += parseFloat(
                    row.total_plastic_bag_taxes
                );
            });

            this.form.total_exportation = _.round(total_exportation, 2);
            this.form.total_exonerated = _.round(total_exonerated, 2);
            this.form.total_taxed = _.round(total_taxed, 2);
            this.form.total_exonerated = _.round(total_exonerated, 2);

            // this.form.total_taxed =
            //   _.round(total_taxed, 2) + this.form.total_exonerated;
            // this.form.total_exonerated = _.round(total_exonerated, 2)
            this.form.total_unaffected = _.round(total_unaffected, 2);
            this.form.total_free = _.round(total_free, 2);
            this.form.total_igv = _.round(total_igv, 2);
            this.form.total_value = _.round(total_value, 2);
            this.form.total_taxes = _.round(total_igv, 2);
            this.form.total_plastic_bag_taxes = _.round(
                total_plastic_bag_taxes,
                2
            );
            // this.form.total = _.round(total, 2);
            this.form.total = _.round(
                total + this.form.total_plastic_bag_taxes,
                2
            );
            this.form.subtotal = this.form.total;

            // Ya no esta funcionando el evento emit de checkPaymentGarage,
            // this.checkPaymentGarage();
            this.$refs.componentFastPaymentGarage.checkPaymentGarage(this.form.total)
        },
        checkPaymentGarage() {
            this.$eventHub.$emit("eventCheckPaymentGarage");
        },
        changeDateOfIssue() {
            // this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
            //     this.form.exchange_rate_sale = response
            // })
        },
        changeExchangeRate() {
            this.searchExchangeRateByDate(this.form.date_of_issue).then(
                response => {
                    this.form.exchange_rate_sale = response;
                }
            );
        },
        async getTables() {
            await this.$http.get(`/${this.resource}/tables`).then(response => {
                //this.all_items = response.data.items;
                this.affectation_igv_types =
                    response.data.affectation_igv_types;
                this.all_customers = response.data.customers;
                this.establishment = response.data.establishment;
                this.currency_types = response.data.currency_types;
                this.user = response.data.user;
                this.form.establishment_id = this.establishment.id;
                this.form.currency_type_id =
                    this.currency_types.length > 0
                        ? this.currency_types[0].id
                        : null;
                this.renderCategories(response.data.categories);
                // this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                // this.changeCurrencyType();
                //this.filterItems();
                this.changeDateOfIssue();
                this.changeExchangeRate();
            });
        },
        selectDefaultCustomer() {
            if (this.establishment.customer_id && !this.form.customer_id) {
                this.form.customer_id = this.establishment.customer_id;
                this.changeCustomer();
            }
        },
        renderCategories(source) {
            const contex = this;
            this.categories = source.map((obj, index) => {
                return {
                    id: obj.id,
                    name: obj.name,
                    color: contex.getColor(index)
                };
            });

            this.categories.unshift({
                id: null,
                name: "Todos",
                color: "#2C8DE3"
            });
        },
        async searchItems() {
            if (this.input_item.length > 0) {
                this.loading = true;
                let parameters = `input_item=${this.input_item}&cat=${
                    this.category_selected
                }`;
                
                if (this.businessTurns && [true, 1, "1"].includes(this.businessTurns.active)) {
                    parameters += '&garage=1';
                }

                await this.$http
                    .get(`/${this.resource}/search_items_cat?${parameters}`)
                    .then(response => {
                        this.all_items = response.data.data;

                        if (response.data.data.length > 0) {
                            // this.all_items = response.data.data;
                            this.filterItems();
                            this.pagination = response.data.meta;
                            this.pagination.per_page = parseInt(
                                response.data.meta.per_page
                            );
                            this.fixItems();
                            this.loading = false;
                        } else {
                            this.loading = false;
                            this.filterItems();
                        }
                    });
            } else {
                this.getRecords();
                this.filterItems();
            }
        },
        async searchItemsBarcode() {
            // console.log(query)
            // console.log("in:" + this.input_item)
            if (this.input_item.length > 1) {
                this.loading = true;
                let parameters = `input_item=${this.input_item}`;

                await this.$http
                    .get(`/${this.resource}/search_items?${parameters}`)
                    .then(response => {
                        console.log("buah");
                        this.items = response.data.items;
                        this.enabledSearchItemsBarcode();
                        this.loading = false;
                        if (this.items.length == 0) {
                            this.filterItems();
                        }
                    });
            } else {
                await this.filterItems();
            }
        },
        fixItems() {
            this.items = this.all_items.map(i => {
                /** Si description es vacio y hay nombre */
                if (i.name !== undefined) {
                    if (i.description === undefined || i.description == null) {
                        i.description = i.name;
                    }
                }
                /** Si description es vacio aun */
                if (i.description == null) {
                    i.description = i.internal_id;
                }

                return i;
            });
            this.all_items = this.items;
        },
        enabledSearchItemsBarcode() {
            if (this.search_item_by_barcode) {
                if (this.items.length == 1) {
                    // console.log(this.items)
                    this.clickAddItem(this.items[0], 0);
                    this.filterItems();
                }

                this.cleanInput();
            }
        },
        changeSearchItemBarcode() {
            this.cleanInput();
        },
        cleanInput() {
            this.input_item = null;
        },
        filterItems() {
            if (this.place === "cat3") {
                this.items = this.all_items;
            } else {
                this.items = this.all_items.map(i => {
                    // console.log(i.description);
                    // if (i.brand) {
                    //     var desc = `${i.description} - ${i.brand}`;
                    //     if(i.description != desc){
                    //         i.description = `${i.description} - ${i.brand}`;
                    //     }
                    // }
                    // console.log(i.description);
                    return i;
                });
            }
        },
        reloadDataCustomers(customer_id) {
            this.$http
                .get(`/${this.resource}/table/customers`)
                .then(response => {
                    this.all_customers = response.data;
                    this.form.customer_id = customer_id;
                    this.changeCustomer();
                });
        },
        reloadDataItems(item_id) {
            this.$http.get(`/${this.resource}/table/items`).then(response => {
                this.all_items = response.data;
                this.fixItems();
                this.filterItems();
            });
        },
        selectCurrencyType() {
            this.form.currency_type_id =
                this.form.currency_type_id === "PEN" ? "USD" : "PEN";
            this.changeCurrencyType();
        },
        async changeCurrencyType() {
            // console.log(this.form.currency_type_id)
            this.currency_type = await _.find(this.currency_types, {
                id: this.form.currency_type_id
            });
            let items = [];
            this.form.items.forEach(row => {
                items.push(
                    calculateRowItem(
                        row,
                        this.form.currency_type_id,
                        this.form.exchange_rate_sale,
                        this.percentage_igv
                    )
                );
                return {
                    ...newRow,
                    unit_type_id: row.unit_type_id,
                };
            });
            this.form.items = items;
            this.calculateTotal();

            await this.setFormPosLocalStorage();
        },
        openFullWindow() {
            location.href = `/${this.resource}/pos_full`;
        },
        back() {
            this.all_items = [];
            this.place = "cat";
            this.loading = false;
        },
        async setView(view) {
            this.place = view;

            if (view == "cat3") {
                this.category_selected = "";
                await this.getRecords();
                this.$refs.table_items.reset();
            }
        },
        nameSets(id) {
            let row = this.items.find(x => x.item_id == id);
            if (row) {
                if (row.sets.length > 0) {
                    return row.sets.join("-");
                } else {
                    return "";
                }
            }
        },
        listReverse(items) {
            console.log(items);
            console.log(_.reverse(items));
            return _.reverse(items);
        },
        DescriptionLength(item) {
            if (item.description === undefined) return 0;
            if (item.description == null) return 0;
            return item.description.length;
        }
    }
};
</script>
