<template>
    <div class="col-md-12 pr-0 pl-0 mt-1">
        <div class="form-group">
            <label class="control-label mt-1">Cambiar Sucursal:</label>

            <!-- <button
            aria-expanded="false"
            class="btn btn-custom btn-sm mt-2 mr-2 dropdown-toggle"
            data-toggle="dropdown"
            type="button"
        >
                <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-list-numbers"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 6h9" /><path d="M11 12h9" /><path d="M12 18h8" /><path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" /><path d="M6 10v-6l-2 2" /></svg>
                    Cambiar Sucursal
                <span class="caret"></span>
        </button> -->
            <select
                class="el-input__inner input-select-establishment"
                name="establishment_selector_header"
                autocomplete="off"
                data-establishment-selector
                @pointerdown="markGesture"
                @mousedown="markGesture"
                @touchstart="markGesture"
                @keydown="markGesture"
                @change="clickChangeEstablishment()"
                v-model="establishment_id"
            >
                <option
                    v-for="item in establishments"
                    :key="item.id"
                    :label="item.description"
                    :value="item.id"
                >
                </option>
            </select>
            <!-- <div
            class="dropdown-menu"
            role="menu"
            style="
                position: absolute;
                will-change: transform;
                top: 0px;
                left: 0px;
                transform: translate3d(0px, 42px, 0px);
                "
            x-placement="bottom-start"
            >
            <a v-for="item in establishments" :key="item.id" :value="item.id" :label="item.description"
                class="dropdown-item text-1"
                href="#"
                @click.prevent="clickChangeEstablishment(item.id)"
                        > {{ item.description }}</a>
        </div> -->
        </div>
    </div>
</template>
<script>
export default {
    props: {
        establishments: {
            type: Array,
            required: true
        },
        current_establishment: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            establishment_id: undefined,
            // Sucursal realmente guardada; se mantiene al día tras cada cambio.
            current_id: undefined,
            last_gesture_at: 0
        };
    },
    mounted() {
        const shared = window.establishmentState && window.establishmentState.current;
        this.current_id = parseInt(shared || this.current_establishment, 10) || undefined;
        this.establishment_id = this.current_id;

        // Al restaurar la pestaña (suspensión/reposo del equipo, botón atrás) el
        // navegador puede reponer otro valor en el <select>; se descarta el gesto
        // pendiente y se devuelve el selector a la sucursal real.
        this._onPageShow = () => {
            this.last_gesture_at = 0;
            this.establishment_id = this.current_id;
        };
        window.addEventListener("pageshow", this._onPageShow);
        document.addEventListener("visibilitychange", this._onPageShow);
    },
    beforeDestroy() {
        if (this._onPageShow) {
            window.removeEventListener("pageshow", this._onPageShow);
            document.removeEventListener("visibilitychange", this._onPageShow);
        }
    },
    methods: {
        markGesture() {
            this.last_gesture_at = Date.now();
        },
        clickChangeEstablishment() {
            const next_id = parseInt(this.establishment_id, 10);

            // Valor inválido: nunca se envía, dejaría al usuario sin sucursal.
            if (!next_id) {
                this.establishment_id = this.current_id;
                return;
            }

            // Sólo se guarda si el cambio lo hizo el usuario y es real.
            if (Date.now() - this.last_gesture_at > 5000) {
                this.establishment_id = this.current_id;
                return;
            }

            if (next_id === this.current_id) {
                return;
            }

            this.loading = true;
            const payload = {
                establishment_id: next_id
            };
            this.$http
                .post(`/hotels/reception/change-user-establishment`, payload)
                .then(response => {
                    this.current_id = response.data.establishment_id || next_id;
                    this.establishment_id = this.current_id;

                    // Mantiene alineado el estado del selector del sidebar.
                    if (window.establishmentState) {
                        window.establishmentState.current = this.current_id;
                    }

                    this.$message({
                        type: "success",
                        message: response.data.message
                    });

                    this.$eventHub.$emit('establishmentChanged', this.current_id);
                    //location.reload();
                })
                .catch(() => {
                    this.establishment_id = this.current_id;
                })
                .finally(() => (this.loading = false));
        }
    }
};
</script>
