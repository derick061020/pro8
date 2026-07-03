<template>
  <div class="card mb-0 public-search-background-card">
    <div class="card-header bg-info bg-info-customer-admin d-flex align-items-center justify-content-between">
      <h3 class="my-0">Personalizar</h3>
      <el-switch v-model="enabled" active-text="Si" inactive-text="No" @change="onToggleEnabled"></el-switch>
    </div>

    <div class="card-body" v-if="enabled">
      <div class="mb-4">
        <div class="control-label mb-2">Color de fondo</div>
        <div class="suggested-colors-grid mb-3">
          <button
            v-for="color in suggestedColors"
            :key="color"
            type="button"
            class="suggested-color-btn"
            :class="{ active: form.background_color === color }"
            :style="{ backgroundColor: color }"
            :aria-label="'Seleccionar color ' + color"
            @click="selectSuggestedColor(color)"
          ></button>
        </div>

        <div class="row align-items-center g-3">
          <div class="col-12 col-md-7">
            <el-color-picker
              :value="form.background_color"
              class="w-100"
              @active-change="onColorPicking"
              @change="onColorConfirmed"
            ></el-color-picker>
          </div>
          <div class="col-12 col-md-5">
            <small class="text-muted d-block">
              Puedes elegir un color sugerido o ajustar uno personalizado.
            </small>
          </div>
        </div>
      </div>

      <div class="mb-4 pt-1">
        <div class="row g-3 align-items-stretch public-search-media-row">
          <div class="col-12 col-md-4 col-lg-3">
            <div class="control-label mb-2">Imagen de fondo</div>
            <label class="public-search-file-square mb-0">
              <input
                ref="imageInput"
                type="file"
                class="public-search-file-input"
                accept="image/png,image/jpeg,image/webp"
                @change="onImageChange"
              >

              <span class="public-search-file-title">Seleccionar archivo</span>
              <small class="public-search-file-hint">{{ fileToUpload ? fileToUpload.name : 'PNG, JPG o WEBP' }}</small>
            </label>
          </div>

          <div class="col-12 col-md-8 col-lg-9">
            <div class="control-label mb-2">Vista previa</div>
            <div class="public-search-background-preview">
              <img v-if="previewUrl" :src="previewUrl" alt="Vista previa del fondo">
              <span v-else>Sin imagen seleccionada</span>
            </div>
          </div>
        </div>

        <small class="form-control-feedback d-block mt-2">Máximo 4 MB.</small>
      </div>

      <div class="public-search-background-actions-row">
        <div class="control-label d-block mb-2">Acciones</div>
        <div class="row g-2">
          <div class="col-12 col-md-4">
            <el-button class="w-100" type="primary" :loading="loading" :disabled="loading" @click="confirmSaveBackground">Guardar</el-button>
          </div>
          <div class="col-12 col-md-4">
            <el-button class="w-100" :disabled="loading || !form.background_image_url" @click="confirmRemoveImage">Quitar imagen</el-button>
          </div>
          <div class="col-12 col-md-4">
            <el-button class="w-100" :disabled="loading" @click="confirmResetToDefault">Restablecer</el-button>
          </div>
        </div>

        <small class="text-muted d-block mt-2">
          Los cambios se aplican a este buscador embebido.
        </small>
      </div>

      <small class="form-control-feedback d-block" :class="statusClass">{{ statusMessage }}</small>
    </div>

    <div class="card-body" v-else>
      <p class="mb-0 text-muted">Activa esta opción para configurar el fondo del buscador embebido.</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    loadUrl: {
      type: String,
      required: true,
    },
    saveUrl: {
      type: String,
      required: true,
    },
    scopeLabel: {
      type: String,
      default: 'Buscador de comprobantes',
    },
  },
  data() {
    return {
      loading: false,
      enabled: false,
      suggestedColors: ['#f0f2f7', '#e9f5ff', '#fef3e8', '#f3f7ec', '#f4ecff', '#ffeef1', '#f8fafc', '#edf2ff'],
      previewUrl: '',
      fileToUpload: null,
      removeCurrentImage: false,
      statusMessage: '',
      statusClass: '',
      form: {
        background_color: '#e4e8f0',
        background_image_url: null,
      },
    };
  },
  async mounted() {
    await this.loadBackground();
  },
  methods: {
    askConfirmation(message, title = 'Confirmar acción', confirmText = 'Sí, continuar') {
      return this.$confirm(message, title, {
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancelar',
        type: 'warning',
      })
        .then(() => true)
        .catch(() => false);
    },
    async confirmSaveBackground() {
      const confirmed = await this.askConfirmation(
        'Se guardarán los cambios de personalización del buscador. ¿Deseas continuar?',
        'Guardar cambios',
        'Sí, guardar'
      );

      if (!confirmed) {
        return;
      }

      await this.saveBackground();
    },
    async confirmRemoveImage() {
      const confirmed = await this.askConfirmation(
        'Se eliminará la imagen de fondo actual. ¿Deseas continuar?',
        'Quitar imagen',
        'Sí, quitar'
      );

      if (!confirmed) {
        return;
      }

      await this.removeImage();
    },
    async confirmResetToDefault() {
      const confirmed = await this.askConfirmation(
        'Esta acción restablecerá color e imagen del buscador a valores predeterminados. ¿Deseas continuar?',
        'Restablecer personalización',
        'Sí, restablecer'
      );

      if (!confirmed) {
        return;
      }

      await this.resetToDefault();
    },
    normalizeHexColor(value) {
      if (!value || typeof value !== 'string') {
        return '';
      }

      const color = value.trim();
      const hexMatch = color.match(/^#([0-9a-fA-F]{6})$/);
      if (hexMatch) {
        return `#${hexMatch[1].toLowerCase()}`;
      }

      const shortHexMatch = color.match(/^#([0-9a-fA-F]{3})$/);
      if (shortHexMatch) {
        const expanded = shortHexMatch[1].split('').map((char) => `${char}${char}`).join('');
        return `#${expanded.toLowerCase()}`;
      }

      const rgbMatch = color.match(/^rgba?\(([^)]+)\)$/i);
      if (!rgbMatch) {
        return '';
      }

      const parts = rgbMatch[1].split(',').map((part) => part.trim());
      if (parts.length < 3) {
        return '';
      }

      const [r, g, b] = parts.slice(0, 3).map(Number);
      if ([r, g, b].some((part) => Number.isNaN(part))) {
        return '';
      }

      const toHex = (num) => Math.max(0, Math.min(255, num)).toString(16).padStart(2, '0');
      return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
    },
    setStatus(message, type = '') {
      this.statusMessage = message;
      this.statusClass = type;
    },
    async loadBackground() {
      try {
        const response = await this.$http.get(this.loadUrl);
        const data = response.data || {};

        const normalizedColor = this.normalizeHexColor(data.background_color || '');
        this.form.background_color = normalizedColor || '#e4e8f0';
        this.form.background_image_url = data.background_image_url || null;
        this.previewUrl = this.form.background_image_url || '';
        this.enabled = !!(normalizedColor || data.background_image_url);
      } catch (error) {
        console.error(error);
        this.setStatus('No se pudo cargar la configuración actual.', 'text-danger');
      }
    },
    async onToggleEnabled(nextValue) {
      if (nextValue) {
        return;
      }

      const confirmed = await this.askConfirmation(
        'Al desactivar la personalización se eliminarán el color y la imagen configurados. ¿Deseas continuar?',
        'Desactivar personalización',
        'Sí, desactivar'
      );

      if (!confirmed) {
        this.enabled = true;
        this.setStatus('La personalización se mantiene activa.', 'text-info');
        return;
      }

      await this.resetToDefault(false);
      this.setStatus('Personalización desactivada y restablecida.', 'text-success');
    },
    selectSuggestedColor(color) {
      this.form.background_color = this.normalizeHexColor(color) || '#e4e8f0';
      this.enabled = true;
      this.setStatus('Color sugerido seleccionado. Presiona Guardar para aplicar.', 'text-info');
    },
    onColorPicking(color) {
      const normalized = this.normalizeHexColor(color || '');
      if (!normalized) {
        return;
      }

      this.form.background_color = normalized;
      this.enabled = true;
      this.setStatus('Color seleccionado. Presiona Guardar para aplicar.', 'text-info');
    },
    onColorConfirmed(color) {
      const normalized = this.normalizeHexColor(color || this.form.background_color || '');
      if (!normalized) {
        return;
      }

      this.form.background_color = normalized;
      this.enabled = true;
      this.setStatus('Color actualizado. Presiona Guardar para aplicar.', 'text-info');
    },
    onImageChange(event) {
      const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
      if (!file) {
        return;
      }

      this.fileToUpload = file;
      this.removeCurrentImage = false;
      this.previewUrl = URL.createObjectURL(file);
      this.enabled = true;
      this.setStatus('Imagen seleccionada. Presiona Guardar para aplicar.', 'text-info');
    },
    async saveBackground() {
      this.loading = true;
      const normalizedColor = this.enabled
        ? (this.normalizeHexColor(this.form.background_color || '') || '#e4e8f0')
        : '';

      this.form.background_color = normalizedColor || '#e4e8f0';

      try {
        let response;
        if (this.fileToUpload || this.removeCurrentImage) {
          const payload = new FormData();
          payload.append('background_color', normalizedColor);

          if (this.fileToUpload) {
            payload.append('background_image', this.fileToUpload);
          }

          if (this.removeCurrentImage) {
            payload.append('remove_background_image', '1');
          }

          response = await this.$http.post(this.saveUrl, payload);
        } else {
          response = await this.$http.post(this.saveUrl, {
            background_color: normalizedColor || null,
          });
        }

        const data = response.data || {};
        this.form.background_color = this.normalizeHexColor(data.background_color || this.form.background_color) || '#e4e8f0';
        this.form.background_image_url = data.background_image_url || null;
        this.previewUrl = this.form.background_image_url || '';
        this.fileToUpload = null;
        this.removeCurrentImage = false;
        this.setStatus('Configuración guardada correctamente.', 'text-success');
      } catch (error) {
        console.error(error);
        this.setStatus('No se pudo guardar la configuración.', 'text-danger');
      } finally {
        this.loading = false;
        if (this.$refs.imageInput) {
          this.$refs.imageInput.value = '';
        }
      }
    },
    async removeImage() {
      this.removeCurrentImage = true;
      this.fileToUpload = null;
      this.previewUrl = '';
      this.form.background_image_url = null;
      await this.saveBackground();
    },
    async resetToDefault(showMessage = true) {
      this.form.background_color = '#e4e8f0';
      this.previewUrl = '';
      this.form.background_image_url = null;
      this.fileToUpload = null;
      this.removeCurrentImage = true;

      if (showMessage) {
        this.setStatus('Configuración preparada para restablecer.', 'text-info');
      }

      await this.saveBackground();
    },
  },
};
</script>

