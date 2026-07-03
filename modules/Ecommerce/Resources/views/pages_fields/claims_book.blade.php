@extends('ecommerce::layouts.layout_ecommerce_cart.index')
@section('content')

<div id="app">
    <div class="mb-2 mt-4">
        <h2 class="mb-0">Libro de Reclamaciones</h2>
    </div>

    <div class="col-lg-12 mb-5">
        <iframe
            id="claims-iframe"
            src="{{ url('/claims/widget') }}"
            style="width: 100%; height: 0; border: none; display: block; overflow: hidden;"
            scrolling="no"
            allowtransparency="true"
            title="Libro de Reclamaciones"
        ></iframe>
    </div>
</div>

<script>
(function () {
    var iframe = document.getElementById('claims-iframe');

    function resize() {
        try {
            var doc = iframe.contentWindow.document;
            var h = Math.max(
                doc.body.scrollHeight,
                doc.documentElement.scrollHeight
            );
            if (h > 0) iframe.style.height = h + 'px';
        } catch (e) {}
    }

    iframe.addEventListener('load', function () {
        resize();
        try {
            var observer = new ResizeObserver(resize);
            observer.observe(iframe.contentWindow.document.body);
        } catch (e) {
            setInterval(resize, 400);
        }
    });
}());
</script>

@endsection
