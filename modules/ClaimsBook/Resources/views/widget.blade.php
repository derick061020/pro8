<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Libro de Reclamaciones</title>

    <script>
        // Resolver color OKLCH: parámetros del servidor tienen prioridad, luego localStorage
        window.widgetOklch = (function () {
            var sl = {!! json_encode($color_l ?? null) !!};
            var sc = {!! json_encode($color_c ?? null) !!};
            var sh = {!! json_encode($color_h ?? null) !!};
            if (sl !== null && sc !== null && sh !== null) {
                return { l: sl, c: sc, h: sh };
            }
            try {
                var stored = localStorage.getItem('claims_widget_primary_oklch');
                if (stored) {
                    var parsed = JSON.parse(stored);
                    if (parsed && parsed.l !== undefined) { return parsed; }
                }
            } catch (e) {}
            return null;
        }());
        // Fallback hex (backward compat con embeds antiguos)
        window.widgetColor = (function () {
            if (window.widgetOklch) { return null; }
            var server = {!! json_encode($primary_color ?? '#18181b') !!};
            if (server && server !== '#18181b') { return server; }
            try {
                var stored = localStorage.getItem('claims_widget_primary_color');
                if (stored && /^#[0-9A-Fa-f]{6}$/.test(stored)) { return stored; }
            } catch (e) {}
            return null;
        }());
        window.widgetShowCompany = {!! json_encode($show_company ?? true) !!};
    </script>

    @vite(['modules/ClaimsBook/Resources/assets/js/app.js'])

    <style>
        /* Estilos de normalización para la vista embebida */
        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: 'Montserrat', 'Helvetica Neue', Arial, sans-serif;
        }
        #main-wrapper {
            padding: 0;
        }
        /* Ocultar la barra lateral y el header del panel admin en el widget */
        .page-sidebar,
        .page-header.navbar,
        aside,
        nav {
            display: none !important;
        }
    </style>
</head>
<body>
    <div id="main-wrapper">
        <tenant-claims-book-form
            :embedded="true"
            tenant-slug="{{ $tenant_slug }}"
            :primary-color="widgetColor"
            :color-l="widgetOklch ? widgetOklch.l : null"
            :color-c="widgetOklch ? widgetOklch.c : null"
            :color-h="widgetOklch ? widgetOklch.h : null"
            :show-company="widgetShowCompany"
        ></tenant-claims-book-form>
    </div>

    <script>
    (function () {
        function sendHeight() {
            var h = Math.max(
                document.body.scrollHeight,
                document.documentElement.scrollHeight
            );
            window.parent.postMessage({ type: 'claims-resize', height: h }, '*');
        }

        window.addEventListener('load', sendHeight);

        if (window.ResizeObserver) {
            new ResizeObserver(sendHeight).observe(document.body);
        } else {
            setInterval(sendHeight, 400);
        }
    }());
    <\/script>
</body>
</html>
