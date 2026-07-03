<template>
    <div>
        <tenant-checkout-izipay @submit="submitChild" :isTenant="true" :form="_form" :disabled="disabled" v-if="type === 'izipay' && up" />
        <tenant-checkout-culqi @submit="submitChild" :isTenant="true" :form="_form" :disabled="disabled" v-else-if="type === 'culqi' && up" />
    </div>

</template>


<script>


/*
        <checkout-tenant :form="{
            amount: 50 * 100,
            currency: 'PEN',
            orderId: '123456',
            description: 'Pago de prueba',
            customer: {
                name: 'John',
                lastName: 'Doe',
                email: 'cristian@buho.la',
                phone: 987654321
            }
        }">
        </checkout-tenant>
*/
/**
 *  form: {
 *    amount: 0,
 *    currency: 'PEN', 
 *    order_id: '',   -> Unicamente para Izipay o Culqi (order)
 *    description: '', -> Unicamente para Culqi 
 *    customer: {
 *      name: '',
 *      email: '',
 *      phone: '', -> Unicamente para Izipay
 *      lastName: '', -> Unicamente para Izipay
 *    }
 *  }
 */
export default {
    props: {
        form: {
            type: Object,
            required: true
        },
        disabled: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            resource: '/payment-gateway',
            type: null,
            up: false,
            isTenant: false
        }
    },
    computed: {
        _form() {
            if (this.type === 'izipay') {
                return {
                    amount: this.form.amount,
                    currency: this.form.currency,
                    orderId: this.form.order_id,
                    _customer: this.form.customer,
                    customer: {
                        email: this.form.customer.email,
                        billingDetails: {
                            firstName: this.form.customer.name,
                            lastName: this.form.customer.lastName,
                            phoneNumber: this.form.customer.phone,
                        }
                    },
                }
            } else if (this.type === 'culqi') {
                return {
                    amount: this.form.amount,
                    _customer: this.form.customer,
                    currency: this.form.currency,
                    title: this.form.description,
                    email: this.form.customer.email,
                    order: this.form.order_id
                }
            }
            return {}
        }
    },
    created() {
        this.enabledCheckout();
    },
    methods: {
        enabledCheckout(){
            this.$http.get(`${this.resource}/enabled-checkout?isTenant=true`)
                .then( response => {
                    this.type = response.data.checkout
                    this.isTenant = response.data.is_tenant
                    this.up = true;
                })
        },
        submitChild(data) {
            this.$emit('submit', data);
        },
    }
}
</script>