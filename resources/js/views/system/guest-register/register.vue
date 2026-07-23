<template>
    <article class="auth__form side">
        <div class="auth__form-content">
            <div class="d-flex justify-content-center">
                <div class="row">
                    <slot name="form-logo"></slot>
                </div>
            </div>
            <form
                autocomplete="off"
                @submit.prevent="handlePrimary"
                :class="{ 'is-processing': creating }"
            >
                <div class="row register-success" v-if="showSuccess">
                    <div class="col-md-12 text-center">
                        <div class="register-success__icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                        <h1 class="auth__title" style="font-weight: 700">
                            ¡Cliente creado con éxito!
                        </h1>
                        <p class="register-success__text">
                            La cuenta
                            <b>{{ successData.name }}</b>
                            se creó correctamente. Ya puedes acceder a tu
                            plataforma desde el siguiente enlace:
                        </p>
                    </div>

                    <div class="col-md-12">
                        <span class="register-success__url-label">
                            Tu enlace de acceso
                        </span>
                        <div class="register-success__url">
                            <a
                                :href="successData.url"
                                target="_blank"
                                rel="noopener"
                                class="register-success__url-text"
                            >
                                {{ successData.url }}
                            </a>
                            <button
                                type="button"
                                class="register-success__copy"
                                :title="copied ? 'Copiado' : 'Copiar enlace'"
                                @click="copyUrl"
                            >
                                <svg
                                    v-if="!copied"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        x="9"
                                        y="9"
                                        width="13"
                                        height="13"
                                        rx="2"
                                        ry="2"
                                    />
                                    <path
                                        d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 pt-3">
                        <a
                            :href="successData.url"
                            class="register-success__cta"
                        >
                            Ir a la plataforma
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div
                    class="row email-verificate"
                    v-else-if="isRegistered"
                    v-loading="loading_submit"
                >
                    <div class="col-md-12 text-center">
                        <h1 class="auth__title">Verificación de correo</h1>
                    </div>
                    <div class="col-md-12">
                        <p class="text-justify">
                            <span class="step-number">1</span>
                            <span class="step-text">
                                Revise su bandeja de entrada en el correo:
                                <b>{{ form.email }}</b> y haga clic en el enlace
                                "Verificar Email" para activar su cuenta.
                            </span>
                        </p>

                        <p class="text-justify">
                            <span class="step-number">2</span>
                            <span class="step-text">
                                Si registró mal su correo, escríbanos al
                                whatsapp para que el equipo de soporte lo
                                corrija por usted.
                            </span>
                        </p>

                        <p class="text-justify">
                            <span class="step-number">3</span>
                            <span class="step-text">
                                Si el correo indicado es correcto y no recibió
                                el correo de verificación en su bandeja de
                                entrada, verifique su bandeja de spam o
                                <a href="#" @click="clickResendEmail"
                                    ><b
                                        >Haga clic aquí para volver a enviar el
                                        correo de verificación.</b
                                    ></a
                                >
                            </span>
                        </p>
                        <p v-if="errors.key || errors.user_id || errors.email">
                            <span class="step-number error-step">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="14"
                                    height="14"
                                    fill="currentColor"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M7.938 2.016a.13.13 0 0 1 .125 0l6.857 11.856c.03.052.03.116 0 .168a.13.13 0 0 1-.125.06H1.205a.13.13 0 0 1-.125-.06.145.145 0 0 1 0-.168L7.938 2.016zM8 5c-.535 0-.954.462-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"
                                    />
                                </svg>
                            </span>
                            <span class="step-text">
                                <small
                                    v-if="errors.key"
                                    class="invalid-feedback"
                                    v-text="errors.key[0]"
                                ></small>
                                <small
                                    v-if="errors.user_id"
                                    class="invalid-feedback"
                                    v-text="errors.user_id[0]"
                                ></small>
                                <small
                                    v-if="errors.email"
                                    class="invalid-feedback"
                                    v-text="errors.email[0]"
                                ></small>
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row" v-else-if="step === 'form'">
                    <div class="col-md-12 text-center">
                        <h1 class="auth__title" style="font-weight: 700">
                            Regístrate gratis
                        </h1>
                    </div>

                    <div class="col-md-12">
                        <div
                            :class="{ 'has-danger': errors.number }"
                            class="form-group"
                        >
                            <label class="control-label">RUC</label>
                            <x-input-service-guest
                                v-model="form.number"
                                class="form-control form-top"
                                :identity_document_type_id="
                                    form.identity_document_type_id
                                "
                                @search="searchNumber"
                            ></x-input-service-guest>
                            <small
                                v-if="rucStatus === 'checking'"
                                class="subdomain-feedback is-checking"
                            >
                                <i class="el-icon-loading"></i>
                                Verificando RUC…
                            </small>
                            <small
                                v-else-if="rucStatus === 'available'"
                                class="subdomain-feedback is-available"
                            >
                                <i class="el-icon-circle-check"></i>
                                {{ rucMessage }}
                            </small>
                            <small
                                v-else-if="rucStatus === 'taken'"
                                class="subdomain-feedback is-taken"
                            >
                                <i class="el-icon-circle-close"></i>
                                {{ rucMessage }}
                            </small>
                            <small
                                v-if="errors.number"
                                class="invalid-feedback"
                                v-text="errors.number[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div
                            :class="{
                                'has-danger': errors.name || errors.uuid,
                            }"
                            class="form-group"
                        >
                            <label class="control-label">
                                Nombre de la empresa
                            </label>
                            <el-input
                                v-model="form.name"
                                class="form-control form-top"
                                :disabled="validateRuc"
                                :placeholder="
                                    validateRuc
                                        ? 'Se completará al validar el RUC en SUNAT'
                                        : ''
                                "
                            >
                            </el-input>
                            <!-- <small
                                v-if="validateRuc && !rucVerified"
                                class="ruc-hint"
                            >
                                El RUC se validará automáticamente en SUNAT al
                                ingresar los 11 dígitos.
                            </small> -->
                            <small
                                v-if="errors.name"
                                class="invalid-feedback"
                                v-text="errors.name[0]"
                            ></small>
                            <small
                                v-if="errors.uuid"
                                class="invalid-feedback"
                                v-text="errors.uuid[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div
                            :class="{
                                'has-danger': errors.subdomain || errors.uuid,
                            }"
                            class="form-group"
                        >
                            <label class="control-label">
                                Nombre de subdominio
                            </label>
                            <el-input
                                v-model="form.subdomain"
                                class="form-control form-top"
                            >
                                <template slot="append">{{ baseUrl }}</template>
                            </el-input>
                            <small
                                v-if="subdomainStatus === 'checking'"
                                class="subdomain-feedback is-checking"
                            >
                                <i class="el-icon-loading"></i>
                                Verificando disponibilidad…
                            </small>
                            <small
                                v-else-if="subdomainStatus === 'available'"
                                class="subdomain-feedback is-available"
                            >
                                <i class="el-icon-circle-check"></i>
                                {{ subdomainMessage }}
                            </small>
                            <small
                                v-else-if="
                                    subdomainStatus === 'taken' ||
                                    subdomainStatus === 'invalid'
                                "
                                class="subdomain-feedback is-taken"
                            >
                                <i class="el-icon-circle-close"></i>
                                {{ subdomainMessage }}
                            </small>
                            <small
                                v-if="errors.subdomain"
                                class="invalid-feedback"
                                v-text="errors.subdomain[0]"
                            ></small>
                            <small
                                v-if="errors.uuid"
                                class="invalid-feedback"
                                v-text="errors.uuid[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-12" v-if="selectablePlans.length">
                        <div
                            :class="{ 'has-danger': errors.plan_id }"
                            class="form-group"
                        >
                            <label class="control-label">
                                Seleccionar plan
                            </label>
                            <el-select
                                v-model="form.plan_id"
                                class="plan-select"
                                placeholder="Elige un plan..."
                                filterable
                                style="width: 100%"
                            >
                                <el-option
                                    v-for="plan in selectablePlans"
                                    :key="plan.id"
                                    :label="plan.name"
                                    :value="plan.id"
                                >
                                    <span style="float: left">{{
                                        plan.name
                                    }}</span>
                                    <span
                                        style="
                                            float: right;
                                            color: #8492a6;
                                            font-size: 13px;
                                        "
                                        >S/ {{ plan.pricing }}</span
                                    >
                                </el-option>
                            </el-select>
                            <small
                                v-if="errors.plan_id"
                                class="invalid-feedback"
                                v-text="errors.plan_id[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div
                            :class="{ 'has-danger': errors.email }"
                            class="form-group"
                        >
                            <label class="control-label">
                                Correo de acceso
                                <el-tooltip
                                    class="item"
                                    content="Ingresa un correo válido: allí recibirás el aviso y el enlace de activación."
                                    effect="dark"
                                    placement="top-start"
                                >
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>
                            <el-input
                                v-model="form.email"
                                class="form-control form-top"
                                :disabled="form.is_update"
                            >
                            </el-input>
                            <small
                                v-if="errors.email"
                                class="invalid-feedback"
                                v-text="errors.email[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div
                            :class="{ 'has-danger': errors.password }"
                            class="form-group"
                        >
                            <label class="control-label"> Contraseña </label>
                            <el-input
                                v-model="form.password"
                                type="password"
                                class="form-control form-top"
                                show-password
                            >
                            </el-input>
                            <small
                                v-if="errors.password"
                                class="invalid-feedback"
                                v-text="errors.password[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-12 text-end pt-2">
                        <button
                            class="btn-signin btn-block mt-0"
                            :disabled="
                                loading_submit ||
                                !form.plan_id ||
                                subdomainStatus !== 'available' ||
                                rucStatus === 'taken' ||
                                rucStatus === 'checking' ||
                                (validateRuc && !rucVerified)
                            "
                            type="submit"
                        >
                            <template v-if="loading_submit">
                                {{ button_text }}
                            </template>
                            <template v-else>{{ primaryButtonText }}</template>
                        </button>
                    </div>

                    <div class="col-md-12 text-center mt-3">
                        <span style="font-size: 12px">
                            <strong
                                >✉ Se requiere confirmar el correo para acceder
                                a la plataforma.</strong
                            >
                        </span>
                    </div>
                </div>

                <!-- Paso de pago (solo si el plan requiere pago) -->
                <div class="row" v-else-if="step === 'payment'">
                    <div class="col-md-12">
                        <a
                            href="#"
                            class="payment-back"
                            @click.prevent="backToForm"
                        >
                            <i class="fa fa-chevron-left"></i> Volver al registro
                        </a>
                    </div>

                    <div class="col-md-12 text-center">
                        <h1 class="auth__title" style="font-weight: 700">
                            Datos de pago
                        </h1>
                    </div>

                    <div class="col-md-12" v-if="selectedPlan">
                        <div class="payment-summary">
                            <div class="payment-summary__info">
                                <span class="payment-summary__label">
                                    Plan seleccionado
                                </span>
                                <span class="payment-summary__plan">
                                    {{ selectedPlan.name }}
                                </span>
                            </div>
                            <div class="payment-summary__price">
                                S/ {{ selectedPlan.pricing }}
                                <small>/ mes</small>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                Número de tarjeta
                            </label>
                            <el-input
                                v-model="form.payment.card_number"
                                class="form-control form-top"
                                placeholder="4242 4242 4242 4242"
                                maxlength="19"
                                @input="formatCardNumber"
                            >
                            </el-input>
                            <div class="payment-brands">
                                <span class="payment-brand brand-visa">VISA</span>
                                <span class="payment-brand brand-mc">MC</span>
                                <span class="payment-brand brand-amex">AMEX</span>
                                <span class="payment-brand brand-yape">YAPE</span>
                            </div>
                            <small v-if="cardNumberError" class="card-feedback">
                                {{ cardNumberError }}
                            </small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                Nombre del titular
                            </label>
                            <el-input
                                v-model="form.payment.card_holder"
                                class="form-control form-top"
                                placeholder="ALEXANDER PÉREZ"
                                @input="formatCardHolder"
                            >
                            </el-input>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Vencimiento</label>
                            <el-input
                                v-model="form.payment.card_expiry"
                                class="form-control form-top"
                                placeholder="MM/AA"
                                maxlength="5"
                                @input="formatExpiry"
                            >
                            </el-input>
                            <small v-if="cardExpiryError" class="card-feedback">
                                {{ cardExpiryError }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">CVV</label>
                            <el-input
                                v-model="form.payment.card_cvv"
                                class="form-control form-top"
                                placeholder="123"
                                maxlength="4"
                                @input="formatCvv"
                            >
                            </el-input>
                            <small v-if="cardCvvError" class="card-feedback">
                                {{ cardCvvError }}
                            </small>
                        </div>
                    </div>

                    <div class="col-md-12 text-end pt-2">
                        <button
                            class="btn-signin btn-signin--pay btn-block mt-0"
                            :disabled="loading_submit || !paymentValid"
                            type="submit"
                        >
                            <template v-if="loading_submit">
                                {{ button_text }}
                            </template>
                            <template v-else> CREAR CUENTA </template>
                        </button>
                    </div> -->


                    <div
                        class="col-md-12"
                        v-if="selectedPlan && checkoutLoaded && !checkoutType"
                    >
                        <div class="payment-no-method">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span>
                                No hay un método de pago configurado. Por favor
                                contacta al administrador para habilitar una
                                pasarela de pago.
                            </span>
                        </div>
                    </div>

                    <checkout-admin
                        @submit="startCreation"
                        @loaded="onCheckoutLoaded"
                        v-if="selectedPlan"
                        :form="_form"
                    >
                    </checkout-admin>
                </div>
            </form>
        </div>
    </article>
</template>

<script>
import { serviceNumber } from "../../../mixins/functions";

export default {
    props: {
        baseUrl: {
            type: String,
            required: true,
        },
        plans: {
            type: Array,
            default: () => [],
        },
        planDefault: {
            type: [Number, String],
            default: null,
        },
        validateRuc: {
            type: Boolean,
            default: true,
        },
    },
    mixins: [serviceNumber],
    data() {
        return {
            resource: "register",
            form: {},
            errors: {},
            loading_submit: false,
            button_text: null,
            isRegistered: false,
            rucVerified: false,
            checkoutLoaded: false,
            checkoutType: null,
            step: "form",
            creating: false,
            // Disponibilidad de subdominio
            subdomainStatus: null, // null|'checking'|'available'|'taken'|'invalid'
            subdomainMessage: "",
            subdomainTimer: null,
            rucTimer: null,
            // Disponibilidad de RUC (existencia en BD)
            rucStatus: null, // null|'checking'|'available'|'taken'
            rucMessage: "",
            rucCheckTimer: null,
            creationResult: null,
            animationDone: false,
            showSuccess: false,
            successData: { name: "", email: "", url: "" },
            copied: false,
        };
    },
    computed: {
        // Lista de planes para el select.
        selectablePlans() {
            return this.plans;
        },
        // Plan actualmente seleccionado en el formulario
        selectedPlan() {
            if (!this.form.plan_id) return null;
            return (
                this.selectablePlans.find(
                    (plan) => String(plan.id) === String(this.form.plan_id)
                ) || null
            );
        },
        isFree() {
            return this.selectedPlan
                ? Number(this.selectedPlan.pricing) === 0
                : false;
        },
        trialDays() {
            return this.selectedPlan
                ? Number(this.selectedPlan.test_days || 0)
                : 0;
        },
        hasTrial() {
            return this.trialDays > 0;
        },
        paymentValid() {
            const p = this.form.payment || {};
            const number = (p.card_number || "").replace(/\s/g, "");
            const holder = (p.card_holder || "").trim();
            const expiry = p.card_expiry || "";
            const cvv = p.card_cvv || "";

            const numberOk = number.length >= 13 && number.length <= 16;
            const holderOk = holder.length > 0;
            const cvvOk = cvv.length >= 3 && cvv.length <= 4;

            let expiryOk = false;
            const match = expiry.match(/^(\d{2})\/(\d{2})$/);
            if (match) {
                const month = parseInt(match[1], 10);
                expiryOk = month >= 1 && month <= 12;
            }

            return numberOk && holderOk && cvvOk && expiryOk;
        },
        cardNumberError() {
            const v = this.form.payment ? this.form.payment.card_number || "" : "";
            if (!v) return null;
            const digits = v.replace(/\D/g, "");
            return digits.length < 13 || digits.length > 16
                ? "Número de tarjeta incompleto."
                : null;
        },
        cardExpiryError() {
            const v = this.form.payment ? this.form.payment.card_expiry || "" : "";
            if (!v) return null;
            const match = v.match(/^(\d{2})\/(\d{2})$/);
            if (!match) return "Formato inválido. Usa MM/AA.";
            const month = parseInt(match[1], 10);
            if (month < 1 || month > 12) return "El mes debe estar entre 01 y 12.";
            return null;
        },
        cardCvvError() {
            const v = this.form.payment ? this.form.payment.card_cvv || "" : "";
            if (!v) return null;
            return v.length < 3 ? "El CVV debe tener 3 o 4 dígitos." : null;
        },
        requiresPayment() {
            return !!this.selectedPlan && !this.isFree && !this.hasTrial;
        },
        primaryButtonText() {
            if (!this.selectedPlan) return "CREAR CUENTA";
            if (this.isFree) return "CREAR CUENTA";
            if (this.hasTrial) return "EMPEZAR PRUEBA GRATIS";
            return "SIGUIENTE";
        },
        _form() {
            if (this.selectedPlan) {
                return {
                    amount: this.selectedPlan.pricing * 100,
                    currency: 'PEN',
                    description: 'Pagos de Registro',
                    customer: {
                        name: '',
                        lastName: '',
                        email: '',
                        phone: null
                    }

                }
            }
        }
    },
    created() {
        this.initForm();
        this.$eventHub.$on("guest-register:creation-finished", () => {
            this.onAnimationFinished();
        });
    },
    watch: {
        "form.plan_id"(value) {
            this.$eventHub.$emit("guest-register:plan-selected", value);
        },
        "form.subdomain"(value) {
            this.onSubdomainChange(value);
        },
        "form.number"(value) {
            this.onNumberChange(value);
        },
    },
    methods: {
        onNumberChange(value) {
            const number = (value || "").trim();

            // Verificación instantánea de existencia en BD (igual que el subdominio).
            this.onRucExistenceChange(number);

            // Validación SUNAT (solo si está activada).
            if (!this.validateRuc) return;

            clearTimeout(this.rucTimer);

            this.rucVerified = false;
            this.form.name = null;

            if (
                this.form.identity_document_type_id === "6" &&
                /^\d{11}$/.test(number)
            ) {
                this.rucTimer = setTimeout(() => {
                    this.$eventHub.$emit("enableClickSearch");
                }, 400);
            }
        },
        onRucExistenceChange(number) {
            clearTimeout(this.rucCheckTimer);

            if (!number || !/^\d{8,11}$/.test(number)) {
                this.rucStatus = null;
                this.rucMessage = "";
                return;
            }

            this.rucStatus = "checking";
            this.rucCheckTimer = setTimeout(() => {
                this.checkRuc(number);
            }, 500);
        },
        async checkRuc(number) {
            await this.$http
                .get(`${this.resource}/check-ruc/${number}`)
                .then((response) => {
                    // Evita aplicar una respuesta obsoleta si el valor cambió.
                    if ((this.form.number || "").trim() !== number) return;

                    this.rucMessage = response.data.message;
                    this.rucStatus = response.data.available
                        ? "available"
                        : "taken";
                })
                .catch(() => {
                    this.rucStatus = null;
                    this.rucMessage = "";
                });
        },
        onSubdomainChange(value) {
            clearTimeout(this.subdomainTimer);
            const sub = (value || "").trim().toLowerCase();

            if (!sub) {
                this.subdomainStatus = null;
                this.subdomainMessage = "";
                return;
            }
            if (!/^[a-z0-9]+$/.test(sub)) {
                this.subdomainStatus = "invalid";
                this.subdomainMessage =
                    "Solo se permiten letras y números, sin símbolos.";
                return;
            }

            this.subdomainStatus = "checking";
            this.subdomainTimer = setTimeout(() => {
                this.checkSubdomain(sub);
            }, 500);
        },
        async checkSubdomain(sub) {
            await this.$http
                .get(`${this.resource}/check-subdomain/${sub}`)
                .then((response) => {
                    if ((this.form.subdomain || "").trim().toLowerCase() !== sub)
                        return;

                    this.subdomainMessage = response.data.message;
                    this.subdomainStatus = response.data.available
                        ? "available"
                        : "taken";
                })
                .catch(() => {
                    this.subdomainStatus = null;
                    this.subdomainMessage = "";
                });
        },
        formatCardNumber(value) {
            const digits = (value || "").replace(/\D/g, "").slice(0, 16);
            this.form.payment.card_number = digits
                .replace(/(.{4})/g, "$1 ")
                .trim();
        },
        formatExpiry(value) {
            const digits = (value || "").replace(/\D/g, "").slice(0, 4);
            this.form.payment.card_expiry =
                digits.length > 2
                    ? `${digits.slice(0, 2)}/${digits.slice(2)}`
                    : digits;
        },
        formatCvv(value) {
            this.form.payment.card_cvv = (value || "")
                .replace(/\D/g, "")
                .slice(0, 4);
        },
        formatCardHolder(value) {
            this.form.payment.card_holder = (value || "").replace(
                /[^a-zA-ZÀ-ÿ\s]/g,
                ""
            );
        },
        async clickResendEmail() {
            this.loading_submit = true;

            const form = {
                user_id: this.form.guest_register.user_id,
                email: this.form.email,
                key: this.form.guest_register.key,
            };

            await this.$http
                .post(`${this.resource}/resend-email`, form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.log(error.response);
                        this.$message.error(error.response.data.message);
                    }
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },
        handlePrimary() {
            if (this.step === "form" && this.requiresPayment) {
                this.step = "payment";
                return;
            }

            this.eventHubCreatingTenant()
            this.submit();
            return;
        },
        backToForm() {
            this.step = "form";
        },
        onCheckoutLoaded(type) {
            this.checkoutLoaded = true;
            this.checkoutType = type;
        },
        startCreation(data) {
            let paid = data.data.paid
            let _data = data.data

            if (!paid && !_data.pending) return

            this.errors = {};
            this.creationResult = null;
            this.animationDone = false;
            this.creating = true;

            this.eventHubCreatingTenant()

            this.submit(_data);
        },
        eventHubCreatingTenant(){
            this.errors = {};
            this.creationResult = null;
            this.animationDone = false;
            this.creating = true;

            const plan = this.selectedPlan;

            this.$eventHub.$emit("guest-register:creating", {
                plan,
                awaitBackend: true,
                account: {
                    empresa: this.form.name || "Tu empresa",
                    planName: plan ? plan.name : "",
                    correo: this.form.email || "",
                    url: `${this.form.subdomain || "tu-tienda"}${this.baseUrl}`,
                },
            });
        },
        async submit(data = null) {
            this.loading_submit = true;
            this.button_text = "CREANDO CUENTA...";

            if (data && data.success) {
                if (data.pending) {
                    this.form.order_state_id = 1
                }  else if (data.paid) {
                    this.form.order_state_id = 2
                    this.form.paid = data.paid
                }

            }

            await this.$http
                .post(`${this.resource}/register`, this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.form.guest_register =
                            response.data.guest_register;

                        if (response.data.payment_url) {
                            this.creationResult = {
                                type: "payment",
                                url: response.data.payment_url,
                            };
                        } else {
                            this.creationResult = { type: "registered" };
                            this.successData = {
                                name: this.form.name || "Tu empresa",
                                email: this.form.email || "",
                                url:
                                    response.data.redirect_url ||
                                    `https://${this.form.subdomain || ""}${
                                        this.baseUrl
                                    }`,
                            };
                            this.creating = false;
                            this.showSuccess = true;
                            this.$eventHub.$emit(
                                "guest-register:creation-complete"
                            );
                        }
                    } else {
                        this.abortCreation(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data;
                        this.abortCreation(
                            "Revisa los datos: hay campos con errores."
                        );
                    } else {
                        this.abortCreation(
                            (error.response &&
                                error.response.data &&
                                error.response.data.message) ||
                                "Ocurrió un error al crear la cuenta."
                        );
                    }
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },
        onAnimationFinished() {
            this.animationDone = true;
            this.maybeFinalize();
        },
        maybeFinalize() {
            if (!this.animationDone || !this.creationResult) return;

            if (this.creationResult.type === "payment") {
                window.location.href = this.creationResult.url;
                return;
            }
            this.$eventHub.$emit("guest-register:creation-complete");
            this.creating = false;
            this.isRegistered = true;
        },
        abortCreation(message) {
            this.creating = false;
            this.step = "form";
            this.$eventHub.$emit("guest-register:creation-abort");
            if (message) this.$message.error(message);
        },
        searchNumber(data) {
            this.form.name = data.name;
        },
        async copyUrl() {
            const url = this.successData.url;
            if (!url) return;

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(url);
                } else {
                    const input = document.createElement("input");
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand("copy");
                    document.body.removeChild(input);
                }
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (e) {
                this.$message && this.$message.error("No se pudo copiar el enlace.");
            }
        },
        initForm() {

            this.form = {
                name: null,
                email: null,
                identity_document_type_id: "6",
                number: "",
                password: null,
                subdomain: null,
                plan_id: this.planDefault,
                guest_register: {},
                payment: {
                    card_number: null,
                    card_holder: null,
                    card_expiry: null,
                    card_cvv: null,
                },
            };

            this.step = "form";
            this.creating = false;
            this.subdomainStatus = null;
            this.subdomainMessage = "";
            this.rucStatus = null;
            this.rucMessage = "";
            this.creationResult = null;
            this.animationDone = false;
            this.successData = { name: "", email: "", url: "" };
            this.copied = false;
            this.errors = {};
        },
        searchNumber(data) {
            if (data && data.name) {
                this.form.name = data.name;
                this.rucVerified = true;
            } else {
                this.form.name = null;
                this.rucVerified = false;
            }
        },
    },
};
</script>

<style scoped>
.auth__form {
    position: relative;
    box-shadow: none;
}

.auth__form-content {
    width: 100%;
}

.plan-select >>> .el-input__inner {
    background-color: transparent !important;
    border: none !important;
    padding: 0 .75rem 0 .75rem !important;
}
.el-select.plan-select {
    padding-top: .375rem;
    border-radius: 8px;
    border: 1px solid #ebebeb;
    background: #f6f6f6;
}

.payment-no-method {
    display: flex;
    align-items: flex-start;
    gap: .625rem;
    padding: .875rem 1rem;
    border-radius: 10px;
    border: 1px solid #fde68a;
    background: #fffbeb;
    color: #92400e;
    font-size: 13px;
    line-height: 1.45;
}

.payment-no-method svg {
    flex: 0 0 auto;
    margin-top: 1px;
    color: #d97706;
}

.register-success__icon {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    color: #22c55e;
}

.register-success__text {
    color: #4b5563;
    font-size: 14px;
    margin-top: .5rem;
    margin-bottom: 1rem;
}

.register-success__url-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #9ca3af;
    margin-bottom: .375rem;
}

