@extends('ecommerce::layouts.layout_ecommerce_cart.index')

@section('content')

<div id="app">
    <div class="thankyou-page">
        <div class="ty-card">
            <div class="ty-badge"><div class="ring">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline class="ty-check" points="20 6 9 17 4 12"/></svg>
            </div></div>
            <h1>¡Gracias por tu compra!</h1>
            <p class="ty-sub">Tu pedido fue registrado con éxito. Te enviaremos los detalles y coordinaremos la entrega contigo.</p>
            <div class="ty-order"><span>N° de pedido:</span> <b>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</b></div>
            <div class="ty-info">
                <div class="ti-row"><span class="lbl">Método de entrega</span><span class="val">{{ $deliveryLabel }}</span></div>
                <div class="ti-row"><span class="lbl">Forma de pago</span><span class="val">{{ $paymentLabel }}</span></div>
                <div class="ti-row"><span class="lbl">Productos</span><span class="val">{{ $itemsCount }}</span></div>
                <div class="ti-row ti-total"><span class="lbl">Total</span><span class="val">S/ {{ number_format($order->total, 2) }}</span></div>
            </div>
            <div class="ty-note">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>@if($isPickup)Acércate a la sucursal elegida para recoger tu pedido. Te avisaremos cuando esté listo.@else Coordinaremos la entrega contigo. Te contactaremos por el teléfono registrado para concretar tu pedido.@endif</span>
            </div>
            <div class="ty-btns">
                <a href="{{ route('tenant_order_list') }}" class="pay-btn second-btn">Ver mis pedidos</a>
                <a href="{{ route('tenant.ecommerce.index') }}" class="pay-btn">Seguir comprando</a>
            </div>
        </div>
    </div>
</div>

@endsection
