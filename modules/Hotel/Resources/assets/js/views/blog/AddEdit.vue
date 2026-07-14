<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onOpen"
    top="4vh"
    width="860px"
    custom-class="blog-editor-dialog"
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

        <!-- Portada: imagen o video -->
        <div class="form-group">
          <label class="control-label">Portada</label>
          <el-radio-group v-model="form.cover_type" size="small" class="mb-2">
            <el-radio-button label="image"><i class="fa fa-image"></i> Imagen</el-radio-button>
            <el-radio-button label="video"><i class="fa fa-youtube-play"></i> Video</el-radio-button>
          </el-radio-group>

          <!-- Portada tipo imagen -->
          <div v-show="form.cover_type === 'image'" class="blog-cover">
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

          <!-- Portada tipo video -->
          <div v-show="form.cover_type === 'video'">
            <el-input
              type="text"
              v-model="form.video_url"
              placeholder="Pega la URL del video (YouTube, Vimeo) o el código <iframe> del embed"
            >
              <template slot="prepend"><i class="fa fa-link"></i></template>
            </el-input>
            <p class="cover-hint">
              Ej: https://www.youtube.com/watch?v=... · https://youtu.be/... · https://vimeo.com/...
            </p>
            <div v-if="videoEmbedUrl" class="video-embed">
              <iframe
                :src="videoEmbedUrl"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
            <div v-else-if="form.video_url" class="video-embed video-embed--empty">
              <span class="text-muted"><i class="fa fa-exclamation-circle"></i> No se pudo reconocer el video. Verifica la URL.</span>
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
          <div class="ckeditor-wrap">
            <vue-ckeditor
              v-if="visible && editorReady"
              ref="editor"
              :key="editorKey"
              :editors="editors"
              type="classic"
              v-model="form.content"
              :config="editorConfig"
              @ready="applyInitialEditorContent"
            />
          </div>
          <p class="cover-hint">
            Usa la barra de herramientas para dar formato: títulos, negritas, listas, enlaces, imágenes, tablas y más.
          </p>
        </div>

        <div class="form-group">
          <label>Publicar (mostrar en la web)</label>
          <el-switch v-model="form.published"></el-switch>
        </div>

        <div class="row text-center align-items-center">
          <div class="col-4">
            <el-button class="btn-block second-buton" @click="onClose">Cancelar</el-button>
          </div>
          <div class="col-4">
            <el-button class="btn-block" icon="el-icon-view" @click="openPreview">Previsualizar</el-button>
          </div>
          <div class="col-4">
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

    <!-- Previsualización tal como se verá en la web -->
    <el-dialog
      title="Vista previa"
      :visible.sync="previewVisible"
      width="820px"
      top="4vh"
      append-to-body
      custom-class="blog-preview-dialog"
    >
      <div class="blog-preview">
        <div v-if="form.cover_type === 'video' && videoEmbedUrl" class="blog-preview__video">
          <iframe
            :src="videoEmbedUrl"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
          ></iframe>
        </div>
        <div
          v-else-if="coverUrl"
          class="blog-preview__cover"
          :style="{ backgroundImage: `url(${coverUrl})` }"
        ></div>

        <h1 class="blog-preview__title">{{ form.title || "Título de la entrada" }}</h1>
        <div class="blog-preview__meta">
          <span v-if="form.author"><i class="fa fa-user"></i> {{ form.author }}</span>
          <span v-if="form.published_at"><i class="fa fa-calendar"></i> {{ formatDate(form.published_at) }}</span>
        </div>
        <p v-if="form.excerpt" class="blog-preview__lead">{{ form.excerpt }}</p>
        <div class="blog-preview__content" v-html="form.content || '<p class=\'text-muted\'>Sin contenido todavía.</p>'"></div>
      </div>
    </el-dialog>
  </el-dialog>
</template>

<script>
import "ckeditor5/ckeditor5.css";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import CKEditor from "vue-ckeditor5";

