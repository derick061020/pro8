@php
    // Cabecera compartida por la web de reservas (landing, blog y detalle de
    // entrada). Centraliza el sistema de diseño (paleta "Makai", tipografía
    // Inter, tokens de Tailwind y clases de componentes) para que las tres
    // páginas mantengan exactamente la misma línea gráfica.
    $pageTitle = $pageTitle ?? 'Reservas online';
    $pageDesc  = $pageDesc  ?? null;
@endphp
<meta charset="utf-8">
<title>{{ $pageTitle }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
<link rel="shortcut icon" href="/landing-reservas/favicon.ico">

<!-- Tipografía Inter (misma que el CRM Makai) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Inter+Tight:wght@500;600;700&display=swap" rel="stylesheet">

<!-- Iconos (Font Awesome ya disponible localmente) -->
<link rel="stylesheet" href="/landing-reservas/css/font-awesome.min.css">

<!-- Tailwind + tokens de la línea gráfica Makai -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sans:    ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
          display: ['"Inter Tight"', 'Inter', 'system-ui', 'sans-serif'],
        },
        colors: {
          brand: { DEFAULT:'#5c7c68', dark:'#4a6354', soft:'#5c7c6833', tint:'#eef2ef' },
          ink: { 950:'#171717', 900:'#222530', 700:'#2b303b', 600:'#525866', 500:'#717784', 400:'#99a0ae', 300:'#cacfd8', 200:'#eaecf0', 100:'#f2f5f8', 50:'#f5f7fa' },
          ok:   { DEFAULT:'#1fc16b', soft:'#e3f7ec', dark:'#1daf61' },
          warn: { DEFAULT:'#fa7319', soft:'#fff3eb', dark:'#e16614' },
          err:  { DEFAULT:'#fb3748', soft:'#ffebec', dark:'#e93544' },
          info: { DEFAULT:'#335cff', soft:'#ebf1ff', dark:'#3559e9' },
          page: '#f4f4f4',
        },
        boxShadow: {
          xs:    '0 1px 2px 0 rgba(10,13,20,0.04)',
          card:  '0 1px 2px 0 rgba(10,13,20,0.06), 0 1px 3px 0 rgba(10,13,20,0.04)',
          panel: '0 2px 8px -2px rgba(10,13,20,0.06), 0 1px 3px 0 rgba(10,13,20,0.05)',
          hover: '0 18px 40px -12px rgba(10,13,20,0.22)',
        },
      }
    }
  }
</script>

<!-- jQuery (usado por la lógica de búsqueda/reserva) -->
<script src="/landing-reservas/js/jquery-1.11.0.min.js"></script>

