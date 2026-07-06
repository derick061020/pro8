<template>
  <div class="landing-settings">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
              <h5 class="mb-0"><i class="fa fa-globe"></i> Personalizar web de reservas</h5>
              <small class="text-muted">Configura el slider, la galería, los textos y todo lo que ven tus clientes.</small>
            </div>
            <div class="ls-toolbar">
              <el-select
                v-if="userType === 'admin'"
                v-model="currentEstablishment"
                size="small"
                style="width:190px"
                @change="load"
              >
                <el-option
                  v-for="e in establishments"
                  :key="e.id"
                  :value="e.id"
                  :label="e.description"
                >{{ e.description }}</el-option>
              </el-select>
              <a v-if="landingUrl" :href="landingUrl" target="_blank" class="btn btn-sm btn-default">
                <i class="fa fa-external-link"></i> Ver web
              </a>
              <el-button type="primary" size="small" :loading="saving" @click="save">
                <i class="fa fa-save"></i> Guardar cambios
              </el-button>
            </div>
          </div>

          <div class="card-body">
            <div v-if="loading" class="text-center text-muted" style="padding:60px 0;">
              <i class="fa fa-spinner fa-spin fa-2x"></i>
              <p style="margin-top:12px;">Cargando configuración…</p>
            </div>

            <el-tabs v-else v-model="tab" type="border-card">
              <!-- ============ SLIDER ============ -->
              <el-tab-pane label="Slider" name="slider">
                <p class="text-muted">Diapositivas del banner principal. Recomendado 1700×449 px.</p>
                <div v-for="(slide, i) in form.slides" :key="'slide'+i" class="ls-item">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="ls-img" :style="bg(slide.image_url)">
                        <span v-if="!slide.image_url" class="ls-img__empty"><i class="fa fa-image"></i></span>
                      </div>
                      <div class="ls-img__actions">
                        <label class="btn btn-xs btn-custom">
                          <i class="fa fa-upload"></i> Imagen
                          <input type="file" accept="image/*" hidden @change="pickImage($event, slide, 'image')" />
                        </label>
                        <button v-if="slide.image" type="button" class="btn btn-xs btn-danger" @click="clearImage(slide, 'image', DEF.slide)">Quitar</button>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="form-group">
                        <label>Título</label>
                        <el-input v-model="slide.title" placeholder="Ej: Bienvenido (vacío = nombre del hotel)" />
                      </div>
                      <div class="form-group">
                        <label>Subtítulo</label>
                        <el-input v-model="slide.subtitle" />
                      </div>
                      <div class="row">
                        <div class="col-md-6 form-group">
                          <label>Texto del botón</label>
                          <el-input v-model="slide.button_text" />
                        </div>
                        <div class="col-md-6 form-group">
                          <label>Enlace del botón</label>
                          <el-input v-model="slide.button_link" placeholder="#rooms-results" />
                        </div>
                      </div>
                      <div class="ls-row-flex">
                        <el-switch v-model="slide.stars" active-text="Mostrar estrellas" />
                        <button type="button" class="btn btn-xs btn-danger" @click="remove(form.slides, i)">
                          <i class="fa fa-trash"></i> Eliminar diapositiva
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <el-button size="small" @click="addSlide"><i class="fa fa-plus"></i> Añadir diapositiva</el-button>
              </el-tab-pane>

              <!-- ============ VENTAJAS ============ -->
              <el-tab-pane label="Ventajas" name="features">
                <div class="row">
                  <div class="col-md-8 form-group">
                    <label>Título de la sección</label>
                    <el-input v-model="form.features_heading" />
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Mostrar sección</label><br>
                    <el-switch v-model="form.show_features" />
                  </div>
                </div>
                <div v-for="(f, i) in form.features" :key="'f'+i" class="ls-item">
                  <div class="row">
                    <div class="col-md-3 form-group">
                      <label>Icono</label>
                      <el-select v-model="f.icon" filterable style="width:100%">
                        <el-option v-for="ic in icons" :key="ic" :value="ic" :label="ic">
                          <i class="fa" :class="ic"></i> {{ ic }}
                        </el-option>
                      </el-select>
                    </div>
                    <div class="col-md-9 form-group">
                      <label>Título</label>
                      <el-input v-model="f.title" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Descripción</label>
                    <el-input type="textarea" :rows="2" v-model="f.text" />
                  </div>
                  <div class="row">
                    <div class="col-md-5 form-group">
                      <label>Texto del enlace</label>
                      <el-input v-model="f.link_text" />
                    </div>
                    <div class="col-md-5 form-group">
                      <label>Enlace</label>
                      <el-input v-model="f.link" />
                    </div>
                    <div class="col-md-2 form-group" style="padding-top:28px;">
                      <button type="button" class="btn btn-xs btn-danger btn-block" @click="remove(form.features, i)"><i class="fa fa-trash"></i></button>
                    </div>
                  </div>
                </div>
                <el-button size="small" @click="addFeature"><i class="fa fa-plus"></i> Añadir ventaja</el-button>
              </el-tab-pane>

              <!-- ============ GALERÍA ============ -->
              <el-tab-pane label="Galería" name="gallery">
                <div class="row">
                  <div class="col-md-8 form-group">
                    <label>Título de la sección</label>
                    <el-input v-model="form.gallery_heading" />
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Mostrar sección</label><br>
                    <el-switch v-model="form.show_gallery" />
                  </div>
                </div>
                <p class="text-muted">Imágenes del carrusel de galería. Recomendado 800×504 px.</p>
                <div class="ls-gallery">
                  <div v-for="(g, i) in form.gallery" :key="'g'+i" class="ls-gallery__cell">
                    <div class="ls-img" :style="bg(g.url)">
                      <span v-if="!g.url" class="ls-img__empty"><i class="fa fa-image"></i></span>
                    </div>
                    <div class="ls-img__actions">
                      <label class="btn btn-xs btn-custom">
                        <i class="fa fa-upload"></i>
                        <input type="file" accept="image/*" hidden @change="pickGallery($event, g)" />
                      </label>
                      <button type="button" class="btn btn-xs btn-danger" @click="remove(form.gallery, i)"><i class="fa fa-trash"></i></button>
                    </div>
                  </div>
                </div>
                <el-button size="small" @click="addGallery"><i class="fa fa-plus"></i> Añadir imagen</el-button>
              </el-tab-pane>

              <!-- ============ TESTIMONIOS ============ -->
              <el-tab-pane label="Testimonios" name="testimonials">
                <div class="row">
                  <div class="col-md-8 form-group">
                    <label>Título de la sección</label>
                    <el-input v-model="form.testimonials_heading" />
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Mostrar sección</label><br>
                    <el-switch v-model="form.show_testimonials" />
                  </div>
                </div>
                <div v-for="(t, i) in form.testimonials" :key="'t'+i" class="ls-item">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="ls-img ls-img--round" :style="bg(t.image_url)">
                        <span v-if="!t.image_url" class="ls-img__empty"><i class="fa fa-user"></i></span>
                      </div>
                      <div class="ls-img__actions">
                        <label class="btn btn-xs btn-custom">
                          <i class="fa fa-upload"></i> Foto
                          <input type="file" accept="image/*" hidden @change="pickImage($event, t, 'image')" />
                        </label>
                        <button v-if="t.image" type="button" class="btn btn-xs btn-danger" @click="clearImage(t, 'image', DEF.review)">Quitar</button>
                      </div>
                    </div>
                    <div class="col-md-9">
                      <div class="form-group">
                        <label>Comentario</label>
                        <el-input type="textarea" :rows="2" v-model="t.text" />
                      </div>
                      <div class="ls-row-flex">
                        <el-input v-model="t.name" placeholder="Nombre y habitación (Ej: María G., Doble)" />
                        <button type="button" class="btn btn-xs btn-danger" @click="remove(form.testimonials, i)"><i class="fa fa-trash"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
                <el-button size="small" @click="addTestimonial"><i class="fa fa-plus"></i> Añadir testimonio</el-button>
              </el-tab-pane>

              <!-- ============ PARALLAX ============ -->
              <el-tab-pane label="Banner destacado" name="parallax">
                <div class="form-group">
                  <label>Mostrar sección</label><br>
                  <el-switch v-model="form.show_parallax" />
                </div>
                <div class="row">
                  <div class="col-md-5">
                    <div class="ls-img ls-img--wide" :style="bg(form.parallax.image_url)">
                      <span v-if="!form.parallax.image_url" class="ls-img__empty"><i class="fa fa-image"></i></span>
                    </div>
                    <div class="ls-img__actions">
                      <label class="btn btn-xs btn-custom">
                        <i class="fa fa-upload"></i> Imagen de fondo
                        <input type="file" accept="image/*" hidden @change="pickImage($event, form.parallax, 'image')" />
                      </label>
                      <button v-if="form.parallax.image" type="button" class="btn btn-xs btn-danger" @click="clearImage(form.parallax, 'image', DEF.parallax)">Quitar</button>
                    </div>
                    <small class="text-muted">Recomendado 1900×911 px.</small>
                  </div>
                  <div class="col-md-7">
                    <div class="form-group">
                      <label>Texto</label>
                      <el-input v-model="form.parallax.text" />
                    </div>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label>Texto del botón</label>
                        <el-input v-model="form.parallax.button_text" />
                      </div>
                      <div class="col-md-6 form-group">
                        <label>Enlace del botón</label>
                        <el-input v-model="form.parallax.button_link" />
                      </div>
                    </div>
                  </div>
                </div>
              </el-tab-pane>

              <!-- ============ SOBRE EL HOTEL ============ -->
              <el-tab-pane label="Sobre el hotel" name="about">
                <div class="row">
                  <div class="col-md-8 form-group">
                    <label>Título de la sección</label>
                    <el-input v-model="form.about_heading" />
                  </div>
                  <div class="col-md-4 form-group">
                    <label>Mostrar sección</label><br>
                    <el-switch v-model="form.show_about" />
                  </div>
                </div>
                <div class="form-group">
                  <div class="ls-img ls-img--wide" :style="bg(form.about.image_url)" style="max-width:260px;">
                    <span v-if="!form.about.image_url" class="ls-img__empty"><i class="fa fa-image"></i></span>
                  </div>
                  <div class="ls-img__actions">
                    <label class="btn btn-xs btn-custom">
                      <i class="fa fa-upload"></i> Imagen
                      <input type="file" accept="image/*" hidden @change="pickImage($event, form.about, 'image')" />
                    </label>
                    <button v-if="form.about.image" type="button" class="btn btn-xs btn-danger" @click="clearImage(form.about, 'image', DEF.about)">Quitar</button>
                  </div>
                </div>
                <div v-for="(t, i) in form.about.tabs" :key="'ab'+i" class="ls-item">
                  <div class="ls-row-flex">
                    <el-input v-model="t.title" placeholder="Título de la pestaña" style="max-width:260px;" />
                    <button type="button" class="btn btn-xs btn-danger" @click="remove(form.about.tabs, i)"><i class="fa fa-trash"></i></button>
                  </div>
                  <div class="form-group" style="margin-top:8px;">
                    <el-input type="textarea" :rows="3" v-model="t.content" />
                  </div>
                </div>
                <el-button size="small" @click="addAboutTab"><i class="fa fa-plus"></i> Añadir pestaña</el-button>
              </el-tab-pane>

              <!-- ============ TEXTOS Y APARIENCIA ============ -->
              <el-tab-pane label="Textos y apariencia" name="general">
                <h6 class="ls-subtitle">Sección de habitaciones</h6>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label>Título</label>
                    <el-input v-model="form.rooms_heading" />
                  </div>
                  <div class="col-md-6 form-group">
                    <label>Subtítulo</label>
                    <el-input v-model="form.rooms_subheading" />
                  </div>
                </div>

                <h6 class="ls-subtitle">Llamada a la acción (final)</h6>
                <div class="row">
                  <div class="col-md-8 form-group">
                    <label>Texto</label>
                    <el-input v-model="form.cta_text" />
                  </div>
                  <div class="col-md-2 form-group">
                    <label>Botón</label>
                    <el-input v-model="form.cta_button" />
                  </div>
                  <div class="col-md-2 form-group">
                    <label>Mostrar</label><br>
                    <el-switch v-model="form.show_cta" />
                  </div>
                </div>

                <h6 class="ls-subtitle">Apariencia</h6>
                <div class="row">
                  <div class="col-md-4 form-group">
                    <label>Color del tema</label>
                    <el-select v-model="form.color" style="width:100%">
                      <el-option v-for="c in colors" :key="c" :value="c" :label="colorLabel(c)">
                        <span class="ls-color-dot" :style="{ background: colorHex(c) }"></span> {{ colorLabel(c) }}
                      </el-option>
                    </el-select>
                  </div>
                </div>
              </el-tab-pane>
            </el-tabs>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    establishments: { type: Array, default: () => [] },
    userType: { type: String, default: "user" },
    establishmentId: { type: Number, default: 0 },
  },
  data() {
    return {
      tab: "slider",
      loading: true,
      saving: false,
      form: {},
      colors: [],
      landingUrl: null,
      currentEstablishment: this.establishmentId,
      DEF: {
        slide: "/landing-reservas/images/slides/1700x449.gif",
        parallax: "/landing-reservas/images/parallax/1900x911.gif",
        gallery: "/landing-reservas/images/gallery/800x504.gif",
        review: "/landing-reservas/images/reviews/100x100.gif",
        about: "/landing-reservas/images/tab/197x147.gif",
      },
      icons: [
        "fa-calendar-check-o", "fa-credit-card", "fa-bed", "fa-headphones",
        "fa-wifi", "fa-coffee", "fa-car", "fa-map-marker", "fa-star",
        "fa-shield", "fa-clock-o", "fa-cutlery", "fa-tv", "fa-snowflake-o",
        "fa-suitcase", "fa-thumbs-up", "fa-heart", "fa-key", "fa-gift",
      ],
    };
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      this.loading = true;
      const params = this.userType === "admin" ? { establishment_id: this.currentEstablishment } : {};
      this.$http
        .get("/hotels/landing-settings/data", { params })
        .then((res) => {
          this.landingUrl = res.data.landing_url;
          this.colors = res.data.colors || [];
          this.form = this.normalize(res.data.config);
        })
        .catch((e) => this.axiosError(e))
        .finally(() => { this.loading = false; });
    },
    // Convierte la config del servidor a la estructura interna (galería como
    // objetos {image, url} para poder editarla cómodamente).
    normalize(cfg) {
      const galleryUrls = cfg.gallery_urls || [];
      const gallery = (cfg.gallery || []).map((img, i) => ({
        image: img || null,
        url: galleryUrls[i] || null,
      }));
      return { ...cfg, gallery };
    },
    bg(url) {
      return url ? { backgroundImage: `url('${url}')` } : {};
    },
    // -------- subida de imágenes --------
    upload(file) {
      const fd = new FormData();
      fd.append("file", file);
      fd.append("type", "image");
      return this.$http
        .post("/items/upload", fd, { headers: { "Content-Type": "multipart/form-data" } })
        .then((r) => (r.data && r.data.data ? r.data.data : null));
    },
    pickImage(event, target, key) {
      const file = (event.target.files || [])[0];
      if (!file) return;
      this.upload(file).then((data) => {
        if (data && data.temp_path) {
          this.$set(target, key, { filename: data.filename, temp_path: data.temp_path });
          this.$set(target, key + "_url", data.temp_image);
        }
      }).catch((e) => this.axiosError(e)).finally(() => { event.target.value = ""; });
    },
    pickGallery(event, cell) {
      const file = (event.target.files || [])[0];
      if (!file) return;
      this.upload(file).then((data) => {
        if (data && data.temp_path) {
          cell.image = { filename: data.filename, temp_path: data.temp_path };
          cell.url = data.temp_image;
        }
      }).catch((e) => this.axiosError(e)).finally(() => { event.target.value = ""; });
    },
    clearImage(target, key, fallback) {
      this.$set(target, key, null);
      this.$set(target, key + "_url", fallback);
    },
    // -------- añadir / quitar --------
    remove(list, i) { list.splice(i, 1); },
    addSlide() {
      this.form.slides.push({ image: null, image_url: this.DEF.slide, title: "", subtitle: "", button_text: "Ver habitaciones", button_link: "#rooms-results", stars: true });
    },
    addFeature() {
      this.form.features.push({ icon: "fa-star", title: "", text: "", link_text: "Ver más", link: "#" });
    },
    addGallery() {
      this.form.gallery.push({ image: null, url: this.DEF.gallery });
    },
    addTestimonial() {
      this.form.testimonials.push({ name: "", text: "", image: null, image_url: this.DEF.review });
    },
    addAboutTab() {
      this.form.about.tabs.push({ title: "", content: "" });
    },
    colorHex(c) {
      const map = { turquoise: "#1abc9c", blue: "#3498db", green: "#2ecc71", orange: "#e67e22", purple: "#9b59b6", red: "#e74c3c", brown: "#8d6e63", black: "#2c3e50" };
      return map[c] || "#1abc9c";
    },
    colorLabel(c) {
      const map = { turquoise: "Turquesa", blue: "Azul", green: "Verde", orange: "Naranja", purple: "Morado", red: "Rojo", brown: "Marrón", black: "Negro" };
      return map[c] || c;
    },
    // -------- guardar --------
    payload() {
      // Clonar y limpiar los campos sólo-preview antes de enviar.
      const c = JSON.parse(JSON.stringify(this.form));
      (c.slides || []).forEach((s) => delete s.image_url);
      (c.testimonials || []).forEach((t) => delete t.image_url);
      if (c.parallax) delete c.parallax.image_url;
      if (c.about) delete c.about.image_url;
      // Galería: de objetos {image,url} a array de referencias.
      c.gallery = (this.form.gallery || []).map((g) => g.image || null);
      delete c.gallery_urls;
      return c;
    },
    save() {
      this.saving = true;
      const body = { config: this.payload() };
      if (this.userType === "admin") body.establishment_id = this.currentEstablishment;
      this.$http
        .post("/hotels/landing-settings/update", body)
        .then((res) => {
          this.$message.success(res.data.message || "Web actualizada.");
          this.form = this.normalize(res.data.config);
        })
        .catch((e) => this.axiosError(e))
        .finally(() => { this.saving = false; });
    },
  },
};
</script>

<style scoped>
.landing-settings .card-header { gap: 12px; }
.ls-toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.ls-item { border: 1px solid #eceff1; border-radius: 8px; padding: 16px; margin-bottom: 16px; background: #fafbfc; }
.ls-img { width: 100%; height: 120px; border-radius: 6px; background: #eceff1 center/cover no-repeat; display: flex; align-items: center; justify-content: center; border: 1px dashed #cfd8dc; }
.ls-img--round { width: 90px; height: 90px; border-radius: 50%; margin: 0 auto; }
.ls-img--wide { height: 150px; }
.ls-img__empty { color: #b0bec5; font-size: 26px; }
.ls-img__actions { display: flex; gap: 6px; justify-content: center; margin-top: 8px; }
.ls-row-flex { display: flex; align-items: center; gap: 10px; }
.ls-gallery { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 14px; }
.ls-gallery__cell { width: 170px; }
.ls-subtitle { margin: 18px 0 10px; font-weight: 700; color: #2c3e50; border-bottom: 1px solid #eceff1; padding-bottom: 6px; }
.ls-color-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
.form-group label { font-weight: 600; font-size: 13px; }
</style>
