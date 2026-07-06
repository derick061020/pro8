<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $hotelPhone = $establishment->telephone ?? null;
    $hotelEmail = $establishment->email ?? null;
    $hotelAddress = $establishment->address ?? null;
    $hotelWeb = $establishment->web_address ?? null;
    $hotelLogo = (!empty($establishment->logo)) ? asset('storage/uploads/logos/'.$establishment->logo) : null;
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
<link rel="stylesheet" href="/landing-reservas/css/colors/turquoise.css">
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
  .search-box { background:#fff; border-radius:6px; box-shadow:0 10px 30px rgba(0,0,0,.12); padding:18px; }
  .search-box label { font-weight:600; font-size:12px; text-transform:uppercase; color:#666; }
  .search-box .form-control { height:42px; }
  .room-card { background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,.08); margin-bottom:30px; display:flex; flex-direction:column; height:calc(100% - 30px); transition:transform .2s, box-shadow .2s; }
  .room-card:hover { transform:translateY(-4px); box-shadow:0 10px 28px rgba(0,0,0,.15); }
  .room-card__img { position:relative; height:210px; background:#eceff1 center/cover no-repeat; }
  .room-card__badge { position:absolute; top:12px; left:12px; background:#1abc9c; color:#fff; font-size:11px; font-weight:600; padding:4px 10px; border-radius:20px; text-transform:uppercase; }
  .room-card__fav { position:absolute; top:12px; right:12px; background:#e67e22; color:#fff; font-size:11px; font-weight:600; padding:4px 10px; border-radius:20px; }
  .room-card__body { padding:16px 18px; flex:1; display:flex; flex-direction:column; }
  .room-card__cat { color:#1abc9c; font-size:12px; text-transform:uppercase; letter-spacing:.5px; font-weight:600; }
  .room-card__title { font-size:19px; font-weight:700; margin:2px 0 6px; color:#2c3e50; }
  .room-card__meta { color:#7f8c8d; font-size:13px; margin-bottom:8px; }
  .room-card__meta i { color:#1abc9c; margin-right:3px; }
  .room-card__desc { color:#666; font-size:13px; flex:1; }
  .room-card__price { font-size:22px; font-weight:700; color:#2c3e50; }
  .room-card__price small { font-size:12px; color:#95a5a6; font-weight:400; }
  .room-card__total { font-size:12px; color:#16a085; font-weight:600; }
  .room-card__actions { display:flex; gap:8px; margin-top:12px; }
  .room-card__actions .btn { flex:1; }
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
  .blog-card__img { display:block; height:200px; background:#eceff1 center/cover no-repeat; }
  .blog-card__body { padding:18px 20px; }
  .blog-card__date { color:#1abc9c; font-size:12px; text-transform:uppercase; letter-spacing:.5px; font-weight:600; margin-bottom:6px; }
  .blog-card__date i { margin-right:4px; }
  .blog-card__title { font-size:19px; font-weight:700; margin:0 0 10px; line-height:1.3; }
  .blog-card__title a { color:#2c3e50; }
  .blog-card__title a:hover { color:#1abc9c; }
  .blog-card__excerpt { color:#666; font-size:14px; margin-bottom:12px; }
  .blog-card__more { color:#1abc9c; font-weight:600; font-size:13px; text-transform:uppercase; }
</style>
</head>

<body>

<!-- Top header -->
<div id="top-header">
  <div class="container">
    <div class="row">
      <div class="col-xs-6">
        <div class="th-text pull-left">
          @if($hotelPhone)<div class="th-item"> <a href="tel:{{ $hotelPhone }}"><i class="fa fa-phone"></i> {{ $hotelPhone }}</a> </div>@endif
          @if($hotelEmail)<div class="th-item"> <a href="mailto:{{ $hotelEmail }}"><i class="fa fa-envelope"></i> {{ $hotelEmail }} </a></div>@endif
        </div>
      </div>
      <div class="col-xs-6">
        <div class="th-text pull-right">
          <div class="th-item">
            <div class="social-icons">
              @if($hotelWeb)<a href="{{ $hotelWeb }}" target="_blank"><i class="fa fa-globe"></i></a>@endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Header -->
<header>
  <div class="navbar yamm navbar-default" id="sticky">
    <div class="container">
      <div class="navbar-header">
        <button type="button" data-toggle="collapse" data-target="#navbar-collapse-grid" class="navbar-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <a href="/reservas" class="navbar-brand">
          @if($hotelLogo)
            <div id="logo"><img src="{{ $hotelLogo }}" alt="{{ $hotelName }}" style="height:44px;"></div>
          @else
            <span style="font-size:22px;font-weight:700;color:#2c3e50;line-height:50px;">{{ $hotelName }}</span>
          @endif
        </a>
      </div>
      <div id="navbar-collapse-grid" class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li class="active"><a href="#top" class="nav-scroll">Inicio</a></li>
          <li><a href="#rooms-results" class="nav-scroll">Habitaciones</a></li>
          <li><a href="#gallery" class="nav-scroll">Galería</a></li>
          @if(isset($blogPosts) && $blogPosts->count())
          <li><a href="#blog" class="nav-scroll">Blog</a></li>
          @endif
          <li><a href="#contacto" class="nav-scroll">Contacto</a></li>
          <li><a href="#reservation-form" class="nav-scroll text-uppercase" style="color:#1abc9c;font-weight:600;">Reservar</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<a id="top"></a>
<!-- Revolution Slider -->
<section class="revolution-slider">
  <div class="bannercontainer">
    <div class="banner">
      <ul>
        <!-- Slide 1 -->
        <li data-transition="fade" data-slotamount="7" data-masterspeed="1500" >
          <!-- Main Image -->
          <img src="/landing-reservas/images/slides/1700x449.gif" style="opacity:0;" alt="slidebg1"  data-bgfit="cover" data-bgposition="left bottom" data-bgrepeat="no-repeat">
          <!-- Layers -->
          <!-- Layer 1 -->
          <div class="caption sft revolution-starhotel bigtext"
          				data-x="505"
                        data-y="30"
                        data-speed="700"
                        data-start="1700"
                        data-easing="easeOutBack">
							<span><i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i></span> {{ $hotelName }} <span><i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i></span></div>
          <!-- Layer 2 -->
          <div class="caption sft revolution-starhotel smalltext"
          				data-x="605"
                        data-y="105"
                        data-speed="800"
                        data-start="1700"
                        data-easing="easeOutBack">
							<span>Reserva tu estancia con nosotros</span></div>
        <!-- Layer 3 -->
                  <div class="caption sft"
          				data-x="775"
                        data-y="175"
                        data-speed="1000"
                        data-start="1900"
                        data-easing="easeOutBack">
							<a href="#rooms-results" class="button btn btn-purple btn-lg nav-scroll">Ver habitaciones</a>
                  </div>
        </li>
		<!-- Slide 2 -->
        <li data-transition="boxfade" data-slotamount="7" data-masterspeed="1000" >
          <!-- Main Image -->
          <img src="/landing-reservas/images/slides/1700x449.gif"  alt="darkblurbg"  data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
          <!-- Layers -->
          <!-- Layer 1 -->
          <div class="caption sft revolution-starhotel bigtext"
          				data-x="585"
                        data-y="30"
                        data-speed="700"
                        data-start="1700"
                        data-easing="easeOutBack">
							<span><i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i></span> Bienvenido <span><i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i> <i class="fa fa-star-o"></i></span></div>
          <!-- Layer 2 -->
          <div class="caption sft revolution-starhotel smalltext"
          				data-x="682"
                        data-y="105"
                        data-speed="800"
                        data-start="1700"
                        data-easing="easeOutBack">
							<span>Disponibilidad en tiempo real</span></div>
        <!-- Layer 3 -->
                  <div class="caption sft"
          				data-x="785"
                        data-y="175"
                        data-speed="1000"
                        data-start="1900"
                        data-easing="easeOutBack">
							<a href="#reservation-form" class="button btn btn-purple btn-lg nav-scroll">Reservar ahora</a>
                  </div>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- Buscador de disponibilidad -->
<section id="reservation-form">
  <div class="container">
    <div class="search-box">
      <form class="form" role="form" id="searchform" onsubmit="return false;">
        <div class="row">
          <div class="col-sm-3 col-xs-6">
            <div class="form-group">
              <label for="checkin_date"><i class="fa fa-calendar"></i> Entrada</label>
              <input name="checkin" type="date" id="checkin_date" class="form-control" required>
            </div>
          </div>
          <div class="col-sm-3 col-xs-6">
            <div class="form-group">
              <label for="checkout_date"><i class="fa fa-calendar"></i> Salida</label>
              <input name="checkout" type="date" id="checkout_date" class="form-control" required>
            </div>
          </div>
          <div class="col-sm-2 col-xs-6">
            <div class="form-group">
              <label for="adults"><i class="fa fa-user"></i> Adultos</label>
              <select name="adults" id="adults" class="form-control">
                @for($i=1;$i<=8;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
              </select>
            </div>
          </div>
          <div class="col-sm-2 col-xs-6">
            <div class="form-group">
              <label for="children"><i class="fa fa-child"></i> Niños</label>
              <select name="children" id="children" class="form-control">
                @for($i=0;$i<=6;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
              </select>
            </div>
          </div>
          <div class="col-sm-2 col-xs-12">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" id="btn-search" class="btn btn-primary btn-block" style="height:42px;"><i class="fa fa-search"></i> Buscar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Resultados / Habitaciones -->
<section class="rooms mt50" id="rooms-results">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="lined-heading"><span id="rooms-heading">Nuestras habitaciones</span></h2>
        <p class="text-center text-muted" id="rooms-subheading" style="margin-top:-10px;margin-bottom:25px;">Selecciona fechas para ver disponibilidad y precios.</p>
      </div>
      <div class="col-sm-12" id="rooms-grid"></div>
    </div>
  </div>
</section>

<!-- USP's -->
<section class="usp mt100">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="lined-heading"><span>¿Por qué reservar con nosotros?</span></h2>
      </div>
      <div class="col-sm-3 bounceIn appear" data-start="0">
      <div class="box-icon">
        <div class="circle"><i class="fa fa-calendar-check-o fa-lg"></i></div>
        <h3>Reserva en línea</h3>
        <p>Consulta disponibilidad en tiempo real y reserva tu habitación en pocos pasos, sin llamadas ni esperas.</p>
        <a href="#reservation-form" class="nav-scroll">Reservar<i class="fa fa-angle-right"></i></a> </div>
        </div>
      <div class="col-sm-3 bounceIn appear" data-start="400">
      <div class="box-icon">
        <div class="circle"><i class="fa fa-credit-card fa-lg"></i></div>
        <h3>Confirmación rápida</h3>
        <p>Recibe la confirmación de tu reserva y coordina el pago directamente con el hotel de forma segura.</p>
        <a href="#reservation-form" class="nav-scroll">Reservar<i class="fa fa-angle-right"></i></a> </div>
        </div>
      <div class="col-sm-3 bounceIn appear" data-start="800">
      <div class="box-icon">
        <div class="circle"><i class="fa fa-bed fa-lg"></i></div>
        <h3>Habitaciones cómodas</h3>
        <p>Conoce el detalle, las fotos y los servicios de cada habitación antes de elegir la que más te conviene.</p>
        <a href="#rooms-results" class="nav-scroll">Ver habitaciones<i class="fa fa-angle-right"></i></a> </div>
        </div>
      <div class="col-sm-3 bounceIn appear" data-start="1200">
      <div class="box-icon">
        <div class="circle"><i class="fa fa-headphones fa-lg"></i></div>
        <h3>Atención dedicada</h3>
        <p>Nuestro equipo te acompaña antes, durante y después de tu estancia para que todo sea perfecto.</p>
        <a href="#contacto" class="nav-scroll">Contacto<i class="fa fa-angle-right"></i></a> </div>
    </div>
    </div>
  </div>
</section>

<!-- Parallax Effect -->
<script type="text/javascript">$(document).ready(function(){$('#parallax-image').parallax("50%", -0.25);});</script>

<section class="parallax-effect mt100">
  <div id="parallax-image" style="background-image: url(/landing-reservas/images/parallax/1900x911.gif);">
    <div class="color-overlay fadeIn appear" data-start="600">
      <div class="container">
        <div class="content">
          <h3 class="text-center"><i class="fa fa fa-star-o"></i> {{ $hotelName }}</h3>
          <p class="text-center">Vive una experiencia inolvidable
		  <br>
		  <a href="#rooms-results" class="btn btn-default btn-lg mt30 nav-scroll">Ver habitaciones</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Gallery -->
<section class="gallery-slider mt100" id="gallery">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="lined-heading"><span>Galería</span></h2>
      </div>
    </div>
  </div>
  <div id="owl-gallery" class="owl-carousel">
    <div class="item"><a href="/landing-reservas/images/gallery/800x504.gif" data-rel="prettyPhoto[gallery1]"><img src="/landing-reservas/images/gallery/800x504.gif" alt="Image 1"><i class="fa fa-search"></i></a></div>
    <div class="item"><a href="/landing-reservas/images/gallery/800x504.gif" data-rel="prettyPhoto[gallery1]"><img src="/landing-reservas/images/gallery/800x504.gif" alt="Image 2"><i class="fa fa-search"></i></a></div>
    <div class="item"><a href="/landing-reservas/images/gallery/800x504.gif" data-rel="prettyPhoto[gallery1]"><img src="/landing-reservas/images/gallery/800x504.gif" alt="Image 3"><i class="fa fa-search"></i></a></div>
    <div class="item"><a href="/landing-reservas/images/gallery/800x504.gif" data-rel="prettyPhoto[gallery1]"><img src="/landing-reservas/images/gallery/800x504.gif" alt="Image 4"><i class="fa fa-search"></i></a></div>
  </div>
</section>

<div class="container">
  <div class="row">
    <!-- Testimonials -->
    <section class="testimonials mt100">
      <div class="col-md-6">
        <h2 class="lined-heading bounceInLeft appear" data-start="0"><span>Lo que opinan nuestros huéspedes</span></h2>
        <div id="owl-reviews" class="owl-carousel">
          <div class="item">
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-2 col-xs-12"> <img src="/landing-reservas/images/reviews/100x100.gif" alt="Review 1" class="img-circle" /></div>
              <div class="col-lg-9 col-md-8 col-sm-10 col-xs-12">
                <div class="text-balloon">Excelente atención y habitaciones impecables. Volveremos sin dudarlo. <span>María G., Habitación doble</span> </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-2 col-xs-12"> <img src="/landing-reservas/images/reviews/100x100.gif" alt="Review 2" class="img-circle" /></div>
              <div class="col-lg-9 col-md-8 col-sm-10 col-xs-12">
                <div class="text-balloon">¡Un 5 de 5! Personal amable, limpio y muy cómodo. Totalmente recomendado. <span>Carlos D., Habitación simple</span> </div>
              </div>
            </div>
          </div>
          <div class="item">
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-2 col-xs-12"> <img src="/landing-reservas/images/reviews/100x100.gif" alt="Review 3" class="img-circle" /></div>
              <div class="col-lg-9 col-md-8 col-sm-10 col-xs-12">
                <div class="text-balloon">Un lugar encantador. La próxima vez reservaré una estancia más larga. <span>Rosa O., Habitación simple</span> </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 col-sm-2 col-xs-12"> <img src="/landing-reservas/images/reviews/100x100.gif" alt="Review 4" class="img-circle" /></div>
              <div class="col-lg-9 col-md-8 col-sm-10 col-xs-12">
                <div class="text-balloon">¡El mejor hotel de la ciudad! Buena ubicación y un servicio inmejorable. <span>Luis A., Habitación simple</span> </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About -->
    <section class="about mt100">
      <div class="col-md-6">
        <h2 class="lined-heading bounceInRight appear" data-start="800"><span>Sobre el hotel</span></h2>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
          <li class="active"><a href="#hotel" data-toggle="tab">El hotel</a></li>
          <li><a href="#events" data-toggle="tab">Eventos</a></li>
          <li><a href="#kids" data-toggle="tab">Familias</a></li>
          <li><a href="#business" data-toggle="tab">Negocios</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane fade in active" id="hotel">
            <p>{{ $establishment->aditional_information ?? 'Te damos la bienvenida a un espacio pensado para tu descanso. Habitaciones cómodas, atención cercana y todo lo que necesitas para una estancia perfecta.' }}</p>
            <p><img src="/landing-reservas/images/tab/197x147.gif" alt="hotel" class="pull-right"> Disfruta de nuestras instalaciones y servicios. Reserva en línea y asegura tu habitación al mejor precio, con confirmación directa del hotel.</p>
          </div>
          <div class="tab-pane fade" id="events">Organizamos y recibimos eventos. Consúltanos por disponibilidad de salas y servicios para tu celebración o reunión.</div>
          <div class="tab-pane fade" id="kids">Un lugar ideal para venir en familia. Contamos con opciones pensadas para que grandes y pequeños se sientan como en casa.</div>
          <div class="tab-pane fade" id="business">¿Viaje de negocios? Ofrecemos habitaciones equipadas, conexión y la comodidad que necesitas para trabajar y descansar.</div>
        </div>
      </div>
    </section>
  </div>
</div>

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
          $postImage = $post->image_url ?: '/landing-reservas/images/gallery/800x504.gif';
          $postDate  = optional($post->published_at)->format('d/m/Y');
        @endphp
        <div class="col-md-4 col-sm-6">
          <div class="blog-card">
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="blog-card__img" style="background-image:url('{{ $postImage }}');"></a>
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

<!-- Call To Action -->
<section id="call-to-action" class="mt100">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-12">
        <h2>¿Listo para tu próxima estancia? Reserva ahora en línea.</h2>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <a href="#reservation-form" class="btn btn-default btn-lg pull-right nav-scroll">Ver disponibilidad</a>
      </div>
    </div>
  </div>
</section>

<!-- Contacto / Footer -->
<footer id="contacto" class="mt100">
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-sm-4">
        <h4>{{ $hotelName }}</h4>
        <p>{{ $establishment->aditional_information ?? 'Te damos la bienvenida. Reserva tu estancia con nosotros y disfruta de una experiencia inolvidable.' }}</p>
      </div>
      <div class="col-md-4 col-sm-4">
        <h4>Contacto</h4>
        <address>
          @if($hotelAddress)<i class="fa fa-map-marker"></i> {{ $hotelAddress }}<br>@endif
          @if($hotelPhone)<i class="fa fa-phone"></i> <a href="tel:{{ $hotelPhone }}">{{ $hotelPhone }}</a><br>@endif
          @if($hotelEmail)<i class="fa fa-envelope"></i> <a href="mailto:{{ $hotelEmail }}">{{ $hotelEmail }}</a><br>@endif
          @if($hotelWeb)<i class="fa fa-globe"></i> <a href="{{ $hotelWeb }}" target="_blank">{{ $hotelWeb }}</a>@endif
        </address>
      </div>
      <div class="col-md-4 col-sm-4">
        <h4>Reserva ahora</h4>
        <p>Consulta disponibilidad en tiempo real y asegura tu habitación.</p>
        <a href="#reservation-form" class="btn btn-primary btn-lg nav-scroll">Ver disponibilidad</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 text-center"> &copy; {{ date('Y') }} {{ $hotelName }}. Todos los derechos reservados. </div>
      </div>
    </div>
  </div>
</footer>

<div id="go-top"><i class="fa fa-angle-up fa-2x"></i></div>

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
                <label>Documento</label>
                <select class="form-control" name="document_type" id="r-doctype">
                  <option value="dni">DNI</option>
                  <option value="ruc">RUC</option>
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

        return '' +
        '<div class="col-sm-6 col-md-4">' +
          '<div class="room-card">' +
            '<div class="room-card__img" style="background-image:url(\'' + img + '\');">' +
              '<span class="room-card__badge">' + esc(room.category) + '</span>' + fav +
            '</div>' +
            '<div class="room-card__body">' +
              '<div class="room-card__cat">' + esc(room.category) + '</div>' +
              '<div class="room-card__title">' + esc(room.name) + '</div>' +
              (meta.length ? '<div class="room-card__meta">' + meta.join(' &nbsp; ') + '</div>' : '') +
              '<div class="room-card__desc">' + esc(desc) + '</div>' +
              '<div style="margin-top:10px;">' + price + total + '</div>' +
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
            adults: $('#adults').val(), children: $('#children').val()
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
        var url = '/reservas/room/' + id;
        if (SEARCH.active) url += '?checkin=' + SEARCH.checkin + '&checkout=' + SEARCH.checkout;
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

    // Consulta de documento (autocompleta nombre)
    $('#r-doc-search').on('click', function () {
        var type = $('#r-doctype').val(), num = $.trim($('#r-docnumber').val());
        var $fb = $('#r-doc-feedback').removeClass('text-danger text-success').addClass('text-muted');
        if (!num) { $fb.text('Ingresa el número de documento.'); return; }
        $fb.html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        $.get('/reservas/document/' + type + '/' + num).done(function (res) {
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
        $('#r-docnumber').attr('placeholder', $(this).val() === 'ruc' ? 'RUC (11 dígitos)' : 'DNI (8 dígitos)');
    });

    // Enviar reserva
    $('#reserveform').on('submit', function (e) {
        e.preventDefault();
        if (!$('#r-checkin').val() || !$('#r-checkout').val()) {
            $('#reserve-message').html(alertHtml('danger', 'Selecciona las fechas en el buscador antes de reservar.'));
            return;
        }
        var $btn = $('#r-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
        $.post('/reservas/store', $(this).serialize())
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
