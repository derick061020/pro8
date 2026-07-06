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
  .post-video { position:relative; width:100%; padding-top:56.25%; border-radius:4px; overflow:hidden; background:#000; margin-bottom:26px; }
  .post-video iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:0; }
  .post-content { color:#3a4b57; font-size:16px; line-height:1.8; }
  .post-content p { margin-bottom:18px; }
  .post-content img { max-width:100%; height:auto; border-radius:6px; }
  .post-content h1, .post-content h2, .post-content h3, .post-content h4 { color:#2c3e50; margin:26px 0 12px; font-weight:700; }
  .post-content ul, .post-content ol { margin:0 0 18px 22px; }
  .post-content blockquote { border-left:4px solid #1abc9c; margin:0 0 18px; padding:6px 18px; color:#55707f; background:#f6fbfa; font-style:italic; }
  .post-content a { color:#1abc9c; }
  .post-content table { border-collapse:collapse; width:100%; margin-bottom:18px; }
  .post-content table td, .post-content table th { border:1px solid #dbe2e6; padding:8px 10px; }
  .post-content figure { max-width:100%; }
  .post-content .media, .post-content iframe { max-width:100%; }
  .post-content figure.media { position:relative; padding-top:56.25%; }
  .post-content figure.media iframe { position:absolute; top:0; left:0; width:100%; height:100%; }
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
          @if($post->hasVideoCover() && $post->video_embed_url)
            <div class="post-video">
              <iframe src="{{ $post->video_embed_url }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
          @elseif($heroImage)
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
                @php
                    // Contenido nuevo (CKEditor) es HTML; entradas antiguas eran texto plano.
                    $isHtml = $post->content && $post->content !== strip_tags($post->content);
                @endphp
                @if($isHtml)
                  {!! $post->content !!}
                @else
                  {!! nl2br(e($post->content)) !!}
                @endif
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
                  $rImg  = $r->cover_image;
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
