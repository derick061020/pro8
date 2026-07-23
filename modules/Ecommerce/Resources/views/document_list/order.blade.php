@extends('ecommerce::layouts.layout_account')
@section('account_content')
<style>

.table-loader {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.6);
  backdrop-filter: blur(3px);
  z-index: 10;
  display: none; /* lo ocultamos por defecto */
}
.table.table-cart tr th{
    text-align: left;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--subtitle-color);
    padding: 14px 26px !important;
    background: #fafbfc;
    border-bottom: 1px solid var(--line);
    border-radius: 0 !important;
}
</style>

<div id="app">
    <div class="panel-head">
        <span class="panel-icon">
            <svg clip-rule="evenodd" fill="currentcolor" fill-rule="evenodd" height="20" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg" id="fi_4893746">
                <path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path>
            </svg>
        </span>
        <div>
            <h2 class="m-0">Mis pedidos</h2>
            <p class="m-0">Revisa el estado y los detalles de tus compras.</p>
        </div>
    </div>
    <div class="">
        <div>
            <div class="dropdown dropdown-table d-flex justify-content-between align-items-center filters">
                <div class="d-flex align-items-end">
                    <template v-if="filterId == 1">
                        <div class="d-flex flex-column">
                            <span>
                                Fecha de inicio
                            </span>
                            <el-date-picker
                                v-model="filters.date_of_start"
                                type="date"
                                placeholder="Seleccionar fecha"
                                size="small"
                                style="width: 200px;"
                                format="dd/MM/yyyy"
                                value-format="yyyy-MM-dd"
                                clearable>
                            </el-date-picker>
                        </div>
                        <div class="d-flex flex-column">
                            <span style="margin-left: 10px">
                                Fecha de fin
                            </span>
                            <el-date-picker
                                v-model="filters.date_of_end"
                                type="date"
                                placeholder="Seleccionar fecha"
                                size="small"
                                style="width: 200px; margin-left: 8px;"
                                format="dd/MM/yyyy"
                                value-format="yyyy-MM-dd"
                                clearable>
                            </el-date-picker>
                        </div>
                        <button
                            class="btn-filter-search ml-2 p-0"
                            @click="getRecords()"
                            type="button"
                            aria-label="Buscar">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                        </button>
                    </template>
                    <template v-else-if="filterId == 2">
                        <span>
                            Por estado:
                        </span>
                        <el-select
                            v-model="filters.state_order_id"
                            placeholder="Seleccionar estado"
                            size="small"
                            style="width: 200px; margin-left: 8px;"
                            class="select-state"
                            @change="getRecords()"
                            clearable>
                            <el-option
                                label="Todos"
                                value="all">
                            </el-option>
                            <el-option
                                label="Pago sin verificar"
                                value="1">
                            </el-option>
                            <el-option
                                label="Pago verificado"
                                value="2">
                            </el-option>
                            <el-option
                                label="Despachado"
                                value="3">
                            </el-option>
                            <el-option
                                label="Confirmado por el cliente"
                                value="4">
                            </el-option>
                        </el-select>
                    </template>
                </div>

                <button class="btn btn-default dropdown-toggle mr-2 mt-1" style="height: 35px" type="button" id="dropdownFilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  Filtros <span class="caret"></span>
                </button>
                <ul class="dropdown-menu  dropdown-menu-table" aria-labelledby="dropdownFilter">
                  {{-- <li><a href="#" @click="filterRecords('all')" >Todos</a></li>
                  <li><a href="#" @click="filterRecords('1')" >Pago sin verificar</a></li>
                  <li><a href="#" @click="filterRecords('2')" >Pago verificado</a></li>
                  <li><a href="#" @click="filterRecords('3')" >Despachado</a></li>
                  <li><a href="#" @click="filterRecords('4')" >Confirmado por el cliente</a></li> --}}
                  <li><a @click="filterId = 1; filters={date_of_issue: new Date().toISOString().split('T')[0]}; getRecords()" href="#">Por fecha</a></li>
                  <li><a @click="filterId = 2; filters={state_order_id: 'all'}; getRecords()" href="#">Estado</a></li>
                </ul>
              </div>
            <table class="table table-cart rounded-0">
                <thead>
                    <tr>
                        <th class="product-col">Código de Pedido</th>
                        <th class="price-col">Total</th>
                        <th class="qty-col">Fecha de creación</th>
                        <th class="qty-col">Estado</th>
                        <th class="qty-col">Cupón</th>
                        <th class="qty-col" v-if="phone_whatsapp"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in records" class="product-row" @click="openDetail(row)" style="cursor:pointer">
                        <td class="text-left">
                            @{{ row.order_id }}
                        </td>
                        <td class="text-success">S/ @{{ row.total }}</td>
                        <td>
                            @{{ formatDateOnly(row.created_at) }}
                        </td>
                        <td>@{{ row.status_order_description }}</td>
                        <td>
                            <span v-if="row.discount_coupon_code">@{{ row.discount_coupon_code }}</span>
                            <span v-else>-</span>
                        </td>
                        <td v-if="phone_whatsapp">
                            <div v-if="row.status_order_id < 4">
                                <button class="btn btn-default btn-sm text-success ml-auto" @click="clickSendWhatsapp(row.order_id)" ><i class="fab fa-whatsapp fa-2x"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>

            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                  <li class="page-item" :class="{
                    disabled: pagL
                  }">
                    <a class="page-link" href="#" tabindex="-1" @click="clickRecord('back')" >&laquo;</a>
                  </li>
                  <li class="page-item active" aria-current="page">
                    <a class="page-link" href="#">@{{ page }}</a>
                  </li>
                  <li class="page-item" :class="{
                    disabled: pagR
                  }">
                    <a class="page-link" @click="clickRecord('front')">&raquo;</a>
                  </li>
                </ul>
              </nav>
              <div id="tableLoader" class=" d-flex justify-content-center align-items-center" :class="{
                'table-loader': loading,
              }">
                <div class="loader" role="status">
                </div>
              </div>
        </div><!-- End .cart-table-container -->
    </div><!-- End .col-lg-8 -->
