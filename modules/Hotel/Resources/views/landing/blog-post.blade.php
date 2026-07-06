<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $hotelLogo = (!empty($establishment->logo)) ? asset('storage/uploads/logos/'.$establishment->logo) : null;
    $postDate  = optional($post->published_at)->format('d/m/Y');
    $heroImage = $post->image_url;
@endphp
<title>{{ $post->title }} · {{ $hotelName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155) }}">
<link rel="shortcut icon" href="/landing-reservas/favicon.ico">

<link rel="stylesheet" href="/landing-reservas/css/animate.css">
<link rel="stylesheet" href="/landing-reservas/css/bootstrap.css">
<link rel="stylesheet" href="/landing-reservas/css/font-awesome.min.css">
<link rel="stylesheet" href="/landing-reservas/css/theme.css">
<link rel="stylesheet" href="/landing-reservas/css/colors/turquoise.css">
<link rel="stylesheet" href="/landing-reservas/css/responsive.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600,700">

<script type="text/javascript" src="/landing-reservas/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/bootstrap.min.js"></script>

<style>
  .post-hero { height:380px; background:#2c3e50 center/cover no-repeat; position:relative; }
  .post-hero::after { content:''; position:absolute; inset:0; background:linear-gradient(to bottom, rgba(0,0,0,.15), rgba(0,0,0,.55)); }
  .post-hero__inner { position:absolute; inset:0; display:flex; align-items:flex-end; z-index:2; }
  .post-hero__inner .container { padding-bottom:34px; }
  .post-hero h1 { color:#fff; font-weight:700; margin:0 0 8px; text-shadow:0 2px 8px rgba(0,0,0,.4); }
  .post-hero .post-meta { color:rgba(255,255,255,.9); font-size:14px; }
  .post-meta i { margin-right:5px; color:#1abc9c; }
  .post-body { padding:50px 0 60px; }
  .post-content { color:#3a4b57; font-size:16px; line-height:1.8; }
  .post-content p { margin-bottom:18px; }
  .post-content img { max-width:100%; height:auto; border-radius:6px; }
  .post-back { display:inline-block; margin-bottom:24px; color:#1abc9c; font-weight:600; }
  .post-sidebar h4 { font-weight:700; color:#2c3e50; border-bottom:2px solid #1abc9c; padding-bottom:8px; display:inline-block; margin-bottom:18px; }
  .recent-item { display:flex; gap:12px; margin-bottom:16px; }
  .recent-item__img { width:70px; height:56px; border-radius:5px; background:#eceff1 center/cover no-repeat; flex-shrink:0; }
  .recent-item__title { font-size:14px; font-weight:600; line-height:1.35; }
  .recent-item__title a { color:#2c3e50; }
  .recent-item__title a:hover { color:#1abc9c; }
  .recent-item__date { font-size:12px; color:#95a5a6; }
</style>
</head>
<body>

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
          <li><a href="/reservas">Inicio</a></li>
          <li><a href="/reservas#rooms-results">Habitaciones</a></li>
          <li class="active"><a href="/reservas/blog">Blog</a></li>
          <li><a href="/reservas#reservation-form" class="text-uppercase" style="color:#1abc9c;font-weight:600;">Reservar</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<section class="post-hero" @if($heroImage)style="background-image:url('{{ $heroImage }}');"@endif>
  <div class="post-hero__inner">
    <div class="container">
      <h1>{{ $post->title }}</h1>
      <div class="post-meta">
        @if($postDate)<span><i class="fa fa-calendar"></i> {{ $postDate }}</span>@endif
        @if($post->author)<span style="margin-left:14px;"><i class="fa fa-user"></i> {{ $post->author }}</span>@endif
      </div>
    </div>
  </div>
</section>

<section class="post-body">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <a href="/reservas/blog" class="post-back"><i class="fa fa-angle-left"></i> Volver al blog</a>
        @if($post->excerpt)
          <p class="lead" style="color:#566573;">{{ $post->excerpt }}</p>
        @endif
        <div class="post-content">
          {!! nl2br(e($post->content)) !!}
        </div>
      </div>
      <div class="col-md-4 post-sidebar">
        @if($recent->count())
          <h4>Entradas recientes</h4>
          @foreach($recent as $r)
            @php $rImg = $r->image_url ?: '/landing-reservas/images/gallery/800x504.gif'; @endphp
            <div class="recent-item">
              <a href="{{ url('reservas/blog/'.$r->slug) }}" class="recent-item__img" style="background-image:url('{{ $rImg }}');"></a>
              <div>
                <div class="recent-item__title"><a href="{{ url('reservas/blog/'.$r->slug) }}">{{ $r->title }}</a></div>
                <div class="recent-item__date">{{ optional($r->published_at)->format('d/m/Y') }}</div>
              </div>
            </div>
          @endforeach
        @endif
        <a href="/reservas#reservation-form" class="btn btn-primary btn-block" style="margin-top:20px;">Reservar ahora</a>
      </div>
    </div>
  </div>
</section>

<footer id="contacto" class="mt100">
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 text-center"> &copy; {{ date('Y') }} {{ $hotelName }}. Todos los derechos reservados. </div>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
