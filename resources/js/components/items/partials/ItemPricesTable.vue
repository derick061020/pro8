<template>
    <div class="item-prices-table my-0">
        <div class="price-labels-container">
            <div v-for="(price, index) in localPrices" :key="index" class="mb-3">
                <div class="form-group">
                    <label class="control-label">{{ price.label }}</label>
                    <el-input
                        class="mt-1"
                        type="number"
                        size="small"
                        v-model.number="price.price"
                        step="0.01"
                        :min="0"
                        @input="emitChanges"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.price-labels-container{
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
    gap: 1rem;
}
</style>
<script>
export default {
    name: 'ItemPricesTable',
    props: {
        value: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            localPrices: [],
            availableLabels: [],
        }
    },
    created() {
        this.loadPriceLabels();
    },
    watch: {
        value: {
            handler(newVal) {
                if (newVal && newVal.length > 0) {
                    this.localPrices = newVal.map(p => ({...p}));
                } else if (this.localPrices.length === 0) {
                    this.localPrices = this.initializePrices(newVal);
                }
            },
            immediate: true,
            deep: true
        },
        availableLabels: {
            handler(newVal) {
                // Sincronizar precios con labels disponibles
                if (newVal && newVal.length > 0) {
                    this.syncPricesWithLabels();
                }
            }
        }
    },
    methods: {
        /**
         * Carga las etiquetas de precios desde la API
         */
        async loadPriceLabels() {
            try {
                const response = await this.$http.get('/price-labels/active');
                this.availableLabels = response.data.data || [];
            } catch (error) {
                console.error('Error al cargar price labels:', error);
                this.availableLabels = [];
            }
        },

        /**
         * Inicializa los precios basándose en los labels disponibles
         */
        initializePrices(prices) {
            if (prices && prices.length > 0) {
                return prices.map(p => ({...p}));
            }

            // Crear estructura desde availableLabels
            if (this.availableLabels.length > 0) {
                return this.availableLabels.map(label => ({
                    position: label.position,
                    label: label.label,
                    price: 0,
                    is_active: true,
                    price_label_id: label.id
                }));
            }

            return [];
        },

        /**
         * Sincroniza los precios locales con los labels disponibles
         * Agrega labels faltantes o elimina los que ya no existen
         */
        syncPricesWithLabels() {
            if (this.availableLabels.length === 0) return;

            // Si no hay precios, inicializar todos
            if (this.localPrices.length === 0) {
                this.localPrices = this.initializePrices(null);
                return;
            }

            // Obtener IDs de labels actuales en localPrices
            const existingLabelIds = this.localPrices.map(p => p.price_label_id).filter(Boolean);

            // Agregar labels que faltan
            this.availableLabels.forEach(label => {
                if (!existingLabelIds.includes(label.id)) {
                    this.localPrices.push({
                        position: label.position,
                        label: label.label,
                        price: 0,
                        is_active: true,
                        price_label_id: label.id
                    });
                }
            });

            // Eliminar precios cuyos labels ya no existen
            const availableLabelIds = this.availableLabels.map(l => l.id);
            this.localPrices = this.localPrices.filter(p =>
                !p.price_label_id || availableLabelIds.includes(p.price_label_id)
            );

            // Ordenar por position
            this.localPrices.sort((a, b) => a.position - b.position);
        },

        /**
         * Emite los cambios al componente padre
         */
        emitChanges() {
            this.$emit('input', this.localPrices);
        }
    }
}
</script>

<style scoped>
.item-prices-table {
    margin: 15px 0;
}
</style>
