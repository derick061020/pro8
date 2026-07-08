@php
    // Footer compartido. $onLanding decide el destino del botón "Ver disponibilidad".
    $onLanding    = $onLanding ?? false;
    $hotelName    = $establishment->description ?? 'Hotel';
    $hotelPhone   = $establishment->telephone ?? null;
    $hotelEmail   = $establishment->email ?? null;
    $hotelWeb     = $establishment->web_address ?? null;
    // Dirección comercial del establecimiento (con fallback a la dirección fiscal).
    $hotelAddress = ($establishment->trade_address ?? null) ?: ($establishment->address ?? null);
    $reserveHref  = $onLanding ? '#reservation-form' : url('/reservas').'#reservation-form';
    $reserveClass = $onLanding ? 'nav-scroll' : '';
@endphp

<!-- Contacto / Footer -->
<footer id="contacto" class="mt100">
  <div class="container">
    <div class="row">
      <div class="col-md-4 col-sm-4">
        <h4>{{ $hotelName }}</h4>
        <p>{{ $establishment->aditional_information ?? 'Te damos la bienvenida. Reserva tu estancia con nosotros y disfruta de una experiencia inolvidable.' }}</p>
      </div>
      <div class="col-md-4 col-sm-4">
        <h4>Contacto</h4>
        <address>
          @if($hotelAddress)<i class="fa fa-map-marker"></i> {{ $hotelAddress }}<br>@endif
          @if($hotelPhone)<i class="fa fa-phone"></i> <a href="tel:{{ $hotelPhone }}">{{ $hotelPhone }}</a><br>@endif
          @if($hotelEmail)<i class="fa fa-envelope"></i> <a href="mailto:{{ $hotelEmail }}">{{ $hotelEmail }}</a><br>@endif
          @if($hotelWeb)<i class="fa fa-globe"></i> <a href="{{ $hotelWeb }}" target="_blank">{{ $hotelWeb }}</a>@endif
        </address>
      </div>
      <div class="col-md-4 col-sm-4">
        <h4>Reserva ahora</h4>
        <p>Consulta disponibilidad en tiempo real y asegura tu habitación.</p>
        <a href="{{ $reserveHref }}" class="btn btn-primary btn-lg {{ $reserveClass }}">Ver disponibilidad</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 text-center"> &copy; {{ date('Y') }} {{ $hotelName }}. Todos los derechos reservados. </div>
      </div>
    </div>
  </div>
</footer>

<div id="go-top"><i class="fa fa-angle-up fa-2x"></i></div>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HPFJJYLZ4Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-HPFJJYLZ4Z');
</script>
