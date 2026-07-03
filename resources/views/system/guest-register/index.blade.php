@extends('system.guest-register.layouts.main')

@section('content')

    <section class="auth auth__form-{{ $login->position_form }}">

        @include('system.guest-register.partials.sidebar_logo')

        <system-guest-register-register
            :base-url="{{json_encode($base_url)}}"
            :plans="{{json_encode($plans)}}"
            :plan-default="{{json_encode($plan_default)}}"
        >
            <template slot="form-logo">
                @include('system.guest-register.partials.form_logo')
            </template>
        </system-guest-register-register>
    </section>
@endsection


