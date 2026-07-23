@extends('ecommerce::layouts.layout_account')
@section('account_content')
@php
    $contact = auth('ecommerce')->user()->contact;
    $first_name = $contact && isset($contact->first_name) ? $contact->first_name : auth('ecommerce')->user()->name;
    $paternal_last_name = $contact && isset($contact->paternal_last_name) ? $contact->paternal_last_name : '';
    $maternal_last_name = $contact && isset($contact->maternal_last_name) ? $contact->maternal_last_name : '';
    $date_of_birth = $contact && isset($contact->date_of_birth) ? $contact->date_of_birth : '';
    $gender = $contact && isset($contact->gender) ? $contact->gender : '';
@endphp

<div id="app">
    <div class="panel-head">
        <span class="panel-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
        </span>
        <div>
            <h2 class="m-0">Mi perfil</h2>
            <p class="m-0">Tus datos personales para pedidos y comprobantes.</p>
        </div>
    </div>
    <div class="panel-body">
        <div class="">
            <div class="row">
                <div class="col-md-6 form-group-container">
                    <label>Nombres</label>
                    <el-input v-model="form.first_name"></el-input>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Apellido Paterno</label>
                    <el-input v-model="form.paternal_last_name"></el-input>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Apellido Materno</label>
                    <el-input v-model="form.maternal_last_name"></el-input>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Correo electrónico</label>
                    <el-input v-model="form.email" type="email"></el-input>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Tipo de documento <span class="tag-ecommerce disabled ml-3">No editable</span></label>
                    <el-select v-model="form.identity_document_type_id" disabled class="w-100">
                        @foreach($identity_document_types as $type)
                            <el-option value="{{ $type->id }}" label="{{ $type->description }}"></el-option>
                        @endforeach
                    </el-select>
                    <small class="text-muted">El documento no se puede modificar por seguridad.</small>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Número de documento <span class="tag-ecommerce disabled ml-3">No editable</span></label>
                    <el-input v-model="form.number" readonly disabled></el-input>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Fecha de nacimiento</label>
                    <el-date-picker v-model="form.date_of_birth" type="date" placeholder="dd/mm/yyyy" format="dd/MM/yyyy" value-format="yyyy-MM-dd" class="w-100"></el-date-picker>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Género</label>
                    <el-select v-model="form.gender" placeholder="Selecciona" class="w-100">
                        <el-option value="Masculino" label="Masculino"></el-option>
                        <el-option value="Femenino" label="Femenino"></el-option>
                        <el-option value="Otro" label="Otro"></el-option>
                    </el-select>
                </div>
                <div class="col-md-6 form-group-container">
                    <label>Número de celular</label>
                    <el-input v-model="form.telephone"></el-input>
                </div>
                <div class="col-md-12 d-flex align-items-center justify-content-end mt-3">
                    <button class="pay-btn w-auto" @click="saveData" :loading="loading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    Vue.use(ELEMENT, { locale: ELEMENT.lang.es });
    var app_account = new Vue({
        el: '#app',
        data: {
            form: {
                first_name: @json($first_name),
                paternal_last_name: @json($paternal_last_name),
                maternal_last_name: @json($maternal_last_name),
                email: @json(auth('ecommerce')->user()->email),
                identity_document_type_id: @json(auth('ecommerce')->user()->identity_document_type_id),
                number: @json(auth('ecommerce')->user()->number),
                date_of_birth: @json($date_of_birth),
                gender: @json($gender),
                telephone: @json(auth('ecommerce')->user()->telephone),
                address: @json(auth('ecommerce')->user()->address)
            },
            loading: false
        },
        methods: {
            async saveData() {
                if (!this.form.first_name || !this.form.paternal_last_name || !this.form.email) {
                    this.$message.error('Los nombres, apellido paterno y correo electrónico son obligatorios.');
                    return;
                }
                
                this.loading = true;
                try {
                    let response = await axios.post(`{{ route('tenant_ecommerce_user_data') }}`, this.form);
                    if (response.data.success) {
                        this.$message.success(response.data.message || 'Datos actualizados correctamente');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        this.$message.error(response.data.message || 'Error al actualizar los datos');
                    }
                } catch (error) {
                    console.error(error);
                    if (error.response && error.response.data && error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error('Error de conexión.');
                    }
                } finally {
                    this.loading = false;
                }
            }
        }
    });
</script>
@endpush
