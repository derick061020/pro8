<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels/reception">
                    <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building-skyscraper"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>CHECK-IN (Asignar habitación)</span></li>
            </ol>
        </div>
        <div class="card mb-0 tab-content-default row-new">
            <div class="card-body invoice p-3">
                <template v-if="canMakePayment">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-12 h1 mt-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-door-exit" style="transform: translateY(-4px);"><g transform="scale(-1 1) translate(-24 0)"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M13 12v.01"></path> <path d="M3 21h18"></path> <path d="M5 21v-16a2 2 0 0 1 2 -2h6m4 10.5v7.5"></path> <path d="M21 7h-7m3 -3l-3 3l3 3"></path></g></svg> Entrada
                                        </div>
                                        <div class="col-4">
                                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-door"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 12v.01" /><path d="M3 21h18" /><path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16" /></svg> Nombre</span>
                                            <h4 class="mt-0"><b>
                                                {{ room.name }}</b></h4>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-category-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 4h6v6h-6z" /><path d="M4 14h6v6h-6z" /><path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /></svg> Categoria</span>
                                            <h4 class="mt-0"><b>
                                                {{ room.category.description }}</b></h4>
                                        </div>
                                        <div class="col-4">
                                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-checkbox"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg> Estado</span>
                                            <h4 class="mt-0">
                                                <b
                                                    :class="onGetStatus(room.status)"
                                                >
                                                {{ room.status }}</b>
                                            </h4>
                                        </div>
                                        <div class="col-12">
                                            <p>{{ room.description }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 card-body bg-accent-color p-3">
                                    <p class="m-0">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg> <b class="text-xs"> Tarifas disponibles</b> <span class="text-muted">(Seleccione una tarifa para esta habitación)</span>
                                </p>
                                    <div class="row">
                                        <div
                                            class="col-12 col-md-6 form-group"
                                            :class="{ 'has-danger': errors.hotel_rate_id }"
                                        >
                                            <label class="control-label" for="rate">Tarifa</label>
                                            <el-select
                                                v-model="form.hotel_rate_id"
                                                @change="onSelectedRate"
                                            >
                                                <el-option
                                                    v-for="option in room.rates"
                                                    :key="option.hotel_rate_id"
                                                    :value="option.hotel_rate_id"
                                                    :label="option.rate.description"
                                                ></el-option>
                                            </el-select>
                                            <small
                                                class="form-control-feedback"
                                                v-if="errors.hotel_rate_id"
                                                v-text="errors.hotel_rate_id[0]"
                                            ></small>
                                        </div>

                                        <!-- afectación igv -->
                                        <div
                                            class="col-12 col-md-6 form-group"
                                            :class="{ 'has-danger': errors.affectation_igv_type_id }"
                                        >
                                            <label class="control-label" for="rate">Tipo de afectación</label>

                                            <el-select
                                                v-model="form.affectation_igv_type_id"
                                            >
                                                <el-option
                                                    v-for="option in getAllowedAffectationIgvTypes"
                                                    :key="option.id"
                                                    :value="option.id"
                                                    :label="option.description"
                                                ></el-option>
                                            </el-select>

                                            <small
                                                class="form-control-feedback"
                                                v-if="errors.affectation_igv_type_id"
                                                v-text="errors.affectation_igv_type_id[0]"
                                            ></small>
                                        </div>
                                        <!-- afectación igv -->
                                        <h3 class="col-12 text-center m-0" v-if="Number(rate_unit_value) > 0">
                                            <span class="text-muted text-xs">Tarifa de alojamiento: </span> <b>S/ {{ Number(rate_unit_value).toFixed(2) }}</b> <span class="text-muted text-xs">por noche</span>
                                        </h3>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h4 class="px-3 my-0">
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" /></svg> Datos del cliente y huéspedes</h4>
                            <div class="card-body py-0">
                            <div class="row">
                                <div
                                    class="form-group col-12 col-md-5"
                                    :class="{ 'has-danger': errors.customer_id }"
                                >
                                    <label class="control-label font-weight-bold text-info">
                                        Cliente
                                        <a href="#" @click.prevent="showDialogNewPerson = true"
                                        >[+ Nuevo]</a
                                        >
                                    </label>
                                    <el-select
                                        v-model="form.customer_id"
                                        filterable
                                        remote
                                        class="border-left rounded-left border-info"
                                        popper-class="el-select-customers"
                                        placeholder="Escriba el nombre o número de documento del cliente"
                                        :remote-method="searchRemoteCustomers"
                                        :loading="loading"
                                        @change="changeCustomer"
                                    >
                                        <el-option
                                            v-for="option in customers"
                                            :key="option.id"
                                            :value="option.id"
                                            :label="option.description"
                                        ></el-option>
                                    </el-select>
                                    <el-checkbox v-model="search_item_by_barcode"
                                                :disabled="recordItem != null">Buscar por
                                        código de
                                        barras
                                    </el-checkbox>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.customer_id"
                                        v-text="errors.customer_id[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-5"
                                    :class="{ 'has-danger': errors['customer.address'] }"
                                >
                                    <label class="control-label">Dirección (Opcional)</label>
                                    <el-input v-model="form.customer.address"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors['customer.address']"
                                        v-text="errors['customer.address'][0]"
                                    ></small>
                                </div>
                                
                                    <div
                                    class="form-group col-12 col-md-2 pt-3"
                                    :class="{ 'has-danger': errors.quantity_persons }"
                                    >

                                    <el-button
                                        class="btn btn-success btn-block position-relative"
                                        @click.prevent="clickAddPerson">
                                        <label
                                            class="badge badge-secondary"
                                            style="position: absolute; top: -13px; right: 0; transform: scale(1.5); padding: 4px !important; background-color: #7e7676;"
                                            v-show="form.customer_id"
                                        >
                                            {{ form.quantity_persons }}</label>
                                        Huéspedes<br>Registrados
                                    </el-button>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.quantity_persons"
                                        v-text="errors.quantity_persons[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-6"
                                    v-if="form.customer && form.customer.observation"
                                >
                                    <div class="alert alert-danger mb-2 py-2" style="font-size: 0.85em;">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-exclamation-triangle mr-2"></i>
                                            <strong>Observaciones:</strong>
                                        </div>
                                        <p class="mb-0 mt-1">{{ form.customer.observation }}</p>
                                    </div>
                                </div>

                            </div>
                            <div class="row d-none">
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.adults }"
                                >
                                    <label class="control-label" for="adults">Adultos</label>
                                    <el-input v-model="form.adults" type="number" min="1" placeholder="Número de adultos"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.adults"
                                        v-text="errors.adults[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.children }"
                                >
                                    <label class="control-label" for="children">Niños</label>
                                    <el-input v-model="form.children" type="number" min="0" placeholder="Número de niños"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.children"
                                        v-text="errors.children[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.quantity_persons }"
                                >
                                    <label class="control-label" for="quantity_persons">Total de personas</label>
                                    <el-input v-model="form.quantity_persons" type="number" min="1" readonly placeholder="Total de huéspedes"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.quantity_persons"
                                        v-text="errors.quantity_persons[0]"
                                    ></small>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.towels }"
                                >
                                    <label class="control-label" for="towels">Toallas</label>
                                    <el-input v-model="form.towels" type="number" min="1"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.towels"
                                        v-text="errors.towels[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.license_plate }"
                                >
                                    <label class="control-label" for="license_plate">Matrícula del vehículo</label>
                                    <el-input v-model="form.license_plate" placeholder="Ej: ABC-123"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.license_plate"
                                        v-text="errors.license_plate[0]"
                                    ></small>
                                </div>
                                <div
                                    class="form-group col-12 col-md-4"
                                    :class="{ 'has-danger': errors.travel_reason }"
                                >
                                    <label class="control-label" for="travel_reason">Motivo de viaje</label>
                                    <el-select
                                        v-model="form.travel_reason"
                                        placeholder="Seleccione motivo"
                                        clearable
                                    >
                                        <el-option label="Visita" value="visita"></el-option>
                                        <el-option label="Trabajo" value="trabajo"></el-option>
                                        <el-option label="Estudio" value="estudio"></el-option>
                                        <el-option label="Religión" value="religion"></el-option>
                                        <el-option label="Salud" value="salud"></el-option>
                                        <el-option label="Compras" value="compras"></el-option>
                                        <el-option label="Otros" value="otros"></el-option>
                                    </el-select>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.travel_reason"
                                        v-text="errors.travel_reason[0]"
                                    ></small>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div
                                    class="form-group col-12 col-md-2"
                                    :class="{ 'has-danger': errors.towels }"
                                >
                                    <label class="control-label" for="notes">Toallas</label>
                                    <el-input v-model="form.towels" type="number"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.towels"
                                        v-text="errors.towels[0]"
                                    ></small>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <div class="card my-0" v-if="Number(rate_unit_value) > 0">
                        <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 col-lg-4" >
                            <div class="row">
                                <h4 class="col-12 my-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-door-enter">
                                        <g transform="scale(-1 1) translate(-24 0)">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M13 12v.01" />
                                            <path d="M3 21h18" />
                                            <path d="M5 21v-16a2 2 0 0 1 2 -2h6m4 10.5v7.5" />
                                            <path d="M21 7h-7m3 -3l-3 3l3 3" />
                                        </g>
                                    </svg> Check-in</h4>
                                <div
                                    class="col-6 col-md-7 form-group"
                                    :class="{ 'has-danger': errors.input_date }"
                                >
                                    <label class="control-label m-0">Fecha de entrada</label>
                                    <el-date-picker
                                        v-model="form.input_date"
                                        type="date"
                                        value-format="yyyy-MM-dd"
                                        placeholder="Seleccione una fecha"
                                    ></el-date-picker>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.input_date"
                                        v-text="errors.input_date[0]"
                                    ></small>
                                </div>
                                <div
                                    class="col-6 col-md-5 form-group"
                                    :class="{ 'has-danger': errors.input_time }"
                                >
                                    <label class="control-label m-0">Hora de entrada</label>
                                    <el-input v-model="form.input_time" placeholder="HH:MM">
                                    </el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.input_time"
                                        v-text="errors.input_time[0]"
                                    ></small>
                                </div>
                            </div>
                        </div>
                            <div class="col-12 col-lg-4" >
                                <div class="row">
                                    <h4 class="col-12 my-0">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-door-exit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 12v.01" /><path d="M3 21h18" /><path d="M5 21v-16a2 2 0 0 1 2 -2h7.5m2.5 10.5v7.5" /><path d="M14 7h7m-3 -3l3 3l-3 3" /></svg> Check-out</h4>
                                    <div
                                    class="col-6 col-md-7 form-group"
                                    :class="{ 'has-danger': errors.output_date }"
                                >
                                    <label class="control-label m-0">Fecha de salida</label>
                                    <el-date-picker
                                        v-model="form.output_date"
                                        type="date"
                                        placeholder="Seleccione una fecha"
                                        value-format="yyyy-MM-dd"
                                    ></el-date-picker>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.output_date"
                                        v-text="errors.output_date[0]"
                                    ></small>
                                </div>
                                <div
                                    class="col-6 col-md-5 form-group"
                                    :class="{ 'has-danger': errors.output_time }"
                                >
                                    <label class="control-label m-0">Hora de salida</label>
                                    <el-input v-model="form.output_time" placeholder="HH:MM">
                                    </el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.output_time"
                                        v-text="errors.output_time[0]"
                                    ></small>
                                </div>

                                </div>
                            </div>
                            <div class="col-12 col-lg-4" >
                                <div class="row">
                                    <h4 class="col-12 my-0">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg> Tarifa final</h4>
                                    <div
                                    class="col-12 col-md-4 form-group"
                                    :class="{ 'has-danger': errors.rate_price }"
                                    v-if="rate"
                                >
                                    <label class="control-label m-0" for="rate">Precio</label>
                                    <el-input-number
                                        v-model="form.rate_price"
                                        controls-position="right"
                                        :min="0"
                                        @change="onUpdateTotalToPay"
                                    ></el-input-number>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.rate_price"
                                        v-text="errors.rate_price[0]"
                                    ></small>
                                </div>


                                <div
                                    class="col-12 col-md-4 form-group"
                                    :class="{ 'has-danger': errors.duration }"
                                    v-if="rate"
                                >
                                    <label class="control-label m-0" for="rate">
                                        {{ form.rental_period_type === 'hour' ? 'Cant. horas' : 
                                           form.rental_period_type === 'month' ? 'Cant. meses' : 'Cant. noches' }}
                                    </label>
                                    <el-input-number
                                        v-model="form.duration"
                                        controls-position="right"
                                        @change="onUpdateTotalToPay"
                                        :min="1"
                                        :step="form.rental_period_type === 'hour' ? 1 : 1"
                                    ></el-input-number>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.duration"
                                        v-text="errors.duration[0]"
                                    ></small>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label class="control-label m-0">Tipo de Renta</label>
                                    <el-select 
                                        v-model="form.rental_period_type" 
                                        placeholder="Seleccionar tipo"
                                        @change="onRentalPeriodChange"
                                        clearable
                                    >
                                        <el-option
                                            label="Estándar (por día)"
                                            value=""
                                        ></el-option>
                                        <el-option
                                            label="Por Hora"
                                            value="hour"
                                        ></el-option>
                                        <el-option
                                            label="Por Día"
                                            value="day"
                                        ></el-option>
                                        <el-option
                                            label="Por Mes"
                                            value="month"
                                        ></el-option>
                                    </el-select>
                                </div>
                                <div class="col-12 col-md-4 form-group" v-if="form.rental_period_type">
                                    <label class="control-label m-0">Precio Personalizado</label>
                                    <el-input
                                        v-model="form.rental_price"
                                        placeholder="0.00"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        @change="onUpdateTotalToPay"
                                    >
                                        <template slot="prepend">S/</template>
                                    </el-input>
                                </div>
                                <div class="col-12 col-md-4 text-center">
                                    <h6>
                                        Total a pagar:
                                        <br/>
                                        <b class="h5">{{ (form.total_to_pay).toFixed(2) }}</b>
                                    </h6>
                                </div>

                                <div>
                                    <div :class="{'has-danger': errors.lot_code}"
                                            class="form-group">
                                        <small v-if="errors.lot_code"
                                                class="form-control-feedback"
                                                v-text="errors.lot_code[0]"></small>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>

                            <div class="row pt-3">
                                <h4 class="col-12 my-0">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-cash-register"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" /><path d="M19 21v1m0 -8v1" /><path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" /><path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" /><path d="M8 14v.01" /><path d="M8 17v.01" /><path d="M12 13.99v.01" /><path d="M12 17v.01" /></svg> Opciones de pago</h4>

                                <div
                                    class="col-6 col-md-3 form-group mb-3"
                                    :class="{ 'has-danger': errors.payment_status }"
                                >
                                    <label class="control-label mt-0">Estado de pago</label>
                                    <el-select
                                        v-model="form.payment_status"
                                        @change="onChangeStatusPayment"
                                    >
                                        <el-option value="PAID" label="Pagado"></el-option>
                                        <!-- <el-option value="ACCOUNT" label="A cuenta"></el-option> -->
                                        <el-option value="DEBT" label="Falta pagar"></el-option>
                                    </el-select>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.payment_status"
                                        v-text="errors.payment_status[0]"
                                    ></small>
                                </div>

                                <!-- mostrar campos adicionales para pago, si tiene estado pagado -->
                                <template v-if="isPaid">
                                    <div class="col-12 col-md-2 form-group">
                                        <div :class="{ 'has-danger': errors.series_id }"
                                            class="form-group">
                                            <label class="control-label mt-0">Serie</label>
                                            <el-select v-model="document.series_id">
                                                <el-option
                                                    v-for="option in series"
                                                    :key="option.id"
                                                    :label="option.number"
                                                    :value="option.id"
                                                ></el-option>
                                            </el-select>
                                            <small
                                                v-if="errors.series_id"
                                                class="form-control-feedback"
                                                v-text="errors.series_id[0]"
                                            ></small>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-2 form-group"
                                        :class="{ 'has-danger': errors['rent_payment.payment_method_type_id'] }"
                                    >
                                        <label class="control-label mt-0" for="rate">Método de pago</label>

                                        <el-select
                                            v-model="form.rent_payment.payment_method_type_id"
                                            filterable
                                        >
                                            <el-option
                                                v-for="option in payment_method_types"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>

                                        <small
                                            class="form-control-feedback"
                                            v-if="errors['rent_payment.payment_method_type_id']"
                                            v-text="errors['rent_payment.payment_method_type_id'][0]"
                                        ></small>
                                    </div>

                                    <div class="col-12 col-md-3 form-group"
                                        :class="{ 'has-danger': errors['rent_payment.payment_destination_id'] }"
                                    >
                                        <label class="control-label mt-0" for="rate">Destino</label>

                                        <el-select
                                            v-model="form.rent_payment.payment_destination_id"
                                            filterable
                                        >
                                            <el-option
                                                v-for="option in payment_destinations"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.description"
                                            ></el-option>
                                        </el-select>

                                        <small
                                            class="form-control-feedback"
                                            v-if="errors['rent_payment.payment_destination_id']"
                                            v-text="errors['rent_payment.payment_destination_id'][0]"
                                        ></small>
                                    </div>

                                    <div class="col-12 col-md-2 form-group">
                                        <label class="control-label mt-0" for="rate">Referencia</label>
                                        <el-input
                                            v-model="form.rent_payment.reference"
                                        ></el-input>
                                    </div>
                                </template>

                            </div>

                        </div>
                    </div>
                    <br>
                    <div class="card my-0" v-if="Number(rate_unit_value) > 0">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="col-12 my-0">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg> Información adicional</h4>
                                <div class="form-group col-12"
                                            :class="{ 'has-danger': errors.notes }"
                                    >
                                    <label class="control-label mt-0" for="notes">Notas</label>
                                    <el-input v-model="form.notes"></el-input>
                                    <small
                                        class="form-control-feedback"
                                        v-if="errors.notes"
                                        v-text="errors.notes[0]"
                                    ></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-0">
                        <div class="d-flex justify-content-between">
                            <template v-if="canMakePayment">
                                <el-button class="btn btn-default" @click="onToBackPage">Cancelar</el-button>
                                <el-button v-if="Number(rate_unit_value) > 0"
                                    type="primary"
                                    :loading="loading"
                                    :disabled="loading"
                                    @click="onSubmit"
                                    class="btn btn-primary"
                                >
                                    <span v-if="isEditMode">Guardar cambios</span>
                                    <span v-else>Guardar y registrar Check-in</span>
                                </el-button>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else-if="submitted">
                    <div class="card text-center">
                        <div>
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="96"  height="96"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" /><path d="M9 12l2 2l4 -4" /></svg>
                            <h2>Registro exitoso en {{ room.name }}</h2>
                            <p v-if="submittedMessage" class="text-muted">{{ submittedMessage }}</p>
                            <el-button
                                @click="onToBackPage"
                                type="primary"
                                class="btn btn-primary mt-4"
                            >
                                <span class="ms-2">Volver a recepción</span>
                            </el-button>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="card text-center">
                        <div class="p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-warning">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9v4"/>
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                <path d="M12 16h.01"/>
                            </svg>
                            <h2 class="mt-3">Habitación no disponible</h2>
                            <p class="text-muted mb-1"><strong>{{ room.name }}</strong> — Estado actual: <em>{{ room.status }}</em></p>
                            <p class="text-muted">{{ roomUnavailableMessage }}</p>
                            <el-button
                                @click="onToBackPage"
                                type="primary"
                                class="btn btn-primary mt-3"
                            >
                                <span class="ms-2">Volver</span>
                            </el-button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <person-form
            :showDialog.sync="showDialogNewPerson"
            type="customers"
            :external="true"
            :input_person="input_person"
            :document_type_id="form.document_type_id"
        ></person-form>

        <quantity-persons
            :persons="form.data_persons"
            :showDialog.sync="showDialogPersons"
            :quantity="form.quantity_persons"
            @addRowPerson="addRowPerson"
            @updatePersonCounts="updatePersonCounts">
        </quantity-persons>

        <sale-note-options :configuration="config"
                           :recordId="form.sale_note_id"
                           :showClose="true"
                           :showDialog.sync="showDialogSaleNoteOptions">
        </sale-note-options>

    </div>
