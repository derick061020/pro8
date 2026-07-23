<!DOCTYPE html>
<html lang="es">
  <head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="preload" as="style" onload="this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?family=Fira Code:wght@400;600&family=Montserrat:wght@500;600;700;800;900&family=Roboto:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
    <script src="https://www.googletagmanager.com/gtag/js?id=G-8PH6FM2JEL" async></script>
    <script>window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-8PH6FM2JEL');
</script>

    <meta charset="UTF-8" />
    <link
      rel="icon"
      href="{{ asset('/vendeya/images/svg/logo/isotipo-oficial.svg') }}"
      type="image/svg+xml"
    />
    <link
      rel="alternate icon"
      href="{{ asset('/vendeya/isotipo-oficial.png') }}"
      type="image/png"
      sizes="16x16"
    />
    <link rel="apple-touch-icon" href="{{ asset('/vendeya/isotipo-oficial.png') }}" sizes="180x180" />
    <link
      rel="mask-icon"
      href="{{ asset('/vendeya/images/svg/logo/isotipo-oficial.svg') }}"
      color="#FFFFFF"
    />
    <meta name="theme-color" content="#ffffff" />
    <meta name="msapplication-TileColor" content="#232326" />
    <meta name="theme-color" content="#ffffff" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <!-- <script type="text/javascript" src="vendors/map.js"></script> -->
    <title id="dynamic-title">Cargando...</title>
    <script>
      /**
       * this is a hack for dragula used on KanbanApp
       *
       * @see src/components/pages/apps/KanbanApp.vue
       */
      var global = global || window
    </script>
    <link
      rel="preload"
      as="style"
      onload="this.rel='stylesheet'"
      href="{{ asset('/vendeya/vendors/font-awesome-v5.css') }}"
    />
    <link
      rel="preload"
      as="style"
      onload="this.rel='stylesheet'"
      href="{{ asset('/vendeya/vendors/line-icons-pro.css') }}"
    />
    <link
      rel="preload"
      as="style"
      onload="this.rel='stylesheet'"
      href="{{ asset('/vendeya/vendors/prism-coldark-cold.css') }}"
    />
    <style>
      body {
        touch-action: pan-x pan-y;
      }
    </style>
    @php
        $vendeyaIndexPath = public_path('vendeya/index.html');
        $cacheKey = 'vendeya_build_assets_' . filemtime($vendeyaIndexPath);
        $vendeyaAssets = cache()->rememberForever($cacheKey, function () use ($vendeyaIndexPath) {
            $html = file_get_contents($vendeyaIndexPath);
            preg_match('/<script type="module" crossorigin src="([^"]+)"/', $html, $js);
            preg_match('/<link rel="modulepreload" href="([^"]+)"/', $html, $vendor);
            preg_match('/<link rel="stylesheet" href="(\/vendeya\/assets\/[^"]+\.css)"/', $html, $css);
            return [
                'js'     => ltrim($js[1] ?? '', '/'),
                'vendor' => ltrim($vendor[1] ?? '', '/'),
                'css'    => ltrim($css[1] ?? '', '/'),
            ];
        });
    @endphp
    <script type="module" crossorigin src="{{ asset($vendeyaAssets['js']) }}"></script>
    <link rel="modulepreload" href="{{ asset($vendeyaAssets['vendor']) }}">
    <link rel="stylesheet" href="{{ asset($vendeyaAssets['css']) }}">
  <link rel="manifest" href="{{ asset('/vendeya/manifest.webmanifest') }}"></head>
  <body>
    <!--
      this is a placeholder for Teleport's vue feature

      @see /src/pages/navbar/dashboards/influencer.vue
      @see /src/pages/sidebar/dashboards/influencer.vue
    -->
    <div data-teleport-bg></div>

    <div id="app" class="app-wrapper"></div>


    <!-- <script
      type="text/javascript"
      src="https://www.gstatic.com/charts/loader.js"
    ></script> -->


  </body>
</html>
