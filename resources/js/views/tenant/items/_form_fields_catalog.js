/**
 * Catálogo de campos nativos del formulario de ítem.
 *
 * Fuente única de verdad para la barra fija personalizable.
 * Define qué field_key se puede fijar, su label, en qué pestaña vive,
 * su ancho por defecto (1..12 sobre grilla de 12), y para qué variantes está disponible.
 *
 * Para agregar un campo pineable nuevo:
 *   1) Añadir la entrada aquí.
 *   2) Soportar su render en `form.vue` como `<template #<field_key>>` dentro de `<item-form-pinned-bar>`.
 *   3) Marcar el wrapper original del campo en `form.vue` con la clase `field-pinnable`
 *      + `:data-field-key="..."` + `v-show="!isPinned('<key>')"` para evitar duplicación y permitir el
 *      botón "Fijar arriba" en modo edición.
 */

const VARIANTS = Object.freeze({
    STANDARD: 'standard',
    ECOMMERCE: 'ecommerce',
    RESTAURANT: 'restaurant',
});

const ALL_VARIANTS = [VARIANTS.STANDARD, VARIANTS.ECOMMERCE, VARIANTS.RESTAURANT];

const TAB_LABELS = {
    top:        'Principal',
    general:    'General',
    warehouses: 'Almacenes',
    attributes: 'Atributos',
    purchase:   'Compra',
};

const INPUT_TYPE_LABELS = {
    text:                      'Texto',
    number:                    'Número',
    price:                     'Número',
    boolean:                   'Sí / No',
    date:                      'Fecha',
    'select-unit-type':        'Selección',
    'select-currency-type':    'Selección',
    'select-affectation-igv':  'Selección',
    'select-category':         'Selección',
    'select-brand':            'Selección',
    'select-warehouse':        'Selección',
};

const FIELDS = [
    {
        key: 'internal_id',
        label: 'Código Interno',
        type: 'text',
        tab: 'top',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'description',
        label: 'Nombre',
        type: 'text',
        tab: 'top',
        defaultWidth: 6,
        availableForVariants: ALL_VARIANTS,
        required: true,
    },
    {
        key: 'sale_unit_price',
        label: 'Precio Unitario',
        type: 'price',
        tab: 'top',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
        required: true,
    },
    {
        key: 'has_igv',
        label: 'Incluye IGV',
        type: 'boolean',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'has_plastic_bag_taxes',
        label: 'Impuesto a la bolsa plástica',
        type: 'boolean',
        tab: 'general',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'calculate_quantity',
        label: 'Calcular cantidad por precio',
        type: 'boolean',
        tab: 'general',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'second_name',
        label: 'Nombre secundario',
        type: 'text',
        tab: 'general',
        defaultWidth: 6,
        availableForVariants: [VARIANTS.STANDARD, VARIANTS.ECOMMERCE],
    },
    {
        key: 'name',
        label: 'Descripción',
        type: 'text',
        tab: 'general',
        defaultWidth: 6,
        availableForVariants: [VARIANTS.STANDARD, VARIANTS.ECOMMERCE],
    },
    {
        key: 'model',
        label: 'Modelo',
        type: 'text',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: [VARIANTS.STANDARD, VARIANTS.ECOMMERCE],
    },
    {
        key: 'unit_type_id',
        label: 'Unidad',
        type: 'select-unit-type',
        tab: 'general',
        defaultWidth: 2,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'currency_type_id',
        label: 'Moneda',
        type: 'select-currency-type',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'sale_affectation_igv_type_id',
        label: 'Tipo de afectación',
        type: 'select-affectation-igv',
        tab: 'general',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'barcode',
        label: 'Código de barra',
        type: 'text',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'stock',
        label: 'Stock Inicial',
        type: 'number',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'stock_min',
        label: 'Stock Mínimo',
        type: 'number',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'warehouse_id',
        label: 'Almacén',
        type: 'select-warehouse',
        tab: 'general',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'category_id',
        label: 'Categoría',
        type: 'select-category',
        tab: 'attributes',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'brand_id',
        label: 'Marca',
        type: 'select-brand',
        tab: 'attributes',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'purchase_unit_price',
        label: 'Precio Unitario (Compra)',
        type: 'price',
        tab: 'purchase',
        defaultWidth: 3,
        availableForVariants: [VARIANTS.STANDARD],
    },
    {
        key: 'purchase_affectation_igv_type_id',
        label: 'Tipo de afectación (Compra)',
        type: 'select-affectation-igv',
        tab: 'purchase',
        defaultWidth: 4,
        availableForVariants: [VARIANTS.STANDARD],
    },
];

const DEFAULT_LAYOUT_BY_VARIANT = {
    [VARIANTS.STANDARD]: [
        { field_key: 'internal_id', width: 3, order: 0 },
        { field_key: 'description', width: 6, order: 1 },
        { field_key: 'sale_unit_price', width: 3, order: 2 },
    ],
    [VARIANTS.ECOMMERCE]: [
        { field_key: 'internal_id', width: 3, order: 0 },
        { field_key: 'description', width: 6, order: 1 },
        { field_key: 'sale_unit_price', width: 3, order: 2 },
    ],
    [VARIANTS.RESTAURANT]: [
        { field_key: 'internal_id', width: 3, order: 0 },
        { field_key: 'description', width: 6, order: 1 },
        { field_key: 'sale_unit_price', width: 3, order: 2 },
    ],
};

function getCatalog() {
    return FIELDS;
}

function getFieldByKey(key) {
    return FIELDS.find(f => f.key === key) || null;
}

function getAvailableFields(variant) {
    return FIELDS.filter(f => f.availableForVariants.includes(variant));
}

function getDefaultLayout(variant) {
    return (DEFAULT_LAYOUT_BY_VARIANT[variant] || []).map(item => ({ ...item }));
}

function getTabLabel(tab) {
    return TAB_LABELS[tab] || tab;
}

function getInputTypeLabel(type) {
    return INPUT_TYPE_LABELS[type] || type;
}

/**
 * Devuelve los campos disponibles para una variante agrupados por pestaña,
 * en el orden en que aparecen en TAB_LABELS.
 */
function getAvailableFieldsGrouped(variant) {
    const fields = getAvailableFields(variant);
    const tabOrder = Object.keys(TAB_LABELS);
    const groups = {};
    fields.forEach(f => {
        if (!groups[f.tab]) groups[f.tab] = [];
        groups[f.tab].push(f);
    });
    return tabOrder
        .filter(tab => groups[tab] && groups[tab].length > 0)
        .map(tab => ({
            tab,
            label: getTabLabel(tab),
            fields: groups[tab],
        }));
}

export {
    VARIANTS,
    FIELDS,
    TAB_LABELS,
    INPUT_TYPE_LABELS,
    DEFAULT_LAYOUT_BY_VARIANT,
    getCatalog,
    getFieldByKey,
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getTabLabel,
    getInputTypeLabel,
};

export default {
    VARIANTS,
    FIELDS,
    TAB_LABELS,
    INPUT_TYPE_LABELS,
    DEFAULT_LAYOUT_BY_VARIANT,
    getCatalog,
    getFieldByKey,
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getTabLabel,
    getInputTypeLabel,
};
