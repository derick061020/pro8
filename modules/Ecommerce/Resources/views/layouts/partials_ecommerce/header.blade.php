
<style>
#header_bar .header-menu {
    max-height: 300px !important;
    overflow:auto;
    overflow-y: auto;
}

#header_bar .header-menu::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.1);
    background-color: #fdfdfd;
}

#header_bar .header-menu::-webkit-scrollbar {
    width: 6px;
    background-color: #fdfdfd;
}

#header_bar .header-menu::-webkit-scrollbar-thumb {
    background-color: #0187cc;
}

.header-dropdown a img {
    border-radius: 8px;
    padding: 4px;
}

@media (max-width: 768px) {
    .header-dropdown {
        min-width: 100px !important;
    }
}

.header-menu ul a {
    padding: 3px 6px;
}

.header-menu {
    box-shadow: 0 0 2px rgba(0,0,0,0.1);
    padding: 0 !important;
    border: none;
    opacity: 1;
    visibility: visible;
}

.header-menu a:hover, .header-menu a:focus {
    color: #0187cc;
    background-color: #f4f4f4;
}

.header-menu ul a {
    text-transform: capitalize !important;
}

.search_input {
    margin-bottom: 0.1rem;
    border-radius: 20px !important;
}

.search_input:focus {
    background-color: #fff;
    border-color: #fff;
    box-shadow: none;
}

.header-contact span {
    font-weight: normal;
}

div.cart-dropdown {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: transparent;
}

.header .dropdown-toggle {
    color: #fff;
    font-size: 10px;
    background-color: #1f1f39;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    padding: 0 10px;
}

.dropdown-toggle .cart-count {
    background-color: transparent !important;
    color: white !important;
    margin-top: 12px;
    margin-right: 27px;
}

.search_input:focus {
    background-color: transparent !important;
}

.search_input {
    width: 100%;
    height: 38px !important;
    border-radius: 20px !important;
    background-color: #eff0f6 !important;
    max-width: 100% !important;
}

.header-dropdown-inside {
    position: relative;
}

.header-dropdown-inside .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.header-dropdown-inside .search_input {
    padding-left: 40px !important;
    padding-right: 40px !important;
    width: 100%;
}

.header-dropdown-inside .clear-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    cursor: pointer;
    display: none;
}
.header-dropdown-inside input:focus + .clear-icon,
.header-dropdown-inside input:not(:placeholder-shown) + .clear-icon {
    display: inline-block; /* Muestra el ícono */
}
/* Overlay oscuro */
#search-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 14;
}
#search-overlay.active {
    display: block;
}
.header-search-wrapper.active {
    display: block;
}

/* Ajuste del header-dropdown dentro del wrapper */
.header-search-wrapper .header-dropdown {
    width: 100%;
    background: #fff;
    border-radius: 20px;
    padding: 0;
}

/* Menú de resultados */
.header-search-wrapper .header-menu {
    background: #fff;
    border-radius: 12px;
    margin-top: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    height: auto;
}

