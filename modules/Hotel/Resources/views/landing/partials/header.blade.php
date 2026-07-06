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
@endphp

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
          <li class="{{ $activeNav === 'inicio' ? 'active' : '' }}"><a href="{{ $onLanding ? '#top' : $base }}" class="{{ $scroll }}">Inicio</a></li>
          <li><a href="{{ $base }}#rooms-results" class="{{ $scroll }}">Habitaciones</a></li>
          <li><a href="{{ $base }}#gallery" class="{{ $scroll }}">Galería</a></li>
          <li class="{{ $activeNav === 'blog' ? 'active' : '' }}"><a href="{{ url('/reservas/blog') }}">Blog</a></li>
          <li><a href="{{ $base }}#contacto" class="{{ $scroll }}">Contacto</a></li>
          <li><a href="{{ $base }}#reservation-form" class="{{ $scroll }} text-uppercase" style="color:#1abc9c;font-weight:600;">Reservar</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>
