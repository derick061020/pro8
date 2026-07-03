<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <!-- <div class="col-md-12 text-right">
                    <h5>Cant. Pedida: {{quantity}}</h5>
                    <h5 v-bind:class="{ 'text-danger': (toAttend < 0) }">Por Atender: {{toAttend}}</h5>
                </div> -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.name}">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.name" :maxlength="11"></el-input>
                            <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.pricing}">
                            <label class="control-label">Precio</label>
                            <el-input v-model="form.pricing"></el-input>
                            <small class="form-control-feedback" v-if="errors.pricing" v-text="errors.pricing[0]"></small>
                            <el-checkbox
                                v-model="form.is_popular"
                                :disabled="popularLockedByOtherPlan"
                                class="mt-2"
                            >
                                Destacar como "Popular" en la vista del cliente
                            </el-checkbox>
                            <small v-if="popularLockedByOtherPlan" class="form-text text-muted d-block">
                                El plan <strong>{{ popular_plan.name }}</strong> ya está marcado como Popular. Desmárcalo primero para destacar este.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.limit_users || errorLUser.limit_users}">
                            <label class="control-label">Límite de usuarios</label>
                            <el-input v-model="limit_users" @input="validateLUsers"  :disabled="users_unlimited"></el-input>
                            <el-checkbox v-model="users_unlimited" @change="setUnlimitUsers">Ilimitado</el-checkbox><br>
                            <small class="form-control-feedback d-block" v-if="errors.limit_users" v-text="errors.limit_users[0]"></small>
                            <small class="form-control-feedback" v-if="errorLUser.limit_users" v-text="errorLUser.limit_users[0]"></small> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.limit_documents || errorLDocument.limit_documents}">
                            <label class="control-label">Límite de documentos</label>
                            <el-input v-model="limit_documents" @input="validateLDocuments" :disabled="documents_unlimited"></el-input>
                            <el-checkbox v-model="documents_unlimited" @change="setUnlimitDocuments">Ilimitado</el-checkbox><br>

                            <el-checkbox v-model="form.include_sale_notes_limit_documents">Incluir notas de venta</el-checkbox><br>

                            <small class="form-control-feedback d-block" v-if="errors.limit_documents" v-text="errors.limit_documents[0]"></small>
                            <small class="form-control-feedback" v-if="errorLDocument.limit_documents" v-text="errorLDocument.limit_documents[0]"></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.establishments_limit}">
                            <label class="control-label">Límite de sucursales</label>

                            <template v-if="form.establishments_unlimited">
                                <el-input value="∞" disabled></el-input>
                            </template>
                            <template v-else>
                                <el-input v-model="form.establishments_limit"></el-input>
                            </template>

                            <el-checkbox v-model="form.establishments_unlimited">Ilimitado</el-checkbox><br>

                            <small class="form-control-feedback d-block" v-if="errors.establishments_limit" v-text="errors.establishments_limit[0]"></small>
                        </div>
                    </div>

                    
                    <div class="col-md-6">
                        <div class="form-group" :class="{'has-danger': errors.sales_limit}">
                            <label class="control-label">
                                Límite de ventas mensual
                                <el-tooltip class="item"
                                            :content="form.include_sale_notes_sales_limit ? 'Disponible para CPE y Nota de venta' : 'Disponible para CPE'"
                                            effect="dark"
                                            placement="top">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                            </label>

                            <template v-if="form.sales_unlimited">
                                <el-input value="∞" disabled></el-input>
                            </template>
                            <template v-else>
                                <el-input v-model="form.sales_limit"></el-input>
                            </template>

                            <el-checkbox v-model="form.sales_unlimited">Ilimitado</el-checkbox><br>
                            <el-checkbox v-model="form.include_sale_notes_sales_limit">Incluir notas de venta</el-checkbox><br>


                            <small class="form-control-feedback d-block" v-if="errors.sales_limit" v-text="errors.sales_limit[0]"></small>
                        </div>
                    </div>

                </div>
                <el-collapse v-model="collapse" class="mt-3">
                    <el-collapse-item name="1" title="Módulos predeterminados sugeridos (Opcional)">
                        <div class="row">
                            <span class="ms-4">Giro de negocio <small>(opcional)</small></span>
                            <div class="col-12">
                                <el-radio-group v-model="business" @change="changeModules">
                                    <el-radio :label="1">Básico</el-radio>
                                    <el-radio :label="2">Farmacia</el-radio>
                                    <el-radio :label="3">Hotel</el-radio>
                                    <el-radio :label="4">Restaurante</el-radio>
                                </el-radio-group>
                            </div>
                            <div class="col-md-6">
                                <span>
                                    Habilitar módulos
                                </span>
                                <div class="form-group tree-container-admin">
                                    <el-tree
                                        ref="tree"
                                        :check-strictly="true"
                                        :data="modules"
                                        :props="defaultProps"
                                        accordion
                                        highlight-current
                                        node-key="id"
                                        show-checkbox
                                        @check="FixChildren">
                                    </el-tree>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span>
                                    Habilitar apps
                                </span>
                                <div class="form-group tree-container-admin">
                                    <el-tree
                                        ref="Apptree"
                                        :check-strictly="true"
                                        :data="apps"
                                        :props="defaultAppsProps"
                                        accordion
                                        highlight-current
                                        node-key="id"
                                        show-checkbox
                                        @check="FixAppChildren">
                                    </el-tree>
                                </div>
                            </div>
                        </div>
                    </el-collapse-item>
                </el-collapse>
                <!-- <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group" :class="{'has-danger': (errors.plan_documents)}">
                            <label class="control-label font-weight-bold mb-0">Habilitar documentos electrónicos</label> 

                            <el-checkbox-group v-model="form.plan_documents"  >
                                <el-checkbox v-for="(city,ind) in plan_documents" class="plan_documents" :label="city.id"  :key="ind">{{city.description}}</el-checkbox>
                            </el-checkbox-group>

                            <small class="form-control-feedback" v-if="errors.plan_documents" v-text="errors.plan_documents[0]"></small> 
                        </div>
                    </div>
                   
                </div> -->
            </div>
            <div class="form-actions text-right pt-2">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<style>
