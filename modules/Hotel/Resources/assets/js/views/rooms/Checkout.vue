<template>
    <div>
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels/reception">
                    <svg  xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px;" width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building-skyscraper"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M5 21v-14l8 -4v18" /><path d="M19 21v-10l-6 -4" /><path d="M9 9l0 .01" /><path d="M9 12l0 .01" /><path d="M9 15l0 .01" /><path d="M9 18l0 .01" /></svg>
                </a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>CHECK-OUT: <b>{{ title }}</b> (Registro de salida)</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <button
                        class="btn btn-custom btn-sm mt-2 me-2"
                        type="button"
                        @click="onGotoBack"
                    >
                        <i class="fa fa-arrow-left"></i> Atras
                    </button>
                </div>
            </div>
        </div>
        <div class="card mb-0 tab-content-default row-new">
            <template v-if="canMakePayment">
                <div class="card-body invoice p-3">
                    <div class="row card-body row card-body shadow-none mb-2">
                        <div class="col-12 h1 m-0 pt-1">
                            Salida
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-door-exit" style="transform: translateY(-4px);"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M13 12v.01"></path><path d="M3 21h18"></path><path d="M5 21v-16a2 2 0 0 1 2 -2h7.5m2.5 10.5v7.5"></path><path d="M14 7h7m-3 -3l3 3l-3 3"></path></svg>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg> Cliente</span>
                            <h4 class="mt-0"><b>
                                {{ currentRent.customer.name }}</b>
                            </h4>
                            <el-button 
                                type="text" 
                                size="mini" 
                                @click="showCustomerObservations"
                                class="mt-2 p-0"
                                title="Ver/Observaciones del cliente"
                            >
                                <i class="fa fa-sticky-note-o"></i> Observaciones
                            </el-button>
                        </div>
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 8l2 0" /><path d="M15 12l2 0" /><path d="M7 16l10 0" /></svg> DNI/RUC/CE</span>
                            <h4 class="m-0"><b>
                                {{ currentRent.customer.number }}</b>
                            </h4>
                        </div>
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-door"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 12v.01" /><path d="M3 21h18" /><path d="M6 21v-16a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v16" /></svg> {{ currentRent.room.category.description }}</span>
                            <h4 class="m-0"><b>
                                {{ currentRent.room.name }}</b></h4>
                            <el-button
                                type="primary"
                                size="mini"
                                @click="showRoomHistory"
                                class="mt-2"
                                title="Ver historial de cambios de la habitación"
                            >
                                <i class="fa fa-history"></i> Ver historial
                            </el-button>
                        </div>
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3 position-relative">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-up"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg> Check-IN</span>
                            <h4 class="m-0"><b>
                                {{ moment(currentRent.input_date).format('ddd D MMM YYYY') }} <br>
                                {{ moment(currentRent.input_time, 'HH:mm:ss').format('h:mm A') }}</b></h4>
                            
                            <!-- Botón circular para editar fecha de ingreso -->
                            <button 
                                class="btn-edit-date"
                                @click="showEditCheckinDates"
                                title="Editar fecha de ingreso"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5 -9.5z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3 position-relative">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" /><path d="M19 16v6" /><path d="M22 19l-3 3l-3 -3" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg> Check-OUT</span>
                            <h4 class="m-0"><b>
                                {{ moment(currentRent.output_date).format('ddd D MMM YYYY') }} <br>
                                {{ moment(currentRent.output_time, 'HH:mm:ss').format('h:mm A') }}</b></h4>
                            
                            <!-- Botones circulares para check-out -->
                            <div class="checkout-buttons">
                                <!-- Botón para extender estadía -->
                                
                                
                                <!-- Botón para editar fecha de salida -->
                                <button 
                                    class="btn-edit-date"
                                    @click="showEditCheckoutDates"
                                    title="Editar fecha de salida"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5 -9.5z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 card card-body bg-light-color my-1 mx-1 p-3" v-if="currentRent.rental_date_time">
                            <span class="text-muted"><svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clock"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg> Renta</span>
                            <h4 class="m-0"><b>
                                <span v-if="currentRent.rental_period_type === 'hour'" class="badge badge-warning">Por Hora</span>
                                <span v-else-if="currentRent.rental_period_type === 'day'" class="badge badge-info">Por Día</span>
                                <span v-else-if="currentRent.rental_period_type === 'month'" class="badge badge-success">Por Mes</span>
                                <span v-else class="badge badge-secondary">Estándar</span>
                                <br>
                                <small v-if="currentRent.rental_price">
                                    S/ {{ parseFloat(currentRent.rental_price).toFixed(2) }}
                                </small>
                            </h4>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="row card-body shadow-none mb-2" v-if="currentRent.towels || currentRent.license_plate || currentRent.travel_reason">
                        <div class="col-12">
                            <h6 class="mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                                </svg>
                                Información Adicional
                            </h6>
                            <div class="row">
                                <div class="col-12 col-md-4" v-if="currentRent.towels">
                                    <div class="info-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 2v7c0 1.1.9 2 2 2h3a2 2 0 0 0 2-2V2"></path>
                                            <path d="M7 2v7"></path>
                                            <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm-1-5h-4"></path>
                                        </svg>
                                        <span class="info-label">Toallas:</span>
                                        <span class="info-value">{{ currentRent.towels }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4" v-if="currentRent.license_plate">
                                    <div class="info-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 17a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                            <path d="M7 9v0a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v0"></path>
                                            <circle cx="12" cy="15" r="1"></circle>
                                        </svg>
                                        <span class="info-label">Matrícula:</span>
                                        <span class="info-value">{{ currentRent.license_plate }}</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4" v-if="currentRent.travel_reason">
                                    <div class="info-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M9 12l2 2 4 -4"></path>
                                        </svg>
                                        <span class="info-label">Motivo de viaje:</span>
                                        <span class="info-value">{{ getTravelReasonLabel(currentRent.travel_reason) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row card-body shadow-none mb-2">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table text-end">
                                    <tbody>
                                    <tr class="text-start">
                                        <td colspan="6"><b class="h6">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg> Alojamiento: Tarifas y Cargos Adicionales</b></td>
                                    </tr>
                                    <tr class="bg-light-color">
                                        <td>#</td>
                                        <td class="text-start">Habitación / Tarifa por día</td>
                                        <td>Cant. noches</td>
                                        <td>Cargo por salir tarde</td>
                                        <td>Comprobante</td>
                                        <td>Total</td>
                                    </tr>
                                    <tr v-for="(r, idx) in roomItems" :key="`hab-${r.id}`">
                                        <td>{{ idx + 1 }}</td>
                                        <td class="text-start">
                                            <div>{{ getRoomItemUnitPrice(r) | toDecimals }}</div>
                                            <small class="text-muted" v-if="r.item && r.item.description">
                                                {{ r.item.description }}
                                            </small>
                                        </td>
                                        <td>{{ getRoomItemQuantity(r) }}</td>
                                        <td class="float-right">
                                            <div class="d-d-inline-block"
                                                v-if="r.id === activeRoomItemId"
                                                style="max-width: 120px;margin: auto;">
                                                <el-input v-model="arrears" type="number"></el-input>
                                            </div>
                                            <span v-else>—</span>
                                        </td>
                                        <td>{{ r.document }}</td>
                                        <td class="float-right">
                                            <div class="d-d-inline-block"
                                                v-if="r.id === activeRoomItemId"
                                                style="max-width: 120px;margin:auto;">
                                                <el-input
                                                    v-model="total"
                                                    readonly
                                                    type="number"
                                                ></el-input>
                                            </div>
                                            <span v-else>{{ getRoomItemTotal(r) | toDecimals }}</span>
                                        </td>
                                    </tr>
                                    <tr class="text-start" v-if="rentPaidItems.length > 0">
                                        <td colspan="6"><b class="h6">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-receipt-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M14 8h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5m2 0v1.5m0 -9v1.5" /></svg> Servicio a la habitación (Pagado)</b></td>
                                    </tr>
                                    <tr class="bg-light-color" v-if="rentPaidItems.length > 0">
                                        <td>#</td>
                                        <td class="text-start">Descripción</td>
                                        <td>Precio unitario</td>
                                        <td>Cantidad</td>
                                        <td>Comprobante</td>
                                        <td>Total</td>
                                    </tr>
                                    <tr
                                        v-for="(it, i) in rentPaidItems"
                                        :key="i"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td class="text-start">{{ it.item.item.description }}</td>
                                        <td>{{ it.item.input_unit_price_value | toDecimals }}</td>
                                        <td>{{ it.item.quantity | toDecimals }}</td>
                                        <td>{{ it.document}}</td>
                                        <td>{{ it.item.total | toDecimals }}</td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr class="text-start" v-if="rentDebtItems.length > 0">
                                        <td colspan="6"><b class="h6 text-danger">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-receipt-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16m2 -2h10a2 2 0 0 1 2 2v10m0 4.01v1.99l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M11 7l4 0" /><path d="M9 11l2 0" /><path d="M13 15l2 0" /><path d="M15 11l0 .01" /><path d="M3 3l18 18" /></svg> Servicio a la habitación (Cargo Pendiente)</b></td>
                                    </tr>
                                    <tr class="bg-light-color" v-if="rentDebtItems.length > 0">
                                        <td>#</td>
                                        <td class="text-start">Descripción</td>
                                        <td>Precio unitario</td>
                                        <td>Cantidad</td>
                                        <td>Estado</td>
                                        <td>Total</td>
                                    </tr>
                                    <tr
                                        v-for="(it, i) in rentDebtItems"
                                        :key="i"
                                        class="text-danger"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td class="text-start">{{ it.item.item.description }}</td>
                                        <td>{{ it.item.input_unit_price_value | toDecimals }}</td>
                                        <td>{{ it.item.quantity | toDecimals }}</td>
                                        <td>
                                            {{ it.payment_status === "PAID" ? "PAGADO" : "DEBE" }}
                                        </td>
                                        <td>{{ it.item.total | toDecimals }}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td class="text-end"
                                            colspan="5">Pagado
                                        </td>
                                        <td>
                                            <h3 class="my-0">
                                                <span class="badge badge-pill badge-info">
                                                    {{ totalPaid | toDecimals }}
                                                </span>
                                            </h3>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end"
                                            colspan="5">Debe
                                        </td>
                                        <td>
                                            <h3 class="my-0">
                                                <span class="badge badge-pill" :class="totalDebt >= 0 ? 'badge-danger' : 'badge-warning'">
                                                    {{ Math.abs(totalDebt) | toDecimals }}
                                                </span>
                                            </h3>
                                        </td>
                                    </tr>
                                    <tr v-if="totalDebt < 0">
                                        <td class="text-end"
                                            colspan="5">
                                            </td>
                                        <td>
                                            <el-button 
                                                type="warning" 
                                                size="small"
                                                @click="processRefund"
                                                :loading="loadingRefund"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7,10 12,15 17,10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                Dar Vuelto
                                            </el-button>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón para abrir modal de pagos: visible siempre que haya
                         items en el alquiler (deshabilitado si no hay deuda) para
                         evitar que el botón aparezca/desaparezca al actualizar
                         (CHECKIN #4). -->
                    <div class="row card-body shadow-none mb-2 border-0" v-if="currentRent.items && currentRent.items.length > 0">
                        <div class="col-12 text-center">
                            <el-button
                                type="success"
                                @click="showPaymentModal = true"
                                size="large"
                                icon="el-icon-money"
                                :disabled="totalDebt <= 0"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                    <path d="M2 10h20"></path>
                                </svg>
                                <span v-if="totalDebt > 0">Procesar Pagos</span>
                                <span v-else>Sin deuda pendiente</span>
                            </el-button>
                        </div>
                    </div>
                    
                    
                    
                    <div class="row card-body shadow-none mb-2">
                        <div class="col-12 h6 m-0">
                            <b><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-dollar"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" /><path d="M12 17v1m0 -8v1" /></svg> Información del comprobante (Solo para el monto pendiente de pago)</b>
                        </div>
                        <div class="col-lg-3">
                            <div
                                :class="{ 'has-danger': errors.document_type_id }"
                                class="form-group"
                            >
                                <label class="control-label">Tipo comprobante</label>
                                <el-select
                                    v-model="document.document_type_id"
                                    class="border-left rounded-left border-info"
                                    dusk="document_type_id"
                                    popper-class="el-select-document_type"
                                    @change="changeDocumentType"
                                >
                                    <el-option
                                        v-for="option in document_types"
                                        :key="option.id"
                                        :label="option.description"
                                        :value="option.id"
                                    ></el-option>
                                </el-select>
                                <small
                                    v-if="errors.document_type_id"
                                    class="form-control-feedback"
                                    v-text="errors.document_type_id[0]"
                                ></small>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div :class="{ 'has-danger': errors.series_id }"
                                class="form-group">
                                <label class="control-label">Serie</label>
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
                        <div class="col-lg-3">
                            <div
                                :class="{ 'has-danger': errors.date_of_issue }"
                                class="form-group"
                            >
                                <label class="control-label">Fecha de emisión</label>
                                <el-date-picker
                                    v-model="document.date_of_issue"
                                    :clearable="false"
                                    readonly
                                    type="date"
                                    value-format="yyyy-MM-dd"
                                    @change="changeDateOfIssue"
                                ></el-date-picker>
                                <small
                                    v-if="errors.date_of_issue"
                                    class="form-control-feedback"
                                    v-text="errors.date_of_issue[0]"
                                ></small>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div
                                :class="{ 'has-danger': errors.date_of_due }"
                                class="form-group"
                            >
                                <label class="control-label">Fecha de vencimiento</label>
                                <el-date-picker
                                    v-model="document.date_of_due"
                                    :clearable="false"
                                    type="date"
                                    value-format="yyyy-MM-dd"
                                ></el-date-picker>
                                <small
                                    v-if="errors.date_of_due"
                                    class="form-control-feedback"
                                    v-text="errors.date_of_due[0]"
                                ></small>
                            </div>
                        </div>
                    </div>
                    <div class="row card-body bg-accent-color mb-2 mx-0 payment-section-hidden">
                        <div class="col-12 h6 m-0">
                            <b><svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-cash-register"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" /><path d="M19 21v1m0 -8v1" /><path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" /><path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" /><path d="M8 14v.01" /><path d="M8 17v.01" /><path d="M12 13.99v.01" /><path d="M12 17v.01" /></svg> Registro de pagos pendientes</b>
                        </div>
                        <div class="col-12">
                            
                        </div>
                    </div>
                    
                    <!-- Botón para ver historial de pagos -->
                    <div class="row card-body shadow-none mb-2 border-0" v-if="savedPayments.length > 0">
                        <div class="col-12">
                            <el-button 
                                type="info" 
                                @click="showPaymentHistoryModal = true"
                                size="medium"
                                icon="el-icon-view"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                Ver Historial de Pagos ({{ savedPayments.length }})
                            </el-button>
                        </div>
                    </div>
                    
                    <div class="row card-body shadow-none mb-2 border-0">
                        <div class="col-12 pt-3 d-flex flex-wrap gap-2 justify-content-end">
                            <el-button
                                :disabled="loading"
                                size="medium"
                                @click="openInvoicesHistory"
                                icon="el-icon-document"
                            >
                                Historial de Comprobantes ({{ invoicesHistoryCount }})
                            </el-button>
                            <el-button
                                v-if="canMakePayment"
                                :disabled="loading || !canGenerateInvoice"
                                :loading="loading"
                                type="success"
                                @click="onGenerateInvoice"
                            >
                                <i class="el-icon-tickets"></i> Generar Comprobante
                            </el-button>
                            <el-button
                                v-if="canMakePayment"
                                :disabled="loading"
                                :loading="loading"
                                type="primary"
                                @click="onGoToFinalizeRent"
                            >
                                <i class="el-icon-check"></i> Dar Salida
                            </el-button>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="card text-center">
                   <div>
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="96"  height="96"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" /><path d="M9 12l2 2l4 -4" /></svg>
                    <h2>Checkout éxitoso en {{this.currentRent.room.name}}</h2>
                    <p class="text-sm">Ya se finalizó el proceso y puede volver a recepción.</p>
                    <el-button
                        @click="onExitPage"
                        type="primary"
                        class="btn btn-primary mt-4"
                    >
                        <span class="ms-2">
                            Volver a recepción
                        </span>
                    </el-button>
                </div>

                </div>
            </template>
        </div>
        <document-options
            :isContingency="false"
            :recordId="documentNewId"
            :showClose="true"
            :showDialog.sync="showDialogDocumentOptions"
        ></document-options>

        <sale-note-options :configuration="config"
                           :recordId="documentNewId"
                           :showClose="true"
                           :showDialog.sync="showDialogSaleNoteOptions">
        </sale-note-options>

        <el-dialog
            title="Historial de Comprobantes"
            width="60%"
            :visible.sync="showInvoicesHistoryModal"
            append-to-body
        >
            <div v-loading="loadingInvoicesHistory">
                <el-table :data="invoicesHistory" empty-text="Sin comprobantes generados" style="width: 100%">
                    <el-table-column prop="document_type" label="Tipo" width="140"></el-table-column>
                    <el-table-column prop="identifier" label="Comprobante" width="160"></el-table-column>
                    <el-table-column prop="items_count" label="Items" width="80" align="center"></el-table-column>
                    <el-table-column label="Total" width="120" align="right">
                        <template slot-scope="scope">
                            {{ scope.row.total | toDecimals }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="created_at" label="Generado el"></el-table-column>
                    <el-table-column label="Acciones" width="120" align="center">
                        <template slot-scope="scope">
                            <el-button
                                size="mini"
                                type="primary"
                                icon="el-icon-printer"
                                @click="printInvoice(scope.row)"
                            ></el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <span slot="footer">
                <el-button @click="showInvoicesHistoryModal = false">Cerrar</el-button>
            </span>
        </el-dialog>

        <!-- Diálogo para extender estadía -->
        <el-dialog
            title="Extender Estadía"
            width="40%"
            :visible.sync="showDialogExtendStay"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            append-to-body
        >
            <div class="form-body">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="control-label">Días a extender</label>
                            <el-input-number
                                v-model="extendForm.days"
                                :min="1"
                                :max="30"
                                placeholder="Número de días"
                                style="width: 100%"
                                @change="onDaysChange"
                            ></el-input-number>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="control-label">Precio por día</label>
                            <el-input
                                v-model="extendForm.pricePerDay"
                                type="number"
                                placeholder="0.00"
                                style="width: 100%"
                                @input="calculateExtensionTotal"
                            >
                                <template slot="prepend">S/</template>
                            </el-input>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="control-label">Total extensión</label>
                            <el-input
                                :value="extensionTotal"
                                type="number"
                                readonly
                                style="width: 100%"
                            >
                                <template slot="prepend">S/</template>
                            </el-input>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="control-label">Nueva fecha de salida</label>
                            <el-date-picker
                                v-model="extendForm.newOutputDate"
                                type="date"
                                placeholder="Seleccionar fecha"
                                style="width: 100%"
                                format="dd-MM-yyyy"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions"
                                @change="onDateChange"
                            ></el-date-picker>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="control-label">Nueva hora de salida</label>
                            <el-time-picker
                                v-model="extendForm.newOutputTime"
                                placeholder="Seleccionar hora"
                                style="width: 100%"
                                format="HH:mm"
                                value-format="HH:mm"
                            ></el-time-picker>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de pago -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group">
                            <el-checkbox v-model="extendForm.includePayment" @change="onIncludePaymentChange">
                                Incluir pago por extensión
                            </el-checkbox>
                        </div>
                    </div>
                    <template v-if="extendForm.includePayment">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="control-label">Monto a pagar</label>
                                <el-input
                                    v-model="extendForm.paymentAmount"
                                    type="number"
                                    placeholder="0.00"
                                    style="width: 100%"
                                >
                                    <template slot="prepend">S/</template>
                                </el-input>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="control-label">Método de pago</label>
                                <el-select v-model="extendForm.paymentMethod" style="width: 100%">
                                    <el-option label="Efectivo" value="cash"></el-option>
                                    <el-option label="Tarjeta de Crédito" value="credit_card"></el-option>
                                    <el-option label="Tarjeta de Débito" value="debit_card"></el-option>
                                    <el-option label="Transferencia" value="transfer"></el-option>
                                    <el-option label="Yape/Plin" value="yape_plin"></el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="control-label">Referencia</label>
                                <el-input
                                    v-model="extendForm.paymentReference"
                                    placeholder="Opcional"
                                    style="width: 100%"
                                ></el-input>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="col-12 mt-4">
                    <div class="alert alert-info">
                        <strong>Información:</strong> Al extender la estadía, se generarán cargos adicionales según el precio por día configurado.
                    </div>
                </div>
            </div>
            <div slot="footer" class="dialog-footer text-right">
                <el-button @click="showDialogExtendStay = false" size="small">Cancelar</el-button>
                <el-button type="primary" @click="confirmExtendStay" :loading="loadingExtend" size="small">Confirmar Extensión</el-button>
            </div>
        </el-dialog>

        <!-- Modal para Editar Fechas -->
        <el-dialog
            :title="editDatesModal.title"
            width="45%"
            :visible.sync="showEditDatesModal"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            append-to-body
        >
            <div class="form-body">
                <div class="row">
                    <!-- Fecha de Ingreso -->
                    <div class="col-12" v-if="editDatesModal.editInput">
                        <div class="form-group">
                            <label class="control-label">Fecha y Hora de Ingreso</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <el-date-picker
                                        v-model="editDatesForm.inputDate"
                                        type="date"
                                        placeholder="Seleccionar fecha de ingreso"
                                        style="width: 100%"
                                        :picker-options="datePickerOptions"
                                        format="dd/MM/yyyy"
                                        value-format="yyyy-MM-dd"
                                        @change="calculateNewPrice"
                                        size="medium"
                                    >
                                    </el-date-picker>
                                </div>
                                <div class="col-md-6">
                                    <el-time-picker
                                        v-model="editDatesForm.inputTime"
                                        placeholder="Seleccionar hora de ingreso"
                                        style="width: 100%"
                                        format="HH:mm"
                                        value-format="HH:mm"
                                        @change="calculateNewPrice"
                                        size="medium"
                                    >
                                    </el-time-picker>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fecha de Salida -->
                    <div class="col-12" v-if="editDatesModal.editOutput">
                        <div class="form-group">
                            <label class="control-label">Fecha y Hora de Salida</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <el-date-picker
                                        v-model="editDatesForm.outputDate"
                                        type="date"
                                        placeholder="Seleccionar fecha de salida"
                                        style="width: 100%"
                                        :picker-options="datePickerOptions"
                                        format="dd/MM/yyyy"
                                        value-format="yyyy-MM-dd"
                                        @change="calculateNewPrice"
                                        size="medium"
                                    >
                                    </el-date-picker>
                                </div>
                                <div class="col-md-6">
                                    <el-time-picker
                                        v-model="editDatesForm.outputTime"
                                        placeholder="Seleccionar hora de salida"
                                        style="width: 100%"
                                        format="HH:mm"
                                        value-format="HH:mm"
                                        @change="calculateNewPrice"
                                        size="medium"
                                    >
                                    </el-time-picker>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Precios -->
                <div class="row mt-3" v-if="editDatesForm.newPrice !== null">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h5><i class="fa fa-info-circle"></i> Recálculo de Precios</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Precio Original:</strong> S/ {{ parseFloat(room.item.unit_price).toFixed(2) }}<br>
                                    <strong>Nuevo Precio:</strong> S/ {{ editDatesForm.newPrice.toFixed(2) }}<br>
                                    <strong>Diferencia:</strong> 
                                    <span :class="editDatesForm.priceDifference >= 0 ? 'text-success' : 'text-danger'">
                                        S/ {{ Math.abs(editDatesForm.priceDifference).toFixed(2) }}
                                        <i :class="editDatesForm.priceDifference >= 0 ? 'fa fa-arrow-up' : 'fa fa-arrow-down'"></i>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Duración Original:</strong> {{ editDatesForm.originalDuration }}<br>
                                    <strong>Nueva Duración:</strong> {{ editDatesForm.newDuration }}<br>
                                    <strong>Tipo de Renta:</strong> 
                                    <span class="badge" :class="getRentalTypeBadgeClass(currentRent.rental_period_type)">
                                        {{ getRentalTypeLabel(currentRent.rental_period_type) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas -->
                <div class="row" v-if="editDatesForm.warning">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> {{ editDatesForm.warning }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div slot="footer" class="dialog-footer text-right">
                <el-button @click="showEditDatesModal = false" size="small">Cancelar</el-button>
                <el-button 
                    type="primary" 
                    @click="confirmDateEdit" 
                    :loading="loadingDateEdit" 
                    :disabled="!canConfirmDateEdit"
                    size="small"
                >
                    Confirmar Cambio
                </el-button>
            </div>
        </el-dialog>

        <!-- Modal de Pagos Interactivos -->
        <el-dialog
            title="Procesar Pagos"
            width="50%"
            :visible.sync="showPaymentModal"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            append-to-body
        >
            <div class="form-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <strong>Deuda actual:</strong> S/ {{ totalDebt | toDecimals }}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Dinero Recibido del Cliente</label>
                            <el-input
                                v-model="paymentForm.received"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="Ej: 150.00"
                                @input="calculateChange"
                                class="payment-input"
                                size="large"
                            >
                                <template slot="prepend">S/</template>
                            </el-input>
                            <small class="text-muted">Dinero que entrega el cliente</small>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Vuelto</label>
                            <el-input
                                :value="paymentForm.change"
                                type="number"
                                step="0.01"
                                readonly
                                :class="getChangeClass()"
                                class="payment-input"
                                size="large"
                            >
                                <template slot="prepend">S/</template>
                            </el-input>
                            <small class="text-muted">Dinero a devolver al cliente</small>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Método de Pago</label>
                            <el-select v-model="paymentForm.method" placeholder="Seleccionar método" style="width: 100%" size="large">
                                <el-option label="Efectivo" value="cash"></el-option>
                                <el-option label="Tarjeta de Crédito" value="credit_card"></el-option>
                                <el-option label="Tarjeta de Débito" value="debit_card"></el-option>
                                <el-option label="Transferencia" value="transfer"></el-option>
                                <el-option label="Yape/Plin" value="yape_plin"></el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="control-label">Referencia (Opcional)</label>
                            <el-input
                                v-model="paymentForm.reference"
                                placeholder="N° de operación, última 4 dígitos, etc."
                                size="large"
                            ></el-input>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="payment-summary" :class="getPaymentSummaryClass()">
                            <div class="payment-icon">
                                <svg v-if="paymentForm.change > 0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <div class="payment-text">
                                <h5>{{ getPaymentMessage() }}</h5>
                                <p class="mb-0">{{ getPaymentSubMessage() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div slot="footer" class="dialog-footer text-right">
                <el-button @click="showPaymentModal = false" size="small">Cancelar</el-button>
                <el-button 
                    type="success" 
                    @click="savePayment"
                    :disabled="!canSavePayment"
                    :loading="loadingPayment"
                    size="small"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Guardar Pago
                </el-button>
                <!-- Botón para procesar vuelto cuando hay cambio positivo -->
                <el-button 
                    v-if="paymentForm.change > 0"
                    type="warning" 
                    @click="processChange"
                    size="small"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7,10 12,15 17,10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Dar Vuelto
                </el-button>
                <el-button 
                    @click="clearPaymentForm"
                    size="small"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Limpiar
                </el-button>
            </div>
        </el-dialog>

        <!-- Modal de Historial de Pagos -->
        <el-dialog
            title="Historial de Pagos Realizados"
            width="70%"
            :visible.sync="showPaymentHistoryModal"
            append-to-body
        >
            <div class="payment-history-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Monto</th>
                                <th>Método</th>
                                <th>Referencia</th>
                                <th>Vuelto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(payment, index) in savedPayments" :key="index">
                                <td>{{ formatDateTime(payment.created_at) }}</td>
                                <td class="text-success fw-bold">S/ {{ payment.amount | toDecimals }}</td>
                                <td>
                                    <el-tag :type="getPaymentMethodTagType(payment.method)" size="small">
                                        {{ getMethodLabel(payment.method) }}
                                    </el-tag>
                                </td>
                                <td>{{ payment.reference || '-' }}</td>
                                <td>
                                    <span v-if="payment.change > 0" class="text-warning fw-bold">
                                        S/ {{ payment.change | toDecimals }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td>
                                    <el-tag type="success" size="small">Confirmado</el-tag>
                                </td>
                                <td>
                                    <el-button 
                                        type="primary" 
                                        size="mini"
                                        @click="editPayment(payment, index)"
                                        title="Editar pago"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </el-button>
                                    <el-button 
                                        type="danger" 
                                        size="mini"
                                        @click="reversePayment(index)"
                                        :loading="loadingReverse"
                                        title="Revertir pago"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 7v6h6"></path>
                                            <path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13"></path>
                                        </svg>
                                    </el-button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="6"><strong>Total Pagado:</strong></td>
                                <td class="text-success fw-bold">S/ {{ totalSavedPayments | toDecimals }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div v-if="savedPayments.length === 0" class="text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <p class="text-muted">No hay pagos registrados</p>
                </div>
            </div>
            <div slot="footer" class="dialog-footer text-right">
                <el-button @click="showPaymentHistoryModal = false" size="small">Cerrar</el-button>
            </div>
        </el-dialog>

        <!-- Modal de Historial de Cambios de Habitación -->
        <el-dialog
            title="Historial de Cambios de Habitación"
            width="80%"
            :visible.sync="showRoomHistoryModal"
            append-to-body
        >
            <div v-loading="loadingRoomHistory" class="room-history-content">
                <div class="room-history-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6><i class="fa fa-door"></i> Habitación</h6>
                                <p class="mb-0"><strong>{{ currentRent.room.name }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6><i class="fa fa-user"></i> Cliente</h6>
                                <p class="mb-0">{{ currentRent.customer.name }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6><i class="fa fa-calendar"></i> Período</h6>
                                <p class="mb-0">
                                    {{ formatDate(currentRent.input_date) }} - {{ formatDate(currentRent.output_date) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <h6><i class="fa fa-clock"></i> Estado Actual</h6>
                                <p class="mb-0">
                                    <span class="badge" :class="getStatusBadgeClass(currentRent.status)">
                                        {{ getStatusLabel(currentRent.status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="room-history-timeline">
                    <h5 class="mb-4">
                        <i class="fa fa-history"></i> Timeline de Cambios
                    </h5>
                    
                    <div class="timeline">
                        <div v-for="(change, index) in roomHistory" :key="index" class="timeline-item">
                            <div class="timeline-marker" :class="getChangeTypeClass(change.change_type)">
                                <i :class="getChangeTypeIcon(change.change_type)"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="change-header">
                                    <span class="change-type">{{ getChangeTypeLabel(change.change_type) }}</span>
                                    <span class="change-date">{{ formatDateTime(change.created_at) }}</span>
                                    <span class="change-user" v-if="change.user">
                                        <i class="fa fa-user-circle"></i> {{ change.user.name }}
                                    </span>
                                </div>
                                <div class="change-details">
                                    <div v-if="change.change_type === 'CHECKIN'" class="change-detail">
                                        <strong>Ingreso:</strong> {{ formatDateTime(change.old_values.input_date, change.old_values.input_time) }}
                                    </div>
                                    <div v-if="change.change_type === 'CHECKOUT'" class="change-detail">
                                        <strong>Salida:</strong> {{ formatDateTime(change.old_values.output_date, change.old_values.output_time) }}
                                    </div>
                                    <div v-if="change.change_type === 'EXTENSION'" class="change-detail">
                                        <strong>Habitación:</strong>
                                        <span class="badge badge-info">{{ (change.new_values && change.new_values.room_name) || (change.old_values && change.old_values.room_name) || currentRent.room.name }}</span>
                                        <br>
                                        <strong>Extensión:</strong>
                                        {{ (change.new_values && change.new_values.added) || change.days || 0 }}
                                        {{ (change.new_values && change.new_values.unit) || 'noche(s)' }}
                                        <br>
                                        <strong>Nueva salida:</strong> {{ formatDateTime(change.new_values.output_date, change.new_values.output_time) }}
                                    </div>
                                    <div v-if="change.change_type === 'DATE_EDIT'" class="change-detail">
                                        <div v-if="change.old_values.input_date !== change.new_values.input_date">
                                            <strong>Cambio de Ingreso:</strong><br>
                                            {{ formatDateTime(change.old_values.input_date, change.old_values.input_time) }} 
                                            <i class="fa fa-arrow-right"></i> 
                                            {{ formatDateTime(change.new_values.input_date, change.new_values.input_time) }}
                                        </div>
                                        <div v-if="change.old_values.output_date !== change.new_values.output_date">
                                            <strong>Cambio de Salida:</strong><br>
                                            {{ formatDateTime(change.old_values.output_date, change.old_values.output_time) }} 
                                            <i class="fa fa-arrow-right"></i> 
                                            {{ formatDateTime(change.new_values.output_date, change.new_values.output_time) }}
                                        </div>
                                        <div v-if="change.price_difference !== 0" class="price-change">
                                            <strong>Cambio de Precio:</strong> 
                                            <span :class="change.price_difference > 0 ? 'text-success' : 'text-danger'">
                                                S/ {{ Math.abs(change.price_difference).toFixed(2) }}
                                                <i :class="change.price_difference > 0 ? 'fa fa-arrow-up' : 'fa fa-arrow-down'"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="change.change_type === 'ROOM_CHANGE'" class="change-detail">
                                        <strong>Cambio de Habitación:</strong><br>
                                        <span class="badge badge-secondary">{{ change.old_values.room_name }}</span>
                                        <i class="fa fa-arrow-right mx-1"></i>
                                        <span class="badge badge-info">{{ change.new_values.room_name }}</span>
                                        <div v-if="change.new_values.consumed != null" class="mt-1">
                                            <small>Consumido: {{ change.new_values.consumed }} {{ change.new_values.unit || 'noche(s)' }} · Restante: {{ change.new_values.remaining }} {{ change.new_values.unit || 'noche(s)' }}</small>
                                        </div>
                                    </div>
                                    <div v-if="change.notes" class="change-notes">
                                        <strong>Notas:</strong> {{ change.notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="roomHistory.length === 0" class="text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                            <path d="M12 8v4l3 3-6-3"></path>
                            <circle cx="12" cy="12" r="9"></circle>
                        </svg>
                        <p class="text-muted">No hay cambios registrados para esta habitación</p>
                    </div>
                </div>
            </div>
            <span slot="footer">
                <el-button @click="showRoomHistoryModal = false">Cerrar</el-button>
            </span>
        </el-dialog>

        <!-- Modal de Observaciones del Cliente -->
        <el-dialog
            title="Observaciones del Cliente"
            width="50%"
            :visible.sync="showCustomerObservationsModal"
            append-to-body
        >
            <div class="customer-observations-content">
                <div class="form-group">
                    <label class="control-label">Cliente</label>
                    <el-input 
                        :value="currentRent.customer.name" 
                        readonly
                        disabled
                    ></el-input>
                </div>
                
                <div class="form-group">
                    <label class="control-label">Observaciones</label>
                    <el-input
                        type="textarea"
                        :rows="4"
                        v-model="customerObservationsForm.observations"
                        placeholder="Ingrese las observaciones del cliente..."
                    ></el-input>
                </div>
            </div>
            
            <div slot="footer" class="dialog-footer text-right">
                <el-button @click="showCustomerObservationsModal = false" size="small">Cancelar</el-button>
                <el-button 
                    type="primary" 
                    @click="saveCustomerObservations"
                    :loading="loadingCustomerObservations"
                    size="small"
                >
                    Guardar
                </el-button>
            </div>
        </el-dialog>

    </div>
</template>

<script>
import moment from "moment";
import DocumentOptions from "@views/documents/partials/options.vue";
import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import {calculateRowItem} from "@helpers/functions";
import {exchangeRate, functions} from "@mixins/functions";
import {mapActions, mapState} from "vuex/dist/vuex.mjs";

export default {
    components: {
        DocumentOptions,
        SaleNoteOptions,
    },
    mixins: [
        exchangeRate,
        functions
    ],
    props: {
        rent: {
            type: Object,
            required: true,
        },
        customer: {
            type: Object,
            required: true,
        },
        room: {
            type: Object,
            required: true,
        },
        paymentMethodTypes: {
            type: Array,
            required: true,
        },
        paymentDestinations: {
            type: Array,
            required: true,
        },
        allSeries: {
            type: Array,
            required: true,
        },
        documentTypesInvoice: {
            type: Array,
            required: true,
        },
        configuration: {
            type: Object,
            required: false,
        },
        affectationIgvTypes: {
            type: Array,
            required: true,
        },
        payments: {
            type: Array,
            required: true,
        },
        rentItems: {
            type: Array,
            required: true,
        },
    },
    computed: {
        ...mapState([
            'config',
        ]),
        canMakePayment: function () {
            if (
                this.currentRent !== undefined &&
                this.currentRent.status !== undefined &&
                this.currentRent.status !== 'FINALIZADO'
            ) {
                return true;
            }
            return false;
        },
        canGenerateReceipt: function () {
            return this.canGenerateInvoice;
        },
        canGenerateInvoice() {
            if (!this.document || !Array.isArray(this.document.items)) return false;
            return this.document.items.some(it => !it._invoiced);
        },
        invoicesHistoryCount() {
            return Array.isArray(this.invoicesHistory) ? this.invoicesHistory.length : 0;
        },
        hasDebt()
        {
            return this.totalDebt > 0
        },
        rentPaidItems()
        {
            // Solo productos pagados.
            // Las extensiones pagadas de HAB se muestran unificadas con la
            // fila de habitación principal.
            return (this.currentRent.items || []).filter(it => {
                if (it.payment_status !== 'PAID') return false;
                return it.type === 'PRO';
            });
        },
        rentDebtItems()
        {
            // Cargos pendientes = productos con deuda + extensiones HAB con deuda.
            return (this.currentRent.items || []).filter(it => {
                if (it.payment_status !== 'DEBT') return false;
                if (it.type === 'PRO') return true;
                if (it.type === 'HAB' && this.isExtensionItem(it)) return true;
                return false;
            });
        },
        roomItems()
        {
            // Items HAB base (sin extensiones).
            // Las extensiones pagadas se suman visualmente en la misma fila;
            // las extensiones con DEBT se muestran separadas en pendientes.
            const source = this.getSourceRentItemsForCheckout();
            return source
                .filter(it => {
                    if (it.type !== 'HAB') return false;
                    const isExt = this.isExtensionItem(it);
                    return !isExt;
                })
                .slice()
                .sort((a, b) => (a.id || 0) - (b.id || 0));
        },
        activeRoomItemId()
        {
            // El item HAB vigente (más reciente) es donde se aplican arrears / total editable.
            const items = this.roomItems;
            return items.length ? items[items.length - 1].id : (this.room && this.room.id);
        },
        canSavePayment() {
            // Permitir guardar siempre que haya dinero recibido y método seleccionado
            return (this.paymentForm.received > 0) && 
                   this.paymentForm.method && 
                   !this.loadingPayment;
        },
        totalSavedPayments() {
            return this.savedPayments.reduce((total, payment) => total + parseFloat(payment.amount), 0);
        },
        extensionTotal() {
            return (parseFloat(this.extendForm.pricePerDay) || 0) * (this.extendForm.days || 1);
        },
        canConfirmDateEdit() {
            // Validar que se puedan confirmar los cambios de fecha
            if (!this.editDatesForm.newPrice) return false;
            
            // Validar que las fechas sean lógicas
            if (this.editDatesModal.editInput && this.editDatesModal.editOutput) {
                const inputDateTime = moment(`${this.editDatesForm.inputDate} ${this.editDatesForm.inputTime || '00:00'}`);
                const outputDateTime = moment(`${this.editDatesForm.outputDate} ${this.editDatesForm.outputTime || '23:59'}`);
                return inputDateTime.isBefore(outputDateTime);
            }
            
            return true;
        }
    },
    created() {
        this.loadConfiguration();
        this.$store.commit('setConfiguration', this.configuration);
        this.currentRent = this.rent
        // Mantener las fechas como strings para evitar problemas de zona horaria
        // this.currentRent.input_date =  moment(this.currentRent.input_date).toDate()
        // this.currentRent.output_date =  moment(this.currentRent.output_date).toDate()
    },
    data() {
        return {
            title: "",
            currentRent: {},
            arrears: 0,
            total: 0,
            debtRoom: 0,
            loading: false,
            totalPaid: 0,
            totalDebt: 0,
            response: {},
            document: {
                payments: [],
            },
            errors: {},
            series: [],
            document_types: [],
            all_document_types: [],
            resource_documents: "documents",
            showDialogDocumentOptions: false,
            documentNewId: null,
            form_cash_document: {},
            showDialogSaleNoteOptions: false,
            showInvoicesHistoryModal: false,
            invoicesHistory: [],
            loadingInvoicesHistory: false,
            showDialogExtendStay: false,
            showPaymentModal: false,
            showPaymentHistoryModal: false,
            showCustomerObservationsModal: false,
            showEditDatesModal: false,
            showRoomHistoryModal: false,
            loadingRoomHistory: false,
            roomHistory: [],
            loadingCustomerObservations: false,
            loadingExtend: false,
            loadingPayment: false,
            loadingReverse: false,
            loadingRefund: false,
            loadingDateEdit: false,
            extendForm: {
                days: 1,
                newOutputDate: null,
                newOutputTime: null,
                pricePerDay: 0,
                includePayment: false,
                paymentAmount: 0,
                paymentMethod: 'cash',
                paymentReference: ''
            },
            paymentForm: {
                received: 0,
                change: 0,
                method: 'cash',
                reference: '',
                payment_index: null // Para edición de pagos
            },
            customerObservationsForm: {
                observations: ''
            },
            savedPayments: [],
            pickerOptions: {
                disabledDate(time) {
                    return time.getTime() < Date.now() - 8.64e7;
                }
            },
            datePickerOptions: {
                disabledDate(time) {
                    // No permitir fechas anteriores a hoy para check-out
                    // Para check-in, permitir fechas pasadas solo si es edición
                    return false; // Permitir todas las fechas, validaremos manualmente
                }
            },
            editDatesModal: {
                title: '',
                editInput: false,
                editOutput: false
            },
            editDatesForm: {
                inputDate: null,
                inputTime: null,
                outputDate: null,
                outputTime: null,
                originalPrice: 0,
                originalUnitPrice: 0,
                newPrice: null,
                priceDifference: 0,
                originalDuration: '',
                newDuration: '',
                warning: ''
            },
            form: {
                establishment_id: null,
                date_of_issue: null
            },
        };
    },
    async mounted() {
        // console.log(this.config);

        this.form.establishment_id = this.config.establishment.id;
        this.form.date_of_issue = moment().format("YYYY-MM-DD");
        await this.getPercentageIgv();

        this.room.item = await calculateRowItem(this.room.item, "PEN", 3, this.percentage_igv);

        this.initForm();
        await this.initDocument();
        this.all_document_types = this.documentTypesInvoice;
        this.title = this.currentRent.room.name;
        this.total = this.room.item.total;

        console.log('=== MOUNTED: currentRent.items ===', this.currentRent.items);

        this.rebuildDocumentItemsFromRent();
        this.syncActiveRoomTotal();

        console.log('=== DEBUG ITEMS EN DOCUMENTO ===');
        console.log('Items filtrados:', this.document.items.length);
        console.log('Items:', this.document.items);
        console.log('Total items:', this.document.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0));

        await this.onCalculateTotals();
        await this.onCalculatePaidAndDebts();
        
        // Cargar los pagos existentes desde el servidor
        console.log('=== CARGANDO PAGOS INICIALES ===');
        await this.loadRentData();
        console.log('=== PAGOS INICIALES CARGADOS ===');

        // Agregar pagos existentes al documento
        if(this.savedPayments.length > 0) {
            let cash = _.find(this.paymentDestinations, {id: 'cash'});
            this.savedPayments.forEach(payment => {
                if(parseFloat(payment.amount) > 0) { // Solo incluir pagos positivos
                    this.document.payments.push({
                        id: payment.id,
                        document_id: null,
                        date_of_payment: moment(payment.created_at).format("YYYY-MM-DD"),
                        payment_method_type_id: this.getPaymentMethodTypeId(payment.method),
                        payment_destination_id: (cash)? cash.id : null,
                        reference: payment.reference,
                        payment: parseFloat(payment.amount),
                    });
                }
            });
        }

        if(this.totalDebt > 0){
            let cash = _.find(this.paymentDestinations, {id: 'cash'})
            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: "01",
                payment_destination_id: (cash)? cash.id : null,
                reference: null,
                payment: this.totalDebt,
            });
        }

        this.validateIdentityDocumentType();
        const date = moment().format("YYYY-MM-DD");
        await this.searchExchangeRateByDate(date).then((res) => {
            this.document.exchange_rate_sale = res;
        });

        await this.loadInvoicesHistory();
    },
    watch: {
        arrears(value) {
            if (isNaN(value)) {
                return;
            }
            if (value >= 0) {
                const total = parseFloat(this.room.item.total) + parseFloat(value);
                this.total = total;
                this.onCalculatePaidAndDebts();
            }
        },
    },
    methods: {
        ...mapActions([
            'loadConfiguration',
        ]),
        moment(...args) {
            return moment(...args);
        },
        getSourceRentItemsForCheckout() {
            const currentItems = this.currentRent && Array.isArray(this.currentRent.items)
                ? this.currentRent.items
                : [];

            if (currentItems.length > 0) return currentItems;

            return Array.isArray(this.rentItems) ? this.rentItems : [];
        },
        isExtensionItem(item) {
            if (!item) return false;

            const marker = item.item && item.item.is_extension;
            if (marker === true || marker === 'true' || marker === 1 || marker === '1') {
                return true;
            }

            const descriptions = [
                item.description,
                item.item && item.item.description,
                item.item && item.item.item && item.item.item.description,
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            return descriptions.includes('extensión') || descriptions.includes('extension');
        },
        isPaidHabExtension(item) {
            return item && item.type === 'HAB' && item.payment_status === 'PAID' && this.isExtensionItem(item);
        },
        getRawItemQuantity(item) {
            if (!item) return 0;

            const jsonQuantity = parseFloat(item.item && item.item.quantity);
            if (Number.isFinite(jsonQuantity) && jsonQuantity > 0) {
                return jsonQuantity;
            }

            const columnQuantity = parseFloat(item.quantity);
            if (Number.isFinite(columnQuantity) && columnQuantity > 0) {
                return columnQuantity;
            }

            return 0;
        },
        getRawItemTotal(item) {
            if (!item) return 0;

            const columnTotal = parseFloat(item.total);
            if (Number.isFinite(columnTotal) && columnTotal > 0) {
                return columnTotal;
            }

            const jsonTotal = parseFloat(item.item && item.item.total);
            if (Number.isFinite(jsonTotal) && jsonTotal > 0) {
                return jsonTotal;
            }

            return 0;
        },
        resolveBaseRoomItemForExtension(extensionItem, baseRoomItems) {
            const baseItems = Array.isArray(baseRoomItems)
                ? baseRoomItems.slice().sort((a, b) => (a.id || 0) - (b.id || 0))
                : [];

            if (baseItems.length === 0) return null;

            const extensionId = parseInt(extensionItem && extensionItem.id, 10) || 0;
            let target = null;

            for (const baseItem of baseItems) {
                if ((baseItem.id || 0) < extensionId) {
                    target = baseItem;
                    continue;
                }
                break;
            }

            return target || baseItems[baseItems.length - 1];
        },
        getPaidExtensionAggregationForRoomItem(roomItem) {
            if (!roomItem || !roomItem.id) {
                return { quantity: 0, total: 0 };
            }

            const baseRoomItems = this.roomItems || [];
            const extensions = this.getSourceRentItemsForCheckout()
                .filter(it => this.isPaidHabExtension(it))
                .slice()
                .sort((a, b) => (a.id || 0) - (b.id || 0));

            return extensions.reduce((acc, extensionItem) => {
                const targetRoomItem = this.resolveBaseRoomItemForExtension(extensionItem, baseRoomItems);
                if (!targetRoomItem || targetRoomItem.id !== roomItem.id) return acc;

                acc.quantity += this.getRawItemQuantity(extensionItem);
                acc.total += this.getRawItemTotal(extensionItem);

                return acc;
            }, { quantity: 0, total: 0 });
        },
        rebuildDocumentItemsFromRent() {
            const sourceItems = this.getSourceRentItemsForCheckout();

            const rows = sourceItems
                .filter(item => !['9', 'PAY'].includes(item.type))
                .map(item => {
                    if (!item.item || typeof item.item !== 'object') {
                        return null;
                    }

                    if (!item.item.affectation_igv_type || _.isEmpty(item.item.affectation_igv_type)) {
                        item.item.affectation_igv_type = _.find(this.affectationIgvTypes, { id: item.item.affectation_igv_type_id });
                    }

                    const row = calculateRowItem(item.item, "PEN", 3, this.percentage_igv);
                    row._rent_item_id = item.id;
                    row._rent_item_ids = [item.id];
                    row._invoiced = !!(item.sale_note_id || item.document_id);
                    row._document = item.document || null;
                    row._source_type = item.type;
                    row._source_payment_status = item.payment_status;
                    row._is_extension = this.isExtensionItem(item);
                    return row;
                })
                .filter(Boolean);

            this.document.items = this.mergePaidExtensionsInDocumentRows(rows);
        },
        mergePaidExtensionsInDocumentRows(rows) {
            if (!Array.isArray(rows) || rows.length === 0) {
                return [];
            }

            const rowByRentItemId = {};
            rows.forEach((row) => {
                rowByRentItemId[row._rent_item_id] = row;
                if (!Array.isArray(row._rent_item_ids) || row._rent_item_ids.length === 0) {
                    row._rent_item_ids = row._rent_item_id ? [row._rent_item_id] : [];
                }
            });

            const baseRoomItems = this.roomItems;

            // Todas las extensiones HAB (pagadas y con deuda), no solo las pagadas:
            // las noches extendidas deben consolidarse en una sola línea aunque
            // todavía no se hayan pagado.
            const extensions = this.getSourceRentItemsForCheckout()
                .filter(it => it.type === 'HAB' && this.isExtensionItem(it))
                .slice()
                .sort((a, b) => (a.id || 0) - (b.id || 0));

            // Agrupar las extensiones por el item HAB base al que pertenecen.
            const groupsByBaseId = {};
            extensions.forEach((extensionItem) => {
                const targetRoomItem = this.resolveBaseRoomItemForExtension(extensionItem, baseRoomItems);
                if (!targetRoomItem || !targetRoomItem.id) return;
                (groupsByBaseId[targetRoomItem.id] = groupsByBaseId[targetRoomItem.id] || [])
                    .push(extensionItem);
            });

            Object.keys(groupsByBaseId).forEach((baseId) => {
                // Filas de extensión aún no facturadas (las ya facturadas pertenecen
                // a otro comprobante y no deben tocarse).
                const extensionRows = groupsByBaseId[baseId]
                    .map(ext => rowByRentItemId[ext.id])
                    .filter(row => row && !row._merged_into && !row._invoiced);

                if (extensionRows.length === 0) return;

                const baseRow = rowByRentItemId[baseId];

                // Destino de la consolidación:
                //  - el item base de la habitación si está disponible y todavía no
                //    se facturó (habitación + extensiones salen como una sola línea);
                //  - de lo contrario, la primera fila de extensión (caso típico: el
                //    comprobante de la habitación ya se emitió y solo se facturan las
                //    extensiones nuevas, que deben verse como un único item).
                const targetRow = (baseRow && !baseRow._invoiced) ? baseRow : extensionRows[0];

                extensionRows.forEach((extensionRow) => {
                    if (extensionRow === targetRow) return;

                    this.mergeDocumentRowTotals(targetRow, extensionRow);
                    targetRow._rent_item_ids = _.uniq([
                        ...(targetRow._rent_item_ids || []),
                        ...(extensionRow._rent_item_ids || []),
                    ]);
                    extensionRow._merged_into = targetRow._rent_item_id;
                });

                // Si el destino es una extensión (la habitación ya fue facturada por
                // separado), reescribir su descripción para que refleje el total de
                // noches consolidadas en lugar de la cantidad de la primera fila.
                if (targetRow !== baseRow) {
                    this.relabelConsolidatedExtensionRow(targetRow);
                }
            });

            return rows.filter(row => !row._merged_into);
        },
        relabelConsolidatedExtensionRow(row) {
            const quantity = parseFloat(row && row.quantity) || 0;
            if (quantity <= 0 || !row.item) return;

            // Unidad de tiempo según el tipo de renta.
            let unit = 'noche(s)';
            const period = this.currentRent && this.currentRent.rental_period_type;
            if (period === 'hour') unit = 'hora(s)';
            else if (period === 'month') unit = 'mes(es)';

            // Conservar el prefijo "Extensión <habitación>" de la descripción
            // original y reemplazar solo la cantidad por el total consolidado.
            const original = row.item.description || row.item.full_description || 'Extensión';
            const prefix = String(original).split(' - ')[0] || 'Extensión';
            const name = `${prefix} - ${quantity} ${unit}`;

            row.item.description = name;
            row.item.full_description = name;
            row.item.name_product_pdf = name;
            row.name_product_pdf = name;
        },
        mergeDocumentRowTotals(targetRow, sourceRow) {
            const numericFields = [
                'quantity',
                'unit_value',
                'total_base_igv',
                'total_igv',
                'total_taxes',
                'total_value',
                'total',
                'total_charge',
                'total_discount',
                'total_plastic_bag_taxes',
                'total_value_without_rounding',
                'total_base_igv_without_rounding',
                'total_igv_without_rounding',
                'total_taxes_without_rounding',
                'total_without_rounding',
            ];

            numericFields.forEach((field) => {
                targetRow[field] = (parseFloat(targetRow[field]) || 0) + (parseFloat(sourceRow[field]) || 0);
            });

            if (targetRow.item) {
                targetRow.item.quantity = targetRow.quantity;
            }
        },
        syncActiveRoomTotal() {
            const activeItem = (this.roomItems || []).length
                ? this.roomItems[this.roomItems.length - 1]
                : null;

            if (activeItem) {
                this.total = this.getRoomItemTotal(activeItem);
            }
        },
        getRoomItemUnitPrice(r) {
            if (!r) return 0;

            const quantity = this.getRoomItemQuantity(r);
            const total = this.getRoomItemTotal(r);
            if (quantity > 0 && total > 0) {
                return total / quantity;
            }

            const col = parseFloat(r.unit_price);
            if (col > 0) return col;
            const json = r.item && (r.item.unit_price ?? r.item.unit_price_value);
            return parseFloat(json) || 0;
        },
        getTotalExtensionNights() {
            // Suma de noches de TODAS las extensiones HAB (pagadas y con deuda),
            // porque el backend incrementa rent.duration por cada extensión sin
            // importar su estado de pago.
            return this.getSourceRentItemsForCheckout()
                .filter(it => it.type === 'HAB' && this.isExtensionItem(it))
                .reduce((sum, it) => sum + this.getRawItemQuantity(it), 0);
        },
        getRoomItemQuantity(r) {
            if (!r) return 0;
            let baseQuantity = this.getRawItemQuantity(r);

            // Compatibilidad con registros antiguos: la columna quantity se
            // inicializó con default 1 y en algunos casos históricos nunca se
            // sincronizó con la duración real del alquiler. Usamos rent.duration
            // como piso, pero RESTANDO las noches de extensiones: rent.duration
            // es acumulativo (incluye extensiones) y éstas se cuentan aparte como
            // items propios. Sin esta resta se inflaba la fila base (p.ej. 1
            // noche se mostraba como 2) y el precio unitario salía mal (70 → 35).
            if (!this.isExtensionItem(r) && (this.roomItems || []).length === 1) {
                const rentDuration = parseFloat(this.currentRent && this.currentRent.duration);
                if (Number.isFinite(rentDuration) && rentDuration > 0) {
                    const baseDuration = Math.max(0, rentDuration - this.getTotalExtensionNights());
                    baseQuantity = Math.max(baseQuantity, baseDuration);
                }
            }

            const extensionAggregation = this.getPaidExtensionAggregationForRoomItem(r);
            return baseQuantity + (extensionAggregation.quantity || 0);
        },
        getRoomItemTotal(r) {
            if (!r) return 0;
            const baseTotal = this.getRawItemTotal(r);
            const extensionAggregation = this.getPaidExtensionAggregationForRoomItem(r);
            return baseTotal + (extensionAggregation.total || 0);
        },
        validateIdentityDocumentType() {

            let identity_document_types = ["0", "1"];
            let customer = this.document.customer;

            if (
                identity_document_types.includes(customer.identity_document_type_id)
            ) {

                this.document_types = this.all_document_types.filter((row) => {
                    return ['03', '80'].includes(row.id)
                })

                // this.document_types = _.filter(this.all_document_types, { id: "03" });
            } else {
                this.document_types = this.all_document_types;
            }

            this.document.document_type_id =
                this.document_types.length > 0 ? this.document_types[0].id : null;
            this.changeDocumentType();
        },
        changeDateOfIssue() {
            this.document.date_of_due = this.document.date_of_issue;
        },
        changeDocumentType() {
            this.document.series_id = null;
            this.series = _.filter(this.allSeries, {
                document_type_id: this.document.document_type_id,
            });
            this.document.series_id =
                this.series.length > 0 ? this.series[0].id : null;
        },
        clickAddPayment() {

            /*
            const payment =
                this.document.payments.length == 0 ? this.document.total : 0;
            */

            let payment = 0

            if(this.document.payments.length == 0)
            {
                if(this.totalDebt > 0)
                {
                    payment = this.totalDebt
                }
            }

            let cash = _.find(this.paymentDestinations, {id: 'cash'})
            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: "01",
                payment_destination_id: (cash)? cash.id : null,
                reference: null,
                payment: payment,
            });
        },
        onExitPage() {
            window.location.href = "/hotels/reception";
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
        validateTotalPayments()
        {
            // Sumar pagos del documento más pagos ya guardados
            const document_payments = _.sumBy(this.document.payments, 'payment');
            const total_payments = document_payments;

            // El total de pagos no debe superar el monto total de los items
            const total_amount = this.document.total || 0;
            
            if(total_payments > total_amount) {
                return this.getResponseValidations(false, `El total de los pagos (S/ ${total_payments.toFixed(2)}) es superior al monto del comprobante (S/ ${total_amount.toFixed(2)}).`);
            }

            return this.getResponseValidations()
        },
        initForm() {
            this.form_cash_document = {
                document_id: null,
                sale_note_id: null,
            };
        },
        updateDataForSend() {
            console.log('updateDataForSend');

            if (this.document.document_type_id === '80') {
                this.document.prefix = 'NV'
                this.resource_documents = 'sale-notes'
            } else {
                this.document.prefix = null
                this.resource_documents = 'documents'
            }

        },
        successGoToInvoice() {
            console.log('successGoToInvoice');

            //inicializa form_cash_document
            this.initForm()

            if (this.document.document_type_id === '80') //NV
            {
                this.form_cash_document.sale_note_id = this.documentNewId
                this.showDialogSaleNoteOptions = true

            } else {
                this.form_cash_document.document_id = this.documentNewId
                this.showDialogDocumentOptions = true;
            }

        },
        async onGoToFinalizeRent() {
            this.loading = true;
            const payloadFinalizedRent = {
                arrears: this.arrears != null && this.arrears !== '' ? this.arrears : 0,
            };
            try {
                const response = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/rent/finalized`,
                    payloadFinalizedRent
                );
                if (response.data.success) {
                    this.$message({
                        message: response.data.message,
                        type: "success",
                    });
                    window.location.href = "/hotels/reception";
                } else {
                    this.$message.error(response.data.message || 'No se pudo finalizar el alquiler');
                }
            } catch (error) {
                // Importante: NO bloquear navegación — siempre liberar el loading
                // y mostrar mensaje de error legible para el usuario.
                console.error('Error al finalizar:', error);
                this.$message.error(error.response?.data?.message || 'Error al finalizar el alquiler. Puede continuar navegando.');
            } finally {
                this.loading = false;
            }
        },
        consolidateRoomNightsForDocument() {
            const items = Array.isArray(this.document.items) ? this.document.items : [];
            const roomItemId = this.room && this.room.item_id;
            if (!roomItemId || items.length === 0) return;

            // Campos numéricos a acumular en la fila destino. Aunque
            // onUpdateItemsWithExtras recalcula la fila a partir de
            // quantity/total, sumamos todos para mantener la fila coherente.
            const sumFields = [
                'quantity',
                'total',
                'total_value',
                'total_base_igv',
                'total_igv',
                'total_taxes',
                'total_charge',
                'total_discount',
                'total_plastic_bag_taxes',
                'total_without_rounding',
                'total_value_without_rounding',
                'total_base_igv_without_rounding',
                'total_igv_without_rounding',
                'total_taxes_without_rounding',
            ];

            let target = null;
            const result = [];

            items.forEach((it) => {
                // Solo se fusionan las noches de la habitación activa que aún no
                // se han facturado. Productos, otras habitaciones y noches ya
                // facturadas (que pertenecen a otro comprobante) no se tocan.
                const isPendingRoomNight = it && it.item_id === roomItemId && !it._invoiced;

                if (!isPendingRoomNight) {
                    result.push(it);
                    return;
                }

                if (!target) {
                    target = it;
                    result.push(it);
                    return;
                }

                sumFields.forEach((field) => {
                    target[field] = (parseFloat(target[field]) || 0) + (parseFloat(it[field]) || 0);
                });
                if (target.item) {
                    target.item.quantity = target.quantity;
                }

                // Conservar todos los ids de items de renta para marcarlos como
                // facturados luego (mark-items-invoiced).
                target._rent_item_ids = _.uniq([
                    ...(Array.isArray(target._rent_item_ids) ? target._rent_item_ids : [target._rent_item_id].filter(Boolean)),
                    ...(Array.isArray(it._rent_item_ids) ? it._rent_item_ids : [it._rent_item_id].filter(Boolean)),
                ]);
            });

            this.document.items = result;
        },
        async onGenerateInvoice() {
            // Consolidar en una sola línea las noches de la misma habitación que
            // todavía no se han facturado (habitación base + extensiones hechas
            // de forma individual). Sin esto, cada noche/extensión sale como un
            // item separado en el comprobante. Se hace ANTES de
            // onUpdateItemsWithExtras para que la suma de noches (quantity) se
            // refleje en la descripción "Habitación X x N noche(s)".
            this.consolidateRoomNightsForDocument();

            await this.onUpdateItemsWithExtras();

            const allItems = this.document.items || [];
            const pendingItems = allItems.filter(it => !it._invoiced);

            if (pendingItems.length === 0) {
                return this.$message.warning("No hay items pendientes por facturar.");
            }

            const validate_payment_destination = this.validatePaymentDestination();
            if (validate_payment_destination.error_by_item > 0) {
                return this.$message.error("El destino del pago es obligatorio");
            }

            this.updateDataForSend();

            // Swap: solo items pendientes, recalcular totales, POST, restaurar al final
            const savedItems = this.document.items;
            const savedPayments = _.cloneDeep(this.document.payments);
            this.document.items = pendingItems;
            await this.onCalculateTotals();

            // Reasignar pagos al total pendiente
            const newTotal = parseFloat(this.document.total) || 0;
            this.document.payments = (savedPayments || []).map((p, idx) => ({
                ...p,
                payment: idx === 0 ? newTotal : 0,
            })).filter(p => p.payment > 0);
            if (this.document.payments.length === 0 && newTotal > 0) {
                const cash = _.find(this.paymentDestinations, { id: 'cash' });
                this.document.payments.push({
                    id: null,
                    document_id: null,
                    date_of_payment: moment().format("YYYY-MM-DD"),
                    payment_method_type_id: "01",
                    payment_destination_id: cash ? cash.id : null,
                    reference: null,
                    payment: newTotal,
                });
            }

            this.loading = true;
            try {
                const response = await this.$http.post(`/${this.resource_documents}`, this.document);
                if (!response.data.success) {
                    this.$message.error(response.data.message);
                    return;
                }

                this.documentNewId = response.data.data.id;
                const isSaleNote = this.document.document_type_id === '80';

                console.log('=== INVOICE CREATED ===');
                console.log('document_type_id:', this.document.document_type_id);
                console.log('isSaleNote:', isSaleNote);
                console.log('documentNewId:', this.documentNewId);
                console.log('pendingItems:', pendingItems);
                console.log('pendingItems _rent_item_ids values:', pendingItems.map(it => it._rent_item_ids || [it._rent_item_id]));

                const markItemIds = _.uniq(
                    pendingItems
                        .flatMap(it => Array.isArray(it._rent_item_ids) ? it._rent_item_ids : [it._rent_item_id])
                        .filter(Boolean)
                );

                const markPayload = {
                    sale_note_id: isSaleNote ? this.documentNewId : null,
                    document_id: isSaleNote ? null : this.documentNewId,
                    item_ids: markItemIds,
                };

                console.log('=== mark-items-invoiced payload ===', markPayload);

                if (markPayload.item_ids.length === 0) {
                    this.$message.error('No se encontraron _rent_item_id en los items pendientes. Revisa la consola.');
                    return;
                }

                const markResponse = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/rent/mark-items-invoiced`,
                    markPayload
                );

                console.log('=== mark-items-invoiced response ===', markResponse.data);

                if (!markResponse.data.success) {
                    this.$message.error(markResponse.data.message || 'No se pudo marcar los items como facturados');
                    return;
                }

                if (markResponse.data.updated_count === 0) {
                    this.$message.warning('Comprobante creado pero no se actualizó ningún item (updated_count=0). Revisa la consola.');
                }

                pendingItems.forEach(it => { this.$set(it, '_invoiced', true); });

                await this.refreshRentItemsFromServer();
                await this.loadInvoicesHistory();

                this.successGoToInvoice();
                this.saveCashDocument();
                this.$message.success('Comprobante generado correctamente.');
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data;
                    const msg = error.response.data && error.response.data.message ? error.response.data.message : 'Datos inválidos';
                    this.$message.error('422: ' + msg);
                    console.error('422 errors:', error.response.data);
                } else {
                    this.$message.error(error.response && error.response.data ? error.response.data.message : 'Error al generar comprobante');
                }
            } finally {
                this.document.items = savedItems;
                this.document.payments = savedPayments || [];
                await this.onCalculateTotals();
                this.loading = false;
            }
        },
        async refreshRentItemsFromServer() {
            try {
                const { data } = await this.$http.get(`/hotels/reception/${this.currentRent.id}/rent/checkout-data`);
                if (!data.success) return;

                const freshItems = data.data.items || [];
                const freshRoom = data.data.room || null;

                freshItems.forEach(fresh => {
                    const target = this.rentItems.find(r => r.id === fresh.id);
                    if (target) {
                        this.$set(target, 'document', fresh.document);
                        this.$set(target, 'payment_status', fresh.payment_status);
                        this.$set(target, 'sale_note_id', fresh.sale_note_id);
                        this.$set(target, 'document_id', fresh.document_id);
                        this.$set(target, 'invoiced_at', fresh.invoiced_at);
                    }
                });

                if (freshRoom && this.room) {
                    this.$set(this.room, 'document', freshRoom.document);
                    this.$set(this.room, 'payment_status', freshRoom.payment_status);
                    this.$set(this.room, 'sale_note_id', freshRoom.sale_note_id);
                    this.$set(this.room, 'document_id', freshRoom.document_id);
                }

                if (Array.isArray(this.document.items)) {
                    this.document.items.forEach(docItem => {
                        const relatedIds = Array.isArray(docItem._rent_item_ids) && docItem._rent_item_ids.length
                            ? docItem._rent_item_ids
                            : [docItem._rent_item_id];
                        const relatedItems = freshItems.filter(r => relatedIds.includes(r.id));

                        if (relatedItems.length > 0) {
                            const allInvoiced = relatedItems.every(r => !!(r.sale_note_id || r.document_id));
                            const firstDocument = relatedItems.find(r => !!r.document);
                            this.$set(docItem, '_invoiced', allInvoiced);
                            this.$set(docItem, '_document', firstDocument ? firstDocument.document : null);
                        }
                    });
                }
            } catch (e) {
                console.error('Error refrescando items:', e);
            }
        },
        async loadInvoicesHistory() {
            this.loadingInvoicesHistory = true;
            try {
                const { data } = await this.$http.get(`/hotels/reception/${this.currentRent.id}/rent/invoices-history`);
                if (data.success) {
                    this.invoicesHistory = data.data;
                }
            } catch (e) {
                console.error('Error cargando historial:', e);
            } finally {
                this.loadingInvoicesHistory = false;
            }
        },
        openInvoicesHistory() {
            this.loadInvoicesHistory();
            this.showInvoicesHistoryModal = true;
        },
        printInvoice(invoice) {
            // Las notas de venta usan su ruta dedicada (/sale-notes/print/...).
            // Para documentos (boletas/facturas) la ruta correcta es la genérica
            // /print/{model}/{external_id}/{format} expuesta por DownloadController.
            const url = invoice.type === 'sale_note'
                ? `/sale-notes/print/${invoice.external_id}/a4`
                : `/print/document/${invoice.external_id}/a4`;
            window.open(url, '_blank');
        },
        onUpdateItemsWithExtras() {
            this.document.items = this.document.items.map((it) => {
                if (it.item_id === this.room.item_id) {
                    let dayQuantity = it.quantity;
                    
                    // Determinar la unidad de tiempo según el tipo de renta
                    let timeUnit = 'noche(s)';
                    if (this.currentRent.rental_period_type === 'hour') {
                        timeUnit = 'hora(s)';
                    } else if (this.currentRent.rental_period_type === 'month') {
                        timeUnit = 'mes(es)';
                    }
                    
                    const name = `${it.item.name} x ${dayQuantity} ${timeUnit}`;
                    it.item.description = name;
                    it.item.full_description = name;
                    it.name_product_pdf = name;
                    it.quantity = 1;
                    const newTotal = parseFloat(it.total) + parseFloat(this.arrears);
                    it.input_unit_price_value = parseFloat(newTotal);
                    it.item.unit_price = parseFloat(newTotal);
                    it.unit_value = parseFloat(newTotal);
                    const newItem = calculateRowItem(it, "PEN", 3, this.percentage_igv);
                    newItem._rent_item_id = it._rent_item_id;
                    newItem._rent_item_ids = Array.isArray(it._rent_item_ids)
                        ? [...it._rent_item_ids]
                        : [it._rent_item_id].filter(Boolean);
                    newItem._invoiced = it._invoiced;
                    newItem._document = it._document;
                    return newItem;
                }
                return it;
            });
        },
        // async getItemsForSaleNote()
        // {
        //   return await this.$http.post(`/sale-notes/items-by-ids`, { ids : _.map(this.document.items, 'item_id')})
        // },
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
        getPaymentMethodFromId(methodId) {
            const methodMap = {
                '01': 'cash',
                '02': 'credit_card',
                '03': 'debit_card',
                '04': 'transfer',
                '05': 'yape_plin'
            };
            return methodMap[methodId] || 'cash';
        },
        getPaymentMethodTypeId(method) {
            const methodMap = {
                'cash': '01',
                'credit_card': '02', 
                'debit_card': '03',
                'transfer': '04',
                'yape_plin': '05'
            };
            return methodMap[method] || '01'; // Default to cash if not found
        },
        async loadRentData() {
            try {
                console.log('Cargando datos del rent ID:', this.currentRent.id);
                
                // Recargar los datos del rent desde el servidor
                const response = await this.$http.get(`/hotels/reception/${this.currentRent.id}/rent/checkout-data`);
                
                console.log('Respuesta completa del servidor:', response.data);
                console.log('Payments del servidor:', response.data.data?.payments);
                console.log('Tipo de payments:', typeof response.data.data?.payments);
                console.log('¿Es array?', Array.isArray(response.data.data?.payments));
                console.log('Longitud de payments:', response.data.data?.payments ? response.data.data.payments.length : 'undefined');
                
                if (response.data && response.data.success) {
                    // Actualizar los datos del rent y sus items
                    this.currentRent = response.data.data.rent;
                    // IMPORTANTE: Actualizar los items con los estados más recientes
                    this.currentRent.items = response.data.data.items || [];
                    this.room = response.data.data.room || this.room;
                    this.rebuildDocumentItemsFromRent();
                    this.syncActiveRoomTotal();
                    
                    // Debug extendido para pagos del backend
                    console.log('=== DEBUG BACKEND PAYMENTS ===');
                    if (response.data.data.payments) {
                        console.log('Payments encontrados:', response.data.data.payments.length);
                        response.data.data.payments.forEach((payment, index) => {
                            console.log(`Backend Payment ${index}:`, payment);
                        });
                    } else {
                        console.log('No hay payments en response.data.data.payments');
                        console.log('Keys en response.data.data:', Object.keys(response.data.data));
                    }
                    
                    // Transformar los pagos al formato esperado por savedPayments
                    if (response.data.data.payments && Array.isArray(response.data.data.payments)) {
                        console.log('Transformando pagos...');
                        this.savedPayments = response.data.data.payments.map((payment, index) => {
                            console.log(`Pago ${index}:`, payment);
                            console.log(`Fecha recibida: ${payment.date_of_payment}`);
                            return {
                                id: payment.id,
                                amount: payment.payment,
                                method: this.getPaymentMethodFromId(payment.payment_method_type_id),
                                reference: payment.reference,
                                created_at: payment.date_of_payment,
                                change: payment.change
                            };
                        });
                        console.log('Pagos transformados:', this.savedPayments);
                    } else {
                        console.log('No hay pagos o no es array, inicializando vacío');
                        this.savedPayments = [];
                    }
                    
                    console.log('savedPayments final:', this.savedPayments);
                    console.log('savedPayments.length final:', this.savedPayments.length);
                    
                    // Recalcular totales después de cargar los pagos
                    console.log('=== RECÁLCULO DESPUÉS DE CARGAR PAGOS ===');
                    this.onCalculatePaidAndDebts();
                }
            } catch (error) {
                console.error('Error al recargar datos:', error);
                this.$message.error('Error al recargar los datos del alquiler');
            }
        },
        onCalculatePaidAndDebts() {
            // Debug: mostrar items actuales
            console.log('Items actuales:', this.currentRent.items);
            console.log('savedPayments.length:', this.savedPayments.length);
            console.log('savedPayments:', this.savedPayments);
            
            // Para el cálculo de deuda: calcular basado en items originales menos pagos positivos
            const totalOriginalItems = this.currentRent.items
                .filter((i) => i.type !== 'PAY') // Excluir items de pago
                .map((i) => {
                    const total = parseFloat(i.item?.total) || 0;
                    console.log('Item original:', i.type, 'payment_status:', i.payment_status, 'total:', total);
                    return total;
                })
                .reduce((a, b) => a + b, 0);
            
            // Calcular pagos positivos reales
            const totalPositivePayments = this.savedPayments
                .filter((payment) => parseFloat(payment.amount) > 0)
                .reduce((sum, payment) => sum + parseFloat(payment.amount), 0);
            
            // Calcular devoluciones (pagos negativos)
            const totalRefunds = this.savedPayments
                .filter((payment) => parseFloat(payment.amount) < 0)
                .reduce((sum, payment) => sum + Math.abs(parseFloat(payment.amount)), 0);
            
            // Calcular total neto pagado (positivos - negativos)
            const netPayments = totalPositivePayments - totalRefunds;
            
            console.log('totalPositivePayments:', totalPositivePayments);
            console.log('totalRefunds:', totalRefunds);
            console.log('netPayments:', netPayments);
            
            // La deuda es: total original - pagos netos + arrears
            const calculatedDebt = totalOriginalItems - netPayments + parseFloat(this.arrears || 0);
            
            // Para el cálculo de deuda visual: usar items con estado DEBT (más preciso para estado actual)
            const totalDebt = this.currentRent.items
                .filter((i) => i.type !== 'PAY') // Excluir items de pago
                .map((i) => {
                    if (i.payment_status === "DEBT") {
                        const total = parseFloat(i.item?.total) || 0;
                        console.log('Item DEBT:', i.type, 'total:', total);
                        return total;
                    }
                    return 0;
                })
                .reduce((a, b) => a + b, 0);
            
            // Debug extendido para savedPayments
            console.log('=== DEBUG SAVEDPAYMENTS ===');
            console.log('savedPayments:', this.savedPayments);
            console.log('savedPayments.length:', this.savedPayments.length);
            console.log('¿Es array?', Array.isArray(this.savedPayments));
            
            if (this.savedPayments.length > 0) {
                this.savedPayments.forEach((payment, index) => {
                    console.log(`Payment ${index}:`, {
                        id: payment.id,
                        amount: payment.amount,
                        method: payment.method,
                        amountParsed: parseFloat(payment.amount),
                        isPositive: parseFloat(payment.amount) > 0
                    });
                });
            } else {
                console.log('No hay pagos en savedPayments');
            }
            
            // Para el cálculo de pagado: usar el historial de pagos completo (positivos + negativos)
            this.totalPaid = this.savedPayments
                .map((payment) => {
                    const amount = parseFloat(payment.amount) || 0;
                    console.log('Pago del historial (completo):', payment.method, 'amount:', amount);
                    return amount;
                })
                .reduce((a, b) => a + b, 0);
                
            // Usar el cálculo más preciso para el totalDebt
            this.totalDebt = calculatedDebt;
            
            // Debug: mostrar valores calculados
            console.log('totalPaid (desde historial):', this.totalPaid);
            console.log('totalDebt (items):', totalDebt);
            console.log('arrears:', this.arrears);
            console.log('totalDebt final:', this.totalDebt);
            console.log('canMakePayment:', this.canMakePayment);
        },
        initDocument() {
            this.document = {
                customer_id: this.currentRent.customer_id,
                customer: this.currentRent.customer,
                document_type_id: null,
                series_id: null,
                prefix: null,
                establishment_id: this.config.establishment.id,
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
                hotel_data_persons: this.currentRent.data_persons,
                source_module: 'HOTEL',
                hotel_rent_id: this.currentRent.id
            };
        },
        onGotoBack() {
            window.location.href = "/hotels/reception";
        },
        clickCancel(index) {
            this.document.payments.splice(index, 1);
        },
        onCalculateTotals() {
            console.log('=== DEBUG ON CALCULATE TOTALS ===');
            console.log('Items en documento:', this.document.items.length);
            console.log('Items:', this.document.items);
            
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
            this.document.items.forEach((row, index) => {
                console.log(`Item ${index}:`, {
                    description: row.description,
                    total: row.total,
                    total_value: row.total_value,
                    affectation_igv_type_id: row.affectation_igv_type_id
                });
                
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

            console.log('Subtotales:', {
                total_taxed,
                total_exonerated,
                total_free,
                total_igv,
                total_before_plastic: total
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
            
            console.log('=== RESULTADO FINAL ===');
            console.log('Document total:', this.document.total);
            console.log('Document subtotal:', this.document.subtotal);
            console.log('Total plastic bag taxes:', this.document.total_plastic_bag_taxes);
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
        onExtendStay() {
            // Inicializar el formulario con la fecha y hora actual de salida
            this.extendForm.newOutputDate = this.currentRent.output_date;
            this.extendForm.newOutputTime = this.currentRent.output_time;
            this.extendForm.days = 1;
            
            // Obtener precio actual de la habitación
            const roomItem = this.currentRent.items.find(item => item.type === 'HAB');
            this.extendForm.pricePerDay = roomItem ? parseFloat(roomItem.item.unit_price) || 0 : 0;
            
            // Inicializar campos de pago
            this.extendForm.includePayment = false;
            this.extendForm.paymentAmount = 0;
            this.extendForm.paymentMethod = 'cash';
            this.extendForm.paymentReference = '';
            
            this.showDialogExtendStay = true;
        },
        onDaysChange(value) {
            // Calcular nueva fecha basada en los días
            if (value && this.currentRent.output_date) {
                const currentDate = moment(this.currentRent.output_date);
                const newDate = currentDate.add(value - 1, 'days'); // -1 porque ya cuenta el día actual
                this.extendForm.newOutputDate = newDate.format('YYYY-MM-DD');
            }
        },
        onDateChange(value) {
            // Calcular días basados en la nueva fecha
            if (value && this.currentRent.output_date) {
                const currentDate = moment(this.currentRent.output_date);
                const newDate = moment(value);
                const days = newDate.diff(currentDate, 'days') + 1; // +1 para incluir ambos días
                this.extendForm.days = Math.max(1, Math.min(30, days)); // Limitar entre 1 y 30
            }
        },
        calculateExtensionTotal() {
            // Este método se llama cuando cambia el precio por día
            // El total se calcula automáticamente con la propiedad computada
        },
        onIncludePaymentChange(value) {
            if (value) {
                // Si se incluye pago, establecer el monto por defecto al total de la extensión
                this.extendForm.paymentAmount = this.extensionTotal;
            } else {
                // Si no se incluye pago, limpiar los campos
                this.extendForm.paymentAmount = 0;
                this.extendForm.paymentMethod = 'cash';
                this.extendForm.paymentReference = '';
            }
        },
        async confirmExtendStay() {
            try {
                this.loadingExtend = true;
                
                const payload = {
                    days: this.extendForm.days,
                    new_output_date: this.extendForm.newOutputDate,
                    new_output_time: this.extendForm.newOutputTime,
                    price_per_day: this.extendForm.pricePerDay,
                    include_payment: this.extendForm.includePayment
                };
                
                // Si se incluye pago, agregar los datos de pago
                if (this.extendForm.includePayment) {
                    payload.payment_amount = this.extendForm.paymentAmount;
                    payload.payment_method = this.extendForm.paymentMethod;
                    payload.payment_reference = this.extendForm.paymentReference;
                }
                
                const response = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/extend-stay`,
                    payload
                );
                
                if (response.data.success) {
                    this.$message.success('Estadía extendida correctamente');
                    
                    // Registrar el cambio en el historial
                    await this.recordRoomChange(
                        'EXTENSION',
                        {
                            output_date: this.currentRent.output_date,
                            output_time: this.currentRent.output_time
                        },
                        {
                            output_date: this.extendForm.newOutputDate,
                            output_time: this.extendForm.newOutputTime
                        },
                        `Extensión de ${this.extendForm.days} días`,
                        this.extensionTotal
                    );
                    
                    // Actualizar los datos del rent actual
                    this.currentRent = response.data.rent;
                    this.rebuildDocumentItemsFromRent();
                    this.syncActiveRoomTotal();
                    
                    // Recalcular totales
                    this.onCalculatePaidAndDebts();
                    
                    // Cerrar el diálogo
                    this.showDialogExtendStay = false;
                    
                    // Actualizar la duración y otros campos si es necesario
                    this.initForm();
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                console.error('Error al extender estadía:', error);
                if (error.response && error.response.data) {
                    this.$message.error(error.response.data.message || 'Error al extender la estadía');
                } else {
                    this.$message.error('Error al extender la estadía');
                }
            } finally {
                this.loadingExtend = false;
            }
        },
        // Métodos para edición de fechas
        showEditCheckinDates() {
            this.editDatesModal.title = 'Editar Fecha de Ingreso';
            this.editDatesModal.editInput = true;
            this.editDatesModal.editOutput = false;
            
            // Inicializar formulario con valores actuales
            this.editDatesForm.inputDate = moment(this.currentRent.input_date).subtract(1, 'day').format('YYYY-MM-DD');
            this.editDatesForm.inputTime = this.currentRent.input_time || '14:00';
            this.editDatesForm.outputDate = moment(this.currentRent.output_date).subtract(1, 'day').format('YYYY-MM-DD');
            this.editDatesForm.outputTime = this.currentRent.output_time || '11:59';
            
            // Guardar precio original - usar el precio real por período
            const rentalType = this.currentRent.rental_period_type;
            let originalUnitPrice = 0;
            
            if (rentalType === 'hour') {
                // Para renta por hora, usar rental_price como precio por hora
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else if (rentalType === 'day') {
                // Para renta por día, usar rental_price como precio por día
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else if (rentalType === 'month') {
                // Para renta por mes, usar rental_price como precio por mes
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else {
                // Para tipo estándar, calcular precio por día del total
                const originalDuration = moment.duration(
                    moment(`${this.currentRent.output_date} ${this.currentRent.output_time}`).diff(
                        moment(`${this.currentRent.input_date} ${this.currentRent.input_time}`)
                    )
                );
                const originalDays = Math.max(1, Math.ceil(originalDuration.asDays()));
                originalUnitPrice = parseFloat(this.room.item.unit_price) / originalDays;
            }
            
            this.editDatesForm.originalPrice = parseFloat(this.room.item.total); // Precio total original
            this.editDatesForm.originalUnitPrice = originalUnitPrice; // Precio por unidad original
            this.editDatesForm.newPrice = null;
            this.editDatesForm.priceDifference = 0;
            
            // Calcular duración original
            this.editDatesForm.originalDuration = this.calculateDuration(
                this.currentRent.input_date, 
                this.currentRent.input_time,
                this.currentRent.output_date, 
                this.currentRent.output_time
            );
            
            this.editDatesForm.warning = '';
            
            this.showEditDatesModal = true;
        },
        showEditCheckoutDates() {
            this.editDatesModal.title = 'Editar Fecha de Salida';
            this.editDatesModal.editInput = false;
            this.editDatesModal.editOutput = true;
            
            // Inicializar formulario con valores actuales
            this.editDatesForm.inputDate = moment(this.currentRent.input_date).subtract(1, 'day').format('YYYY-MM-DD');
            this.editDatesForm.inputTime = this.currentRent.input_time || '14:00';
            this.editDatesForm.outputDate = moment(this.currentRent.output_date).subtract(1, 'day').format('YYYY-MM-DD');
            this.editDatesForm.outputTime = this.currentRent.output_time || '11:59';
            
            // Guardar precio original - usar el precio real por período
            const rentalType = this.currentRent.rental_period_type;
            let originalUnitPrice = 0;
            
            if (rentalType === 'hour') {
                // Para renta por hora, usar rental_price como precio por hora
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else if (rentalType === 'day') {
                // Para renta por día, usar rental_price como precio por día
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else if (rentalType === 'month') {
                // Para renta por mes, usar rental_price como precio por mes
                originalUnitPrice = parseFloat(this.currentRent.rental_price) || parseFloat(this.room.item.unit_price);
            } else {
                // Para tipo estándar, calcular precio por día del total
                const originalDuration = moment.duration(
                    moment(`${this.currentRent.output_date} ${this.currentRent.output_time}`).diff(
                        moment(`${this.currentRent.input_date} ${this.currentRent.input_time}`)
                    )
                );
                const originalDays = Math.max(1, Math.ceil(originalDuration.asDays()));
                originalUnitPrice = parseFloat(this.room.item.unit_price) / originalDays;
            }
            
            this.editDatesForm.originalPrice = parseFloat(this.room.item.total); // Precio total original
            this.editDatesForm.originalUnitPrice = originalUnitPrice; // Precio por unidad original
            this.editDatesForm.newPrice = null;
            this.editDatesForm.priceDifference = 0;
            
            // Calcular duración original
            this.editDatesForm.originalDuration = this.calculateDuration(
                this.currentRent.input_date, 
                this.currentRent.input_time,
                this.currentRent.output_date, 
                this.currentRent.output_time
            );
            
            this.editDatesForm.warning = '';
            
            this.showEditDatesModal = true;
        },
        calculateDuration(inputDate, inputTime, outputDate, outputTime) {
            const inputDateTime = moment(`${inputDate} ${inputTime || '14:00'}`);
            const outputDateTime = moment(`${outputDate} ${outputTime || '11:59'}`);
            
            const duration = moment.duration(outputDateTime.diff(inputDateTime));
            
            const rentalType = this.currentRent.rental_period_type;
            
            if (rentalType === 'hour') {
                const hours = Math.ceil(duration.asHours());
                return `${hours} hora${hours !== 1 ? 's' : ''}`;
            } else if (rentalType === 'day') {
                const days = Math.ceil(duration.asDays());
                return `${days} día${days !== 1 ? 's' : ''}`;
            } else if (rentalType === 'month') {
                const months = Math.ceil(duration.asMonths());
                return `${months} mes${months !== 1 ? 'es' : ''}`;
            } else {
                // Calcular en días y horas para tipo estándar
                const days = Math.floor(duration.asDays());
                const hours = Math.floor(duration.asHours() % 24);
                return `${days}d ${hours}h`;
            }
        },
        calculateNewPrice() {
            // Solo calcular si tenemos todas las fechas necesarias
            if (!this.editDatesForm.inputDate || !this.editDatesForm.outputDate) {
                this.editDatesForm.newPrice = null;
                this.editDatesForm.priceDifference = 0;
                this.editDatesForm.newDuration = '';
                return;
            }
            
            const inputDateTime = moment(`${this.editDatesForm.inputDate} ${this.editDatesForm.inputTime || '14:00'}`);
            const outputDateTime = moment(`${this.editDatesForm.outputDate} ${this.editDatesForm.outputTime || '11:59'}`);
            
            // Validar que la fecha de salida sea posterior a la de ingreso
            if (!inputDateTime.isBefore(outputDateTime)) {
                this.editDatesForm.warning = 'La fecha de salida debe ser posterior a la fecha de ingreso';
                this.editDatesForm.newPrice = null;
                this.editDatesForm.priceDifference = 0;
                this.editDatesForm.newDuration = '';
                return;
            }
            
            // Calcular nueva duración
            this.editDatesForm.newDuration = this.calculateDuration(
                this.editDatesForm.inputDate,
                this.editDatesForm.inputTime,
                this.editDatesForm.outputDate,
                this.editDatesForm.outputTime
            );
            
            // Calcular el nuevo precio basado en el tipo de renta
            const rentalType = this.currentRent.rental_period_type;
            const duration = moment.duration(outputDateTime.diff(inputDateTime));
            
            let newPrice = 0;
            const unitPrice = this.editDatesForm.originalUnitPrice; // Usar precio por unidad correcto
            
            if (rentalType === 'hour') {
                // Para renta por hora, usar precio por hora directamente
                const newHours = Math.ceil(duration.asHours());
                newPrice = unitPrice * newHours;
            } else if (rentalType === 'day') {
                // Para renta por día, usar precio por día directamente
                const newDays = Math.ceil(duration.asDays());
                newPrice = unitPrice * newDays;
            } else if (rentalType === 'month') {
                // Para renta por mes, usar precio por mes directamente
                const newMonths = Math.ceil(duration.asMonths());
                newPrice = unitPrice * newMonths;
            } else {
                // Para tipo estándar, usar precio por día
                const newDays = Math.ceil(duration.asDays());
                newPrice = unitPrice * newDays;
            }
            
            this.editDatesForm.newPrice = newPrice;
            this.editDatesForm.priceDifference = newPrice - this.editDatesForm.originalPrice;
            this.editDatesForm.warning = '';
        },
        async confirmDateEdit() {
            try {
                this.loadingDateEdit = true;
                
                const payload = {
                    edit_input: this.editDatesModal.editInput,
                    edit_output: this.editDatesModal.editOutput,
                    input_date: this.editDatesForm.inputDate,
                    input_time: this.editDatesForm.inputTime,
                    output_date: this.editDatesForm.outputDate,
                    output_time: this.editDatesForm.outputTime,
                    new_price: this.editDatesForm.newPrice,
                    price_difference: this.editDatesForm.priceDifference
                };
                
                const response = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/edit-dates`,
                    payload
                );
                
                if (response.data.success) {
                    this.$message.success('Fechas actualizadas correctamente');

                    // Cerrar el diálogo PRIMERO para que el flujo sea ágil
                    this.showEditDatesModal = false;

                    // Registrar el cambio en el historial
                    const changeType = this.editDatesModal.editInput ? 'DATE_EDIT' : 'DATE_EDIT';
                    const oldValues = {
                        input_date: this.currentRent.input_date,
                        input_time: this.currentRent.input_time,
                        output_date: this.currentRent.output_date,
                        output_time: this.currentRent.output_time
                    };
                    const newValues = {
                        input_date: this.editDatesForm.inputDate,
                        input_time: this.editDatesForm.inputTime,
                        output_date: this.editDatesForm.outputDate,
                        output_time: this.editDatesForm.outputTime
                    };

                    let notes = '';
                    if (this.editDatesModal.editInput) {
                        notes = 'Edición de fecha de ingreso';
                    } else if (this.editDatesModal.editOutput) {
                        notes = 'Edición de fecha de salida';
                    }

                    // Registrar el cambio SIN abrir el modal de historial
                    this.recordRoomChange(
                        changeType,
                        oldValues,
                        newValues,
                        notes,
                        this.editDatesForm.priceDifference
                    );

                    // Actualizar los datos del rent actual
                    this.currentRent = response.data.rent;

                    // Actualizar el precio del item si cambió
                    if (response.data.new_item_price) {
                        this.room.item.unit_price = response.data.new_item_price.unit_price;
                        this.room.item.total = response.data.new_item_price.total;
                        this.total = this.room.item.total;
                    }

                    // Recalcular totales
                    await this.onCalculateTotals();
                    this.onCalculatePaidAndDebts();

                    // Actualizar la duración y otros campos si es necesario
                    this.initForm();
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                console.error('Error al editar fechas:', error);
                if (error.response && error.response.data) {
                    this.$message.error(error.response.data.message || 'Error al editar las fechas');
                } else {
                    this.$message.error('Error al editar las fechas');
                }
            } finally {
                this.loadingDateEdit = false;
            }
        },
        getRentalTypeLabel(type) {
            const labels = {
                'hour': 'Por Hora',
                'day': 'Por Día',
                'month': 'Por Mes'
            };
            return labels[type] || 'Estándar';
        },
        getRentalTypeBadgeClass(type) {
            const classes = {
                'hour': 'badge-warning',
                'day': 'badge-info',
                'month': 'badge-success'
            };
            return classes[type] || 'badge-secondary';
        },
        // Métodos para pagos interactivos
        calculateChange() {
            const received = parseFloat(this.paymentForm.received) || 0;
            const totalDebt = parseFloat(this.totalDebt) || 0;
            
            // Calcular vuelto: dinero recibido - deuda total
            this.paymentForm.change = received - totalDebt;
            
            console.log('Cálculo de pago parcial:', {
                totalDebt: totalDebt,
                received: received,
                change: this.paymentForm.change,
                paymentAmount: Math.min(received, totalDebt)
            });
        },
        getChangeClass() {
            const change = parseFloat(this.paymentForm.change) || 0;
            if (change > 0) {
                return 'change-positive'; // Hay vuelto
            } else if (change < 0) {
                return 'change-negative'; // Falta dinero
            }
            return ''; // Pago exacto
        },
        getPaymentSummaryClass() {
            const change = parseFloat(this.paymentForm.change) || 0;
            if (change >= 0) {
                return 'payment-success'; // Pago completo o con vuelto
            } else {
                return 'payment-warning'; // Falta dinero
            }
        },
        getPaymentMessage() {
            const change = parseFloat(this.paymentForm.change) || 0;
            if (change > 0) {
                return '¡Pago Correcto!';
            } else if (change < 0) {
                return 'Falta dinero';
            }
            return 'Pago exacto';
        },
        getPaymentSubMessage() {
            const change = parseFloat(this.paymentForm.change) || 0;
            if (change > 0) {
                return `Debe dar S/ ${Math.abs(change).toFixed(2)} de vuelto`;
            } else if (change < 0) {
                return `Faltan S/ ${Math.abs(change).toFixed(2)} para completar el pago`;
            }
            return 'Monto exacto, no hay vuelto';
        },
        async savePayment() {
            console.log('=== INICIANDO savePayment ===');
            try {
                this.loadingPayment = true;
                
                // Usar el monto recibido completo, no limitarlo a la deuda
                const receivedAmount = parseFloat(this.paymentForm.received) || 0;
                const debtAmount = parseFloat(this.totalDebt) || 0;
                
                // Determinar si es edición o nuevo pago
                const isEditing = this.paymentForm.payment_index !== null;
                
                const payload = {
                    hotel_rent_id: this.currentRent.id,
                    amount: receivedAmount, // Usar el monto completo recibido
                    method: this.paymentForm.method,
                    reference: this.paymentForm.reference,
                    received: this.paymentForm.received,
                    change: this.paymentForm.change
                };
                
                // Si es edición, agregar el ID del pago
                if (isEditing) {
                    const payment = this.savedPayments[this.paymentForm.payment_index];
                    payload.payment_id = payment.id;
                    console.log('=== EDITANDO PAGO ID:', payment.id);
                } else {
                    console.log('=== CREANDO NUEVO PAGO ===');
                }
                
                const response = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/rent/save-payment`,
                    payload
                );
                
                if (response.data.success) {
                    this.$message.success(isEditing ? 'Pago actualizado correctamente' : 'Pago guardado correctamente');
                    
                    console.log('=== LLAMANDO A loadRentData ===');
                    // Recargar los datos desde el servidor para asegurar persistencia
                    await this.loadRentData();
                    console.log('=== loadRentData COMPLETADO ===');
                    
                    // Limpiar formulario
                    this.clearPaymentForm();
                    
                    // Cerrar el modal de pagos
                    this.showPaymentModal = false;
                    
                    // Recalcular totales
                    this.onCalculatePaidAndDebts();
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                console.error('Error al guardar pago:', error);
                this.$message.error('Error al guardar el pago');
            } finally {
                this.loadingPayment = false;
            }
        },
        clearPaymentForm() {
            this.paymentForm = {
                received: 0,
                change: 0,
                method: 'cash',
                reference: '',
                payment_index: null // Limpiar índice de edición
            };
        },
        processChange() {
            const changeAmount = this.paymentForm.change;
            const receivedAmount = this.paymentForm.received;
            
            this.$confirm(
                `¿Confirmar que se ha entregado S/ ${changeAmount.toFixed(2)} de vuelto al cliente?`,
                'Procesar Vuelto',
                {
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }
            ).then(() => {
                // Mostrar mensaje de éxito
                this.$message.success(`Vuelto de S/ ${changeAmount.toFixed(2)} procesado correctamente`);
                
                // Limpiar el formulario después de procesar el vuelto
                this.clearPaymentForm();
                
                // Cerrar el modal de pago
                this.showPaymentModal = false;
            }).catch(() => {
                // El usuario canceló, no hacer nada
            });
        },
        async processRefund() {
            const refundAmount = Math.abs(this.totalDebt); // Convertir a positivo
            
            this.$confirm(
                `¿Procesar devolución de S/ ${refundAmount.toFixed(2)} al cliente? Esto generará un pago negativo.`,
                'Procesar Devolución',
                {
                    confirmButtonText: 'Sí, procesar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }
            ).then(async () => {
                try {
                    this.loadingRefund = true;
                    
                    const payload = {
                        hotel_rent_id: this.currentRent.id,
                        amount: -refundAmount, // Pago negativo
                        method: 'cash',
                        reference: 'DEVOLUCION/VOUELTO',
                        received: 0,
                        change: 0
                    };
                    
                    const response = await this.$http.post(
                        `/hotels/reception/${this.currentRent.id}/rent/save-payment`,
                        payload
                    );
                    
                    if (response.data.success) {
                        this.$message.success('Devolución procesada correctamente');
                        
                        // Recargar los datos
                        await this.loadRentData();
                        
                        console.log('Devolución procesada:', response.data);
                    } else {
                        this.$message.error(response.data.message);
                    }
                } catch (error) {
                    console.error('Error al procesar devolución:', error);
                    this.$message.error('Error al procesar la devolución');
                } finally {
                    this.loadingRefund = false;
                }
            }).catch(() => {
                // El usuario canceló, no hacer nada
            });
        },
        async reversePayment(index) {
            try {
                this.loadingReverse = true;
                
                const payment = this.savedPayments[index];
                
                const response = await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/reverse-payment`,
                    { payment_id: payment.id }
                );
                
                if (response.data.success) {
                    this.$message.success('Pago revertido correctamente');
                    
                    // Eliminar de la lista
                    this.savedPayments.splice(index, 1);
                    
                    // Recalcular totales
                    this.onCalculatePaidAndDebts();
                } else {
                    this.$message.error(response.data.message);
                }
            } catch (error) {
                console.error('Error al revertir pago:', error);
                this.$message.error('Error al revertir el pago');
            } finally {
                this.loadingReverse = false;
            }
        },
        async editPayment(payment, index) {
            try {
                // Cargar los datos del pago en el formulario de pago
                this.paymentForm.amount = parseFloat(payment.amount);
                this.paymentForm.received = parseFloat(payment.amount); // Cargar también el campo received
                this.paymentForm.method = payment.method;
                this.paymentForm.reference = payment.reference || '';
                this.paymentForm.payment_index = index; // Guardar el índice para referencia
                
                // Calcular el vuelto (si existe)
                this.paymentForm.change = parseFloat(payment.change) || 0;
                
                // Abrir el modal de pagos
                this.showPaymentModal = true;
                
                this.$message.info('Modo de edición: Puede modificar los datos del pago y guardar los cambios');
                
                console.log('Datos de pago cargados para edición:', {
                    amount: this.paymentForm.amount,
                    received: this.paymentForm.received,
                    method: this.paymentForm.method,
                    reference: this.paymentForm.reference,
                    change: this.paymentForm.change
                });
                
            } catch (error) {
                console.error('Error al cargar pago para edición:', error);
                this.$message.error('Error al cargar el pago para edición');
            }
        },
        showCustomerObservations() {
            // Cargar las observaciones actuales del cliente
            console.log('customer prop:', this.customer);
            console.log('observation:', this.customer.observation);
            this.customerObservationsForm.observations = this.customer.observation || '';
            this.showCustomerObservationsModal = true;
        },
        async saveCustomerObservations() {
            try {
                this.loadingCustomerObservations = true;
                
                const response = await this.$http.post(
                    `/persons/${this.currentRent.customer_id}/observations`,
                    {
                        observations: this.customerObservationsForm.observations
                    }
                );
                
                if (response.data.success) {
                    this.$message.success('Observaciones del cliente actualizadas correctamente');
                    
                    // Actualizar las observaciones en el rent actual
                    this.customer.observation = this.customerObservationsForm.observations;
                    this.currentRent.customer.observation = this.customerObservationsForm.observations;
                    
                    // Cerrar el modal
                    this.showCustomerObservationsModal = false;
                } else {
                    this.$message.error(response.data.message || 'Error al actualizar observaciones');
                }
            } catch (error) {
                console.error('Error al guardar observaciones del cliente:', error);
                this.$message.error('Error al actualizar las observaciones del cliente');
            } finally {
                this.loadingCustomerObservations = false;
            }
        },
        getMethodLabel(method) {
            const labels = {
                'cash': 'Efectivo',
                'credit_card': 'Tarjeta de Crédito',
                'debit_card': 'Tarjeta de Débito',
                'transfer': 'Transferencia',
                'yape_plin': 'Yape/Plin'
            };
            return labels[method] || method;
        },
        getPaymentMethodTagType(method) {
            const types = {
                'cash': 'success',
                'credit_card': 'primary',
                'debit_card': 'info',
                'transfer': 'warning',
                'yape_plin': 'danger'
            };
            return types[method] || 'info';
        },
        formatDateTime(dateTime, time = null) {
            if (time) {
                return moment(`${dateTime} ${time}`).format('DD/MM/YYYY HH:mm');
            }
            return moment(dateTime).format('DD/MM/YYYY HH:mm');
        },
        formatDate(date) {
            return moment(date).format('DD/MM/YYYY');
        },
        // Métodos para historial de cambios
        async showRoomHistory() {
            try {
                this.loadingRoomHistory = true;
                this.showRoomHistoryModal = true;
                
                const response = await this.$http.get(
                    `/hotels/reception/${this.currentRent.id}/room-history`
                );
                
                if (response.data.success) {
                    this.roomHistory = response.data.history || [];
                } else {
                    this.$message.error(response.data.message || 'Error al cargar el historial');
                    this.roomHistory = [];
                }
            } catch (error) {
                console.error('Error al cargar historial:', error);
                this.$message.error('Error al cargar el historial de cambios');
                this.roomHistory = [];
            } finally {
                this.loadingRoomHistory = false;
            }
        },
        async recordRoomChange(changeType, oldValues, newValues, notes = null, priceDifference = 0) {
            try {
                const payload = {
                    change_type: changeType,
                    old_values: oldValues,
                    new_values: newValues,
                    notes: notes,
                    price_difference: priceDifference
                };

                await this.$http.post(
                    `/hotels/reception/${this.currentRent.id}/record-change`,
                    payload
                );

                // Solo refrescar el array de historial en background; NO abrir
                // el modal automáticamente (eso bloqueaba el flujo después de
                // editar fechas o extender la estadía).
                try {
                    const response = await this.$http.get(
                        `/hotels/reception/${this.currentRent.id}/room-history`
                    );
                    if (response.data && response.data.success) {
                        this.roomHistory = response.data.history || [];
                    }
                } catch (e) {
                    // si falla la recarga de historial, no es crítico
                }
            } catch (error) {
                console.error('Error al registrar cambio:', error);
                this.$message.error('Error al registrar el cambio');
            }
        },
        getChangeTypeLabel(changeType) {
            const labels = {
                'CHECKIN': 'Check-In',
                'CHECKOUT': 'Check-Out',
                'EXTENSION': 'Extensión',
                'DATE_EDIT': 'Edición de Fechas',
                'ROOM_CHANGE': 'Cambio de Habitación',
                'PRICE_CHANGE': 'Cambio de Precio'
            };
            return labels[changeType] || changeType;
        },
        getChangeTypeClass(changeType) {
            const classes = {
                'CHECKIN': 'timeline-checkin',
                'CHECKOUT': 'timeline-checkout',
                'EXTENSION': 'timeline-extension',
                'DATE_EDIT': 'timeline-date-edit',
                'ROOM_CHANGE': 'timeline-room-change',
                'PRICE_CHANGE': 'timeline-price-change'
            };
            return classes[changeType] || 'timeline-default';
        },
        getChangeTypeIcon(changeType) {
            const icons = {
                'CHECKIN': 'fa fa-sign-in-alt',
                'CHECKOUT': 'fa fa-sign-out-alt',
                'EXTENSION': 'fa fa-calendar-plus',
                'DATE_EDIT': 'fa fa-calendar-alt',
                'ROOM_CHANGE': 'fa fa-exchange-alt',
                'PRICE_CHANGE': 'fa fa-dollar-sign'
            };
            return icons[changeType] || 'fa fa-info-circle';
        },
        getStatusLabel(status) {
            const labels = {
                'OCUPADO': 'Ocupado',
                'RESERVADO': 'Reservado',
                'LIMPIEZA': 'En Limpieza',
                'MANTENIMIENTO': 'Mantenimiento',
                'DISPONIBLE': 'Disponible',
                'FINALIZADO': 'Finalizado'
            };
            return labels[status] || status;
        },
        getStatusBadgeClass(status) {
            const classes = {
                'OCUPADO': 'badge-success',
                'RESERVADO': 'badge-warning',
                'LIMPIEZA': 'badge-info',
                'MANTENIMIENTO': 'badge-secondary',
                'DISPONIBLE': 'badge-primary',
                'FINALIZADO': 'badge-dark'
            };
            return classes[status] || 'badge-secondary';
        },
    },
};
</script>

<style scoped>
.info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 8px;
    border-left: 3px solid #409eff;
}
td{
    text-align: center;
}

.info-item svg {
    flex-shrink: 0;
    color: #409eff;
}

.info-label {
    font-weight: 500;
    color: #666;
    min-width: 100px;
}

.info-value {
    font-weight: 600;
    color: #333;
}

.btn-extend-stay {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: #ff9800;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-extend-stay:hover {
    background: #f57c00;
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-extend-stay:active {
    transform: scale(0.95);
}

.btn-extend-stay svg {
    width: 16px;
    height: 16px;
}

/* Estilos para botones de edición de fechas */
.btn-edit-date {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: #409eff;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-edit-date:hover {
    background: #337ecc;
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-edit-date:active {
    transform: scale(0.95);
}

.btn-edit-date svg {
    width: 14px;
    height: 14px;
}

/* Contenedor para botones de checkout */
.checkout-buttons {
    position: absolute;
    top: 8px;
    right: 8px;
    display: flex;
    gap: 5px;
    z-index: 10;
}

.checkout-buttons .btn-extend-stay {
    position: static;
    width: 28px;
    height: 28px;
}

.checkout-buttons .btn-edit-date {
    position: static;
}

.dialog-footer {
    padding: 15px 0 0 0;
    border-top: 1px solid #e4e7ed;
    margin-top: 20px;
}

.dialog-footer .el-button {
    margin-left: 10px;
}

.dialog-footer .el-button:first-child {
    margin-left: 0;
}

/* Estilos para pagos interactivos */
.payment-input .el-input__inner {
    text-align: center;
    font-weight: bold;
}

.change-positive .el-input__inner {
    color: #67c23a;
    background-color: #f0f9ff;
}

.change-negative .el-input__inner {
    color: #f56c6c;
    background-color: #fef0f0;
}

.payment-summary {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border-left: 4px solid;
}

.payment-success {
    background-color: #f0f9ff;
    border-left-color: #67c23a;
    color: #67c23a;
}

.payment-warning {
    background-color: #fef0f0;
    border-left-color: #f56c6c;
    color: #f56c6c;
}

.payment-info {
    background-color: #f4f4f5;
    border-left-color: #909399;
    color: #909399;
}

.payment-icon {
    margin-right: 15px;
    flex-shrink: 0;
}

.payment-text h5 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.payment-text p {
    margin: 0;
    font-size: 14px;
    opacity: 0.8;
}

.payment-summary.payment-success .payment-icon svg {
    color: #67c23a;
}

.payment-summary.payment-warning .payment-icon svg {
    color: #f56c6c;
}

.payment-summary.payment-info .payment-icon svg {
    color: #909399;
}

.table-responsive {
    max-height: 300px;
    overflow-y: auto;
}

.table-responsive table {
    margin-bottom: 0;
}

.fw-bold {
    font-weight: 600 !important;
}

/* Ocultar tabla de registro de pagos pendientes */
.payment-section-hidden {
    display: none !important;
}

/* Estilos para el historial de cambios de habitación */
.room-history-content {
    padding: 20px;
}

.room-history-header {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box {
    text-align: center;
    padding: 15px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-box h6 {
    margin: 0 0 10px 0;
    color: #409eff;
}

.info-box p {
    margin: 0;
    font-weight: 600;
}

.room-history-timeline {
    max-height: 500px;
    overflow-y: auto;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e4e7ed;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: white;
}

.timeline-checkin {
    background: #67c23a;
}

.timeline-checkout {
    background: #f56c6c;
}

.timeline-extension {
    background: #e6a23c;
}

.timeline-date-edit {
    background: #409eff;
}

.timeline-room-change {
    background: #909399;
}

.timeline-price-change {
    background: #f39c12;
}

.timeline-default {
    background: #909399;
}

.timeline-content {
    margin-left: 20px;
    background: white;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 3px solid #e4e7ed;
}

.change-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 10px;
}

.change-type {
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    text-transform: uppercase;
}

.change-date {
    color: #666;
    font-size: 12px;
}

.change-user {
    color: #409eff;
    font-size: 12px;
}

.change-details {
    margin-top: 10px;
}

.change-detail {
    margin-bottom: 8px;
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
    font-size: 14px;
}

.price-change {
    margin-top: 8px;
    padding: 8px;
    border-radius: 4px;
    font-weight: 600;
}

.change-notes {
    margin-top: 8px;
    padding: 8px;
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    border-radius: 4px;
    font-style: italic;
}

/* Estilos para el modal de pagos */
.payment-modal .el-dialog__body {
    padding: 20px;
}

.payment-modal .payment-input {
    font-size: 16px;
}

.payment-modal .payment-summary {
    margin: 20px 0;
    padding: 20px;
    border-radius: 8px;
}

.payment-modal .alert {
    margin-bottom: 20px;
    font-size: 16px;
}
</style>