<style>
  html, body { font-family:'Inter', system-ui, sans-serif; background:#f4f4f4; color:#222530; scroll-behavior:smooth; overflow-x:hidden; max-width:100%; }
  .font-display { font-family:'Inter Tight','Inter',sans-serif; }
  ::-webkit-scrollbar { width:9px; height:9px; }
  ::-webkit-scrollbar-thumb { background:#cacfd8; border-radius:8px; }
  ::-webkit-scrollbar-track { background:transparent; }
  a { text-decoration:none; }

  /* ===== Botones ===== */
  .hb-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 20px; border-radius:10px; font-size:14px; font-weight:600; line-height:1; cursor:pointer; border:1px solid transparent; transition:background-color .15s, border-color .15s, transform .15s, box-shadow .15s; white-space:nowrap; }
  .hb-btn-primary { background:#5c7c68; color:#fff; border-color:#5c7c68; box-shadow:0 10px 22px -10px rgba(92,124,104,.65); }
  .hb-btn-primary:hover { background:#4a6354; border-color:#4a6354; color:#fff; transform:translateY(-1px); }
  .hb-btn-ghost { background:#fff; color:#2b303b; border-color:#eaecf0; }
  .hb-btn-ghost:hover { background:#f5f7fa; border-color:#cacfd8; color:#222530; }
  .hb-btn-light { background:rgba(255,255,255,.12); color:#fff; border-color:rgba(255,255,255,.35); }
  .hb-btn-light:hover { background:rgba(255,255,255,.22); color:#fff; }
  .hb-btn-lg { padding:14px 26px; font-size:15px; border-radius:12px; }
  .hb-btn-block { width:100%; }
  .hb-btn:disabled { opacity:.65; cursor:default; transform:none; }

  /* ===== Formularios ===== */
  .hb-label { display:block; font-size:11px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:#717784; margin-bottom:7px; }
  .hb-label i { color:#5c7c68; margin-right:5px; }
  .hb-input, .hb-select { width:100%; height:46px; border:1px solid #eaecf0; border-radius:10px; padding:0 14px; font-size:14px; color:#222530; background:#fff; outline:none; transition:border-color .15s, box-shadow .15s; -webkit-appearance:none; -moz-appearance:none; appearance:none; }
  .hb-input:focus, .hb-select:focus { border-color:#5c7c68; box-shadow:0 0 0 3px rgba(92,124,104,.18); }
  .hb-select { padding-right:38px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23717784' d='M6 8 0 2 1.5.5 6 5 10.5.5 12 2z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
  textarea.hb-input { height:auto; padding:12px 14px; line-height:1.5; }
  .hb-input[type="date"]::-webkit-calendar-picker-indicator { cursor:pointer; opacity:.5; }
  .hb-input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity:1; }

  /* ===== Tarjetas / superficies ===== */
  .hb-card { background:#fff; border:1px solid #eaecf0; border-radius:16px; box-shadow:0 1px 2px 0 rgba(10,13,20,.06), 0 1px 3px 0 rgba(10,13,20,.04); }
  .hb-pill { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:500; background:#f2f5f8; color:#525866; }
  .hb-pill i { color:#5c7c68; }
  .hb-eyebrow { display:inline-flex; align-items:center; gap:7px; font-size:12px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#5c7c68; }
  .hb-eyebrow::before { content:""; width:22px; height:2px; border-radius:2px; background:#5c7c68; display:inline-block; }

  /* ===== Encabezados de sección ===== */
  .hb-section { padding:72px 0; }
  .hb-h2 { font-family:'Inter Tight','Inter',sans-serif; font-size:34px; line-height:1.15; font-weight:700; color:#171717; letter-spacing:-.01em; }
  .hb-sub { font-size:15px; color:#717784; margin-top:10px; }

  /* ===== Alertas (reutilizadas por el fragmento del backend) ===== */
  .alert { border-radius:12px; padding:12px 16px; font-size:14px; margin-bottom:14px; position:relative; border:1px solid transparent; }
  .alert .close { position:absolute; top:8px; right:12px; background:none; border:0; font-size:20px; line-height:1; color:inherit; opacity:.55; cursor:pointer; }
  .alert .close:hover { opacity:1; }
  .alert-success, .alert-ok { background:#e3f7ec; border-color:#c4ecd4; color:#1a7a47; }
  .alert-danger, .alert-err { background:#ffebec; border-color:#ffd0d3; color:#c02636; }
  .alert-warning, .alert-warn { background:#fff3eb; border-color:#ffe0cc; color:#b5540f; }
  .alert-info { background:#ebf1ff; border-color:#d5e0ff; color:#2647c0; }

  /* ===== Modal ===== */
  .hb-modal { position:fixed; inset:0; z-index:1000; display:none; align-items:flex-start; justify-content:center; padding:24px 16px; overflow-y:auto; }
  .hb-modal.is-open { display:flex; }
  .hb-modal__backdrop { position:fixed; inset:0; background:rgba(23,23,23,.55); backdrop-filter:blur(2px); }
  .hb-modal__dialog { position:relative; z-index:1; width:100%; max-width:720px; background:#fff; border-radius:18px; box-shadow:0 30px 70px -20px rgba(10,13,20,.5); margin:auto; animation:hbpop .22s ease; }
  .hb-modal__dialog--sm { max-width:560px; }
  @keyframes hbpop { from { opacity:0; transform:translateY(12px) scale(.98); } to { opacity:1; transform:none; } }
  .hb-modal__head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:20px 24px; border-bottom:1px solid #f2f5f8; }
  .hb-modal__title { font-family:'Inter Tight','Inter',sans-serif; font-size:19px; font-weight:700; color:#171717; }
  .hb-modal__close { width:34px; height:34px; border-radius:9px; border:1px solid #eaecf0; background:#fff; color:#717784; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; transition:background-color .15s; }
  .hb-modal__close:hover { background:#f5f7fa; color:#222530; }
  .hb-modal__body { padding:22px 24px; }
  body.hb-modal-open { overflow:hidden; }

  .hb-muted { color:#717784; }
  .hb-spinner { color:#99a0ae; text-align:center; padding:40px 0; }
</style>
