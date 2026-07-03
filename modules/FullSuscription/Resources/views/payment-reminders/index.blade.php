@extends('tenant.layouts.app')

@section('content')

    <full-suscription-payment-reminders
        :configuration="{{ \App\Models\Tenant\Configuration::getPublicConfig() }}"
        :date="'{{ \Carbon\Carbon::now()->format('Y-m-d') }}'">
    </full-suscription-payment-reminders>

@endsection
