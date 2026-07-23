<template>
    <div class="plan-panel">
        <!-- ============ VISTA: PLAN SELECCIONADO ============ -->
        <template v-if="mode === 'plan'">
        <template v-if="plan">
            <div class="plan-panel__eyebrow">
                <span class="plan-panel__eyebrow-line"></span>
                TU PLAN SELECCIONADO
            </div>

            <div class="plan-panel__head">
                <h2 class="plan-panel__name">{{ plan.name }}</h2>
                <span v-if="isPopular" class="plan-panel__badge">
                    MÁS POPULAR
                </span>
            </div>

            <!-- <p class="plan-panel__desc">
                El favorito de las pymes. Sin límites de emisión y con
                integraciones para crecer.
            </p> -->

            <div v-if="hasTrial" class="plan-panel__trial">
                <span class="plan-panel__trial-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-gift"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 9a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1l0 -2" /><path d="M12 8l0 13" /><path d="M19 12v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-7" /><path d="M7.5 8a2.5 2.5 0 0 1 0 -5a4.8 8 0 0 1 4.5 5a4.8 8 0 0 1 4.5 -5a2.5 2.5 0 0 1 0 5" /></svg>
                </span>
                <div class="plan-panel__trial-text">
                    <strong>{{ trialDays }} días de prueba GRATIS</strong>
                    <span>Sin tarjeta · no pagas hoy · cancela cuando quieras</span>
                </div>
            </div>

            <div class="plan-panel__price">
                <span
                    class="plan-panel__currency"
                    :class="{ 'text-success': isFree }"
                    >S/</span
                >
                <span
                    class="plan-panel__amount"
                    :class="{ 'text-success': isFree }"
                    >{{ priceFormatted }}</span
                >
                <span v-if="!isFree" class="plan-panel__period">/ mes</span>
            </div>
            <p class="plan-panel__price-note">
                Facturado mensualmente · IGV incluido · cancela cuando quieras
            </p>

            <hr class="plan-panel__divider" />

            <ul class="plan-panel__features">
                <li v-if="hasTrial" class="is-highlight">
                    <span class="plan-panel__check">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    Incluye {{ trialDays }} días de prueba gratis
                </li>
                <li v-for="(feature, index) in features" :key="index">
                    <span class="plan-panel__check">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    {{ feature }}
                </li>
            </ul>

            <div
                v-if="plan.selected_modules && plan.selected_modules.length"
                class="plan-panel__modules"
            >
                <button
                    type="button"
                    class="plan-panel__modules-toggle"
                    :class="{ 'is-open': modulesOpen }"
                    @click="modulesOpen = !modulesOpen"
                >
                    <span class="plan-panel__modules-title">
                        Módulos incluidos
                        <span class="plan-panel__modules-count">
                            {{ plan.selected_modules.length }}
                        </span>
                    </span>
                    <svg
                        class="plan-panel__modules-chevron"
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <ul v-show="modulesOpen" class="plan-panel__modules-list">
                    <li
                        v-for="(module, index) in plan.selected_modules"
                        :key="index"
                        class="plan-panel__module"
                    >
                        <span class="plan-panel__module-name">
                            <span class="plan-panel__module-dot"></span>
                            {{ module.name }}
                        </span>
                        <span
                            v-if="module.submodules && module.submodules.length"
                            class="plan-panel__submodules"
                        >
                            <span
                                v-for="(submodule, subIndex) in module.submodules"
                                :key="subIndex"
                                class="plan-panel__submodule-tag"
                            >
                                {{ submodule }}
                            </span>
                        </span>
                    </li>
                </ul>
            </div>

            <p class="plan-panel__footer">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lock"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
                Pago y datos protegidos · puedes cambiar de plan luego
            </p>
        </template>

        <div v-else class="plan-panel__empty">
            <div class="plan-panel__eyebrow">
                <span class="plan-panel__eyebrow-line"></span>
                TU PLAN SELECCIONADO
            </div>
            <p class="plan-panel__empty-text">
                Selecciona un plan en el formulario para ver los detalles aquí.
            </p>
        </div>
        </template>

        <!-- ============ VISTA: CREANDO CUENTA (animación) ============ -->
        <div v-else-if="mode === 'creating'" class="plan-proc">
            <div class="plan-proc__stage">
                <span class="plan-proc__halo"></span>
                <span
                    class="plan-proc__icon"
                    :key="'ic-' + stepIndex"
                    v-html="currentIcon"
                ></span>
            </div>
            <div class="plan-proc__now">
                <span :key="'msg-' + stepIndex" class="plan-proc__now-text">
                    {{ currentMessage }}
                </span>
                <span class="plan-proc__dots"><i></i><i></i><i></i></span>
            </div>
            <p class="plan-proc__sub">
                Estamos preparando todo para ti, no cierres esta ventana.
            </p>
            <ul class="plan-proc__log">
                <li
                    v-for="item in visibleCompleted"
                    :key="item.id"
                    class="plan-proc__log-item"
                >
                    <span class="plan-proc__log-check">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="13"
                            height="13"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </span>
                    {{ item.t }}
                </li>
            </ul>
        </div>

        <!-- ============ VISTA: ÉXITO ============ -->
        <div v-else-if="mode === 'success'" class="plan-success">
            <div class="plan-success__ring">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="46"
                    height="46"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.6"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>
            <h2 class="plan-success__title">¡Cuenta creada con éxito!</h2>
            <p class="plan-success__text">
                Tu cuenta ya está lista. Revisa tu correo para confirmar tu
                cuenta y empezar a facturar.
            </p>
            <div class="plan-success__card">
                <div class="plan-success__row">
                    <span>Empresa</span>
                    <b>{{ account.empresa }}</b>
                </div>
                <div class="plan-success__row">
                    <span>Plan</span>
                    <b>{{ account.planName }}</b>
                </div>
                <div class="plan-success__row">
                    <span>Correo de acceso</span>
                    <b>{{ account.correo }}</b>
                </div>
                <div class="plan-success__row">
                    <span>URL de tu tienda</span>
                    <b class="is-url">{{ account.url }}</b>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const ICONS = {
    rocket: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2c.7-.8.7-2 0-2.8a2 2 0 0 0-3 0Z"/><path d="M12 15l-3-3a22 22 0 0 1 8-10c2 0 4 2 4 4a22 22 0 0 1-10 8Z"/><path d="M9 12H4s.5-2.8 2-4 5-1 5-1"/><path d="M12 15v5s2.8-.5 4-2 1-5 1-5"/></svg>`,
    badge: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>`,
    user: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>`,
    db: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg>`,
    shield: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/></svg>`,
    server: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 7.5h.01M7 16.5h.01"/></svg>`,
    globe: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18Z"/></svg>`,
    store: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9 4 4h16l1 5a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0Z"/><path d="M4 10v9h16v-9"/></svg>`,
    box: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8 12 3 3 8l9 5 9-5Z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>`,
    card: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>`,
    mail: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>`,
    sparkle: `<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18"/></svg>`,
};

