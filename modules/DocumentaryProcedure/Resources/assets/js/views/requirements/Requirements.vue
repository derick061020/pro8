<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/documentary-procedure/requirements">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        style="margin-top: -5px;"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-folder">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REGISTRO DE REQUERIMIENTOS</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <button
                        class="btn btn-custom btn-sm mt-2 me-2"
                        type="button"
                        @click="onCreate"
                    >
                        <i class="fa fa-plus-circle"></i> Nuevo
                    </button>
                </div>
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de requisitos</h3>
            </div> -->
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-2 mb-3">
                        <form class="form-group" @submit.prevent="onFilter">
                            <div class="input-group mb-3">
                                <input
                                    v-model="filter.name"
                                    class="form-control form-control-default"
                                    placeholder="Filtrar por nombre"
                                    type="text"
                                />
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-search-default btn-outline-secondary"
                                        style="border-color: #ced4da"
                                        type="submit"
                                    >
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <!-- <th>#</th> -->
                            <th>Requerimiento</th>
                            <!--<th>Requiere la carga de archivo</th>-->
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="(item,index) in items"
                            :key="item.id"
                        >
                            <!-- <td class="text-left">{{ index + 1 }}</td> -->
                            <td>{{ item.name }}</td>
                            <!--
                            <td class="text-center">
                                <span v-if="item.file">Si</span>
                                <span v-else>No</span>
                            </td>
                            -->
                            <td class="text-end">
                                <button
                                    class="btn btn-xs btn-info me-1 btn-shad"
                                    :disabled="loading"
                                    @click="onEdit(item)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" /><path d="M16 5l3 3" /></svg>
                                </button>
                                <button
                                    :disabled="loading"
                                    class="btn btn-xs btn-danger btn-shad"
                                    @click="onDelete(item)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <ModalAddEdit
            :requirement="requirement"
            :visible.sync="openModalAddEdit"
            @onAddItem="onAddItem"
            @onUpdateItem="onUpdateItem"
        ></ModalAddEdit>
    </div>
</template>

<script>
import ModalAddEdit from "./ModalAddEdit.vue";

export default {
    props: ['requirements'],
    components: {
        ModalAddEdit,
    },
    data() {
        return {
            items: [],
            requirement: null,
            openModalAddEdit: false,
            loading: false,
            filter: {
                name: "",
            },
            basePath: '/documentary-procedure/requirements'
        };
    },
    mounted() {
        this.items = this.requirements;
    },
    methods: {
        onFilter() {
            this.loading = true;
            const params = this.filter;
            this.$http
                .post(this.basePath, {params})
                .then((response) => {
                    this.items = response.data.data;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        onDelete(item) {
            this.$confirm(
                `¿estás seguro de eliminar al elemento ${item.name}?`,
                "Atención",
                {
                    confirmButtonText: "Si, continuar",
                    cancelButtonText: "No, cerrar",
                    type: "warning",
                }
            )
                .then(() => {
                    this.$http
                        .delete(`${this.basePath}/${item.id}/delete`)
                        .then((response) => {
                            this.$message({
                                type: "success",
                                message: response.data.message,
                            });
                            this.items = this.items.filter((i) => i.id !== item.id);
                        })
                        .catch((error) => {
                            this.axiosError(error);
                        });
                })
                .catch();
        },
        onEdit(item) {
            this.requirement = {...item};
            this.openModalAddEdit = true;
        },
        onUpdateItem(data) {
            this.items = this.items.map((i) => {
                if (i.id === data.id) {
                    return data;
                }
                return i;
            });
        },
        onAddItem(data) {
            this.items.unshift(data);
        },
        onCreate() {
            this.requirement = null;
            this.openModalAddEdit = true;
        },
    },
};
</script>
