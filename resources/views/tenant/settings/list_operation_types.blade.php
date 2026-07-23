@extends('tenant.layouts.app')

@section('content')
    <tenant-operation-types :type-user="{{json_encode(Auth::user()->type)}}"></tenant-operation-types>
@endsection