export default {
    props: {
        plans: {
            type: Array,
            default: () => [],
        },
        planDefault: {
            type: [Number, String],
            default: null,
        },
    },
    data() {
        return {
            selectedPlanId: this.planDefault,
            modulesOpen: false,
            mode: "plan",
            steps: [],
            stepIndex: -1,
            completedSteps: [],
            account: {},
            creationTimer: null,
            awaitBackend: false,
            holding: false,
        };
    },
    computed: {
        allPlans() {
            return this.plans;
        },
        plan() {
            if (this.selectedPlanId === null || this.selectedPlanId === undefined) {
                return null;
            }
            return (
                this.allPlans.find(
                    (plan) => String(plan.id) === String(this.selectedPlanId)
                ) || null
            );
        },
        isPopular() {
            return (
                this.plan &&
                this.planDefault !== null &&
                String(this.plan.id) === String(this.planDefault)
            );
        },
        priceFormatted() {
            if (!this.plan) return "";
            const value = Number(this.plan.pricing);
            if (isNaN(value)) return this.plan.pricing;
            return value % 1 === 0 ? value.toString() : value.toFixed(2);
        },
        trialDays() {
            return this.plan ? Number(this.plan.test_days || 0) : 0;
        },
        hasTrial() {
            return this.trialDays > 0;
        },
        isFree() {
            return this.plan ? Number(this.plan.pricing) === 0 : false;
        },
        features() {
            if (!this.plan) return [];

            const list = [];

            list.push(
                this.plan.limit_users === 0
                    ? "Usuarios ilimitados"
                    : `${this.plan.limit_users} usuarios`
            );

            list.push(
                this.plan.limit_documents === 0
                    ? "Comprobantes ilimitados"
                    : `${this.plan.limit_documents} comprobantes`
            );

            list.push(
                this.plan.establishments_unlimited
                    ? "Sucursales ilimitados"
                    : `${this.plan.establishments_limit} sucursales`
            );

            list.push(
                this.plan.sales_unlimited
                    ? "Ventas ilimitadas"
                    : `Total ventas mensuales S/${this.plan.sales_limit}`
            );

            return list;
        },
        currentStep() {
            return this.steps[this.stepIndex] || null;
        },
        currentMessage() {
            if (this.holding) return "Finalizando los últimos detalles";
            return this.currentStep ? this.currentStep.t : "";
        },
        currentIcon() {
            if (this.holding) return ICONS.sparkle;
            const key = this.currentStep ? this.currentStep.ic : "rocket";
            return ICONS[key] || ICONS.rocket;
        },
        visibleCompleted() {
            return this.completedSteps.slice(-5);
        },
    },
    created() {
        this.$eventHub.$on("guest-register:plan-selected", (planId) => {
            this.selectedPlanId = planId;
        });
        this.$eventHub.$on("guest-register:creating", (payload) => {
            this.startCreation(payload || {});
        });
        this.$eventHub.$on("guest-register:creation-abort", () => {
            this.resetCreation();
        });
        this.$eventHub.$on("guest-register:creation-complete", () => {
            clearTimeout(this.creationTimer);
            this.holding = false;
            this.mode = "success";
        });
    },
    beforeDestroy() {
        clearTimeout(this.creationTimer);
    },
    methods: {
        randomDelay() {
            return 3000 + Math.floor(Math.random() * 2000);
        },
        buildSteps(plan) {
            const modules = (plan && plan.selected_modules) || [];

            const intro = [
                { t: "Iniciando tu registro", ic: "rocket" },
                { t: "Validando tu RUC en SUNAT", ic: "badge" },
                { t: "Creando tu cuenta", ic: "user" },
                { t: "Conectando con la base de datos", ic: "db" },
            ];

            const moduleSteps = modules.map((m) => ({
                t: `Activando el módulo ${m.name}`,
                ic: "box",
            }));

            const outro = [
                { t: "Configurando permisos y roles", ic: "shield" },
                { t: "Levantando tus servidores", ic: "server" },
                { t: "Generando tu subdominio seguro", ic: "globe" },
                { t: "Instalando tu tienda y catálogo", ic: "store" },
                { t: "Puliendo cada detalle para ti", ic: "sparkle" },
                { t: "Enviando tu correo de confirmación", ic: "mail" },
            ];

            return [...intro, ...moduleSteps, ...outro];
        },
        startCreation(payload) {
            clearTimeout(this.creationTimer);
            this.account = payload.account || {};
            this.awaitBackend = !!payload.awaitBackend;
            this.holding = false;
            this.steps = this.buildSteps(payload.plan || this.plan);
            this.completedSteps = [];
            this.stepIndex = -1;
            this.mode = "creating";
            this.nextStep();
        },
        resetCreation() {
            clearTimeout(this.creationTimer);
            this.mode = "plan";
            this.stepIndex = -1;
            this.completedSteps = [];
            this.holding = false;
        },
        nextStep() {
            if (this.stepIndex >= 0 && this.steps[this.stepIndex]) {
                this.completedSteps.push({
                    id: this.stepIndex,
                    t: this.steps[this.stepIndex].t,
                });
            }
            this.stepIndex++;

            if (this.stepIndex >= this.steps.length) {
                this.$eventHub.$emit("guest-register:creation-finished");

                if (this.awaitBackend) {
                    this.holding = true;
                } else {
                    this.creationTimer = setTimeout(() => {
                        this.mode = "success";
                    }, 700);
                }
                return;
            }

            this.creationTimer = setTimeout(
                () => this.nextStep(),
                this.randomDelay()
            );
        },
    },
};
</script>