export default {
  components: {
    "vue-ckeditor": CKEditor.component,
  },
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
      previewVisible: false,
      editorReady: false,
      editorKey: 0,
      editors: {
        classic: ClassicEditor,
      },
      editorConfig: {
        licenseKey: "GPL",
        toolbar: {
          items: [
            "heading",
            "|",
            "bold",
            "italic",
            "link",
            "bulletedList",
            "numberedList",
            "|",
            "outdent",
            "indent",
            "|",
            "blockQuote",
            "insertTable",
            "mediaEmbed",
            "|",
            "undo",
            "redo",
            "|",
            "sourceEditing",
          ],
          shouldNotGroupWhenFull: true,
        },
        table: {
          contentToolbar: ["tableColumn", "tableRow", "mergeTableCells"],
        },
      },
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
    videoEmbedUrl() {
      return this.toEmbedUrl(this.form.video_url);
    },
  },
  methods: {
    onOpen() {
      this.errors = {};
      this.previewVisible = false;
      // El editor se monta recién después de poblar el form, para que
      // vue-ckeditor lea el contenido correcto en su create()/mounted().
      this.editorReady = false;
      this.editorKey += 1;
      if (this.post) {
        this.title = "Editar entrada";
        this.form = {
          title: this.post.title,
          author: this.post.author,
          excerpt: this.post.excerpt,
          content: this.post.content || "",
          cover_type: this.post.cover_type || "image",
          video_url: this.post.video_url || "",
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
          cover_type: "image",
          video_url: "",
          published: true,
          published_at: this.today(),
          establishment_id: this.establishmentId,
          image: null,
          image_url: null,
          image_name: null,
        };
      }
      // Ahora que form.content ya está asignado, montamos el editor.
      this.$nextTick(() => {
        this.editorReady = true;
        // Respaldo: vue-ckeditor no siempre aplica el valor inicial al crearse,
        // así que forzamos el contenido en cuanto la instancia esté disponible.
        this.applyInitialEditorContent();
      });
    },
    // Fuerza el contenido dentro del editor CKEditor. La instancia se crea de
    // forma asíncrona, por eso reintentamos hasta que esté lista.
    applyInitialEditorContent(retries = 40) {
      if (retries <= 0) return;
      const editor = this.$refs.editor;
      if (!editor || !editor.instance) {
        setTimeout(() => this.applyInitialEditorContent(retries - 1), 25);
        return;
      }
      const value = this.form.content || "";
      if (editor.instance.getData() !== value) {
        editor.instance.setData(value);
      }
    },
    today() {
      const d = new Date();
      const m = String(d.getMonth() + 1).padStart(2, "0");
      const day = String(d.getDate()).padStart(2, "0");
      return `${d.getFullYear()}-${m}-${day}`;
    },
    formatDate(value) {
      if (!value) return "";
      const parts = String(value).split("-");
      if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
      }
      return value;
    },
    // Normaliza una URL de video a su forma incrustable (espejo de la lógica del modelo PHP).
    toEmbedUrl(url) {
      url = (url || "").trim();
      if (!url) return null;

      const iframeMatch = url.match(/<iframe[^>]*\ssrc=["']([^"']+)["']/i);
      if (iframeMatch) {
        url = iframeMatch[1];
      }

      const ytId = this.extractYoutubeId(url);
      if (ytId) {
        return `https://www.youtube.com/embed/${ytId}`;
      }

      const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
      if (vimeo) {
        return `https://player.vimeo.com/video/${vimeo[1]}`;
      }

      if (/^https?:\/\//i.test(url)) {
        return url;
      }
      return null;
    },
    extractYoutubeId(url) {
      const patterns = [
        /youtube\.com\/watch\?(?:.*&)?v=([\w-]{11})/i,
        /youtu\.be\/([\w-]{11})/i,
        /youtube\.com\/embed\/([\w-]{11})/i,
        /youtube\.com\/shorts\/([\w-]{11})/i,
        /youtube\.com\/v\/([\w-]{11})/i,
      ];
      for (const p of patterns) {
        const m = url.match(p);
        if (m) return m[1];
      }
      return null;
    },
    openPreview() {
      this.previewVisible = true;
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
        cover_type: this.form.cover_type || "image",
        video_url: this.form.cover_type === "video" ? this.form.video_url : null,
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
      this.editorReady = false;
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
.cover-hint {
  font-size: 12px;
  color: #90a4ae;
  margin: 6px 0 0;
}
.video-embed {
  position: relative;
  width: 100%;
  max-width: 480px;
  padding-top: 270px;
  margin-top: 12px;
  border-radius: 8px;
  overflow: hidden;
  background: #000;
}
.video-embed iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.video-embed--empty {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 0;
  height: 120px;
  background: #f5f7f8;
  border: 1px dashed #cfd8dc;
}
.ckeditor-wrap :deep(.ck-editor__editable) {
  min-height: 260px;
}

/* Vista previa */
.blog-preview {
  color: #3a4b57;
}
.blog-preview__video {
  position: relative;
  width: 100%;
  padding-top: 56.25%;
  border-radius: 8px;
  overflow: hidden;
  background: #000;
  margin-bottom: 22px;
}
.blog-preview__video iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.blog-preview__cover {
  width: 100%;
  height: 340px;
  background: #eceff1 center/cover no-repeat;
  border-radius: 8px;
  margin-bottom: 22px;
}
.blog-preview__title {
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 10px;
  color: #2c3e50;
}
.blog-preview__meta {
  color: #90a4ae;
  font-size: 13px;
  margin-bottom: 18px;
}
.blog-preview__meta span {
  margin-right: 16px;
}
.blog-preview__lead {
  font-size: 18px;
  font-weight: 600;
  color: #55707f;
  margin-bottom: 18px;
}
.blog-preview__content {
  font-size: 16px;
  line-height: 1.8;
}
.blog-preview__content :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 6px;
}
.blog-preview__content :deep(table) {
  border-collapse: collapse;
  width: 100%;
}
.blog-preview__content :deep(table td),
.blog-preview__content :deep(table th) {
  border: 1px solid #dbe2e6;
  padding: 8px 10px;
}
</style>
