<template>
    <el-dialog :close-on-click-modal="false"
               :title="titleDialog"
               :visible="showDialog"
               :append-to-body="true"
               @open="create"
               @opened="opened"
               :close-on-press-escape="false" 
               @close="handleCloseDialog">
        <form autocomplete="off"
              @submit.prevent="submit">
            <div class="form-body">
                <el-tabs v-model="activeName">
                    <el-tab-pane class
                                 name="first">
                        <span slot="label">{{ titleTabDialog }}</span>

                        <div class="row mb-3 section-header section-header-first">
                            <div class="col-12">
                                <h5 class="section-title">Identificación</h5>
                                <p class="section-subtitle">Datos de documento y nombre del cliente/proveedor</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div :class="{'has-danger': errors.identity_document_type_id}"
                                     class="form-group">
                                    <label class="control-label">Tipo Doc. Identidad <span class="text-danger">*</span></label>
                                    <el-select v-model="form.identity_document_type_id"
                                               dusk="identity_document_type_id"
                                               filterable
                                               popper-class="el-select-identity_document_type"
                                               @change="changeIdentityDocType">
                                        <el-option v-for="option in identity_document_types"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.identity_document_type_id"
                                           class="form-control-feedback"
                                           v-text="errors.identity_document_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div :class="{'has-danger': errors.country_id}"
                                     class="form-group">
                                    <label class="control-label">Nacionalidad</label>
                                    <el-select v-model="form.nationality_id"
                                               dusk="country_id"
                                               filterable>
                                        <el-option v-for="option in countries"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.country_id"
                                           class="form-control-feedback"
                                           v-text="errors.country_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.number}"
                                     class="form-group">
                                    <label class="control-label">Número <span class="text-danger">*</span></label>

                                    <div v-if="api_service_token != false">
                                        <x-input-service v-model="form.number"
                                                         :identity_document_type_id="form.identity_document_type_id"
                                                         @search="searchNumber"></x-input-service>
                                    </div>
                                    <div v-else class="sunat-row">
                                        <el-input class="sunat-input" v-model="form.number"
                                                  :maxlength="maxLength"
                                                  dusk="number"></el-input>

                                        <el-button v-if="form.identity_document_type_id === '6' || form.identity_document_type_id === '1'"
                                                   class="sunat-service-button"
                                                   :loading="loading_search"
                                                   icon="el-icon-search"
                                                   type="primary"
                                                   @click.prevent="searchCustomer">
                                            {{ form.identity_document_type_id === '6' ? 'SUNAT' : 'RENIEC' }}
                                        </el-button>
                                    </div>

                                    <small v-if="errors.number"
                                           class="form-control-feedback"
                                           v-text="errors.number[0]"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Establecimientos SUNAT (solo con token ApiPeru y RUC consultado) -->
                        <div v-if="canQueryEstablishments" class="row mt-2">
                            <div class="col-12 text-end">
                                <el-button icon="el-icon-office-building"
                                           size="small"
                                           class="btn-query-establishments"
                                           :loading="loading_establishments"
                                           @click.prevent="consultEstablishments">
                                    Consultar establecimientos
                                </el-button>
                            </div>
                        </div>

                        <div v-if="establishments.length > 0" class="row mt-2">
                            <div class="col-12">
                                <div class="establishments-panel">
                                    <div class="establishments-head">
                                        <div class="establishments-title">
                                            Establecimientos
                                            <el-tag size="mini" type="success">{{ establishments.length }} encontrados</el-tag>
                                        </div>
                                        <div class="establishments-presets">
                                            <el-button size="mini" @click.prevent="selectAllEstablishments">Todas</el-button>
                                            <el-button size="mini" @click.prevent="selectOnlyPrincipal">Solo principal</el-button>
                                        </div>
                                    </div>

                                    <div class="establishments-rows">
                                        <div v-for="est in establishments"
                                             :key="est.codigo"
                                             class="establishment-row"
                                             :class="{'is-dim': !est.has_address || est.registered}">
                                            <el-checkbox v-model="est.selected"
                                                         :disabled="!est.has_address || est.registered || est.codigo === principal_establishment_code"
                                                         @change="onToggleEstablishment(est)"></el-checkbox>
                                            <span class="est-badge"
                                                  :class="est.codigo === principal_establishment_code ? 'principal' : 'sucursal'">
                                                {{ est.codigo === principal_establishment_code ? 'Principal' : 'Sucursal' }}
                                            </span>
                                            <span class="est-addr" :class="{'warn': !est.has_address}">
                                                {{ est.direccion || 'Sin dirección registrada' }}
                                            </span>
                                            <el-tag v-if="est.registered" size="mini" type="info" class="est-registered">Registrado</el-tag>
                                            <!-- Switch para elegir la dirección principal -->
                                            <el-tooltip :content="est.codigo === principal_establishment_code ? 'Dirección principal' : 'Marcar como principal'"
                                                        placement="top">
                                                <el-switch class="est-switch"
                                                           :value="est.codigo === principal_establishment_code"
                                                           :disabled="!est.has_address || est.registered || est.codigo === principal_establishment_code"
                                                           @change="setAsPrincipal(est)"></el-switch>
                                            </el-tooltip>
                                            <span class="est-code">N° {{ est.codigo }}</span>
                                        </div>
                                    </div>

                                    <div class="establishments-note" v-html="establishmentsNote"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Datos generales</h5>
                                <p class="section-subtitle">Nombre y datos comerciales del cliente/proveedor</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.name}"
                                     class="form-group">
                                    <label class="control-label">Nombre <span class="text-danger">*</span></label>
                                    <el-input v-model="form.name"
                                              dusk="name"></el-input>
                                    <small v-if="errors.name"
                                           class="form-control-feedback"
                                           v-text="errors.name[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.trade_name}"
                                     class="form-group">
                                    <label class="control-label">Nombre comercial</label>
                                    <el-input v-model="form.trade_name"
                                              dusk="trade_name"></el-input>
                                    <small v-if="errors.trade_name"
                                           class="form-control-feedback"
                                           v-text="errors.trade_name[0]"></small>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>

                    <el-tab-pane class
                                 name="second">
                        <span slot="label">
                            Dirección
                            <span v-if="addressesBadgeCount > 0" class="tab-count-badge">{{ addressesBadgeCount }}</span>
                        </span>

                        <div class="row mb-3 section-header section-header-first">
                            <div class="col-12">
                                <h5 class="section-title">Dirección principal</h5>
                                <p class="section-subtitle">Datos de ubicación, país, ubigeo y dirección de contacto</p>
                            </div>
                        </div>

                        <div class="subsection-panel">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h6 class="section-subtitle-sm">Ubicación</h6>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Nacionalidad -->
                            <!-- País -->

                            <div class="col-md-3">
                                <div :class="{'has-danger': errors.country_id}"
                                     class="form-group">
                                    <label class="control-label">País</label>
                                    <el-select v-model="form.country_id"
                                               filterable
                                               @change="handleCountryChange(row, index)">
                                        <el-option v-for="option in countries"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.country_id"
                                           class="form-control-feedback"
                                           v-text="errors.country_id[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div :class="{'has-danger': errors.location_id}"
                                     class="form-group">
                                    <label class="control-label">
                                        Ubigeo
                                        <span v-if="form.country_id === 'PE'" class="text-danger">*</span>
                                    </label>
                                    <el-cascader v-model="form.location_id"
                                                 :clearable="true"
                                                 :options="locations"
                                                 filterable
                                                 :disabled="form.country_id !== 'PE'"
                                                 :filter-method="customFilterMethod"></el-cascader>
                                    <small v-if="errors.location_id"
                                           class="form-control-feedback"
                                           v-text="errors.location_id[0]"></small>
                                    <small v-if="form.country_id === 'PE'" class="text-muted">
                                        Campo obligatorio
                                    </small>
                                    <small v-if="form.country_id !== 'PE'" class="text-muted">
                                        Ubigeo solo disponible para Perú
                                    </small>
                                </div>
                            </div>
                            <!-- Departamento -->
