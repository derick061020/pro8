<!DOCTYPE html>
<html lang="es">

<!-- Mirrored from portotheme.com/html/porto_ecommerce/demo-6/ by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 07 Sep 2019 03:39:38 GMT -->

<head>
    @php($pageCompany = $company ?? $vc_company ?? null)
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="Pedidos, Menu, Restaurante" />
    <meta name="description" content="{{ $restaurantDescription ?? 'Restaurante' }}">
    <meta name="author" content="SW-THEMES">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta property="og:description" content="{{ $restaurantDescription ?? 'Restaurante' }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    @php($headerLogo = data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo'))
    <meta property="og:image" content="{{ $headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png') }}" />
    <meta property="og:site_name" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}" />
    <meta name="twitter:description" content="{{ $restaurantDescription ?? 'Restaurante' }}" />
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

<body data-company-title="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}">

    <div class="page-wrapper">

        @include('restaurant::layouts.partials.header')
        <main class="main">
            @yield('content')
        </main><!-- End .main -->

        <footer class="footer">
            @include('restaurant::layouts.partials.footer')
        </footer><!-- End .footer -->
    </div><!-- End .page-wrapper -->

    <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

    <div class="mobile-menu-container">

        @include('restaurant::layouts.partials.mobile_menu')

    </div><!-- End .mobile-menu-container -->

    <div class="newsletter-popup mfp-hide" id="newsletter-popup-form">
        <!-- style="background-image: url(assets/images/newsletter_popup_bg.jpg)" -->
        <div class="newsletter-popup-content">
            <img src="{{ asset('porto-ecommerce/assets/images/logo-black.png') }}" alt="Logo" class="logo-newsletter">
            <h2>BE THE FIRST TO KNOW</h2>
            <p>Subscribe to the Porto eCommerce newsletter to receive timely updates from your favorite products.</p>
            <form action="#">
                <div class="input-group">
                    <input type="email" class="form-control" id="newsletter-email" name="newsletter-email"
                        placeholder="Email address" required>
                    <input type="submit" class="btn" value="Go!">
                </div><!-- End .from-group -->
            </form>
            <div class="newsletter-subscribe">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1">
                        Don't show this popup again
                    </label>
                </div>
            </div>
        </div><!-- End .newsletter-popup-content -->
    </div><!-- End .newsletter-popup -->

    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

    <!-- Plugins JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/plugins.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('porto-ecommerce/assets/js/cart.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/main.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/vue.min.js') }}"></script>

    @stack('scripts')
</body>
</html>

