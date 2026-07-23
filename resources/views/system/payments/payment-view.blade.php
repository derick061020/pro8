@extends('system.layouts.web')

@push('styles')
<style>
    /* Esta vista pública no usa sidebar: ocupar todo el ancho y centrar */
    .inner-wrapper { display: block !important; }
    #main-wrapper {
        margin-left: 0 !important;
        width: 100% !important;
        float: none !important;
    }
</style>
@endpush

@section('content')

    <system-payments-view-index
        :payment_order='@json($payment_order)'
        :client='@json($client)'
        :plan='@json($plan)'
    ></system-payments-view-index>

@endsection