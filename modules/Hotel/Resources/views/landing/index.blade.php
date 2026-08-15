<!DOCTYPE HTML>
<html lang="es">
<head>
@php
    use Modules\Hotel\Models\HotelLandingSetting;

    $hotelName = $establishment->description ?? 'Hotel';
    // Configuración de personalización de la web (con valores por defecto).
    $cfg = $settings ?? HotelLandingSetting::mergeDefaults([]);
@endphp
@include('hotel::landing.partials.head', ['pageTitle' => $hotelName.' · Reservas online', 'pageDesc' => 'Reserva tu habitación en '.$hotelName.'. Disponibilidad y precios en tiempo real.'])

<style>
  /* ===== Hero (patrón home Makai: intro imagen+texto + slider horizontal) ===== */
  .hr-stage { position:relative; background:#12140f; height:min(88vh, 880px); min-height:600px; overflow:hidden; }
  .hr-track { display:flex; height:100%; transform:translateX(0); transition:transform .85s cubic-bezier(.22,1,.36,1); will-change:transform; }
  .hr-cell { position:relative; flex:0 0 100%; width:100%; height:100%; overflow:hidden; }
  .hr-photo { position:absolute; inset:0; background-size:cover; background-position:center; transform:scale(1.08); transition:transform 7.5s ease; }
  .hr-cell.is-active .hr-photo { transform:scale(1); }
  /* Capa sobre la foto: SOLO lo justo para que se lea el texto.
     Antes oscurecía la imagen entera (hasta un 82% de negro abajo, más una
     viñeta del 34%), así que la foto subida se veía mucho más apagada que el
     original. Ahora el centro queda limpio —la imagen se ve con sus colores—
     y el oscurecido se limita a las dos franjas donde hay contenido: arriba
     el menú flotante y abajo el fundido hacia la página. */
  .hr-overlay { position:absolute; inset:0; z-index:1; background:
      linear-gradient(180deg, rgba(18,20,15,.30) 0%, rgba(18,20,15,.06) 22%, rgba(18,20,15,0) 45%, rgba(18,20,15,.18) 72%, rgba(18,20,15,.55) 100%); }
  .hr-cell-caption { position:absolute; inset:0; z-index:3; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; color:#fff; padding:96px 22px 150px; }
  /* Sombra suave detrás del bloque de texto: sustituye al oscurecido general.
     Va pegada al título, así la foto se mantiene limpia alrededor. */
  .hr-cell--img .hr-cell-caption::before { content:""; position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); width:min(1100px,92%); height:min(420px,60%); z-index:-1; pointer-events:none;
      background:radial-gradient(closest-side ellipse, rgba(18,20,15,.42) 0%, rgba(18,20,15,.24) 55%, rgba(18,20,15,0) 100%); }
  .hr-caption-inner { width:100%; max-width:840px; position:relative; }

  /* Chip / título / subtítulo (compartidos por las celdas de imagen). */
  .hr-chip { display:inline-flex; align-items:center; gap:9px; padding:7px 15px; margin-bottom:22px; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.24); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); font-size:12.5px; font-weight:600; letter-spacing:.03em; color:#fff; }
  .hr-chip .stars { color:#f4c542; letter-spacing:1.5px; font-size:11px; }
  /* Al aligerar la capa oscura, la legibilidad la sostiene la sombra del propio
     texto (no oscurece la foto alrededor). */
  .hr-title { font-family:'Inter Tight','Inter',sans-serif; font-size:clamp(33px,5.6vw,62px); font-weight:700; line-height:1.06; letter-spacing:-.025em; margin:0 auto 18px; max-width:14ch; text-shadow:0 2px 6px rgba(0,0,0,.45), 0 6px 34px rgba(0,0,0,.5); }
  .hr-subtitle { font-size:clamp(15px,2vw,20px); font-weight:400; max-width:600px; margin:0 auto; color:rgba(255,255,255,.95); text-shadow:0 1px 4px rgba(0,0,0,.5), 0 3px 20px rgba(0,0,0,.45); line-height:1.55; }
  /* Botón de la diapositiva (texto y enlace configurables desde el panel). */
  .hr-cta { margin-top:28px; }
  .hr-cta .hb-btn { box-shadow:0 12px 30px -14px rgba(0,0,0,.6); }

  /* Wordmark gigante de la celda intro ("imagen con texto" makai). */
  .hr-wordmark { display:block; font-family:'Inter Tight','Inter',sans-serif; font-weight:800; text-transform:uppercase; font-size:clamp(42px,10.5vw,138px); line-height:.9; letter-spacing:-.01em; max-width:13ch; margin:0 auto 8px; text-wrap:balance;
    background:linear-gradient(180deg, rgba(255,255,255,.97) 32%, rgba(255,255,255,.55) 100%); -webkit-background-clip:text; background-clip:text; color:transparent; -webkit-text-fill-color:transparent;
    filter:drop-shadow(0 8px 34px rgba(0,0,0,.4)); }

  /* Entrada: el wordmark cae desde arriba; el resto sube desde abajo (staggered). */
  .hr-anim-down { opacity:0; transform:translateY(-135%); }
  .hr-anim-up   { opacity:0; transform:translateY(38px); }
  #hero.is-revealed .hr-cell--hero .hr-anim-down { animation:hrDrop 1.5s cubic-bezier(.22,1,.36,1) .12s both; }
  #hero.is-revealed .hr-cell--hero .hr-anim-up.d1 { animation:hrRise 1.1s cubic-bezier(.22,1,.36,1) .28s both; }
  #hero.is-revealed .hr-cell--hero .hr-anim-up.d2 { animation:hrRise 1.1s cubic-bezier(.22,1,.36,1) .5s both; }
  @keyframes hrDrop { 0% { opacity:0; transform:translateY(-135%); } 58% { opacity:1; } 100% { opacity:1; transform:translateY(0); } }
  @keyframes hrRise { 0% { opacity:0; transform:translateY(38px); } 100% { opacity:1; transform:translateY(0); } }

  /* Celdas de imagen (slides): su caption aparece al activarse. */
  .hr-cell--img .hr-caption-inner { opacity:0; transform:translateY(28px); transition:opacity .8s cubic-bezier(.16,1,.3,1), transform .8s cubic-bezier(.16,1,.3,1); }
  .hr-cell--img.is-active .hr-caption-inner { opacity:1; transform:none; transition-delay:.25s; }

  /* Indicador de píldora (segmentos con relleno temporizado, como makai). */
  .hr-nav { position:absolute; left:50%; bottom:132px; transform:translateX(-50%); z-index:6; display:flex; align-items:center; gap:8px; padding:9px 14px; border-radius:999px; background:rgba(18,20,15,.30); border:1px solid rgba(255,255,255,.24); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); box-shadow:0 6px 20px rgba(0,0,0,.18); }
  .hr-seg { position:relative; width:26px; height:4px; border-radius:999px; background:rgba(255,255,255,.45); border:0; padding:0; cursor:pointer; overflow:hidden; transition:width .4s cubic-bezier(.22,1,.36,1); }
  .hr-seg.is-active { width:44px; }
  .hr-seg-fill { position:absolute; inset:0 auto 0 0; width:0; background:#fff; border-radius:999px; }
  .hr-seg.is-active .hr-seg-fill { width:100%; transition:width var(--seg-dur,5200ms) linear; }
  .hr-scroll { position:absolute; left:50%; bottom:88px; transform:translateX(-50%); z-index:6; width:38px; height:38px; border-radius:999px; border:1px solid rgba(255,255,255,.35); background:rgba(18,20,15,.34); backdrop-filter:blur(6px); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; animation:heroBounce 2.2s ease-in-out infinite; box-shadow:0 6px 18px rgba(0,0,0,.2); }
  .hr-scroll:hover { background:rgba(18,20,15,.5); color:#fff; }
  @keyframes heroBounce { 0%,100% { transform:translate(-50%,0); } 50% { transform:translate(-50%,7px); } }

  /* Botón de pausa/reproducción, integrado en la píldora de navegación. */
  .hr-play { margin-left:4px; width:22px; height:22px; flex:0 0 22px; display:flex; align-items:center; justify-content:center; border:0; padding:0; border-radius:999px; background:rgba(255,255,255,.18); color:#fff; font-size:9px; cursor:pointer; transition:background .2s; }
  .hr-play:hover { background:rgba(255,255,255,.34); }

  /* Flechas de navegación (aparecen al acercar el puntero al hero). */
  .hr-arrow { position:absolute; top:50%; z-index:7; width:46px; height:46px; margin-top:-23px; display:flex; align-items:center; justify-content:center; border-radius:999px; border:1px solid rgba(255,255,255,.28); background:rgba(18,20,15,.32); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#fff; font-size:22px; cursor:pointer; opacity:0; transition:opacity .3s ease, background .2s ease, transform .2s ease; box-shadow:0 6px 20px rgba(0,0,0,.20); }
  .hr-arrow--prev { left:22px; }
  .hr-arrow--next { right:22px; }
  .hr-stage:hover .hr-arrow, .hr-arrow:focus-visible { opacity:1; }
  .hr-arrow:hover { background:rgba(18,20,15,.55); transform:scale(1.06); }
  .hr-arrow:active { transform:scale(.96); }
  /* En pantallas táctiles las flechas están siempre visibles (no hay hover). */
  @media (hover:none) { .hr-arrow { opacity:.85; } }

  /* Deslizar sin que el navegador secuestre el gesto ni seleccione texto. */
  .hr-track { touch-action:pan-y; -webkit-user-select:none; user-select:none; }
  .hr-photo, .hr-cell img { -webkit-user-drag:none; user-drag:none; }
  /* Fondo neutro mientras la foto diferida termina de cargar. */
  .hr-cell--img .hr-photo { background-color:#1b1e18; transition:transform 7.5s ease, opacity .5s ease; }

  /* ===== Celda intro: composición por capas de Makai (cielo · nubes · texto · edificio) ===== */
  .hr-hero-makai { background:#cfe7f5; }
  /* Cielo: SKY.png ya trae nubes; una deriva lenta las mueve sin costuras. */
  .hr-sky { position:absolute; top:0; left:-6%; width:112%; height:100%; max-width:none; object-fit:cover; object-position:center top; z-index:1; pointer-events:none; user-select:none; animation:hrCloudDrift 40s ease-in-out infinite alternate; }
  /* Wordmark en una sola línea que siempre cabe en la franja de cielo, por
     encima del edificio (evita que una 2ª línea quede tapada). */
  .hr-wordmark2 { position:absolute; left:0; right:0; top:22%; z-index:3; margin:0 auto; padding:0 16px; max-width:11ch; text-align:center; font-family:'Inter Tight','Inter',sans-serif; font-weight:800; text-transform:uppercase; font-size:clamp(34px,8.4vw,128px); line-height:.96; letter-spacing:.01em; text-wrap:balance;
    background:linear-gradient(to bottom, rgba(255,255,255,.97) 20%, rgba(255,255,255,.66) 100%); -webkit-background-clip:text; background-clip:text; color:transparent; -webkit-text-fill-color:transparent; mix-blend-mode:luminosity; pointer-events:none; }
  /* El borde superior del edificio se funde con el cielo (elimina la costura
     entre SKY.png y MAKAI.png). */
  /* Edificio: PNG con fondo transparente (sin cielo propio) → se compone limpio
     sobre SKY.png, sin ninguna costura. */
  .hr-building { position:absolute; left:0; bottom:-1%; width:100%; height:auto; z-index:4; pointer-events:none; user-select:none; }
  /* Fundido inferior del edificio hacia el fondo de la página / buscador. */
  .hr-hero-makai::after { content:""; position:absolute; left:0; right:0; bottom:-1px; height:16%; z-index:5; pointer-events:none; background:linear-gradient(to top, #f4f4f4 0%, rgba(244,244,244,.65) 42%, rgba(244,244,244,0) 100%); }
  /* Botón de la portada: va por encima del edificio (z-index 4) y del fundido
     inferior, y despejado de la píldora de navegación (bottom:132px). */
  .hr-hero-cta { position:absolute; left:0; right:0; bottom:196px; z-index:6; display:flex; justify-content:center; padding:0 22px; }
  .hr-hero-cta .hb-btn { box-shadow:0 14px 34px -16px rgba(18,20,15,.75); }

  /* Entradas exactas de Makai: el texto cae desde arriba, el edificio sube desde abajo. */
  .hr-wordmark2 { opacity:0; transform:translateY(-180%); }
  .hr-building  { opacity:0; transform:translateY(105%); }
  #hero.is-revealed .hr-cell--hero .hr-wordmark2 { animation:mkTextEnter 1.6s cubic-bezier(.22,1,.36,1) .1s both; }
  #hero.is-revealed .hr-cell--hero .hr-building  { animation:mkBuildEnter 1.6s cubic-bezier(.22,1,.36,1) .1s both; }
  @keyframes mkTextEnter  { 0% { transform:translateY(-180%); opacity:0; } 60% { opacity:1; } 100% { transform:translateY(0); opacity:1; } }
  @keyframes mkBuildEnter { 0% { transform:translateY(105%);  opacity:0; } 40% { opacity:1; } 100% { transform:translateY(0); opacity:1; } }
  @keyframes hrCloudDrift { 0% { transform:translateX(-3%); } 100% { transform:translateX(3%); } }

  @media (max-width:640px) {
    .hr-wordmark2 { top:35%;text-align: left!important;padding:0!important; font-size:clamp(28px,12vw,72px); }
    .hr-building {         max-width: 250vw; width: 250vw !important; left:20%; margin-left:-100%; bottom:0; }
  }

  /* ===== Loader de marca (intro tipo Makai) ===== */
  #hbLoader { position:fixed; inset:0; z-index:2000; display:flex; align-items:center; justify-content:center; background:#f4f4f4; transition:opacity .6s ease, visibility .6s ease; }
  #hbLoader.is-hidden { opacity:0; visibility:hidden; }
  .ml-inner { display:flex; flex-direction:column; align-items:center; gap:24px; animation:ml-fade-in .7s ease both; }
  .ml-rings { position:relative; width:118px; height:118px; }
  .ml-ring { position:absolute; inset:0; margin:auto; width:34px; height:34px; border-radius:50%; border:1.5px solid #5c7c68; transform:scale(.3); opacity:0; animation:ml-ripple 2.4s cubic-bezier(.22,.61,.36,1) infinite; }
  .ml-ring:nth-child(2){ animation-delay:.6s; }
  .ml-ring:nth-child(3){ animation-delay:1.2s; }
  .ml-core { position:absolute; inset:0; margin:auto; width:44px; height:44px; border-radius:50%; background:#5c7c68; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Inter Tight',sans-serif; font-weight:800; font-size:20px; animation:ml-pulse 2.4s ease-in-out infinite; }
  .ml-name { font-family:'Inter Tight',sans-serif; font-weight:700; font-size:15px; letter-spacing:.02em; color:#2b303b; }
  .ml-bar { width:150px; height:3px; border-radius:99px; background:rgba(92,124,104,.15); overflow:hidden; }
  .ml-bar span { display:block; height:100%; width:40%; border-radius:99px; background:linear-gradient(90deg, transparent, #5c7c68, transparent); animation:ml-slide 1.3s ease-in-out infinite; }
  @keyframes ml-ripple { 0% { transform:scale(.3); opacity:0; } 15% { opacity:.55; } 100% { transform:scale(3.2); opacity:0; } }
  @keyframes ml-pulse { 0%,100% { transform:scale(1); } 50% { transform:scale(.92); } }
  @keyframes ml-slide { 0% { transform:translateX(-120%); } 100% { transform:translateX(330%); } }
  @keyframes ml-fade-in { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }

  /* Accesibilidad: sin movimiento, todo el contenido del hero visible al instante. */
  @media (prefers-reduced-motion: reduce) {
    .hr-anim-down, .hr-anim-up, .hr-cell--img .hr-caption-inner { opacity:1 !important; transform:none !important; animation:none !important; transition:none !important; }
    .hr-photo, .hr-track { transition:none !important; }
    .ml-ring { animation:none; } .ml-ring:first-child { opacity:.35; transform:scale(2.2); }
    .ml-bar span { animation:none; width:100%; }
  }

  /* ===== Rejilla de tipos de habitación =====
     Flex en vez de grid para que la última fila (o un listado de 1-2 tipos)
     quede centrada en lugar de pegada a la izquierda. Los anchos replican las
     columnas de antes: 1 / 2 / 3 por fila según el ancho de pantalla. */
  .rooms-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:24px; }
  .rooms-grid > .rc { width:100%; }
  @media (min-width:640px)  { .rooms-grid > .rc { width:calc((100% - 24px) / 2); } }
  @media (min-width:1024px) { .rooms-grid > .rc { width:calc((100% - 48px) / 3); } }

  /* ===== Tarjeta de habitación (estilo unit card Makai) ===== */
  .rc { position:relative; display:flex; flex-direction:column; background:#fff; border:1px solid #eaecf0; border-radius:24px; overflow:hidden; transition:box-shadow .35s cubic-bezier(.16,1,.3,1), transform .35s cubic-bezier(.16,1,.3,1); }
  .rc:hover { box-shadow:0 22px 48px -14px rgba(10,13,20,.24); transform:translateY(-3px); }
  .rc-inner { padding:6px 6px 0; }
  .rc-img { position:relative; height:216px; border-radius:20px; overflow:hidden; background:#f2f5f8; cursor:pointer; }
  .rc-img .bg { position:absolute; inset:0; background:center/cover no-repeat; transition:transform .6s cubic-bezier(.16,1,.3,1); }
  .rc:hover .rc-img .bg { transform:scale(1.06); }
  .rc-chips { position:absolute; top:0; left:0; right:0; display:flex; justify-content:space-between; align-items:flex-start; gap:8px; padding:10px; z-index:2; }
  .rc-cat { display:inline-flex; align-items:center; padding:5px 12px; border-radius:100px; background:rgba(255,255,255,.92); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#2b303b; font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,.10); }
  .rc-fav { display:inline-flex; align-items:center; gap:5px; padding:5px 11px; border-radius:100px; background:rgba(255,255,255,.92); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#b5540f; font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,.10); }
  .rc-fav i { color:#f4a52b; }
  .rc-body { display:flex; flex-direction:column; gap:14px; padding:12px 10px; }
  .rc-head { display:flex; flex-direction:column; gap:3px; }
  .rc-title-row { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
  .rc-name { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:18px; color:#171717; line-height:1.25; }
  .rc-sub { font-size:12.5px; color:#99a0ae; font-weight:500; }
  .rc-divider { height:1px; background:#ebebeb; margin:3px 0; }
  .rc-price-row { display:flex; align-items:flex-end; gap:8px; flex-wrap:wrap; }
  .rc-price { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:24px; color:#5c7c68; line-height:1; }
  .rc-price small { font-size:12px; font-weight:500; color:#a3a3a3; }
  .rc-total { font-size:12px; font-weight:600; color:#5c7c68; }
  .rc-stats { display:flex; align-items:stretch; border:1px solid #f2f5f8; border-radius:12px; overflow:hidden; }
  .rc-stat { flex:1 1 0; min-width:0; display:flex; flex-direction:column; align-items:center; gap:3px; padding:10px 4px; }
  .rc-stat + .rc-stat { border-left:1px solid #f2f5f8; }
  .rc-stat i { font-size:16px; color:#5c7c68; }
  .rc-stat .v { font-size:11px; font-weight:600; color:#525866; letter-spacing:.2px; white-space:nowrap; }
  .rc-actions { display:flex; gap:8px; }
  .rc-btn-info { flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 16px; background:#f2f5f8; border:0; border-radius:12px; font-weight:600; font-size:13.5px; color:#717784; cursor:pointer; transition:background .2s; }
  .rc-btn-info:hover { background:#e7eaef; color:#525866; }
  .rc-btn-cta { flex:1 1 0; min-width:0; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 16px; border-radius:12px; background:#5c7c68; color:#fff; border:1px solid #5c7c68; font-weight:600; font-size:13.5px; cursor:pointer; box-shadow:0 1px 2px rgba(14,18,27,.2); transition:background .2s, transform .15s; }
  .rc-btn-cta:hover { background:#4a6354; }
  .rc-btn-cta:active { transform:scale(.98); }

  /* ===== Tarjeta de TIPO de habitación (vista por defecto de la web) ===== */
  .gc-desc { font-size:12.5px; color:#717784; line-height:1.5; margin:4px 0 0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .gc-avail { display:inline-flex; align-items:center; padding:5px 12px; border-radius:100px; background:rgba(255,255,255,.92); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); color:#4a6354; font-size:11px; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,.10); }
  .gc-avail.is-low { color:#b5540f; }
  .gc-amenities { display:flex; flex-wrap:wrap; gap:6px; }
  .gc-amenity { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:100px; background:#f5f8f6; color:#5c7c68; font-size:11.5px; font-weight:500; }
  .gc-amenity i { font-size:11px; }
  .gc-btn-pick { flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:11px 14px; background:#f2f5f8; border:0; border-radius:12px; font-weight:600; font-size:13px; color:#717784; cursor:pointer; white-space:nowrap; transition:background .2s, color .2s; }
  .gc-btn-pick:hover { background:#e7eaef; color:#525866; }

  /* Desglose por habitación (solo si el visitante quiere elegir una). */
  .gc-rooms { margin-top:4px; padding-top:12px; border-top:1px dashed #e6e9ee; display:flex; flex-direction:column; gap:8px; }
  .gc-rooms-title { font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#99a0ae; }
  .gc-room { display:flex; align-items:center; gap:10px; padding:9px 11px; border:1px solid #eef0f3; border-radius:11px; background:#fbfbfc; transition:border-color .2s, background .2s; }
  .gc-room:hover { border-color:#d7e3db; background:#fff; }
  .gc-room-info { display:flex; flex-direction:column; gap:1px; min-width:0; flex:1 1 auto; }
  .gc-room-name { font-size:13px; font-weight:600; color:#2b303b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .gc-room-meta { font-size:11px; color:#99a0ae; }
  .gc-room-price { font-size:12px; font-weight:700; color:#5c7c68; white-space:nowrap; }
  .gc-room-actions { display:flex; align-items:center; gap:6px; flex-shrink:0; }
  .gc-room-detail { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border:0; border-radius:8px; background:#f2f5f8; color:#717784; font-size:11px; cursor:pointer; transition:background .2s; }
  .gc-room-detail:hover { background:#e7eaef; }
  .gc-room-reserve { padding:6px 13px; border:0; border-radius:8px; background:#5c7c68; color:#fff; font-size:12px; font-weight:600; cursor:pointer; transition:background .2s; }
  .gc-room-reserve:hover { background:#4a6354; }

  @media (max-width:400px) {
    .gc-room { flex-wrap:wrap; }
    .gc-room-price { order:3; }
    .gc-room-actions { margin-left:auto; }
  }

  /* ===== Buscador flotante ===== */
  .hr-search { position:relative; z-index:20; }
  #reservation-form.hr-search { margin-top:-70px; }
  .hr-search__card { display:grid; grid-template-columns:1fr 1fr auto auto; gap:16px; align-items:end; padding:22px; background:#fff; border:1px solid #eaecf0; border-radius:20px; box-shadow:0 24px 60px -20px rgba(10,13,20,.30); }
  .hr-field { min-width:0; }
  .hr-datetime { display:flex; gap:8px; }
  .hr-datetime input[type="date"] { flex:1 1 60%; min-width:0; }
  .hr-datetime input[type="time"] { flex:1 1 40%; min-width:0; padding:0 6px; text-align:center; }

  /* Selector de huéspedes */
  .hr-guests { position:relative; }
  .hr-guests-box { height:46px; border:1px solid #eaecf0; border-radius:10px; background:#fff; padding:0 14px; display:flex; align-items:center; justify-content:space-between; gap:12px; cursor:pointer; font-size:14px; color:#222530; min-width:170px; white-space:nowrap; transition:border-color .15s, box-shadow .15s; }
  .hr-guests-box i { color:#99a0ae; }
  .hr-guests-box.is-open { border-color:#5c7c68; box-shadow:0 0 0 3px rgba(92,124,104,.18); }
  .hr-guests-pop { position:absolute; left:0; bottom:calc(100% + 10px); width:280px; background:#fff; border:1px solid #eaecf0; border-radius:14px; box-shadow:0 24px 60px -18px rgba(10,13,20,.30); padding:18px; z-index:60; display:none; }
  .hr-guests-pop.is-open { display:block; }
  .hr-pop-field { margin-bottom:14px; }
  .hr-pop-field > label { display:block; font-size:13px; font-weight:600; color:#2b303b; margin-bottom:7px; }
  .hr-btn-search { height:46px; }
  /* Nº de noches junto a la etiqueta de salida. */
  .hr-nights-hint { margin-left:6px; font-size:11.5px; font-weight:600; color:#5c7c68; }
  /* Aviso de validación del buscador (sustituye a los alert() del navegador). */
  .hr-search__error { grid-column:1 / -1; margin:-4px 0 0; padding:10px 13px; border-radius:10px; background:#fdecec; border:1px solid #f7c9c9; color:#a02020; font-size:13px; font-weight:500; display:flex; align-items:center; gap:8px; }
  .hr-search__card .hb-input.is-invalid { border-color:#e05252; box-shadow:0 0 0 3px rgba(224,82,82,.15); }

  /* ===== Barra de resultados (resumen + filtros) ===== */
  .hr-results-bar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:14px; margin-bottom:24px; padding:14px 18px; background:#fff; border:1px solid #eaecf0; border-radius:16px; box-shadow:0 2px 10px rgba(10,13,20,.05); }
  .hr-results-summary { display:flex; flex-wrap:wrap; align-items:center; gap:8px; }
  .hr-results-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:100px; background:#f2f5f8; color:#525866; font-size:12.5px; font-weight:600; }
  .hr-results-edit, .hr-results-clear { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border:0; border-radius:100px; background:transparent; color:#5c7c68; font-size:12.5px; font-weight:600; cursor:pointer; transition:background .15s; }
  .hr-results-edit:hover, .hr-results-clear:hover { background:#eef3ef; }
  .hr-results-clear { color:#99a0ae; }
  .hr-results-clear:hover { background:#f2f5f8; color:#525866; }
  .hr-results-filters { display:flex; align-items:center; gap:12px; }
  .hr-filter { display:inline-flex; align-items:center; gap:7px; font-size:12.5px; font-weight:600; color:#717784; white-space:nowrap; }
  .hr-filter-select { height:38px; min-width:150px; font-size:13px; }

  /* Alternativas de fechas cuando no hay disponibilidad. */
  .hr-alt { margin-top:8px; padding:24px; border-radius:18px; background:#f7faf8; border:1px solid #e2ebe5; }
  .hr-alt h4 { font-family:'Inter Tight','Inter',sans-serif; font-weight:700; font-size:16px; color:#2b303b; margin:0 0 4px; }
  .hr-alt p { font-size:13.5px; color:#717784; margin:0 0 14px; }
  .hr-alt-list { display:flex; flex-wrap:wrap; gap:10px; }
  .hr-alt-btn { display:flex; flex-direction:column; align-items:flex-start; gap:2px; padding:11px 16px; border-radius:12px; border:1px solid #d7e3db; background:#fff; cursor:pointer; text-align:left; transition:border-color .2s, box-shadow .2s, transform .15s; }
  .hr-alt-btn:hover { border-color:#5c7c68; box-shadow:0 6px 16px -8px rgba(92,124,104,.5); transform:translateY(-1px); }
  .hr-alt-btn b { font-size:13.5px; font-weight:700; color:#2b303b; }
  .hr-alt-btn small { font-size:11.5px; color:#5c7c68; font-weight:600; }


  @media (max-width:640px) {
    .hr-results-bar { flex-direction:column; align-items:stretch; }
    .hr-results-filters { flex-direction:column; align-items:stretch; gap:10px; }
    .hr-filter { justify-content:space-between; }
    .hr-filter-select { flex:1 1 auto; min-width:0; }
    .hr-alt-list { flex-direction:column; }
    .hr-alt-btn { width:100%; }
  }

  /* ---- Tablet ---- */
  @media (max-width:991px) {
    .hr-stage { height:74vh; min-height:540px; }
    .hr-cell-caption { padding:104px 24px 140px; }
    .hr-nav { bottom:118px; }
    .hr-scroll { bottom:74px; }
    .hr-arrow { width:40px; height:40px; margin-top:-20px; font-size:19px; }
    .hr-arrow--prev { left:12px; }
    .hr-arrow--next { right:12px; }
    .hr-search__card { grid-template-columns:1fr 1fr; }
    .hr-field--guests, .hr-field--submit { grid-column:auto; }
    .hr-field--submit { grid-column:1 / -1; }
    .hr-btn-search { width:100%; }
  }
  /* ---- Móvil ---- */
  @media (max-width:640px) {
    .hr-stage { height:auto; min-height:0; }
    .hr-cell { min-height:64vh; }
    .hr-cell--hero { min-height:58vh; }
    .hr-cell-caption { padding:112px 20px 108px; }
    .hr-chip { margin-bottom:16px; }
    .hr-title { font-size:clamp(29px,8.5vw,40px); margin-bottom:14px; }
    .hr-subtitle { font-size:16px; }
    .hr-wordmark { font-size:clamp(40px,15vw,72px); }
    .hr-nav { bottom:64px; }
    .hr-scroll { display:none; }
    /* En móvil manda el deslizamiento con el dedo: las flechas ocuparían
       demasiado sobre la foto. */
    .hr-arrow { display:none; }
    #reservation-form.hr-search { margin-top:-40px; }
    .hr-search__card { grid-template-columns:1fr; padding:16px; gap:14px; border-radius:18px; }
    .hr-guests-box { min-width:0; }
    .hr-guests-pop { width:100%; }
  }
  @media (max-width:380px) {
    .hr-title { font-size:26px; }
    .hr-cell { min-height:80vh; }
  }

  /* ===== Galería lightbox ===== */
  .hb-lightbox { position:fixed; inset:0; z-index:1100; display:none; align-items:center; justify-content:center; background:rgba(23,23,23,.9); padding:24px; }
  .hb-lightbox.is-open { display:flex; }
  .hb-lightbox img { max-width:92vw; max-height:88vh; border-radius:12px; box-shadow:0 30px 80px rgba(0,0,0,.5); }
  .hb-lightbox__close { position:absolute; top:22px; right:26px; color:#fff; font-size:30px; cursor:pointer; opacity:.8; }
  .hb-lightbox__close:hover { opacity:1; }

  .lined-title { position:relative; }
</style>
</head>

<body>

{{-- Loader de marca (intro tipo home Makai): se muestra un instante y, al
     levantarse, revela y reproduce la animación de entrada del hero. --}}
<div id="hbLoader" aria-hidden="true">
  <div class="ml-inner">
    <div class="ml-rings">
      <span class="ml-ring"></span><span class="ml-ring"></span><span class="ml-ring"></span>
      <span class="ml-core">{{ mb_strtoupper(mb_substr($hotelName, 0, 1)) }}</span>
    </div>
    <div class="ml-name">{{ $hotelName }}</div>
    <div class="ml-bar"><span></span></div>
  </div>
</div>
{{-- Sin JS: no ocultar contenido animado ni bloquear con el loader. --}}
<noscript><style>.hr-anim-down,.hr-anim-up,.hr-cell--img .hr-caption-inner{opacity:1!important;transform:none!important}#hbLoader{display:none!important}</style></noscript>

@include('hotel::landing.partials.header', ['onLanding' => true, 'activeNav' => 'inicio'])

@php
  // Garantizamos al menos una diapositiva aunque el tenant guarde la lista vacía.
  $slides = !empty($cfg['slides']) && is_array($cfg['slides'])
    ? $cfg['slides']
    : [['image' => null, 'title' => '', 'subtitle' => 'Reserva tu estancia con nosotros', 'stars' => true]];

  // La primera "slide" es la portada intro (imagen + wordmark del hotel);
  // las siguientes continúan como slides normales.
  $firstSlide = $slides[0];
  $introImg   = HotelLandingSetting::imageUrl($firstSlide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
  $introSub   = trim($firstSlide['subtitle'] ?? '') !== '' ? $firstSlide['subtitle'] : 'Reserva tu estancia con nosotros';
  $introStars = $firstSlide['stars'] ?? true;
  $restSlides = array_slice($slides, 1);
  $cellCount  = 1 + count($restSlides);
@endphp

<!-- ===== Hero ===== -->
<section class="hr-stage {{ $cellCount > 1 ? 'has-slider' : '' }}" id="hero"
         aria-roledescription="carrusel" aria-label="Portada de {{ $hotelName }}">
  <div class="hr-track" id="hrTrack">

    <!-- Celda 0: portada intro — composición por capas de Makai
         (cielo · nubes · wordmark que cae · edificio que sube) -->
    <div class="hr-cell hr-cell--hero hr-hero-makai is-active"
         role="group" aria-roledescription="diapositiva" aria-label="1 de {{ $cellCount }}"
         aria-hidden="false">
      <img class="hr-sky" src="/landing-reservas/images/hero/SKY.png" alt="" aria-hidden="true">
      <h1 class="hr-wordmark2">{{ $hotelName }}</h1>
      <img class="hr-building" src="/landing-reservas/images/hero/catedral.png" alt="{{ $hotelName }}">
      @if(!empty($firstSlide['button_text']))
        <div class="hr-hero-cta hr-anim-up d2">
          <a href="{{ $firstSlide['button_link'] ?: '#rooms-results' }}" class="nav-scroll hb-btn hb-btn-primary hb-btn-lg">
            {{ $firstSlide['button_text'] }} <i class="fa fa-angle-right"></i>
          </a>
        </div>
      @endif
    </div>

    <!-- Celdas 1..n: slides.
         La imagen de la primera diapositiva se precarga (fetchpriority alto) y
         las demás se cargan de forma diferida cuando se acercan a pantalla,
         para no penalizar el LCP con 5 fotos a pantalla completa. -->
    @foreach($restSlides as $slide)
      @php
        $slideImg   = HotelLandingSetting::imageUrl($slide['image'] ?? null, HotelLandingSetting::DEFAULT_SLIDE);
        $slideTitle = trim($slide['title'] ?? '') !== '' ? $slide['title'] : $hotelName;
        $showStars  = $slide['stars'] ?? true;
        $eager      = $loop->first;
      @endphp
      {{-- aria-hidden ya viene en el HTML (no solo cuando arranca el JS): las
           diapositivas de fondo no deben leerse desde el primer pintado. --}}
      <div class="hr-cell hr-cell--img"
           role="group" aria-roledescription="diapositiva" aria-label="{{ $loop->index + 2 }} de {{ $cellCount }}"
           aria-hidden="true">
        <div class="hr-photo"
             data-bg="{{ $slideImg }}"
             @if($eager) style="background-image:url('{{ $slideImg }}');" @endif></div>
        <div class="hr-overlay"></div>
        <div class="hr-cell-caption">
          <div class="hr-caption-inner">
            <span class="hr-chip">
              @if($showStars)<span class="stars">@for($s=0;$s<5;$s++)★@endfor</span>@endif
              {{ $hotelName }}
            </span>
            <h2 class="hr-title">{{ $slideTitle }}</h2>
            @if(!empty($slide['subtitle']))<p class="hr-subtitle">{{ $slide['subtitle'] }}</p>@endif
            @if(!empty($slide['button_text']))
              <div class="hr-cta">
                <a href="{{ $slide['button_link'] ?: '#rooms-results' }}" class="nav-scroll hb-btn hb-btn-light hb-btn-lg">
                  {{ $slide['button_text'] }} <i class="fa fa-angle-right"></i>
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  @if($cellCount > 1)
    <button type="button" class="hr-arrow hr-arrow--prev" id="hrPrev" aria-label="Diapositiva anterior">
      <i class="fa fa-angle-left"></i>
    </button>
    <button type="button" class="hr-arrow hr-arrow--next" id="hrNext" aria-label="Diapositiva siguiente">
      <i class="fa fa-angle-right"></i>
    </button>

    <div class="hr-nav" id="hrNav" role="tablist" aria-label="Portada">
      @for($s = 0; $s < $cellCount; $s++)
        <button type="button" class="hr-seg {{ $s === 0 ? 'is-active' : '' }}" data-i="{{ $s }}"
                role="tab" aria-selected="{{ $s === 0 ? 'true' : 'false' }}"
                aria-label="Ir a la vista {{ $s + 1 }} de {{ $cellCount }}"><span class="hr-seg-fill"></span></button>
      @endfor
      <button type="button" class="hr-play" id="hrPlay" aria-label="Pausar la reproducción automática" aria-pressed="false">
        <i class="fa fa-pause"></i>
      </button>
    </div>
  @endif

  <a href="#rooms-results" class="hr-scroll nav-scroll" aria-label="Desplázate"><i class="fa fa-angle-down"></i></a>
</section>

<script>
(function () {
  // Controlador del hero: track horizontal (celda intro + slides) con
  // indicador de píldora, arrancado tras la revelación del loader.
  //
  // Además del avance automático soporta: flechas, teclado, arrastre/deslizado
  // táctil, pausa al pasar el ratón o al enfocar con el teclado, pausa cuando
  // la pestaña no está visible o el hero sale de pantalla, botón de
  // pausa/reproducción y carga diferida de las imágenes.
  var hero  = document.getElementById('hero');
  var track = document.getElementById('hrTrack');
  var nav   = document.getElementById('hrNav');
  if (!hero || !track) return;

  var cells = track.children, count = cells.length;
  var segs  = nav ? nav.querySelectorAll('.hr-seg') : [];
  var prevBtn = document.getElementById('hrPrev');
  var nextBtn = document.getElementById('hrNext');
  var playBtn = document.getElementById('hrPlay');

  var INTRO_MS = 6200, SLIDE_MS = 5200;
  var idx = 0, timer = null, revealed = false;
  var userPaused = false;      // el visitante pulsó "pausa"
  var hoverPaused = false;     // ratón/foco sobre el hero
  var offscreen = false;       // hero fuera de la pantalla o pestaña oculta

  var reduceMotion = window.matchMedia
    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- Carga diferida de las fotos de las diapositivas ---- */
  function loadPhoto(i) {
    var cell = cells[i];
    if (!cell) return;
    var photo = cell.querySelector('.hr-photo[data-bg]');
    if (!photo) return;
    var url = photo.getAttribute('data-bg');
    photo.removeAttribute('data-bg');
    if (!url) return;
    // Precarga real: solo se pinta cuando la imagen está lista, así no se ve
    // el salto de un fondo vacío a la foto.
    var img = new Image();
    img.onload = function () { photo.style.backgroundImage = "url('" + url + "')"; };
    img.src = url;
  }
  // Cargar la diapositiva actual y la siguiente (una de adelanto).
  function ensureLoaded(i) {
    loadPhoto(i);
    loadPhoto((i + 1) % count);
  }

  function duration(i) { return i === 0 ? INTRO_MS : SLIDE_MS; }

  function fill(seg, active, dur) {
    var f = seg.querySelector('.hr-seg-fill');
    if (!f) return;
    if (active && !reduceMotion) {
      f.style.transition = 'none'; f.style.width = '0'; void f.offsetWidth;
      f.style.transition = 'width ' + dur + 'ms linear'; f.style.width = '100%';
    } else if (active) {
      f.style.transition = 'none'; f.style.width = '100%';
    } else {
      f.style.transition = 'none'; f.style.width = '0';
    }
  }

  function activate(n) {
    idx = (n + count) % count;
    ensureLoaded(idx);
    track.style.transform = 'translateX(' + (-idx * 100) + '%)';
    for (var i = 0; i < count; i++) {
      cells[i].classList.toggle('is-active', i === idx);
      // Las diapositivas ocultas no deben ser alcanzables con el tabulador
      // (aria-hidden no lo impide: hay que retirar el botón del recorrido).
      cells[i].setAttribute('aria-hidden', i === idx ? 'false' : 'true');
      var links = cells[i].querySelectorAll('a, button');
      for (var j = 0; j < links.length; j++) {
        if (i === idx) links[j].removeAttribute('tabindex');
        else links[j].setAttribute('tabindex', '-1');
      }
    }
    var dur = duration(idx);
    for (var k = 0; k < segs.length; k++) {
      var on = k === idx;
      segs[k].classList.toggle('is-active', on);
      segs[k].setAttribute('aria-selected', on ? 'true' : 'false');
      fill(segs[k], on, dur);
    }
  }

  function paused() { return userPaused || hoverPaused || offscreen || reduceMotion; }

  function stop() { clearTimeout(timer); timer = null; }

  function schedule() {
    stop();
    if (count < 2 || !revealed || paused()) return;
    timer = setTimeout(function () { activate(idx + 1); schedule(); }, duration(idx));
  }

  // Un cambio manual reinicia el temporizador y la barra de progreso.
  function goTo(n) { activate(n); schedule(); }
  function next() { goTo(idx + 1); }
  function prev() { goTo(idx - 1); }

  /* ---- Controles ---- */
  for (var k = 0; k < segs.length; k++) {
    segs[k].addEventListener('click', function () {
      goTo(parseInt(this.getAttribute('data-i'), 10));
    });
  }
  if (nextBtn) nextBtn.addEventListener('click', next);
  if (prevBtn) prevBtn.addEventListener('click', prev);

  if (playBtn) {
    playBtn.addEventListener('click', function () {
      userPaused = !userPaused;
      playBtn.setAttribute('aria-pressed', userPaused ? 'true' : 'false');
      playBtn.setAttribute('aria-label', userPaused
        ? 'Reanudar la reproducción automática'
        : 'Pausar la reproducción automática');
      playBtn.innerHTML = userPaused
        ? '<i class="fa fa-play"></i>'
        : '<i class="fa fa-pause"></i>';
      if (userPaused) {
        stop();
        // Congelar la barra de progreso donde esté.
        var f = segs[idx] && segs[idx].querySelector('.hr-seg-fill');
        if (f) {
          var w = f.getBoundingClientRect().width;
          var parentW = f.parentNode.getBoundingClientRect().width || 1;
          f.style.transition = 'none';
          f.style.width = (w / parentW * 100) + '%';
        }
      } else {
        activate(idx);
        schedule();
      }
    });
  }

  // Teclado: flechas para navegar cuando el hero tiene el foco.
  hero.setAttribute('tabindex', '-1');
  hero.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') { e.preventDefault(); next(); }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); prev(); }
  });

  // Pausa mientras el visitante está leyendo (ratón encima o foco dentro).
  function setHoverPause(v) {
    if (hoverPaused === v) return;
    hoverPaused = v;
    if (v) { stop(); } else { activate(idx); schedule(); }
  }
  hero.addEventListener('mouseenter', function () { setHoverPause(true); });
  hero.addEventListener('mouseleave', function () { setHoverPause(false); });
  hero.addEventListener('focusin',  function () { setHoverPause(true); });
  hero.addEventListener('focusout', function (e) {
    if (!hero.contains(e.relatedTarget)) setHoverPause(false);
  });

  // No gastar CPU ni "saltarse" diapositivas con la pestaña oculta.
  document.addEventListener('visibilitychange', function () {
    offscreen = document.hidden;
    if (offscreen) stop(); else { activate(idx); schedule(); }
  });

  // Tampoco animar si el hero ya no está en pantalla.
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      var visible = entries[0] && entries[0].isIntersecting;
      offscreen = !visible || document.hidden;
      if (offscreen) stop(); else if (revealed) { activate(idx); schedule(); }
    }, { threshold: 0.15 }).observe(hero);
  }

  /* ---- Deslizar con el dedo / arrastrar con el ratón ---- */
  var dragStartX = 0, dragStartY = 0, dragging = false, dragLocked = false;
  var SWIPE_MIN = 45;   // px mínimos para considerar un deslizamiento

  function pointerDown(x, y) {
    dragStartX = x; dragStartY = y; dragging = true; dragLocked = false;
    stop();
  }
  function pointerMove(x, y, ev) {
    if (!dragging) return;
    var dx = x - dragStartX, dy = y - dragStartY;
    // Solo se toma como deslizamiento horizontal si domina sobre el vertical,
    // así no se secuestra el scroll de la página.
    if (!dragLocked && Math.abs(dx) > 10 && Math.abs(dx) > Math.abs(dy)) {
      dragLocked = true;
    }
    if (dragLocked && ev && ev.cancelable) ev.preventDefault();
  }
  function pointerUp(x) {
    if (!dragging) return;
    var dx = x - dragStartX;
    dragging = false;
    if (dragLocked && Math.abs(dx) >= SWIPE_MIN) {
      dx < 0 ? next() : prev();
    } else {
      schedule();
    }
    dragLocked = false;
  }

  track.addEventListener('touchstart', function (e) {
    var t = e.changedTouches[0];
    pointerDown(t.clientX, t.clientY);
  }, { passive: true });
  track.addEventListener('touchmove', function (e) {
    var t = e.changedTouches[0];
    pointerMove(t.clientX, t.clientY, e);
  }, { passive: false });
  track.addEventListener('touchend', function (e) {
    pointerUp(e.changedTouches[0].clientX);
  });

  track.addEventListener('mousedown', function (e) {
    if (e.button !== 0) return;
    pointerDown(e.clientX, e.clientY);
  });
  window.addEventListener('mousemove', function (e) { pointerMove(e.clientX, e.clientY, null); });
  window.addEventListener('mouseup', function (e) { if (dragging) pointerUp(e.clientX); });

  window.hbHeroReveal = function () {
    if (revealed) return; revealed = true;
    hero.classList.add('is-revealed');
    activate(0);
    schedule();
  };
  // Salvaguarda por si el loader no dispara la revelación.
  setTimeout(function () { window.hbHeroReveal(); }, 2600);
})();

(function () {
  // Loader de marca: mínimo visible, luego revela el hero.
  var loader = document.getElementById('hbLoader');
  if (!loader) { if (window.hbHeroReveal) window.hbHeroReveal(); return; }
  var start = Date.now(), MIN = 1300, done = false;
  function hide() {
    if (done) return; done = true;
    if (window.hbHeroReveal) window.hbHeroReveal();
    loader.classList.add('is-hidden');
    setTimeout(function () { if (loader.parentNode) loader.parentNode.removeChild(loader); }, 650);
  }
  if (document.readyState === 'complete') { setTimeout(hide, MIN); }
  else { window.addEventListener('load', function () { var r = MIN - (Date.now() - start); setTimeout(hide, r > 0 ? r : 0); }); }
  setTimeout(hide, 6000); // nunca bloquear más de 6s
})();
</script>

<!-- ===== Buscador de disponibilidad ===== -->
<section class="hr-search" id="reservation-form">
  <div class="max-w-6xl mx-auto px-6">
    <form class="hr-search__card" id="searchform" onsubmit="return false;">
      <div class="hr-field">
        <label class="hb-label" for="checkin_date"><i class="fa fa-calendar"></i> Entrada</label>
        <input name="checkin" type="date" id="checkin_date" class="hb-input" required>
      </div>
      <div class="hr-field">
        <label class="hb-label" for="checkout_date">
          <i class="fa fa-calendar"></i> Salida
          <span class="hr-nights-hint" id="nightsHint"></span>
        </label>
        <input name="checkout" type="date" id="checkout_date" class="hb-input" required>
      </div>
      <div class="hr-field hr-field--guests hr-guests">
        <label class="hb-label"><i class="fa fa-users"></i> Huéspedes</label>
        <div class="hr-guests-box" id="guestsToggle">
          <span id="guestsSummary">2 adultos</span>
          <i class="fa fa-angle-down"></i>
        </div>
        <div class="hr-guests-pop" id="guestsPop">
          <div class="hr-pop-field">
            <label>Adultos</label>
            <select name="adults" id="adults" class="hb-select">
              @for($i=1;$i<=8;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'adulto' : 'adultos' }}</option>@endfor
            </select>
          </div>
          <div class="hr-pop-field">
            <label>Niños</label>
            <select name="children" id="children" class="hb-select">
              @for($i=0;$i<=6;$i++)<option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'niño' : 'niños' }}</option>@endfor
            </select>
          </div>
          <button type="button" class="hb-btn hb-btn-ghost hb-btn-block" id="guestsSave">Guardar</button>
        </div>
      </div>
      <div class="hr-field hr-field--submit">
        <button type="submit" id="btn-search" class="hb-btn hb-btn-primary hr-btn-search"><i class="fa fa-search"></i> Buscar</button>
      </div>
      <div class="hr-search__error" id="searchError" role="alert" hidden></div>
    </form>
  </div>
</section>

<script>
(function () {
  // Abrir el calendario nativo al pulsar el campo de fecha.
  var dateInputs = document.querySelectorAll('.hr-search__card input[type="date"]');
  for (var k = 0; k < dateInputs.length; k++) {
    var openPicker = function () { try { if (this.showPicker) this.showPicker(); } catch (e) {} };
    dateInputs[k].addEventListener('click', openPicker);
  }

  // Selector de huéspedes.
  var gToggle  = document.getElementById('guestsToggle');
  var gPop     = document.getElementById('guestsPop');
  var gSave    = document.getElementById('guestsSave');
  var gSummary = document.getElementById('guestsSummary');
  var aSel     = document.getElementById('adults');
  var cSel     = document.getElementById('children');
  if (gToggle && gPop) {
    var updateSummary = function () {
      var a = parseInt(aSel.value, 10) || 0, c = parseInt(cSel.value, 10) || 0;
      var parts = [a + (a === 1 ? ' adulto' : ' adultos')];
      if (c > 0) parts.push(c + (c === 1 ? ' niño' : ' niños'));
      gSummary.textContent = parts.join(', ');
    };
    var closePop = function () { gPop.classList.remove('is-open'); gToggle.classList.remove('is-open'); };
    gToggle.addEventListener('click', function (e) { e.stopPropagation(); gPop.classList.toggle('is-open'); gToggle.classList.toggle('is-open'); });
    gPop.addEventListener('click', function (e) { e.stopPropagation(); });
    document.addEventListener('click', closePop);
    if (gSave) gSave.addEventListener('click', function () { updateSummary(); closePop(); });
    aSel.addEventListener('change', updateSummary);
    cSel.addEventListener('change', updateSummary);
    updateSummary();
    // El resumen debe poder refrescarse desde fuera (p. ej. al restaurar una
    // búsqueda compartida por enlace).
    window.hbUpdateGuestsSummary = updateSummary;
  }

  /* ---- Fechas coherentes desde el primer momento ----
     Antes ambos campos salían vacíos y sin límites: se podía pedir una entrada
     en el pasado o una salida anterior a la entrada, y el error solo aparecía
     al pulsar "Buscar". Ahora el formulario llega relleno (hoy → mañana), no
     deja elegir fechas pasadas y la salida se reajusta sola. */
  var inEl  = document.getElementById('checkin_date');
  var outEl = document.getElementById('checkout_date');
  var hint  = document.getElementById('nightsHint');
  if (!inEl || !outEl) return;

  function iso(d) {
    var p = function (n) { return (n < 10 ? '0' : '') + n; };
    return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate());
  }
  function addDays(dateStr, n) {
    var parts = dateStr.split('-');
    var d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    d.setDate(d.getDate() + n);
    return iso(d);
  }
  function nightsBetween(a, b) {
    if (!a || !b) return 0;
    var pa = a.split('-'), pb = b.split('-');
    var da = new Date(pa[0], pa[1] - 1, pa[2]), db = new Date(pb[0], pb[1] - 1, pb[2]);
    return Math.round((db - da) / 86400000);
  }

  var today = iso(new Date());
  inEl.min = today;

  // Valores iniciales: los del enlace compartido (?checkin=&checkout=) o
  // hoy → mañana.
  var qs = new URLSearchParams(window.location.search);
  var qIn = qs.get('checkin'), qOut = qs.get('checkout');
  inEl.value  = (qIn && qIn >= today) ? qIn : today;
  outEl.value = (qOut && qOut > inEl.value) ? qOut : addDays(inEl.value, 1);

  var qAdults = parseInt(qs.get('adults'), 10);
  var qChildren = parseInt(qs.get('children'), 10);
  if (aSel && qAdults >= 1) { aSel.value = String(Math.min(qAdults, 8)); }
  if (cSel && qChildren >= 0) { cSel.value = String(Math.min(qChildren, 6)); }
  if (window.hbUpdateGuestsSummary) window.hbUpdateGuestsSummary();

  function syncDates() {
    if (inEl.value && inEl.value < today) inEl.value = today;
    // La salida nunca puede ser anterior o igual a la entrada.
    outEl.min = inEl.value ? addDays(inEl.value, 1) : today;
    if (outEl.value && inEl.value && outEl.value <= inEl.value) {
      outEl.value = addDays(inEl.value, 1);
    }
    var n = nightsBetween(inEl.value, outEl.value);
    if (hint) hint.textContent = n > 0 ? '· ' + n + (n === 1 ? ' noche' : ' noches') : '';
  }

  inEl.addEventListener('change', syncDates);
  outEl.addEventListener('change', syncDates);
  syncDates();

  // Exponer utilidades a la lógica de reservas (más abajo).
  window.hbDates = { iso: iso, addDays: addDays, nightsBetween: nightsBetween, today: today };
  if (aSel && cSel) window.hbGuestSelects = { adults: aSel, children: cSel };
})();
</script>

<!-- ===== Habitaciones ===== -->
<section class="hb-section pt-16" id="rooms-results">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Alojamiento</span>
      <h2 class="hb-h2" id="rooms-heading">{{ $cfg['rooms_heading'] ?? 'Nuestras habitaciones' }}</h2>
      <p class="hb-sub" id="rooms-subheading">{{ $cfg['rooms_subheading'] ?? 'Selecciona fechas para ver disponibilidad y precios.' }}</p>
    </div>

    <!-- Resumen de la búsqueda + filtros de los resultados -->
    <div class="hr-results-bar" id="resultsBar" hidden>
      <div class="hr-results-summary" id="resultsSummary" hidden>
        <span class="hr-results-chip" id="chipDates"></span>
        <span class="hr-results-chip" id="chipGuests"></span>
        <button type="button" class="hr-results-edit" id="btnEditSearch"><i class="fa fa-pencil"></i> Modificar búsqueda</button>
        <button type="button" class="hr-results-clear" id="btnClearSearch"><i class="fa fa-times"></i> Quitar filtros</button>
      </div>
      <div class="hr-results-filters">
        <label class="hr-filter">
          <span>Tipo</span>
          <select id="filterCategory" class="hb-select hr-filter-select"><option value="">Todos</option></select>
        </label>
        <label class="hr-filter">
          <span>Ordenar</span>
          <select id="filterSort" class="hb-select hr-filter-select">
            <option value="featured">Recomendadas</option>
            <option value="price_asc">Precio: menor a mayor</option>
            <option value="price_desc">Precio: mayor a menor</option>
            <option value="capacity_desc">Mayor capacidad</option>
          </select>
        </label>
      </div>
    </div>

    <div id="rooms-grid"></div>

    <!-- Fechas alternativas cuando no hay disponibilidad -->
    <div id="rooms-suggestions"></div>
  </div>
</section>

@if(($cfg['show_features'] ?? true) && count($cfg['features'] ?? []))
<!-- ===== Ventajas ===== -->
<section class="hb-section bg-white border-y border-ink-100">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="hb-eyebrow mb-3">Ventajas</span>
      <h2 class="hb-h2">{{ $cfg['features_heading'] ?? '¿Por qué reservar con nosotros?' }}</h2>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      @foreach($cfg['features'] as $feature)
      <div class="reveal hb-card p-7 text-center transition duration-300 ease-makai hover:shadow-panel hover:-translate-y-1" style="--reveal-i:{{ $loop->index }}">
        <div class="w-14 h-14 rounded-2xl bg-brand-tint text-brand flex items-center justify-center mx-auto mb-5">
          <i class="fa {{ $feature['icon'] ?? 'fa-star' }} text-2xl"></i>
        </div>
        <h3 class="font-display font-semibold text-lg text-ink-900 mb-2">{{ $feature['title'] ?? '' }}</h3>
        <p class="text-[14px] text-ink-500 leading-relaxed">{{ $feature['text'] ?? '' }}</p>
        @if(!empty($feature['link_text']))
        <a href="{{ $feature['link'] ?? '#' }}" class="nav-scroll inline-flex items-center gap-1.5 mt-4 text-[13px] font-semibold text-brand hover:text-brand-dark">{{ $feature['link_text'] }} <i class="fa fa-angle-right"></i></a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@if($cfg['show_parallax'] ?? true)
@php $parallaxImg = HotelLandingSetting::imageUrl($cfg['parallax']['image'] ?? null, HotelLandingSetting::DEFAULT_PARALLAX); @endphp
<!-- ===== Parallax ===== -->
<section class="relative bg-fixed bg-center bg-cover" style="background-image:url('{{ $parallaxImg }}');">
  <div class="bg-ink-950/70">
    <div class="max-w-3xl mx-auto px-6 py-28 text-center text-white">
      <i class="fa fa-star-o text-brand text-3xl mb-5"></i>
      <h3 class="font-display text-3xl md:text-4xl font-bold mb-4">{{ $hotelName }}</h3>
      <p class="text-lg text-white/85">{{ $cfg['parallax']['text'] ?? 'Vive una experiencia inolvidable' }}</p>
      @if(!empty($cfg['parallax']['button_text']))
        <a href="{{ $cfg['parallax']['button_link'] ?: '#rooms-results' }}" class="nav-scroll hb-btn hb-btn-light hb-btn-lg mt-8">{{ $cfg['parallax']['button_text'] }} <i class="fa fa-angle-right"></i></a>
      @endif
    </div>
  </div>
</section>
@endif

@if(($cfg['show_gallery'] ?? true) && count($cfg['gallery'] ?? []))
<!-- ===== Galería ===== -->
<section class="hb-section" id="gallery">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Galería</span>
      <h2 class="hb-h2">{{ $cfg['gallery_heading'] ?? 'Galería' }}</h2>
    </div>
    <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      @foreach($cfg['gallery'] as $gi => $galleryImg)
        @php $gImg = HotelLandingSetting::imageUrl($galleryImg, HotelLandingSetting::DEFAULT_GALLERY); @endphp
        <button type="button" class="reveal group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-ink-100 {{ $gi === 0 ? 'col-span-2 row-span-2 md:aspect-square' : '' }}" style="--reveal-i:{{ $gi }}" data-lightbox="{{ $gImg }}">
          <img src="{{ $gImg }}" alt="Imagen {{ $gi + 1 }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
          <span class="absolute inset-0 bg-ink-950/0 group-hover:bg-ink-950/30 transition flex items-center justify-center">
            <i class="fa fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition"></i>
          </span>
        </button>
      @endforeach
    </div>
  </div>
</section>
@endif

@php
  $showTestimonials = ($cfg['show_testimonials'] ?? true) && count($cfg['testimonials'] ?? []);
  $showAbout        = ($cfg['show_about'] ?? true) && count($cfg['about']['tabs'] ?? []);
@endphp
@if($showTestimonials || $showAbout)
<section class="hb-section bg-white border-y border-ink-100">
  <div class="max-w-7xl mx-auto px-6 grid gap-14 lg:grid-cols-2">
    @if($showTestimonials)
    <div>
      <span class="hb-eyebrow mb-3">Opiniones</span>
      <h2 class="hb-h2 mb-8">{{ $cfg['testimonials_heading'] ?? 'Lo que opinan nuestros huéspedes' }}</h2>
      <div class="space-y-4">
        @foreach($cfg['testimonials'] as $t)
          @php $tImg = HotelLandingSetting::imageUrl($t['image'] ?? null, HotelLandingSetting::DEFAULT_REVIEW); @endphp
          <div class="reveal hb-card p-6 flex gap-4" style="--reveal-i:{{ $loop->index }}">
            <img src="{{ $tImg }}" alt="{{ $t['name'] ?? 'Huésped' }}" class="w-12 h-12 rounded-full object-cover shrink-0">
            <div>
              <div class="text-[#f4c542] text-sm mb-1.5">@for($s=0;$s<5;$s++)<i class="fa fa-star"></i>@endfor</div>
              <p class="text-[14px] text-ink-700 leading-relaxed mb-2">“{{ $t['text'] ?? '' }}”</p>
              <span class="text-[13px] font-semibold text-ink-900">{{ $t['name'] ?? '' }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($showAbout)
    @php $aboutImg = HotelLandingSetting::imageUrl($cfg['about']['image'] ?? null, HotelLandingSetting::DEFAULT_ABOUT); @endphp
    <div id="aboutTabs">
      <span class="hb-eyebrow mb-3">El hotel</span>
      <h2 class="hb-h2 mb-6">{{ $cfg['about_heading'] ?? 'Sobre el hotel' }}</h2>
      <div class="flex flex-wrap gap-2 mb-5">
        @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
          <button type="button" class="hb-tab px-4 py-2 rounded-full text-[13px] font-semibold transition {{ $ti === 0 ? 'bg-brand text-white' : 'bg-ink-100 text-ink-600 hover:bg-ink-200' }}" data-tab="{{ $ti }}">{{ $aboutTab['title'] ?? 'Pestaña' }}</button>
        @endforeach
      </div>
      @if($aboutImg)<img src="{{ $aboutImg }}" alt="{{ $cfg['about_heading'] ?? 'Hotel' }}" class="float-right ml-5 mb-3 w-40 rounded-2xl object-cover">@endif
      @foreach($cfg['about']['tabs'] as $ti => $aboutTab)
        <div class="hb-tab-pane text-[15px] text-ink-600 leading-relaxed {{ $ti === 0 ? '' : 'hidden' }}" data-pane="{{ $ti }}">{{ $aboutTab['content'] ?? '' }}</div>
      @endforeach
    </div>
    @endif
  </div>
</section>
<script>
(function () {
  var wrap = document.getElementById('aboutTabs');
  if (!wrap) return;
  var tabs = wrap.querySelectorAll('.hb-tab');
  var panes = wrap.querySelectorAll('.hb-tab-pane');
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      tabs.forEach(function (t) { t.classList.remove('bg-brand','text-white'); t.classList.add('bg-ink-100','text-ink-600'); });
      tab.classList.add('bg-brand','text-white'); tab.classList.remove('bg-ink-100','text-ink-600');
      panes.forEach(function (p) { p.classList.toggle('hidden', p.getAttribute('data-pane') !== tab.getAttribute('data-tab')); });
    });
  });
})();
</script>
@endif

@if(isset($blogPosts) && $blogPosts->count())
<!-- ===== Blog ===== -->
<section class="hb-section" id="blog">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="hb-eyebrow mb-3">Blog</span>
      <h2 class="hb-h2">Nuestro blog</h2>
      <p class="hb-sub">Novedades, consejos y noticias del hotel.</p>
    </div>
    <div class="grid gap-7 md:grid-cols-3">
      @foreach($blogPosts as $post)
        @php
          $postImage = $post->cover_image ?: HotelLandingSetting::DEFAULT_GALLERY;
          $postDate  = optional($post->published_at)->format('d/m/Y');
        @endphp
        <article class="reveal hb-card overflow-hidden group transition duration-300 ease-makai hover:shadow-panel hover:-translate-y-1" style="--reveal-i:{{ $loop->index }}">
          <a href="{{ url('reservas/blog/'.$post->slug) }}" class="relative block aspect-[16/10] bg-ink-100 overflow-hidden">
            <span class="absolute inset-0 bg-center bg-cover transition duration-500 group-hover:scale-105" style="background-image:url('{{ $postImage }}');"></span>
            @if($post->hasVideoCover())<i class="fa fa-play-circle absolute inset-0 m-auto w-max h-max text-white text-5xl drop-shadow-lg"></i>@endif
          </a>
          <div class="p-6">
            @if($postDate)<div class="text-[12px] font-semibold text-brand uppercase tracking-wide mb-2"><i class="fa fa-calendar"></i> {{ $postDate }}</div>@endif
            <h3 class="font-display font-semibold text-lg text-ink-900 leading-snug mb-2"><a href="{{ url('reservas/blog/'.$post->slug) }}" class="hover:text-brand transition">{{ $post->title }}</a></h3>
            <p class="text-[14px] text-ink-500 leading-relaxed mb-3">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 110) }}</p>
            <a href="{{ url('reservas/blog/'.$post->slug) }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-brand hover:text-brand-dark">Leer más <i class="fa fa-angle-right"></i></a>
          </div>
        </article>
      @endforeach
    </div>
    <div class="text-center mt-10">
      <a href="{{ url('reservas/blog') }}" class="hb-btn hb-btn-ghost hb-btn-lg">Ver todo el blog</a>
    </div>
  </div>
</section>
@endif

@if($cfg['show_cta'] ?? true)
<!-- ===== Call to action ===== -->
<section class="pb-4">
  <div class="max-w-7xl mx-auto px-6">
    <div class="rounded-3xl bg-brand text-white px-8 py-12 md:px-14 md:py-14 flex flex-col md:flex-row items-center gap-8 justify-between shadow-hover">
      <h2 class="font-display text-2xl md:text-3xl font-bold leading-snug max-w-2xl text-center md:text-left">{{ $cfg['cta_text'] ?? '¿Listo para tu próxima estancia? Reserva ahora en línea.' }}</h2>
      <a href="#reservation-form" class="nav-scroll hb-btn hb-btn-lg bg-white text-brand-dark hover:bg-ink-50 shrink-0">{{ $cfg['cta_button'] ?? 'Ver disponibilidad' }} <i class="fa fa-angle-right"></i></a>
    </div>
  </div>
</section>
@endif

@include('hotel::landing.partials.footer', ['onLanding' => true])

<!-- ===== Modal: Detalle de habitación (estilo unit modal Makai) ===== -->
<div class="hb-modal" id="roomDetailModal">
  <div class="hb-modal__backdrop" data-close></div>
  <div class="hb-modal__shell hb-modal__shell--tall">
    <div class="hb-modal__head">
      <div class="hb-modal__head-left">
        <span class="hb-modal__brand">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
        <span class="hb-modal__unit" id="detail-unit">Habitación</span>
        <span id="detail-badge"></span>
      </div>
      <button type="button" class="hb-modal__close" data-close aria-label="Cerrar"><i class="fa fa-times"></i></button>
    </div>
    <div id="detail-content" style="flex:1;min-height:0;display:flex;flex-direction:column;">
      <div class="hb-spinner" style="margin:auto;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
    </div>
  </div>
</div>

<!-- ===== Modal: Reservar ===== -->
<div class="hb-modal" id="reserveModal">
  <div class="hb-modal__backdrop" data-close></div>
  <div class="hb-modal__shell hb-modal__shell--sm">
    <div class="hb-modal__head">
      <div class="hb-modal__head-left">
        <span class="hb-modal__brand">{{ mb_strtoupper(mb_substr($hotelName,0,1)) }}</span>
        <span class="hb-modal__title">Completa tu reserva</span>
      </div>
      <button type="button" class="hb-modal__close" data-close aria-label="Cerrar"><i class="fa fa-times"></i></button>
    </div>
    <div class="hb-modal__body" style="padding:22px 24px;">
      <div id="reserve-message"></div>
      <div class="rounded-xl bg-brand-tint border border-brand-soft px-4 py-3 mb-5 text-[13px] text-ink-700" id="reserve-summary"></div>
      <form id="reserveform" class="space-y-4">
        <input type="hidden" name="room" id="r-room">
        <input type="hidden" name="checkin" id="r-checkin">
        <input type="hidden" name="checkout" id="r-checkout">
        <input type="hidden" name="checkin_time" id="r-checkin-time">
        <input type="hidden" name="checkout_time" id="r-checkout-time">
        <input type="hidden" name="adults" id="r-adults">
        <input type="hidden" name="children" id="r-children">

        <div id="reserve-dates" class="grid grid-cols-2 gap-4" style="display:none;">
          <div>
            <label class="hb-label" for="r-checkin-date"><i class="fa fa-calendar"></i> Entrada</label>
            <input type="date" class="hb-input" id="r-checkin-date">
          </div>
          <div>
            <label class="hb-label" for="r-checkout-date"><i class="fa fa-calendar"></i> Salida</label>
            <input type="date" class="hb-input" id="r-checkout-date">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-1">
            <label class="hb-label">Tipo doc.</label>
            <select class="hb-select" name="document_type" id="r-doctype">
              @foreach(($documentTypes ?? []) as $dt)
                <option value="{{ $dt['id'] }}">{{ $dt['description'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="hb-label">Número de documento</label>
            <div class="flex gap-2">
              <input type="text" class="hb-input" name="document_number" id="r-docnumber" placeholder="Nº de documento">
              <button class="hb-btn hb-btn-ghost shrink-0" type="button" id="r-doc-search"><i class="fa fa-search"></i></button>
            </div>
            <div class="text-[12px] mt-1.5 text-ink-500" id="r-doc-feedback"></div>
          </div>
        </div>
        <div>
          <label class="hb-label">Nombre / Razón social <span class="text-err">*</span></label>
          <input type="text" class="hb-input" name="name" id="r-name" required placeholder="Tu nombre completo">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="hb-label">Teléfono</label>
            <input type="text" class="hb-input" name="telephone" id="r-telephone" placeholder="Celular / teléfono">
          </div>
          <div>
            <label class="hb-label">E-mail</label>
            <input type="email" class="hb-input" name="email" id="r-email" placeholder="correo@ejemplo.com">
          </div>
        </div>
        <div>
          <label class="hb-label">Método de pago <span class="text-err">*</span></label>
          <select class="hb-select" name="payment_method" id="r-payment-method" required>
            <option value="Efectivo">Efectivo (pagar en recepción)</option>
            <option value="Yape">Yape</option>
            <option value="Plin">Plin</option>
            <option value="Transferencia bancaria">Transferencia bancaria</option>
            <option value="Tarjeta">Tarjeta (débito / crédito)</option>
          </select>
          <div class="text-[12px] mt-1.5 text-ink-500" id="r-payment-feedback">Elige cómo realizarás el pago de tu reserva.</div>
        </div>
        <div>
          <label class="hb-label">Comentarios / solicitudes especiales</label>
          <textarea class="hb-input" name="notes" id="r-notes" rows="2" placeholder="Ej: llegada tardía, cuna, piso alto..."></textarea>
        </div>
        <button type="submit" class="hb-btn hb-btn-primary hb-btn-block hb-btn-lg" id="r-submit"><i class="fa fa-calendar-check-o"></i> Confirmar reserva</button>
      </form>
    </div>
  </div>
</div>

<!-- Lightbox de galería -->
<div class="hb-lightbox" id="hbLightbox">
  <span class="hb-lightbox__close" data-lightbox-close>&times;</span>
  <img src="" alt="">
</div>

<!-- ===== Lógica de reservas ===== -->
<script type="text/javascript">
jQuery(function ($) {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var CURRENCY = 'S/';
    var CURRENT_ESTABLISHMENT = {!! json_encode($establishmentId ?? null) !!};
    var DATA = {
        rooms: {!! json_encode($rooms, JSON_UNESCAPED_UNICODE) !!},
        featured: {!! json_encode($featured, JSON_UNESCAPED_UNICODE) !!},
        // Catálogo agrupado por tipo de habitación (lo que se muestra por
        // defecto); se calcula en el servidor con el mismo criterio que usa la
        // búsqueda, para que ambas vistas coincidan.
        groups: {!! json_encode($roomGroups ?? [], JSON_UNESCAPED_UNICODE) !!}
    };
    var SEARCH = { checkin: null, checkout: null, checkin_time: '14:00', checkout_time: '12:00', adults: 1, children: 0, active: false };
    var PLACEHOLDER = '/landing-reservas/images/rooms/356x228.gif';

    // ---- Helpers de modal (animación de entrada estilo Makai) ----
    function openModal(id) {
        var $m = $('#' + id).addClass('is-open');
        $('body').addClass('hb-modal-open');
        // Reproduce la animación de apertura (mismo patrón que la web makai).
        var el = $m.get(0);
        el.classList.remove('is-opening');
        void el.offsetWidth;
        el.classList.add('is-opening');
    }
    function closeModal(el) { $(el).closest('.hb-modal').removeClass('is-open is-opening'); if (!$('.hb-modal.is-open').length) $('body').removeClass('hb-modal-open'); }
    $(document).on('click', '[data-close]', function () { closeModal(this); });
    // Cerrar al pulsar fuera del diálogo (sobre el fondo del modal).
    $(document).on('click', '.hb-modal', function (e) { if (e.target === this) { $(this).removeClass('is-open'); if (!$('.hb-modal.is-open').length) $('body').removeClass('hb-modal-open'); } });
    $(document).on('keydown', function (e) { if (e.key === 'Escape') { $('.hb-modal').removeClass('is-open'); $('body').removeClass('hb-modal-open'); } });

    // ---- utilidades ----
    function money(n) { return CURRENCY + ' ' + Number(n || 0).toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }
    function amenityIcon(a) {
        a = (a || '').toLowerCase();
        if (a.indexOf('wi') === 0 || a.indexOf('wifi') > -1 || a.indexOf('wi-fi') > -1) return 'fa-wifi';
        if (a.indexOf('tv') > -1) return 'fa-television';
        if (a.indexOf('aire') > -1 || a.indexOf('clima') > -1) return 'fa-snowflake-o';
        if (a.indexOf('desayun') > -1) return 'fa-coffee';
        if (a.indexOf('baño') > -1 || a.indexOf('bano') > -1) return 'fa-bath';
        if (a.indexOf('estacion') > -1 || a.indexOf('parking') > -1) return 'fa-car';
        if (a.indexOf('frigo') > -1 || a.indexOf('mini') > -1) return 'fa-glass';
        if (a.indexOf('caja') > -1) return 'fa-lock';
        if (a.indexOf('vista') > -1) return 'fa-eye';
        if (a.indexOf('jacuzzi') > -1 || a.indexOf('tina') > -1) return 'fa-tint';
        return 'fa-check-circle';
    }

    // ---- tarjeta de TIPO de habitación ----
    //
    // Es lo que ve el visitante por defecto. Un huésped no reserva "la 203":
    // reserva un tipo. Mostrar el listado plano obligaba a comparar decenas de
    // tarjetas casi idénticas, así que aquí va una tarjeta por tipo con lo que
    // realmente decide la reserva —foto, para cuántas personas, precio desde y
    // cuántas quedan— y el desglose por habitación solo si lo pide.
    function groupCard(group, index) {
        var img = group.main_image || PLACEHOLDER;

        var priceHtml = group.min_price > 0
            ? '<span class="rc-price">' + money(group.min_price) + '</span><small>/ noche</small>'
            : '<span class="text-[14px] font-semibold text-ink-500">Consultar tarifa</span>';

        var total = (group.total && group.nights)
            ? '<span class="rc-total">· ' + group.nights + ' noche(s): ' + money(group.total) + '</span>' : '';

        var stats = [];
        if (group.capacity) stats.push('<div class="rc-stat"><i class="fa fa-users"></i><span class="v">Hasta ' + group.capacity + '</span></div>');
        if (group.beds)     stats.push('<div class="rc-stat"><i class="fa fa-bed"></i><span class="v">' + esc(group.beds) + '</span></div>');
        if (group.size)     stats.push('<div class="rc-stat"><i class="fa fa-expand"></i><span class="v">' + group.size + ' m²</span></div>');
        var statsHtml = stats.length ? '<div class="rc-stats">' + stats.join('') + '</div>' : '';

        // Máximo 3 servicios: los suficientes para orientar, sin saturar.
        var amenities = (group.amenities || []).slice(0, 3);
        var amenitiesHtml = amenities.length
            ? '<div class="gc-amenities">' + amenities.map(function (a) {
                  return '<span class="gc-amenity"><i class="fa ' + amenityIcon(a) + '"></i>' + esc(a) + '</span>';
              }).join('') + '</div>'
            : '';

        var fav = group.featured ? '<span class="rc-fav"><i class="fa fa-star"></i> Destacada</span>' : '<span></span>';

        // Disponibilidad: tranquiliza (o urge) sin mentir.
        var avail = SEARCH.active
            ? '<span class="gc-avail' + (group.count <= 2 ? ' is-low' : '') + '">' +
                (group.count === 1 ? '¡Última habitación!' : group.count + ' disponibles') + '</span>'
            : '<span class="gc-avail">' + group.count + ' habitación(es)</span>';

        var desc = group.description
            ? '<p class="gc-desc">' + esc(group.description) + '</p>' : '';

        // Elegir habitación concreta solo tiene sentido si hay más de una.
        var pickBtn = group.count > 1
            ? '<button class="gc-btn-pick" data-group="' + index + '">' +
                '<i class="fa fa-th-list"></i> Ver las ' + group.count + '</button>'
            : '<button class="rc-btn-info btn-detail" data-id="' + group.default_room_id + '">Detalle</button>';

        return '' +
        '<div class="reveal rc gc" data-group="' + index + '">' +
          '<div class="rc-inner">' +
            '<div class="rc-img btn-detail" data-id="' + group.default_room_id + '">' +
              '<div class="bg" style="background-image:url(\'' + img + '\');"></div>' +
              '<div class="rc-chips">' + avail + fav + '</div>' +
            '</div>' +
          '</div>' +
          '<div class="rc-body">' +
            '<div class="rc-head">' +
              '<div class="rc-title-row"><span class="rc-name">' + esc(group.category) + '</span></div>' +
              desc +
              '<div class="rc-divider"></div>' +
              '<div class="rc-price-row">' + priceHtml + total + '</div>' +
            '</div>' +
            statsHtml +
            amenitiesHtml +
            '<div class="rc-actions">' +
              pickBtn +
              '<button class="rc-btn-cta btn-reserve" data-id="' + group.default_room_id + '">' +
                '<i class="fa fa-calendar-check-o"></i> Reservar</button>' +
            '</div>' +
            '<div class="gc-rooms" id="gc-rooms-' + index + '" hidden></div>' +
          '</div>' +
        '</div>';
    }

    // Fila compacta de una habitación concreta dentro de su tipo.
    function roomRow(room) {
        var price = room.min_price > 0 ? money(room.min_price) + ' / noche' : 'Consultar tarifa';
        var meta = [];
        if (room.floor) meta.push(esc(room.floor));
        if (room.capacity) meta.push(room.capacity + ' huésp.');

        return '<div class="gc-room">' +
                 '<div class="gc-room-info">' +
                   '<span class="gc-room-name">Habitación ' + esc(room.name) + '</span>' +
                   (meta.length ? '<span class="gc-room-meta">' + meta.join(' · ') + '</span>' : '') +
                 '</div>' +
                 '<span class="gc-room-price">' + price + '</span>' +
                 '<div class="gc-room-actions">' +
                   '<button class="gc-room-detail btn-detail" data-id="' + room.id + '" title="Ver detalle"><i class="fa fa-info"></i></button>' +
                   '<button class="gc-room-reserve btn-reserve" data-id="' + room.id + '">Reservar</button>' +
                 '</div>' +
               '</div>';
    }

    // Desplegar / plegar las habitaciones de un tipo.
    $(document).on('click', '.gc-btn-pick', function () {
        var idx = parseInt($(this).data('group'), 10);
        var group = (window.__lastGroups || [])[idx];
        if (!group) return;

        var $box = $('#gc-rooms-' + idx);
        var open = !$box.prop('hidden');

        if (open) {
            $box.prop('hidden', true);
            $(this).html('<i class="fa fa-th-list"></i> Ver las ' + group.count);
            return;
        }

        if (!$box.children().length) {
            $box.html('<div class="gc-rooms-title">Elige tu habitación</div>' +
                      group.rooms.map(roomRow).join(''));
        }
        $box.prop('hidden', false);
        $(this).html('<i class="fa fa-chevron-up"></i> Ocultar');
    });

    function renderGroups(groups, emptyReason) {
        var $grid = $('#rooms-grid');
        window.__lastGroups = groups || [];

        if (!groups || !groups.length) {
            // El texto nunca revela la ocupación del hotel: si no hay sitio se
            // dice en genérico. Solo se concreta cuando el motivo es el aforo
            // pedido, que es un dato de la propia búsqueda.
            var msg = emptyReason === 'capacity'
                ? 'No tenemos habitaciones con capacidad para ese número de huéspedes. Prueba a reducirlo o a reservar más de una habitación.'
                : 'No hay habitaciones disponibles para esas fechas.';
            $grid.html('<div class="text-center py-16 text-ink-400"><i class="fa fa-bed fa-3x"></i><p class="mt-4 text-[15px]">' + msg + '</p></div>');
            return;
        }

        $grid.html('<div class="rooms-grid">' +
                   groups.map(groupCard).join('') + '</div>');
        $grid.find('.reveal').each(function (i) { this.style.setProperty('--reveal-i', i % 6); });
        if (window.hbInitReveal) window.hbInitReveal();
    }

    // Agrupa en el navegador (mismo criterio que el backend) para el listado
    // inicial y para los filtros que no requieren volver a consultar.
    function groupLocally(list) {
        var byCat = {}, order = [];
        (list || []).forEach(function (r) {
            var cat = r.category || 'Habitación';
            if (!byCat[cat]) { byCat[cat] = []; order.push(cat); }
            byCat[cat].push(r);
        });

        return order.map(function (cat) {
            var rooms = byCat[cat].slice().sort(function (a, b) {
                return (a.min_price || 0) - (b.min_price || 0);
            });
            var rep = rooms.filter(function (r) { return r.main_image; })[0] || rooms[0];
            var prices = rooms.map(function (r) { return r.min_price || 0; }).filter(function (p) { return p > 0; });

            return {
                category: cat,
                count: rooms.length,
                min_price: prices.length ? Math.min.apply(null, prices) : 0,
                max_price: prices.length ? Math.max.apply(null, prices) : 0,
                nights: rep.nights || null,
                total: rooms.reduce(function (m, r) { return (r.total && (m === null || r.total < m)) ? r.total : m; }, null),
                capacity: Math.max.apply(null, rooms.map(function (r) { return r.capacity || 0; })),
                beds: rep.beds || null,
                size: rep.size || null,
                featured: rooms.some(function (r) { return r.featured; }),
                description: rep.short_description || rep.description || null,
                amenities: rep.amenities || [],
                main_image: rep.main_image || null,
                default_room_id: rooms[0].id,
                rooms: rooms,
            };
        });
    }

    function findRoom(id) {
        id = parseInt(id, 10);
        if (window.__lastList) {
            var r = window.__lastList.filter(function (x) { return x.id === id; })[0];
            if (r) return r;
        }
        return DATA.rooms.filter(function (x) { return x.id === id; })[0];
    }

    // Render inicial: tipos de habitación (el catálogo completo, agrupado).
    window.__lastList = DATA.rooms;
    renderGroups(DATA.groups && DATA.groups.length ? DATA.groups : groupLocally(DATA.rooms));

    // Filtros disponibles desde el primer momento (sin necesidad de buscar):
    // el visitante puede acotar por tipo y ordenar el catálogo completo.
    (function initFilters() {
        var cats = [];
        (DATA.rooms || []).forEach(function (r) {
            if (r.category && cats.indexOf(r.category) === -1) cats.push(r.category);
        });
        var $cat = $('#filterCategory').empty().append('<option value="">Todos los tipos</option>');
        cats.forEach(function (c) { $cat.append('<option value="' + esc(c) + '">' + esc(c) + '</option>'); });

        // Con un solo tipo no hay nada que filtrar ni ordenar.
        if (cats.length > 1) $('#resultsBar').removeAttr('hidden');
    })();

    // ---- Buscar disponibilidad ----
    //
    // Mejoras sobre la versión anterior:
    //  · La validación se muestra dentro del formulario (nada de alert()).
    //  · Los resultados llevan resumen, filtro por tipo y orden, sin repetir
    //    la búsqueda a mano.
    //  · Las habitaciones descartadas se muestran aparte CON el motivo.
    //  · Sin disponibilidad se proponen las fechas libres más cercanas.
    //  · La búsqueda queda en la URL (enlace compartible) y se restaura al
    //    volver a la página.

    function showSearchError(msg, $field) {
        var $box = $('#searchError');
        $('.hr-search__card .hb-input').removeClass('is-invalid');
        if (!msg) { $box.attr('hidden', true).empty(); return; }
        $box.removeAttr('hidden').html('<i class="fa fa-exclamation-circle"></i> ' + esc(msg));
        if ($field && $field.length) { $field.addClass('is-invalid').focus(); }
    }

    function scrollToResults() {
        var el = document.getElementById('rooms-results');
        if (!el) return;
        var top = el.getBoundingClientRect().top + window.scrollY - 76;
        window.scrollTo({ top: top, behavior: 'smooth' });
    }

    // Guarda la búsqueda en la URL para poder compartirla / recargar sin perderla.
    function persistSearch(s) {
        if (!window.history || !window.history.replaceState) return;
        var qs = new URLSearchParams(window.location.search);
        qs.set('checkin', s.checkin);
        qs.set('checkout', s.checkout);
        qs.set('adults', s.adults);
        if (s.children) qs.set('children', s.children); else qs.delete('children');
        window.history.replaceState(null, '', window.location.pathname + '?' + qs.toString() + '#rooms-results');
    }

    function renderSuggestions(suggestions) {
        var $box = $('#rooms-suggestions').empty();
        if (!suggestions || !suggestions.length) return;

        var items = suggestions.map(function (s) {
            return '<button type="button" class="hr-alt-btn" data-checkin="' + s.checkin + '" data-checkout="' + s.checkout + '">' +
                     '<b>' + esc(s.label) + '</b>' +
                     '<small>' + s.rooms + ' habitación(es)' + (s.min_price > 0 ? ' · desde ' + money(s.min_price) : '') + '</small>' +
                   '</button>';
        }).join('');

        $box.html(
            '<div class="hr-alt">' +
              '<h4><i class="fa fa-calendar-check-o"></i> Fechas cercanas con disponibilidad</h4>' +
              '<p>No hay habitaciones libres en las fechas elegidas, pero sí en estas:</p>' +
              '<div class="hr-alt-list">' + items + '</div>' +
            '</div>'
        );
    }

    // Al pulsar una fecha alternativa se relanza la búsqueda con ella.
    $(document).on('click', '.hr-alt-btn', function () {
        $('#checkin_date').val($(this).data('checkin'));
        $('#checkout_date').val($(this).data('checkout')).trigger('change');
        $('#searchform').trigger('submit');
    });

    function renderResultsBar(res) {
        $('#resultsBar').removeAttr('hidden');
        $('#resultsSummary').removeAttr('hidden');
        $('#chipDates').html('<i class="fa fa-calendar"></i> ' + esc(res.checkin) + ' → ' + esc(res.checkout) + ' · ' + res.nights + ' noche(s)');
        $('#chipGuests').html('<i class="fa fa-users"></i> ' + res.adults + ' adulto(s)' + (res.children ? ', ' + res.children + ' niño(s)' : ''));

        // Tipos disponibles para filtrar, conservando la selección actual.
        var $cat = $('#filterCategory');
        var current = $cat.val();
        $cat.empty().append('<option value="">Todos los tipos</option>');
        (res.categories || []).forEach(function (c) {
            $cat.append('<option value="' + esc(c) + '">' + esc(c) + '</option>');
        });
        if (current) $cat.val(current);
    }

    $('#btnEditSearch').on('click', function () {
        var el = document.getElementById('reservation-form');
        if (!el) return;
        window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY - 90, behavior: 'smooth' });
        $('#checkin_date').focus();
    });

    // Volver al listado completo (sin fechas ni filtros).
    $('#btnClearSearch').on('click', function () {
        SEARCH.active = false;
        $('#filterCategory').val('');
        $('#filterSort').val('featured');
        $('#resultsSummary').attr('hidden', true);
        $('#rooms-suggestions').empty();
        window.__lastList = DATA.rooms;
        renderGroups(DATA.groups && DATA.groups.length ? DATA.groups : groupLocally(DATA.rooms));
        $('#rooms-heading').text({!! json_encode($cfg['rooms_heading'] ?? 'Nuestras habitaciones') !!});
        $('#rooms-subheading').text({!! json_encode($cfg['rooms_subheading'] ?? 'Selecciona fechas para ver disponibilidad y precios.') !!});
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', window.location.pathname);
        }
    });

    // Cambiar tipo u orden relanza la búsqueda; sin búsqueda activa se filtra y
    // ordena el listado ya cargado en el navegador (respuesta instantánea).
    $('#filterCategory, #filterSort').on('change', function () {
        if (SEARCH.active) { runSearch({ silent: true }); return; }

        var cat = $('#filterCategory').val();
        var sort = $('#filterSort').val();
        var list = (DATA.rooms || []).slice();

        if (cat) list = list.filter(function (r) { return r.category === cat; });

        var groups = groupLocally(list);
        groups.sort(function (a, b) {
            if (sort === 'price_asc')     return a.min_price - b.min_price;
            if (sort === 'price_desc')    return b.min_price - a.min_price;
            if (sort === 'capacity_desc') return b.capacity - a.capacity;
            // Recomendadas: destacadas primero y, dentro, por precio.
            var f = (a.featured ? 0 : 1) - (b.featured ? 0 : 1);
            return f !== 0 ? f : a.min_price - b.min_price;
        });

        window.__lastList = list;
        renderGroups(groups);
        $('#rooms-heading').text(cat || {!! json_encode($cfg['rooms_heading'] ?? 'Nuestras habitaciones') !!});
    });

    function runSearch(opts) {
        opts = opts || {};
        var checkin = $('#checkin_date').val(), checkout = $('#checkout_date').val();
        var checkinTime = $('#checkin_time').val() || '14:00', checkoutTime = $('#checkout_time').val() || '12:00';

        if (!checkin || !checkout) {
            showSearchError('Selecciona las fechas de entrada y salida.', $(!checkin ? '#checkin_date' : '#checkout_date'));
            return;
        }
        if ((checkout + ' ' + checkoutTime) <= (checkin + ' ' + checkinTime)) {
            showSearchError('La salida debe ser posterior a la entrada.', $('#checkout_date'));
            return;
        }
        showSearchError(null);

        var $btn = $('#btn-search').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Buscando...');
        $('#rooms-grid').html('<div class="text-center py-16 text-ink-400"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-4">Buscando habitaciones disponibles...</p></div>');
        $('#rooms-suggestions').empty();

        $.post('/reservas/search', {
            checkin: checkin, checkout: checkout,
            checkin_time: checkinTime, checkout_time: checkoutTime,
            adults: $('#adults').val(), children: $('#children').val(),
            category: $('#filterCategory').val() || '',
            sort: $('#filterSort').val() || 'featured',
            establishment_id: CURRENT_ESTABLISHMENT
        }).done(function (res) {
            if (!res.success) {
                $('#rooms-grid').html('<div class="text-center py-16 text-ink-400">' + esc(res.message || 'No se pudo buscar.') + '</div>');
                return;
            }

            SEARCH = {
                checkin: checkin, checkout: checkout,
                checkin_time: checkinTime, checkout_time: checkoutTime,
                adults: parseInt($('#adults').val(), 10), children: parseInt($('#children').val(), 10),
                active: true
            };

            window.__lastList = res.rooms;
            renderGroups(
                res.groups && res.groups.length ? res.groups : groupLocally(res.rooms),
                res.empty_reason
            );
            renderResultsBar(res);
            renderSuggestions(res.suggestions);
            persistSearch(SEARCH);

            // El titular habla de TIPOS, que es lo que el visitante compara.
            // Se cuenta sobre lo realmente pintado (`__lastGroups`), no sobre
            // `res.groups`: si la respuesta no trae los grupos ya calculados,
            // renderGroups los agrupa en el navegador y el titular debe seguir
            // cuadrando con lo que se ve.
            var tipos = (window.__lastGroups || []).length;
            $('#rooms-heading').text(res.count > 0
                ? (tipos === 1
                    ? '1 tipo de habitación disponible'
                    : tipos + ' tipos de habitación disponibles')
                : 'Sin disponibilidad en esas fechas');
            $('#rooms-subheading').text('Del ' + res.checkin + ' ' + res.checkin_time + ' al ' + res.checkout + ' ' + res.checkout_time + ' · ' + res.nights + ' noche(s) · ' + res.adults + ' adulto(s)' + (res.children ? ', ' + res.children + ' niño(s)' : ''));

            if (!opts.silent) scrollToResults();
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo realizar la búsqueda.';
            $('#rooms-grid').html('<div class="text-center py-16 text-ink-400">' + esc(m) + '</div>');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Buscar');
        });
    }

    $('#searchform').on('submit', function (e) {
        e.preventDefault();
        runSearch();
    });

    // Enlace compartido con fechas (?checkin=...&checkout=...): buscar solo.
    (function restoreSharedSearch() {
        var qs = new URLSearchParams(window.location.search);
        if (qs.get('checkin') && qs.get('checkout')) {
            runSearch({ silent: true });
        }
    })();

    // ---- Detalle ----
    var DETAIL_SPINNER = '<div class="hb-spinner" style="margin:auto;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';

    $(document).on('click', '.btn-detail', function () {
        var id = $(this).data('id');
        $('#detail-unit').text('Habitación');
        $('#detail-badge').html('');
        $('#detail-content').html(DETAIL_SPINNER);
        openModal('roomDetailModal');
        var url = '/reservas/room/' + id + '?establishment_id=' + (CURRENT_ESTABLISHMENT || '');
        if (SEARCH.active) url += '&checkin=' + SEARCH.checkin + '&checkout=' + SEARCH.checkout + '&checkin_time=' + SEARCH.checkin_time + '&checkout_time=' + SEARCH.checkout_time;
        $.get(url).done(function (res) {
            if (!res.success) { $('#detail-content').html('<p class="text-err" style="margin:auto;">No se pudo cargar el detalle.</p>'); return; }
            renderDetail(res.room);
        }).fail(function () {
            $('#detail-content').html('<p class="text-err" style="margin:auto;">No se pudo cargar el detalle.</p>');
        });
    });

    function statBox(icon, value, label) {
        return '<div class="mt-stat"><div class="value"><i class="fa ' + icon + '"></i> ' + value + '</div><div class="label">' + label + '</div></div>';
    }

    function renderDetail(room) {
        // Cabecera del modal (unidad + estado), igual que el unit modal de makai.
        $('#detail-unit').text(room.category + ' · ' + room.name);
        if (typeof room.available !== 'undefined') {
            $('#detail-badge').html(room.available
                ? '<span class="hb-badge hb-badge-ok"><span class="dot"></span> Disponible</span>'
                : '<span class="hb-badge hb-badge-off"><span class="dot"></span> No disponible</span>');
        } else if (room.status === 'MANTENIMIENTO') {
            $('#detail-badge').html('<span class="hb-badge hb-badge-off"><span class="dot"></span> Mantenimiento</span>');
        } else {
            $('#detail-badge').html('');
        }

        var images = (room.images && room.images.length) ? room.images : [PLACEHOLDER];
        var thumbs = images.length > 1
            ? '<div class="mt-thumbs">' + images.map(function (u, i) { return '<img src="' + u + '" class="mt-thumb ' + (i===0?'active':'') + '" data-src="' + u + '">'; }).join('') + '</div>'
            : '';

        // Cajas de estadística (capacidad / camas / m² / piso).
        var stats = [];
        if (room.capacity) stats.push(statBox('fa-users', room.capacity, 'Huéspedes'));
        if (room.beds)     stats.push(statBox('fa-bed', esc(room.beds), 'Camas'));
        if (room.size)     stats.push(statBox('fa-expand', room.size + ' m²', 'Superficie'));
        if (room.floor)    stats.push(statBox('fa-building', esc(room.floor), 'Piso'));
        var statsHtml = stats.length ? '<div class="mt-stats">' + stats.join('') + '</div>' : '';

        var amenities = (room.amenities && room.amenities.length)
            ? '<div class="mt-divider"></div><h5 class="font-semibold text-ink-900 mb-2 text-[13px] uppercase tracking-wide">Servicios</h5><div class="flex flex-wrap gap-2">' + room.amenities.map(function (a) { return '<span class="hb-pill"><i class="fa ' + amenityIcon(a) + '"></i>' + esc(a) + '</span>'; }).join('') + '</div>'
            : '';

        var priceHtml = room.min_price > 0
            ? '<span class="mt-price">' + money(room.min_price) + '</span> <small>/ noche</small>'
            : '<span class="text-ink-500 font-semibold">Consultar tarifa</span>';
        var totalLine = (room.total && room.nights) ? '<div class="text-[13px] text-ink-600 mt-2">Total ' + room.nights + ' noche(s): <strong class="text-ink-900">' + money(room.total) + '</strong></div>' : '';

        var reserveDisabled = (typeof room.available !== 'undefined' && !room.available) || room.status === 'MANTENIMIENTO';

        var html =
            '<div class="hb-modal__grid" style="flex:1;">' +
              '<div class="hb-modal__left"><div class="hb-modal__left-inner">' +
                '<div class="hb-eyebrow mb-3">' + esc(room.category) + '</div>' +
                '<h3 class="font-display text-2xl font-bold text-ink-900 mb-3">' + esc(room.name) + '</h3>' +
                '<div class="mb-4">' + priceHtml + totalLine + '</div>' +
                (room.description || room.short_description ? '<p class="text-[14px] text-ink-600 leading-relaxed mb-4">' + esc(room.description || room.short_description) + '</p>' : '') +
                statsHtml +
                amenities +
                '<button class="hb-btn hb-btn-primary hb-btn-block hb-btn-lg btn-reserve mt-5" data-id="' + room.id + '"' + (reserveDisabled ? ' disabled' : '') + '><i class="fa fa-calendar-check-o"></i> ' + (reserveDisabled ? 'No disponible' : 'Reservar') + '</button>' +
              '</div></div>' +
              '<div class="hb-modal__right">' +
                '<div class="mt-gallery"><img id="detail-main" src="' + images[0] + '" alt="' + esc(room.name) + '"></div>' +
                thumbs +
              '</div>' +
            '</div>';
        $('#detail-content').html(html);
    }

    $(document).on('click', '.mt-thumb', function () {
        $('#detail-main').attr('src', $(this).data('src'));
        $('.mt-thumb').removeClass('active');
        $(this).addClass('active');
    });

    // ---- Reservar ----
    $(document).on('click', '.btn-reserve', function () {
        var room = findRoom($(this).data('id'));
        if (!room) return;
        $('#roomDetailModal').removeClass('is-open is-opening');
        openReserve(room);
    });

    // Contexto de la reserva en curso (para poder refrescar el resumen cuando el
    // usuario elige las fechas dentro del propio modal).
    var RESERVE_CTX = { room: null, adults: 1, children: 0 };

    function renderReserveSummary() {
        var room = RESERVE_CTX.room; if (!room) return;
        var checkin = $('#r-checkin').val(), checkout = $('#r-checkout').val();
        var checkinTime = $('#r-checkin-time').val() || '14:00', checkoutTime = $('#r-checkout-time').val() || '12:00';
        var adults = RESERVE_CTX.adults, children = RESERVE_CTX.children;
        var datesTxt = (checkin && checkout)
            ? 'Del <strong>' + checkin + ' ' + checkinTime + '</strong> al <strong>' + checkout + ' ' + checkoutTime + '</strong>'
            : '<span class="text-err">Elige las fechas de tu estancia.</span>';
        var priceTxt = room.min_price > 0 ? money(room.min_price) + ' / noche' : 'Consultar tarifa';
        $('#reserve-summary').html(
            '<div class="font-semibold text-ink-900">' + esc(room.category) + ' · ' + esc(room.name) + '</div>' +
            '<div class="mt-0.5">' + datesTxt + '</div>' +
            '<div class="mt-0.5">' + (adults || 1) + ' adulto(s)' + (children ? ', ' + children + ' niño(s)' : '') + ' · ' + priceTxt + '</div>'
        );
    }

    function openReserve(room) {
        $('#reserve-message').empty();
        $('#reserveform')[0].reset();
        $('#r-room').val(room.id);

        var checkin = SEARCH.active ? SEARCH.checkin : $('#checkin_date').val();
        var checkout = SEARCH.active ? SEARCH.checkout : $('#checkout_date').val();
        var checkinTime = SEARCH.active ? SEARCH.checkin_time : '14:00';
        var checkoutTime = SEARCH.active ? SEARCH.checkout_time : '12:00';
        var adults = SEARCH.active ? SEARCH.adults : parseInt($('#adults').val(), 10);
        var children = SEARCH.active ? SEARCH.children : parseInt($('#children').val(), 10);

        $('#r-checkin').val(checkin || '');
        $('#r-checkout').val(checkout || '');
        $('#r-checkin-time').val(checkinTime || '14:00');
        $('#r-checkout-time').val(checkoutTime || '12:00');
        $('#r-adults').val(adults || 1);
        $('#r-children').val(children || 0);

        RESERVE_CTX = { room: room, adults: adults || 1, children: children || 0 };

        // Si no llegaron fechas desde el buscador, se eligen aquí mismo.
        var needDates = !(checkin && checkout);
        var $dates = $('#reserve-dates');
        var d = new Date(), today = d.getFullYear() + '-' + ('0'+(d.getMonth()+1)).slice(-2) + '-' + ('0'+d.getDate()).slice(-2);
        $('#r-checkin-date').attr('min', today).val(checkin || '');
        $('#r-checkout-date').attr('min', checkin || today).val(checkout || '');
        if (needDates) { $dates.show(); } else { $dates.hide(); }

        renderReserveSummary();
        openModal('reserveModal');
    }

    // Sincroniza los selectores de fecha del modal con los campos ocultos.
    $('#r-checkin-date').on('change', function () {
        var v = $(this).val();
        $('#r-checkin').val(v || '');
        $('#r-checkout-date').attr('min', v || '');
        if (v && $('#r-checkout-date').val() && $('#r-checkout-date').val() < v) {
            $('#r-checkout-date').val('');
            $('#r-checkout').val('');
        }
        renderReserveSummary();
    });
    $('#r-checkout-date').on('change', function () {
        $('#r-checkout').val($(this).val() || '');
        renderReserveSummary();
    });

    function docLookupType(id) {
        id = String(id);
        if (id === '1') return 'dni';
        if (id === '6') return 'ruc';
        return null;
    }

    $('#r-doc-search').on('click', function () {
        var lookup = docLookupType($('#r-doctype').val());
        var num = $.trim($('#r-docnumber').val());
        var $fb = $('#r-doc-feedback').removeClass('text-err text-ok').addClass('text-ink-500');
        if (!lookup) { $fb.text('Este tipo de documento se ingresa manualmente.'); return; }
        if (!num) { $fb.text('Ingresa el número de documento.'); return; }
        $fb.html('<i class="fa fa-spinner fa-spin"></i> Consultando...');
        $.get('/reservas/document/' + lookup + '/' + num).done(function (res) {
            if (res.success && res.data) {
                var d = res.data;
                if (d.name) $('#r-name').val(d.name);
                $fb.removeClass('text-ink-500').addClass('text-ok').text('Datos encontrados.');
            } else {
                $fb.removeClass('text-ink-500').addClass('text-err').text(res.message || 'No se encontraron datos.');
            }
        }).fail(function (xhr) {
            var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo consultar.';
            $fb.removeClass('text-ink-500').addClass('text-err').text(m);
        });
    });

    $('#r-doctype').on('change', function () {
        var id = String($(this).val());
        var placeholders = { '1': 'DNI (8 dígitos)', '6': 'RUC (11 dígitos)', '7': 'Nº de pasaporte', '4': 'Nº de carnet de extranjería' };
        $('#r-docnumber').attr('placeholder', placeholders[id] || 'Nº de documento');
        $('#r-doc-search').toggle(!!docLookupType(id));
    }).trigger('change');

    $('#reserveform').on('submit', function (e) {
        e.preventDefault();
        var rCheckin = $('#r-checkin').val(), rCheckout = $('#r-checkout').val();
        if (!rCheckin || !rCheckout) {
            $('#reserve-dates').show();
            $('#reserve-message').html(alertHtml('danger', 'Elige las fechas de entrada y salida.'));
            return;
        }
        if (rCheckout <= rCheckin) {
            $('#reserve-message').html(alertHtml('danger', 'La fecha de salida debe ser posterior a la de entrada.'));
            return;
        }
        var $btn = $('#r-submit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
        $.post('/reservas/store', $(this).serialize() + '&establishment_id=' + (CURRENT_ESTABLISHMENT || ''))
            .done(function (html) {
                $('#reserve-message').html(html);
                if (html.indexOf('alert-success') !== -1) {
                    $('#reserveform')[0].reset();
                    if (SEARCH.active) { $('#searchform').trigger('submit'); }
                }
            })
            .fail(function (xhr) {
                $('#reserve-message').html(
                    (xhr.responseText && xhr.responseText.indexOf('alert') !== -1)
                        ? xhr.responseText
                        : alertHtml('danger', 'No se pudo registrar la reserva. Inténtalo de nuevo.'));
            })
            .always(function () { $btn.prop('disabled', false).html('<i class="fa fa-calendar-check-o"></i> Confirmar reserva'); });
    });

    function alertHtml(type, msg) {
        return '<div class="alert alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + esc(msg) + '</div>';
    }
    $(document).on('click', '.alert .close', function () { $(this).closest('.alert').remove(); });

    // ---- Galería lightbox ----
    $(document).on('click', '[data-lightbox]', function () {
        $('#hbLightbox img').attr('src', $(this).data('lightbox'));
        $('#hbLightbox').addClass('is-open'); $('body').addClass('hb-modal-open');
    });
    $(document).on('click', '[data-lightbox-close], #hbLightbox', function (e) {
        if (e.target !== this) return;
        $('#hbLightbox').removeClass('is-open'); $('body').removeClass('hb-modal-open');
    });
    $('[data-lightbox-close]').on('click', function () { $('#hbLightbox').removeClass('is-open'); $('body').removeClass('hb-modal-open'); });

    // ---- fechas mínimas ----
    (function initDates() {
        var localToday = function () {
            var d = new Date();
            var mm = ('0' + (d.getMonth() + 1)).slice(-2);
            var dd = ('0' + d.getDate()).slice(-2);
            return d.getFullYear() + '-' + mm + '-' + dd;
        };
        var today = localToday();
        $('#checkin_date').attr('min', today).on('change', function () {
            var v = $(this).val();
            $('#checkout_date').attr('min', v || today);
            if ($('#checkout_date').val() && $('#checkout_date').val() < v) {
                $('#checkout_date').val(v);
            }
        });
        $('#checkout_date').attr('min', today);
    })();
});
</script>

</body>
</html>
