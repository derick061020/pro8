<template>
    <div>
        <header class="page-header">
            <h2>
                <a href="/dashboard">
                    <i class="fas fa-user-shield"></i>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>Admin Reseller / Administradores</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button class="btn btn-custom btn-sm mt-2 me-2 mb-3 primary-buton" type="button" @click="openCreate">
                    <i class="fa fa-plus-circle"></i> Nuevo Administrador
                </button>
            </div>
        </header>

        <div class="card">
            <div class="card-body mx-2">
                <div class="btn-filter-content mb-3 d-flex">
                    <el-button type="secondary" class="btn-show-filter" :class="{ shift: isFiltersVisible }" @click="toggleFilters">
                        {{ isFiltersVisible ? 'Ocultar filtros' : 'Mostrar filtros' }}
                    </el-button>
                    <el-button v-if="searchQuery" type="secondary" @click="clearFilters">Limpiar Filtros</el-button>
                </div>

                <div v-if="isFiltersVisible" class="filter-section mb-3">
                    <div class="row">
                        <div class="form-group col-lg-4 col-md-6 col-sm-12 mb-2">
                            <label class="control-label mb-1">Buscar:</label>
                            <el-input v-model="searchQuery" placeholder="Nombre o correo" prefix-icon="el-icon-search"></el-input>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th class="text-center">Módulos</th>
                                <th class="text-center">Empresas</th>
                                <th class="text-center">Crear Cliente</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in filteredRecords" :key="row.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ row.name }}</td>
                                <td>{{ row.email }}</td>
                                <td class="text-center">
                                    <el-tag
                                        v-if="isCurrentUser(row)"
                                        type="info"
                                        size="small"
                                        class="admin-reseller-master-tag">
                                        MASTER
                                    </el-tag>
                                    <span v-else class="text-muted small">{{ moduleCountLabel(row) }}</span>
                                </td>
                                <td class="text-center">
                                    <el-tag
                                        v-if="isCurrentUser(row)"
                                        type="info"
                                        size="small"
                                        class="admin-reseller-master-tag">
                                        TODAS
                                    </el-tag>
                                    <span v-else class="text-muted small">{{ companiesCountLabel(row) }}</span>
                                </td>
                                <td class="text-center">
                                    <el-switch
                                        v-model="row.can_create_clients"
                                        @change="changeCreateClientPermission(row)">
                                    </el-switch>
                                </td>
                                <td class="text-center">
                                    <el-switch v-model="row.status" @change="changeStatus(row)"></el-switch>
                                </td>
                                <td class="text-end align-middle">
                                    <el-button 
                                        v-if="!isCurrentUser(row)"
                                        type="primary" 
                                        size="small" 
                                        class="me-1 mb-0" 
                                        @click="openEdit(row)">
                                        Editar
                                    </el-button>
                                    <el-button
                                        v-if="!isCurrentUser(row)"
                                        type="primary"
                                        size="small"
                                        class="mb-0"
                                        @click="remove(row)">
                                        Eliminar
                                    </el-button>
                                </td>
                            </tr>
                            <tr v-if="filteredRecords.length === 0">
                                <td colspan="8" class="text-center text-muted py-4">No hay administradores registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <el-dialog
            :title="form.id ? 'Editar Administrador' : 'Nuevo Administrador'"
            :visible.sync="showDialog"
            width="92%"
            top="5vh"
            custom-class="dialog-administrator-form"
            append-to-body
            @closed="handleDialogClosed">
            <el-tabs v-model="activeTab" type="card" class="administrator-form-tabs">
                <el-tab-pane label="General" name="general">
                    <div class="row pt-2 px-1">
                        <div class="col-md-6">
                            <div class="form-group mb-3" :class="{ 'has-danger': errors.name }">
                                <label class="control-label">Nombre</label>
                                <el-input v-model="form.name" placeholder="Nombre completo"></el-input>
                                <small class="form-control-feedback" v-if="errors.name">{{ errors.name[0] }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3" :class="{ 'has-danger': errors.email }">
                                <label class="control-label">Email</label>
                                <el-input v-model="form.email" placeholder="correo@empresa.com"></el-input>
                                <small class="form-control-feedback" v-if="errors.email">{{ errors.email[0] }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3" :class="{ 'has-danger': errors.password }">
                                <label class="control-label">{{ form.id ? 'Password (opcional)' : 'Password' }}</label>
                                <el-input v-model="form.password" show-password placeholder="En edición, dejar vacío para no cambiar"></el-input>
                                <small class="form-control-feedback" v-if="errors.password">{{ errors.password[0] }}</small>
                            </div>
                            <div
                                v-if="!form.id || form.password"
                                class="form-group mb-3"
                                :class="{ 'has-danger': errors.password_confirmation || passwordMismatch }">
                                <label class="control-label">{{ form.id ? 'Confirmar nueva contraseña' : 'Confirmar contraseña' }}</label>
                                <el-input
                                    v-model="form.password_confirmation"
                                    show-password
                                    :placeholder="form.id ? 'Repita la nueva contraseña' : 'Repita la contraseña'">
                                </el-input>
                                <small class="form-control-feedback text-danger" v-if="errors.password_confirmation">
                                    {{ errors.password_confirmation[0] }}
                                </small>
                                <small class="form-control-feedback text-danger" v-else-if="passwordMismatch">
                                    Las contraseñas no coinciden
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="control-label invisible" aria-hidden="true">
                                    Permitir crear nuevos clientes
                                </label>
                                <div
                                    class="admin-can-create-clients-box d-flex justify-content-between align-items-center flex-nowrap w-100"
                                    role="group"
                                    aria-label="Permitir crear nuevos clientes">
                                    <span class="admin-can-create-clients-box-label text-truncate me-2">
                                        Permitir crear nuevos clientes
                                    </span>
                                    <el-switch
                                        v-model="form.can_create_clients"
                                        class="admin-can-create-clients-switch flex-shrink-0">
                                    </el-switch>
                                </div>
                                <small class="admin-can-create-clients-helper small d-block">
                                    Si está desactivado, no verá el botón para registrar clientes.
                                </small>
                            </div>
                        </div>
                    </div>
                </el-tab-pane>

                <el-tab-pane label="Módulos del sistema" name="modules">
                    <p class="text-muted small mb-2 px-1">Marque los módulos del panel a los que podrá acceder este administrador.</p>
                    <div :class="{ 'has-danger': errors.module_permissions }">
                        <el-checkbox-group v-model="form.module_permissions" class="row">
                            <div
                                v-for="opt in moduleOptions"
                                :key="opt.key"
                                class="col-md-6 center-el-checkbox mb-2">
                                <el-checkbox :label="opt.key">{{ opt.label }}</el-checkbox>
                            </div>
                        </el-checkbox-group>
                        <small class="form-control-feedback d-block" v-if="errors.module_permissions">{{ errors.module_permissions[0] }}</small>
                    </div>
                </el-tab-pane>

                <el-tab-pane label="Clientes asignados" name="clients">
                    <p class="text-muted small mb-2 px-1">
                        Seleccione las empresas (clientes) a las que podrá acceder. Se listan todas las registradas en el sistema.
                        Además, siempre podrá ver los clientes que él mismo registre.
                    </p>
                    <div class="form-group mb-0 px-1" :class="{ 'has-danger': errors.client_ids }">
                        <el-select
                            v-model="form.client_ids"
                            multiple
                            filterable
                            placeholder="Buscar clientes..."
                            style="width: 100%">
                            <el-option
                                v-for="c in assignableClients"
                                :key="c.id"
                                :label="`${c.number} — ${c.name}`"
                                :value="c.id">
                            </el-option>
                        </el-select>
                        <small class="form-control-feedback d-block" v-if="errors.client_ids">{{ errors.client_ids[0] }}</small>
                    </div>
                </el-tab-pane>
            </el-tabs>

            <span slot="footer" class="dialog-footer">
                <el-button @click="showDialog = false">Cancelar</el-button>
                <el-button type="primary" :loading="loadingSubmit" :disabled="isSaveDisabled" @click="submit">Guardar</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            resource: 'admin-reseller/administrators',
            records: [],
            showDialog: false,
            loadingSubmit: false,
            isFiltersVisible: true,
            searchQuery: '',
            errors: {},
            form: {},
            activeTab: 'general',
            moduleOptions: [],
            assignableClients: [],
            auth_user_id: null,
        };
    },
    computed: {
        filteredRecords() {
            if (!this.searchQuery) return this.records;
            const query = this.searchQuery.toLowerCase();
            return this.records.filter((row) =>
                (row.name || '').toLowerCase().includes(query) ||
                (row.email || '').toLowerCase().includes(query)
            );
        },
        /** Edición: el usuario escribió algo en password (truthy, p. ej. string no vacío). */
        hasPasswordChangeInEdit() {
            return !!(this.form.id && this.form.password);
        },
        showPasswordConfirmation() {
            return !this.form.id || !!this.form.password;
        },
        passwordMismatch() {
            if (!this.showPasswordConfirmation) return false;
            const p = this.form.password != null ? String(this.form.password) : '';
            const c = this.form.password_confirmation != null ? String(this.form.password_confirmation) : '';
            if (c === '') return false;
            return p !== c;
        },
        isSaveDisabled() {
            const c = this.form.password_confirmation != null ? String(this.form.password_confirmation) : '';
            const p = this.form.password != null ? String(this.form.password) : '';
            if (!this.form.id) {
                if (c === '') return true;
                if (p !== c) return true;
                return false;
            }
            if (!this.hasPasswordChangeInEdit) return false;
            if (c === '') return true;
            if (p !== c) return true;
            return false;
        },
    },
    watch: {
        'form.password'(val) {
            if (!this.form.id) return;
            if (val == null || val === '') {
                this.form.password_confirmation = '';
            }
        },
    },
    created() {
        this.initForm();
        this.getData();
    },
    methods: {
        initForm() {
            this.errors = {};
            this.activeTab = 'general';
            this.form = {
                id: null,
                name: null,
                email: null,
                password: '',
                password_confirmation: '',
                status: true,
                module_permissions: [],
                client_ids: [],
                can_create_clients: false,
            };
        },
        handleDialogClosed() {
            this.initForm();
        },
        isCurrentUser(row) {
            return !!(row && row.id === this.auth_user_id);
        },
        moduleCountLabel(row) {
            const n = (row.module_permissions || []).length;
            return `${n} módulo${n === 1 ? '' : 's'}`;
        },
        companiesCountLabel(row) {
            const n = Array.isArray(row.assigned_client_ids) ? row.assigned_client_ids.length : 0;
            return `${n} empresa${n === 1 ? '' : 's'}`;
        },
        toggleFilters() {
            this.isFiltersVisible = !this.isFiltersVisible;
        },
        clearFilters() {
            this.searchQuery = '';
        },
        normalizePermissions(item) {
            const p = item.module_permissions;
            if (Array.isArray(p)) {
                return [...p];
            }
            if (p && typeof p === 'object') {
                return Object.values(p);
            }
            return [];
        },
        buildModuleOptions(definitions) {
            if (!definitions || typeof definitions !== 'object') {
                return [];
            }
            return Object.keys(definitions).map((key) => ({
                key,
                label: definitions[key],
            }));
        },
        getData() {
            this.$http.get(`/${this.resource}/records`).then((response) => {
                if (response.data.module_definitions) {
                    this.moduleOptions = this.buildModuleOptions(response.data.module_definitions);
                }
                if (response.data.assignable_clients) {
                    this.assignableClients = response.data.assignable_clients;
                }
                if (response.data.auth_user_id) {
                    this.auth_user_id = response.data.auth_user_id;
                }
                this.records = (response.data.data || []).map((item) => ({
                    ...item,
                    status: !!item.status,
                    is_master: !!(item.is_master === true || item.is_master === 1),
                    module_permissions: this.normalizePermissions(item),
                    can_create_clients: !!item.can_create_clients,
                    assigned_client_ids: Array.isArray(item.assigned_client_ids) ? item.assigned_client_ids : [],
                }));
            });
        },
        openCreate() {
            this.initForm();
            this.showDialog = true;
        },
        openEdit(row) {
            this.initForm();
            this.form = {
                id: row.id,
                name: row.name,
                email: row.email,
                password: '',
                password_confirmation: '',
                status: !!row.status,
                module_permissions: this.normalizePermissions(row),
                client_ids: [...(row.assigned_client_ids || [])],
                can_create_clients: !!row.can_create_clients,
            };
            this.showDialog = true;
        },
        submit() {
            this.errors = {};
            const rawPassword = this.form.password;
            const hasPassword = rawPassword != null && rawPassword !== '';
            const needsMinLength = hasPassword;
            if (needsMinLength && String(rawPassword).length < 6) {
                this.errors = {
                    password: ['La contraseña debe tener al menos 6 caracteres.'],
                };
                return;
            }

            if (this.showPasswordConfirmation) {
                const p = rawPassword != null ? String(rawPassword) : '';
                const c = this.form.password_confirmation != null ? String(this.form.password_confirmation) : '';
                if (p !== c) {
                    this.errors = {
                        password_confirmation: ['Las contraseñas no coinciden'],
                    };
                    return;
                }
            }

            this.loadingSubmit = true;

            const payload = {
                name: this.form.name,
                email: this.form.email,
                password: this.form.password || undefined,
                status: this.form.status,
                module_permissions: this.form.module_permissions || [],
                client_ids: this.form.client_ids || [],
                can_create_clients: !!this.form.can_create_clients,
            };
            const sendConfirmation = !this.form.id || this.hasPasswordChangeInEdit;
            if (this.showPasswordConfirmation && sendConfirmation) {
                payload.password_confirmation = this.form.password_confirmation;
            }

            const request = this.form.id
                ? this.$http.put(`/${this.resource}/${this.form.id}`, payload)
                : this.$http.post(`/${this.resource}`, payload);

            request
                .then((response) => {
                    this.$message.success(response.data.message);
                    this.showDialog = false;
                    this.getData();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const d = error.response.data;
                        this.errors = (d && d.errors) ? d.errors : d || {};
                        return;
                    }
                    const d = error.response && error.response.data;
                    const msg =
                        (d && (d.message || (typeof d === 'string' ? d : ''))) ||
                        'No se pudo guardar el registro.';
                    this.$message.error(msg);
                })
                .finally(() => {
                    this.loadingSubmit = false;
                });
        },
        changeStatus(row) {
            this.$http
                .put(`/${this.resource}/${row.id}`, {
                    name: row.name,
                    email: row.email,
                    status: row.status,
                    module_permissions: row.module_permissions || [],
                    client_ids: row.assigned_client_ids || [],
                    can_create_clients: !!row.can_create_clients,
                })
                .then((response) => {
                    this.$message.success(response.data.message);
                })
                .catch((error) => {
                    row.status = !row.status;
                    const d = error.response && error.response.data;
                    const msg =
                        (d && d.message) || 'No se pudo actualizar el estado.';
                    this.$message.error(msg);
                });
        },
        changeCreateClientPermission(row) {
            this.$http
                .put(`/${this.resource}/${row.id}`, {
                    name: row.name,
                    email: row.email,
                    status: !!row.status,
                    module_permissions: row.module_permissions || [],
                    client_ids: row.assigned_client_ids || [],
                    can_create_clients: !!row.can_create_clients,
                })
                .then(() => {
                    this.$message.success('Permiso actualizado correctamente.');
                })
                .catch((error) => {
                    row.can_create_clients = !row.can_create_clients;
                    const d = error.response && error.response.data;
                    const msg =
                        (d && d.message) || 'No se pudo actualizar el permiso.';
                    this.$message.error(msg);
                });
        },
        remove(row) {
            this.$confirm(`Se eliminará el administrador ${row.name}.`, 'Confirmación', {
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
            })
                .then(() => this.$http.delete(`/${this.resource}/${row.id}`))
                .then((response) => {
                    this.$message.success(response.data.message);
                    this.getData();
                })
                .catch((error) => {
                    if (!error || !error.response) {
                        return;
                    }
                    if (error.response.status === 403) {
                        const msg =
                            (error.response.data && error.response.data.message) ||
                            'No autorizado: no se puede eliminar al usuario maestro.';
                        this.$message.error(msg);
                    }
                });
        },
    },
};
</script>

