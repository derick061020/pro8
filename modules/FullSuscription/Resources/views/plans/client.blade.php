@extends('tenant.layouts.web')

@section('content')

    <full-suscription-plans-client
        :plan-id="'{{$plan_id}}'"
        :public-key="{{json_encode($public_key)}}"
        :initial-plan="{{json_encode($plan_data)}}"
        :company="{{json_encode($company)}}">
    </full-suscription-plans-client>

@endsection
