<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
        :close-on-click-modal="false"
        width="70%">
        <form
            autocomplete="off"
            @submit.prevent="submit">
            <el-tabs v-model="tabActive">
                <el-tab-pane class name="first">
                    <span slot="label">
                        1. Configuración del plan
                    </span>
                    <div class="form-body">

                        <div class="row">
                            <div class="col-md-5">
                                <div
                                    :class="{'has-danger': errors.name}"
                                    class="form-group">
                                    <label class="control-label">
                                        Nombre
                                    </label>
                                    <el-input
                                        v-model="fakeForm.name"
                                    ></el-input>
                                    <small
                                        v-if="errors.name"
                                        class="form-control-feedback"
                                        v-text="errors.name[0]">
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div
                                    :class="{'has-danger': errors.periods}"
                                    class="form-group">
                                    <label class="control-label">
                                        Tipo de Periodos
                                    </label>
                                    <el-select
                                        v-model="fakeForm.periods"
                                        filterable>
                                        <el-option
                                            v-for="option in periods"
                                            :key="option.period"
                                            :label="option.name"
                                            :value="option.period">
                                        </el-option>
                                    </el-select>
                                    <small
                                        v-if="errors.periods"
                                        class="form-control-feedback"
                                        v-text="errors.periods[0]">
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div
                                    :class="{'has-danger': errors.quantity_period}"
                                    class="form-group">
                                    <label class="control-label">
                                        Cant de Periodos
                                    </label>
                                    <el-input
                                        v-model="fakeForm.quantity_period"
                                        :disabled="fakeForm.unlimited"
                                    ></el-input>
                                    <small
                                        v-if="errors.quantity_period"
                                        class="form-control-feedback"
                                        v-text="errors.quantity_period[0]">
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-12 text-end mt-2">
                                <el-switch
                                    v-model="fakeForm.unlimited"
                                    @change="toggleUnlimited"
                                ></el-switch>
                                <label class="ms-2">Ilimitado — sin vencimiento</label>
                            </div>

                            <div class="col-md-12">
                                <div
                                    :class="{'has-danger': errors.description}"
                                    class="form-group">
                                    <label class="control-label">
                                        Descripcion
                                    </label>
                                    <el-input
                                        v-model="fakeForm.description"
                                    ></el-input>
                                    <small
                                        v-if="errors.description"
                                        class="form-control-feedback"
                                        v-text="errors.description[0]">
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane class name="second">
                    <span slot="label">
                        2. Productos y precios
                    </span>
                    <div class="form-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="font-weight-bold">Descripción</th>
                                    <th class="text-center font-weight-bold">Unidad</th>
                                    <th class="text-end font-weight-bold">Cantidad</th>
                                    <th class="text-end font-weight-bold">Valor Unitario</th>
                                    <th class="text-end font-weight-bold">Total</th>
                                    <th v-if="fakeForm.items && fakeForm.items.length > 1" class="font-weight-bold">Aplicar en periodo</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody v-if="fakeForm.items !== undefined && fakeForm.items.length > 0">
                                <tr v-for="(row, index) in fakeForm.items"
                                    :key="`item-${row.id || index}-${index}`">
                                    <td>{{ index + 1 }}</td>
                                    <td>
                                        {{ setDescriptionOfItem(row.item) }}
                                        {{
                                            (row.item && row.item.presentation && row.item.presentation.hasOwnProperty('description')) ? row.item.presentation.description : ''
                                        }}<br/><small>{{ row.affectation_igv_type.description }}</small>
                                    </td>
                                    <td class="text-center">{{ row.item.unit_type_id }}</td>
                                    <td class="text-end">{{ row.quantity }}</td>
                                    <td class="text-end">{{ currency_type.symbol }}
                                        {{ row.total_value }}
                                    </td>
                                    <td class="text-end">{{ currency_type.symbol }} {{ row.total }}</td>
                                    <td v-if="fakeForm.items && fakeForm.items.length > 1">
                                        <el-select
                                            v-model="row.apply_in_period"
                                            placeholder="Aplicar en"
                                            style="width: 100%;"
                                            size="mini"
                                        >
                                            <el-option
                                                label="Cobrar en todos los periodos"
                                                value="all"
                                            ></el-option>
                                            <el-option
                                                v-if="fakeForm.unlimited"
                                                label="Estar al primer cobro"
                                                value="first"
                                            ></el-option>
                                            <el-option
                                                v-for="period in getAvailablePeriods()"
                                                :key="period.value"
                                                :label="period.label"
                                                :value="period.value"
                                            ></el-option>
                                        </el-select>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn waves-effect waves-light btn-xs btn-info me-1"
                                            type="button"
                                            @click="ediItem(row, index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                        </button>
                                        <button class="btn waves-effect waves-light btn-xs btn-danger"
                                            type="button"
                                            @click.prevent="clickRemoveItem(index)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="fakeForm.items && fakeForm.items.length > 1 && !fakeForm.items.some(i => i.apply_in_period === 'all')" class="alert alert-warning py-1 px-2 mt-2 mb-0">
                            Al menos un producto debe tener seleccionada la opción <strong>"Cobrar en todos los periodos"</strong>.
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mt-2">
                                <div class="form-group">
                                    <button class="btn waves-effect waves-light btn-primary btn-sm" type="button" @click="clickAddItem">+ Agregar Producto
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 px-0">
                                <div class="row">
                                    <div class="col-4 col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Periodo de prueba</label>
                                            <el-input
                                                type="number"
                                                v-model.number="fakeForm.trial_days"
                                                placeholder="N° Días"
                                            ></el-input>
                                            <small class="form-control-feedback">Días de prueba gratuita</small>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-8 text-end">
                                        <p v-if="fakeForm.total_exportation > 0"
                                           class="text-end mb-0">
                                            OP.EXPORTACIÓN: {{ currency_type.symbol }} {{ fakeForm.total_exportation }}
                                        </p>
                                        <p v-if="fakeForm.total_free > 0"
                                           class="text-end mb-0">
                                            OP.GRATUITAS: {{ currency_type.symbol }} {{ fakeForm.total_free }}
                                        </p>
                                        <p v-if="fakeForm.total_unaffected > 0"
                                           class="text-end mb-0">
                                            OP.INAFECTAS: {{ currency_type.symbol }} {{ fakeForm.total_unaffected }}
                                        </p>
                                        <p v-if="fakeForm.total_exonerated > 0"
                                           class="text-end mb-0">
                                            OP.EXONERADAS: {{ currency_type.symbol }} {{ fakeForm.total_exonerated }}
                                        </p>
                                        <p v-if="fakeForm.total_taxed > 0"
                                           class="text-end mb-0">
                                            OP.GRAVADA: {{ currency_type.symbol }} {{ fakeForm.total_taxed }}
                                        </p>
                                        <p v-if="fakeForm.total_igv > 0"
                                           class="text-end mb-0">
                                            IGV: {{ currency_type.symbol }} {{ fakeForm.total_igv }}
                                        </p>
                                        <h3 v-if="fakeForm.total > 0"
                                            class="text-end mb-0 font-weight-bold">
                                            TOTAL DEL PLAN:
                                            {{ currency_type.symbol }} {{ fakeForm.total }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12"  v-if="errors.items">
                                <small
                                    v-if="errors.items"
                                    class="form-control-feedback"
                                    v-text="errors.items[0]">
                                </small>
                            </div>
                        </div>
                    </div>
                </el-tab-pane>
            </el-tabs>
            <div class="form-actions text-end mt-4">
                <el-button
                    class="second-buton me-2"
                    @click.prevent="close()">
                    Cancelar
                </el-button>
                <el-button
                    :loading="loading_submit"
                    native-type="submit"
                    type="primary">
                    Guardar
                </el-button>
            </div>
        </form>


        <tenant-quotations-item-form
            :configuration="config"
            :currency-type-id-active="config.currency_type_id"
            :exchange-rate-sale="exchange_rate"
            :recordItem="recordItem"
            :showDialog.sync="showDialogAddItem"
            :typeUser="config.typeUser"
            :displayDiscount="false"
            @add="addRow"
            :percentage-igv="percentage_igv"
        ></tenant-quotations-item-form>
    </el-dialog>
</template>

<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";
import {serviceNumber, functions} from '../../../../../../resources/js/mixins/functions'
import {
    calculateRowItem,
    FormatUnitPriceRow,
    showNamePdfOfDescription
} from "@helpers/functions";

export default {
    mixins: [
        serviceNumber,
        functions
    ],
    props: [
        'showDialog',
    ],
    data() {
        return {
            loading_submit: false,
            showDialogAddItem: false,
            titleDialog: null,
            errors: {},
            tabActive: 'first',
            currency_type: {},
            //countries: [],
            //all_departments: [],
            //all_provinces: [],
            //all_districts: [],
            provinces: [],
            districts: [],
            recordItem: null,
            fakeForm: {},
            //identity_document_types: []
            form: {
                date_of_issue: moment().format('YYYY-MM-DD'),
                establishment_id: null
            }, //usado para obtener los datos del igv
            previousQuantityPeriod: null,
        }
    },
    async created() {
        this.$store.commit('setItemSearchExtraParameters', {'only_service': 0});
        await this.initForm()
        await this.$http
            .post(`/full_suscription/${this.resource}/tables`, {})
            .then(response => {
                this.$store.commit('setPeriods', response.data.periods)

                this.form.establishment_id = response.data.establishment_id
            })
        await this.getCommonData()
        await this.getPercentageIgv()
    },
    computed: {
        ...mapState([
            'config',
            'resource',
            'periods',
            'form_data',
            'exchange_rate',
            'currency_types',
            'affectation_igv_types',
            'unit_types',
            'payment_method_types',
            'item_search_extra_parameters',
        ]),
    },
    methods: {
        ...mapActions([
            'loadConfiguration',
            'clearFormData',
            'EmitEvent',
        ]),

        getCommonData(){
            this.$http.post('/full_suscription/CommonData',{})
                .then((response)=>{
                    this.$store.commit('setCurrencyTypes',response.data.currency_types)
                    this.$store.commit('setAffectationIgvTypes',response.data.affectation_igv_types)
                    this.$store.commit('setUnitTypes',response.data.unit_types)
                    this.$store.commit('setPaymentMethodTypes', response.data.payments_credit)
                })
            .then(()=>{
                this.changeCurrencyType()
            })
        },
        clearForm() {
            let form = {
                id: null,
                cat_period_id: null,
                name: null,
                description: null,
                period: null,
                items: [],
                periods: 12,
                quantity_period : 'M',
                unlimited: false
                ,
                trial_days: null
            }
            this.$store.commit('setFormData', form)
        },
        initForm() {
            this.tabActive = 'first'
            this.errors = {}

            if (this.form_data === undefined) {
                this.clearForm();
            }
            this.titleDialog = (this.form_data.id) ? 'Editar Plan' : 'Nuevo Plan'
            if (this.form_data.items === undefined) this.form_data.items = [];
            // Deep clone para evitar referencia directa y permitir que Vue detecte cambios
            this.fakeForm = JSON.parse(JSON.stringify(this.form_data))

            // Asegurar que el formulario tenga `currency_type_id`; tomar de `config` si falta
            if (this.fakeForm.currency_type_id === undefined || this.fakeForm.currency_type_id === null) {
                this.fakeForm.currency_type_id = this.config.currency_type_id;
            }

            this.changeCurrencyType()

            // Si el registro cargado ya tiene unlimited activo, asegurarnos de limpiar quantity_period
            if (this.fakeForm && this.fakeForm.unlimited) {
                this.previousQuantityPeriod = this.fakeForm.quantity_period || null
                this.fakeForm.quantity_period = null
            }
        },
        create() {
            this.initForm()
            if (this.form_data.id) {
                this.$http
                    .post(`/full_suscription/${this.resource}/record`, {
                        person: this.form_data.id,
                    })
                    .then(response => {
                        this.$store.commit('setFormData', response.data.data)
                        // Re-inicializar fakeForm con los datos actualizados
                        this.initForm()
                    })
            } else {
                // ensure quantity_period null if unlimited on new form
                if (this.fakeForm && this.fakeForm.unlimited) {
                    this.previousQuantityPeriod = this.fakeForm.quantity_period || null
                    this.fakeForm.quantity_period = null
                }
            }
        },

        submit() {
            this.loading_submit = true
            // Validar que al menos 1 producto tenga 'Cobrar en todos los periodos' cuando hay más de 1
            if (this.fakeForm.items && this.fakeForm.items.length > 1) {
                const hasAll = this.fakeForm.items.some(item => item.apply_in_period === 'all');
                if (!hasAll) {
                    this.errors = { apply_in_period: ['Al menos un producto debe tener seleccionada la opción "Cobrar en todos los periodos".'] };
                    this.loading_submit = false;
                    return;
                }
            }
            // Sincronizar fakeForm hacia el store (form_data) antes de enviar
            this.$store.commit('setFormData', this.fakeForm)
            let data = this.fakeForm;
            // console.dir(data)
            this.$http.post(`/full_suscription/${this.resource}`, data)
                .then(() => {
                    this.$eventHub.$emit('reloadData')
                    // this.EmitEvent('reloadData')
                    this.close()
                })
                .catch(error => {
                    console.error(error)
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        console.log(error)
                    }
                })
                .finally(() => {
                    this.loading_submit = false
                })
        },
        close() {
            this.$emit('update:showDialog', false)
            this.clearForm();
            this.clearFormData();
        },
        searchCustomer() {
            this.searchServiceNumberByType()
        },
        addRow(row) {
            /* Extraido de resources/js/views/tenant/quotations/form.vue */
            if (this.recordItem) {
                this.fakeForm.items[this.recordItem.indexi] = row
                this.recordItem = null

            } else {
                // Deep clone del item y asegurar que tiene apply_in_period por defecto
                const newItem = JSON.parse(JSON.stringify(row));
                if (!newItem.apply_in_period) {
                    newItem.apply_in_period = 'all';
                }
                this.fakeForm.items.push(newItem);
            }
            // Si solo hay 1 item, forzar apply_in_period = 'all'
            if (this.fakeForm.items.length === 1) {
                this.fakeForm.items[0].apply_in_period = 'all';
            }
            // Sincronizar fakeForm con store
            this.$store.commit('setFormData', this.fakeForm)

            this.calculateTotal();
            // Sincronizar nuevamente después de calcular totales
            this.$store.commit('setFormData', this.fakeForm)
        },
        clickAddItem() {
            this.recordItem = null;
            this.showDialogAddItem = true;
        },

        clickRemoveItem(index) {
            // Crear nuevo array sin el item para que Vue reactive correctamente
            const newItems = [...this.fakeForm.items]
            newItems.splice(index, 1)
            // Si queda solo 1 item, forzar apply_in_period = 'all'
            if (newItems.length === 1) {
                newItems[0].apply_in_period = 'all';
            }
            // Usar $set para que Vue detecte el cambio
            this.$set(this.fakeForm, 'items', newItems)

            // Recalcular totales
            this.calculateTotal()
            // Sincronizar con store
            this.$store.commit('setFormData', this.fakeForm)
        },
        ediItem(row, index) {
            row.indexi = index
            this.recordItem = row
            this.showDialogAddItem = true
        },

        // Construir opciones de períodos basado en unlimited y quantity_period
        getAvailablePeriods() {
            const periods = [];

            // Si el plan no es ilimitado y tiene cantidad de períodos, mostrar opciones por período
            if (!this.fakeForm.unlimited && this.fakeForm.quantity_period) {
                const qty = parseInt(this.fakeForm.quantity_period) || 0;
                for (let i = 1; i <= qty; i++) {
                    periods.push({
                        label: `Solo al ${this.getPeriodNumber(i)} cobro`,
                        value: i.toString()
                    });
                }
            }

            return periods;
        },

        // Convertir número a texto ordinal (1er, 2do, 3er, etc.)
        getPeriodNumber(n) {
            const suffixes = ['er', 'do', 'er', 'to', 'to', 'to', 'mo', 'vo', 'no', 'o'];
            const tens = n % 10;
            const suffix = (n > 10 && n < 20) ? 'o' : suffixes[tens - 1] || 'o';
            return n + suffix;
        },

        changeCurrencyType() {
            let currencyT =  this.config.currency_type_id;
            if( this.fakeForm.currency_type_id !== undefined){
                currencyT =  this.fakeForm.currency_type_id;
            }
            if(currencyT === null) {
                currencyT = this.config.currency_type_id;
                this.fakeForm.currency_type_id = this.config.currency_type_id;
            }
            this.currency_type = _.find(this.currency_types, {'id': currencyT})

            // Guardar apply_in_period antes de recalcular
            const applyInPeriodMap = {};
            this.fakeForm.items.forEach((row, index) => {
                applyInPeriodMap[index] = row.apply_in_period || 'all';
            });

            let items = []
            this.fakeForm.items.forEach((row, index) => {
                const calculatedItem = calculateRowItem(row, this.fakeForm.currency_type_id, this.exchange_rate, this.percentage_igv)
                // Restaurar apply_in_period después del cálculo
                calculatedItem.apply_in_period = applyInPeriodMap[index] || 'all';
                items.push(calculatedItem)
            });
            this.fakeForm.items = items
            this.calculateTotal()
        },

        // Cuando cambie el switch 'unlimited' debemos poner quantity_period en null y deshabilitar el campo.
        toggleUnlimited(value) {
            if (value) {
                // guardar valor previo y limpiar
                this.previousQuantityPeriod = this.fakeForm.quantity_period || null
                this.fakeForm.quantity_period = null
            } else {
                // restaurar si existe un previo
                if (this.previousQuantityPeriod !== null) {
                    this.fakeForm.quantity_period = this.previousQuantityPeriod
                }
                this.previousQuantityPeriod = null
            }
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
            let total_igv_free = 0

            this.fakeForm.items.forEach((row) => {
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


                if (['11', '12', '13', '14', '15', '16'].includes(row.affectation_igv_type_id)) {

                    let unit_value = row.total_value / row.quantity
                    let total_value_partial = unit_value * row.quantity
                    row.total_taxes = row.total_value - total_value_partial
                    row.total_igv = total_value_partial * (row.percentage_igv / 100)
                    row.total_base_igv = total_value_partial
                    total_value -= row.total_value
                    total_igv_free += row.total_igv

                }

            });

            this.fakeForm.total_igv_free = _.round(total_igv_free, 2)
            this.fakeForm.total_exportation = _.round(total_exportation, 2)
            this.fakeForm.total_taxed = _.round(total_taxed, 2)
            this.fakeForm.total_exonerated = _.round(total_exonerated, 2)
            this.fakeForm.total_unaffected = _.round(total_unaffected, 2)
            this.fakeForm.total_free = _.round(total_free, 2)
            this.fakeForm.total_igv = _.round(total_igv, 2)
            this.fakeForm.total_value = _.round(total_value, 2)
            this.fakeForm.total_taxes = _.round(total_igv, 2)
            this.fakeForm.total = _.round(total, 2)


            this.setTotalDefaultPayment()

        },

        setTotalDefaultPayment() {

            if (this.fakeForm.payments !== undefined && this.fakeForm.payments.length > 0) {

                this.fakeForm.payments[0].payment = this.fakeForm.total
            }
        },

        getFormatUnitPriceRow(number) {
            return FormatUnitPriceRow(number)
        },
        setDescriptionOfItem(item) {
            return showNamePdfOfDescription(item, this.config.show_pdf_name)
        }
    }
}
</script>
