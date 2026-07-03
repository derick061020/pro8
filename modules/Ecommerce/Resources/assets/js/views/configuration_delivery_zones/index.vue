<template>
  <div>
    <!-- Filtros -->
    <!-- <div class="mb-1">
      <h6 class="fw-semibold mb-0">Filtrar zonas</h6>
    </div>
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
      <el-input v-model="filters.q" placeholder="Buscar por nombre..." size="small" clearable style="width: 220px;"
        @input="onSearchInput" @clear="applyFilters"></el-input>
      <el-select v-model="filters.active" placeholder="Estado" size="small" clearable style="width: 140px;"
        @change="applyFilters">
        <el-option label="Activo" value="1"></el-option>
        <el-option label="Inactivo" value="0"></el-option>
      </el-select>
    </div> -->

    <!-- Tabla -->
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="text-center">Activo</th>
            <th style="min-width: 150px;" class="text-start">Nombre</th>
            <th style="width: 120px;" class="text-start">Precio (S/)</th>
            <th style="width: 260px;" class="text-end">Cobertura</th>
            <th style="width: 100px;" class="text-end">Opciones</th>
          </tr>
        </thead>

        <!-- Cargando -->
        <tbody v-if="loading">
          <tr>
            <td colspan="5" class="text-center py-4">
              <i class="fa fa-spinner fa-spin"></i> Cargando...
            </td>
          </tr>
        </tbody>

        <tbody v-else>

          <!-- ── Nueva zona ── -->
          <template v-if="showingNewRow">
          <tr class="" :style="newRow._open ? 'border-bottom: 0px solid transparent !important' : ''" :class="newRow._open ? 'bb-td-0' : ''">
            <td class="text-center">
              <el-switch v-model="newRow.active"></el-switch>
            </td>
            <td>
              <el-input v-model="newRow.name" placeholder="Nombre de la zona" size="small"></el-input>
            </td>
            <td>
              <el-input v-model="newRow.price" :min="0" :precision="2" :step="1" size="small" type="number"
                style="width: 100%;"></el-input>
            </td>
            <td class="text-end">
              <span :class="coverageLabelClass(newRow)" style="font-size:11px; margin-left:3px;">
                {{ coverageLabel(newRow) }}
              </span>
              <!-- Toggle cobertura -->
              <button class="btn btn-xs btn-light btn-shad me-1" type="button"
                :title="newRow._open ? 'Ocultar cobertura' : 'Ver cobertura'"
                @click.prevent="newRow._open = !newRow._open">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline v-if="newRow._open" points="18 15 12 9 6 15"></polyline>
                  <polyline v-else points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>
            </td>
            <td class="text-end" style="white-space: nowrap;">
              <!-- Guardar -->
              <button class="btn btn-xs btn-success btn-shad me-1" type="button" title="Guardar"
                :disabled="newRow._saving || !newRowReady" @click.prevent="saveNewRow">
                <i v-if="newRow._saving" class="fa fa-spinner fa-spin"></i>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
              </button>
              <!-- Cancelar -->
              <button class="btn btn-xs btn-secondary btn-shad" type="button" title="Cancelar"
                @click.prevent="resetNewRow">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </td>
          </tr>

          <!-- ── Nueva zona: fila cobertura colapsable ── -->
          <tr v-if="newRow._open">
            <td colspan="5" class="pt-2 pb-4">
              <div class="d-flex flex-wrap gap-1 mb-1">
                <span class="d-flex align-items-center text-muted me-1">Añadir zonas</span>
                <el-select v-model="newRow._picker.depts" multiple collapse-tags filterable placeholder="Departamento"
                  size="small" style="min-width: 80px; flex: 1;" @change="onPickerDeptChange(newRow._picker)">
                  <el-option v-for="d in getPickerAvailableDepts(newRow)" :key="d.value" :label="d.label" :value="d.value"></el-option>
                </el-select>
                <el-select v-if="newRow._picker.depts.length === 1" v-model="newRow._picker.provs" multiple collapse-tags filterable placeholder="Provincia (opc.)"
                  size="small" style="min-width: 80px; flex: 1;"
                  @change="onPickerProvChange(newRow._picker)">
                  <el-option v-for="p in getPickerProvinces(newRow._picker)" :key="p.value" :label="p.label"
                    :value="p.value"></el-option>
                </el-select>
                <el-select v-if="newRow._picker.depts.length === 1 && newRow._picker.provs.length === 1" v-model="newRow._picker.dists" multiple collapse-tags filterable
                  placeholder="Distrito (opc.)" size="small" style="min-width: 80px; flex: 1;">
                  <el-option v-for="d in getPickerDistricts(newRow._picker)" :key="d.value" :label="d.label"
                    :value="d.value"></el-option>
                </el-select>
                <el-button type="primary" size="mini" class="btn btn-sm"
                  :disabled="newRow._picker.depts.length === 0"
                  @click.prevent="addCoverage(newRow)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                  Añadir
                </el-button>
              </div>
              <div v-if="newRow.locations.length > 0" class="d-flex flex-wrap gap-1">
                <el-tag v-for="(loc, idx) in newRow.locations" :key="idx" closable size="mini" type="info"
                  @close="newRow.locations.splice(idx, 1)">{{ loc.label }}</el-tag>
              </div>
            </td>
          </tr>
          </template>

          <!-- ── Zonas existentes ── -->
          <template v-for="row in records">

            <!-- Fila principal -->
            <tr :key="'main-' + row.id" :style="row._open ? 'border-bottom: 0px solid transparent !important' : ''">
              <td class="text-center">
                <el-switch v-model="row.active" @change="toggleStatus(row)"></el-switch>
              </td>
              <td>
                <el-input v-model="row.name" size="small" placeholder="Nombre de la zona"></el-input>
              </td>
              <td>
                <el-input v-model="row.price" :min="0" :precision="2" :step="1" size="small" type="number"
                  style="width: 100%;"></el-input>
              </td>
              <td class="text-end">
                <span :class="coverageLabelClass(row)" style="font-size:11px; margin-left:3px;">
                  {{ coverageLabel(row) }}
                </span>
                <!-- Toggle cobertura -->
                <button class="btn btn-xs btn-light btn-shad me-1" type="button"
                  :title="row._open ? 'Ocultar cobertura' : 'Ver cobertura'"
                  @click.prevent="row._open = !row._open">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline v-if="row._open" points="18 15 12 9 6 15"></polyline>
                    <polyline v-else points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
              </td>
              <td class="text-end" style="white-space: nowrap;">
                <!-- Guardar -->
                <button class="btn btn-xs btn-success btn-shad me-1" type="button" title="Guardar"
                  :disabled="row._saving" @click.prevent="saveRow(row)">
                  <i v-if="row._saving" class="fa fa-spinner fa-spin"></i>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                </button>
                <!-- Duplicar -->
                <button class="btn btn-xs btn-warning btn-shad me-1" type="button" title="Duplicar"
                  @click.prevent="duplicateRow(row)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                    <path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                  </svg>
                </button>
                <!-- Eliminar -->
                <button class="btn btn-xs btn-danger btn-shad" type="button" title="Eliminar"
                  @click.prevent="deleteRow(row.id)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7l16 0" />
                    <path d="M10 11l0 6" />
                    <path d="M14 11l0 6" />
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                  </svg>
                </button>
              </td>
            </tr>

            <!-- Fila cobertura colapsable -->
            <tr v-if="row._open" :key="'cov-' + row.id">
              <td colspan="5" class="pt-2 pb-4">
                <div class="d-flex flex-wrap gap-1 mb-1">
                  <span class="d-flex align-items-center text-muted me-1">Añadir zonas</span>
                  <el-select v-model="row._picker.depts" multiple collapse-tags filterable placeholder="Departamento"
                    size="small" style="min-width: 80px; flex: 1;" @change="onPickerDeptChange(row._picker)">
                    <el-option v-for="d in getPickerAvailableDepts(row)" :key="d.value" :label="d.label" :value="d.value"></el-option>
                  </el-select>
                  <el-select v-if="row._picker.depts.length === 1" v-model="row._picker.provs" multiple collapse-tags filterable placeholder="Provincia (opc.)"
                    size="small" style="min-width: 80px; flex: 1;"
                    @change="onPickerProvChange(row._picker)">
                    <el-option v-for="p in getPickerProvinces(row._picker)" :key="p.value" :label="p.label"
                      :value="p.value"></el-option>
                  </el-select>
                  <el-select v-if="row._picker.depts.length === 1 && row._picker.provs.length === 1" v-model="row._picker.dists" multiple collapse-tags filterable placeholder="Distrito (opc.)"
                    size="small" style="min-width: 80px; flex: 1;">
                    <el-option v-for="d in getPickerDistricts(row._picker)" :key="d.value" :label="d.label"
                      :value="d.value"></el-option>
                  </el-select>
                  <el-button type="primary" size="mini" class="btn btn-sm"
                    :disabled="row._picker.depts.length === 0"
                    @click.prevent="addCoverage(row)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Añadir
                  </el-button>
                </div>
                <div v-if="row.locations && row.locations.length > 0" class="d-flex flex-wrap gap-1">
                  <el-tag v-for="(loc, idx) in row.locations" :key="idx" closable size="mini" type="primary"
                    @close="row.locations.splice(idx, 1)">{{ loc.label }}</el-tag>
                </div>
                <span v-else class="text-muted" style="font-size: 12px;">— sin cobertura asignada</span>
              </td>
            </tr>

          </template>

          <!-- Sin resultados -->
          <tr v-if="records.length === 0">
            <td colspan="4" class="text-center text-warning py-4">
              No se encontraron zonas de delivery.
            </td>
          </tr>

        </tbody>
      </table>
    </div>

    <el-pagination v-if="pagination.total > pagination.per_page" @current-change="onPageChange"
      :current-page.sync="pagination.current_page" :page-size="pagination.per_page" :total="pagination.total"
      layout="total, prev, pager, next" background small></el-pagination>

    <div class="form-group bg-danger-light rounded p-3 coverage-config-message mt-3">
      <label class="control-label mb-0 text-nowrap">Mensaje sin cobertura:</label>
      <div class="input-group">
        <el-input v-model="noCoverageMessage"
          placeholder="Ej: Lo sentimos, por ahora no contamos con delivery en tu zona." style="flex: 1;"></el-input>
        <el-button type="primary" size="small" plain class="ms-2 btn btn-sm" style="white-space: nowrap;" :loading="savingMessage"
          @click="saveNoCoverageMessage">Guardar</el-button>
      </div>
    </div>

  </div>
