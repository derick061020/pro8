import './bootstrap';

import Vue from 'vue'
import store from './store'
import ElementUI from 'element-ui'

import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'

// Cargar Bootstrap PRIMERO
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.js'; // Incluye Popper

// Luego Element UI
import '../sass/element-ui.scss';
import 'element-ui/lib/theme-chalk/index.css';
import { MessageBox } from 'element-ui'
import Swal from 'sweetalert2'

// Personalizar textos del idioma español
lang.el.select.noData = 'No se encontraron resultados'
lang.el.cascader.noData = 'No se encontraron resultados'
lang.el.transfer.noData = 'No se encontraron resultados'
lang.el.table.emptyText = 'No se encontraron resultados'
lang.el.tree.emptyText = 'No se encontraron resultados'
lang.el.empty.description = 'No se encontraron resultados'

locale.use(lang)

ElementUI.Select.computed.readonly = function () {
    const isIE = !this.$isServer && !Number.isNaN(Number(document.documentMode));
    return !(this.filterable || this.multiple || !isIE) && !this.visible;
};

export default ElementUI;

Vue.use(ElementUI, { size: 'small' })
// Interceptor global: sesión vencida por inactividad (419)
let sessionExpiredShown = false;
Vue.prototype.$eventHub = new Vue()

// Tenant app: only tenant components here
import './tenant-components'
if (Vue.prototype.$http) {
    Vue.prototype.$http.interceptors.response.use(
        response => response,
        error => {
            if (error.response && error.response.status === 419 && !sessionExpiredShown) {
                sessionExpiredShown = true;
                Swal.fire({
                    title: 'Sesión cerrada por inactividad',
                    text: 'Por seguridad tu sesión expiró. Pulsa Continuar para recargar y seguir trabajando.',
                    icon: 'warning',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#5b21b6', // morado tipo botón Guardar
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => window.location.reload());
                return new Promise(() => {});
            }
            return Promise.reject(error);
        }
    );
}

// Importar scripts migrados desde dom-fixes.js
import { applyThemeAndShowContent, setupHeaderDomEvents, setupEcommerceAuthHandlers, updateTenantPageTitle } from './tenant/dom-fixes';

// Inicializar lógica DOM migrada
if (window && window.vc_visual) {
    applyThemeAndShowContent(window.vc_visual.sidebar_theme, window.vc_visual.black_theme);
} else {
    applyThemeAndShowContent();
}
setupHeaderDomEvents();
setupEcommerceAuthHandlers();
updateTenantPageTitle();

// System reports moved to system.js


import VueClipboard from 'vue-clipboard2'
Vue.use(VueClipboard)


import VueApexCharts from 'vue-apexcharts'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)


import moment from 'moment';

// Formato de fecha/hora configurable por el usuario (Configuración general > formato).
// El valor vive en store.state.config (hidratado desde el backend vía getCollectionData)
// y se almacena como token de moment.js. Si todavía no está disponible se usa el
// formato histórico para no alterar la vista en instalaciones existentes.
const DATE_PARSE_FORMATS = ['YYYY-MM-DD HH:mm:ss', moment.ISO_8601, 'YYYY-MM-DD', 'DD-MM-YYYY', 'DD/MM/YYYY'];
const TIME_PARSE_FORMATS = ['HH:mm:ss', 'HH:mm', moment.ISO_8601];

function configDateFormat() {
    return (store.state.config && store.state.config.date_format) || 'DD-MM-YYYY';
}

function configTimeFormat() {
    return (store.state.config && store.state.config.time_format) || 'HH:mm:ss';
}

// Las fechas llegan en formatos mixtos según la vista (ISO desde unas, DD-MM-YYYY desde
// otras), por eso se intenta primero un parseo estricto multi-formato y, si falla, se cae
// al parseo permisivo de moment (comportamiento previo del filtro).
function parseFlexible(value, formats) {
    const m = moment(value, formats, true);
    return m.isValid() ? m : moment(value);
}

// El resto (MM, HH, hh, mm, ss) coincide. Esta función traduce el token guardado
// (moment) al token que entiende el prop `format` de el-date-picker.
// IMPORTANTE: solo afecta el display del input; el `value-format` (lo que va al backend)
// se mantiene en ISO en cada componente que lo usa, así la request al backend no cambia.
function momentToElementUIFormat(token) {
    if (!token) return '';
    return String(token).replace(/YYYY/g, 'yyyy').replace(/DD/g, 'dd').replace(/\bA\b/g, 'a');
}

Vue.mixin({
    computed: {
        dpDateFormat() {
            return momentToElementUIFormat(configDateFormat());
        },
        dpTimeFormat() {
            return momentToElementUIFormat(configTimeFormat());
        }
    }
});

Vue.mixin({
    filters: {
        toDecimals(number, decimal = 2) {
            return Number(number).toFixed(decimal);
        },
        DecimalText: function (number, decimal = 2) {
            return isNaN(parseFloat(number)) ? number : Number(number).toFixed(decimal);
        },
        toDate(date) {
            if (date) {
                return parseFlexible(date, DATE_PARSE_FORMATS).format(configDateFormat());
            }
            return '';
        },
        toTime(time) {
            if (time) {
                const fmt = configTimeFormat();
                if (typeof time === 'string' && time.length === 5) {
                    return moment(time + ':00', 'HH:mm:ss').format(fmt);
                }
                return parseFlexible(time, TIME_PARSE_FORMATS).format(fmt);
            }
            return '';
        },
        pad(value, fill = '', length = 3) {
            if (value) {
                return String(value).padStart(length, fill);
            }
            return value;
        }
    },
    methods: {
        axiosError(error) {
            const response = error.response;
            const status = response.status;
            if (status === 422) {
                this.errors = response.data
            }
            if (status === 500) {
                this.$message({
                    type: 'info',
                    message: response.data.message
                  });
            }
        },
        getResponseValidations(success = true, message = null)
        {
            return {
                success: success,
                message: message
            }
        },
        generalSleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms))
        }
    }
})
const mainWrapper = document.getElementById('main-wrapper');
if (mainWrapper) {
    new Vue({
        store: store,
        el: '#main-wrapper'
    });
}

const sidebarMultiUserRoots = document.querySelectorAll('.sidebar-multi-user-selector-container');
if (sidebarMultiUserRoots && sidebarMultiUserRoots.length) {
    sidebarMultiUserRoots.forEach((el) => {
        new Vue({
            store: store,
            el: el
        });
    });
}
// Mantener viva la sesión mientras la pestaña esté abierta
setInterval(() => {
    if (Vue.prototype.$http) {
        Vue.prototype.$http.get('/keep-alive').catch(() => {});
    }
}, 5 * 60 * 1000);