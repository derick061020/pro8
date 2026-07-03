@foreach ($dataPaginate as $item)
    @php
        $configuration = \App\Models\Tenant\Configuration::first();
        $defaultImage = $configuration->product_default_image ?? 'imagen-no-disponible.jpg';
        $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
            ? asset('logo/imagen-no-disponible.jpg')
            : asset('storage/defaults/' . $defaultImage);

        $imagePath = $item->image !== 'imagen-no-disponible.jpg'
            ? asset('storage/uploads/items/' . $item->image)
            : $defaultImagePath;
    @endphp
    <div class="col-6 {{ \Route::currentRouteName() == 'tenant.ecommerce.index' ? 'col-md-3' : 'col-md-4' }}">
        <div class="product product-style {{ stock($item, $configuration) ? 'productdisabled' : '' }}">
            <figure class="product-image-container product-image-container-ecommerce">

                <a href="/ecommerce/item/{{ $item->id }}-{{ \Illuminate\Support\Str::slug($item->description) }}" class="product-image product-image-list">
                    <img src="{{ $imagePath }}" class="image" alt="{{ $item->description }}">
                </a>
                <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-zoom-scan"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M4 8v-2a2 2 0 0 1 2 -2h2" /><path d="M4 16v2a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v2" /><path d="M16 20h2a2 2 0 0 0 2 -2v-2" /><path d="M8 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M16 16l-2.5 -2.5" /></svg> Vista Rápida</a>
                {{-- <span class="product-label label-sale">-20%</span> --}}
                @if(json_encode($item->is_new) == 1)
                    <span class="product-label label-hot">Nuevo</span>
                @endif
            </figure>
            <div class="product-details-ecommerce">
                <div class="ratings-container">
                    <div class="product-ratings">
                        <span class="ratings" style="width:0%"></span>
                    </div>
                </div>
                <div class="product-information">
                    <h2 class="product-title-ecommerce">
                        <a href="/ecommerce/item/{{ $item->id }}-{{ \Illuminate\Support\Str::slug($item->description) }}">{{ $item->description }}</a>
                    </h2>

                    @if(isset($preferences['show_description']) && $preferences['show_description'] == 1)
                        @if ($item->name)
                            <p class="text-muted product-description">
                                {{ strip_tags($item->name) }}
                            </p>
                        @else
                            <p class="text-muted product-description" style="opacity: .5">
                                Sin descripción disponible.
                            </p>
                        @endif
                    @endif

                    @if(isset($preferences['show_stock']) && $preferences['show_stock'] == 1)
                        @if($item->stock > 0)
                        <h3 class="product-stock">Disponible:
                            <span>{{ number_format($item->getStockByWarehouseMain(), 0) }}</span>
                        </h3>
                        @else
                        <h3 class="product-stock text-danger">
                            Sin stock
                        </h3>
                        @endif
                    @endif
                </div>
                <div class="product-price-ecommerce">
                    <div class="price-box-ecommerce">
                        <span class="old-price">S/ {{ number_format(round($item->sale_unit_price * 1.25), 2) }}</span>


                        <span class="product-price-ecommerce">{{ $item->currency_type['symbol'] }} {{ number_format($item->sale_unit_price, 2) }}</span>
                    </div>
                    <div class="product-action">
                        @if(stock($item, $configuration))
                    <span class="product-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-box-off"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M17.765 17.757l-5.765 3.243l-8 -4.5v-9l2.236 -1.258m2.57 -1.445l3.194 -1.797l8 4.5v8.5" /><path d="M14.561 10.559l5.439 -3.059" /><path d="M12 12v9" /><path d="M12 12l-8 -4.5" /><path d="M3 3l18 18" /></svg> Agotado
                    </span>
               @else
                        <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                            <svg clip-rule="evenodd" fill-rule="evenodd" width="22" height="22" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" id="fi_4893746"><path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path></svg>
                            <span>Agregar a Carrito</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endforeach

<?php

    function stock($item, $config)
    {
        if($config) {
            $stock=0;
            foreach ($item->warehouses as $key => $value) {
                $stock += $value->stock;
            }
            return ($stock > 0) ? false : true;
        }
    }
?>

<style>
    /* .product-style {
        border-style: solid;
        border-width: 1px;
        border-color: "#ddd";
        margin: 10px 1px;
    } */
    .product-image-list {
        max-height: 210px;
        min-height: 210px;
    }
    .image {
        max-height: 210px;
    }
    .productdisabled
    {
        /* pointer-events: none; */
        /* opacity: 0.7; */
    }
</style>
