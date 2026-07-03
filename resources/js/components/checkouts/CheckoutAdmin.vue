<template>
    <div>
        <template v-if="from === 'admin'">
            <system-checkout-culqi  :form="_form" v-if="type === 'culqi' && up" />
            <system-checkout-izipay :form="_form" v-else-if="type === 'izipay' && up" />
        </template>
        <template v-else>
            <tenant-checkout-izipay  :form="_form" v-if="type === 'izipay' && up" />
            <tenant-checkout-culqi  :form="_form" v-else-if="type === 'culqi' && up" />
        </template>
    </div>

</template>


<script>

/*
        <checkout-admin :form="{
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
        </checkout-admin>
    */


/* Para uso de tenant
 
        <checkout-admin :from="'tenant'" :form="{
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
        from: {
            type: String,
            default: 'admin'
        }
    },
    data() {
        return {
            resource: '/payment-gateway',
            type: null,
            _form: {},
            up: false,
            isTenant: false
        }
    },
    created() {
        this.enabledCheckout();
        this.transform();
    },
    methods: {
        enabledCheckout(){
            this.$http.get(`${this.resource}/enabled-checkout`)
                .then( response => {
                    console.log(response.data);
                    this.type = response.data.checkout
                    this.isTenant = response.data.is_tenant

                    this.transform();
                    this.up = true;
                })
        }, 
        transform() {

            
            if (this.type === 'izipay') {
                this._form = {
                    amount: this.form.amount,
                    currency: this.form.currency,
                    orderId: this.form.order_id,
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
                this._form = {
                    amount: this.form.amount,
                    currency: this.form.currency,
                    title: this.form.description,
                    email: this.form.customer.email,
                    order: this.form.order_id
                }
            }
        }

    }
}
</script>