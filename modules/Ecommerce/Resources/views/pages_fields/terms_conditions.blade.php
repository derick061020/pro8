@extends('ecommerce::layouts.layout_ecommerce_cart.index')
@section('content')

<div id="app">
    <div class="mb-2 mt-4">
        <div>
            <h2 class="mb-0">Términos y Condiciones</h2>
        </div>
    </div>

    <div class="col-lg-12">
        @if(isset($terms_conditions) && $terms_conditions)
            <div class="mt-3">
                {!! $terms_conditions !!}
            </div>
        @else
            <div class="mt-3 text-muted">No se han definido términos y condiciones.</div>
        @endif
    </div>

</div>

@endsection

