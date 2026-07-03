// Entrypoint standalone para el widget público del Libro de Reclamaciones.
// Contiene solo las dependencias mínimas: Vue, ElementUI y el formulario.
// NO incluye tenant-components, store ni el resto del sistema.

import Vue from 'vue'
import axios from 'axios'
import moment from 'moment'
import ElementUI from 'element-ui'
import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'
import '/resources/sass/element-ui.scss'
import 'element-ui/lib/theme-chalk/index.css'

// Textos en español para ElementUI
lang.el.select.noData     = 'No se encontraron resultados'
lang.el.table.emptyText   = 'No se encontraron resultados'
lang.el.cascader.noData   = 'No se encontraron resultados'
lang.el.empty.description = 'No se encontraron resultados'
locale.use(lang)

Vue.use(ElementUI, { size: 'small' })

// Configurar axios con CSRF y moment
window.moment = moment
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
}
Vue.prototype.$http    = axios
Vue.prototype.$eventHub = new Vue()

// Único componente necesario: el formulario de reclamaciones
import ClaimsBookForm from './views/claim_form.vue'
Vue.component('tenant-claims-book-form', ClaimsBookForm)

// Montar sobre el punto de anclaje definido en widget.blade.php
const el = document.getElementById('main-wrapper')
if (el) {
    new Vue({
        el,
        // Exponer el color resuelto (URL param o localStorage) para que el template lo use como prop
        data: {
            widgetColor: window.widgetColor || '#18181b',
        },
    })
}
