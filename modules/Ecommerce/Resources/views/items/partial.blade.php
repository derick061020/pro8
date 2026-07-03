@php
    $configurationModel = \App\Models\Tenant\Configuration::first();
    $defaultImage = $configurationModel->product_default_image ?? 'imagen-no-disponible.jpg';
    $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
        ? asset('logo/imagen-no-disponible.jpg')
        : asset('storage/defaults/' . $defaultImage);
    $mainImagePath = ($record->image && $record->image !== 'imagen-no-disponible.jpg')
        ? asset('storage/uploads/items/'.$record->image)
        : $defaultImagePath;
@endphp
<div class="product-single-container product-single-default product-quick-view container position-relative">
    <div class="row">
        <div class="col-lg-6 col-md-6 product-single-gallery">
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
            <div class="prod-thumbnail row owl-dots" id='carousel-custom-dots'>
                <div class="col-3 owl-dot">
                    <img src="{{ $mainImagePath }}" alt="{{ $record->description }}" />
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
        </div><!-- End .col-lg-7 -->

        <div class="col-lg-6 col-md-6">
            <div class="product-single-details">
                <h1 class="product-title tony">{{$record->description}}</h1>

                <div class="ratings-container">
                    <div class="product-ratings">
                        <span class="ratings" style="width:60%"></span><!-- End .ratings -->
                    </div><!-- End .product-ratings -->

                    <a href="#" class="rating-link">( 6 Reviews )</a>
                </div><!-- End .product-container -->

                <div class="price-box">
                    <span class="old-price">{{ $record->currency_type['symbol'] }} {{ number_format( ($record->sale_unit_price * 1.2 ) , 2 ) }}</span>
                    <span class="product-price">{{ $record->currency_type['symbol'] }} {{ number_format($record->sale_unit_price, 2) }}</span>
                </div><!-- End .price-box -->
                <div class="product-desc">
                    @if ($record->category && $record->category->name)
                        <p class="product-category hsja">Categoría: <span> {{$record->category->name}} </span></p>
                    @endif
                    <p class="product-stock">Disponible: <span>{{number_format(($record->getStockByWarehouseMain()), 0)}} </span>
                    <?php
                    if($record->getStockByWarehouseMain() > 0){?>
                        <span
                        class="alert-stock" role="alert">En stock</span>
                    <?php
                    }else{?>
                        <span
                        class="alert-sin-stock"
                        role="alert">Sin stock</span>
                    <?php
                    }
                    ?>
                    </p>
                    <p>{!! $record->name !!}</p>
                </div><!-- End .product-desc -->



                <div class="product-action">
                    <!-- div class="product-single-qty">
                        <input class="horizontal-quantity form-control" type="text">
                    </div><!-- End .product-single-qty -->

                    <a href="#" onclick="cart_add('{{ json_encode( $record ) }}')" class="paction add-cart"
                        title="Add to Cart">
                        <svg clip-rule="evenodd" fill-rule="evenodd" height="24" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" width="24" xmlns="http://www.w3.org/2000/svg" id="fi_4893746"><path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path></svg>
                        <span>Agregar a Carrito</span>
                    </a>

                </div><!-- End .product-action -->

            </div><!-- End .product-single-details -->
        </div><!-- End .col-lg-5 -->
    </div><!-- End .row -->
</div><!-- End .product-single-container -->
