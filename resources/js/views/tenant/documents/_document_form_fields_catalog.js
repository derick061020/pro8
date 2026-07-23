/**
 * Catálogo de campos de los datos generales personalizables del formulario de comprobante
 * (resources/js/views/tenant/documents/invoice_generate.vue).
 */

const VARIANTS = Object.freeze({
    INVOICE: 'invoice',
});

const ALL_VARIANTS = [VARIANTS.INVOICE];

const GROUP_LABELS = {
    main:       'Principal',
    additional: 'Información adicional',
};

const INPUT_TYPE_LABELS = {
    text:                    'Texto',
    textarea:                'Texto',
    number:                  'Número',
    date:                    'Fecha',
    'select-customer':       'Selección',
    'select-address':        'Selección',
    'select-currency-type':  'Selección',
    'select-consigned':      'Selección',
    'select-itinerant':      'Selección',
    'select-seller':         'Selección',
};

const FIELDS = [
    {
        key: 'customer_id',
        label: 'Cliente',
        type: 'select-customer',
        group: 'main',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
        required: true,
    },
    {
        key: 'customer_address_id',
        label: 'Dirección',
        type: 'select-address',
        group: 'main',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'date_of_issue',
        label: 'Fec. Emisión',
        type: 'date',
        group: 'main',
        defaultWidth: 2,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'date_of_due',
        label: 'Fec. Vencimiento',
        type: 'date',
        group: 'main',
        defaultWidth: 2,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'consigned_id',
        label: 'Consignado',
        type: 'select-consigned',
        group: 'main',
        defaultWidth: 6,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra si habilitas "Consignado" en la configuración y tienes consignados registrados.',
    },
    {
        key: 'consigned_address_id',
        label: 'Dirección (Consignado)',
        type: 'select-address',
        group: 'main',
        defaultWidth: 6,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra si habilitas "Consignado" en la configuración y tienes consignados registrados.',
    },
    {
        key: 'currency_type_id',
        label: 'Moneda',
        type: 'select-currency-type',
        group: 'main',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra si tienes más de un tipo de moneda configurado.',
    },
    {
        key: 'exchange_rate_sale',
        label: 'Tipo de cambio',
        type: 'number',
        group: 'main',
        defaultWidth: 3,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra si tienes más de un tipo de moneda configurado.',
    },
    {
        key: 'itinerant_option_id',
        label: 'Punto de venta itinerante',
        type: 'select-itinerant',
        group: 'main',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra en operaciones de venta itinerante (tipo 0101 con punto de venta itinerante).',
    },
    {
        key: 'ruc_itinerant',
        label: 'Ruc del establecimiento',
        type: 'text',
        group: 'main',
        defaultWidth: 4,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra cuando el punto de venta itinerante requiere el RUC del establecimiento.',
    },
    {
        key: 'purchase_order',
        label: 'Orden de Compra',
        type: 'textarea',
        group: 'additional',
        defaultWidth: 12,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'additional_information',
        label: 'Observaciones',
        type: 'textarea',
        group: 'additional',
        defaultWidth: 12,
        availableForVariants: ALL_VARIANTS,
    },
    {
        key: 'plate_number',
        label: 'N° Placa',
        type: 'textarea',
        group: 'additional',
        defaultWidth: 12,
        availableForVariants: ALL_VARIANTS,
        conditionHint: 'Solo se muestra si tienes activo el giro de negocio "TAP".',
    },
    {
        key: 'seller_id',
        label: 'Vendedor',
        type: 'select-seller',
        group: 'additional',
        defaultWidth: 6,
        availableForVariants: ALL_VARIANTS,
    },
];

const DEFAULT_LAYOUT_BY_VARIANT = {
    [VARIANTS.INVOICE]: [
        { field_key: 'customer_id', width: 4, order: 0 },
        { field_key: 'customer_address_id', width: 4, order: 1 },
        { field_key: 'date_of_issue', width: 2, order: 2 },
        { field_key: 'date_of_due', width: 2, order: 3 },
        { field_key: 'consigned_id', width: 6, order: 4 },
        { field_key: 'consigned_address_id', width: 6, order: 5 },
        { field_key: 'currency_type_id', width: 3, order: 6 },
        { field_key: 'exchange_rate_sale', width: 3, order: 7 },
        { field_key: 'itinerant_option_id', width: 4, order: 8 },
        { field_key: 'ruc_itinerant', width: 4, order: 9 },
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

function getGroupLabel(group) {
    return GROUP_LABELS[group] || group;
}

function getInputTypeLabel(type) {
    return INPUT_TYPE_LABELS[type] || type;
}

/**
 * Devuelve los campos disponibles para una variante agrupados por origen,
 * en el orden en que aparecen en GROUP_LABELS.
 */
function getAvailableFieldsGrouped(variant) {
    const fields = getAvailableFields(variant);
    const groupOrder = Object.keys(GROUP_LABELS);
    const groups = {};
    fields.forEach(f => {
        if (!groups[f.group]) groups[f.group] = [];
        groups[f.group].push(f);
    });
    return groupOrder
        .filter(group => groups[group] && groups[group].length > 0)
        .map(group => ({
            tab: group,
            label: getGroupLabel(group),
            fields: groups[group],
        }));
}

export {
    VARIANTS,
    FIELDS,
    GROUP_LABELS,
    INPUT_TYPE_LABELS,
    DEFAULT_LAYOUT_BY_VARIANT,
    getCatalog,
    getFieldByKey,
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getGroupLabel,
    getInputTypeLabel,
};

export default {
    VARIANTS,
    FIELDS,
    GROUP_LABELS,
    INPUT_TYPE_LABELS,
    DEFAULT_LAYOUT_BY_VARIANT,
    getCatalog,
    getFieldByKey,
    getAvailableFields,
    getAvailableFieldsGrouped,
    getDefaultLayout,
    getGroupLabel,
    getInputTypeLabel,
};