/* Ícono de búsqueda en el botón - cursor pointer */
.btn-search-icon {
    cursor: pointer;
}
/* Forzar que el buscador y overlay estén sobre TODO, incluido el sticky header */
.header-search-wrapper {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100vw - 100px);
    max-width: 700px !important;
    max-width: 90vw;
    display: none;
    z-index: 15;
}
</style>
<div id="search-overlay"></div>
<div class="header-search-wrapper" id="search-wrapper">
    <div class="header-dropdown header-dropdown-inside">
        <img src="{{ asset('images/search.svg') }}" alt="search" class="search-icon">
        <input
            placeholder="Buscar..."
            type="text"
            class="search_input form-control form-control-lg"
            id="search-input-field"
            v-model="value"
            v-on:keyup="autoComplete"
            @focus="isFocused = true"
            @blur="isFocused = false"
        />
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x clear-icon" @click="clearInput"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
    </div>

    {{-- Mostrar si hay resultados y el input está activo o tiene texto --}}
    <div class="header-menu results-container" v-show="results.length > 0 && (isFocused || value.length > 0)">
        <ul class="p-4">
            <span class="">Productos sugeridos</span>
            <div class="suggestions-list">
                <li class="mt-2 py-2" v-for="result in results">
                    <div class="row mx-0">
                        <a :href="'/ecommerce/item/' + result.slug" class="d-flex col-7 px-0" @click="suggestionClick(result)">
                            <img style="max-width: 80px" class="img-product-results" :src="result.image_url_small" alt="">
                            <span class="search_title d-flex align-items-end ml-3" style="font-size: 1.0em;"> @{{ result.description }} </span>
                        </a>
                        <div class="col-5 px-0 d-flex justify-content-between align-items-center">
                            <span>@{{ result.sale_unit_price }}</span>
                            <div>
                                <button class="btn-add-cart btn-success" v-if="!getCartQuantity(result.id)" @click.stop.prevent="addToCart(result)">
                                    Agregar al carrito
                                </button>
                                <div class="quantity-container d-flex align-items-center" v-if="getCartQuantity(result.id)">
                                    <button v-if="cartQuantities[result.id] <= 1" @click.stop.prevent="removeFromCart(result)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                    </button>
                                    <button v-if="cartQuantities[result.id] > 1" @click.stop.prevent="decrementQuantity(result)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                                    </button>
                                    <input type="number" class="input-quantity mx-2" v-model.number="cartQuantities[result.id]" min="1" style="width: 50px; text-align: center;" @change="updateQuantity(result)">
                                    <button @click.stop.prevent="incrementQuantity(result)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </div>
        </ul>
    </div>
    <div class="header-menu results-container" v-show="value.length > 0 && results.length === 0 && isFocused">
        <div style="padding: 16px; text-align: center; color: #888;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block; margin: 0 auto 8px;">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                <path d="M21 21l-6 -6"/>
            </svg>
            <span style="font-size: 0.9em;">No se encontraron resultados para <strong>"@{{ value }}"</strong></span>
        </div>
    </div>
