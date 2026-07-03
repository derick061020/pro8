@extends('ecommerce::layouts.layout_ecommerce_cart.index')

@push('styles')
<style>
    #addressModal .modal-dialog {
        max-width: 800px;
    }

    #map {
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

    #addressModal .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
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
<h2 class="my-4" style="font-weight: 900;">Finalizar compra</h2>
<div class="row" id="app">
    <div class="col-md-8 mb-3">
        <div class="card">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#cartCollapse" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0" style="cursor: pointer;">
                    <svg clip-rule="evenodd"
                        fill-rule="evenodd"
                        height="24"
                        stroke-linejoin="round"
                        stroke-miterlimit="2"
                        viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg" id="fi_4893746">
                        <path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"/>
                    </svg>
                    <span class="ml-2 font-weight-bold">Tu carrito</span>
                </div>
            </button>

            <div id="cartCollapse" class="collapse show">
                <div class="card-body border-top card-body-h-auto">
                    <div class="row" v-if="records.length > 0">
                        <div v-for="(row, index) in records" class="col-12" :key="row.id">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <figure class="product-image-container m-0">
                                        <a href="#" class="product-image">
                                            <img class="image-product w-100" :src="(row.image && row.image !== 'imagen-no-disponible.jpg') ? '{{ $itemsBasePath }}' + '/' + row.image : '{{ $defaultImagePath }}'" :alt="row.description || 'Producto sin imagen'">
                                        </a>
                                    </figure>
                                </div>
                                <div class="col-md-7">
                                    <h5 class="product-title m-0">
                                        <a href="#">@{{ row.description }}</a>
                                    </h5>
                                    <span class="price text-muted">
                                        @{{ row.currency_type_symbol }} @{{ row.sale_unit_price }}
                                    </span>
                                </div>
                                <div class="col-md-3 text-right">
                                    <button type="button" @click="deleteItem(row.id, index)" class="btn btn-sm btn-link text-muted px-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                    <div class="input-group input-group-sm">
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
                                    <strong>@{{ row.currency_type_symbol }} @{{ (row.sale_unit_price * row.cantidad).toFixed(2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="alert alert-info mb-0">
                        Tu carrito está vacío
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-6">
                            <a href="/ecommerce" class="btn btn-light btn-sm text-muted text-capitalize">
                                <i class="fa fa-arrow-left"></i>
                                Continuar Comprando
                            </a>
                        </div>
                        <div class="col-6 text-right" v-if="records.length > 0">
                            <a href="#" @click="clearShoppingCart" class="btn btn-light btn-sm text-danger text-capitalize">Limpiar Carrito</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="records.length > 0">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#deliveryCollapse" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0" style="cursor: pointer;">
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
                    <span class="ml-2 font-weight-bold">Datos de envio</span>
                </div>
            </button>
            <div id="deliveryCollapse" class="collapse show">
                <div class="card-body border-top card-body-h-auto">
                    <div class="row">
                        <template v-if="records.length > 0">
                            {{-- Switch: Recojo en tienda (solo si está habilitado en configuración) --}}
                            <div class="col-6 mb-2" v-if="enableStorePickup">
                                <div class="d-flex align-items-center">
                                    <label class="mb-0 mr-2 font-weight-bold" style="cursor:pointer;" @click="togglePickupMode">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="mr-1" style="vertical-align:middle;"><path d="M3 9l9 -7 9 7v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                        Recojo en tienda
                                    </label>
                                    <div
                                        @click="togglePickupMode"
                                        style="cursor:pointer; display:inline-flex; align-items:center; width:44px; height:24px; border-radius:12px; padding:2px; transition:background 0.2s;"
                                        :style="isPickupMode ? 'background:#ff6600;' : 'background:#ccc;'"
                                    >
                                        <div style="width:20px; height:20px; border-radius:50%; background:#fff; transition:transform 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.3);"
                                            :style="isPickupMode ? 'transform:translateX(20px);' : 'transform:translateX(0);'"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button class="btn btn-light btn-sm text-muted text-capitalize" @click="openAddressModal">@{{ form_contact.address ? 'Cambiar dirección' : 'Agregar dirección' }}</button>
                            </div>

                            {{-- Modo recojo en tienda: radio buttons de sucursales --}}
                            <div class="col-12" v-if="isPickupMode">
                                <div v-if="pickupBranches.length === 0" class="alert alert-info py-2 mb-0">
                                    No hay sucursales de recojo configuradas.
                                </div>
                                <div v-else>
                                    <label class="font-weight-bold d-block mb-1">Selecciona una sucursal:</label>
                                    <div
                                        v-for="branch in pickupBranches"
                                        :key="branch.id"
                                        class="d-flex align-items-start border rounded px-3 py-2 mb-1"
                                        :style="selectedPickupBranch && selectedPickupBranch.id === branch.id ? 'border-color:#ff6600 !important; background:#fff8f4;' : ''"
                                        @click="selectPickupBranch(branch)"
                                        style="cursor:pointer;"
                                    >
                                        <input
                                            type="radio"
                                            :value="branch.id"
                                            :checked="selectedPickupBranch && selectedPickupBranch.id === branch.id"
                                            @change="selectPickupBranch(branch)"
                                            class="mt-1 mr-2"
                                            style="cursor:pointer; accent-color:#ff6600; flex-shrink:0;"
                                        >
                                        <div>
                                            <strong>@{{ branch.name }}</strong>
                                            <div class="text-muted" style="font-size:0.875rem;" v-if="branch.address">@{{ branch.address }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="col-6">
                            <label class="font-weight-bold mb-1" style="">
                                Teléfono de contacto
                            </label>
                            <input
                                type="tel"
                                v-model="form_contact.telephone"
                                class="form-control form-control-sm"
                                placeholder="Ej: 987654321"
                                maxlength="15"
                                required
                            >
                        </div>

                        {{-- Modo delivery normal --}}
                        <template v-if="!isPickupMode">
                            <div class="col-8">
                                <label class="font-weight-bold mb-1" v-if="form_contact.address">
                                    Dirección
                                </label>
                                <ul class="mb-0">
                                    <li>@{{ form_contact.address }}</li>
                                    <li>@{{ ubigeoLabel }} </li>
                                </ul>
                            </div>
                            <div class="col-12">
                                <!-- Mensaje sin cobertura de delivery -->
                                <div v-if="deliveryMessage != ''" class="alert alert-warning text-left py-2 px-3 mb-0" role="alert">
                                    <strong>&#9888; Sin cobertura:</strong> @{{ deliveryMessage }}
                                </div>

                                <!-- Opciones de envío: mostrar cuando hay múltiples zonas disponibles -->
                                <div v-if="availableDeliveryZones.length > 1" class="mt-2">
                                    <label class="font-weight-bold d-block mb-1">Opciones de envío:</label>
                                    <div
                                        v-for="zone in availableDeliveryZones"
                                        :key="zone.id"
                                        class="d-flex align-items-center justify-content-between border rounded px-3 py-2 mb-1"
                                        :style="deliveryZone && deliveryZone.id === zone.id ? 'border-color:#ff6600 !important; background:#fff8f4;' : 'cursor:pointer;'"
                                        @click="selectDeliveryZone(zone)"
                                        style="cursor:pointer;"
                                    >
                                        <div class="d-flex align-items-center">
                                            <input
                                                type="radio"
                                                :value="zone.id"
                                                :checked="deliveryZone && deliveryZone.id === zone.id"
                                                @change="selectDeliveryZone(zone)"
                                                class="mr-2"
                                                style="cursor:pointer; accent-color:#ff6600;"
                                            >
                                            <span>@{{ zone.name }}</span>
                                        </div>
                                        <strong class="text-dark">S/ @{{ parseFloat(zone.price).toFixed(2) }}</strong>
                                    </div>
                                </div>

                                <!-- Una sola zona disponible: mostrar informativo -->
                                <div v-else-if="availableDeliveryZones.length === 1 && deliveryZone" class="mt-2">
                                    <small class="text-success">
                                        <i class="fa fa-check-circle"></i>
                                        Envío disponible: <strong>@{{ deliveryZone.name }}</strong> &mdash; S/ @{{ parseFloat(deliveryZone.price).toFixed(2) }}
                                    </small>
                                </div>
                            </div>
                        </template>
                        {{-- fin modo delivery --}}

                    </div>
                </div>
            </div>
        </div>

        @if($enable_electronic_documents)
            {{-- Modo documentos electrónicos: solo lectura, tipo inferido del número del usuario --}}
            <div class="card">
                <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#documentyCollapse" style="text-decoration: none; display: block;">
                    <div class="card-header d-flex align-items-center bg-white border-bottom-0" style="cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2b2b2b" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 17h6" />
                            <path d="M9 13h6" />
                        </svg>
                        <span class="ml-2 font-weight-bold">Datos del comprobante</span>
                    </div>
                </button>
                <div id="documentyCollapse" class="collapse show">
                    <div class="card-body border-top card-body-h-auto">
                        <ul class="list-unstyled mb-0">
                            <li><span class="text-muted">Cliente:</span> <strong>@{{ user.name }}</strong></li>
                            <li><span class="text-muted">Documento:</span> <strong>@{{ user.number }}</strong></li>
                            <li><span class="text-muted">Tipo de doc.:</span> <strong>@{{ invoiceTypeLabel }}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="card" v-if="records.length > 0">
            <button type="button" class="btn btn-link btn-block text-left p-0" data-toggle="collapse" data-target="#paymentCollapse" style="text-decoration: none; display: block;">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0" style="cursor: pointer;">
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
                    <span class="ml-2 font-weight-bold">Método de pago</span>
                </div>
            </button>
            <div id="paymentCollapse" class="collapse show">
                <div class="card-body border-top card-body-h-auto">
                    @guest('ecommerce')
                    <p class="text-muted mb-0"><a href="{{route('tenant_ecommerce_login')}}">Inicia sesión</a> para seleccionar un método de pago.</p>
                    @elseauth('ecommerce')
                    <div class="btn-group btn-group-toggle d-block" role="group">
                        <label class="btn btn-outline-primary d-block" :class="{ active: selectedPaymentMethod === 'culqi' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="culqi" autocomplete="off"> Pagar con VISA
                        </label>
                        <label class="btn btn-outline-primary d-block mt-2" :class="{ active: selectedPaymentMethod === 'cash' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="cash" autocomplete="off"> Pagar con EFECTIVO
                        </label>
                        <label v-if="enableYape" class="btn btn-outline-primary d-block mt-2" :class="{ active: selectedPaymentMethod === 'yape' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="yape" autocomplete="off"> Pagar con YAPE
                        </label>
                        <label v-if="enableTransfer" class="btn btn-outline-primary d-block mt-2" :class="{ active: selectedPaymentMethod === 'transfer' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="transfer" autocomplete="off"> Transferencia Bancaria
                        </label>
                        @if($information->script_paypal)
                        <label class="btn btn-outline-primary d-block mt-2" :class="{ active: selectedPaymentMethod === 'paypal' }">
                            <input type="radio" v-model="selectedPaymentMethod" value="paypal" autocomplete="off"> PayPal
                        </label>
                        @endif
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div><!-- End .col-lg-8 -->

    <div class="col-md-4">
        <div class="cart-summary">
            <h3>Resumen</h3>
            <table class="table table-totals">
                <tbody>

                    <tr v-if="summary.total_exonerated > 0">
                        <td>OP.EXONERADAS</td>
                        <td>S/ @{{ summary.total_exonerated }}</td>
                    </tr>
                    <tr v-if="summary.total_taxed > 0">
                        <td>OP.GRAVADA</td>
                        <td>S/ @{{ summary.total_taxed }}</td>
                    </tr>
                    <tr v-if="summary.total_igv > 0">
                        <td>IGV</td>
                        <td>S/ @{{ summary.total_igv }}</td>
                    </tr>
                    <tr v-if="appliedCoupon && appliedCoupon.code">
                        <td>
                            Cupon <span class="badge badge-dark">@{{ appliedCoupon.code }}</span><br>
                            <button class="btn btn-sm btn-link text-danger" @click="removeCoupon">Eliminar Cupon</button>
                        </td>
                        <td>
                            S/ @{{ appliedCoupon.discount }}
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
            <div class="coupon-block mt-3">
                <div class="input-group">
                    <input v-model="couponField" type="text" class="form-control" placeholder="Código de cupón">
                    <div class="input-group-append">
                        <button class="btn btn-primary" @click="applyCoupon" :disabled="couponLoading">Aplicar</button>
                    </div>
                </div>

                <small class="text-danger" v-if="couponMessage">@{{ couponMessage }}</small>
            </div>

            <div class="checkout-methods text-center">
                @guest('ecommerce')
                <a href="{{route('tenant_ecommerce_login')}}" class="btn btn-block btn-sm btn-primary login-link culqi">Pagar
                    con VISA</a>
                <a href="{{route('tenant_ecommerce_login')}}" class="btn btn-block btn-sm btn-primary login-link">Pagar
                    con EFECTIVO</a>
                <a style="margin-left:15%" href="{{route('tenant_ecommerce_login')}}"
                    class="btn btn-block btn-sm login-link">
                    <img src="{{ asset('porto-ecommerce/assets/images/btn_buynowCC_LG.gif') }}" alt="">
                </a>

                @elseauth('ecommerce')
                    <button v-if="selectedPaymentMethod !== 'paypal'" class="btn btn-block btn-primary mt-2" :disabled="!selectedPaymentMethod" @click="executePayment">Pagar</button>

                @endauth

            </div><!-- End .checkout-methods -->
        </div><!-- End .cart-summary -->
    </div><!-- End .col-lg-4 -->

    <!-- Modal de Dirección -->
    <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                    <h5 style="font-size: 1.25rem; margin: 0; font-weight: 500; color: #333;">Confirmar dirección</h5>
                    <button type="button" @click="closeAddressModal()" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; padding: 0; margin: 0; color: #000; opacity: 0.5;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div>
                    @if(!empty($googleMapsApiKey))
                        <div id="map" style="height: 400px; width: 100%; margin: 0px;"></div>
                    @else
                        <br>
                    @endif
                    <div style="padding: 15px 15px 15px 15px;">
                        @if(empty($googleMapsApiKey))
                            <div style="margin-bottom: 10px;">
                                <div style="display: flex; gap: 10px;">
                                    <div style="flex: 1;">
                                        <label for="department" style="display: block; margin-bottom: 5px; font-weight: 500; color: #555; width: 100%;">Departamento</label>
                                        <select v-model="selectedDepartment" @change="updateProvinces" name="department" id="department" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background-color: #fff; width: 100%;">
                                            <option value="">Seleccione departamento</option>
                                            <option v-for="department in departments" :key="department.value" :value="department.value">
                                                @{{ department.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div style="flex: 1;">
                                        <label for="province" style="display: block; margin-bottom: 5px; font-weight: 500; color: #555; width: 100%;">Provincia</label>
                                        <select v-model="selectedProvince" @change="updateDistricts" name="province" id="province" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background-color: #fff; width: 100%;">
                                            <option value="">Seleccione provincia</option>
                                            <option v-for="province in provinces" :key="province.value" :value="province.value">
                                                @{{ province.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div style="flex: 1;">
                                        <label for="district" style="display: block; margin-bottom: 5px; font-weight: 500; color: #555; width: 100%;">Distrito</label>
                                        <select v-model="selectedDistrict" @change="checkDeliveryZone" name="district" id="district" style="padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; background-color: #fff; width: 100%;">
                                            <option value="">Seleccione distrito</option>
                                            <option v-for="district in districts" :key="district.value" :value="district.value">
                                                @{{ district.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Dirección</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="position: relative; width: 100%;">
                                    <input
                                        v-model="addressModal.address"
                                        @input="onAddressInputChange"
                                        @keydown.down.prevent="moveSuggestion(1)"
                                        @keydown.up.prevent="moveSuggestion(-1)"
                                        @keydown.enter.prevent="selectHighlighted"
                                        @keydown.esc="clearSuggestions"
                                        id="addressAutocompleteInput"
                                        type="text"
                                        placeholder="Ingrese su dirección completa"
                                        autocomplete="off"
                                        style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; outline: none; background-color: #fff;">

                                    <ul v-if="addressSuggestions.length > 0"
                                        style="position: absolute; top: 100%; left: 0; right: 0;
                                            background: #fff;
                                            border: 1px solid #ced4da;
                                            border-top: none;
                                            border-radius: 0 0 4px 4px;
                                            list-style: none;
                                            margin: 0; padding: 0;
                                            z-index: 9999;
                                            max-height: 156px;
                                            overflow-y: auto;
                                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                                    >
                                        <li v-for="(suggestion, i) in addressSuggestions"
                                            :key="i"
                                            @mousedown.prevent="selectSuggestionFromList(suggestion)"
                                            :style="{
                                                padding: '10px 14px',
                                                cursor: 'pointer',
                                                fontSize: '13px',
                                                borderBottom: '1px solid #f0f0f0',
                                                backgroundColor: highlightedIndex === i ? '#f0f4ff' : '#fff',
                                                color: '#333'
                                            }">
                                            <span style="font-weight: 500;">@{{ suggestion.mainText }}</span>
                                            <span style="color: #888; font-size: 12px; display: block;">@{{ suggestion.secondaryText }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500; color: #555;">Referencias</label>
                            <textarea v-model="addressModal.reference" placeholder="Ej: Al costado del parque, frente a la iglesia" rows="2" style="width: 100%; padding: 8px 12px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; resize: none; outline: none; background-color: #fff;"></textarea>
                        </div>
                    </div>
                </div>
                <!-- Alerta sin cobertura dentro del modal de dirección -->
                <div v-if="deliveryMessage" style="margin: 0 15px 10px; padding: 10px 14px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404;" class="mt-2">
                    <strong>Sin cobertura:</strong> @{{ deliveryMessage }}
                </div>
                <!-- Alerta de cobertura disponible dentro del modal -->
                {{-- <div v-if="deliveryZone" style="margin: 0 15px 10px; padding: 10px 14px; background: #d1e7dd; border: 1px solid #a3cfbb; border-radius: 4px; color: #0a3622;">
                    <strong>&#10003; Delivery disponible:</strong> @{{ deliveryZone.name }} &mdash; S/ @{{ deliveryZone.price }}
                </div> --}}
                <div style="padding: 15px; border-top: 1px solid #dee2e6;">
                    <button type="button" @click="confirmAddress()" style="background-color: #ff6600; color: white; border: none; padding: 12px 40px; font-size: 1rem; font-weight: 500; border-radius: 50px; cursor: pointer; width: 100%; text-transform: uppercase;">Continuar</button>
                </div>
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
    };
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
