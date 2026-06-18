<?php
    $base = '/landing-reservas';
    $hotelName = $establishment->description ?? ($configuration->business_name ?? 'Hotel');
    $hotelPhone = $establishment->telephone ?? '';
    $hotelEmail = $establishment->email ?? '';
    $hotelAddress = $establishment->address ?? '';
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
<meta charset="utf-8">
<title>{{ $hotelName }} · Reservas</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="shortcut icon" href="{{ $base }}/favicon.ico">

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ $base }}/css/animate.css">
<link rel="stylesheet" href="{{ $base }}/css/bootstrap.css">
<link rel="stylesheet" href="{{ $base }}/css/font-awesome.min.css">
<link rel="stylesheet" href="{{ $base }}/css/owl.carousel.css">
<link rel="stylesheet" href="{{ $base }}/css/owl.theme.css">
<link rel="stylesheet" href="{{ $base }}/css/smoothness/jquery-ui-1.10.4.custom.min.css">
<link rel="stylesheet" href="{{ $base }}/css/theme.css">
<link rel="stylesheet" href="{{ $base }}/css/colors/turquoise.css">
<link rel="stylesheet" href="{{ $base }}/css/responsive.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600,700">

<script type="text/javascript" src="{{ $base }}/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="{{ $base }}/js/bootstrap.min.js"></script>
<script type="text/javascript" src="{{ $base }}/js/jquery-ui-1.10.4.custom.min.js"></script>