</template>

<script>

import PersonForm from "@views/persons/form.vue";
import moment from "moment";
import {calculateRowItem} from "@helpers/functions";
import {functions} from "@mixins/functions";
import {mapState} from "vuex/dist/vuex.mjs";
import QuantityPersons from './partials/QuantityPersons.vue';
import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import { conformsTo } from "lodash";

export default {
    components: {
        PersonForm,
        QuantityPersons,
        SaleNoteOptions,
    },
    mixins: [
        functions
    ],
    props: {
        room: {
            type: Object,
            required: true,
            default: {},
        },
        affectationIgvTypes: {
            type: Array,
            required: true,
        },
        allSeries: {
            type: Array,
            required: true,
        },
        reservation: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            customers: [],
            customer: {},
            customerId: null,
            customer_barcode: null,
            form: {
                customer: {},
                towels: 1,
                adults: 1,
                children: 0,
                quantity_persons: 1,
                license_plate: null,
                travel_reason: null,
                notes: null,
                hotel_rate_id: null,
                rate_price: 10,
                duration: 1,
                payment_status: 'DEBT',
                input_date: moment().format('YYYY-MM-DD'),
                input_time: moment().format('HH:mm'),
                output_date: moment().add(1, 'days').format('YYYY-MM-DD'),
                output_time: '12:00',
                affectation_igv_type_id: '10',
                rental_period_type: 'day',
                date_of_issue: moment().format('YYYY-MM-DD'),
                rental_price: null,
                is_reservation: false, // Agregar campo para reservas
                sale_note_id: null,
                rent_payment: {
                    payment_method_type_id: null,
                    payment_destination_id: null,
                    reference: null,
                    payment: 0,
                },
            },
            rate: null,
            rate_unit_value: 0,
            loading: false,
            submitted: false,
            submittedMessage: '',
            showDialogNewPerson: false,
            showDialogPersons: false,
            search_item_by_barcode: false,
            input_person: {},
            configuration: {},
            errors: {
                customer: {},
            },
            recordItem: null,
            payment_method_types: [],
            payment_destinations: [],
            resource_documents: "sale-notes",
            document: {
                payments: [],
                items: [],
            },
            series: [],
            form_cash_document: {},
            showDialogSaleNoteOptions: false,
        };
    },
    async mounted() {
        console.log('=== MOUNTED - INICIO ===');
        console.log('output_time inicial:', this.form.output_time);
        console.log('rental_period_type inicial:', this.form.rental_period_type);
        
        await this.onFetchTables();
        console.log('output_time después de onFetchTables:', this.form.output_time);
        
        this.onUpdateOutputDate();
        console.log('output_time después de onUpdateOutputDate:', this.form.output_time);
        
        await this.initDocument();
        console.log('output_time después de initDocument:', this.form.output_time);
        
        // Cargar datos de reserva para edición desde calendario PRIMERO
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('edit_mode') === 'true' && urlParams.get('reservation_id')) {
            console.log('Detectado modo edición desde calendario...');
            await this.loadReservationForEdit(urlParams.get('reservation_id'));
        }
        
        // Cargar datos de la reserva si existe (después de cargar clientes)
        if (this.reservation) {
            console.log('Cargando datos de reserva...');
            await this.loadReservationData();
            console.log('output_time después de loadReservationData:', this.form.output_time);
        }
        
        console.log('=== MOUNTED - FIN ===');
        console.log('output_time final:', this.form.output_time);
    },
    async created() {
        this.$eventHub.$on("reloadDataPersons", (customerId) => {
            this.reloadDataCustomers(customerId);
        });
        this.updateDuration(); // Calcular duración inicial
    },
    computed: {
        isPaid()
        {
            return (this.form.payment_status === 'PAID' || this.form.payment_status === 'ACCOUNT')
        },
        ...mapState([
            'config',
        ]),
        getAllowedAffectationIgvTypes: function () {
            return this.affectationIgvTypes.filter((item) => {
                return ['10', '20'].includes(item.id)
            })
        },
        isReservationContext: function () {
            try {
                if (this.form.is_reservation) return true;
                if (this.form.is_checkin_from_reservation) return true;
                const href = window.location.href || '';
                const search = window.location.search || '';
                const ref = document.referrer || '';
                return href.includes('is_reservation=true')
                    || search.includes('is_reservation=true')
                    || search.includes('source=calendar')
                    || ref.includes('reservations/calendar');
            } catch (e) {
                return false;
            }
        },
        isEditMode: function () {
            try {
                const href = window.location.href || '';
                const search = window.location.search || '';
                return search.includes('edit_mode=true') || this.form.is_edit_from_reservation;
            } catch (e) {
                return false;
            }
        },
        canMakePayment: function () {
            // Si ya se guardó, ya no se permite seguir editando.
            if (this.submitted) return false;
            // Una sale_note pre-existente bloquea el flujo.
            if (this.form.sale_note_id != null) return false;
            // Mantenimiento bloquea siempre — la habitación no está utilizable.
            if (this.room.status === 'MANTENIMIENTO') return false;
            // Para reservas futuras, el estado actual (OCUPADO/LIMPIEZA) no aplica:
            // el backend valida solapamiento contra el rango de fechas elegido.
            if (this.isReservationContext) return true;
            // Check-in inmediato: solo permitido en habitaciones disponibles.
            return this.room.status === 'DISPONIBLE';
        },
        roomUnavailableMessage: function () {
            if (this.form.sale_note_id != null) {
                return 'Esta habitación ya tiene un registro asociado a una nota de venta.';
            }
            if (this.room.status === 'MANTENIMIENTO') {
                return 'La habitación está en mantenimiento y no puede recibir reservas.';
            }
            if (this.room.status === 'LIMPIEZA') {
                return 'La habitación está en limpieza. Asigne un limpiador o espere a que se libere.';
            }
            if (this.room.status === 'OCUPADO') {
                return 'La habitación está ocupada por otra reserva. No es posible registrar un check-in en este momento.';
            }
            return 'La habitación no está disponible en este momento.';
        },
    },
    watch: {
        'form.adults': {
            handler: function(newVal) {
                this.updateTotalPersons();
            },
            immediate: true
        },
        'form.children': {
            handler: function(newVal) {
                this.updateTotalPersons();
            },
            immediate: true
        },
        'form.input_date': {
            handler: function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.updateDuration();
                }
            }
        },
        'form.output_date': {
            handler: function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.updateDuration();
                }
            }
        },
        'form.input_time': {
            handler: function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.updateDuration();
                }
            }
        },
        'form.output_time': {
            handler: function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.updateDuration();
                }
            }
        },
        'form.duration': {
            handler: function(newVal, oldVal) {
                // Solo actualizar fechas si cambia la duración y es por hora o mes
                if (newVal !== oldVal && this.form.input_date && 
                    (this.form.rental_period_type === 'hour' || this.form.rental_period_type === 'month')) {
                    this.onUpdateOutputDate();
                }
            }
        }
    },
    methods: {
        updateTotalPersons() {
            const adults = parseInt(this.form.adults) || 0;
            const children = parseInt(this.form.children) || 0;
            this.form.quantity_persons = adults + children;
        },
        updateDuration() {
            if (!this.form.input_date) return;
            
            const checkInDateTime = moment(`${this.form.input_date} ${this.form.input_time || '12:00'}`);
            
            if (this.form.rental_period_type === 'hour') {
                // Para renta por hora: la duración es en horas
                const hours = this.form.duration || 1;
                const checkOutDateTime = checkInDateTime.clone().add(hours, 'hours');
                
                // Actualizar solo si hay cambios para evitar bucles
                if (this.form.output_date !== checkOutDateTime.format('YYYY-MM-DD') || 
                    this.form.output_time !== checkOutDateTime.format('HH:mm')) {
                    this.form.output_date = checkOutDateTime.format('YYYY-MM-DD');
                    this.form.output_time = checkOutDateTime.format('HH:mm');
                }
                
            } else if (this.form.rental_period_type === 'month') {
                // Para renta por mes: la duración es en meses
                const months = this.form.duration || 1;
                const checkOutDateTime = checkInDateTime.clone().add(months, 'months');
                
                // Actualizar solo si hay cambios para evitar bucles
                if (this.form.output_date !== checkOutDateTime.format('YYYY-MM-DD') || 
                    this.form.output_time !== checkOutDateTime.format('HH:mm')) {
                    this.form.output_date = checkOutDateTime.format('YYYY-MM-DD');
                    this.form.output_time = checkOutDateTime.format('HH:mm');
                }
                
            } else {
                // Para renta estándar por día: mantener duración y ajustar fecha de salida
                const currentDuration = this.form.duration || 1;
                const checkOutDateTime = checkInDateTime.clone().add(currentDuration, 'days');
                
                // Actualizar fecha de salida manteniendo la misma cantidad de noches
                // Para renta por día, usar siempre 12:00 como hora de salida
                const newOutputTime = '12:00';
                if (this.form.output_date !== checkOutDateTime.format('YYYY-MM-DD') || 
                    this.form.output_time !== newOutputTime) {
                    this.form.output_date = checkOutDateTime.format('YYYY-MM-DD');
                    this.form.output_time = newOutputTime;
                }
                
                // Si hay una fecha de salida específica, recalcular duración
                if (this.form.output_date && this.form.output_date !== checkOutDateTime.format('YYYY-MM-DD')) {
                    const existingCheckOutDateTime = moment(`${this.form.output_date} ${this.form.output_time || '12:00'}`);
                    const hoursDiff = existingCheckOutDateTime.diff(checkInDateTime, 'hours');
                    let days = Math.ceil(hoursDiff / 24);
                    days = Math.max(1, days);
                    
                    // Actualizar solo si hay cambios para evitar bucles
                    if (this.form.duration !== days) {
                        this.form.duration = days;
                    }
                }
            }
        },
        onUpdateOutputDate() {
            if (!this.form.input_date || !this.form.input_time) return;
            
            const checkInDateTime = moment(`${this.form.input_date} ${this.form.input_time || '12:00'}`);
            const rentalType = this.form.rental_period_type;
            let checkOutDateTime;
            
            if (rentalType === 'hour') {
                // RENTA POR HORA: calcular salida basada en horas
                const hours = this.form.duration || 1;
                checkOutDateTime = checkInDateTime.clone().add(hours, 'hours');
                
                // Para renta por hora, la salida debe ser como mínimo el mismo día
                if (checkOutDateTime.isBefore(checkInDateTime)) {
                    checkOutDateTime = checkInDateTime.clone().add(1, 'hour');
                }
                
            } else if (rentalType === 'month') {
                // RENTA POR MES: calcular salida basada en meses
                const months = this.form.duration || 1;
                checkOutDateTime = checkInDateTime.clone().add(months, 'months');
                
                // Para renta por mes, la salida es el mismo día del mes siguiente
                // Por ejemplo: entra 15 Ene, 1 mes → sale 15 Feb
                checkOutDateTime.date(checkInDateTime.date());
                
            } else {
                // RENTA POR DÍA/NOCHES: calcular salida basada en días
                const days = this.form.duration || 1;
                checkOutDateTime = checkInDateTime.clone().add(days, 'days');
            }
            
            // Actualizar solo si los valores realmente cambiaron para evitar bucles infinitos
            const newOutputDate = checkOutDateTime.format('YYYY-MM-DD');
            // Para renta por día, usar siempre 12:00 como hora de salida
            const newOutputTime = rentalType === 'day' ? '12:00' : checkOutDateTime.format('HH:mm');
            
            if (this.form.output_date !== newOutputDate || this.form.output_time !== newOutputTime) {
                this.form.output_date = newOutputDate;
                this.form.output_time = newOutputTime;
                
                // Si es renta por día/noches y la fecha de salida cambió, recalcular duración
                if (rentalType === 'day' && this.form.output_date && this.form.output_date !== newOutputDate) {
                    const existingCheckOutDateTime = moment(`${this.form.output_date} ${this.form.output_time || '12:00'}`);
                    const hoursDiff = existingCheckOutDateTime.diff(checkInDateTime, 'hours');
                    const calculatedDays = Math.max(1, Math.ceil(hoursDiff / 24));
                    
                    // Actualizar duración solo si es diferente
                    if (this.form.duration !== calculatedDays) {
                        this.form.duration = calculatedDays;
                    }
                }
            }
        },
        async loadReservationData() {
            if (!this.reservation) return;
            
            console.log('Cargando datos de reserva:', this.reservation);
            
            // Cargar cliente
            console.log('=== DEBUG RESERVATION CUSTOMER ===');
            console.log('this.reservation:', this.reservation);
            console.log('this.reservation.customer:', this.reservation.customer);
            console.log('typeof this.reservation.customer:', typeof this.reservation.customer);
            
            if (this.reservation.customer) {
                // Si solo tenemos el ID, buscar en la lista de clientes ya cargados
                if (typeof this.reservation.customer === 'number' || (typeof this.reservation.customer === 'object' && !this.reservation.customer.name)) {
                    const customerId = typeof this.reservation.customer === 'number' ? 
                                     this.reservation.customer : 
                                     this.reservation.customer.id;
                    
                    console.log('=== DETECTADO SOLO ID - Buscando cliente en lista cargada ID:', customerId);
                    console.log('Clientes disponibles:', this.customers);
                    
                    // Buscar cliente en la lista ya cargada por onFetchTables
                    const existingCustomer = this.customers.find(c => c.id == customerId);
                    if (existingCustomer) {
                        console.log('Cliente encontrado en lista cargada:', existingCustomer);
                        this.form.customer = existingCustomer;
                        this.form.customer_id = existingCustomer.id;
                    } else {
                        console.log('Cliente no encontrado en lista, cargando desde API...');
                        try {
                            // Cargar datos completos del cliente si no está en la lista
                            const response = await this.$http.get(`/persons/search/${customerId}`);
                            console.log('Respuesta de API cliente:', response.data);
                            console.log('response.data.customers:', response.data.customers);
                            
                            if (response.data.customers && response.data.customers.length > 0) {
                                const customer = response.data.customers[0];
                                console.log('Cliente encontrado:', customer);
                                this.form.customer = customer;
                                this.form.customer_id = customer.id;
                                // Agregar a la lista de clientes
                                this.customers.push(customer);
                                console.log('=== FORM ACTUALIZADO ===');
                                console.log('this.form.customer:', this.form.customer);
                            } else {
                                console.log('No se encontró cliente con ID:', customerId);
                            }
                        } catch (error) {
                            console.error('Error al cargar cliente:', error);
                        }
                    }
                } else {
                    // Si ya tenemos los datos completos, usarlos directamente
                    console.log('=== USANDO DATOS EXISTENTES ===');
                    console.log('this.reservation.customer:', this.reservation.customer);
                    this.form.customer = this.reservation.customer;
                    this.form.customer_id = this.reservation.customer.id;
                    // Asegurar que el cliente esté en la lista
                    if (!this.customers.find(c => c.id === this.reservation.customer.id)) {
                        this.customers.push(this.reservation.customer);
                    }
                    console.log('=== FORM ACTUALIZADO CON DATOS EXISTENTES ===');
                    console.log('this.form.customer:', this.form.customer);
                }
            } else {
                console.log('=== NO HAY CUSTOMER EN RESERVATION ===');
            }
            
            // Cargar fechas
            this.form.input_date = this.reservation.input_date;
            this.form.input_time = this.reservation.input_time || '12:00';
            this.form.output_date = this.reservation.output_date;
            this.form.output_time = this.reservation.output_time || '12:00';
            
            // Cargar tarifa
            if (this.reservation.hotel_rate_id) {
                this.form.hotel_rate_id = this.reservation.hotel_rate_id;
                this.onSelectedRate();
            }
            
            // Cargar personas
            if (this.reservation.data_persons) {
                this.form.data_persons = this.reservation.data_persons;
            }
            
            // Establecer que es un check-in de reserva
            this.form.is_checkin_from_reservation = true;
            this.form.reservation_id = this.reservation.id;
            
            // Actualizar duración
            this.updateDuration();
            
            console.log('Datos de reserva cargados en el formulario');
        },
        async loadReservationForEdit(reservationId) {
            try {
                console.log('Cargando reserva para edición ID:', reservationId);
                
                // Obtener datos detallados de la reserva
                const response = await this.$http.get(`/hotels/reservations/calendar/${reservationId}/details`);
                const reservationData = response.data?.data;
                
                if (!reservationData) {
                    this.$message.error('No se encontraron datos de la reserva');
                    return;
                }
                
                console.log('Datos de reserva obtenidos:', reservationData);
                
                // Cargar cliente
                if (reservationData.customer) {
                    // Si solo tenemos el ID, buscar en la lista de clientes ya cargados
                    if (typeof reservationData.customer === 'number' || (typeof reservationData.customer === 'object' && !reservationData.customer.name)) {
                        const customerId = typeof reservationData.customer === 'number' ? 
                                         reservationData.customer : 
                                         reservationData.customer.id;
                        
                        console.log('=== DETECTADO SOLO ID - Buscando cliente en lista cargada ID:', customerId);
                        console.log('Clientes disponibles:', this.customers);
                        
                        // Buscar cliente en la lista ya cargada por onFetchTables
                        const existingCustomer = this.customers.find(c => c.id == customerId);
                        if (existingCustomer) {
                            console.log('Cliente encontrado en lista cargada:', existingCustomer);
                            this.form.customer = existingCustomer;
                            this.form.customer_id = existingCustomer.id;
                        } else {
                            console.log('Cliente no encontrado en lista, cargando desde API...');
                            try {
                                // Cargar datos completos del cliente si no está en la lista
                                const response = await this.$http.get(`/persons/search/${customerId}`);
                                console.log('Respuesta de API cliente:', response.data);
                                
                                if (response.data.customers && response.data.customers.length > 0) {
                                    const customer = response.data.customers[0];
                                    console.log('Cliente encontrado:', customer);
                                    this.form.customer = customer;
                                    this.form.customer_id = customer.id;
                                    // Agregar a la lista de clientes
                                    this.customers.push(customer);
                                } else {
                                    console.log('No se encontró cliente con ID:', customerId);
                                }
                            } catch (error) {
                                console.error('Error al cargar cliente:', error);
                            }
                        }
                    } else {
                        // Si ya tenemos los datos completos, usarlos directamente
                        console.log('=== USANDO DATOS EXISTENTES ===');
                        console.log('reservationData.customer:', reservationData.customer);
                        this.form.customer = reservationData.customer;
                        this.form.customer_id = reservationData.customer.id;
                        // Asegurar que el cliente esté en la lista
                        if (!this.customers.find(c => c.id === reservationData.customer.id)) {
                            this.customers.push(reservationData.customer);
                        }
                    }
                }
                
                // Cargar habitación
                const roomId = reservationData.room?.id || reservationData.hotel_room_id;
                if (roomId) {
                    this.form.hotel_room_id = roomId;
                    console.log('Habitación asignada:', roomId);
                }
                
                // Cargar fechas
                this.form.input_date = reservationData.dates?.input_date || reservationData.input_date;
                this.form.input_time = (reservationData.dates?.input_time || reservationData.input_time || '12:00').slice(0,5);
                this.form.output_date = reservationData.dates?.output_date || reservationData.output_date;
                this.form.output_time = (reservationData.dates?.output_time || reservationData.output_time || '12:00').slice(0,5);
                
                // Cargar tarifa
                if (reservationData.rate?.hotel_rate_id || reservationData.hotel_rate_id) {
                    console.log('Cargando tarifa:', reservationData.rate?.hotel_rate_id || reservationData.hotel_rate_id);
                    console.log('Tarifas disponibles en room.rates:', this.room.rates);
                    
                    // Asignar la tarifa directamente
                    this.form.hotel_rate_id = reservationData.rate?.hotel_rate_id || reservationData.hotel_rate_id;
                    this.onSelectedRate();
                }
                
                // Cargar personas
                if (reservationData.data_persons) {
                    this.form.data_persons = reservationData.data_persons;
                    this.form.quantity_persons = reservationData.quantity_persons || 1;
                }
                
                // Cargar otros datos
                this.form.adults = reservationData.adults || 1;
                this.form.children = reservationData.children || 0;
                this.form.towels = reservationData.towels || 1;
                this.form.license_plate = reservationData.license_plate || '';
                this.form.travel_reason = reservationData.travel_reason || '';
                this.form.notes = reservationData.notes || '';
                
                // Establecer que es edición de reserva (no check-in)
                this.form.is_edit_from_reservation = true;
                this.form.reservation_id = reservationId;
                this.form.is_reservation = true; // Mantener como reserva
                
                // Actualizar duración
                this.updateDuration();
                
                console.log('Reserva cargada para edición exitosamente');
                this.$message.success('Reserva cargada para edición');
                
            } catch (error) {
                console.error('Error al cargar reserva para edición:', error);
                console.error('Error response:', error.response);
                console.error('Error status:', error.response?.status);
                console.error('Error data:', error.response?.data);
                this.$message.error('Error al cargar los datos de la reserva: ' + (error.response?.data?.message || error.message));
            }
        },
        async updateReservation() {
            try {
                if (!this.form.reservation_id) {
                    this.$message.error('No se encontró el ID de la reserva');
                    return;
                }

                // Validar datos básicos
                console.log('=== VALIDACIÓN UPDATE RESERVATION ===');
                console.log('form.customer_id:', this.form.customer_id);
                console.log('form.hotel_room_id:', this.form.hotel_room_id);
                console.log('form.hotel_rate_id:', this.form.hotel_rate_id);
                console.log('form.input_date:', this.form.input_date);
                console.log('form.output_date:', this.form.output_date);
                
                if (!this.form.customer_id) {
                    this.$message.error('Debe seleccionar un cliente');
                    return;
                }
                if (!this.form.hotel_room_id || !this.form.hotel_rate_id) {
                    console.log('Error - habitación o tarifa faltante');
                    console.log('hotel_room_id:', this.form.hotel_room_id);
                    console.log('hotel_rate_id:', this.form.hotel_rate_id);
                    this.$message.error('Seleccione habitación y tarifa');
                    return;
                }
                if (!this.form.input_date || !this.form.output_date) {
                    this.$message.error('Seleccione fechas de ingreso y salida');
                    return;
                }

                // Preparar payload para actualizar reserva
                const payload = {
                    customer_id: this.form.customer_id,
                    customer: { ...this.form.customer },
                    hotel_room_id: this.form.hotel_room_id,
                    hotel_rate_id: this.form.hotel_rate_id,
                    input_date: this.form.input_date,
                    input_time: this.form.input_time || '14:00',
                    output_date: this.form.output_date,
                    output_time: this.form.output_time || '12:00',
                    duration: parseInt(this.form.duration, 10) || 1,
                    adults: parseInt(this.form.adults, 10) || 0,
                    children: parseInt(this.form.children, 10) || 0,
                    quantity_persons: Math.max(1, (parseInt(this.form.adults, 10) || 0) + (parseInt(this.form.children, 10) || 0)),
                    towels: parseInt(this.form.towels, 10) || 0,
                    license_plate: this.form.license_plate || null,
                    travel_reason: this.form.travel_reason || null,
                    notes: this.form.notes || null,
                };

                console.log('Actualizando reserva con payload:', payload);

                // Llamar al endpoint de actualización de reserva
                const response = await this.$http.put(`/hotels/reservations/calendar/${this.form.reservation_id}/update`, payload);

                if (response.data?.success) {
                    this.$message.success(response.data.message || 'Reserva actualizada exitosamente');
                    
                    // Redirigir de vuelta al calendario
                    setTimeout(() => {
                        window.location.href = '/hotels/reservations/calendar';
                    }, 1500);
                } else {
                    this.$message.error(response.data?.message || 'No se pudo actualizar la reserva');
                }
            } catch (error) {
                console.error('Error al actualizar reserva:', error);
                const msg = error?.response?.data?.message || 'Error al actualizar la reserva';
                this.$message.error(msg);
            } finally {
                this.loading = false;
            }
        },
        async onSubmit() {
            try {
                this.loading = true;

                // Si estamos en modo edición, actualizar la reserva en lugar de hacer check-in
                if (this.isEditMode) {
                    console.log('Modo edición detectado, actualizando reserva...');
                    await this.updateReservation();
                    return;
                }

                // Detectar si es una reserva (viene del calendario)
                const isReservation = window.location.href.includes('is_reservation=true') || 
                                   window.location.search.includes('is_reservation=true') ||
                                   document.referrer.includes('reservations/calendar') ||
                                   this.form.is_checkin_from_reservation;
                                   
                console.log('Rent.vue - isReservation detected:', isReservation);

                // Si es un check-in de reserva, convertir en renta normal
                if (this.form.is_checkin_from_reservation) {
                    this.form.is_reservation = false; // Convertir a renta normal
                    console.log('Convirtiendo reserva en renta normal, reservation_id:', this.form.reservation_id);
                } else {
                    // Asignar is_reservation al formulario
                    this.form.is_reservation = isReservation;
                }

                const response = await this.$http.get(`/documents/search/item/${this.room.item_id}`);
                const payload = {};
                const item = response.data.items[0];

                payload.item = item;
                payload.discounts = [];
                payload.charges = [];
                payload.attributes = [];
                payload.item_unit_types = item.item_unit_types;
                payload.unit_price_value = this.form.rate_price;
                payload.has_igv = item.has_igv;
                payload.has_plastic_bag_taxes = item.has_plastic_bag_taxes;

                payload.affectation_igv_type_id = this.form.affectation_igv_type_id;
                payload.affectation_igv_type = _.find(this.affectationIgvTypes, {
                    id: payload.affectation_igv_type_id,
                });

                payload.quantity = this.form.duration;
                const unit_price = item.has_igv
                    ? payload.unit_price_value
                    : payload.unit_price_value * (1 + this.percentage_igv);

                payload.input_unit_price_value = payload.unit_price_value;
                payload.unit_price = unit_price;
                payload.item.unit_price = unit_price;

                const currencyTypeIdActive = "PEN";
                const exchangeRateSale = 0;
                const product = calculateRowItem(
                    payload,
                    currencyTypeIdActive,
                    exchangeRateSale,
                    this.percentage_igv
                );

                this.form.product = product;

                if (this.isPaid) {
                    this.document.items.push(product);
                    console.log("PAYMENTS")
                    
                    // Solo generar comprobante si NO es renta como pagado
                    if (!this.isPaid) {
                        await this.onGoToInvoice();
                    }
                }

                this.form.product.quantity = this.form.duration;
                const response_reception = await this.$http.post(`/hotels/reception/${this.room.id}/rent/store`, this.form);

                // El backend puede responder 200 con success=false (p. ej. solapamiento detectado).
                if (response_reception && response_reception.data && response_reception.data.success === false) {
                    this.$message({
                        message: response_reception.data.message || 'No se pudo registrar la reserva.',
                        type: 'error',
                        duration: 5000,
                    });
                    return; // No marcar submitted; permitir al usuario corregir.
                }

                this.$message({
                    message: response_reception.data.message || 'Registro guardado correctamente.',
                    type: 'success',
                });
                this.submitted = true;
                this.submittedMessage = response_reception.data.message || '';

                if (this.form.is_reservation) {
                    window.parent.postMessage({
                        action: 'reservation_created',
                        success: true,
                        message: 'Reserva creada exitosamente'
                    }, '*');
                    setTimeout(() => {
                        if (!this.isPaid) this.onToBackPage();
                    }, 500);
                } else {
                    this.onToBackPage();
                }

            } catch (error) {
                // El backend devuelve 422 para conflictos (overlap / mantenimiento).
                const status = error?.response?.status;
                const serverMessage = error?.response?.data?.message;
                if ((status === 422 || status === 400) && serverMessage) {
                    this.$message({
                        message: serverMessage,
                        type: 'error',
                        duration: 5000,
                    });
                } else {
                    this.axiosError(error);
                }
            } finally {
                this.loading = false;
            }
        },
        onToBackPage() {
            window.location.href = "/hotels/reception";
        },
        onChangeStatusPayment() {
            if (this.form.payment_status === "DEBT") {
                this.form.payment_type = "CASH";
            }
        },
        onUpdateTotalToPay() {
            // Si hay un precio de renta personalizado, usarlo en lugar del cálculo estándar
            if (this.form.rental_price) {
                this.form.total_to_pay = parseFloat(this.form.rental_price);
                // Actualizar rate_price para reflejar el precio unitario correcto
                // Esto evita que el sistema siga usando el precio estándar en otros cálculos
                this.form.rate_price = parseFloat(this.form.rental_price) / this.form.duration;
            } else {
                this.form.total_to_pay = this.form.rate_price * this.form.duration;
            }
            this.onUpdateOutputDate();
            this.setTotalPayment()
        },
        onRentalPeriodChange(value) {
            // Limpiar el precio personalizado si vuelve a estándar
            if (!value) {
                this.form.rental_price = null;
                // Recalcular con el precio estándar
                this.onUpdateTotalToPay();
                this.updateDuration(); // Recalcular fecha/hora de salida
            }
            
            // Ajustar etiquetas y comportamiento según el tipo
            if (value === 'hour') {
                // Renta por hora
                this.$message({
                    message: 'Modo renta por hora activado. La duración se calculará en horas.',
                    type: 'info'
                });
                
                // Ajustar duración a un valor razonable para horas
                if (this.form.duration > 24) {
                    this.form.duration = 1; // Resetear a 1 hora por defecto
                }
                
            } else if (value === 'month') {
                // Renta por mes
                this.$message({
                    message: 'Modo renta por mes activado. La duración se calculará en meses.',
                    type: 'info'
                });
                
                // Ajustar duración a un valor razonable para meses
                if (this.form.duration > 12) {
                    this.form.duration = 1; // Resetear a 1 mes por defecto
                }
                
            } else if (value === 'day') {
                // Renta por día (estándar)
                this.$message({
                    message: 'Modo renta por día activado. La duración se calculará en días.',
                    type: 'info'
                });
            } else {
                // Estándar (vacío)
                this.$message({
                    message: 'Modo estándar por día activado.',
                    type: 'info'
                });
            }
            
            // Recalcular todo después del cambio
            this.updateDuration();
            this.onUpdateTotalToPay();
        },
        setTotalPayment()
        {
            this.form.rent_payment.payment = this.form.total_to_pay
        },
        onUpdateOutputDate() {
            console.log('=== DEBUG onUpdateOutputDate ===');
            console.log('rental_period_type:', this.form.rental_period_type);
            console.log('input_date:', this.form.input_date);
            console.log('input_time:', this.form.input_time);
            console.log('duration:', this.form.duration);
            console.log('output_time ANTES:', this.form.output_time);
            
            if (!this.form.input_date) return;
            
            const checkInDateTime = moment(`${this.form.input_date} ${this.form.input_time || '12:00'}`);
            const newDate = checkInDateTime.add(this.form.duration, "days");
            this.form.output_date = newDate.format('YYYY-MM-DD');
            
            // Para renta por día, usar siempre 12:00 como hora de salida
            const newOutputTime = this.form.rental_period_type === 'day' ? '12:00' : newDate.format('HH:mm');
            this.form.output_time = newOutputTime;
            
            console.log('newDate.format(HH:mm):', newDate.format('HH:mm'));
            console.log('newOutputTime calculado:', newOutputTime);
            console.log('output_time DESPUÉS:', this.form.output_time);
            console.log('=== FIN DEBUG onUpdateOutputDate ===');
        },
        onSelectedRate() {
            const rate = this.room.rates
                .filter((r) => r.hotel_rate_id === this.form.hotel_rate_id)
                .reduce((r) => r);
            this.rate = rate.rate;
            this.rate.price = rate.price;
            this.rate_unit_value = rate.price;
            this.form.rate_price = rate.price;
            this.onUpdateTotalToPay();
        },
        async reloadDataCustomers(customerId) {
            await this.$http
                .get(`/persons/search/${customerId}`)
                .then((response) => {
                    this.customers = response.data.customers;
                    this.form.customer_id = customerId;
                    this.changeCustomer();
                });

        },
        keyupCustomer() {
            if (this.input_person.number) {
                if (!isNaN(parseInt(this.input_person.number))) {
                    switch (this.input_person.number.length) {
                        case 8:
                            this.input_person.identity_document_type_id = "1";
                            this.showDialogNewPerson = true;
                            break;

                        case 11:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                        default:
                            this.input_person.identity_document_type_id = "6";
                            this.showDialogNewPerson = true;
                            break;
                    }
                }
            }
        },
        async onFetchTables() {
            this.loading = true;
            await this.$http
                .get("/hotels/reception/tables")
                .then((response) => {
                    this.customers = response.data.customers;
                    this.configuration = response.data.configuration

                    this.payment_method_types = response.data.payment_method_types
                    this.payment_destinations = response.data.payment_destinations
                    this.setDefaultDataPayments()

                    this.setAffectationIgvType()
                })
                .finally(() => {
                    this.loading = false;
                });
            this.form.establishment_id = this.room.establishment_id;
            this.series = _.filter(this.allSeries, {
                document_type_id: '80',
            });

            await this.getPercentageIgv();
        },
        setDefaultDataPayments()
        {
            this.form.rent_payment.payment_method_type_id = this.payment_method_types.length > 0 ? this.payment_method_types[0].id : null;
            this.form.rent_payment.payment_destination_id = this.payment_destinations.length > 0 ? this.payment_destinations[0].id : null;
        },
        setAffectationIgvType() {

            let affectation_igv_type = _.find(this.getAllowedAffectationIgvTypes, {id: this.configuration.affectation_igv_type_id})
            this.form.affectation_igv_type_id = (affectation_igv_type) ? affectation_igv_type.id : '10'

        },
        searchRemoteCustomers(input) {
            if (input.length > 0) {
                this.loading = true;

                const params = {
                    'input': input,
                    'search_by_barcode': this.search_item_by_barcode ? 1 : 0
                }
                this.$http
                    .get(`/hotels/reception/tables/customers`, {params})
                    .then((response) => {
                        this.customers = response.data.customers;

                        this.input_person.number = null;

                        if (this.customers.length == 0) {
                            this.input_person.number = input;
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            } else {
                this.input_person.number = null;
            }
        },
        changeCustomer() {
            this.customer = this.customers
                .filter((c) => c.id === this.form.customer_id)
                .reduce((c) => c);

            this.form.customer = {...this.customer};
            this.form.data_persons = [];
            this.form.data_persons.push({
                number: this.form.customer.number,
                name: this.form.customer.name,
                type: 'adult'
            });
        },
        onGetStatus(status) {
            if (status === "DISPONIBLE") {
                return "text-success";
            }
            if (status === "OCUPADO") {
                return "text-danger";
            }
            if (status === "MANTENIMIENTO") {
                return "text-warning";
            }
            if (status === "LIMPIEZA") {
                return "text-info";
            }
        },
        clickAddPerson() {
            this.showDialogPersons = true
        },
        addRowPerson(persons) {
            this.form.quantity_persons = persons.length
            this.form.data_persons = persons
        },
        updatePersonCounts(counts) {
            this.form.adults = counts.adults;
            this.form.children = counts.children;
            this.updateTotalPersons();
        },
        initDocument() {
            this.document = {
                customer_id: null,
                customer: null,
                document_type_id: '80',
                series_id: this.series.length > 0 ? this.series[0].id : null,
                prefix: 'NV',
                establishment_id: this.room.establishment_id,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: moment().format("HH:mm:ss"),
                currency_type_id: "PEN",
                purchase_order: null,
                exchange_rate_sale: 0,
                total_prepayment: 0,
                total_charge: 0,
                total_discount: 0,
                total_exportation: 0,
                total_free: 0,
                total_taxed: 0,
                total_unaffected: 0,
                total_exonerated: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                total: 0,
                subtotal: 0,
                operation_type_id: "0101",
                date_of_due: moment().format("YYYY-MM-DD"),
                delivery_date: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                additional_information: null,
                actions: {
                    format_pdf: "a4",
                },
                dispatch_id: null,
                dispatch: null,
                is_receivable: false,
                payments: [],
                hotel: {},
                hotel_data_persons: [],
                source_module: 'HOTEL',
                hotel_rent_id: null
            };
        },
        async onGoToInvoice() {
            try {
                await this.onUpdateItemsWithExtras();
                await this.onCalculateTotals();
                await this.setDataPayments();

                let validate_payment_destination = this.validatePaymentDestination();
                if (validate_payment_destination.error_by_item > 0) {
                    return this.$message.error("El destino del pago es obligatorio");
                }

                this.document.customer_id = this.form.customer_id
                this.document.customer = this.form.customer
                this.document.hotel_data_persons = this.form.data_persons
                this.loading = true;

                const response = await this.$http.post(`/${this.resource_documents}`, this.document);

                if (response.data.success) {
                    this.form.sale_note_id = response.data.data.id;
                    this.successGoToInvoice();
                    this.$emit("update:showDialog", false);
                    this.saveCashDocument();
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                console.error("Error en onGoToInvoice:", error);
                if (error.response) {
                    this.errors = error.response.data;
                } else {
                    this.$message.error(error.response.data.message || "Error inesperado");
                }
            } finally {
                this.loading = false;
            }
        },
        async onUpdateItemsWithExtras() {
            try {
                if (!this.document.items || !Array.isArray(this.document.items)) {
                    throw new Error("document.items no está definido o no es un array");
                }

                this.document.items = this.document.items.map((it) => {
                    if (!it.item) {
                        throw new Error("Elemento en items no tiene la propiedad 'item'");
                    }

                    let timeUnit = 'noche(s)';
                    if (this.form.rental_period_type === 'hour') {
                        timeUnit = 'hora(s)';
                    } else if (this.form.rental_period_type === 'month') {
                        timeUnit = 'mes(es)';
                    }
                    
                    const name = `${it.item.description} x ${this.form.duration} ${timeUnit}`;
                    it.item.description = name;
                    it.item.full_description = name;
                    it.name_product_pdf = name;
                    it.quantity = 1;

                    const newTotal = parseFloat(this.form.total_to_pay);

                    it.input_unit_price_value = newTotal;
                    it.item.unit_price = newTotal;
                    it.unit_value = newTotal;

                    const newItem = calculateRowItem(it, "PEN", 3, this.percentage_igv);
                    return newItem;
                });

            } catch (error) {
                this.axiosError(error);
                throw error;
            }
        },
        saveCashDocument() {
            this.$http
                .post(`/cash/cash_document`, this.form_cash_document)
                .then((response) => {
                    if (!response.data.success) {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        onCalculateTotals() {
            let total_exportation = 0;
            let total_taxed = 0;
            let total_exonerated = 0;
            let total_unaffected = 0;
            let total_free = 0;
            let total_igv = 0;
            let total_value = 0;
            let total = 0;
            let total_plastic_bag_taxes = 0;
            let total_discount = 0;
            let total_charge = 0;
            this.document.items.forEach((row) => {
                total_discount += parseFloat(row.total_discount);
                total_charge += parseFloat(row.total_charge);

                if (row.affectation_igv_type_id === "10") {
                    total_taxed += parseFloat(row.total_value);
                }

                if (row.affectation_igv_type_id === '20') {
                    total_exonerated += parseFloat(row.total_value)
                }

                if (["10", "20", "30", "40"].indexOf(row.affectation_igv_type_id) < 0) {
                    total_free += parseFloat(row.total_value);
                }

                if (
                    ["10", "20", "30", "40"].indexOf(row.affectation_igv_type_id) > -1
                ) {
                    total_igv += parseFloat(row.total_igv);
                    total += parseFloat(row.total);
                }

                total_value += parseFloat(row.total_value);
                total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes);

                if (["13", "14", "15"].includes(row.affectation_igv_type_id)) {
                    let unit_value =
                        row.total_value / row.quantity / (1 + this.percentage_igv / 100);
                    let total_value_partial = unit_value * row.quantity;
                    row.total_taxes = row.total_value - total_value_partial;
                    row.total_igv = row.total_value - total_value_partial;
                    row.total_base_igv = total_value_partial;
                    total_value -= row.total_value;
                }
            });

            this.document.total_exportation = _.round(total_exportation, 2);
            this.document.total_taxed = _.round(total_taxed, 2);
            this.document.total_exonerated = _.round(total_exonerated, 2);
            this.document.total_unaffected = _.round(total_unaffected, 2);
            this.document.total_free = _.round(total_free, 2);
            this.document.total_igv = _.round(total_igv, 2);
            this.document.total_value = _.round(total_value, 2);
            this.document.total_taxes = _.round(total_igv, 2);
            this.document.total_plastic_bag_taxes = _.round(
                total_plastic_bag_taxes,
                2
            );
            this.document.total = _.round(
                total + this.document.total_plastic_bag_taxes,
                2
            );
            this.document.subtotal = _.round(
                this.document.total,
                2
            );
        },
        validatePaymentDestination() {
            let error_by_item = 0;
            this.document.payments.forEach((item) => {
                if (item.payment_destination_id == null) error_by_item++;
            });

            return {
                error_by_item: error_by_item,
            };
        },
        successGoToInvoice() {
            this.initFormCashDocument()
            this.form_cash_document.sale_note_id = this.form.sale_note_id
            this.showDialogSaleNoteOptions = true
        },
        initFormCashDocument() {
            this.form_cash_document = {
                document_id: null,
                sale_note_id: null,
            };
        },
        async setDataPayments(){
            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: this.form.date_of_issue,
                payment_method_type_id: this.form.rent_payment.payment_method_type_id,
                payment_destination_id: this.form.rent_payment.payment_destination_id,
                reference: this.form.rent_payment.reference,
                payment: this.document.total
            });
        }
    },
};
</script>

<style>

</style>