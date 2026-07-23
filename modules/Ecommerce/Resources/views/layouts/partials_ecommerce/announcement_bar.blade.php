@php
    $ecommerceConfig = \App\Models\Tenant\ConfigurationEcommerce::first();
@endphp
@if($ecommerceConfig && $ecommerceConfig->publicidad_activa)
    <style>
        .announcement-bar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10001;
            color: white;
            padding: 3px 0;
            font-size: 14px;
            font-weight: 500;
            display: none;
            width: 100%;
            background-color: {{ $ecommerceConfig->publicidad_color_fondo }};
        }
        .announcement-link {
            color: white !important;
            text-decoration: none;
            display: block;
        }
        .announcement-link:hover {
            text-decoration: underline;
        }
        .close-announcement {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            line-height: 1;
            padding: 4px 8px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease-in-out;
        }
        .close-announcement:focus {
            outline: none;
        }
        .close-announcement:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }
        .close-announcement .close-text {
            display: none;
            margin-left: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .close-announcement:hover .close-text {
            display: inline-block;
        }
        .close-announcement .close-icon {
            font-size: 18px;
            font-weight: bold;
        }
        body.has-announcement-bar .header-search-wrapper {
            top: 40px;
        }
    </style>
    <div id="announcement-bar" class="announcement-bar">
        <div class="container text-center position-relative">
            <a href="{{ $ecommerceConfig->publicidad_link ?? '#' }}" target="_blank" class="announcement-link">
                {{ $ecommerceConfig->publicidad_texto }}
            </a>
            <button type="button" class="close-announcement" aria-label="Close" onclick="closeAnnouncementBar()">
                <span class="close-icon">&times;</span>
                <span class="close-text">Cerrar</span>
            </button>
        </div>
    </div>
    <script>
        function closeAnnouncementBar() {
            const bar = document.getElementById('announcement-bar');
            if (bar) {
                bar.style.display = 'none';
                localStorage.setItem('hide_announcement_bar', 'true');
                document.body.style.paddingTop = '0px';
                document.body.classList.remove('has-announcement-bar');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const bar = document.getElementById('announcement-bar');
            if (bar && !localStorage.getItem('hide_announcement_bar')) {
                bar.style.display = 'block';
                document.body.classList.add('has-announcement-bar');
                // Adjust body padding to prevent overlapping the header
                document.body.style.paddingTop = bar.offsetHeight + 'px';
            }
        });
    </script>
@endif
