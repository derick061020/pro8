@extends('tenant.layouts.app')

@section('title', 'Relación de reservas')

@section('content')
    <tenant-hotel-reservation-relation
        :establishment='@json($establishment)'
    ></tenant-hotel-reservation-relation>
@endsection
