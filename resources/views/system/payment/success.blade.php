@extends('system.guest-register.layouts.main')

@push('styles')
<style>
    body { background-color: #f6f6f6; }
    .payment-success {
        min-height: 100vh;
        padding: 3rem 1rem;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .payment-success__container { width: 100%; max-width: 520px; }
    .payment-success__card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        text-align: center;
        padding: 2.5rem 2rem;
    }
    .payment-success__icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #e6f7ee;
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    .payment-success__title {
        font-family: 'Roboto', sans-serif;
        color: #042a42;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0 0 0.5rem 0;
    }
    .payment-success__order {
        color: #8a96a3;
        font-size: 0.9rem;
        margin: 0 0 1.5rem 0;
    }
    .payment-success__amount {
        font-size: 2rem;
        font-weight: 800;
        color: #042a42;
        margin: 0 0 1.5rem 0;
    }
    .payment-success__next {
        background: #f6f6f6;
        border: 1px solid #ebebeb;
        border-radius: 8px;
        padding: 1rem;
        text-align: left;
        font-size: 0.9rem;
        color: #042a42;
    }
    .payment-success__next strong { display: block; margin-bottom: 0.4rem; }
</style>
@endpush

@section('content')
<section class="payment-success">
    <div class="payment-success__container">
        <div class="payment-success__card">
            <div class="payment-success__icon"><i class="fa fa-check"></i></div>
            <h1 class="payment-success__title">¡Pago confirmado!</h1>
            <p class="payment-success__order">Orden #{{ $order_number }}</p>
            <p class="payment-success__amount">S/ {{ number_format($amount, 2) }}</p>

            @if($is_autoregistro)
                <div class="payment-success__next">
                    <strong><i class="fa fa-envelope"></i> Siguiente paso: verifica tu correo</strong>
                    Revisa la bandeja de entrada de <strong>{{ $client_email }}</strong> y haz clic en el botón
                    <em>"Confirmar mi correo"</em> para activar tu cuenta y comenzar a usar la plataforma.
                </div>
            @else
                <div class="payment-success__next">
                    <strong><i class="fa fa-check-circle"></i> Tu pago se registró correctamente.</strong>
                    Gracias por mantener tu servicio al día. Puedes cerrar esta ventana.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
