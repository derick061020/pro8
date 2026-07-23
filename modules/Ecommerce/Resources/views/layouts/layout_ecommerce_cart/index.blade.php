<!DOCTYPE html>
<html lang="es">

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:40:04 GMT -->
<head>
    @php($pageCompany = $company ?? $vc_company ?? null)
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') ?: 'eCommerce' }}</title>

    <meta name="keywords" content="eCommerce, {{ data_get($pageCompany, 'trade_name') }}" />
    <meta name="description" content="{{ $ecommerceDescription ?? 'eCommerce' }}" />
    <meta name="author" content="SW-THEMES">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta property="og:description" content="{{ $ecommerceDescription ?? 'eCommerce' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @php($headerLogo = data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo'))
    <meta property="og:image" content="{{ $headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png') }}" />
    <meta property="og:site_name" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta name="twitter:description" content="{{ $ecommerceDescription ?? 'eCommerce' }}" />
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
    <link rel="icon" type="image/x-icon" href="{{ asset('porto-ecommerce/assets/images/icons/favicon.svg') }}">

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/bootstrap.min.css') }}">

    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/custom.css') }}">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/font-awesome/css/fontawesome-all.min.css') }}">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('porto-light/css/styles_ecommerce.css') }}" />
    @include('ecommerce::layouts.partials_ecommerce.primary_color_style')

    <!-- Element UI CSS -->
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">

    @stack('styles')

</head>
<body>
    @include('ecommerce::layouts.partials_ecommerce.announcement_bar')
    <div class="page-wrapper">
        @include('ecommerce::layouts.partials_ecommerce.header')
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
            @include('ecommerce::layouts.partials_ecommerce.footer')
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

    <!-- Element UI JavaScript -->
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <!-- Element UI Spanish Locale -->
    <script src="https://unpkg.com/element-ui/lib/umd/locale/es.js"></script>

    @stack('scripts')
</body>

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/cart.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:40:04 GMT -->
</html>
