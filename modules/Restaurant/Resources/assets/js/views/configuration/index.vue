<template>
    <div>
      <div class="page-header pe-0">
        <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
        <ol class="breadcrumbs">
          <li class="active"><span>Configuración</span></li>
          <li><span class="text-muted">Restaurante</span></li>
        </ol>
      </div>
      <template>
        <form autocomplete="off">
          <el-tabs v-model="activeName" type="border-card" class="rounded" @tab-click="onTabClick">
            <el-tab-pane class="mb-3"  name="first">
              <span slot="label">Ambientes</span>
              <Environments />
              <!-- <div class="row">
                <div class="col-md-12">
                  <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                      <div class="card-body bg-light m-0">
                        <h4 class="text-dark">Ambiente: <b>{{ environment_1.original_name }}</b>
                          <template v-if="form.enabled_environment_1">
                              <el-tooltip class="item"
                                          content="Editar Nombre de Ambiente"
                                          effect="dark"
                                          placement="top-start">
                                <el-button class="second-buton btn btn-xs float-right" @click="environment_1.enabled_edit=true">
                                          Editar nombre
                                </el-button>
                              </el-tooltip>
                          </template>
                        </h4>
                        <div v-if="environment_1.enabled_edit" class="row">
                          <div class=" col-6 form-group mb-0" :class="{'has-danger': errors.number}">
                              <el-input v-model="environment_1.name" :maxlength="30"></el-input>
                              <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                          </div>
                          <div class=" form-group mb-0">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickEditEnvironment(1)">
                              <i class="fa fa-check"></i>
                            </button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelEnvironment(1)">
                                <i class="el-icon-close"></i>
                            </button>
                          </div>
                        </div>
                        <label class="control-label">
                          Habilitar ambiente
                        </label>

                      <div :class="{'has-danger': errors.enabled_environment_1}"
                            class="form-group tables-restaurant">
                        <el-switch v-model="form.enabled_environment_1"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.enabled_environment_1"
                                class="form-control-feedback"
                                v-text="errors.enabled_environment_1[0]"></small>
                        <br><br>
                        <label class="control-label tables-quantity">Cantidad de mesas <b>{{ form.tables_quantity }}</b></label>
                        <el-slider
                          :disabled="!form.enabled_environment_1"
                          v-model="form.tables_quantity"
                          :step="1"
                          :min="5"
                          :max="50"
                          show-stops
                          @change="submit">
                        </el-slider>
                      </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-4">
                      <div class="card-body bg-light m-0">
                      <h4 class="text-dark">Ambiente: <b>{{ environment_2.original_name }}</b>
                        <template  v-if="form.enabled_environment_2">
                          <el-tooltip class="item"
                                        content="Editar Nombre de Ambiente"
                                        effect="dark"
                                        placement="top-start">
                              <el-button class="btn btn-xs float-right" @click="environment_2.enabled_edit=true">
                                Editar nombre
                              </el-button>
                          </el-tooltip>
                        </template>
                      </h4>
                      <div v-if="environment_2.enabled_edit" class="row">
                          <div class=" col-6 form-group mb-0" :class="{'has-danger': errors.number}">
                              <el-input v-model="environment_2.name" :maxlength="30"></el-input>
                              <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                          </div>
                          <div class=" form-group mb-0">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickEditEnvironment(2)">
                              <i class="fa fa-check"></i>
                            </button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelEnvironment(2)">
                                <i class="el-icon-close"></i>
                            </button>
                          </div>
                      </div>
                      <label class="control-label">
                        Habilitar Ambiente
                      </label>
                      <div :class="{'has-danger': errors.enabled_environment_2}"
                            class="form-group tables-restaurant">
                        <el-switch v-model="form.enabled_environment_2"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.enabled_environment_2"
                                class="form-control-feedback"
                                v-text="errors.enabled_environment_2[0]"></small>
                        <br><br>
                        <label class="control-label tables-quantity">Cantidad de mesas <b>{{ form.tables_quantity_environment_2 }}</b></label>
                        <el-slider
                          :disabled="!form.enabled_environment_2"
                          v-model="form.tables_quantity_environment_2"
                          :step="1"
                          :min="5"
                          :max="50"
                          show-stops
                          @change="submit">
                        </el-slider>
                      </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-4">
                      <div class="card-body bg-light m-0">
                      <h4 class="text-dark">Ambiente: <b>{{ environment_3.original_name }}</b>
                        <template  v-if="form.enabled_environment_3">
                          <el-tooltip class="item"
                                        content="Editar Nombre de Ambiente"
                                        effect="dark"
                                        placement="top-start">
                            <el-button class="btn btn-xs float-right" @click="environment_3.enabled_edit=true">
                              Editar nombre
                            </el-button>
                          </el-tooltip>
                        </template>
                      </h4>
                      <div v-if="environment_3.enabled_edit" class="row">
                          <div class=" col-6 form-group mb-0" :class="{'has-danger': errors.number}">
                              <el-input v-model="environment_3.name" :maxlength="30"></el-input>
                              <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                          </div>
                          <div class=" form-group mb-0">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickEditEnvironment(3)">
                              <i class="fa fa-check"></i>
                            </button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelEnvironment(3)">
                                <i class="el-icon-close"></i>
                            </button>
                          </div>
                      </div>
                      <label class="control-label">
                        Habilitar Ambiente
                      </label>
                      <div :class="{'has-danger': errors.enabled_environment_3}"
                            class="form-group tables-restaurant">
                        <el-switch v-model="form.enabled_environment_3"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.enabled_environment_3"
                                class="form-control-feedback"
                                v-text="errors.enabled_environment_3[0]"></small>
                        <br><br>
                        <label class="control-label tables-quantity">Cantidad de mesas <b>{{ form.tables_quantity_environment_3 }}</b></label>
                        <el-slider
                          :disabled="!form.enabled_environment_3"
                          v-model="form.tables_quantity_environment_3"
                          :step="1"
                          :min="5"
                          :max="50"
                          show-stops
                          @change="submit">
                        </el-slider>
                      </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-4">
                      <div class="card-body bg-light m-0">
                      <h4 class="text-dark">Ambiente: <b>{{ environment_4.original_name }}</b>
                        <template  v-if="form.enabled_environment_4">
                            <el-tooltip class="item"
                                        content="Editar Nombre de Ambiente"
                                        effect="dark"
                                        placement="top-start">
                              <el-button class="btn btn-xs float-right" @click="environment_4.enabled_edit=true">
                              Editar nombre
                            </el-button>
                          </el-tooltip>
                        </template>
                      </h4>
                      <div v-if="environment_4.enabled_edit" class="row">
                          <div class=" col-6 form-group mb-0" :class="{'has-danger': errors.number}">
                              <el-input v-model="environment_4.name" :maxlength="30"></el-input>
                              <small class="form-control-feedback" v-if="errors.number" v-text="errors.number[0]"></small>
                          </div>
                          <div class=" form-group mb-0">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickEditEnvironment(4)">
                              <i class="fa fa-check"></i>
                            </button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancelEnvironment(4)">
                                <i class="el-icon-close"></i>
                            </button>
                          </div>
                      </div>
                      <label class="control-label">
                        Habilitar Ambiente
                      </label>
                      <div :class="{'has-danger': errors.enabled_environment_4}"
                            class="form-group tables-restaurant">
                        <el-switch v-model="form.enabled_environment_4"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="submit"></el-switch>
                        <small v-if="errors.enabled_environment_4"
                                class="form-control-feedback"
                                v-text="errors.enabled_environment_4[0]"></small>
                        <br><br>
                        <label class="control-label tables-quantity">Cantidad de mesas <b>{{ form.tables_quantity_environment_4 }}</b></label>
                        <el-slider
                          :disabled="!form.enabled_environment_4"
                          v-model="form.tables_quantity_environment_4"
                          :step="1"
                          :min="5"
                          :max="50"
                          show-stops
                          @change="submit">
                        </el-slider>
                      </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->
            </el-tab-pane>
            <el-tab-pane class="mb-3"  name="second">
              <span slot="label">Usuarios</span>
              <div class="row d-flex align-items-end">
                <div class="col-sm-4 col-md-4 mt-4">
                  <el-button class="submit" type="primary" @click="clickCreateUser()" >Nuevo Usuario
                  </el-button>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Usuario</th>
                          <th>Correo</th>
                          <th>Rol</th>
                          <th class="text-end">Accesos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="user in users" :key="user.id">
                          <td>{{user.name}}</td>
                          <td>{{user.email}}</td>
                          <td>{{user.restaurant_role_name }}</td>
                          <td class="text-end">
                            <template v-if="user.restaurant_role_code == 'ADM'">
                              <el-tag type="primary" v-for="(item, index) in permission_adm" :key="index + 'ADM'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'CAJA'">
                              <el-tag type="primary" v-for="(item, index) in permission_caja" :key="index + 'CAJA'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'KITBAR'">
                               <el-tag type="primary" v-for="(item, index) in permission_kitbar" :key="index + 'KITBAR'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'MOZO'">
                              <el-tag type="primary" v-for="(item, index) in permission_mozo" :key="index + 'MOZO'">{{ item }}
                                <el-tooltip class="item"
                                            content="Para habilitar acceso a Comandas y POS, ingresar a Configuración/Restaurante  Configuración adicional"
                                            effect="dark"
                                            placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                              </el-tag>
                            </template>
                            <template>
                              <el-tag type="warning" @click="clickCreateUser(user.id)" style="cursor: pointer;">Editar
                                <el-tooltip class="item"
                                            content="Editar usuario"
                                            effect="dark"
                                            placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                              </el-tag>
                            </template>
                            <!-- <template v-if="user.restaurant_role_id">
                              <el-tag type="danger" @click="sendDeleteRole(user.id)" style="cursor: pointer;">Eliminar Rol
                                <el-tooltip class="item"
                                            content="Quitar ROL de usuario"
                                            effect="dark"
                                            placement="top-start">
                                    <i class="fa fa-info-circle"></i>
                                </el-tooltip>
                              </el-tag>
                            </template> -->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </el-tab-pane>

            <el-tab-pane class="mb-3"  name="four">
              <span slot="label">Notas rápidas</span>
                <Notas/>
            </el-tab-pane>
            <el-tab-pane class="mb-3"  name="five">
              <span slot="label">Configuración adicional</span>
              <div class="col-md-12">
                <div class="row mt-3">
                  <div class="col-md-12">
                    <h5><b>Habilitar botones en Comanda:</b></h5>
                    <span>Según el flujo de su restaurante, usted puede habilitar solo la impresión de comandas o enviar a directo cocina (mostrar en pantalla) sin la necesidad de imprimirlo, o también puede hacer ambos.</span>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Solo impresora
                    </label>
                    <div :class="{'has-danger': errors.enabled_print_command}"
                          class="form-group">
                      <el-switch v-model="form.enabled_print_command"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_print_command"
                              class="form-control-feedback"
                              v-text="errors.enabled_print_command[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Imprimir comandas por grupo
                    </label>
                    <div :class="{'has-danger': errors.enabled_print_group_commands}"
                          class="form-group">
                      <el-switch v-model="form.enabled_print_group_commands"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_print_group_commands"
                              class="form-control-feedback"
                              v-text="errors.enabled_print_group_commands[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Solo pantalla en cocina (Comanda en pantalla)
                    </label>
                    <div :class="{'has-danger': errors.enabled_send_command}"
                          class="form-group">
                      <el-switch v-model="form.enabled_send_command"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_send_command"
                              class="form-control-feedback"
                              v-text="errors.enabled_send_command[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Tengo impresora y pantalla en cocina
                    </label>
                    <div :class="{'has-danger': errors.enabled_printsend_command}"
                          class="form-group">
                      <el-switch v-model="form.enabled_printsend_command"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_printsend_command"
                              class="form-control-feedback"
                              v-text="errors.enabled_printsend_command[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Habilitar servicio central de impresión
                    </label>
                    <div :class="{'has-danger': errors.enabled_server_print}"
                          class="form-group">
                      <el-switch v-model="form.enabled_server_print"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_server_print"
                              class="form-control-feedback"
                              v-text="errors.enabled_server_print[0]"></small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row mt-3">
                  <div class="col-md-12">
                    <h5><b>Permisos adicionales para mozos:</b></h5>
                    <span>Por defecto, los mozos solo tienen acceso únicamente a las mesas para tomar pedidos e imprimir la pre-cuenta. Desde aquí, usted puede habilitar el acceso a comanda y a POS.</span>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Habilitar comanda
                    </label>
                    <div :class="{'has-danger': errors.enabled_command_waiter}"
                          class="form-group">
                      <el-switch v-model="form.enabled_command_waiter"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_command_waiter"
                              class="form-control-feedback"
                              v-text="errors.enabled_command_waiter[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Habilitar POS
                    </label>
                    <div :class="{'has-danger': errors.enabled_pos_waiter}"
                          class="form-group">
                      <el-switch v-model="form.enabled_pos_waiter"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_pos_waiter"
                              class="form-control-feedback"
                              v-text="errors.enabled_pos_waiter[0]"></small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Habilitar cerrar mesa
                    </label>
                    <div :class="{'has-danger': errors.enabled_close_table_mozo}"
                          class="form-group">
                      <el-switch v-model="form.enabled_close_table_mozo"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_close_table_mozo"
                              class="form-control-feedback"
                              v-text="errors.enabled_close_table_mozo[0]"></small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row mt-3">
                  <div class="col-md-12">
                    <h5><b>Permisos adicionales para cajero:</b></h5>
                    <span>Por defecto, el cajero tiene habilitado el cerrar mesas, pero puede deshabilitarlo.</span>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Habilitar cerrar mesa
                    </label>
                    <div :class="{'has-danger': errors.enabled_close_table}"
                          class="form-group">
                      <el-switch v-model="form.enabled_close_table"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.enabled_close_table"
                              class="form-control-feedback"
                              v-text="errors.enabled_close_table[0]"></small>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row mt-3">
                  <div class="col-md-12">
                    <h5><b>Otros permisos generales:</b></h5>
                    <span></span>
                  </div>
                  <div class="col-md-4">
                    <label class="control-label">
                      Imprimir el comprobante del facturador en lugar de la plantilla de mozo
                    </label>
                    <div :class="{'has-danger': errors.replace_template_mozo}"
                          class="form-group">
                      <el-switch v-model="form.replace_template_mozo"
                                  active-text="Si"
                                  inactive-text="No"
                                  @change="submit"></el-switch>
                      <small v-if="errors.replace_template_mozo"
                              class="form-control-feedback"
                              v-text="errors.replace_template_mozo[0]"></small>
                    </div>
                  </div>
                </div>
              </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="seven">
              <span slot="label">Impresión</span>

              <div class="row mb-3">
                <div class="col-md-12">
                  <h5><b>Asignación de impresoras:</b></h5>
                  <span class="text-muted">Selecciona qué impresora se usará para cada tipo de salida.</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      Impresora - Comanda
                      <el-tooltip
                        content="Impresora donde se imprimirán las comandas enviadas a los mozos"
                        effect="dark"
                        placement="top">
                        <i class="fa fa-info-circle text-muted ml-1"></i>
                      </el-tooltip>
                    </label>
                    <el-select
                      v-model="form_assignment.printer_name_comanda"
                      clearable
                      placeholder="Seleccionar impresora"
                      class="w-100"
                      @change="saveAssignment">
                      <el-option
                        v-for="p in registered_printers"
                        :key="p.name"
                        :label="p.name + (p.is_default ? ' (predeterminada)' : '')"
                        :value="p.name">
                      </el-option>
                    </el-select>
                    <p style="text-align: justify; font-size: 0.5em;" class="text-muted mt-2">
                      Si el producto no tiene un área de preparación asignada, se imprimirá automáticamente en la impresora seleccionada.
                    </p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      Impresora - Documents
                      <el-tooltip
                        content="Impresora asignada para impresión de documentos"
                        effect="dark"
                        placement="top">
                        <i class="fa fa-info-circle text-muted ml-1"></i>
                      </el-tooltip>
                    </label>
                    <el-select
                      v-model="form_assignment.printer_name_documents"
                      clearable
                      placeholder="Seleccionar impresora"
                      class="w-100"
                      @change="saveAssignment">
                      <el-option
                        v-for="p in registered_printers"
                        :key="p.name"
                        :label="p.name + (p.is_default ? ' (predeterminada)' : '')"
                        :value="p.name">
                      </el-option>
                    </el-select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label">
                      Impresora - Precuenta
                      <el-tooltip
                        content="Impresora donde se imprimirá la pre-cuenta para el cliente"
                        effect="dark"
                        placement="top">
                        <i class="fa fa-info-circle text-muted ml-1"></i>
                      </el-tooltip>
                    </label>
                    <el-select
                      v-model="form_assignment.printer_name_precuenta"
                      clearable
                      placeholder="Seleccionar impresora"
                      class="w-100"
                      @change="saveAssignment">
                      <el-option
                        v-for="p in registered_printers"
                        :key="p.name"
                        :label="p.name + (p.is_default ? ' (predeterminada)' : '')"
                        :value="p.name">
                      </el-option>
                    </el-select>
                  </div>
                </div>
              </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="six">
              <span slot="label">Áreas de preparación</span>

              <div class="row mt-3">
                <div class="col-md-12">
                  <h5><b>Áreas de preparación:</b></h5>
                  <span class="text-muted">
                    Por defecto, todas las áreas utilizarán la impresora configurada en “Impresora - Comanda”.<br>
                    Si deseas asignar una impresora diferente para cada área, activa la opción a continuación.
                  </span>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-12 d-flex align-items-center">
                  <el-switch
                    v-model="form_assignment.printer_per_area_enabled"
                    active-text="Permitir una impresora distinta por área"
                    @change="savePrinterPerArea">
                  </el-switch>
                  <el-tooltip
                    content="Cuando está activo, podrás escoger una impresora específica para cada área. Si está inactivo, todas las áreas comparten la impresora de comanda."
                    effect="dark"
                    placement="top">
                    <i class="fa fa-info-circle text-muted ml-2"></i>
                  </el-tooltip>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-md-4">
                  <label class="control-label">
                    Impresora
                    <el-tooltip
                      v-if="!form_assignment.printer_per_area_enabled"
                      content="Se usa la misma impresora seleccionada en 'Impresora - Comanda'. Activa el toggle de arriba para escoger una distinta."
                      effect="dark"
                      placement="top">
                      <i class="fa fa-info-circle text-muted ml-1"></i>
                    </el-tooltip>
                  </label>
                  <el-select v-model="areaPrinterModel"
                    :placeholder="form_assignment.printer_per_area_enabled ? 'Seleccionar impresora' : 'Selecciona primero \'Impresora - Comanda\''"
                    :disabled="!form_assignment.printer_per_area_enabled"
                    filterable
                    style="width: 100%;">
                    <el-option
                      v-for="p in registered_printers"
                      :key="p.name"
                      :label="p.name + (p.is_default ? ' (predeterminada)' : '')"
                      :value="p.name">
                    </el-option>
                  </el-select>
                </div>
                <div class="col-md-4">
                  <label class="control-label">Nombre del área</label>
                  <el-input
                    v-model="form_preparation_area.name"
                    placeholder="Ej: Cocina, Bar, Parrilla"></el-input>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <el-button type="primary" class="mx-1" @click="savePreparationArea" :disabled="!canSaveArea">
                    {{ form_preparation_area.id ? 'Actualizar' : 'Guardar' }}
                  </el-button>
                  <el-button v-if="form_preparation_area.id" @click="cancelEditArea">
                    Cancelar
                  </el-button>
                </div>
              </div>

              <div class="row mt-4" v-if="preparation_areas.length > 0">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Nombre de Área</th>
                          <th>Impresora</th>
                          <th class="text-end">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="area in preparation_areas" :key="area.id">
                          <td>{{ area.name }}</td>
                          <td>{{ area.printer }}</td>
                          <td class="text-end">
                            <button type="button" class="btn btn-xs btn-info btn-shad" title="Editar" @click="editArea(area)">
                              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger btn-shad ms-1" title="Eliminar" @click="deleteArea(area.id)">
                              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </el-tab-pane>
          </el-tabs>
        </form>
      </template>


      <users-form :showDialog.sync="showDialog"
        :recordId="recordId"></users-form>
    </div>
