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
                <p class="card-title text-center">Recuperar contraseña</p>
                <h1 class="display-3 position-absolute text-left font-weight-bold"
                    style="left: 90%; margin-top: -35px !important; color: rgba(255,255,255,.1) !important; font-size: 4.5rem !important; font-weight: 600 !important;">8</h1>
            </div>

            <div class="card-body p-4">

                @if (session('status'))
                    <div class="alert mb-3" style="background-color: #d4edda !important; border: 1px solid #c3e6cb !important; border-radius: 6px;">
                        <label class="mb-0" style="color: #155724 !important;">
                            <i class="fas fa-check-circle mr-1"></i> <strong>{{ session('status') }}</strong>
                        </label>
                    </div>
                @endif

                <div id="contenedor-alerta">
                    @if ($errors->any())
                        @php 
                            $mensaje = $errors->first();
                            $esBloqueo = str_contains(strtolower($mensaje), 'intentos') || str_contains(strtolower($mensaje), 'espera');
                        @endphp

                        @if ($esBloqueo)
                            <div class="alert mb-3" style="background-color: #fff3cd !important; border: 1px solid #ffeeba !important; border-radius: 6px;">
                                <label class="mb-0" style="color: #856404 !important;">
                                    <i class="fas fa-clock mr-1"></i> <strong id="texto-bloqueo">{{ $mensaje }}</strong>
                                </label>
                            </div>
                        @else
                            <div class="alert mb-3" style="background-color: #f8d7da !important; border: 1px solid #f5c6cb !important; border-radius: 6px;">
                                <label class="mb-0" style="color: #721c24 !important;">
                                    <i class="fas fa-exclamation-circle mr-1"></i> <strong>{{ $mensaje }}</strong>
                                </label>
                            </div>
                        @endif
                    @endif
                </div>

                <p class="text-center text-muted mb-4">
                    Ingresa tu correo para recuperar tu contraseña
                </p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group mb-4">
                        <label>Correo electrónico</label>
                        <div class="input-group">
                            <input type="email" id="input-email" name="email" class="form-control form-control-lg" required>
                            <span class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" id="btn-enviar" class="btn btn-primary btn-lg btn-block mt-2">
                                Enviar enlace
                            </button>
                        </div>
                    </div>

                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-primary font-weight-bold">
                        Volver al login
                    </a>
                </div>

            </div>
        </div>

        <p class="text-center text-muted mt-3 mb-3">
            {{ config('app.name') }} &copy; Copyright {{ date('Y') }}. Todos los derechos reservados
        </p>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputEmail = document.getElementById('input-email');
        const btnEnviar = document.getElementById('btn-enviar');
        const textoBloqueo = document.getElementById('texto-bloqueo');
        const contenedorAlerta = document.getElementById('contenedor-alerta');
        
        const mensajeError = "{{ $errors->first() }}";
        
        // Función para aplicar estilos e impedir escritura o clics
        function bloquearFormulario() {
            inputEmail.readOnly = true;
            inputEmail.style.backgroundColor = '#e9ecef';
            inputEmail.style.cursor = 'not-allowed';
            btnEnviar.disabled = true;
            btnEnviar.style.cursor = 'not-allowed';
            btnEnviar.innerText = 'Formulario bloqueado temporalmente';
        }

        // Función para restaurar el formulario a su estado original
        function desbloquearFormulario() {
            inputEmail.readOnly = false;
            inputEmail.style.backgroundColor = '';
            inputEmail.style.cursor = '';
            btnEnviar.disabled = false;
            btnEnviar.style.cursor = '';
            btnEnviar.innerText = 'Enviar enlace';
            contenedorAlerta.innerHTML = ''; 
            localStorage.removeItem('bloqueo_hasta'); 
        }

        // 1. EVALUAR PERSISTENCIA (Por si el usuario recarga la página)
        const tiempoGuardado = localStorage.getItem('bloqueo_hasta');
        let segundosRestantes = 0;

        if (tiempoGuardado) {
            const ahora = Math.floor(Date.now() / 1000);
            segundosRestantes = tiempoGuardado - ahora; // Segundos que faltan para liberar el tiempo
        }

        // Si no había tiempo guardado, pero Laravel acaba de retornar la alerta de espera
        if (segundosRestantes <= 0 && mensajeError && mensajeError.toLowerCase().includes('espera')) {
            const matchSegundos = mensajeError.match(/\d+/); 
            if (matchSegundos) {
                segundosRestantes = parseInt(matchSegundos[0]);
                // Marcar en qué segundo del tiempo UNIX exacto debe finalizar el bloqueo
                const tiempoTermina = Math.floor(Date.now() / 1000) + segundosRestantes;
                localStorage.setItem('bloqueo_hasta', tiempoTermina);
            }
        }

        // 3. EJECUTAR EL RELOJ REGRESIVO
        // Si hay segundos por cumplir, bloquear el formulario e iniciar el intervalo
        if (segundosRestantes > 0) {
            bloquearFormulario();
            
            // Si el usuario recargó la página, volver a pintar el contenedor amarillo
            if (!textoBloqueo) {
                contenedorAlerta.innerHTML = `
                    <div class="alert mb-3" style="background-color: #fff3cd !important; border: 1px solid #ffeeba !important; border-radius: 6px;">
                        <label class="mb-0" style="color: #856404 !important;">
                            <i class="fas fa-clock mr-1"></i> <strong id="texto-bloqueo">Demasiados intentos. Espera ${segundosRestantes} segundos.</strong>
                        </label>
                    </div>
                `;
            }

            const textoActualizable = document.getElementById('texto-bloqueo');

            const intervalo = setInterval(function() {
                segundosRestantes--;
                
                // Si llega a 0 o menos, frenar el intervalo y ejecutar el desbloqueo
                if (segundosRestantes <= 0) {
                    clearInterval(intervalo);
                    desbloquearFormulario();
                } else if (textoActualizable) {
                    textoActualizable.innerText = `Demasiados intentos. Espera ${segundosRestantes} segundos.`;
                }
            }, 1000); // Ejecutar cada 1 segundo
        }
    });
</script>

@endsection