.register-success__url {
    display: flex;
    align-items: center;
    gap: .5rem;
    width: 100%;
    padding: .5rem .5rem .5rem .875rem;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.register-success__url:focus-within,
.register-success__url:hover {
    border-color: #c7d2fe;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
}

.register-success__url-text {
    flex: 1 1 auto;
    min-width: 0;
    word-break: break-all;
    color: #2563eb;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
}

.register-success__url-text:hover {
    text-decoration: underline;
}

.register-success__copy {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    background: #eef2ff;
    color: #4f46e5;
    cursor: pointer;
    transition: background .2s ease, color .2s ease, transform .1s ease;
}

.register-success__copy:hover {
    background: #e0e7ff;
}

.register-success__copy:active {
    transform: scale(.92);
}

.register-success__cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    width: 100%;
    padding: .8rem 1rem;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #4f46e5, #2563eb);
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: .01em;
    text-decoration: none;
    box-shadow: 0 8px 20px -8px rgba(37, 99, 235, .6);
    transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
}

.register-success__cta:hover {
    color: #fff;
    filter: brightness(1.05);
    transform: translateY(-1px);
    box-shadow: 0 12px 24px -8px rgba(37, 99, 235, .7);
}

.register-success__cta:active {
    transform: translateY(0);
}

.register-success__cta svg {
    transition: transform .2s ease;
}

.register-success__cta:hover svg {
    transform: translateX(3px);
}
</style>