</template>

<script>
import { deletable } from '@mixins/deletable';

export default {
  name: 'DeliveryZonesIndex',
  mixins: [deletable],
  data() {
    return {
      records: [],
      loading: false,
      pagination: {
        current_page: 1,
        per_page: 15,
        total: 0,
      },
      filters: {
        q: '',
        active: '',
      },
      searchTimeout: null,
      showingNewRow: false,
      newRow: { name: '', price: 0, active: true, locations: [], _picker: { depts: [], provs: [], dists: [] }, _saving: false, _open: false },
      noCoverageMessage: '',
      savingMessage: false,
      locationTree: [],
    };
  },
  async created() {
    await Promise.all([
      this.loadConfigMessage(),
      this.loadLocationTree(),
    ]);
    this.getRecords();
  },
  computed: {
    newRowReady() {
      return !!(this.newRow.name && this.newRow.name.trim()) &&
        this.newRow.price > 0;
    },
    newRowDirty() {
      return !!(this.newRow.name && this.newRow.name.trim()) ||
        this.newRow.price > 0 ||
        this.newRow.locations.length > 0;
    },
  },
  methods: {

    getRecords() {
      this.loading = true;
      this.$http.post('/ecommerce/delivery-zones/records', {
        ...this.filters,
        page: this.pagination.current_page,
      }).then(response => {
        const data = response.data;
        this.records = (data.data || []).map(r => ({
          ...r,
          price: parseFloat(r.price) || 0,
          _picker: { depts: [], provs: [], dists: [] },
          _saving: false,
          _open: false,
        }));
        if (data.meta) {
          this.pagination.current_page = data.meta.current_page;
          this.pagination.per_page = data.meta.per_page;
          this.pagination.total = data.meta.total;
        }
      }).finally(() => {
        this.loading = false;
      });
    },
    applyFilters() {
      this.pagination.current_page = 1;
      this.getRecords();
    },
    onSearchInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => this.applyFilters(), 350);
    },
    onPageChange(page) {
      this.pagination.current_page = page;
      this.getRecords();
    },
    async loadConfigMessage() {
      try {
        const res = await this.$http.get('/ecommerce/record');
        this.noCoverageMessage = res.data?.data?.delivery_no_coverage_message || '';
      } catch (e) { }
    },
    saveNoCoverageMessage() {
      this.savingMessage = true;
      this.$http.get('/ecommerce/record').then(res => {
        const id = res.data?.data?.id;
        return this.$http.post('/ecommerce/configuration_delivery', {
          id,
          delivery_no_coverage_message: this.noCoverageMessage,
        });
      }).then(() => {
        this.$message.success('Mensaje guardado.');
      }).catch(() => {
        this.$message.error('Error al guardar el mensaje.');
      }).finally(() => {
        this.savingMessage = false;
      });
    },


    async loadLocationTree() {
      try {
        const res = await this.$http.get('/ecommerce/get-location-cascade');
        this.locationTree = res.data;
      } catch (e) {
        this.locationTree = [];
      }
    },
    getPickerAvailableDepts(row) {
      const locs = row.locations || [];
      const usedDeptIds = new Set(locs.map(l => l.department_id));
      return this.locationTree.filter(d => !usedDeptIds.has(d.value));
    },
    getPickerProvinces(picker) {
      if (picker.depts.length !== 1) return [];
      const dept = this.locationTree.find(d => d.value === picker.depts[0]);
      return dept ? (dept.children || []) : [];
    },
    getPickerDistricts(picker) {
      if (picker.depts.length !== 1 || picker.provs.length !== 1) return [];
      const dept = this.locationTree.find(d => d.value === picker.depts[0]);
      if (!dept) return [];
      const prov = (dept.children || []).find(p => p.value === picker.provs[0]);
      return prov ? (prov.children || []) : [];
    },
    onPickerDeptChange(picker) {
      picker.provs = [];
      picker.dists = [];
    },
    onPickerProvChange(picker) {
      picker.dists = [];
    },


    addCoverage(row) {
      const picker = row._picker;
      if (!picker || picker.depts.length === 0) return;
      if (!row.locations) this.$set(row, 'locations', []);

      const { depts, provs, dists } = picker;

      if (depts.length > 1) {
        depts.forEach(deptId => {
          if (!(row.locations).some(l => l.department_id === deptId && !l.province_id && !l.district_id)) {
            const dept = this.locationTree.find(d => d.value === deptId);
            row.locations.push({ department_id: deptId, province_id: null, district_id: null, label: dept ? dept.label : deptId });
          }
        });
      } else if (depts.length === 1 && provs.length > 1) {
        const dept = this.locationTree.find(d => d.value === depts[0]);
        provs.forEach(provId => {
          if (!(row.locations).some(l => l.department_id === depts[0] && l.province_id === provId && !l.district_id)) {
            const prov = dept ? (dept.children || []).find(p => p.value === provId) : null;
            row.locations.push({ department_id: depts[0], province_id: provId, district_id: null, label: [dept?.label, prov?.label].filter(Boolean).join(' > ') });
          }
        });
      } else if (depts.length === 1 && provs.length === 1 && dists.length > 0) {
        const dept = this.locationTree.find(d => d.value === depts[0]);
        const prov = dept ? (dept.children || []).find(p => p.value === provs[0]) : null;
        dists.forEach(distId => {
          if (!(row.locations).some(l => l.department_id === depts[0] && l.province_id === provs[0] && l.district_id === distId)) {
            const dist = prov ? (prov.children || []).find(d => d.value === distId) : null;
            row.locations.push({ department_id: depts[0], province_id: provs[0], district_id: distId, label: [dept?.label, prov?.label, dist?.label].filter(Boolean).join(' > ') });
          }
        });
      } else if (depts.length === 1 && provs.length === 1 && dists.length === 0) {
        const dept = this.locationTree.find(d => d.value === depts[0]);
        const prov = dept ? (dept.children || []).find(p => p.value === provs[0]) : null;
        if (!(row.locations).some(l => l.department_id === depts[0] && l.province_id === provs[0] && !l.district_id)) {
          row.locations.push({ department_id: depts[0], province_id: provs[0], district_id: null, label: [dept?.label, prov?.label].filter(Boolean).join(' > ') });
        }
      } else if (depts.length === 1 && provs.length === 0) {
        const dept = this.locationTree.find(d => d.value === depts[0]);
        if (!(row.locations).some(l => l.department_id === depts[0] && !l.province_id && !l.district_id)) {
          row.locations.push({ department_id: depts[0], province_id: null, district_id: null, label: dept ? dept.label : depts[0] });
        }
      }

      row._picker = { depts: [], provs: [], dists: [] };
    },


    _freshRow() {
      return { name: '', price: 0, active: true, locations: [], _picker: { depts: [], provs: [], dists: [] }, _saving: false, _open: false };
    },
    clickNew() {
      const open = () => { this.newRow = this._freshRow(); this.showingNewRow = true; };
      if (this.showingNewRow && this.newRowDirty) {
        this.$confirm('Tienes cambios sin guardar que se perderán. ¿Deseas continuar?', 'Advertencia', {
          confirmButtonText: 'Descartar cambios',
          cancelButtonText: 'Cancelar',
          type: 'warning',
        }).then(open).catch(() => {});
        return;
      }
      open();
    },
    resetNewRow() {
      const close = () => { this.showingNewRow = false; this.newRow = this._freshRow(); };
      if (this.newRowDirty) {
        this.$confirm('Tienes cambios sin guardar que se perderán. ¿Deseas descartarlos?', 'Advertencia', {
          confirmButtonText: 'Descartar cambios',
          cancelButtonText: 'Cancelar',
          type: 'warning',
        }).then(close).catch(() => {});
        return;
      }
      close();
    },
    saveNewRow() {
      if (!this.newRow.name || !this.newRow.name.trim()) {
        this.$message.warning('El nombre de la zona es obligatorio.');
        return;
      }
      this.newRow._saving = true;
      let locations = this.newRow.locations;
      if (locations.length === 0) {
        locations = this.locationTree.map(d => ({ department_id: d.value, province_id: null, district_id: null, label: d.label }));
      }
      this.$http.post('/ecommerce/delivery-zones', {
        name: this.newRow.name.trim(),
        price: this.newRow.price,
        active: this.newRow.active,
        locations: locations.map(({ department_id, province_id, district_id }) => ({
          department_id,
          province_id: province_id || null,
          district_id: district_id || null,
        })),
      }).then(() => {
        this.$message.success('Zona creada correctamente.');
        this.showingNewRow = false;
        this.newRow = this._freshRow();
        this.getRecords();
      }).catch(error => {
        const msg = this.extractErrorMessage(error, 'Error al crear la zona.');
        this.$message.error(msg);
      }).finally(() => {
        if (this.newRow) this.newRow._saving = false;
      });
    },
    saveRow(row) {
      if (!row.name || !row.name.trim()) {
        this.$message.warning('El nombre de la zona es obligatorio.');
        return;
      }
      row._saving = true;
      let locations = row.locations || [];
      if (locations.length === 0) {
        locations = this.locationTree.map(d => ({ department_id: d.value, province_id: null, district_id: null, label: d.label }));
      }
      this.$http.post('/ecommerce/delivery-zones', {
        id: row.id,
        name: row.name.trim(),
        price: row.price,
        active: row.active,
        locations: locations.map(({ department_id, province_id, district_id }) => ({
          department_id,
          province_id: province_id || null,
          district_id: district_id || null,
        })),
      }).then(() => {
        this.$message.success('Zona actualizada correctamente.');
      }).catch(error => {
        const msg = this.extractErrorMessage(error, 'Error al guardar la zona.');
        this.$message.error(msg);
      }).finally(() => {
        row._saving = false;
      });
    },
    toggleStatus(row) {
      this.$http.post(`/ecommerce/delivery-zones/${row.id}/status`, { active: row.active })
        .catch(() => {
          row.active = !row.active;
          this.$message.error('No se pudo cambiar el estado.');
        });
    },
    duplicateRow(row) {
      this.$http.post('/ecommerce/delivery-zones', {
        name: row.name + ' copia',
        price: row.price,
        active: false,
        locations: (row.locations || []).map(({ department_id, province_id, district_id }) => ({
          department_id,
          province_id: province_id || null,
          district_id: district_id || null,
        })),
      }).then(() => {
        this.$message.success('Zona duplicada correctamente.');
        this.getRecords();
      }).catch(() => {
        this.$message.error('No se pudo duplicar la zona.');
      });
    },
    deleteRow(id) {
      this.destroy(`/ecommerce/delivery-zones/${id}`)
        .then(() => this.getRecords());
    },


    coverageLabel(row) {
      const locs = row.locations || [];
      if (locs.length === 0) return 'Sin cobertura (se aplicará a todo el Perú)';
      const uniqueDepts = [...new Set(locs.map(l => l.department_id))];
      if (this.locationTree.length > 0 && uniqueDepts.length >= this.locationTree.length) {
        const allDeptLevel = uniqueDepts.every(deptId =>
          locs.some(l => l.department_id === deptId && !l.province_id && !l.district_id)
        );
        if (allDeptLevel) return 'Todo el Perú';
      }
      const deptCount = uniqueDepts.length;
      const hasDetail = locs.some(l => l.province_id || l.district_id);
      if (!hasDetail) {
        return deptCount === 1 ? '1 departamento' : `${deptCount} departamentos`;
      }
      return deptCount === 1
        ? `1 departamento (${locs.length} ${locs.length === 1 ? 'zona' : 'zonas'})`
        : `${deptCount} departamentos (${locs.length} ${locs.length === 1 ? 'zona' : 'zonas'})`;
    },
    coverageLabelClass(row) {
      const locs = row.locations || [];
      if (locs.length === 0) return 'text-muted';
      const uniqueDepts = [...new Set(locs.map(l => l.department_id))];
      if (this.locationTree.length > 0 && uniqueDepts.length >= this.locationTree.length) return 'text-success fw-semibold';
      return 'text-primary';
    },

    extractErrorMessage(error, fallback) {
      if (error.response?.status === 422) {
        const first = Object.values(error.response.data)[0];
        return Array.isArray(first) ? first[0] : first;
      }
      return fallback;
    },
  },
};
</script>
