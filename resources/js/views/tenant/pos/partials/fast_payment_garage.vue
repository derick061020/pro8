<template>
    <div class="row col-lg-12 m-0 p-0">
        <Keypress :key-code="113"
                  key-event="keyup"
                  @success="handleFn113"/>

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
        ></table-items>

        <div class="col-12 p-0 fp-payment-panel">

            <!-- Botones de acción (arriba del monto) -->
            <div class="fp-action-row px-3 pt-2 pb-1 d-flex" style="gap:8px">
                <button
                    v-if="!disabledDiscountForSeller"
                    class="fp-action-btn flex-grow-1"
                    :class="{'fp-action-btn--on': enabled_discount}"
                    @click="userTouchedDiscount = true; toggleDiscount()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tag"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6.5 7.5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M3 6v5.172a2 2 0 0 0 .586 1.414l7.71 7.71a2.41 2.41 0 0 0 3.408 0l5.592 -5.592a2.41 2.41 0 0 0 0 -3.408l-7.71 -7.71a2 2 0 0 0 -1.414 -.586h-5.172a3 3 0 0 0 -3 3" /></svg>
                    {{ enabled_discount ? 'Quitar Dto.' : 'Descuento' }}
                </button>
                <button class="fp-action-btn flex-grow-1" @click="clickAddPayment()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" /><path d="M7 10a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1l0 -8" /><path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /></svg>
                    Pagos
                </button>
                <button v-if="businessTurns.active" class="fp-action-btn" @click="openPlateNumberDialog" title="Agregar Placa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6m-6 -6h15m-6 0v-5"/></svg>
                </button>
            </div>

            <!-- Monto + descuento + vuelto -->
            <div class="fp-amount-row px-1 pt-1 pb-1 d-flex align-items-end" style="gap:8px">
                <div class="flex-grow-1 fp-field-wrap" v-if="enabled_discount">
                    <label class="fp-field-label mb-0">
                        Descuento ({{ discount_type === '01' ? currencyTypeActive.symbol : '%' }})
                    </label>
                    <div class="position-relative">
                        <span class="fp-sym">{{ discount_type === '01' ? currencyTypeActive.symbol : '%' }}</span>
                        <el-input
                            v-model="discount_amount"
                            size="small"
                            min="0"
                            @input="userTouchedDiscount = true; inputDiscountAmount()"
                            @blur="normalizeDiscountAmount()"
                            class="fp-amount-inp"
                        />
                    </div>
                </div>
                <div class="flex-grow-1 fp-field-wrap">
                    <label class="fp-field-label mb-0">Ingrese monto</label>
                    <div class="position-relative">
                        <span class="fp-sym">{{ currencyTypeActive.symbol }}</span>
                        <el-input
                            ref="enter_amount"
                            v-model="enter_amount"
                            size="small"
                            @input="userTouchedAmount = true; enterAmount()"
                            @keyup.enter.native="keyupEnterAmount()"
                            class="fp-amount-inp"
                        />
                    </div>
                </div>
                <div class="fp-change-wrap text-center">
                    <small class="fp-field-label d-block" :class="difference < 0 ? 'text-danger' : 'text-success'">
                        {{ difference < 0 ? 'Faltante' : 'Vuelto' }}
                    </small>
                    <span class="fp-change-val" :class="difference < 0 ? 'text-danger' : 'text-success'">
                        {{ currencyTypeActive.symbol }} {{ isNaN(parseFloat(difference)) ? difference : Number(difference).toFixed(2) }}
                    </span>
                </div>
            </div>

            <!-- Totales -->
            <div class="fp-totals px-3 pt-2">
                <template v-if="form.total_plastic_bag_taxes > 0">
                    <div class="fp-total-row d-flex justify-content-between py-1">
                        <span class="fp-total-label">Subtotal</span>
                        <span class="fp-total-val">{{ currencyTypeActive.symbol }} {{ Number(form.total_taxed).toFixed(2) }}</span>
                    </div>
                    <div class="fp-total-row d-flex justify-content-between py-1" v-if="!isNrus">
                        <span class="fp-total-label">IGV (18%)</span>
                        <span class="fp-total-val">{{ currencyTypeActive.symbol }} {{ Number(form.total_igv).toFixed(2) }}</span>
                    </div>
                    <div class="fp-total-row d-flex justify-content-between py-1">
                        <span class="fp-total-label">Descuento</span>
                        <span class="fp-total-val text-danger">{{ currencyTypeActive.symbol }} {{ Number(form.total_discount).toFixed(2) }}</span>
                    </div>
                    <div class="fp-total-row d-flex justify-content-between py-1">
                        <span class="fp-total-label">ICBPER</span>
                        <span class="fp-total-val">{{ currencyTypeActive.symbol }} {{ Number(form.total_plastic_bag_taxes).toFixed(2) }}</span>
                    </div>
                </template>
                <template v-else>
                    <div class="fp-total-row d-flex justify-content-between py-1">
                        <span class="fp-total-label">Subtotal</span>
                        <span class="fp-total-val">{{ currencyTypeActive.symbol }} {{ Number(form.total_taxed).toFixed(2) }}</span>
                    </div>
                    <div class="fp-total-row d-flex justify-content-between py-1" v-if="!isNrus">
                        <span class="fp-total-label">IGV (18%)</span>
                        <span class="fp-total-val">{{ currencyTypeActive.symbol }} {{ Number(form.total_igv).toFixed(2) }}</span>
                    </div>
                    <div class="fp-total-row d-flex justify-content-between py-1">
                        <span class="fp-total-label">Descuento</span>
                        <span class="fp-total-val text-danger">{{ currencyTypeActive.symbol }} {{ Number(form.total_discount).toFixed(2) }}</span>
                    </div>
                </template>
                <!-- Gran total -->
                <div class="fp-grand d-flex justify-content-between align-items-center pt-2 pb-1">
                    <span class="fp-grand-label">Total</span>
                    <span class="fp-grand-amount">{{ currencyTypeActive.symbol }} {{ Number(form.total).toFixed(2) }}</span>
                </div>
            </div>

            <!-- Botón Pagar -->
            <div class="px-1 pt-1 pb-1">
                <el-button
                    :disabled="button_payment"
                    :loading="loading_submit"
                    class="fp-pay-btn w-100"
                    @click="clickPayment"
                >
                    Pagar
                </el-button>
            </div>

            <!-- Cancelar -->
            <div class="px-1 pb-2">
                <button class="fp-cancel-btn btn btn-link w-100" @click="clickCancel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash-x"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7h16" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /><path d="M10 12l4 4m0 -4l-4 4" /></svg>
                    Cancelar Venta
                </button>
            </div>

            <!-- Dialog Placa -->
            <template>
                <el-dialog
                    title="Agregar Placa"
                    :visible.sync="showDialogPlateNumber"
                    width="30%"
                    :close-on-click-modal="false">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="control-label mb-0">Número de Placa</label>
                                <template v-if="config_tap.save_plates_client">
                                    <a v-if="!btn_save_plates" href="#" @click.prevent="btn_save_plates = true">[+ Nuevo]</a>
                                    <a v-else href="#" @click.prevent="btn_save_plates = false">[ Cancelar]</a>
                                </template>
                                <template v-if="!config_tap.save_plates_client">
                                    <el-input v-model="form.plate_number" type="text">
                                        <el-tooltip slot="append" placement="right" v-if="btn_save_plates">
                                            <div slot="content" v-html="messageBoxPlate"></div>
                                            <el-button :disabled="!Boolean(form.customer_id)" @click="savePlates" icon="el-icon-folder-add"></el-button>
                                        </el-tooltip>
                                    </el-input>
                                </template>
                                <template v-else>
                                    <el-select v-if="!btn_save_plates" v-model="form.plate_number" filterable placeholder="Seleccione Placa">
                                        <el-option v-for="option in current_plates" :key="option" :label="option.value" :value="option.value"></el-option>
                                    </el-select>
                                    <el-input v-else v-model="form.plate_number" type="text">
                                        <el-tooltip slot="append" placement="right" v-if="btn_save_plates">
                                            <div slot="content" v-html="messageBoxPlate"></div>
                                            <el-button :disabled="!Boolean(form.customer_id)" @click="savePlates" icon="el-icon-folder-add"></el-button>
                                        </el-tooltip>
                                    </el-input>
                                </template>
                            </div>
                        </div>
                    </div>
                    <span slot="footer" class="dialog-footer">
                        <el-button class="second-buton" @click="showDialogPlateNumber = false">Cancelar</el-button>
                        <el-button type="primary" @click="closeDialogPlateNumber">Aceptar</el-button>
                    </span>
                </el-dialog>
            </template>
        </div>
        <person-form
                :showDialog.sync="showDialogNewPerson"
                type="customers"
                :input_person="input_person"
                :external="true"
                :document_type_id="form.document_type_id"
        ></person-form>
        <options-form
            :recordId="documentNewId"
            :resource="resource_options"
            :showDialog.sync="showDialogOptions"
            :statusDocument="statusDocument"
        ></options-form>

        <multiple-payment-form
            :payments="payments"
            :showDialog.sync="showDialogMultiplePayment"
            :total="form.total"
            @add="addRow"
            ref="componentMultiplePaymentGarage"
        ></multiple-payment-form>

        <history-sales-form
            :showDialog.sync="showDialogHistorySales"
            :item_id="history_item_id"
            :customer_id="form.customer_id"
        ></history-sales-form>

        <!-- <sale-notes-options :showDialog.sync="showDialogSaleNote"
                          :recordId="saleNotesNewId"
                          :showClose="true"></sale-notes-options>  -->

        <card-brands-form :external="true"
                          :recordId="null"
                          :showDialog.sync="showDialogNewCardBrand"></card-brands-form>
    </div>