<style scoped>
/* Info / plomo: aspecto de etiqueta, no de botón (Element UI tag plain + refuerzo visual) */
.admin-reseller-master-tag {
    font-weight: 500;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    border-color: #e4e7ed !important;
    background-color: #f4f4f5 !important;
    color: #909399 !important;
}

.admin-reseller-master-tag:hover {
    border-color: #dcdfe6 !important;
    background-color: #eef0f3 !important;
}

.admin-can-create-clients-box {
    box-sizing: border-box;
    min-height: 40px;
    padding: 0 12px;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    background-color: var(--light-color, #fff);
}

.admin-can-create-clients-box:hover {
    border-color: #b3bad3;
}

.admin-can-create-clients-box-label {
    font-size: 14px;
    line-height: 1.4;
    color: var(--dark-color, #303133);
    min-width: 0;
}

.admin-can-create-clients-helper {
    margin-top: 0.25rem;
    margin-bottom: 0;
    line-height: 1.35;
    color: #909399;
}

.admin-can-create-clients-switch {
    display: inline-flex;
    align-items: center;
    vertical-align: middle;
}

.administrator-form-tabs >>> .el-tabs__content {
    padding-top: 8px;
}
</style>

<style>
/* Dialog creado en body: sin scoped para que aplique a custom-class */
.dialog-administrator-form {
    max-width: 880px;
    margin-left: auto !important;
    margin-right: auto !important;
}

.dialog-administrator-form .el-dialog__body {
    padding: 12px 20px 20px;
}

@media (max-width: 576px) {
    .dialog-administrator-form .el-dialog__body {
        padding: 8px 12px 16px;
    }
}
</style>
