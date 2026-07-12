@php
    // Header flotante (píldora) — misma línea gráfica que la web pública de
    // launchbase/makai: barra blanca centrada, esquinas redondeadas y sombra en
    // capas, fija sobre el hero.
    // $onLanding = true  -> enlaces de ancla con scroll suave (misma página)
    // $onLanding = false -> enlaces absolutos hacia /reservas#ancla
    $onLanding   = $onLanding   ?? false;
    $activeNav   = $activeNav   ?? 'inicio';
    $hotelName   = $establishment->description ?? 'Hotel';
    $hotelPhone  = $establishment->telephone ?? null;
    $hotelLogo   = (!empty($establishment->logo)) ? asset('storage/uploads/logos/'.$establishment->logo) : null;
    $base        = $onLanding ? '' : url('/reservas');
    $scroll      = $onLanding ? 'nav-scroll' : '';
    $branches       = $establishments ?? collect();
    $currentBranch  = $establishmentId ?? ($establishment->id ?? null);
    $currentBranchName = optional($branches->firstWhere('id', $currentBranch))->description ?? $hotelName;
    $hasBranches    = $branches instanceof \Illuminate\Support\Collection ? $branches->count() > 1 : false;

    $navLinks = [
        ['label' => 'Inicio',       'href' => $onLanding ? '#top' : $base,  'active' => $activeNav === 'inicio'],
        ['label' => 'Habitaciones', 'href' => $base.'#rooms-results',       'active' => false],
        ['label' => 'Galería',      'href' => $base.'#gallery',             'active' => false],
        ['label' => 'Blog',         'href' => url('/reservas/blog'),        'active' => $activeNav === 'blog'],
        ['label' => 'Contacto',     'href' => $base.'#contacto',            'active' => false],
    ];
@endphp

<a id="top"></a>

<!-- Navbar flotante -->
<div class="fixed top-0 left-0 right-0 z-[100] px-3 sm:px-6 pt-3 sm:pt-4 pb-2 flex justify-center pointer-events-none" id="siteHeader">
  <div class="relative pointer-events-auto w-full max-w-6xl flex items-center justify-between gap-3 h-[60px] pl-3 pr-2 bg-white rounded-full shadow-float transition-all duration-300 ease-makai" id="navPill">

    <!-- Izquierda: logo + nombre + selector de sucursal -->
    <div class="flex items-center gap-2.5 min-w-0 shrink-0">
      <a href="{{ url('/reservas') }}" class="flex items-center gap-2.5 min-w-0">
        @if($hotelLogo)
          <img src="{{ $hotelLogo }}" alt="{{ $hotelName }}" class="h-8 w-auto max-w-[130px] object-contain">
        @else
          <span class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-display font-bold text-base shrink-0">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
          <span class="font-display text-[15px] font-bold text-ink-900 truncate max-w-[38vw] lg:max-w-[220px]">{{ $hotelName }}</span>
        @endif
      </a>
      @if($hasBranches)
      <div class="relative shrink-0" id="branchDropdown">
        <button type="button" class="w-7 h-7 rounded-full hover:bg-ink-100 text-ink-400 flex items-center justify-center transition" data-branch-toggle aria-label="Cambiar de sucursal">
          <i class="fa fa-angle-down"></i>
        </button>
        <div class="absolute left-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-hover border border-ink-200 py-2 hidden z-[120]" data-branch-menu>
          <div class="px-4 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-ink-400">Elige una sucursal</div>
          @foreach($branches as $b)
            @php $branchAddress = $b->trade_address ?: $b->address; @endphp
            <a href="{{ url('/reservas') }}?sucursal={{ $b->id }}" class="flex items-start gap-2.5 px-4 py-2.5 hover:bg-ink-50 transition {{ $b->id === $currentBranch ? 'bg-brand-tint' : '' }}">
              <i class="fa {{ $b->id === $currentBranch ? 'fa-check text-brand' : 'fa-building-o text-ink-400' }} mt-0.5 w-4 text-center"></i>
              <span class="min-w-0">
                <span class="block text-[13px] font-medium {{ $b->id === $currentBranch ? 'text-brand-dark' : 'text-ink-900' }}">{{ $b->description }}</span>
                @if(!empty($branchAddress))<span class="block text-[11px] text-ink-400 leading-snug">{{ $branchAddress }}</span>@endif
              </span>
            </a>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    <!-- Centro: navegación (desktop) -->
    <nav class="hidden lg:flex items-center gap-1 absolute left-1/2 -translate-x-1/2">
      @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}" class="{{ $scroll }} px-3.5 py-2 rounded-full text-[13.5px] font-medium transition {{ $link['active'] ? 'text-brand-dark bg-brand-tint' : 'text-ink-600 hover:text-ink-900 hover:bg-ink-100' }}">{{ $link['label'] }}</a>
      @endforeach
    </nav>

    <!-- Derecha: teléfono + reservar + hamburguesa -->
    <div class="flex items-center gap-2 shrink-0">
      @if($hotelPhone)
        <a href="tel:{{ $hotelPhone }}" class="hidden xl:inline-flex items-center gap-2 pr-1 text-[13px] font-medium text-ink-600 hover:text-brand transition"><i class="fa fa-phone text-brand"></i> {{ $hotelPhone }}</a>
      @endif
      <a href="{{ $base }}#reservation-form" class="{{ $scroll }} hb-btn hb-btn-primary h-[44px]"><i class="fa fa-calendar-check-o"></i> <span class="hidden sm:inline">Reservar</span></a>
      <button type="button" class="lg:hidden w-11 h-11 rounded-full hover:bg-ink-100 text-ink-700 flex items-center justify-center transition" data-menu-toggle aria-label="Menú">
        <i class="fa fa-bars"></i>
      </button>
    </div>
  </div>

  <!-- Menú móvil (tarjeta flotante) -->
  <div class="pointer-events-auto lg:hidden hidden absolute top-[76px] left-3 right-3 sm:left-6 sm:right-6 bg-white rounded-2xl shadow-hover border border-ink-100 p-2" data-mobile-menu>
    <nav class="flex flex-col">
      @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}" class="{{ $scroll }} px-4 py-3 rounded-xl text-[15px] font-medium {{ $link['active'] ? 'text-brand-dark bg-brand-tint' : 'text-ink-700 hover:bg-ink-50' }}">{{ $link['label'] }}</a>
      @endforeach
      @if($hotelPhone)<a href="tel:{{ $hotelPhone }}" class="px-4 py-3 rounded-xl text-[15px] font-medium text-ink-700 hover:bg-ink-50"><i class="fa fa-phone text-brand mr-1"></i> {{ $hotelPhone }}</a>@endif
    </nav>
  </div>
