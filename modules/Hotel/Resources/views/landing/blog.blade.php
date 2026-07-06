<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $cfg = $settings ?? \Modules\Hotel\Models\HotelLandingSetting::mergeDefaults([]);
    $themeColor = in_array(($cfg['color'] ?? 'turquoise'), ['turquoise','blue','green','orange','purple','red','brown','black']) ? $cfg['color'] : 'turquoise';
@endphp
<title>Blog · {{ $hotelName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
<script type="text/javascript" src="/landing-reservas/js/jquery.sticky.js"></script>
<script type="text/javascript" src="/landing-reservas/js/waypoints.min.js"></script>
<script type="text/javascript" src="/landing-reservas/js/custom.js"></script>

<style>
  .blog article .article-img { display:block; height:340px; background:#eceff1 center/cover no-repeat; border-radius:4px; overflow:hidden; }
  .blog article .intro { margin-top:8px; }
  .news-thumb .news-thumb-img { display:block; width:65px; height:65px; background:#eceff1 center/cover no-repeat; border-radius:4px; }
  .blog-empty { text-align:center; padding:40px 0; color:#7f8c8d; }
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
              <li class="active">Blog</li>
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
        @forelse($posts as $post)
          @php
            $postImage = $post->image_url;
            $pubDate   = $post->published_at ?: $post->created_at;
            $excerpt   = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 220);
          @endphp
          <article>
            @if($postImage)
              <a href="{{ url('reservas/blog/'.$post->slug) }}" class="article-img" style="background-image:url('{{ $postImage }}');"></a>
            @endif
            <div class="row">
              <div class="col-sm-1 col-xs-2 meta">
                <div class="meta-date"><span>{{ $pubDate ? ucfirst($pubDate->translatedFormat('M')) : '' }}</span>{{ $pubDate ? $pubDate->format('d') : '' }}</div>
              </div>
              <div class="col-sm-11 col-xs-10 meta">
                <h2><a href="{{ url('reservas/blog/'.$post->slug) }}">{{ $post->title }}</a></h2>
                @if($post->author)<span class="meta-author"><i class="fa fa-user"></i> {{ $post->author }}</span>@endif
                @if($pubDate)<span class="meta-category"><i class="fa fa-calendar"></i> {{ $pubDate->format('d/m/Y') }}</span>@endif
                <p class="intro">{{ $excerpt }}</p>
                <a href="{{ url('reservas/blog/'.$post->slug) }}" class="btn btn-primary pull-right">Leer más</a>
              </div>
            </div>
          </article>
        @empty
          <div class="blog-empty">
            <i class="fa fa-newspaper-o fa-3x" style="margin-bottom:15px;"></i>
            <p>Todavía no hay entradas publicadas. Vuelve pronto.</p>
          </div>
        @endforelse

        @if(method_exists($posts, 'hasPages') && $posts->hasPages())
          <div class="text-center mt50">
            {!! $posts->onEachSide(1)->links() !!}
          </div>
        @endif
      </div>
    </section>

    <!-- Aside -->
    <aside class="mt50">
      <div class="col-md-3">
        <div class="widget">
          <h3>Acerca del blog</h3>
          <p>{{ $establishment->aditional_information ?? 'Novedades, consejos y noticias de '.$hotelName.'. Descubre todo lo que tenemos para tu próxima estancia.' }}</p>
        </div>
        @if($posts->count())
          <div class="widget clearfix">
            <h3>Últimas entradas</h3>
            <ul class="list-unstyled">
              @foreach($posts->take(4) as $recent)
                @php
                  $rImg  = $recent->image_url;
                  $rDate = $recent->published_at ?: $recent->created_at;
                @endphp
                <li>
                  <article>
                    <div class="news-thumb">
                      <a href="{{ url('reservas/blog/'.$recent->slug) }}" class="news-thumb-img" @if($rImg)style="background-image:url('{{ $rImg }}');"@endif></a>
                    </div>
                    <div class="news-content clearfix">
                      <h4><a href="{{ url('reservas/blog/'.$recent->slug) }}">{{ \Illuminate\Support\Str::limit($recent->title, 45) }}</a></h4>
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
