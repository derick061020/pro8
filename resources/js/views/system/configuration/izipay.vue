<template>
    <div class="card">
      <div class="card-header bg-info bg-info-customer-admin">
        <h3 class="my-0">Izipay</h3>
      </div>
      <div class="card-body">
        <form autocomplete="off" @submit.prevent="submit">
          <div class="form-body">
            <div class="row">
              <div class="col-md-12">
                <div>
                    <div :class="{'has-danger': errors.enabled_izipay}"
                                    class="form-group">
                      <label>
                        Habilitar
                        </label>
                        <div>
                                <el-switch v-model="form.enabled_izipay"
                                            active-text="Si"
                                            inactive-text="No"></el-switch>
                                <small v-if="errors.enabled_izipay"
                                        class="form-control-feedback"
                                        v-text="errors.enabled_izipay[0]"></small>

                        </div>
                    </div>

                </div>
                <div class="form-group" :class="{'has-danger': errors.username_izipay}">
                  <label class="control-label">
                    Usuario
                  </label>
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
                  <el-input v-model="form.password_izipay"></el-input>
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
      soap_sends: [],
      soap_types: []
    };
  },
  async created() {
    await this.initForm();

    await this.$http.get(`/${this.resource}/record`).then(response => {
      this.form.enabled_izipay = response.data.enabled_izipay;
      this.form.username_izipay = response.data.username_izipay;
      this.form.password_izipay = response.data.password_izipay;
      this.form.publickey_izipay = response.data.publickey_izipay;
      this.form.sha256key_izipay = response.data.sha256key_izipay;
    });
  },
  methods: {
    initForm() {
      this.errors = {};
      this.form = {
        username_izipay: null,
        enabled_izipay: false,
        password_izipay: null,
        publickey_izipay: null,
        sha256key_izipay: null
      };
    },
    submit() {
      this.loading_submit = true;
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

