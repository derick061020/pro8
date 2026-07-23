<!DOCTYPE html>
@php
    $path = explode('/', request()->path());
    $path[1] = (array_key_exists(1, $path) > 0) ? $path[1] : '';
    $path[2] = (array_key_exists(2, $path) > 0) ? $path[2] : '';
    $path[0] = ($path[0] === '') ? 'documents' : $path[0];
    $visual->sidebar_theme = property_exists($visual, 'sidebar_theme') ? $visual->sidebar_theme : '';
    $visual->sidebar_margin = property_exists($visual, 'sidebar_margin') ? (bool)$visual->sidebar_margin : true;
    $sidebar_mode = isset($vc_compact_sidebar) ? ($vc_compact_sidebar->sidebar_mode ?? 'light') : 'light';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="fixed no-mobile-device custom-scroll
        sidebar-white sidebar-light {{ $sidebar_mode === 'dark' ? 'sidebarMode-dark' : 'sidebarMode-light' }}
        {{ $visual->sidebar_margin ? 'sidebar-left-floating' : 'sidebar-left-fixed' }}
        {{$vc_compact_sidebar->compact_sidebar == true
    || $path[0] === 'pos'
    || $path[0] === 'pos' && $path[1] === 'fast'
    || $path[0] === 'documents' && $path[1] === 'create' ? 'sidebar-left-collapsed' : ''}}
        {{-- header-{{$visual->navbar ?? 'fixed'}} --}}
        {{-- {{$visual->header == 'dark' ? 'header-dark' : ''}} --}}
        {{-- {{$visual->sidebars == 'dark' ? '' : 'sidebar-light'}} --}}
        {{$visual->bg == 'dark' ? 'dark' : ''}}
        {{ ($path[0] === 'documents' && $path[1] === 'create'
    || $path[0] === 'documents' && $path[1] === 'note'
    || $path[0] === 'quotations' && $path[1] === 'create'
    || $path[0] === 'sale-opportunities' && $path[1] === 'create'
    || $path[0] === 'order-notes' && $path[1] === 'create'
    || $path[0] === 'sale-notes' && $path[1] === 'create'
    || $path[0] === 'purchase-quotations' && $path[1] === 'create'
    || $path[0] === 'purchase-orders' && $path[1] === 'create'
    || $path[0] === 'dispatches' && $path[1] === 'create'
    || $path[0] === 'purchases' && $path[1] === 'create') ? 'newinvoice' : ''}}
        {{ $path[0] === 'home' ? 'page-home' : '' }}
        ">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $vc_company->title_web ?: $vc_company->trade_name }}</title>
    <meta name="googlebot" content="noindex">
    <meta name="robots" content="noindex">

    <script>
        window.vc_visual = window.vc_visual || {};
        window.vc_visual.sidebar_theme = @json($visual->sidebar_theme);
    </script>

    @vite(['resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">

    {{-- <link rel="stylesheet" href="{{ asset('porto-light/vendor/bootstrap/css/bootstrap.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/animate/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/font-awesome/5.11/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/meteocons/css/meteocons.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/select2/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/select2-bootstrap-theme/select2-bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/datatables/media/css/dataTables.bootstrap4.css') }}" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.29/sweetalert2.min.css" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" />

    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-ui/jquery-ui.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-ui/jquery-ui.theme.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/select2/css/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/select2-bootstrap-theme/select2-bootstrap.min.css')}}" />

    <link href="{{ asset('porto-light/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('porto-light/vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('porto-light/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css')}}" />

    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-loading/dist/jquery.loading.css')}}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('porto-light/master/style-switcher/style-switcher.css')}}">

    <link rel="stylesheet" href="{{ asset('porto-light/css/theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/css/custom.css') }}" />

    @if (file_exists(public_path('theme/custom_styles.css')))
        <link rel="stylesheet" href="{{ asset('theme/custom_styles.css') }}" />
    @endif

    @if($vc_compact_sidebar->skin)
        @if (file_exists(storage_path('app/public/skins/' . $vc_compact_sidebar->skin->filename)))
            <link rel="stylesheet" href="{{ asset('storage/skins/' . $vc_compact_sidebar->skin->filename) }}" />
        @endif
    @endif


    @stack('styles')


    <script src="{{ asset('porto-light/vendor/modernizr/modernizr.js') }}"></script>

    <style>
        body {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            transition: overflow 0.3s;
        }

        html.sidebar-left-opened,
        html.options-user-mobile-opened {
            overflow: hidden !important;
        }

        body.visible {
            opacity: 1;
        }

        .descarga {
            color: black;
            padding: 5px;
        }

        .el-checkbox__label {
            font-size: 13px;
        }

        .center-el-checkbox {
            display: flex;
            align-items: center;
        }

        .center-el-checkbox .el-checkbox {
            margin-bottom: 0
        }

        .logo-light {
            display: block;
        }

        .logo-dark {
            display: none;
        }

        html.dark .logo-light {
            display: var(--show-light-logo, none);
        }

        html.dark .logo-dark {
            display: var(--show-dark-logo, block);
        }
    </style>

    @if ($vc_company->favicon)
        <link rel="shortcut icon" type="image/png" href="{{ asset($vc_company->favicon) }}" />
    @endif

    @php
        $themeInlineCss = '';
        $blackThemeInlineCss = '';

        $themeKey = $visual->sidebar_theme ?: 'white';
        $themesPath = public_path('json/themes/themes.json');
        if (is_file($themesPath)) {
            $themesAll = json_decode(file_get_contents($themesPath), true) ?: [];
            $colors = $themesAll[$themeKey] ?? $themesAll['white'] ?? null;
            if (is_array($colors) && !isset($colors['--primary-color'])) {
                $colors = $colors['default'] ?? $colors['light'] ?? $colors;
            }
            if (is_array($colors)) {
                foreach ($colors as $var => $val) {
                    if (strpos($var, '--') === 0) {
                        $themeInlineCss .= $var . ':' . $val . ';';
                    }
                }
            }
        }

        $blackThemeKey = (property_exists($visual, 'black_theme') && $visual->black_theme) ? $visual->black_theme : 'default';
        $blackThemesPath = public_path('json/themes/black-themes.json');
        if (is_file($blackThemesPath)) {
            $blackAll = json_decode(file_get_contents($blackThemesPath), true) ?: [];
            $blackColors = $blackAll[$blackThemeKey] ?? $blackAll['default'] ?? null;
            if (is_array($blackColors)) {
                foreach ($blackColors as $var => $val) {
                    if (strpos($var, '--') === 0) {
                        $blackThemeInlineCss .= $var . ':' . $val . ';';
                    }
                }
            }
        }
    @endphp
    @if($themeInlineCss)
        <style id="theme-styles">:root{ {!! $themeInlineCss !!} }</style>
    @endif
    @if($blackThemeInlineCss)
        <style id="black-theme-styles">:root{ {!! $blackThemeInlineCss !!} }</style>
    @endif

    <script async src="https://social.buho.la/pixel/y9nonmie9j8dkwha20ct2ua7nwsywi2m"></script>
</head>

<body class="pr-0"
    data-tenant="true"
    data-company-title="{{ $vc_company->title_web ?: $vc_company->trade_name }}">
    <section class="body">
        <!-- start: header -->
        {{-- @include('tenant.layouts.partials.header') --}}
        <!-- end: header -->
        <div class="inner-wrapper">
            <!-- start: sidebar -->
            @include('tenant.layouts.partials.sidebar')
            <!-- end: sidebar -->
            <section role="main" class="content-body" id="main-wrapper">
                @include('tenant.layouts.partials.header')
                @yield('content')
                @include('tenant.layouts.partials.sidebar_styles')
                {{-- @include('tenant.layouts.partials.sidebar_establishment') --}}

                @include('tenant.layouts.partials.check_last_password_update')

                <tenant-help-drawer></tenant-help-drawer>
                <tenant-global-help-button></tenant-global-help-button>

            </section>

            @yield('package-contents')
        </div>
    </section>
    @if($show_ws)
        @if(strlen($phone_whatsapp) > 0)
            <a class='ws-flotante d-flex align-items-center justify-content-center' href='https://wa.me/{{$phone_whatsapp}}'
                target="BLANK"
                style="font-size: 45px; color: #fff !important; background-color: #0074ff; text-decoration: none; border-radius: 30% !important;">
                <i class="fab fa-whatsapp"></i>
            </a>
        @endif
    @endif


    <!-- Vendor -->
    <script src="{{ asset('porto-light/vendor/jquery/jquery.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-browser-mobile/jquery.browser.mobile.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-cookie/jquery-cookie.js')}}"></script>
    {{--
    <script src="{{ asset('porto-light/master/style-switcher/style.switcher.js')}}"></script> --}}
    <script src="{{ asset('porto-light/vendor/popper/umd/popper.min.js')}}"></script>
    {{-- <script src="{{ asset('porto-light/vendor/bootstrap/js/bootstrap.js')}}"></script> --}}
    {{--
    <script src="{{ asset('porto-light/vendor/common/common.js')}}"></script> --}}
    <script src="{{ asset('porto-light/vendor/nanoscroller/nanoscroller.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/magnific-popup/jquery.magnific-popup.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-placeholder/jquery-placeholder.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/select2/js/select2.js') }}"></script>
    <script src="{{ asset('porto-light/vendor/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/datatables/media/js/dataTables.bootstrap4.min.js')}}"></script>

    {{-- Specific Page Vendor --}}
    <script src="{{asset('porto-light/vendor/jquery-ui/jquery-ui.js')}}"></script>
    <script src="{{asset('porto-light/vendor/jqueryui-touch-punch/jqueryui-touch-punch.js')}}"></script>
    <!--<script src="{{asset('porto-light/vendor/select2/js/select2.js')}}"></script>-->

    <script src="{{asset('porto-light/vendor/jquery-loading/dist/jquery.loading.js')}}"></script>

    <!--<script src="assets/vendor/select2/js/select2.js"></script>-->
    {{--
    <script src="{{asset('porto-light/vendor/bootstrap-multiselect/bootstrap-multiselect.js')}}"></script>--}}

    <!-- Moment -->
    {{--
    <script src="{{ asset('porto-light/vendor/moment/moment.js') }}"></script>--}}

    <!-- DatePicker -->
    {{--
    <script src="{{asset('porto-light/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>--}}

    <!-- Date range Plugin JavaScript -->
    {{--
    <script src="{{ asset('porto-light/vendor/bootstrap-timepicker/bootstrap-timepicker.js') }}"></script>--}}
    {{--
    <script src="{{ asset('porto-light/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>--}}

    <!-- Theme Initialization Files -->
    {{--
    <script src="{{asset('porto-light/js/theme.init.js')}}"></script> --}}

    {{--
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>--}}
    {{--
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>--}}

    @stack('scripts')

    <script src="{{ asset('js/sign-message.js') }}"></script>
    <script src="{{ asset('js/sha-256.min.js') }}"></script>
    <script src="{{ asset('js/rsvp-3.1.0.min.js') }}"></script>
    {{-- <script src="{{ asset('js/vendor.js') }}"></script> --}}
    <!-- Theme Base, Components and Settings -->
    <script src="{{asset('porto-light/js/theme.js')}}"></script>

    <!-- Theme Custom -->
    <script src="{{asset('porto-light/js/custom.js')}}"></script>
    <script src="{{asset('porto-light/js/jquery.xml2json.js')}}"></script>

    <script>

        function parseXMLToJSON(source) {
            let transform = $.xml2json(source);
            return transform
        }

        function openMozoApp(token) {
            if (token && token.trim().length > 0) {
                localStorage.setItem('token', token);
            }
            window.open('{{ route("tenant.restaurant.mozo", ["any" => "app"]) }}', '_blank');
        }

        function openVendeyaApp(token) {
            if (token && token.trim().length > 0) {
                localStorage.setItem('token', token);
            }
            window.open('{{ route("tenant.restaurant.vendeya", ["any" => "app"]) }}', '_blank');
        }

        $(document).ready(function () {
            $('#dropdown-notifications').click(function (e) {
                $('#dropdown-notifications').toggleClass('showed');
                $('#dn-toggle').toggleClass('show');
                $('#dn-menu').toggleClass('show');
                e.stopPropagation();
            });
        });

        $(document).click(function () {
            $('#dropdown-notifications').removeClass('showed');
            $('#dn-toggle').removeClass('show');
            $('#dn-menu').removeClass('show');
        });

    </script>
    <!-- <script src="//code.tidio.co/1vliqewz9v7tfosw5wxiktpkgblrws5w.js"></script> -->
     @if(session('toast_warning'))
    <div id="app-toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:99999;
        background:#fff8e1;border:1px solid #ffe082;color:#8a6d00;padding:12px 18px;border-radius:8px;
        box-shadow:0 4px 16px rgba(0,0,0,.15);font-family:-apple-system,'Segoe UI',Roboto,sans-serif;
        font-size:14px;max-width:90%;text-align:center;">
        {{ session('toast_warning') }}
    </div>
    <script>
        setTimeout(function () {
            var t = document.getElementById('app-toast');
            if (t) { t.style.transition = 'opacity .4s'; t.style.opacity = '0'; setTimeout(function(){ t.remove(); }, 400); }
        }, 6000);
    </script>
    @endif
</body>

</html>