<order-detail
    :visible.sync="showOrderModal"
    :record="selectedOrder">
</order-detail>
</div>
@include('ecommerce::document_list.order_detail')
<input type="hidden" id="total_amount" data-total="0.0">

@endsection

@push('scripts')
<!-- script src="https://checkout.culqi.com/js/v3"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.31.1/dist/sweetalert2.all.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script -->


<script type="text/javascript">
    Vue.use(ELEMENT, { locale: ELEMENT.lang.es });
    Vue.component('order-detail', {
        props: ['visible', 'record'],
        template: '#order-detail-template',
        watch: {
            visible(val) {
                if (val) {
                    $('#orderDetailModal').modal('show');
                } else {
                    $('#orderDetailModal').modal('hide');
                }
            }
        },
        mounted() {
            // Sincroniza el cierre por click afuera / ESC / botón X con el padre
            $('#orderDetailModal').on('hidden.bs.modal', () => {
                this.$emit('update:visible', false);
            });
        },
        methods: {
            close() {
                this.$emit('update:visible', false);
            }
        }
    });
    var app_cart = new Vue({
        el: '#app',
        data: {
            records: [],
            filter_records: [],
            page: 1,
            links: null,
            loading: false,
            filters: {},
            last_page: null,
            filterId: 1,
            showOrderModal: false,
            selectedOrder: null,
        },
        computed: {
            pagL: function () {
                return this.page == 1 ? true: false
            },
            pagR: function () {
                return this.page != this.last_page ? false: true
            }

        },
        created() {
            this.getRecords();
            // this.filter_records = this.records;
        },
        methods: {
            formatDateOnly(date) {
                if (!date) return '-';
                const parsedDate = moment(date);
                return parsedDate.isValid() ? parsedDate.format("DD-MM-YYYY") : '-';
            },
            openDetail(row) {
                this.selectedOrder = row;
                this.showOrderModal = true;
            },
            filterRecords(state_id)
            {
                this.page = 1;
                this.filters = {
                    state_order_id: state_id
                }

                this.getRecords()
            },
            async getRecords()
            {
                this.loading = true;

                let parameters = `?page=${this.page}`;

                if(!(this.filters && Object.keys(this.filters).length === 0)) {
                    for (const obj in this.filters) {
                        parameters += `&${obj}=${this.filters[obj]}`;
                    }
                }

                try {
                    let response = await axios.get(`/ecommerce/orders${parameters}`);

                    this.records = response.data.data || [];
                    this.filter_records = response.data.data || [];
                    this.last_page = response.data.last_page || 1;
                    this.loading = false;
                } catch (error) {
                    console.error('Error al cargar pedidos:', error);
                    this.records = [];
                    this.filter_records = [];
                    this.loading = false;

                    // Mostrar mensaje de error al usuario
                    if (error.response && error.response.status === 401) {
                        alert('Sesión expirada. Por favor, inicie sesión nuevamente.');
                        window.location.href = '/ecommerce/login';
                    }
                }
            },
            clickRecord(type)
            {
                if (type == "front") {
                    this.filter_records = [];
                    if(!this.pagR) {
                        this.page += 1;
                        this.getRecords();
                    }

                } else if (type == "back") {
                    this.filter_records = [];
                    if(!this.pagL) {
                        this.page -= 1;
                        this.getRecords();
                    }
                }
            },

        }

    })

</script>


@endpush
