<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $pubDate   = $post->published_at ?: $post->created_at;
    $heroImage = $post->image_url;
    $cfg = $settings ?? \Modules\Hotel\Models\HotelLandingSetting::mergeDefaults([]);
    $themeColor = in_array(($cfg['color'] ?? 'turquoise'), ['turquoise','blue','green','orange','purple','red','brown','black']) ? $cfg['color'] : 'turquoise';
@endphp
<title>{{ $post->title }} · {{ $hotelName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="description" content="{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155) }}">
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
<script type="text/javascript" src="/landing-reservas/js/jquery.parallax-1.1.3.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="/landing-reservas/js/jquery.sticky.js"></script>
<script type="text/javascript" src="/landing-reservas/js/waypoints.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/custom.js"></script>

<style>
  .post-featured { display:block; height:420px; background:#eceff1 center/cover no-repeat; border-radius:4px; margin-bottom:26px; }
  .post-content { color:#3a4b57; font-size:16px; line-height:1.8; }
  .post-content p { margin-bottom:18px; }
  .post-content img { max-width:100%; height:auto; border-radius:6px; }
  .news-thumb .news-thumb-img { display:block; width:65px; height:65px; background:#eceff1 center/cover no-repeat; border-radius:4px; }
</style>
</head>

<body>

@include('hotel::landing.partials.header', ['onLanding' => false, 'activeNav' => 'blog'])

<!-- Parallax Effect -->
<script type="text/javascript">$(document).ready(function(){$('#parallax-pagetitle').parallax("50%", -0.55);});</script>

<section class="parallax-effect">
  <div id="parallax-pagetitle" style="background-image: url(/landing-reservas/images/parallax/1900x911.gif);">
    <div class="color-overlay">
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <ol class="breadcrumb">
              <li><a href="/reservas">Inicio</a></li>
              <li><a href="/reservas/blog">Blog</a></li>
              <li class="active">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
            </ol>
            <h1>Blog</h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <div class="row">
    <!-- Blog -->
    <section class="blog mt50">
      <div class="col-md-9">
        <article>
          @if($heroImage)
            <span class="post-featured" style="background-image:url('{{ $heroImage }}');"></span>
          @endif
          <div class="row">
            <div class="col-sm-1 col-xs-2 meta">
              <div class="meta-date"><span>{{ $pubDate ? ucfirst($pubDate->translatedFormat('M')) : '' }}</span>{{ $pubDate ? $pubDate->format('d') : '' }}</div>
            </div>
            <div class="col-sm-11 col-xs-10 meta">
              <h2>{{ $post->title }}</h2>
              @if($post->author)<span class="meta-author"><i class="fa fa-user"></i> {{ $post->author }}</span>@endif
              @if($pubDate)<span class="meta-category"><i class="fa fa-calendar"></i> {{ $pubDate->format('d/m/Y') }}</span>@endif
            </div>
            <div class="col-md-12">
              @if($post->excerpt)
                <p class="lead">{{ $post->excerpt }}</p>
              @endif
              <div class="post-content">
                {!! nl2br(e($post->content)) !!}
              </div>
              <a href="/reservas/blog" class="btn btn-default mt30"><i class="fa fa-angle-left"></i> Volver al blog</a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- Aside -->
    <aside class="mt50">
      <div class="col-md-3">
        @if($recent->count())
          <div class="widget clearfix">
            <h3>Entradas recientes</h3>
            <ul class="list-unstyled">
              @foreach($recent as $r)
                @php
                  $rImg  = $r->image_url;
                  $rDate = $r->published_at ?: $r->created_at;
                @endphp
                <li>
                  <article>
                    <div class="news-thumb">
                      <a href="{{ url('reservas/blog/'.$r->slug) }}" class="news-thumb-img" @if($rImg)style="background-image:url('{{ $rImg }}');"@endif></a>
                    </div>
                    <div class="news-content clearfix">
                      <h4><a href="{{ url('reservas/blog/'.$r->slug) }}">{{ \Illuminate\Support\Str::limit($r->title, 45) }}</a></h4>
                      <span>{{ $rDate ? $rDate->format('d/m/Y') : '' }}</span>
                    </div>
                  </article>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="widget">
          <a href="{{ url('/reservas') }}#reservation-form" class="btn btn-primary btn-block">Reservar ahora</a>
        </div>
      </div>
    </aside>
  </div>
</div>

@include('hotel::landing.partials.footer', ['onLanding' => false])

</body>
</html>
