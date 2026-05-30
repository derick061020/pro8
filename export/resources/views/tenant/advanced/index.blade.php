@extends('tenant.layouts.app')

@section('content')
    <div class="row row-mx-0">
        <div class="col-lg-12">
            <tenant-configurations-form
                :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
                :soap-type-id="{{ json_encode(\App\Models\Tenant\Company::first()->soap_type_id ?? '01') }}"
                    :type-user="{{ json_encode(auth()->user()->type) }}"></tenant-configurations-form>
        </div>
    </div>
@endsection
