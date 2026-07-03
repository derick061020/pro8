<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="open"
        :close-on-click-modal="false"
        width="680px"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">

                <!-- Nombre de la zona -->
                <div class="form-group" :class="{'has-danger': errors.name}">
                    <label class="control-label">NOMBRE DE LA ZONA</label>
                    <el-input
                        v-model="form.name"
                        placeholder="Ej: Lima Centro, Provincia, Callao..."
                        :maxlength="150"
                    ></el-input>
                    <small class="form-control-feedback" v-if="errors.name" v-text="errors.name[0]"></small>
                </div>

                <!-- Precio de envío -->
                <div class="row">
                    <div class="col-6">
                        <div class="form-group" :class="{'has-danger': errors.price}">
                            <label class="control-label">PRECIO DE ENVÍO (S/)</label>
                            <el-input-number
                                v-model="form.price"
                                :min="0"
                                :precision="2"
                                :step="1"
                                style="width: 100%;"
                            ></el-input-number>
                            <small class="form-control-feedback" v-if="errors.price" v-text="errors.price[0]"></small>
                        </div>
                    </div>
                    <div class="col-6 d-flex align-items-end pb-3">
                        <el-switch
                            v-model="form.active"
                            active-text="Activo"
                            inactive-text="Inactivo"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                        ></el-switch>
                    </div>
                </div>

                <!-- Selector de ubicaciones -->
                <div class="border rounded p-3 mb-3">
                    <label class="control-label mb-2 d-block">COBERTURA (ubigeo)</label>
                    <small class="text-muted d-block mb-2">
                        Selecciona Departamento → Provincia (opcional) → Distrito (opcional) y pulsa <strong>+ Agregar</strong>.
                        Dejar Provincia vacía cubre todo el departamento.
                    </small>

                    <!-- Selectores en cascada -->
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                        <el-select
                            v-model="locationPicker.department_id"
                            placeholder="Departamento"
                            size="small"
                            filterable
                            clearable
                            style="width: 170px;"
                            @change="onPickerDepartmentChange"
                        >
                            <el-option
                                v-for="dep in allDepartments"
                                :key="dep.value"
                                :label="dep.label"
                                :value="dep.value"
                            ></el-option>
                        </el-select>

                        <el-select
                            v-model="locationPicker.province_id"
                            placeholder="Provincia"
                            size="small"
                            filterable
                            clearable
                            :disabled="!locationPicker.department_id"
                            style="width: 170px;"
                            @change="onPickerProvinceChange"
                        >
                            <el-option
                                v-for="prov in pickerProvinces"
                                :key="prov.value"
                                :label="prov.label"
                                :value="prov.value"
                            ></el-option>
                        </el-select>

                        <el-select
                            v-model="locationPicker.district_id"
                            placeholder="Distrito (opc.)"
                            size="small"
                            filterable
                            clearable
                            :disabled="!locationPicker.province_id"
                            style="width: 170px;"
                        >
                            <el-option
                                v-for="dist in pickerDistricts"
                                :key="dist.value"
                                :label="dist.label"
                                :value="dist.value"
                            ></el-option>
                        </el-select>

                        <el-button
                            type="primary"
                            size="small"
                            :disabled="!locationPicker.department_id"
                            @click.prevent="addLocation"
                        >
                            + Agregar
                        </el-button>
                    </div>

                    <!-- Error de ubicaciones -->
                    <small class="form-control-feedback text-danger" v-if="errors.locations" v-text="errors.locations[0]"></small>

                    <!-- Chips de ubicaciones agregadas -->
                    <div v-if="form.locations.length > 0" class="d-flex flex-wrap gap-1 mt-2">
                        <el-tag
                            v-for="(loc, idx) in form.locations"
                            :key="idx"
                            closable
                            size="small"
                            type="info"
                            @close="removeLocation(idx)"
                        >
                            {{ loc.label }}
                        </el-tag>
                    </div>
                    <div v-else class="text-muted" style="font-size: 0.82rem;">
                        Sin ubicaciones agregadas aún.
                    </div>
                </div>

            </div><!-- /.form-body -->

            <div class="form-actions text-end pt-2">
                <el-button @click.prevent="close">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading">
                    Guardar Zona
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    name: 'DeliveryZoneForm',
    props: {
        showDialog: {
            type: Boolean,
            default: false,
        },
        zoneRecord: {
            default: null,
        },
        // Árbol de ubicaciones cargado por el componente padre
        locationTree: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            loading: false,
            errors: {},
            form: this.initFormData(),
            // Estado del picker de nueva ubicación
            locationPicker: {
                department_id: '',
                province_id:   '',
                district_id:   '',
            },
            pickerProvinces: [],
            pickerDistricts: [],
        };
    },
    computed: {
        titleDialog() {
            return this.zoneRecord && this.zoneRecord.id ? 'Editar Zona de Delivery' : 'Nueva Zona de Delivery';
        },
        allDepartments() {
            return this.locationTree;
        },
    },
    methods: {
        initFormData() {
            return {
                id:        null,
                name:      '',
                price:     0,
                active:    true,
                locations: [],  // [{ department_id, province_id, district_id, label }]
            };
        },
        open() {
            this.errors = {};
            this.resetPicker();

            if (this.zoneRecord) {
                this.form = {
                    id:        this.zoneRecord.id || null,
                    name:      this.zoneRecord.name,
                    price:     parseFloat(this.zoneRecord.price) || 0,
                    active:    this.zoneRecord.active !== undefined ? this.zoneRecord.active : true,
                    locations: (this.zoneRecord.locations || []).map(loc => ({
                        department_id: loc.department_id,
                        province_id:   loc.province_id   || null,
                        district_id:   loc.district_id   || null,
                        label:         loc.label || this.buildLabel(loc),
                    })),
                };
            } else {
                this.form = this.initFormData();
            }
        },
        close() {
            this.$emit('update:showDialog', false);
            this.$emit('update:zoneRecord', null);
        },
        resetPicker() {
            this.locationPicker  = { department_id: '', province_id: '', district_id: '' };
            this.pickerProvinces = [];
            this.pickerDistricts = [];
        },
        onPickerDepartmentChange(deptId) {
            this.locationPicker.province_id  = '';
            this.locationPicker.district_id  = '';
            this.pickerDistricts = [];

            const dept = this.locationTree.find(d => d.value === deptId);
            this.pickerProvinces = dept ? (dept.children || []) : [];
        },
        onPickerProvinceChange(provId) {
            this.locationPicker.district_id = '';

            const prov = this.pickerProvinces.find(p => p.value === provId);
            this.pickerDistricts = prov ? (prov.children || []) : [];
        },
        addLocation() {
            const { department_id, province_id, district_id } = this.locationPicker;
            if (!department_id) return;

            // Evitar duplicados exactos
            const exists = this.form.locations.some(
                l => l.department_id === department_id
                  && (l.province_id || null) === (province_id || null)
                  && (l.district_id || null) === (district_id || null)
            );
            if (exists) {
                this.$message.warning('Esta combinación ya fue agregada.');
                return;
            }

            this.form.locations.push({
                department_id: department_id,
                province_id:   province_id || null,
                district_id:   district_id || null,
                label:         this.buildPickerLabel(department_id, province_id, district_id),
            });

            this.resetPicker();
        },
        removeLocation(idx) {
            this.form.locations.splice(idx, 1);
        },
        buildPickerLabel(deptId, provId, distId) {
            const parts = [];
            const dept = this.locationTree.find(d => d.value === deptId);
            if (dept) parts.push(dept.label);

            if (provId) {
                const prov = (dept?.children || []).find(p => p.value === provId);
                if (prov) parts.push(prov.label);

                if (distId) {
                    const dist = (prov?.children || []).find(d => d.value === distId);
                    if (dist) parts.push(dist.label);
                }
            }

            return parts.join(' > ');
        },
        buildLabel(loc) {
            // Fallback cuando el label no viene pre-calculado del backend
            return [loc.department_id, loc.province_id, loc.district_id]
                .filter(Boolean)
                .join(' > ');
        },
        submit() {
            this.loading = true;
            this.errors  = {};

            const payload = {
                id:        this.form.id,
                name:      this.form.name,
                price:     this.form.price,
                active:    this.form.active,
                locations: this.form.locations.map(({ department_id, province_id, district_id }) => ({
                    department_id,
                    province_id:  province_id  || null,
                    district_id:  district_id  || null,
                })),
            };

            this.$http.post('/ecommerce/delivery-zones', payload)
                .then(() => {
                    this.$message.success('Zona guardada correctamente.');
                    this.$eventHub.$emit('reloadData');
                    this.close();
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        this.$message.error('Ocurrió un error al guardar la zona.');
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>
