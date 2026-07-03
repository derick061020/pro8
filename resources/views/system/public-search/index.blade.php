@extends('system.layouts.auth')

@section('content')
    <style>
        :root {
            --brand-color: {{ $brand['color'] ?? '#0d8796' }};
            --action-color: #101d3f;
            --navy: #0B2545;
            --surface: #FFFFFF;
            --surface-2: #F4F6FA;
            --border: rgba(11,37,69,0.1);
            --text-primary: #0B2545;
            --text-secondary: #4A6080;
            --text-muted: #8A9BB0;
            --radius-md: 12px;
            --radius-lg: 18px;
            --default-page-bg: linear-gradient(160deg, #f0f2f7 0%, #e4e8f0 100%);
        }

        * { box-sizing: border-box; }

        .search-page {
            background: var(--default-page-bg);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 32px 12px 48px;
        }

        .search-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(11,37,69,.10);
            overflow: hidden;
            width: 100%;
            max-width: 750px;
        }

        /* ── HEADER ─────────────────────────────── */
        .sc-header {
            background: #fff;
            border-top: 4px solid var(--brand-color);
            border-bottom: 1px solid #eaeff5;
            padding: 22px 28px 18px;
            text-align: center;
        }

        .sc-logo {
            height: 80px;
            width: auto;
            max-width: 260px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #e2ebf2;
            padding: 6px 10px;
            background: #f7fafc;
            margin-bottom: 10px;
        }

        .sc-logo-placeholder {
            height: 68px;
            width: 68px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--surface-2);
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--brand-color);
            border: 1px solid #dce6ef;
            margin-bottom: 10px;
        }

        .sc-title {
            font-size: .88rem;
            font-weight: 700;
            color: #0f2942;
            letter-spacing: .05em;
            margin: 0 0 2px;
            text-transform: uppercase;
        }

        .sc-company {
            font-size: .82rem;
            font-weight: 600;
            color: #4a6080;
            margin: 0 0 2px;
        }

        .sc-desc {
            font-size: .78rem;
            color: #7a8fa3;
            margin: 0 0 16px;
        }

        /* RUC row in header */
        .ruc-row {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 380px;
            margin: 0 auto;
        }

        .ruc-label-sm {
            font-size: 10.5px;
            font-weight: 600;
            color: #5a6f82;
            text-align: left;
            margin-bottom: 4px;
        }

        .ruc-input-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            background: var(--surface-2);
            border: 1px solid #d5dfe9;
            border-radius: 10px;
            padding: 0 12px;
            gap: 8px;
            transition: border-color .2s, box-shadow .2s;
        }

        .ruc-input-wrap:focus-within {
            border-color: var(--brand-color);
            box-shadow: 0 0 0 3px rgba(13,135,150,.10);
            background: #fff;
        }

        .ruc-icon {
            width: 14px;
            height: 14px;
            opacity: .38;
            flex-shrink: 0;
        }

        .ruc-input-wrap input {
            flex: 1;
            border: none;
            outline: none;
            background: none;
            font-size: 13px;
            font-family: 'DM Mono', monospace;
            letter-spacing: .4px;
            color: var(--text-primary);
            padding: 9px 0;
        }

        .ruc-input-wrap input::placeholder {
            color: var(--text-muted);
            font-size: 12px;
        }

        .ruc-digits {
            font-size: 9.5px;
            font-weight: 500;
            color: var(--brand-color);
            background: rgba(13,135,150,.09);
            border: 1px solid rgba(13,135,150,.18);
            border-radius: 4px;
            padding: 2px 6px;
            white-space: nowrap;
        }

        .btn-ruc-clear {
            background: none;
            border: 1px solid #d0dbe7;
            border-radius: 8px;
            color: #5a6f82;
            font-size: 12px;
            font-weight: 500;
            padding: 11px;
            cursor: pointer;
            white-space: nowrap;
            transition: border-color .2s, color .2s;
            flex-shrink: 0;
        }

        .btn-ruc-clear:hover {
            border-color: var(--brand-color);
            color: var(--brand-color);
        }

        /* ── BODY ───────────────────────────────── */
        .sc-body {
            padding: 20px 28px 24px;
        }

        .sc-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #8a9bb0;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eaeff5;
        }

        /* Document type tabs */
        .doc-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 18px;
        }

        .doc-tab {
            flex: 1 1 auto;
            min-width: 110px;
            background: var(--surface-2);
            border: 1.5px solid #dce6ef;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 11.5px;
            font-weight: 500;
            color: #4a6080;
            cursor: pointer;
            text-align: center;
            transition: all .18s;
            line-height: 1.2;
        }

        .doc-tab:hover {
            border-color: var(--brand-color);
            color: var(--brand-color);
            background: rgba(13,135,150,.05);
        }

        .doc-tab.active {
            background: var(--brand-color);
            border-color: var(--brand-color);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13,135,150,.22);
        }

        /* Form fields */
        .field-label {
            font-size: 11px;
            font-weight: 600;
            color: #5a6f82;
            margin-bottom: 5px;
            display: block;
        }

        .field-wrap {
            display: flex;
            align-items: center;
            background: var(--surface-2);
            border: 1px solid #d5dfe9;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }

        .field-wrap:focus-within {
            border-color: var(--brand-color);
            box-shadow: 0 0 0 3px rgba(13,135,150,.10);
            background: #fff;
        }

        .field-wrap input,
        .field-wrap select {
            flex: 1;
            border: none;
            outline: none;
            background: none;
            font-size: 13px;
            color: var(--text-primary);
            padding: 9px 12px;
        }

        .field-wrap select {
            cursor: pointer;
        }

        .field-wrap.has-icon {
            padding-left: 10px;
        }

        .field-icon {
            width: 15px;
            height: 15px;
            opacity: .35;
            flex-shrink: 0;
        }

        .field-wrap input::placeholder {
            color: var(--text-muted);
            font-size: 12px;
        }

        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }

        .form-group-field {
            display: flex;
            flex-direction: column;
        }

        .invalid-msg {
            font-size: 10.5px;
            color: #e53935;
            margin-top: 3px;
        }

        /* Actions */
        .sc-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }

        .btn-search {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--action-color);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px 28px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(16,29,63,.26);
            transition: background .18s, box-shadow .18s;
        }

        .btn-search:hover {
            background: #1a2e5a;
            box-shadow: 0 8px 22px rgba(16,29,63,.32);
        }

        .btn-clear-link {
            background: none;
            border: none;
            color: #8a9bb0;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            padding: 4px 2px;
            transition: color .18s;
        }

        .btn-clear-link:hover { color: #e53935; }

        /* Footer links */
        .sc-footer-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-top: 16px;
            font-size: 11px;
            color: #8a9bb0;
        }

        .sc-footer-links a {
            color: var(--brand-color);
            font-weight: 500;
            text-decoration: none;
        }

        .sc-footer-links a:hover { text-decoration: underline; }

        .sc-footer-sep { color: #c5d0dc; margin: 0 4px; }

        /* Alert */
        .sc-alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-top: 16px;
        }

        /* Results table */
        .sc-result-table {
            margin-top: 18px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eaeff5;
        }

        .sc-result-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .sc-result-table thead tr {
            background: #f3f7fa;
        }

        .sc-result-table th {
            padding: 9px 14px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .06em;
            color: #334155;
            text-transform: uppercase;
            text-align: left;
        }

        .sc-result-table td {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-primary);
            border-top: 1px solid #eaeff5;
        }

        .download-btns { display: flex; gap: 6px; justify-content: flex-end; }

        .btn-dl {
            background: #2f6df6;
            color: #fff;
            border: none;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 14px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(47,109,246,.22);
        }

        .btn-dl:hover { background: #1a58e0; color: #fff; }

        .bg-customizer {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .bg-customizer.is-hidden {
            display: none !important;
        }

        .bg-customizer-toggle {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid #d4deea;
            background: #ffffff;
            color: #0f2942;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(11,37,69,.18);
            transition: transform .15s, box-shadow .15s;
        }

        .bg-customizer-toggle:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(11,37,69,.22);
        }

        .bg-customizer-panel {
            width: 280px;
            border-radius: 14px;
            border: 1px solid #dce6ef;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(11,37,69,.18);
            padding: 12px;
            display: none;
        }

        .bg-customizer-panel.open {
            display: block;
        }

        .bg-customizer-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f2942;
            margin: 0 0 10px;
        }

        .bg-color-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .bg-swatch {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 10px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: transform .12s, border-color .12s;
        }

        .bg-swatch:hover {
            transform: translateY(-1px);
            border-color: rgba(11,37,69,.28);
        }

        .bg-swatch.active {
            border-color: #0f2942;
        }

        .bg-customizer-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .bg-customizer-row label {
            font-size: 11px;
            color: #5a6f82;
            white-space: nowrap;
        }

        .bg-customizer-row input[type="color"] {
            width: 100%;
            height: 32px;
            border: 1px solid #d4deea;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            padding: 2px;
        }

        .bg-file-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            margin-bottom: 10px;
        }

        .bg-file-row label {
            font-size: 11px;
            color: #5a6f82;
        }

        .bg-file-row input[type="file"] {
            width: 100%;
            font-size: 11px;
            color: #4a6080;
            border: 1px solid #d4deea;
            border-radius: 8px;
            padding: 5px 6px;
            background: #fff;
        }

        .bg-image-preview {
            width: 100%;
            height: 82px;
            border-radius: 10px;
            border: 1px dashed #c9d6e3;
            background: #f8fafc;
            margin-bottom: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .bg-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .bg-image-preview.has-image img {
            display: block;
        }

        .bg-image-preview-empty {
            font-size: 11px;
            color: #7b8da2;
        }

        .bg-image-preview.has-image .bg-image-preview-empty {
            display: none;
        }

        .bg-customizer-actions {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .bg-customizer-status {
            min-height: 16px;
            margin: 4px 0 8px;
            font-size: 10px;
            color: #6b7f95;
        }

        .bg-customizer-status.is-success {
            color: #0e8b59;
        }

        .bg-customizer-status.is-error {
            color: #c62828;
        }

        .bg-customizer-status.is-pending {
            color: #45658a;
        }

        .bg-btn {
            border: 1px solid #d4deea;
            border-radius: 8px;
            background: #fff;
            color: #4a6080;
            font-size: 0;
            font-weight: 600;
            padding: 8px 0;
            cursor: pointer;
            min-width: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .bg-btn:hover {
            border-color: var(--brand-color);
            color: var(--brand-color);
        }

        .bg-btn.primary {
            background: var(--brand-color);
            border-color: var(--brand-color);
            color: #fff;
        }

        .bg-btn.primary:hover {
            filter: brightness(.96);
            color: #fff;
        }

        .bg-btn .btn-icon {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .bg-btn .btn-text {
            display: none;
        }

        @media (max-width: 640px) {
            .bg-customizer {
                right: 12px;
                bottom: 12px;
            }

            .bg-customizer-panel {
                width: min(280px, calc(100vw - 24px));
            }
        }

    </style>

    @php
        $searchAction = !empty($allowTenantCustomization)
            ? route('tenant.public_search.form.search')
            : ($tenantSlug
                ? route('system.public_search.widget.search', ['slug' => $tenantSlug])
                : route('system.public_search.search'));

        $bgColor = $brand['bg_color'] ?? '';
        $bgImageUrl = $brand['bg_image_url'] ?? '';
        $pageBackgroundStyle = '';

        if (!empty($bgImageUrl)) {
            $pageBackgroundStyle = "background: " . (!empty($bgColor) ? $bgColor : '#f0f2f7')
                . "; background-image: url('{$bgImageUrl}'); background-repeat: no-repeat; background-size: cover; background-position: center;";
        } elseif (!empty($bgColor)) {
            $pageBackgroundStyle = "background: {$bgColor};";
        }
    @endphp

    <div
        class="search-page"
        style="{{ $pageBackgroundStyle }}"
        data-tenant-slug="{{ $tenantSlug ?? '' }}"
        data-bg-color="{{ $brand['bg_color'] ?? '' }}"
        data-bg-image-url="{{ $brand['bg_image_url'] ?? '' }}"
        data-bg-update-url=""
        data-can-persist-bg="0"
    >
        <div class="search-card">

            {{-- ── HEADER ─────────────────────────── --}}
            <div class="sc-header">
                @if(!empty($brand['logo']))
                    <img src="{{ $brand['logo'] }}" alt="Logo {{ $brand['name'] }}" class="sc-logo"><br>
                @else
                    <div class="sc-logo-placeholder">
                        {{ strtoupper(substr($brand['name'] ?? 'C', 0, 1)) }}
                    </div><br>
                @endif

                <p class="sc-title">Buscar Comprobante Electrónico</p>
                <p class="sc-company">{{ $brand['name'] ?? 'Mi Empresa S.A.C.' }}</p>
                <p class="sc-desc">Consulta de validez de comprobantes electrónicos de pago.</p>

                {{-- RUC Emisor --}}
                <div style="max-width:380px; margin:0 auto; text-align:left;">
                    <label class="ruc-label-sm" for="ruc_emisor">RUC Emisor <span style="color:#e53935">*</span></label>
                    <div class="ruc-row">
                        <div class="ruc-input-wrap">
                            <svg class="ruc-icon" viewBox="0 0 24 24" fill="none" stroke="#0B2545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="4" x2="9" y2="9"/>
                            </svg>
                            <input
                                type="text"
                                name="ruc_emisor"
                                id="ruc_emisor"
                                form="public-search-form"
                                maxlength="11"
                                value="{{ old('ruc_emisor', $form['ruc_emisor']) }}"
                                placeholder="Ej: 20123456789"
                                oninput="this.value=this.value.replace(/\D/g,'')"
                            >
                            <span class="ruc-digits">11 dígitos</span>
                        </div>
                    </div>
                    @error('ruc_emisor')
                        <div class="invalid-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── BODY ───────────────────────────── --}}
            <div class="sc-body">
                <form id="public-search-form" method="POST" action="{{ $searchAction }}" autocomplete="off">
                    @csrf

                    @if($tenantSlug)
                        <input type="hidden" name="tenant_slug" value="{{ $tenantSlug }}">
                    @endif

                    {{-- Datos del comprobante --}}
                    <p class="sc-section-label">Datos del Comprobante</p>
                    <div class="form-row-3">
                        <div class="form-group-field">
                            <label class="field-label" for="series">Serie <span style="color:#e53935">*</span></label>
                            <div class="field-wrap">
                                <input type="text" name="series" id="series" maxlength="10"
                                    value="{{ old('series', $form['series']) }}"
                                    placeholder="F001">
                            </div>
                            @error('series')
                                <div class="invalid-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label class="field-label" for="number">Número <span style="color:#e53935">*</span></label>
                            <div class="field-wrap">
                                <input type="text" name="number" id="number" maxlength="20"
                                    value="{{ old('number', $form['number']) }}"
                                    placeholder="000001">
                            </div>
                            @error('number')
                                <div class="invalid-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label class="field-label" for="total">Monto total <span style="color:#e53935">*</span></label>
                            <div class="field-wrap">
                                <input type="text" name="total" id="total"
                                    value="{{ old('total', $form['total']) }}"
                                    placeholder="0.00">
                            </div>
                            @error('total')
                                <div class="invalid-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Datos del cliente --}}
                    <p class="sc-section-label">Datos del Cliente (RUC / DNI)</p>
                    <div class="form-row-2">
                        <div class="form-group-field">
                            <label class="field-label" for="customer_number">Número de documento</label>
                            <div class="field-wrap">
                                <input type="text" name="customer_number" id="customer_number" maxlength="15"
                                    value="{{ old('customer_number', $form['customer_number']) }}"
                                    placeholder="Número de documento" style="padding-left:14px;">
                            </div>
                            @error('customer_number')
                                <div class="invalid-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-field">
                            <label class="field-label" for="date_of_issue">Fecha de emisión</label>
                            <div class="field-wrap has-icon">
                                <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="#0B2545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="4" x2="9" y2="9"/>
                                </svg>
                                <input type="date" name="date_of_issue" id="date_of_issue"
                                    value="{{ old('date_of_issue', $form['date_of_issue']) }}"
                                    style="padding-left:8px;">
                            </div>
                            @error('date_of_issue')
                                <div class="invalid-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="sc-actions">
                        <button type="submit" class="btn-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            Buscar comprobante
                        </button>
                        <button type="button" class="btn-ruc-clear" onclick="clearForm()">Limpiar</button>
                    </div>
                </form>

                @if($statusMessage)
                    <div class="sc-alert {{ $statusType === 'warning' ? 'alert-warning' : 'alert-info' }} alert">
                        {{ $statusMessage }}
                    </div>
                @endif

                @if($result)
                    <div class="sc-result-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Número</th>
                                    <th style="text-align:right">Total</th>
                                    <th style="text-align:right">Descargas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $item)
                                    <tr>
                                        <td>{{ $item['customer'] }}</td>
                                        <td>{{ $item['number'] }}</td>
                                        <td style="text-align:right">{{ $item['total'] }}</td>
                                        <td>
                                            <div class="download-btns">
                                                <a href="{{ $item['download_xml'] }}" target="_blank" rel="noopener" class="btn-dl">XML</a>
                                                <a href="{{ $item['download_pdf'] }}" target="_blank" rel="noopener" class="btn-dl">PDF</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if(false)
        <div class="bg-customizer" id="bgCustomizer"></div>
        @endif

    </div>

    <script src="{{ asset('porto-ecommerce/assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        function clearRuc() {
            document.getElementById('ruc_emisor').value = '';
            document.getElementById('ruc_emisor').focus();
        }

        function clearForm() {
            document.getElementById('ruc_emisor').value = '';
            document.getElementById('series').value = '';
            document.getElementById('number').value = '';
            document.getElementById('total').value = '';
            document.getElementById('customer_number').value = '';
            document.getElementById('date_of_issue').value = '';
        }

        (function initBackgroundCustomizer() {
            // Lógica anterior desactivada: ahora el fondo proviene únicamente de Configuración Admin.
            return;

            var page = document.querySelector('.search-page');
            var customizer = document.getElementById('bgCustomizer');
            var toggle = document.getElementById('bgCustomizerToggle');
            var panel = document.getElementById('bgCustomizerPanel');
            var colorGrid = document.getElementById('bgColorGrid');
            var colorInput = document.getElementById('bgCustomColor');
            var imageInput = document.getElementById('bgImageInput');
            var imagePreview = document.getElementById('bgImagePreview');
            var imagePreviewImg = document.getElementById('bgImagePreviewImg');
            var saveBtn = document.getElementById('bgSaveBtn');
            var removeImageBtn = document.getElementById('bgRemoveImageBtn');
            var resetBtn = document.getElementById('bgResetBtn');
            var closeBtn = document.getElementById('bgCloseBtn');
            var saveStatus = document.getElementById('bgSaveStatus');
            var csrfInput = document.querySelector('#public-search-form input[name="_token"]');

            var isIframeContext = window.self !== window.top;
            if (isIframeContext && customizer) {
                customizer.classList.add('is-hidden');
                return;
            }

            if (!page || !toggle || !panel || !colorGrid || !colorInput || !imageInput || !imagePreview || !imagePreviewImg || !saveBtn || !removeImageBtn || !resetBtn || !closeBtn || !saveStatus) {
                return;
            }

            var tenantSlug = page.dataset.tenantSlug || '';
            var pageKey = tenantSlug ? tenantSlug : window.location.pathname;
            var storageKey = 'public-search-bg:' + pageKey;
            var canPersist = page.dataset.canPersistBg === '1';
            var updateUrl = page.dataset.bgUpdateUrl || '';
            var serverColor = page.dataset.bgColor || '';
            var serverImageUrl = page.dataset.bgImageUrl || '';
            var csrfToken = csrfInput ? csrfInput.value : '';
            var palette = ['#f0f2f7', '#e9f5ff', '#fef3e8', '#f3f7ec', '#f4ecff', '#ffeef1', '#f8fafc', '#edf2ff', '#fff7d6', '#dff7f3'];
            var localColor = normalizeColor(localStorage.getItem(storageKey));
            var appliedColor = normalizeColor(serverColor) || localColor;
            var selectedColor = appliedColor;
            var appliedImageUrl = serverImageUrl;
            var selectedImageUrl = serverImageUrl;
            var selectedImageFile = null;
            var removeImageOnSave = false;
            var previewObjectUrl = null;

            function setStatus(message, statusClass) {
                saveStatus.textContent = message || '';
                saveStatus.classList.remove('is-success', 'is-error', 'is-pending');
                if (statusClass) {
                    saveStatus.classList.add(statusClass);
                }
            }

            function normalizeColor(color) {
                if (!color || !/^#([0-9a-fA-F]{6})$/.test(color)) {
                    return '';
                }

                return color.toLowerCase();
            }

            function persistBackground(color, imageFile, removeImage) {
                if (!canPersist || !updateUrl || !csrfToken) {
                    return Promise.resolve({
                        background_color: color,
                        background_image_url: selectedImageUrl,
                    });
                }

                var hasImagePayload = !!imageFile || !!removeImage;

                if (!hasImagePayload) {
                    return fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            background_color: color || null,
                        }),
                    }).then(function (response) {
                        return response.json().catch(function () {
                            return {};
                        }).then(function (payload) {
                            if (!response.ok) {
                                throw new Error(payload.message || 'No se pudo guardar el fondo');
                            }

                            return payload;
                        });
                    });
                }

                var formData = new FormData();
                formData.append('background_color', color || '');

                if (imageFile) {
                    formData.append('background_image', imageFile);
                }

                if (removeImage) {
                    formData.append('remove_background_image', '1');
                }

                return fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                }).then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (payload) {
                        if (!response.ok) {
                            throw new Error(payload.message || 'No se pudo guardar el fondo');
                        }

                        return payload;
                    });
                });
            }

            function storeLocalBackground(color) {
                if (!color) {
                    localStorage.removeItem(storageKey);
                    return;
                }

                localStorage.setItem(storageKey, color);
            }

            function setBackground(color, imageUrl) {
                if (imageUrl) {
                    page.style.background = color || '#f0f2f7';
                    page.style.backgroundImage = 'url("' + imageUrl.replace(/"/g, '\\"') + '")';
                    page.style.backgroundRepeat = 'no-repeat';
                    page.style.backgroundSize = 'cover';
                    page.style.backgroundPosition = 'center';
                    return;
                }

                page.style.backgroundImage = '';
                page.style.backgroundRepeat = '';
                page.style.backgroundSize = '';
                page.style.backgroundPosition = '';

                if (!color) {
                    page.style.background = 'var(--default-page-bg)';
                    return;
                }

                page.style.background = color;
            }

            function setImagePreview(imageUrl) {
                if (imageUrl) {
                    imagePreview.classList.add('has-image');
                    imagePreviewImg.src = imageUrl;
                    return;
                }

                imagePreview.classList.remove('has-image');
                imagePreviewImg.src = '';
            }

            function resetFileInput() {
                imageInput.value = '';
            }

            function askConfirmation(title, text, confirmText) {
                if (typeof window.swal === 'function') {
                    return window.swal({
                        title: title,
                        text: text,
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e53935',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then(function (result) {
                        return !!(result && result.value);
                    });
                }

                if (window.Swal && typeof window.Swal.fire === 'function') {
                    return window.Swal.fire({
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e53935',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                    }).then(function (result) {
                        return !!(result && (result.isConfirmed || result.value));
                    });
                }

                return Promise.resolve(false);
            }

            function applySelectedBackground() {
                var color = normalizeColor(selectedColor);

                saveBtn.disabled = true;
                removeImageBtn.disabled = true;
                setStatus('Guardando...', 'is-pending');

                return persistBackground(color, selectedImageFile, removeImageOnSave)
                    .then(function (response) {
                        storeLocalBackground(color);
                        appliedColor = color;

                        if (response && Object.prototype.hasOwnProperty.call(response, 'background_image_url')) {
                            selectedImageUrl = response.background_image_url || '';
                        }

                        appliedImageUrl = selectedImageUrl;

                        selectedImageFile = null;
                        removeImageOnSave = false;
                        resetFileInput();
                        setImagePreview(selectedImageUrl);
                        setBackground(appliedColor, selectedImageUrl);
                        setStatus('Fondo guardado correctamente.', 'is-success');
                    })
                    .catch(function (error) {
                        storeLocalBackground(color);

                        if (previewObjectUrl) {
                            URL.revokeObjectURL(previewObjectUrl);
                            previewObjectUrl = null;
                        }

                        selectedImageFile = null;
                        removeImageOnSave = false;
                        selectedColor = appliedColor;
                        selectedImageUrl = appliedImageUrl;
                        colorInput.value = appliedColor || '#e4e8f0';
                        resetFileInput();
                        setImagePreview(selectedImageUrl);
                        setBackground(appliedColor, selectedImageUrl);
                        refreshActiveSwatch(appliedColor);
                        setStatus((error && error.message) ? error.message : 'No se pudo guardar en servidor.', 'is-error');
                    })
                    .finally(function () {
                        saveBtn.disabled = false;
                        removeImageBtn.disabled = false;
                    });
            }

            function refreshActiveSwatch(color) {
                var swatches = colorGrid.querySelectorAll('.bg-swatch');
                swatches.forEach(function (swatch) {
                    swatch.classList.toggle('active', swatch.dataset.color === color);
                });
            }

            palette.forEach(function (color) {
                var swatch = document.createElement('button');
                swatch.type = 'button';
                swatch.className = 'bg-swatch';
                swatch.style.background = color;
                swatch.dataset.color = color;
                swatch.title = color;
                swatch.addEventListener('click', function () {
                    colorInput.value = color;
                    selectedColor = color;
                    setBackground(color, selectedImageUrl);
                    refreshActiveSwatch(color);
                    setStatus('Color seleccionado. Presiona Aplicar para guardar.', 'is-pending');
                });
                colorGrid.appendChild(swatch);
            });

            if (selectedColor) {
                colorInput.value = selectedColor;
            }

            if (!selectedColor && colorInput.value) {
                selectedColor = normalizeColor(colorInput.value);
            }

            setImagePreview(selectedImageUrl);
            setBackground(selectedColor, selectedImageUrl);
            refreshActiveSwatch(selectedColor);

            if (canPersist && !normalizeColor(serverColor) && localColor) {
                setStatus('Hay un color local. Presiona Aplicar para sincronizar en todos los navegadores.', 'is-pending');
            }

            toggle.addEventListener('click', function () {
                var isOpen = panel.classList.toggle('open');
                panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            });

            saveBtn.addEventListener('click', function () {
                applySelectedBackground();
            });

            closeBtn.addEventListener('click', function () {
                panel.classList.remove('open');
                panel.setAttribute('aria-hidden', 'true');
            });

            colorInput.addEventListener('input', function () {
                var color = colorInput.value;
                selectedColor = color;
                setBackground(color, selectedImageUrl);
                refreshActiveSwatch(color);
                setStatus('Color seleccionado. Presiona Aplicar para guardar.', 'is-pending');
            });

            imageInput.addEventListener('change', function () {
                var file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
                if (!file) {
                    return;
                }

                selectedImageFile = file;
                removeImageOnSave = false;

                if (previewObjectUrl) {
                    URL.revokeObjectURL(previewObjectUrl);
                    previewObjectUrl = null;
                }

                previewObjectUrl = URL.createObjectURL(file);
                selectedImageUrl = previewObjectUrl;
                setImagePreview(selectedImageUrl);
                setBackground(selectedColor, selectedImageUrl);
                setStatus('Imagen seleccionada. Presiona Aplicar para guardar.', 'is-pending');
            });

            removeImageBtn.addEventListener('click', function () {
                if (!selectedImageUrl && !selectedImageFile) {
                    return;
                }

                askConfirmation(
                    'Quitar imagen de fondo',
                    'La imagen se eliminará y se usará solo el color de fondo. ¿Deseas continuar?',
                    'Sí, quitar imagen'
                ).then(function (confirmed) {
                    if (!confirmed) {
                        return;
                    }

                    if (previewObjectUrl) {
                        URL.revokeObjectURL(previewObjectUrl);
                        previewObjectUrl = null;
                    }

                    selectedImageFile = null;
                    removeImageOnSave = true;
                    selectedImageUrl = '';
                    resetFileInput();
                    setImagePreview('');
                    setBackground(selectedColor, '');
                    setStatus('Imagen removida. Presiona Aplicar para guardar.', 'is-pending');
                });
            });

            resetBtn.addEventListener('click', function () {
                askConfirmation(
                    'Confirmar restablecimiento',
                    'Se perderán el color y la imagen actual, y se volverá al fondo por defecto. ¿Deseas continuar?',
                    'Sí, restablecer'
                ).then(function (confirmed) {
                    if (!confirmed) {
                        return;
                    }

                    if (previewObjectUrl) {
                        URL.revokeObjectURL(previewObjectUrl);
                        previewObjectUrl = null;
                    }

                    selectedColor = '';
                    selectedImageFile = null;
                    removeImageOnSave = !!selectedImageUrl;
                    selectedImageUrl = '';
                    resetFileInput();
                    setImagePreview('');
                    setBackground('', '');
                    refreshActiveSwatch('');
                    applySelectedBackground();
                });
            });

            document.addEventListener('click', function (event) {
                var customizer = document.getElementById('bgCustomizer');
                if (!customizer.contains(event.target)) {
                    panel.classList.remove('open');
                    panel.setAttribute('aria-hidden', 'true');
                }
            });

            window.addEventListener('beforeunload', function () {
                if (previewObjectUrl) {
                    URL.revokeObjectURL(previewObjectUrl);
                }
            });
        })();
    </script>

@endsection