</template>
<style>
.c-width {
    margin-right: 0 !important;
    padding: 0 !important;
    width: 80px !important;
}

/* ── POS · Venta Rápida — Panel de pago ───────────────────────
   Los estilos `.fp-*` (panel de pago: tabs documento, totales,
   botón Pagar, Cancelar, Descuento/Pagos, montos) viven en
   storage/app/public/skins/tu-quipu.css
   No duplicar aquí. */

.el-radio-button--small .el-radio-button__inner {
    padding: 9px 6px;
    font-size: 10px;
}

.el-radio-button {
    margin-bottom: 0px;
}
</style>

<script>
import Keypress from 'vue-keypress'

import CardBrandsForm from '../../card_brands/form.vue'
import SaleNotesOptions from '../../sale_notes/partials/options.vue'
import OptionsForm from './options.vue'
import MultiplePaymentForm from './multiple_payment_garage.vue'
import PersonForm from '../../../tenant/persons/form.vue'
import { buhoprinter } from '@mixins/buhoprinter'

export default {
    components: {OptionsForm, CardBrandsForm, SaleNotesOptions, MultiplePaymentForm, Keypress, PersonForm},
    mixins: [buhoprinter],

    props: [
        'form',
        'customer',
        'currencyTypeActive',
        'exchangeRateSale',
        'is_payment',
        'soapCompany',
        'businessTurns',
        'isPrint',
        'rowsItems',
        'configuration',
        'typeUser',
        'customer_email',
        'config'
    ],
    data() {
        return {
            showDialogPlateNumber: false,
            messageBoxPlate: '',
            btn_save_plates: false,
            current_plates: [],
            enabled_discount: false,
            discount_amount: 0,
            discount_type: '01',
            loading_submit: false,
            showDialogOptions: false,
            showDialogMultiplePayment: false,
            showDialogSaleNote: false,
            showDialogNewCardBrand: false,
            documentNewId: null,
            printTicketUrl: null,
            saleNotesNewId: null,
            resource_options: null,
            has_card: false,
            resource: 'pos',
            showDialogNewPerson: false,
            resource_documents: 'documents',
            resource_payments: 'document_payments',
            amount: 0,
            enter_amount: 0,
            difference: 0,
            button_payment: false,
            input_item: '',
            form_payment: {},
            input_person: {},
            series: [],
            all_series: [],
            cards_brand: [],
            cancel: false,
            form_cash_document: {},
            statusDocument: {},
            payment_method_types: [],
            payments: [],
            locked_submit: false,
            all_customers: [],
            focusClienteSelect: false,
            loading_submit_cancel: false,
            show_discount: false,
            userSelectedDocType: false,
            userTouchedDiscount: false,
            userTouchedAmount: false,
        }
    },
    watch: {
        series(newSeries) {
            this.$emit('series-filtered', newSeries.length);
        },
        customer: {
            handler(valueNew, valueOld) {
                if (!_.isNull(valueNew)) {
                    if (!this.userTouchedDiscount) {
                        this.enabled_discount = valueNew.has_discount;
                        this.discount_type = valueNew.discount_type;
                        this.discount_amount = valueNew.discount_amount;
                    }
                } else {
                    this.enabled_discount = false;
                    this.discount_type = '01';
                    this.discount_amount = 0;
                    this.userTouchedDiscount = false;
                }

                const index = this.all_customers.findIndex(cliente => cliente.id === valueNew.id);
                if (index === -1) {
                    this.all_customers.push(valueNew)
                    this.form.customer_id = (valueNew) ? valueNew.id : null;
                }
                this.inputDiscountAmount();
            }
        },
    },
    async created() {

        await this.initLStoPayment()
        await this.getTables()
        this.initFormPayment()
        this.inputAmount()
        this.form.payments = []
        this.$eventHub.$on('reloadDataCardBrands', (card_brand_id) => {
            this.reloadDataCardBrands(card_brand_id)
        })

        this.$eventHub.$on('localSPaymentsGarage', (payments) => {
            this.payments = payments
        })

        this.events();

        await this.setInitialAmount()

        await this.getFormPosLocalStorage()
        // console.log(this.form.payments, this.payments)
        // La conexión directa con BuhoPrinter ya no es necesaria desde el frontend.
        // La impresión se centraliza vía PrintOrder → Redis → BuhoPrinter agent.
        // if (!this.isBuhoActive && this.isPrint) {
        //     this.startConnectionBuho();
        // }
    },
    mounted() {
        // console.log(this.currencyTypeActive)
        this.checkPaymentGarage()
    },
    computed: {
        isNrus()
        {
            return !!((this.configuration && this.configuration.is_nrus) || (this.config && this.config.is_nrus));
        },
        disabledDiscountForSeller()
        {
            return this.configuration.restrict_seller_discount && this.typeUser === 'seller';
        }
    },
    methods: {
        openPlateNumberDialog() {
            this.showDialogPlateNumber = true;
            let customer = _.find(this.all_customers, {
                id: this.form.customer_id
            });

            if (!customer) {
                this.messageBoxPlate =  'Debe seleccionar un cliente para guardar la placa.'
            } else {
                this.messageBoxPlate = `Se guardara la placa al cliente: ${customer.name}.`
                this.getPlates()
            }

            this.$nextTick(() => {
                this.$refs.plateNumberInput.focus();
            });
        },
        closeDialogPlateNumber() {
            this.showDialogPlateNumber = false;
            // Enfocar el campo de monto después de cerrar
            this.$nextTick(() => {
                this.$refs.enter_amount.$el.getElementsByTagName('input')[0].focus();
            });
        },
        toggleDiscount() {
            this.enabled_discount = !this.enabled_discount;
            this.changeEnabledDiscount();
        },
        clickDeleteCustomer() {
            this.form.customer_id = null;
            this.messageBoxPlate = '';
            this.customer = null;
            this.userSelectedDocType = false;
            this.setLocalStorageIndex("customer", null);
            this.setFormPosLocalStorage();
        },
        setLocalStorageIndex(key, obj) {
            localStorage.setItem(key, JSON.stringify(obj));
        },
        clickHistorySales(item_id) {
            if (!this.form.customer_id)
                return this.$message.error("Debe seleccionar el cliente");

            this.history_item_id = item_id;
            this.showDialogHistorySales = true;
            // console.log(item)
        },
        changeCustomer() {
            // console.log('clien 13')

            let customer = _.find(this.all_customers, {
                id: this.form.customer_id
            });
            this.customer = customer;

            // Solo cambia el tipo de documento si el usuario no ha seleccionado manualmente
            if (!this.userSelectedDocType) {
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
            }

            // console.log(this.customer);

            //if (this.$refs.componentFastPaymentGarage)
            //    this.$refs.componentFastPaymentGarage.filterSeries();

            this.filterSeries();

            if (!this.form.customer_id) {
                this.userSelectedDocType = false;
            }

            this.setLocalStorageIndex("customer", this.customer);
            this.setFormPosLocalStorage();
        },
        selectDefaultCustomer() {
            if (this.establishment.customer_id && !this.form.customer_id) {
                this.form.customer_id = this.establishment.customer_id;
                this.changeCustomer();
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
        keyupEnterCustomer() {
            if (this.place == "cat3") {
                return false;
            }

            if (this.form.customer_id) {
                this.clickPayment();
                return;
            }

            this.openDialogNewPerson();
        },
        handleFn113() {
            const code = this.form.document_type_id
            if (this.isNrus) {
                // En NRUS solo Boleta (03) y N. Venta (80); se omite Factura
                this.form.document_type_id = (code == '03') ? '80' : '03'
                this.filterSeries()
                return
            }
            if (code == '01') {
                this.form.document_type_id = '03'
            } else if (code == '03') {
                this.form.document_type_id = '80'
            } else if (code == '80') {
                this.form.document_type_id = '01'
            }

            this.filterSeries()
        },
        handleFn115() {
            this.openDialogNewPerson();
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
        keyupEnterAmount() {

            if (this.button_payment) {
                return this.$message.warning("El monto a pagar es menor al total")
            }

            if (this.locked_submit) return;

            this.clickPayment()

        },
        async setInitialAmount() {
            this.enter_amount = this.form.total
            // this.form.payments = this.payments
            // this.$eventHub.$emit('eventSetFormPosLocalStorage', this.form)
            await this.$refs.enter_amount.$el.getElementsByTagName('input')[0].focus()
            await this.$refs.enter_amount.$el.getElementsByTagName('input')[0].select()
            // console.log(this.$refs.enter_amount.$el.getElementsByTagName('input')[0])
        },
        changeEnabledDiscount() {
            if (!this.enabled_discount) {
                this.discount_amount = 0
                this.deleteDiscountGlobal()
                this.reCalculateTotal()
            }
        },
        normalizeDiscountAmount() {
            if (this.discount_amount === '' || this.discount_amount === null || isNaN(parseFloat(this.discount_amount))) {
                this.discount_amount = 0
                this.inputDiscountAmount()
            }
        },
        inputDiscountAmount() {
            if (this.enabled_discount) {
                if (this.discount_amount && !isNaN(this.discount_amount) && parseFloat(this.discount_amount) > 0) {
                    if (this.discount_amount >= this.form.total) {
                        return this.$message.error("El monto de descuento debe ser menor al total de venta")
                    }
                }
            }
            this.deleteDiscountGlobal()
            this.reCalculateTotal()
        },
        isExonerated() {

            let not_exonerated = this.form.items.find((item) => {
                return item.affectation_igv_type_id != '20'
            })

            return (not_exonerated) ? false : true
        },
        async discountGlobal() {
            const existingIdx = this.form.discounts.findIndex(d => d.discount_type_id === '03')
            if (existingIdx > -1) {
                this.form.discounts.splice(existingIdx, 1)
            }
            this.form.total_discount = 0

            let base = parseFloat(this.form.total)
            let global_discount = parseFloat(this.discount_amount)

            if (this.discount_type === '02') {
                global_discount = _.round(base * global_discount / 100, 2);
            }

            if (!isNaN(global_discount) && global_discount > 0) {

                let amount = global_discount
                let factor = _.round(amount / base, 5)

                this.form.total_discount = _.round(amount, 2)
                this.form.total = _.round(this.form.total - amount, 2)

                this.form.discounts.push({
                    discount_type_id: '03',
                    description: 'Descuentos globales que no afectan la base imponible del IGV/IVAP',
                    factor: factor,
                    amount: amount,
                    base: base
                })

            }

            if (!this.userTouchedAmount) {
                this.enter_amount = this.form.total
                this.syncEnterAmount()
                return
            }

            this.difference = this.enter_amount - this.form.total
        },
        syncEnterAmount() {
            const value = parseFloat(this.enter_amount) || 0

            if (this.form.payments && this.form.payments.length > 0) {
                const ind = this.form.payments.length - 1
                this.form.payments[ind].payment = value

                const r_item = _.last(this.payments)
                if (r_item) {
                    r_item.payment = value
                }

                let acum_payment = 0
                this.form.payments.forEach((item) => {
                    acum_payment += parseFloat(item.payment)
                })
                this.amount = acum_payment
            } else {
                this.amount = value
            }

            this.inputAmount()
        },
        reCalculateTotal() {

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
            let total_plastic_bag_taxes = 0
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
                if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) > -1) {
                    total_igv += parseFloat(row.total_igv)
                    total += parseFloat(row.total)
                }
                total_value += parseFloat(row.total_value)
                total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes)

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
            this.form.total_taxes = _.round(total_igv + total_isc, 2);

            this.form.total_plastic_bag_taxes = _.round(total_plastic_bag_taxes, 2)
            // this.form.total = _.round(total, 2)
            this.form.subtotal = _.round(total + this.form.total_plastic_bag_taxes, 2)
            this.form.total = _.round(total + this.form.total_plastic_bag_taxes, 2)

            this.discountGlobal()


        },
        deleteDiscountGlobal() {

            let discount = _.find(this.form.discounts, {'discount_type_id': '03'})
            let index = this.form.discounts.indexOf(discount)
            // let is_exonerated = this.isExonerated()

            if (index > -1) {
                this.form.discounts.splice(index, 1)
                this.form.total_discount = 0
                // this.setDiscountByItem(0, is_exonerated)
            }

        },
        back() {
            this.$emit('update:is_payment', false)
        },
        async initLStoPayment() {

            this.amount = await this.getLocalStoragePayment('amount', 0)
            this.enter_amount = await this.getLocalStoragePayment('enter_amount', 0)
            this.difference = await this.getLocalStoragePayment('difference', 0)
        },
        getFormPosLocalStorage() {

            let form_pos = localStorage.getItem('form_pos_garage');
            form_pos = JSON.parse(form_pos)
            if (form_pos) {
                this.form.payments = form_pos.payments
            }

        },
        clickAddPayment() {

            // Si ya hay pagos registrados, conservarlos al reabrir el diálogo
            if (this.form.payments && this.form.payments.length > 0) {
                this.payments = this.form.payments;
                this.showDialogMultiplePayment = true;
                this.$nextTick(() => {
                    if (this.$refs.componentMultiplePaymentGarage) {
                        this.$refs.componentMultiplePaymentGarage.$data.form.payment = this.form.total;
                    }
                });
                return;
            }

            // Primera vez: arrancar con un pago por defecto
            this.payments = [];
            this.showDialogMultiplePayment = true;

            this.$nextTick(() => {
                if (this.$refs.componentMultiplePaymentGarage) {
                    this.$refs.componentMultiplePaymentGarage.$data.form.payment = this.form.total;
                    this.$refs.componentMultiplePaymentGarage.clickAddPayment(this.form.total);
                }
            });
        },
        reloadDataCardBrands(card_brand_id) {
            this.$http.get(`/${this.resource}/table/card_brands`).then((response) => {
                this.cards_brand = response.data
                this.form_payment.card_brand_id = card_brand_id
                this.changePaymentMethodType()
            })
        },
        getDescriptionPaymentMethodType(id) {
            let payment_method_type = _.find(this.payment_method_types, {'id': id})
            return (payment_method_type) ? payment_method_type.description : ''

        },
        changePaymentMethodType() {
            let payment_method_type = _.find(this.payment_method_types, {'id': this.form_payment.payment_method_type_id})
            this.has_card = payment_method_type.has_card
            this.form_payment.card_brand_id = (payment_method_type.has_card) ? this.form_payment.card_brand_id : null
        },
        addRow(payments) {

            this.form.payments = payments
            let acum_payment = 0

            this.form.payments.forEach((item) => {
                acum_payment += parseFloat(item.payment)
            })

            // this.amount = acum_payment
            this.setAmount(acum_payment)

            // console.log(this.form.payments)
        },
        setAmount(amount) {
            // this.amount = parseFloat(this.amount) + parseFloat(amount)
            this.amount = parseFloat(amount) //+ parseFloat(amount)
            this.enter_amount = parseFloat(amount) //+ parseFloat(amount)
            this.inputAmount()
        },
        setAmountCash(amount) {
            let row = _.last(this.payments, {'payment_method_type_id': '01'})
            row.payment = parseFloat(row.payment) + parseFloat(amount)
            // console.log(row.payment)

            this.form.payments = this.payments
            let acum_payment = 0

            this.form.payments.forEach((item) => {
                acum_payment += parseFloat(item.payment)
            })

            this.setAmount(acum_payment)

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
        async enterAmount() {

            let r_item = await _.last(this.payments, {'payment_method_type_id': '01'})
            r_item.payment = await parseFloat(this.enter_amount)
            // console.log(r_item.payment)

            let ind = this.form.payments.length - 1
            this.form.payments[ind].payment = parseFloat(this.enter_amount)
            // this.setAmount(item.payment)

            let acum_payment = 0

            await this.form.payments.forEach((item) => {
                acum_payment += parseFloat(item.payment)
            })
            // console.log(this.form.payments)

            // this.amount = item.payment
            this.amount = acum_payment
            // this.amount = this.enter_amount
            // console.log(this.amount)
            this.difference = this.amount - this.form.total

            if (isNaN(this.difference)) {
                this.button_payment = true
                this.difference = "-"
            } else if (this.difference >= 0) {
                this.button_payment = false
                this.difference = this.amount - this.form.total
            } else {
                this.button_payment = true
            }
            this.difference = _.round(this.difference, 2)

            this.$eventHub.$emit('eventSetFormPosLocalStorageGarage', this.form)

            await this.lStoPayment()

        },
        getLocalStoragePayment(key, re_default = null) {

            let ls_obj = localStorage.getItem(key + '_garage');
            ls_obj = JSON.parse(ls_obj)

            if (ls_obj) {
                return ls_obj
            }

            return re_default
        },
        setLocalStoragePayment(key, obj) {
            const ky = `${key}_garage`
            localStorage.setItem(ky, JSON.stringify(obj));
        },
        inputAmount() {

            this.difference = this.amount - this.form.total

            if (isNaN(this.difference)) {
                this.button_payment = true
                this.difference = "-"
            } else if (this.difference >= 0) {
                this.button_payment = false
                this.difference = this.amount - this.form.total
            } else {
                this.button_payment = true
            }
            this.difference = _.round(this.difference, 2)
            // this.form_payment.payment = this.amount

            this.$eventHub.$emit('eventSetFormPosLocalStorageGarage', this.form)
            this.lStoPayment()

        },
        lStoPayment() {

            this.setLocalStoragePayment('enter_amount', this.enter_amount)
            this.setLocalStoragePayment('amount', this.amount)
            // console.log(this.amount)
            this.setLocalStoragePayment('difference', this.difference)

        },
        initFormPayment() {

            this.difference = -this.form.total

            this.form_payment = {
                id: null,
                date_of_payment: moment().format('YYYY-MM-DD'),
                payment_method_type_id: '01',
                reference: null,
                card_brand_id: null,
                document_id: null,
                sale_note_id: null,
                payment: this.form.total,
            }

            /*
            this.form_cash_document = {
                document_id: null,
                sale_note_id: null
            }
            */

            this.initFormCashDocument()

        },
        initFormCashDocument()
        {
            this.form_cash_document = {
                document_id: null,
                sale_note_id: null
            }
        },
        filterSeries() {
            this.userSelectedDocType = true;

            this.form.series_id = null
            this.series = _.filter(this.all_series, {'document_type_id': this.form.document_type_id});
            this.form.series_id = (this.series.length > 0) ? this.series[0].id : null

            if (!this.form.series_id) {
                return this.$message.warning('El sucursal no tiene series disponibles para el comprobante');
            }
        },
        async clickCancel() {

            try {
                await this.$confirm(
                    '¿Desea cancelar la venta? Se eliminarán todos los productos, el cliente seleccionado y los pagos ingresados.',
                    'Cancelar Venta',
                    {
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No',
                        type: 'warning'
                    }
                )
            } catch (e) {
                return
            }

            this.loading_submit_cancel = true
            await this.sleep(400);
            this.loading_submit_cancel = false

            this.enabled_discount = false
            this.discount_amount = 0
            this.discount_type = '01'
            this.userTouchedDiscount = false
            this.enter_amount = 0
            this.amount = 0
            this.userTouchedAmount = false
            this.difference = 0
            this.payments = []
            this.cleanLocalStoragePayment()

            this.$eventHub.$emit('cancelSaleGarage')
            this.$message.success('Venta cancelada')

        },
        async events() {
            await this.$eventHub.$on("initInputPerson", () => {
                this.initInputPerson();
            });

            await this.$eventHub.$on("reloadDataPersons", customer_id => {
                this.reloadDataCustomers(customer_id);
                this.setFormPosLocalStorage();
            });

            await this.$eventHub.$on("eventCheckPaymentGarage", () => {
                this.checkPaymentGarage()
            })
        },
        cleanLocalStoragePayment() {

            this.setLocalStoragePayment('amount', null)
            this.setLocalStoragePayment('enter_amount', null)
            this.setLocalStoragePayment('difference', null)
        },
        sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        },
        async asignPlateNumberToItems() {
            if (this.form.plate_number) {

                await this.form.items.forEach(item => {

                    let at = _.find(item.attributes, {'attribute_type_id': '5010'})

                    if (!at) {
                        item.attributes.push({
                            attribute_type_id: '7000',
                            description: "Gastos Art. 37 Renta:  Número de Placa",
                            value: this.form.plate_number,
                            start_date: null,
                            end_date: null,
                            duration: null,
                        })
                    }
                });
            }
        },
        cleanPayments() {
            this.payments = []
        },
        initDataComponent() {
            this.cleanPayments()
            // this.filterSeries()
        },
        async autoSendPdfMail() {
            if (!this.config.auto_send_pdf_email) return;

            if (!this.customer_email) {
                this.$message.warning('El cliente no tiene correo registrado.');
                return;
            }

            this.$http.post(`/${this.resource_documents}/email`, {
                customer_email: this.customer_email,
                id: this.documentNewId
            }).then(response => {
                if (response.data.success) {
                    this.$message.success('El correo fue enviado satisfactoriamente');
                } else {
                    this.$message.error('Error al enviar el correo');
                }
            }).catch(() => {
                this.$message.error('Error al enviar el correo');
            });
        },
        async clickPayment() {
            // if(this.has_card && !this.form_payment.card_brand_id) return this.$message.error('Seleccione una tarjeta');

            if (this.rowsItems < 1) {
                return this.$message.warning('Debe agregar productos antes de pagar');
            }

            if (!this.form.customer_id) {
                this.$emit('customer-required');
                return this.$message.warning('Debe seleccionar un cliente');
            }

            if (this.form.document_type_id === '01') {
                const customer = _.find(this.all_customers, { id: this.form.customer_id });
                if (customer && customer.identity_document_type_id !== '6') {
                    return this.$message.warning('Para emitir factura el cliente debe tener RUC');
                }
            }

            if (this.businessTurns.active) {
                if (this.form.document_type_id == '01' && !this.form.plate_number) {
                    return this.$message.warning('Debe ingresar placa');
                }
            }

            if (this.button_payment) {
                return this.$message.warning('El monto a pagar es menor al total');
            }

            let unit_type_notAllowed = ['ZZ','NIU'];

            let errorZeroQuantity = false
            let errorFloatQuantity = false
            let existError = this.form.items.some(item => {
                if (Number(item.quantity) == 0) {
                    errorZeroQuantity = true
                    return true;
                }

                if (unit_type_notAllowed.includes(item.unit_type_id) && !Number.isInteger(Number(item.quantity))) {
                    errorFloatQuantity =  true
                    return  true
                }
                return item.quantity == 0 ? true : false
            });

            if(existError) {
                if (errorZeroQuantity) {
                    this.$message.error('Los productos deben tener cantidades mayor a 0');
                }
                if (errorFloatQuantity) {
                    this.$message.error('El producto con ese tipo de unidad no permite cantidad en decimales');
                }

                return
            }

            if (!moment(moment().format("YYYY-MM-DD")).isSame(this.form.date_of_issue)) {
                return this.$message.error('La fecha de emisión no coincide con la del día actual');
            }

            if (!this.form.series_id) {
                return this.$message.warning('El sucursal no tiene series disponibles para el comprobante');
            }

            if (this.form.document_type_id === "80") {
                this.form.prefix = "NV";
                this.form.paid = 1;
                this.resource_documents = "sale-notes";
                this.resource_payments = "sale_note_payments";
                this.resource_options = this.resource_documents;
            } else {
                this.form.prefix = null;
                this.resource_documents = "documents";
                this.resource_payments = "document_payments";
                this.resource_options = this.resource_documents;
                await this.asignPlateNumberToItems()
            }

            if (this.configuration.show_terms_condition_pos) {
                this.form.terms_condition = this.configuration.terms_condition_sale;
            }

            this.loading_submit = true
            this.locked_submit = true

            await this.$http.post(`/${this.resource_documents}`, this.form).then(async (response) => {
                let response_sent = null
                if (response.data.success) {


                    if (this.form.document_type_id === "80") {

                        // this.form_payment.sale_note_id = response.data.data.id;
                        this.form_cash_document.sale_note_id = response.data.data.id;
                        this.documentNewId  = response.data.data.id;
                    } else {
                        this.documentNewId = response.data.data.id;

                        if(this.configuration.send_auto && this.form.document_type_id === '01') {
                            response_sent = await this.sendDocument(this.documentNewId);
                        } else if (this.configuration.ticket_single_shipment && this.form.document_type_id === '03') {
                            response_sent = await this.sendDocument(this.documentNewId);
                        }

                        // this.form_payment.document_id = response.data.data.id;
                        this.form_cash_document.document_id = response.data.data.id;

                    }

                    // Almacena la URL del PDF de ticket para impresión con BuhoPrinter
                    this.printTicketUrl = response.data?.links?.print_ticket ?? null;
                    this.autoSendPdfMail();
                    this.showDialogOptions = true;

                    // this.savePaymentMethod();
                    this.saveCashDocument();

                    // this.initFormPayment() ;
                    this.cleanLocalStoragePayment()
                    if (this.isPrint) {
                        this.gethtml();
                    }
                    this.$eventHub.$emit('saleSuccess');

                    this.initDataComponent()

                } else {
                    this.$message.error(response.data.message);
                }
            }).catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data;
                } else {
                    this.$message.error(error.response.data.message);
                }
            }).then(() => {
                this.loading_submit = false;
                this.locked_submit = false
            });
        },
        gethtml() {
            this.form.datahtml = "";
            var doc = 'salenote';
            var route = `/printticket/document/${this.documentNewId}/ticket`;
            if (this.resource_documents !== 'documents') {
                route = `/sale-notes/ticket/${this.documentNewId}/ticket`;
            }

            // console.log(route);

            this.$http.get(route)
                .then(response => {
                    if (response.data.length > 0) {
                        this.form.datahtml = response.data;
                        this.printticket();
                    }

                })
                .catch(error => {
                    console.log(error);
                })
        },
        async printticket() {
            //getUpdatedConfig();
            await this.sleep(400);
            const url = this.printTicketUrl;
            if (url) {
                // Centraliza la impresión vía backend → Redis → BuhoPrinter agent
                await this.printDocument(url, this.configuration?.printer_name_documents);
            } else {
                console.warn('[BuhoPrinter] print_ticket URL no disponible.');
            }
        },
        saveCashDocument() {
            this.$http.post(`/cash/cash_document`, this.form_cash_document)
                .then(response => {
                    if (response.data.success) {
                        // console.log(response)
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.log(error);
                })
                .finally(() => {
                    this.initFormCashDocument()
                })
        },
        savePaymentMethod() {
            this.$http.post(`/${this.resource_payments}`, this.form_payment)
                .then(response => {
                    if (response.data.success) {
                        // console.log(response)
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.records[index].errors = error.response.data;
                    } else {
                        console.log(error);
                    }
                })
        },
        async getTables() {
            await this.$http.get(`/${this.resource}/payment_tables`)
                .then(response => {
                    this.all_series = response.data.series
                    this.payment_method_types = response.data.payment_method_types
                    this.cards_brand = response.data.cards_brand
                    this.filterSeries()
                });

            await this.$http.get(`/${this.resource}/tables`).then(response => {
                //this.all_items = response.data.items;
                this.config_tap = response.data.config_tap || {};
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
                // this.renderCategories(response.data.categories);
                // this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
                // this.changeCurrencyType();
                //this.filterItems();
                // this.changeDateOfIssue();
                // this.changeExchangeRate();
            });

        },
        initForm() {
            this.form = {
                customer_id: null
            };

            this.initFormItem();
            // this.changeDateOfIssue();
            this.initInputPerson();
        },
        initInputPerson() {
            this.input_person = {
                number: "",
                identity_document_type_id: ""
            };
        },
        async sendDocument(id)
        {
            return await this.$http
                .get(`/documents/send/${id}`)

        },
        checkPaymentGarage(total = null) {
            let amount = total ? total: this.form.total

            if (!this.form.items || this.form.items.length === 0) {
                this.userTouchedAmount = false
                this.enter_amount = 0
            }

            this.inputDiscountAmount()

            if (!this.form.items || this.form.items.length < 1) {
                this.form.payments = [];
                this.payments = [];
                this.setAmount(0);
                return;
            }

            if (this.form.payments.length == 0) {
                this.$refs.componentMultiplePaymentGarage.clickAddPayment(amount)
                this.setAmount(effectiveAmount)
            } else if (this.form.payments.length == 1) {

                this.form.payments[0].payment = effectiveAmount

                if (this.payments.length == 0) {
                    this.payments = this.form.payments
                }

                this.setAmount(effectiveAmount)

            } else {
                // multiples pagos no controlados
            }

        },
        savePlates()
        {
            this.$http
                .post(`\\bussiness_turns\\plates`, {
                    plates: this.form.plate_number,
                    person_id: this.form.customer_id
                })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getPlates()
                    }
                })
                .finally(() => {
                    this.btn_save_plates = false
                });
        },
        getPlates()
        {
            this.$http
                .get(`\\bussiness_turns\\plates\\${this.form.customer_id}`)
                .then((response) => {
                    if (response.data.success) {
                        this.current_plates = response.data.plates
                    }
                });
        }
    }
}
</script>
