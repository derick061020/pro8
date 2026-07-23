@php
    $configEcommerce = \App\Models\Tenant\ConfigurationEcommerce::first();
    $primaryHsl = null;
    $primaryColor = optional($configEcommerce)->color_ecommerce;

    $headerPrefs = optional($configEcommerce)->preferences;
    if (is_string($headerPrefs)) {
        $headerPrefs = json_decode($headerPrefs, true);
    }
    $headerTheme = data_get($headerPrefs, 'header_theme', 'light');
    if ($primaryColor && preg_match('/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $primaryColor)) {
        $hex = ltrim($primaryColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        if ($max == $min) {
            $h = 0; $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2; break;
                default: $h = ($r - $g) / $d + 4; break;
            }
            $h /= 6;
        }
        $primaryHsl = [round($h * 360), round($s * 100), round($l * 100)];
    }
@endphp
@if($primaryHsl)
<style>
    :root {
        --primary-h: {{ $primaryHsl[0] }};
        --primary-s: {{ $primaryHsl[1] }}%;
        --primary-l: {{ $primaryHsl[2] }}%;
    }
</style>
@endif

@if($headerTheme === 'dark')
<style>
    /* Tema oscuro del encabezado: usa el color principal como fondo */
    .header {
        background-color: var(--primary-color);
        border-bottom-color: rgba(0, 0, 0, 0.1);
    }
    .header .customlinks a,
    .header .category-dropdown-toggle,
    .header .btn-search-icon,
    .header .mobile-menu-toggler,
    .header .user-name-ecommerce .text-name,
    .user-name-ecommerce,
    .header .header-contact, .header .whatsapp-order,
    .header .cart-dropdown .header-contact svg {
        color: #fff !important;
    }
    .header .btn-search-icon svg,
    .header .category-dropdown-toggle svg {
        stroke: #fff;
    }
    .header a.dropdown-toggle svg {
        fill: #fff;
    }
    .header .cart-dropdown .dropdown-toggle .cart-count {
        background-color: #fff !important;
        color: var(--primary-color) !important;
    }
</style>
@endif
