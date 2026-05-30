<template>
    <div class="rcal">
        <!-- Header -->
        <div class="page-header pe-0">
            <h2>
                <a href="/hotels">
                    <svg xmlns="http://www.w3.org/2000/svg" style="margin-top:-5px" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M5 21v-14l8 -4v18"/><path d="M19 21v-10l-6 -4"/><path d="M9 9l0 .01"/><path d="M9 12l0 .01"/><path d="M9 15l0 .01"/><path d="M9 18l0 .01"/></svg>
                </a>
                Calendario de Reservas
            </h2>
            <ol class="breadcrumbs">
                <li><span><a href="/hotels">Hoteles</a></span></li>
                <li class="active"><span>Calendario</span></li>
            </ol>
        </div>

        <!-- Toolbar -->
        <div class="rcal-toolbar">
            <div class="rcal-tb-left">
                <!-- View toggles -->
                <div class="rcal-view-btns">
                    <button class="rcal-vbtn" :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'" title="Vista lista">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                    <button class="rcal-vbtn" :class="{ active: viewMode === 'calendar' }" @click="viewMode = 'calendar'" title="Vista calendario">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </button>
                    <button class="rcal-vbtn" title="Buscar" @click="toggleSearch">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </button>
                    <button class="rcal-vbtn rcal-vbtn-add" title="Nueva reserva" @click="newReservation">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                </div>

                <!-- Navigation -->
                <div class="rcal-nav">
                    <button class="rcal-nav-btn" @click="navigatePrev" title="Anterior">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="rcal-nav-btn rcal-nav-today" @click="goToday">HOY</button>
                    <button class="rcal-nav-btn" @click="navigateNext" title="Siguiente">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            <div class="rcal-tb-center">
                <span class="rcal-month-title">{{ monthTitle }}</span>
            </div>

            <div class="rcal-tb-right">
                <!-- Search box -->
                <div v-if="searchOpen" class="rcal-search-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input v-model="searchQuery" placeholder="Buscar reservas, huéspedes y más" class="rcal-search-input" ref="searchInput" @keyup.esc="searchOpen = false"/>
                </div>
                <!-- Category filter -->
                <select v-model="selectedCategory" @change="applyFilters" class="rcal-select">
                    <option :value="null">Todos los tipos de habitación</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.description }}</option>
                </select>
                <span class="rcal-tb-label">Calendario</span>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="rcal-container" v-loading="loading">
            <div class="rcal-scroll" ref="calScroll">
                <div class="rcal-grid" :style="{ minWidth: (roomColWidth + visibleDays * dayCellWidth) + 'px' }">

                    <!-- === GLOBAL HEADER: Occupancy % row === -->
                    <div class="rcal-row rcal-row-header">
                        <div class="rcal-room-cell rcal-corner"></div>
                        <div class="rcal-days-row">
                            <div class="rcal-day-header" v-for="day in days" :key="'gh'+day.dateStr"
                                 :class="{ 'dh-today': day.isToday, 'dh-weekend': day.isWeekend }"
                                 :style="{ width: dayCellWidth + 'px' }">
                                <div class="rcal-dh-avail">
                                    <span class="rcal-dh-avail-num">{{ getDayAvailability(day).available }}</span>
                                </div>
                                <div class="rcal-dh-date">
                                    <span class="rcal-dh-dayname">{{ day.shortName }} {{ day.dayNumber }}</span>
                                </div>
                                <div class="rcal-dh-pct" :class="getPctClass(getDayAvailability(day).pct)">
                                    {{ getDayAvailability(day).pct.toFixed(2) }}%
                                </div>
                                <div class="rcal-dh-sales" :class="{ 'dh-sales-today': day.isToday }">
                                    <small>Ventas: S/ {{ formatPrice(getDaySalesTotal(day)) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- === CATEGORY GROUPS === -->
                    <template v-for="group in roomGroups">
                        <!-- Category header row -->
                        <div class="rcal-row rcal-row-category" :key="'cat'+group.id" @click="toggleCategory(group.id)">
                            <div class="rcal-room-cell rcal-cat-label">
                                <svg :class="{ 'rcal-chevron-open': !collapsedCategories[group.id] }" class="rcal-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                <span>{{ group.name }}</span>
                            </div>
                            <div class="rcal-days-row">
                                <div class="rcal-day-cat-cell" v-for="day in days" :key="'cc'+group.id+day.dateStr"
                                     :class="{ 'dcc-today': day.isToday }"
                                     :style="{ width: dayCellWidth + 'px' }">
                                    <div class="rcal-cat-stats">
                                        <span class="rcal-cat-avail">{{ getCategoryDayStats(group, day).available }}</span>
                                        <span class="rcal-cat-price" :class="{ 'price-highlight': day.isToday }">
                                            S/ {{ formatPrice(getCategoryDayStats(group, day).price) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Room rows -->
                        <template v-if="!collapsedCategories[group.id]">
                            <div class="rcal-row rcal-row-room" v-for="room in group.rooms" :key="'rm'+room.id">
                                <!-- Room label -->
                                <div class="rcal-room-cell rcal-room-label">
                                    <span class="rcal-room-name">{{ room.name }}</span>
                                    <span class="rcal-room-icons">
                                        <span v-if="getRoomStatusIcon(room.status)" class="rcal-room-icon" :class="'ricon-'+room.status.toLowerCase()" :title="getRoomStatusLabel(room.status)">{{ getRoomStatusIcon(room.status) }}</span>
                                    </span>
                                </div>

                                <!-- Day cells + bars -->
                                <div class="rcal-days-row rcal-days-body" :data-room="room.id">
                                    <!-- Background day cells -->
                                    <div class="rcal-day-cell" v-for="day in days" :key="'dc'+room.id+day.dateStr"
                                         :class="{ 'dc-today': day.isToday, 'dc-weekend': day.isWeekend }"
                                         :style="{ width: dayCellWidth + 'px' }"
                                         @click="onCellClick(room, day)">
                                    </div>

                                    <!-- Reservation bars -->
                                    <div v-for="bar in getRoomBars(room.id)" :key="'bar'+bar.id+'-'+bar.start_date"
                                         class="rcal-bar"
                                         :class="getBarClass(bar)"
                                         :style="bar.style"
                                         @click.stop="openDetails(bar)"
                                         :title="bar.customer_name + ' — ' + bar.duration + ' noches'">
                                        <span class="rcal-bar-text">
                                            <template v-if="bar.isMultiGuest">+ </template>{{ bar.customer_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Empty -->
                    <div v-if="!roomGroups.length && !loading" class="rcal-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l18 0"/><path d="M5 21v-14l8 -4v18"/><path d="M19 21v-10l-6 -4"/></svg>
                        <span>No hay habitaciones para mostrar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="rcal-legend">
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-info"></i>Información</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-connected"></i>Conectado</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-confirmed"></i>Confirmada</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-pending"></i>Pendiente</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-checkin"></i>Check-in</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-active"></i>Activa</span>
            <span class="rcal-lg-item"><i class="rcal-lg-dot lg-checkout"></i>Check-out</span>
        </div>

        <!-- Detail / Edit Modal -->
        <el-dialog :visible.sync="showDetail" width="640px" custom-class="rcal-modal" :show-close="true" append-to-body @close="cancelEdit">
            <template slot="title">
                <div class="rcal-modal-title">
                    <span>{{ editing ? 'Editar Reserva' : 'Reserva' }}</span>
                    <span v-if="detail" class="rcal-modal-badge" :class="'mbadge-' + (detail.status || 'default')">{{ getStatusLabel(detail.status) }}</span>
                </div>
            </template>

            <div v-loading="loadingDetail">
                <!-- VIEW MODE -->
                <div v-if="!editing && detail" class="rcal-detail">
                    <div class="rcal-det-section">
                        <div class="rcal-det-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Cliente
                        </div>
                        <div class="rcal-det-grid">
                            <div class="rcal-det-field"><label>Nombre</label><span>{{ detail.customer?.name || detail.customer_name || '—' }}</span></div>
                            <div class="rcal-det-field"><label>Documento</label><span>{{ detail.customer?.number || detail.customer_number || '—' }}</span></div>
                            <div class="rcal-det-field"><label>Teléfono</label><span>{{ detail.customer?.telephone || detail.customer_telephone || '—' }}</span></div>
                            <div class="rcal-det-field"><label>Dirección</label><span>{{ detail.customer?.address || detail.customer_address || '—' }}</span></div>
                        </div>
                    </div>
                    <div class="rcal-det-section">
                        <div class="rcal-det-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Estadía
                        </div>
                        <div class="rcal-det-grid">
                            <div class="rcal-det-field"><label>Habitación</label><span>{{ detail.room?.name || detail.room_name }}</span></div>
                            <div class="rcal-det-field"><label>Categoría</label><span>{{ detail.room?.category || detail.room_category }}</span></div>
                            <div class="rcal-det-field"><label>Tarifa</label><span>{{ detail.rate?.rate_description || '—' }} <small v-if="detail.rate?.rental_price">· S/ {{ Number(detail.rate.rental_price).toFixed(2) }}</small></span></div>
                            <div class="rcal-det-field"><label>Check-in</label><span>{{ detail.dates?.input_date || detail.start_date }} {{ (detail.dates?.input_time || detail.input_time || '').slice(0,5) }}</span></div>
                            <div class="rcal-det-field"><label>Check-out</label><span>{{ detail.dates?.output_date || detail.end_date }} {{ (detail.dates?.output_time || detail.output_time || '').slice(0,5) }}</span></div>
                            <div class="rcal-det-field"><label>Noches</label><span>{{ detail.dates?.duration || detail.duration }}</span></div>
                            <div class="rcal-det-field"><label>Adultos / Niños</label><span>{{ detail.adults ?? '—' }} / {{ detail.children ?? '—' }}</span></div>
                            <div class="rcal-det-field"><label>Toallas</label><span>{{ detail.towels ?? '—' }}</span></div>
                            <div class="rcal-det-field"><label>Placa</label><span>{{ detail.license_plate || '—' }}</span></div>
                            <div class="rcal-det-field"><label>Motivo</label><span>{{ getTravelReasonLabel(detail.travel_reason) }}</span></div>
                            <div class="rcal-det-field rcal-det-price"><label>Total</label><span>S/ {{ Number(detail.totals?.total ?? detail.total ?? 0).toFixed(2) }}</span></div>
                        </div>
                    </div>
                    <div class="rcal-det-section" v-if="detail.notes">
                        <div class="rcal-det-title">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Notas
                        </div>
                        <p class="rcal-det-notes">{{ detail.notes }}</p>
                    </div>
                </div>

                <!-- EDIT MODE -->
                <div v-else-if="editing" class="rcal-edit">
                    <div class="rcal-det-section">
                        <div class="rcal-det-title">Cliente</div>
                        <div class="rcal-edit-grid">
                            <el-form-item label="Nombre" required class="rcal-edit-field">
                                <el-input v-model="editForm.customer.name" size="small" placeholder="Nombre del cliente" />
                            </el-form-item>
                            <el-form-item label="Documento" class="rcal-edit-field">
                                <el-input v-model="editForm.customer.number" size="small" />
                            </el-form-item>
                            <el-form-item label="Teléfono" class="rcal-edit-field">
                                <el-input v-model="editForm.customer.telephone" size="small" />
                            </el-form-item>
                            <el-form-item label="Email" class="rcal-edit-field">
                                <el-input v-model="editForm.customer.email" size="small" />
                            </el-form-item>
                            <el-form-item label="Dirección" class="rcal-edit-field rcal-edit-field-wide">
                                <el-input v-model="editForm.customer.address" size="small" />
                            </el-form-item>
                        </div>
                    </div>
                    <div class="rcal-det-section">
                        <div class="rcal-det-title">Estadía</div>
                        <div class="rcal-edit-grid">
                            <el-form-item label="Habitación" required class="rcal-edit-field">
                                <el-select v-model="editForm.hotel_room_id" size="small" filterable @change="onEditRoomChange" style="width:100%">
                                    <el-option v-for="r in rooms" :key="r.id" :value="r.id" :label="r.name"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="Tarifa" required class="rcal-edit-field">
                                <el-select v-model="editForm.hotel_rate_id" size="small" @change="onEditRateChange" :disabled="!editForm.hotel_room_id" style="width:100%">
                                    <el-option v-for="rt in editForm._availableRates" :key="rt.hotel_rate_id" :value="rt.hotel_rate_id"
                                               :label="`${rt.rate_description} — S/ ${Number(rt.price).toFixed(2)}`"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="Check-in" required class="rcal-edit-field">
                                <el-date-picker v-model="editForm.input_date" type="date" size="small" value-format="yyyy-MM-dd" placeholder="Fecha" style="width:100%" @change="recomputeDuration"></el-date-picker>
                            </el-form-item>
                            <el-form-item label="Hora ingreso" class="rcal-edit-field">
                                <el-time-select v-model="editForm.input_time" size="small" :picker-options="{ start: '00:00', step: '00:15', end: '23:45' }" placeholder="--:--" style="width:100%"></el-time-select>
                            </el-form-item>
                            <el-form-item label="Check-out" required class="rcal-edit-field">
                                <el-date-picker v-model="editForm.output_date" type="date" size="small" value-format="yyyy-MM-dd" placeholder="Fecha" style="width:100%" @change="recomputeDuration"></el-date-picker>
                            </el-form-item>
                            <el-form-item label="Hora salida" class="rcal-edit-field">
                                <el-time-select v-model="editForm.output_time" size="small" :picker-options="{ start: '00:00', step: '00:15', end: '23:45' }" placeholder="--:--" style="width:100%"></el-time-select>
                            </el-form-item>
                            <el-form-item label="Noches" class="rcal-edit-field">
                                <el-input-number v-model="editForm.duration" :min="1" size="small" controls-position="right" style="width:100%"></el-input-number>
                            </el-form-item>
                            <el-form-item label="Adultos" class="rcal-edit-field">
                                <el-input-number v-model="editForm.adults" :min="0" size="small" controls-position="right" style="width:100%"></el-input-number>
                            </el-form-item>
                            <el-form-item label="Niños" class="rcal-edit-field">
                                <el-input-number v-model="editForm.children" :min="0" size="small" controls-position="right" style="width:100%"></el-input-number>
                            </el-form-item>
                            <el-form-item label="Toallas" class="rcal-edit-field">
                                <el-input-number v-model="editForm.towels" :min="0" size="small" controls-position="right" style="width:100%"></el-input-number>
                            </el-form-item>
                            <el-form-item label="Placa" class="rcal-edit-field">
                                <el-input v-model="editForm.license_plate" size="small" placeholder="—"></el-input>
                            </el-form-item>
                            <el-form-item label="Motivo del viaje" class="rcal-edit-field">
                                <el-select v-model="editForm.travel_reason" size="small" clearable style="width:100%">
                                    <el-option v-for="r in travelReasons" :key="r.value" :value="r.value" :label="r.label"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="Notas" class="rcal-edit-field rcal-edit-field-wide">
                                <el-input v-model="editForm.notes" type="textarea" :rows="2" size="small" placeholder="Observaciones..."></el-input>
                            </el-form-item>
                        </div>
                    </div>
                </div>
            </div>

            <div slot="footer" class="rcal-modal-footer">
                <template v-if="!editing">
                    <el-button size="small" @click="showDetail = false">Cerrar</el-button>
                    <el-button size="small" type="danger"
                               v-if="isEditableStatus(detail && detail.status)"
                               @click="deleteReservation(detail)"
                               :loading="deleting">
                        Eliminar
                    </el-button>
                    <el-button size="small"
                               v-if="isEditableStatus(detail && detail.status)"
                               @click="goToEditMode(detail)">
                        Editar
                    </el-button>
                    <el-button size="small" type="primary"
                               v-if="isEditableStatus(detail && detail.status)"
                               @click="goToEdit(detail)">
                        Gestionar
                    </el-button>
                </template>
                <template v-else>
                    <el-button size="small" @click="cancelEdit">Cancelar</el-button>
                    <el-button size="small" type="primary" :loading="saving" @click="saveEdit">Guardar cambios</el-button>
                </template>
            </div>
        </el-dialog>

        <!-- Room Selection Dialog -->
        <el-dialog :visible.sync="showReservationPopup" width="450px" custom-class="rcal-modal rcal-modal-compact" :show-close="true" append-to-body>
            <template slot="title">
                <div class="rcal-modal-title">
                    <span>Seleccionar Habitación</span>
                </div>
            </template>
            <div class="room-selection-grid">
                <div v-for="room in filteredRooms" :key="room.id" class="room-card room-card-compact" @click="selectRoomForReservation(room)">
                    <div class="room-card-header">
                        <h4>{{ room.name }}</h4>
                        <span class="room-category">{{ room.category?.description || 'Sin categoría' }}</span>
                    </div>
                    <div class="room-card-body">
                        <p class="room-status" :class="'status-' + room.status.toLowerCase()">
                            {{ room.status === 'DISPONIBLE' ? 'Disponible' : room.status }}
                        </p>
                        <p class="room-price" v-if="room.category?.price">
                            S/ {{ parseFloat(room.category.price).toFixed(2) }}
                        </p>
                    </div>
                </div>
            </div>
            <div slot="footer" class="rcal-modal-footer">
                <el-button size="small" @click="closeReservationPopup">Cancelar</el-button>
            </div>
        </el-dialog>

        <!-- Rent Iframe Dialog -->
        <el-dialog 
            :visible.sync="showRentIframe" 
            width="95%" 
            height="90%" 
            custom-class="rcal-modal rcal-modal-fullscreen" 
            :show-close="true" 
            append-to-body
            @close="closeRentIframe"
        >
            <template slot="title">
                <div class="rcal-modal-title">
                    <span>Nueva Reserva - {{ selectedRoom?.name }}</span>
                </div>
            </template>
            <div v-if="selectedRoom" class="iframe-container">
                <iframe 
                    :src="getReservationUrl(selectedRoom)"
                    style="width: 100%; height: 80vh; border: none; border-radius: 8px;"
                    @load="onIframeLoad"
                ></iframe>
            </div>
            <div slot="footer" class="rcal-modal-footer">
                <el-button size="small" @click="closeRentIframe">Cerrar</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            // View
            viewMode: 'calendar',
            visibleDays: 16,
            dayCellWidth: 90,
            roomColWidth: 160,
            startDate: null,
            days: [],
            // Data
            rooms: [],
            filteredRooms: [],
            categories: [],
            reservations: [],
            roomRates: {},
            dailySalesTotals: {},
            categoryDailySales: {},
            // Filters
            selectedCategory: null,
            searchOpen: false,
            searchQuery: '',
            collapsedCategories: {},
            // Detail
            loading: false,
            showDetail: false,
            detail: null,
            deleting: false,
            loadingDetail: false,
            editing: false,
            saving: false,
            editForm: this.emptyEditForm(),
            // New reservation popup
            showReservationPopup: false,
            selectedRoom: null,
            showRentIframe: false,
            // Color assignment
            barColorIndex: {},
            // Catálogos
            travelReasons: [
                { value: 'visita',   label: 'Visita' },
                { value: 'trabajo',  label: 'Trabajo' },
                { value: 'estudio',  label: 'Estudio' },
                { value: 'religion', label: 'Religión' },
                { value: 'salud',    label: 'Salud' },
                { value: 'compras',  label: 'Compras' },
                { value: 'otros',    label: 'Otros' },
            ],
        }
    },
    computed: {
        monthTitle() {
            if (!this.days.length) return ''
            const first = this.days[0].date
            const last = this.days[this.days.length - 1].date
            const mOpts = { month: 'long', year: 'numeric' }
            if (first.getMonth() === last.getMonth()) {
                let t = first.toLocaleDateString('es-ES', mOpts)
                return t.charAt(0).toUpperCase() + t.slice(1)
            }
            const m1 = first.toLocaleDateString('es-ES', { month: 'long' })
            const m2 = last.toLocaleDateString('es-ES', mOpts)
            return (m1.charAt(0).toUpperCase() + m1.slice(1)) + ' — ' + (m2.charAt(0).toUpperCase() + m2.slice(1))
        },
        roomGroups() {
            const groups = []
            const catMap = {}
            const rooms = this.searchQuery
                ? this.filteredRooms.filter(r => {
                    const q = this.searchQuery.toLowerCase()
                    if (r.name.toLowerCase().includes(q)) return true
                    // Check if any reservation in this room matches the search
                    return this.reservations.some(res =>
                        res.hotel_room_id == r.id && res.customer_name.toLowerCase().includes(q)
                    )
                })
                : this.filteredRooms

            rooms.forEach(room => {
                const catId = room.hotel_category_id || 0
                const catName = room.category ? room.category.description : 'Sin categoría'
                if (!catMap[catId]) {
                    catMap[catId] = { id: catId, name: catName, rooms: [] }
                    groups.push(catMap[catId])
                }
                catMap[catId].rooms.push(room)
            })
            return groups
        },
    },
    created() {
        this.goToday()
        this.loadData()
    },
    mounted() {
        // Escuchar mensajes del iframe (Rent.vue)
        window.addEventListener('message', this.handleIframeMessage);
    },
    beforeDestroy() {
        // Limpiar listener al destruir el componente
        window.removeEventListener('message', this.handleIframeMessage);
    },
    methods: {
        /* ── Date Helpers ── */
        parseDate(str) {
            const p = str.split('-')
            return new Date(+p[0], +p[1] - 1, +p[2])
        },
        toStr(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0')
        },
        daysBetween(a, b) {
            return Math.round((b - a) / 864e5)
        },
        isSameDay(a, b) { return a.toDateString() === b.toDateString() },
        isWeekend(d) { return d.getDay() === 0 || d.getDay() === 6 },

        /* ── Navigation ── */
        goToday() {
            const t = new Date()
            t.setHours(0, 0, 0, 0)
            // Start 2 days before today so today is visible and near left
            const s = new Date(t)
            s.setDate(t.getDate() - 2)
            this.startDate = s
            this.buildDays()
            this.loadReservations()
        },
        navigatePrev() {
            const s = new Date(this.startDate)
            s.setDate(s.getDate() - 7)
            this.startDate = s
            this.buildDays()
            this.loadReservations()
        },
        navigateNext() {
            const s = new Date(this.startDate)
            s.setDate(s.getDate() + 7)
            this.startDate = s
            this.buildDays()
            this.loadReservations()
        },
        buildDays() {
            const dayNames = ['DOM', 'LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB']
            const today = new Date()
            today.setHours(0, 0, 0, 0)
            this.days = []
            for (let i = 0; i < this.visibleDays; i++) {
                const d = new Date(this.startDate)
                d.setDate(this.startDate.getDate() + i)
                this.days.push({
                    date: d,
                    dateStr: this.toStr(d),
                    shortName: dayNames[d.getDay()],
                    dayNumber: d.getDate(),
                    monthShort: d.toLocaleDateString('es-ES', { month: 'short' }).toUpperCase(),
                    isToday: this.isSameDay(d, today),
                    isWeekend: this.isWeekend(d),
                })
            }
        },

        /* ── Data Loading ── */
        async loadData() {
            await Promise.all([this.loadRooms(), this.loadCategories(), this.loadReservations()])
        },
        async loadRooms() {
            try {
                const r = await this.$http.get('/hotels/rooms')
                this.rooms = r.data?.rooms?.data || r.data?.data || []
                this.filteredRooms = [...this.rooms]
            } catch (e) {
                console.error('Rooms error:', e)
            }
        },
        async loadCategories() {
            try {
                const r = await this.$http.get('/hotels/categories')
                this.categories = r.data?.categories?.data || r.data?.data || []
            } catch (e) { this.categories = [] }
        },
        async loadReservations() {
            this.loading = true
            try {
                const endDate = new Date(this.startDate)
                endDate.setDate(this.startDate.getDate() + this.visibleDays - 1)
                const r = await this.$http.get('/hotels/reservations/calendar/events', {
                    params: { start_date: this.toStr(this.startDate), end_date: this.toStr(endDate) }
                })
                this.reservations = r.data?.data || []
                
                // Load daily sales totals for all visible days
                await this.loadDailySalesTotals()
            } catch (e) {
                console.error('Reservations error:', e)
            } finally {
                this.loading = false
            }
        },
        applyFilters() {
            this.filteredRooms = this.rooms.filter(r => {
                if (this.selectedCategory && r.hotel_category_id != this.selectedCategory) return false
                return true
            })
        },
        toggleSearch() {
            this.searchOpen = !this.searchOpen
            if (this.searchOpen) {
                this.$nextTick(() => this.$refs.searchInput && this.$refs.searchInput.focus())
            } else {
                this.searchQuery = ''
            }
        },
        toggleCategory(catId) {
            this.$set(this.collapsedCategories, catId, !this.collapsedCategories[catId])
        },

        /* ── Availability Stats ── */
        getDayAvailability(day) {
            const total = this.filteredRooms.length
            if (!total) return { available: 0, occupied: 0, pct: 0 }
            let occupied = 0
            const dayDate = day.date
            this.filteredRooms.forEach(room => {
                const hasRes = this.reservations.some(r => {
                    if (r.hotel_room_id != room.id) return false
                    const s = this.parseDate(r.start_date)
                    const e = this.parseDate(r.end_date)
                    return dayDate >= s && dayDate <= e
                })
                if (hasRes) occupied++
            })
            return {
                available: total - occupied,
                occupied,
                pct: (occupied / total) * 100,
            }
        },
        getPctClass(pct) {
            if (pct >= 100) return 'pct-full'
            if (pct >= 75) return 'pct-high'
            if (pct >= 50) return 'pct-mid'
            return 'pct-low'
        },
        getCategoryDayStats(group, day) {
            const rooms = group.rooms
            const total = rooms.length
            let occupied = 0
            const dayDate = day.date
            rooms.forEach(room => {
                const hasRes = this.reservations.some(r => {
                    if (r.hotel_room_id != room.id) return false
                    const s = this.parseDate(r.start_date)
                    const e = this.parseDate(r.end_date)
                    return dayDate >= s && dayDate <= e
                })
                if (hasRes) occupied++
            })
            // Price: get from daily sales total for this category
            const price = this.getCategoryDayPrice(group, day)
            return { available: total - occupied, price }
        },
        getRoomCategoryPrice(catId) {
            // Try to find price from room rates
            const room = this.rooms.find(r => r.hotel_category_id == catId)
            if (room && room.rates && room.rates.length) {
                return room.rates[0].price || 0
            }
            return 0
        },
        formatPrice(val) {
            if (!val) return '0.00'
            return Number(val).toFixed(2)
        },
        async loadDailySalesTotals() {
            this.dailySalesTotals = {}
            this.categoryDailySales = {}
            
            const promises = []
            
            // Load overall daily sales totals
            this.days.forEach(day => {
                promises.push(
                    this.$http.get('/hotels/reservations/calendar/daily-sales-total', {
                        params: { date: this.toStr(day.date) }
                    }).then(response => {
                        this.$set(this.dailySalesTotals, this.toStr(day.date), parseFloat(response.data.total || 0));
                    }).catch(e => {
                        console.error('Error getting daily sales total for', this.toStr(day.date), ':', e);
                        this.$set(this.dailySalesTotals, this.toStr(day.date), 0);
                    })
                )
            })
            
            // Load category-specific daily sales totals
            this.categories.forEach(category => {
                this.days.forEach(day => {
                    promises.push(
                        this.$http.get('/hotels/reservations/calendar/category-daily-sales-total', {
                            params: { 
                                date: this.toStr(day.date),
                                category_id: category.id
                            }
                        }).then(response => {
                            const key = `${this.toStr(day.date)}_${category.id}`
                            this.$set(this.categoryDailySales, key, parseFloat(response.data.total || 0));
                        }).catch(e => {
                            console.error('Error getting category daily sales total for', category.id, this.toStr(day.date), ':', e);
                            const key = `${this.toStr(day.date)}_${category.id}`
                            this.$set(this.categoryDailySales, key, 0);
                        })
                    )
                })
            })
            
            await Promise.all(promises);
        },
        getDaySalesTotal(day) {
            return this.dailySalesTotals[this.toStr(day.date)] || 0;
        },
        getCategoryDayPrice(group, day) {
            const key = `${this.toStr(day.date)}_${group.id}`
            return this.categoryDailySales[key] || 0;
        },

        /* ── Bar Calculation ── */
        getRoomBars(roomId) {
            const ws = this.startDate
            const weDate = new Date(this.startDate)
            weDate.setDate(ws.getDate() + this.visibleDays - 1)
            const bars = []
            const barColors = [
                'bar-sky', 'bar-teal', 'bar-emerald', 'bar-amber',
                'bar-rose', 'bar-violet', 'bar-lime', 'bar-orange'
            ]

            this.reservations.forEach(r => {
                if (r.hotel_room_id != roomId) return
                const rStart = this.parseDate(r.start_date)
                const rEnd = this.parseDate(r.end_date)
                if (rEnd < ws || rStart > weDate) return

                const visStart = rStart < ws ? ws : rStart
                const visEnd = rEnd > weDate ? weDate : rEnd
                const startIdx = this.daysBetween(ws, visStart)
                const span = this.daysBetween(visStart, visEnd) + 1

                // Assign a consistent color based on reservation id
                if (!this.barColorIndex[r.id]) {
                    const idx = Object.keys(this.barColorIndex).length % barColors.length
                    this.barColorIndex[r.id] = barColors[idx]
                }

                const leftPx = startIdx * this.dayCellWidth + 2
                const widthPx = span * this.dayCellWidth - 4

                bars.push({
                    ...r,
                    colorClass: this.barColorIndex[r.id],
                    isMultiGuest: false,
                    style: {
                        left: leftPx + 'px',
                        width: widthPx + 'px',
                    }
                })
            })
            return bars
        },
        getBarClass(bar) {
            // Si es una reserva, mostrar con color especial
            if (bar.is_reserve) {
                return 'bars-reservation';
            }
            
            const statusMap = {
                confirmed: 'bars-confirmed',
                pending: 'bars-pending',
                cancelled: 'bars-cancelled',
                checked_in: 'bars-checkin',
                checked_out: 'bars-checkout',
                ACTIVE: 'bars-active',
                FINALIZADO: 'bars-finalized',
                active: 'bars-active',
                Confirmada: 'bars-confirmed',
                Pendiente: 'bars-pending',
            }
            return statusMap[bar.status] || 'bars-default'
        },

        /* ── Labels ── */
        getRoomStatusLabel(s) {
            return { DISPONIBLE: 'Disponible', OCUPADO: 'Ocupada', MANTENIMIENTO: 'Mantenimiento', LIMPIEZA: 'Limpieza' }[s] || s || ''
        },
        getRoomStatusIcon(s) {
            return { OCUPADO: '★', MANTENIMIENTO: '▲', LIMPIEZA: '×', DISPONIBLE: '□' }[s] || ''
        },
        getStatusLabel(s) {
            return { confirmed: 'Confirmada', pending: 'Pendiente', cancelled: 'Cancelada', checked_in: 'Check-in', checked_out: 'Check-out', ACTIVE: 'Activa', FINALIZADO: 'Finalizado' }[s] || s || '—'
        },
        getTravelReasonLabel(v) {
            if (!v) return '—';
            const r = this.travelReasons.find(x => x.value === v);
            return r ? r.label : v;
        },
        // Cualquier reserva no finalizada es editable. El status real puede ser
        // 'INICIADO' (default), 'ACTIVE', 'confirmed', 'pending', 'checked_in', etc.
        // La única razón para bloquear edición es 'FINALIZADO'.
        isEditableStatus(status) {
            if (!status) return true;
            const s = String(status).toUpperCase();
            return s !== 'FINALIZADO' && s !== 'CANCELLED' && s !== 'CHECKED_OUT';
        },

        /* ── Actions ── */
        onCellClick(room, day) {
            const hit = this.reservations.find(r => {
                if (r.hotel_room_id != room.id) return false
                const s = this.parseDate(r.start_date), e = this.parseDate(r.end_date)
                return day.date >= s && day.date <= e
            })
            if (hit) {
                this.openDetails(hit)
            } else if (room.status === 'DISPONIBLE') {
                window.location.href = `/hotels/reception/${room.id}/rent`
            }
        },
        emptyEditForm() {
            return {
                id: null,
                customer_id: null,
                customer: { id: null, name: '', telephone: '', address: '', email: '', number: '', identity_document_type_id: null },
                hotel_room_id: null,
                hotel_rate_id: null,
                input_date: '', input_time: '',
                output_date: '', output_time: '',
                duration: 1,
                adults: 1, children: 0, quantity_persons: 1, towels: 1,
                license_plate: '', travel_reason: '', notes: '',
                rental_price: 0,
                _availableRates: [],
            };
        },
        async openDetails(res) {
            this.editing = false;
            this.showDetail = true;
            this.loadingDetail = true;
            try {
                const r = await this.$http.get(`/hotels/reservations/calendar/${res.id}/details`);
                this.detail = r.data?.data || res;
                this.populateEditForm(this.detail);
            } catch (e) {
                console.error('Detalle error:', e);
                // Fallback con los datos del evento (al menos algo)
                this.detail = res;
                this.populateEditForm(null, res);
            } finally {
                this.loadingDetail = false;
            }
        },
        populateEditForm(detail, fallbackEvent) {
            if (detail) {
                this.editForm = {
                    id: detail.id,
                    customer_id: detail.customer?.id || null,
                    customer: {
                        id: detail.customer?.id || null,
                        name: detail.customer?.name || '',
                        telephone: detail.customer?.telephone || '',
                        address: detail.customer?.address || '',
                        email: detail.customer?.email || '',
                        number: detail.customer?.number || '',
                        identity_document_type_id: detail.customer?.identity_document_type_id || null,
                    },
                    hotel_room_id: detail.room?.id || null,
                    hotel_rate_id: detail.rate?.hotel_rate_id || null,
                    input_date: detail.dates?.input_date || '',
                    input_time: (detail.dates?.input_time || '').slice(0,5),
                    output_date: detail.dates?.output_date || '',
                    output_time: (detail.dates?.output_time || '').slice(0,5),
                    duration: detail.dates?.duration || 1,
                    adults: detail.adults ?? 1,
                    children: detail.children ?? 0,
                    quantity_persons: detail.quantity_persons ?? 1,
                    towels: detail.towels ?? 1,
                    license_plate: detail.license_plate || '',
                    travel_reason: detail.travel_reason || '',
                    notes: detail.notes || '',
                    rental_price: detail.rate?.rental_price || 0,
                    _availableRates: detail.room?.rates || [],
                };
            } else if (fallbackEvent) {
                this.editForm = {
                    ...this.emptyEditForm(),
                    id: fallbackEvent.id,
                    hotel_room_id: fallbackEvent.hotel_room_id,
                    hotel_rate_id: fallbackEvent.hotel_rate_id,
                    input_date: fallbackEvent.start_date,
                    input_time: (fallbackEvent.input_time || '').slice(0,5),
                    output_date: fallbackEvent.end_date,
                    output_time: (fallbackEvent.output_time || '').slice(0,5),
                    duration: fallbackEvent.duration || 1,
                    adults: fallbackEvent.adults ?? 1,
                    children: fallbackEvent.children ?? 0,
                    quantity_persons: fallbackEvent.quantity_persons ?? 1,
                    towels: fallbackEvent.towels ?? 1,
                    license_plate: fallbackEvent.license_plate || '',
                    travel_reason: fallbackEvent.travel_reason || '',
                    notes: fallbackEvent.notes || '',
                    customer: {
                        ...this.emptyEditForm().customer,
                        id: fallbackEvent.customer_id || null,
                        name: fallbackEvent.customer_name || '',
                        telephone: fallbackEvent.customer_telephone || '',
                        address: fallbackEvent.customer_address || '',
                        number: fallbackEvent.customer_number || '',
                    },
                    customer_id: fallbackEvent.customer_id || null,
                };
            }
        },
        startEdit() {
            this.editing = true;
            // Si la lista de tarifas no se cargó (fallback), pedirla a /room/{id}
            if (!this.editForm._availableRates?.length && this.editForm.hotel_room_id) {
                this.loadRatesForRoom(this.editForm.hotel_room_id);
            }
        },
        cancelEdit() {
            this.editing = false;
            this.populateEditForm(this.detail);
        },
        async loadRatesForRoom(roomId) {
            try {
                const r = await this.$http.get(`/hotels/rooms/${roomId}/rates`);
                this.editForm._availableRates = (r.data?.room_rates || []).map(rr => ({
                    hotel_rate_id: rr.rate?.id ?? rr.hotel_rate_id,
                    rate_description: rr.rate?.description ?? '',
                    price: parseFloat(rr.price || 0),
                }));
            } catch (e) {
                console.error('Error tarifas:', e);
                this.editForm._availableRates = [];
            }
        },
        onEditRoomChange(roomId) {
            this.editForm.hotel_rate_id = null;
            this.editForm._availableRates = [];
            if (roomId) this.loadRatesForRoom(roomId);
        },
        onEditRateChange(rateId) {
            const rate = this.editForm._availableRates.find(r => r.hotel_rate_id === rateId);
            if (rate) this.editForm.rental_price = rate.price;
        },
        recomputeDuration() {
            const a = this.parseDate(this.editForm.input_date);
            const b = this.parseDate(this.editForm.output_date);
            if (a && b && !isNaN(a) && !isNaN(b)) {
                const d = Math.max(1, this.daysBetween(a, b));
                this.editForm.duration = d;
            }
        },
        async saveEdit() {
            if (!this.editForm.id) return;
            if (!this.editForm.customer?.name) {
                this.$message.warning('El nombre del cliente es obligatorio.');
                return;
            }
            if (!this.editForm.hotel_room_id || !this.editForm.hotel_rate_id) {
                this.$message.warning('Seleccione habitación y tarifa.');
                return;
            }
            if (!this.editForm.input_date || !this.editForm.output_date) {
                this.$message.warning('Seleccione fechas de ingreso y salida.');
                return;
            }
            if (this.parseDate(this.editForm.input_date) >= this.parseDate(this.editForm.output_date)) {
                this.$message.warning('La fecha de salida debe ser posterior a la de ingreso.');
                return;
            }

            this.saving = true;
            try {
                const payload = {
                    customer_id: this.editForm.customer.id || this.editForm.customer_id,
                    customer: { ...this.editForm.customer },
                    hotel_room_id: this.editForm.hotel_room_id,
                    hotel_rate_id: this.editForm.hotel_rate_id,
                    input_date: this.editForm.input_date,
                    input_time: this.editForm.input_time || '14:00',
                    output_date: this.editForm.output_date,
                    output_time: this.editForm.output_time || '12:00',
                    duration: parseInt(this.editForm.duration, 10) || 1,
                    adults: parseInt(this.editForm.adults, 10) || 0,
                    children: parseInt(this.editForm.children, 10) || 0,
                    quantity_persons: Math.max(1, (parseInt(this.editForm.adults, 10) || 0) + (parseInt(this.editForm.children, 10) || 0)),
                    towels: parseInt(this.editForm.towels, 10) || 0,
                    license_plate: this.editForm.license_plate || null,
                    travel_reason: this.editForm.travel_reason || null,
                    notes: this.editForm.notes || null,
                };
                const r = await this.$http.put(`/hotels/reservations/calendar/${this.editForm.id}/update`, payload);
                if (r.data?.success) {
                    this.$message.success(r.data.message || 'Reserva actualizada.');
                    this.editing = false;
                    await this.loadReservations();
                    // Refrescar el detail mostrado con los datos nuevos
                    const fresh = await this.$http.get(`/hotels/reservations/calendar/${this.editForm.id}/details`);
                    this.detail = fresh.data?.data || this.detail;
                    this.populateEditForm(this.detail);
                } else {
                    this.$message.error(r.data?.message || 'No se pudo actualizar la reserva.');
                }
            } catch (e) {
                const msg = e?.response?.data?.message || 'Error al actualizar la reserva.';
                this.$message.error(msg);
            } finally {
                this.saving = false;
            }
        },
        goToEdit(res) {
            if (res.status === 'ACTIVE' || res.status === 'checked_in') {
                window.location.href = `/hotels/reception/${res.id}/rent/checkout`
            } else {
                window.location.href = `/hotels/reception/${res.hotel_room_id}/rent`
            }
        },
        goToEditMode(res) {
            // Redirigir a la página de rent con parámetros para editar la reserva
            // sin marcar como iniciada (similar a check-in de reserva pero solo edición)
            const roomId = res.room?.id || res.hotel_room_id || res.room_id;
            console.log('goToEditMode - res:', res);
            console.log('goToEditMode - roomId:', roomId);
            
            if (!roomId) {
                console.error('No se encontró ID de habitación para la reserva:', res);
                this.$message.error('No se puede editar la reserva: no se encontró la habitación asociada');
                return;
            }
            
            const baseUrl = `/hotels/reception/${roomId}/rent`;
            const params = new URLSearchParams({
                reservation_id: res.id.toString(),
                edit_mode: 'true',
                source: 'calendar_edit',
                t: Date.now().toString()
            });
            window.location.href = `${baseUrl}?${params.toString()}`;
        },
        newReservation() {
            // Mostrar diálogo para seleccionar habitación
            this.showReservationPopup = true;
        },
        selectRoomForReservation(room) {
            this.selectedRoom = room;
            // Cerrar el diálogo de selección
            this.showReservationPopup = false;
            // Abrir el popup de Rent.vue en un iframe
            this.$nextTick(() => {
                this.showRentIframe = true;
            });
        },
        closeReservationPopup() {
            this.showReservationPopup = false;
            this.selectedRoom = null;
        },
        closeRentIframe() {
            this.showRentIframe = false;
            this.selectedRoom = null;
            // Recargar las reservas después de cerrar
            this.loadReservations();
        },
        onIframeLoad() {
            // El iframe ha cargado completamente
            console.log('Iframe de Rent.vue cargado');
        },
        getReservationUrl(room) {
            // Construir URL con parámetros codificados para asegurar que lleguen
            const baseUrl = `/hotels/reception/${room.id}/rent`;
            const params = new URLSearchParams({
                is_reservation: 'true',
                t: Date.now().toString(),
                source: 'calendar'
            });
            return `${baseUrl}?${params.toString()}`;
        },
        handleIframeMessage(event) {
            // Verificar que el mensaje sea del iframe de reserva
            if (event.data && event.data.action === 'reservation_created') {
                console.log('Reserva creada detectada:', event.data);
                
                // Mostrar mensaje de éxito
                this.$message({
                    message: event.data.message || 'Reserva creada exitosamente',
                    type: 'success',
                    duration: 3000
                });
                
                // Cerrar el popup del iframe
                this.closeRentIframe();
            }
        },
        deleteReservation(reservation) {
            const name = reservation.customer_name || (reservation.customer && reservation.customer.name) || 'esta reserva';
            this.$confirm(`¿Está seguro de eliminar la reserva de ${name}?`, 'Eliminar Reserva', {
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
                confirmButtonClass: 'el-button--danger'
            }).then(() => {
                this.deleting = true;
                this.$http.delete(`/hotels/reservations/calendar/${reservation.id}/delete`)
                    .then(response => {
                        if (response.data?.success === false) {
                            this.$message.error(response.data.message || 'No se pudo eliminar la reserva.');
                            return;
                        }
                        this.$message.success(response.data?.message || 'Reserva eliminada exitosamente');
                        this.showDetail = false;
                        this.loadReservations();
                    })
                    .catch(error => {
                        const msg = error?.response?.data?.message || 'Error al eliminar la reserva.';
                        this.$message.error(msg);
                    })
                    .finally(() => {
                        this.deleting = false;
                    });
            }).catch(() => { /* cancelado */ });
        },
    }
}
</script>

<style scoped>
/* ════════════════════════════════════════════════
   RESERVATION CALENDAR — Cloudbeds-style Timeline
   ════════════════════════════════════════════════ */

.rcal {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 13px;
    color: #333;
}

/* ── Toolbar ── */
.rcal-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #dce0e6;
    border-radius: 8px;
    padding: 8px 14px;
    margin-bottom: 0;
    gap: 12px;
    flex-wrap: wrap;
}
.rcal-tb-left, .rcal-tb-right { display: flex; align-items: center; gap: 10px; }
.rcal-tb-center { font-weight: 600; color: #374151; }

.rcal-view-btns { display: flex; align-items: center; gap: 2px; }
.rcal-vbtn {
    display: flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border: 1px solid #dce0e6; background: #fff;
    border-radius: 6px; color: #6b7280; cursor: pointer; transition: all .15s;
}
.rcal-vbtn:hover { background: #f3f4f6; color: #374151; }
.rcal-vbtn.active { background: #f0f4ff; border-color: #a5b4fc; color: #4f46e5; }
.rcal-vbtn-add { background: #10b981; border-color: #10b981; color: #fff; }
.rcal-vbtn-add:hover { background: #059669; border-color: #059669; color: #fff; }

.rcal-nav { display: flex; align-items: center; background: #f3f4f6; border-radius: 6px; padding: 2px; gap: 1px; }
.rcal-nav-btn {
    display: flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border: none; background: transparent;
    border-radius: 5px; color: #6b7280; cursor: pointer; font-size: 12px; transition: all .12s;
}
.rcal-nav-btn:hover { background: #fff; color: #111; box-shadow: 0 1px 3px rgba(0,0,0,.1); }
.rcal-nav-today { width: auto; padding: 0 14px; font-weight: 700; font-size: 12px; letter-spacing: .3px; }

.rcal-month-title { font-size: 15px; font-weight: 700; color: #111827; }

.rcal-select {
    appearance: none; -webkit-appearance: none;
    background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 8px center/14px no-repeat;
    border: 1px solid #dce0e6; border-radius: 6px; padding: 6px 30px 6px 10px;
    font-size: 12px; color: #374151; cursor: pointer; outline: none; transition: all .12s;
}
.rcal-select:focus { border-color: #818cf8; box-shadow: 0 0 0 2px rgba(99,102,241,.12); }
.rcal-tb-label { font-size: 13px; font-weight: 600; color: #6b7280; padding: 4px 12px; background: #f3f4f6; border-radius: 6px; }

.rcal-search-box {
    display: flex; align-items: center; gap: 6px;
    border: 1px solid #dce0e6; border-radius: 6px; padding: 5px 10px;
    background: #fff; min-width: 240px;
}
.rcal-search-box svg { color: #9ca3af; flex-shrink: 0; }
.rcal-search-input {
    border: none; outline: none; font-size: 12px; color: #374151;
    background: transparent; width: 100%;
}

/* ── Calendar Container ── */
.rcal-container {
    border: 1px solid #dce0e6;
    border-top: none;
    background: #fff;
    border-radius: 0 0 8px 8px;
    overflow: hidden;
}
.rcal-scroll {
    overflow-x: auto;
    overflow-y: auto;
    max-height: calc(100vh - 240px);
}
.rcal-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
.rcal-scroll::-webkit-scrollbar-track { background: #f9fafb; }
.rcal-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.rcal-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

.rcal-grid { display: flex; flex-direction: column; }

/* ── Row base ── */
.rcal-row { display: flex; border-bottom: 1px solid #ebedf0; }
.rcal-room-cell {
    width: 160px; min-width: 160px; flex-shrink: 0;
    border-right: 2px solid #dce0e6;
    background: #fff;
    position: sticky; left: 0; z-index: 5;
}
.rcal-days-row { display: flex; flex: 1; min-width: 0; position: relative; }

/* ── Global Header Row ── */
.rcal-row-header { background: #f8f9fb; border-bottom: 2px solid #dce0e6; position: sticky; top: 0; z-index: 12; }
.rcal-row-header .rcal-room-cell { background: #f8f9fb; }
.rcal-corner { display: flex; align-items: center; justify-content: center; }

.rcal-day-header {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    border-right: 1px solid #ebedf0; padding: 6px 2px; gap: 2px;
    flex-shrink: 0; transition: background .1s;
}
.rcal-day-header:last-child { border-right: none; }
.dh-today { background: rgba(59,130,246,.06); }
.dh-weekend { color: #9ca3af; }

.rcal-dh-avail { display: flex; align-items: center; justify-content: center; }
.rcal-dh-avail-num {
    display: flex; align-items: center; justify-content: center;
    width: 22px; height: 22px; border-radius: 50%;
    font-size: 10px; font-weight: 700; color: #fff;
    background: #3b82f6;
}
.dh-today .rcal-dh-avail-num { background: #10b981; }

.rcal-dh-date { text-align: center; }
.rcal-dh-dayname { font-size: 11px; font-weight: 700; color: #4b5563; text-transform: uppercase; }
.dh-today .rcal-dh-dayname { color: #2563eb; font-weight: 800; }

.rcal-dh-pct { font-size: 10px; font-weight: 700; }
.pct-full { color: #059669; }
.pct-high { color: #d97706; }
.pct-mid { color: #2563eb; }
.pct-low { color: #9ca3af; }

/* ── Category Row ── */
.rcal-row-category {
    background: #f1f3f5; cursor: pointer; border-bottom: 1px solid #dce0e6;
    transition: background .1s;
}
.rcal-row-category:hover { background: #e9ecef; }
.rcal-row-category .rcal-room-cell { background: #f1f3f5; }
.rcal-row-category:hover .rcal-room-cell { background: #e9ecef; }

.rcal-cat-label {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 14px; font-size: 13px; font-weight: 700; color: #374151;
}
.rcal-chevron { transition: transform .2s; color: #6b7280; flex-shrink: 0; }
.rcal-chevron-open { transform: rotate(90deg); }

.rcal-day-cat-cell {
    border-right: 1px solid #e5e7eb; padding: 6px 4px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rcal-day-cat-cell:last-child { border-right: none; }
.dcc-today { background: rgba(59,130,246,.04); }

.rcal-cat-stats { display: flex; flex-direction: column; align-items: center; gap: 1px; }
.rcal-cat-avail { font-size: 12px; font-weight: 700; color: #374151; }
.rcal-cat-price { font-size: 10px; color: #6b7280; }
.rcal-cat-price.price-highlight { color: #2563eb; text-decoration: underline; font-weight: 600; }

/* ── Room Row ── */
.rcal-row-room { min-height: 38px; transition: background .08s; }
.rcal-row-room:hover { background: #fafbfc; }
.rcal-row-room:hover .rcal-room-label { background: #fafbfc; }

.rcal-room-label {
    display: flex; align-items: center; justify-content: space-between;
    padding: 4px 12px; background: #fff; gap: 6px;
}
.rcal-room-name { font-size: 13px; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rcal-room-icons { display: flex; align-items: center; gap: 4px; }
.rcal-room-icon { font-size: 11px; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; border-radius: 3px; }
.ricon-ocupado { color: #dc2626; }
.ricon-disponible { color: #059669; }
.ricon-mantenimiento { color: #d97706; }
.ricon-limpieza { color: #2563eb; }

/* ── Day Cells ── */
.rcal-days-body { position: relative; overflow: visible; }
.rcal-day-cell {
    height: 38px; border-right: 1px solid #f0f1f3; cursor: pointer;
    flex-shrink: 0; transition: background .08s;
}
.rcal-day-cell:last-child { border-right: none; }
.rcal-day-cell:hover { background: rgba(59,130,246,.04); }
.dc-today { background: rgba(59,130,246,.03); }
.dc-weekend:not(.dc-today) { background: #fafbfc; }

/* ── Reservation Bars ── */
.rcal-bar {
    position: absolute; top: 3px; bottom: 3px;
    border-radius: 4px; z-index: 4; cursor: pointer;
    display: flex; align-items: center; padding: 0 8px;
    overflow: hidden; transition: filter .12s, box-shadow .12s;
    min-width: 20px;
}
.rcal-bar:hover {
    filter: brightness(.92);
    box-shadow: 0 3px 12px rgba(0,0,0,.15);
    z-index: 6;
}
.rcal-bar-text {
    font-size: 11px; font-weight: 600; color: #fff;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    text-shadow: 0 1px 2px rgba(0,0,0,.15);
}

/* Bar status colors — Diagonal stripe pattern matching screenshot */
.bars-confirmed {
    background: linear-gradient(135deg, #4fc3f7 25%, #29b6f6 25%, #29b6f6 50%, #4fc3f7 50%, #4fc3f7 75%, #29b6f6 75%);
    background-size: 14px 14px;
}
.bars-pending {
    background: linear-gradient(135deg, #ffd54f 25%, #ffca28 25%, #ffca28 50%, #ffd54f 50%, #ffd54f 75%, #ffca28 75%);
    background-size: 14px 14px;
}
.bars-pending .rcal-bar-text { color: #5d4037; text-shadow: none; }

/* Color especial para reservas */
.bars-reservation {
    background: linear-gradient(135deg, #ce93d8 25%, #ba68c8 25%, #ba68c8 50%, #ce93d8 50%, #ce93d8 75%, #ba68c8 75%);
    background-size: 14px 14px;
}
.bars-reservation .rcal-bar-text { 
    color: #fff; 
    text-shadow: 0 1px 2px rgba(0,0,0,.2);
}

.bars-cancelled {
    background: linear-gradient(135deg, #ef9a9a 25%, #e57373 25%, #e57373 50%, #ef9a9a 50%, #ef9a9a 75%, #e57373 75%);
    background-size: 14px 14px;
}
.bars-checkin {
    background: linear-gradient(135deg, #81d4fa 25%, #4fc3f7 25%, #4fc3f7 50%, #81d4fa 50%, #81d4fa 75%, #4fc3f7 75%);
    background-size: 14px 14px;
}
.bars-checkout {
    background: linear-gradient(135deg, #b39ddb 25%, #9575cd 25%, #9575cd 50%, #b39ddb 50%, #b39ddb 75%, #9575cd 75%);
    background-size: 14px 14px;
}
.bars-active {
    background: linear-gradient(135deg, #81c784 25%, #66bb6a 25%, #66bb6a 50%, #81c784 50%, #81c784 75%, #66bb6a 75%);
    background-size: 14px 14px;
}
.bars-finalized {
    background: linear-gradient(135deg, #b0bec5 25%, #90a4ae 25%, #90a4ae 50%, #b0bec5 50%, #b0bec5 75%, #90a4ae 75%);
    background-size: 14px 14px;
}
.bars-default {
    background: linear-gradient(135deg, #4fc3f7 25%, #29b6f6 25%, #29b6f6 50%, #4fc3f7 50%, #4fc3f7 75%, #29b6f6 75%);
    background-size: 14px 14px;
}

/* ── Empty state ── */
.rcal-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 60px 20px; color: #b0b7c3; gap: 10px; font-size: 14px; font-weight: 500;
}

/* ── Legend ── */
.rcal-legend {
    display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    padding: 10px 16px; margin-top: 10px;
    background: #fff; border: 1px solid #dce0e6; border-radius: 8px;
}
.rcal-lg-item { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #6b7280; font-weight: 500; }
.rcal-lg-dot {
    width: 14px; height: 14px; border-radius: 3px; flex-shrink: 0; display: inline-block;
}
.lg-info { background: #4caf50; }
.lg-connected { background: #2196f3; }
.lg-confirmed {
    background: linear-gradient(135deg, #4fc3f7 25%, #29b6f6 25%, #29b6f6 50%, #4fc3f7 50%, #4fc3f7 75%, #29b6f6 75%);
    background-size: 8px 8px;
}
.lg-pending {
    background: linear-gradient(135deg, #ffd54f 25%, #ffca28 25%, #ffca28 50%, #ffd54f 50%, #ffd54f 75%, #ffca28 75%);
    background-size: 8px 8px;
}
.lg-checkin {
    background: linear-gradient(135deg, #81d4fa 25%, #4fc3f7 25%, #4fc3f7 50%, #81d4fa 50%, #81d4fa 75%, #4fc3f7 75%);
    background-size: 8px 8px;
}
.lg-active {
    background: linear-gradient(135deg, #81c784 25%, #66bb6a 25%, #66bb6a 50%, #81c784 50%, #81c784 75%, #66bb6a 75%);
    background-size: 8px 8px;
}
.lg-checkout {
    background: linear-gradient(135deg, #b39ddb 25%, #9575cd 25%, #9575cd 50%, #b39ddb 50%, #b39ddb 75%, #9575cd 75%);
    background-size: 8px 8px;
}

/* ── Detail Modal ── */
.rcal-modal-title { display: flex; align-items: center; gap: 10px; }
.rcal-modal-title > span:first-child { font-size: 18px; font-weight: 800; color: #111827; }
.rcal-modal-badge {
    font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    padding: 3px 10px; border-radius: 6px; color: #fff;
}
.mbadge-confirmed { background: #29b6f6; }
.mbadge-pending { background: #ffca28; color: #5d4037; }
.mbadge-cancelled { background: #e57373; }
.mbadge-checked_in { background: #4fc3f7; }
.mbadge-checked_out { background: #9575cd; }
.mbadge-ACTIVE { background: #66bb6a; }
.mbadge-FINALIZADO { background: #90a4ae; }
.mbadge-default { background: #9ca3af; }

.rcal-detail { display: flex; flex-direction: column; gap: 22px; }
.rcal-det-section {}
.rcal-det-title {
    display: flex; align-items: center; gap: 7px;
    font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px;
    color: #9ca3af; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;
}
.rcal-det-title svg { color: #c9cdd5; }
.rcal-det-fields { display: flex; flex-direction: column; gap: 8px; }
.rcal-det-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
.rcal-det-field label {
    display: block; font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .3px; color: #b0b7c3; margin-bottom: 1px;
}
.rcal-det-field span { font-size: 14px; font-weight: 600; color: #111827; }
.rcal-det-price span { font-size: 20px; font-weight: 800; color: #059669; }
.rcal-det-notes { font-size: 13px; color: #6b7280; line-height: 1.5; margin: 0; background: #f9fafb; padding: 10px 14px; border-radius: 8px; }
.rcal-modal-footer { display: flex; justify-content: flex-end; gap: 8px; }

/* ── Edit form layout ── */
.rcal-edit { padding-top: 4px; }
.rcal-edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 16px;
}
.rcal-edit-field { margin-bottom: 0 !important; }
.rcal-edit-field-wide { grid-column: 1 / -1; }
.rcal-edit-field .el-form-item__label {
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
    color: #6b7280; padding: 0 0 2px 0; line-height: 1.2;
}
.rcal-edit-field .el-form-item__content { line-height: 1; }
@media (max-width: 640px) {
    .rcal-edit-grid { grid-template-columns: 1fr; }
}

/* ── Room Selection Grid ── */
.room-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    max-height: 300px;
    overflow-y: auto;
    padding: 8px 0;
}

.room-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.room-card-compact {
    padding: 10px;
}

.room-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
}

.room-card-header {
    margin-bottom: 8px;
}

.room-card-header h4 {
    margin: 0 0 3px 0;
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.room-card-compact .room-card-header h4 {
    font-size: 13px;
}

.room-category {
    font-size: 11px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 12px;
    display: inline-block;
}

.room-card-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.room-status {
    font-size: 11px;
    font-weight: 500;
    padding: 3px 6px;
    border-radius: 4px;
    text-align: center;
    margin: 0;
}

.room-status.status-disponible {
    background: #dcfce7;
    color: #166534;
}

.room-status.status-ocupado {
    background: #fee2e2;
    color: #dc2626;
}

.room-status.status-limpieza {
    background: #fef3c7;
    color: #d97706;
}

.room-status.status-mantenimiento {
    background: #f3f4f6;
    color: #6b7280;
}

.room-price {
    font-size: 16px;
    font-weight: 700;
    color: #059669;
    margin: 0;
}

.room-card-compact .room-price {
    font-size: 14px;
}

/* ── Modal Compact Styles ── */
.rcal-modal-compact .el-dialog__header {
    padding: 12px 16px !important;
}

.rcal-modal-compact .el-dialog__body {
    padding: 0 16px 16px 16px !important;
}

.rcal-modal-compact .rcal-modal-title {
    font-size: 16px !important;
}

.rcal-modal-compact .rcal-modal-footer {
    padding: 8px 16px 16px !important;
}

/* ── Rent Iframe ── */
.iframe-container {
    width: 100%;
    height: 100%;
}

.rcal-modal-fullscreen {
    max-width: 95vw;
    max-height: 95vh;
}

.rcal-modal-fullscreen .el-dialog__body {
    padding: 0;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .rcal-toolbar { flex-direction: column; align-items: stretch; }
    .rcal-tb-left, .rcal-tb-right { justify-content: center; flex-wrap: wrap; }
    .rcal-room-cell { width: 120px; min-width: 120px; }
    .rcal-det-grid { grid-template-columns: 1fr; }
}
</style>