</template>

<style>
.el-tabs__header,
.el-tabs__nav-wrap {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px ;
}

.el-tag {
  margin-left: 2px;
}
</style>

<script>
import { io } from 'socket.io-client'
import {deletable} from '@mixins/deletable'
import { buhoprinter } from '@mixins/buhoprinter'
import Notas from '../notes/index.vue'
import UsersForm from './partials/form.vue'
import Environments from './partials/environments.vue'
// import qz from 'qz-tray'

const url = 'https://milanmario.com'
const SOCKET = io(url, {
  reconnectionDelayMax: 100,
  transports: ['polling'],
  autoConnect: false,
})

// connect()

//  function connect(username = 'usuario') {
//     SOCKET.auth = { username }
//     SOCKET.connect()
//  }

export default {
    mixins: [deletable, buhoprinter],
    components: {Notas,UsersForm,Environments},
    data() {
      return {
        resource: 'restaurant',
        showDialog: false,
        recordId: null,
        errors: {},
        form: {
          menu_pos: true,
          menu_order: true,
          menu_tables: true,
          menu_bar: true,
          menu_kitchen: true,
          first_menu: 'POS',
          tables_quantity: 15
        },
        activeName: 'first',
        menu_list: [
          {id: 1, description: 'POS', name: 'POS'},
          {id: 2, description: 'Mesas', name: 'TABLES'},
          {id: 3, description: 'Pedidos', name: 'ORDER'},
        ],
        users: {},
        roles: {},
        form_role: {
          user_id: '',
          role_id: ''
        },
        form_waiter: {
          name: null,
          last_name: null,
          id: null
        },
        waiters: [],
        info : {
          ruc:null,
          userEmail: null,
          socketServer: null
        },
        socket: null,
        permission_adm: ['Todos los menús'],
        permission_caja: ['POS', 'Mesas', 'Pedidos'],
        permission_mozo: ['Mesas'],
        permission_kitbar: ['Comandas'],
        form_delete_role: {
          user_id: '',
        },
        environment_1 :{name: 'Ambiente 1',original_name:'Ambiente 1',enabled_edit: false},
        environment_2 :{name: 'Ambiente 2',original_name:'Ambiente 2',enabled_edit: false},
        environment_3 :{name: 'Ambiente 3',original_name:'Ambiente 3',enabled_edit: false},
        environment_4 :{name: 'Ambiente 4',original_name:'Ambiente 4',enabled_edit: false},
        printers: ['No asignada'],
        registered_printers: [],
        form_assignment: {
          printer_name_comanda:     null,
          printer_name_documents:   null,
          printer_name_precuenta:   null,
          printer_per_area_enabled: false,
        },
        preparation_areas: [],
        form_preparation_area: {
          id: null,
          name: '',
          printer: '',
        },
        qzConnected: false
      }
    },
    computed: {
      isFormRole: function () {
        return (this.form_role.user_id != '' && this.form_role.role_id != '') ? false : true
      },
      canSaveArea() {
        const effectivePrinter = this.form_assignment.printer_per_area_enabled
          ? this.form_preparation_area.printer
          : this.form_assignment.printer_name_comanda;
        return this.form_preparation_area.name.trim() !== '' &&
               !!(effectivePrinter || '').trim();
      },
      /**
       * Modelo del select Impresora del form de áreas.
       * Cuando el toggle "Permitir una impresora distinta por área" está OFF,
       * el select se sincroniza con la impresora de Comanda y no es editable.
       * Cuando está ON, el usuario puede escoger una impresora distinta por área.
       */
      areaPrinterModel: {
        get() {
          return this.form_assignment.printer_per_area_enabled
            ? this.form_preparation_area.printer
            : this.form_assignment.printer_name_comanda;
        },
        set(value) {
          if (this.form_assignment.printer_per_area_enabled) {
            this.form_preparation_area.printer = value;
          }
        }
      }
    },
    created() {
      this.$eventHub.$on('reloadData', () => {
          this.getUsers()
      })
      this.getRecords();
      this.getUsers();
      this.getWaiters();
      this.getEnvs();
      this.getPreparationAreas();
      this.loadPrinterAssignment();
    },
    mounted() {

    },
    methods: {
      async getRecords() {
        await this.$http.get(`/${this.resource}/configuration/record`).then(response => {
          if (response.data !== '') {
            this.form = response.data.data;
            const infoData = response.data.info
            this.info.ruc = infoData.ruc
            this.info.userEmail = infoData.userEmail
            this.info.socketServer = infoData.socketServer
            this.currentUserType = infoData.userType
          }
        });
        this.$http.get(`/${this.resource}/get-roles`).then(response => {
          if (response.data !== '') {
            this.roles = response.data.data;
          }
        });

        this.sendCompany()
      },
      getUsers() {
        this.$http.get(`/${this.resource}/get-users`).then(response => {
          if (response.data !== '') {
            this.users = response.data.data;
          }
        });
      },
      getWaiters() {
        this.$http.get(`/${this.resource}/waiter`).then(response => {
          this.waiters = response.data.data
        });
      },
      getEnvs() {
        this.$http.get(`/${this.resource}/configuration/get-envs`).then(response => {
          let data = response.data;
          if (data.success) {
            this.environment_1 = data.data.environment_1;
            this.environment_2 = data.data.environment_2;
            this.environment_3 = data.data.environment_3;
            this.environment_4 = data.data.environment_4;
          }
        });
      },
      submit() {
        console.log(this.form);
        this.$http.post(`/${this.resource}/configuration`, this.form).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
            this.resetTablensAndEnvClients()
          } else {
            this.$message.error(data.message);
          }
          if (data !== undefined && data.configuration !== undefined) {
            this.form = data.configuration
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        })
      },
      sendFormRole() {
        this.$http.post(`/${this.resource}/user/set-role`, this.form_role).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {
          this.getUsers();
          const role = this.roles.find(x => x.id == this.form_role.role_id)
          const user = this.users.find(x => x.id == this.form_role.user_id)
          this.sendUserUpdate(user.email, role.code )
          this.form_role.user_id = '';
          this.form_role.role_id = '';
          // this.loading_submit = false;
        });
      },
      initFormWaiter() {
        this.form_waiter = {
          name: null,
          last_name: null,
          id: null
        }
      },
      sendFormWaiter() {
        this.$http.post(`/${this.resource}/waiter`, this.form_waiter).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          this.getWaiters()
          this.initFormWaiter()

        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {

        });
      },
      clickDeleteWaiter(id) {
        this.destroy(`/${this.resource}/waiter/${id}`).then(() =>
          this.getWaiters()
        )
      },
      resetTablensAndEnvClients() {
        SOCKET.emit('reset-table-envs')
      },
      sendUserUpdate(userEmail, roleCode) {
        const data = {
          user_email: userEmail,
          role_code: roleCode,
        }
        SOCKET.emit('data-user-profile', data)
      },
      sendCompany() {
        const data = {
          ruc: this.info.ruc,
          user: this.info.userEmail
        }
        SOCKET.emit('data-company', data)
      },
      sendDeleteRole(user_id) {
        this.form_delete_role.user_id = user_id
        this.$http.post(`/${this.resource}/user/delete-role`, this.form_delete_role).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {
          this.getUsers();
          this.form_delete_role.user_id = '';
        });
      },
      clickCreateUser(recordId = null) {
        this.recordId = recordId
        this.showDialog = true
      },
      clickEditEnvironment(index) {
        const environment = this[`environment_${index}`];
        const form_update = {
          id: index,
          name: environment.name,
        };

        this.$http.post(`/${this.resource}/configuration/update-envs`, form_update)
          .then(response => {
            const { data } = response;
            if (data.success) {
              environment.original_name = environment.name;
              environment.enabled_edit = false;
              this.$message.success(data.message);
            } else {
              this.handleUpdateError(environment, data.message);
            }
          })
          .catch(error => this.handleUpdateCatch(environment, error))
          .finally(() => {
            environment.enabled_edit = false;
          });
      },
      handleUpdateError(environment, message) {
        this.$message.error(message);
        environment.name = environment.original_name;
      },
      handleUpdateCatch(environment, error) {
        environment.name = environment.original_name;
        environment.enabled_edit = false;

        if (error.response.status === 422) {
          this.errors = error.response.data.errors;
        } else {
          console.error(error);
        }
      },
      clickCancelEnvironment(index) {
        this[`environment_${index}`].enabled_edit = false;
        this[`environment_${index}`].name = this[`environment_${index}`].original_name;
      },

      onTabClick(tab) {
        if (tab.name === 'six' && this.printers.length <= 1) {
          this.loadPrintersFromDB()
        }
      },
      async loadPrintersFromDB() {
        try {
          const { data } = await this.$http.get(`/${this.resource}/printers/`)
          if (data.success) {
            this.registered_printers = data.data
            this.printers = ['No asignada', ...data.data.map(p => p.name)]
          }
        } catch (err) {
          console.error('Error al cargar impresoras desde BD:', err)
          this.printers = ['No asignada']
          this.registered_printers = []
        }
      },
      async loadPrinterAssignment() {
        try {
          const { data } = await this.$http.get(`/${this.resource}/printers/config`)
          if (data.success) {
            this.form_assignment.printer_name_comanda     = data.data.printer_name_comanda
            this.form_assignment.printer_name_documents   = data.data.printer_name_documents
            this.form_assignment.printer_name_precuenta   = data.data.printer_name_precuenta
            this.form_assignment.printer_per_area_enabled = data.data.printer_per_area_enabled || false
            this.registered_printers = data.data.printers || []
          }
        } catch (err) {
          console.error('Error al cargar configuración de impresión:', err)
        }
      },
      async saveAssignment() {
        try {
          const res = await this.$http.post(`/${this.resource}/printers/config`, {
            printer_name_comanda:   this.form_assignment.printer_name_comanda,
            printer_name_documents: this.form_assignment.printer_name_documents,
            printer_name_precuenta: this.form_assignment.printer_name_precuenta,
          })
          if (res.data.success) {
            this.$message.success('Asignación de impresoras actualizada.')
          }
        } catch (err) {
          this.$message.error('Error al guardar la asignación de impresoras.')
          console.error(err)
        }
      },
      async savePrinterPerArea() {
        try {
          const res = await this.$http.post(`/${this.resource}/printers/config`, {
            printer_per_area_enabled: this.form_assignment.printer_per_area_enabled,
          })
          if (res.data.success) {
            this.$message.success('Configuración actualizada.')
          }
        } catch (err) {
          this.$message.error('Error al guardar la configuración.')
          console.error(err)
        }
      },
      async getPreparationAreas() {
        try {
          const response = await this.$http.get(`/${this.resource}/preparation-areas`);
          if (response.data.success) {
            this.preparation_areas = response.data.data;
          }
        } catch (error) {
          console.error('Error al cargar áreas:', error);
        }
      },
      async savePreparationArea() {
        try {
          const url = this.form_preparation_area.id
            ? `/${this.resource}/preparation-areas/${this.form_preparation_area.id}`
            : `/${this.resource}/preparation-areas`;

          const method = this.form_preparation_area.id ? 'put' : 'post';

          const effectivePrinter = this.form_assignment.printer_per_area_enabled
            ? this.form_preparation_area.printer
            : this.form_assignment.printer_name_comanda;

          const response = await this.$http[method](url, {
            name: this.form_preparation_area.name,
            printer: effectivePrinter,
          });

          if (response.data.success) {
            this.$message.success(response.data.message);
            this.getPreparationAreas();
            this.resetPreparationAreaForm();
          }
        } catch (error) {
          this.$message.error('Error al guardar área de preparación');
          console.error(error);
        }
      },
      editArea(area) {
        this.form_preparation_area = {
          id: area.id,
          name: area.name,
          printer: area.printer,
        };
      },
      cancelEditArea() {
        this.resetPreparationAreaForm();
      },
      async deleteArea(id) {
        try {
          await this.$confirm('¿Está seguro de eliminar esta área de preparación?', 'Confirmar', {
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            type: 'warning'
          });

          const response = await this.$http.delete(`/${this.resource}/preparation-areas/${id}`);

          if (response.data.success) {
            this.$message.success(response.data.message);
            this.getPreparationAreas();
          }
        } catch (error) {
          if (error !== 'cancel') {
            this.$message.error('Error al eliminar área');
            console.error(error);
          }
        }
      },
      resetPreparationAreaForm() {
        this.form_preparation_area = {
          id: null,
          name: '',
          printer: '',
        };
      }
    }
}
</script>
