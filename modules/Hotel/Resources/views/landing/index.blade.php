<!DOCTYPE HTML>
<html lang="es">
<head>
@php
    use Modules\Hotel\Models\HotelLandingSetting;

    $hotelName = $establishment->description ?? 'Hotel';
    // Configuración de personalización de la web (con valores por defecto).
    $cfg = $settings ?? HotelLandingSetting::mergeDefaults([]);
@endphp
@include('hotel::landing.partials.head', ['pageTitle' => $hotelName.' · Reservas online', 'pageDesc' => 'Reserva tu habitación en '.$hotelName.'. Disponibilidad y precios en tiempo real.'])

<style>
  /* ===== Hero (patrón home Makai: intro imagen+texto + slider horizontal) ===== */
  .hr-stage { position:relative; background:#12140f; height:min(88vh, 880px); min-height:600px; overflow:hidden; }
  .hr-track { display:flex; height:100%; transform:translateX(0); transition:transform .85s cubic-bezier(.22,1,.36,1); will-change:transform; }
  .hr-cell { position:relative; flex:0 0 100%; width:100%; height:100%; overflow:hidden; }
  .hr-photo { position:absolute; inset:0; background-size:cover; background-position:center; transform:scale(1.08); transition:transform 7.5s ease; }
  .hr-cell.is-active .hr-photo { transform:scale(1); }
  /* Degradado en dos capas + viñeta: legible bajo el nav flotante y fundido abajo. */
  .hr-overlay { position:absolute; inset:0; z-index:1; background:
      linear-gradient(180deg, rgba(18,20,15,.56) 0%, rgba(18,20,15,.12) 30%, rgba(18,20,15,.30) 62%, rgba(18,20,15,.82) 100%),
      radial-gradient(130% 90% at 50% 32%, rgba(18,20,15,0) 42%, rgba(18,20,15,.34) 100%); }
  .hr-cell-caption { position:absolute; inset:0; z-index:3; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; color:#fff; padding:96px 22px 150px; }
  .hr-caption-inner { width:100%; max-width:840px; }

  /* Chip / título / subtítulo (compartidos por las celdas de imagen). */
  .hr-chip { display:inline-flex; align-items:center; gap:9px; padding:7px 15px; margin-bottom:22px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.24); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); font-size:12.5px; font-weight:600; letter-spacing:.03em; color:#fff; }
  .hr-chip .stars { color:#f4c542; letter-spacing:1.5px; font-size:11px; }
  .hr-title { font-family:'Inter Tight','Inter',sans-serif; font-size:clamp(33px,5.6vw,62px); font-weight:700; line-height:1.06; letter-spacing:-.025em; margin:0 auto 18px; max-width:14ch; text-shadow:0 4px 40px rgba(0,0,0,.4); }
  .hr-subtitle { font-size:clamp(15px,2vw,20px); font-weight:400; max-width:600px; margin:0 auto; color:rgba(255,255,255,.9); text-shadow:0 2px 18px rgba(0,0,0,.4); line-height:1.55; }

  /* Wordmark gigante de la celda intro ("imagen con texto" makai). */
  .hr-wordmark { display:block; font-family:'Inter Tight','Inter',sans-serif; font-weight:800; text-transform:uppercase; font-size:clamp(42px,10.5vw,138px); line-height:.9; letter-spacing:-.01em; max-width:13ch; margin:0 auto 8px; text-wrap:balance;
    background:linear-gradient(180deg, rgba(255,255,255,.97) 32%, rgba(255,255,255,.55) 100%); -webkit-background-clip:text; background-clip:text; color:transparent; -webkit-text-fill-color:transparent;
    filter:drop-shadow(0 8px 34px rgba(0,0,0,.4)); }

  /* Entrada: el wordmark cae desde arriba; el resto sube desde abajo (staggered). */
  .hr-anim-down { opacity:0; transform:translateY(-135%); }
  .hr-anim-up   { opacity:0; transform:translateY(38px); }
  #hero.is-revealed .hr-cell--hero .hr-anim-down { animation:hrDrop 1.5s cubic-bezier(.22,1,.36,1) .12s both; }
  #hero.is-revealed .hr-cell--hero .hr-anim-up.d1 { animation:hrRise 1.1s cubic-bezier(.22,1,.36,1) .28s both; }
  #hero.is-revealed .hr-cell--hero .hr-anim-up.d2 { animation:hrRise 1.1s cubic-bezier(.22,1,.36,1) .5s both; }
  @keyframes hrDrop { 0% { opacity:0; transform:translateY(-135%); } 58% { opacity:1; } 100% { opacity:1; transform:translateY(0); } }
  @keyframes hrRise { 0% { opacity:0; transform:translateY(38px); } 100% { opacity:1; transform:translateY(0); } }

  /* Celdas de imagen (slides): su caption aparece al activarse. */
  .hr-cell--img .hr-caption-inner { opacity:0; transform:translateY(28px); transition:opacity .8s cubic-bezier(.16,1,.3,1), transform .8s cubic-bezier(.16,1,.3,1); }
  .hr-cell--img.is-active .hr-caption-inner { opacity:1; transform:none; transition-delay:.25s; }

  /* Indicador de píldora (segmentos con relleno temporizado, como makai). */
  .hr-nav { position:absolute; left:50%; bottom:132px; transform:translateX(-50%); z-index:6; display:flex; align-items:center; gap:8px; padding:9px 14px; border-radius:999px; background:rgba(18,20,15,.30); border:1px solid rgba(255,255,255,.24); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); box-shadow:0 6px 20px rgba(0,0,0,.18); }
  .hr-seg { position:relative; width:26px; height:4px; border-radius:999px; background:rgba(255,255,255,.45); border:0; padding:0; cursor:pointer; overflow:hidden; transition:width .4s cubic-bezier(.22,1,.36,1); }
  .hr-seg.is-active { width:44px; }
  .hr-seg-fill { position:absolute; inset:0 auto 0 0; width:0; background:#fff; border-radius:999px; }
  .hr-seg.is-active .hr-seg-fill { width:100%; transition:width var(--seg-dur,5200ms) linear; }
  .hr-scroll { position:absolute; left:50%; bottom:88px; transform:translateX(-50%); z-index:6; width:38px; height:38px; border-radius:999px; border:1px solid rgba(255,255,255,.35); background:rgba(18,20,15,.34); backdrop-filter:blur(6px); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; animation:heroBounce 2.2s ease-in-out infinite; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .hr-scroll:hover { background:rgba(18,20,15,.5); color:#fff; }
  @keyframes heroBounce { 0%,100% { transform:translate(-50%,0); } 50% { transform:translate(-50%,7px); } }

  /* ===== Celda intro: composición por capas de Makai (cielo · nubes · texto · edificio) ===== */
  .hr-hero-makai { background:#cfe7f5; }
  /* Cielo: SKY.png ya trae nubes; una deriva lenta las mueve sin costuras. */
  .hr-sky { position:absolute; top:0; left:-6%; width:112%; height:100%; max-width:none; object-fit:cover; object-position:center top; z-index:1; pointer-events:none; user-select:none; animation:hrCloudDrift 40s ease-in-out infinite alternate; }
  /* Wordmark en una sola línea que siempre cabe en la franja de cielo, por
     encima del edificio (evita que una 2ª línea quede tapada). */
  .hr-wordmark2 { position:absolute; left:0; right:0; top:22%; z-index:3; margin:0 auto; padding:0 16px; max-width:11ch; text-align:center; font-family:'Inter Tight','Inter',sans-serif; font-weight:800; text-transform:uppercase; font-size:clamp(34px,8.4vw,128px); line-height:.96; letter-spacing:.01em; text-wrap:balance;
    background:linear-gradient(to bottom, rgba(255,255,255,.97) 20%, rgba(255,255,255,.66) 100%); -webkit-background-clip:text; background-clip:text; color:transparent; -webkit-text-fill-color:transparent; mix-blend-mode:luminosity; pointer-events:none; }
  /* El borde superior del edificio se funde con el cielo (elimina la costura
     entre SKY.png y MAKAI.png). */
  /* Edificio: PNG con fondo transparente (sin cielo propio) → se compone limpio
     sobre SKY.png, sin ninguna costura. */
  .hr-building { position:absolute; left:0; bottom:-2%; width:100%; height:auto; z-index:4; pointer-events:none; user-select:none; filter:drop-shadow(0 24px 44px rgba(0,0,0,.20)); }
  /* Fundido inferior del edificio hacia el fondo de la página / buscador. */
  .hr-hero-makai::after { content:""; position:absolute; left:0; right:0; bottom:-1px; height:16%; z-index:5; pointer-events:none; background:linear-gradient(to top, #f4f4f4 0%, rgba(244,244,244,.65) 42%, rgba(244,244,244,0) 100%); }

  /* Entradas exactas de Makai: el texto cae desde arriba, el edificio sube desde abajo. */
  .hr-wordmark2 { opacity:0; transform:translateY(-180%); }
  .hr-building  { opacity:0; transform:translateY(105%); }
  #hero.is-revealed .hr-cell--hero .hr-wordmark2 { animation:mkTextEnter 1.6s cubic-bezier(.22,1,.36,1) .1s both; }
  #hero.is-revealed .hr-cell--hero .hr-building  { animation:mkBuildEnter 1.6s cubic-bezier(.22,1,.36,1) .1s both; }
  @keyframes mkTextEnter  { 0% { transform:translateY(-180%); opacity:0; } 60% { opacity:1; } 100% { transform:translateY(0); opacity:1; } }
  @keyframes mkBuildEnter { 0% { transform:translateY(105%);  opacity:0; } 40% { opacity:1; } 100% { transform:translateY(0); opacity:1; } }
  @keyframes hrCloudDrift { 0% { transform:translateX(-3%); } 100% { transform:translateX(3%); } }

  @media (max-width:640px) {
    .hr-wordmark2 { top:19%; font-size:clamp(28px,12vw,72px); }
    .hr-building { width:200%; left:50%; margin-left:-100%; bottom:0; }
  }

  /* ===== Loader de marca (intro tipo Makai) ===== */
  #hbLoader { position:fixed; inset:0; z-index:2000; display:flex; align-items:center; justify-content:center; background:#f4f4f4; transition:opacity .6s ease, visibility .6s ease; }
  #hbLoader.is-hidden { opacity:0; visibility:hidden; }
  .ml-inner { display:flex; flex-direction:column; align-items:center; gap:24px; animation:ml-fade-in .7s ease both; }
  .ml-rings { position:relative; width:118px; height:118px; }
  .ml-ring { position:absolute; inset:0; margin:auto; width:34px; height:34px; border-radius:50%; border:1.5px solid #5c7c68; transform:scale(.3); opacity:0; animation:ml-ripple 2.4s cubic-bezier(.22,.61,.36,1) infinite; }
  .ml-ring:nth-child(2){ animation-delay:.6s; }
  .ml-ring:nth-child(3){ animation-delay:1.2s; }
  .ml-core { position:absolute; inset:0; margin:auto; width:44px; height:44px; border-radius:50%; background:#5c7c68; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Inter Tight',sans-serif; font-weight:800; font-size:20px; animation:ml-pulse 2.4s ease-in-out infinite; }
  .ml-name { font-family:'Inter Tight',sans-serif; font-weight:700; font-size:15px; letter-spacing:.02em; color:#2b303b; }
  .ml-bar { width:150px; height:3px; border-radius:99px; background:rgba(92,124,104,.15); overflow:hidden; }
  .ml-bar span { display:block; height:100%; width:40%; border-radius:99px; background:linear-gradient(90deg, transparent, #5c7c68, transparent); animation:ml-slide 1.3s ease-in-out infinite; }
  @keyframes ml-ripple { 0% { transform:scale(.3); opacity:0; } 15% { opacity:.55; } 100% { transform:scale(3.2); opacity:0; } }
  @keyframes ml-pulse { 0%,100% { transform:scale(1); } 50% { transform:scale(.92); } }
  @keyframes ml-slide { 0% { transform:translateX(-120%); } 100% { transform:translateX(330%); } }
  @keyframes ml-fade-in { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }

  /* Accesibilidad: sin movimiento, todo el contenido del hero visible al instante. */
  @media (prefers-reduced-motion: reduce) {
    .hr-anim-down, .hr-anim-up, .hr-cell--img .hr-caption-inner { opacity:1 !important; transform:none !important; animation:none !important; transition:none !important; }
    .hr-photo, .hr-track { transition:none !important; }
    .ml-ring { animation:none; } .ml-ring:first-child { opacity:.35; transform:scale(2.2); }
    .ml-bar span { animation:none; width:100%; }
  }

  /* ===== Tarjeta de habitación (estilo unit card Makai) ===== */
  .rc { position:relative; display:flex; flex-direction:column; background:#fff; border:1px solid #eaecf0; border-radius:24px; overflow:hidden; transition:box-shadow .35s cubic-bezier(.16,1,.3,1), transform .35s cubic-bezier(.16,1,.3,1); }
  .rc:hover { box-shadow:0 22px 48px -14px rgba(10,13,20,.24); transform:translateY(-3px); }
  .rc-inner { padding:6px 6px 0; }
  .rc-img { position:relative; height:216px; border-radius:20px; overflow:hidden; background:#f2f5f8; cursor:pointer; }
  .rc-img .bg { position:absolute; inset:0; background:center/cover no-repeat; transition:transform .6s cubic-bezier(.16,1,.3,1); }
  .rc:hover .rc-img .bg { transform:scale(1.06); }
  .rc-chips { position:absolute; top:0; left:0; right:0; display:flex; justify-content:space-between; align-items:flex-start; gap:8px; padding:10px; z-index:2; }
  .rc-cat { display:inline-flex; align-items:center; padding:5px 12px; border-radius:100px; background:rgba(255,255,255,.92); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#2b303b; font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,.10); }
  .rc-fav { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:100px; background:rgba(255,255,255,.92); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#b5540f; font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,.10); }
  .rc-fav i { color:#f4a52b; }
  .rc-body { display:flex; flex-direction:column; gap:14px; padding:12px 10px; }
  .rc-head { display:flex; flex-direction:column; gap:3px; }
  .rc-title-row { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
  .rc-name { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:18px; color:#171717; line-height:1.25; }
  .rc-sub { font-size:12.5px; color:#99a0ae; font-weight:500; }
  .rc-divider { height:1px; background:#ebebeb; margin:3px 0; }
  .rc-price-row { display:flex; align-items:flex-end; gap:8px; flex-wrap:wrap; }
  .rc-price { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:24px; color:#5c7c68; line-height:1; }
  .rc-price small { font-size:12px; font-weight:500; color:#a3a3a3; }
  .rc-total { font-size:12px; font-weight:600; color:#5c7c68; }
  .rc-stats { display:flex; align-items:stretch; border:1px solid #f2f5f8; border-radius:12px; overflow:hidden; }
  .rc-stat { flex:1 1 0; min-width:0; display:flex; flex-direction:column; align-items:center; gap:3px; padding:10px 4px; }
  .rc-stat + .rc-stat { border-left:1px solid #f2f5f8; }
  .rc-stat i { font-size:16px; color:#5c7c68; }
  .rc-stat .v { font-size:11px; font-weight:600; color:#525866; letter-spacing:.2px; white-space:nowrap; }
  .rc-actions { display:flex; gap:8px; }
  .rc-btn-info { flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 16px; background:#f2f5f8; border:0; border-radius:12px; font-weight:600; font-size:13.5px; color:#717784; cursor:pointer; transition:background .2s; }
  .rc-btn-info:hover { background:#e7eaef; color:#525866; }
  .rc-btn-cta { flex:1 1 0; min-width:0; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 16px; border-radius:12px; background:#5c7c68; color:#fff; border:1px solid #5c7c68; font-weight:600; font-size:13.5px; cursor:pointer; box-shadow:0 1px 2px rgba(14,18,27,.2); transition:background .2s, transform .15s; }
  .rc-btn-cta:hover { background:#4a6354; }
  .rc-btn-cta:active { transform:scale(.98); }

  /* ===== Buscador flotante ===== */
  .hr-search { position:relative; z-index:20; }
  #reservation-form.hr-search { margin-top:-70px; }
  .hr-search__card { display:grid; grid-template-columns:1fr 1fr auto auto; gap:16px; align-items:end; padding:22px; background:#fff; border:1px solid #eaecf0; border-radius:20px; box-shadow:0 24px 60px -20px rgba(10,13,20,.30); }
  .hr-field { min-width:0; }
  .hr-datetime { display:flex; gap:8px; }
  .hr-datetime input[type="date"] { flex:1 1 60%; min-width:0; }
  .hr-datetime input[type="time"] { flex:1 1 40%; min-width:0; padding:0 6px; text-align:center; }

  /* Selector de huéspedes */
  .hr-guests { position:relative; }
  .hr-guests-box { height:46px; border:1px solid #eaecf0; border-radius:10px; background:#fff; padding:0 14px; display:flex; align-items:center; justify-content:space-between; gap:12px; cursor:pointer; font-size:14px; color:#222530; min-width:170px; white-space:nowrap; transition:border-color .15s, box-shadow .15s; }
  .hr-guests-box i { color:#99a0ae; }
  .hr-guests-box.is-open { border-color:#5c7c68; box-shadow:0 0 0 3px rgba(92,124,104,.18); }
  .hr-guests-pop { position:absolute; left:0; bottom:calc(100% + 10px); width:280px; background:#fff; border:1px solid #eaecf0; border-radius:14px; box-shadow:0 24px 60px -18px rgba(10,13,20,.30); padding:18px; z-index:60; display:none; }
  .hr-guests-pop.is-open { display:block; }
  .hr-pop-field { margin-bottom:14px; }
  .hr-pop-field > label { display:block; font-size:13px; font-weight:600; color:#2b303b; margin-bottom:7px; }
  .hr-btn-search { height:46px; }

  /* ---- Tablet ---- */
  @media (max-width:991px) {
    .hr-stage { height:74vh; min-height:540px; }
    .hr-cell-caption { padding:104px 24px 140px; }
    .hr-nav { bottom:118px; }
    .hr-scroll { bottom:74px; }
    .hr-search__card { grid-template-columns:1fr 1fr; }
    .hr-field--guests, .hr-field--submit { grid-column:auto; }
    .hr-field--submit { grid-column:1 / -1; }
    .hr-btn-search { width:100%; }
  }
  /* ---- Móvil ---- */
  @media (max-width:640px) {
    .hr-stage { height:auto; min-height:0; }
    .hr-cell { min-height:64vh; }
    .hr-cell--hero { min-height:58vh; }
    .hr-cell-caption { padding:112px 20px 108px; }
    .hr-chip { margin-bottom:16px; }
    .hr-title { font-size:clamp(29px,8.5vw,40px); margin-bottom:14px; }
    .hr-subtitle { font-size:16px; }
    .hr-wordmark { font-size:clamp(40px,15vw,72px); }
    .hr-nav { bottom:64px; }
    .hr-scroll { display:none; }
    #reservation-form.hr-search { margin-top:-40px; }
    .hr-search__card { grid-template-columns:1fr; padding:16px; gap:14px; border-radius:18px; }
    .hr-guests-box { min-width:0; }
    .hr-guests-pop { width:100%; }
  }
  @media (max-width:380px) {
    .hr-title { font-size:26px; }
    .hr-cell { min-height:80vh; }
  }

  /* ===== Galería lightbox ===== */
  .hb-lightbox { position:fixed; inset:0; z-index:1100; display:none; align-items:center; justify-content:center; background:rgba(23,23,23,.9); padding:24px; }
  .hb-lightbox.is-open { display:flex; }
  .hb-lightbox img { max-width:92vw; max-height:88vh; border-radius:12px; box-shadow:0 30px 80px rgba(0,0,0,.5); }
  .hb-lightbox__close { position:absolute; top:22px; right:26px; color:#fff; font-size:30px; cursor:pointer; opacity:.8; }
  .hb-lightbox__close:hover { opacity:1; }

  .lined-title { position:relative; }
</style>
</head>

<body>

{{-- Loader de marca (intro tipo home Makai): se muestra un instante y, al
     levantarse, revela y reproduce la animación de entrada del hero. --}}
<div id="hbLoader" aria-hidden="true">
  <div class="ml-inner">
    <div class="ml-rings">
      <span class="ml-ring"></span><span class="ml-ring"></span><span class="ml-ring"></span>
      <span class="ml-core">{{ mb_strtoupper(mb_substr($hotelName, 0, 1)) }}</span>
    </div>
    <div class="ml-name">{{ $hotelName }}</div>
    <div class="ml-bar"><span></span></div>
  </div>
</div>
{{-- Sin JS: no ocultar contenido animado ni bloquear con el loader. --}}
<noscript><style>.hr-anim-down,.hr-anim-up,.hr-cell--img .hr-caption-inner{opacity:1!important;transform:none!important}#hbLoader{display:none!important}</style></noscript>

@include('hotel::landing.partials.header', ['onLanding' => true, 'activeNav' => 'inicio'])

@php
  // Garantizamos al menos una diapositiva aunque el tenant guarde la lista vacía.
  $slides = !empty($cfg['slides']) && is_array($cfg['slides'])
    ? $cfg['slides']
    : [['image' => null, 'title' => '', 'subtitle' => 'Reserva tu estancia con nosotros', 'stars' => true]];

  // La primera "slide" es la portada intro (imagen + wordmark del hotel);
  // las siguientes continúan como slides normales.
  $firstSlide = $slides[0];
  $introImg   = HotelLandingSetting::imageUrl($firstSlide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
  $introSub   = trim($firstSlide['subtitle'] ?? '') !== '' ? $firstSlide['subtitle'] : 'Reserva tu estancia con nosotros';
  $introStars = $firstSlide['stars'] ?? true;
  $restSlides = array_slice($slides, 1);
  $cellCount  = 1 + count($restSlides);
@endphp

<!-- ===== Hero ===== -->
<section class="hr-stage {{ $cellCount > 1 ? 'has-slider' : '' }}" id="hero">
  <div class="hr-track" id="hrTrack">

    <!-- Celda 0: portada intro — composición por capas de Makai
         (cielo · nubes · wordmark que cae · edificio que sube) -->
    <div class="hr-cell hr-cell--hero hr-hero-makai is-active">
      <img class="hr-sky" src="/landing-reservas/images/hero/SKY.png" alt="" aria-hidden="true">
      <h1 class="hr-wordmark2">{{ $hotelName }}</h1>
      <img class="hr-building" src="/landing-reservas/images/hero/MAKAI.png" alt="{{ $hotelName }}">
    </div>

    <!-- Celdas 1..n: slides -->
    @foreach($restSlides as $slide)
      @php
        $slideImg   = HotelLandingSetting::imageUrl($slide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
        $slideTitle = trim($slide['title'] ?? '') !== '' ? $slide['title'] : $hotelName;
        $showStars  = $slide['stars'] ?? true;
      @endphp
      <div class="hr-cell hr-cell--img">
        <div class="hr-photo" style="background-image:url('{{ $slideImg }}');"></div>
        <div class="hr-overlay"></div>
        <div class="hr-cell-caption">
          <div class="hr-caption-inner">
            <span class="hr-chip">
              @if($showStars)<span class="stars">@for($s=0;$s<5;$s++)★@endfor</span>@endif
              {{ $hotelName }}
            </span>
            <h2 class="hr-title">{{ $slideTitle }}</h2>
            @if(!empty($slide['subtitle']))<p class="hr-subtitle">{{ $slide['subtitle'] }}</p>@endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  @if($cellCount > 1)
    <div class="hr-nav" id="hrNav" role="tablist" aria-label="Portada">
      @for($s = 0; $s < $cellCount; $s++)
        <button type="button" class="hr-seg {{ $s === 0 ? 'is-active' : '' }}" data-i="{{ $s }}" aria-label="Ir a la vista {{ $s + 1 }}"><span class="hr-seg-fill"></span></button>
      @endfor
    </div>
  @endif

  <a href="#rooms-results" class="hr-scroll nav-scroll" aria-label="Desplázate"><i class="fa fa-angle-down"></i></a>
</section>

<script>
(function () {
  // Controlador del hero: track horizontal (celda intro + slides) con
  // indicador de píldora, arrancado tras la revelación del loader.
  var hero  = document.getElementById('hero');
  var track = document.getElementById('hrTrack');
  var nav   = document.getElementById('hrNav');
  if (!hero || !track) return;
  var cells = track.children, count = cells.length;
  var segs  = nav ? nav.querySelectorAll('.hr-seg') : [];
  var INTRO_MS = 6200, SLIDE_MS = 5200;
  var idx = 0, timer = null, revealed = false;

  function fill(seg, active, dur) {
    var f = seg.querySelector('.hr-seg-fill');
    if (!f) return;
    if (active) {
      f.style.transition = 'none'; f.style.width = '0'; void f.offsetWidth;
      f.style.transition = 'width ' + dur + 'ms linear'; f.style.width = '100%';
    } else { f.style.transition = 'none'; f.style.width = '0'; }
  }
  function activate(n) {
    idx = (n + count) % count;
    track.style.transform = 'translateX(' + (-idx * 100) + '%)';
    for (var i = 0; i < count; i++) cells[i].classList.toggle('is-active', i === idx);
    var dur = idx === 0 ? INTRO_MS : SLIDE_MS;
    for (var k = 0; k < segs.length; k++) {
      segs[k].classList.toggle('is-active', k === idx);
      fill(segs[k], k === idx, dur);
    }
  }
  function schedule() {
    if (count < 2) return;
    clearTimeout(timer);
    timer = setTimeout(function () { activate(idx + 1); schedule(); }, idx === 0 ? INTRO_MS : SLIDE_MS);
  }
  for (var k = 0; k < segs.length; k++) {
    segs[k].addEventListener('click', function () { activate(parseInt(this.getAttribute('data-i'), 10)); schedule(); });
  }

  window.hbHeroReveal = function () {
    if (revealed) return; revealed = true;
    hero.classList.add('is-revealed');
    activate(0);
    schedule();
  };
  // Salvaguarda por si el loader no dispara la revelación.
  setTimeout(function () { window.hbHeroReveal(); }, 2600);
})();

(function () {
  // Loader de marca: mínimo visible, luego revela el hero.
  var loader = document.getElementById('hbLoader');
  if (!loader) { if (window.hbHeroReveal) window.hbHeroReveal(); return; }
  var start = Date.now(), MIN = 1300, done = false;
  function hide() {
    if (done) return; done = true;
    if (window.hbHeroReveal) window.hbHeroReveal();
    loader.classList.add('is-hidden');
    setTimeout(function () { if (loader.parentNode) loader.parentNode.removeChild(loader); }, 650);
  }
  if (document.readyState === 'complete') { setTimeout(hide, MIN); }
  else { window.addEventListener('load', function () { var r = MIN - (Date.now() - start); setTimeout(hide, r > 0 ? r : 0); }); }
  setTimeout(hide, 6000); // nunca bloquear más de 6s
})();
</script>

<!-- ===== Buscador de disponibilidad ===== -->
<section class="hr-search" id="reservation-form">
  <div class="max-w-6xl mx-auto px-6">
    <form class="hr-search__card" id="searchform" onsubmit="return false;">
      <div class="hr-field">
        <label class="hb-label" for="checkin_date"><i class="fa fa-calendar"></i> Entrada</label>
        <div class="hr-datetime">
          <input name="checkin" type="date" id="checkin_date" class="hb-input" required>
          <input name="checkin_time" type="time" id="checkin_time" class="hb-input" value="14:00" required aria-label="Hora de entrada">
        </div>
      </div>
      <div class="hr-field">
        <label class="hb-label" for="checkout_date"><i class="fa fa-calendar"></i> Salida</label>
        <div class="hr-datetime">
          <input name="checkout" type="date" id="checkout_date" class="hb-input" required>
          <input name="checkout_time" type="time" id="checkout_time" class="hb-input" value="12:00" required aria-label="Hora de salida">
        </div>
      </div>
      <div class="hr-field hr-field--guests hr-guests">
        <label class="hb-label"><i class="fa fa-users"></i> Huéspedes</label>
        <div class="hr-guests-box" id="guestsToggle">
          <span id="guestsSummary">2 adultos</span>
          <i class="fa fa-angle-down"></i>
        </div>
        <div class="hr-guests-pop" id="guestsPop">
          <div class="hr-pop-field">
            <label>Adultos</label>
            <select name="adults" id="adults" class="hb-select">
              @for($i=1;$i<=8;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'adulto' : 'adultos' }}</option>@endfor
            </select>
          </div>
          <div class="hr-pop-field">
            <label>Niños</label>
            <select name="children" id="children" class="hb-select">
              @for($i=0;$i<=6;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'niño' : 'niños' }}</option>@endfor
            </select>
          </div>
          <button type="button" class="hb-btn hb-btn-ghost hb-btn-block" id="guestsSave">Guardar</button>
        </div>
      </div>
      <div class="hr-field hr-field--submit">
        <button type="submit" id="btn-search" class="hb-btn hb-btn-primary hr-btn-search"><i class="fa fa-search"></i> Buscar</button>
      </div>
    </form>
  </div>
</section>

<script>
(function () {
  // Abrir el calendario nativo al pulsar el campo de fecha.
  var dateInputs = document.querySelectorAll('.hr-search__card input[type="date"]');
  for (var k = 0; k < dateInputs.length; k++) {
    var openPicker = function () { try { if (this.showPicker) this.showPicker(); } catch (e) {} };
    dateInputs[k].addEventListener('click', openPicker);
  }

  // Selector de huéspedes.
  var gToggle  = document.getElementById('guestsToggle');
  var gPop     = document.getElementById('guestsPop');
  var gSave    = document.getElementById('guestsSave');
  var gSummary = document.getElementById('guestsSummary');
  var aSel     = document.getElementById('adults');
  var cSel     = document.getElementById('children');
  if (gToggle && gPop) {
    var updateSummary = function () {
      var a = parseInt(aSel.value, 10) || 0, c = parseInt(cSel.value, 10) || 0;
      var parts = [a + (a === 1 ? ' adulto' : ' adultos')];
      if (c > 0) parts.push(c + (c === 1 ? ' niño' : ' niños'));
      gSummary.textContent = parts.join(', ');
    };
    var closePop = function () { gPop.classList.remove('is-open'); gToggle.classList.remove('is-open'); };
    gToggle.addEventListener('click', function (e) { e.stopPropagation(); gPop.classList.toggle('is-open'); gToggle.classList.toggle('is-open'); });
    gPop.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', closePop);
    if (gSave) gSave.addEventListener('click', function () { updateSummary(); closePop(); });
    aSel.addEventListener('change', updateSummary);
    cSel.addEventListener('change', updateSummary);
    updateSummary();
  }
})();
</script>

<!-- ===== Habitaciones ===== -->
<section class="hb-section pt-16" id="rooms-results">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Alojamiento</span>
      <h2 class="hb-h2" id="rooms-heading">{{ $cfg['rooms_heading'] ?? 'Nuestras habitaciones' }}</h2>
      <p class="hb-sub" id="rooms-subheading">{{ $cfg['rooms_subheading'] ?? 'Selecciona fechas para ver disponibilidad y precios.' }}</p>
    </div>
    <div id="rooms-grid"></div>
  </div>
</section>

@if(($cfg['show_features'] ?? true) && count($cfg['features'] ?? []))
<!-- ===== Ventajas ===== -->
<section class="hb-section bg-white border-y border-ink-100">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="hb-eyebrow mb-3">Ventajas</span>
      <h2 class="hb-h2">{{ $cfg['features_heading'] ?? '¿Por qué reservar con nosotros?' }}</h2>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      @foreach($cfg['features'] as $feature)
      <div class="reveal hb-card p-7 text-center transition duration-300 ease-makai hover:shadow-panel hover:-translate-y-1" style="--reveal-i:{{ $loop->index }}">
        <div class="w-14 h-14 rounded-2xl bg-brand-tint text-brand flex items-center justify-center mx-auto mb-5">
          <i class="fa {{ $feature['icon'] ?? 'fa-star' }} text-2xl"></i>
        </div>
        <h3 class="font-display font-semibold text-lg text-ink-900 mb-2">{{ $feature['title'] ?? '' }}</h3>
        <p class="text-[14px] text-ink-500 leading-relaxed">{{ $feature['text'] ?? '' }}</p>
        @if(!empty($feature['link_text']))
        <a href="{{ $feature['link'] ?? '#' }}" class="nav-scroll inline-flex items-center gap-1.5 mt-4 text-[13px] font-semibold text-brand hover:text-brand-dark">{{ $feature['link_text'] }} <i class="fa fa-angle-right"></i></a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@if($cfg['show_parallax'] ?? true)
@php $parallaxImg = HotelLandingSetting::imageUrl($cfg['parallax']['image'] ?? null, HotelLandingSetting::DEFAULT_PARALLAX); @endphp
<!-- ===== Parallax ===== -->
<section class="relative bg-fixed bg-center bg-cover" style="background-image:url('{{ $parallaxImg }}');">
  <div class="bg-ink-950/70">
    <div class="max-w-3xl mx-auto px-6 py-28 text-center text-white">
      <i class="fa fa-star-o text-brand text-3xl mb-5"></i>
      <h3 class="font-display text-3xl md:text-4xl font-bold mb-4">{{ $hotelName }}</h3>
      <p class="text-lg text-white/85">{{ $cfg['parallax']['text'] ?? 'Vive una experiencia inolvidable' }}</p>
      @if(!empty($cfg['parallax']['button_text']))
        <a href="{{ $cfg['parallax']['button_link'] ?: '#rooms-results' }}" class="nav-scroll hb-btn hb-btn-light hb-btn-lg mt-8">{{ $cfg['parallax']['button_text'] }} <i class="fa fa-angle-right"></i></a>
      @endif
    </div>
  </div>
</section>
@endif

@if(($cfg['show_gallery'] ?? true) && count($cfg['gallery'] ?? []))
<!-- ===== Galería ===== -->
<section class="hb-section" id="gallery">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Galería</span>
      <h2 class="hb-h2">{{ $cfg['gallery_heading'] ?? 'Galería' }}</h2>
    </div>
    <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      @foreach($cfg['gallery'] as $gi => $galleryImg)
        @php $gImg = HotelLandingSetting::imageUrl($galleryImg, HotelLandingSetting::DEFAULT_GALLERY); @endphp
        <button type="button" class="reveal group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-ink-100 {{ $gi === 0 ? 'col-span-2 row-span-2 md:aspect-square' : '' }}" style="--reveal-i:{{ $gi }}" data-lightbox="{{ $gImg }}">
          <img src="{{ $gImg }}" alt="Imagen {{ $gi + 1 }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
          <span class="absolute inset-0 bg-ink-950/0 group-hover:bg-ink-950/30 transition flex items-center justify-center">
            <i class="fa fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
          </span>
        </button>
      @endforeach
    </div>
  </div>
</section>
@endif

@php
  $showTestimonials = ($cfg['show_testimonials'] ?? true) && count($cfg['testimonials'] ?? []);
  $showAbout        = ($cfg['show_about'] ?? true) && count($cfg['about']['tabs'] ?? []);
@endphp
@if($showTestimonials || $showAbout)
<section class="hb-section bg-white border-y border-ink-100">
  <div class="max-w-7xl mx-auto px-6 grid gap-14 lg:grid-cols-2">
    @if($showTestimonials)
    <div>
      <span class="hb-eyebrow mb-3">Opiniones</span>
      <h2 class="hb-h2 mb-8">{{ $cfg['testimonials_heading'] ?? 'Lo que opinan nuestros huéspedes' }}</h2>
      <div class="space-y-4">
        @foreach($cfg['testimonials'] as $t)
          @php $tImg = HotelLandingSetting::imageUrl($t['image'] ?? null, HotelLandingSetting::DEFAULT_REVIEW); @endphp
          <div class="reveal hb-card p-6 flex gap-4" style="--reveal-i:{{ $loop->index }}">
            <img src="{{ $tImg }}" alt="{{ $t['name'] ?? 'Huésped' }}" class="w-12 h-12 rounded-full object-cover shrink-0">
            <div>
              <div class="text-[#f4c542] text-sm mb-1.5">@for($s=0;$s<5;$s++)<i class="fa fa-star"></i>@endfor</div>
              <p class="text-[14px] text-ink-700 leading-relaxed mb-2">“{{ $t['text'] ?? '' }}”</p>
              <span class="text-[13px] font-semibold text-ink-900">{{ $t['name'] ?? '' }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($showAbout)
    @php $aboutImg = HotelLandingSetting::imageUrl($cfg['about']['image'] ?? null, HotelLandingSetting::DEFAULT_ABOUT); @endphp
    <div id="aboutTabs">
      <span class="hb-eyebrow mb-3">El hotel</span>
      <h2 class="hb-h2 mb-6">{{ $cfg['about_heading'] ?? 'Sobre el hotel' }}</h2>
      <div class="flex flex-wrap gap-2 mb-5">
        @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
          <button type="button" class="hb-tab px-4 py-2 rounded-full text-[13px] font-semibold transition {{ $ti === 0 ? 'bg-brand text-white' : 'bg-ink-100 text-ink-600 hover:bg-ink-200' }}" data-tab="{{ $ti }}">{{ $aboutTab['title'] ?? 'Pestaña' }}</button>
        @endforeach
      </div>
      @if($aboutImg)<img src="{{ $aboutImg }}" alt="{{ $cfg['about_heading'] ?? 'Hotel' }}" class="float-right ml-5 mb-3 w-40 rounded-2xl object-cover">@endif
      @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
        <div class="hb-tab-pane text-[15px] text-ink-600 leading-relaxed {{ $ti === 0 ? '' : 'hidden' }}" data-pane="{{ $ti }}">{{ $aboutTab['content'] ?? '' }}</div>
      @endforeach
    </div>
    @endif
  </div>
</section>
<script>
(function () {
  var wrap = document.getElementById('aboutTabs');
  if (!wrap) return;
  var tabs = wrap.querySelectorAll('.hb-tab');
  var panes = wrap.querySelectorAll('.hb-tab-pane');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('bg-brand','text-white'); t.classList.add('bg-ink-100','text-ink-600'); });
      tab.classList.add('bg-brand','text-white'); tab.classList.remove('bg-ink-100','text-ink-600');
      panes.forEach(function (p) { p.classList.toggle('hidden', p.getAttribute('data-pane') !== tab.getAttribute('data-tab')); });
    });
  });
})();
</script>
@endif

@if(isset($blogPosts) && $blogPosts->count())
<!-- ===== Blog ===== -->
<section class="hb-section" id="blog">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Blog</span>
      <h2 class="hb-h2">Nuestro blog</h2>
      <p class="hb-sub">Novedades, consejos y noticias del hotel.</p>
    </div>
    <div class="grid gap-7 md:grid-cols-3">
      @foreach($blogPosts as $post)
        @php
          $postImage = $post->cover_image ?: HotelLandingSetting::DEFAULT_GALLERY;
          $postDate  = optional($post->published_at)->format('d/m/Y');
        @endphp
        <article class="reveal hb-card overflow-hidden group transition duration-300 ease-makai hover:shadow-panel hover:-translate-y-1" style="--reveal-i:{{ $loop->index }}">
          <a href="{{ url('reservas/blog/'.$post->slug) }}" class="relative block aspect-[16/10] bg-ink-100 overflow-hidden">
            <span class="absolute inset-0 bg-center bg-cover transition duration-500 group-hover:scale-105" style="background-image:url('{{ $postImage }}');"></span>
            @if($post->hasVideoCover())<i class="fa fa-play-circle absolute inset-0 m-auto w-max h-max text-white text-5xl drop-shadow-lg"></i>@endif
          </a>
          <div class="p-6">
            @if($postDate)<div class="text-[12px] font-semibold text-brand uppercase tracking-wide mb-2"><i class="fa fa-calendar"></i> {{ $postDate }}</div>@endif
            <h3 class="font-display font-semibold text-lg text-ink-900 leading-snug mb-2"><a href="{{ url('reservas/blog/'.$post->slug) }}" class="hover:text-brand transition">{{ $post->title }}</a></h3>
            <p class="text-[14px] text-ink-500 leading-relaxed mb-3">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-brand hover:text-brand-dark">Leer más <i class="fa fa-angle-right"></i></a>
          </div>
        </article>
      @endforeach
    </div>
    <div class="text-center mt-10">
      <a href="{{ url('reservas/blog') }}" class="hb-btn hb-btn-ghost hb-btn-lg">Ver todo el blog</a>
    </div>
  </div>
</section>
@endif

@if($cfg['show_cta'] ?? true)
<!-- ===== Call to action ===== -->
<section class="pb-4">
  <div class="max-w-7xl mx-auto px-6">
    <div class="rounded-3xl bg-brand text-white px-8 py-12 md:px-14 md:py-14 flex flex-col md:flex-row items-center gap-8 justify-between shadow-hover">
      <h2 class="font-display text-2xl md:text-3xl font-bold leading-snug max-w-2xl text-center md:text-left">{{ $cfg['cta_text'] ?? '¿Listo para tu próxima estancia? Reserva ahora en línea.' }}</h2>
      <a href="#reservation-form" class="nav-scroll hb-btn hb-btn-lg bg-white text-brand-dark hover:bg-ink-50 shrink-0">{{ $cfg['cta_button'] ?? 'Ver disponibilidad' }} <i class="fa fa-angle-right"></i></a>
    </div>
  </div>
</section>
@endif

@include('hotel::landing.partials.footer', ['onLanding' => true])

<!-- ===== Modal: Detalle de habitación (estilo unit modal Makai) ===== -->
<div class="hb-modal" id="roomDetailModal">
  <div class="hb-modal__backdrop" data-close></div>
  <div class="hb-modal__shell hb-modal__shell--tall">
    <div class="hb-modal__head">
      <div class="hb-modal__head-left">
        <span class="hb-modal__brand">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
        <span class="hb-modal__unit" id="detail-unit">Habitación</span>
        <span id="detail-badge"></span>
      </div>
      <button type="button" class="hb-modal__close" data-close aria-label="Cerrar"><i class="fa fa-times"></i></button>
    </div>
    <div id="detail-content" style="flex:1;min-height:0;display:flex;flex-direction:column;">
      <div class="hb-spinner" style="margin:auto;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
    </div>
  </div>
</div>

<!-- ===== Modal: Reservar ===== -->
<div class="hb-modal" id="reserveModal">
  <div class="hb-modal__backdrop" data-close></div>
  <div class="hb-modal__shell hb-modal__shell--sm">
    <div class="hb-modal__head">
      <div class="hb-modal__head-left">
        <span class="hb-modal__brand">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
        <span class="hb-modal__title">Completa tu reserva</span>
      </div>
      <button type="button" class="hb-modal__close" data-close aria-label="Cerrar"><i class="fa fa-times"></i></button>
    </div>
    <div class="hb-modal__body" style="padding:22px 24px;">
      <div id="reserve-message"></div>
      <div class="rounded-xl bg-brand-tint border border-brand-soft px-4 py-3 mb-5 text-[13px] text-ink-700" id="reserve-summary"></div>
      <form id="reserveform" class="space-y-4">
        <input type="hidden" name="room" id="r-room">
        <input type="hidden" name="checkin" id="r-checkin">
        <input type="hidden" name="checkout" id="r-checkout">
        <input type="hidden" name="checkin_time" id="r-checkin-time">
        <input type="hidden" name="checkout_time" id="r-checkout-time">
        <input type="hidden" name="adults" id="r-adults">
        <input type="hidden" name="children" id="r-children">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-1">
            <label class="hb-label">Tipo doc.</label>
            <select class="hb-select" name="document_type" id="r-doctype">
              @foreach(($documentTypes ?? []) as $dt)
                <option value="{{ $dt['id'] }}">{{ $dt['description'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="hb-label">Número de documento</label>
            <div class="flex gap-2">
              <input type="text" class="hb-input" name="document_number" id="r-docnumber" placeholder="Nº de documento">
              <button class="hb-btn hb-btn-ghost shrink-0" type="button" id="r-doc-search"><i class="fa fa-search"></i></button>
            </div>
            <div class="text-[12px] mt-1.5 text-ink-500" id="r-doc-feedback"></div>
          </div>
        </div>
        <div>
          <label class="hb-label">Nombre / Razón social <span class="text-err">*</span></label>
          <input type="text" class="hb-input" name="name" id="r-name" required placeholder="Tu nombre completo">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="hb-label">Teléfono</label>
            <input type="text" class="hb-input" name="telephone" id="r-telephone" placeholder="Celular / teléfono">
          </div>
          <div>
            <label class="hb-label">E-mail</label>
            <input type="email" class="hb-input" name="email" id="r-email" placeholder="correo@ejemplo.com">
          </div>
        </div>
        <div>
          <label class="hb-label">Comentarios / solicitudes especiales</label>
          <textarea class="hb-input" name="notes" id="r-notes" rows="2" placeholder="Ej: llegada tardía, cuna, piso alto..."></textarea>
        </div>
        <button type="submit" class="hb-btn hb-btn-primary hb-btn-block hb-btn-lg" id="r-submit"><i class="fa fa-calendar-check-o"></i> Confirmar reserva</button>
      </form>
    </div>
  </div>
</div>

<!-- Lightbox de galería -->
<div class="hb-lightbox" id="hbLightbox">
  <span class="hb-lightbox__close" data-lightbox-close>&times;</span>
  <img src="" alt="">
</div>

<!-- ===== Lógica de reservas ===== -->
<script type="text/javascript">
jQuery(function ($) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var CURRENCY = 'S/';
    var CURRENT_ESTABLISHMENT = {!! json_encode($establishmentId ?? null) !!};
    var DATA = {
        rooms: {!! json_encode($rooms, JSON_UNESCAPED_UNICODE) !!},
        featured: {!! json_encode($featured, JSON_UNESCAPED_UNICODE) !!}
    };
    var SEARCH = { checkin: null, checkout: null, checkin_time: '14:00', checkout_time: '12:00', adults: 1, children: 0, active: false };
    var PLACEHOLDER = '/landing-reservas/images/rooms/356x228.gif';

    // ---- Helpers de modal (animación de entrada estilo Makai) ----
    function openModal(id) {
        var $m = $('#' + id).addClass('is-open');
        $('body').addClass('hb-modal-open');
        // Reproduce la animación de apertura (mismo patrón que la web makai).
        var el = $m.get(0);
        el.classList.remove('is-opening');
        void el.offsetWidth;
        el.classList.add('is-opening');
    }
    function closeModal(el) { $(el).closest('.hb-modal').removeClass('is-open is-opening'); if (!$('.hb-modal.is-open').length) $('body').removeClass('hb-modal-open'); }
    $(document).on('click', '[data-close]', function () { closeModal(this); });
    // Cerrar al pulsar fuera del diálogo (sobre el fondo del modal).
    $(document).on('click', '.hb-modal', function (e) { if (e.target === this) { $(this).removeClass('is-open'); if (!$('.hb-modal.is-open').length) $('body').removeClass('hb-modal-open'); } });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') { $('.hb-modal').removeClass('is-open'); $('body').removeClass('hb-modal-open'); } });

    // ---- utilidades ----
    function money(n) { return CURRENCY + ' ' + Number(n || 0).toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }
    function amenityIcon(a) {
        a = (a || '').toLowerCase();
        if (a.indexOf('wi') === 0 || a.indexOf('wifi') > -1 || a.indexOf('wi-fi') > -1) return 'fa-wifi';
        if (a.indexOf('tv') > -1) return 'fa-television';
        if (a.indexOf('aire') > -1 || a.indexOf('clima') > -1) return 'fa-snowflake-o';
        if (a.indexOf('desayun') > -1) return 'fa-coffee';
        if (a.indexOf('baño') > -1 || a.indexOf('bano') > -1) return 'fa-bath';
        if (a.indexOf('estacion') > -1 || a.indexOf('parking') > -1) return 'fa-car';
        if (a.indexOf('frigo') > -1 || a.indexOf('mini') > -1) return 'fa-glass';
        if (a.indexOf('caja') > -1) return 'fa-lock';
        if (a.indexOf('vista') > -1) return 'fa-eye';
        if (a.indexOf('jacuzzi') > -1 || a.indexOf('tina') > -1) return 'fa-tint';
        return 'fa-check-circle';
    }

    // ---- tarjeta de habitación (compacta, estilo unit card Makai) ----
    // La card muestra sólo lo esencial (imagen, nombre, ubicación, precio y
    // 3 datos clave). El resto de la información se ve en el modal de detalle.
    function roomCard(room) {
        var img = room.main_image || PLACEHOLDER;

        var priceHtml = room.min_price > 0
            ? '<span class="rc-price">' + money(room.min_price) + '</span><small>/ noche</small>'
            : '<span class="text-[14px] font-semibold text-ink-500">Consultar tarifa</span>';
        var total = (room.total && room.nights)
            ? '<span class="rc-total">· ' + room.nights + ' noche(s): ' + money(room.total) + '</span>' : '';

        // Subtítulo: categoría + piso (breve, como el subtitle de makai).
        var subParts = [esc(room.category)];
        if (room.floor) subParts.push(esc(room.floor));
        var sub = subParts.join(' · ');

        // Fila de estadísticas: sólo los datos disponibles.
        var stats = [];
        if (room.capacity) stats.push('<div class="rc-stat"><i class="fa fa-users"></i><span class="v">' + room.capacity + ' huésp.</span></div>');
        if (room.beds)     stats.push('<div class="rc-stat"><i class="fa fa-bed"></i><span class="v">' + esc(room.beds) + '</span></div>');
        if (room.size)     stats.push('<div class="rc-stat"><i class="fa fa-expand"></i><span class="v">' + room.size + ' m²</span></div>');
        var statsHtml = stats.length ? '<div class="rc-stats">' + stats.join('') + '</div>' : '';

        var fav = room.featured ? '<span class="rc-fav"><i class="fa fa-star"></i> Destacada</span>' : '<span></span>';

        return '' +
        '<div class="reveal rc">' +
          '<div class="rc-inner">' +
            '<div class="rc-img btn-detail" data-id="' + room.id + '">' +
              '<div class="bg" style="background-image:url(\'' + img + '\');"></div>' +
              '<div class="rc-chips"><span class="rc-cat">' + esc(room.category) + '</span>' + fav + '</div>' +
            '</div>' +
          '</div>' +
          '<div class="rc-body">' +
            '<div class="rc-head">' +
              '<div class="rc-title-row"><span class="rc-name">' + esc(room.name) + '</span></div>' +
              '<div class="rc-sub">' + sub + '</div>' +
              '<div class="rc-divider"></div>' +
              '<div class="rc-price-row">' + priceHtml + total + '</div>' +
            '</div>' +
            statsHtml +
            '<div class="rc-actions">' +
              '<button class="rc-btn-info btn-detail" data-id="' + room.id + '">Detalle</button>' +
              '<button class="rc-btn-cta btn-reserve" data-id="' + room.id + '"><i class="fa fa-calendar-check-o"></i> Reservar</button>' +
            '</div>' +
          '</div>' +
        '</div>';
    }

    function renderRooms(list) {
        var $grid = $('#rooms-grid');
        if (!list || !list.length) {
            $grid.html('<div class="text-center py-16 text-ink-400"><i class="fa fa-bed fa-3x"></i><p class="mt-4 text-[15px]">No hay habitaciones disponibles para los criterios seleccionados.</p></div>');
            return;
        }
        $grid.html('<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">' + list.map(roomCard).join('') + '</div>');
        // Asignar retardo escalonado y activar el revelado por scroll.
        $grid.find('.reveal').each(function (i) { this.style.setProperty('--reveal-i', i % 6); });
        if (window.hbInitReveal) window.hbInitReveal();
    }

    function findRoom(id) {
        id = parseInt(id, 10);
        if (window.__lastList) {
            var r = window.__lastList.filter(function (x) { return x.id === id; })[0];
            if (r) return r;
        }
        return DATA.rooms.filter(function (x) { return x.id === id; })[0];
    }

    // Render inicial: destacadas si las hay, si no todas
    window.__lastList = (DATA.featured && DATA.featured.length) ? DATA.featured : DATA.rooms;
    renderRooms(window.__lastList);
    if (DATA.featured && DATA.featured.length) {
        $('#rooms-heading').text('Habitaciones destacadas');
    }

    // ---- Buscar disponibilidad ----
    $('#searchform').on('submit', function (e) {
        e.preventDefault();
        var checkin = $('#checkin_date').val(), checkout = $('#checkout_date').val();
        var checkinTime = $('#checkin_time').val() || '14:00', checkoutTime = $('#checkout_time').val() || '12:00';
        if (!checkin || !checkout) { alert('Selecciona las fechas de entrada y salida.'); return; }
        if ((checkout + ' ' + checkoutTime) <= (checkin + ' ' + checkinTime)) {
            alert('La salida debe ser posterior a la entrada (revisa la fecha y la hora).'); return;
        }

        var $btn = $('#btn-search').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');
        $('#rooms-grid').html('<div class="text-center py-16 text-ink-400"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-4">Buscando habitaciones disponibles...</p></div>');

        $.post('/reservas/search', {
            checkin: checkin, checkout: checkout,
            checkin_time: checkinTime, checkout_time: checkoutTime,
            adults: $('#adults').val(), children: $('#children').val(),
            establishment_id: CURRENT_ESTABLISHMENT
        }).done(function (res) {
            if (!res.success) { $('#rooms-grid').html('<div class="text-center py-16 text-ink-400">' + esc(res.message || 'No se pudo buscar.') + '</div>'); return; }
            SEARCH = { checkin: checkin, checkout: checkout, checkin_time: checkinTime, checkout_time: checkoutTime, adults: parseInt($('#adults').val(),10), children: parseInt($('#children').val(),10), active: true };
            window.__lastList = res.rooms;
            renderRooms(res.rooms);
            $('#rooms-heading').text(res.count + ' habitación(es) disponible(s)');
            $('#rooms-subheading').text('Del ' + res.checkin + ' ' + res.checkin_time + ' al ' + res.checkout + ' ' + res.checkout_time + ' · ' + res.nights + ' noche(s) · ' + res.adults + ' adulto(s)' + (res.children ? ', ' + res.children + ' niño(s)' : ''));
            var top = document.getElementById('rooms-results').getBoundingClientRect().top + window.scrollY - 76;
            window.scrollTo({ top: top, behavior: 'smooth' });
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo realizar la búsqueda.';
            $('#rooms-grid').html('<div class="text-center py-16 text-ink-400">' + esc(m) + '</div>');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Buscar');
        });
    });

    // ---- Detalle ----
    var DETAIL_SPINNER = '<div class="hb-spinner" style="margin:auto;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';

    $(document).on('click', '.btn-detail', function () {
        var id = $(this).data('id');
        $('#detail-unit').text('Habitación');
        $('#detail-badge').html('');
        $('#detail-content').html(DETAIL_SPINNER);
        openModal('roomDetailModal');
        var url = '/reservas/room/' + id + '?establishment_id=' + (CURRENT_ESTABLISHMENT || '');
        if (SEARCH.active) url += '&checkin=' + SEARCH.checkin + '&checkout=' + SEARCH.checkout + '&checkin_time=' + SEARCH.checkin_time + '&checkout_time=' + SEARCH.checkout_time;
        $.get(url).done(function (res) {
            if (!res.success) { $('#detail-content').html('<p class="text-err" style="margin:auto;">No se pudo cargar el detalle.</p>'); return; }
            renderDetail(res.room);
        }).fail(function () {
            $('#detail-content').html('<p class="text-err" style="margin:auto;">No se pudo cargar el detalle.</p>');
        });
    });

    function statBox(icon, value, label) {
        return '<div class="mt-stat"><div class="value"><i class="fa ' + icon + '"></i> ' + value + '</div><div class="label">' + label + '</div></div>';
    }

    function renderDetail(room) {
        // Cabecera del modal (unidad + estado), igual que el unit modal de makai.
        $('#detail-unit').text(room.category + ' · ' + room.name);
        if (typeof room.available !== 'undefined') {
            $('#detail-badge').html(room.available
                ? '<span class="hb-badge hb-badge-ok"><span class="dot"></span> Disponible</span>'
                : '<span class="hb-badge hb-badge-off"><span class="dot"></span> No disponible</span>');
        } else if (room.status === 'MANTENIMIENTO') {
            $('#detail-badge').html('<span class="hb-badge hb-badge-off"><span class="dot"></span> Mantenimiento</span>');
        } else {
            $('#detail-badge').html('');
        }

        var images = (room.images && room.images.length) ? room.images : [PLACEHOLDER];
        var thumbs = images.length > 1
            ? '<div class="mt-thumbs">' + images.map(function (u, i) { return '<img src="' + u + '" class="mt-thumb ' + (i===0?'active':'') + '" data-src="' + u + '">'; }).join('') + '</div>'
            : '';

        // Cajas de estadística (capacidad / camas / m² / piso).
        var stats = [];
        if (room.capacity) stats.push(statBox('fa-users', room.capacity, 'Huéspedes'));
        if (room.beds)     stats.push(statBox('fa-bed', esc(room.beds), 'Camas'));
        if (room.size)     stats.push(statBox('fa-expand', room.size + ' m²', 'Superficie'));
        if (room.floor)    stats.push(statBox('fa-building', esc(room.floor), 'Piso'));
        var statsHtml = stats.length ? '<div class="mt-stats">' + stats.join('') + '</div>' : '';

        var amenities = (room.amenities && room.amenities.length)
            ? '<div class="mt-divider"></div><h5 class="font-semibold text-ink-900 mb-2 text-[13px] uppercase tracking-wide">Servicios</h5><div class="flex flex-wrap gap-2">' + room.amenities.map(function (a) { return '<span class="hb-pill"><i class="fa ' + amenityIcon(a) + '"></i>' + esc(a) + '</span>'; }).join('') + '</div>'
            : '';

        var priceHtml = room.min_price > 0
            ? '<span class="mt-price">' + money(room.min_price) + '</span> <small>/ noche</small>'
            : '<span class="text-ink-500 font-semibold">Consultar tarifa</span>';
        var totalLine = (room.total && room.nights) ? '<div class="text-[13px] text-ink-600 mt-2">Total ' + room.nights + ' noche(s): <strong class="text-ink-900">' + money(room.total) + '</strong></div>' : '';

        var reserveDisabled = (typeof room.available !== 'undefined' && !room.available) || room.status === 'MANTENIMIENTO';

        var html =
            '<div class="hb-modal__grid" style="flex:1;">' +
              '<div class="hb-modal__left"><div class="hb-modal__left-inner">' +
                '<div class="hb-eyebrow mb-3">' + esc(room.category) + '</div>' +
                '<h3 class="font-display text-2xl font-bold text-ink-900 mb-3">' + esc(room.name) + '</h3>' +
                '<div class="mb-4">' + priceHtml + totalLine + '</div>' +
                (room.description || room.short_description ? '<p class="text-[14px] text-ink-600 leading-relaxed mb-4">' + esc(room.description || room.short_description) + '</p>' : '') +
                statsHtml +
                amenities +
                '<button class="hb-btn hb-btn-primary hb-btn-block hb-btn-lg btn-reserve mt-5" data-id="' + room.id + '"' + (reserveDisabled ? ' disabled' : '') + '><i class="fa fa-calendar-check-o"></i> ' + (reserveDisabled ? 'No disponible' : 'Reservar') + '</button>' +
              '</div></div>' +
              '<div class="hb-modal__right">' +
                '<div class="mt-gallery"><img id="detail-main" src="' + images[0] + '" alt="' + esc(room.name) + '"></div>' +
                thumbs +
              '</div>' +
            '</div>';
        $('#detail-content').html(html);
    }

    $(document).on('click', '.mt-thumb', function () {
        $('#detail-main').attr('src', $(this).data('src'));
        $('.mt-thumb').removeClass('active');
        $(this).addClass('active');
    });

    // ---- Reservar ----
    $(document).on('click', '.btn-reserve', function () {
        var room = findRoom($(this).data('id'));
        if (!room) return;
        $('#roomDetailModal').removeClass('is-open is-opening');
        openReserve(room);
    });

    function openReserve(room) {
        $('#reserve-message').empty();
        $('#reserveform')[0].reset();
        $('#r-room').val(room.id);

        var checkin = SEARCH.active ? SEARCH.checkin : $('#checkin_date').val();
        var checkout = SEARCH.active ? SEARCH.checkout : $('#checkout_date').val();
        var checkinTime = SEARCH.active ? SEARCH.checkin_time : ($('#checkin_time').val() || '14:00');
        var checkoutTime = SEARCH.active ? SEARCH.checkout_time : ($('#checkout_time').val() || '12:00');
        var adults = SEARCH.active ? SEARCH.adults : parseInt($('#adults').val(), 10);
        var children = SEARCH.active ? SEARCH.children : parseInt($('#children').val(), 10);

        $('#r-checkin').val(checkin || '');
        $('#r-checkout').val(checkout || '');
        $('#r-checkin-time').val(checkinTime || '');
        $('#r-checkout-time').val(checkoutTime || '');
        $('#r-adults').val(adults || 1);
        $('#r-children').val(children || 0);

        var datesTxt = (checkin && checkout)
            ? 'Del <strong>' + checkin + ' ' + checkinTime + '</strong> al <strong>' + checkout + ' ' + checkoutTime + '</strong>'
            : '<span class="text-err">Selecciona fechas en el buscador antes de reservar.</span>';
        var priceTxt = room.min_price > 0 ? money(room.min_price) + ' / noche' : 'Consultar tarifa';
        $('#reserve-summary').html(
            '<div class="font-semibold text-ink-900">' + esc(room.category) + ' · ' + esc(room.name) + '</div>' +
            '<div class="mt-0.5">' + datesTxt + '</div>' +
            '<div class="mt-0.5">' + (adults || 1) + ' adulto(s)' + (children ? ', ' + children + ' niño(s)' : '') + ' · ' + priceTxt + '</div>'
        );
        openModal('reserveModal');
    }

    function docLookupType(id) {
        id = String(id);
        if (id === '1') return 'dni';
        if (id === '6') return 'ruc';
        return null;
    }

    $('#r-doc-search').on('click', function () {
        var lookup = docLookupType($('#r-doctype').val());
        var num = $.trim($('#r-docnumber').val());
        var $fb = $('#r-doc-feedback').removeClass('text-err text-ok').addClass('text-ink-500');
        if (!lookup) { $fb.text('Este tipo de documento se ingresa manualmente.'); return; }
        if (!num) { $fb.text('Ingresa el número de documento.'); return; }
        $fb.html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        $.get('/reservas/document/' + lookup + '/' + num).done(function (res) {
            if (res.success && res.data) {
                var d = res.data;
                if (d.name) $('#r-name').val(d.name);
                $fb.removeClass('text-ink-500').addClass('text-ok').text('Datos encontrados.');
            } else {
                $fb.removeClass('text-ink-500').addClass('text-err').text(res.message || 'No se encontraron datos.');
            }
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo consultar.';
            $fb.removeClass('text-ink-500').addClass('text-err').text(m);
        });
    });

    $('#r-doctype').on('change', function () {
        var id = String($(this).val());
        var placeholders = { '1': 'DNI (8 dígitos)', '6': 'RUC (11 dígitos)', '7': 'Nº de pasaporte', '4': 'Nº de carnet de extranjería' };
        $('#r-docnumber').attr('placeholder', placeholders[id] || 'Nº de documento');
        $('#r-doc-search').toggle(!!docLookupType(id));
    }).trigger('change');

    $('#reserveform').on('submit', function (e) {
        e.preventDefault();
        if (!$('#r-checkin').val() || !$('#r-checkout').val()) {
            $('#reserve-message').html(alertHtml('danger', 'Selecciona las fechas en el buscador antes de reservar.'));
            return;
        }
        var $btn = $('#r-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
        $.post('/reservas/store', $(this).serialize() + '&establishment_id=' + (CURRENT_ESTABLISHMENT || ''))
            .done(function (html) {
                $('#reserve-message').html(html);
                if (html.indexOf('alert-success') !== -1) {
                    $('#reserveform')[0].reset();
                    if (SEARCH.active) { $('#searchform').trigger('submit'); }
                }
            })
            .fail(function (xhr) {
                $('#reserve-message').html(
                    (xhr.responseText && xhr.responseText.indexOf('alert') !== -1)
                        ? xhr.responseText
                        : alertHtml('danger', 'No se pudo registrar la reserva. Inténtalo de nuevo.'));
            })
            .always(function () { $btn.prop('disabled', false).html('<i class="fa fa-calendar-check-o"></i> Confirmar reserva'); });
    });

    function alertHtml(type, msg) {
        return '<div class="alert alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + esc(msg) + '</div>';
    }
    $(document).on('click', '.alert .close', function () { $(this).closest('.alert').remove(); });

    // ---- Galería lightbox ----
    $(document).on('click', '[data-lightbox]', function () {
        $('#hbLightbox img').attr('src', $(this).data('lightbox'));
        $('#hbLightbox').addClass('is-open'); $('body').addClass('hb-modal-open');
    });
    $(document).on('click', '[data-lightbox-close], #hbLightbox', function (e) {
        if (e.target !== this) return;
        $('#hbLightbox').removeClass('is-open'); $('body').removeClass('hb-modal-open');
    });
    $('[data-lightbox-close]').on('click', function () { $('#hbLightbox').removeClass('is-open'); $('body').removeClass('hb-modal-open'); });

    // ---- fechas mínimas ----
    (function initDates() {
        var localToday = function () {
            var d = new Date();
            var mm = ('0' + (d.getMonth() + 1)).slice(-2);
            var dd = ('0' + d.getDate()).slice(-2);
            return d.getFullYear() + '-' + mm + '-' + dd;
        };
        var today = localToday();
        $('#checkin_date').attr('min', today).on('change', function () {
            var v = $(this).val();
            $('#checkout_date').attr('min', v || today);
            if ($('#checkout_date').val() && $('#checkout_date').val() < v) {
                $('#checkout_date').val(v);
            }
        });
        $('#checkout_date').attr('min', today);
    })();
});
</script>

</body>
</html>
