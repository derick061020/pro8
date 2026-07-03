@extends('ecommerce::layouts.layout_ecommerce_item.record')

@section('content')

@php
    $configurationModel = \App\Models\Tenant\Configuration::first();
    $ecommerceConfiguration = \App\Models\Tenant\ConfigurationEcommerce::first();
    $phoneWhatsapp = $ecommerceConfiguration->phone_whatsapp ?? $configurationModel->phone_whatsapp ?? null;
    $defaultImage = $configurationModel->product_default_image ?? 'imagen-no-disponible.jpg';
    $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
        ? asset('logo/imagen-no-disponible.jpg')
        : asset('storage/defaults/' . $defaultImage);
    $mainImagePath = ($record->image && $record->image !== 'imagen-no-disponible.jpg')
        ? asset('storage/uploads/items/'.$record->image)
        : $defaultImagePath;
@endphp

<div class="product-single-container product-single-default">
    <div class="row">
        <div class="col-lg-7 col-md-6 product-single-gallery">
            <div class="product-slider-container product-item">
                <div class="product-single-carousel owl-carousel owl-theme">
                    <div class="product-item">
                        <img class="product-single-image" src="{{ $mainImagePath }}"
                            data-zoom-image="{{ $mainImagePath }}" />
                            
                    </div>
                    @foreach($record->images as $row)

                        <div class="product-item">
                            @php
                                $loopImagePath = ($row->image && $row->image !== 'imagen-no-disponible.jpg')
                                    ? asset('storage/uploads/items/'.$row->image)
                                    : $defaultImagePath;
                            @endphp
                            <img class="product-single-image" src="{{ $loopImagePath }}"
                                 data-zoom-image="{{ $loopImagePath }}" alt="{{ $record->description }}" />
                        </div>

                    @endforeach
                    <!--<div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-2.jpg"
                            data-zoom-image="assets/images/products/zoom/product-2-big.jpg" />
                    </div>
                    <div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-3.jpg"
                            data-zoom-image="assets/images/products/zoom/product-3-big.jpg" />
                    </div>
                    <div class="product-item">
                        <img class="product-single-image" src="assets/images/products/zoom/product-4.jpg"
                            data-zoom-image="assets/images/products/zoom/product-4-big.jpg" />
                    </div>-->
                </div>
                <!-- End .product-single-carousel -->
                <span class="prod-full-screen">
                    <i class="icon-plus"></i>
                </span>
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
                    <img src="assets/images/products/zoom/product-2.jpg" />
                </div>
                <div class="col-3 owl-dot">
                    <img src="assets/images/products/zoom/product-3.jpg" />
                </div>
                <div class="col-3 owl-dot">
                    <img src="assets/images/products/zoom/product-4.jpg" />
                </div> -->
            </div>
        </div><!-- End .col-lg-7 -->

        <div class="col-lg-5 col-md-6">
            <div id="product-detail-vue" class="product-single-details">
                <h1 class="product-title mb-0">{{$record->description}}</h1>

                @php
                    // Genera rating random entre 4.1 y 4.9
                    $rating = ($record->id % 9 + 41) / 10;
                
                    // Convierte a porcentaje para pintar estrellas
                    $percentage = ($rating / 5) * 100;
                @endphp

                <div class="ratings-container d-flex align-items-center gap-2">

                    <!-- Estrellas -->
                    <div class="product-ratings position-relative" style="display:inline-block; line-height:1;">

                        <!-- Fondo gris -->
                        <div style="color:#ddd; font-size:22px;">
                            ★★★★★
                        </div>

                        <!-- Relleno amarillo -->
                        <div style="
                            position:absolute;
                            top:0;
                            left:0;
                            width:{{ $percentage }}%;
                            overflow:hidden;
                            white-space:nowrap;
                            color:#f4b400;
                            font-size:22px;">
                            ★★★★★
                        </div>
                    </div>

                    <!-- Puntaje -->
                    <span style="font-size:16px; font-weight:500;" class="mt-1 ml-3">
                        {{ number_format($rating, 1) }}/5
                    </span>

                </div>

                <div class="price-box">
                    <span class="old-price">{{ $record->currency_type['symbol'] }} {{ number_format( ($record->sale_unit_price * 1.2 ) , 2 ) }}</span>
                    <span class="product-price">{{ $record->currency_type['symbol'] }} {{ number_format($record->sale_unit_price, 2) }}</span>
                </div><!-- End .price-box -->

                <div class="product-desc pb-0">
                    @if ($record->category && $record->category->name)
                        <p class="product-category">Categoría: <span> {{$record->category->name}} </span></p>
                    @endif
                <p class="product-stock">Disponible: <span>{{number_format(($record->stock), 0)}} </span>
                <?php
                if($record->stock > 0){?>
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
                <div class="product-description-wrapper">
                    <div id="productShortDescription" class="product-description-clamp">
                        {!! $record->name !!}
                    </div>

                    <a href="javascript:void(0);" 
                       id="toggleProductDescription" 
                       class="product-description-toggle"
                       style="display:none;">
                        Ver todo
                    </a>
                </div>
                </div><!-- End .product-desc -->

                <div>
                @foreach($record->attributes as $at)
                   <small> {{$at->description}}: {{$at->value}} </small> <br>
                @endforeach
                </div>

                <div class="product-action product-all-icons">
                    <div class="d-flex align-items-center gap-2">
                        <div class="quantity-container d-flex align-items-center mb-1">
                            <button v-if="quantity <= 1 && getCartQuantity(product.id)" @click.stop.prevent="removeFromCart(product)" title="Quitar del carrito">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                            <button 
                                @click.stop.prevent="decrementQuantity(product)" 
                                v-if="!getCartQuantity(product.id) || quantity > 1"
                                title="Disminuir cantidad">
                                
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-minus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l14 0" />
                                </svg>
                            </button>
                            <input type="number" class="input-quantity mx-2" v-model.number="quantity" min="1" style="width: 50px; text-align: center;" @change="onQuantityInput">
                            <button @click.stop.prevent="incrementQuantity(product)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            </button>
                        </div>
                        <button class="paction add-cart mb-1 mr-0 ml-2" @click.stop.prevent="addOrUpdateCart(product)">
                            <span v-if="getCartQuantity(product.id)">Actualizar cantidad</span>
                            <span v-else>Agregar a Carrito</span>
                        </button>
                    </div>
                    

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
                    
                    <!-- <a href="#" class="paction add-wishlist" title="Add to Wishlist">
                        <span>Add to Wishlist</span>
                    </a>
                    <a href="#" class="paction add-compare" title="Add to Compare">
                        <span>Add to Compare</span>
                    </a> -->
                </div><!-- End .product-action -->

                <div class="product-single-share">
                    <!--<label>Share:</label> -->
                    <!-- www.addthis.com share plugin-->
                    <div class="addthis_inline_share_toolbox"></div>
                </div><!-- End .product single-share -->
            </div><!-- End .product-single-details -->
        </div><!-- End .col-lg-5 -->
    </div><!-- End .row -->
</div><!-- End .product-single-container -->

<div class="product-single-tabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active"  id="product-tab-desc" data-toggle="tab" href="#product-desc-content" role="tab"
                aria-controls="product-desc-content" aria-selected="true">Descripcion</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" onclick="getRating('{{ $record->id}}')" id="product-tab-reviews" data-toggle="tab" href="#product-reviews-content" role="tab"
                aria-controls="product-reviews-content" aria-selected="false">Reviews</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="product-tab-especTecn" data-toggle="tab" href="#product-especTecn-content" role="tab" aria-controls="product-especTecn-content" aria-selected="true">Especificaciones Técnicas</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
            aria-labelledby="product-tab-desc">
            <div class="product-desc-content">
                <p> {{ $record->description}} </p>
                <p> {!! $record->name !!} </p>
            </div><!-- End .product-desc-content -->
        </div><!-- End .tab-pane -->

        <div class="tab-pane fade" id="product-reviews-content" role="tabpanel" aria-labelledby="product-tab-reviews">
            <div class="product-reviews-content">
                <div class="collateral-box">

                    <div class="page">
                        <div class="page__demo">

                            <div class="page__group">
                                <div class="rating">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc6" onclick="sendRating(1,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc7" onclick="sendRating(2,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc8" onclick="sendRating(3,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc9" onclick="sendRating(4,{{$record->id}})">
                                    <input type="radio" name="rating-star2" class="rating__control" id="rc10" onclick="sendRating(5,{{$record->id}})" >
                                    <label for="rc6" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">1</span>
                                    </label>
                                    <label for="rc7" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">2</span>
                                    </label>
                                    <label for="rc8" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">3</span>
                                    </label>
                                    <label for="rc9" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">4</span>
                                    </label>
                                    <label for="rc10" class="rating__item">
                                        <svg class="rating__star">
                                            <use xlink:href="#star"></use>
                                        </svg>
                                        <span class="rating__label">5</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" style="display: none">
                        <symbol id="star" viewBox="0 0 26 28">
                            <path
                                d="M26 10.109c0 .281-.203.547-.406.75l-5.672 5.531 1.344 7.812c.016.109.016.203.016.313 0 .406-.187.781-.641.781a1.27 1.27 0 0 1-.625-.187L13 21.422l-7.016 3.687c-.203.109-.406.187-.625.187-.453 0-.656-.375-.656-.781 0-.109.016-.203.031-.313l1.344-7.812L.39 10.859c-.187-.203-.391-.469-.391-.75 0-.469.484-.656.875-.719l7.844-1.141 3.516-7.109c.141-.297.406-.641.766-.641s.625.344.766.641l3.516 7.109 7.844 1.141c.375.063.875.25.875.719z" />
                        </symbol>
                    </svg>

                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="product-especTecn-content" role="tabpanel" aria-labelledby="product-tab-especTecn">
            <div class="product-especTecn-content">
                <p> {!! $record->technical_specifications !!} </p>
            </div><!-- End .product-desc-content -->
        </div><!-- End .tab-pane -->
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('product-detail-vue')) {
        new Vue({
            el: '#product-detail-vue',
            data: {
                product: {
                    id: {{ $record->id }},
                    description: @json($record->description),
                    sale_unit_price: {{ $record->sale_unit_price }},
                    sale_unit_price_display: @json($record->sale_unit_price),
                    image_small: @json($record->image ?? 'imagen-no-disponible.jpg'),
                    image: @json($record->image ?? 'imagen-no-disponible.jpg'),
                    sale_affectation_igv_type_id: @json($record->sale_affectation_igv_type_id ?? '10'),
                    currency_type_id: @json($record->currency_type_id ?? 'PEN'),
                    currency_type_symbol: @json($record->currency_type['symbol'] ?? 'S/'),
                    unit_type_id: @json($record->unit_type_id ?? 'NIU'),
                    internal_id: @json($record->internal_id ?? ''),
                },
                cartQuantities: {},
                quantity: 1,
            },
            created() {
                this.loadCartQuantities();
                window.addEventListener('productAddedToCart', this.loadCartQuantities);
            },
            watch: {
                cartQuantities: {
                    handler(val) {
                        if (this.getCartQuantity(this.product.id)) {
                            this.quantity = this.getCartQuantity(this.product.id);
                        } else {
                            this.quantity = 1;
                        }
                    },
                    deep: true
                }
            },
            methods: {
                addOrUpdateCart(item) {
                    let array = localStorage.getItem('products_cart');
                    array = array ? JSON.parse(array) : [];
                    let found = array.find(x => x.id == item.id);
                    if (found) {
                        found.quantity = this.quantity;
                    } else {
                        array.push({
                            ...item,
                            quantity: this.quantity
                        });
                    }
                    localStorage.setItem('products_cart', JSON.stringify(array));
                    this.cartQuantities = Object.assign({}, this.cartQuantities, { [item.id]: this.quantity });
                    window.dispatchEvent(new Event('productAddedToCart'));
                },
                getCartQuantity(id) {
                    return this.cartQuantities[id] || 0;
                },
                onQuantityInput() {
                    if (this.quantity < 1) this.quantity = 1;
                },
                loadCartQuantities() {
                    let array = localStorage.getItem('products_cart');
                    array = array ? JSON.parse(array) : [];
                    let obj = {};
                    array.forEach(function(item) {
                        obj[item.id] = item.quantity || 1;
                    });
                    this.cartQuantities = obj;
                },
                incrementQuantity(item) {
                    this.quantity++;
                },
                decrementQuantity(item) {
                    if (this.quantity > 1) this.quantity--;
                },
                removeFromCart(item) {
                    let array = localStorage.getItem('products_cart');
                    array = array ? JSON.parse(array) : [];
                    array = array.filter(x => x.id != item.id);
                    localStorage.setItem('products_cart', JSON.stringify(array));
                    this.$set(this.cartQuantities, item.id, 0);
                    window.dispatchEvent(new Event('productAddedToCart'));
                }
            }
        });
    }

    const description = document.getElementById('productShortDescription');
    const toggleBtn = document.getElementById('toggleProductDescription');

    if (description && toggleBtn) {
        const isOverflowing = description.scrollHeight > description.clientHeight + 2;

        if (isOverflowing) {
            toggleBtn.style.display = 'inline-block';
        }

        toggleBtn.addEventListener('click', function() {
            description.classList.toggle('expanded');

            if (description.classList.contains('expanded')) {
                toggleBtn.textContent = 'Ver menos';
            } else {
                toggleBtn.textContent = 'Ver todo';
            }
        });
    }
});
</script>
