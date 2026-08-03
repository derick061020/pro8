<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span> Modo offline </span></li>
            </ol>
        </div>

        <!-- Estado de la conexión -->
        <div class="card tab-content-default row-new mb-3" v-if="status && status.is_client">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <h4 class="my-0">
                            <span class="badge" :class="status.online ? 'badge-success' : 'badge-danger'">
                                {{ status.online ? 'EN LÍNEA' : 'SIN CONEXIÓN' }}
                            </span>
                            <span class="ml-2">{{ status.terminal_name || status.terminal_code || 'Terminal sin nombre' }}</span>
                        </h4>
                        <small class="text-muted" v-if="status.server">Servidor: {{ status.server }}</small>
                        <small class="text-muted d-block" v-if="!status.paired">
                            Este terminal todavía no está pareado con el servidor.
                        </small>
                    </div>
                    <div>
                        <el-button type="primary" icon="el-icon-refresh" :loading="loading_sync"
                                   :disabled="!status.paired" @click.prevent="syncNow">
                            Sincronizar ahora
                        </el-button>
                    </div>
                </div>

                <div class="row mt-4 text-center">
                    <div class="col-md-3 col-6">
                        <h3 class="my-0" :class="{'text-warning': status.pending > 0}">{{ status.pending }}</h3>
                        <small class="text-muted">Cambios en cola</small>
                    </div>
                    <div class="col-md-3 col-6">
                        <h3 class="my-0" :class="{'text-warning': status.pending_documents > 0}">{{ status.pending_documents }}</h3>
                        <small class="text-muted">Comprobantes sin subir</small>
                    </div>
                    <div class="col-md-3 col-6">
                        <h3 class="my-0" :class="{'text-danger': status.stuck > 0}">{{ status.stuck }}</h3>
                        <small class="text-muted">Trabados</small>
                    </div>
                    <div class="col-md-3 col-6">
                        <h3 class="my-0">{{ status.last_push_at || '—' }}</h3>
                        <small class="text-muted">Última subida</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Numeración reservada -->
        <div class="card tab-content-default row-new mb-3" v-if="status && status.is_client && status.paired">
            <div class="card-body">
                <h5>Numeración reservada</h5>
                <p class="text-muted small">
                    Sin internet los comprobantes se numeran con estos bloques que reservó el servidor.
                    Si un bloque se agota estando desconectado, no se podrá emitir hasta recuperar la conexión.
                </p>

                <el-alert v-if="!status.ranges || !status.ranges.length" type="warning" :closable="false"
                          title="Este terminal no tiene numeración reservada: no podrá emitir comprobantes sin internet."
                          class="mb-3">
                </el-alert>

                <el-table :data="status.ranges" size="mini" v-if="status.ranges && status.ranges.length">
                    <el-table-column prop="series" label="Serie" width="100"></el-table-column>
                    <el-table-column label="Bloque">
                        <template slot-scope="scope">{{ scope.row.from_number }} — {{ scope.row.to_number }}</template>
                    </el-table-column>
                    <el-table-column label="Último usado">
                        <template slot-scope="scope">{{ scope.row.current_number || '—' }}</template>
                    </el-table-column>
                    <el-table-column label="Quedan" width="120">
                        <template slot-scope="scope">
                            <span :class="scope.row.warning ? 'text-danger font-weight-bold' : ''">
                                {{ scope.row.remaining }}
                            </span>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <el-input v-model="range_form.series" placeholder="Serie (ej. B001)" size="small"></el-input>
                    </div>
                    <div class="col-md-3">
                        <el-input-number v-model="range_form.size" :min="1" :max="10000" size="small"></el-input-number>
                    </div>
                    <div class="col-md-5">
                        <el-button size="small" :loading="loading_range" :disabled="!status.online"
                                   @click.prevent="allocateRange">
                            Reservar más números
                        </el-button>
                        <small class="text-muted d-block" v-if="!status.online">
                            Necesita conexión con el servidor.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cambios trabados -->
        <div class="card tab-content-default row-new mb-3"
             v-if="status && status.conflicts && status.conflicts.length">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="my-0">Cambios que no pudieron subirse</h5>
                    <el-button size="mini" :loading="loading_retry" @click.prevent="retryStuck">Reintentar todos</el-button>
                </div>
                <p class="text-muted small mt-2">
                    El servidor los rechazó o se agotaron los reintentos. Revisá el motivo antes de reintentar.
                </p>
                <el-table :data="status.conflicts" size="mini">
                    <el-table-column prop="date" label="Fecha" width="140"></el-table-column>
                    <el-table-column prop="entity" label="Tipo" width="120"></el-table-column>
                    <el-table-column prop="entity_id" label="N°" width="80"></el-table-column>
                    <el-table-column prop="error" label="Motivo"></el-table-column>
                </el-table>
            </div>
        </div>

        <!-- Terminales (modo servidor) -->
        <div class="card tab-content-default row-new mb-3" v-if="status && !status.is_client">
            <div class="card-body">
                <h5>Terminales offline conectados</h5>
                <p class="text-muted small">
                    Esta instalación funciona como servidor. Acá aparecen las PC que sincronizan contra ella.
                </p>
                <el-table :data="status.terminals" size="mini" v-if="status.terminals && status.terminals.length">
                    <el-table-column prop="code" label="Código" width="100"></el-table-column>
                    <el-table-column prop="name" label="Nombre"></el-table-column>
                    <el-table-column prop="app_version" label="Versión" width="110"></el-table-column>
                    <el-table-column label="Último contacto">
                        <template slot-scope="scope">
                            <span :class="scope.row.minutes_ago > 30 ? 'text-danger' : 'text-success'">
                                {{ scope.row.last_seen_at || 'nunca' }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column label="Estado" width="110">
                        <template slot-scope="scope">
                            <span :class="scope.row.active ? 'text-success' : 'text-muted'">
                                {{ scope.row.active ? 'Activo' : 'Desactivado' }}
                            </span>
                        </template>
                    </el-table-column>
                </el-table>
                <p class="text-muted small mb-0" v-else>Todavía no se pareó ningún terminal.</p>
            </div>
        </div>

        <!-- Configuración -->
        <div class="card tab-content-default row-new">
            <div class="card-body">
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label">Activar modo offline (esta PC es un terminal)</label>
                                <div class="form-group" :class="{'has-danger': errors.is_client}">
                                    <el-switch v-model="form.is_client" active-text="Si" inactive-text="No"></el-switch>
                                    <small class="form-control-feedback" v-if="errors.is_client" v-text="errors.is_client[0]"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2" v-if="form.is_client">
                            <div class="col-md-6">
                                <label class="control-label">URL Servidor</label>
                                <div class="form-group" :class="{'has-danger': errors.url_server}">
                                    <el-input v-model="form.url_server" placeholder="https://mi-empresa.com"></el-input>
                                    <small class="form-control-feedback" v-if="errors.url_server" v-text="errors.url_server[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Token Servidor</label>
                                <div class="form-group" :class="{'has-danger': errors.token_server}">
                                    <el-input v-model="form.token_server" show-password></el-input>
                                    <small class="form-control-feedback" v-if="errors.token_server" v-text="errors.token_server[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="control-label">Código del terminal</label>
                                <div class="form-group" :class="{'has-danger': errors.terminal_code}">
                                    <el-input v-model="form.terminal_code" placeholder="T01" maxlength="20"></el-input>
                                    <small class="form-text text-muted">Único por PC. Identifica el origen de cada venta.</small>
                                    <small class="form-control-feedback" v-if="errors.terminal_code" v-text="errors.terminal_code[0]"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Nombre del terminal</label>
                                <div class="form-group">
                                    <el-input v-model="form.terminal_name" placeholder="Caja principal"></el-input>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Sincronizar cada (segundos)</label>
                                <div class="form-group" :class="{'has-danger': errors.sync_interval}">
                                    <el-input-number v-model="form.sync_interval" :min="15" :max="3600"></el-input-number>
                                    <small class="form-control-feedback" v-if="errors.sync_interval" v-text="errors.sync_interval[0]"></small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="control-label">Sincronización automática</label>
                                <div class="form-group">
                                    <el-switch v-model="form.sync_enabled" active-text="Si" inactive-text="No"></el-switch>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Rama de actualización</label>
                                <div class="form-group">
                                    <el-input v-model="form.git_branch" placeholder="deploy/offline"></el-input>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions text-right pt-2">
                            <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bitácora -->
        <div class="card tab-content-default row-new mt-3" v-if="status && status.logs && status.logs.length">
            <div class="card-body">
                <h5>Últimas sincronizaciones</h5>
                <el-table :data="status.logs" size="mini">
                    <el-table-column prop="date" label="Fecha" width="160"></el-table-column>
                    <el-table-column prop="direction" label="Acción" width="100"></el-table-column>
                    <el-table-column prop="records" label="Registros" width="90"></el-table-column>
                    <el-table-column label="Resultado" width="100">
                        <template slot-scope="scope">
                            <span :class="scope.row.success ? 'text-success' : 'text-danger'">
                                {{ scope.row.success ? 'OK' : 'Error' }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="message" label="Detalle"></el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data() {
            return {
                loading_submit: false,
                loading_sync: false,
                loading_retry: false,
                loading_range: false,
                resource: 'offline-configurations',
                errors: {},
                form: {},
                status: null,
                status_timer: null,
                range_form: {
                    series: null,
                    size: 500,
                },
            }
        },
        async created() {
            await this.initForm();

            await this.$http.get(`/${this.resource}/record`).then(response => {
                if (response.data !== '') this.form = response.data.data;
            });

            await this.loadStatus();

            // El estado se refresca solo: en un terminal lo que importa es ver
            // en el momento si volvió la conexión y si la cola está bajando.
            this.status_timer = setInterval(this.loadStatus, 15000);
        },
        beforeDestroy() {
            if (this.status_timer) clearInterval(this.status_timer);
        },
        methods: {
            initForm() {
                this.errors = {};

                this.form = {
                    is_client: false,
                    token_server: null,
                    url_server: null,
                    terminal_code: null,
                    terminal_name: null,
                    sync_enabled: true,
                    sync_interval: 60,
                    git_branch: null,
                };
            },
            async loadStatus() {
                await this.$http.get(`/${this.resource}/status`).then(response => {
                    if (response.data.success) this.status = response.data.data;
                }).catch(() => {
                    // Si el propio panel no responde no tiene sentido avisar en
                    // cada ciclo: el reintento llega solo.
                });
            },
            submit() {
                this.loading_submit = true;

                this.$http.post(`/${this.resource}`, this.form).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.errors = {};
                        this.loadStatus();
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || error.response.data;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            syncNow() {
                this.loading_sync = true;

                this.$http.post(`/${this.resource}/sync`).then(response => {
                    response.data.success
                        ? this.$message.success(response.data.message)
                        : this.$message.warning(response.data.message);

                    this.loadStatus();
                }).catch(error => {
                    this.$message.error('No se pudo sincronizar.');
                    console.log(error);
                }).then(() => {
                    this.loading_sync = false;
                });
            },
            retryStuck() {
                this.loading_retry = true;

                this.$http.post(`/${this.resource}/retry`).then(response => {
                    this.$message.success(response.data.message);
                    this.loadStatus();
                }).then(() => {
                    this.loading_retry = false;
                });
            },
            allocateRange() {
                if (!this.range_form.series) {
                    this.$message.warning('Indicá la serie.');
                    return;
                }

                this.loading_range = true;

                this.$http.post(`/${this.resource}/ranges/allocate`, this.range_form).then(response => {
                    response.data.success
                        ? this.$message.success(response.data.message)
                        : this.$message.error(response.data.message);

                    this.loadStatus();
                }).then(() => {
                    this.loading_range = false;
                });
            },
        }
    }
</script>
