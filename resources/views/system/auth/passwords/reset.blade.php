@extends('system.layouts.auth')

@section('content')

@include('system.auth.partials.theme')

@php
    use App\Models\System\Configuration;
    $configuration = Configuration::first();
    $logo = $configuration->login->logo ?? null;
@endphp

<section class="body-sign">
    <div class="center-sign">

        <div class="logo-login mb-3">
            @if ($logo)
                <img class="uk-logo-inverse" src="{{ $logo }}" alt="Logo" />
            @elseif (file_exists(public_path('theme/logo.svg')))
                <img class="uk-logo-inverse" src="{{ asset('theme/logo.svg') }}" alt="Logo" />
            @endif
        </div>

        <div class="card">
            <div class="card-header bg-info d-flex align-items-center justify-content-between py-3">
                <p class="card-title mb-0 text-white font-weight-bold">Nueva Contraseña</p>
                <span class="login-version">
                    <i class="ti ti-sparkles"></i> Versión <b>9</b>
                </span>
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
                        <label class="control-label">Nueva contraseña</label>
                        <div class="login-pass">
                            <input id="password" type="password" name="password" class="form-control"
                                placeholder="••••••••" required>
                            <span class="login-pass-toggle" onclick="togglePass('password', this)" role="button"
                                aria-label="Mostrar contraseña">
                                <i class="ti ti-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="control-label">Confirmar contraseña</label>
                        <div class="login-pass">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                                placeholder="••••••••" required>
                            <span class="login-pass-toggle" onclick="togglePass('password_confirmation', this)" role="button"
                                aria-label="Mostrar contraseña">
                                <i class="ti ti-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg btn-block login-submit mt-2">
                                <i class="ti ti-device-floppy"></i> Guardar nueva contraseña
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
        icon.className = 'ti ti-eye-off';
    } else {
        input.type = "password";
        icon.className = 'ti ti-eye';
    }
}
</script>

@endsection