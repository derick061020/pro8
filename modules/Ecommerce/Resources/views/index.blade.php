@extends('ecommerce::layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 ecommerce-view" style="{{ isset($full_width_banner) && $full_width_banner ? 'padding-top: 60px' : 'padding-top: 8rem' }}">
            @php
                $tagid = Request::segment(3);
            @endphp

            @if(!$tagid && !isset($category))
                @include('ecommerce::layouts.partials_ecommerce.home_slider')
            @endif
            @if(isset($category))
                <div class="row">
                    <div class="col-12 text-center py-5">
                        {{-- Usamos su clase oficial, pero le añadimos un estilo local solo para el tamaño --}}
                        <h1 class="title-category text-uppercase" style="font-size: 2.5rem; letter-spacing: 2px;">
                            {{ $category->name }}
                        </h1>
                    </div>
                </div>
            @endif
            <div class="row py-4 mx-0">
                @include('ecommerce::layouts.partials_ecommerce.categories')
            </div>
                {{-- @include('ecommerce::layouts.partials_ecommerce.featured_products') --}}
            <div class="row py-4">
                <div class="container d-flex justify-content-between align-items-center">
                    <h1 class="title-category m-0">Explora nuestros productos</h1>

                    <div class="filter-sort">
                        <select class="" onchange="location = this.value;" style="width: 200px;">
                            <option value="{{ request()->fullUrlWithQuery(['order' => 'name_asc']) }}" {{ request('order') == 'name_asc' ? 'selected' : '' }}>Nombre: A - Z</option>
                            <option value="{{ request()->fullUrlWithQuery(['order' => 'name_desc']) }}" {{ request('order') == 'name_desc' ? 'selected' : '' }}>Nombre: Z - A</option>
                            <option value="{{ request()->fullUrlWithQuery(['order' => 'price_asc']) }}" {{ request('order') == 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                            <option value="{{ request()->fullUrlWithQuery(['order' => 'price_desc']) }}" {{ request('order') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                        </select>
                    </div>
                </div>
            </div>
                <div class="row row-sm">
                @include('ecommerce::layouts.partials_ecommerce.list_products')
            </div>
            <div class="row row-sm mt-0">
            </div>
            <div class="row page-pagination mt-2">
              <div class="col-md-12 col-lg-12 d-flex justify-content-end mb-4">
                {{ $dataPaginate->onEachSide(1)->links('restaurant::layouts.partials.pagination') }}
              </div>
            </div>
            <div class="row">
                @include('ecommerce::layouts.partials_ecommerce.offers')
            </div>
        </div>
    </div>
</div>
@endsection
