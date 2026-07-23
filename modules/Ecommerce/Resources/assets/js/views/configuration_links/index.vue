
 <template>
  <div>
    <form autocomplete="off" @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <div class="col-12">
            <h4 class="control-label"><strong>Configuración de Barra de Publicidad</strong></h4>
          </div>
          <div class="col-md-3">
            <div class="form-group" :class="{'has-danger': errors.publicidad_activa}">
              <label class="control-label">Activar Publicidad</label><br>
              <el-switch v-model="form.publicidad_activa" active-text="Si" inactive-text="No"></el-switch>
              <small
                class="form-control-feedback"
                v-if="errors.publicidad_activa"
                v-text="errors.publicidad_activa[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-6" v-show="form.publicidad_activa">
            <div class="form-group" :class="{'has-danger': errors.publicidad_texto}">
              <label class="control-label">Texto de Publicidad</label>
              <el-input v-model="form.publicidad_texto"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.publicidad_texto"
                v-text="errors.publicidad_texto[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-3" v-show="form.publicidad_activa">
            <div class="form-group" :class="{'has-danger': errors.publicidad_color_fondo}">
              <label class="control-label">Color de Fondo</label><br>
              <div class="d-flex align-items-center">
                <input 
                  type="color" 
                  v-model="form.publicidad_color_fondo" 
                  class="form-control form-control-color" 
                  style="width: 80px; height: 40px; padding: 2px; cursor: pointer; border: 1px solid #dcdfe6;"
                >
                <span class="ms-2 text-muted" style="font-family: monospace;">{{ form.publicidad_color_fondo }}</span>
              </div>
              <small
                class="form-control-feedback"
                v-if="errors.publicidad_color_fondo"
                v-text="errors.publicidad_color_fondo[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-12 mb-3" v-show="form.publicidad_activa">
            <div class="form-group" :class="{'has-danger': errors.publicidad_link}">
              <label class="control-label">Link de Publicidad (URL)</label>
              <el-input v-model="form.publicidad_link" placeholder="https://ejemplo.com"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.publicidad_link"
                v-text="errors.publicidad_link[0]"
              ></small>
            </div>
          </div>

          <div class="col-12 mt-2">
            <hr>
            <h4><strong>Links personalizados para el menú</strong></h4>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.title_one_customised_link}">
              <label class="control-label">Titulo 1</label>
              <el-input v-model="form.title_one_customised_link"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.title_one_customised_link"
                v-text="errors.title_one_customised_link[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group" :class="{'has-danger': errors.customised_link_one}">
              <label class="control-label">Link 1</label>
              <el-input v-model="form.customised_link_one"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.customised_link_one"
                v-text="errors.customised_link_one[0]"
              ></small>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.title_two_customised_link}">
              <label class="control-label">Titulo 2</label>
              <el-input v-model="form.title_two_customised_link"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.title_two_customised_link"
                v-text="errors.title_two_customised_link[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group" :class="{'has-danger': errors.customised_link_two}">
              <label class="control-label">Link 2</label>
              <el-input v-model="form.customised_link_two"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.customised_link_two"
                v-text="errors.customised_link_two[0]"
              ></small>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.title_three_customised_link}">
              <label class="control-label">Titulo 3</label>
              <el-input v-model="form.title_three_customised_link"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.title_three_customised_link"
                v-text="errors.title_three_customised_link[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group" :class="{'has-danger': errors.customised_link_three}">
              <label class="control-label">Link 3</label>
              <el-input v-model="form.customised_link_three"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.customised_link_three"
                v-text="errors.customised_link_three[0]"
              ></small>
            </div>
          </div>

          <!-- Tags menú -->
          <div class="col-12 mt-3">
            <h4><strong>Tags menú</strong></h4>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.tag_shipping}">
              <label class="control-label">Tag Envío</label>
              <el-input v-model="form.tag_shipping"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.tag_shipping"
                v-text="errors.tag_shipping[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.tag_dollar}">
              <label class="control-label">Tag moneda</label>
              <el-input v-model="form.tag_dollar"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.tag_dollar"
                v-text="errors.tag_dollar[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group" :class="{'has-danger': errors.tag_support}">
              <label class="control-label">Tag soporte</label>
              <el-input v-model="form.tag_support"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.tag_support"
                v-text="errors.tag_support[0]"
              ></small>
            </div>
          </div>

          <!-- Redes sociales -->
          <div class="col-12 mt-3">
            <h4><strong>Redes Sociales</strong></h4>
          </div>
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.link_facebook}">
              <label class="control-label">Link Facebook</label>
              <el-input v-model="form.link_facebook"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.link_facebook"
                v-text="errors.link_facebook[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.link_youtube}">
              <label class="control-label">Link YouTube</label>
              <el-input v-model="form.link_youtube"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.link_youtube"
                v-text="errors.link_youtube[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.link_tiktok}">
              <label class="control-label">Link TikTok</label>
              <el-input v-model="form.link_tiktok"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.link_tiktok"
                v-text="errors.link_tiktok[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" :class="{'has-danger': errors.link_instagram}">
              <label class="control-label">Link Instagram</label>
              <el-input v-model="form.link_instagram"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.link_instagram"
                v-text="errors.link_instagram[0]"
              ></small>
            </div>
          </div>
        </div>
      </div>
      <div class="form-actions text-end float-end pt-2">
        <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
      </div>
    </form>
  </div>
