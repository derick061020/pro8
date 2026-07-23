@extends('ecommerce::layouts.layout_ecommerce_cart.index')

@push('styles')
<style>
    #addressModal .modal-dialog {
        max-width: 800px;
    }

    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
    }

    #addressModal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    #addressModal .modal-title {
        font-weight: 600;
        color: #333;
    }

    #addressModal .close {
        font-size: 1.5rem;
        font-weight: 700;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    #addressModal .close:hover {
        opacity: 1;
    }

    .btn-input-group {
        height: 26.6px !important;
        border-radius: 0px !important;
        min-width: 32px !important;
    }

    .card-body-h-auto {
        min-height: auto !important;
    }

    .collapse-arrow {
        margin-left: auto;
        transition: transform 0.25s ease;
        flex-shrink: 0;
    }

    .btn[aria-expanded="false"] .collapse-arrow {
        transform: rotate(-90deg);
    }

    /* Aceptación de términos y condiciones */
    .terms {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 14px 0 12px;
        cursor: pointer;
        user-select: none;
        line-height: 1.35;
        font-size: 13px
    }

    .terms input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .terms .box {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        border-radius: 7px;
        border: 2px solid #d5dbe0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        transition: background-color .15s ease, border-color .15s ease, transform .12s ease;
    }

    .terms .box svg {
        opacity: 0;
        transform: scale(.6);
        transition: opacity .15s ease, transform .15s ease;
    }

    .terms--checked .box svg {
        opacity: 1;
        transform: scale(1);
    }

    .terms-txt a:hover {
        text-decoration: underline;
    }

    /* Desktop - una fila */
    .items-cart {
        display: grid;
        grid-template-columns: 80px 1fr auto auto auto;
        grid-template-areas: "thumb info quantity total delete";
        align-items: center;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .items-cart .thumb { grid-area: thumb; }
    .items-cart .info { grid-area: info; }
    .items-cart .modern-quantity-container { grid-area: quantity; }
    .items-cart .total { grid-area: total; }
    .items-cart .delete-item-btn { grid-area: delete; }

    /* Móvil - dos filas */
    @media (max-width: 576px) {
        .items-cart {
            grid-template-columns: 70px 1fr auto auto auto;
            grid-template-areas:
                "thumb info info info info"
                "thumb quantity quantity total delete";
        }
    }
</style>
@endpush

@section('content')

@php
    $configurationModel = \App\Models\Tenant\Configuration::first();
    $defaultImage = $configurationModel->product_default_image ?? 'imagen-no-disponible.jpg';
    $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
        ? asset('logo/imagen-no-disponible.jpg')
        : asset('storage/defaults/' . $defaultImage);
    $itemsBasePath = asset('storage/uploads/items');
    $googleMapsApiKey = app(Modules\Ecommerce\Http\Controllers\EcommerceController::class)->getGoogleMaps();
    $globalDiscountTypeId = $global_discount_type_id ?? null;
@endphp
<h2 class="my-4 mt-4" style="font-weight: 900;">Finalizar compra</h2>
<div class="row" id="app">
    <div class="col-md-8 mb-3">
        <div class="card card-cart">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#cartCollapse" aria-expanded="true" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0 card-cart-header" style="cursor: pointer;">
                    <span class="icon-card cart">
                        <svg clip-rule="evenodd"
                            fill-rule="evenodd"
                            height="24"
                            stroke-linejoin="round"
                            stroke-miterlimit="2"
                            viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg" id="fi_4893746">
                            <path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"/>
                        </svg>
                    </span>
                    <span class="ml-2 font-weight-bold title-card">Tu carrito</span>
                    <span class="head-summary">
                        <template v-if="records.length > 0">
                            <b>@{{ records.length }} @{{ records.length === 1 ? 'producto' : 'productos' }}</b>
                            <span class="head-summary-sep">·</span>
                            <span class="head-summary-amt">S/ @{{ summary.total }}</span>
                        </template>
                        <span v-else class="head-summary-warn">Carrito vacío</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="collapse-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
                </div>
            </button>

            <div id="cartCollapse" class="collapse show">
                <div class="card-cart-body">
                    <div v-if="records.length > 0">
                        <div v-for="(row, index) in records" class="items-cart" :key="row.id">
                            <div class="thumb">
                                <figure class="product-image-container m-0">
                                    <a href="#" class="product-image">
                                        <img class="image-product w-100" :src="(row.image && row.image !== 'imagen-no-disponible.jpg') ? '{{ $itemsBasePath }}' + '/' + row.image : '{{ $defaultImagePath }}'" :alt="row.description || 'Producto sin imagen'">
                                    </a>
                                </figure>
                            </div>

                            <div class="info">
                                <h5 class="product-title m-0">
                                    <a href="#">@{{ row.description }}</a>
                                </h5>
                                <span class="price text-muted">
                                    @{{ row.currency_type_symbol }} @{{ row.sale_unit_price }}
                                </span>
                            </div>

                            <div class="input-group input-group-sm modern-quantity-container w-auto">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-input-group" type="button" @click.stop.prevent="decrementQuantity(row)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                                    </button>
                                </div>
                                <input class="input-quantity form-control text-center" :data-product="row.id" type="number" v-model.number="row.cantidad">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-input-group" type="button" @click.stop.prevent="incrementQuantity(row)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    </button>
                                </div>
                            </div>

                            <strong class="total">@{{ row.currency_type_symbol }} @{{ (row.sale_unit_price * row.cantidad).toFixed(2) }}</strong>

                            <button type="button" @click="deleteItem(row.id, index)" class="btn btn-sm btn-link text-muted px-0 delete-item-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>

                        </div>
                    </div>
                    <div v-else class="cart-empty">
                        <span class="cart-empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.966 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z"/><path d="M9 11v-5a3 3 0 0 1 6 0v5"/></svg>
                        </span>
                        <p class="cart-empty-title">Tu carrito está vacío</p>
                        <p class="cart-empty-text">Agrega productos para continuar con tu compra.</p>
                        <a href="/ecommerce" class="cart-empty-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            Ver productos
                        </a>
                    </div>
                </div>

                <div class="card-footer card-cart-footer border-0">
                    <div class="row">
                        <div class="col-6">
                            <a href="/ecommerce" class="text-muted text-capitalize">
                                <i class="fa fa-arrow-left"></i>
                                Continuar Comprando
                            </a>
                        </div>
                        <div class="col-6 text-right" v-if="records.length > 0">
                            <a href="#" @click="clearShoppingCart" class="text-danger text-capitalize">Limpiar Carrito</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-cart" v-if="records.length > 0">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#deliveryCollapse" aria-expanded="true" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0 card-cart-header" style="cursor: pointer;">
                    <span class="icon-card">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#2b2b2b"
                            stroke-width="1.75"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            >
                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                            <path d="M3 9l4 0" />
                        </svg>
                    </span>
                    <span class="ml-2 font-weight-bold title-card">Datos de envio</span>
                    <span class="head-summary">
                        <template v-if="isPickupMode">
                            <b v-if="selectedPickupBranch">@{{ selectedPickupBranch.name }}</b>
                            <span v-else class="head-summary-warn">Elige sucursal</span>
                        </template>
                        <template v-else-if="form_contact.address || form_contact.telephone">
                            <span class="head-summary-addr" v-if="form_contact.address">@{{ form_contact.address }}</span>
                            <span class="head-summary-sep" v-if="form_contact.address && form_contact.telephone">·</span>
                            <b v-if="form_contact.telephone">@{{ form_contact.telephone }}</b>
                        </template>
                        <span v-else class="head-summary-warn">Falta completar</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="collapse-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
                </div>
            </button>
            <div id="deliveryCollapse" class="collapse show">
                <div class="card-body card-body-h-auto card-cart-body ship-body">

                    {{-- Switch: Recojo en tienda (solo si está habilitado en configuración) --}}
                    <div class="pickup-switch" v-if="enableStorePickup">
                        {{-- <label class="pickup-switch-label" @click="togglePickupMode">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9 -7 9 7v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            Recojo en tienda
                        </label>
                        <button type="button" class="switch" :class="{ 'switch--on': isPickupMode }" @click="togglePickupMode" :aria-pressed="isPickupMode ? 'true' : 'false'" aria-label="Activar recojo en tienda">
                            <span class="switch-knob"></span>
                        </button> --}}
                        <label
                            class="option-card send-mode"
                            :class="{ 'option-card--active': !isPickupMode }"
                        >
                            <span class="icon-delivery">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentcolor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path> <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path> <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path> <path d="M3 9l4 0"></path></svg>
                            </span>
                            <span class="option-card-body">
                                <strong>Envío a domicilio</strong>
                                <span class="option-card-sub">Llega a tu dirección</span>
                            </span>
                            <input
                                type="radio"
                                name="delivery_mode"
                                style="margin-left: auto"
                                :checked="!isPickupMode"
                                @change="setPickupMode(false)"
                            >
                        </label>
                        <label
                            class="option-card send-mode"
                            :class="{ 'option-card--active': isPickupMode }"
                        >
                            <span class="icon-delivery">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1-5h16l1 5"></path><path d="M4 9v11a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9"></path><path d="M3 9h18"></path><path d="M9 21v-6h6v6"></path></svg>
                            </span>
                            <span class="option-card-body">
                                <strong>Recojo en tienda</strong>
                                <span class="option-card-sub">Gratis · tú lo retiras</span>
                            </span>
                            <input
                                type="radio"
                                name="delivery_mode"
                                style="margin-left: auto"
                                :checked="isPickupMode"
                                @change="setPickupMode(true)"
                            >
                        </label>
                    </div>

                    <div class="ship-grid">
                        {{-- Columna izquierda: dirección de entrega o sucursal de recojo --}}
                        <div class="ship-col">
                            {{-- Modo recojo en tienda: radio buttons de sucursales --}}
                            <template v-if="isPickupMode">
                                <span class="field-label">Selecciona una sucursal</span>
                                <div v-if="pickupBranches.length === 0" class="ship-alert ship-alert--info">
                                    No hay sucursales de recojo configuradas.
                                </div>
                                <div v-else class="option-list">
                                    <label
                                        v-for="branch in pickupBranches"
                                        :key="branch.id"
                                        class="option-card"
                                        :class="{ 'option-card--active': selectedPickupBranch && selectedPickupBranch.id === branch.id }"
                                    >
                                        <input
                                            type="radio"
                                            :value="branch.id"
                                            :checked="selectedPickupBranch && selectedPickupBranch.id === branch.id"
                                            @change="selectPickupBranch(branch)"
                                        >
                                        <span class="option-card-body">
                                            <strong>@{{ branch.name }}</strong>
                                            <span class="option-card-sub" v-if="branch.address">@{{ branch.address }}</span>
                                        </span>
                                    </label>
                                </div>
                            </template>

                            {{-- Modo delivery normal --}}
                            <template v-else>
                                <span class="field-label">Dirección de entrega</span>
                                <button v-if="!form_contact.address" type="button" class="addr-btn" @click="openAddressModal">
                                    <span class="plus">+</span>
                                    Agregar dirección
                                </button>
                                <div v-else class="addr-card">
                                    <span class="addr-card-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7 -9 13 -9 13s-9 -6 -9 -13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </span>
                                    <div class="addr-card-info">
                                        <strong>@{{ form_contact.address }}</strong>
                                        <span class="addr-card-sub">@{{ ubigeoLabel }}</span>
                                    </div>
                                    <button type="button" class="addr-change" @click="openAddressModal">Cambiar</button>
                                </div>

                                {{-- Mensaje sin cobertura de delivery --}}
                                <div v-if="deliveryMessage != ''" class="ship-alert ship-alert--warn" role="alert">
                                    <strong>&#9888; Sin cobertura:</strong> @{{ deliveryMessage }}
                                </div>

                                {{-- Opciones de envío: mostrar cuando hay múltiples zonas disponibles --}}
                                <div v-if="availableDeliveryZones.length > 1" class="option-list mt-2">
                                    <span class="field-label">Opciones de envío</span>
                                    <label
                                        v-for="zone in availableDeliveryZones"
                                        :key="zone.id"
                                        class="option-card option-card--row"
                                        :class="{ 'option-card--active': deliveryZone && deliveryZone.id === zone.id }"
                                    >
                                        <span class="option-card-left">
                                            <input
                                                type="radio"
                                                :value="zone.id"
                                                :checked="deliveryZone && deliveryZone.id === zone.id"
                                                @change="selectDeliveryZone(zone)"
                                            >
                                            <span>@{{ zone.name }}</span>
                                        </span>
                                        <strong class="option-card-price">S/ @{{ parseFloat(zone.price).toFixed(2) }}</strong>
                                    </label>
                                </div>

                                {{-- Una sola zona disponible: mostrar informativo --}}
                                <div v-else-if="availableDeliveryZones.length === 1 && deliveryZone" class="ship-note ship-note--ok mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5 -5"/></svg>
                                    Envío disponible: <strong>@{{ deliveryZone.name }}</strong> &mdash; S/ @{{ parseFloat(deliveryZone.price).toFixed(2) }}
                                </div>
                            </template>
                        </div>

                        {{-- Columna derecha: teléfono de contacto --}}
                        <div class="ship-col">
                            <span class="field-label">Teléfono de contacto</span>
                            <input
                                type="tel"
                                v-model="form_contact.telephone"
                                class="input"
                                placeholder="Ej: 987 654 321"
                                maxlength="15"
                                inputmode="numeric"
                                required
                            >
                            <p class="hint">Te escribiremos por aquí para coordinar la entrega.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($enable_electronic_documents)
            {{-- Modo documentos electrónicos: solo lectura, tipo inferido del número del usuario --}}
            <div class="card card-cart">
                <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#documentyCollapse" aria-expanded="true" style="text-decoration: none; display: block;">
                    <div class="card-header d-flex align-items-center bg-white border-bottom-0 card-cart-header" style="cursor: pointer;">
                        <span class="icon-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 17h6" />
                                <path d="M9 13h6" />
                            </svg>
                        </span>
                        <span class="ml-2 font-weight-bold title-card">Datos del comprobante</span>
                        <span class="head-summary">
                            <b v-if="invoiceTypeLabel">@{{ invoiceTypeLabel }}</b>
                            <template v-if="user && user.number">
                                <span class="head-summary-sep" v-if="invoiceTypeLabel">·</span>
                                <span>@{{ user.number }}</span>
                            </template>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="collapse-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
                    </div>
                </button>
                <div id="documentyCollapse" class="collapse show">
                    <div class="card-body card-body-h-auto card-cart-body">
                        <ul class="doc-list">
                            <li><span>Cliente</span> <strong>@{{ user.name }}</strong></li>
                            <li><span>Documento</span> <strong>@{{ user.number }}</strong></li>
                            <li><span>Tipo de doc.</span> <strong>@{{ invoiceTypeLabel }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="card card-cart" v-if="records.length > 0">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#paymentCollapse" aria-expanded="true" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0 card-cart-header" style="cursor: pointer;">
                    <span class="icon-card">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#2b2b2d"
                            stroke-width="1.75"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            >
                            <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                            <path d="M3 10l18 0" />
                            <path d="M7 15l.01 0" />
                            <path d="M11 15l2 0" />
                        </svg>
                    </span>
                    <span class="ml-2 font-weight-bold title-card">Método de pago</span>
                    <span class="head-summary">
                        @guest('ecommerce')
                            <span class="head-summary-warn">Inicia sesión</span>
                        @else
                            <b v-if="selectedPaymentMethod === 'culqi'">Tarjeta (VISA)</b>
                            <b v-else-if="selectedPaymentMethod === 'cash'">Efectivo</b>
                            <b v-else-if="selectedPaymentMethod === 'yape'">Yape</b>
                            <b v-else-if="selectedPaymentMethod === 'transfer'">Transferencia</b>
                            <b v-else-if="selectedPaymentMethod === 'paypal'">PayPal</b>
                            <span v-else class="head-summary-warn">Elige un método</span>
                        @endguest
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="collapse-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
                </div>
            </button>
            <div id="paymentCollapse" class="collapse show">
                <div class="card-body card-body-h-auto card-cart-body">
                    @guest('ecommerce')
                    <div class="login-note">
                        <span class="login-note-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        </span>
                        <p><a href="{{route('tenant_ecommerce_login')}}" class="login-link">Inicia sesión</a> para seleccionar y guardar tu método de pago.</p>
                    </div>
                    @elseauth('ecommerce')
                    <div class="pay-methods" role="radiogroup">
                        <label class="pay-method" :class="{ 'pay-method--active': selectedPaymentMethod === 'culqi' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="culqi" autocomplete="off">
                            <span class="pay-method-ic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            </span>
                            <span class="pay-method-label">Pagar con tarjeta (VISA)</span>
                        </label>
                        <label class="pay-method" :class="{ 'pay-method--active': selectedPaymentMethod === 'cash' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="cash" autocomplete="off">
                            <span class="pay-method-ic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                            </span>
                            <span class="pay-method-label">Pagar con efectivo</span>
                        </label>
                        <label v-if="enableYape" class="pay-method" :class="{ 'pay-method--active': selectedPaymentMethod === 'yape' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="yape" autocomplete="off">
                            <span class="pay-method-ic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="2"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                            </span>
                            <span class="pay-method-label">Pagar con Yape</span>
                        </label>
                        <label v-if="enableTransfer" class="pay-method" :class="{ 'pay-method--active': selectedPaymentMethod === 'transfer' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="transfer" autocomplete="off">
                            <span class="pay-method-ic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V10l7 -5l7 5v11"/><path d="M9 21v-6h6v6"/></svg>
                            </span>
                            <span class="pay-method-label">Transferencia bancaria</span>
                        </label>
                        @if($information->script_paypal)
                        <label class="pay-method" :class="{ 'pay-method--active': selectedPaymentMethod === 'paypal' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="paypal" autocomplete="off">
                            <span class="pay-method-ic">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11l1 -7h6a3 3 0 0 1 0 6h-4"/><path d="M5 20l1.5 -9h5a3 3 0 0 1 0 6h-4"/></svg>
                            </span>
                            <span class="pay-method-label">PayPal</span>
                        </label>
                        @endif
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div><!-- End .col-lg-8 -->

    <div class="col-md-4">
      <div class="summary-sticky">
        <div class="cart-summary">
            <div class="sum-head"><h3>Resumen</h3></div>
            <div class="sum-body">
                <table class="table table-totals">
                    <tbody>

                        <tr v-if="summary.total_exonerated > 0">
                            <td>Op. exoneradas</td>
                            <td>S/ @{{ summary.total_exonerated }}</td>
                        </tr>
                        <tr v-if="summary.total_taxed > 0">
                            <td>Op. gravada</td>
                            <td>S/ @{{ summary.total_taxed }}</td>
                        </tr>
                        <tr v-if="summary.total_igv > 0">
                            <td>IGV (18%)</td>
                            <td>S/ @{{ summary.total_igv }}</td>
                        </tr>
                        <tr v-if="appliedCoupon && appliedCoupon.code">
                            <td>
                                Cupón <span class="badge badge-dark">@{{ appliedCoupon.code }}</span>
                                <button class="coupon-remove" @click="removeCoupon">Eliminar</button>
                            </td>
                            <td>
                                &minus; S/ @{{ appliedCoupon.discount }}
                            </td>
                        </tr>
                        <tr v-if="deliveryZone && parseFloat(deliveryZone.price) > 0">
                            <td>Envío <small class="text-muted">(@{{ deliveryZone.name }})</small></td>
                            <td>S/ @{{ summary.delivery }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>S/ @{{summary.total}}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Coupon input and applied coupon display -->
                <div class="coupon-block">
                    <div class="coupon">
                        <input v-model="couponField" type="text" class="input" placeholder="Código de cupón">
                        <button class="coupon-btn" @click="applyCoupon" :disabled="couponLoading">Aplicar</button>
                    </div>
                    <small class="coupon-msg text-danger" v-if="couponMessage">@{{ couponMessage }}</small>
                </div>
                <label class="terms" :class="{ 'terms--checked': acceptedTerms }" id="termsLabel">
                  <input type="checkbox" id="termsCheck" v-model="acceptedTerms">
                  <span class="box"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                  <span class="terms-txt">He leído y acepto los <a href="#" data-modal-open="termsModal" @click.prevent>Términos y Condiciones</a>.</span>
                </label>
                <div class="checkout-methods">
                    @guest('ecommerce')
                    <a href="{{route('tenant_ecommerce_login')}}" class="pay-btn login-link culqi" :class="{ disabled: !acceptedTerms }">Pagar con VISA</a>
                    <a href="{{route('tenant_ecommerce_login')}}" class="pay-btn pay-btn--ghost login-link" :class="{ disabled: !acceptedTerms }">Pagar con efectivo</a>

                    @elseauth('ecommerce')
                        <button v-if="selectedPaymentMethod !== 'paypal'" class="pay-btn" :class="{ disabled: !acceptedTerms }" :disabled="!selectedPaymentMethod || !acceptedTerms" @click="executePayment">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            Pagar
                        </button>

                    @endauth

                </div><!-- End .checkout-methods -->

                <div class="trust">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Pago 100% seguro y protegido
                </div>
                <div class="cards-row px-5">
                    <img src="{{ asset('porto-ecommerce/assets/images/payments-bordered.svg') }}" alt="payment methods" class="footer-payments">
                </div>
            </div>
            <div class="secure-foot">Transacción cifrada · IGV incluido según ley peruana</div>
        </div><!-- End .cart-summary -->
      </div><!-- End .summary-sticky -->
    </div><!-- End .col-lg-4 -->

    <!-- Modal de Dirección -->
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header h-auto">
                    <h3 class="modal-title">Confirmar dirección</h3>
                    <button type="button" class="close" @click="closeAddressModal()" aria-label="Close">
                        <span aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body p-2">
                    @if(!empty($googleMapsApiKey))
                        <div id="map"></div>
                    @endif

                    <div class="p-3">
                        @if(empty($googleMapsApiKey))
                            <div class="form-row mb-2">
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <label class="field-label" for="department">Departamento</label>
                                    <select v-model="selectedDepartment" @change="updateProvinces" name="department" id="department" class="input">
                                        <option value="">Seleccione departamento</option>
                                        <option v-for="department in departments" :key="department.value" :value="department.value">
                                            @{{ department.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <label class="field-label" for="province">Provincia</label>
                                    <select v-model="selectedProvince" @change="updateDistricts" name="province" id="province" class="input">
                                        <option value="">Seleccione provincia</option>
                                        <option v-for="province in provinces" :key="province.value" :value="province.value">
                                            @{{ province.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="field-label" for="district">Distrito</label>
                                    <select v-model="selectedDistrict" @change="checkDeliveryZone" name="district" id="district" class="input">
                                        <option value="">Seleccione distrito</option>
                                        <option v-for="district in districts" :key="district.value" :value="district.value">
                                            @{{ district.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="field-label">Dirección</label>
                            <div class="position-relative">
                                <input
                                    v-model="addressModal.address"
                                    @input="onAddressInputChange"
                                    @keydown.down.prevent="moveSuggestion(1)"
                                    @keydown.up.prevent="moveSuggestion(-1)"
                                    @keydown.enter.prevent="selectHighlighted"
                                    @keydown.esc="clearSuggestions"
                                    id="addressAutocompleteInput"
                                    type="text"
                                    class="input"
                                    placeholder="Ingrese su dirección completa"
                                    autocomplete="off">

                                <ul v-if="addressSuggestions.length > 0" class="addr-suggestions">
                                    <li v-for="(suggestion, i) in addressSuggestions"
                                        :key="i"
                                        @mousedown.prevent="selectSuggestionFromList(suggestion)"
                                        class="addr-suggestion"
                                        :class="{ 'addr-suggestion--active': highlightedIndex === i }">
                                        <span class="font-weight-bold">@{{ suggestion.mainText }}</span>
                                        <span class="d-block text-muted small">@{{ suggestion.secondaryText }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="field-label">Referencias</label>
                            <textarea v-model="addressModal.reference" class="input" rows="2" placeholder="Ej: Al costado del parque, frente a la iglesia"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Alerta sin cobertura dentro del modal de dirección -->
                <div v-if="deliveryMessage" class="ship-alert ship-alert--warn mx-3 mb-3">
                    <strong>&#9888; Sin cobertura:</strong> @{{ deliveryMessage }}
                </div>

                <div class="p-3 border-top px-4">
                    <button type="button" class="pay-btn" @click="confirmAddress()">Continuar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Modal reutilizable: Términos y Condiciones ===== --}}
    <div class="app-modal" id="termsModal" role="dialog" aria-modal="true" aria-labelledby="termsModalTitle" aria-hidden="true">
        <div class="app-modal__dialog">
            <div class="app-modal__header">
                <span class="app-modal__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/><path d="M9 9h1"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>
                </span>
                <h3 class="app-modal__title" id="termsModalTitle">Términos y Condiciones</h3>
                <button type="button" class="app-modal__close" data-modal-close aria-label="Cerrar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="app-modal__body">
                @if(!empty($configuration->terms_conditions))
                    <div class="app-modal__prose">{!! $configuration->terms_conditions !!}</div>
                @else
                    <p class="app-modal__prose text-muted">No se han definido términos y condiciones.</p>
                @endif
            </div>
            <div class="app-modal__footer">
                <button type="button" class="pay-btn second-btn" data-modal-close>Cerrar</button>
                <button type="button" class="pay-btn" data-modal-accept="termsCheck" data-modal-close>Acepto los términos</button>
            </div>
        </div>
    </div>

    <!-- ===== Overlay de carga mientras se procesa el pago ===== -->
    <div class="purchase-overlay purchase-overlay--show" v-if="processingPayment">
        <div class="purchase-loading" role="status" aria-live="polite">
            <span class="purchase-spinner" aria-hidden="true"></span>
            <h3>Estamos generando tu pedido</h3>
            <p>Por favor no cierres esta ventana hasta que el proceso termine.</p>
        </div>
    </div>

    <!-- ===== Confirmación de compra (post-pago) ===== -->
    <div class="purchase-overlay" :class="{ 'purchase-overlay--show': showConfirmModal }" v-if="successOrder">
        <div class="purchase-confirm" role="dialog" aria-modal="true" aria-label="Detalle de tu compra">
            <div class="purchase-confirm-head">
                <span class="ic">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1 -2 2H5a2 2 0 0 1 -2 -2V5a2 2 0 0 1 2 -2h11"/></svg>
                </span>
                <div>
                    <h3>¡Pago realizado!</h3>
                    <div class="ordn">Pedido @{{ successOrder.number }}</div>
                </div>
            </div>
            <div class="purchase-confirm-body">
                <div class="o-item" v-for="(it, i) in successOrder.items" :key="i">
                    <div><span class="o-q">@{{ it.cantidad }}×</span>@{{ it.description }}</div>
                    <span class="o-amt">@{{ it.symbol }} @{{ it.total }}</span>
                </div>
                <div class="o-sep"></div>
                <div class="o-row" v-if="parseFloat(successOrder.total_exonerated) > 0">Op. exoneradas <span class="v">S/ @{{ successOrder.total_exonerated }}</span></div>
                <div class="o-row" v-if="parseFloat(successOrder.total_taxed) > 0">Op. gravada <span class="v">S/ @{{ successOrder.total_taxed }}</span></div>
                <div class="o-row" v-if="parseFloat(successOrder.total_igv) > 0">IGV (18%) <span class="v">S/ @{{ successOrder.total_igv }}</span></div>
                <div class="o-row" v-if="parseFloat(successOrder.delivery) > 0">Envío <span class="v">S/ @{{ successOrder.delivery }}</span></div>
                <div class="o-total"><span class="l">Total pagado</span><span class="a">S/ @{{ successOrder.total }}</span></div>
                <div class="o-pay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Pago: @{{ successOrder.paymentLabel }} · @{{ successOrder.deliveryLabel }}
                </div>
            </div>
            <div class="purchase-confirm-foot">
                <button type="button" class="pay-btn" @click="goToThankYou">
                    Continuar
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </div>
        </div>
    </div>

</div><!-- End .row -->

@if(auth('ecommerce')->check() && $information->script_paypal)
<div id="paypal-widget-container" style="display:none;">
    {!!html_entity_decode($information->script_paypal)!!}
</div>
@endif

<input type="hidden" id="total_amount" data-total="0.0">

@endsection

@push('scripts')
<!-- Configuration globals para cart app -->
<script>
    window.__ecommerce_config = {
        phone_whatsapp: {!! json_encode($configuration->phone_whatsapp ?? '') !!},
        global_discount_type: {!! json_encode($global_discount_type ?? []) !!},
        user: {!! json_encode(optional(Auth::guard("ecommerce")->user())->makeHidden(['password', 'remember_token'])) !!},
        userAddress: {!! json_encode($userAddress ?? null) !!},
        enable_electronic_documents: {!! json_encode($enable_electronic_documents ?? false) !!},
        enable_store_pickup: {!! json_encode($enable_store_pickup ?? false) !!},
        pickup_branches: {!! json_encode($pickup_branches ?? []) !!},
        enable_yape: {!! json_encode($enable_yape ?? false) !!},
        enable_transfer: {!! json_encode($enable_transfer ?? false) !!},
    };

    window.__routes = {
        payment_cash: '{{ route("tenant_ecommerce_payment_cash") }}',
        user_data: '{{ route("tenant_ecommerce_user_data") }}',
        locations: '{{ route("get_location_cascade") }}',
        home: '{{ route("tenant.ecommerce.index") }}',
        culqi: '{{ route("tenant_ecommerce_culqui") }}',
        thank_you: '{{ route("tenant_ecommerce_thank_you", ["external_id" => "EXTERNAL_ID"]) }}',
    };
</script>

<script>
    (function () {
        function openModal(modal) {
            if (!modal) return;
            modal.classList.add('app-modal--open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('app-modal--open');
            modal.setAttribute('aria-hidden', 'true');
            if (!document.querySelector('.app-modal--open')) {
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('click', function (e) {
            // Abrir
            var opener = e.target.closest('[data-modal-open]');
            if (opener) {
                e.preventDefault();
                openModal(document.getElementById(opener.getAttribute('data-modal-open')));
                return;
            }

            // Aceptar: marca un checkbox antes de cerrar (dispara change para Vue/listeners)
            var accepter = e.target.closest('[data-modal-accept]');
            if (accepter) {
                var check = document.getElementById(accepter.getAttribute('data-modal-accept'));
                if (check && !check.checked) {
                    check.checked = true;
                    check.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            // Cerrar: botón con [data-modal-close] o clic en el overlay
            if (e.target.closest('[data-modal-close]')) {
                closeModal(e.target.closest('.app-modal'));
                return;
            }
            if (e.target.classList.contains('app-modal')) {
                closeModal(e.target);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal(document.querySelector('.app-modal--open'));
            }
        });
    })();
</script>

@vite('modules/Ecommerce/Resources/assets/js/frontend/cart-app.js')

<script>
    Culqi.publicKey = {!! json_encode($configuration->token_public_culqui ) !!};
    if(!Culqi.publicKey)
    {
      $('.culqi').hide()
/*
        swal({
            title: "Culqi configuración",
            text: "El pago con visa aun no esta disponible. Intente con efectivo.",
            type: "error",
            position: 'top-end',
            icon: 'warning',
        })
*/
    }
    Culqi.options({
        installments: true
    });

    async function askedDocument(order) {
        app_cart.order_generated = order
        $('#modal_ask_document').modal('show')
    }

    async function execCulqi() {

       console.log( 'errores', app_cart.errors)

       //app_cart.errors = 'demo'

    //   console.log( 'errores22', app_cart.errors)


        let precio = Math.round((Number($("#total_amount").data('total')) * 100).toFixed(2));
        if (precio > 0) {
            Culqi.settings({
                title: "Productos Ecommerce",
                currency: 'PEN',
                description: 'Compras Ecommerce Facturador Pro',
                amount: precio
            });
            Culqi.open();
        }
    }


    async function culqi() {
        if (Culqi.token) {

            swal({
                title: "Estamos hablando con su banco",
                text: `Por favor no cierre esta ventana hasta que el proceso termine.`,
                focusConfirm: false,
                onOpen: () => {
                    Swal.showLoading()
                }
            });

            let precio = Math.round((Number($("#total_amount").data('total')).toFixed(2) * 100));
            let precio_culqi = Number($("#total_amount").data('total')).toFixed(2);

            var url = "/culqi";
            var token = Culqi.token.id;
            var email = Culqi.token.email;
            var installments = Culqi.token.metadata.installments;

            const formpayment = await app_cart.getFormPaymentCash()

            var data = {
                producto: 'Compras Ecommerce Facturador Pro',
                precio: precio,
                precio_culqi: precio_culqi,
                token: token,
                email: email,
                installments: installments,
                customer: JSON.stringify(formpayment.customer),
                items: JSON.stringify(getItems()),
                purchase: JSON.stringify(formpayment.purchase),
                // Coupon fields
                discount_coupon_code: formpayment.discount_coupon_code,
                discount_coupon_id: formpayment.discount_coupon_id,
                total_discount: formpayment.total_discount,
                shipping_address: formpayment.shipping_address || '',
            }

            $.ajax({
              url: "{{route('tenant_ecommerce_culqui')}}",
              method: 'post',
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: data,
              dataType: 'JSON',
              success: function (data) {
                if (data.success == true) {
                  app_cart.saveContactDataUser();
                  app_cart.clearShoppingCart();
                  swal({
                    title: "Gracias por su pago!",
                    text: "En breve le enviaremos un correo electronico con los detalles de su compra.",
                    type: "success"
                  }).then((x) => {
                    askedDocument(data.order);
                    //window.location = "{{ route('tenant.ecommerce.index') }}";
                  })
                } else {
                  const message = data.message
                  swal("Pago No realizado", message, "error");
                }
              },
              error: function (error_data) {
                console.log(error_data)
                if (error_data.status === 422) {
                    app_cart.errors = JSON.parse( error_data.responseText);
                }
                swal("Pago No realizado", 'Faltan completar campos', "error");
              }
            });

        } else {
            console.log(Culqi.error);
            swal("Pago No realizado", Culqi.error.user_message, "error");
        }
    };

    function getCustomer() {
        let user = JSON.parse('{!! json_encode( Auth::guard("ecommerce")->user() ) !!}')
        return {
            "codigo_tipo_documento_identidad": "0",
            "numero_documento": "0",
            "apellidos_y_nombres_o_razon_social": user.name,
            "codigo_pais": "PE",
            "ubigeo": "150101",
            "direccion": app_cart.user.address,
            "correo_electronico": user.email,
            "telefono": app_cart.user.telephone
        }
    }

    function getItems() {
        return app_cart.records
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

</script>

<script src="{{ route('google_maps_script') }}"></script>

@endpush
