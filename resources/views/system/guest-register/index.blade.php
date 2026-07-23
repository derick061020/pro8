@extends('system.guest-register.layouts.main')

@section('content')

    <section class="auth__register auth auth__form-{{ $login->position_form }}">

        <div class="auth__container d-flex">
            @include('system.guest-register.partials.sidebar_logo')
            <system-guest-register-register
                :base-url="{{json_encode($base_url)}}"
                :plans="{{json_encode($plans)}}"
                :plan-default="{{json_encode($plan_default)}}"
                :validate-ruc="{{json_encode($validate_ruc_register)}}"
            >
                <template slot="form-logo">
                    @include('system.guest-register.partials.form_logo')
                </template>
            </system-guest-register-register>
        </div>
    </section>
@endsection


