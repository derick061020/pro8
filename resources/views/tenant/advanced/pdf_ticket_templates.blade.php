@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <tenant-configurations-ticket-pdf
	            :type-user="{{ json_encode(auth()->user()->type) }}"
                :establishment-id="{{ json_encode($establishment_id) }}"
                :establishments="{{ json_encode($establishments) }}">
	        </tenant-configurations-ticket-pdf>
        </div>
    </div>
@endsection
