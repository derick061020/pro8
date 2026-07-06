@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-landing-settings
        :establishments='@json($establishments)'
        :user-type="'{{ $userType }}'"
        :establishment-id="{{ $establishmentId }}"
    ></tenant-hotel-landing-settings>
@endsection
