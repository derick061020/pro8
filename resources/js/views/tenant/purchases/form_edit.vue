<template>
    <div>
        <div class="page-header pe-0 d-none d-md-block">
            <h2><a href="/purchases">
                <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Editar Compra </span></li>
            </ol>
        </div>
        <div class="card tab-content-default row-new mb-0 pt-2 pt-md-0 mt-0 mt-md-5">
            <div class="tab-content tab-content-default card-body">
                <div class="invoice p-1 p-md-3">
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">

                        <!-- Fila 1: Tipo comprobante, Serie, Número, Fec. Emisión, Fec. Vencimiento -->
                        <div class="row mx-0">
                            <div class="col-6 col-lg-4">
                                <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                                    <label class="control-label">Tipo comprobante</label>
                                    <el-select v-model="form.document_type_id" @change="changeDocumentType">
                                        <el-option v-for="option in document_types" :key="option.id" :value="option.id"
                                                   :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.document_type_id"
                                           v-text="errors.document_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-3 col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.series}">
                                    <label class="control-label">Serie <span class="text-danger">*</span></label>
                                    <el-input v-model="form.series" :maxlength="4" @input="inputSeries"></el-input>
                                    <small class="form-control-feedback" v-if="errors.series"
                                           v-text="errors.series[0]"></small>
                                </div>
                            </div>
                            <div class="col-3 col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.number}">
                                    <label class="control-label">Número <span class="text-danger">*</span></label>
                                    <el-input v-model="form.number"></el-input>
                                    <small class="form-control-feedback" v-if="errors.number"
                                           v-text="errors.number[0]"></small>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                                    <label class="control-label">Fec Emisión</label>
                                    <el-date-picker v-model="form.date_of_issue" :readonly="readonly_date_of_due"
                                                    type="date" value-format="yyyy-MM-dd" :clearable="false"
                                                    @change="changeDateOfIssue"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_issue"
                                           v-text="errors.date_of_issue[0]"></small>
                                </div>
                            </div>
                            <div class="col-6 col-lg-2">
                                <div class="form-group" :class="{'has-danger': errors.date_of_due}">
                                    <label class="control-label">Fec. Vencimiento</label>
                                    <el-date-picker v-model="form.date_of_due" type="date" :readonly="readonly_date_of_due"
                                                    value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                                    <small class="form-control-feedback" v-if="errors.date_of_due"
                                           v-text="errors.date_of_due[0]"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Proveedor, Moneda, Tipo de cambio, Observaciones, Checkboxes -->
                        <div class="row mx-0">
                            <div class="col-6">
                                <div class="form-group position-relative" :class="{'has-danger': errors.supplier_id}">
                                    <label class="control-label">Proveedor</label>
                                    <el-select v-model="form.supplier_id" filterable @change="changeSupplier"
                                               ref="select_person" @keyup.native="keyupSupplier"
                                               @keyup.enter.native="keyupEnterSupplier">
                                        <el-option v-for="option in suppliers" :key="option.id" :value="option.id"
                                                   :label="option.description"></el-option>
                                    </el-select>
                                    <span class="btn-add-new" @click.prevent="showDialogNewPerson = true" title="Agregar nuevo proveedor">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                                    </span>
                                    <small class="form-control-feedback" v-if="errors.supplier_id"
                                           v-text="errors.supplier_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 col-3">
                                <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                                    <label class="control-label">Moneda</label>
                                    <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                        <el-option v-for="option in currency_types" :key="option.id" :value="option.id"
                                                   :label="option.description"></el-option>
                                    </el-select>
                                    <small class="form-control-feedback" v-if="errors.currency_type_id"
                                           v-text="errors.currency_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-lg-2 col-3">
                                <div class="form-group" :class="{'has-danger': errors.exchange_rate_sale}">
                                    <label class="control-label">Tipo de cambio
                                        <el-tooltip class="item" effect="dark"
                                                    content="Tipo de cambio del día, extraído de SUNAT" placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </label>
                                    <el-input v-model="form.exchange_rate_sale"></el-input>
                                    <small class="form-control-feedback" v-if="errors.exchange_rate_sale"
                                           v-text="errors.exchange_rate_sale[0]"></small>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-6 col-lg-4">
                                <label class="control-label">Observaciones</label>
                                <el-input v-model="form.observation" placeholder="Observaciones"></el-input>
                            </div>

                            <div class="col-12">&nbsp;</div>

                            <div class="col-md-8 mt-4">
                                <div class="form-group">
                                    <el-checkbox v-model="form.has_client" @change="changeHasClient">¿Desea agregar el
                                        cliente para esta compra?
                                    </el-checkbox>
                                </div>
                            </div>

                            <div class="col-md-8 mt-2 mb-2">
                                <div class="form-group">
                                    <el-checkbox v-model="form.has_payment" @change="changeHasPayment">¿Desea agregar pagos
                                        a esta compra?
                                    </el-checkbox>
                                </div>
                            </div>

                            <div class="col-md-8 mt-2 mb-2" v-if="configuration.enabled_global_igv_to_purchase">
                                <div class="form-group">
                                    <el-checkbox v-model="localHasGlobalIgv"
                                                 :disabled="(form.items.length != 0 && configuration.enabled_global_igv_to_purchase)"
                                                 @change="changeHasGlobalIgv">¿La compra tiene igv?
                                        <el-tooltip class="item"
                                                    content="Al estar la configuracion activa, sobreescribe el igv del item. Si no esta checado, el producto no tendra igv."
                                                    effect="dark"
                                                    placement="top-end">
                                            <i class="fa fa-info-circle"></i>
                                        </el-tooltip>
                                    </el-checkbox>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6" v-if="form.has_client">
                                <div class="form-group">
                                    <label class="control-label">Clientes</label>
                                    <el-select v-model="form.customer_id" filterable remote
                                               popper-class="el-select-customers" clearable
                                               placeholder="Nombre o número de documento"
                                               :remote-method="searchRemotePersons"
                                               :loading="loading_search">
                                        <el-option v-for="option in customers" :key="option.id" :value="option.id"
                                                   :label="option.description"></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 3: Pagos -->
                        <div class="row mx-0">
                            <template v-if="form.has_payment">

                                <div class="col-lg-2 col-md-2">
                                    <div :class="{'has-danger': errors.payment_condition_id}"
                                         class="form-group">
                                        <label class="control-label">Condición de pago</label>
                                        <el-select v-model="form.payment_condition_id"
                                                   @change="changePaymentCondition">
                                            <el-option v-for="option in payment_conditions"
                                                       :key="option.id"
                                                       :label="option.name"
                                                       :value="option.id"></el-option>
                                        </el-select>
                                        <small v-if="errors.payment_condition_id"
                                               class="form-control-feedback"
                                               v-text="errors.payment_condition_id[0]"></small>
                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-12 mt-2">
                                    <!-- Contado -->
                                    <template v-if="form.payment_condition_id === '01'">
                                        <table>
                                            <thead>
                                            <tr width="100%">
                                                <th v-if="form.payments.length > 0" class="pb-2">Forma de pago</th>
                                                <th v-if="form.payments.length > 0" class="pb-2">Desde
                                                    <el-tooltip class="item"
                                                                content="Aperture caja o cuentas bancarias"
                                                                effect="dark"
                                                                placement="top-start">
                                                        <i class="fa fa-info-circle"></i>
                                                    </el-tooltip>
                                                </th>
                                                <th v-if="form.payments.length > 0" class="pb-2">Referencia</th>
                                                <th v-if="form.payments.length > 0" class="pb-2">Monto</th>
                                                <th width="15%"><a class="text-center font-weight-bold text-info"
                                                                   href="#"
                                                                   @click.prevent="clickAddPayment">[+ Agregar]</a>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.payments" :key="index">
                                                <td>
                                                    <div class="form-group mb-2 me-2">
                                                        <el-select v-model="row.payment_method_type_id"
                                                                   @change="changePaymentMethodType(index)">
                                                            <el-option v-for="option in cashPaymentMethod"
                                                                       :key="option.id"
                                                                       :label="option.description"
                                                                       :value="option.id"></el-option>
                                                        </el-select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-2 me-2">
                                                        <el-select v-model="row.payment_destination_id" filterable>
                                                            <el-option v-for="option in payment_destinations"
                                                                       :key="option.id"
                                                                       :label="option.description"
                                                                       :value="option.id"></el-option>
                                                        </el-select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-2 me-2">
                                                        <el-input v-model="row.reference"></el-input>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-2 me-2">
                                                        <el-input v-model="row.payment"></el-input>
                                                    </div>
                                                </td>
                                                <td class="series-table-actions text-center">
                                                    <button class="btn waves-effect waves-light btn-xs btn-danger"
                                                            type="button"
                                                            @click.prevent="clickCancel(index)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                                <br>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </template>

                                    <!-- Credito -->
                                    <template v-else-if="form.payment_condition_id === '02'">
                                        <table v-if="form.fee.length > 0">
                                            <thead>
                                            <tr width="100%">
                                                <th class="pb-2" v-if="form.fee.length > 0">Método de pago</th>
                                                <th class="pb-2">Fecha</th>
                                                <th class="pb-2">Monto</th>
                                                <th class="pb-2"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.fee" :key="index">
                                                <td>
                                                    <el-select v-model="row.payment_method_type_id"
                                                               @change="changePaymentMethodType(index)">
                                                        <el-option v-for="option in creditPaymentMethod"
                                                                   :key="option.id"
                                                                   :label="option.description"
                                                                   :value="option.id"></el-option>
                                                    </el-select>
                                                </td>
                                                <td>
                                                    <el-date-picker v-model="row.date"
                                                                    :clearable="false"
                                                                    format="dd/MM/yyyy"
                                                                    type="date"
                                                                    :readonly="readonly_date_of_due"
                                                                    value-format="yyyy-MM-dd"></el-date-picker>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.amount"></el-input>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </template>

                                    <!-- Crédito con cuotas -->
                                    <template v-else>
                                        <table v-if="form.fee.length > 0">
                                            <thead>
                                            <tr width="100%">
                                                <th class="pb-2">Fecha</th>
                                                <th class="pb-2">Monto</th>
                                                <th class="pb-2"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(row, index) in form.fee" :key="index">
                                                <td>
                                                    <el-date-picker v-model="row.date"
                                                                    :clearable="false"
                                                                    format="dd/MM/yyyy"
                                                                    type="date"
                                                                    value-format="yyyy-MM-dd"></el-date-picker>
                                                </td>
                                                <td>
                                                    <el-input v-model="row.amount"></el-input>
                                                </td>
                                                <td class="text-center">
                                                    <button v-if="index > 0"
                                                            class="btn waves-effect waves-light btn-xs btn-danger"
                                                            type="button"
                                                            @click.prevent="clickRemoveFee(index)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5">
                                                    <label class="control-label">
                                                        <a class="" href="#" @click.prevent="clickAddFee">
                                                            <i class="fa fa-plus font-weight-bold text-info"></i>
                                                            <span style="color: #777777">Agregar cuota</span>
                                                        </a>
                                                    </label>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Fila 4: Botón agregar producto -->
                        <div class="row mx-0">
                            <div class="col-12 d-flex align-items-end mt-4">
                                <div class="form-group">
                                    <button type="button" class="btn waves-effect waves-light btn-primary"
                                            @click.prevent="showDialogAddItem = true">+ Agregar Producto
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de ítems -->
                        <div v-if="form.items !== undefined && form.items.length > 0" class="row mt-3 mx-0">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Almacén</th>
                                            <th>Lote</th>
                                            <th class="text-center">Unidad</th>
                                            <th class="text-end">Cantidad</th>
                                            <th class="text-end">Valor Unitario</th>
                                            <th class="text-end">Precio Unitario</th>
                                            <th class="text-end">Descuento</th>
                                            <th class="text-end">Cargo</th>
                                            <th class="text-end">Total</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(row, index) in form.items" :key="index">
                                            <td>
                                                {{ setDescriptionOfItem(row.item) }}
                                                <br/><small>{{ row.affectation_igv_type.description }}</small>
                                            </td>
                                            <td class="text-start">{{ getWarehouseDescription(row) }}</td>
                                            <td class="text-start">{{ row.lot_code }}</td>
                                            <td class="text-center">{{ row.item.unit_type_id }}</td>
                                            <td class="text-end">{{ parseInt(row.quantity) }}</td>
                                            <td class="text-end">{{ currency_type.symbol }}
                                                {{ formatDecimal(row.unit_value) }}
                                            </td>
                                            <td class="text-end">{{ currency_type.symbol }}
                                                {{ formatDecimal(row.unit_price) }}
                                            </td>
                                            <td class="text-end">{{ currency_type.symbol }} {{ formatDecimal(row.total_discount) }}</td>
                                            <td class="text-end">{{ currency_type.symbol }} {{ formatDecimal(row.total_charge) }}</td>
                                            <td class="text-end">{{ currency_type.symbol }} {{ formatDecimal(row.total) }}</td>
                                            <td class="text-end">
                                                <button type="button"
                                                        class="btn waves-effect waves-light btn-xs btn-danger"
                                                        @click.prevent="clickRemoveItem(index)">x
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div v-if="form.items.length > 0" class="total-rows">
                                        <span>Total de ítems: {{ form.items.length }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Totales -->
                            <div class="col-md-12">
                                <p v-if="form.total_exportation > 0" class="text-end">
                                    OP.EXPORTACIÓN: {{ currency_type.symbol }} {{ formatDecimal(form.total_exportation) }}
                                </p>
                                <p v-if="form.total_free > 0" class="text-end">
                                    OP.GRATUITAS: {{ currency_type.symbol }} {{ formatDecimal(form.total_free) }}
                                </p>
                                <p v-if="form.total_unaffected > 0" class="text-end">
                                    OP.INAFECTAS: {{ currency_type.symbol }} {{ formatDecimal(form.total_unaffected) }}
                                </p>
                                <p v-if="form.total_exonerated > 0" class="text-end">
                                    OP.EXONERADAS: {{ currency_type.symbol }} {{ formatDecimal(form.total_exonerated) }}
                                </p>
                                <p v-if="form.total_taxed > 0" class="text-end">
                                    OP.GRAVADA: {{ currency_type.symbol }} {{ formatDecimal(form.total_taxed) }}
                                </p>
                                <p v-if="form.total_igv > 0" class="text-end">
                                    IGV: {{ currency_type.symbol }} {{ formatDecimal(form.total_igv) }}
                                </p>
                                <p v-if="form.total_isc > 0" class="text-end">
                                    ISC: {{ currency_type.symbol }} {{ formatDecimal(form.total_isc) }}
                                </p>
                                <p v-if="form.total_discount > 0" class="text-end">
                                    DESCUENTOS TOTALES: {{ currency_type.symbol }} {{ formatDecimal(form.total_discount) }}
                                </p>

                                <h3 v-if="form.total > 0" class="text-end">
                                    <b>TOTAL COMPRAS: </b>{{ currency_type.symbol }} {{ formatDecimal(form.total) }}
                                </h3>

                                <template v-if="is_perception_agent">
                                    <hr>
                                    <div class="row mt-1">
                                        <div class="col-lg-10 float-end">
                                            <label class="float-end control-label">NÚMERO PERCEPCIÓN: </label>
                                        </div>
                                        <div class="col-lg-2 float-end">
                                            <div class="form-group" :class="{'has-danger': errors.perception_number}">
                                                <el-input v-model="form.perception_number"></el-input>
                                                <small class="form-control-feedback" v-if="errors.perception_number"
                                                       v-text="errors.perception_number[0]"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-lg-10 float-end">
                                            <label class="float-end control-label">FEC EMISIÓN PERCEPCIÓN: </label>
                                        </div>
                                        <div class="col-lg-2 float-end">
                                            <div class="form-group" :class="{'has-danger': errors.perception_date}">
                                                <el-date-picker v-model="form.perception_date" type="date"
                                                                value-format="yyyy-MM-dd" :clearable="false"
                                                                @change="changeDateOfIssue"></el-date-picker>
                                                <small class="form-control-feedback" v-if="errors.perception_date"
                                                       v-text="errors.perception_date[0]"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-lg-10 float-end">
                                            <label class="float-end control-label">IMPORTE PERCEPCIÓN: </label>
                                        </div>
                                        <div class="col-lg-2 float-end">
                                            <div class="form-group" :class="{'has-danger': errors.total_perception}">
                                                <el-input v-model="form.total_perception"
                                                          @input="inputTotalPerception"
                                                          :readonly="true"></el-input>
                                                <small class="form-control-feedback" v-if="errors.total_perception"
                                                       v-text="errors.total_perception[0]"></small>
                                            </div>
                                        </div>
                                    </div>

                                    <h3 v-if="form.total > 0 && !hide_button" class="text-end">
                                        <b>MONTO TOTAL : </b>{{ currency_type.symbol }} {{ formatDecimal(total_amount) }}
                                    </h3>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Acciones del formulario -->
                    <div
                        class="form-actions mt-4 footer-card-default gap-2
                               d-flex flex-column flex-md-row
                               justify-content-center justify-content-md-between
                               align-items-stretch align-items-md-center"
                    >
                        <el-button class="btn btn-default second-buton-default"
                                   @click.prevent="close()">Cancelar</el-button>
                        <el-button v-if="form.items !== undefined && form.items.length > 0 && !hide_button"
                                   :loading="loading_submit"
                                   native-type="submit"
                                   class="btn btn-primary btn-submit-default"
                                   type="primary">Guardar cambios
                        </el-button>
                    </div>
                </form>
                </div>
            </div>

            <purchase-form-item :showDialog.sync="showDialogAddItem"
                                :currency-type-id-active="form.currency_type_id"
                                :currency-types="currency_types"
                                :exchange-rate-sale="form.exchange_rate_sale"
                                :localHasGlobalIgv="localHasGlobalIgv"
                                :percentage-igv="percentage_igv"
                                @add="addRow"></purchase-form-item>

            <person-form :showDialog.sync="showDialogNewPerson"
                         type="suppliers"
                         :input_person="input_person"
                         :external="true"></person-form>

            <purchase-options :type="type" :showDialog.sync="showDialogOptions"
                              :recordId="purchaseNewId"
                              :showClose="false"></purchase-options>
        </div>
    </div>
</template>

<script>

import PurchaseFormItem from './partials/item.vue'
import PersonForm from '../persons/form.vue'
import PurchaseOptions from './partials/options.vue'
import {functions, exchangeRate, fnPaymentsFee} from '../../../mixins/functions'
import {calculateRowItem, showNamePdfOfDescription} from '../../../helpers/functions'

export default {
    props: {
        'resourceId': {
            required: true,
            default: 0
        }
    },
    components: {PurchaseFormItem, PersonForm, PurchaseOptions},
    mixins: [functions, exchangeRate, fnPaymentsFee],
    data() {
        return {
            input_person: {},
            type: 'edit',
            resource: 'purchases',
            showDialogAddItem: false,
            showDialogNewPerson: false,
            showDialogOptions: false,
            loading_submit: false,
            hide_button: false,
            is_perception_agent: false,
            errors: {},
            form: {},
            aux_supplier_id: null,
            total_amount: 0,
            document_types: [],
            currency_types: [],
            discount_types: [],
            charges_types: [],
            payment_method_types: [],
            all_suppliers: [],
            suppliers: [],
            all_customers: [],
            customers: [],
            company: null,
            operation_types: [],
            establishment: {},
            all_series: [],
            payment_destinations: [],
            payment_conditions: [],
            series: [],
            loading_search: false,
            currency_type: {},
            readonly_date_of_due: false,
            configuration: {},
            purchaseNewId: null,
            localHasGlobalIgv: false,
            warehouses: [],
            decimal_quantity: 2
        }
    },
    async created() {
        await this.initForm()
        await this.$http.get(`/${this.resource}/tables`)
            .then(async (response) => {

                this.document_types = response.data.document_types_invoice
                this.currency_types = response.data.currency_types
                this.establishment = response.data.establishment
                this.all_suppliers = response.data.suppliers
                this.discount_types = response.data.discount_types
                this.payment_method_types = response.data.payment_method_types
                this.payment_destinations = response.data.payment_destinations
                this.all_customers = response.data.customers
                this.configuration = response.data.configuration
                this.payment_conditions = response.data.payment_conditions
                this.warehouses = response.data.warehouses

                this.charges_types = response.data.charges_types
                this.form.currency_type_id = (this.currency_types.length > 0) ? this.currency_types[0].id : null
                this.form.establishment_id = (this.establishment.id) ? this.establishment.id : null
                this.form.document_type_id = (this.document_types.length > 0) ? this.document_types[0].id : null

                await this.changeDateOfIssue()
                this.changeDocumentType()
                this.changeCurrencyType()

                await this.initRecord()
            })

        this.$eventHub.$on('reloadDataPersons', (supplier_id) => {
            this.reloadDataSuppliers(supplier_id)
        })

        this.$eventHub.$on('initInputPerson', () => {
            this.initInputPerson()
        })

        await this.filterCustomers()
        await this.changeHasPayment()
        await this.changeHasClient()
        this.initGlobalIgv()
        this.loadDecimalQuantity()
    },
    computed: {
        creditPaymentMethod: function () {
            return _.filter(this.payment_method_types, {'is_credit': true})
        },
        cashPaymentMethod: function () {
            return _.filter(this.payment_method_types, {'is_credit': false})
        },
        isCreditPaymentCondition: function () {
            return ['02', '03'].includes(this.form.payment_condition_id)
        },
    },
    methods: {
        loadDecimalQuantity() {
            // Obtener la configuracion general para los decimales
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
            if (value === undefined || value === null || isNaN(value)) return '';
            return Number(value).toLocaleString('en-US', { minimumFractionDigits: this.decimal_quantity, maximumFractionDigits: this.decimal_quantity });
        },
        setDescriptionOfItem(item)
        {
            return showNamePdfOfDescription(item, this.configuration.show_pdf_name)
        },
        getWarehouse(id) {
            return _.find(this.warehouses, {id: id})
        },
        getWarehouseDescription(row) {

            let description = null

            if (row.warehouse_description) {
                description = row.warehouse_description
            } else if (row.warehouse) {
                description = row.warehouse.description
            } else {
                const warehouse = this.getWarehouse(row.warehouse_id)
                if (warehouse) description = warehouse.description
            }

            return description
        },
        changeHasGlobalIgv() {

        },
        changeHasPayment() {

            if (!this.form.has_payment) {
                this.form.payments = []
                this.form.fee = []
                this.form.payment_condition_id = '01'
            } else {
                this.changePaymentCondition()
            }

        },
        changeHasClient() {

            if (!this.form.has_client) {
                this.form.customer_id = null
            }
        },
        searchRemotePersons(input) {

            if (input.length > 1) {

                this.loading_search = true
                let parameters = `input=${input}`

                this.$http.get(`/reports/data-table/persons/customers?${parameters}`)
                    .then(response => {
                        this.customers = response.data.persons
                        this.loading_search = false

                        if (this.customers.length == 0) {
                            this.filterCustomers()
                        }
                    })
            } else {
                this.filterCustomers()
            }

        },
        filterCustomers() {
            this.customers = this.all_customers
        },
        getFormatUnitPriceRow(unit_price) {
            return _.round(unit_price, 6)
            // return unit_price.toFixed(6)
        },
        async validate_payments() {

            let error_by_item = 0
            let acum_total = 0
            let q_affectation_free = 0

            await this.form.payments.forEach((item) => {
                acum_total += parseFloat(item.payment)
                if (item.payment <= 0 || item.payment == null) error_by_item++;
            })

            //determinate affectation igv
            await this.form.items.forEach((item) => {
                if (item.affectation_igv_type.free) {
                    q_affectation_free++
                }
            })

            let all_free = (q_affectation_free == this.form.items.length) ? true : false

            if (!all_free && (acum_total > parseFloat(this.form.total) || error_by_item > 0)) {
                return {
                    success: false,
                    message: 'Los montos ingresados superan al monto a pagar o son incorrectos'
                }
            }

            if (this.form.has_client && !this.form.customer_id) {
                return {
                    success: false,
                    message: 'Debe seleccionar un cliente'
                }
            }

            if (this.form.has_payment) {

                if (this.form.payment_condition_id === '01' && this.form.payments.length == 0) {

                    return {
                        success: false,
                        message: 'Debe registrar al menos un pago'
                    }

                }

                if (this.isCreditPaymentCondition && this.form.fee.length == 0) {

                    return {
                        success: false,
                        message: 'Debe registrar al menos una cuota'
                    }

                }
            }

            return {
                success: true,
                message: null
            }
        },
        clickCancel(index) {
            this.form.payments.splice(index, 1);
            this.calculatePayments()
        },
        clickAddPayment() {

            this.form.payments.push({
                id: null,
                purchase_id: null,
                date_of_payment: moment().format('YYYY-MM-DD'),
                payment_method_type_id: '01',
                reference: null,
                payment_destination_id: this.getPaymentDestinationId(),
                payment: 0,
            });

            this.calculatePayments()

        },
        setTotalDefaultPayment() {

            if (this.form.payments.length > 0) {

                this.form.payments[0].payment = this.form.total
            }
        },
        getPaymentDestinationId() {

            if (this.configuration.destination_sale && this.payment_destinations.length > 0) {

                let cash = _.find(this.payment_destinations, {id: 'cash'})

                return (cash) ? cash.id : this.payment_destinations[0].id

            }

            return null

        },
        initInputPerson() {
            this.input_person = {
                number: '',
                identity_document_type_id: ''
            }
        },
        keyupEnterSupplier() {

            if (this.input_person.number) {

                if (!isNaN(parseInt(this.input_person.number))) {

                    switch (this.input_person.number.length) {
                        case 8:
                            this.input_person.identity_document_type_id = '1'
                            this.showDialogNewPerson = true
                            break;

                        case 11:
                            this.input_person.identity_document_type_id = '6'
                            this.showDialogNewPerson = true
                            break;
                        default:
                            this.input_person.identity_document_type_id = '6'
                            this.showDialogNewPerson = true
                            break;
                    }
                }
            }
        },
        keyupSupplier(e) {

            if (e.key !== "Enter") {

                this.input_person.number = this.$refs.select_person.$el.getElementsByTagName('input')[0].value
                let exist_persons = this.suppliers.filter((supplier) => {
                    let pos = supplier.description.search(this.input_person.number);
                    return (pos > -1)
                })

                this.input_person.number = (exist_persons.length == 0) ? this.input_person.number : null
            }

        },
        inputSeries() {

            const pattern = new RegExp('^[A-Z0-9]+$', 'i');
            if (!pattern.test(this.form.series)) {
                this.form.series = this.form.series.substring(0, this.form.series.length - 1);
            } else {
                this.form.series = this.form.series.toUpperCase()
            }

        },
        setCurrencyType() {
            this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
        },
        async initRecord() {
            await this.$http.get(`/${this.resource}/record/${this.resourceId}`)
                .then(response => {
                    let dato = response.data.data.purchase
                    this.form.id = dato.id
                    this.form.document_type_id = dato.document_type_id
                    this.form.series = dato.series
                    this.form.number = dato.number
                    this.form.date_of_due = dato.date_of_due
                    this.form.date_of_issue = dato.date_of_issue
                    this.form.supplier_id = dato.supplier_id
                    this.aux_supplier_id = dato.supplier_id
                    this.form.payment_method_type_id = dato.purchase_payments.payment_method_type_id
                    this.form.currency_type_id = dato.currency_type_id
                    this.form.exchange_rate_sale = dato.exchange_rate_sale
                    this.form.items = dato.items
                    this.form.payments = dato.purchase_payments
                    this.form.purchase_payments_id = dato.purchase_payments.id
                    this.form.purchase_order_id = dato.purchase_order_id
                    this.form.customer_id = dato.customer_id
                    this.form.establishment_id = dato.establishment_id

                    this.setCurrencyType()

                    if (this.form.customer_id) {
                        this.searchRemotePersons(dato.customer_number)
                    }

                    // this.form.has_payment = (this.form.payments.length>0) ? true:false
                    this.form.has_client = (this.form.customer_id) ? true : false

                    this.form.payment_condition_id = dato.payment_condition_id
                    this.form.fee = dato.fee
                    this.form.has_payment = (this.form.fee.length > 0 || this.form.payments.length > 0) ? true : false

                    if (this.form.payment_condition_id == '02') this.readonly_date_of_due = true

                    this.changeDocumentType()
                    // this.changePaymentMethodType()
                    this.calculateTotal()

                    // this.calculateTotal()
                })

            await this.getPercentageIgv();
        },
        getPayments(payments) {

            payments.forEach(it => {
                it.payment_destination_id = it.global_payment.destination_type == "App\Models\Tenant\Cash" ? 'cash' : it.global_payment.destination_id
            });

            return payments
        },
        changePaymentMethodType(index) {

            let id = '01'

            if (this.form.payments.length > 0) {
                id = this.form.payments[index].payment_method_type_id
            } else if (this.form.fee.length > 0) {
                id = this.form.fee[index].payment_method_type_id
            }

            let payment_method_type = _.find(this.payment_method_types, {'id': id})

            if (payment_method_type.number_days) {

                this.form.date_of_due = moment(this.form.date_of_issue).add(payment_method_type.number_days, 'days').format('YYYY-MM-DD')
                this.readonly_date_of_due = true

                let date = moment(this.form.date_of_issue).add(payment_method_type.number_days, 'days').format('YYYY-MM-DD')

                if (this.form.fee.length > 0) {
                    for (let index = 0; index < this.form.fee.length; index++) {
                        this.form.fee[index].date = date
                    }
                }

            } else {

                this.form.date_of_due = this.form.date_of_issue
                this.readonly_date_of_due = false

            }

        },
        inputTotalPerception() {
            this.total_amount = parseFloat(this.form.total) + parseFloat(this.form.total_perception)
            if (isNaN(this.total_amount)) {
                this.hide_button = true
            } else {
                this.hide_button = false

            }
        },
        changeSupplier() {
            this.calculatePerception()
        },
        filterSuppliers() {
            if (this.form.document_type_id === '01') {
                this.suppliers = _.filter(this.all_suppliers, (item) => {
                    return ['6', '0'].includes(item.identity_document_type_id)
                })
                this.selectSupplier()

            } else {
                this.suppliers = this.all_suppliers;
                this.selectSupplier()
            }
        },
        selectSupplier() {

            let supplier = _.find(this.suppliers, {'id': this.aux_supplier_id})
            this.form.supplier_id = (supplier) ? supplier.id : null
            this.aux_supplier_id = null

        },
        initForm() {
            this.errors = {}
            this.form = {
                purchase_payments_id: 0,
                id: 0,
                establishment_id: null,
                document_type_id: null,
                series: null,
                number: null,
                date_of_issue: moment().format('YYYY-MM-DD'),
                time_of_issue: moment().format('HH:mm:ss'),
                supplier_id: null,
                payment_method_type_id: '01',
                currency_type_id: null,
                purchase_order: null,
                exchange_rate_sale: 0,
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
                total_taxes: 0,
                total_value: 0,
                total: 0,
                perception_date: null,
                perception_number: null,
                total_perception: 0,
                date_of_due: moment().format('YYYY-MM-DD'),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                payments: [],
                guides: [],
                customer_id: null,
                has_client: false,
                has_payment: false,
                payment_condition_id: '01',
                fee: [],
            }

            // this.clickAddPayment()
            this.initInputPerson()
            this.readonly_date_of_due = false

            this.initGlobalIgv()

        },
        initGlobalIgv() {
            this.localHasGlobalIgv = this.configuration.checked_global_igv_to_purchase
        },
        resetForm() {
            this.initForm()
            this.form.currency_type_id = (this.currency_types.length > 0) ? this.currency_types[0].id : null
            this.form.establishment_id = this.establishment.id
            this.form.document_type_id = (this.document_types.length > 0) ? this.document_types[0].id : null

            this.changeDateOfIssue()
            this.changeDocumentType()
            this.changeCurrencyType()
        },
        changePaymentCondition() {

            this.form.fee = []
            this.form.payments = []

            if (this.form.payment_condition_id === '01') {

                this.clickAddPayment()
                this.initDataPaymentCondition()

            }
            if (this.form.payment_condition_id === '02') {
                this.clickAddFeeNew()
                this.readonly_date_of_due = true
            }
            if (this.form.payment_condition_id === '03') {
                this.clickAddFee()
                this.initDataPaymentCondition()
            }

        },
        async changeDateOfIssue() {
            this.form.date_of_due = this.form.date_of_issue
            await this.searchExchangeRateByDate(this.form.date_of_issue).then(response => {
                this.form.exchange_rate_sale = response
            })
            await this.getPercentageIgv();
            this.changeCurrencyType();
        },
        changeDocumentType() {
            this.filterSuppliers()
        },
        addRow(row) {
            this.form.items.push(row)
            this.calculateTotal()
        },
        clickRemoveItem(index) {
            this.form.items.splice(index, 1)
            this.calculateTotal()
        },
        changeCurrencyType() {
            this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
            let items = []
            this.form.items.forEach((row) => {
                items.push(calculateRowItem(row, this.form.currency_type_id, this.form.exchange_rate_sale, this.percentage_igv))
            });
            this.form.items = items
            this.calculateTotal()
        },
        calculateTotal() {
            let total_discount = 0
            let total_charge = 0
            let total_exportation = 0
            let total_taxed = 0
            let total_exonerated = 0
            let total_unaffected = 0
            let total_free = 0
            let total_igv = 0
            let total_value = 0
            let total = 0

            let total_base_isc = 0
            let total_isc = 0

            this.form.items.forEach((row) => {
                total_discount += parseFloat(row.total_discount)
                total_charge += parseFloat(row.total_charge)

                if (row.affectation_igv_type_id === '10') {
                    total_taxed += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '20') {
                    total_exonerated += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '30') {
                    total_unaffected += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '40') {
                    total_exportation += parseFloat(row.total_value)
                }
                if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) < 0) {
                    total_free += parseFloat(row.total_value)
                }

                total_value += parseFloat(row.total_value)
                total_igv += parseFloat(row.total_igv)
                total += parseFloat(row.total)

                // isc
                total_isc += parseFloat(row.total_isc)
                total_base_isc += parseFloat(row.total_base_isc)

            });

            // isc
            this.form.total_base_isc = _.round(total_base_isc, 2)
            this.form.total_isc = _.round(total_isc, 2)

            this.form.total_exportation = _.round(total_exportation, 2)
            this.form.total_taxed = _.round(total_taxed, 2)
            this.form.total_exonerated = _.round(total_exonerated, 2)
            this.form.total_unaffected = _.round(total_unaffected, 2)
            this.form.total_free = _.round(total_free, 2)
            this.form.total_igv = _.round(total_igv, 2)
            this.form.total_value = _.round(total_value, 2)
            // this.form.total_taxes = _.round(total_igv, 2)

            //impuestos (isc + igv)
            this.form.total_taxes = _.round(total_igv + total_isc, 2)

            this.form.total = _.round(total, 2)

            this.calculatePerception()
            // this.setTotalDefaultPayment()
            this.calculatePayments()
            this.calculateFee()

        },
        calculatePerception() {

            let supplier = _.find(this.all_suppliers, {'id': this.form.supplier_id})

            if (supplier) {

                if (supplier.perception_agent) {

                    let total_perception = 0
                    let quantity_item_perception = 0
                    let total_amount = 0
                    this.form.total_perception = 0

                    this.form.perception_date = moment().format('YYYY-MM-DD')

                    this.form.items.forEach((row) => {
                        quantity_item_perception += (row.item.has_perception) ? 1 : 0
                        total_perception += (row.item.has_perception) ? (parseFloat(row.unit_price) * parseFloat(row.quantity) * (parseFloat(row.item.percentage_perception) / 100)) : 0
                    });

                    this.is_perception_agent = (quantity_item_perception > 0) ? true : false
                    this.form.total_perception = _.round(total_perception, 2)
                    total_amount = this.form.total + parseFloat(this.form.total_perception)
                    this.total_amount = _.round(total_amount, 2)

                } else {

                    this.is_perception_agent = false
                    this.form.perception_date = null
                    this.form.perception_number = null
                    this.form.total_perception = null

                }

            }


        },
        validatePaymentDestination() {

            let error_by_item = 0

            this.form.payments.forEach((item) => {
                if (item.payment_destination_id == null) error_by_item++;
            })

            return {
                error_by_item: error_by_item,
            }

        },
        async submit() {


            let validate = await this.validate_payments()
            if (!validate.success) {
                return this.$message.error(validate.message);
            }

            let validate_payment_destination = await this.validatePaymentDestination()

            if (validate_payment_destination.error_by_item > 0) {
                return this.$message.error('El destino del pago es obligatorio');
            }

            this.loading_submit = true
            // await this.changePaymentMethodType(false)
            await this.$http.post(`/${this.resource}/update`, this.form)
                .then(response => {

                    if (response.data.success) {
                        this.resetForm()
                        this.purchaseNewId = response.data.data.id
                        this.showDialogOptions = true
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        this.$message.error(error.response.data.message)
                    }
                })
                .then(() => {
                    this.loading_submit = false
                })
        },
        close() {
            location.href = '/purchases'
        },
        reloadDataSuppliers(supplier_id) {

            this.$http.get(`/${this.resource}/table/suppliers`).then((response) => {

                this.aux_supplier_id = supplier_id
                this.all_suppliers = response.data
                this.filterSuppliers()

            })
        },
    }
}
</script>