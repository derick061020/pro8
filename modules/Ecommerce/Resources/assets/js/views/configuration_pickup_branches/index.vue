<template>
  <div>
    <!-- Botón sincronizar con establecimientos -->
    <div class="d-flex align-items-center justify-content-between mb-3">
      <small class="text-muted">
        Gestiona las sucursales donde los clientes pueden recoger sus pedidos.
        Puedes sincronizarlas desde los establecimientos del sistema o crearlas manualmente.
      </small>
      <el-button
        type="default"
        size="small"
        :loading="syncing"
        @click="syncFromEstablishments"
        class="ms-3 text-nowrap"
      >
        <i class="fa fa-refresh"></i> Sincronizar establecimientos
      </el-button>
    </div>

    <!-- Tabla de sucursales existentes -->
    <div class="table-responsive mb-3">
      <table class="table table-sm">
        <thead>
          <tr>
            <th class="text-center" style="width: 70px;">Activo</th>
            <th>Sucursal</th>
            <th>Dirección</th>
            <th class="text-end" style="width: 80px;">Opciones</th>
          </tr>
        </thead>
        <tbody v-if="loading">
          <tr>
            <td colspan="4" class="text-center py-3">
              <i class="fa fa-spinner fa-spin"></i> Cargando...
            </td>
          </tr>
        </tbody>
        <tbody v-else-if="branches.length === 0">
          <tr>
            <td colspan="4" class="text-center py-3 text-muted">
              No hay sucursales registradas. Sincroniza los establecimientos o agrega una manualmente.
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="branch in branches" :key="branch.id">
            <td class="text-center">
              <el-switch
                :value="branch.active"
                @change="toggleActive(branch)"
              ></el-switch>
            </td>
            <td>{{ branch.name }}</td>
            <td class="text-muted">{{ branch.address || '—' }}</td>
            <td class="text-end">
              <button
                type="button"
                class="btn btn-xs btn-danger btn-shad"
                @click="deleteBranch(branch)"
              >
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
        </tbody>

        <!-- Fila de nueva sucursal -->
        <tfoot>
          <tr>
            <td class="text-center">
              <el-switch v-model="newRow.active"></el-switch>
            </td>
            <td>
              <el-input
                v-model="newRow.name"
                placeholder="Nombre de la sucursal"
                size="small"
              ></el-input>
            </td>
            <td>
              <el-input
                v-model="newRow.address"
                placeholder="Dirección (opcional)"
                size="small"
              ></el-input>
            </td>
            <td class="text-end">
              <el-button
                type="primary"
                class="btn btn-xs"
                :loading="saving"
                @click="saveBranch"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Añadir
              </el-button>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PickupBranches',
  data() {
    return {
      branches: [],
      loading: false,
      saving: false,
      syncing: false,
      newRow: {
        name: '',
        address: '',
        active: true,
      },
    };
  },
  created() {
    this.fetchRecords();
  },
  methods: {
    async fetchRecords() {
      this.loading = true;
      try {
        const res = await this.$http.get('/ecommerce/pickup-branches/records');
        this.branches = res.data.data || [];
      } catch (e) {
        this.$message.error('Error al cargar las sucursales.');
      } finally {
        this.loading = false;
      }
    },

    async saveBranch() {
      if (!this.newRow.name.trim()) {
        this.$message.warning('El nombre de la sucursal es obligatorio.');
        return;
      }
      this.saving = true;
      try {
        const res = await this.$http.post('/ecommerce/pickup-branches', {
          name: this.newRow.name.trim(),
          address: this.newRow.address.trim() || null,
        });
        if (res.data.success) {
          this.branches.push(res.data.data);
          this.newRow = { name: '', address: '', active: true };
          this.$message.success(res.data.message || 'Sucursal creada.');
        }
      } catch (e) {
        this.$message.error('Error al guardar la sucursal.');
      } finally {
        this.saving = false;
      }
    },

    async toggleActive(branch) {
      try {
        const res = await this.$http.post(`/ecommerce/pickup-branches/${branch.id}/status`, {
          active: !branch.active,
        });
        if (res.data.success) {
          branch.active = res.data.data.active;
        }
      } catch (e) {
        this.$message.error('Error al actualizar el estado.');
      }
    },

    async deleteBranch(branch) {
      try {
        await this.$confirm(`¿Eliminar la sucursal "${branch.name}"?`, 'Confirmar', {
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          type: 'warning',
        });
      } catch {
        return; // El usuario canceló
      }

      try {
        const res = await this.$http.delete(`/ecommerce/pickup-branches/${branch.id}`);
        if (res.data.success) {
          this.branches = this.branches.filter(b => b.id !== branch.id);
          this.$message.success(res.data.message || 'Sucursal eliminada.');
        }
      } catch (e) {
        this.$message.error('Error al eliminar la sucursal.');
      }
    },

    async syncFromEstablishments() {
      this.syncing = true;
      try {
        const res = await this.$http.post('/ecommerce/pickup-branches/sync');
        if (res.data.success) {
          this.$message.success(res.data.message || 'Sincronización completada.');
          await this.fetchRecords();
        }
      } catch (e) {
        this.$message.error('Error al sincronizar establecimientos.');
      } finally {
        this.syncing = false;
      }
    },
  },
};
</script>
