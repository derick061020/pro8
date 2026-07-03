
 <template>
  <div>
    <form autocomplete="off" @submit.prevent="submit">
      <div class="form-body">
        <div class="row">
          <!-- Métodos de pago alternativos -->
          <div class="col-md-6">
            <div class="form-group form-modern mb-3">
              <el-switch v-model="form.enable_yape" :active-value="1" :inactive-value="0"></el-switch>
              <label class="ms-2 mb-0">Habilitar pago con Yape</label>
              <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">
                Muestra la opción &ldquo;Pagar con YAPE&rdquo; en el checkout.
              </small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group form-modern mb-3">
              <el-switch v-model="form.enable_transfer" :active-value="1" :inactive-value="0"></el-switch>
              <label class="ms-2 mb-0">Habilitar transferencia bancaria</label>
              <small class="d-block text-muted ms-5" style="padding: 0 !important; line-height: 1.5;">
                Muestra la opción &ldquo;Transferencia Bancaria&rdquo; en el checkout.
              </small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
              <label class="control-label">
                Token Público
                <el-tooltip placement="right-start">
                  <div slot="content">
                    Token Público.
                    <a href="#" @click="openCulqi">Culqi</a>
                  </div>
                  <i class="fa fa-info-circle"></i>
                </el-tooltip>
              </label>
              <el-input v-model="form.token_public_culqui"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.token_public_culqui"
                v-text="errors.token_public_culqui[0]"
              ></small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group" :class="{'has-danger': errors.token_private_culqui}">
              <label class="control-label">Token Privado  <el-tooltip placement="right-start">
                  <div slot="content">
                    Token Privado.
                    <a href="#" @click="openCulqi">Culqi</a>
                  </div>
                  <i class="fa fa-info-circle"></i>
                </el-tooltip></label>
              <el-input v-model="form.token_private_culqui"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.token_private_culqui"
                v-text="errors.token_private_culqui[0]"
              ></small>
            </div>
          </div>
          <!-- Script Paypal copiado de configuration_paypal -->
          <div class="col-md-12">
            <div class="form-group form-modern" :class="{'has-danger': errors.script_paypal}">
              <label class="control-label">
                Script Paypal
                <el-tooltip placement="right-start">
                  <div slot="content">
                    Codigo Html Formulario Paypal.
                    <a href="#" @click="openPaypal">Paypal</a>
                  </div>
                  <i class="fa fa-info-circle"></i>
                </el-tooltip>
              </label>
              <br />
              <el-input type="textarea" :rows="4" v-model="form.script_paypal"></el-input>
              <small
                class="form-control-feedback"
                v-if="errors.script_paypal"
                v-text="errors.script_paypal[0]"
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
      // headers: headers_token,
      resource: "ecommerce",
      errors: {},
      form: {},
      soap_sends: [],
      soap_types: []
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      if (response.data !== "") {
        let data = response.data.data;
        this.form.id = data.id;
        this.form.token_public_culqui = data.token_public_culqui;
        this.form.token_private_culqui = data.token_private_culqui;
        this.form.script_paypal = data.script_paypal;
        this.form.enable_yape = data.enable_yape ? 1 : 0;
        this.form.enable_transfer = data.enable_transfer ? 1 : 0;
      }
    });
  },
  methods: {
    openCulqi() {
      window.open("https://www.culqi.com");
    },
    openPaypal() {
      window.open(
        "https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/buy-now-step-1/#open-the-paypal-button-creation-page"
      );
    },
    initForm() {
      this.errors = {};
      this.form = {
        id: null,
        token_public_culqui: "",
        token_private_culqui: "",
        script_paypal: "",
        enable_yape: 0,
        enable_transfer: 0,
      };
    },
    submit() {
      this.loading_submit = true;
      this.$http
        .post(`/${this.resource}/configuration_culqui`, this.form)
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
    submit_paypal() {}
  }
};
</script>

