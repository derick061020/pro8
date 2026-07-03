<template>
    <div>
        <el-button type="primary" :disabled="disabled" @click.prevent="submit">Pagar con Culqi</el-button>
    </div>
</template>
<script>

/**
 * Culqi Checkout Integration Example
 * cfg -> { 
 *  publicKey: Clave pública de Culqi
 *  rsa: Clave RSA para encriptar datos sensibles (opcional)
 *  idrsa: Identificador de la clave RSA (opcional)
 * }
 * Form -> [
 *  amount: Monto a cobrar (en centavos)
 *  currency: Moneda (PEN o USD) 
 *  email: Correo del cliente
 *  description: Descripción del cargo
 * ]
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
            default: '/payment-gateway/culqi'
        },
    },
    created() {
        if (!window.Culqi) {
            const script = document.createElement('script');
            script.src = "https://js.culqi.com/checkout-js";
            script.async = true;
            document.head.appendChild(script);

            this.loadConfiguration();
        }
    },
    data() {
        return {
            settings: {
                title: "Configuración de Culqi"
            },
            payment_methods: {
                tarjeta: true,
                yape: true,
                // billetera: true,
                bancaMovil: true,
                agente: true,
                // cuotealo: true,
            },
            publicKey: null
        }
    },
    computed: {
        resource() {
            return this.endpointPrefix;
        }
    },
    methods:  {
        submit() {
            let config  = { 
                settings: {
                    title : this.form.description,
                    currency: this.form.currency,
                    amount: this.form.amount,
                },
                client: {
                    email: this.form.email,
                },
                options : {
                    modal: true,
                    // installments: true,
                    paymentMethods: {
                        tarjeta: true,
                        yape: true,
                        // billetera: true,
                        bancaMovil: true,
                        agente: true,
                        // cuotealo: true,	
                    },
                paymentMethodsSort: Object.keys({
                tarjeta: true,
                yape: true,
                // billetera: true,
                bancaMovil: true,
                agente: true,
                // cuotealo: true,	
            })
                }, 
                appearance : {
                    menuType: "sidebar",
                }
            }

            const Culqi = new CulqiCheckout(this.publicKey, config);

            Culqi.culqi = () =>  {
                
                if (Culqi.token) {
                    this.form.email = Culqi.token.email ? Culqi.token.email : this.form.email;

                    const token = Culqi.token.id;
                    this.$http.post(`${this.resource}/charge`, {
                        source_id: token,
                        installments: this.form.installments,
                        description : this.form.description,
                        amount: this.form.amount,
                        email: this.form.email,
                        currency_code: this.form.currency,
                    }).then(response => {
                        const data = response.data
                        console.log(data);
                        
                        this.$emit('submit', {
                            status : response.data.result.outcome.type,
                            customer: this.form._customer
                        });
                        Culqi.close();
                    }).catch(error => {
                        console.log(error);
                        
                        const msg = error.response?.data?.user_message
                            || 'Error al procesar el pago. Intente nuevamente.';
                        
                        let status = error.response?.data?.result?.type ;
                        this.$emit('submit', {
                            status : status,
                            customer: this.form._customer
                        });
                        Culqi.close();

                        this.$message.error(msg);
                    });
                } else if (Culqi.order) {
                    // orden pendiente, sin acción por ahora
                } else {
                    const culqiErr = Culqi.error;
                    const msg = culqiErr?.user_message
                        || culqiErr?.merchant_message
                        || 'Ocurrió un error en el proceso de pago.';
                    this.$message.error(msg);
                }
            }
            Culqi.open();

        },
        showCulqiError(msg) {
            document.querySelector('.culqi-inline-error')?.remove();

            const el = document.createElement('div');
            el.className = 'culqi-inline-error';
            el.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    style="flex-shrink:0;">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v4m0 4h.01"/>
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.871l-8.106 -13.534a1.914 1.914 0 0 0 -3.274 0z"/>
                </svg>
                <span>${msg}</span>
            `;
            el.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, calc(-50% - 140px));
                background: #fff1f0;
                border: 1px solid #ffa39e;
                color: #a8071a;
                padding: 12px 18px;
                border-radius: 8px;
                z-index: 2147483647;
                font-size: 13px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                box-shadow: 0 4px 16px rgba(0,0,0,0.18);
                max-width: 340px;
                width: max-content;
                display: flex;
                align-items: center;
                gap: 8px;
                line-height: 1.4;
            `;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 6000);
        },
        loadConfiguration() {
            this.$http.get(`${this.resource}/record?isTenant=${this.isTenant}`)
                .then(response => {
                    this.publicKey = response.data.publickey_culqi;
                })
        }
    }


}

</script>