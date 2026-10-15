 <template>
    <div class="card">
      <div class="card-header bg-info bg-info-customer-admin">
        <h3 class="my-0">Consulta RUC/DNIe</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_false}">
                    <el-checkbox v-model="form.token_false">Usar consulta interna</el-checkbox><br>
                    <small class="form-control-feedback" v-if="errors.token_false" v-text="errors.token_false[0]"></small>
                </div>
              </div>
              <div class="col-md-12" v-if="form.token_false == false">
                <div class="form-group" :class="{'has-danger': errors.url_apiruc}">
                  <label class="control-label">
                    URL
                  </label>
                  <el-input v-model="form.url_apiruc"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.url_apiruc"
                    v-text="errors.url_apiruc[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12" v-if="form.token_false == false">
                <div class="form-group" :class="{'has-danger': errors.token_apiruc}">
                  <label class="control-label">Token</label>
                  <el-input v-model="form.token_apiruc"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.token_apiruc"
                    v-text="errors.token_apiruc[0]"
                  ></small>
                </div>
              </div>
            </div>

            <hr>
            <h4 class="mb-1">API SUNAT propia (apisu)</h4>
            <p class="text-muted small mb-3">
              Es la API principal de consulta RUC/DNI y tipo de cambio. El token se crea en el panel de apisu
              (Tokens de API). Si lo dejas vacío se usa el del archivo .env del servidor.
            </p>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.apisu_url}">
                  <label class="control-label">URL</label>
                  <el-input v-model="form.apisu_url" placeholder="https://apisu.hotelbuendia.net"></el-input>
                  <small class="form-control-feedback" v-if="errors.apisu_url" v-text="errors.apisu_url[0]"></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.apisu_key}">
                  <label class="control-label">Token</label>
                  <el-input v-model="form.apisu_key" :type="showApisuKey ? 'text' : 'password'" placeholder="sk_...">
                    <el-button slot="append" @click="showApisuKey = !showApisuKey">{{ showApisuKey ? 'Ocultar' : 'Ver' }}</el-button>
                  </el-input>
                  <small class="form-control-feedback" v-if="errors.apisu_key" v-text="errors.apisu_key[0]"></small>
                </div>
              </div>
              <div class="col-md-12">
                <el-button size="small" :loading="testing_apisu" @click="testApisu">Probar conexión</el-button>
                <span v-if="apisuResult" class="ml-2 small" :class="apisuResult.success ? 'text-success' : 'text-danger'">
                  {{ apisuResult.message }}
                </span>
              </div>
            </div>
          </div>
          <div class="form-actions text-end pt-2">
            <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
          </div>
        </form>
      </div>
    </div>
</template>


<script>
export default {
  data() {
    return {
      loading_submit: false,
      // headers: headers_token,
      resource: "configurations",
      errors: {},
      form: {},
      showApisuKey: false,
      testing_apisu: false,
      apisuResult: null,
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/apiruc`).then(response => {
        this.form.url_apiruc = response.data.url_apiruc;
        this.form.token_apiruc = response.data.token_apiruc;
        this.form.apisu_url = response.data.apisu_url;
        this.form.apisu_key = response.data.apisu_key;

        if (this.form.token_apiruc == 'false') {
          this.form.token_false = true;
        }
    });
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        url_apiruc: null,
        token_apiruc: null,
        token_false: false,
        apisu_url: null,
        apisu_key: null
      };
    },
    testApisu() {
      this.testing_apisu = true;
      this.apisuResult = null;
      this.$http
        .post(`/${this.resource}/apisu/test`, { apisu_url: this.form.apisu_url, apisu_key: this.form.apisu_key })
        .then(response => {
          this.apisuResult = response.data;
        })
        .catch(() => {
          this.apisuResult = { success: false, message: 'No se pudo hacer la prueba.' };
        })
        .then(() => {
          this.testing_apisu = false;
        });
    },
    submit() {
      this.loading_submit = true;
      if (this.form.token_false == true) {
        this.form.token_apiruc = 'false';
      }
      this.$http
        .post(`/${this.resource}`, this.form)
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
    },
  }
};
</script>

