<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/dispatches">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        style="margin-top: -5px;"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-truck"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path
                            d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"
                        />
                    </svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Guías de remisión</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <a
                    :href="`/${resource}/create`"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
                <a
                    href="#"
                    @click.prevent="showModalGenerateCPE = true"
                    class="btn btn-custom btn-sm  mt-2 me-2"
                    >Generar comprobante desde múltiples guías</a
                >
            </div>
        </div>
        <div class="card tab-content-default row-new mb-0">
            <div class="card-body">
                <div class="data-table-visible-columns">
                    <el-dropdown :hide-on-click="false" slot="showhide">
                        <el-button type="secondary">
                            Mostrar columnas<i
                                class="el-icon-arrow-down el-icon--right"
                            ></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown" style="min-width: 220px;">
                            <div style="max-height: 520px; overflow-y: auto;">
                                <el-dropdown-item divided disabled>
                                    <strong>Campos personalizados</strong>
                                </el-dropdown-item>
                                <el-dropdown-item
                                    v-for="field in customFieldColumns"
                                    :key="`custom-field-${field.id}`"

                                >
                                    <el-checkbox
                                        @change="updateCustomFieldColumns()"
                                        v-model="field.visible"
                                        >{{ field.name }}</el-checkbox
                                    >
                                </el-dropdown-item>
                            </div>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
                <data-table :resource="resource">
                    <tr slot="heading">
                        <!-- <th>#</th> -->
                        <th class="text-start">Fecha Emisión</th>
                        <th>Cliente</th>
                        <th>Número</th>
                        <th>Estado</th>
                        <th class="text-center">Fecha Envío</th>
                        <th class="text-center">N° Comprobante</th>
                        <th
                            v-for="field in customFieldColumns"
                            :key="field.id"
                            class="text-start"
                            v-if="field.visible"
                        >
                            {{ field.name }}
                        </th>
                        <th class="text-center">Descargas</th>
                        <th class="text-center">Acciones</th>
                    </tr>

                    <tr></tr>
                    <tr
                        slot-scope="{ index, row }"
                        :class="{ 'text-danger': row.state_type_id === '11' }"
                    >
                        <!-- <td>{{ index }}</td> -->
                        <td class="text-start">
                            {{ formatDate(row.date_of_issue) }}
                        </td>
                        <template v-if="!row.customer_id">
                            <td>
                                <small>{{
                                    row.transfer_reason_type.description
                                }}</small>
                            </td>
                        </template>
                        <template v-else>
                            <td>
                                {{ row.customer_name }} <br />
                                <small>{{ row.customer_number }}</small>
                            </td>
                        </template>
                        <td>{{ row.number }}</td>
                        <td>
                            <span
                                class="badge bg-secondary text-white"
                                :class="{
                                    'bg-secondary': row.state_type_id === '01',
                                    'bg-info': row.state_type_id === '03',
                                    'bg-success': row.state_type_id === '05',
                                    'bg-secondary': row.state_type_id === '07',
                                    'bg-dark': row.state_type_id === '09'
                                }"
                                >{{ row.state_type_description }}</span
                            >
                        </td>
                        <td class="text-center">
                            {{ formatDate(row.date_of_shipping) }}
                        </td>

                        <td class="text-center">
                            <template v-for="(row, index) in row.documents">
                                <label class="d-block" :key="index">{{
                                    row.description
                                }}</label>
                            </template>
                        </td>
                        <td
                            v-for="field in customFieldColumns"
                            :key="field.id"
                            class="text-start"
                            v-if="field.visible"
                        >
                            <template v-if="isEditableCustomField(field)">
                                <template v-if="field.type === 'text'">
                                    <el-input
                                        v-model="row.custom_fields_data[field.slug]"
                                        @blur="saveCustomFieldValue(row, field)"
                                        size="small"
                                        :placeholder="field.name"
                                    ></el-input>
                                </template>
                                <template v-else-if="field.type === 'number'">
                                    <el-input
                                        v-model.number="row.custom_fields_data[field.slug]"
                                        type="number"
                                        @blur="saveCustomFieldValue(row, field)"
                                        size="small"
                                        :placeholder="field.name"
                                    ></el-input>
                                </template>
                                <template v-else-if="field.type === 'textarea'">
                                    <el-input
                                        v-model="row.custom_fields_data[field.slug]"
                                        type="textarea"
                                        :rows="2"
                                        @blur="saveCustomFieldValue(row, field)"
                                        size="small"
                                        :placeholder="field.name"
                                    ></el-input>
                                </template>
                                <template v-else-if="field.type === 'select'">
                                    <el-select
                                        v-model="row.custom_fields_data[field.slug]"
                                        @change="saveCustomFieldValue(row, field)"
                                        size="small"
                                        clearable
                                        :placeholder="field.name"
                                    >
                                        <el-option
                                            v-for="option in normalizeOptions(field.options)"
                                            :key="option"
                                            :label="option"
                                            :value="option"
                                        ></el-option>
                                    </el-select>
                                </template>
                                <template v-else-if="field.type === 'checkbox'">
                                    <el-checkbox-group
                                        v-model="row.custom_fields_data[field.slug]"
                                        @change="saveCustomFieldValue(row, field)"
                                    >
                                        <el-checkbox
                                            v-for="option in normalizeOptions(field.options)"
                                            :key="option"
                                            :label="option"
                                            :value="option"
                                        >
                                            {{ option }}
                                        </el-checkbox>
                                    </el-checkbox-group>
                                </template>
                                <template v-else-if="field.type === 'date'">
                                    <el-date-picker
                                        v-model="row.custom_fields_data[field.slug]"
                                        type="date"
                                        format="yyyy-MM-dd"
                                        value-format="yyyy-MM-dd"
                                        @change="saveCustomFieldValue(row, field)"
                                        size="small"
                                        :placeholder="field.name"
                                    ></el-date-picker>
                                </template>
                                <template v-else>
                                    {{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}
                                </template>
                            </template>
                            <template v-else>
                                {{ formatCustomFieldValue(row.custom_fields_data[field.slug]) }}
                            </template>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="
                                    clickDownload(row.download_external_xml)
                                "
                            >
                                XML
                            </button>
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="
                                    clickDownload(row.download_external_pdf)
                                "
                                v-if="row.btn_pdf"
                            >
                                PDF
                            </button>
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info"
                                @click.prevent="
                                    clickDownload(row.download_external_cdr)
                                "
                                v-if="row.has_cdr"
                            >
                                CDR
                            </button>
                        </td>
                        <td class="text-center">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="onGenerateDocument(row.id)"
                                v-if="row.btn_generate_document"
                            >
                                Generar comprobante
                            </button>
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="
                                    btnStatusTicket(row.external_id)
                                "
                                v-if="row.btn_status_ticket"
                            >
                                Consultar ticket
                            </button>
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="clickOptions(row.id)"
                                v-if="row.btn_options"
                            >
                                Opciones
                            </button>
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-xs btn-info me-1"
                                @click.prevent="sendSunat(row.external_id)"
                                v-if="row.btn_send"
                            >
                                Enviar a Sunat
                            </button>
                            <a
                                :href="
                                    `/dispatches/create_new/dispatch/${row.id}`
                                "
                                class="btn waves-effect waves-light btn-xs btn-warning m-1__2 me-1"
                                v-if="row.btn_edit"
                                >Editar</a
                            >
                        </td>
                    </tr>
                </data-table>
            </div>
        </div>
        <dispatch-options
            :showDialog.sync="showDialogOptions"
            :recordId="recordId"
            :showClose="true"
        ></dispatch-options>

        <FormGenerateDocument
            :showDialog.sync="showDialogGenerateDocument"
            :recordId="recordId"
            :showClose="true"
            :showGenerate="true"
            :configuration="configuration"
        ></FormGenerateDocument>
        <ModalGenerateCPE :show.sync="showModalGenerateCPE"></ModalGenerateCPE>
    </div>
</template>
<style>
@media only screen and (max-width: 390px) {
    .filter-content {
        margin-top: 0px;
        display: flex;
        align-items: start;
        justify-content: start;
    }
}
</style>
<script>
import DataTable from "../../../components/DataTableDispatch.vue";
// import DataTable from '../../../components/DataTable.vue'
import DispatchOptions from "./partials/options.vue";
import FormGenerateDocument from "./generate-document.vue";
import ModalGenerateCPE from "./ModalGenerateCPE.vue";

export default {
    components: {
        DataTable,
        DispatchOptions,
        FormGenerateDocument,
        ModalGenerateCPE
    },
    props: ["configuration"],
    data() {
        return {
            resource: "dispatches",
            showDialogOptions: false,
            recordId: null,
            showDialogGenerateDocument: false,
            showModalGenerateCPE: false,
            customFieldColumns: [],
        };
    },
    created() {
        this.$setStorage("configuration", this.configuration);
        this.loadCustomFieldsColumns();
    },
    methods: {
        formatDate(date) {
            if (!date) return null;
            const parsedDate = moment(date);
            return parsedDate.isValid()
                ? parsedDate.format("DD-MM-YYYY")
                : null;
        },
        showSentSunat(row) {
            let data = row.soap_shipping_response;
            if (this.configuration.auto_send_dispatchs_to_sunat === true)
                return false;
            if (data === undefined || data === null) return true;
            if (data.sent === null || data.sent === false) return true;
            return false;
        },
        async sendSunat(external_id) {
            await this.$http
                .get(`/service/dispatch/send/${external_id}`)
                .then(response => {
                    // console.log(response.data);
                    let data = response.data;
                    if (data.success) {
                        this.$notify.success({
                            title: "Se ha realizado el envio",
                            message: data.message
                        });
                    } else {
                        this.$notify.error({
                            title: "Envio no realizado",
                            message: data.message
                        });
                    }
                })
                .then(() => {
                    //this.loading_sunat_send = false;
                });
            this.$eventHub.$emit("reloadData");

            // this.$http.post(`/dispatches/sendSunat/${id}`)
            //     .then((result) => {
            //         let data = result.data;
            //         if (data.sent === false) {
            //             this.$notify.error({
            //                 title: 'Envio no realizado',
            //                 message: data.description,
            //             });
            //         } else {
            //             this.$notify.success({
            //                 title: 'Se ha realizado el envio',
            //                 message: data.description,
            //             });
            //         }
            //     }).catch(() => {
            //     this.$notify.success({
            //         title: 'Error',
            //         message: 'Error desconocido',
            //     });
            // })
        },
        onGenerateDocument(dispatchId) {
            this.recordId = dispatchId;
            this.showDialogGenerateDocument = true;
        },
        clickOptions(recordId = null) {
            this.recordId = recordId;
            this.showDialogOptions = true;
        },
        clickDownload(download) {
            window.open(download, "_blank");
        },
        clickPrint(external_id) {
            window.open(`/print/dispatch/${external_id}/a4`, "_blank");
        },
        btnStatusTicket(external_id) {
            this.$http
                .post(`/dispatches/status_ticket`, {
                    external_id: external_id
                })
                .then(result => {
                    let data = result.data;
                    if (data.success) {
                        this.$message.success(data.message);
                    } else {
                        this.$message.error(data.message);
                    }
                    this.$eventHub.$emit("reloadData");
                })
                .catch(() => {
                    this.$notify.success({
                        title: "Error",
                        message: "Error desconocido"
                    });
                });
        },
        async loadCustomFieldsColumns() {
            try {
                const response = await this.$http.get(
                    "/configurations/custom-fields/dispatches"
                );
                this.customFieldColumns = (response.data.data || []).map(field => ({
                    ...field,
                    visible: field.visible !== undefined ? field.visible : true
                }));
            } catch (error) {
                console.error("Error cargando columnas de campos personalizados:", error);
                this.customFieldColumns = [];
            }
        },
        updateCustomFieldColumns() {
            // Custom fields visibility is handled client-side for sale note columns.
            // Persist here if needed by backend later.
        },
        isEditableCustomField(field) {
            return [
                'text',
                'number',
                'textarea',
                'select',
                'checkbox',
                'date'
            ].includes(field.type);
        },
        normalizeOptions(options) {
            if (!options) return [];
            if (Array.isArray(options)) {
                if (options.length === 1 && typeof options[0] === 'string' && options[0].includes(',')) {
                    return options[0]
                        .split(',')
                        .map(opt => opt.trim())
                        .filter(opt => opt.length > 0);
                }
                return options;
            }
            if (typeof options === 'string') {
                return options
                    .split(/[,\n]/)
                    .map(opt => opt.trim())
                    .filter(opt => opt.length > 0);
            }
            return [];
        },
        ensureCustomFieldsData(row, field = null) {
            if (!row.custom_fields_data || typeof row.custom_fields_data !== 'object') {
                this.$set(row, 'custom_fields_data', {});
            }
            if (field && field.type === 'checkbox' && row.custom_fields_data[field.slug] === undefined) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            return row.custom_fields_data;
        },
        saveCustomFieldValue(row, field) {
            this.ensureCustomFieldsData(row, field);
            if (field.type === 'checkbox' && !Array.isArray(row.custom_fields_data[field.slug])) {
                this.$set(row.custom_fields_data, field.slug, []);
            }
            this.$http
                .post('/dispatches/custom-fields/update', {
                    id: row.id,
                    custom_fields_data: row.custom_fields_data
                })
                .then(response => {
                    if (response.data.success) {
                        if (response.data.data !== undefined) {
                            this.$set(row, 'custom_fields_data', response.data.data);
                        }
                        this.$message.success('Campo personalizado actualizado correctamente.');
                    }
                })
                .catch(error => {
                    console.error('Error guardando campo personalizado:', error);
                    this.$message.error('No se pudo actualizar el campo personalizado.');
                });
        },
        formatCustomFieldValue(value) {
            if (value === null || value === undefined || value === "") {
                return "";
            }
            if (Array.isArray(value)) {
                return value.join(", ");
            }
            if (typeof value === "object") {
                return JSON.stringify(value);
            }
            return value;
        }
    }
};
</script>
