<template>
    <div class="minitour-root" v-if="mounted">
        <div class="minitour-overlay" :class="{ active: active }">
            <svg>
                <defs>
                    <mask :id="maskId">
                        <rect width="100%" height="100%" fill="white" />
                        <rect
                            :x="hole.x"
                            :y="hole.y"
                            :width="hole.w"
                            :height="hole.h"
                            rx="12"
                            fill="black"
                        />
                    </mask>
                </defs>
                <rect
                    width="100%"
                    height="100%"
                    fill="rgba(16,24,40,.55)"
                    :mask="`url(#${maskId})`"
                />
            </svg>
        </div>

        <div class="minitour-ring" :class="{ active: active }" :style="ringStyle"></div>

        <div
            class="minitour-tooltip"
            :class="{ active: active }"
            :style="tipStyle"
            role="dialog"
            aria-modal="true"
            aria-live="polite"
            ref="tip"
        >
            <div class="minitour-arrow" :style="arrowStyle"></div>
            <div class="minitour-step-tag">
                {{ currentStep.tag }}
                <span v-if="currentStep.badge" class="minitour-badge">{{ currentStep.badge }}</span>
            </div>
            <h2>{{ currentStep.title }}</h2>
            <p v-html="currentStep.body"></p>
            <div class="minitour-footer">
                <div class="minitour-dots">
                    <span
                        v-for="(s, k) in steps"
                        :key="k"
                        class="minitour-dot"
                        :class="{ on: k === current }"
                    ></span>
                </div>
                <div class="minitour-actions">
                    <button type="button" class="minitour-btn ghost" @click="end(false)">Omitir</button>
                    <button type="button" class="btn btn-sm btn-primary" ref="next" @click="next">
                        {{ isLast ? 'Entendido' : 'Siguiente' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="minitour-toast" :class="{ show: toast }">
            <span class="minitour-check">✔</span> {{ doneText }}
        </div>

        <button
            v-if="showFab"
            type="button"
            class="minitour-fab"
            :class="{ hidden: active, nudge: nudge }"
            :style="{ right: avoidActive ? fabRightAvoid : fabRight }"
            :aria-label="fabLabel"
            :title="fabLabel"
            @click="start"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9" />
                <path d="M9.3 9a2.8 2.8 0 0 1 5.4 .9c0 1.8 -2.7 2.3 -2.7 3.6" />
                <line x1="12" y1="17" x2="12" y2="17.01" />
            </svg>
            <span class="minitour-fab-label">{{ fabLabel }}</span>
        </button>
    </div>
</template>

<script>
export default {
    name: "MiniTour",
    props: {
        steps: {
            type: Array,
            required: true,
        },
        storageKey: {
            type: String,
            required: true,
        },
        version: {
            type: [Number, String],
            default: 1,
        },
        auto: {
            type: Boolean,
            default: false,
        },
        autoDelay: {
            type: Number,
            default: 600,
        },
        doneText: {
            type: String,
            default: "¡Listo! Ya conoces las opciones del formulario.",
        },
        // Botón flotante para re-lanzar el tour
        showFab: {
            type: Boolean,
            default: true,
        },
        fabLabel: {
            type: String,
            default: "Ver tutorial",
        },
        // Si existe un elemento que matchee este selector (p. ej. el botón
        // flotante de WhatsApp), el FAB se desplaza para no encimarse.
        fabAvoidSelector: {
            type: String,
            default: "",
        },
        fabRight: {
            type: String,
            default: "22px",
        },
        fabRightAvoid: {
            type: String,
            default: "90px",
        },
    },
    data() {
        return {
            mounted: false,
            active: false,
            toast: false,
            nudge: false,
            avoidActive: false,
            current: -1,
            highlighted: null,
            hole: { x: 0, y: 0, w: 0, h: 0 },
            ringStyle: {},
            tipStyle: {},
            arrowStyle: {},
            PAD: 8,
            _nudged: false,
            _toastTimer: null,
            _autoTimer: null,
            _waitTimer: null,
            _nudgeTimer: null,
        };
    },
    computed: {
        maskId() {
            return `minitour-mask-${this._uid}`;
        },
        currentStep() {
            return this.steps[this.current] || {};
        },
        isLast() {
            return this.current === this.steps.length - 1;
        },
    },
    mounted() {
        this.mounted = true;
        if (this.fabAvoidSelector) {
            this.avoidActive = !!document.querySelector(this.fabAvoidSelector);
        }
        window.addEventListener("keydown", this.onKeydown);
        window.addEventListener("resize", this.reposition);
        window.addEventListener("scroll", this.reposition, { passive: true });

        if (this.auto && !this.hasSeen()) {
            this._autoTimer = setTimeout(() => this.start(), this.autoDelay);
        }
    },
    beforeDestroy() {
        window.removeEventListener("keydown", this.onKeydown);
        window.removeEventListener("resize", this.reposition);
        window.removeEventListener("scroll", this.reposition);
        clearTimeout(this._toastTimer);
        clearTimeout(this._autoTimer);
        clearInterval(this._waitTimer);
        clearTimeout(this._nudgeTimer);
        this.clearHighlight();
    },
    methods: {
        hasSeen() {
            try {
                return localStorage.getItem(this.storageKey) === String(this.version);
            } catch (e) {
                return false;
            }
        },
        markSeen() {
            try {
                localStorage.setItem(this.storageKey, String(this.version));
            } catch (e) {
                /* almacenamiento no disponible */
            }
        },
        waitForEl(selector, timeout = 6000, interval = 120) {
            return new Promise(resolve => {
                const found = document.querySelector(selector);
                if (found) return resolve(found);
                const started = Date.now();
                clearInterval(this._waitTimer);
                this._waitTimer = setInterval(() => {
                    const el = document.querySelector(selector);
                    if (el) {
                        clearInterval(this._waitTimer);
                        resolve(el);
                    } else if (Date.now() - started >= timeout) {
                        clearInterval(this._waitTimer);
                        resolve(null);
                    }
                }, interval);
            });
        },
        start() {
            if (!this.steps.length) return;
            this.toast = false;
            this.current = 0;
            this.showStep(0);
        },
        async showStep(i) {
            const step = this.steps[i];
            const el = await this.waitForEl(step.target);
            if (this.current !== i) return;
            if (!el) {
                if (i < this.steps.length - 1) {
                    this.current = i + 1;
                    this.showStep(this.current);
                } else {
                    this.end(false);
                }
                return;
            }

            this.clearHighlight();
            this.highlighted = el;
            el.classList.add("minitour-highlight");

            this.active = true;

            el.scrollIntoView({ block: "nearest", behavior: "smooth" });
            this.$nextTick(() => {
                requestAnimationFrame(() => this.positionFor(el));
                if (this.$refs.next) this.$refs.next.focus();
            });
        },
        positionFor(el) {
            if (!el) return;
            const r = el.getBoundingClientRect();
            const PAD = this.PAD;

            this.hole = {
                x: r.left - PAD,
                y: r.top - PAD,
                w: r.width + PAD * 2,
                h: r.height + PAD * 2,
            };

            this.ringStyle = {
                left: r.left - PAD + "px",
                top: r.top - PAD + "px",
                width: r.width + PAD * 2 + "px",
                height: r.height + PAD * 2 + "px",
            };

            const tipEl = this.$refs.tip;
            const tw = tipEl ? tipEl.offsetWidth : 330;
            const th = tipEl ? tipEl.offsetHeight : 160;
            let top = r.bottom + PAD + 16;
            let left = r.left + r.width / 2 - tw / 2;
            left = Math.max(16, Math.min(left, window.innerWidth - tw - 16));
            let arrowOnTop = true;
            if (top + th > window.innerHeight - 16) {
                top = r.top - PAD - th - 16;
                arrowOnTop = false;
            }
            this.tipStyle = { top: top + "px", left: left + "px" };

            const ax = r.left + r.width / 2 - left - 7;
            this.arrowStyle = {
                left: Math.max(14, Math.min(ax, tw - 28)) + "px",
                top: arrowOnTop ? "-7px" : "auto",
                bottom: arrowOnTop ? "auto" : "-7px",
            };
        },
        reposition() {
            if (this.current < 0) return;
            const step = this.steps[this.current];
            if (step) this.positionFor(document.querySelector(step.target));
        },
        next() {
            if (this.current < this.steps.length - 1) {
                this.current++;
                this.showStep(this.current);
            } else {
                this.end(true);
            }
        },
        end(finished) {
            this.active = false;
            this.clearHighlight();
            this.current = -1;
            this.markSeen();
            // El FAB reaparece con un pulso sutil, solo la primera vez
            if (this.showFab && !this._nudged) {
                this._nudged = true;
                this.nudge = true;
                clearTimeout(this._nudgeTimer);
                this._nudgeTimer = setTimeout(() => {
                    this.nudge = false;
                }, 1700);
            }
            if (finished) {
                this.toast = true;
                clearTimeout(this._toastTimer);
                this._toastTimer = setTimeout(() => {
                    this.toast = false;
                }, 2800);
            }
        },
        clearHighlight() {
            if (this.highlighted) {
                this.highlighted.classList.remove("minitour-highlight");
                this.highlighted = null;
            }
        },
        onKeydown(e) {
            if (this.current < 0) return;
            if (e.key === "Escape") this.end(false);
            if (e.key === "Enter" || e.key === "ArrowRight") this.next();
        },
    },
};
</script>

<style>

.minitour-overlay {
    position: fixed;
    inset: 0;
    z-index: 3000;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.minitour-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.minitour-overlay svg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}

.minitour-highlight {
    position: relative;
    z-index: 3001 !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

.minitour-ring {
    position: fixed;
    z-index: 3002;
    border-radius: 12px;
    pointer-events: none;
    box-shadow: 0 0 0 3px #2563eb;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
}
.minitour-ring.active {
    opacity: 1;
    animation: minitour-pulse 1.8s ease-out infinite;
}
@keyframes minitour-pulse {
    0% {
        box-shadow: 0 0 0 3px #2563eb, 0 0 0 3px rgba(37, 99, 235, 0.45);
    }
    70% {
        box-shadow: 0 0 0 3px #2563eb, 0 0 0 14px rgba(37, 99, 235, 0);
    }
    100% {
        box-shadow: 0 0 0 3px #2563eb, 0 0 0 14px rgba(37, 99, 235, 0);
    }
}

.minitour-tooltip {
    position: fixed;
    z-index: 3003;
    width: 330px;
    max-width: calc(100vw - 32px);
    background: #1d2939;
    color: #f9fafb;
    border-radius: 14px;
    padding: 20px 20px 16px;
    box-shadow: 0 20px 45px rgba(16, 24, 40, 0.35);
    opacity: 0;
    transform: translateY(6px);
    transition: opacity 0.3s ease, transform 0.3s ease,
        top 0.35s cubic-bezier(0.4, 0, 0.2, 1),
        left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}
.minitour-tooltip.active {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}
.minitour-arrow {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #1d2939;
    transform: rotate(45deg);
    border-radius: 2px;
}
.minitour-step-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #93b4f8;
    margin-bottom: 10px;
}
.minitour-badge {
    background: #12b76a;
    color: #fff;
    font-size: 0.66rem;
    padding: 2px 8px;
    border-radius: 99px;
    letter-spacing: 0.04em;
    text-transform: none;
}
.minitour-tooltip h2 {
    font-size: 1.05rem !important;
    font-weight: 700;
    margin: 0 0 8px !important;
    letter-spacing: -0.01em;
    color: #f9fafb !important;
    line-height: normal;
}
.minitour-tooltip p {
    font-size: 0.88rem;
    line-height: 1.55;
    color: #d0d5dd !important;
    margin: 0;
}
.minitour-tooltip p b {
    color: #fff;
    font-weight: 600;
}

.minitour-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 18px;
}
.minitour-dots {
    display: flex;
    gap: 6px;
}
.minitour-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #475467;
    transition: background 0.2s, width 0.2s;
}
.minitour-dot.on {
    background: #93b4f8;
    width: 20px;
    border-radius: 99px;
}
.minitour-actions {
    display: flex;
    gap: 8px;
}
.minitour-btn {
    border: none;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
}
.minitour-btn.ghost {
    background: transparent;
    color: #98a2b3;
}
.minitour-btn.ghost:hover {
    color: #e4e7ec;
}
.minitour-btn.primary {
    background: #2563eb;
    color: #fff;
}
.minitour-btn.primary:hover {
    background: #1d4fd7;
}

