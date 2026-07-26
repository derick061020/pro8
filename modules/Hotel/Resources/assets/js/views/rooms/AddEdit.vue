<template>
  <el-dialog
    :title="title"
    :visible="visible"
    @close="onClose"
    @open="onCreate"
    width="640px"
    top="6vh"
  >
    <form autocomplete="off" @submit.prevent="onSubmit">
      <div class="row form-body">
        <div class="col-12 form-group mb-0">
          <label class="control-label" for="name">Nombre de la habitación</label>
          <el-input
            type="text"
            id="name"
            v-model="form.name"
            :class="{ 'is-invalid': errors.name }"
          />
          <div v-if="errors.name" class="invalid-feedback">
            {{ errors.name[0] }}
          </div>
        </div>
        <div v-if="userType==='admin'" class="col-12 form-group mb-0">
          <label class="control-label" for="establishment">Sucursal</label>
          <el-select
            type="text"
            id="establishment"
            v-model="form.establishment_id"
            :class="{ 'is-invalid': errors.establishment_id }"
            @change="onChangeEstablishment(form.establishment_id)"
          >
            <el-option v-for="item in establishments" :key="item.id" :value="item.id" :label="item.description">
              {{ item.description }}
            </el-option>
          </el-select>
          <div v-if="errors.establishment_id" class="invalid-feedback">
            {{ errors.establishment_id[0] }}
          </div>
        </div>
        <div class="col-6 form-group mb-0">
          <label class="control-label" for="category">Categoría</label>
          <el-select
            type="text"
            id="category"
            v-model="form.hotel_category_id"
            :class="{ 'is-invalid': errors.hotel_category_id }"
          >
            <el-option v-for="cat in categories" :key="cat.id" :value="cat.id" :label="cat.description">
              {{ cat.description }}
            </el-option>
          </el-select>
          <div v-if="errors.hotel_category_id" class="invalid-feedback">
            {{ errors.hotel_category_id[0] }}
          </div>
        </div>
        <div class="col-6 form-group mb-0">
          <label class="control-label" for="floor">Ubicación</label>
          <el-select
            type="text"
            id="floor"
            v-model="form.hotel_floor_id"
            :class="{ 'is-invalid': errors.hotel_floor_id }"
          >
            <el-option v-for="flo in floors" :key="flo.id" :value="flo.id" :label="flo.description">
              {{ flo.description }}
            </el-option>
          </el-select>
          <div v-if="errors.hotel_floor_id" class="invalid-feedback">
            {{ errors.hotel_floor_id[0] }}
          </div>
        </div>
        <div class="col-12 form-group">
          <label class="control-label" for="description">Detalles</label>
          <el-input
            rows="3"
            type="textarea"
            id="description"
            v-model="form.description"
            :class="{ 'is-invalid': errors.description }"
          >
          </el-input>
          <div v-if="errors.description" class="invalid-feedback">
            {{ errors.description[0] }}
          </div>
        </div>

        <!-- ============ Web de reservas ============ -->
        <div class="col-12 mt-2 mb-2">
          <h6 class="font-weight-bold text-primary" style="border-bottom:1px solid #eee;padding-bottom:6px;">
            <i class="fa fa-globe"></i> Información para la web de reservas
          </h6>
          <small class="text-muted">Estos datos se muestran a tus clientes en la web pública de reservas.</small>
        </div>

        <div class="col-12 form-group mb-2">
          <label class="control-label" for="short_description">Resumen corto</label>
          <el-input
            type="text"
            id="short_description"
            maxlength="255"
            show-word-limit
            placeholder="Ej: Habitación matrimonial con vista al mar y balcón privado"
            v-model="form.short_description"
          />
        </div>

        <div class="col-4 form-group mb-2">
          <label class="control-label" for="capacity">Capacidad (huéspedes)</label>
          <el-input-number id="capacity" v-model="form.capacity" :min="1" :max="50" controls-position="right" style="width:100%" />
        </div>
        <div class="col-4 form-group mb-2">
          <label class="control-label" for="beds">Camas</label>
          <el-input type="text" id="beds" placeholder="Ej: 1 cama queen" v-model="form.beds" />
        </div>
        <div class="col-4 form-group mb-2">
          <label class="control-label" for="size">Tamaño (m²)</label>
          <el-input-number id="size" v-model="form.size" :min="1" :max="9999" controls-position="right" style="width:100%" />
        </div>

        <div class="col-6 form-group mb-2">
          <label class="control-label" for="web_price">Precio personalizado para la web (por noche)</label>
          <el-input-number
            id="web_price"
            v-model="form.web_price"
            :min="0"
            :precision="2"
            :step="1"
            controls-position="right"
            placeholder="Ej: 120.00"
            style="width:100%"
          />
          <small class="text-muted">Opcional. Si lo defines, este precio reemplaza al de las tarifas en la web de reservas. Déjalo en 0 o vacío para usar la tarifa más baja.</small>
        </div>

        <div class="col-12 form-group mb-2">
          <label class="control-label">Amenidades / Servicios</label>
          <el-select
            v-model="form.amenities"
            multiple
            filterable
            allow-create
            default-first-option
            placeholder="Escribe y presiona Enter para agregar (Wi-Fi, TV, Aire acondicionado...)"
            style="width:100%"
          >
            <el-option v-for="a in amenitySuggestions" :key="a" :label="a" :value="a"></el-option>
          </el-select>
        </div>

        <div class="col-12 form-group">
          <label class="control-label d-block">Galería de imágenes</label>
          <div class="room-gallery">
            <div v-for="(img, idx) in form.images" :key="idx" class="room-gallery__item">
              <img :src="img.url" alt="imagen" />
              <button type="button" class="room-gallery__remove" @click="removeImage(idx)" title="Quitar">&times;</button>
            </div>
            <label class="room-gallery__add" :class="{ 'is-loading': uploadingImage }">
              <i class="fa" :class="uploadingImage ? 'fa-spinner fa-spin' : 'fa-plus'"></i>
              <span>{{ uploadingImage ? 'Subiendo...' : 'Agregar' }}</span>
              <input type="file" accept="image/*" multiple @change="onPickImages" :disabled="uploadingImage" hidden />
            </label>
          </div>
          <small class="text-muted">La primera imagen se usa como principal en las tarjetas de la web.</small>
        </div>

        <div class="col-4 form-group">
          <label class="d-block">Mostrar habitación</label>
          <el-switch v-model="form.active"></el-switch>
          <small class="d-block text-muted">Desactiva la habitación en todo el sistema.</small>
        </div>
        <div class="col-4 form-group">
          <label class="d-block">Visible en la web</label>
          <el-switch v-model="form.web_visible"></el-switch>
          <small class="d-block text-muted">Oculta la habitación solo en la web de reservas.</small>
        </div>
        <div class="col-4 form-group">
          <label class="d-block">Destacar en la web</label>
          <el-switch v-model="form.featured"></el-switch>
        </div>

        <div class="col-12 row text-center">
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
    room: {
      type: Object,
      required: false,
      default: {},
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
      categories: [],
      floors: [],
      form: {},
      title: "",
      errors: {},
      loading: false,
      uploadingImage: false,
      amenitySuggestions: [
        "Wi-Fi gratis",
        "TV cable",
        "Smart TV",
        "Aire acondicionado",
        "Calefacción",
        "Agua caliente",
        "Frigobar",
        "Caja fuerte",
        "Desayuno incluido",
        "Baño privado",
        "Balcón",
        "Vista al mar",
        "Escritorio",
        "Secadora de cabello",
        "Servicio a la habitación",
        "Jacuzzi",
        "Estacionamiento",
      ],
    };
  },
  methods: {
    buildPayload() {
      return {
        ...this.form,
        amenities: this.form.amenities || [],
        // Sólo enviamos filename + temp_path; las existentes ya tienen filename.
        images: (this.form.images || []).map((img) => ({
          filename: img.filename,
          temp_path: img.temp_path || null,
        })),
      };
    },
    onUpdate() {
      this.loading = true;
      this.$http
        .put(`/hotels/rooms/${this.room.id}/update`, this.buildPayload())
        .then((response) => {
          this.$emit("onUpdateItem", response.data.data);
          this.onClose();
        })
        .finally(() => {
          this.loading = false;
          this.errors = {};
        })
        .catch((error) => {
          this.axiosError(error);
        });
    },
    onStore() {
      this.loading = true;
      const payload = {
        account_id: null,
        attributes: [],
        brand_id: null,
        calculate_quantity: false,
        category_id: null,
        currency_type_id: "PEN",
        date_of_due: null,
        description: "Habitación " + this.form.name,
        has_igv: true,
        has_isc: false,
        has_perception: false,
        has_plastic_bag_taxes: false,
        id: null,
        image: null,
        image_url: null,
        internal_id: null,
        is_set: false,
        item_code: null,
        item_code_gs1: null,
        item_type_id: "01",
        item_unit_types: [],
        line: null,
        lot_code: null,
        lots: [],
        lots_enabled: false,
        name: "Habitación " + this.form.name,
        percentage_isc: 0,
        percentage_of_profit: 0,
        percentage_perception: 0,
        purchase_affectation_igv_type_id: "10",
        purchase_has_igv: false,
        purchase_unit_price: 0,
        sale_affectation_igv_type_id: "10",
        sale_unit_price: "20",
        second_name: "Habitación " + this.form.name,
        series_enabled: false,
        stock: 0,
        stock_min: 1,
        suggested_price: 0,
        system_isc_type_id: null,
        temp_path: null,
        unit_type_id: "ZZ",
        web_platform_id: null,
      };
      this.$http
        .post("/items", payload)
        .then((response) => {
          this.form.item_id = response.data.id;
          this.$http
            .post("/hotels/rooms/store", this.buildPayload())
            .then((response) => {
              this.$emit("onAddItem", response.data.data);
              this.onClose();
            })
            .finally(() => {
              this.loading = false;
              this.errors = {};
            })
            .catch((error) => {
              this.axiosError(error);
            });
        })
        .catch((error) => this.axiosError(error))
        .finally(() => (this.loading = false));
    },
    onSubmit() {
      if (this.room) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onPickImages(event) {
      const files = Array.from(event.target.files || []);
      if (!files.length) return;
      this.uploadingImage = true;
      const uploads = files.map((file) => {
        const fd = new FormData();
        fd.append("file", file);
        fd.append("type", "image");
        return this.$http
          .post("/items/upload", fd, {
            headers: { "Content-Type": "multipart/form-data" },
          })
          .then((response) => {
            const data = response.data && response.data.data ? response.data.data : null;
            if (data && data.temp_path) {
              this.form.images.push({
                filename: data.filename,
                temp_path: data.temp_path,
                url: data.temp_image,
              });
            }
          });
      });
      Promise.all(uploads)
        .catch((error) => this.axiosError(error))
        .finally(() => {
          this.uploadingImage = false;
          event.target.value = "";
        });
    },
    removeImage(idx) {
      this.form.images.splice(idx, 1);
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      if (this.room) {
        this.form = {
          ...this.room,
          featured: !!this.room.featured,
          web_visible: this.room.web_visible === undefined ? true : !!this.room.web_visible,
          capacity: this.room.capacity || undefined,
          size: this.room.size || undefined,
          web_price: this.room.web_price ? Number(this.room.web_price) : undefined,
          amenities: Array.isArray(this.room.amenities) ? [...this.room.amenities] : [],
          images: this.buildExistingImages(this.room),
        };
        this.title = "Editar habitación";
      } else {
        this.title = "Crear habitación";
        this.form = {
          active: true,
          featured: false,
          web_visible: true,
          establishment_id: this.establishmentId,
          amenities: [],
          images: [],
        };
      }
      this.getTables(this.form.establishment_id || this.establishmentId);
    },
    buildExistingImages(room) {
      const names = Array.isArray(room.images) ? room.images : [];
      const urls = Array.isArray(room.image_urls) ? room.image_urls : [];
      return names.map((filename, i) => ({
        filename,
        temp_path: null,
        url: urls[i] || filename,
      }));
    },
    getTables(establishment_id) {
      this.$http.get(`/hotels/rooms/tables/${establishment_id}`).then((response) => {
        this.floors = response.data.floors;
        this.categories = response.data.categories;
      });
    },
    onChangeEstablishment(id){
      this.getTables(id)
      this.form.hotel_floor_id = '';
      this.form.hotel_category_id = '';
    }

  },
};
</script>

<style scoped>
.room-gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 6px;
}
.room-gallery__item {
  position: relative;
  width: 90px;
  height: 90px;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #e4e7ed;
  background: #f5f7fa;
}
.room-gallery__item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.room-gallery__remove {
  position: absolute;
  top: 2px;
  right: 2px;
  width: 20px;
  height: 20px;
  line-height: 18px;
  border: none;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  font-size: 15px;
  cursor: pointer;
  padding: 0;
}
.room-gallery__add {
  width: 90px;
  height: 90px;
  border: 1px dashed #c0c4cc;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #909399;
  font-size: 12px;
  margin: 0;
  transition: border-color 0.2s, color 0.2s;
}
.room-gallery__add:hover {
  border-color: #409eff;
  color: #409eff;
}
.room-gallery__add i {
  font-size: 18px;
  margin-bottom: 4px;
}
.room-gallery__add.is-loading {
  pointer-events: none;
  opacity: 0.7;
}
</style>
