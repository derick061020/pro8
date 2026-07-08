<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    use Modules\Hotel\Models\HotelLandingSetting;

    $hotelName = $establishment->description ?? 'Hotel';
    $hotelPhone = $establishment->telephone ?? null;
    $hotelEmail = $establishment->email ?? null;
    $hotelAddress = $establishment->address ?? null;
    $hotelWeb = $establishment->web_address ?? null;
    $hotelLogo = (!empty($establishment->logo)) ? asset('storage/uploads/logos/'.$establishment->logo) : null;

    // Configuración de personalización de la web (con valores por defecto).
    $cfg = $settings ?? HotelLandingSetting::mergeDefaults([]);
    $themeColor = in_array(($cfg['color'] ?? 'turquoise'), ['turquoise','blue','green','orange','purple','red','brown','black']) ? $cfg['color'] : 'turquoise';
@endphp
<title>{{ $hotelName }} · Reservas online</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="/landing-reservas/favicon.ico">

<!-- Stylesheets -->
<link rel="stylesheet" href="/landing-reservas/css/animate.css">
<link rel="stylesheet" href="/landing-reservas/css/bootstrap.css">
<link rel="stylesheet" href="/landing-reservas/css/font-awesome.min.css">
<link rel="stylesheet" href="/landing-reservas/css/owl.carousel.css">
<link rel="stylesheet" href="/landing-reservas/css/owl.theme.css">
<link rel="stylesheet" href="/landing-reservas/css/prettyPhoto.css">
<link rel="stylesheet" href="/landing-reservas/css/smoothness/jquery-ui-1.10.4.custom.min.css">
<link rel="stylesheet" href="/landing-reservas/rs-plugin/css/settings.css">
<link rel="stylesheet" href="/landing-reservas/css/theme.css">
<link rel="stylesheet" href="/landing-reservas/css/colors/{{ $themeColor }}.css">
<link rel="stylesheet" href="/landing-reservas/css/responsive.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600,700">

<!-- Javascripts -->
<script type="text/javascript" src="/landing-reservas/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.parallax-1.1.3.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.forms.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.sticky.js"></script>
<script type="text/javascript" src="/landing-reservas/js/waypoints.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.isotope.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.gmap.min.js"></script>
<script type="text/javascript" src="/landing-reservas/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script type="text/javascript" src="/landing-reservas/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/custom.js"></script>