.minitour-toast {
    position: fixed;
    bottom: 26px;
    left: 50%;
    transform: translate(-50%, 20px);
    background: #1d2939;
    color: #fff;
    padding: 13px 22px;
    border-radius: 99px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.35s, transform 0.35s;
    z-index: 3010;
    pointer-events: none;
    box-shadow: 0 12px 30px rgba(16, 24, 40, 0.3);
}
.minitour-toast.show {
    opacity: 1;
    transform: translate(-50%, 0);
}
.minitour-check {
    color: #12b76a;
    font-size: 1.05rem;
}

.minitour-fab {
    position: fixed;
    bottom: 22px;
    right: 22px;
    z-index: 1030;
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid #e4e7ec;
    background: #fff;
    color: #667085;
    border-radius: 99px;
    height: 40px;
    padding: 0 11px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(16, 24, 40, 0.08);
    opacity: 0.55;
    transition: opacity 0.2s, box-shadow 0.2s, color 0.2s;
    overflow: hidden;
    white-space: nowrap;
    font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
}
.minitour-fab:hover,
.minitour-fab:focus-visible {
    opacity: 1;
    color: #2563eb;
    box-shadow: 0 4px 14px rgba(16, 24, 40, 0.14);
}
.minitour-fab-label {
    max-width: 0;
    opacity: 0;
    font-size: 0.85rem;
    font-weight: 600;
    transition: max-width 0.25s ease, opacity 0.2s ease, margin 0.25s ease;
    margin-left: 0;
}
.minitour-fab:hover .minitour-fab-label,
.minitour-fab:focus-visible .minitour-fab-label {
    max-width: 120px;
    opacity: 1;
    margin-left: 8px;
}
.minitour-fab.hidden {
    opacity: 0;
    pointer-events: none;
}
.minitour-fab.nudge {
    animation: minitour-nudge 1.6s ease 1;
}
@keyframes minitour-nudge {
    0%,
    100% {
        transform: scale(1);
    }
    15% {
        transform: scale(1.12);
    }
    30% {
        transform: scale(1);
    }
    45% {
        transform: scale(1.08);
    }
    60% {
        transform: scale(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .minitour-ring.active {
        animation: none;
    }
    .minitour-overlay,
    .minitour-ring,
    .minitour-tooltip {
        transition: none;
    }
    .minitour-fab.nudge {
        animation: none;
    }
    .minitour-fab-label {
        transition: none;
    }
}
</style>
