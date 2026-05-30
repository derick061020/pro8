<template>
    <div class="users">
        <div class="page-header pe-0">
            <h2><a href="/users">
                <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            </a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Usuarios</span></li>
            </ol>
            <div class="right-wrapper pull-right">

                <template v-if="showAccessTokenForDiscount">
                    <el-tooltip class="item" content="Genera un token aleatorio para permitir realizar ventas con un porcentaje de descuento superior al límite configurado - Para vendedores" effect="dark" placement="top-start">
                        <button type="button" class="btn btn-info btn-sm  mt-2 me-2" @click.prevent="clickAccessTokenForDiscount()"><i class="fa fa-check"></i> Generar token</button>
                    </el-tooltip>
                </template>

                <button type="button" class="btn btn-custom btn-sm  mt-2 me-2" v-if="typeUser == 'admin'" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>

                <!--<button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickImport()"><i class="fa fa-upload"></i> Importar</button>-->
            </div>
        </div>
        <div class="card tab-content-default row-new">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de usuarios</h3>
            </div> -->
            <div class="card-body">
                <div class="col-md-12">
                <div class="scroll-shadow shadow-left" v-show="showLeftShadow"></div>
                <div class="scroll-shadow shadow-right" v-show="showRightShadow"></div>
                <div class="table-responsive" ref="scrollContainer">
                    <table class="table">
                        <thead>
                        <tr>
                            <!-- <th>#</th> -->
                            <th>Email</th>
                            <th>Nombre</th>
                            <th>Perfil</th>
                            <th>Api Token</th>
                            <th>Sucursal</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in records" :key="index" :class="!row.active ? 'text-danger' : ''">
                            <!-- <td>{{ index + 1 }}</td> -->
                            <td>
                                {{ row.email }}
                                <sup v-if="row.is_multi_user" style="padding: 0px 3px;border-radius: 4px;" class="bg-info text-white">Multi Usuario</sup>
                            </td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.type }}</td>
                            <td>
                                <span>
                                    {{ row.showToken ? row.api_token : maskToken(row.api_token) }}
                                </span>
                            
                                <button
                                    class="btn-view-token"
                                    @click.prevent="toggleToken(row)"
                                >
                                    <svg v-if="row.showToken" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>
                                </button>
                            
                                <button
                                    class="btn-copy-token"
                                    @click.prevent="clickCopy(row)"
                                    :class="{ copied: row.copied }"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667l0 -8.666" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg>
                                </button>
                            </td>
                            <td>{{ row.establishment_description }}</td>
                            <td class="text-end">
                                <button
                                    v-if="typeUser === 'admin' && row.active"
                                    type="button"
                                    class="btn waves-effect waves-light btn-xs btn-info me-1"
                                    @click.prevent="clickCreate(row.id)">
                                    Editar
                                </button>
                                <template v-if="row.id != 1 && typeUser === 'admin'">
                                    <button
                                        v-show="!row.is_multi_user"
                                        type="button"
                                        class="btn waves-effect waves-light btn-xs btn-danger me-1"
                                        @click.prevent="clickDelete(row.id)">
                                        Eliminar
                                    </button>
                                </template>
                                <template v-if="!isMainUser(row.id) && isAdminUser">
                                    <button
                                        v-show="!row.is_multi_user"
                                        type="button"
                                        class="btn waves-effect waves-light btn-xs"
                                        :class="row.active ? 'btn-warning' : 'btn-success'"
                                        @click.prevent="clickActive(row.active, row.id)">
                                        {{ row.active ? 'Inhabilitar' : 'Habilitar'}}
                                    </button>
                                </template>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>                
            </div>
            <users-form :showDialog.sync="showDialog"
                        :typeUser="typeUser"
                        :recordId="recordId"></users-form>

            <authorized-token-discount-form :showDialog.sync="showDialogAuthorizedTokenForDiscount" ></authorized-token-discount-form>
        </div>
    </div>
</template>

<script>

    import UsersForm from './form1.vue'
    import AuthorizedTokenDiscountForm from './partials/authorized_token_discount.vue'
    import {deletable} from '../../../mixins/deletable'

    export default {
        props: ['typeUser', 'configuration'],
        mixins: [deletable],
        components: {UsersForm, AuthorizedTokenDiscountForm},
        data() {
            return {
                showDialog: false,
                showDialogAuthorizedTokenForDiscount: false,
                resource: 'users',
                recordId: null,
                records: [],
                showLeftShadow: false,
                showRightShadow: false,
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        computed:
        {
            showAccessTokenForDiscount()
            {
                return this.typeUser === 'admin' && this.configuration.restrict_seller_discount
            },
            isAdminUser()
            {
                return this.typeUser === 'admin'
            }
        },
        mounted() {
            this.$nextTick(() => {
                const el = this.$refs.scrollContainer;
                if (el) {
                    el.addEventListener('scroll', this.checkScrollShadows);
                    this.checkScrollShadows();
                }
            });
        },
        methods: {
            checkScrollShadows() {
                const el = this.$refs.scrollContainer;
                if (!el) return;
                
                const scrollLeft = el.scrollLeft;
                const scrollRight = el.scrollWidth - el.clientWidth - scrollLeft;
                
                this.showLeftShadow = scrollLeft > 1;
                this.showRightShadow = scrollRight > 1;
            },
            isMainUser(user_id)
            {
                return user_id === 1
            },
            clickAccessTokenForDiscount()
            {
                this.showDialogAuthorizedTokenForDiscount = true
            },
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data.map(r => ({
                            ...r,
                            showToken: false,
                            copied: false
                        }))
                    })
            },
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            },
            clickActive(active, id)
            {
                this.changeActive(`/${this.resource}/change-active`, { active, id })
                    .then(() => this.$eventHub.$emit('reloadData'))
            },            
            maskToken(token) {
                if (!token) return ''
                if (token.length <= 6) return token

                const start = token.substring(0, 4)
                const end = token.substring(token.length - 3)

                return `${start}••••••••${end}`
            },
            toggleToken(row) {
                row.showToken = !row.showToken
            },
            clickCopy(row) {
                if (!row || !row.api_token) return

                const token = row.api_token

                const markCopied = () => {
                    row.copied = true
                    setTimeout(() => { row.copied = false }, 3000)
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(token)
                        .then(() => {
                            this.$message.success('Token copiado correctamente')
                            markCopied()
                        })
                        .catch(() => {
                            this.$message.error('No se pudo copiar el token')
                        })
                } else {
                    // Fallback para http
                    const textArea = document.createElement("textarea")
                    textArea.value = token
                    textArea.style.position = "fixed"
                    textArea.style.opacity = "0"
                    document.body.appendChild(textArea)
                    textArea.focus()
                    textArea.select()

                    try {
                        const successful = document.execCommand('copy')
                        if (successful) {
                            this.$message.success('Token copiado correctamente')
                            markCopied()
                        } else {
                            this.$message.error('No se pudo copiar el token')
                        }
                    } catch (err) {
                        this.$message.error('No se pudo copiar el token')
                    }

                    document.body.removeChild(textArea)
                }
            },
            
        }
    }
</script>
