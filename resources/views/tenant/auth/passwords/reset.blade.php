@extends('tenant.layouts.auth')

@section('content')
<section class="auth">
    @include('tenant.auth.partials.side_left')
    <article class="auth__form">
        <form class="form-material" id="loginform" method="POST" action="{{ route('password.update') }}">
            @include('tenant.auth.partials.form_logo')
            <h1 class="auth__title">Bienvenido a<br>{{ $company->trade_name }}</h1>
            <p>
                Ingrese y confirme su nueva contraseña para restablecer el acceso con su correo
                <strong>{{ $email ?? old('email') }}</strong>
            </p>    
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            @if ($errors->has('email'))
            <div class="form-group mb-0">
                <span class="invalid-feedback d-block text-left" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            </div>
            @endif

            <div class="form-group">
                 <label for="password" class="col-form-label d-block text-left">Contraseña</label>

                <div class="">
                    <input id="password" type="password"
                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                        required>

                    @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="password-confirm" class="col-form-label d-block text-left">Confirmar contraseña</label>

                <div class="">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                        required>
                </div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-signin btn-block">REINICIAR CONTRASEÑA</button>
                <br>
                <a href="{{ route('login') }}" class="btn btn-link">
                    <i class="fa fa-arrow-left mr-2"></i> Regresar al login
                </a>
            </div>
            @include('tenant.auth.partials.socials')
        </form>
    </article>
</section>
@endsection