<!--                            <div class="col-md-3">-->
<!--                                <div :class="{'has-danger': errors.department_id}"-->
<!--                                     class="form-group">-->
<!--                                    <label class="control-label">Departamento</label>-->
<!--                                    <el-select v-model="form.department_id"-->
<!--                                               dusk="department_id"-->
<!--                                               filterable-->
<!--                                               popper-class="el-select-departments"-->
<!--                                               @change="filterProvince">-->
<!--                                        <el-option v-for="option in all_departments"-->
<!--                                                   :key="option.id"-->
<!--                                                   :label="option.description"-->
<!--                                                   :value="option.id"></el-option>-->
<!--                                    </el-select>-->
<!--                                    <small v-if="errors.department_id"-->
<!--                                           class="form-control-feedback"-->
<!--                                           v-text="errors.department_id[0]"></small>-->
<!--                                </div>-->
<!--                            </div>-->
                            <!-- Provincia -->
<!--                            <div class="col-md-3">-->
<!--                                <div :class="{'has-danger': errors.province_id}"-->
<!--                                     class="form-group">-->
<!--                                    <label class="control-label">Provincia</label>-->
<!--                                    <el-select v-model="form.province_id"-->
<!--                                               dusk="province_id"-->
<!--                                               filterable-->
<!--                                               popper-class="el-select-provinces"-->
<!--                                               @change="filterDistrict">-->
<!--                                        <el-option v-for="option in provinces"-->
<!--                                                   :key="option.id"-->
<!--                                                   :label="option.description"-->
<!--                                                   :value="option.id"></el-option>-->
<!--                                    </el-select>-->
<!--                                    <small v-if="errors.province_id"-->
<!--                                           class="form-control-feedback"-->
<!--                                           v-text="errors.province_id[0]"></small>-->
<!--                                </div>-->
<!--                            </div>-->
                            <!-- Distrito -->