</template>




<script>
export default {
  data() {
    return {
      loading_submit: false,
      resource: "ecommerce", // Recurso del que se va a hacer la solicitud
      errors: {}, // Para almacenar errores de validación
      form: {
        title_one_customised_link: "", // Título 1
        customised_link_one: "", // Link 1
        title_two_customised_link: "", // Título 2
        customised_link_two: "", // Link 2
        title_three_customised_link: "", // Título 3
        customised_link_three: "", // Link 3
        tag_shipping: "",
        tag_dollar: "",
        tag_support: "",
        link_facebook: "",
        link_youtube: "",
        link_tiktok: "",
        link_instagram: "",
      },
    };
  },
  async created() {
    await this.initForm();
    await this.$http.get(`/${this.resource}/record`).then(response => {
      if (response.data !== "") {
        let data = response.data.data;
        this.form.id = data.id;
        this.form.title_one_customised_link = data.title_one_customised_link;
        this.form.title_two_customised_link = data.title_two_customised_link;
        this.form.title_three_customised_link = data.title_three_customised_link;
        this.form.customised_link_one = data.customised_link_one;
        this.form.customised_link_two = data.customised_link_two;
        this.form.customised_link_three = data.customised_link_three;
        this.form.link_facebook = data.link_facebook;
        this.form.link_youtube = data.link_youtube;
        this.form.link_tiktok = data.link_tiktok;
        this.form.link_instagram = data.link_instagram;
        this.form.tag_shipping = data.tag_shipping;
        this.form.tag_dollar = data.tag_dollar;
        this.form.tag_support = data.tag_support;
        this.form.publicidad_activa = data.publicidad_activa;
        this.form.publicidad_texto = data.publicidad_texto;
        this.form.publicidad_color_fondo = data.publicidad_color_fondo;
        this.form.publicidad_link = data.publicidad_link;
      }
    });
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        title_one_customised_link: "",
        title_two_customised_link: "",
        title_three_customised_link: "",
        customised_link_one: "",
        title_two_customised_link: "",
        customised_link_two: "",
        title_three_customised_link: "",
        customised_link_three: "",
        tag_shipping: "",
        tag_dollar: "",
        tag_support: "",
        link_facebook: "",
        link_youtube: "",
        link_tiktok: "",
        link_instagram: "",
        publicidad_activa: false,
        publicidad_texto: "",
        publicidad_color_fondo: "#000000",
        publicidad_link: "",
      };
    },
    submit() {
      this.loading_submit = true;
      // Guardar ambos tipos de datos en un solo endpoint o dividir según backend
      this.$http
        .post(`/${this.resource}/configuration_links`, this.form)
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

<style scoped>
/* Estilos para mejorar la disposición */
.card-config {
  margin-top: 20px;
}

.form-body {
  padding: 15px;
}

.el-input {
  width: 100%;
}

/* Ajustes adicionales de espacio entre los campos */
.form-actions {
  margin-top: 20px;
}
</style>