<style>
  /* Ajustes propios de la web de reservas (no alteran el tema base) */
  /* Evita el scroll/desborde horizontal en móvil (el hero se veía corrido). */
  html, body { overflow-x:hidden; max-width:100%; }
  .hr-hero, .hr-search, .hr-slide__caption .container { max-width:100%; }
  .search-box { background:#fff; border-radius:6px; box-shadow:0 10px 30px rgba(0,0,0,.12); padding:18px; }
  .search-box label { font-weight:600; font-size:12px; text-transform:uppercase; color:#666; }
  .search-box .form-control { height:42px; }
  /* Grid de habitaciones: fila flex para que todas las tarjetas de una fila
     tengan la misma altura y no se "salten" espacios cuando el contenido varía. */
  #rooms-grid > .row { display:flex; flex-wrap:wrap; }
  #rooms-grid > .row:before, #rooms-grid > .row:after { content:none; display:none; }
  #rooms-grid > .row > [class*="col-"] { display:flex; flex-direction:column; margin-bottom:30px; }
  /* ===== Tarjeta de habitación con revelado al hover (estilo /landing-page) =====
     La imagen ocupa toda la tarjeta; una barra inferior muestra siempre el
     nombre y el precio, y al pasar el cursor sube un panel con toda la info. */
  .room-card { position:relative; width:100%; height:340px; background:#eceff1; border-radius:10px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,.10); transition:box-shadow .25s ease; }
  .room-card:hover { box-shadow:0 14px 34px rgba(0,0,0,.20); }
  .room-card__media { position:absolute; top:0; left:0; right:0; bottom:0; background:#eceff1 center/cover no-repeat; transition:transform .55s ease; }
  .room-card:hover .room-card__media { transform:scale(1.06); }
  .room-card__badge { position:absolute; top:14px; left:14px; z-index:3; background:#1abc9c; color:#fff; font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:.4px; box-shadow:0 4px 12px rgba(0,0,0,.20); }
  .room-card__fav { position:absolute; top:14px; right:14px; z-index:3; background:#e67e22; color:#fff; font-size:11px; font-weight:600; padding:4px 12px; border-radius:20px; box-shadow:0 4px 12px rgba(0,0,0,.20); }
  /* Barra inferior siempre visible */
  .room-card__bar { position:absolute; left:0; right:0; bottom:0; z-index:2; padding:44px 18px 16px; color:#fff; background:linear-gradient(180deg, rgba(15,23,32,0) 0%, rgba(15,23,32,.55) 45%, rgba(15,23,32,.88) 100%); display:flex; align-items:flex-end; justify-content:space-between; gap:10px; transition:opacity .3s ease; }
  .room-card__bar-title { font-size:18px; font-weight:700; line-height:1.2; text-shadow:0 2px 8px rgba(0,0,0,.5); }
  .room-card__bar-price { font-size:16px; font-weight:700; white-space:nowrap; text-shadow:0 2px 8px rgba(0,0,0,.5); }
  .room-card__bar-price small { font-weight:400; font-size:11px; opacity:.85; }
  /* Panel revelado al hover */
  .room-card__reveal { position:absolute; top:0; left:0; right:0; bottom:0; z-index:4; background:#fff; padding:20px; display:flex; flex-direction:column; transform:translateY(101%); transition:transform .42s cubic-bezier(.22,.61,.36,1); }
  .room-card:hover .room-card__reveal { transform:translateY(0); }
  .room-card:hover .room-card__bar { opacity:0; }
  .room-card__cat { color:#1abc9c; font-size:12px; text-transform:uppercase; letter-spacing:.5px; font-weight:600; }
  .room-card__title { font-size:19px; font-weight:700; margin:2px 0 6px; color:#2c3e50; }
  .room-card__meta { color:#7f8c8d; font-size:13px; margin-bottom:8px; }
  .room-card__meta i { color:#1abc9c; margin-right:3px; }
  .room-card__desc { color:#666; font-size:13px; flex:1; overflow:hidden; }
  .room-card__price { font-size:22px; font-weight:700; color:#2c3e50; margin-top:8px; }
  .room-card__price small { font-size:12px; color:#95a5a6; font-weight:400; }
  .room-card__total { font-size:12px; color:#16a085; font-weight:600; }
  .room-card__actions { display:flex; gap:8px; margin-top:12px; }
  .room-card__actions .btn { flex:1; }
  /* Dispositivos táctiles (sin hover, p.ej. móviles): se muestra toda la
     información sin necesidad de hover, en formato de tarjeta apilada. */
  @media (hover: none) {
    .room-card { height:auto; }
    .room-card__media { position:relative; height:200px; }
    .room-card__bar { display:none; }
    .room-card__reveal { position:relative; top:auto; left:auto; right:auto; bottom:auto; transform:none; }
  }
  .amenity-pill { display:inline-block; background:#f0f3f5; color:#566573; border-radius:16px; padding:4px 12px; font-size:12px; margin:0 6px 6px 0; }
  .amenity-pill i { color:#1abc9c; margin-right:5px; }
  .modal-gallery img { width:100%; border-radius:6px; margin-bottom:10px; cursor:pointer; max-height:360px; object-fit:cover; }
  .modal-thumbs { display:flex; gap:8px; flex-wrap:wrap; }
  .modal-thumbs img { width:70px; height:55px; object-fit:cover; border-radius:5px; border:2px solid transparent; cursor:pointer; }
  .modal-thumbs img.active { border-color:#1abc9c; }
  .summary-box { background:#f8fafb; border:1px solid #eceff1; border-radius:6px; padding:12px 14px; margin-bottom:14px; }
  .summary-box strong { color:#2c3e50; }
  .loading-rooms { text-align:center; padding:40px 0; color:#95a5a6; }
  .empty-rooms { text-align:center; padding:40px 0; color:#7f8c8d; }
  .doc-feedback { font-size:12px; margin-top:4px; }
  /* Blog */
  .blog-card { background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,.08); margin-bottom:30px; transition:transform .2s, box-shadow .2s; }
  .blog-card:hover { transform:translateY(-4px); box-shadow:0 10px 28px rgba(0,0,0,.15); }
  .blog-card__img { position:relative; display:block; height:200px; background:#eceff1 center/cover no-repeat; }
  .blog-card__body { padding:18px 20px; }
  .blog-card__date { color:#1abc9c; font-size:12px; text-transform:uppercase; letter-spacing:.5px; font-weight:600; margin-bottom:6px; }
  .blog-card__date i { margin-right:4px; }
  .blog-card__title { font-size:19px; font-weight:700; margin:0 0 10px; line-height:1.3; }
  .blog-card__title a { color:#2c3e50; }
  .blog-card__title a:hover { color:#1abc9c; }
  .blog-card__excerpt { color:#666; font-size:14px; margin-bottom:12px; }
  .blog-card__more { color:#1abc9c; font-weight:600; font-size:13px; text-transform:uppercase; }

  /* ===== Hero de reservas (moderno, sin plugin — fondo a pantalla completa, sin cortes) ===== */
  .hr-hero { position:relative; background:#0f1720; }
  .hr-slides { position:relative; height:74vh; min-height:480px; max-height:760px; overflow:hidden; }
  .hr-slide { position:absolute; top:0; left:0; right:0; bottom:0; background-size:cover; background-position:center; opacity:0; transition:opacity 1.1s ease; }
  .hr-slide.is-active { opacity:1; }
  .hr-slide__overlay { position:absolute; top:0; left:0; right:0; bottom:0; background:linear-gradient(180deg, rgba(15,23,32,.40) 0%, rgba(15,23,32,.28) 45%, rgba(15,23,32,.70) 100%); }
  .hr-slide__caption { position:absolute; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; text-align:center; color:#fff; padding-bottom:60px; }
  .hr-slide__caption .container { width:100%; }
  .hr-stars { color:#f1c40f; font-size:18px; letter-spacing:4px; margin-bottom:14px; }
  .hr-title { font-size:52px; font-weight:700; margin:0 0 14px; color:#fff; line-height:1.1; text-shadow:0 4px 24px rgba(0,0,0,.45); }
  .hr-subtitle { font-size:20px; font-weight:300; max-width:720px; margin:0 auto 26px; text-shadow:0 2px 12px rgba(0,0,0,.4); }
  .hr-btn { display:inline-block; background:#1abc9c; color:#fff; border:0; border-radius:40px; padding:13px 34px; font-weight:600; font-size:14px; text-transform:uppercase; letter-spacing:.5px; box-shadow:0 10px 26px rgba(26,188,156,.40); transition:transform .2s, box-shadow .2s, background .2s; }
  .hr-btn:hover, .hr-btn:focus { background:#16a085; color:#fff; text-decoration:none; transform:translateY(-2px); box-shadow:0 14px 32px rgba(26,188,156,.5); }
  .hr-dots { position:absolute; left:0; right:0; bottom:120px; display:flex; justify-content:center; gap:10px; z-index:3; }
  .hr-dot { width:11px; height:11px; border-radius:50%; border:0; padding:0; background:rgba(255,255,255,.5); cursor:pointer; transition:background .2s, transform .2s; }
  .hr-dot.is-active { background:#1abc9c; transform:scale(1.25); }

  /* Buscador flotante sobre el hero */
  .hr-search { position:relative; z-index:5; }
  #reservation-form.hr-search { margin-top:-72px; margin-bottom:10px; }
  .hr-search__card { background:#fff; border-radius:16px; box-shadow:0 18px 50px rgba(0,0,0,.18); padding:22px 26px; display:grid; grid-template-columns:1fr 1fr auto auto; gap:16px; align-items:end; }
  .hr-field { display:flex; flex-direction:column; min-width:0; }
  /* Selector de huéspedes tipo popover (adultos + niños + guardar) */
  .hr-guests { position:relative; }
  .hr-guests-box { height:50px; border:1px solid #e3e8ec; border-radius:10px; background:#f8fafb; padding:0 14px; display:flex; align-items:center; justify-content:space-between; gap:12px; cursor:pointer; font-size:15px; color:#2c3e50; min-width:160px; white-space:nowrap; transition:border-color .2s, box-shadow .2s, background-color .2s; }
  .hr-guests-box:hover { border-color:#cfd8dd; }
  .hr-guests-box.is-open { border-color:#1abc9c; background-color:#fff; box-shadow:0 0 0 3px rgba(26,188,156,.15); }
  .hr-guests-box i { color:#8a97a3; }
  .hr-guests-pop { position:absolute; left:0; bottom:calc(100% + 12px); width:270px; background:#fff; border-radius:12px; box-shadow:0 18px 50px rgba(0,0,0,.22); padding:20px; z-index:60; display:none; }
  .hr-guests-pop.is-open { display:block; }
  .hr-pop-field { margin-bottom:16px; }
  .hr-pop-field > label { display:block; font-size:15px; font-weight:700; color:#3a4b57; margin-bottom:8px; text-transform:none; letter-spacing:0; }
  .hr-pop-field select { width:100%; height:46px; border:1px solid #e3e8ec; border-radius:8px; padding:0 34px 0 14px; font-size:15px; color:#7a8791; background-color:#fff; -webkit-appearance:none; -moz-appearance:none; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%238a97a3' d='M6 8 0 2 1.5.5 6 5 10.5.5 12 2z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
  .hr-pop-field select:focus { outline:none; border-color:#1abc9c; box-shadow:0 0 0 3px rgba(26,188,156,.15); }
  .hr-pop-save { width:100%; height:48px; border:1px solid #dce3e7; border-radius:8px; background:#fff; color:#1abc9c; font-weight:700; font-size:15px; letter-spacing:.5px; cursor:pointer; transition:background-color .2s; }
  .hr-pop-save:hover { background-color:#f4f7f8; }
  .hr-field label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#8a97a3; margin-bottom:7px; }
  .hr-field label i { color:#1abc9c; margin-right:5px; }
  .hr-field input, .hr-field select { width:100%; height:50px; border:1px solid #e3e8ec; border-radius:10px; padding:0 14px; font-size:15px; color:#2c3e50; background-color:#f8fafb; box-shadow:none; transition:border-color .2s, box-shadow .2s, background-color .2s; -webkit-appearance:none; -moz-appearance:none; appearance:none; }
  .hr-field input:focus, .hr-field select:focus { outline:none; border-color:#1abc9c; background-color:#fff; box-shadow:0 0 0 3px rgba(26,188,156,.15); }
  .hr-field select { padding-right:36px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%238a97a3' d='M6 8 0 2 1.5.5 6 5 10.5.5 12 2z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
  .hr-field input[type="date"]::-webkit-calendar-picker-indicator { cursor:pointer; opacity:.55; }
  .hr-field input[type="date"]::-webkit-calendar-picker-indicator:hover { opacity:1; }
  .hr-btn-search { height:50px; border:0; border-radius:10px; background:#1abc9c; color:#fff; font-weight:700; font-size:15px; padding:0 28px; cursor:pointer; white-space:nowrap; box-shadow:0 10px 24px rgba(26,188,156,.35); transition:background .2s, transform .2s, box-shadow .2s; }
  .hr-btn-search:hover { background:#16a085; transform:translateY(-1px); box-shadow:0 14px 30px rgba(26,188,156,.45); }
  .hr-btn-search:disabled { opacity:.7; cursor:default; transform:none; }
  @media (max-width:991px) {
    .hr-title { font-size:42px; }
    .hr-search__card { grid-template-columns:1fr 1fr; }
    .hr-field--submit { grid-column:1 / -1; }
    .hr-btn-search { width:100%; }
  }
  @media (max-width:767px) {
    .hr-slides { height:64vh; min-height:400px;     position: static!important;}
    .hr-title { font-size:32px; word-break:break-word; }
    .hr-subtitle { font-size:16px; }
    .hr-dots { bottom:96px; }
    #reservation-form.hr-search { margin-top:-44px; }
    .hr-search__card { grid-template-columns:1fr; padding:18px; }
    /* Centrado robusto del contenido del hero en móvil (evita que el texto se
       corra hacia un lado si algún ancho fuerza desborde). */
    .hr-slide__caption { justify-content:center; padding-left:16px; padding-right:16px; }
    .hr-slide__caption .container { width:100%; max-width:100%; padding-left:0; padding-right:0; margin:0 auto; }
  }

  /* ===== Ajustes responsive propios de la web de reservas ===== */
  @media (max-width:767px) {
    /* Barra superior (teléfono / correo / sucursal): apilar y reducir para que
       no se solape en pantallas pequeñas. */
    #top-header .th-text { float:none !important; text-align:center; }
    #top-header .th-item { display:inline-block; float:none; font-size:12px; margin:2px 6px; }
    #top-header .col-xs-6 { width:100%; }

    /* El popover de huéspedes ocupa el ancho disponible sin desbordar. */
    .hr-guests-pop { width:100%; min-width:0; left:0; right:0; }

    /* Espaciados grandes del tema reducidos en móvil. */
    .mt100 { margin-top:50px !important; }
    .mt50  { margin-top:30px !important; }

    /* Tarjetas de habitación a una columna con separación cómoda. */
    #rooms-grid > .row > [class*="col-"] { margin-bottom:22px; }
    .room-card__actions { flex-direction:column; }

    /* Galería del detalle de habitación: imagen principal más baja. */
    .modal-gallery img { max-height:240px; }

    /* Modal de reserva cómodo en móvil (ocupa casi toda la pantalla). */
    #reserveModal .modal-dialog, #roomDetailModal .modal-dialog { margin:10px; }
    #reserveModal .form-group, #roomDetailModal .form-group { margin-bottom:12px; }
  }

  @media (max-width:480px) {
    .hr-title { font-size:26px; }
    .hr-subtitle { font-size:15px; }
    .room-card__title { font-size:17px; }
    .room-card__price { font-size:20px; }
    /* Los pares de columnas del formulario de reserva se apilan (col-sm ya lo
       hace <768px; se refuerza el espaciado). */
    #reserveModal .row > [class*="col-"] { margin-bottom:2px; }
  }
</style>
</head>

<body>

@include('hotel::landing.partials.header', ['onLanding' => true, 'activeNav' => 'inicio'])

<a id="top"></a>
@php
  // Garantizamos al menos una diapositiva aunque el tenant guarde la lista vacía.
  $slides = !empty($cfg['slides']) && is_array($cfg['slides'])
    ? $cfg['slides']
    : [['image' => null, 'title' => '', 'subtitle' => 'Reserva tu estancia con nosotros', 'button_text' => 'Ver habitaciones', 'button_link' => '#rooms-results', 'stars' => true]];
@endphp
<!-- Hero de reservas -->
<section class="hr-hero" id="hero">
  <div class="hr-slides">
    @foreach($slides as $i => $slide)
      @php
        $slideImg   = HotelLandingSetting::imageUrl($slide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
        $slideTitle = trim($slide['title'] ?? '') !== '' ? $slide['title'] : $hotelName;
        $showStars  = $slide['stars'] ?? true;
      @endphp
      <div class="hr-slide {{ $i === 0 ? 'is-active' : '' }}" style="background-image:url('{{ $slideImg }}');">
        <div class="hr-slide__overlay"></div>
        <div class="hr-slide__caption">
          <div class="container">
            @if($showStars)
              <div class="hr-stars">@for($s=0;$s<5;$s++)<i class="fa fa-star"></i>@endfor</div>
            @endif
            <h1 class="hr-title">{{ $slideTitle }}</h1>
            @if(!empty($slide['subtitle']))<p class="hr-subtitle">{{ $slide['subtitle'] }}</p>@endif
            @if(!empty($slide['button_text']))
              <a href="{{ $slide['button_link'] ?: '#rooms-results' }}" class="hr-btn nav-scroll">{{ $slide['button_text'] }}</a>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  @if(count($slides) > 1)
    <div class="hr-dots">
      @foreach($slides as $i => $slide)
        <button type="button" class="hr-dot {{ $i === 0 ? 'is-active' : '' }}" data-i="{{ $i }}" aria-label="Ir a la diapositiva {{ $i + 1 }}"></button>
      @endforeach
    </div>
  @endif
</section>

<!-- Buscador de disponibilidad (selector de fechas moderno) -->
<section class="hr-search" id="reservation-form">
  <div class="container">
    <form class="hr-search__card" role="form" id="searchform" onsubmit="return false;">
      <div class="hr-field">
        <label for="checkin_date"><i class="fa fa-calendar"></i> Entrada</label>
        <input name="checkin" type="date" id="checkin_date" required>
      </div>
      <div class="hr-field">
        <label for="checkout_date"><i class="fa fa-calendar"></i> Salida</label>
        <input name="checkout" type="date" id="checkout_date" required>
      </div>
      <div class="hr-field hr-guests">
        <label><i class="fa fa-users"></i> Huéspedes</label>
        <div class="hr-guests-box" id="guestsToggle">
          <span id="guestsSummary">2 adultos</span>
          <i class="fa fa-angle-down"></i>
        </div>
        <div class="hr-guests-pop" id="guestsPop">
          <div class="hr-pop-field">
            <label>Adultos</label>
            <select name="adults" id="adults">
              @for($i=1;$i<=8;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'adulto' : 'adultos' }}</option>@endfor
            </select>
          </div>
          <div class="hr-pop-field">
            <label>Niños</label>
            <select name="children" id="children">
              @for($i=0;$i<=6;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'niño' : 'niños' }}</option>@endfor
            </select>
          </div>
          <button type="button" class="hr-pop-save" id="guestsSave">Guardar</button>
        </div>
      </div>
      <div class="hr-field hr-field--submit">
        <button type="submit" id="btn-search" class="hr-btn-search"><i class="fa fa-search"></i> Buscar</button>
      </div>
    </form>
  </div>
</section>

<script>
(function () {
  // Slideshow ligero del hero (crossfade). Reemplaza al Revolution Slider.
  var slides = document.querySelectorAll('.hr-slide');
  var dots   = document.querySelectorAll('.hr-dot');
  if (slides.length >= 2) {
    var idx = 0, timer;
    var go = function (n) {
      slides[idx].classList.remove('is-active');
      if (dots[idx]) dots[idx].classList.remove('is-active');
      idx = (n + slides.length) % slides.length;
      slides[idx].classList.add('is-active');
      if (dots[idx]) dots[idx].classList.add('is-active');
    };
    var start = function () { timer = setInterval(function () { go(idx + 1); }, 6000); };
    var reset = function () { clearInterval(timer); start(); };
    for (var d = 0; d < dots.length; d++) {
      dots[d].addEventListener('click', function () { go(parseInt(this.getAttribute('data-i'), 10)); reset(); });
    }
    start();
  }

  // Abrir el calendario nativo al hacer click en cualquier parte del input de fecha.
  var dateInputs = document.querySelectorAll('.hr-search__card input[type="date"]');
  for (var k = 0; k < dateInputs.length; k++) {
    var openPicker = function () { try { if (this.showPicker) this.showPicker(); } catch (e) {} };
    dateInputs[k].addEventListener('click', openPicker);
    dateInputs[k].addEventListener('focus', openPicker);
  }

  // Selector de huéspedes (popover Adultos/Niños + Guardar).
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
    gToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      gPop.classList.toggle('is-open');
      gToggle.classList.toggle('is-open');
    });
    gPop.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', closePop);
    if (gSave) gSave.addEventListener('click', function () { updateSummary(); closePop(); });
    aSel.addEventListener('change', updateSummary);
    cSel.addEventListener('change', updateSummary);
    updateSummary();
  }
})();
</script>

<!-- Resultados / Habitaciones -->
<section class="rooms mt50" id="rooms-results">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="lined-heading"><span id="rooms-heading">{{ $cfg['rooms_heading'] ?? 'Nuestras habitaciones' }}</span></h2>
        <p class="text-center text-muted" id="rooms-subheading" style="margin-top:-10px;margin-bottom:25px;">{{ $cfg['rooms_subheading'] ?? 'Selecciona fechas para ver disponibilidad y precios.' }}</p>
      </div>
      <div class="col-sm-12" id="rooms-grid"></div>
    </div>
  </div>
</section>

@if(($cfg['show_features'] ?? true) && count($cfg['features'] ?? []))
<!-- USP's -->
<section class="usp mt100">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="lined-heading"><span>{{ $cfg['features_heading'] ?? '¿Por qué reservar con nosotros?' }}</span></h2>
      </div>
      @php $fCols = count($cfg['features']) >= 4 ? 3 : (12 / max(1, count($cfg['features']))); @endphp
      @foreach($cfg['features'] as $fi => $feature)
      <div class="col-sm-{{ $fCols }} bounceIn appear" data-start="{{ $fi * 400 }}">
        <div class="box-icon">
          <div class="circle"><i class="fa {{ $feature['icon'] ?? 'fa-star' }} fa-lg"></i></div>
          <h3>{{ $feature['title'] ?? '' }}</h3>
          <p>{{ $feature['text'] ?? '' }}</p>
          @if(!empty($feature['link_text']))
          <a href="{{ $feature['link'] ?? '#' }}" class="nav-scroll">{{ $feature['link_text'] }}<i class="fa fa-angle-right"></i></a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Parallax Effect -->
<script type="text/javascript">$(document).ready(function(){$('#parallax-image').parallax("50%", -0.25);});</script>

@if($cfg['show_parallax'] ?? true)
@php $parallaxImg = HotelLandingSetting::imageUrl($cfg['parallax']['image'] ?? null, HotelLandingSetting::DEFAULT_PARALLAX); @endphp
<section class="parallax-effect mt100">
  <div id="parallax-image" style="background-image: url('{{ $parallaxImg }}');">
    <div class="color-overlay fadeIn appear" data-start="600">
      <div class="container">
        <div class="content">
          <h3 class="text-center"><i class="fa fa fa-star-o"></i> {{ $hotelName }}</h3>
          <p class="text-center">{{ $cfg['parallax']['text'] ?? 'Vive una experiencia inolvidable' }}
		  <br>
		  @if(!empty($cfg['parallax']['button_text']))<a href="{{ $cfg['parallax']['button_link'] ?: '#rooms-results' }}" class="btn btn-default btn-lg mt30 nav-scroll">{{ $cfg['parallax']['button_text'] }}</a>@endif</p>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

@if(($cfg['show_gallery'] ?? true) && count($cfg['gallery'] ?? []))
<!-- Gallery -->
<section class="gallery-slider mt100" id="gallery">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="lined-heading"><span>{{ $cfg['gallery_heading'] ?? 'Galería' }}</span></h2>
      </div>
    </div>
  </div>
  <div id="owl-gallery" class="owl-carousel">
    @foreach($cfg['gallery'] as $gi => $galleryImg)
      @php $gImg = HotelLandingSetting::imageUrl($galleryImg, HotelLandingSetting::DEFAULT_GALLERY); @endphp
      <div class="item"><a href="{{ $gImg }}" data-rel="prettyPhoto[gallery1]"><img src="{{ $gImg }}" alt="Imagen {{ $gi + 1 }}"><i class="fa fa-search"></i></a></div>
    @endforeach
  </div>
</section>
@endif

@php
  $showTestimonials = ($cfg['show_testimonials'] ?? true) && count($cfg['testimonials'] ?? []);
  $showAbout        = ($cfg['show_about'] ?? true) && count($cfg['about']['tabs'] ?? []);
@endphp
@if($showTestimonials || $showAbout)
<div class="container">
  <div class="row">
    @if($showTestimonials)
    <!-- Testimonials -->
    <section class="testimonials mt100">
      <div class="col-md-6">
        <h2 class="lined-heading bounceInLeft appear" data-start="0"><span>{{ $cfg['testimonials_heading'] ?? 'Lo que opinan nuestros huéspedes' }}</span></h2>
        <div id="owl-reviews" class="owl-carousel">
          @foreach(collect($cfg['testimonials'])->chunk(2) as $pair)
          <div class="item">
            @foreach($pair as $t)
              @php $tImg = HotelLandingSetting::imageUrl($t['image'] ?? null, HotelLandingSetting::DEFAULT_REVIEW); @endphp
              <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-2 col-xs-12"> <img src="{{ $tImg }}" alt="{{ $t['name'] ?? 'Huésped' }}" class="img-circle" /></div>
                <div class="col-lg-9 col-md-8 col-sm-10 col-xs-12">
                  <div class="text-balloon">{{ $t['text'] ?? '' }} <span>{{ $t['name'] ?? '' }}</span> </div>
                </div>
              </div>
            @endforeach
          </div>
          @endforeach
        </div>
      </div>
    </section>
    @endif

    @if($showAbout)
    @php $aboutImg = HotelLandingSetting::imageUrl($cfg['about']['image'] ?? null, HotelLandingSetting::DEFAULT_ABOUT); @endphp
    <!-- About -->
    <section class="about mt100">
      <div class="col-md-6">
        <h2 class="lined-heading bounceInRight appear" data-start="800"><span>{{ $cfg['about_heading'] ?? 'Sobre el hotel' }}</span></h2>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
          @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
            <li class="{{ $ti === 0 ? 'active' : '' }}"><a href="#about-tab-{{ $ti }}" data-toggle="tab">{{ $aboutTab['title'] ?? 'Pestaña' }}</a></li>
          @endforeach
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
            <div class="tab-pane fade {{ $ti === 0 ? 'in active' : '' }}" id="about-tab-{{ $ti }}">
              @if($ti === 0 && $aboutImg)<img src="{{ $aboutImg }}" alt="{{ $cfg['about_heading'] ?? 'Hotel' }}" class="pull-right" style="max-width:200px;margin:0 0 10px 15px;">@endif
              <p>{{ $aboutTab['content'] ?? '' }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </section>
    @endif
  </div>
</div>
@endif

@if(isset($blogPosts) && $blogPosts->count())
<!-- Blog -->
<section id="blog" class="blog-section mt100">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="lined-heading"><span>Nuestro blog</span></h2>
        <p class="text-center text-muted" style="margin-top:-10px;margin-bottom:30px;">Novedades, consejos y noticias del hotel.</p>
      </div>
    </div>
    <div class="row">
      @foreach($blogPosts as $post)
        @php
          $postImage = $post->cover_image ?: '/landing-reservas/images/gallery/800x504.gif';
          $postDate  = optional($post->published_at)->format('d/m/Y');
        @endphp
        <div class="col-md-4 col-sm-6">
          <div class="blog-card">
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="blog-card__img" style="background-image:url('{{ $postImage }}');">
              @if($post->hasVideoCover())<i class="fa fa-play-circle" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:46px;text-shadow:0 2px 6px rgba(0,0,0,.55);"></i>@endif
            </a>
            <div class="blog-card__body">
              @if($postDate)<div class="blog-card__date"><i class="fa fa-calendar"></i> {{ $postDate }}</div>@endif
              <h3 class="blog-card__title"><a href="{{ url('reservas/blog/'.$post->slug) }}">{{ $post->title }}</a></h3>
              <p class="blog-card__excerpt">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}</p>
              <a href="{{ url('reservas/blog/'.$post->slug) }}" class="blog-card__more">Leer más <i class="fa fa-angle-right"></i></a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="row">
      <div class="col-md-12 text-center" style="margin-top:10px;">
        <a href="{{ url('reservas/blog') }}" class="btn btn-primary btn-lg">Ver todo el blog</a>
      </div>
    </div>
  </div>
</section>
@endif

@if($cfg['show_cta'] ?? true)
<!-- Call To Action -->
<section id="call-to-action" class="mt100">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-12">
        <h2>{{ $cfg['cta_text'] ?? '¿Listo para tu próxima estancia? Reserva ahora en línea.' }}</h2>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="#reservation-form" class="btn btn-default btn-lg pull-right nav-scroll">{{ $cfg['cta_button'] ?? 'Ver disponibilidad' }}</a>
      </div>
    </div>
  </div>
</section>
@endif

@include('hotel::landing.partials.footer', ['onLanding' => true])

<!-- ===================== Modal: Detalle de habitación ===================== -->
<div class="modal fade" id="roomDetailModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="detail-title">Detalle</h4>
      </div>
      <div class="modal-body" id="detail-body">
        <div class="loading-rooms"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
      </div>
    </div>
  </div>
</div>

<!-- ===================== Modal: Reservar ===================== -->
<div class="modal fade" id="reserveModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Completa tu reserva</h4>
      </div>
      <div class="modal-body">
        <div id="reserve-message"></div>
        <div class="summary-box" id="reserve-summary"></div>
        <form id="reserveform">
          <input type="hidden" name="room" id="r-room">
          <input type="hidden" name="checkin" id="r-checkin">
          <input type="hidden" name="checkout" id="r-checkout">
          <input type="hidden" name="adults" id="r-adults">
          <input type="hidden" name="children" id="r-children">

          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Tipo de documento</label>
                <select class="form-control" name="document_type" id="r-doctype">
                  @foreach(($documentTypes ?? []) as $dt)
                    <option value="{{ $dt['id'] }}">{{ $dt['description'] }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-sm-8">
              <div class="form-group">
                <label>Número de documento</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="document_number" id="r-docnumber" placeholder="Nº de documento">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" id="r-doc-search"><i class="fa fa-search"></i></button>
                  </span>
                </div>
                <div class="doc-feedback text-muted" id="r-doc-feedback"></div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Nombre / Razón social <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="r-name" required placeholder="Tu nombre completo">
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Teléfono</label>
                <input type="text" class="form-control" name="telephone" id="r-telephone" placeholder="Celular / teléfono">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label>E-mail</label>
                <input type="email" class="form-control" name="email" id="r-email" placeholder="correo@ejemplo.com">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Comentarios / solicitudes especiales</label>
            <textarea class="form-control" name="notes" id="r-notes" rows="2" placeholder="Ej: llegada tardía, cuna, piso alto..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg" id="r-submit">Confirmar reserva</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ===================== Lógica de reservas ===================== -->
<script type="text/javascript">
jQuery(function ($) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var CURRENCY = 'S/';
    var CURRENT_ESTABLISHMENT = {!! json_encode($establishmentId ?? null) !!};
    var DATA = {
        rooms: {!! json_encode($rooms, JSON_UNESCAPED_UNICODE) !!},
        featured: {!! json_encode($featured, JSON_UNESCAPED_UNICODE) !!}
    };
    // Estado de la búsqueda actual (para detalle y reserva)
    var SEARCH = { checkin: null, checkout: null, adults: 1, children: 0, active: false };
    var PLACEHOLDER = '/landing-reservas/images/rooms/356x228.gif';

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

    // ---- render de tarjetas ----
    function roomCard(room) {
        var img = room.main_image || PLACEHOLDER;
        var price = room.min_price > 0
            ? '<div class="room-card__price">' + money(room.min_price) + ' <small>/ noche</small></div>'
            : '<div class="room-card__price"><small>Consultar tarifa</small></div>';
        var total = (room.total && room.nights)
            ? '<div class="room-card__total">' + room.nights + ' noche(s): ' + money(room.total) + '</div>' : '';
        var meta = [];
        if (room.capacity) meta.push('<i class="fa fa-users"></i> ' + room.capacity);
        if (room.beds) meta.push('<i class="fa fa-bed"></i> ' + esc(room.beds));
        if (room.size) meta.push('<i class="fa fa-expand"></i> ' + room.size + ' m²');
        var desc = room.short_description || room.description || 'Habitación cómoda y equipada para tu estancia.';
        var fav = room.featured ? '<span class="room-card__fav"><i class="fa fa-star"></i> Destacada</span>' : '';

        var barPrice = room.min_price > 0
            ? money(room.min_price) + ' <small>/ noche</small>'
            : '<small>Consultar</small>';

        return '' +
        '<div class="col-sm-6 col-md-4">' +
          '<div class="room-card">' +
            '<div class="room-card__media" style="background-image:url(\'' + img + '\');"></div>' +
            '<span class="room-card__badge">' + esc(room.category) + '</span>' + fav +
            // Barra inferior siempre visible: nombre + precio.
            '<div class="room-card__bar">' +
              '<div class="room-card__bar-title">' + esc(room.name) + '</div>' +
              '<div class="room-card__bar-price">' + barPrice + '</div>' +
            '</div>' +
            // Panel que sube al hacer hover con toda la información.
            '<div class="room-card__reveal">' +
              '<div class="room-card__cat">' + esc(room.category) + '</div>' +
              '<div class="room-card__title">' + esc(room.name) + '</div>' +
              (meta.length ? '<div class="room-card__meta">' + meta.join(' &nbsp; ') + '</div>' : '') +
              '<div class="room-card__desc">' + esc(desc) + '</div>' +
              '<div>' + price + total + '</div>' +
              '<div class="room-card__actions">' +
                '<button class="btn btn-default btn-detail" data-id="' + room.id + '"><i class="fa fa-info-circle"></i> Detalle</button>' +
                '<button class="btn btn-primary btn-reserve" data-id="' + room.id + '"><i class="fa fa-calendar-check-o"></i> Reservar</button>' +
              '</div>' +
            '</div>' +
          '</div>' +
        '</div>';
    }

    function renderRooms(list) {
        var $grid = $('#rooms-grid');
        if (!list || !list.length) {
            $grid.html('<div class="empty-rooms"><i class="fa fa-bed fa-3x"></i><p style="margin-top:12px;">No hay habitaciones disponibles para los criterios seleccionados.</p></div>');
            return;
        }
        $grid.html('<div class="row">' + list.map(roomCard).join('') + '</div>');
    }

    function findRoom(id) {
        id = parseInt(id, 10);
        // Preferir la lista de la última búsqueda (trae nights/total)
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
        if (!checkin || !checkout) { alert('Selecciona las fechas de entrada y salida.'); return; }
        if (checkout <= checkin) { alert('La fecha de salida debe ser posterior a la de entrada.'); return; }

        var $btn = $('#btn-search').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');
        $('#rooms-grid').html('<div class="loading-rooms"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Buscando habitaciones disponibles...</p></div>');

        $.post('/reservas/search', {
            checkin: checkin, checkout: checkout,
            adults: $('#adults').val(), children: $('#children').val(),
            establishment_id: CURRENT_ESTABLISHMENT
        }).done(function (res) {
            if (!res.success) { $('#rooms-grid').html('<div class="empty-rooms">' + esc(res.message || 'No se pudo buscar.') + '</div>'); return; }
            SEARCH = { checkin: checkin, checkout: checkout, adults: parseInt($('#adults').val(),10), children: parseInt($('#children').val(),10), active: true };
            window.__lastList = res.rooms;
            renderRooms(res.rooms);
            $('#rooms-heading').text(res.count + ' habitación(es) disponible(s)');
            $('#rooms-subheading').text('Del ' + res.checkin + ' al ' + res.checkout + ' · ' + res.nights + ' noche(s) · ' + res.adults + ' adulto(s)' + (res.children ? ', ' + res.children + ' niño(s)' : ''));
            $('html,body').animate({ scrollTop: $('#rooms-results').offset().top - 70 }, 400);
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo realizar la búsqueda.';
            $('#rooms-grid').html('<div class="empty-rooms">' + esc(m) + '</div>');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Buscar');
        });
    });

    // ---- Detalle ----
    $(document).on('click', '.btn-detail', function () {
        var id = $(this).data('id');
        $('#detail-body').html('<div class="loading-rooms"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $('#roomDetailModal').modal('show');
        var url = '/reservas/room/' + id + '?establishment_id=' + (CURRENT_ESTABLISHMENT || '');
        if (SEARCH.active) url += '&checkin=' + SEARCH.checkin + '&checkout=' + SEARCH.checkout;
        $.get(url).done(function (res) {
            if (!res.success) { $('#detail-body').html('<p class="text-danger">No se pudo cargar el detalle.</p>'); return; }
            renderDetail(res.room);
        }).fail(function () {
            $('#detail-body').html('<p class="text-danger">No se pudo cargar el detalle.</p>');
        });
    });

    function renderDetail(room) {
        $('#detail-title').text(room.category + ' · ' + room.name);
        var images = (room.images && room.images.length) ? room.images : [PLACEHOLDER];
        var gallery = '<div class="modal-gallery"><img id="detail-main" src="' + images[0] + '" alt="' + esc(room.name) + '"></div>';
        var thumbs = images.length > 1
            ? '<div class="modal-thumbs">' + images.map(function (u, i) { return '<img src="' + u + '" class="' + (i===0?'active':'') + '" data-src="' + u + '">'; }).join('') + '</div>'
            : '';
        var meta = [];
        if (room.capacity) meta.push('<i class="fa fa-users"></i> ' + room.capacity + ' huésped(es)');
        if (room.beds) meta.push('<i class="fa fa-bed"></i> ' + esc(room.beds));
        if (room.size) meta.push('<i class="fa fa-expand"></i> ' + room.size + ' m²');
        if (room.floor) meta.push('<i class="fa fa-building"></i> ' + esc(room.floor));
        var amenities = (room.amenities && room.amenities.length)
            ? '<h5 style="margin-top:14px;">Servicios</h5>' + room.amenities.map(function (a) { return '<span class="amenity-pill"><i class="fa ' + amenityIcon(a) + '"></i>' + esc(a) + '</span>'; }).join('')
            : '';
        var price = room.min_price > 0 ? money(room.min_price) + ' <small class="text-muted">/ noche</small>' : 'Consultar tarifa';
        var totalLine = (room.total && room.nights) ? '<div class="room-card__total" style="font-size:14px;">Total ' + room.nights + ' noche(s): <strong>' + money(room.total) + '</strong></div>' : '';
        var availability = '';
        if (typeof room.available !== 'undefined') {
            availability = room.available
                ? '<div class="alert alert-success" style="padding:8px 12px;">Disponible para las fechas seleccionadas.</div>'
                : '<div class="alert alert-warning" style="padding:8px 12px;">No disponible en esas fechas.</div>';
        }

        var html =
            '<div class="row">' +
              '<div class="col-sm-7">' + gallery + thumbs + '</div>' +
              '<div class="col-sm-5">' +
                '<div class="room-card__cat">' + esc(room.category) + '</div>' +
                '<h3 style="margin-top:4px;">' + esc(room.name) + '</h3>' +
                (meta.length ? '<p class="room-card__meta">' + meta.join(' &nbsp; ') + '</p>' : '') +
                '<p>' + esc(room.description || room.short_description || '') + '</p>' +
                '<div class="room-card__price" style="margin-top:10px;">' + price + '</div>' +
                totalLine + availability + amenities +
                '<button class="btn btn-primary btn-block btn-lg btn-reserve" data-id="' + room.id + '" style="margin-top:16px;"><i class="fa fa-calendar-check-o"></i> Reservar</button>' +
              '</div>' +
            '</div>';
        $('#detail-body').html(html);
    }

    $(document).on('click', '.modal-thumbs img', function () {
        $('#detail-main').attr('src', $(this).data('src'));
        $('.modal-thumbs img').removeClass('active');
        $(this).addClass('active');
    });

    // ---- Reservar ----
    $(document).on('click', '.btn-reserve', function () {
        var room = findRoom($(this).data('id'));
        if (!room) return;
        $('#roomDetailModal').modal('hide');
        openReserve(room);
    });

    function openReserve(room) {
        $('#reserve-message').empty();
        $('#reserveform')[0].reset();
        $('#r-room').val(room.id);

        // Tomar fechas de la búsqueda si existe, si no del buscador
        var checkin = SEARCH.active ? SEARCH.checkin : $('#checkin_date').val();
        var checkout = SEARCH.active ? SEARCH.checkout : $('#checkout_date').val();
        var adults = SEARCH.active ? SEARCH.adults : parseInt($('#adults').val(), 10);
        var children = SEARCH.active ? SEARCH.children : parseInt($('#children').val(), 10);

        $('#r-checkin').val(checkin || '');
        $('#r-checkout').val(checkout || '');
        $('#r-adults').val(adults || 1);
        $('#r-children').val(children || 0);

        var datesTxt = (checkin && checkout)
            ? 'Del <strong>' + checkin + '</strong> al <strong>' + checkout + '</strong>'
            : '<span class="text-danger">Selecciona fechas en el buscador antes de reservar.</span>';
        var priceTxt = room.min_price > 0 ? money(room.min_price) + ' / noche' : 'Consultar tarifa';
        $('#reserve-summary').html(
            '<div><strong>' + esc(room.category) + ' · ' + esc(room.name) + '</strong></div>' +
            '<div>' + datesTxt + '</div>' +
            '<div>' + (adults || 1) + ' adulto(s)' + (children ? ', ' + children + ' niño(s)' : '') + ' · ' + priceTxt + '</div>'
        );
        $('#reserveModal').modal('show');
    }

    // Sólo DNI (1) y RUC (6) tienen consulta automática (RENIEC/SUNAT). El resto
    // de documentos (carnet de extranjería, pasaporte, etc.) se ingresan a mano.
    function docLookupType(id) {
        id = String(id);
        if (id === '1') return 'dni';
        if (id === '6') return 'ruc';
        return null;
    }

    // Consulta de documento (autocompleta nombre)
    $('#r-doc-search').on('click', function () {
        var lookup = docLookupType($('#r-doctype').val());
        var num = $.trim($('#r-docnumber').val());
        var $fb = $('#r-doc-feedback').removeClass('text-danger text-success').addClass('text-muted');
        if (!lookup) { $fb.text('Este tipo de documento se ingresa manualmente.'); return; }
        if (!num) { $fb.text('Ingresa el número de documento.'); return; }
        $fb.html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        $.get('/reservas/document/' + lookup + '/' + num).done(function (res) {
            if (res.success && res.data) {
                var d = res.data;
                if (d.name) $('#r-name').val(d.name);
                $fb.removeClass('text-muted').addClass('text-success').text('Datos encontrados.');
            } else {
                $fb.removeClass('text-muted').addClass('text-danger').text(res.message || 'No se encontraron datos.');
            }
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo consultar.';
            $fb.removeClass('text-muted').addClass('text-danger').text(m);
        });
    });

    $('#r-doctype').on('change', function () {
        var id = String($(this).val());
        var placeholders = { '1': 'DNI (8 dígitos)', '6': 'RUC (11 dígitos)', '7': 'Nº de pasaporte', '4': 'Nº de carnet de extranjería' };
        $('#r-docnumber').attr('placeholder', placeholders[id] || 'Nº de documento');
        // Mostrar el botón de consulta sólo para DNI/RUC.
        $('#r-doc-search').toggle(!!docLookupType(id));
    }).trigger('change');

    // Enviar reserva
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
                    // Refrescar disponibilidad si había búsqueda activa
                    if (SEARCH.active) { $('#searchform').trigger('submit'); }
                }
            })
            .fail(function (xhr) {
                $('#reserve-message').html(
                    (xhr.responseText && xhr.responseText.indexOf('alert') !== -1)
                        ? xhr.responseText
                        : alertHtml('danger', 'No se pudo registrar la reserva. Inténtalo de nuevo.'));
            })
            .always(function () { $btn.prop('disabled', false).html('Confirmar reserva'); });
    });

    function alertHtml(type, msg) {
        return '<div class="alert alert-' + type + ' alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>' + esc(msg) + '</div>';
    }

    // ---- fechas mínimas ----
    (function initDates() {
        var today = new Date().toISOString().split('T')[0];
        $('#checkin_date').attr('min', today).on('change', function () {
            var next = new Date($(this).val()); next.setDate(next.getDate() + 1);
            $('#checkout_date').attr('min', next.toISOString().split('T')[0]);
            if ($('#checkout_date').val() && $('#checkout_date').val() <= $(this).val()) {
                $('#checkout_date').val(next.toISOString().split('T')[0]);
            }
        });
        $('#checkout_date').attr('min', today);
    })();

    // Smooth scroll sólo para los enlaces de navegación internos (no rompe tabs/sliders)
    $(document).on('click', 'a.nav-scroll', function (e) {
        var target = $(this).attr('href');
        if (target && target.charAt(0) === '#' && target.length > 1 && $(target).length) {
            e.preventDefault();
            $('html,body').animate({ scrollTop: $(target).offset().top - 70 }, 400);
        }
    });
});
</script>

</body>
</html>