<!--                            <div class="col-md-3">-->
<!--                                <div :class="{'has-danger': errors.province_id}"-->
<!--                                     class="form-group">-->
<!--                                    <label class="control-label">Distrito</label>-->
<!--                                    <el-select v-model="form.district_id"-->
<!--                                               dusk="district_id"-->
<!--                                               filterable-->
<!--                                               popper-class="el-select-districts">-->
<!--                                        <el-option v-for="option in districts"-->
<!--                                                   :key="option.id"-->
<!--                                                   :label="option.description"-->
<!--                                                   :value="option.id"></el-option>-->
<!--                                    </el-select>-->
<!--                                    <small v-if="errors.district_id"-->
<!--                                           class="form-control-feedback"-->
<!--                                           v-text="errors.district_id[0]"></small>-->
<!--                                </div>-->
<!--                            </div>-->
                            <!-- Direccion -->
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.address}"
                                     class="form-group">
                                    <label class="control-label">Dirección</label>
                                    <el-input v-model="form.address"
                                              dusk="address"></el-input>
                                    <small v-if="errors.address"
                                           class="form-control-feedback"
                                           v-text="errors.address[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.establishment_code}"
                                     class="form-group">
                                    <label class="control-label">Código de sucursal</label>
                                    <el-input v-model="form.establishment_code"
                                              dusk="establishment_code"></el-input>
                                    <small v-if="errors.establishment_code"
                                           class="form-control-feedback"
                                           v-text="errors.establishment_code[0]"></small>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="subsection-panel">
                            <div class="row section-subgroup">
                                <div class="col-12 mb-2">
                                    <h6 class="section-subtitle-sm">Detalles de dirección</h6>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Telefono -->
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.telephone}"
                                     class="form-group">
                                    <label class="control-label">Teléfono</label>
                                    <el-input v-model="form.telephone"
                                              dusk="telephone"></el-input>
                                    <small v-if="errors.telephone"
                                           class="form-control-feedback"
                                           v-text="errors.telephone[0]"></small>
                                </div>
                            </div>
                            <!-- Correo electronico contacto -->
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.email}"
                                     class="form-group">
                                    <label class="control-label">Correo electrónico</label>
                                    <el-input v-model="form.email"
                                              dusk="email"></el-input>
                                    <small v-if="errors.email"
                                           class="form-control-feedback"
                                           v-text="errors.email[0]"></small>
                                </div>
                            </div>

                            <!-- Correos electronicos alterno -->
                            <div class="col-6">
                                <div
                                    class="form-group">
                                    <label class="control-label">Correos opcionales </label>
                                    <el-input

                                        v-model="temp_email"
                                        dusk="email"
                                        @change="checkEmail()"
                                    >

                                    </el-input>


                                    <el-button
                                        v-if="temp_email != null && temp_email.length > 1 && checkEmail() == true"
                                        icon="el-icon-plus"
                                        size="mini"
                                        @click.prevent="clickAddMail()">
                                        Agregar Correo
                                    </el-button>

                                    <label v-if="temp_optional_email !== undefined &&
                                     temp_optional_email.length > 0"
                                           class="control-label">
                                        <el-tag
                                            v-for="mail in temp_optional_email"
                                            :key="mail.email"
                                            :closable="true"
                                            :type="'success'"
                                            @close="removeEmail(mail)"
                                        >
                                            {{ mail.email }}
                                        </el-tag>

                                    </label>
                                    <small v-if="errors.temp_email && errors.temp_email.length > 0"
                                           class="form-control-feedback"
                                           v-text="errors.temp_email"></small>


                                    <small v-if="errors.email"
                                           class="form-control-feedback"
                                           v-text="errors.email[0]"></small>
                                </div>
                            </div>
                            <!-- Correos electronicos alterno -->
                        </div>
                        </div>
                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Direcciones adicionales</h5>
                                <p class="section-subtitle">Agregar una o varias direcciones secundarias del cliente/proveedor</p>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12 text-center">
                                <el-button icon="el-icon-plus"
                                           class="btn-add-address"
                                           size="mini"
                                           @click.prevent="clickAddAddress()">
                                    Agregar dirección
                                </el-button>
                            </div>
                        </div>
                        <div v-for="(row, index) in form.addresses"
                             class="row m-t-10">
                            <div class="col-md-12">
                                <label v-if="index === 0"
                                       class="control-label">
                                    Dirección principal
                                </label>
                                <label v-else
                                       class="control-label">
                                    Dirección secundaria # {{ index }}
                                    <el-button class="btn-default-danger"
                                               icon="el-icon-minus"
                                               size="mini"
                                               @click.prevent="clickRemoveAddress(index)">Eliminar dirección
                                    </el-button>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <div :class="{'has-danger': errors.country_id}"
                                     class="form-group">
                                    <label class="control-label">País</label>
                                    <el-select v-model="row.country_id"
                                               filterable
                                               @change="handleCountryChange(row, index)">
                                               filterable
                                               @change="handleCountryChange(row, index)">
                                        <el-option v-for="option in countries"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.country_id"
                                           class="form-control-feedback"
                                           v-text="errors.country_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div :class="{'has-danger': errors.location_id}"
                                     class="form-group">
                                    <label class="control-label">
                                        Ubigeo
                                        <span v-if="row.country_id === 'PE'" class="text-danger">*</span>
                                    </label>
                                    <el-cascader v-model="row.location_id"
                                                 :clearable="true"
                                                 :options="locations"
                                                 :disabled="row.country_id !== 'PE'"
                                                 filterable></el-cascader>
                                    <small v-if="errors.location_id"
                                           class="form-control-feedback"
                                           v-text="errors.location_id[0]"></small>
                                    <small v-if="row.country_id === 'PE'" class="text-muted">
                                        Campo obligatorio
                                    </small>
                                    <small v-else class="text-muted">
                                        Ubigeo solo disponible para Perú
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.address}"
                                     class="form-group">
                                    <label class="control-label">Dirección</label>
                                    <el-input v-model="row.address"></el-input>
                                    <small v-if="errors.address"
                                           class="form-control-feedback"
                                           v-text="errors.address[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.establishment_code}"
                                     class="form-group">
                                    <label class="control-label">Código de sucursal</label>
                                    <el-input v-model="row.establishment_code"></el-input>
                                    <small v-if="errors.establishment_code"
                                           class="form-control-feedback"
                                           v-text="errors.establishment_code[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.phone}"
                                     class="form-group">
                                    <label class="control-label">Teléfono</label>
                                    <el-input v-model="row.phone"></el-input>
                                    <small v-if="errors.phone"
                                           class="form-control-feedback"
                                           v-text="errors.phone[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.email}"
                                     class="form-group">
                                    <label class="control-label">Correo electrónico</label>
                                    <el-input v-model="row.email"></el-input>
                                    <small v-if="errors.email"
                                           class="form-control-feedback"
                                           v-text="errors.email[0]"></small>
                                </div>
                            </div>
                            <div v-if="config.enable_consigned" class="col-md-6 center-el-checkbox">
                                <div :class="{'has-danger': errors.has_consigned}"
                                    class="form-group">
                                    <el-checkbox v-model="row.has_consigned">¿Agregar consignado?</el-checkbox>
                                    <br>
                                    <small v-if="errors.has_consigned"
                                        class="invalid-feedback"
                                        v-text="errors.has_consigned[0]"></small>
                                </div>
                            </div>
                            <div v-if="row.has_consigned && config.enable_consigned" class="col-12 col-md-12">
                                <div :class="{'has-danger': errors.consigned_id}"
                                    class="form-group">
                                    <label class="control-label">Consignado
                                        <a
                                        href="#"
                                        @click.prevent="showDialogConsignedForm = true">[+ Nuevo]</a></label>
                                    <el-select class="w-100" 
                                            v-model="row.consigned_id"
                                            filterable
                                            placeholder="Seleccionar consignado">
                                        <el-option v-for="option in consigneds"
                                                :key="option.id"
                                                :label="option.name"
                                                :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.consigned_id"
                                        class="invalid-feedback"
                                        v-text="errors.consigned_id[0]"></small>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane class
                                 name="third">
                        <span slot="label">Contacto</span>
                        <div class="row mb-3 section-header section-header-first">
                            <div class="col-12">
                                <h5 class="section-title">Contacto</h5>
                                <p class="section-subtitle">Información de persona de contacto y canales de comunicación</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div :class="{'has-danger': errors.contact}"
                                             class="form-group">
                                            <label class="control-label">Nombre y Apellido</label>
                                            <el-input v-model="form.contact.full_name"></el-input>
                                            <small v-if="errors.contact"
                                                   class="form-control-feedback"
                                                   v-text="errors.contact[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div :class="{'has-danger': errors.website }"
                                             class="form-group">
                                            <label class="control-label">Sitio Web</label>
                                            <el-input v-model="form.website"></el-input>
                                            <small v-if="errors.website"
                                                   class="form-control-feedback"
                                                   v-text="errors.website[0]"></small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div :class="{'has-danger': errors.contact}"
                                             class="form-group">
                                            <label class="control-label">Teléfono</label>
                                            <el-input v-model="form.contact.phone"></el-input>
                                            <small v-if="errors.contact"
                                                   class="form-control-feedback"
                                                   v-text="errors.contact[0]"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div :class="{'has-danger': errors.observation }"
                                     class="form-group contact-observation">
                                    <label class="control-label">Observaciones</label>
                                    <el-input type="textarea"
                                              :rows="8"
                                              v-model="form.observation"></el-input>
                                    <small v-if="errors.observation"
                                           class="form-control-feedback"
                                           v-text="errors.observation[0]"></small>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane class
                                 name="fourth">
                        <span slot="label">Avanzado</span>

                        <div class="row mb-3 section-header section-header-first">
                            <div class="col-12">
                                <h5 class="section-title">Crédito y Facturación</h5>
                                <p class="section-subtitle">Configuraciones de días de crédito y condiciones especiales</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div :class="{'has-danger': errors.credit_days}"
                                     class="form-group">
                                    <label class="control-label">Días de crédito</label>
                                    <el-input-number
                                        v-model="form.credit_days"
                                        :controls="false"
                                        :min="0"
                                        :precision="0"></el-input-number>
                                    <small v-if="errors.credit_days"
                                           class="form-control-feedback"
                                           v-text="errors.credit_days[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Clasificación</h5>
                                <p class="section-subtitle">Tipo de cliente y agentes</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div :class="{'has-danger': errors.person_type_id}"
                                     class="form-group">
                                    <label class="control-label">{{ typeDialog }}</label>
                                    <el-select v-model="form.person_type_id"
                                               clearable
                                               filterable>
                                        <el-option v-for="option in person_types"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                    </el-select>
                                    <small v-if="errors.person_type_id"
                                           class="form-control-feedback"
                                           v-text="errors.person_type_id[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-4 center-el-checkbox">
                                <div :class="{'has-danger': errors.is_agent_retention}" class="form-group">
                                    <el-checkbox v-model="form.is_agent_retention">Es agente de retención</el-checkbox>
                                    <br>
                                    <small v-if="errors.is_agent_retention" class="invalid-feedback" v-text="errors.is_agent_retention[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-4 center-el-checkbox" v-if="type === 'suppliers'">
                                <div :class="{'has-danger': errors.perception_agent}" class="form-group">
                                    <el-checkbox v-model="form.perception_agent">Es agente de percepción</el-checkbox>
                                    <br>
                                    <small v-if="errors.perception_agent" class="form-control-feedback" v-text="errors.perception_agent[0]"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="form.perception_agent && type === 'suppliers'">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Porcentaje de percepción</label>
                                    <el-input v-model="form.percentage_perception"></el-input>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Identificadores</h5>
                                <p class="section-subtitle">Códigos internos y de barra para integraciones</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div :class="{'has-danger': errors.internal_code}"
                                     class="form-group">
                                    <label class="control-label">Código interno</label>
                                    <el-input v-model="form.internal_code"></el-input>
                                    <small v-if="errors.internal_code"
                                           class="form-control-feedback"
                                           v-text="errors.internal_code[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div :class="{'has-danger': errors.barcode}"
                                     class="form-group">
                                    <label class="control-label">Código de barra</label>
                                    <el-input v-model="form.barcode"></el-input>
                                    <small v-if="errors.barcode"
                                           class="form-control-feedback"
                                           v-text="errors.barcode[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Vendedor asignado</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4">
                                <div :class="{'has-danger': errors.seller_id}" class="form-group">
                                    <label class="control-label">Vendedor</label>
                                    <el-select v-model="form.seller_id" clearable>
                                        <el-option v-for="option in sellers" :key="option.id" :label="option.name" :value="option.id">{{ option.name }}</el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 section-header">
                            <div class="col-12">
                                <h5 class="section-title">Acceso al portal</h5>
                                <p class="section-subtitle">Credenciales de usuario (solo clientes)</p>
                            </div>
                        </div>
                        <div class="row">
                            <div v-if="type === 'customers'" class="col-md-4">
                                <div :class="{ 'has-danger': errors.password }" class="form-group">
                                    <label class="control-label">Contraseña
                                        <el-tooltip class="item" effect="dark" placement="top-start" v-if="config_regex_password_user">
                                            <i class="fa fa-info-circle"></i>
                                            <div slot="content">
                                                <strong>FORMATO DE CONTRASEÑA</strong><br/><br/>
                                                La contraseña debe contener al menos una letra minúscula.<br/>
                                                La contraseña debe contener al menos una letra mayúscula.<br/>
                                                La contraseña debe contener al menos un dígito.<br/>
                                                La contraseña debe contener al menos un carácter especial [@.$!%*#?&-].<br/>
                                            </div>
                                        </el-tooltip>
                                    </label>
                                    <el-input v-model="form.password" show-password></el-input>
                                    <small v-if="errors.password" class="form-control-feedback" v-text="errors.password[0]"></small>
                                </div>
                            </div>
                            <div v-if="type === 'customers'" class="col-md-4">
                                <div :class="{ 'has-danger': errors.password_confirmation }" class="form-group">
                                    <label class="control-label">Confirmar contraseña</label>
                                    <el-input v-model="form.password_confirmation" show-password></el-input>
                                    <small v-if="errors.password_confirmation" class="form-control-feedback" v-text="errors.password_confirmation[0]"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="type === 'customers'">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <el-checkbox v-model="form.notify_email_on_save">Enviar credenciales por correo al guardar</el-checkbox>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div v-if="form.state" class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Estado del Contribuyente</label>
                                    <template v-if="form.state == 'ACTIVO'">
                                        <el-alert :closable="false" :title="`${form.state}`" show-icon type="success"></el-alert>
                                    </template>
                                    <template v-else>
                                        <el-alert :closable="false" :title="`${form.state}`" show-icon type="error"></el-alert>
                                    </template>
                                </div>
                            </div>
                            <div v-if="form.condition" class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Condición del Contribuyente</label>
                                    <template v-if="form.condition == 'HABIDO'">
                                        <el-alert :closable="false" :title="`${form.condition}`" show-icon type="success"></el-alert>
                                    </template>
                                    <template v-else>
                                        <el-alert :closable="false" :title="`${form.condition}`" show-icon type="error"></el-alert>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <div class="form-actions text-end mt-4">
                <el-button class="second-buton me-2" @click.prevent="handleCloseDialog()">Cancelar</el-button>
                <el-button :loading="loading_submit"
                           native-type="submit"
                           type="primary">Guardar
                </el-button>
            </div>
        </form>
    <consigned-form
        :recordId="recordId"
        :showDialog.sync="showDialogConsignedForm"
        ></consigned-form>
    </el-dialog>


</template>

<script>
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

import {serviceNumber} from '../../../mixins/functions'
import ConsignedForm from './partials/consigned.vue'

export default {
    mixins: [serviceNumber],
    props: [
        'showDialog',
        'type',
        'recordId',
        'external',
        'document_type_id',
        'input_person',
        'parentId',
        'is_dispatch'
    ],
    components: {ConsignedForm},
    data() {
        return {
            form_zone: {add: false, name: null, id: null},
            parent: null,
            loading_submit: false,
            titleDialog: null,
            titleTabDialog: null,
            typeDialog: null,
            resource: 'persons',
            showDialogConsignedForm: false, 
            errors: {},
            api_service_token: false,
            form: {
                optional_email: []
            },
            temp_optional_email: [],
            temp_email: null,
            countries: [],
            zones: [],
            sellers: [],
            all_departments: [],
            all_provinces: [],
            all_districts: [],
            provinces: [],
            districts: [],
            locations: [],
            consigneds: [],
            person_types: [],
            identity_document_types: [],
            discount_types: [],
            activeName: 'first',
            config_regex_password_user: false,
            establishments: [],
            loading_establishments: false,
            principal_establishment_code: null,
        }
    },
    async created() {

        this.loadConfiguration()
        await this.initForm()
        await this.getConsigneds()
        await this.$http.get(`/${this.resource}/tables`)
            .then(response => {
                this.api_service_token = response.data.api_service_token
                // console.log(this.api_service_token)

                this.countries = response.data.countries
                this.zones = response.data.zones
                this.sellers = response.data.sellers
                this.all_departments = response.data.departments;
                this.all_provinces = response.data.provinces;
                this.all_districts = response.data.districts;
                this.identity_document_types = response.data.identity_document_types;
                this.locations = response.data.locations;
                this.person_types = response.data.person_types;
                this.discount_types = response.data.discount_types;
            })
            .finally(() => {
                if (this.api_service_token === false) {
                    if (this.config.api_service_token !== undefined) {
                        this.api_service_token = this.config.api_service_token
                    }
                }
            })

        this.$eventHub.$on("reloadDataConsigned", () => {
            this.getConsigneds();
        });
    },
    computed: {
        ...mapState([
            'config',
            'person',
            'parentPerson',
        ]),
        maxLength: function () {
            if (this.form.identity_document_type_id === '6') {
                return 11
            }
            if (this.form.identity_document_type_id === '1') {
                return 8
            }
        },
        canQueryEstablishments() {
            return this.api_service_token != false
                && this.form.identity_document_type_id === '6'
                && !!this.form.name
                && !!this.form.number
                && this.form.number.length === 11
        },
        establishmentsNote() {
            const selected = this.establishments.filter(e => e.selected).length
            const principal = this.establishments.find(e => e.codigo === this.principal_establishment_code)
            if (!principal) return 'Selecciona una dirección principal.'
            return `Principal <b>N° ${principal.codigo}</b> · se guardarán <b>${selected} dirección(es)</b>.`
        },
        addressesBadgeCount() {
            // Dirección principal (columnas de la persona) + direcciones adicionales.
            // Al guardar, si no hay adicionales la principal se persiste como 1 dirección.
            const additional = (this.form.addresses || []).length
            const hasMain = !!(this.form.address
                && Array.isArray(this.form.location_id)
                && this.form.location_id.length === 3
                && this.form.location_id.every(x => x))
            if (additional === 0) return hasMain ? 1 : 0
            return additional + (hasMain ? 1 : 0)
        },
    },
    watch: {
        'form.country_id': function(newValue) {
            if (newValue !== 'PE' && this.form.location_id && this.form.location_id.length > 0) {
                this.form.location_id = [];
            }
        }
    },
    methods: {
        handleCloseDialog() {
          if (this.hasUnsavedChanges()) {
            this.$confirm('¿Estás seguro de cerrar el formulario? Se perderán los datos no guardados.', 'Confirmar', {
              confirmButtonText: 'Cerrar sin guardar',
              cancelButtonText: 'Cancelar',
              type: 'warning'
            }).then(() => {
              this.forceCloseDialog()
            }).catch(() => {

            });
          } else {
            this.forceCloseDialog()
          }
        },
        getConsigneds() {
            return this.$http
                .get(`/consigneds/data`)
                .then(response => {
                    this.consigneds = response.data.data;
                })
                .catch(error => {})
                .then(() => {

                });
        },
        forceCloseDialog() {
          this.initForm()
          this.$emit('update:showDialog', false)
        },
        hasUnsavedChanges() {
            return JSON.stringify(this.form) !== this.originalForm
        },
        customFilterMethod(node, keyword) {
            return node.label.toLowerCase().includes(keyword.toLowerCase());
        },

        ...mapActions([
            'loadConfiguration',
        ]),
        initForm() {
            this.errors = {}
            this.form = {
                id: null,
                type: this.type,
                credit_days: 0,
                identity_document_type_id: '6',
                number: '',
                name: null,
                trade_name: null,
                country_id: 'PE',
                nationality_id: 'PE',
                location_id: [],
                password: null,
                password_confirmation: null,
                address: null,
                telephone: null,
                condition: null,
                state: null,
                email: null,
                perception_agent: false,
                percentage_perception: 0,
                person_type_id: null,
                comment: null,
                addresses: [],
                contact: {
                    full_name: null,
                    phone: null,
                },
                optional_email: [],
                has_discount: false,
                discount_type: '01',
                is_agent_retention: false,
                discount_amount: 0,
                establishment_code:'0000',
                notify_email_on_save: false,

            }
            this.updateEmail()
            this.resetEstablishments()
            this.originalForm = JSON.stringify(this.form)

        },
        async opened() {

            if (this.external && this.input_person) {
                if (this.form.number.length === 8 || this.form.number.length === 11) {
                    if (this.api_service_token != false) {
                        await this.$eventHub.$emit('enableClickSearch')
                    } else {
                        this.searchCustomer()
                    }
                }
            }

        },
        create() {
            // console.log(this.input_person)
            this.parent = 0;
            if (this.parentId !== undefined) {
                this.parent = this.parentId;
            }
            /*

            'person',
            'parentPerson',
            */
            if (this.external) {
                if (this.document_type_id === '01') {
                    this.form.identity_document_type_id = '6'
                }
                if (this.document_type_id === '03') {
                    this.form.identity_document_type_id = '1'
                }

                if (this.input_person) {
                    // Si input_person es un string, asignarlo al nombre
                    if (typeof this.input_person === 'string') {
                        this.form.name = this.input_person
                    } else {
                        // Si es un objeto, mantener la lógica existente
                        this.form.identity_document_type_id = (this.input_person.identity_document_type_id) ? this.input_person.identity_document_type_id : this.form.identity_document_type_id
                        this.form.number = (this.input_person.number) ? this.input_person.number : ''
                    }
                }
            }
            if (this.type === 'customers') {
                this.titleDialog = (this.recordId) ? 'Editar Cliente' : 'Nuevo Cliente'
                this.titleTabDialog = 'Datos Básicos';
                this.typeDialog = 'Tipo de cliente'
            }
            if (this.type === 'suppliers') {
                this.titleDialog = (this.recordId) ? 'Editar Proveedor' : 'Nuevo Proveedor'
                this.titleTabDialog = 'Datos del proveedor';
                this.typeDialog = 'Tipo de proveedor'
            }

            if (this.recordId) {
                this.$http.get(`/${this.resource}/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data.data
                        if (response.data.data.contact == null) {
                            this.form.contact = {
                                full_name: null,
                                phone: null,
                            }
                        }
                        this.filterProvinces()
                        this.filterDistricts()
                    }).then(() => {
                    this.updateEmail()

                })
            }
        },
        clickAddAddress() {
            /* this.form.more_address.push({
                 location_id: [],
                 address: null,
             })*/

            this.form.addresses.push({
                'id': null,
                'country_id': 'PE',
                'location_id': [],
                'address': null,
                'email': null,
                'phone': null,
                'main': false,
                'establishment_code':'0000',
                'has_consigned': false,
                'consigned_id': null,
            });
        },
        validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        },
        updateEmail() {
            this.temp_optional_email = this.form.optional_email;

        },
        removeEmail(email) {
            if (this.form.optional_email === undefined) this.form.optional_email = []
            this.form.optional_email = this.form.optional_email.filter(function (item) {
                return item.email !== email.email;
            });

            this.updateEmail()

        },
        checkEmail() {
            this.errors.temp_email = null;
            if (this.temp_email === null) {
                return false;
            }
            if (this.temp_email === undefined) {
                return false;
            }
            let email = this.temp_email;

            if (this.validateEmail(email)) {
                if (this.form.optional_email !== undefined) {
                    let tem = _.find(this.form.optional_email, {'email': email});
                    if (tem === undefined) {
                        return true;
                    } else {
                        // this.errors.temp_email = "Correo ya registrado"
                    }
                }
            } else {
                // this.errors.temp_email = "No es un correo valido"
            }
            return false;

        },
        clickAddMail() {
            if (this.form.optional_email === undefined) this.form.optional_email = []
            if (this.checkEmail() === true) {
                let email = this.temp_email;
                this.form.optional_email.push(
                    {email: email,}
                )
                this.updateEmail()
                this.temp_email = null;
            }

        },
        validateDigits() {

            const pattern_number = new RegExp('^[0-9]+$', 'i');

            if (this.form.identity_document_type_id === '6') {

                if (this.form.number.length !== 11) {
                    return {
                        success: false,
                        message: `El campo número debe tener 11 dígitos.`
                    }
                }

                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`
                    }
                }

            }


            if (this.form.identity_document_type_id === '1') {

                if (this.form.number.length !== 8) {
                    return {
                        success: false,
                        message: `El campo número debe tener 8 dígitos.`
                    }
                }

                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`
                    }
                }
            }


            if (['4', '7', '0'].includes(this.form.identity_document_type_id)) {

                const pattern = new RegExp('^[A-Z0-9\-]+$', 'i');

                if (!pattern.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número no cumple con el formato establecido`
                    }
                }

            }


            return {
                success: true
            }
        },
        async submit() {

            let val_digits = await this.validateDigits()
            if (!val_digits.success) {
                return this.$message.error(val_digits.message)
            }

            if (!this.config.enable_discount_by_customer) {
                this.form.has_discount = false;
            }

            if (!this.form.has_discount) {
                this.form.discount_type = '01';
                this.form.discount_amount = 0;
            }

            /*if (this.is_dispatch) {
               if (this.form.location_id.length !== 3 || !this.form.address) {
                    return this.$message.error('Falta agregar Ubigeo o dirección');
               } 
            }*/

            let hasErrorInAdditionalAddresses = false;
            let addressWithError = null;

            if (this.form.addresses && this.form.addresses.length > 0) {
                for (let i = 0; i < this.form.addresses.length; i++) {
                    const address = this.form.addresses[i];
                    if (address.country_id === 'PE' && (!address.location_id || address.location_id.length !== 3)) {
                        hasErrorInAdditionalAddresses = true;
                        addressWithError = i + 1;
                        break;
                    }
                }
            }

            if (hasErrorInAdditionalAddresses) {
                const mensaje = addressWithError - 1 === 0 
                    ? `Falta registrar el ubigeo en la Dirección principal` 
                    : `Falta registrar el ubigeo en la Dirección secundaria #${addressWithError - 1}`;
                return this.$message.error(mensaje);
            }

            // if(this.form.location_id.length===3 && this.form.identity_document_type_id === '6'){
            //     if(!this.form.address){
            //         return this.$message.error('Se debe ingresar dirección');
            //     }
            // }

            if( this.form.location_id.every(lid => lid !== null ) &&  this.form.addresses.length==0 && this.form.location_id.length===3 && this.form.address!=null ){
                this.form.addresses.push({
                    'id': null,
                    'country_id': 'PE',
                    'location_id': this.form.location_id,
                    'address': this.form.address,
                    'email': this.form.email,
                    'phone': this.form.telephone,
                    'main': false,
                    'establishment_code':this.form.establishment_code,
                    'has_consigned' : false
                });
            }

            this.loading_submit = true
            this.form.parent_id = parseInt(this.parent);
            await this.$http.post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        if (this.external) {
                            this.$eventHub.$emit('reloadDataPersons', response.data.id)
                        } else {
                            this.$eventHub.$emit('reloadData')
                        }
                        this.close()
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
                .finally(() => {
                    this.loading_submit = false
                })
        },
        changeIdentityDocType() {
            (this.recordId == null) ? this.setDataDefaultCustomer() : null
        },
        setDataDefaultCustomer() {

            if (this.form.identity_document_type_id == '0') {
                this.form.number = '99999999'
                this.form.name = "Clientes - Varios"
            } else {
                this.form.number = ''
                this.form.name = null
            }

        },
        close() {
            this.$eventHub.$emit('initInputPerson')
            this.$emit('update:showDialog', false)
            this.initForm()
        },
        searchCustomer() {
            this.searchServiceNumberByType()
        },
        searchNumber(data) {
            //cambios apiperu
            this.resetEstablishments()
            this.form.name = data.name;
            if (data.trade_name) this.form.trade_name = data.trade_name;
            this.form.location_id = data.location_id;
            this.form.address = data.address;
            // this.form.department_id = data.department_id;
            // this.form.department_id = data.department_id;
            // this.form.province_id = data.province_id;
            // this.form.district_id = data.district_id;
            this.form.condition = data.condition;
            this.form.state = data.state;
            this.form.is_agent_retention = data.is_agent_retention
            // this.filterProvinces()
            // this.filterDistricts()
//                this.form.addresses[0].telephone = data.telefono;
            // Mostrar el domicilio fiscal (0000) como primera y única opción del listado
            this.setPrincipalFromRuc(data)
        },
        clickRemoveAddress(index) {
            this.form.addresses.splice(index, 1);
        },

        resetEstablishments() {
            this.establishments = []
            this.principal_establishment_code = null
            this.loading_establishments = false
        },
        isEstablishmentRegistered(code) {
            if (!this.recordId) return false
            if (this.form.establishment_code && this.form.establishment_code === code) return true
            return (this.form.addresses || []).some(a => a.id && a.establishment_code === code)
        },
        setPrincipalFromRuc(data) {
            // Construye el domicilio fiscal (0000) con la data de la consulta RUC
            // y lo muestra como primera y única opción del listado de establecimientos.
            const location_id = Array.isArray(data.location_id) ? data.location_id : []
            const code = this.form.establishment_code || '0000'
            const has_address = !!data.address && location_id.length === 3
            const row = {
                codigo: code,
                tipo: 'Domicilio Fiscal',
                direccion: data.address || null,
                direccion_completa: data.address || null,
                location_id: location_id,
                has_address: has_address,
                is_ruc_principal: true,
                selected: true,
                registered: this.isEstablishmentRegistered(code),
            }
            this.establishments = [row]
            this.principal_establishment_code = code
            this.syncAddressesFromEstablishments()
        },
        mapApiEstablishment(item) {
            // Normaliza un item crudo del endpoint ruc-establecimientos-anexos
            const direccion = (item.direccion || '').trim()
            const ubigeo = Array.isArray(item.ubigeo) ? item.ubigeo : []
            const location_id = ubigeo.length === 3 ? ubigeo : []
            const has_address = direccion !== '' && direccion !== '-' && location_id.length === 3
            return {
                codigo: item.codigo || null,
                tipo: item.tipo_de_establecimiento || null,
                direccion: has_address ? direccion : null,
                location_id: location_id,
                has_address: has_address,
                selected: false,
                registered: this.isEstablishmentRegistered(item.codigo),
            }
        },
        async consultEstablishments() {
            if (!this.form.number || this.form.number.length !== 11) {
                return this.$message.error('Ingresar un RUC válido de 11 dígitos')
            }
            this.loading_establishments = true
            try {
                const response = await this.$http.get(`/service/ruc-establecimientos/${this.form.number}`)
                if (response.data.success) {
                    const anexos = response.data.data || []
                    // Conservar el domicilio fiscal (0000) que vino de la consulta RUC como primera fila
                    const rucPrincipal = this.establishments.find(e => e.is_ruc_principal)
                    const merged = []
                    if (rucPrincipal) merged.push(rucPrincipal)
                    anexos.forEach(item => {
                        const est = this.mapApiEstablishment(item)
                        if (merged.some(m => m.codigo === est.codigo)) return
                        merged.push(est)
                    })
                    if (merged.length === 0) {
                        return this.$message.info('No se encontraron establecimientos para este RUC')
                    }
                    this.establishments = merged
                    // Principal por defecto: el domicilio fiscal; si no tiene dirección, el primer registro utilizable
                    const principal = merged.find(e => e.is_ruc_principal && e.has_address && !e.registered)
                        || merged.find(e => e.has_address && !e.registered)
                        || merged.find(e => e.has_address)
                        || merged[0]
                    this.principal_establishment_code = principal ? principal.codigo : null
                    this.selectOnlyPrincipal()
                } else {
                    // p. ej. "No se encontró información para locales anexos."
                    // Se conserva el domicilio fiscal (0000) ya mostrado.
                    this.$message.info(response.data.message || 'No se encontraron establecimientos anexos')
                }
            } catch (error) {
                console.log(error)
                this.$message.error('Error al consultar los establecimientos')
            } finally {
                this.loading_establishments = false
            }
        },
        selectOnlyPrincipal() {
            this.establishments.forEach(e => {
                e.selected = (e.codigo === this.principal_establishment_code)
            })
            this.syncAddressesFromEstablishments()
        },
        selectAllEstablishments() {
            this.establishments.forEach(e => {
                e.selected = e.has_address && !e.registered
            })
            const principal = this.establishments.find(e => e.codigo === this.principal_establishment_code)
            if (principal) principal.selected = true
            this.syncAddressesFromEstablishments()
        },
        onToggleEstablishment(est) {
            // No permitir desmarcar la principal
            if (est.codigo === this.principal_establishment_code && !est.selected) {
                est.selected = true
                return
            }
            this.syncAddressesFromEstablishments()
        },
        setAsPrincipal(est) {
            if (!est.has_address || est.registered) return
            this.principal_establishment_code = est.codigo
            est.selected = true
            this.syncAddressesFromEstablishments()
        },
        buildAddressFromEstablishment(est, main) {
            return {
                id: null,
                country_id: 'PE',
                location_id: est.location_id,
                address: est.direccion,
                email: null,
                phone: null,
                main: main,
                establishment_code: est.codigo,
                has_consigned: false,
                consigned_id: null,
                from_sunat_establishment: true,
            }
        },
        syncAddressesFromEstablishments() {
            const principal = this.establishments.find(e => e.codigo === this.principal_establishment_code)

            // La principal alimenta la sección "Dirección principal" (columnas de la persona)
            if (principal) {
                this.form.address = principal.direccion
                this.form.location_id = principal.location_id
                this.form.establishment_code = principal.codigo
            }

            // Se conservan las direcciones guardadas (con id) y las manuales;
            // solo se reconstruyen las que provienen de este panel.
            const preserved = (this.form.addresses || []).filter(a => !a.from_sunat_establishment)

            // Secundarias = seleccionadas distintas de la principal, con dirección y no registradas
            const secondary = this.establishments
                .filter(e => e.selected
                    && e.codigo !== this.principal_establishment_code
                    && e.has_address
                    && !e.registered)
                .map(e => this.buildAddressFromEstablishment(e, false))

            this.form.addresses = [...preserved, ...secondary]
        },

        saveZone() {
            this.form_zone.add = false

            this.$http.post(`/zones`, this.form_zone)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        this.zones.push(response.data.data)
                        this.form_zone.name = null

                    } else {
                        this.$message.error('No se guardaron los cambios')
                    }
                })
                .catch(error => {

                })


        },
        handleCountryChange(row, index) {
            if (row.country_id !== 'PE' && row.location_id && row.location_id.length > 0) {
                this.$set(this.form.addresses[index], 'location_id', []);
            }
        },
    }
}
</script>

