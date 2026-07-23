<template>
  <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
    <form autocomplete="off" @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <div class="col-12 col-sm-6">
            <div class="col-12">
              <div class="form-group banner-img" :class="{'has-danger': errors.image}">
                <label class="control-label">
                  Imágen
                  <span class="text-danger"></span>
                  <div class="sub-title text-danger">
                    <small>Se requiere resoluciones 1024x720</small>
                  </div>
                </label>
                <el-upload
                  class="avatar-uploader"
                  :data="{'type': 'promotions'}"
                  :headers="headers"
                  :action="`/${resource}/upload`"
                  :show-file-list="false"
                  :on-success="onSuccess"
                >
                  <img v-if="form.image_url" :src="form.image_url" class="avatar" />
                  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                </el-upload>
                <small class="form-control-feedback" v-if="errors.image" v-text="errors.image[0]"></small>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="col-12">
              <div class="form-group" :class="{'has-danger': errors.name}">
                <label class="control-label">Nombre</label>
                <el-input v-model="form.name"></el-input>
                <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
              </div>
            </div>
          </div>
          <div class="col-12">
              <div class="form-group redirect-group">
                <label class="control-label redirect-title">Destino del banner</label>
                <p class="redirect-help">
                  Elige a dónde lleva el banner. Solo puedes asignar un destino.
                </p>
                <div class="redirect-segment">
                  <button
                    type="button"
                    class="redirect-segment__btn"
                    :class="{ active: redirectType === 'item' }"
                    @click="toggleRedirect('item')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304" /><path d="M9 11v-5a3 3 0 0 1 6 0v5" /></svg>
                    <span>Producto</span>
                  </button>
                  <button
                    type="button"
                    class="redirect-segment__btn"
                    :class="{ active: redirectType === 'category' }"
                    @click="toggleRedirect('category')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 4h6v6h-6l0 -6" /><path d="M14 4h6v6h-6l0 -6" /><path d="M4 14h6v6h-6l0 -6" /><path d="M14 17a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg>
                    <span>Categoría</span>
                  </button>
                  <button
                    type="button"
                    class="redirect-segment__btn"
                    :class="{ active: redirectType === 'custom' }"
                    @click="toggleRedirect('custom')"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-link"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 15l6 -6" /><path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" /><path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" /></svg>
                    <span>Enlace</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="col-12" v-if="redirectType === 'item'">
              <div class="form-group" :class="{'has-danger': errors.item_id}">
                <label class="control-label">Producto</label>
                <el-select
                  v-model="form.item_id"
                  dusk="item_id"
                  placeholder="Seleccione producto"
                  clearable
                  filterable
                  class="w-100"
                >
                  <el-option
                    v-for="option in items"
                    :key="option.id"
                    :value="option.id"
                    :label="option.description"
                  ></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.item_id"
                  v-text="errors.item_id[0]"
                ></small>
              </div>
            </div>

            <div class="col-12" v-if="redirectType === 'category'">
              <div class="form-group" :class="{'has-danger': errors.category_id}">
                <label class="control-label">Categoría</label>
                <el-select
                  v-model="form.category_id"
                  placeholder="Seleccione categoría"
                  clearable
                  filterable
                  class="w-100"
                >
                  <el-option
                    v-for="option in categories"
                    :key="option.id"
                    :value="option.id"
                    :label="option.name"
                  ></el-option>
                </el-select>
                <small class="form-control-feedback" v-if="errors.category_id" v-text="errors.category_id[0]"></small>
              </div>
            </div>

            <div class="col-12" v-if="redirectType === 'custom'">
              <div class="form-group" :class="{'has-danger': errors.custom_link}">
                <label class="control-label">Pega la URL de destino</label>
                <el-input v-model="form.custom_link" placeholder="https://..." />
                <small class="form-control-feedback" v-if="errors.custom_link" v-text="errors.custom_link[0]"></small>
              </div>
            </div>

            <div class="col-12">
              <div class="redirect-hint" v-if="redirectType === 'none'">
                <i class="el-icon-warning-outline"></i>
                <span>No has seleccionado un destino. Si continúas, el registro se guardará sin un enlace asociado.</span>
              </div>
            </div>
          <!-- <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.description}">
              <label class="control-label">Descripcion</label>
              <el-input v-model="form.description"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.description"
                v-text="errors.description[0]"
              ></small>
            </div>
          </div> -->
        </div>
      </div>
      <div class="form-actions text-end mt-4">
        <el-button class="second-buton me-2" @click.prevent="close()">Cancelar</el-button>
        <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
      </div>
    </form>
  </el-dialog>