<style scoped>
.public-search-background-preview {
  height: 220px;
  border: 1px dashed #cfd8e3;
  border-radius: 10px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  padding: 12px;
}

.public-search-background-preview img {
  width: 100%;
  max-height: 100%;
  object-fit: cover;
  border-radius: 8px;
}

.public-search-media-row > [class*='col-'] {
  display: flex;
  flex-direction: column;
}

.public-search-file-square {
  width: 100%;
  height: 220px;
  border: 2px dashed #cfd8e3;
  border-radius: 12px;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  cursor: pointer;
  padding: 12px;
  transition: border-color .15s ease, background-color .15s ease;
  overflow: hidden;
}

.public-search-file-square:hover {
  border-color: #94a3b8;
  background: #f1f5f9;
}

.public-search-file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

.public-search-file-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.public-search-file-hint {
  margin-top: 8px;
  color: #64748b;
  line-height: 1.4;
  max-width: 100%;
  word-break: break-word;
}

.suggested-colors-grid {
  display: grid;
  grid-template-columns: repeat(8, minmax(28px, 1fr));
  gap: 10px;
}

.suggested-color-btn {
  width: 100%;
  aspect-ratio: 1 / 1;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}

.suggested-color-btn:hover {
  transform: translateY(-1px);
  border-color: #94a3b8;
}

.suggested-color-btn.active {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, .18);
  border-color: #2563eb;
}

.public-search-background-actions-row {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
}

@media (max-width: 767px) {
  .suggested-colors-grid {
    grid-template-columns: repeat(4, minmax(28px, 1fr));
  }

  .public-search-background-preview,
  .public-search-file-square {
    height: 180px;
  }
}
</style>