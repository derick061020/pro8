@extends('tenant.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}"/>
@endpush

@section('content')
    <tenant-pos-fast
      :configuration="{{ $configuration}}"
      :soap-company="{{ json_encode($soap_company) }}"
      :business-turns="{{ $business_turns }}"
      :type-user="{{json_encode(Auth::user()->type)}}"
      :is-print="{{json_encode($configuration->auto_print)}}">
    </tenant-pos-fast>
@endsection
