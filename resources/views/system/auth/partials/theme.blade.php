{{-- Estilos y herencia de tema compartidos por login / recuperar / restablecer contraseña --}}

{{-- Iconos Tabler.io --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.30.0/dist/tabler-icons.min.css" />

{{-- Hereda el color/tema elegido en el theme-sidebar del admin.
     Normaliza la estructura anidada de themes.json (ej. "white" -> {light, default})
     y cae al JSON público si no hay caché en localStorage. --}}
<script>
    (function () {
        function normalize(obj) {
            if (obj && typeof obj === 'object' && !obj['--primary-color']) {
                return obj.default || obj.light || obj;
            }
            return obj;
        }
        function inject(colors) {
            if (!colors || typeof colors !== 'object') return;
            var tag = document.getElementById('theme-styles');
            if (!tag) {
                tag = document.createElement('style');
                tag.id = 'theme-styles';
                document.head.appendChild(tag);
            }
            var css = ':root {';
            Object.keys(colors).forEach(function (k) {
                if (k.indexOf('--') === 0) css += k + ': ' + colors[k] + '; ';
            });
            css += '}';
            tag.innerHTML = css;
        }
        try {
            var theme = localStorage.getItem('current_theme') || 'white';
            var cached = localStorage.getItem('theme_colors_' + theme);
            var parsed = cached ? normalize(JSON.parse(cached)) : null;
            // Usa el caché solo si trae tipografía; si no, refresca desde el JSON
            if (parsed && parsed['--font-family']) {
                inject(parsed);
            } else {
                fetch('/json/themes/themes.json?v=' + Date.now())
                    .then(function (r) { return r.json(); })
                    .then(function (all) { inject(normalize(all[theme])); })
                    .catch(function () { /* usa los colores por defecto de admin_styles.css */ });
            }
        } catch (e) { /* usa los colores por defecto de admin_styles.css */ }
    })();
</script>

{{-- CSS nuevo: solo lo estrictamente necesario. La etiqueta flotante y el resaltado al
     enfocar ya vienen de admin_styles.css (.control-label:has(+div) y .form-group:has(input:focus)). --}}
<style>
    .body-sign { max-width: 520px; }

    /* Tarjeta sin borde y ancho fijo */
    .body-sign .card,
    .body-sign .card .card-header,
    .body-sign .card .card-body { border: none !important; }
    .body-sign .card {
        width: 480px;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    /* Badge de versión resaltado */
    .login-version {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .3);
    }
    .login-version b { font-size: 14px; font-weight: 800; }
    .login-version .ti { font-size: 15px; color: #ffd54a; }

    /* Inputs con el MISMO aspecto y foco que los formularios del admin (.el-input__inner),
       para que la etiqueta flotante y el input coincidan al enfocar */
    .body-sign .form-control {
        background-color: var(--light-color);
        border: 1px solid var(--accent-color);
        border-radius: 8px;
        color: var(--dark-color);
        height: auto;
        padding: 12px 14px;
        font-size: 15px;
        /* Igual que .el-input__inner del admin: sin animación, para que el borde del
           input y el de la etiqueta flotante cambien a la vez al enfocar */
        transition: none !important;
    }
    .body-sign .form-control:focus {
        background-color: #fff;
        border: 1px solid var(--highlight-color);
        box-shadow:
            -4px 0 var(--accent-color), 4px 0 var(--accent-color),
            0 4px var(--accent-color), 0 -4px var(--accent-color),
            -3px 3px var(--accent-color), 3px 3px var(--accent-color),
            -3px -3px var(--accent-color), 3px -3px var(--accent-color);
    }

    /* Toggle ver/ocultar contraseña */
    .login-pass { position: relative; }
    .login-pass .form-control { padding-right: 44px; }
    .login-pass-toggle {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        padding: 6px;
        cursor: pointer;
        color: var(--muted);
    }
    .login-pass-toggle:hover { color: var(--primary-color); }
    .login-pass-toggle .ti { font-size: 19px; }

    /* Botón principal a todo el ancho (tamaño normal) */
    .login-submit {
        width: 100%;
        display: block;
    }
    .login-submit .ti { vertical-align: -2px; }
</style>
