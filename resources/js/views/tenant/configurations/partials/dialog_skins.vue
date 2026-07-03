<template>
  <el-dialog
    title="Administrar temas"
    :visible.sync="showDialog"
    @close="close"
    top="5vh"
    width="500px"
  >
    <div class="mb-3">
      <div class="fw-bold text-muted mb-3">
        <span>Temas disponibles</span>
        <el-tag type="primary" class="ms-1">{{ skins.length }}</el-tag>
      </div>

      <div v-if="!skins || skins.length === 0" class="text-center py-4 px-2 text-muted" style="border: 1px dashed #dcdfe6; border-radius: 4px;">
        <i class="el-icon-picture-outline" style="font-size: 24px; margin-bottom: 10px;"></i>
        <p style="margin: 0; font-size: 14px;">No hay temas cargados aún</p>
      </div>

      <div v-else>
        <div
          v-for="(skin, index) in skins"
          :key="skin.id"
          class="d-flex align-items-center justify-content-between p-2 mb-2 template-skin-item"
          style=" border-radius: 4px;"
        >
          <div style="flex: 1;">
            <span style="font-size: 14px;" class="fw-medium">
              {{ skin.name }}
              <small v-if="skin.is_system" class="text-muted ms-1">
                (Tema del sistema)
              </small>
            </span>
            <div style="font-size: 12px;" class="mt-1">
              <i class="el-icon-document me-2"></i>{{ skin.filename }}
            </div>
          </div>

          <div class="d-flex gap-2">
            <el-button
              size="mini"
              type="primary"
              plain
              @click="downloadSkin(skin)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
              Descargar
            </el-button>
            <el-button
              v-if="!skin.is_system"
              size="mini"
              type="danger"
              plain
              @click.prevent="confirmDelete(index)"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
              Eliminar
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <el-divider>Subir nuevo tema</el-divider>

    <div>
      <p style="font-size: 12px;" class="mb-2">
        <i class="el-icon-warning-outline"></i> Solo se aceptan archivos <strong>.css</strong>.
      </p>
      <el-upload
        :headers="headers"
        :multiple="false"
        :on-remove="handleRemove"
        ref="upload"
        :action="`/configurations/visual/upload_skin`"
        :show-file-list="true"
        :on-success="onSuccess"
        :on-error="errorUpload"
        :limit="1"
        drag
        accept=".css"
        style="width: 100%;"
      >
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">Arrastra tu archivo CSS aquí o <em>haz clic para subir</em></div>
      </el-upload>
    </div>

    <span slot="footer" class="dialog-footer">
      <el-button @click.prevent="close()">Cerrar</el-button>
    </span>
  </el-dialog>
</template>

<script>
export default {
  props: ['showDialog', 'skins'],
  data() {
    return {
      form: {},
      headers: headers_token,
      index_file: null,
      records: [],
      fileList: [],
    };
  },
  methods: {
    close() {
      this.$emit('update:showDialog', false);
    },
    handleRemove(file, fileList) {
      this.fileList = [];
      this.index_file = null;
    },
    errorUpload(error) {
      this.$message({ message: 'Error al subir el archivo CSS', type: 'error' });
    },
    onSuccess(response, file, fileList) {
      this.fileList = fileList;
      if (response.success) {
        this.$message.success(response.message);
        if (response.skins !== undefined) {
          this.skins = response.skins;
          this.$emit('update:skins', response.skins);
        }
      } else {
        this.fileList = [];
        this.$message.error(response.message);
      }
    },
    downloadSkin(skin) {
      const a = document.createElement('a');
      a.href = '/storage/skins/' + skin.filename;
      a.download = skin.filename;
      a.click();
    },
    confirmDelete(index) {
      this.$confirm('¿Estás seguro de eliminar este tema?', 'Confirmar', {
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
      }).then(() => {
        this.deleteSkin(index);
      }).catch(() => {});
    },
    deleteSkin(index) {
      this.form.id = this.skins[index].id;
      this.$http.post(`/configurations/visual/delete_skin`, this.form)
        .then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          if (data !== undefined && data.skins !== undefined) {
            this.skins = data.skins;
            this.$emit('update:skins', data.skins);
          }
        })
        .catch(error => {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            this.$message.error('Error al eliminar el tema');
          }
        });
    },
  }
};
</script>

<style scoped>
.el-upload {
  width: 100%;
}
::v-deep .el-upload-dragger {
  width: 100%;
}
</style>
