<template>
    <div class="sale_notes">
        <div class="page-header pr-0">
            <h2>
                <a href="/sale-notes">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        style="margin-top: -5px;"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-file-text"
                    >
                        <path
                            d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                        ></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Notas de Venta</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    href="#"
                    @click.prevent="clickCreate()"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
                <a
                    href="#"
                    @click.prevent="onOpenModalGenerateCPE"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    >Generar comprobante desde múltiples Notas</a
                >
                <a
                    href="#"
                    v-if="config.send_data_to_other_server === true"
                    @click.prevent="onOpenModalMigrateNv"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    >Migrar Datos</a
                >
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="secondary">
                        Mostrar columnas<i
                            class="el-icon-arrow-down el-icon--right"
                        ></i>
                    </el-button>
                     <el-dropdown-menu slot="dropdown" style="min-width: 220px;">
                        <div style="max-height: 520px; overflow-y: auto;">
                            <div class="px-3 py-2 bg-light border-bottom" v-if=" customFieldColumns.length > 0 ">
                                <strong>Campos personalizados</strong>
                            </div>
                            <el-dropdown-item
                                v-for="field in customFieldColumns"
                                :key="`custom-field-${field.id}`"

                            >
                                <el-checkbox
                                    @change="updateCustomFieldColumns()"
                                    v-model="field.visible"
                                    >{{ field.name }}</el-checkbox
                                >
                            </el-dropdown-item>
                            <el-dropdown-item divided v-if=" customFieldColumns.length > 0 "></el-dropdown-item>
                            <el-dropdown-item disabled>
                                <strong>Seleccionar columnas</strong>
                            </el-dropdown-item>
                            <el-dropdown-item v-for="col in tenantSelectableColumns" :key="col.key">
                                <el-checkbox @change="getColumnsToShow(1)" v-model="columns[col.key].visible">{{ col.title }}</el-checkbox>
                            </el-dropdown-item>
                        </div>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <template v-for="col in orderedColumns">
                            <th v-if="col.visible && col.key === 'seller_name'" :key="col.key" class="text-end">Vendedor</th>
                            <th v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-center">Fecha Emisión</th>
                            <th v-if="col.visible && col.key === 'date_payment'" :key="col.key" class="text-center">Fecha de pago</th>
                            <th v-if="col.visible && col.key === 'customer'" :key="col.key">Cliente</th>
                            <th v-if="col.visible && col.key === 'full_number'" :key="col.key">Nota de Venta</th>
                            <th v-if="col.visible && col.key === 'state_type'" :key="col.key">Estado</th>
                            <th v-if="col.visible && col.key === 'exchange_rate_sale'" :key="col.key" class="text-end">T.C.</th>
                            <th v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">Moneda</th>
                            <th v-if="col.visible && col.key === 'due_date'" :key="col.key" class="text-end">F. Vencimiento</th>
                            <th v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end">T.Exportación</th>
                            <th v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end">T.Gratuito</th>
                            <th v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end">T.Inafecta</th>
                            <th v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end">T.Exonerado</th>
                            <th v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end">T.Gravado</th>
                            <th v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end">T.Igv</th>
                            <th v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end">Total</th>
                            <th v-if="col.visible && col.key === 'total_paid'" :key="col.key" class="text-center">Pagado</th>
                            <th v-if="col.visible && col.key === 'total_pending_paid'" :key="col.key" class="text-center">Por pagar</th>
                            <th v-if="col.visible && col.key === 'documents'" :key="col.key" class="text-center">Comprobantes</th>
                            <th v-if="col.visible && col.key === 'payment_status'" :key="col.key" class="text-center">Estado pago</th>
                            <th v-if="col.visible && col.key === 'purchase_order'" :key="col.key" class="text-center">Orden de compra</th>
                            <th v-if="col.visible && col.key === 'payments'" :key="col.key" class="text-center">Pagos</th>
                            <th v-if="col.visible && col.key === 'download'" :key="col.key" class="text-center">Descarga</th>
                            <!-- Campos personalizados: posición configurable vía columna virtual `personalized` (visibilidad la dicta cada field) -->
                            <template v-if="col.key === 'personalized'">
                                <template v-for="field in customFieldColumns">
                                    <th v-if="field.visible" :key="`cf-head-${field.id}`" class="text-start" style="min-width: 120px;">{{ field.name }}</th>
                                </template>
                            </template>
                            <th v-if="col.visible && col.key === 'recurrence'" :key="col.key" class="text-center">Recurrencia</th>
                            <th v-if="col.visible && col.key === 'region'" :key="col.key" class="text-start">Region</th>
                            <th v-if="col.visible && col.key === 'dispatch_status'" :key="col.key" class="text-end">Estado de despacho</th>
                            <th v-if="col.visible && col.key === 'type_period'" :key="col.key" class="text-center">Tipo Periodo</th>
                            <th v-if="col.visible && col.key === 'quantity_period'" :key="col.key" class="text-center">Cantidad Periodo</th>
                            <th v-if="col.visible && col.key === 'paid'" :key="col.key" class="text-center">Estado de Pago</th>
                            <th v-if="col.visible && col.key === 'license_plate'" :key="col.key" class="text-center">Placa</th>
                            <th v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">Acciones</th>
                        </template>
                    </tr>
                    <tr slot-scope="{ index, row }" :class="{'anulate_color': row.state_type_id === '11'}">
                        <template v-for="col in orderedColumns">
                            <td v-if="col.visible && col.key === 'seller_name'" :key="col.key" class="text-end">{{ row.seller_name }}</td>
                            <td v-if="col.visible && col.key === 'date_of_issue'" :key="col.key" class="text-center">{{ row.date_of_issue | toDate }}</td>
                            <td v-if="col.visible && col.key === 'date_payment'" :key="col.key" class="text-center">{{ row.date_of_payment | toDate }}</td>
                            <td v-if="col.visible && col.key === 'customer'" :key="col.key">{{ row.customer_name }}<br /><small v-text="row.customer_number"></small></td>
                            <td v-if="col.visible && col.key === 'full_number'" :key="col.key">{{ row.full_number }}</td>
                            <td v-if="col.visible && col.key === 'state_type'" :key="col.key">{{ row.state_type_description }}</td>
                            <td v-if="col.visible && col.key === 'exchange_rate_sale'" :key="col.key" class="text-center">{{ row.exchange_rate_sale }}</td>
                            <td v-if="col.visible && col.key === 'currency_type'" :key="col.key" class="text-center">{{ row.currency_type_id }}</td>
                            <td v-if="col.visible && col.key === 'due_date'" :key="col.key" class="text-end">{{ row.due_date | toDate }}</td>
                            <td v-if="col.visible && col.key === 'total_exportation'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exportation) }}</td>
                            <td v-if="col.visible && col.key === 'total_free'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_free) }}</td>
                            <td v-if="col.visible && col.key === 'total_unaffected'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_unaffected) }}</td>
                            <td v-if="col.visible && col.key === 'total_exonerated'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_exonerated) }}</td>
                            <td v-if="col.visible && col.key === 'total_taxed'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_taxed) }}</td>
                            <td v-if="col.visible && col.key === 'total_igv'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_igv) }}</td>
                            <td v-if="col.visible && col.key === 'total'" :key="col.key" class="text-end text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total) }}</td>
                            <td v-if="col.visible && col.key === 'total_paid'" :key="col.key" class="text-center text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_paid) }}</td>
                            <td v-if="col.visible && col.key === 'total_pending_paid'" :key="col.key" class="text-center text-nowrap">{{ row.currency_type_id === 'PEN' ? 'S/' : '$' }} {{ formatDecimal(row.total_pending_paid) }}</td>
                            <td v-if="col.visible && col.key === 'documents'" :key="col.key">
                                <label v-for="(document, i) in row.documents" :key="i" v-text="document.number_full" class="d-block"></label>
                            </td>
                            <td v-if="col.visible && col.key === 'payment_status'" :key="col.key" class="text-center">
                                <template v-if="row.state_type_id === '11'"><span class="badge text-white bg-danger">{{ row.state_type_description }}</span></template>
                                <template v-else><span class="badge text-white" :class="{ 'bg-success': row.total_canceled, 'bg-warning': !row.total_canceled }">{{ row.total_canceled ? 'Pagado' : 'Pendiente' }}</span></template>
                            </td>
                            <td v-if="col.visible && col.key === 'purchase_order'" :key="col.key">{{ row.purchase_order }}</td>
                            <td v-if="col.visible && col.key === 'payments'" :key="col.key" class="text-center">
                                <button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-primary" @click.prevent="clickPayment(row.id)"><i class="fas fa-money-bill-alt"></i></button>
                            </td>
                            <td v-if="col.visible && col.key === 'download'" :key="col.key" class="text-end">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickDownload(row.external_id)"><i class="fas fa-file-pdf"></i></button>
                            </td>
                            <!-- Campos personalizados: posición configurable vía columna virtual `personalized` (visibilidad la dicta cada field) -->
                            <template v-if="col.key === 'personalized'">
                                <template v-for="field in customFieldColumns">
                                    <td v-if="field.visible" :key="`cf-data-${field.id}`" class="text-start">
                                        <template v-if="isEditableCustomField(field)">
                                            <template v-if="field.type === 'text'"><el-input v-model="row.custom_fields_data[field.slug]" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'number'"><el-input v-model.number="row.custom_fields_data[field.slug]" type="number" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'textarea'"><el-input v-model="row.custom_fields_data[field.slug]" type="textarea" :rows="2" @blur="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-input></template>
                                            <template v-else-if="field.type === 'select'">
                                                <el-select v-model="row.custom_fields_data[field.slug]" @change="saveCustomFieldValue(row, field)" size="small" clearable :placeholder="field.name">
                                                    <el-option v-for="option in normalizeOptions(field.options)" :key="option" :label="option" :value="option"></el-option>
                                                </el-select>
                                            </template>
                                            <template v-else-if="field.type === 'checkbox'">
                                                <el-checkbox-group v-model="row.custom_fields_data[field.slug]" @change="saveCustomFieldValue(row, field)">
                                                    <el-checkbox v-for="option in normalizeOptions(field.options)" :key="option" :label="option" :value="option">{{ option }}</el-checkbox>
                                                </el-checkbox-group>
                                            </template>
                                            <template v-else-if="field.type === 'date'"><el-date-picker v-model="row.custom_fields_data[field.slug]" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" @change="saveCustomFieldValue(row, field)" size="small" :placeholder="field.name"></el-date-picker></template>
                                            <template v-else>{{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}</template>
                                        </template>
                                        <template v-else>{{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}</template>
                                    </td>
                                </template>
                            </template>
                            <td v-if="col.visible && col.key === 'recurrence'" :key="col.key" class="text-end">
                                <template v-if="row.type_period && row.quantity_period > 0">
                                    <el-switch :disabled="row.apply_concurrency" v-model="row.enabled_concurrency" active-text="Si" inactive-text="No" @change="changeConcurrency(row)"></el-switch>
                                </template>
                            </td>
                            <td v-if="col.visible && col.key === 'region'" :key="col.key" class="text-start">{{ row.customer_region }}</td>
                            <td v-if="col.visible && col.key === 'dispatch_status'" :key="col.key" class="text-end">
                                <template v-if="row.status_dispatch === 'ENTREGADO'"><button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-success" @click.prevent="clickDispatchStatus(row.id, false)">{{ row.status_dispatch }}</button></template>
                                <template v-if="row.status_dispatch === 'PENDIENTE'"><button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDispatchStatus(row.id, true)">{{ row.status_dispatch }}</button></template>
                                <template v-if="row.status_dispatch === 'PARCIAL'"><button type="button" style="min-width: 41px" class="btn waves-effect waves-light btn-xs btn-warning" @click.prevent="clickDispatchStatus(row.id, true)">{{ row.status_dispatch }}</button></template>
                            </td>
                            <td v-if="col.visible && col.key === 'type_period'" :key="col.key" class="text-end">{{ row.type_period | period }}</td>
                            <td v-if="col.visible && col.key === 'quantity_period'" :key="col.key" class="text-end">{{ row.quantity_period }}</td>
                            <td v-if="col.visible && col.key === 'paid'" :key="col.key" class="text-end">{{ row.paid ? 'Pagado' : 'Pendiente' }}</td>
                            <td v-if="col.visible && col.key === 'license_plate'" :key="col.key" class="text-end">{{ row.license_plate }}</td>
                            <td v-if="col.visible && col.key === 'actions'" :key="col.key" class="text-end">
                            <el-dropdown trigger="click" size="small">
                                <el-button class="btn-dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                    <i class="fas fa-ellipsis-h" style="display: none;"></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                  <el-dropdown-item
                                    v-if="row.btn_generate && row.state_type_id != '11' && typeUser != 'seller'"
                                    @click.native="clickCreate(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                    Editar
                                  </el-dropdown-item>

                                  <el-dropdown-item
                                    v-if="!row.changed && row.state_type_id != '11' && soapCompany != '03'"
                                    @click.native="clickGenerate(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
                                    Generar comprobante
                                  </el-dropdown-item>

                                  <el-dropdown-item divided />

                                  <template v-if="row.changed">
                                    <el-dropdown-item
                                      v-for="(document, i) in row.documents"
                                      :key="i"
                                    >
                                      <a
                                        :href="`/dispatches/create/${document.id}`"
                                        style="text-decoration: none; color: inherit;"
                                      >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
                                        Generar guía desde CPE
                                      </a>
                                    </el-dropdown-item>
                                  </template>

                                  <el-dropdown-item>
                                    <a
                                      :href="`/dispatches/create_new/sale_note/${row.id}`"
                                      style="text-decoration: none; color: inherit;"
                                    >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck me-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                                      </svg>
                                      Generar guía
                                    </a>
                                  </el-dropdown-item>

                                  <el-dropdown-item divided />

                                  <el-dropdown-item
                                    v-if="row.state_type_id != '11'"
                                    @click.native="clickOptions(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                    Imprimir
                                  </el-dropdown-item>

                                  <el-dropdown-item
                                    @click.native="duplicate(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                    Duplicar nota de venta
                                  </el-dropdown-item>

                                  <el-dropdown-item
                                    v-if="row.state_type_id != '11' && row.send_other_server === true"
                                    @click.native="sendToServer(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-server-2 me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M3 12m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M7 8l0 .01" /><path d="M7 16l0 .01" /><path d="M11 8h6" /><path d="M11 16h6" /></svg>
                                    Enviar a otro servidor
                                  </el-dropdown-item>

                                  <el-dropdown-item divided />

                                  <el-dropdown-item
                                    v-if="configuration.delete_relation_note_to_invoice && row.documents.length > 0"
                                    class="text-danger option-delete"
                                    @click.native="sendDeleteRelationInvoice(row.id)"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    Eliminar factura relacionada
                                  </el-dropdown-item>

                                  <el-dropdown-item
                                    v-if="row.state_type_id != '11'"
                                    @click.native="clickVoided(row.id)"
                                    class="text-danger option-delete"
                                  >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x me-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>
                                    Anular
                                  </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </td>
                        </template>
                    </tr>
                </data-table>
            </div>
        </div>
        <!-- <el-dialog
            title="Eliminar Documento Relacionado"
            :visible="showDialogDeleteRelationInvoice"
            >
            <table>
                <tr v-for="(document, index) in dataDeleteRelation.documents" :key="index">
                    <td>
                        <el-button
                            type="button"
                            class="btn waves-effect waves-light btn-xs btn-danger"
                            @click.prevent="deleteRelationInvoice(row.id)">
                            <i class="fas fa-trash"></i>
                        </el-button>
                    </td>
                    <td>
                        {{document.number_full}}
                    </td>
                </tr>
            </table>
        </el-dialog> -->

        <sale-note-payments
            :showDialog.sync="showDialogPayments"
            :documentId="recordId"
        ></sale-note-payments>

        <sale-notes-options
            :showDialog.sync="showDialogOptions"
            :recordId="saleNotesNewId"
            :showClose="true"
            :configuration="config"
        ></sale-notes-options>

        <sale-note-generate
            :show.sync="showDialogGenerate"
            :recordId="recordId"
            :showGenerate="true"
            :showClose="false"
        ></sale-note-generate>
        <ModalGenerateCPE :show.sync="showModalGenerateCPE"></ModalGenerateCPE>
        <UploadToOtherServer
            :configuration="config"
            :showMigrate.sync="showMigrateNv"
        ></UploadToOtherServer>

        <sale-note-dispatch-status
            :showDialog.sync="showDialogDispatch"
            :documentId="recordId"
            :statusDispatch="statusDispatch"
            :typeUser="typeUser"
        ></sale-note-dispatch-status>
    </div>
</template>

<script>
import DataTable from "../../../components/DataTableSaleNote.vue";
import UploadToOtherServer from "./partials/upload_other_server_group.vue";
import SaleNotePayments from "./partials/payments.vue";
import SaleNotesOptions from "./partials/options.vue";
import SaleNoteGenerate from "./partials/option_documents.vue";
import { deletable } from "../../../mixins/deletable";
import ModalGenerateCPE from "./ModalGenerateCPE.vue";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import SaleNoteDispatchStatus from "./partials/dispatch_status.vue";

export default {
    props: ["soapCompany", "typeUser", "configuration"],
    mixins: [deletable],
    components: {
        DataTable,
        SaleNotePayments,
        SaleNotesOptions,
        SaleNoteGenerate,
        ModalGenerateCPE,
        UploadToOtherServer,
        SaleNoteDispatchStatus
    },
    computed: {
        ...mapState(["config"]),
        orderedColumns() {
            return Object.entries(this.columns)
                .map(([key, col]) => ({ key, ...col }))
                .sort((a, b) => a.order - b.order);
        },
        tenantSelectableColumns() {
            return this.orderedColumns.filter(col => col.key !== 'personalized');
        },
    },
    data() {
        return {
            showModalGenerateCPE: false,
            showMigrateNv: false,
            resource: "sale-notes",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            showDialogDispatch: false,
            saleNotesNewId: null,
            recordId: null,
            statusDispatch: null,
            columns: {
                seller_name:        { title: "Vendedor",             visible: false, order: 0  },
                date_of_issue:      { title: "Fecha Emisión",        visible: true,  order: 1  },
                date_payment:       { title: "Fecha de pago",        visible: false, order: 2  },
                customer:           { title: "Cliente",              visible: true,  order: 3  },
                full_number:        { title: "Nota de Venta",        visible: true,  order: 4  },
                state_type:         { title: "Estado",               visible: true,  order: 5  },
                exchange_rate_sale: { title: "Tipo de cambio",       visible: false, order: 6  },
                currency_type:      { title: "Moneda",               visible: true,  order: 7  },
                due_date:           { title: "Fecha de Vencimiento", visible: false, order: 8  },
                total_exportation:  { title: "T.Exportación",        visible: false, order: 9  },
                total_free:         { title: "T.Gratuito",           visible: false, order: 10 },
                total_unaffected:   { title: "T.Inafecto",           visible: false, order: 11 },
                total_exonerated:   { title: "T.Exonerado",          visible: false, order: 12 },
                total_taxed:        { title: "T.Gravado",            visible: false, order: 13 },
                total_igv:          { title: "T.IGV",                visible: false, order: 14 },
                total:              { title: "Total",                visible: true,  order: 15 },
                total_paid:         { title: "Pagado",               visible: false, order: 16 },
                total_pending_paid: { title: "Por pagar",            visible: false, order: 17 },
                documents:          { title: "Comprobantes",         visible: true,  order: 18 },
                payment_status:     { title: "Estado pago",          visible: true,  order: 19 },
                purchase_order:     { title: "Orden de compra",      visible: true,  order: 20 },
                payments:           { title: "Pagos",                visible: true,  order: 21 },
                download:           { title: "Descarga",             visible: true,  order: 22 },
                personalized:       { title: "Personalizados",       visible: true,  order: 23 },
                recurrence:         { title: "Recurrencia",          visible: false, order: 24 },
                region:             { title: "Region",               visible: false, order: 25 },
                dispatch_status:    { title: "Estado de despacho",   visible: false, order: 26 },
                type_period:        { title: "Tipo Periodo",         visible: true,  order: 27 },
                quantity_period:    { title: "Cantidad Periodo",     visible: true,  order: 28 },
                paid:               { title: "Estado de Pago",       visible: false, order: 29 },
                license_plate:      { title: "Placa",                visible: true,  order: 30 },
                actions:            { title: "Acciones",             visible: true,  order: 31 },
            },
            customFieldColumns: [],
            savedCustomFieldVisibilities: {},
            decimal_quantity: 2,
            // showDialogDeleteRelationInvoice: false,
            // dataDeleteRelation: {
            //     documents: {},
            //     id: ''
            // }
        };
    },
    async created() {
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
        this.loadDecimalQuantity();
        await this.getColumnsToShow();
        this.loadCustomFieldsColumns();
    },
    filters: {
        period(name) {
            let res = "";
            switch (name) {
                case "month":
                    res = "Mensual";
                    break;
                case "year":
                    res = "Anual";
                    break;
                default:
                    break;
            }

            return res;
        }
    },
    methods: {
        loadDecimalQuantity() {
            // Obtener la configuración general para los decimales
            this.$http ? this.$http.get('/configurations/record').then(response => {
                if (response.data && response.data.data && response.data.data.decimal_quantity) {
                    this.decimal_quantity = response.data.data.decimal_quantity;
                }
            }) :
            (window.axios && window.axios.get('/configurations/record').then(response => {
                if (response.data && response.data.data && response.data.data.decimal_quantity) {
                    this.decimal_quantity = response.data.data.decimal_quantity;
                }
            }));
        },
        formatDecimal(value) {
            if (value === undefined || value === null || value === '') return '';
            let cleanValue = value;
            if (typeof cleanValue === 'string') {
                cleanValue = cleanValue.replace(/,/g, '').trim();
            }
            if (isNaN(Number(cleanValue))) return '';
            const num = Number(cleanValue);
            return num.toLocaleString('en-US', { minimumFractionDigits: this.decimal_quantity, maximumFractionDigits: this.decimal_quantity });
        },
        formatDate(date) {
            if (!date) return null;
            return moment(date).format("DD-MM-YYYY");
        },
        ...mapActions(["loadConfiguration"]),
        getColumnsToShow(updated) {
            const columnsPayload = {};
            Object.keys(this.columns).forEach(key => {
                columnsPayload[key] = { title: this.columns[key].title, visible: this.columns[key].visible, order: this.columns[key].order };
            });
            if (updated !== undefined && columnsPayload.personalized) {
                const fields = {};
                this.customFieldColumns.forEach(field => {
                    fields[field.slug] = field.visible;
                });
                columnsPayload.personalized.fields = fields;
            }
            return this.$http
                .post("/validate_columns", {
                    columns: columnsPayload,
                    report: "sale_notes_index",
                    updated: updated !== undefined
                })
                .then(response => {
                    if (updated === undefined) {
                        let currentCols = response.data.columns;
                        if (currentCols !== undefined) {
                            Object.keys(currentCols).forEach(key => {
                                if (this.columns[key] !== undefined) {
                                    this.columns[key].visible = currentCols[key].visible;
                                    if (currentCols[key].order !== undefined) {
                                        this.columns[key].order = currentCols[key].order;
                                    }
                                }
                            });
                            if (currentCols.personalized && currentCols.personalized.fields) {
                                this.savedCustomFieldVisibilities = currentCols.personalized.fields;
                            }
                        } else {
                            this.$http.get('/column-visibility/sale_notes').then(res => {
                                if (res.data.success && res.data.data) {
                                    Object.keys(res.data.data).forEach(key => {
                                        if (this.columns[key] !== undefined) {
                                            this.columns[key].visible = res.data.data[key].visible;
                                            if (res.data.data[key].order !== undefined) {
                                                this.columns[key].order = res.data.data[key].order;
                                            }
                                        }
                                    });
                                    if (res.data.data.personalized && res.data.data.personalized.fields) {
                                        this.savedCustomFieldVisibilities = res.data.data.personalized.fields;
                                    }
                                }
                            }).catch(() => {});
                        }
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
        async loadCustomFieldsColumns() {
            try {
                const response = await this.$http.get(
                    "/configurations/custom-fields/sale-notes"
                );
                const defaultVisible = this.columns.personalized
                    ? this.columns.personalized.visible
                    : true;
                this.customFieldColumns = (response.data.data || []).map(field => {
                    const savedVisible = this.savedCustomFieldVisibilities[field.slug];
                    return {
                        ...field,
                        visible: savedVisible !== undefined ? savedVisible : defaultVisible
                    };
                });
            } catch (error) {
                console.error("Error cargando columnas de campos personalizados:", error);
                this.customFieldColumns = [];
            }
        },
        updateCustomFieldColumns() {
            this.getColumnsToShow(1);
        },
        isEditableCustomField(field) {
            return [
                'text',
                'number',
                'textarea',
                'select',
                'checkbox',
                'date'
            ].includes(field.type);
        },
        normalizeOptions(options) {
            if (!options) return [];
            if (Array.isArray(options)) {
                if (options.length === 1 && typeof options[0] === 'string' && options[0].includes(',')) {
                    return options[0]
                        .split(',')
                        .map(opt => opt.trim())
                        .filter(opt => opt.length > 0);
                }
                return options;
            }
            if (typeof options === 'string') {
                return options
                    .split(/[,\n]/)
                    .map(opt => opt.trim())
                    .filter(opt => opt.length > 0);
            }
            return [];
        },
        ensureCustomFieldsData(row, field = null) {
            if (!row.custom_fields_data || typeof row.custom_fields_data !== 'object') {
                this.$set(row, 'custom_fields_data', {});
            }
            if (field && field.type === 'checkbox' && row.custom_fields_data[field.slug] === undefined) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            return row.custom_fields_data;
        },
        saveCustomFieldValue(row, field) {
            this.ensureCustomFieldsData(row, field);
            if (field.type === 'checkbox' && !Array.isArray(row.custom_fields_data[field.slug])) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            this.$http
                .post('/sale-notes/custom-fields/update', {
                    id: row.id,
                    custom_fields_data: row.custom_fields_data
                })
                .then(response => {
                    if (response.data.success) {
                        if (response.data.data !== undefined) {
                            this.$set(row, 'custom_fields_data', response.data.data);
                        }
                        this.$message.success('Campo personalizado actualizado correctamente.');
                    }
                })
                .catch(error => {
                    console.error('Error guardando campo personalizado:', error);
                    this.$message.error('No se pudo actualizar el campo personalizado.');
                });
        },
        formatCustomFieldValue(value) {
            if (value === null || value === undefined || value === "") {
                return "";
            }
            if (Array.isArray(value)) {
                return value.join(", ");
            }
            if (typeof value === "object") {
                return JSON.stringify(value);
            }
            return value;
        },
        duplicate(id) {

            this.$confirm(
                    '¿Estás seguro que quieres realizar la duplicidad de la nota de venta?',
                    'Confirmar Duplicidad',
                    {
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar',
                        type: 'warning',
                    }
                ).then(() => {
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

                }).catch(() => {});
        },
        onOpenModalGenerateCPE() {
            this.showModalGenerateCPE = true;
        },
        onOpenModalMigrateNv() {
            this.showMigrateNv = true;
        },
        clickDownload(external_id) {
            window.open(
                `/sale-notes/downloadExternal/${external_id}`,
                "_blank"
            );
        },
        clickOptions(recordId) {
            this.saleNotesNewId = recordId;
            this.showDialogOptions = true;
        },
        sendToServer(recordId) {
            this.$http
                .post("/sale-notes/UpToOther", { sale_note_id: recordId })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (
                        error.response !== undefined &&
                        error.response.status !== undefined &&
                        error.response.status.errors !== undefined &&
                        error.response.status === 422
                    ) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickGenerate(recordId) {
            this.recordId = recordId;
            this.showDialogGenerate = true;
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        clickCreate(id = "") {
            location.href = `/${this.resource}/create/${id}`;
        },
        changeConcurrency(row) {
            this.$http
                .post(`/${this.resource}/enabled-concurrency`, row)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickVoided(id) {
            this.anular(`/${this.resource}/anulate/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        clickDispatchStatus(recordId, status) {
            this.recordId = recordId;
            this.showDialogDispatch = true;
            this.statusDispatch = status;
        },

        // deleteRelationInvoice(saleNote) {
        //     this.dataDeleteRelation.documents = saleNote.documents
        //     this.dataDeleteRelation.id = saleNote.id
        //     this.showDialogDeleteRelationInvoice = true
        // },
        sendDeleteRelationInvoice(id) {
            this.$http
                .post(`${this.resource}/delete-relation-invoice`, { id })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se ha eliminado el comprobante relacionado correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            this.$eventHub.$emit("reloadData");
        }
    }
};
</script>

