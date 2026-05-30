<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels/reception">
                    <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building-skyscraper"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Vista general recepción</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap dropdown">
                    <button
                        aria-expanded="false"
                        class="btn btn-custom btn-sm mt-2 me-2 dropdown-toggle"
                        data-bs-toggle="dropdown"
                        type="button"
                    >
                        <i class="fa fa-download"></i> Exportar
                        <span class="caret"></span>
                    </button>
                    <div
                        class="dropdown-menu"
                        role="menu"
                        style="
                            position: absolute;
                            will-change: transform;
                            top: 0px;
                            left: 0px;
                            transform: translate3d(0px, 42px, 0px);
                        "
                        x-placement="bottom-start"
                    >
                        <a
                            class="dropdown-item text-1"
                            href="#"
                            @click.prevent="clickExport()"
                        >Reporte recepción</a>

                    </div>
                </div>
            </div>

        </div>
        <div class="card tab-content-default row-new mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Vista general recepción</h3>
            </div> -->
            <div class="card-body">
                <div class="row">
                    <!-- piso -->
                    <div class="col-md-3 col-sm-12 pb-2">
                        <el-select
                            v-model="hotel_floor_id"
                            :disabled="loading"
                            clearable
                            placeholder="Ubicación"
                            @change="searchRooms"
                        >
                            <el-option
                                v-for="f in floors"
                                :key="f.id"
                                :label="f.description"
                                :value="f.id"
                            >
                            </el-option>
                        </el-select>
                    </div>
                    <!-- Campo de busqueda -->
                    <div class="col-md-4 col-sm-12 pb-2">
                        <el-input
                            v-model="hotel_name_room"
                            clearable
                            placeholder="Buscar habitación"
                            prefix-icon="el-icon-search"
                            style="width: 100%;"
                            @input="searchRooms"
                        >
                        </el-input>
                    </div>
                    <!-- filtro de status con contadores -->
                    <div class="col-md-3 col-sm-12 pb-2 text-end ms-auto">
                        <el-select
                            v-model="hotel_status_room"
                            :disabled="loading"
                            clearable
                            placeholder="Estado"
                            style="width: 100%;"
                            @change="onFilterByStatus"
                        >
                            <el-option
                                label="Todos"
                                value=""
                            >
                                <span>Todos</span>
                                <span class="status-count-badge">{{ statusCounts.total }}</span>
                            </el-option>
                            <el-option
                                v-for="st in roomStatus"
                                :key="st"
                                :label="`${st} (${statusCounts[st] || 0})`"
                                :value="st"
                            >
                                <span>{{ st }}</span>
                                <span class="status-count-badge">{{ statusCounts[st] || 0 }}</span>
                            </el-option>
                            <el-option label="RESERVADA" value="RESERVADA">
                                <span>RESERVADA</span>
                                <span class="status-count-badge">{{ statusCounts.RESERVADA || 0 }}</span>
                            </el-option>
                        </el-select>
                    </div>
                </div>

                <!-- Resumen rápido de contadores -->
                <div class="status-summary-bar">
                    <span class="ss-chip ss-available" @click="onFilterByStatus('DISPONIBLE')">
                        <span class="ss-dot"></span>Disponibles
                        <span class="ss-num">{{ statusCounts.DISPONIBLE || 0 }}</span>
                    </span>
                    <span class="ss-chip ss-occupied" @click="onFilterByStatus('OCUPADO')">
                        <span class="ss-dot"></span>Ocupadas
                        <span class="ss-num">{{ statusCounts.OCUPADO || 0 }}</span>
                    </span>
                    <span class="ss-chip ss-cleaning" @click="onFilterByStatus('LIMPIEZA')">
                        <span class="ss-dot"></span>Limpieza
                        <span class="ss-num">{{ statusCounts.LIMPIEZA || 0 }}</span>
                    </span>
                    <span class="ss-chip ss-maintenance" @click="onFilterByStatus('MANTENIMIENTO')">
                        <span class="ss-dot"></span>Mantenimiento
                        <span class="ss-num">{{ statusCounts.MANTENIMIENTO || 0 }}</span>
                    </span>
                    <span class="ss-chip ss-reserved" @click="onFilterByStatus('RESERVADA')">
                        <span class="ss-dot"></span>Reservadas
                        <span class="ss-num">{{ statusCounts.RESERVADA || 0 }}</span>
                    </span>
                </div>
                <div class="room-container mt-3">
                    <div v-for="ro in filteredItems"
                         :key="ro.id"
                         class="card hotel-rooms m-0">
                        <el-card
                            :class="onGetColorStatus(ro)"
                            shadow="never"
                            class="room-el-card"
                        >
                            <!-- Badge "Reservada" solo cuando hay una reserva activa (en ventana) -->
                            <div v-if="ro.is_active_reservation" class="reservation-indicator">
                                Reservada
                            </div>

                            <!-- Chip-contador de reservas vigentes.
                                 Se oculta cuando la habitación ya muestra el badge "Reservada"
                                 y solo existe esa reserva (sería redundante). -->
                            <div
                                v-if="ro.reservations_count > 0 && !(ro.is_active_reservation && ro.reservations_count === 1)"
                                class="reservations-count-chip"
                                :style="(ro.has_reservation || ro.status !== 'DISPONIBLE') ? 'margin-top:40px;' : ''"
                                :class="{ 'chip-on-reserved': ro.is_active_reservation }"
                                @click.stop="onOpenReservationsList(ro)"
                                :title="`${ro.reservations_count} reserva${ro.reservations_count === 1 ? '' : 's'} vigente${ro.reservations_count === 1 ? '' : 's'} — clic para ver detalle`"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span style="color:black!important;" class="reservations-count-num">{{ ro.reservations_count }}</span>
                            </div>
                            
                            <div class="card-rent">
                                <!-- <h4>{{ ro.status }}</h4> -->

                                   <span v-if="!ro.has_reservation" class="text-muted">{{ ro.category.description }}</span>
                                   <h2 class="mt-0">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="32"  height="32"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-door"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 12v.01" /><path d="M3 21h18" /><path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16" /></svg>
                                      <b>{{ ro.name }}</b>
                                      <!-- Indicador de deuda al lado del nombre (solo si está disponible) -->
                                      <span v-if="ro.status !== 'DISPONIBLE' && !ro.has_reservation && getRoomDebt(ro) > 0" class="debt-indicator-inline">
                                          Falta pagar: {{ getFormattedDebt(ro) }}
                                      </span>
                                    </h2>
                                   <p v-if="!ro.has_reservation && ro.description" class="description">{{ ro.description }}</p>
                                   
                                   <!-- Mostrar información del cliente para habitaciones con reserva -->
                                   <div v-if="ro.has_reservation && ro.rent && ro.rent.customer" class="reservation-customer-info">
                                       <p style="margin: 5px 0; font-size: 14px;">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                               <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                               <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                           </svg>
                                           <strong> {{ ro.rent.customer.name }}</strong>
                                           <span v-if="ro.rent.customer.person_type_id || ro.status == 'DISPONIBLE'" class="customer-type-badge ml-2" style="height: 100%;margin-left: 10px;">
                                               {{ ro.rent.customer.person_type.description }}
                                           </span>
                                       </p>
                                       
                                       <!-- Contador regresivo para reservas -->
                                       <div class="room-top-right-elements" style="margin-top: 10px;">
                                           <div class="rent-countdown-simple" v-if="ro.rent && timeRemaining[ro.id]">
                                               <div class="countdown-display">
                                                   <div class="countdown-unit">
                                                       <div class="countdown-value">{{ timeRemaining[ro.id].days }}</div>
                                                       <div class="countdown-label">{{ timeRemaining[ro.id].days === 1 ? 'día' : 'días' }}</div>
                                                   </div>
                                                   <div class="countdown-separator"></div>
                                                   <div class="countdown-unit">
                                                       <div class="countdown-value">{{ String(timeRemaining[ro.id].hours).padStart(2, '0') }}</div>
                                                       <div class="countdown-label">{{ timeRemaining[ro.id].hours === 1 ? 'hora' : 'hora' }}</div>
                                                   </div>
                                                   <div class="countdown-separator"></div>
                                                   <div class="countdown-unit">
                                                       <div class="countdown-value">{{ String(timeRemaining[ro.id].minutes).padStart(2, '0') }}</div>
                                                       <div class="countdown-label">{{ timeRemaining[ro.id].minutes === 1 ? 'min' : 'min' }}</div>
                                                   </div>
                                                   <div class="countdown-separator"></div>
                                                   <div class="countdown-unit">
                                                       <div class="countdown-value">{{ String(timeRemaining[ro.id].seconds).padStart(2, '0') }}</div>
                                                       <div class="countdown-label">{{ timeRemaining[ro.id].seconds === 1 ? 'seg' : 'seg' }}</div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>

                                <template v-if="ro.status === 'LIMPIEZA'">
                                    <!-- Si hay un rent activo (limpieza rápida sobre habitación
                                         ocupada), mostrar info del huésped para no perder el
                                         contexto de quién está ocupando la habitación. -->
                                    <div v-if="ro.rent && ro.rent.customer" class="cleaning-with-guest">
                                        <p class="mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/>
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                            </svg>
                                            <strong>{{ ro.rent.customer.name }}</strong>
                                            <span class="badge badge-warning ml-2" style="margin-left:6px">Limpieza rápida</span>
                                        </p>
                                    </div>
                                    <el-button
                                        v-if="canManageCleaning && !ro.has_cleaner_assigned"
                                        :disabled="loading"
                                        :loading="loading"
                                        title="Asignar limpiador"
                                        class="btn btn-block btn-info w-100"
                                        @click="onAssignCleaner(ro)"
                                    >
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
                                        Asignar Limpiador
                                    </el-button>
                                    <el-button
                                        v-else-if="canManageCleaning && ro.has_cleaner_assigned"
                                        :disabled="loading"
                                        :loading="loading"
                                        title="Finalizar limpieza"
                                        class="btn btn-block btn-info w-100"
                                        @click="onFinalizeClean(ro)"
                                    >
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-spray"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10m0 2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2z" /><path d="M6 10v-4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v4" /><path d="M15 7h.01" /><path d="M18 9h.01" /><path d="M18 5h.01" /><path d="M21 3h.01" /><path d="M21 7h.01" /><path d="M21 11h.01" /><path d="M10 7h1" /></svg>
                                        Finalizar limpieza
                                    </el-button>
                                    <div v-else-if="!ro.has_cleaner_assigned" class="text-center text-muted">
                                        <small>Esperando asignación de limpiador...</small>
                                    </div>
                                </template>
                                <template v-if="ro.status === 'MANTENIMIENTO'">
                                    <h4 class="text-warning text-center mb-0">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-tool"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 10h3v-3l-3.5 -3.5a6 6 0 0 1 8 8l6 6a2 2 0 0 1 -3 3l-6 -6a6 6 0 0 1 -8 -8l3.5 3.5" /></svg>
                                        <b>En mantenimiento:</b>
                                    </h4>
                                    <p class="text-center">Debe cambiar el estado a <b>Disponible</b> en el módulo Habitaciones.</p>
                                </template>

                                <template v-if="ro.status === 'OCUPADO'">
                                    <div>
                                        <p :style="ro.rent.license_plate ? 'margin: 0;' : ''">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                                    <span class="">{{ ro.rent.customer.name }}</span>
                                    <span v-if="ro.rent.customer.person_type_id" class="customer-type-badge ml-2" style="height: 100%;margin-left: 10px;">
                                        {{ ro.rent.customer.person_type.description }}
                                    </span><br>
                                    <span class="license-plate-display" v-if="ro.rent.license_plate">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 17a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                            <path d="M7 9v0a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v0"></path>
                                            <circle cx="12" cy="15" r="1"></circle>
                                        </svg>
                                        {{ ro.rent.license_plate }}
                                    </span>
                                    </p>
                                    <!-- Contenedor para contador y tooltip en esquina superior derecha -->
                                    <div class="room-top-right-elements">
                                        <!-- Contador regresivo -->
                                    <div class="rent-countdown-simple" v-if="ro.rent && timeRemaining[ro.id]">
                                        <div class="countdown-display">
                                            <div class="countdown-unit">
                                                <div class="countdown-value">{{ timeRemaining[ro.id].days }}</div>
                                                <div class="countdown-label">{{ timeRemaining[ro.id].days === 1 ? 'día' : 'días' }}</div>
                                            </div>
                                            <div class="countdown-separator"></div>
                                            <div class="countdown-unit">
                                                <div class="countdown-value">{{ String(timeRemaining[ro.id].hours).padStart(2, '0') }}</div>
                                                <div class="countdown-label">{{ timeRemaining[ro.id].hours === 1 ? 'hora' : 'hora' }}</div>
                                            </div>
                                            <div class="countdown-separator"></div>
                                            <div class="countdown-unit">
                                                <div class="countdown-value">{{ String(timeRemaining[ro.id].minutes).padStart(2, '0') }}</div>
                                                <div class="countdown-label">{{ timeRemaining[ro.id].minutes === 1 ? 'min' : 'min' }}</div>
                                            </div>
                                            <div class="countdown-separator"></div>
                                            <div class="countdown-unit">
                                                <div class="countdown-value">{{ String(timeRemaining[ro.id].seconds).padStart(2, '0') }}</div>
                                                <div class="countdown-label">{{ timeRemaining[ro.id].seconds === 1 ? 'seg' : 'seg' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                        <!-- Indicador de observaciones -->
                                        <div 
                                            v-if="ro.rent && ro.rent.notes" 
                                            class="observations-indicator"
                                            @mouseenter="showObservationTooltip(ro, $event)"
                                            @mouseleave="hideObservationTooltip"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                            </svg>
                                        </div>
                                    </div>
                                    <!---<p>
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M11 15h1" /><path d="M12 15v3" /></svg>
                                        <span class="">{{ formatDate(ro.rent.output_date) }}
                                        <el-button
                                            title="Extender Tiempo"
                                            class="btn btn-xs"
                                            @click="ShowDialogExtendTimeRoom(ro)"
                                        > Modificar
                                        </el-button>
                                        <br>
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="18"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                        {{ ro.rent.output_time }}
                                    </span>
                                    </p>-->

                                </div>
                                <div class="row" style="margin-right:0px;margin-left:0px;margin-top: auto;">
                                    <div class="col-3" style="padding-left:0px;">
                                        <button
                                            title="Agregar productos"
                                            class="btn btn-block btn-danger px-0 py-2 w-100"
                                            data-toggle="tooltip"
                                            @click="onGoToAddProducts(ro)"
                                        >
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-hotel-service"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.5 10a1.5 1.5 0 0 1 -1.5 -1.5a5.5 5.5 0 0 1 11 0v10.5a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2c0 -1.38 .71 -2.444 1.88 -3.175l4.424 -2.765c1.055 -.66 1.696 -1.316 1.696 -2.56a2.5 2.5 0 1 0 -5 0a1.5 1.5 0 0 1 -1.5 1.5z" /></svg> 
                                        </button>
                                    </div>
                                    <div class="col-9" style="padding-right:0px;">
                                        <button
                                            title="Opciones de habitación ocupada"
                                            :class="[
                                                'btn btn-block px-0 py-2 w-100',
                                                ro.rent && ro.rent.rental_period_type === 'month' 
                                                    ? 'btn-secondary' 
                                                    : 'btn-danger'
                                            ]"
                                            :style="ro.rent && ro.rent.rental_period_type === 'month' 
                                                ? 'background-color: white !important; color: #6c757d !important; border: 1px solid #6c757d !important;'
                                                : 'background-color: white !important; color: black !important;'"
                                            @click="showOccupiedModalWithDebug(ro)"
                                        >Ocupado
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-door-exit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 12v.01" /><path d="M3 21h18" /><path d="M5 21v-16a2 2 0 0 1 2 -2h7.5m2.5 10.5v7.5" /><path d="M14 7h7m-3 -3l3 3l-3 3" /></svg>
                                        </button>
                                    </div>
                                </div>
                                </template>
                                <el-button
                                    v-if="ro.ready_for_checkin && canManageRooms"
                                    class="btn btn-block btn-success w-100"
                                    style="margin-top: 27px;"
                                    @click="onCheckIn(ro)"
                                >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                        <path d="M15 19l2 2l4 -4" />
                                    </svg> Check-in
                                </el-button>
                                <el-button
                                    v-else-if="(ro.status === 'DISPONIBLE' || (ro.status === 'OCUPADO' && ro.has_reservation)) && canManageRooms"
                                    class="btn btn-block btn-success w-100"
                                    style="margin-top: 27px;"
                                    @click="onToRent(ro)"
                                >
                                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-door-enter">
                                        <g transform="scale(-1 1) translate(-24 0)">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M13 12v.01" />
                                            <path d="M3 21h18" />
                                            <path d="M5 21v-16a2 2 0 0 1 2 -2h6m4 10.5v7.5" />
                                            <path d="M21 7h-7m3 -3l-3 3l3 3" />
                                        </g>
                                    </svg> 
                                    <span v-if="ro.status === 'DISPONIBLE'">Disponible</span>
                                    <span v-else-if="ro.status === 'OCUPADO' && ro.has_reservation">Reservar (Checkout futuro)</span>
                                </el-button>
                            </div>

                        </el-card>
                    </div>
                </div>
            </div>
        </div>
        <ModalRoomRates
            :room="room"
            :visible.sync="openModalRoomRates"
            @onAddRoomRate="onAddRoomRate"
            @onDeleteRate="onDeleteRate"
        ></ModalRoomRates>
        <ExtendTimeRoom
            :room="roomToExtend"
            :visible.sync="openDialogExtendTimeRoom"
            @onRefresh="onRefresh">
        </ExtendTimeRoom>
        <reception-export
            :showDialog.sync="showExportDialog"
            :user-type="userType"
            :establishment-id="establishmentId"
        >
        </reception-export>
        
        <!-- Modal para opciones de habitación ocupada -->
        <el-dialog
            title="Opciones de habitación ocupada"
            :visible.sync="showOccupiedOptionsModal"
            width="600px"
            :close-on-click-modal="false"
        >
            <div class="occupied-options-grid">
                <button
                    class="btn btn-primary occupied-option-btn"
                    @click="onGoToCheckout(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-door-exit">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M13 12v.01" />
                        <path d="M3 21h18" />
                        <path d="M5 21v-16a2 2 0 0 1 2 -2h7.5m2.5 10.5v7.5" />
                        <path d="M14 7h7m-3 -3l3 3l-3 3" />
                    </svg>
                    Checkout
                </button>
                
                <button
                    v-if="canManageRooms"
                    class="btn btn-info occupied-option-btn"
                    @click="onQuickClean(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-spray">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 10m0 2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2z" />
                        <path d="M6 10v-4a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v4" />
                        <path d="M15 7h.01" />
                        <path d="M18 9h.01" />
                        <path d="M18 5h.01" />
                        <path d="M21 3h.01" />
                        <path d="M21 7h.01" />
                        <path d="M21 11h.01" />
                        <path d="M10 7h1" />
                    </svg>
                    Limpieza rápida
                </button>
                
                <button
                    v-if="canManageRooms"
                    class="btn btn-warning occupied-option-btn"
                    @click="ShowDialogExtendTimeRoom(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                        <path d="M12 7v5l3 3" />
                    </svg>
                    Extender estadía
                </button>
                
                <button
                    v-if="canManageRooms"
                    class="btn btn-secondary occupied-option-btn"
                    @click="onViewEditObservations(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 9l1 0" />
                        <path d="M9 13l6 0" />
                        <path d="M9 17l6 0" />
                    </svg>
                    Ver/editar observaciones
                </button>
                
                <button
                    v-if="canManageRooms"
                    class="btn btn-success occupied-option-btn"
                    @click="onChangeRoom(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-exchange">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 10h10l-3 -3m0 6l3 3" />
                        <path d="M17 14h-10l3 3m0 -6l-3 -3" />
                    </svg>
                    Cambiar de habitación
                </button>
                
                <button
                    v-if="canManageRooms"
                    class="btn btn-primary occupied-option-btn"
                    @click="onAdvancePayment(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <rect x="7" y="9" width="14" height="10" rx="2"/>
                        <circle cx="14" cy="14" r="2"/>
                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"/>
                    </svg>
                    Adelanto de pago
                </button>

                <button
                    v-if="canManageRooms"
                    class="btn btn-danger occupied-option-btn"
                    @click="onDeleteRecord(selectedRoom)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7l16 0" />
                        <path d="M10 11l0 6" />
                        <path d="M14 11l0 6" />
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg>
                    Eliminar registro
                </button>
            </div>
        </el-dialog>

        <!-- Modal para cambiar de habitación -->
        <el-dialog
            title="Cambiar de habitación"
            :visible.sync="showChangeRoomModal"
            width="500px"
            :close-on-click-modal="false"
        >
            <div class="change-room-container">
                <div class="current-room-info">
                    <h4>Habitación actual:</h4>
                    <div class="room-details">
                        <strong>{{ selectedRoom?.name }}</strong> - {{ selectedRoom?.category?.description }}
                    </div>
                    <div class="customer-info">
                        <small>Cliente: {{ selectedRoom?.rent?.customer?.name }}</small>
                    </div>
                </div>
                
                <div class="new-room-selection">
                    <h4>Seleccionar nueva habitación:</h4>
                    <el-select
                        v-model="selectedNewRoom"
                        placeholder="Buscar habitación disponible..."
                        filterable
                        clearable
                        :loading="loadingChangeRoom"
                        style="width: 100%;"
                        @change="onNewRoomSelected"
                    >
                        <el-option
                            v-for="room in availableRooms"
                            :key="room.id"
                            :label="`${room.name} - ${room.category?.description || ''}`"
                            :value="room.id"
                        >
                            <div class="room-option">
                                <span class="room-name">{{ room.name }}</span>
                                <span class="room-category">{{ room.category?.description || '' }}</span>
                            </div>
                        </el-option>
                    </el-select>
                    
                    <!-- Select de tarifas para la nueva habitación -->
                    <div v-if="selectedNewRoom" class="rate-selection">
                        <h4>Seleccionar tarifa:</h4>
                        <el-select
                            v-model="selectedNewRate"
                            placeholder="Seleccionar tarifa..."
                            style="width: 100%;"
                            @change="onRateChanged"
                        >
                            <el-option
                                v-for="rate in availableRates"
                                :key="rate.id"
                                :label="`${rate.description} - S/ ${rate.price}`"
                                :value="rate.id"
                            >
                                <div class="rate-option">
                                    <span class="rate-name">{{ rate.description }}</span>
                                    <span class="rate-price">S/ {{ rate.price }}</span>
                                </div>
                            </el-option>
                        </el-select>
                        
                        <div v-if="selectedNewRate" class="rate-preview">
                            <small>
                                <strong>Tarifa seleccionada:</strong> {{ getSelectedRateDescription() }}<br>
                                <strong>Precio:</strong> S/ {{ getSelectedRatePrice() }}
                            </small>
                        </div>
                    </div>

                    <!-- Resumen del cambio (consumido / restante / diferencia) -->
                    <div v-if="changePreview" class="change-room-preview">
                        <div class="preview-row">
                            <span>Consumido en {{ selectedRoom?.name }}:</span>
                            <strong>{{ changePreview.consumed }} {{ changePreview.unit }} · S/ {{ changePreview.consumedTotal.toFixed(2) }}</strong>
                        </div>
                        <div class="preview-row">
                            <span>Restante en nueva habitación:</span>
                            <strong>{{ changePreview.remaining }} {{ changePreview.unit }} · S/ {{ changePreview.remainingTotal.toFixed(2) }}</strong>
                        </div>
                        <div class="preview-row preview-diff" :class="changePreview.diff >= 0 ? 'is-up' : 'is-down'">
                            <span>Diferencia de precio (restante):</span>
                            <strong>S/ {{ changePreview.diff.toFixed(2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <span slot="footer" class="dialog-footer">
                <el-button @click="showChangeRoomModal = false">Cancelar</el-button>
                <el-button
                    type="primary"
                    @click="confirmRoomChange"
                    :loading="loadingChangeRoom"
                    :disabled="!selectedNewRoom || !selectedNewRate"
                >
                    Cambiar habitación
                </el-button>
            </span>
        </el-dialog>

        <!-- Modal para seleccionar limpiador -->
        <el-dialog
            :title="getCleanerModalTitle()"
            :visible.sync="showCleanerModal"
            width="400px"
            @close="showCleanerModal = false"
        >
            <div class="form-group">
                <label class="control-label">Seleccionar Limpiador</label>
                <el-select
                    v-model="selectedCleaner"
                    placeholder="Seleccione un limpiador"
                    style="width: 100%"
                    :loading="loadingCleaners"
                >
                    <el-option
                        v-for="cleaner in cleaners"
                        :key="cleaner.id"
                        :label="cleaner.name"
                        :value="cleaner.id"
                    >
                        <span>{{ cleaner.name }}</span>
                        <span style="float: right; color: #8492a6; font-size: 13px">{{ cleaner.email }}</span>
                    </el-option>
                </el-select>
            </div>
            
            <div class="form-group">
                <label class="control-label">Notas (opcional)</label>
                <el-input
                    type="textarea"
                    v-model="cleaningNotes"
                    placeholder="Notas sobre la limpieza..."
                    :rows="3"
                ></el-input>
            </div>

            <span slot="footer" class="dialog-footer">
                <el-button @click="showCleanerModal = false">Cancelar</el-button>
                <el-button 
                    type="primary" 
                    @click="confirmStartCleaning"
                    :loading="loadingStartCleaning"
                    :disabled="!selectedCleaner"
                >
                    Iniciar Limpieza
                </el-button>
            </span>
        </el-dialog>

        <!-- Modal para ver/editar observaciones -->
        <el-dialog
            title="Observaciones del Registro"
            :visible.sync="showObservationsModal"
            width="600px"
            :close-on-click-modal="false"
        >
            <div class="observations-container">
                <div class="form-group">
                    <label for="observations" class="form-label">Observaciones:</label>
                    <el-input
                        id="observations"
                        type="textarea"
                        :rows="6"
                        placeholder="Ingrese las observaciones del registro..."
                        v-model="observationsText"
                    >
                    </el-input>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showObservationsModal = false">Cancelar</el-button>
                <el-button
                    type="primary"
                    @click="saveObservations"
                    :loading="loadingObservations"
                >
                    Guardar
                </el-button>
            </span>
        </el-dialog>

        <!-- Modal: Reservas vigentes de la habitación -->
        <el-dialog
            :visible.sync="showReservationsListModal"
            width="640px"
            :close-on-click-modal="true"
            append-to-body
            custom-class="reservations-list-dialog"
        >
            <template slot="title">
                <div>
                    <span style="font-weight:600">Reservas vigentes</span>
                    <span v-if="reservationsListRoom" style="color:#6b7280; margin-left:8px">— {{ reservationsListRoom.name }}</span>
                </div>
            </template>

            <div v-if="!reservationsListRoom || !reservationsListRoom.upcoming_reservations || !reservationsListRoom.upcoming_reservations.length"
                 class="reservations-empty">
                No hay reservas vigentes para esta habitación.
            </div>

            <ul v-else class="reservations-list">
                <li
                    v-for="r in reservationsListRoom.upcoming_reservations"
                    :key="r.id"
                    class="reservations-list-item"
                    :class="{ 'is-current': r.is_current }"
                >
                    <div class="rli-left">
                        <div class="rli-customer">{{ r.customer_name }}</div>
                        <div class="rli-dates">
                            <span>{{ r.input_date }} {{ (r.input_time || '').slice(0,5) }}</span>
                            <span class="rli-arrow">→</span>
                            <span>{{ r.output_date }} {{ (r.output_time || '').slice(0,5) }}</span>
                        </div>
                        <div class="rli-meta">
                            <span v-if="r.duration">{{ r.duration }} {{ r.duration === 1 ? 'noche' : 'noches' }}</span>
                            <span v-if="r.license_plate" class="rli-plate">{{ r.license_plate }}</span>
                            <span v-if="r.notes" class="rli-notes" :title="r.notes">📝</span>
                        </div>
                    </div>
                    <div class="rli-right">
                        <span v-if="r.is_current" class="rli-tag tag-current">En curso</span>
                        <span v-else-if="r.is_future" class="rli-tag tag-future">Próxima</span>
                        <span v-else class="rli-tag tag-other">{{ r.status }}</span>
                        <el-button size="mini" @click="onGoToReservationFromList(r)" plain>Abrir</el-button>
                    </div>
                </li>
            </ul>

            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="showReservationsListModal = false">Cerrar</el-button>
                <el-button size="small" type="primary" @click="onGoToCalendarFromList">Ir al calendario</el-button>
            </span>
        </el-dialog>

        <!-- Modal: Adelanto de pago -->
        <el-dialog
            title="Adelanto de pago"
            :visible.sync="showAdvancePaymentModal"
            width="500px"
            :close-on-click-modal="false"
        >
            <div v-if="selectedRoom && selectedRoom.rent">
                <div class="alert alert-info">
                    <strong>Cliente:</strong> {{ selectedRoom.rent.customer && selectedRoom.rent.customer.name }}<br>
                    <strong>Habitación:</strong> {{ selectedRoom.name }}<br>
                    <strong>Deuda actual:</strong> S/ {{ getRoomDebt(selectedRoom).toFixed(2) }}
                    <span v-if="getRoomDebt(selectedRoom) <= 0" class="d-block mt-2">
                        <em>Sin deuda — este pago quedará como adelanto y se aplicará a futuros cargos.</em>
                    </span>
                </div>
                <div class="form-group">
                    <label class="control-label">Monto del adelanto</label>
                    <el-input
                        v-model="advancePayment.amount"
                        type="number"
                        placeholder="0.00"
                    >
                        <template slot="prepend">S/</template>
                    </el-input>
                </div>
                <div class="form-group">
                    <label class="control-label">Método de pago</label>
                    <el-select v-model="advancePayment.method" style="width: 100%">
                        <el-option label="Efectivo" value="cash"></el-option>
                        <el-option label="Tarjeta de Crédito" value="credit_card"></el-option>
                        <el-option label="Tarjeta de Débito" value="debit_card"></el-option>
                        <el-option label="Transferencia" value="transfer"></el-option>
                        <el-option label="Yape/Plin" value="yape_plin"></el-option>
                    </el-select>
                </div>
                <div class="form-group">
                    <label class="control-label">Referencia (opcional)</label>
                    <el-input v-model="advancePayment.reference" placeholder="Opcional"></el-input>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showAdvancePaymentModal = false">Cancelar</el-button>
                <el-button
                    type="primary"
                    :loading="loadingAdvancePayment"
                    @click="confirmAdvancePayment"
                >Registrar adelanto</el-button>
            </span>
        </el-dialog>

        <!-- Tooltip para observaciones -->
        <div 
            v-if="observationTooltip.visible" 
            class="observation-tooltip"
            :style="{ left: observationTooltip.x + 'px', top: observationTooltip.y + 'px' }"
        >
            <div class="observation-tooltip-content">
                <div class="observation-tooltip-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <strong>Observaciones</strong>
                </div>
                <div class="observation-tooltip-text">
                    {{ observationTooltip.text }}
                </div>
            </div>
        </div>
    </div>
</template>
<style>
/* ============================================================
   REDISEÑO RECEPCIÓN — CARDS DE HABITACIÓN
   Objetivo: que las cards siempre se vean bien sin importar el
   tamaño de pantalla. Layout flex column con footer anclado,
   contenido que se adapta, botones nunca se cortan.
   ============================================================ */

.room-container {
    display: grid !important;
    gap: 14px !important;
    /* Máximo 4 columnas; se reduce gradualmente en pantallas más
       angostas hasta llegar a 1 columna en móvil. */
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    align-items: stretch;
    justify-content: start;
}

@media (max-width: 1199px) {
    .room-container {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
}
@media (max-width: 899px) {
    .room-container {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
@media (max-width: 599px) {
    .room-container {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
    }
}

.room-container > .card.hotel-rooms {
    width: 100%;
    margin: 0 !important;
    display: flex;
    background: transparent;
    border: none;
    box-shadow: none;
}

/* Sobrescribimos la regla global `.hotel-rooms .el-card { height: 180px }`
   de theme.css. Altura FIJA para que TODAS las cards sean uniformes
   (armonía visual independiente del estado/contenido). */
.room-container .room-el-card,
.hotel-rooms .room-el-card.el-card {
    width: 100%;
    height: 230px !important;
    min-height: 230px;
    display: flex;
    flex-direction: column;
    border-radius: 14px !important;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    transition: transform .18s ease, box-shadow .18s ease;
}

.room-container .room-el-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12) !important;
}

.room-container .room-el-card .el-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 14px !important;
    min-height: 0;
    gap: 0;
    overflow: hidden;
}

.room-container .room-el-card .card-rent {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    gap: 4px;
}

/* La categoría (Matrimonial / Doble) — chip pequeño sobre el título */
.room-container .room-el-card .card-rent > .text-muted {
    font-size: 12px;
    line-height: 1.2;
    opacity: 0.85;
    margin-bottom: 0;
}

/* Título / número de habitación: tamaño adaptable, sin padding extra */
.room-container .room-el-card .card-rent h2 {
    margin: 0 !important;
    padding: 0 !important;
    font-size: clamp(22px, 2.2vw, 30px);
    line-height: 1.1;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    word-break: break-word;
}

.room-container .room-el-card .card-rent h2 svg {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
}

.room-container .room-el-card .card-rent p {
    margin: 0;
    word-break: break-word;
    overflow-wrap: break-word;
    line-height: 1.35;
}

.room-container .room-el-card .card-rent p.description {
    font-size: 12px;
    opacity: 0.85;
}

/* El footer de acciones SIEMPRE pegado al fondo */
.room-container .room-el-card .card-rent > .row:last-child,
.room-container .room-el-card .card-rent > .el-button:last-child,
.room-container .room-el-card .card-rent > button:last-child {
    margin-top: auto !important;
}

/* Footer de botones: el row de "agregar productos + Ocupado" */
.room-container .room-el-card .card-rent > .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
    display: flex;
    flex-wrap: nowrap;
    gap: 6px;
    width: 100%;
}

.room-container .room-el-card .card-rent > .row > [class*="col-"] {
    padding: 0 !important;
}

/* Botones del footer: tamaño consistente, no se cortan */
.room-container .room-el-card .card-rent .btn,
.room-container .room-el-card .card-rent .el-button {
    min-height: 40px;
    padding: 8px 10px !important;
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.room-container .room-el-card .card-rent .btn svg,
.room-container .room-el-card .card-rent .el-button svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* El botón grande (Disponible / Check-in) anclado al final */
.room-container .room-el-card .card-rent > .el-button {
    margin-top: auto !important;
    width: 100%;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* Forzar reset del margin-top:27px inline que rompe el layout en cards
   con contenido cuando el botón hereda height del row anterior. */
.room-container .room-el-card .card-rent > .el-button[style*="margin-top: 27px"] {
    margin-top: auto !important;
}

/* Reposicionamos el countdown: en lugar de absolute en la esquina
   (que choca con el nombre de la habitación), lo integramos en el
   flujo cuando el espacio es reducido. */
.room-container .room-el-card .room-top-right-elements {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    z-index: 5;
    max-width: 55%;
    flex-wrap: wrap;
    justify-content: flex-end;
}

/* Indicador de deuda al lado del nombre — que envuelva en vez de salirse */
.debt-indicator-inline {
    display: inline-block;
    margin-left: 0 !important;
    padding: 3px 8px;
    background: #ff4757;
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    animation: pulse 2s infinite;
    white-space: nowrap;
    flex-shrink: 0;
}

/* AGENTE VENDEDOR / customer-type-badge — que no rompa el layout */
.customer-type-badge {
    display: inline-block;
    padding: 2px 8px;
    background-color: #6c757d;
    color: white;
    font-size: 10px;
    font-weight: 600;
    border-radius: 6px;
    line-height: 1.4;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-left: 6px !important;
    vertical-align: middle;
    white-space: nowrap;
}

/* Placa: contraste asegurado, no se sale */
.license-plate-display {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 0 !important;
    margin-top: 4px;
    padding: 3px 8px;
    background: rgba(0, 0, 0, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: 0.5px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Info de cliente reservado */
.reservation-customer-info {
    margin-top: 10px;
}
.reservation-customer-info p {
    margin: 4px 0 !important;
    font-size: 13px !important;
    line-height: 1.4;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

/* Separación entre el nombre de la habitación (h2) y el bloque
   de información del cliente (OCUPADO / reserva / limpieza). */
.room-container .room-el-card .card-rent h2 + div,
.room-container .room-el-card .card-rent h2 + .reservation-customer-info,
.room-container .room-el-card .card-rent h2 + .cleaning-with-guest {
    margin-top: 10px;
}

/* ----- Responsive: tablets ----- */
@media (max-width: 991px) {
    .room-container .room-el-card,
    .hotel-rooms .room-el-card.el-card {
        height: 220px !important;
        min-height: 220px;
    }
    .room-container .room-el-card .card-rent h2 {
        font-size: 22px;
    }
    .room-container .room-el-card .room-top-right-elements {
        max-width: 50%;
    }
}

/* ----- Responsive: móvil ----- */
@media (max-width: 599px) {
    .room-container .room-el-card,
    .hotel-rooms .room-el-card.el-card {
        height: 210px !important;
        min-height: 210px;
    }
    .room-container .room-el-card .el-card__body {
        padding: 12px !important;
    }
    .room-container .room-el-card .card-rent h2 {
        font-size: 22px;
    }
    /* En móvil reducimos el countdown para que no aplaste el nombre */
    .room-container .room-el-card .room-top-right-elements {
        max-width: 60%;
        top: 8px;
        right: 8px;
    }
    .countdown-display {
        padding: 3px 6px !important;
    }
    .countdown-value {
        font-size: 10px !important;
    }
    .countdown-label {
        font-size: 6px !important;
    }
    .countdown-separator {
        width: 4px !important;
    }
    /* Botones más cómodos al tacto */
    .room-container .room-el-card .card-rent .btn,
    .room-container .room-el-card .card-rent .el-button {
        min-height: 44px;
        font-size: 14px;
    }
}

.occupied-options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.occupied-option-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    min-height: 100px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.occupied-option-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.occupied-option-btn svg {
    font-size: 2rem;
}

/* Indicador de observaciones */
.observations-indicator {
    right: 10px;
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid #ff9800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
    z-index: 10;
}

.observations-indicator:hover {
    background: rgba(255, 255, 255, 1);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
}

.observations-indicator svg {
    width: 12px;
    height: 12px;
    color: #ff9800;
    opacity: 0.8;
}

.observations-indicator:hover svg {
    opacity: 1;
}

/* Contenedor para elementos en esquina superior derecha */
.room-top-right-elements {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    align-items: flex-start;
    gap: 6px;
    z-index: 10;
    flex-wrap: wrap;
    max-width: 180px;
}

/* Responsive: apilar verticalmente en pantallas pequeñas */
@media (max-width: 768px) {
    .room-top-right-elements {
        flex-direction: column;
        align-items: flex-end;
        max-width: 150px;
    }
}

/* Contador regresivo estilo compacto con unidades */
.rent-countdown-simple {
    display: inline-block;
}

.countdown-display {
    background: rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    gap: 2px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    transition: all 0.3s ease;
}

.countdown-display:hover {
    background: rgba(0, 0, 0, 0.25);
    transform: scale(1.02);
}

.countdown-unit {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 20px;
}

.countdown-value {
    font-size: 11px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.countdown-label {
    font-size: 7px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1;
    margin-top: 1px;
}

.countdown-separator {
    width: 8px;
}

/* Cuando hay limpieza rápida sobre una habitación ocupada, mantenemos el
   fondo de LIMPIEZA pero damos contraste al recuadro del huésped para que
   se vea con claridad. */
.cleaning-with-guest {
    background: rgba(255,255,255,0.18);
    border-radius: 6px;
    padding: 6px 10px;
    margin-bottom: 6px;
}
.cleaning-with-guest p { color: #fff !important; }
.cleaning-with-guest .badge { color: #5d4037; }

/* Badge de contador dentro del el-option */
.status-count-badge {
    float: right;
    background: #f3f4f6;
    color: #374151;
    border-radius: 999px;
    padding: 0 8px;
    font-size: 12px;
    font-weight: 700;
    line-height: 18px;
    margin-left: 8px;
}

/* Barra resumen de contadores por estado, debajo de los filtros */
.status-summary-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 8px 0 0 0;
    padding: 8px 0;
}
.status-summary-bar .ss-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}
.status-summary-bar .ss-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.status-summary-bar .ss-dot {
    width: 10px; height: 10px; border-radius: 50%;
}
.status-summary-bar .ss-num {
    background: rgba(0,0,0,0.08);
    border-radius: 999px;
    padding: 0 8px;
    line-height: 18px;
    font-weight: 700;
}
.ss-available .ss-dot { background: #22c55e; }
.ss-occupied .ss-dot { background: #ef4444; }
.ss-cleaning .ss-dot { background: #06b6d4; }
.ss-maintenance .ss-dot { background: #f59e0b; }
.ss-reserved .ss-dot { background: #8b5cf6; }

/* Indicador de observaciones - ajustado para estar en el contenedor */
.observations-indicator {
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid #ff9800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
    flex-shrink: 0;
    margin-top: 2px;
}

.observations-indicator:hover {
    background: rgba(255, 255, 255, 1);
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(255, 152, 0, 0.3);
}

.observations-indicator svg {
    width: 12px;
    height: 12px;
    color: #ff9800;
    opacity: 0.8;
}

.observations-indicator:hover svg {
    opacity: 1;
}

/* Tooltip para observaciones */
.observation-tooltip {
    position: fixed;
    z-index: 9999;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    padding: 0;
    max-width: 300px;
    animation: fadeInTooltip 0.3s ease;
}

.observation-tooltip-content {
    padding: 12px 16px;
}

.observation-tooltip-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

.observation-tooltip-header svg {
    color: #ff9800;
}

.observation-tooltip-text {
    color: #666;
    font-size: 13px;
    line-height: 1.4;
    word-wrap: break-word;
    white-space: pre-wrap;
}

@keyframes fadeInTooltip {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive: opciones del modal de habitación ocupada */
@media (max-width: 768px) {
    .occupied-options-grid {
        grid-template-columns: 1fr;
    }
    .observation-tooltip {
        max-width: 250px;
        font-size: 12px;
    }
}

/* Estilos para modal de cambiar habitación */
.change-room-container {
    padding: 0;
}

.current-room-info {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #409eff;
}

.current-room-info h4 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 16px;
}

.room-details {
    margin-bottom: 8px;
    font-size: 14px;
    color: #555;
}

.customer-info {
    font-size: 12px;
    color: #777;
}

.new-room-selection h4 {
    margin: 0 0 12px 0;
    color: #333;
    font-size: 16px;
}

.room-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.room-name {
    font-weight: 600;
    color: #333;
}

.room-category {
    font-size: 12px;
    color: #666;
}

/* Estilos para selección de tarifas */
.rate-selection {
    margin-top: 20px;
    padding: 16px;
    background: #f0f8ff;
    border-radius: 8px;
    border: 1px solid #e1f5fe;
}

.rate-selection h4 {
    margin: 0 0 12px 0;
    color: #1976d2;
    font-size: 15px;
    font-weight: 600;
}

.rate-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.rate-name {
    font-weight: 500;
    color: #333;
}

.rate-price {
    font-weight: 600;
    color: #4caf50;
    background: #e8f5e8;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.rate-preview {
    margin-top: 12px;
    padding: 12px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    line-height: 1.4;
}

.rate-preview strong {
    color: #333;
}

/* Resumen del cambio de habitación */
.change-room-preview {
    margin-top: 16px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 13px;
}

.change-room-preview .preview-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    color: #444;
}

.change-room-preview .preview-row strong {
    color: #222;
}

.change-room-preview .preview-diff {
    margin-top: 6px;
    padding-top: 8px;
    border-top: 1px dashed #e0e0e0;
}

.change-room-preview .preview-diff.is-up strong {
    color: #d32f2f;
}

.change-room-preview .preview-diff.is-down strong {
    color: #2e7d32;
}

/* Estilos para matrícula en recepción */
.license-plate-display {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 8px;
    padding: 2px 6px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.license-plate-display svg {
    opacity: 0.9;
}

/* Estilos para información adicional de habitación */
.room-additional-info {
    margin: 8px 0;
    padding: 8px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

/* Estilos para indicador de deuda */
.debt-indicator {
    margin-top: 4px;
    padding: 2px 6px;
    background: #ff4757;
    color: white;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    text-align: center;
    line-height: 1.2;
    animation: pulse 2s infinite;
}

/* Estilos para indicador de deuda inline (al lado del nombre) */
.debt-indicator-inline {
    margin-left: 8px;
    padding: 2px 8px;
    background: #ff4757;
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    animation: pulse 2s infinite;
    vertical-align: middle;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.info-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 4px 0;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.9);
}

.info-row svg {
    flex-shrink: 0;
    opacity: 0.8;
}

.info-label {
    font-weight: 500;
    opacity: 0.8;
    min-width: 50px;
}

.info-value {
    font-weight: 600;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}
</style>
<script>
import ExtendTimeRoom from './partials/ExtendTimeRoom.vue';
import ModalRoomRates from "./RoomRates.vue";
import ReceptionExport from './partials/ReceptionExport.vue';

export default {
    components: {
        ModalRoomRates,
        ExtendTimeRoom,
        ReceptionExport,
    },
    props: {
        roomStatus: {
            type: Array,
            required: true,
        },
        floors: {
            type: Array,
            required: true,
        },
        rooms: {
            type: Array,
            required: true,
            default: [],
        },
        userType: {
            type: String,
            required: true,
        },
        establishmentId: {
            type: Number,
            required: true,
        },
        establishments: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            hotel_floor_id: "",
            hotel_name_room: null,
            hotel_status_room: null,
            loading: false,
            items: [],
            room: null,
            openModalRoomRates: false,
            roomToExtend: {},
            openDialogExtendTimeRoom: false,
            showExportDialog: false,
            showOccupiedOptionsModal: false,
            selectedRoom: null,
            showObservationsModal: false,
            observationsText: "",
            loadingObservations: false,
            // Modal: lista de reservas vigentes
            showReservationsListModal: false,
            reservationsListRoom: null,
            observationTooltip: {
                visible: false,
                text: '',
                x: 0,
                y: 0
            },
            timeRemaining: {},
            countdownInterval: null,
            showChangeRoomModal: false,
            availableRooms: [],
            selectedNewRoom: null,
            loadingChangeRoom: false,
            availableRates: [],
            selectedNewRate: null,
            showCleanerModal: false,
            cleaners: [],
            selectedCleaner: null,
            cleaningNotes: '',
            loadingCleaners: false,
            loadingStartCleaning: false,
            showAdvancePaymentModal: false,
            loadingAdvancePayment: false,
            advancePayment: {
                amount: 0,
                method: 'cash',
                reference: '',
            },
            observationsRefreshInterval: null,
        };
    },
    computed: {
        canManageCleaning() {
            return this.userType === 'admin' || this.userType === 'limpiador';
        },
        canManageRooms() {
            return this.userType === 'admin' || this.userType === 'recepcion';
        },
        getRoomDebt() {
            return (room) => {
                if (!room.rent) return 0;

                // Preferir el valor calculado por el backend (misma fórmula que Checkout.vue:
                // Σ items.item.total (excluyendo PAY) - Σ pagos netos + arrears).
                if (room.rent.total_debt !== undefined && room.rent.total_debt !== null) {
                    return parseFloat(room.rent.total_debt) || 0;
                }

                // Fallback defensivo: cálculo local si el backend no envió el balance.
                if (!Array.isArray(room.rent.items)) return 0;

                const totalItems = room.rent.items
                    .filter(i => i.type !== 'PAY')
                    .reduce((sum, i) => sum + (parseFloat(i.item?.total) || 0), 0);

                const savedPayments = Array.isArray(room.rent.saved_payments)
                    ? room.rent.saved_payments : [];
                const netPayments = savedPayments
                    .reduce((sum, p) => sum + (parseFloat(p.payment) || 0), 0);

                const arrears = parseFloat(room.rent.arrears) || 0;
                return totalItems - netPayments + arrears;
            };
        },
        getFormattedDebt() {
            return (room) => {
                const debt = this.getRoomDebt(room);
                return `S/ ${debt.toFixed(2)}`;
            };
        },
        statusCounts() {
            // Cuenta cuántas habitaciones hay en cada estado real, excluyendo
            // las que están solo "reservadas" del conteo de OCUPADAS.
            const counts = {
                DISPONIBLE: 0,
                OCUPADO: 0,
                LIMPIEZA: 0,
                MANTENIMIENTO: 0,
                RESERVADA: 0,
                total: 0,
            };
            const source = Array.isArray(this.items) ? this.items : [];
            source.forEach(r => {
                counts.total++;
                // Reservada = habitación con reserva activa (no rent real).
                if (r.is_active_reservation) {
                    counts.RESERVADA++;
                    return;
                }
                const st = r.status || 'DISPONIBLE';
                if (counts[st] !== undefined) counts[st]++;
            });
            return counts;
        },
        filteredItems() {
            // Filtra por estado seleccionado en el cliente para que los
            // contadores siempre tengan datos totales (items) y la grilla
            // muestre solo lo elegido. Soporta filtro virtual "RESERVADA".
            const source = Array.isArray(this.items) ? this.items : [];
            const st = this.hotel_status_room;
            if (!st) return source;
            if (st === 'RESERVADA') {
                return source.filter(r => !!r.is_active_reservation);
            }
            return source.filter(r => !r.is_active_reservation && r.status === st);
        },
        changePreview() {
            if (!this.selectedRoom?.rent || !this.selectedNewRate) return null;

            const rent = this.selectedRoom.rent;
            const inputAt = new Date(`${rent.input_date}T${(rent.input_time || '14:00').slice(0,5)}:00`);
            const outputAt = new Date(`${rent.output_date}T${(rent.output_time || '12:00').slice(0,5)}:00`);
            const now = new Date();
            if (isNaN(inputAt) || isNaN(outputAt)) return null;

            const period = rent.rental_period_type || 'day';
            const divisor = period === 'hour' ? 3600e3 : period === 'month' ? 30 * 86400e3 : 86400e3;
            const unit = period === 'hour' ? 'hora(s)' : period === 'month' ? 'mes(es)' : 'noche(s)';

            const duration = Math.max(1, parseInt(rent.duration, 10) || 1);
            // floor: solo cuenta el período cuando se cumple por completo
            const elapsed = Math.max(0, (now - inputAt) / divisor);
            const consumed = Math.max(0, Math.min(Math.floor(elapsed), duration - 1));
            const remaining = Math.max(1, duration - consumed);

            const oldUnit = parseFloat(rent.rental_price) || 0;
            const newUnit = parseFloat(this.getSelectedRatePrice()) || 0;

            return {
                consumed,
                remaining,
                unit,
                consumedTotal: oldUnit * consumed,
                remainingTotal: newUnit * remaining,
                diff: (newUnit - oldUnit) * remaining,
            };
        }
    },
    mounted() {
        this.items = this.rooms;
        this.initializeCountdown();
        // Polling para que las observaciones (y otros campos del rent) se
        // actualicen automáticamente sin necesidad de recargar la página.
        this.observationsRefreshInterval = setInterval(() => {
            if (!this.loading
                && !this.showObservationsModal
                && !this.showOccupiedOptionsModal
                && !this.showChangeRoomModal
                && !this.showCleanerModal
                && !this.showAdvancePaymentModal) {
                this.searchRooms();
            }
        }, 30000); // cada 30s
    },
    beforeDestroy() {
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        if (this.observationsRefreshInterval) {
            clearInterval(this.observationsRefreshInterval);
        }
    },
    /*
    watch: {
        hotel_floor_id() {
            this.onFilterByStatus();
        },
    },
    */
    methods: {
        onAssignCleaner(room) {
            this.selectedRoom = room;
            this.selectedCleaner = null;
            this.cleaningNotes = '';
            this.showCleanerModal = true;
            this.loadCleaners();
        },
        getCleanerModalTitle() {
            if (!this.selectedRoom) return 'Seleccionar Limpiador';
            
            const isPendingCleaning = this.selectedRoom.status === 'LIMPIEZA' && !this.selectedRoom.has_cleaner_assigned;
            return isPendingCleaning ? 'Asignar Limpiador para Checkout' : 'Iniciar Limpieza Rápida';
        },
        onFinalizeClean(room) {
            const text = `Está a punto de terminar la limpieza de la habitación ${room.name}`;
            this.$confirm(text, "Atención", {
                confirmButtonText: "Si",
                cancelButtonText: "No",
                type: "warning",
            })
                .then(() => {
                    this.loading = true;
                    
                    // Usar el endpoint de completar limpieza en lugar de cambiar estado directamente
                    this.$http
                        .get(`/hotels/reception/active-cleanings`)
                        .then(response => {
                            if (response.data.success) {
                                // Buscar la limpieza activa para esta habitación
                                const cleaning = response.data.cleanings.find(c => c.hotel_room_id == room.id);
                                if (cleaning) {
                                    // Completar la limpieza usando el endpoint adecuado
                                    return this.$http.post(`/hotels/reception/complete-cleaning/${cleaning.id}`);
                                } else {
                                    // Si no hay limpieza activa, cambiar estado directamente
                                    return this.$http.post(`/hotels/rooms/${room.id}/change-status`, {
                                        status: "DISPONIBLE"
                                    });
                                }
                            }
                        })
                        .then((response) => {
                            // Usar el estado actualizado desde el backend
                            if (response.data.success && response.data.cleaning) {
                                // Obtener el estado actualizado de la habitación desde el backend
                                this.$http.get(`/hotels/reception/rooms/${room.id}`)
                                    .then(roomResponse => {
                                        if (roomResponse.data.success) {
                                            const updatedRoom = roomResponse.data.room;
                                            this.items = this.items.map((r) => {
                                                if (r.id === room.id) {
                                                    return {
                                                        ...r,
                                                        status: updatedRoom.status,
                                                        has_cleaner_assigned: false
                                                    };
                                                }
                                                return r;
                                            });
                                        }
                                    })
                                    .catch(() => {
                                        // Si hay error, actualizar con valores por defecto
                                        room.has_cleaner_assigned = false;
                                        this.items = this.items.map((r) => {
                                            if (r.id === room.id) {
                                                return room;
                                            }
                                            return r;
                                        });
                                    });
                            }
                            
                            this.$message({
                                type: "success",
                                message: response.data.message || 'Limpieza completada exitosamente',
                            });
                        })
                        .catch((error) => {
                            console.error('Error al finalizar limpieza:', error);
                            this.$message({
                                type: "error",
                                message: 'Error al finalizar la limpieza',
                            });
                        })
                        .finally(() => (this.loading = false));
                })
                .catch();
        },
        onGoToCheckout(room) {
            window.location.href = `/hotels/reception/${room.rent.id}/rent/checkout`;
        },
        onGoToAddProducts(room) {
            window.location.href = `/hotels/reception/${room.rent.id}/rent/products`;
        },
        onDeleteRate(rateId) {
            this.room.rates = this.room.rates.filter((r) => r.id !== rateId);
        },
        onAddRoomRate(rate) {
            this.room.rates.push(rate);
        },
        onCheckIn(room) {
            if (!room.rent || !room.rent.id) {
                this.$message.error('No se encontró la información de la reserva');
                return;
            }
            
            // Redirigir a la página de rent con el parámetro checkin=true
            window.location.href = `/hotels/reception/${room.id}/rent?checkin=true`;
        },
        onToRent(room) {
            if (room.rates.length > 0) {
                window.location.href = `/hotels/reception/${room.id}/rent`;
            } else {
                this.room = room;
                this.openModalRoomRates = true;
            }
        },
        searchRooms() {
            this.loading = true;
            // No enviamos hotel_status_room al backend: filtramos por estado
            // en el cliente para que los contadores siempre reflejen el
            // total de habitaciones del piso/búsqueda actual.
            let form = {
                hotel_name_room: this.hotel_name_room,
                hotel_floor_id: this.hotel_floor_id,
            }
            this.$http
                .post("/hotels/reception/search", form)
                .then((response) => {
                    this.items = response.data.rooms;
                    
                    // Cargar limpiezas activas para determinar si tienen limpiador asignado
                    this.$http.get('/hotels/reception/active-cleanings')
                        .then(cleaningResponse => {
                            if (cleaningResponse.data.success) {
                                const activeCleanings = cleaningResponse.data.cleanings;
                                
                                // Marcar las habitaciones con limpiador asignado
                                this.items = this.items.map(room => {
                                    if (room.status === 'LIMPIEZA') {
                                        const cleaning = activeCleanings.find(c => c.hotel_room_id == room.id);
                                        room.has_cleaner_assigned = cleaning && cleaning.user_id !== null;
                                    } else {
                                        room.has_cleaner_assigned = false;
                                    }
                                    return room;
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al cargar limpiezas activas:', error);
                            // En caso de error, asumir que no tienen limpiador asignado
                            this.items = this.items.map(room => ({
                                ...room,
                                has_cleaner_assigned: false
                            }));
                        })
                        .finally(() => {
                            // Reinicializar el contador con las nuevas habitaciones
                            this.updateCountdown();
                        });
                })
                .finally(() => {
                    this.loading = false;
                })
        },
        onFilterByStatus(status = "") {
            // El filtrado por estado es ahora 100% en el cliente vía
            // filteredItems, así no se golpea el backend al cambiar filtro.
            this.hotel_status_room = (status === "" || status == null) ? null : status;
        },
        onGetColorStatus(ro) {
            const status = ro.status;

            // Solo aplicar el estilo "reservada" cuando la reserva está activa
            // (su hora de ingreso ya llegó y no hay rent real ocupando la habitación).
            if (ro.is_active_reservation) {
                return "has-reservation";
            }

            // Reserva para hoy (futura, dentro del día actual) → azul
            if (status === "DISPONIBLE" && ro.reservations_count > 0 && Array.isArray(ro.upcoming_reservations)) {
                const today = new Date();
                const todayStr = today.getFullYear() + '-' +
                    String(today.getMonth() + 1).padStart(2, '0') + '-' +
                    String(today.getDate()).padStart(2, '0');
                const hasTodayReservation = ro.upcoming_reservations.some(
                    r => r.input_date === todayStr && !r.is_current
                );
                if (hasTodayReservation) {
                    return "available reserved-today";
                }
            }

            if (status === "DISPONIBLE") {
                return "available";
            } else if (status === "MANTENIMIENTO") {
                return "maintenance";
            } else if (status === "OCUPADO") {
                // Check if it's a monthly rental
                if (ro.rent && ro.rent.rental_period_type === 'month') {
                    return "occupied-monthly";
                }
                
                // Check if countdown has reached 0
                if (this.timeRemaining[ro.id] &&
                    this.timeRemaining[ro.id].days === 0 &&
                    this.timeRemaining[ro.id].hours === 0 &&
                    this.timeRemaining[ro.id].minutes === 0 &&
                    this.timeRemaining[ro.id].seconds === 0) {
                    return "occupied-last occupied";
                }
                // Menos de 1 hora restante: marcar morado (próximo a vencer)
                if (this.timeRemaining[ro.id] &&
                    this.timeRemaining[ro.id].totalMs > 0 &&
                    this.timeRemaining[ro.id].totalMs <= 3600000) {
                    return "occupied-soon occupied";
                }
                return "occupied";
            } else if (status === "LIMPIEZA") {
                return "cleaning";
            }
            return "";
        },
        ShowDialogExtendTimeRoom(room) {
            this.roomToExtend = room
            if(this.roomToExtend){
                this.openDialogExtendTimeRoom = true
            }
        },
        onRefresh() {
            this.searchRooms()
        },
        clickExport() {
            this.showExportDialog = true;
        },
        clickChangeEstablishment(establishment_id){
            this.loading = true;
            const payload = {
                establishment_id: establishment_id,
            };
            this.$http
                .post(`/hotels/reception/change-user-establishment`, payload)
                .then((response) => {
                    this.$message({
                        type: "success",
                        message: response.data.message,
                    });
                    location.reload();
                })
                .finally(() => (this.loading = false));
        },
        formatDate(dateString) {
            if (!dateString) return "";
            const [year, month, day] = dateString.split("-");
            return `${day}/${month}`;
        },
        showOccupiedModal(room) {
            this.selectedRoom = room;
            this.showOccupiedOptionsModal = true;
        },
        showOccupiedModalWithDebug(room) {
            console.log('=== CLICK BOTÓN OCUPADO ===');
            console.log('Room:', room);
            console.log('Deuda calculada:', this.getRoomDebt(room));
            console.log('Deuda formateada:', this.getFormattedDebt(room));
            
            this.selectedRoom = room;
            this.showOccupiedOptionsModal = true;
        },
        onQuickClean(room) {
            this.selectedRoom = room;
            this.selectedCleaner = null;
            this.cleaningNotes = '';
            this.showCleanerModal = true;
            this.loadCleaners();
        },
        onViewEditObservations(room) {
            this.selectedRoom = room;
            this.observationsText = room.rent?.notes || "";
            this.showObservationsModal = true;
        },
        saveObservations() {
            this.loadingObservations = true;
            this.$http
                .put(`/hotels/reception/${this.selectedRoom.rent.id}/observations`, {
                    notes: this.observationsText
                })
                .then((response) => {
                    this.$message({
                        type: "success",
                        message: response.data.message || "Observaciones guardadas exitosamente",
                    });
                    // Actualizar las observaciones en el room local
                    if (this.selectedRoom.rent) {
                        this.selectedRoom.rent.notes = this.observationsText;
                    }
                    this.showObservationsModal = false;
                })
                .catch((error) => {
                    this.$message({
                        type: "error",
                        message: error.response?.data?.message || "Error al guardar las observaciones",
                    });
                })
                .finally(() => {
                    this.loadingObservations = false;
                });
        },
        showObservationTooltip(room, event) {
            if (room.rent && room.rent.notes) {
                // Calcular posición del tooltip
                const rect = event.target.getBoundingClientRect();
                const tooltipWidth = 300; // Ancho máximo del tooltip
                const tooltipHeight = 150; // Altura estimada
                
                let x = rect.left + rect.width / 2 - tooltipWidth / 2;
                let y = rect.bottom + 10;
                
                // Ajustar si se sale de la pantalla
                if (x < 10) x = 10;
                if (x + tooltipWidth > window.innerWidth - 10) {
                    x = window.innerWidth - tooltipWidth - 10;
                }
                if (y + tooltipHeight > window.innerHeight - 10) {
                    y = rect.top - tooltipHeight - 10;
                }
                
                this.observationTooltip = {
                    visible: true,
                    text: room.rent.notes,
                    x: x,
                    y: y
                };
            }
        },
        hideObservationTooltip() {
            this.observationTooltip.visible = false;
        },
        initializeCountdown() {
            this.updateCountdown();
            this.countdownInterval = setInterval(() => {
                this.updateCountdown();
            }, 1000); // Actualizar cada segundo
        },
        updateCountdown() {
            const now = new Date();
            
            this.items.forEach(room => {
                if ((room.status === 'OCUPADO' || room.has_reservation) && room.rent) {
                    // Parsear la fecha de salida (YYYY-MM-DD)
                    const dateParts = room.rent.output_date.split('-');
                    const year = parseInt(dateParts[0]);
                    const month = parseInt(dateParts[1]) - 1; // Los meses en JS son 0-11
                    const day = parseInt(dateParts[2]);
                    
                    // Parsear la hora de salida (HH:MM)
                    const timeParts = room.rent.output_time.split(':');
                    const hours = parseInt(timeParts[0]);
                    const minutes = parseInt(timeParts[1]);
                    
                    // Crear la fecha de salida en la zona horaria local
                    const outputDate = new Date(year, month, day, hours, minutes, 0, 0);
                    
                    const difference = outputDate - now;
                    
                    if (difference > 0) {
                        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((difference % (1000 * 60)) / 1000);
                        
                        this.$set(this.timeRemaining, room.id, {
                            days,
                            hours,
                            minutes,
                            seconds,
                            isExpired: false,
                            totalMs: difference
                        });
                    } else {
                        // Tiempo expirado
                        this.$set(this.timeRemaining, room.id, {
                            days: 0,
                            hours: 0,
                            minutes: 0,
                            seconds: 0,
                            isExpired: true,
                            totalMs: 0
                        });
                    }
                }
            });
        },
        formatTimeRemaining(timeData) {
            if (!timeData) return '';
            
            if (timeData.isExpired) {
                return 'Vencido';
            }
            
            // Mostrar solo la unidad de tiempo más significativa
            if (timeData.days > 0) {
                return `Salida en ${timeData.days} ${timeData.days === 1 ? 'día' : 'días'}`;
            } else if (timeData.hours > 0) {
                return `Salida en ${timeData.hours} ${timeData.hours === 1 ? 'hora' : 'horas'}`;
            } else if (timeData.minutes > 0) {
                return `Salida en ${timeData.minutes} ${timeData.minutes === 1 ? 'minuto' : 'minutos'}`;
            } else if (timeData.seconds > 0) {
                return `Salida en ${timeData.seconds} ${timeData.seconds === 1 ? 'segundo' : 'segundos'}`;
            } else {
                return 'Ahora';
            }
        },
        getTravelReasonLabel(reason) {
            const reasons = {
                'visita': 'Visita',
                'trabajo': 'Trabajo',
                'estudio': 'Estudio',
                'religion': 'Religión',
                'salud': 'Salud',
                'compras': 'Compras',
                'otros': 'Otros'
            };
            return reasons[reason] || reason;
        },
        onChangeRoom(room) {
            this.selectedRoom = room;
            this.selectedNewRoom = null;
            this.availableRooms = [];
            this.showChangeRoomModal = true;
            this.loadAvailableRooms();
        },
        onOpenReservationsList(room) {
            this.reservationsListRoom = room;
            this.showReservationsListModal = true;
        },
        onGoToReservationFromList(reservation) {
            // Abre el calendario centrado en la fecha de la reserva
            // (la vista del calendario maneja la apertura del detalle al cargar)
            if (!reservation) return;
            const date = reservation.input_date || '';
            window.location.href = `/hotels/reservations/calendar${date ? `?date=${date}` : ''}`;
        },
        onGoToCalendarFromList() {
            window.location.href = '/hotels/reservations/calendar';
        },
        loadAvailableRooms() {
            this.loadingChangeRoom = true;
            this.$http
                .get('/hotels/reception/available-rooms')
                .then((response) => {
                    this.availableRooms = response.data.rooms || [];
                })
                .catch((error) => {
                    this.$message({
                        type: 'error',
                        message: error.response?.data?.message || 'Error al cargar habitaciones disponibles'
                    });
                })
                .finally(() => {
                    this.loadingChangeRoom = false;
                });
        },
        onNewRoomSelected(roomId) {
            // Limpiar tarifa seleccionada anterior
            this.selectedNewRate = null;
            this.availableRates = [];
            
            if (!roomId) return;
            
            // Cargar tarifas disponibles para la nueva habitación
            this.$http
                .get(`/hotels/rooms/${roomId}/rates`)
                .then((response) => {
                    // Transformar los datos al formato esperado
                    this.availableRates = response.data.room_rates.map(roomRate => ({
                        id: roomRate.rate.id,
                        description: roomRate.rate.description,
                        price: roomRate.price
                    }));
                    console.log('Tarifas cargadas:', this.availableRates);
                })
                .catch((error) => {
                    console.error('Error al cargar tarifas:', error);
                    this.$message.error('Error al cargar las tarifas de la habitación');
                });
        },
        onRateChanged(rateId) {
            console.log('Tarifa cambiada:', rateId);
            // Aquí puedes agregar lógica adicional si necesitas
        },
        getSelectedRateDescription() {
            const rate = this.availableRates.find(r => r.id === this.selectedNewRate);
            return rate ? rate.description : '';
        },
        getSelectedRatePrice() {
            const rate = this.availableRates.find(r => r.id === this.selectedNewRate);
            return rate ? rate.price : 0;
        },
        confirmRoomChange() {
            if (!this.selectedRoom || !this.selectedRoom.rent) {
                this.$message.error('No se encontró la información del alquiler');
                return;
            }

            if (!this.selectedNewRoom) {
                this.$message.error('Debe seleccionar una nueva habitación');
                return;
            }

            if (!this.selectedNewRate) {
                this.$message.error('Debe seleccionar la tarifa para la nueva habitación');
                return;
            }

            const newRoom = this.availableRooms.find(room => room.id === this.selectedNewRoom);
            const rateInfo = this.getSelectedRateDescription();
            const ratePrice = this.getSelectedRatePrice();

            let confirmMessage = `¿Está seguro de cambiar de ${this.selectedRoom.name} a ${newRoom?.name}?`;
            confirmMessage += `\n\nTarifa: ${rateInfo} (S/ ${ratePrice})`;

            if (this.changePreview) {
                confirmMessage += `\nConsumido: ${this.changePreview.consumed} ${this.changePreview.unit}`;
                confirmMessage += `\nRestante: ${this.changePreview.remaining} ${this.changePreview.unit}`;
            }

            this.$confirm(confirmMessage, 'Confirmar cambio', {
                confirmButtonText: 'Si, cambiar',
                cancelButtonText: 'Cancelar',
                type: 'warning'
            })
            .then(() => {
                this.loadingChangeRoom = true;
                const now = new Date();
                const pad = n => String(n).padStart(2, '0');
                const payload = {
                    new_room_id: this.selectedNewRoom,
                    new_rate_id: this.selectedNewRate,
                    change_date: `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`,
                    change_time: `${pad(now.getHours())}:${pad(now.getMinutes())}`,
                };

                this.$http
                    .post(`/hotels/reception/${this.selectedRoom.rent.id}/change-room`, payload)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                            this.showChangeRoomModal = false;
                            this.searchRooms();
                            this.$emit('refresh');
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        const msg = error.response?.data?.message || 'Error al cambiar de habitación';
                        this.$message.error(msg);
                    })
                    .finally(() => {
                        this.loadingChangeRoom = false;
                    });
            })
            .catch(() => {
                // Usuario canceló
            });
        },
        loadCleaners() {
            this.loadingCleaners = true;
            this.$http
                .get('/hotels/reception/cleaners')
                .then(response => {
                    if (response.data.success) {
                        this.cleaners = response.data.cleaners;
                    }
                })
                .catch(error => {
                    this.$message.error('Error al cargar limpiadores');
                })
                .finally(() => {
                    this.loadingCleaners = false;
                });
        },
        confirmStartCleaning() {
            if (!this.selectedCleaner) {
                this.$message.warning('Por favor seleccione un limpiador');
                return;
            }

            this.loadingStartCleaning = true;
            
            // Determinar si es limpieza pendiente de checkout o limpieza rápida
            const isPendingCleaning = this.selectedRoom.status === 'LIMPIEZA' && !this.selectedRoom.has_cleaner_assigned;
            const endpoint = isPendingCleaning ? '/hotels/reception/assign-cleaner-start' : '/hotels/reception/start-cleaning';
            
            this.$http
                .post(endpoint, {
                    room_id: this.selectedRoom.id,
                    cleaner_id: this.selectedCleaner,
                    notes: this.cleaningNotes || (isPendingCleaning ? 'Limpieza asignada desde checkout' : 'Limpieza rápida')
                })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.showCleanerModal = false;
                        
                        // Actualizar la habitación para reflejar que tiene limpiador asignado
                        if (isPendingCleaning) {
                            this.selectedRoom.has_cleaner_assigned = true;
                            this.items = this.items.map((r) => {
                                if (r.id === this.selectedRoom.id) {
                                    return {...r, has_cleaner_assigned: true};
                                }
                                return r;
                            });
                        } else {
                            // Para limpieza rápida, actualizar la lista completa
                            this.searchRooms();
                        }
                        this.$emit('refresh');
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    this.$message.error('Error al iniciar la limpieza');
                })
                .finally(() => {
                    this.loadingStartCleaning = false;
                });
        },
        onAdvancePayment(room) {
            this.selectedRoom = room;
            this.advancePayment = {
                amount: 0,
                method: 'cash',
                reference: '',
            };
            this.showOccupiedOptionsModal = false;
            this.showAdvancePaymentModal = true;
        },
        confirmAdvancePayment() {
            if (!this.selectedRoom || !this.selectedRoom.rent) {
                this.$message.error('No se encontró información de la habitación');
                return;
            }
            const amount = parseFloat(this.advancePayment.amount) || 0;
            if (amount <= 0) {
                this.$message.warning('Ingrese un monto mayor a cero');
                return;
            }
            this.loadingAdvancePayment = true;
            this.$http
                .post(`/hotels/reception/${this.selectedRoom.rent.id}/rent/save-payment`, {
                    amount: amount,
                    method: this.advancePayment.method,
                    reference: this.advancePayment.reference || '',
                })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success('Adelanto registrado correctamente');
                        this.showAdvancePaymentModal = false;
                        this.searchRooms();
                    } else {
                        this.$message.error(response.data.message || 'No se pudo registrar el adelanto');
                    }
                })
                .catch((error) => {
                    this.$message.error(error.response?.data?.message || 'Error al registrar el adelanto');
                })
                .finally(() => {
                    this.loadingAdvancePayment = false;
                });
        },
        onDeleteRecord(room) {
            this.$confirm(`¿Está seguro de eliminar el registro de la habitación ${room.name}? Esta acción no se puede deshacer.`, "Atención", {
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "No, cancelar",
                type: "warning",
            })
                .then(() => {
                    this.loading = true;
                    this.$http
                        .delete(`/hotels/rooms/${room.rent?.id}/delete-record`)
                        .then((response) => {
                            this.$message({
                                type: "success",
                                message: response.data.message || "Registro eliminado exitosamente",
                            });
                            // Cerrar el modal
                            this.showOccupiedOptionsModal = false;
                            // Actualizar la lista de habitaciones
                            this.searchRooms();
                        })
                        .catch((error) => {
                            this.$message({
                                type: "error",
                                message: error.response?.data?.message || "Error al eliminar el registro",
                            });
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                })
                .catch(() => {
                    // Usuario canceló la acción
                });
        },
    },
};
</script>

<style scoped>
/* Estilos para badge de tipo de cliente */
.customer-type-badge {
    display: inline-block;
    padding: 2px 6px;
    background-color: #6c757d;
    color: white;
    font-size: 10px;
    font-weight: 500;
    border-radius: 4px;
    line-height: 1;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Estilos específicos por tipo de documento */
.customer-type-badge[data-type="DNI"] {
    background-color: #007bff;
}

.customer-type-badge[data-type="RUC"] {
    background-color: #28a745;
}

.customer-type-badge[data-type="CE"] {
    background-color: #ffc107;
    color: #212529;
}

.customer-type-badge[data-type="PAS"] {
    background-color: #17a2b8;
}

/* Habitación disponible pero con reserva para hoy (futura): azul */
.reserved-today {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
    border: 2px solid #1d4ed8 !important;
    color: white !important;
}
.reserved-today .card-rent h2,
.reserved-today .card-rent p,
.reserved-today .card-rent span {
    color: white !important;
}

/* Estilo para habitaciones ocupadas próximas a vencer (< 1h restante) - morado */
.occupied-soon {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important;
    border: 2px solid #6d28d9 !important;
    color: white !important;
    animation: pulse 2s infinite;
}
.occupied-soon .card-rent h2,
.occupied-soon .card-rent p,
.occupied-soon .card-rent span {
    color: white !important;
}

/* Estilo para habitaciones ocupadas por mes (color plomo/gris) */
.occupied-monthly {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: white !important;
}

.occupied-monthly .room-status,
.occupied-monthly .room-info {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Estilo para habitaciones con reserva (color morado) */
.has-reservation {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    border: 2px solid #7c3aed !important;
    color: white !important;
    position: relative;
    overflow: hidden;
}

/* Estilo para habitaciones ocupadas con reserva futura */
.occupied-with-future-reservation {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    border: 2px solid #d97706 !important;
    color: white !important;
    position: relative;
    overflow: hidden;
}

.occupied-with-future-reservation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
}

.has-reservation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
}

.has-reservation .card-rent h2,
.has-reservation .card-rent p,
.has-reservation .card-rent span {
    color: white !important;
}

/* Estilo para habitaciones listas para check-in (color verde) */
.ready-checkin {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    border: 2px solid #059669 !important;
    color: white !important;
    position: relative;
    overflow: hidden;
}

.ready-checkin::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    animation: pulse 2s infinite;
}

.ready-checkin .card-rent h2,
.ready-checkin .card-rent p,
.ready-checkin .card-rent span {
    color: white !important;
}

/* Indicador de reserva */
.reservation-indicator {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(255, 255, 255, 0.9);
    color: #7c3aed;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 10;
}

/* Chip-contador de reservas vigentes */
.reservations-count-chip {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 11;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(124, 58, 237, 0.25);
    color: #000000;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color .15s ease, transform .15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}
.reservations-count-chip:hover {
    background: #f5f3ff;
    transform: translateY(-1px);
}
.reservations-count-chip svg { color: #000000; }
.reservations-count-chip .reservations-count-num { line-height: 1; }
.reservations-count-chip.chip-on-reserved {
    /* Cuando la propia habitación ya está marcada como Reservada,
       el chip se pinta con borde más sutil para no competir con el badge. */
    border-color: rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.92);
}

/* Posicionamiento del contador cuando la habitación está ocupada */
.has-reservation .reservations-count-chip,
.occupied-monthly .reservations-count-chip,
.ready-checkin .reservations-count-chip {
    top: auto;
    bottom: 8px;
    right: 8px;
}

/* Modal de reservas */
.reservations-list-dialog .el-dialog__body { padding: 16px 20px; }
.reservations-empty {
    text-align: center;
    color: #9ca3af;
    padding: 24px 0;
    font-size: 13px;
}
.reservations-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; }
.reservations-list-item {
    display: flex; justify-content: space-between; align-items: center; gap: 12px;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fafafa;
}
.reservations-list-item.is-current {
    border-color: #c4b5fd;
    background: #f5f3ff;
}
.reservations-list-item .rli-left { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.reservations-list-item .rli-customer { font-weight: 600; color: #111827; font-size: 14px; }
.reservations-list-item .rli-dates { font-size: 12px; color: #4b5563; display: flex; gap: 6px; flex-wrap: wrap; }
.reservations-list-item .rli-arrow { color: #9ca3af; }
.reservations-list-item .rli-meta { font-size: 11px; color: #6b7280; margin-top: 2px; display: flex; gap: 8px; flex-wrap: wrap; }
.reservations-list-item .rli-plate {
    background: #fff; border: 1px solid #e5e7eb; padding: 1px 6px; border-radius: 6px;
    font-family: ui-monospace, SFMono-Regular, monospace;
}
.reservations-list-item .rli-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.reservations-list-item .rli-tag {
    font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px;
    text-transform: uppercase; letter-spacing: .3px;
}
.rli-tag.tag-current { background: #ddd6fe; color: #5b21b6; }
.rli-tag.tag-future  { background: #dbeafe; color: #1e40af; }
.rli-tag.tag-other   { background: #f3f4f6; color: #6b7280; }

/* Indicador de check-in */
.checkin-indicator {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(255, 255, 255, 0.9);
    color: #059669;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 10;
    animation: pulse 2s infinite;
}
</style>
