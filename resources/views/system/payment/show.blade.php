@extends('system.guest-register.layouts.main')

@push('styles')
<style>
    body { background-color: #f6f6f6; }
    .payment-public {
        min-height: 100vh;
        padding: 3rem 1rem;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }
    .payment-public__container { width: 100%; max-width: 520px; }
    .payment-public__header { text-align: center; margin-bottom: 2rem; }
    .payment-public__company {
        font-family: 'Roboto', sans-serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: #042a42;
        margin: 0 0 0.25rem 0;
    }
    .payment-public__ruc {
        font-size: 0.9rem;
        color: #8a96a3;
        margin: 0;
    }
    .payment-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        position: relative;
    }
    .payment-card::before {
        content: '';
        display: block;
        height: 8px;
        background: linear-gradient(90deg, #ff8a3d, #ffb547);
    }
    .payment-card__badge {
        text-align: center;
        padding: 1.5rem 1.5rem 0.5rem;
    }
    .payment-card__order {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: #fff7e6;
        color: #d97706;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.04em;
    }
    .payment-card__title {
        text-align: center;
        font-family: 'Roboto', sans-serif;
        font-weight: 800;
        font-size: 1.75rem;
        color: #042a42;
        margin: 1rem 0 1.5rem;
        letter-spacing: 0.02em;
    }
    .payment-card__amount-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem 1.5rem;
        border-bottom: 1px solid #ebebeb;
    }
    .payment-card__amount { display: flex; align-items: baseline; gap: 0.25rem; }
    .payment-card__currency {
        font-size: 1.1rem;
        color: #042a42;
        font-weight: 600;
    }
    .payment-card__value {
        font-size: 3rem;
        font-weight: 800;
        color: #042a42;
        line-height: 1;
    }
    .payment-card__state {
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .payment-card__state--pending { background: #fff7e6; color: #d97706; }
    .payment-card__state--paid { background: #e6f7ee; color: #16a34a; }

    .payment-card__details {
        list-style: none;
        padding: 1.25rem 1.5rem;
        margin: 0;
        border-bottom: 1px solid #ebebeb;
    }
    .payment-card__detail {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.5rem 0;
        gap: 1rem;
    }
    .payment-card__detail-label {
        color: #8a96a3;
        font-size: 0.85rem;
    }
    .payment-card__detail-label i { margin-right: 0.4rem; }
    .payment-card__state i { margin-right: 0.3rem; }
    .payment-card__detail-value {
        color: #042a42;
        font-size: 0.9rem;
        font-weight: 500;
        text-align: right;
    }
    .payment-card__checkout { padding: 1.5rem; }
    .payment-card__placeholder {
        text-align: center;
        color: #8a96a3;
        font-size: 0.9rem;
        margin: 0;
    }
    .payment-card__paid-msg {
        text-align: center;
        color: #16a34a;
        font-weight: 600;
        font-size: 1rem;
        margin: 0;
        padding: 1rem 0;
    }
</style>
@endpush

@section('content')
<section class="payment-public">
    <div class="payment-public__container">

        <header class="payment-public__header">
            <h1 class="payment-public__company">{{ $client['name'] ?? 'Cliente' }}</h1>
            @if(!empty($client['number']))
                <p class="payment-public__ruc">RUC {{ $client['number'] }}</p>
            @endif
        </header>

        <article class="payment-card">
            <div class="payment-card__badge">
                <span class="payment-card__order">RECORDATORIO DE PAGO · #{{ $order['order'] }}</span>
            </div>

            <h2 class="payment-card__title">{{ strtoupper($order['description']) }}</h2>

            <div class="payment-card__amount-row">
                <div class="payment-card__amount">
                    <span class="payment-card__currency">S/</span>
                    <span class="payment-card__value">{{ number_format($order['amount'], 0) }}</span>
                </div>
                <span class="payment-card__state payment-card__state--{{ $order['is_paid'] ? 'paid' : 'pending' }}">
                    @if($order['is_paid'])
                        <i class="fa fa-check"></i> Pagado
                    @else
                        <i class="fa fa-clock"></i> {{ $order['state'] }}
                    @endif
                </span>
            </div>

            <ul class="payment-card__details">
                @if($order['date_of_due'])
                    <li class="payment-card__detail">
                        <span class="payment-card__detail-label"><i class="fa fa-calendar"></i> Vencimiento</span>
                        <span class="payment-card__detail-value">{{ $order['date_of_due'] }}</span>
                    </li>
                @endif
                <li class="payment-card__detail">
                    <span class="payment-card__detail-label"><i class="fa fa-user"></i> Cliente</span>
                    <span class="payment-card__detail-value">{{ $client['name'] }}</span>
                </li>
            </ul>

            <div class="payment-card__checkout">
                @if($order['is_paid'])
                    <p class="payment-card__paid-msg">Esta orden ya fue pagada. Gracias.</p>
                @else
                    <checkout-guest
                        :payment-uuid="{{ json_encode($order['uuid']) }}"
                        :amount="{{ (float) $order['amount'] }}"
                        :description="{{ json_encode($order['description']) }}"
                        :order-number="{{ json_encode($order['order']) }}"
                        :customer-email="{{ json_encode($client['email'] ?? '') }}"
                        :customer-name="{{ json_encode($client['name'] ?? '') }}"
                    ></checkout-guest>
                @endif
            </div>
        </article>

    </div>
</section>

@endsection
