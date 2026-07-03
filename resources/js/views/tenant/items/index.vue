<template>
    <div class="items">
        <div class="page-header pe-0">
            <h2>
                <a :href="itemUrl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        style="margin-top: -5px;"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-category-2"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 4h6v6h-6z" />
                        <path d="M4 14h6v6h-6z" />
                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>{{ titleTopBar }}</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <template v-if="typeUser === 'admin'">
                    <div class="btn-group flex-wrap dropdown">
                        <!-- <button
                            aria-expanded="false"
                            class="btn btn-custom btn-sm mt-2 me-2 dropdown-toggle"
                            @click="showDialogTagsExports = true"
                            type="button"
                        >
                            <i class="fa fa-download"></i> Etiquetas
                            <span class="caret"></span>
                        </button> -->
                        <button
                            aria-expanded="false"
                            class="btn btn-custom btn-sm mt-2 me-2 dropdown-toggle"
                            data-bs-toggle="dropdown" 
                            type="button"
                        >
                            <i class="fa fa-download"></i> Exportar
                            <span class="caret"></span>
                        </button>
                        <div
                            class="dropdown-menu"
                            role="menu"
                            style="
                                position: absolute;
                                will-change: transform;
                                top: 0px;
                                left: 0px;
                                transform: translate3d(0px, 42px, 0px);
                            "
                            x-placement="bottom-start"
                        >
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickExport()"
                                >Productos</a
                            >
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickExportWp()"
                                >Woocommerce</a
                            >
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click="showDialogTagsExports = true"
                                >Etiquetas Personalizadas PDF</a
                            >
                            <template v-if="config.show_extra_info_to_item">
                                <a
                                    class="dropdown-item text-1"
                                    href="#"
                                    @click.prevent="clickExportExtra()"
                                >
                                    Atributos Extra
                                </a>
                            </template>
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickExportBartender()"
                                >TXT para impresoras tiqueteras</a
                            >
                        </div>
                    </div>
                    <div class="btn-group flex-wrap dropdown">
                        <button
                            aria-expanded="false"
                            class="btn btn-custom btn-sm mt-2 me-2 dropdown-toggle"
                            data-bs-toggle="dropdown" 
                            type="button"
                        >
                            <i class="fa fa-upload"></i> Importar
                            <span class="caret"></span>
                        </button>
                        <div
                            class="dropdown-menu"
                            role="menu"
                            style="
                                position: absolute;
                                will-change: transform;
                                top: 0px;
                                left: 0px;
                                transform: translate3d(0px, 42px, 0px);
                            "
                            x-placement="bottom-start"
                        >
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickImport()"
                                >Productos</a
                            >
                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickImportListPrice()"
                                >L. Precios</a
                            >
                            <template v-if="config.show_extra_info_to_item">
                                <a
                                    class="dropdown-item text-1"
                                    href="#"
                                    @click.prevent="
                                        clickImportExtraWithExtraInfo()
                                    "
                                    >L. Atributos</a
                                >
                            </template>

                            <a
                                class="dropdown-item text-1"
                                href="#"
                                @click.prevent="clickImportUpdatePrice()"
                                >Actualizar precios</a
                            >
                        </div>
                    </div>
                </template>
                <button
                    v-if="can_add_new_product"
                    class="btn btn-custom btn-sm mt-2 me-2"
                    type="button"
                    @click.prevent="clickCreate()"
                >
                    <i class="fa fa-plus-circle"></i> Nuevo
                </button>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">{{ title }}</h3>
            </div> -->
            <div class="data-table-visible-columns">                
                <el-dropdown v-if="selected.length > 0">
                  <el-button aria-expanded="false"
                    class="dropdown-toggle me-2"
                    data-toggle="dropdown"
                    type="button">
                    Acciones masivas
                    <i class="el-icon-arrow-down el-icon--right"></i>
                  </el-button>
                  <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item @click.native="clickDeleteSelected">Eliminar</el-dropdown-item>
                    <el-dropdown-item @click.native="duplicateSelected">Duplicar</el-dropdown-item>
                
                    <!-- Solo si TODOS los seleccionados están habilitados -->
                    <el-dropdown-item
                      v-if="showDisable"
                      @click.native="clickDisableSelected">
                      Inhabilitar
                    </el-dropdown-item>
                
                    <!-- Solo si TODOS los seleccionados están inhabilitados -->
                    <el-dropdown-item
                      v-if="showEnable"
                      @click.native="clickEnableSelected">
                      Habilitar
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </el-dropdown>
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i
                            class="el-icon-arrow-down el-icon--right"
                        ></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item v-for="col in orderedColumns" :key="col.key">
                            <el-checkbox v-model="columns[col.key].visible" @change="saveColumnVisibility">{{ col.title }}</el-checkbox>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table ref="DataTable" :productType="type" :resource="resource" :sort-field="sortField" :sort-direction="sortDirection" :showProductFilter="type !== 'ZZ'" @sort-change="handleSortChange" @records-changed="handleRecordsChanged">
                    <tr slot="heading" width="100%" slot-scope="{ sort }">
                        <th class="text-center" style="width: 34px;">
                            <el-checkbox :value="allSelectedInView" @change="toggleSelectAll"></el-checkbox>
                        </th>
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'id'" :key="col.key" class="text-end" style="max-width: 83px;">ID</th>
                            <th v-if="col.visible && col.key === 'internal_id'" :key="col.key" class="text-end">Cód. Interno</th>
                            <th v-if="col.visible && col.key === 'unit_type'" :key="col.key">Unidad</th>
                            <th v-if="col.visible && col.key === 'image'" :key="col.key">Imagen</th>
                            <th v-if="col.visible && col.key === 'name'" :key="col.key">
                                <a href="#" @click.prevent="sort('description')" style="color: inherit; text-decoration: none;">
                                    Nombre
                                    <i class="fas" :class="{ 'fa-sort-up': sortField === 'description' && sortDirection === 'asc', 'fa-sort-down': sortField === 'description' && sortDirection === 'desc', 'fa-sort': sortField !== 'description' || (sortField === 'description' && sortDirection === 'default') }"></i>
                                </a>
                            </th>
                            <th v-if="col.visible && col.key === 'description'" :key="col.key">Descripción</th>
                            <th v-if="col.visible && col.key === 'model'" :key="col.key">Modelo</th>
                            <th v-if="col.visible && col.key === 'brand'" :key="col.key">Marca</th>
                            <th v-if="col.visible && col.key === 'item_code'" :key="col.key" class="text-end">Cód. SUNAT</th>
                            <th v-if="col.visible && col.key === 'sanitary'" :key="col.key" class="text-end">R.S.</th>
                            <th v-if="col.visible && col.key === 'cod_digemid'" :key="col.key">DIGEMID</th>
                            <th v-if="col.visible && col.key === 'history' && typeUser == 'admin'" :key="col.key" class="text-center">Historial</th>
                            <th v-if="col.visible && col.key === 'stock'" :key="col.key" class="text-start">Stock</th>
                            <th v-if="col.visible && col.key === 'extra_data'" :key="col.key" class="text-center">Stock por datos extra</th>
                            <th v-if="col.visible && col.key === 'sale_unit_price'" :key="col.key" class="text-end">P.Unitario (Venta)</th>
                            <th v-if="col.visible && col.key === 'purchase_unit_price' && typeUser != 'seller'" :key="col.key" class="text-end">P.Unitario (Compra)</th>
                            <th v-if="col.visible && col.key === 'real_unit_price'" :key="col.key" class="text-end">P. venta</th>
                            <th v-if="col.visible && col.key === 'has_igv'" :key="col.key" class="text-start">Tiene Igv (Venta)</th>
                            <th v-if="col.visible && col.key === 'purchase_has_igv_description'" :key="col.key" class="text-start">Tiene Igv (Compra)</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end"></th>
                        </template>
                    </tr>

                    <tr></tr>
                    <tr valign="middle"
                        slot-scope="{ index, row }"
                        :class="{ disable_color: !row.active }"
                    >
                        <td>
                            <el-checkbox :value="selected.includes(row.id)" @change="handleSelectionChange(row)"></el-checkbox>
                        </td>
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'id'" :key="col.key" class="text-end">{{ row.id }}</td>
                            <td v-if="col.visible && col.key === 'internal_id'" :key="col.key" class="text-end">{{ row.internal_id }}</td>
                            <td v-if="col.visible && col.key === 'unit_type'" :key="col.key">{{ row.unit_type_id }}</td>
                            <td v-if="col.visible && col.key === 'image'" :key="col.key"><img :src="row.image_url_small" style="object-fit: contain;" alt width="32px" height="32px" /></td>
                            <td v-if="col.visible && col.key === 'name'" :key="col.key">{{ row.description }}</td>
                            <td v-if="col.visible && col.key === 'description'" :key="col.key"><div class="limit-4-lines">{{ stripHtml(row.name) }}</div></td>
                            <td v-if="col.visible && col.key === 'model'" :key="col.key">{{ row.model }}</td>
                            <td v-if="col.visible && col.key === 'brand'" :key="col.key">{{ row.brand }}</td>
                            <td v-if="col.visible && col.key === 'item_code'" :key="col.key" class="text-end">{{ row.item_code }}</td>
                            <td v-if="col.visible && col.key === 'sanitary'" :key="col.key">{{ row.sanitary }}</td>
                            <td v-if="col.visible && col.key === 'cod_digemid'" :key="col.key" class="text-end">{{ row.cod_digemid }}</td>
                            <td v-if="col.visible && col.key === 'history' && typeUser == 'admin'" :key="col.key" class="text-center">
                                <button class="btn waves-effect waves-light btn-xs btn-primary" type="button" @click.prevent="clickHistory(row.id)"><i class="fa fa-history"></i></button>
                            </td>
                            <td v-if="col.visible && col.key === 'stock'" :key="col.key">
                                <div v-if="config.product_only_location == true" :class="{ 'text-danger': row.stock < row.stock_min }">
                                    {{ formatStock(row.stock, row.unit_type_id) }} <!-- <small class="text-muted ms-1">{{ unitSymbol(row.unit_type_id) }}</small> -->
                                </div>
                                <div v-else>
                                    <template v-if="typeUser == 'seller' && row.unit_type_id != 'ZZ'">
                                        <span :class="{ 'text-danger': row.stock < row.stock_min }">
                                            {{ formatStock(row.stock, row.unit_type_id) }}<!-- <small class="text-muted ms-1">{{ unitSymbol(row.unit_type_id) }}</small> -->
                                        </span>
                                    </template>
                                    <template v-else-if="typeUser != 'seller' && row.unit_type_id != 'ZZ'">
                                        <button class="btn waves-effect waves-light btn-xs btn-info" type="button" @click.prevent="clickWarehouseDetail(row.warehouses, row.item_unit_types)"><i class="fa fa-search"></i></button>
                                    </template>
                                </div>
                            </td>
                            <td v-if="col.visible && col.key === 'extra_data'" :key="col.key" class="text-center">
                                <template v-if="config.show_extra_info_to_item && (row.stock_by_extra.total !== null || row.stock_by_extra.colors !== null || row.stock_by_extra.CatItemSize !== null || row.stock_by_extra.CatItemStatus !== null || row.stock_by_extra.CatItemUnitBusiness !== null || row.stock_by_extra.CatItemMoldCavity !== null || row.stock_by_extra.CatItemPackageMeasurement !== null || row.stock_by_extra.CatItemUnitsPerPackage !== null || row.stock_by_extra.CatItemMoldProperty !== null || row.stock_by_extra.CatItemProductFamily !== null)">
                                    <button class="btn waves-effect waves-light btn-xs btn-primary" type="button" @click.prevent="clickStockItems(row)"><i class="fa fa-database"></i></button>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'sale_unit_price'" :key="col.key" class="text-end">{{ row.sale_unit_price }}</td>
                            <td v-if="col.visible && col.key === 'purchase_unit_price' && typeUser != 'seller'" :key="col.key" class="text-end">{{ row.purchase_unit_price }}</td>
                            <td v-if="col.visible && col.key === 'real_unit_price'" :key="col.key" class="text-end">{{ row.sale_unit_price_with_igv }}</td>
                            <td v-if="col.visible && col.key === 'has_igv'" :key="col.key" class="text-start">{{ row.has_igv_description }}</td>
                            <td v-if="col.visible && col.key === 'purchase_has_igv_description'" :key="col.key" class="text-start">{{ row.purchase_has_igv_description }}</td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                            <el-dropdown trigger="click">
                                <button
                                    id="dropdownMenuButton"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    class="btn btn-default btn-sm btn-dropdown-toggle"
                                    type="button"
                                >
                                    <i class="fas fa-ellipsis-v"></i>
                                    <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                </button>
                                <el-dropdown-menu slot="dropdown">
                                  <template v-if="typeUser === 'admin'">                                    
                                    <el-dropdown-item
                                      @click.native.prevent="clickCreate(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                      Editar
                                    </el-dropdown-item>
                                
                                    <el-dropdown-item
                                      @click.native.prevent="clickPrintBarcode(row)"
                                      class="d-flex align-items-center justify-content-between"
                                    >
                                      <span class="d-flex align-items-center me-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tags me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8v4.172a2 2 0 0 0 .586 1.414l5.71 5.71a2.41 2.41 0 0 0 3.408 0l3.592 -3.592a2.41 2.41 0 0 0 0 -3.408l-5.71 -5.71a2 2 0 0 0 -1.414 -.586h-4.172a2 2 0 0 0 -2 2z" /><path d="M18 19l1.592 -1.592a4.82 4.82 0 0 0 0 -6.816l-4.592 -4.592" /><path d="M7 10h-.01" /></svg>
                                        Etiquetas
                                      </span>
                                
                                      <el-tooltip
                                        effect="dark"
                                        content="Generar código de barras"
                                        placement="top-start"
                                      >
                                        <button
                                          class="position-relative btn barcode d-flex align-items-center justify-content-center"
                                          @click.stop.prevent="clickBarcode(row)"
                                        >
                                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                                            <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                            <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                                            <path d="M5 11h1v2h-1z" />
                                            <path d="M10 11l0 2" />
                                            <path d="M14 11h1v2h-1z" />
                                            <path d="M19 11l0 2" />
                                          </svg>
                                        </button>
                                      </el-tooltip>
                                    </el-dropdown-item>
                                
                                    <el-dropdown-item
                                      @click.native.prevent="duplicate(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                      Duplicar
                                    </el-dropdown-item>
                                
                                    <el-dropdown-item divided />
                                
                                    <el-dropdown-item
                                      v-if="row.active"
                                      @click.native.prevent="clickDisable(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-ban me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M5.7 5.7l12.6 12.6" /></svg>
                                      Inhabilitar
                                    </el-dropdown-item>
                                
                                    <el-dropdown-item
                                      v-else
                                      @click.native.prevent="clickEnable(row.id)"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>
                                      Habilitar
                                    </el-dropdown-item>
                                
                                    <el-dropdown-item
                                      @click.native.prevent="clickDelete(row.id)"
                                      class="text-danger option-delete"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                      Eliminar
                                    </el-dropdown-item>
                                  </template>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </td>
                        </template>
                    </tr>
                </data-table>
            </div>

            <items-form
                :recordId="recordId"
                :showDialog.sync="showDialog"
                :type="type"
            ></items-form>

            <items-import :showDialog.sync="showImportDialog"></items-import>
            <items-export :showDialog.sync="showExportDialog"></items-export>
            <items-export-wp
                :showDialog.sync="showExportWpDialog"
            ></items-export-wp>

            <items-export-barcode
                :showDialog.sync="showExportBarcodeDialog"
            ></items-export-barcode>

            <items-export-extra
                :showDialog.sync="showExportExtraDialog"
            ></items-export-extra>
            <warehouses-detail
                :item_unit_types="item_unit_types"
                :price_labels="price_labels"
                :config="config"
                :showDialog.sync="showWarehousesDetail"
                :warehouses="warehousesDetail"
            >
            </warehouses-detail>

            <items-import-list-price
                :showDialog.sync="showImportListPriceDialog"
            ></items-import-list-price>

            <items-import-extra-info
                :showDialog.sync="showImportExtraWithExtraInfo"
            ></items-import-extra-info>

            <items-import-update-price
                :showDialog.sync="showImporUpdatePrice"
            ></items-import-update-price>

            <items-import-tags
                :showDialog.sync="showDialogTagsExports"
            ></items-import-tags>

            <!--
            : false,
            show_extra_info_to_item
            -->
            <tenant-item-aditional-info-modal
                :item="recordItem"
                :showDialog.sync="showDialogItemStock"
            ></tenant-item-aditional-info-modal>
            <items-history
                :recordId="recordId"
                :showDialog.sync="showDialogHistory"
            >
            </items-history>
            <items-export-bartender
                :configuration="configuration"
                :showDialog.sync="showExportBartenderDialog"
            ></items-export-bartender>
        </div>
    </div>
