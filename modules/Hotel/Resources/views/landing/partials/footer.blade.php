@php
    // Footer compartido. $onLanding decide el destino del botón "Ver disponibilidad".
    $onLanding    = $onLanding ?? false;
    $hotelName    = $establishment->description ?? 'Hotel';
    $hotelPhone   = $establishment->telephone ?? null;
    $hotelEmail   = $establishment->email ?? null;
    $hotelWeb     = $establishment->web_address ?? null;
    $hotelAddress = ($establishment->trade_address ?? null) ?: ($establishment->address ?? null);
    $reserveHref  = $onLanding ? '#reservation-form' : url('/reservas').'#reservation-form';
    $reserveClass = $onLanding ? 'nav-scroll' : '';
    $footerAbout  = $establishment->aditional_information ?? 'Te damos la bienvenida. Reserva tu estancia con nosotros y disfruta de una experiencia inolvidable.';
@endphp

<footer id="contacto" class="bg-ink-950 text-ink-300 mt-24">
  <div class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid gap-10 md:grid-cols-12">
      <div class="md:col-span-5">
        <div class="flex items-center gap-3 mb-4">
          <span class="w-9 h-9 rounded-xl bg-brand text-white flex items-center justify-center font-display font-bold text-lg">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
          <span class="font-display text-lg font-bold text-white">{{ $hotelName }}</span>
        </div>
        <p class="text-[14px] leading-relaxed text-ink-400 max-w-md">{{ $footerAbout }}</p>
      </div>

      <div class="md:col-span-4">
        <h4 class="font-display text-white font-semibold mb-4">Contacto</h4>
        <ul class="space-y-3 text-[14px]">
          @if($hotelAddress)<li class="flex items-start gap-3"><i class="fa fa-map-marker text-brand mt-1 w-4 text-center"></i><span>{{ $hotelAddress }}</span></li>@endif
          @if($hotelPhone)<li class="flex items-center gap-3"><i class="fa fa-phone text-brand w-4 text-center"></i><a href="tel:{{ $hotelPhone }}" class="hover:text-white transition">{{ $hotelPhone }}</a></li>@endif
          @if($hotelEmail)<li class="flex items-center gap-3"><i class="fa fa-envelope text-brand w-4 text-center"></i><a href="mailto:{{ $hotelEmail }}" class="hover:text-white transition">{{ $hotelEmail }}</a></li>@endif
          @if($hotelWeb)<li class="flex items-center gap-3"><i class="fa fa-globe text-brand w-4 text-center"></i><a href="{{ $hotelWeb }}" target="_blank" class="hover:text-white transition">{{ $hotelWeb }}</a></li>@endif
        </ul>
      </div>

      <div class="md:col-span-3">
        <h4 class="font-display text-white font-semibold mb-4">Reserva ahora</h4>
        <p class="text-[14px] text-ink-400 mb-5">Consulta disponibilidad en tiempo real y asegura tu habitación.</p>
        {{-- Botón secundario (outline sobre el footer oscuro): evita duplicar el
             mismo botón verde sólido de la banda CTA que va justo encima. --}}
        <a href="{{ $reserveHref }}" class="hb-btn hb-btn-light hb-btn-block {{ $reserveClass }}"><i class="fa fa-calendar-check-o"></i> Ver disponibilidad</a>
      </div>
    </div>
  </div>

  <div class="border-t border-white/10">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-center text-[13px] text-ink-500 text-center">
      &copy; {{ date('Y') }} {{ $hotelName }}. Todos los derechos reservados.
    </div>
  </div>
</footer>

<button id="go-top" type="button" class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-xl bg-brand text-white shadow-hover opacity-0 pointer-events-none transition hover:bg-brand-dark" aria-label="Ir arriba">
  <i class="fa fa-angle-up text-lg"></i>
</button>

<script>
(function () {
  var goTop = document.getElementById('go-top');
  if (goTop) {
    var toggle = function () {
      var show = window.scrollY > 500;
      goTop.style.opacity = show ? '1' : '0';
      goTop.style.pointerEvents = show ? 'auto' : 'none';
    };
    window.addEventListener('scroll', toggle); toggle();
    goTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }
})();
</script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-HPFJJYLZ4Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-HPFJJYLZ4Z');
</script>
