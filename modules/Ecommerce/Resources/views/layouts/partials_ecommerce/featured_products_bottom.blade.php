@php

$currentItem = $record ?? null;
$currentItemId = data_get($currentItem, 'id');
$currentCategoryId = data_get($currentItem, 'category.id') ?? data_get($currentItem, 'category_id');

$relatedItems = collect($items ?? [])->filter(function ($item) use ($currentItemId, $currentCategoryId) {
    return $currentCategoryId
        && data_get($item, 'category_id') == $currentCategoryId
        && data_get($item, 'id') != $currentItemId;
});

$configuration = \App\Models\Tenant\Configuration::first();
$defaultImage = $configuration->product_default_image ?? 'imagen-no-disponible.jpg';
$defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
    ? asset('logo/imagen-no-disponible.jpg')
    : asset('storage/defaults/' . $defaultImage);

@endphp
@if($relatedItems->isNotEmpty())
<div class="container">
    <h2 class="carousel-title">Productos Relacionados</h2>

    <div class="featured-products owl-carousel owl-theme owl-dots-top">

        @foreach ($relatedItems as $item)
                    <div class="product product-style d-flex align-items-center justify-content-start compact-product">
                        <figure class="product-image-container" style="flex-shrink: 0;">
                            <a href="/ecommerce/item/{{ $item->id }}/{{ \Illuminate\Support\Str::slug($item->description) }}" class="product-image">
                                @php
                                    $itemImagePath = ($item->image && $item->image !== 'imagen-no-disponible.jpg')
                                        ? asset('storage/uploads/items/'.$item->image)
                                        : $defaultImagePath;
                                @endphp
                                <img src="{{ $itemImagePath }}" alt="{{ $item->description }}">
                            </a>
                            {{-- <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a> --}}
                        </figure>
                        <div class="product-details d-flex align-items-center justify-content-between" style="flex: 1 1 auto; min-width: 0;">
                            <h2 class="product-title-ecommerce m-0 d-flex flex-column justify-content-start h-auto" style="gap: 4px; min-width: 0; flex: 1 1 auto;">
                                <a class="text-left d-block" href="/ecommerce/item/{{ $item->id }}/{{ \Illuminate\Support\Str::slug($item->description) }}" title="{{ $item->description }}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">{{$item->description}}</a>
                                @if($item->name)
                                    <span class="text-muted product-description text-left">{{ strip_tags($item->name) }}</span>
                                @else
                                    <span class="text-muted product-description text-left" style="opacity: .5">Sin descripción disponible.</span>
                                @endif
                            </h2>
                            <div class="d-flex flex-column ml-3" style="gap: 10px; flex-shrink: 0;">
                                <div class="price-box w-100 d-flex flex-column align-items-end m-0">
                                    <span class="text-muted" style="font-size: 13px; text-decoration: line-through;">S/ {{ number_format(round($item->sale_unit * 1.25), 2) }}</span>
                                    
                                    <span class="product-price text-nowrap m-0 font-weight-bold" style="font-size: 15px">{{ $item->currency_type_symbol }} {{ number_format($item->sale_unit, 2) }}</span>
                                </div><!-- End .price-box -->

                                <div class="product-action text-right">
                                    <!--<a href="#" class="paction add-wishlist" title="Add to Wishlist">
                                                <span>Add to Wishlist</span>
                                            </a>-->

                                    <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                                        <svg clip-rule="evenodd" fill-rule="evenodd" width="22" height="22" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" id="fi_4893746"><path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path></svg>
                                        <span>Agregar a Carrito</span>
                                    </a>

                                    <!--<a href="#" class="paction add-compare" title="Add to Compare">
                                                <span>Add to Compare</span>
                                            </a>-->
                                </div><!-- End .product-action -->
                            </div>
                        </div><!-- End .product-details -->
                    </div>

        @endforeach

    </div>
</div>
@endif
