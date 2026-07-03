@extends('tenant.layouts.app')

@section('content')

    <affiliation-receipts
        :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
        :soap-company="{{ json_encode($soap_company) }}">
    </affiliation-receipts>

@endsection
