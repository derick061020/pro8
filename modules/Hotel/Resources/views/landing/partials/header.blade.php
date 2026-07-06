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
          @if($hasBranches)
          <div class="th-item th-branch dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Cambiar de sucursal">
              <i class="fa fa-map-marker"></i> {{ $currentBranchName }} <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right th-branch__menu">
              <li class="dropdown-header">Elige una sucursal</li>
              @foreach($branches as $b)
                <li class="{{ $b->id === $currentBranch ? 'active' : '' }}">
                  <a href="{{ url('/reservas') }}?sucursal={{ $b->id }}">
                    <i class="fa {{ $b->id === $currentBranch ? 'fa-check' : 'fa-building-o' }}"></i> {{ $b->description }}
                    @if(!empty($b->address))<br><small class="text-muted" style="padding-left:20px;">{{ $b->address }}</small>@endif
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
          @endif
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

<style>
  .th-branch { position: relative; }
  .th-branch > .dropdown-toggle { cursor: pointer; font-weight: 600; }
  .th-branch__menu { min-width: 230px; padding: 6px 0; }
  .th-branch__menu > li > a { padding: 8px 16px; white-space: normal; }
  .th-branch__menu > li.active > a { color: #1abc9c; font-weight: 600; }
  .th-branch__menu .dropdown-header { padding: 4px 16px; text-transform: uppercase; font-size: 11px; letter-spacing: .5px; }
</style>

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
