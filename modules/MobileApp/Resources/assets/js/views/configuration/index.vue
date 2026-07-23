<template>
    <div v-loading="loading">
        <div class="page-header pe-0">
            <h2><a href="/app/configuration">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" /><path d="M11 4h2" /><path d="M12 17v.01" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> App Móvil</span></li>
                <li class="active"><span> Configuración</span></li>
            </ol>
            <div class="right-wrapper pull-right"></div>
        </div>
        <div class="row">
            <div class="short-div col-md-8">
                <tenant-mobile-app-permissions></tenant-mobile-app-permissions>
            </div>
            <div class="short-div col-md-4">
                <app-logo-config></app-logo-config>
                <app-color-config></app-color-config>
            </div>
        </div>
    </div>
</template>

<script>
    import AppColorConfig from './partials/AppColorConfig.vue'
    import AppLogoConfig from './partials/AppLogoConfig.vue'

    export default {
        components: { AppColorConfig, AppLogoConfig },
        data() {
            return {
                form: {},
                loading_submit: false,
                resource: 'app/configurations',
                loading: false,
            }
        },
        async created(){
            await this.initForm()
            await this.getRecord()
        },
        methods: {
            async getRecord(){
                this.loading = true
                await this.$http.get(`/${this.resource}`)
                        .then(response => {
                            this.form = response.data.data
                        })
                        .then(()=>{
                            this.loading = false
                        })
            },
            initForm(){
                this.form = {
                    theme_color: 'blue',
                    card_color: 'multicolored',
                    header_waves: false,
                    app_mode: 'default',
                    direct_send_documents_whatsapp: false,
                }
            },
            async submit() {
                this.loading_submit = true
                await this.$http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
            }
        }
    }
</script>
