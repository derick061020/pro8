<!DOCTYPE HTML>
<html lang="es">
<head>
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $pubDate   = $post->published_at ?: $post->created_at;
    $heroImage = $post->image_url;
    $cfg = $settings ?? \Modules\Hotel\Models\HotelLandingSetting::mergeDefaults([]);
    $bannerImg = \Modules\Hotel\Models\HotelLandingSetting::imageUrl($cfg['parallax']['image'] ?? null, \Modules\Hotel\Models\HotelLandingSetting::DEFAULT_PARALLAX);
    $metaDesc  = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155);
@endphp
@include('hotel::landing.partials.head', ['pageTitle' => $post->title.' · '.$hotelName, 'pageDesc' => $metaDesc])

<style>
  .post-content { color:#2b303b; font-size:16px; line-height:1.8; }
  .post-content p { margin-bottom:18px; }
  .post-content img { max-width:100%; height:auto; border-radius:12px; }
  .post-content h1, .post-content h2, .post-content h3, .post-content h4 { font-family:'Inter Tight','Inter',sans-serif; color:#171717; margin:28px 0 12px; font-weight:700; }
  .post-content ul, .post-content ol { margin:0 0 18px 22px; }
  .post-content ul { list-style:disc; } .post-content ol { list-style:decimal; }
  .post-content blockquote { border-left:3px solid #5c7c68; margin:0 0 18px; padding:8px 18px; color:#525866; background:#eef2ef; border-radius:0 8px 8px 0; font-style:italic; }
  .post-content a { color:#5c7c68; text-decoration:underline; }
  .post-content table { border-collapse:collapse; width:100%; margin-bottom:18px; }
  .post-content table td, .post-content table th { border:1px solid #eaecf0; padding:9px 12px; }
  .post-content figure { max-width:100%; }
  .post-content figure.media { position:relative; padding-top:56.25%; }
  .post-content figure.media iframe { position:absolute; inset:0; width:100%; height:100%; }
</style>
</head>

<body>

@include('hotel::landing.partials.header', ['onLanding' => false, 'activeNav' => 'blog'])

<!-- Cabecera -->
<section class="relative bg-cover bg-center" style="background-image:url('{{ $bannerImg }}');">
  <div class="bg-ink-950/72">
    <div class="max-w-4xl mx-auto px-6 pt-28 pb-16 text-white">
      <nav class="flex items-center gap-2 text-[13px] text-white/70 mb-4">
        <a href="/reservas" class="hover:text-white">Inicio</a>
        <i class="fa fa-angle-right text-[11px]"></i>
        <a href="/reservas/blog" class="hover:text-white">Blog</a>
      </nav>
      <div class="flex flex-wrap items-center gap-4 text-[13px] text-white/80 mb-3">
        @if($pubDate)<span class="inline-flex items-center gap-1.5"><i class="fa fa-calendar text-brand"></i> {{ $pubDate->format('d/m/Y') }}</span>@endif
        @if($post->author)<span class="inline-flex items-center gap-1.5"><i class="fa fa-user text-brand"></i> {{ $post->author }}</span>@endif
      </div>
      <h1 class="font-display text-3xl md:text-4xl font-bold leading-tight">{{ $post->title }}</h1>
    </div>
  </div>
</section>

<div class="max-w-6xl mx-auto px-6 py-14">
  <div class="grid gap-12 lg:grid-cols-3">
    <!-- Contenido -->
    <article class="lg:col-span-2">
      @if($post->hasVideoCover() && $post->video_embed_url)
        <div class="relative w-full rounded-2xl overflow-hidden bg-black mb-8" style="padding-top:56.25%;">
          <iframe src="{{ $post->video_embed_url }}" class="absolute inset-0 w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      @elseif($heroImage)
        <img src="{{ $heroImage }}" alt="{{ $post->title }}" class="w-full rounded-2xl mb-8 object-cover">
      @endif

      @if($post->excerpt)
        <p class="text-xl text-ink-700 font-medium leading-relaxed mb-6">{{ $post->excerpt }}</p>
      @endif

      <div class="post-content">
        @php $isHtml = $post->content && $post->content !== strip_tags($post->content); @endphp
        @if($isHtml)
          {!! $post->content !!}
        @else
          {!! nl2br(e($post->content)) !!}
        @endif
      </div>

      <a href="/reservas/blog" class="hb-btn hb-btn-ghost mt-8"><i class="fa fa-angle-left"></i> Volver al blog</a>
    </article>

    <!-- Aside -->
    <aside class="space-y-6">
      @if($recent->count())
        <div class="hb-card p-6">
          <h3 class="font-display font-semibold text-lg text-ink-900 mb-4">Entradas recientes</h3>
          <ul class="space-y-4">
            @foreach($recent as $r)
              @php
                $rImg  = $r->cover_image;
                $rDate = $r->published_at ?: $r->created_at;
              @endphp
              <li>
                <a href="{{ url('reservas/blog/'.$r->slug) }}" class="flex gap-3 group">
                  <span class="w-16 h-16 rounded-lg bg-ink-100 bg-center bg-cover shrink-0" @if($rImg)style="background-image:url('{{ $rImg }}');"@endif></span>
                  <span class="min-w-0">
                    <span class="block text-[14px] font-medium text-ink-800 leading-snug group-hover:text-brand transition">{{ \Illuminate\Support\Str::limit($r->title, 50) }}</span>
                    <span class="block text-[12px] text-ink-400 mt-1">{{ $rDate ? $rDate->format('d/m/Y') : '' }}</span>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="hb-card p-6 bg-brand-tint border-brand-soft">
        <h3 class="font-display font-semibold text-lg text-ink-900 mb-2">Reserva tu estancia</h3>
        <p class="text-[14px] text-ink-600 mb-4">Disponibilidad y precios en tiempo real.</p>
        <a href="{{ url('/reservas') }}#reservation-form" class="hb-btn hb-btn-primary hb-btn-block">Reservar ahora</a>
      </div>
    </aside>
  </div>
</div>

@include('hotel::landing.partials.footer', ['onLanding' => false])

</body>
</html>
