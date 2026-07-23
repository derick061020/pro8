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
.height-table {
    height: 850px;
    overflow-y: auto;
}
.table.table-cart tr th{
    padding: 1rem !important;
}
.status-badge {
    font-weight: 600;
}
.status-accepted {
    color: #16a34a;
}
.status-voided {
    color: #dc2626;
}
.status-rejected {
    color: #c2410c;
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
            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
        </span>
        <div>
            <h2 class="m-0">Mis comprobantes</h2>
            <p class="m-0">Revisa el estado y los detalles de tus comprobantes.</p>
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
                        <div class="d-flex flex-column">
                            <span>
                                Por estado:
                            </span>
                            <el-select 
                                v-model="filters.state_type_id" 
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
                                    label="Aceptados"
                                    value="05">
                                </el-option>
                                <el-option
                                    label="Rechazados"
                                    value="09">
                                </el-option>
                                <el-option
                                    label="Anulados"
                                    value="11">
                                </el-option>
                            </el-select>
                        </div>
                    </template>                
                </div>

                <button class="btn btn-default dropdown-toggle mr-2 mt-1" style="height: 35px" type="button" id="dropdownFilter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  Filtros <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-table" aria-labelledby="dropdownFilter">
                  {{-- <li><a href="#" @click="filterRecords('all')" >Todos</a></li>
                  <li><a href="#" @click="filterRecords('05')" >Aceptados</a></li>
                  <li><a href="#" @click="filterRecords('09')" >Rechazados</a></li>
                  <li><a href="#" @click="filterRecords('11')" >Anulados</a></li> --}}
                  <li><a @click="filterId = 1; filters={date_of_issue: new Date().toISOString().split('T')[0]}; getRecords()" href="#">Por fecha</a></li> 
                  <li><a @click="filterId = 2; filters={state_type_id: 'all'}; getRecords()" href="#">Estado</a></li>
                </ul>
            </div>        
            <table class="table table-cart rounded-0">
                <thead>
                    <tr>
                        <th class="product-col">Emisión</th>
                        <th class="price-col">Número</th>
                        <th class="qty-col">Estado</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in filter_records" class="product-row">
                        <td class="text-left">
                            @{{ row.date_of_issue }}
                        </td>
                        <td>@{{ row.number }} <br> @{{ row.description }} </td>
                        <td>
                            <span :class="statusClass(row)">
                                @{{ row.status }}
                            </span>
                        </td>
                        <td class="text-success">S/ @{{ row.total }}</td>
                        <td class="text-right">
                            <template v-if="row.download_pdf || row.download_xml">
                                <el-dropdown trigger="click" placement="bottom-end">
                                    <el-button type="primary" size="small">
                                        Documentos <i class="el-icon-arrow-down el-icon--right"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item v-if="row.download_pdf">
                                            <a :href="row.download_pdf" target="_blank" style="text-decoration: none; color: inherit;">PDF</a>
                                        </el-dropdown-item>
                                        <el-dropdown-item v-if="row.download_xml">
                                            <a :href="row.download_xml" target="_blank" style="text-decoration: none; color: inherit;">XML</a>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </template>
                            <span class="mr-3" v-else>
                                No disponible
                            </span>
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

</div>

@endsection

@push('scripts')


<script type="text/javascript">
    Vue.use(ELEMENT, { locale: ELEMENT.lang.es });
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
        },
        computed: {
            pagL: function () {
                return this.page == 1 ? true: false
            }, 
            pagR: function () {
                return this.page != this.last_page ? false: true
            },
        },
        created() {
            this.getRecords();
        },
        methods: {
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
                    let response = await axios.get(`/ecommerce/documents${parameters}`);            
                    
                    this.records = response.data.data || [];
                    this.filter_records = response.data.data || [];
                    this.last_page = response.data.last_page || 1;
                    this.loading = false;   
                } catch (error) {
                    console.error('Error al cargar documentos:', error);
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
            statusClass(row) {
                if (!row || !row.status) return 'status-badge';

                const status = row.status.toString().toLowerCase();

                if (status.includes('anulad')) {
                    return ['status-badge', 'status-voided'];
                }

                if (status.includes('acept')) {
                    return ['status-badge', 'status-accepted'];
                }

                if (status.includes('rechaz')) {
                    return ['status-badge', 'status-rejected'];
                }

                return 'status-badge';
            }

        }
        })

</script>


@endpush
