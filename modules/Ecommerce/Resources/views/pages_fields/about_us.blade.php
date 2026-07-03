@extends('ecommerce::layouts.layout_ecommerce_cart.index')
@section('content')

<div id="app">
    <div class="mb-2 mt-4">
        <div>
            <h2 class="mb-0">Sobre Nosotros</h2>
        </div>
    </div>

    <div class="col-lg-12">
        @if(isset($about_us) && $about_us)
            <div class="mt-3">
                {!! $about_us !!}
            </div>
        @else
            <div class="mt-3 text-muted">No se ha definido la sección Sobre Nosotros.</div>
        @endif
    </div>

</div>

@endsection

