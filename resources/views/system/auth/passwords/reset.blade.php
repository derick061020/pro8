@extends('system.layouts.auth')

@section('content')

@php
    use App\Models\System\Configuration;
    $configuration = Configuration::first();
    $logo = $configuration->login->logo ?? null;
@endphp

<section class="body-sign">
    <div class="center-sign">
        
        <div class="logo-login">
            @if ($logo)
                <img class="uk-logo-inverse" width="100" height="auto" src="{{ $logo }}" alt="Logo" />
            @elseif (file_exists(public_path('theme/logo.svg')))
                <img class="uk-logo-inverse" width="100" height="auto" src="{{ asset('theme/logo.svg') }}" alt="Logo" />
            @endif
        </div>

        <div class="">
            <div class="card card-header card-primary bg-info">
                <p class="card-title text-center">Nueva Contraseña</p>
                <h1 class="display-3 position-absolute text-left font-weight-bold"
                    style="left: 90%; margin-top: -35px !important; color: rgba(255,255,255,.1) !important; font-size: 4.5rem !important; font-weight: 600 !important;">8</h1>
            </div>

            <div class="card-body p-4">
                
                @if ($errors->any())
                    <div class="alert mb-3" style="background-color: #f8d7da !important; border: 1px solid #f5c6cb !important; border-radius: 6px;">
                        <label class="mb-0" style="color: #721c24 !important;">
                            <i class="fas fa-exclamation-circle mr-1"></i> <strong>{{ $errors->first() }}</strong>
                        </label>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="form-group mb-3">
                        <label>Nueva contraseña</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password" class="form-control form-control-lg" required>
                            <span class="input-group-append" onclick="togglePass('password', this)" style="cursor: pointer;">
                                <span class="input-group-text">
                                    <i class="fas fa-eye" id="eye-icon-pass"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label>Confirmar contraseña</label>
                        <div class="input-group">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-lg" required>
                            <span class="input-group-append" onclick="togglePass('password_confirmation', this)" style="cursor: pointer;">
                                <span class="input-group-text">
                                    <i class="fas fa-eye" id="eye-icon-conf"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-2">
                                Guardar nueva contraseña
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-3 mb-3">
            {{ config('app.name') }} &copy; Copyright {{ date('Y') }}. Todos los derechos reservados
        </p>
    </div>
</section>

<script>
// Alternar la visualización del texto de la contraseña
function togglePass(id, el) {
    let input = document.getElementById(id);
    let icon = el.querySelector('i');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

@endsection