</template>
<style>
.dropdown-menu.show {
    max-height: 130px;
}
.btn-icon{
    border-radius: 8px;
    padding: 3px !important;
    line-height: normal;
}
</style>
<script>
import ItemsForm from "./form.vue";
import WarehousesDetail from "./partials/warehouses.vue";
import ItemsImport from "./import.vue";
import ItemsImportListPrice from "./partials/import_list_price.vue";
import ItemsImportExtraInfo from "./partials/import_list_extra_info.vue";
// resources/js/views/tenant/items/partials/import_list_extra_info.vue
import ItemsExport from "./partials/export.vue";
import ItemsExportWp from "./partials/export_wp.vue";
import ItemsExportBarcode from "./partials/export_barcode.vue";
import ItemsExportExtra from "./partials/export_extra.vue";
import DataTable from "../../../components/DataTable.vue";
import { deletable } from "../../../mixins/deletable";
import ItemsHistory from "@viewsModuleItem/items/history.vue";
import { mapActions, mapState } from "vuex";
import ItemsImportUpdatePrice from "./partials/update_prices.vue";
import ItemsImportTags from "./partials/export_tag.vue";
import ItemsExportBartender from "./partials/export_bartender.vue";

export default {
    props: ["configuration", "typeUser", "type"],
    mixins: [deletable],
    components: {
        ItemsForm,
        ItemsImport,
        ItemsExport,
        ItemsExportWp,
        ItemsExportBarcode,
        ItemsExportExtra,
        DataTable,
        WarehousesDetail,
        ItemsImportListPrice,
        ItemsImportExtraInfo,
        ItemsHistory,
        ItemsImportTags,
        ItemsImportUpdatePrice,
        ItemsExportBartender
    },
    data() {
        return {
            selected: [],
            selectedMeta: {},
            visibleRows: [],
            can_add_new_product: false,
            showDialog: false,
            showImportDialog: false,
            showExportDialog: false,
            showExportWpDialog: false,
            showExportBarcodeDialog: false,
            showExportBartenderDialog: false,
            showExportExtraDialog: false,
            showDialogTagsExports: false,
            showImportListPriceDialog: false,
            showImportExtraWithExtraInfo: false,
            showImporUpdatePrice: false,
            showWarehousesDetail: false,
            resource: "items",
            recordId: null,
            recordItem: {},
            warehousesDetail: [],
            price_labels: {},
            columns: {
                id:                          { title: "ID",                                                   visible: true,  order: 0  },
                internal_id:                 { title: "Cód. Interno",                                        visible: true,  order: 1  },
                unit_type:                   { title: "Unidad",                                              visible: true,  order: 2  },
                image:                       { title: "Imagen",                                              visible: true,  order: 3  },
                name:                        { title: "Nombre",                                              visible: true,  order: 4  },
                description:                 { title: "Descripción",                                         visible: false, order: 5  },
                model:                       { title: "Modelo",                                              visible: false, order: 6  },
                brand:                       { title: "Marca",                                               visible: false, order: 7  },
                item_code:                   { title: "Cód. SUNAT",                                          visible: false, order: 8  },
                sanitary:                    { title: "N° Sanitario",                                        visible: false, order: 9  },
                cod_digemid:                 { title: "DIGEMID",                                             visible: false, order: 10 },
                extra_data:                  { title: "Stock Por datos extra",                               visible: false, order: 11 },
                history:                     { title: "Historial",                                           visible: true,  order: 12 },
                stock:                       { title: "Stock",                                               visible: true,  order: 13 },
                sale_unit_price:             { title: "P.Unitario (Venta)",                                  visible: true,  order: 14 },
                purchase_unit_price:         { title: "P.Unitario (Compra)",                                 visible: false, order: 15 },
                real_unit_price:             { title: "Mostrar el precio de venta total (con el cálculo IGV)", visible: false, order: 16 },
                has_igv:                     { title: "Tiene Igv (Venta)",                                   visible: true,  order: 17 },
                purchase_has_igv_description:{ title: "Tiene Igv (Compra)",                                  visible: false, order: 18 },
                actions:                     { title: "Acciones",                                            visible: true,  order: 19 },
            },
            item_unit_types: [],
            titleTopBar: "",
            title: "",
            showDialogHistory: false,
            showDialogItemStock: false,
            sortField: localStorage.getItem('itemSortField') || 'id',
            sortDirection: localStorage.getItem('itemSortDirection') || 'desc',
        };
    },
    created() {
        this.loadColumnVisibility();
        this.$store.commit("setConfiguration", this.configuration);
        this.loadConfiguration();

        if (this.config.is_pharmacy !== true) {
            delete this.columns.sanitary;
            delete this.columns.cod_digemid;
        }
        if (this.config.show_extra_info_to_item !== true) {
            delete this.columns.extra_data;
        }
        if (this.type === "ZZ") {
            this.titleTopBar = "Servicios";
            this.title = "Listado de servicios";
        } else {
            this.titleTopBar = "Productos";
            this.title = "Listado de productos";
        }
        this.$http.get(`/configurations/record`).then(response => {
            this.$store.commit("setConfiguration", response.data.data);
            //this.config = response.data.data;
        });
        this.canCreateProduct();
        this.getItems();

        this.filterDisabled = localStorage.getItem('filterDisabled') || 'all'

        this.$eventHub.$on("establishmentChanged", () => {
            this.loadColumnVisibility();
            this.$store.commit("setConfiguration", this.configuration);
            this.loadConfiguration();

            if (this.config.is_pharmacy !== true) {
                delete this.columns.sanitary;
                delete this.columns.cod_digemid;
            }
            if (this.config.show_extra_info_to_item !== true) {
                delete this.columns.extra_data;
            }
            if (this.type === "ZZ") {
                this.titleTopBar = "Servicios";
                this.title = "Listado de servicios";
            } else {
                this.titleTopBar = "Productos";
                this.title = "Listado de productos";
            }
            this.$http.get(`/configurations/record`).then(response => {
                this.$store.commit("setConfiguration", response.data.data);
                //this.config = response.data.data;
            });
            this.canCreateProduct();
            this.getItems();
            this.reloadTable();

            
            this.filterDisabled = localStorage.getItem('filterDisabled') || 'all'
        });

    },
    computed: {
        ...mapState([
            "config",
            "colors",
            "CatItemSize",
            "CatItemMoldCavity",
            "CatItemMoldProperty",
            "CatItemUnitBusiness",
            "CatItemStatus",
            "CatItemPackageMeasurement",
            "CatItemProductFamily",
            "CatItemUnitsPerPackage"
        ]),
        columnsComputed: function() {
            return this.columns;
        },
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order);
        },
        itemUrl() {
            return this.type === "ZZ" ? "/services" : "/items";
        },
        selectedEnabledCount() {
          return this.selected.reduce((acc, id) => {
            const meta = this.selectedMeta[id];
            return acc + (meta && meta.active ? 1 : 0);
          }, 0);
        },
        selectedDisabledCount() {
          return this.selected.reduce((acc, id) => {
            const meta = this.selectedMeta[id];
            // active === false => inhabilitado
            return acc + (meta && meta.active === false ? 1 : 0);
          }, 0);
        },
        showDisable() {
          // Mostrar "Inhabilitar" solo si TODOS los seleccionados están habilitados
          return this.selected.length > 0 &&
                 this.selectedEnabledCount === this.selected.length &&
                 this.selectedDisabledCount === 0;
        },
        showEnable() {
          // Mostrar "Habilitar" solo si TODOS los seleccionados están inhabilitados
          return this.selected.length > 0 &&
                 this.selectedDisabledCount === this.selected.length &&
                 this.selectedEnabledCount === 0;
        },

        selectedInViewCount() {
            if (!this.visibleRows.length) return 0;
            const visibleIds = new Set(this.visibleRows.map(r => r.id));
            return this.selected.reduce((acc, id) => acc + (visibleIds.has(id) ? 1 : 0), 0);
        },
        allSelectedInView() {
            return this.visibleRows.length > 0 && this.selectedInViewCount === this.visibleRows.length;
        },
        isIndeterminate() {
            return this.selectedInViewCount > 0 && this.selectedInViewCount < this.visibleRows.length;
        },
    },
    methods: {
        handleRecordsChanged(records) {
            this.visibleRows = Array.isArray(records) ? records : [];
        },
        stripHtml(html) {
            if (!html) return html
            return html.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
        },
        isDecimalUnit(unitTypeId) {
            // Unidades de peso, volumen, longitud, área, tiempo: admiten decimales
            const decimals = ['KGM','GRM','MGM','TNE','LBR','LTR','MLT','GLL','MTR','CMT','KMT','MTK','MTQ','HUR','DAY','MIN'];
            return decimals.includes(unitTypeId);
        },
        unitSymbol(unitTypeId) {
            const map = {
                NIU: 'und', BX: 'caja', BO: 'bot', BG: 'bls', DZN: 'doc',
                PK: 'paq', SET: 'jgo', PR: 'par', '4B': 'rollo', CEN: 'cien', MLR: 'mll',
                KGM: 'kg', GRM: 'g', MGM: 'mg', TNE: 't', LBR: 'lb',
                LTR: 'L', MLT: 'mL', GLL: 'gal',
                MTR: 'm', CMT: 'cm', KMT: 'km', MTK: 'm²', MTQ: 'm³',
                HUR: 'h', DAY: 'día', MIN: 'min',
                ZZ: '',
            };
            return map[unitTypeId] !== undefined ? map[unitTypeId] : (unitTypeId || '').toLowerCase();
        },
        formatStock(stock, unitTypeId) {
            const value = Number(stock) || 0;
            let str;
            if (this.isDecimalUnit(unitTypeId)) {
                // Hasta 3 decimales, recortando ceros a la derecha
                str = value.toFixed(3).replace(/\.?0+$/, '');
            } else {
                // Unidad discreta: entero
                str = Math.round(value).toString();
            }
            // Separador de miles con coma (estándar Perú/SUNAT)
            const parts = str.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        },
        toggleSelectAll(checked) {
            if (!this.visibleRows.length) return;

            if (checked) {
                this.visibleRows.forEach(row => {
                    if (!this.selected.includes(row.id)) {
                        this.selected.push(row.id);
                    }
                    this.$set(this.selectedMeta, row.id, { active: !!row.active });
                });
                return;
            }

            this.visibleRows.forEach(row => {
                const idx = this.selected.indexOf(row.id);
                if (idx > -1) this.selected.splice(idx, 1);
                if (this.selectedMeta[row.id] !== undefined) this.$delete(this.selectedMeta, row.id);
            });
        },
        reloadTable() {
          localStorage.setItem('filterDisabled', this.filterDisabled)
          this.$refs.DataTable.showDisabled = this.filterDisabled;
          this.$refs.DataTable.getRecords();
        },
        clickDeleteSelected() {
            return new Promise((resolve) => {
                this.$confirm('¿Desea eliminar los registro seleccionado?', 'Eliminar', {
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.post(`${this.resource}/destroyMassive`, {
                        selected: this.selected
                    })
                        .then(res => {
                            if(res.data.success) {
                                this.$message.success(res.data.message)
                                this.selected = []
                                this.selectedMeta = {}
                                this.$eventHub.$emit("reloadData")
                                resolve()
                            }else{
                                this.$message.error(res.data.message)
                                resolve()
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar eliminar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },
        clickDisableSelected() {
            return new Promise((resolve) => {
                this.$confirm('¿Desea inhabilitar los registros seleccionados?', 'Inhabilitar', {
                    confirmButtonText: 'Inhabilitar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.post(`${this.resource}/disableMassive`, {
                        selected: this.selected
                    })
                        .then(res => {
                            if(res.data.success) {
                                this.$message.success(res.data.message)
                                this.selected = []
                                this.selectedMeta = {}
                                this.$eventHub.$emit("reloadData")
                                resolve()
                            }else{
                                this.$message.error(res.data.message)
                                resolve()
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar inhabilitar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },
        duplicateSelected() {
            this.selected.forEach(id => this.duplicate(id));
            this.selected = []
            this.selectedMeta = {}
            this.$eventHub.$emit("reloadData")
        },
        clickEnableSelected() {
            return new Promise((resolve) => {
                this.$confirm('¿Desea habilitar los registros seleccionados?', 'Habilitar', {
                    confirmButtonText: 'Habilitar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.post(`${this.resource}/enableMassive`, {
                        selected: this.selected
                    })
                        .then(res => {
                            if(res.data.success) {
                                this.$message.success(res.data.message)
                                this.selected = []
                                this.selectedMeta = {}
                                this.$eventHub.$emit("reloadData")
                                resolve()
                            }else{
                                this.$message.error(res.data.message)
                                resolve()
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar habilitar');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },
        handleSortChange(sort) {
            if (this.sortField === sort.field && this.sortDirection === 'desc' && sort.field === 'description') {
                this.sortField = 'id';
                this.sortDirection = 'desc';
            } else {
                this.sortField = sort.field;
                this.sortDirection = sort.direction;
            }

            localStorage.setItem('itemSortField', this.sortField);
            localStorage.setItem('itemSortDirection', this.sortDirection);
        },
        saveColumnVisibility() {
            const columns = {};
            Object.keys(this.columns).forEach(key => {
                columns[key] = { title: this.columns[key].title, visible: this.columns[key].visible, order: this.columns[key].order };
            });
            this.$http.post('/column-visibility/items_index', { columns }).catch(() => {});
        },
        loadColumnVisibility() {
            this.$http.get('/column-visibility/items_index').then(response => {
                if (response.data.success && response.data.data) {
                    const data = response.data.data;
                    Object.keys(data).forEach(key => {
                        if (this.columns[key] !== undefined) {
                            this.columns[key].visible = data[key].visible;
                            if (data[key].order !== undefined) {
                                this.columns[key].order = data[key].order;
                            }
                        }
                    });
                }
            }).catch(() => {});
        },
        ...mapActions(["loadConfiguration"]),
        clickHistory(recordId) {
            this.recordId = recordId;
            this.showDialogHistory = true;
        },
        clickStockItems(row) {
            this.recordItem = row;
            this.showDialogItemStock = true;
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
        duplicate(id) {
            this.$http
                .post(`${this.resource}/duplicate`, { id })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se guardaron los cambios correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch(error => {});
            this.$eventHub.$emit("reloadData");
        },
        clickWarehouseDetail(warehouses, item_unit_types) {
            this.warehousesDetail = warehouses;
            this.item_unit_types = item_unit_types;
            this.showWarehousesDetail = true;
        },
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickImport() {
            this.showImportDialog = true;
        },
        clickExport() {
            this.showExportDialog = true;
        },
        clickExportWp() {
            this.showExportWpDialog = true;
        },
        clickExportBarcode() {
            this.showExportBarcodeDialog = true;
        },
        clickExportBartender() {
            this.showExportBartenderDialog = true;
        },
        clickExportExtra() {
            this.showExportExtraDialog = true;
        },
        clickImportListPrice() {
            this.showImportListPriceDialog = true;
        },
        clickImportExtraWithExtraInfo() {
            this.showImportExtraWithExtraInfo = true;
        },
        clickImportUpdatePrice() {
            this.showImporUpdatePrice = true;
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickDisable(id) {
            this.disable(`/${this.resource}/disable/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickEnable(id) {
            this.enable(`/${this.resource}/enable/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickBarcode(row) {
            if (!row.barcode) {
                return this.$message.error(
                    "Para generar el código de barras debe registrar el código de barras."
                );
            }

            window.open(`/${this.resource}/barcode/${row.id}`);
        },
        clickPrintBarcode(row) {
            if (!row.barcode) {
                return this.$message.error(
                    "Para generar el código de barras debe registrar el código de barras."
                );
            }

            window.open(`/${this.resource}/export/barcode/print?id=${row.id}`);
        },
        clickPrintBarcodeX(row, x) {
            if (!row.barcode) {
                return this.$message.error(
                    "Para generar el código de barras debe registrar el código de barras."
                );
            }

            window.open(
                `/${this.resource}/export/barcode/print_x?format=${x}&id=${
                    row.id
                }`
            );
        },
        getItems() {
            this.$http.get(`/${this.resource}/item/tables`).then(response => {
                let data = response.data;
                
                this.price_labels = data.price_labels;

                if (this.config.show_extra_info_to_item) {
                    this.$store.commit("setColors", data.colors);
                    this.$store.commit("setCatItemSize", data.CatItemSize);
                    this.$store.commit(
                        "setCatItemMoldCavity",
                        data.CatItemMoldCavity
                    );
                    this.$store.commit(
                        "setCatItemMoldProperty",
                        data.CatItemMoldProperty
                    );
                    this.$store.commit(
                        "setCatItemUnitBusiness",
                        data.CatItemUnitBusiness
                    );
                    this.$store.commit("setCatItemStatus", data.CatItemStatus);
                    this.$store.commit(
                        "setCatItemPackageMeasurement",
                        data.CatItemPackageMeasurement
                    );
                    this.$store.commit(
                        "setCatItemProductFamily",
                        data.CatItemProductFamily
                    );
                    this.$store.commit(
                        "setCatItemUnitsPerPackage",
                        data.CatItemUnitsPerPackage
                    );
                }
            });
        },
        handleSelectionChange(row) {
          const id = row.id;
          const idx = this.selected.indexOf(id);

          if (idx > -1) {
            this.selected.splice(idx, 1);
            this.$delete(this.selectedMeta, id);
          } else {
            this.selected.push(id);
            this.$set(this.selectedMeta, id, { active: !!row.active });
          }
        //   console.log('selected:', this.selected, 'meta:', this.selectedMeta);
        },
    }
};
</script>
