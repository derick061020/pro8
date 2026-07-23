<template>
    <div class="kr-izipay-container checkout-pay">
        <button
            type="button"
            class="checkout-pay__btn checkout-pay__btn--izipay"
            :disabled="disabled || loading"
            @click.prevent="submit"
        >
            <span class="checkout-pay__label">
                <span v-if="loading" class="checkout-pay__spinner"></span>
                {{ loading ? 'Cargando…' : 'Pagar con izipay' }}
            </span>
            <span v-if="!loading" class="checkout-pay__amount">{{ formattedAmount }}</span>
        </button>
        <div class="kr-izipay-container-inner" >

        </div>
    </div>
</template>
<script>
import _ from 'lodash';

/**
 * form : {
 *  amount: 0,
 *  currency: 'PEN',
 *  orderId: '',
 *  customer: {
 *   email: '',
 *   billingDetails: {
 *      firstName: '',
 *      lastName: '',
 *      phoneNumber: '',
 *      identityType: '',
 *      identityNumber: '',
 *      address:'',
 *      country: '',
 *      city: '',
 *      state: '',
 *      zipCode: ''
 *   }
 *  }
 * }
 */

export default {
    props: {
        form: {
            type: Object,
            required: true
        },
        isTenant: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        endpointPrefix: {
            type: String,
            default: '/payment-gateway/izipay'
        }
    },
    data() {
        return {
            formtoken: null,
            url_result: null,
            loading: true
        }
    },
    computed: {
        resource() {
            return this.endpointPrefix;
        },
        formattedAmount() {
            const amount = Number(this.form.amount || 0) / 100;
            const symbol = this.form.currency === 'USD' ? '$' : 'S/';
            return `${symbol} ${amount.toLocaleString('es-PE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
        }
    },
    // computed: {
    //     resource() {
    //         return this.isTenant ? '/payment-gateway/izipay' : '/payment_configurations';
    //     }
    // },
    async created() {
        await this.loadConfiguration();
        // Cargar el script de Izipay si no está ya cargado
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = "https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        script.setAttribute('kr-public-key', this.publicKey);
        // script.setAttribute('kr-post-url-success', this.url_result);
        script.setAttribute('kr-popin', true)
        script.setAttribute('kr-language', "es-Es")
        script.setAttribute('kr-no-pay-button', true)
        document.head.appendChild(script);


        //Cargar estilos del checkout
        const link = document.createElement('link');
        const script_style = document.createElement('script');

        link.rel = 'stylesheet';
        link.href = "https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css";

        document.head.appendChild(link);

        script_style.type = 'text/javascript';
        script_style.src = "https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"

        document.head.appendChild(script_style);

        this.waitForKR().then(() => {
            this.eventOnSubmit()
            this.loading = false;
        })

        
        if (_.isNull(this.form)) {
           this.$message.error('No se han cargado información para crear pago con Izipay'); 
        } 

    },
    methods: {
        async loadConfiguration() {
            await this.$http.get(`${this.resource}/record?isTenant=${this.isTenant}`)
                .then(response => {
                    let data = response.data;
                    this.publicKey = data.publickey_izipay;
                    this.url_result = data.url_result;
                })
        },

        submit(){
            this.$http.post(`${this.resource}/payment`, this.form)
                .then(async (response) => {

                    let formToken = response.data.formToken;
                    if (response.data.success && formToken) {

                        let container = document.querySelector('.kr-izipay-container-inner');
                        let embedded = document.createElement('div');

                        embedded.classList.add('kr-embedded');
                        embedded.style.display = 'none'

                        // embedded.style.opacity = 0;
                        // embedded.style.position = 'absolute';

                        container.appendChild(embedded);

                        await KR.setFormConfig({
                            formToken: formToken ,
                            'kr-no-pay-button': true
                        })
                        KR.openPopin()
                    } else {
                        this.$message.error('No se pudo iniciar el pago con Izipay. Verifica que las credenciales configuradas sean válidas.');
                    }

                })
                .catch(() => {
                    this.$message.error('Ocurrió un error al iniciar el pago con Izipay. Intenta nuevamente.');
                })

        },
        eventOnSubmit() {
            KR.onSubmit(async (response) => {
                const uuid = response.clientAnswer.transactions[0].uuid

                this.$http.post(`${this.resource}/transaction`, { uuid: uuid })
                    .then(response => {
                        if (response.data.success) {
                            this.$emit('submit', {
                                status : response.data.result.answer.status,
                                customer: this.form._customer,
                                data: response.data
                            });
                            this.$message.success('Pago realizado con éxito');
                            KR.closePopin();
                        } else {
                            this.$message.error('No se pudo verificar el pago');
                        }
                    })
                    .finally(() => {
                        let container = document.querySelector('.kr-izipay-container-inner');
                        container.innerHTML = '';
                    })
            })
        },
        waitForKR() {
            return new Promise((resolve) => {
            const check = setInterval(() => {
                if (window.KR) {
                clearInterval(check)
                resolve()
                }
            }, 100)
        })
  },
    }

}
</script>

<style scoped>
.checkout-pay {
    width: 100%;
}
.checkout-pay__btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    padding: 14px 20px;
    border: none;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
.checkout-pay__btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
}
.checkout-pay__btn:active:not(:disabled) {
    transform: translateY(0);
}
.checkout-pay__btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.checkout-pay__btn--izipay {
    background: linear-gradient(135deg, #e2204e 0%, #c1001e 100%);
    box-shadow: 0 4px 12px rgba(193, 0, 30, 0.25);
}
.checkout-pay__label {
    display: flex;
    align-items: center;
    gap: 8px;
}
.checkout-pay__amount {
    font-size: 16px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 6px;
    white-space: nowrap;
}
.checkout-pay__spinner {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: checkout-spin 0.7s linear infinite;
}
@keyframes checkout-spin {
    to { transform: rotate(360deg); }
}
</style>