<style>
    #lp-hero { background: linear-gradient(rgba(26,188,156,.85), rgba(44,62,80,.9)), url('{{ $base }}/images/parallax/1900x911.gif') center/cover no-repeat; color:#fff; padding:120px 0 90px; text-align:center; }
    #lp-hero h1 { color:#fff; font-weight:700; font-size:46px; margin-bottom:10px; }
    #lp-hero p { font-size:18px; opacity:.9; }
    #reservation-form { background:#1abc9c; padding:22px 0; }
    #reservation-form label { color:#fff; font-weight:600; }
    .lp-room-card { border:1px solid #eee; border-radius:6px; overflow:hidden; margin-bottom:30px; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); transition:.2s; height:100%; }
    .lp-room-card:hover { box-shadow:0 6px 18px rgba(0,0,0,.15); }
    .lp-room-card .lp-room-img { height:200px; background:#34495e center/cover no-repeat; position:relative; }
    .lp-room-card .lp-room-img .lp-cat { position:absolute; bottom:0; left:0; background:#1abc9c; color:#fff; padding:4px 12px; font-size:12px; text-transform:uppercase; letter-spacing:1px; }
    .lp-room-body { padding:18px; }
    .lp-room-body h4 { margin:0 0 6px; font-weight:700; }
    .lp-price { color:#1abc9c; font-size:22px; font-weight:700; }
    .lp-price small { color:#999; font-size:13px; font-weight:400; }
    .lp-muted { color:#999; font-size:13px; min-height:38px; }
    .lp-section { padding:60px 0; }
    #lp-alert { display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%); z-index:3000; min-width:320px; }
    .lp-foot { background:#2c3e50; color:#bdc3c7; padding:30px 0; text-align:center; }
    .lp-foot a { color:#1abc9c; }
</style>
</head>
<body>

<div id="lp-alert" class="alert"></div>

<!-- Top header -->
<div id="top-header">
  <div class="container">
    <div class="row">
      <div class="col-xs-7">
        <div class="th-text pull-left">
          @if($hotelPhone)<div class="th-item"><a href="tel:{{ $hotelPhone }}"><i class="fa fa-phone"></i> {{ $hotelPhone }}</a></div>@endif
          @if($hotelEmail)<div class="th-item"><a href="mailto:{{ $hotelEmail }}"><i class="fa fa-envelope"></i> {{ $hotelEmail }}</a></div>@endif
        </div>
      </div>
      <div class="col-xs-5">
        <div class="th-text pull-right">
          @if($hotelAddress)<div class="th-item"><i class="fa fa-map-marker"></i> {{ $hotelAddress }}</div>@endif
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
        <a href="#" class="navbar-brand">
          @if(!empty($establishment->logo))
            <img src="{{ $establishment->logo }}" alt="{{ $hotelName }}" style="height:44px;">
          @else
            <strong style="font-size:22px;color:#1abc9c;">{{ $hotelName }}</strong>
          @endif
        </a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#lp-rooms">Habitaciones</a></li>
          <li><a href="#reservation-form">Reservar</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<!-- Hero -->
<section id="lp-hero">
  <div class="container">
    <h1>Bienvenido a {{ $hotelName }}</h1>
    <p>Reserva tu habitación de forma rápida y segura.</p>
  </div>
</section>

<!-- Availability / Reservation form -->
<section id="reservation-form">
  <div class="container">
    <form class="form-inline" id="lp-availability-form">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group" style="display:block;">
            <label>Entrada (Check-in)</label>
            <input type="text" name="input_date" id="lp-checkin" class="form-control" style="width:100%;" placeholder="Fecha de entrada" readonly>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group" style="display:block;">
            <label>Salida (Check-out)</label>
            <input type="text" name="output_date" id="lp-checkout" class="form-control" style="width:100%;" placeholder="Fecha de salida" readonly>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group" style="display:block;">
            <label>Adultos</label>
            <select name="adults" id="lp-adults" class="form-control" style="width:100%;">
              @for($i=1;$i<=6;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group" style="display:block;">
            <label>Niños</label>
            <select name="children" id="lp-children" class="form-control" style="width:100%;">
              @for($i=0;$i<=4;$i++)<option value="{{ $i }}">{{ $i }}</option>@endfor
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <label style="display:block;">&nbsp;</label>
          <button type="submit" class="btn btn-default btn-block">Ver disponibilidad</button>
        </div>
      </div>
    </form>
  </div>
</section>

<!-- Rooms -->
<section class="lp-section" id="lp-rooms">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 text-center" style="margin-bottom:30px;">
        <h2 class="lined-heading"><span>Nuestras habitaciones</span></h2>
      </div>
    </div>
    <div class="row" id="lp-rooms-grid">
      @forelse($rooms as $room)
        <div class="col-sm-4 lp-room-col" data-room-id="{{ $room['id'] }}">
          <div class="lp-room-card">
            <div class="lp-room-img" style="background-image:url('{{ $base }}/images/rooms/356x228.gif');">
              <span class="lp-cat">{{ $room['category'] }}</span>
            </div>
            <div class="lp-room-body">
              <h4>{{ $room['name'] }}</h4>
              <p class="lp-muted">{{ $room['description'] ?: 'Habitación cómoda y equipada para tu estancia.' }}</p>
              <div class="lp-price">
                @if($room['min_price'] > 0)
                  S/ {{ number_format($room['min_price'], 2) }} <small>/ noche</small>
                @else
                  <small>Consultar tarifa</small>
                @endif
              </div>
              <button type="button" class="btn btn-primary btn-block lp-reserve-btn"
                      data-room-id="{{ $room['id'] }}"
                      data-room-name="{{ $room['name'] }}"
                      data-room-cat="{{ $room['category'] }}"
                      data-room-price="{{ $room['min_price'] }}"
                      data-room-rates='@json($room['rates'])'
                      style="margin-top:10px;">Reservar</button>
            </div>
          </div>
        </div>
      @empty
        <div class="col-sm-12 text-center">
          <p class="lp-muted">No hay habitaciones publicadas en este momento.</p>
        </div>
      @endforelse
    </div>
  </div>
</section>

<!-- Booking modal -->
<div class="modal fade" id="lp-booking-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="lp-booking-form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Reservar <span id="lp-modal-room"></span></h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="hotel_room_id" id="lp-room-id">
          <div class="row">
            <div class="col-sm-6 form-group">
              <label>Fecha de entrada *</label>
              <input type="text" name="input_date" id="lp-b-checkin" class="form-control" readonly>
            </div>
            <div class="col-sm-6 form-group">
              <label>Fecha de salida *</label>
              <input type="text" name="output_date" id="lp-b-checkout" class="form-control" readonly>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group">
              <label>Adultos *</label>
              <input type="number" name="adults" id="lp-b-adults" class="form-control" min="1" value="1">
            </div>
            <div class="col-sm-6 form-group">
              <label>Niños</label>
              <input type="number" name="children" id="lp-b-children" class="form-control" min="0" value="0">
            </div>
          </div>
          <div class="form-group" id="lp-rate-wrap" style="display:none;">
            <label>Tarifa</label>
            <select name="hotel_rate_id" id="lp-b-rate" class="form-control"></select>
          </div>
          <hr>
          <div class="form-group">
            <label>Nombre completo *</label>
            <input type="text" name="customer_name" class="form-control" required>
          </div>
          <div class="row">
            <div class="col-sm-6 form-group">
              <label>Correo electrónico *</label>
              <input type="email" name="customer_email" class="form-control" required>
            </div>
            <div class="col-sm-6 form-group">
              <label>Teléfono *</label>
              <input type="text" name="customer_phone" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label>Documento (DNI/RUC)</label>
            <input type="text" name="customer_doc" class="form-control">
          </div>
          <div class="form-group">
            <label>Comentarios</label>
            <textarea name="notes" class="form-control" rows="2" maxlength="250"></textarea>
          </div>
          <p class="lp-muted" id="lp-estimate"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="lp-submit-btn">Confirmar reserva</button>
        </div>
      </form>
    </div>
  </div>
</div>

<footer class="lp-foot">
  <div class="container">
    <p>&copy; {{ date('Y') }} {{ $hotelName }}. Reservas en línea.</p>
  </div>
</footer>

<script>
(function ($) {
    var CSRF = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF } });

    var today = new Date();
    function dpOpts(extra) {
        return $.extend({ dateFormat: 'yy-mm-dd', minDate: 0 }, extra || {});
    }

    function showAlert(type, msg) {
        $('#lp-alert').removeClass('alert-success alert-danger alert-info')
            .addClass('alert-' + type).html(msg).fadeIn();
        clearTimeout(window._lpAlertT);
        window._lpAlertT = setTimeout(function () { $('#lp-alert').fadeOut(); }, 5000);
    }

    // Availability date pickers (top form)
    $('#lp-checkin').datepicker(dpOpts({
        onSelect: function (d) {
            var next = $(this).datepicker('getDate'); next.setDate(next.getDate() + 1);
            $('#lp-checkout').datepicker('option', 'minDate', next);
        }
    }));
    $('#lp-checkout').datepicker(dpOpts());

    // Booking modal date pickers
    $('#lp-b-checkin').datepicker(dpOpts({
        onSelect: function () {
            var next = $(this).datepicker('getDate'); next.setDate(next.getDate() + 1);
            $('#lp-b-checkout').datepicker('option', 'minDate', next);
            updateEstimate();
        }
    }));
    $('#lp-b-checkout').datepicker(dpOpts({ onSelect: updateEstimate }));

    var currentRates = [];

    function nightsBetween(a, b) {
        if (!a || !b) return 0;
        var d1 = new Date(a), d2 = new Date(b);
        var n = Math.round((d2 - d1) / 86400000);
        return n > 0 ? n : 0;
    }

    function selectedPrice() {
        if (!currentRates.length) return 0;
        var rid = $('#lp-b-rate').val();
        var found = currentRates.filter(function (r) { return String(r.hotel_rate_id) === String(rid); })[0];
        return found ? parseFloat(found.price) : parseFloat(currentRates[0].price || 0);
    }

    function updateEstimate() {
        var n = nightsBetween($('#lp-b-checkin').val(), $('#lp-b-checkout').val());
        var price = selectedPrice();
        if (n > 0 && price > 0) {
            $('#lp-estimate').text('Estimado: ' + n + ' noche(s) × S/ ' + price.toFixed(2) + ' = S/ ' + (n * price).toFixed(2));
        } else {
            $('#lp-estimate').text('');
        }
    }
    $('#lp-b-rate').on('change', updateEstimate);

    // Availability search
    $('#lp-availability-form').on('submit', function (e) {
        e.preventDefault();
        var ci = $('#lp-checkin').val(), co = $('#lp-checkout').val();
        if (!ci || !co) { showAlert('danger', 'Selecciona fechas de entrada y salida.'); return; }
        $.post('/reservas/availability', { input_date: ci, output_date: co })
            .done(function (res) {
                var ids = {};
                (res.data || []).forEach(function (r) { ids[r.id] = true; });
                var available = 0;
                $('.lp-room-col').each(function () {
                    var rid = $(this).data('room-id');
                    if (ids[rid]) { $(this).show(); available++; }
                    else { $(this).hide(); }
                });
                // Prefill booking dates/guests
                $('#lp-b-checkin').val(ci); $('#lp-b-checkout').val(co);
                $('#lp-b-adults').val($('#lp-adults').val());
                $('#lp-b-children').val($('#lp-children').val());
                showAlert('info', available + ' habitación(es) disponible(s) del ' + ci + ' al ' + co + '.');
                $('html,body').animate({ scrollTop: $('#lp-rooms').offset().top - 60 }, 400);
            })
            .fail(function (xhr) {
                showAlert('danger', (xhr.responseJSON && xhr.responseJSON.message) || 'Error al consultar disponibilidad.');
            });
    });

    // Open booking modal
    $(document).on('click', '.lp-reserve-btn', function () {
        var $b = $(this);
        $('#lp-room-id').val($b.data('room-id'));
        $('#lp-modal-room').text($b.data('room-cat') + ' ' + $b.data('room-name'));
        currentRates = $b.data('room-rates') || [];
        var $rate = $('#lp-b-rate').empty();
        if (currentRates.length) {
            currentRates.forEach(function (r) {
                $rate.append($('<option>').val(r.hotel_rate_id).text(r.description + ' - S/ ' + parseFloat(r.price).toFixed(2)));
            });
            $('#lp-rate-wrap').show();
        } else {
            $('#lp-rate-wrap').hide();
        }
        // carry over availability form values if present
        if ($('#lp-checkin').val()) $('#lp-b-checkin').val($('#lp-checkin').val());
        if ($('#lp-checkout').val()) $('#lp-b-checkout').val($('#lp-checkout').val());
        $('#lp-b-adults').val($('#lp-adults').val() || 1);
        $('#lp-b-children').val($('#lp-children').val() || 0);
        updateEstimate();
        $('#lp-booking-modal').modal('show');
    });

    // Submit booking
    $('#lp-booking-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#lp-submit-btn');
        $btn.prop('disabled', true).text('Enviando...');
        $.post('/reservas/store', $(this).serialize())
            .done(function (res) {
                $('#lp-booking-modal').modal('hide');
                $('#lp-booking-form')[0].reset();
                $('#lp-estimate').text('');
                showAlert('success', res.message || 'Reserva registrada.');
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo registrar la reserva.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.keys(xhr.responseJSON.errors).map(function (k) {
                        return xhr.responseJSON.errors[k][0];
                    }).join('<br>');
                }
                showAlert('danger', msg);
            })
            .always(function () {
                $btn.prop('disabled', false).text('Confirmar reserva');
            });
    });
})(jQuery);
</script>
</body>
</html>
