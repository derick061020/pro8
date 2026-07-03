<template>
  <div class="col-12 pt-2 pt-md-0">
    <el-tabs type="border-card" tab-position="left" class="el-tab-ecommerce-config">
      <el-tab-pane label="Información">
        <div>
          <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
              <div class="row">
                <div class="col-12 mb-3">
                  <h4 class="mb-0"><strong>Información de Contacto</strong></h4>
                </div>
                <div class="col-md-6">
                  <div class="form-group" :class="{'has-danger': errors.information_contact_email}">
                    <label class="control-label">Email</label>
                    <el-input v-model="form.information_contact_email"></el-input>
                    <small
                      class="form-control-feedback"
                      v-if="errors.information_contact_email"
                      v-text="errors.information_contact_email[0]"
                    ></small>
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="form-group" :class="{'has-danger': errors.information_contact_name}">
                    <label class="control-label">
                      Nombre
                      <span class="text-danger">*</span>
                    </label>
                    <el-input v-model="form.information_contact_name"></el-input>
                    <small
                      class="form-control-feedback"
                      v-if="errors.information_contact_name"
                      v-text="errors.information_contact_name[0]"
                    ></small>
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="form-group" :class="{'has-danger': errors.information_contact_phone}">
                    <label class="control-label">
                      Teléfono
                      <span class="text-danger">*</span>
                    </label>
                    <el-input v-model="form.information_contact_phone"></el-input>
                    <small
                      class="form-control-feedback"
                      v-if="errors.information_contact_phone"
                      v-text="errors.information_contact_phone[0]"
                    ></small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" :class="{'has-danger': errors.information_contact_address}">
                    <label class="control-label">
                      Horario de atención
                      <span class="text-danger">*</span>
                    </label>
                    <el-input v-model="form.information_contact_address"></el-input>
                    <small
                      class="form-control-feedback"
                      v-if="errors.information_contact_address"
                      v-text="errors.information_contact_address[0]"
                    ></small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" :class="{'has-danger': errors.phone_whatsapp}">
                    <label class="control-label">
                      Whatsapp
                    </label>
                    <el-input v-model="form.phone_whatsapp"></el-input>
                    <small
                      class="form-control-feedback"
                      v-if="errors.phone_whatsapp"
                      v-text="errors.phone_whatsapp[0]"
                    ></small>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group editor-resizable">
                      <label class="control-label">Términos y Condiciones</label>
                      <vue-ckeditor
                        :editors="editors"
                        type="classic"
                        :config="editorConfig"
                        v-model="form.terms_conditions"
                      />
                      <div class="resize-handle" @mousedown="startResize"></div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group editor-resizable">
                      <label class="control-label">Política de Privacidad</label>
                      <vue-ckeditor
                        :editors="editors"
                        type="classic"
                        :config="editorConfig"
                        v-model="form.privacy_policy"
                      />
                      <div class="resize-handle" @mousedown="startResize"></div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group editor-resizable">
                      <label class="control-label">Sobre Nosotros</label>
                      <vue-ckeditor
                        :editors="editors"
                        type="classic"
                        :config="editorConfig"
                        v-model="form.about_us"
                      />
                      <div class="resize-handle" @mousedown="startResize"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-actions text-end float-end pt-2">
              <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
          </form>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Apariencia">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-12 mb-3">
                <h4 class="mb-0"><strong>Apariencia de la Tienda</strong></h4>
              </div>
              <div class="col-md-6">
                  <div class="form-group form-modern mb-3">
                    <el-switch v-model="form.full_width_banner" :active-value="1" :inactive-value="0"></el-switch>
                    <label class="ms-2 mb-0">Activar ancho completo del banner</label>
                    <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">Las imágenes del carrusel ocuparán el 100% del ancho de la pantalla.
                      Aseguresé que sus imágenes tenga la proporción 5:2
                    </small>
                  </div>
                <div class="form-group form-modern mb-3">
                  <el-switch v-model="form.show_description" :active-value="1" :inactive-value="0"></el-switch>
                  <label class="ms-2 mb-0">Mostrar descripción del producto</label>
                  <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">Muestra el nombre adicional o descripción corta debajo del título del producto</small>
                </div>
                <div class="form-group form-modern mb-3">
                  <el-switch v-model="form.show_stock" :active-value="1" :inactive-value="0"></el-switch>
                  <label class="ms-2 mb-0">Mostrar stock disponible</label>
                  <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">Muestra la cantidad disponible en inventario de cada producto</small>
                </div>
                <div class="form-group form-modern mb-3">
                  <el-switch v-model="form.only_available_products" :active-value="1" :inactive-value="0"></el-switch>
                  <label class="ms-2 mb-0">Ocultar productos sin stock</label>
                  <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">Los productos agotados no aparecerán en el catálogo de la tienda</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-modern">
                  <label class="control-label">Color Principal de la Tienda</label>
                  <div class="d-flex align-items-center mt-1">
                    <input 
                      type="color" 
                      v-model="form.color_ecommerce" 
                      class="form-control form-control-color" 
                      style="width: 80px; height: 40px; padding: 2px; cursor: pointer; border: 1px solid #dcdfe6;"
                    >
                    <span class="ms-2 text-muted" style="font-family: monospace;">{{ form.color_ecommerce }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Logo</label>
                  <div class="d-flex align-items-start gap-3">
                    <div>
                      <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                        Para subir o cambiar el logo, ve a
                        <strong>Configuración y más &rsaquo; Configuraciones globales &rsaquo; Empresa</strong>.
                        Desde allí podrás cargar el logo en modo claro y modo oscuro.
                      </p>
                      <a href="/companies/create" class="el-button--primary btn btn-sm w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                        Ir a configuración de empresa
                      </a>
                    </div>
                  </div>
                </div>
              </div>


            </div>
          </div>
          <div class="form-actions text-end float-end pt-2">
            <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
          </div>
        </form>
      </el-tab-pane>
      <el-tab-pane label="Enlaces">
        <ConfigurationLinks />
      </el-tab-pane>
      <el-tab-pane label="Pasarelas de pago">
        <div>
          <div class="mb-3">
            <h4 class="mb-0"><strong>Pasarelas de Pago</strong></h4>
          </div>
          <PaymentGateways />
        </div>
      </el-tab-pane>
      <el-tab-pane label="Cupones de descuento">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0"><strong>Cupones de Descuento</strong></h4>
            <button class="btn btn-custom btn-sm" type="button" @click.prevent="$refs.digitalCoupon.clickNew()">
              <i class="fa fa-plus-circle"></i> Nuevo Cupón
            </button>
          </div>
          <DigitalCoupon ref="digitalCoupon" />
        </div>
      </el-tab-pane>
      <el-tab-pane label="Zonas de delivery">
        <div>
          <div class="mb-3">
            <h4 class="mb-2 d-flex align-items-center justify-content-between">
              <strong>Zonas de Delivery</strong>
              <el-button type="primary" plain class="btn btn-sm" @click.prevent="$refs.deliveryZones.clickNew()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Nuevo
              </el-button>
            </h4>
          </div>
          <DeliveryZones ref="deliveryZones" />
          <div class="mb-3">
            <h4 class="mb-2"><strong>Sucursales de Recojo</strong></h4>
          </div>
          <div class="form-group form-modern mb-3">
            <el-switch v-model="form.enable_store_pickup" :active-value="1" :inactive-value="0" @change="submit"></el-switch>
            <label class="ms-2 mb-0">Habilitar recojo en tienda</label>
            <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">
              Permite a los clientes elegir recoger su pedido en una sucursal en lugar de recibirlo por delivery.
            </small>
          </div>
          <!-- Sucursales de recojo: solo visible cuando el recojo en tienda está activo -->
          <div v-if="form.enable_store_pickup == 1" class="mt-4">
            <PickupBranches />
          </div>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Otras configuraciones">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-12 mb-3">
                <h4 class="mb-0"><strong>Otras Configuraciones</strong></h4>
              </div>
              <div class="col-md-6">
                <div class="form-group form-modern mb-3">
                  <el-switch v-model="form.enable_electronic_documents" :active-value="1" :inactive-value="0"></el-switch>
                  <label class="ms-2 mb-0">Habilitar documentos electrónicos</label>
                  <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">
                    Cuando está desactivado, se genera automáticamente una nota de venta.
                  </small>
                </div>
              </div>

            </div>
          </div>
          <div class="form-actions text-end float-end pt-2">
            <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
          </div>
        </form>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<style>
.editor-resizable {
    position: relative;
}
.resize-handle {
    height: 10px;
    cursor: ns-resize;
    background: #eee;
}
.editor-resizable .ck-editor__editable {
    min-height: 150px;
    height: 200px;
}
</style>
<script>
import ConfigurationLinks from '../configuration_links/index.vue';
import PaymentGateways from '../payment_gateways/index.vue';
import DeliveryZones from '../configuration_delivery_zones/index.vue';
import 'ckeditor5/ckeditor5.css';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import DigitalCoupon from '../configuration_digital_coupon/index.vue';
import PickupBranches from '../configuration_pickup_branches/index.vue';
import CKEditor from 'vue-ckeditor5';
export default {
  components: {
    ConfigurationLinks,
    PaymentGateways,
    DigitalCoupon,
    DeliveryZones,
    PickupBranches,
    'vue-ckeditor': CKEditor.component
  },
  data() {
    return {
      loading_submit: false,
      resource: "ecommerce",
      errors: {},
      form: {},
      soap_sends: [],
      soap_types: [],
      editors: {
          classic: ClassicEditor
      },
      editorConfig: {
          licenseKey: 'GPL',
          toolbar: [
              'heading',
              '|',
              'bold', 'italic', 'link',
              'bulletedList', 'numberedList',
              '|',
              'blockQuote',
              'undo', 'redo',
              '|',
              'sourceEditing'
          ]
      },
      isResizing: false,
    };
  },
  async created() {
    await this.$http.get(`/${this.resource}/record`).then(response => {
      if (response.data !== "") {
        let data = response.data.data;

        // Cargar preferencias si existen
        let preferences = { show_description: 1, show_stock: 0, only_available_products: 0, full_width_banner: 0 };
        if (data.preferences) {
          const prefs = typeof data.preferences === 'string'
            ? JSON.parse(data.preferences)
            : data.preferences;
          preferences = prefs;
        }

        // Inicializar form con todos los datos de una vez
        this.form = {
          // campos originales
          id: data.id,
          information_contact_email: data.information_contact_email || "",
          information_contact_name: data.information_contact_name || null,
          information_contact_phone: data.information_contact_phone || null,
          information_contact_address: data.information_contact_address || null,
          phone_whatsapp: data.phone_whatsapp || null,
          // campos de color y preferencias
          color_ecommerce: data.color_ecommerce,
          show_description: parseInt(preferences.show_description) || 0,
          show_stock: parseInt(preferences.show_stock) || 0,
          only_available_products: parseInt(preferences.only_available_products) || 0,
          full_width_banner: parseInt(preferences.full_width_banner) || 0,
          // campos de páginas personalizadas
          terms_conditions: data.terms_conditions || '',
          privacy_policy: data.privacy_policy || '',
          about_us: data.about_us || '',
          // configuración de documentos electrónicos y recojo en tienda
          enable_electronic_documents: data.enable_electronic_documents ? 1 : 0,
          enable_store_pickup: data.enable_store_pickup ? 1 : 0,
        };
      } else {
        this.initForm();
      }
    });
  },
  methods: {
    startResize(e) {
      this.isResizing = true

      const editor = e.target.previousElementSibling.querySelector('.ck-editor__editable')

      const onMouseMove = (event) => {
        if (!this.isResizing) return
        editor.style.height = event.clientY - editor.getBoundingClientRect().top + 'px'
      }

      const onMouseUp = () => {
        this.isResizing = false
        document.removeEventListener('mousemove', onMouseMove)
        document.removeEventListener('mouseup', onMouseUp)
      }

      document.addEventListener('mousemove', onMouseMove)
      document.addEventListener('mouseup', onMouseUp)
    },
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        information_contact_email: "",
        information_contact_name: null,
        information_contact_phone: null,
        information_contact_address: null,
        phone_whatsapp: null,
        color_ecommerce: null,
        show_description: 1,
        show_stock: 0,
        only_available_products: 0,
        full_width_banner: 0,
        terms_conditions: '',
        privacy_policy: '',
        about_us: '',
        enable_electronic_documents: 0,
        enable_store_pickup: 0,
      };
    },
    submit() {
      this.loading_submit = true;
      // Copiar el form y empaquetar los switches en 'preferences'
      const payload = { ...this.form };
      payload.preferences = {
        show_description: this.form.show_description,
        show_stock: this.form.show_stock,
        only_available_products: this.form.only_available_products,
        full_width_banner: this.form.full_width_banner
      };
      // Eliminar los switches planos para evitar duplicidad
      delete payload.show_description;
      delete payload.show_stock;
      delete payload.only_available_products;
      delete payload.full_width_banner;

      this.$http
        .post(`/${this.resource}/configuration`, payload)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            console.log(error);
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    }
  }
};
</script>
