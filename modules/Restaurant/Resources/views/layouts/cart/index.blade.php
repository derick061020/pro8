<!DOCTYPE html>
<html lang="es">

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:40:04 GMT -->
<head>
    @php($pageCompany = $company ?? $vc_company ?? null)
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') ?: 'Restaurante' }}</title>

    <meta name="keywords" content="Pedidos, Menu, Restaurante" />
    <meta name="description" content="Disfruta lo mejor de la gastronomía en {{ data_get($pageCompany, 'trade_name') }}. Descubre una amplia variedad de platos deliciosos, ingredientes de calidad y atención excepcional, con pedidos fáciles y servicio rápido y confiable.">
    <meta name="author" content="SW-THEMES">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta property="og:description" content="Disfruta lo mejor de la gastronomía en {{ data_get($pageCompany, 'trade_name') }}. Descubre una amplia variedad de platos deliciosos, ingredientes de calidad y atención excepcional, con pedidos fáciles y servicio rápido y confiable." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @php($headerLogo = data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo'))
    <meta property="og:image" content="{{ $headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png') }}" />
    <meta property="og:site_name" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta name="twitter:description" content="Disfruta lo mejor de la gastronomía en {{ data_get($pageCompany, 'trade_name') }}. Descubre una amplia variedad de platos deliciosos, ingredientes de calidad y atención excepcional, con pedidos fáciles y servicio rápido y confiable." />
    <meta name="twitter:image" content="{{ $headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png') }}" />

    <!-- Schema.org JSON-LD (ItemList para listado de productos) -->
    @if(isset($products) && count($products))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "name": "Listado de productos",
        "itemListElement": [
            @foreach($products as $index => $product)
            {
                "@type": "Product",
                "position": {{ $index + 1 }},
                "name": "{{ addslashes($product->name) }}",
                "image": "{{ $product->image_url ?? ($headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png')) }}",
                "url": "{{ route('ecommerce.product.show', $product->slug) }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
    @endif

     <!-- Favicon -->
     <link rel="icon" type="image/x-icon" href="{{ asset('porto-ecommerce/assets/images/icons/favicon.ico') }}">

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/bootstrap.min.css') }}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/custom.css') }}">

    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/rating.css') }}">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/font-awesome/css/fontawesome-all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('porto-light/css/styles_orders.css') }}" />
</head>
<body>
    <div class="page-wrapper">
        @include('restaurant::layouts.partials.header')
        @include('ecommerce::layouts.partials_ecommerce.header_bottom_sticky')
        <main class="main">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('ecommerce')}}"><i class="icon-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                    </ol>
                </div><!-- End .container -->
            </nav>

            <div class="container">
                 @yield('content')
            </div><!-- End .container -->

            <div class="mb-6"></div><!-- margin -->
        </main><!-- End .main -->

        <footer class="footer">
            @include('restaurant::layouts.partials.footer')
        </footer><!-- End .footer -->
    </div><!-- End .page-wrapper -->

    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <div class="mobile-menu-container">
        @include('ecommerce::layouts.partials_ecommerce.mobile_menu')
    </div><!-- End .mobile-menu-container -->



    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

     <!-- Plugins JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/culqi_v3.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/moment.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/main.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/vue.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/axios.min.js') }}"></script>

    @stack('scripts')
</body>

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:40:04 GMT -->
</html>
