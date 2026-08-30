@extends('tenant.layouts.app')

@section('title', 'Reporte de Reservas')

@section('content')
    <tenant-hotel-reservation-report
        :establishment='@json($establishment)'
    ></tenant-hotel-reservation-report>
@endsection