</div>

<script>
(function () {
  var pill = document.getElementById('navPill');
  var onScroll = function () {
    if (!pill) return;
    // Sutil "encogido" al bajar, como en makai.
    if (window.scrollY > 12) { pill.style.height = '54px'; pill.classList.add('shadow-hover'); }
    else { pill.style.height = ''; pill.classList.remove('shadow-hover'); }
  };
  window.addEventListener('scroll', onScroll); onScroll();

  // Menú móvil.
  var mToggle = document.querySelector('[data-menu-toggle]');
  var mMenu   = document.querySelector('[data-mobile-menu]');
  if (mToggle && mMenu) {
    mToggle.addEventListener('click', function (e) { e.stopPropagation(); mMenu.classList.toggle('hidden'); });
    mMenu.addEventListener('click', function (e) { if (e.target.tagName === 'A') mMenu.classList.add('hidden'); });
    document.addEventListener('click', function () { mMenu.classList.add('hidden'); });
  }

  // Selector de sucursal.
  var bWrap = document.getElementById('branchDropdown');
  if (bWrap) {
    var bToggle = bWrap.querySelector('[data-branch-toggle]');
    var bMenu   = bWrap.querySelector('[data-branch-menu]');
    bToggle.addEventListener('click', function (e) { e.stopPropagation(); bMenu.classList.toggle('hidden'); });
    document.addEventListener('click', function () { bMenu.classList.add('hidden'); });
  }

  // Scroll suave para anclas internas (offset por la barra flotante).
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a.nav-scroll');
    if (!a) return;
    var href = a.getAttribute('href') || '';
    var hash = href.charAt(0) === '#' ? href : (href.indexOf('#') > -1 ? href.slice(href.indexOf('#')) : '');
    if (hash && hash.length > 1 && document.querySelector(hash)) {
      e.preventDefault();
      var top = document.querySelector(hash).getBoundingClientRect().top + window.scrollY - 90;
      window.scrollTo({ top: top, behavior: 'smooth' });
    }
  });
})();
</script>
