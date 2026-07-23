@php
    $configurationModel = \App\Models\Tenant\Configuration::first();
    $phoneWhatsapp = $ecommerceConfiguration->phone_whatsapp ?? $configurationModel->phone_whatsapp ?? null;
    $defaultImage = $configurationModel->product_default_image ?? 'imagen-no-disponible.jpg';
    $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
        ? asset('logo/imagen-no-disponible.jpg')
        : asset('storage/defaults/' . $defaultImage);
    $mainImagePath = ($record->image && $record->image !== 'imagen-no-disponible.jpg')
        ? asset('storage/uploads/items/'.$record->image)
        : $defaultImagePath;
@endphp
<div class="product-single-container product-single-default product-quick-view container position-relative p-0">
    <div class="row mx-0">
        <div class="col-lg-6 col-md-6 product-single-gallery px-5 py-5 preview-img-container">
            <div class="product-slider-container product-item">
                <div class="product-single-carousel owl-carousel owl-theme">
                    <div class="product-item">
                        <img class="product-single-image" src="{{ $mainImagePath }}"
                             data-zoom-image="{{ $mainImagePath }}" alt="{{ $record->description }}" />
                    </div>

                    @foreach($record->images as $row)

                    <div class="product-item">
                        @php
                            $loopImagePath = ($row->image && $row->image !== 'imagen-no-disponible.jpg')
                                ? asset('storage/uploads/items/'.$row->image)
                                : $defaultImagePath;
                        @endphp
                        <img class="product-single-image"
                             src="{{ $loopImagePath }}"
                             data-zoom-image="{{ $loopImagePath }}" alt="{{ $record->description }}" />
                    </div>

                    @endforeach

                    <!--<div class="product-item">
                        <img class="product-single-image"
                            src="{{ asset('storage/uploads/items/'.$record->image_medium) }}"
                            data-zoom-image="{{ asset('storage/uploads/items/'.$record->image_medium) }}" />
                    </div> -->

                </div>

            </div>
            @if($record->images->count() > 0)
            <div class="prod-thumbnail row owl-dots" id='carousel-custom-dots'>
                <div class="col-3 owl-dot">
                    <img class="img-miniature" src="{{ $mainImagePath }}" alt="{{ $record->description }}" />
                </div>

                @foreach($record->images as $row)

                    <div class="col-3 owl-dot">
                        @php
                            $thumbImagePath = ($row->image && $row->image !== 'imagen-no-disponible.jpg')
                                ? asset('storage/uploads/items/'.$row->image)
                                : $defaultImagePath;
                        @endphp
                        <img src="{{ $thumbImagePath }}" alt="{{ $record->description }}" />
                    </div>

                @endforeach

                <!--<div class="col-3 owl-dot">
                    <img src="{{ asset('porto_ecommerce/ajax/assets/images/products/zoom/product-2.html') }}" />
                </div>
                <div class="col-3 owl-dot">
                    <img src="{{ asset('porto_ecommerce/ajax/assets/images/products/zoom/product-3.html') }}" />
                </div>
                <div class="col-3 owl-dot">
                    <img src="{{ asset('porto_ecommerce/ajax/assets/images/products/zoom/product-4.html') }}" />
                </div>-->
            </div>
            @endif
        </div><!-- End .col-lg-7 -->

        <div class="col-lg-6 col-md-6 px-5 py-5">
            <div class="product-single-details mt-0">
                @if ($record->category && $record->category->name)
                <span class="tag-ecommerce primary text-uppercase">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tag mr-1" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6.5 7.5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M3 6v5.172a2 2 0 0 0 .586 1.414l7.71 7.71a2.41 2.41 0 0 0 3.408 0l5.592 -5.592a2.41 2.41 0 0 0 0 -3.408l-7.71 -7.71a2 2 0 0 0 -1.414 -.586h-5.172a3 3 0 0 0 -3 3" /></svg>
                    {{$record->category->name}}
                </span>
                @endif

                <h1 class="product-title tony mt-1">{{$record->description}}</h1>

                @php
                    $oldPrice = $record->sale_unit_price * 1.2;
                    $savings = $oldPrice - $record->sale_unit_price;
                @endphp
                <div class="price-box preview d-flex align-items-end justify-content-start w-100 mb-1" style="gap: 10px">
                    <span class="product-price">{{ $record->currency_type['symbol'] }} {{ number_format($record->sale_unit_price, 2) }}</span>
                    <span class="old-price">{{ $record->currency_type['symbol'] }} {{ number_format($oldPrice, 2) }}</span>
                    <span class="tag-ecommerce warning">
                        Ahorras {{ $record->currency_type['symbol'] }} {{ number_format($savings, 2) }}
                    </span>
                </div><!-- End .price-box -->
                <div class="stock-row mb-1">
                    <?php
                    if($record->getStockByWarehouseMain() > 0){?>
                        <span class="stock-dot success"></span>
                        <span><b>En stock</b> · {{number_format(($record->getStockByWarehouseMain()), 0)}} unidades disponibles</span>
                    <?php
                    }else{?>
                        <span class="stock-dot danger"></span>
                        <span><b class="text-danger">Sin stock</b> · temporalmente agotado</span>
                    <?php
                    }
                    ?>
                </div>
                <div class="product-desc pb-4 mb-2">
                    <p class="mb-0">{!! $record->name !!}</p>
                </div><!-- End .product-desc -->



                <div class="product-action w-100 d-flex align-items-center justify-content-between" style="gap: 10px"
                    data-qv-scope
                    data-unit-price="{{ $record->sale_unit_price }}"
                    data-symbol="{{ $record->currency_type['symbol'] }}"
                    data-qv-product="{{ json_encode( $record ) }}">
                    @php $stockQv = $record->getStockByWarehouseMain(); @endphp
                    @if($stockQv > 0)
                    <div class="input-group input-group-sm modern-quantity-container w-auto">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary btn-input-group" type="button" onclick="qvStep(this, -1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                            </button>
                        </div>
                        <input class="input-quantity text-center qv-quantity" type="number" min="1" value="1"
                            oninput="qvUpdatePrice(qvScope(this))" onchange="qvSync(this)">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-input-group" type="button" onclick="qvStep(this, 1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            </button>
                        </div>
                    </div>

                    <a href="#" onclick="qvAddToCart(this); return false;" class="paction add-cart w-auto m-0"
                        title="Add to Cart" style="flex: 1; height: 39px; padding: 0 12px;">
                        <svg clip-rule="evenodd" fill-rule="evenodd" height="24" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg" id="fi_4893746"><path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path></svg>
                        <span class="font-weight-bold qv-add-label" style="white-space: nowrap;">
                            Agregar a Carrito · {{ $record->currency_type['symbol'] }} {{ number_format($record->sale_unit_price, 2) }}
                        </span>
                    </a>
                    @php
                        $showWhatsapp = ($configurationModel->enable_whatsapp ?? false) && !empty($phoneWhatsapp);
                    @endphp
                    @if($showWhatsapp)
                        @php
                            $waPhoneRaw = preg_replace('/\D+/', '', $phoneWhatsapp);
                            $waPhone = (strlen($waPhoneRaw) == 9 && str_starts_with($waPhoneRaw, '9')) ? '51'.$waPhoneRaw : $waPhoneRaw;
                            $waText = rawurlencode("Buenas, deseo consultar acerca del producto *{$record->description}*, con precio de {$record->currency_type['symbol']}{$record->sale_unit_price}. ¿Podrían brindarme más información?");
                            $waLink = "https://wa.me/{$waPhone}?text={$waText}";
                        @endphp
                        <a href="{{ $waLink }}" class="btn-whatsapp" target="_blank" rel="noopener" title="Consultar por WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp" style="margin-top: -3px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
                            <span>Consultar por WhatsApp</span>
                        </a>
                    @endif
                    @else
                    <div class="d-flex flex-column w-100" style="gap: 10px">
                        <button class="btn btn-disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mood-sad" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 10l.01 0" /><path d="M15 10l.01 0" /><path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" /></svg>
                            Agotado por ahora
                        </button>

                        <button type="button" class="btn btn-outline-primary" onclick="jQuery.magnificPopup.close()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            Seguir buscando
                        </button>
                    </div>
                    @endif

                </div><!-- End .product-action -->

            </div><!-- End .product-single-details -->
        </div><!-- End .col-lg-5 -->
    </div><!-- End .row -->
</div><!-- End .product-single-container -->

<style>
.price-box.preview{
    display: flex;
    flex-wrap: wrap; /* permite bajar los elementos completos */
    gap: 10px;
}

.price-box.preview .product-price,
.price-box.preview .old-price,
.price-box.preview .tag-ecommerce{
    white-space: nowrap; /* evita S/ arriba y el monto abajo */
}
@media (max-width: 576px){
    .price-box.preview{
        flex-direction: column;
        align-items: flex-start !important;
        gap: 4px;
    }
}
.product-action{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.modern-quantity-container{
    flex: 0 0 auto;
}

.paction.add-cart{
    flex: 1;
    min-width: 0;
}

.btn-whatsapp{
    flex: 1 1 100%;
}

@media (max-width: 768px){
    .product-action{
        flex-direction: column;
    }

    .modern-quantity-container,
    .paction.add-cart,
    .btn-whatsapp{
        width: 100%;
    }

    .modern-quantity-container{
        justify-content: center;
    }
}
</style>