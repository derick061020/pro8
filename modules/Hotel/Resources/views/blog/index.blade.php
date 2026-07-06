@extends('tenant.layouts.app')

@section('content')
    <tenant-hotel-blog
        :posts='@json($posts)'
        :establishments='@json($establishments)'
        :user-type="'{{ $userType }}'"
        :establishment-id="{{ $establishmentId }}"
    ></tenant-hotel-blog>
@endsection
