export default {
    '/documents/create': [
        { 
            element: '.module-title-marker', 
            popover: { 
                title: 'Nuevo Comprobante', 
                description: 'Aquí comienza la emisión de tus comprobantes electrónicos.',
                side: 'bottom'
            } 
        },
        { 
            element: '.inputs-container', 
            popover: { 
                title: 'Datos Principales', 
                description: 'Aquí seleccionas el tipo de comprobante, serie, fecha y moneda.',
                side: 'bottom'
            } 
        },
        { 
            element: '.tour-step-cliente', 
            popover: { 
                title: 'Cliente', 
                description: 'Busca o registra rápidamente a tu cliente aquí.',
                side: 'bottom'
            } 
        },
        { 
            element: '.table', 
            popover: { 
                title: 'Productos y Servicios', 
                description: 'Añade los ítems a facturar usando el botón de búsqueda o agregándolos directamente.',
                side: 'top'
            } 
        },
        { 
            element: '#btn-submit', 
            popover: { 
                title: 'Generar', 
                description: 'Una vez completado el formulario, haz clic aquí para generar el comprobante.',
                side: 'top'
            } 
        }
    ],
    '/persons/customers': [
        {
            element: '.page-header',
            popover: {
                title: 'Gestión de Clientes',
                description: 'En este módulo puedes administrar tu cartera de clientes, exportar e importar datos.',
                side: 'bottom'
            }
        },
        {
            element: '.right-wrapper button:nth-of-type(3)',
            popover: {
                title: 'Nuevo Cliente',
                description: 'Haz clic en Siguiente y te mostraremos el formulario de creación.',
                side: 'left',
                onNextClick: (element, step, opts) => {
                    const btnNew = document.querySelector('.right-wrapper button:nth-of-type(3)');
                    if (btnNew) {
                        btnNew.click(); // Abrir el modal simulando el click
                    }
                    // Esperamos a que la animación de apertura de ElementUI termine (aprox 300-400ms)
                    setTimeout(() => {
                        opts.driver.moveNext();
                    }, 400);
                }
            }
        },
        {
            element: '.tour-person-form',
            popover: {
                title: 'Formulario de Cliente',
                description: 'Aquí puedes rellenar todos los datos. Si pones el RUC o DNI y haces clic en el botón SUNAT/RENIEC, buscará automáticamente los datos.',
                side: 'left',
                onNextClick: (element, step, opts) => {
                    const closeBtn = document.querySelector('.el-dialog__headerbtn');
                    if(closeBtn) closeBtn.click(); // Opcional: cerrar el modal al continuar
                    opts.driver.moveNext();
                }
            }
        }
    ],
    '/items': [
        {
            element: '.tour-step-items-table',
            popover: {
                title: 'Listado de Productos',
                description: 'En esta sección verás los productos creados/listados.',
                side: 'top'
            }
        },
        {
            element: '.tour-step-btn-new',
            popover: {
                title: 'Nuevo Producto',
                description: 'En el botón Nuevo se puede crear un nuevo producto.',
                side: 'left',
                onNextClick: (element, step, opts) => {
                    const btnNew = document.querySelector('.tour-step-btn-new');
                    if (btnNew) {
                        btnNew.click();
                    }
                    setTimeout(() => {
                        opts.driver.moveNext();
                    }, 400);
                }
            }
        },
        {
            element: '.tour-step-basic-info',
            popover: {
                title: 'Datos Básicos',
                description: 'Se mostrarán los 3 datos obligatorios principales (básicos) y una breve descripción.',
                side: 'bottom'
            }
        },
        {
            element: '#tab-first',
            popover: {
                title: 'Sección General',
                description: 'Selecciona la sección de General para más configuraciones.',
                side: 'bottom',
                onNextClick: (element, step, opts) => {
                    const tabGeneral = document.querySelector('#tab-first');
                    if (tabGeneral) {
                        tabGeneral.click();
                    }
                    setTimeout(() => {
                        opts.driver.moveNext();
                    }, 300);
                }
            }
        },
        {
            element: '.tour-step-stock-inicial',
            popover: {
                title: 'Stock Inicial',
                description: 'Aquí puedes ingresar el stock inicial del producto.',
                side: 'top'
            }
        },
        {
            element: '.tour-step-almacen',
            popover: {
                title: 'Almacén',
                description: 'Selecciona el almacén al que ingresará este stock.',
                side: 'top'
            }
        },
        {
            element: '.tour-step-tipo-afectacion',
            popover: {
                title: 'Tipo de Afectación',
                description: 'Selecciona el tipo de afectación para el IGV de este producto.',
                side: 'top'
            }
        }
    ]
};
