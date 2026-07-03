@extends('tenant.layouts.web')

@section('content')

    <full-suscription-pending-payments-view-order
        :person="{{ json_encode($person) }}"
        :suscription="{{ json_encode($suscription) }}"
        :order="{{ json_encode($order) }}"
        :company="{{ json_encode($company) }}"
    >
    </full-suscription-pending-payments-view-order>

@endsection