<style scoped>
.sunat-row {
  display: flex;
  align-items: stretch;
  gap: 0;
}

.sunat-input {
  flex: 1;
  width: 100%;
}

.sunat-service-button {
  min-width: 95px !important;
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  border-top-right-radius: 6px !important;
  border-bottom-right-radius: 6px !important;
  margin-left: -1px !important;
  white-space: nowrap !important;
}

.sunat-input .el-input__suffix,
.sunat-input .el-input__append {
  display: none !important;
}



.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  text-transform: uppercase;
  color: #1f3a8a;
  margin-bottom: 0.2rem;
}

.section-subtitle {
  color: #6b7280;
  font-size: 0.86rem;
  margin-bottom: 0.5rem;
}

.section-subtitle-sm {
  font-size: 0.89rem;
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.5rem;
}

.section-divider {
  border-top: 2px solid #3b82f6;
  margin: 0.35rem 0 1rem 0;
}

.section-header {
  border-top: 1px solid #d4e4f4;
  margin-top: 1.8rem;
  padding-top: 0.8rem;
  padding-bottom: 0.7rem;
}

.section-header.section-header-first {
  border-top: 0;
  margin-top: 0;
  padding-top: 0;
  padding-bottom: 0.7rem;
}

.section-subgroup {
  padding: 0.8rem 0;
  margin-bottom: 0.8rem;
  border-top: 1px solid #e7eff9;
  background: #f8fbff;
  border-radius: 8px;
}

