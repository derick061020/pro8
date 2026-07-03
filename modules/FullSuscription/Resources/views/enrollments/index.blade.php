@extends('tenant.layouts.app')

@section('content')

    <full-suscription-enrollments
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :date="'{{Carbon\Carbon::now()->format('Y-m-d')}}'">
    </full-suscription-enrollments>

@endsection
