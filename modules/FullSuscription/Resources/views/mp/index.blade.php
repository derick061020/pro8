@extends('tenant.layouts.app')

@section('content')

    <full-suscription-mp
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :date="'{{Carbon\Carbon::now()->format('Y-m-d')}}'">
    </full-suscription-mp>

@endsection