.plan_documents{ display:block ; margin: 15px 0 ;}
</style>

<script>

    export default {
        props: ['showDialog', 'recordId','plan_documents'],
        data() {
            return {
                loading_submit: false,
                titleDialog: null,
                resource: 'plans',
                documents_unlimited:null,
                users_unlimited:null,
                limit_users:null,
                limit_documents:null,
                errors: {},
                errorLDocument:{},
                errorLUser:{},
                form: {},
                collapse: 1,
                business: null,
                modules: [],
                apps: [],
                group_basic: [],
                group_hotel: [],
                group_pharmacy: [],
                group_restaurant: [],
                group_hotel_apps: [],
                group_pharmacy_apps: [],
                group_restaurant_apps: [],
                popular_plan: null,
                defaultProps: {
                    children: 'childrens',
                    label: 'description'
                },
                defaultAppsProps: {
                    children: 'childrens',
                    label: 'description'
                },
            }
        },
        computed: {
            popularLockedByOtherPlan() {
                return !!(this.popular_plan && this.popular_plan.id !== this.form.id);
            }
        },
        created() 
        {
            this.initForm()
            this.$http.get(`/${this.resource}/tables`).then(response => {
                this.modules = response.data.modules
                this.apps = response.data.apps
                this.group_basic = response.data.group_basic
                this.group_hotel = response.data.group_hotel
                this.group_pharmacy = response.data.group_pharmacy
                this.group_restaurant = response.data.group_restaurant
                this.group_hotel_apps = response.data.group_hotel_apps
                this.group_pharmacy_apps = response.data.group_pharmacy_apps
                this.group_restaurant_apps = response.data.group_restaurant_apps
                this.popular_plan = response.data.popular_plan
            })
        },
        methods: {
            notEmpty(value)
            {
                return !_.isEmpty(value)
            },
            initForm() {
                this.limit_users = null
                this.limit_documents = null
                this.documents_unlimited = false
                this.users_unlimited = false
                this.errors = {}
                this.errorLDocument = {}
                this.errorLUser = {}
                
                this.form = {
                    id: null,
                    name: null,
                    pricing: null,
                    is_popular: false,
                    limit_users: null,
                    limit_documents: null,
                    plan_documents:[],

                    establishments_limit : 0,
                    establishments_unlimited : true,

                    sales_limit : 0,
                    sales_unlimited : true,
                    include_sale_notes_sales_limit : false,
                    include_sale_notes_limit_documents: false,
                    module_permissions: null
                }
                this.business = null;
                setTimeout(() => {
                    if(this.$refs.tree) this.$refs.tree.setCheckedKeys([]);
                    if(this.$refs.Apptree) this.$refs.Apptree.setCheckedKeys([]);
                }, 100);
            },
            create() {

                this.titleDialog = (this.recordId)? 'Editar plan':'Nuevo plan'
                this.$http.get(`/${this.resource}/popular`).then(response => {
                    this.popular_plan = response.data.popular_plan
                })
                if (this.recordId) {
                    this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                            this.setData(response.data.data)
                        })
                }
            },
            validateInputs()
            {
                if(!this.form.establishments_unlimited)
                {
                    if(isNaN(this.form.establishments_limit)) return this.getResponseValidations(false, 'Límite de sucursales no es un número válido.')
                } 

                if(!this.form.sales_unlimited)
                {
                    if(isNaN(this.form.sales_limit)) return this.getResponseValidations(false, 'Límite de ventas no es un número válido.')
                } 

                return this.getResponseValidations()
            },
            submit() {   

                if(this.validateLUsers().limit_users || this.validateLDocuments().limit_documents)
                    return
                    
                const validate_inputs = this.validateInputs()
                if(!validate_inputs.success) return this.$message.error(validate_inputs.message)
                
                const modulesAndLevelsSelecteds = this.$refs.tree.getCheckedNodes();
                const appsAndLevelsSelecteds = this.$refs.Apptree.getCheckedNodes();
                const selModules = [];
                modulesAndLevelsSelecteds.map(m => {
                    if (m.is_parent) {
                        selModules.push(m.id);
                    }
                });
                const selApps = [];
                appsAndLevelsSelecteds.map(m => {
                    if (m.is_parent) {
                        selApps.push(m.id);
                    }
                });
                const selLevels = [];
                modulesAndLevelsSelecteds.filter(l => {
                    if (!l.is_parent) {
                        const idArray = l.id.split('-');
                        selLevels.push(idArray[1]);
                    }
                })
                appsAndLevelsSelecteds.filter(l => {
                    if (!l.is_parent) {
                        const idArray = l.id.split('-');
                        selLevels.push(idArray[1]);
                    }
                })

                this.form.module_permissions = {
                    business: this.business,
                    modules: selModules,
                    apps: selApps,
                    levels: selLevels
                };

                this.transform()

                this.loading_submit = true  
                this.$http.post(`${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            this.$eventHub.$emit('reloadData')
                            this.close()
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data 
                        } else {
                            console.log(error.response)
                        }
                    })
                    .then(() => {
                        this.loading_submit = false
                    })
                    
            },
            setData(data){

                this.form = data
                this.form.plan_documents = Object.values(data.plan_documents)
                this.users_unlimited = (data.limit_users == 0) ? true : false
                this.documents_unlimited = (data.limit_documents == 0) ? true : false                
                this.limit_users = (this.users_unlimited) ? "∞": data.limit_users
                this.limit_documents = (this.documents_unlimited) ? "∞":  data.limit_documents

                const preSelecteds = [];
                const preAppSelecteds = [];

                if (this.form.module_permissions) {
                    this.business = this.form.module_permissions.business;
                    const preSelectedsModules = this.form.module_permissions.modules || [];
                    const preSelectedsApps = this.form.module_permissions.apps || [];
                    const preSelectedsLevels = this.form.module_permissions.levels || [];
                    
                    this.modules.map(m => {
                        if (preSelectedsModules.includes(m.id)) {
                            preSelecteds.push(m.id);
                        }
                        m.childrens.map(c => {
                            const idArray = c.id.split('-');
                            if (preSelectedsLevels.includes(parseInt(idArray[1])) || preSelectedsLevels.includes(idArray[1])) {
                                preSelecteds.push(c.id);
                            }
                        })
                    });

                    this.apps.map(m => {
                        if (preSelectedsApps.includes(m.id)) {
                            preAppSelecteds.push(m.id);
                        }
                        m.childrens.map(c => {
                            const idArray = c.id.split('-');
                            if (preSelectedsLevels.includes(parseInt(idArray[1])) || preSelectedsLevels.includes(idArray[1])) {
                                preAppSelecteds.push(c.id);
                            }
                        })
                    });
                }
                
                setTimeout(() => {
                    if(this.$refs.tree) this.$refs.tree.setCheckedKeys(preSelecteds);
                    if(this.$refs.Apptree) this.$refs.Apptree.setCheckedKeys(preAppSelecteds);
                }, 500);
            },
            transform(){

                if(this.users_unlimited){
                    this.form.limit_users = 0
                }else{
                    this.form.limit_users = this.limit_users
                }

                if(this.documents_unlimited){
                    this.form.limit_documents = 0
                }else{
                    this.form.limit_documents = this.limit_documents
                }
                
            },
            validateLDocuments(){

                this.errorLDocument = {} 

                if(!this.documents_unlimited){
                    if(this.limit_documents < 1)
                        this.$set(this.errorLDocument, 'limit_documents', ['limite de documentos debe ser mayor a cero']);
                } 

                return this.errorLDocument 
            },            
            
            validateLUsers(){

                this.errorLUser = {}  
                 
                if(!this.users_unlimited){
                    if(this.limit_users < 1)
                        this.$set(this.errorLUser, 'limit_users', ['limite de usuarios debe ser mayor a cero']);
                }

                return this.errorLUser 
            },            
            setUnlimitDocuments(){
                this.limit_documents = (this.documents_unlimited) ? "∞" : null
                this.form.limit_documents = (this.limit_documents == "∞") ? 0 : this.limit_documents
            },
            setUnlimitUsers(){
                this.limit_users = (this.users_unlimited) ? "∞" : null
                this.form.limit_users = (this.limit_users == "∞") ? 0 : this.limit_users

            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            FixChildren(currentObj, treeStatus) {
                let element = this.$refs.tree
                if (currentObj !== undefined) {
                    let selected = treeStatus.checkedKeys.indexOf(currentObj.id)
                    if (selected !== -1) {
                        this.SelectParent(currentObj, element)
                        this.FixSameValueToChild(currentObj, true, element)
                    } else {
                        if (currentObj.childrens !== undefined && currentObj.childrens.length !== 0) {
                            this.FixSameValueToChild(currentObj, false, element)
                        }
                    }
                }
            },
            FixAppChildren(currentObj, treeStatus) {
                let element = this.$refs.Apptree
                if (currentObj !== undefined) {
                    let selected = treeStatus.checkedKeys.indexOf(currentObj.id)
                    if (selected !== -1) {
                        this.SelectParent(currentObj, element)
                        this.FixSameValueToChild(currentObj, true, element)
                    } else {
                        if (currentObj.childrens !== undefined && currentObj.childrens.length !== 0) {
                            this.FixSameValueToChild(currentObj, false, element)
                        }
                    }
                }
            },
            FixSameValueToChild(treeList, isSelected, element) {
                if (treeList !== undefined && element !== undefined) {
                    element.setChecked(treeList.id, isSelected)
                    if (treeList.childrens !== undefined) {
                        for (let i = 0; i < treeList.childrens.length; i++) {
                            this.FixSameValueToChild(treeList.childrens[i], isSelected, element)
                        }
                    }
                }
            },
            SelectParent(currentObj, element) {
                if (currentObj !== undefined) {
                    let currentNode = element.getNode(currentObj)
                    if (currentNode.parent.key !== undefined) {
                        element.setChecked(currentNode.parent, true)
                        this.SelectParent(currentNode.parent, element)
                    }
                }
            },
            changeModules() {
                var group = {
                    modules: [],
                    apps: [],
                };
                if(this.business == 1){
                    group.modules = this.getIds(this.group_basic);
                }
                if(this.business == 2){
                    group.modules = this.getIds(this.group_pharmacy);
                    group.apps = this.getIds(this.group_pharmacy_apps);
                }
                if(this.business == 3){
                    group.modules = this.getIds(this.group_hotel);
                    group.apps = this.getIds(this.group_hotel_apps);
                }
                if(this.business == 4){
                    group.modules = this.getIds(this.group_restaurant);
                    group.apps = this.getIds(this.group_restaurant_apps);
                }
                this.$refs.tree.setCheckedKeys(group.modules);
                this.$refs.Apptree.setCheckedKeys(group.apps);
            },
            getIds(modules) {
                const preSelecteds = [];
                modules.map(m => {
                    preSelecteds.push(m.id);
                    m.childrens.map(c => {
                        preSelecteds.push(c.id);
                    });
                });
                return preSelecteds
            }
        }
    }
</script>