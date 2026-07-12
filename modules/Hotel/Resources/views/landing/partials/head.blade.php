@php
    // Cabecera compartida por la web de reservas (landing, blog y detalle de
    // entrada). Centraliza el sistema de diseño (paleta "Makai", tipografía
    // Inter, tokens de Tailwind, animaciones y clases de componentes) para que
    // las tres páginas mantengan exactamente la misma línea gráfica que
    // launchbase/makai.
    $pageTitle = $pageTitle ?? 'Reservas online';
    $pageDesc  = $pageDesc  ?? null;
@endphp
<meta charset="utf-8">
<title>{{ $pageTitle }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
<link rel="shortcut icon" href="/landing-reservas/favicon.ico">

<!-- Tipografía Inter (misma que el CRM/web Makai) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Inter+Tight:wght@500;600;700;800&display=swap" rel="stylesheet">

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
          float: '0 5px 5px rgba(0,0,0,0.04), 0 18px 16px rgba(0,0,0,0.05), 0 41px 22.5px rgba(0,0,0,0.04), 0 73px 30px rgba(0,0,0,0.02)',
        },
        transitionTimingFunction: {
          makai: 'cubic-bezier(.16,1,.3,1)',
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
  .hb-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:11px 20px; border-radius:999px; font-size:14px; font-weight:600; line-height:1; cursor:pointer; border:1px solid transparent; transition:background-color .18s cubic-bezier(.16,1,.3,1), border-color .18s, transform .18s cubic-bezier(.16,1,.3,1), box-shadow .18s; white-space:nowrap; }
  .hb-btn:active { transform:translateY(0) scale(.97); }
  .hb-btn-primary { background:#5c7c68; color:#fff; border-color:#5c7c68; box-shadow:0 10px 22px -10px rgba(92,124,104,.65); }
  .hb-btn-primary:hover { background:#4a6354; border-color:#4a6354; color:#fff; transform:translateY(-1px); }
  .hb-btn-ghost { background:#fff; color:#2b303b; border-color:#eaecf0; }
  .hb-btn-ghost:hover { background:#f5f7fa; border-color:#cacfd8; color:#222530; }
  .hb-btn-light { background:rgba(255,255,255,.12); color:#fff; border-color:rgba(255,255,255,.35); }
  .hb-btn-light:hover { background:rgba(255,255,255,.22); color:#fff; }
  .hb-btn-lg { padding:14px 26px; font-size:15px; }
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

  /* ============================================================
     MODAL estilo Makai (unit modal) — overlay + backdrop borroso
     + shell redondeado con animación de entrada.
     ============================================================ */
  .hb-modal { position:fixed; inset:0; z-index:1104; display:none; align-items:center; justify-content:center; padding:24px; overflow:auto; }
  .hb-modal.is-open { display:flex; }
  body.hb-modal-open { overflow:hidden; }
  .hb-modal__backdrop { position:absolute; inset:0; background:rgba(15,18,27,.55); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); }
  .hb-modal__shell { position:relative; width:100%; max-width:1040px; background:#f2f5f8; border:1px solid #eaecf0; border-radius:20px; overflow:hidden; box-shadow:0 32px 80px rgba(10,13,20,.35); display:flex; flex-direction:column; max-height:calc(100vh - 48px); margin:auto; }
  .hb-modal__shell--tall { height:min(760px, calc(100vh - 48px)); }
  .hb-modal__shell--sm { max-width:560px; }

  .hb-modal__head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px; background:#fff; border-bottom:1px solid #eaecf0; flex-shrink:0; }
  .hb-modal__head-left { display:flex; align-items:center; gap:10px; min-width:0; }
  .hb-modal__brand { width:26px; height:26px; border-radius:8px; background:#5c7c68; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-family:'Inter Tight',sans-serif; font-weight:700; font-size:13px; flex-shrink:0; }
  .hb-modal__unit { font-weight:600; font-size:14px; color:#525866; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .hb-modal__title { font-family:'Inter Tight','Inter',sans-serif; font-size:16px; font-weight:700; color:#171717; }
  .hb-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 9px 4px 7px; border-radius:7px; font-size:11px; font-weight:600; letter-spacing:.02em; }
  .hb-badge .dot { width:6px; height:6px; border-radius:50%; }
  .hb-badge-ok { background:#e3f7ec; border:1px solid #c4ecd4; color:#1a7a47; } .hb-badge-ok .dot { background:#1fc16b; }
  .hb-badge-off { background:#fde2e1; border:1px solid #fde2e1; color:#b91c1c; } .hb-badge-off .dot { background:#e2534e; }
  .hb-modal__close { width:32px; height:32px; border-radius:999px; background:#fff; border:1px solid #ebebeb; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:#5c5c5c; transition:all .15s ease; flex-shrink:0; }
  .hb-modal__close:hover { background:#f2f5f8; color:#171717; }

  .hb-modal__body { min-height:0; flex:1; overflow-y:auto; }
  .hb-modal__grid { display:grid; grid-template-columns:1.1fr 1fr; min-height:0; flex:1; overflow:hidden; }
  .hb-modal__left { background:#fff; border-right:1px solid #eaecf0; display:flex; flex-direction:column; min-height:0; }
  .hb-modal__left-inner { padding:22px 24px; overflow-y:auto; flex:1; min-height:0; }
  .hb-modal__right { display:flex; flex-direction:column; background:#f2f5f8; min-height:0; }

  /* Bloque de precio */
  .mt-price { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:32px; color:#5c7c68; line-height:1; letter-spacing:-.02em; }
  .mt-price small { font-size:13px; font-weight:500; color:#99a0ae; }
  .mt-peopleview { display:flex; align-items:center; gap:6px; padding:8px 24px; background:#fff3eb; border-bottom:1px solid #ffe6d5; color:#b75310; font-size:11px; }
  .mt-peopleview b { font-weight:700; }

  /* Cajas de estadística (capacidad / camas / m² / piso) */
  .mt-stats { display:flex; flex-wrap:wrap; gap:8px; }
  .mt-stat { flex:1 1 0; min-width:80px; background:#fff; border:1px solid #eaecf0; border-radius:10px; overflow:hidden; display:flex; flex-direction:column; }
  .mt-stat .value { display:flex; align-items:center; justify-content:center; gap:5px; padding:11px 6px; font-weight:600; font-size:15px; color:#525866; }
  .mt-stat .value i { color:#5c7c68; }
  .mt-stat .label { background:#f2f5f8; border-top:1px solid #eaecf0; padding:5px 4px; text-align:center; font-size:9px; font-weight:600; color:#99a0ae; letter-spacing:.06em; text-transform:uppercase; }
  .mt-divider { height:1px; background:#ebebeb; margin:18px 0; }

  /* Galería del panel derecho */
  .mt-gallery { position:relative; flex:1; background:#e9edf1; overflow:hidden; display:flex; align-items:center; justify-content:center; min-height:320px; }
  .mt-gallery img { width:100%; height:100%; object-fit:cover; display:block; }
  .mt-thumbs { display:flex; gap:8px; padding:12px; background:#fff; border-top:1px solid #eaecf0; overflow-x:auto; flex-shrink:0; }
  .mt-thumb { width:60px; height:46px; object-fit:cover; border-radius:8px; border:2px solid transparent; cursor:pointer; flex-shrink:0; transition:border-color .15s; }
  .mt-thumb.active { border-color:#5c7c68; }

  @keyframes mtBackdropIn { from { opacity:0; } to { opacity:1; } }
  @keyframes mtPanelIn { from { opacity:0; transform:translateY(24px) scale(.96); } to { opacity:1; transform:none; } }
  .hb-modal.is-opening .hb-modal__backdrop { animation:mtBackdropIn .25s ease both; }
  .hb-modal.is-opening .hb-modal__shell { animation:mtPanelIn .42s cubic-bezier(.16,1,.3,1) both; }

  @media (max-width:820px) {
    .hb-modal__grid { grid-template-columns:1fr; overflow-y:auto; }
    .hb-modal__left { border-right:none; }
    .hb-modal__right { order:-1; }
    .hb-modal__shell--tall { height:auto; }
    .mt-gallery { min-height:240px; }
  }
  @media (max-width:560px) {
    .hb-modal { padding:12px; }
    .hb-modal__shell { border-radius:16px; }
  }

  /* ============================================================
     Animaciones de revelado al hacer scroll (como makai fgLazyIn)
     ============================================================ */
  @keyframes hbReveal { 0% { opacity:0; transform:translateY(22px) scale(.98); } 100% { opacity:1; transform:none; } }
  .reveal { opacity:0; }
  .reveal.is-in { animation:hbReveal .6s cubic-bezier(.16,1,.3,1) both; animation-delay:calc(var(--reveal-i, 0) * 70ms); }

  @keyframes hbPulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.4; transform:scale(.7); } }
  .hb-live-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:#5c7c68; box-shadow:0 0 6px rgba(92,124,104,.6); animation:hbPulse 1.6s infinite; }

  .hb-muted { color:#717784; }
  .hb-spinner { color:#99a0ae; text-align:center; padding:40px 0; }

  @media (prefers-reduced-motion: reduce) {
    .reveal.is-in, .hb-modal.is-opening .hb-modal__backdrop, .hb-modal.is-opening .hb-modal__shell, .hb-live-dot, .hb-btn { animation:none !important; transition:none !important; }
    .reveal { opacity:1 !important; }
    html { scroll-behavior:auto; }
  }
</style>

<script>
  // Revelado progresivo al hacer scroll (IntersectionObserver, patrón makai).
  window.hbInitReveal = function () {
    var els = document.querySelectorAll('.reveal:not(.is-in)');
    if (!('IntersectionObserver' in window)) { els.forEach(function (el) { el.classList.add('is-in'); }); return; }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
    }, { threshold:.12, rootMargin:'0px 0px -8% 0px' });
    els.forEach(function (el) { io.observe(el); });
  };
  document.addEventListener('DOMContentLoaded', function () { window.hbInitReveal(); });
</script>