</div>
<header class="header">
    <div class="header-middle">
        <div class="container">
            <div class="header-left">
                <a href="{{ route("tenant.ecommerce.index") }}" class="logo" style="max-width: 180px">
                    @php($headerLogo = data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo'))
                    @if($headerLogo)
                        <img src="{{ asset('storage/uploads/logos/'.$headerLogo) }}" alt="Logo" />
                    @else
                        <img src="{{asset('logo/tulogo.png')}}" alt="Logo" />
                    @endif
                </a>
            </div>

            <div class="category-dropdown" id="category-dropdown">
                <button type="button" class="category-dropdown-toggle" id="category-toggle" @if(count($categories) == 0) style="cursor: default; pointer-events: none;" @endif>
                    <span class="category-toggle-left">
                        <span>Categorías</span>
                    </span>

                    @if(count($categories) > 0)
                    <svg class="category-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6 9l6 6l6 -6" />
                    </svg>
                    @endif
                </button>

                @if(count($categories) > 0)
                <div class="category-menu" id="category-menu">
                    <div class="category-menu-grid">
                        @foreach ($categories as $category)
                        <a href="{{ route('tenant.ecommerce.category', \Illuminate\Support\Str::slug($category->name, '-')) }}" class="category-item">
                            <div class="category-item-icon">
                                @if($category->image && file_exists(public_path('storage/uploads/categories/'. $category->image)))
                                    <img src="{{ asset('storage/uploads/categories/'. $category->image) }}"
                                         alt="{{ $category->name }}"
                                         style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                @else
                                    <img src="{{ asset('logo/Image_not_available.png') }}"
                                         alt="{{ $category->name }}"
                                         style="width:40px; height:40px; object-fit:cover; border-radius:8px;">
                                @endif
                            </div>
                            <div class="category-item-content">
                                <span class="category-item-title">{{ $category->name }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="customlinks" style="display: flex !important; align-items: center; flex-shrink: 0; white-space: nowrap; margin-right: 15px !important;">
                @if(!empty($customLinks['title_one']) && !empty($customLinks['link_one']))
                    <a href="{{ $customLinks['link_one'] }}" style="display: inline-block !important; font-weight: 500; font-size: 14px; color: var(--title-color); text-decoration: none; margin-left: 12px; white-space: nowrap;" target="_blank">{{ $customLinks['title_one'] }}</a>
                @endif
                @if(!empty($customLinks['title_two']) && !empty($customLinks['link_two']))
                    <a href="{{ $customLinks['link_two'] }}" style="display: inline-block !important; font-weight: 500; font-size: 14px; color: var(--title-color); text-decoration: none; margin-left: 12px; white-space: nowrap;" target="_blank">{{ $customLinks['title_two'] }}</a>
                @endif
                @if(!empty($customLinks['title_three']) && !empty($customLinks['link_three']))
                    <a href="{{ $customLinks['link_three'] }}" style="display: inline-block !important; font-weight: 500; font-size: 14px; color: var(--title-color); text-decoration: none; margin-left: 12px; white-space: nowrap;" target="_blank">{{ $customLinks['title_three'] }}</a>
                @endif
            </div>

            <div id="header_bar" class="header-center header-dropdowns">

                <!-- Botón lupa -->
                <div class="mr-3 btn-search-icon" id="btn-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"/>
                        <path d="M21 21l-6 -6"/>
                    </svg>
                </div>

                <!-- Wrapper del buscador (posición absoluta centrada) -->

            </div>

            <div class="header-right">
                <button class="mobile-menu-toggler" type="button">
                    <i class="icon-menu"></i>
                </button>
                @include('ecommerce::layouts.partials_ecommerce.cart_dropdown')
                @include('ecommerce::partials.headers.session')
            </div>
        </div>
    </div>

    <div class="header-bottom sticky-header">
        <div class="container d-flex">
            <nav class="main-nav flex-grow-1"></nav>
        </div>
    </div>
</header>

<script>
(function() {

    document.body.addEventListener('click', function(e) {
        var overlay = document.getElementById('search-overlay');
        var wrapper = document.getElementById('search-wrapper');
        var isOpen  = wrapper && wrapper.classList.contains('active');

        if (e.target.closest('#btn-search-icon')) {
            if (isOpen) {
                wrapper.classList.remove('active');
                overlay.classList.remove('active');
            } else {
                wrapper.classList.add('active');
                overlay.classList.add('active');
                var input = wrapper.querySelector('input');
                if (input) setTimeout(function(){ input.focus(); }, 80);
            }
            return;
        }

        if (e.target.id === 'search-overlay') {
            wrapper.classList.remove('active');
            overlay.classList.remove('active');
            return;
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var overlay = document.getElementById('search-overlay');
            var wrapper = document.getElementById('search-wrapper');
            if (wrapper) wrapper.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
        }
    });

    function waitForVue(cb) {
        if (typeof Vue !== 'undefined') return cb();
        setTimeout(function(){ waitForVue(cb); }, 100);
    }

    (function() {
        var categoryDropdown = document.getElementById('category-dropdown');
        var categoryToggle = document.getElementById('category-toggle');

        if (categoryDropdown && categoryToggle) {
            categoryToggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    e.stopPropagation();
                    categoryDropdown.classList.toggle('active');
                }
            });

            document.addEventListener('click', function(e) {
                if (!categoryDropdown.contains(e.target)) {
                    categoryDropdown.classList.remove('active');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    categoryDropdown.classList.remove('active');
                }
            });
        }
    })();

    waitForVue(function() {

        // ✅ Vue montado en #search-wrapper, no en #header_bar
        if (document.getElementById('search-wrapper')) {
            new Vue({
                el: '#search-wrapper',
                data: {
                    value: '',
                    isFocused: false,
                    suggestions: [],
                    resource: 'ecommerce',
                    results: [],
                    cartQuantities: {},
                },
                created() {
                    this.getItems();
                    this.loadCartQuantities();
                    window.addEventListener('productAddedToCart', this.loadCartQuantities);
                },
                methods: {
                    clearInput() {
                        this.value = '';
                        this.results = [];
                    },
                    autoComplete() {
                        if (this.value) {
                            var val = this.value.toUpperCase();
                            this.results = this.suggestions.filter(function(obj) {
                                var desc = obj.description.toUpperCase();
                                var id = obj.internal_id ? obj.internal_id.toUpperCase() : '';
                                return desc.includes(val) || id.includes(val);
                            });
                        } else {
                            this.results = [];
                        }
                    },
                    getItems() {
                        var ctx = this;
                        fetch('/' + this.resource + '/items_bar')
                            .then(function(r){ return r.json(); })
                            .then(function(json){ ctx.suggestions = json.data; });
                    },
                    suggestionClick(item) {
                        this.results = [];
                        this.value = item.description;
                    },
                    addToCart(item) {
                        let array = localStorage.getItem('products_cart');
                        array = array ? JSON.parse(array) : [];
                        if (!array.some(x => x.id == item.id)) {
                            var imageSmall = 'imagen-no-disponible.jpg';
                            if (item.image_url_small) {
                                imageSmall = item.image_url_small.split(/[\\/]/).pop();
                            }

                            let priceClean = item.sale_unit_price;
                            if (typeof priceClean === 'string') {
                                priceClean = priceClean.replace(/[^\d.,-]/g, '').replace(',', '.');
                            }
                            priceClean = parseFloat(priceClean) || 0;

                            array.push({
                                id: item.id,
                                description: item.description,
                                sale_unit_price: priceClean,
                                sale_unit_price_display: item.sale_unit_price,
                                image_small: imageSmall,
                                image: imageSmall,
                                // campos que necesita detail.blade.php
                                sale_affectation_igv_type_id: item.sale_affectation_igv_type_id || '10',
                                currency_type_id: item.currency_type_id || 'PEN',
                                currency_type_symbol: item.currency_type_symbol || 'S/',
                                unit_type_id: item.unit_type_id || 'NIU',
                                internal_id: item.internal_id || '',
                                quantity: 1
                            });

                            localStorage.setItem('products_cart', JSON.stringify(array));
                            this.cartQuantities = Object.assign({}, this.cartQuantities, { [item.id]: 1 });
                            window.dispatchEvent(new Event('productAddedToCart'));
                        }
                    },
                    getCartQuantity(id) {
                        return this.cartQuantities[id] || 0;
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
                        let array = localStorage.getItem('products_cart');
                        array = array ? JSON.parse(array) : [];
                        let found = array.find(x => x.id == item.id);
                        if (found) {
                            found.quantity = (found.quantity || 1) + 1;
                            localStorage.setItem('products_cart', JSON.stringify(array));
                            this.cartQuantities[item.id] = found.quantity;
                            window.dispatchEvent(new Event('productAddedToCart'));
                        }
                    },
                    decrementQuantity(item) {
                        let array = localStorage.getItem('products_cart');
                        array = array ? JSON.parse(array) : [];
                        let found = array.find(x => x.id == item.id);
                        if (found && found.quantity > 1) {
                            found.quantity--;
                            localStorage.setItem('products_cart', JSON.stringify(array));
                            this.cartQuantities[item.id] = found.quantity;
                            window.dispatchEvent(new Event('productAddedToCart'));
                        } else if (found && found.quantity <= 1) {
                            this.removeFromCart(item);
                        }
                    },
                    updateQuantity(item) {
                        let qty = parseInt(this.cartQuantities[item.id]) || 1;
                        if (qty < 1) qty = 1;
                        let array = localStorage.getItem('products_cart');
                        array = array ? JSON.parse(array) : [];
                        let found = array.find(x => x.id == item.id);
                        if (found) {
                            found.quantity = qty;
                            localStorage.setItem('products_cart', JSON.stringify(array));
                            this.cartQuantities[item.id] = qty;
                            window.dispatchEvent(new Event('productAddedToCart'));
                        }
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

    });

})();
</script>
