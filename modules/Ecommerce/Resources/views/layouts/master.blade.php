<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @php
        $pageCompany = $company ?? $vc_company ?? null;
        $titleWeb = data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') ?: 'eCommerce';
        $headerLogo = data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo');
        $ecommerceDescription = $ecommerceDescription ?? 'eCommerce';
    @endphp
    <title>{{ $titleWeb }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="ecommerce, {{ $titleWeb }}" />
    <meta name="description" content="{{ $ecommerceDescription }}" />
    
    <meta property="og:title" content="{{ $titleWeb }}" />
    <meta property="og:description" content="{{ $ecommerceDescription }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ $headerLogo ? asset('storage/uploads/logos/'.$headerLogo) : asset('logo/tulogo.png') }}" />
    
    <link rel="icon" type="image/x-icon" href="{{ asset('porto-ecommerce/assets/images/icons/favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/css/rating.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-ecommerce/assets/font-awesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('porto-light/css/styles_ecommerce.css') }}" />
    @include('ecommerce::layouts.partials_ecommerce.primary_color_style')
</head>

<body data-company-title="{{ data_get($pageCompany, 'title_web') ?: data_get($pageCompany, 'trade_name') }}">

    @include('ecommerce::layouts.partials_ecommerce.announcement_bar')
    <?php
        
        $configurationModel = \App\Models\Tenant\Configuration::first();
        $ecommerceConfiguration = \App\Models\Tenant\ConfigurationEcommerce::first();
        $phoneWhatsapp = $ecommerceConfiguration->phone_whatsapp ?? $configurationModel->phone_whatsapp ?? null;
        $showWhatsapp = ($configurationModel && ($configurationModel->enable_whatsapp ?? false)) && !empty($phoneWhatsapp);
        $waPhone = $phoneWhatsapp ? preg_replace('/\D+/', '', $phoneWhatsapp) : '';
        $waText = rawurlencode('Hola, tengo una consulta desde la tienda online');
        $waLink = $waPhone ? "https://wa.me/{$waPhone}?text={$waText}" : '';
    ?>

    <div class="page-wrapper">
        @include('ecommerce::layouts.partials_ecommerce.header')
        <main class="main">
            @yield('content')
        </main>
        <footer class="footer">
            @include('ecommerce::layouts.partials_ecommerce.footer')
        </footer>
    </div>
    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu-container">
        @include('ecommerce::layouts.partials_ecommerce.mobile_menu')
    </div>
    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>
    <script src="{{ asset('porto-ecommerce/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/cart.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/main.js') }}"></script>
    <script src="{{ asset('porto-ecommerce/assets/js/vue.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
