<template>
  <div>
    <div class="page-header pe-0">
      <h2>
        <a href="/hotels/blog">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-news"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" /><path d="M8 8l4 0" /><path d="M8 12l4 0" /><path d="M8 16l4 0" /></svg>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>Blog de la web de reservas</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <div class="btn-group flex-wrap">
          <button
            type="button"
            class="btn btn-custom btn-sm mt-2 me-2"
            @click="onCreate"
          >
            <i class="fa fa-plus-circle"></i> Nueva entrada
          </button>
        </div>
      </div>
    </div>
    <div class="card tab-content-default row-new mb-0">
      <div class="card-body">
        <div class="row">
          <div v-if="userType === 'admin'" class="col-12 col-md-3 mb-3">
            <div class="form-group">
              <select
                class="form-control form-control-default"
                v-model="filter.establishment_id"
                @change="onFilter"
              >
                <option value="">Todas las sucursales</option>
                <option v-for="item in establishments" :key="item.id" :value="item.id">
                  {{ item.description }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-12 col-md-4 mb-3">
            <div class="form-group">
              <input
                type="text"
                class="form-control form-control-default"
                placeholder="Buscar por título..."
                v-model="filter.title"
                @input="onFilter"
              />
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th style="width:64px;"></th>
                <th>Título</th>
                <th>Autor</th>
                <th>Publicación</th>
                <th class="text-end">Visible</th>
                <th>Sucursal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in items" :key="item.id">
                <td>
                  <div style="position:relative;width:56px;">
                    <img
                      v-if="item.cover_image || item.image_url"
                      :src="item.cover_image || item.image_url"
                      alt=""
                      style="width:56px;height:42px;object-fit:cover;border-radius:5px;"
                    />
                    <span v-else class="text-muted"><i class="fa fa-image"></i></span>
                    <i
                      v-if="item.cover_type === 'video'"
                      class="fa fa-play-circle"
                      style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:18px;text-shadow:0 1px 3px rgba(0,0,0,.6);"
                    ></i>
                  </div>
                </td>
                <td>{{ item.title }}</td>
                <td>{{ item.author || '—' }}</td>
                <td>{{ item.published_at || '—' }}</td>
                <td class="text-end">
                  <span v-if="item.published">Sí</span>
                  <span v-else>No</span>
                </td>
                <td>{{ item.establishment_name }}</td>
                <td class="text-end">
                  <el-button class="me-2 btn-info btn btn-sm" type="info" @click="onEdit(item)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                  </el-button>
                  <el-button class="btn btn-sm" type="danger" @click="onDelete(item)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                  </el-button>
                </td>
              </tr>
              <tr v-if="!items.length">
                <td colspan="7" class="text-center text-muted py-4">No hay entradas de blog registradas.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="text-center">
          <el-pagination
            :page-size="paginator.per_page"
            :page-count="paginator.size"
            layout="prev, pager, next"
            :total="paginator.total"
            :current-page.sync="filter.page"
            @current-change="onFilter"
          >
          </el-pagination>
        </div>
      </div>
    </div>
    <ModalAddEdit
      :visible.sync="openModalAddEdit"
      @onAddItem="onAddItem"
      @onUpdateItem="onUpdateItem"
      :post="post"
      :establishments="establishments"
      :user-type="userType"
      :establishment-id="establishmentId"
    ></ModalAddEdit>
  </div>
</template>

<script>
import ModalAddEdit from "./AddEdit.vue";

export default {
  props: {
    posts: {
      type: Object,
      required: true,
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
  components: {
    ModalAddEdit,
  },
  data() {
    return {
      items: [],
      post: null,
      openModalAddEdit: false,
      loading: false,
      searchTimer: null,
      filter: {
        establishment_id: "",
        title: "",
        page: 1,
      },
      paginator: {
        size: 1,
        total: 1,
        per_page: 1,
      },
    };
  },
  mounted() {
    this.items = this.posts.data;
    this.paginator.size = this.posts.last_page;
    this.paginator.total = this.posts.total;
    this.paginator.per_page = this.posts.per_page;
    this.filter.establishment_id = this.establishmentId;
  },
  methods: {
    onDelete(item) {
      this.$confirm(`¿estás seguro de eliminar la entrada "${item.title}"?`, "Atención", {
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "No, cerrar",
        type: "warning",
      })
        .then(() => {
          this.$http
            .delete(`/hotels/blog/${item.id}/delete`)
            .then((response) => {
              this.$message({ type: "success", message: response.data.message });
              this.items = this.items.filter((i) => i.id !== item.id);
            })
            .catch((error) => {
              this.axiosError(error);
            });
        })
        .catch();
    },
    onEdit(item) {
      this.post = { ...item };
      this.openModalAddEdit = true;
    },
    onCreate() {
      this.post = null;
      this.openModalAddEdit = true;
    },
    onUpdateItem() {
      this.onFilter();
    },
    onAddItem() {
      this.onFilter();
    },
    onFilter() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => {
        this.loading = true;
        const params = this.filter;
        this.$http
          .get("/hotels/blog", { params })
          .then((response) => {
            this.items = response.data.posts.data;
            this.paginator.size = response.data.posts.last_page;
            this.paginator.total = response.data.posts.total;
            this.paginator.per_page = response.data.posts.per_page;
          })
          .finally(() => {
            this.loading = false;
          });
      }, 300);
    },
  },
};
</script>
