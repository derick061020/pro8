@php
    // Header compartido entre la landing (index) y las páginas del blog.
    // $onLanding = true  -> enlaces de ancla con scroll suave (misma página)
    // $onLanding = false -> enlaces absolutos hacia /reservas#ancla
    $onLanding   = $onLanding   ?? false;
    $activeNav   = $activeNav   ?? 'inicio';
    $hotelName   = $establishment->description ?? 'Hotel';
    $hotelPhone  = $establishment->telephone ?? null;
    $hotelEmail  = $establishment->email ?? null;
    $hotelWeb    = $establishment->web_address ?? null;
    $hotelLogo   = (!empty($establishment->logo)) ? asset('storage/uploads/logos/'.$establishment->logo) : null;
    $base        = $onLanding ? '' : url('/reservas');
    $scroll      = $onLanding ? 'nav-scroll' : '';
    $branches       = $establishments ?? collect();
    $currentBranch  = $establishmentId ?? ($establishment->id ?? null);
    $currentBranchName = optional($branches->firstWhere('id', $currentBranch))->description ?? $hotelName;
    $hasBranches    = $branches instanceof \Illuminate\Support\Collection ? $branches->count() > 1 : false;

    $navLinks = [
        ['label' => 'Inicio',       'href' => $onLanding ? '#top' : $base,          'active' => $activeNav === 'inicio'],
        ['label' => 'Habitaciones', 'href' => $base.'#rooms-results',               'active' => false],
        ['label' => 'Galería',      'href' => $base.'#gallery',                     'active' => false],
        ['label' => 'Blog',         'href' => url('/reservas/blog'),                'active' => $activeNav === 'blog'],
        ['label' => 'Contacto',     'href' => $base.'#contacto',                    'active' => false],
    ];
@endphp

<a id="top"></a>

<!-- Barra de contacto superior -->
<div class="hidden md:block bg-ink-950 text-ink-300 text-[12.5px]">
  <div class="max-w-7xl mx-auto px-6 h-10 flex items-center justify-between">
    <div class="flex items-center gap-5">
      @if($hotelPhone)<a href="tel:{{ $hotelPhone }}" class="inline-flex items-center gap-2 hover:text-white transition"><i class="fa fa-phone text-brand"></i> {{ $hotelPhone }}</a>@endif
      @if($hotelEmail)<a href="mailto:{{ $hotelEmail }}" class="inline-flex items-center gap-2 hover:text-white transition"><i class="fa fa-envelope text-brand"></i> {{ $hotelEmail }}</a>@endif
    </div>
    <div class="flex items-center gap-4">
      @if($hasBranches)
      <div class="relative" id="branchDropdown">
        <button type="button" class="inline-flex items-center gap-2 hover:text-white transition font-medium" data-branch-toggle>
          <i class="fa fa-map-marker text-brand"></i> {{ $currentBranchName }} <i class="fa fa-angle-down text-[10px]"></i>
        </button>
        <div class="absolute right-0 top-full mt-2 w-72 bg-white rounded-xl shadow-hover border border-ink-200 py-2 hidden z-[60]" data-branch-menu>
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
      @if($hotelWeb)<a href="{{ $hotelWeb }}" target="_blank" class="hover:text-white transition" title="Sitio web"><i class="fa fa-globe"></i></a>@endif
    </div>
  </div>
</div>

<!-- Navbar principal -->
<header id="siteHeader" class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-ink-200 transition-shadow">
  <div class="max-w-7xl mx-auto px-6">
    <div class="h-[68px] flex items-center justify-between gap-6">
      <a href="{{ url('/reservas') }}" class="flex items-center gap-3 shrink-0">
        @if($hotelLogo)
          <img src="{{ $hotelLogo }}" alt="{{ $hotelName }}" class="h-9 w-auto">
        @else
          <span class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-display font-bold text-lg">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
        @endif
        <span class="font-display text-lg font-bold text-ink-900 truncate max-w-[42vw] md:max-w-none">{{ $hotelName }}</span>
      </a>

      <nav class="hidden lg:flex items-center gap-1">
        @foreach($navLinks as $link)
          <a href="{{ $link['href'] }}" class="{{ $scroll }} px-3.5 py-2 rounded-lg text-[14px] font-medium transition {{ $link['active'] ? 'text-brand-dark' : 'text-ink-600 hover:text-ink-900 hover:bg-ink-50' }}">{{ $link['label'] }}</a>
        @endforeach
      </nav>

      <div class="flex items-center gap-2">
        <a href="{{ $base }}#reservation-form" class="{{ $scroll }} hb-btn hb-btn-primary hidden sm:inline-flex"><i class="fa fa-calendar-check-o"></i> Reservar</a>
        <button type="button" class="lg:hidden w-11 h-11 rounded-xl border border-ink-200 text-ink-600 flex items-center justify-center" data-menu-toggle aria-label="Menú">
          <i class="fa fa-bars"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Menú móvil -->
  <div class="lg:hidden hidden border-t border-ink-100 bg-white" data-mobile-menu>
    <nav class="max-w-7xl mx-auto px-6 py-3 flex flex-col">
      @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}" class="{{ $scroll }} py-2.5 text-[15px] font-medium border-b border-ink-100 last:border-0 {{ $link['active'] ? 'text-brand-dark' : 'text-ink-700' }}">{{ $link['label'] }}</a>
      @endforeach
      <a href="{{ $base }}#reservation-form" class="{{ $scroll }} hb-btn hb-btn-primary mt-3"><i class="fa fa-calendar-check-o"></i> Reservar ahora</a>
    </nav>
  </div>
</header>

<script>
(function () {
  // Sombra del header al hacer scroll.
  var header = document.getElementById('siteHeader');
  var onScroll = function () {
    if (!header) return;
    header.classList.toggle('shadow-panel', window.scrollY > 8);
  };
  window.addEventListener('scroll', onScroll); onScroll();

  // Menú móvil.
  var mToggle = document.querySelector('[data-menu-toggle]');
  var mMenu   = document.querySelector('[data-mobile-menu]');
  if (mToggle && mMenu) {
    mToggle.addEventListener('click', function () { mMenu.classList.toggle('hidden'); });
    mMenu.addEventListener('click', function (e) { if (e.target.tagName === 'A') mMenu.classList.add('hidden'); });
  }

  // Selector de sucursal.
  var bWrap = document.getElementById('branchDropdown');
  if (bWrap) {
    var bToggle = bWrap.querySelector('[data-branch-toggle]');
    var bMenu   = bWrap.querySelector('[data-branch-menu]');
    bToggle.addEventListener('click', function (e) { e.stopPropagation(); bMenu.classList.toggle('hidden'); });
    document.addEventListener('click', function () { bMenu.classList.add('hidden'); });
  }

  // Scroll suave para anclas internas.
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a.nav-scroll');
    if (!a) return;
    var href = a.getAttribute('href') || '';
    var hash = href.charAt(0) === '#' ? href : (href.indexOf('#') > -1 ? href.slice(href.indexOf('#')) : '');
    if (hash && hash.length > 1 && document.querySelector(hash)) {
      e.preventDefault();
      var top = document.querySelector(hash).getBoundingClientRect().top + window.scrollY - 76;
      window.scrollTo({ top: top, behavior: 'smooth' });
    }
  });
})();
</script>
