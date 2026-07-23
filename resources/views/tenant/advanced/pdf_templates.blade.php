@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <tenant-configurations-pdf
	            :type-user="{{ json_encode(auth()->user()->type) }}"
                :establishment-id="{{ json_encode($establishment_id) }}"
                :establishments="{{ json_encode($establishments) }}">
	        </tenant-configurations-pdf>
        </div>
    </div>
@endsection
