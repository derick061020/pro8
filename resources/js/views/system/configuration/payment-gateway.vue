<template>
  <div class="card">
    <div class="card-header bg-info bg-info-customer-admin">
      <h3 class="my-0">Pasarela de pago</h3>
    </div>
    <div class="card-body">
      <form autocomplete="off" @submit.prevent="submit">
        <div class="form-body">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group" :class="{'has-danger': errors.gateway}">
                <label class="control-label">
                  Método de pago
                  <el-tooltip placement="right-start">
                    <div slot="content">
                      Solo se puede tener una pasarela activa a la vez.
                      La seleccionada será la única disponible en el registro.
                    </div>
                    <i class="fa fa-info-circle"></i>
                  </el-tooltip>
                </label>
                <el-select v-model="form.gateway" placeholder="Selecciona un método de pago" style="width: 100%">
                  <el-option label="Ninguno" value=""></el-option>
                  <el-option label="Culqi" value="culqi"></el-option>
                  <el-option label="Izipay" value="izipay"></el-option>
                  <el-option label="Mercado Pago" value="mercadopago"></el-option>
                </el-select>
                <small
                  class="form-control-feedback"
                  v-if="errors.gateway"
                  v-text="errors.gateway[0]"
                ></small>
              </div>
            </div>

            <!-- Culqi -->
            <template v-if="form.gateway === 'culqi'">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.token_public_culqui}">
                  <label class="control-label">
                    Token Público
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Token Público.
                        <a href="#" @click.prevent="openCulqi">Culqi</a>
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
                  <label class="control-label">
                    Token Privado
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Token Privado.
                        <a href="#" @click.prevent="openCulqi">Culqi</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                  </label>
                  <el-input v-model="form.token_private_culqui" show-password></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.token_private_culqui"
                    v-text="errors.token_private_culqui[0]"
                  ></small>
                </div>
              </div>
            </template>

            <!-- Izipay -->
            <template v-else-if="form.gateway === 'izipay'">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.username_izipay}">
                  <label class="control-label">Usuario</label>
                  <el-input v-model="form.username_izipay"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.username_izipay"
                    v-text="errors.username_izipay[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.password_izipay}">
                  <label class="control-label">Contraseña</label>
                  <el-input v-model="form.password_izipay" show-password></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.password_izipay"
                    v-text="errors.password_izipay[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.publickey_izipay}">
                  <label class="control-label">Public Key</label>
                  <el-input v-model="form.publickey_izipay"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.publickey_izipay"
                    v-text="errors.publickey_izipay[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.sha256key_izipay}">
                  <label class="control-label">SHA256 Key</label>
                  <el-input v-model="form.sha256key_izipay"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.sha256key_izipay"
                    v-text="errors.sha256key_izipay[0]"
                  ></small>
                </div>
              </div>
            </template>

            <!-- Mercado Pago -->
            <template v-else-if="form.gateway === 'mercadopago'">
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.public_key_mp}">
                  <label class="control-label">
                    Public Key
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Public Key de tus credenciales.
                        <a href="#" @click.prevent="openMercadoPago">Mercado Pago</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                  </label>
                  <el-input v-model="form.public_key_mp"></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.public_key_mp"
                    v-text="errors.public_key_mp[0]"
                  ></small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group" :class="{'has-danger': errors.access_token_mp}">
                  <label class="control-label">
                    Access Token
                    <el-tooltip placement="right-start">
                      <div slot="content">
                        Access Token de tus credenciales.
                        <a href="#" @click.prevent="openMercadoPago">Mercado Pago</a>
                      </div>
                      <i class="fa fa-info-circle"></i>
                    </el-tooltip>
                  </label>
                  <el-input v-model="form.access_token_mp" show-password></el-input>
                  <small
                    class="form-control-feedback"
                    v-if="errors.access_token_mp"
                    v-text="errors.access_token_mp[0]"
                  ></small>
                </div>
              </div>
            </template>
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
      resource: "configurations",
      errors: {},
      form: {
        gateway: ""
      }
    };
  },
  async created() {
    this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      const data = response.data;

      this.form.token_public_culqui = data.token_public_culqui;
      this.form.token_private_culqui = data.token_private_culqui;

      this.form.username_izipay = data.username_izipay;
      this.form.password_izipay = data.password_izipay;
      this.form.publickey_izipay = data.publickey_izipay;
      this.form.sha256key_izipay = data.sha256key_izipay;

      this.form.public_key_mp = data.public_key_mp;
      this.form.access_token_mp = data.access_token_mp;

      // Derivar la pasarela activa desde los flags enabled_*
      if (data.enabled_izipay) {
        this.form.gateway = "izipay";
      } else if (data.enabled_culqi) {
        this.form.gateway = "culqi";
      } else if (data.enabled_mp) {
        this.form.gateway = "mercadopago";
      } else {
        this.form.gateway = "";
      }
    });
  },
  methods: {
    openMercadoPago() {
      window.open("https://www.mercadopago.com.pe/developers/panel/app");
    },
    openCulqi() {
      window.open("https://www.culqi.com");
    },
    initForm() {
      this.errors = {};
      this.form = {
        gateway: "",
        token_public_culqui: null,
        token_private_culqui: null,
        username_izipay: null,
        password_izipay: null,
        publickey_izipay: null,
        sha256key_izipay: null,
        public_key_mp: null,
        access_token_mp: null
      };
    },
    submit() {
      this.loading_submit = true;

      // Solo una pasarela puede estar habilitada a la vez.
      const payload = {
        ...this.form,
        enabled_culqi: this.form.gateway === "culqi",
        enabled_izipay: this.form.gateway === "izipay",
        enabled_mp: this.form.gateway === "mercadopago"
      };

      this.$http
        .post(`/${this.resource}`, payload)
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
