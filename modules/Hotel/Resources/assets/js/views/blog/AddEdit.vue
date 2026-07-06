<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onOpen"
    top="5vh"
    width="720px"
  >
    <form autocomplete="off" @submit.prevent="onSubmit">
      <div class="form-body">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label class="control-label" for="title">Título</label>
              <el-input
                type="text"
                id="title"
                v-model="form.title"
                :class="{ 'is-invalid': errors.title }"
              />
              <div v-if="errors.title" class="invalid-feedback">
                {{ errors.title[0] }}
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="author">Autor</label>
              <el-input type="text" id="author" v-model="form.author" />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="published_at">Fecha de publicación</label>
              <el-date-picker
                v-model="form.published_at"
                type="date"
                value-format="yyyy-MM-dd"
                format="dd/MM/yyyy"
                placeholder="Selecciona una fecha"
                style="width:100%"
              ></el-date-picker>
            </div>
          </div>
          <div v-if="userType === 'admin'" class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="establishment">Sucursal</label>
              <el-select id="establishment" v-model="form.establishment_id" style="width:100%">
                <el-option
                  v-for="item in establishments"
                  :key="item.id"
                  :value="item.id"
                  :label="item.description"
                >
                  {{ item.description }}
                </el-option>
              </el-select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label">Imagen de portada</label>
          <div class="blog-cover">
            <div class="blog-cover__preview" :style="coverStyle">
              <span v-if="!coverUrl" class="text-muted"><i class="fa fa-image"></i> Sin imagen</span>
            </div>
            <div class="blog-cover__actions">
              <label class="btn btn-sm btn-custom" :class="{ 'disabled': uploadingImage }">
                <i class="fa" :class="uploadingImage ? 'fa-spinner fa-spin' : 'fa-upload'"></i>
                {{ uploadingImage ? "Subiendo..." : "Subir imagen" }}
                <input type="file" accept="image/*" @change="onPickImage" :disabled="uploadingImage" hidden />
              </label>
              <button
                v-if="coverUrl"
                type="button"
                class="btn btn-sm btn-danger ms-2"
                @click="removeImage"
              >
                Quitar
              </button>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label" for="excerpt">Resumen (extracto)</label>
          <el-input
            type="textarea"
            id="excerpt"
            :rows="2"
            v-model="form.excerpt"
            :class="{ 'is-invalid': errors.excerpt }"
            placeholder="Breve descripción que se muestra en el listado del blog"
          />
          <div v-if="errors.excerpt" class="invalid-feedback">
            {{ errors.excerpt[0] }}
          </div>
        </div>

        <div class="form-group">
          <label class="control-label" for="content">Contenido</label>
          <el-input
            type="textarea"
            id="content"
            :rows="8"
            v-model="form.content"
            placeholder="Escribe aquí el contenido de la entrada. Puedes usar saltos de línea."
          />
        </div>

        <div class="form-group">
          <label>Publicar (mostrar en la web)</label>
          <el-switch v-model="form.published"></el-switch>
        </div>

        <div class="row text-center">
          <div class="col-6">
            <el-button class="btn-block second-buton" @click="onClose">Cancelar</el-button>
          </div>
          <div class="col-6">
            <el-button
              native-type="submit"
              :disabled="loading"
              type="primary"
              class="btn-block"
              :loading="loading"
              >Guardar</el-button
            >
          </div>
        </div>
      </div>
    </form>
  </el-dialog>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
      default: false,
    },
    post: {
      type: Object,
      required: false,
      default: null,
    },
    establishments: {
      type: Array,
      required: true,
    },
    userType: {
      type: String,
      required: true,
    },
    establishmentId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      form: {},
      title: "",
      errors: {},
      loading: false,
      uploadingImage: false,
    };
  },
  computed: {
    coverUrl() {
      if (this.form.image && this.form.image.url) {
        return this.form.image.url;
      }
      return this.form.image_url || null;
    },
    coverStyle() {
      return this.coverUrl ? { backgroundImage: `url(${this.coverUrl})` } : {};
    },
  },
  methods: {
    onOpen() {
      this.errors = {};
      if (this.post) {
        this.title = "Editar entrada";
        this.form = {
          title: this.post.title,
          author: this.post.author,
          excerpt: this.post.excerpt,
          content: this.post.content,
          published: this.post.published,
          published_at: this.post.published_at,
          establishment_id: this.post.establishment_id || this.establishmentId,
          image: null,
          image_url: this.post.image_url,
          image_name: this.post.image,
        };
      } else {
        this.title = "Nueva entrada";
        this.form = {
          title: "",
          author: "",
          excerpt: "",
          content: "",
          published: true,
          published_at: this.today(),
          establishment_id: this.establishmentId,
          image: null,
          image_url: null,
          image_name: null,
        };
      }
    },
    today() {
      const d = new Date();
      const m = String(d.getMonth() + 1).padStart(2, "0");
      const day = String(d.getDate()).padStart(2, "0");
      return `${d.getFullYear()}-${m}-${day}`;
    },
    onPickImage(event) {
      const file = (event.target.files || [])[0];
      if (!file) return;
      this.uploadingImage = true;
      const fd = new FormData();
      fd.append("file", file);
      fd.append("type", "image");
      this.$http
        .post("/items/upload", fd, {
          headers: { "Content-Type": "multipart/form-data" },
        })
        .then((response) => {
          const data = response.data && response.data.data ? response.data.data : null;
          if (data && data.temp_path) {
            this.$set(this.form, "image", {
              filename: data.filename,
              temp_path: data.temp_path,
              url: data.temp_image,
            });
            this.form.image_url = data.temp_image;
          }
        })
        .catch((error) => this.axiosError(error))
        .finally(() => {
          this.uploadingImage = false;
          event.target.value = "";
        });
    },
    removeImage() {
      this.form.image = { filename: null, temp_path: null };
      this.form.image_url = null;
      this.form.image_name = null;
    },
    payload() {
      return {
        title: this.form.title,
        author: this.form.author,
        excerpt: this.form.excerpt,
        content: this.form.content,
        published: this.form.published,
        published_at: this.form.published_at,
        establishment_id: this.form.establishment_id,
        // image: objeto {filename, temp_path} para subida nueva, o {filename}
        // para conservar la existente, o null para quitarla.
        image: this.resolveImagePayload(),
      };
    },
    resolveImagePayload() {
      if (this.form.image) {
        // Subida nueva (o borrado explícito con filename null).
        return this.form.image;
      }
      if (this.form.image_name) {
        // Conservar la imagen ya almacenada.
        return { filename: this.form.image_name, temp_path: null };
      }
      return null;
    },
    onStore() {
      this.loading = true;
      this.$http
        .post("/hotels/blog/store", this.payload())
        .then((response) => {
          this.$emit("onAddItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors || {};
          } else {
            this.axiosError(error);
          }
        });
    },
    onUpdate() {
      this.loading = true;
      this.$http
        .put(`/hotels/blog/${this.post.id}/update`, this.payload())
        .then((response) => {
          this.$emit("onUpdateItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.errors = error.response.data.errors || {};
          } else {
            this.axiosError(error);
          }
        });
    },
    onSubmit() {
      if (this.post) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
  },
};
</script>

<style scoped>
.blog-cover {
  display: flex;
  align-items: center;
  gap: 16px;
}
.blog-cover__preview {
  width: 180px;
  height: 120px;
  border-radius: 8px;
  background: #eceff1 center/cover no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px dashed #cfd8dc;
  flex-shrink: 0;
}
</style>