</template>

<script>
// import {serviceNumber} from '../../../mixins/functions'

export default {
  // mixins: [serviceNumber],
  props: ["showDialog", "recordId", "external"],
  data() {
    return {
      items: [],
      categories: [],
      headers: headers_token,
      loading_submit: false,
      titleDialog: null,
      resource: "promotions",
      errors: {},
      redirectType: "none",
      form: {},
      countries: [],
      all_departments: [],
      all_provinces: [],
      all_districts: [],
      provinces: [],
      districts: [],
      identity_document_types: []
    };
  },
  created() {
    this.initForm();
    this.$http.get(`/${this.resource}/tables`).then(response => {
      this.items = response.data.items;
      this.categories = response.data.categories || [];
    });
  },
  computed: {},
  watch: {
    redirectType(value) {
      // Solo se permite un tipo de redirección a la vez: al cambiar,
      // se limpian los demás campos para no guardar combinaciones.
      this.form.item_id = value === 'item' ? this.form.item_id : null;
      this.form.category_id = value === 'category' ? this.form.category_id : null;
      this.form.has_custom_link = value === 'custom';
      if (value !== 'custom') {
        this.form.custom_link = null;
      }
    }
  },
  methods: {
    initForm() {
      this.errors = {};
      this.redirectType = "none";
      this.form = {
        name: null,
        description: '',
        image: null,
        image_url: null,
        temp_path: null,
        type: "banners",
        item_id: null,
        category_id: null,
        has_custom_link: false,
        custom_link: null
      };
    },
    create() {
      this.titleDialog = this.recordId ? "Editar Banner" : "Nuevo Banner";
      if (this.recordId) {
        this.$http
          .get(`/${this.resource}/record/${this.recordId}`)
          .then(response => {
            this.form = response.data.data;
            this.syncRedirectType();
            // Asegurar que description nunca sea null
            if (this.form.description === null) {
              this.form.description = '';
            }
          });
      }
    },

    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}`, this.form)
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);

            this.$eventHub.$emit("reloadData");

            this.close();
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data;
          } else {
            console.log(error);
            this.$message.error(error.response.data.message)
          }
        })
        .then(() => {
          this.loading_submit = false;
        });
    },
    close() {
      this.$emit("update:showDialog", false);
      this.initForm();
    },
    toggleRedirect(type) {
      // Al volver a pulsar el destino activo, se deselecciona (sin destino).
      this.redirectType = this.redirectType === type ? 'none' : type;
    },
    syncRedirectType() {
      // Determina el tipo de redirección a partir del registro cargado,
      // dando prioridad: producto > categoría > link personalizado.
      if (this.form.item_id) {
        this.redirectType = 'item';
      } else if (this.form.category_id) {
        this.redirectType = 'category';
      } else if (this.form.custom_link) {
        this.redirectType = 'custom';
      } else {
        this.redirectType = 'none';
      }
    },
    onSuccess(response, file, fileList) {
      if (response.success) {
        this.form.image = response.data.filename;
        this.form.image_url = response.data.temp_image;
        this.form.temp_path = response.data.temp_path;
      } else {
        this.$message.error(response.message);
      }
    }
  }
};
</script>

<style scoped>
.redirect-group {
  margin-top: 4px;
}
.redirect-title {
  font-weight: 600;
  margin-bottom: 2px;
}
.redirect-help {
  color: #909399;
  font-size: 13px;
  margin: 0 0 12px;
}
.redirect-segment {
  display: flex;
  gap: 4px;
  padding: 4px;
  background: #f2f3f5;
  border-radius: 10px;
}
.redirect-segment__btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 10px;
  border: none;
  background: transparent;
  border-radius: 8px;
  color: #606266;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
}
.redirect-segment__btn:hover {
  color: #303133;
}
.redirect-segment__btn.active {
  background: #ffffff;
  color: #409eff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.redirect-segment__btn i {
  font-size: 16px;
}
.redirect-hint {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  padding: 10px 14px;
  background: #f2f3f5;
  border-radius: 8px;
  color: #909399;
  font-size: 13px;
}
.redirect-hint i {
  font-size: 16px;
}
</style>