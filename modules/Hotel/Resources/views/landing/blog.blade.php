<!DOCTYPE HTML>
<html lang="es">
<head>
@php
    $hotelName = $establishment->description ?? 'Hotel';
    $cfg = $settings ?? \Modules\Hotel\Models\HotelLandingSetting::mergeDefaults([]);
    $heroImg = \Modules\Hotel\Models\HotelLandingSetting::imageUrl($cfg['parallax']['image'] ?? null, \Modules\Hotel\Models\HotelLandingSetting::DEFAULT_PARALLAX);
@endphp
@include('hotel::landing.partials.head', ['pageTitle' => 'Blog · '.$hotelName, 'pageDesc' => 'Novedades, consejos y noticias de '.$hotelName])
</head>

<body>

@include('hotel::landing.partials.header', ['onLanding' => false, 'activeNav' => 'blog'])

<!-- Cabecera -->
<section class="relative bg-cover bg-center" style="background-image:url('{{ $heroImg }}');">
  <div class="bg-ink-950/72">
    <div class="max-w-7xl mx-auto px-6 pt-28 pb-16 text-white">
      <nav class="flex items-center gap-2 text-[13px] text-white/70 mb-3">
        <a href="/reservas" class="hover:text-white">Inicio</a>
        <i class="fa fa-angle-right text-[11px]"></i>
        <span class="text-white">Blog</span>
      </nav>
      <h1 class="font-display text-4xl md:text-5xl font-bold">Nuestro blog</h1>
    </div>
  </div>
</section>

<div class="max-w-7xl mx-auto px-6 py-16">
  <div class="grid gap-10 lg:grid-cols-3">
    <!-- Listado -->
    <div class="lg:col-span-2 space-y-8">
      @forelse($posts as $post)
        @php
          $postImage = $post->cover_image;
          $pubDate   = $post->published_at ?: $post->created_at;
          $excerpt   = $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 220);
        @endphp
        <article class="hb-card overflow-hidden group transition hover:shadow-panel">
          @if($postImage)
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="relative block aspect-[16/8] bg-ink-100 overflow-hidden">
              <span class="absolute inset-0 bg-center bg-cover transition duration-500 group-hover:scale-105" style="background-image:url('{{ $postImage }}');"></span>
              @if($post->hasVideoCover())<i class="fa fa-play-circle absolute inset-0 m-auto w-max h-max text-white text-6xl drop-shadow-lg"></i>@endif
            </a>
          @endif
          <div class="p-7">
            <div class="flex flex-wrap items-center gap-4 text-[12.5px] text-ink-500 mb-3">
              @if($pubDate)<span class="inline-flex items-center gap-1.5"><i class="fa fa-calendar text-brand"></i> {{ $pubDate->format('d/m/Y') }}</span>@endif
              @if($post->author)<span class="inline-flex items-center gap-1.5"><i class="fa fa-user text-brand"></i> {{ $post->author }}</span>@endif
            </div>
            <h2 class="font-display text-2xl font-bold text-ink-900 leading-snug mb-3"><a href="{{ url('reservas/blog/'.$post->slug) }}" class="hover:text-brand transition">{{ $post->title }}</a></h2>
            <p class="text-[15px] text-ink-500 leading-relaxed mb-5">{{ $excerpt }}</p>
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="hb-btn hb-btn-ghost">Leer más <i class="fa fa-angle-right"></i></a>
          </div>
        </article>
      @empty
        <div class="hb-card text-center py-20 text-ink-400">
          <i class="fa fa-newspaper-o fa-3x mb-4"></i>
          <p class="text-[15px]">Todavía no hay entradas publicadas. Vuelve pronto.</p>
        </div>
      @endforelse

      @if(method_exists($posts, 'hasPages') && $posts->hasPages())
        <div class="pt-4">{!! $posts->onEachSide(1)->links() !!}</div>
      @endif
    </div>

    <!-- Aside -->
    <aside class="space-y-6">
      <div class="hb-card p-6">
        <h3 class="font-display font-semibold text-lg text-ink-900 mb-3">Acerca del blog</h3>
        <p class="text-[14px] text-ink-500 leading-relaxed">{{ $establishment->aditional_information ?? 'Novedades, consejos y noticias de '.$hotelName.'. Descubre todo lo que tenemos para tu próxima estancia.' }}</p>
      </div>

      @if($posts->count())
        <div class="hb-card p-6">
          <h3 class="font-display font-semibold text-lg text-ink-900 mb-4">Últimas entradas</h3>
          <ul class="space-y-4">
            @foreach($posts->take(4) as $recent)
              @php
                $rImg  = $recent->cover_image;
                $rDate = $recent->published_at ?: $recent->created_at;
              @endphp
              <li>
                <a href="{{ url('reservas/blog/'.$recent->slug) }}" class="flex gap-3 group">
                  <span class="relative w-16 h-16 rounded-lg bg-ink-100 bg-center bg-cover shrink-0" @if($rImg)style="background-image:url('{{ $rImg }}');"@endif>
                    @if($recent->hasVideoCover())<i class="fa fa-play-circle absolute inset-0 m-auto w-max h-max text-white text-lg drop-shadow"></i>@endif
                  </span>
                  <span class="min-w-0">
                    <span class="block text-[14px] font-medium text-ink-800 leading-snug group-hover:text-brand transition">{{ \Illuminate\Support\Str::limit($recent->title, 50) }}</span>
                    <span class="block text-[12px] text-ink-400 mt-1">{{ $rDate ? $rDate->format('d/m/Y') : '' }}</span>
                  </span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="hb-card p-6 bg-brand-tint border-brand-soft">
        <h3 class="font-display font-semibold text-lg text-ink-900 mb-2">¿Listo para tu estancia?</h3>
        <p class="text-[14px] text-ink-600 mb-4">Consulta disponibilidad en tiempo real.</p>
        <a href="{{ url('/reservas') }}#reservation-form" class="hb-btn hb-btn-primary hb-btn-block">Reservar ahora</a>
      </div>
    </aside>
  </div>
</div>

@include('hotel::landing.partials.footer', ['onLanding' => false])

</body>
</html>
