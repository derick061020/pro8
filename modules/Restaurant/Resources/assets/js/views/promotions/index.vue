<template>
  <div>
    <div class="page-header pe-0">
      <h2>
        <a href="/restaurant/promotions">
          <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12zm0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" /></svg>
        </a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active">
          <span>Promociones</span>
        </li>
      </ol>    
    </div>

    <div class="card tab-content-default row-new mb-0 bg-transparent promotions-section">
      <div class="col-12 d-flex justify-content-between align-items-center bg-transparent promotions-header">
        <h3 class="">Banners principales</h3>
        <div class="right-wrapper pull-right">
          <template>
            <button
              type="button"
              class="btn btn-custom btn-sm mt-2 me-2"
              @click.prevent="clickCreate()"
            >
              <i class="fa fa-plus-circle"></i> Nuevo
            </button>
          </template>
        </div>
      </div>
      <div class="card-body">
        <data-table :apply-filter="false" :promotionType="'promotions'" :resource="resource">
          <tr slot="heading" width="100%">
            <!-- <th>#</th> -->
            <th>Nombre</th>
            <!-- <th>Descripción</th> -->
            <th class="text-center">Imagen</th>
            <th class="text-end">Acciones</th>
          </tr>
          <tr></tr>
          <tr slot-scope="{ index, row }">
            <!-- <td>{{ index }}</td> -->
            <td>{{ row.name }}</td>
            <!-- <td>{{ row.description }}</td> -->
            <td class="text-center">
              <img :src="row.image_url" alt width="170" height="130" />
            </td>
            <td class="text-end">
              <template>
                <!-- v-if="typeUser === 'admin'" -->
                <button
                  type="button"
                  class="btn btn-xs btn-info btn-shad" title="Editar"
                  @click.prevent="clickCreate(row.id)"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
              </button>
                <button
                  type="button"
                  class="btn btn-xs btn-danger btn-shad ms-1" title="Eliminar"
                  @click.prevent="clickDelete(row.id)"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
              </button>
              </template>
            </td>
          </tr>
        </data-table>
      </div>

      <promotions-form :showDialog.sync="showDialog" :recordId="recordId"></promotions-form>
    </div>

    <div class="card tab-content-default row-new mb-0 bg-transparent promotions-section">
      <div class="col-12 d-flex justify-content-between align-items-center bg-transparent promotions-header">
        <h3 class="">Listado de Promociones <small class="text-muted">(Hasta 4 imágenes)</small></h3>
        <div class="right-wrapper pull-right">
          <template>
            <button
              type="button"
              class="btn btn-custom btn-sm me-2"
              @click.prevent="clickCreateSpotList()"
            >
              <i class="fa fa-plus-circle"></i> Nuevo
            </button>
          </template>
        </div>
      </div>
      <div class="card-body">
        <data-table :apply-filter="false" :promotionType="'spots'" :resource="resource">
          <tr slot="heading" width="100%"> 
            <th>Nombre</th>
            <th class="text-center">Imagen</th>
            <th class="text-end">Acciones</th>
          </tr>
          <tr></tr>
          <tr slot-scope="{ index, row }">
            <td>{{ row.name }}</td>
            <td class="text-center">
              <img :src="row.image_url" alt width="170" height="130" />
            </td>
            <td class="text-end">
              <template>
                <button
                  type="button"
                  class="btn btn-xs btn-info btn-shad" title="Editar"
                  @click.prevent="clickCreateSpotList(row.id)"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
              </button>
                <button
                  type="button"
                  class="btn btn-xs btn-danger btn-shad ms-1" title="Eliminar"
                  @click.prevent="clickDeleteSpotList(row.id)"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
              </button>
              </template>
            </td>
          </tr>
        </data-table>
      </div>
    
      <spot-list-form :showDialog.sync="showDialogSpotList" :recordId="recordIdSpot"></spot-list-form>
    </div>

  </div>
</template>
<style scoped>
.card-body .table {
  width: 100%;
  table-layout: fixed;
}

.card-body .table th:nth-child(1),
.card-body .table td:nth-child(1) {
  width: 20%;
  word-wrap: break-word;
}

.card-body .table th:nth-child(2),
.card-body .table td:nth-child(2) {
  width: 30%;
  word-wrap: break-word;
}

.card-body .table th:nth-child(3),
.card-body .table td:nth-child(3) {
  width: 30%;
}

.card-body .table th:nth-child(4),
.card-body .table td:nth-child(4) {
  width: 20%;
}
.card-body .table img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 0 auto;
}
</style>
<script>
import PromotionsForm from "./form.vue";
import SpotListForm from "./spotListForm.vue";
// import ItemsImport from './import.vue'
import DataTable from "../../components/Datatable.vue";
import { deletable } from "@mixins/deletable";

export default {
  props: [], //'typeUser'
  mixins: [deletable],
  components: { PromotionsForm, SpotListForm, DataTable }, //ItemsImport
  data() {
    return {
      showDialog: false,
      showDialogSpotList: false,
      showImportDialog: false,

      showImageDetail: false,
      resource: "restaurant/promotions",
      recordId: null,
      recordIdSpot: null
    };
  },
  created() {},
  methods: {
    clickCreate(recordId = null) {
      this.recordId = recordId;
      this.showDialog = true;
    },
    clickImport() {
      this.showImportDialog = true;
    },
    clickDelete(id) {
      this.destroy(`/${this.resource}/${id}`).then(() =>
        this.$eventHub.$emit("reloadData")
      );
    },
    clickCreateSpotList(recordId = null) {
      this.recordIdSpot = recordId;
      this.showDialogSpotList = true;
    },
    clickDeleteSpotList(id) {
      this.destroy(`/restaurant/spot-list/${id}`).then(() =>
        this.$eventHub.$emit("reloadData")
      );
    }
  }
};
</script>
