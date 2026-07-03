@extends('tenant.layouts.app')

@section('content')

    <div>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Pago pendiente</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Pago pendiente</a></li>
                            <li class="breadcrumb-item active">Pago pendiente</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">

                <div class="row p-4">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Tu cuenta está pendiente de pago.</strong>
                        Para activar tu cuenta y comenzar a usar el sistema, necesitas completar el pago del plan que seleccionaste durante el registro.
                    </div>

                    @if($payment_url)
                        <div class="col-12 text-center mt-3">
                            <a href="{{ $payment_url }}" class="btn btn-primary btn-lg">
                                <i class="fa fa-credit-card"></i> Pagar ahora
                            </a>
                        </div>
                    @else
                        <div class="col-12 text-center mt-3">
                            <p class="text-muted">No se encontró una orden de pago pendiente. Por favor contacta a soporte.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endsection
