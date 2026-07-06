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
<title>Blog · {{ $hotelName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  .blog-hero { background:#2c3e50 center/cover no-repeat; color:#fff; padding:70px 0 60px; text-align:center; }
  .blog-hero h1 { color:#fff; font-weight:700; margin:0 0 8px; }
  .blog-hero p { color:rgba(255,255,255,.85); font-size:16px; margin:0; }
  .blog-list { padding:60px 0; }
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
  .blog-empty { text-align:center; padding:60px 0; color:#7f8c8d; }
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

<section class="blog-hero">
  <div class="container">
    <h1>Nuestro blog</h1>
    <p>Novedades, consejos y noticias de {{ $hotelName }}</p>
  </div>
</section>

<section class="blog-list">
  <div class="container">
    @if($posts->count())
      <div class="row">
        @foreach($posts as $post)
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
    @else
      <div class="blog-empty">
        <i class="fa fa-newspaper-o fa-3x" style="margin-bottom:15px;"></i>
        <p>Todavía no hay entradas publicadas. Vuelve pronto.</p>
      </div>
    @endif
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