.subsection-panel {
  border: 1px solid #dbe9f8;
  border-radius: 8px;
  background: #fbfdff;
  padding: 1rem;
  margin-bottom: 1rem;
}

.subsection-panel .section-subtitle-sm {
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #1f3a8a;
  font-size: 0.98rem;
}

.section-subtitle-sm {
  font-size: 0.95rem;
  font-weight: 600;
  color: #1f3a8a;
  margin-bottom: 0.35rem;
}

.contact-observation .el-textarea__inner {
  min-height: 170px !important;
}

.contact-observation {
  height: 100%;
}

/* ---- Panel de establecimientos SUNAT ---- */
.btn-query-establishments {
  border: 1px dashed #b9c9e0;
  color: #1f3a8a;
  background: #f4f8ff;
}

.establishments-panel {
  border: 1px solid #dbe9f8;
  border-radius: 10px;
  background: #f8fbff;
  padding: 12px 14px 6px;
}

.establishments-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 8px;
}

.establishments-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1f3a8a;
  display: flex;
  align-items: center;
  gap: 8px;
}

.establishments-presets {
  display: flex;
  gap: 6px;
}

.establishments-rows {
  display: block;
  width: 100%;
}

.establishment-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 4px;
  border-top: 1px solid #e7eff9;
}

.establishment-row:first-child {
  border-top: none;
}

.establishment-row.is-dim {
  opacity: 0.55;
}

.est-switch {
  flex-shrink: 0;
}

.tab-count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  margin-left: 6px;
  border-radius: 9px;
  background: #409eff;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 600;
  line-height: 1;
}

.est-badge {
  font-size: 0.68rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 6px;
  flex-shrink: 0;
}

.est-badge.principal {
  background: #e8f0ff;
  color: #2f6bff;
}

.est-badge.sucursal {
  background: #eceef1;
  color: #6b7280;
}

.est-addr {
  flex: 1;
  min-width: 0;
  font-size: 0.85rem;
  color: #334155;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.est-addr.warn {
  color: #b5710a;
}

.est-registered {
  flex-shrink: 0;
}

.est-code {
  font-size: 0.72rem;
  color: #94a3b8;
  flex-shrink: 0;
}

.establishments-note {
  font-size: 0.76rem;
  color: #6b7280;
  padding: 8px 4px 6px;
  border-top: 1px solid #e7eff9;
  margin-top: 2px;
}
</style>