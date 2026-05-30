<div class="footer-middle">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="widget widget-info">
                    <h4 class="widget-title">Contáctanos</h4>
                    <ul class="contact-info">
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 172 172" style="fill:#000000;">
                                <g fill-rule="nonzero" stroke="none" style="mix-blend-mode: normal;">
                                    <path d="M0,172v-172h172v172z" fill="none"></path>
                                    <g fill="#fff">
                                        <path d="M121.69,102.5636c-2.32773,-1.36453 -5.18867,-1.33587 -7.50493,0.04013l-11.7304,6.98893c-2.62587,1.5652 -5.90533,1.38173 -8.31333,-0.4988c-4.1624,-3.2508 -10.86467,-8.7204 -16.69547,-14.5512c-5.8308,-5.8308 -11.3004,-12.53307 -14.5512,-16.69547c-1.88053,-2.408 -2.064,-5.68747 -0.4988,-8.31333l6.98893,-11.7304c1.38173,-2.31627 1.3932,-5.20013 0.02867,-7.52787l-17.21147,-29.40053c-1.6684,-2.84373 -4.98227,-4.24267 -8.1872,-3.4572c-3.1132,0.7568 -7.1552,2.60293 -11.39213,6.8456c-13.26693,13.26693 -20.3132,35.64413 29.57827,85.5356c49.89147,49.89147 72.26293,42.85093 85.5356,29.57827c4.2484,-4.2484 6.0888,-8.29613 6.85133,-11.41507c0.774,-3.1992 -0.602,-6.49013 -3.44,-8.1528c-7.0864,-4.1452 -22.37147,-13.09493 -29.45787,-17.24587z"></path>
                                    </g>
                                </g>
                            </svg>
                            <a href="tel:{{$information->information_contact_phone}}" target="blank" style="font-size: 25px;">{{$information->information_contact_phone}}</a>
                        </li>
                        @if($information->information_contact_address)
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 172 172" style="fill:#000000;">
                                <g fill-rule="nonzero" stroke="none" style="mix-blend-mode: normal;">
                                    <path d="M0,172v-172h172v172z" fill="none"></path>
                                    <g fill="#fff">
                                        <path d="M121.69,102.5636c-2.32773,-1.36453 -5.18867,-1.33587 -7.50493,0.04013l-11.7304,6.98893c-2.62587,1.5652 -5.90533,1.38173 -8.31333,-0.4988c-4.1624,-3.2508 -10.86467,-8.7204 -16.69547,-14.5512c-5.8308,-5.8308 -11.3004,-12.53307 -14.5512,-16.69547c-1.88053,-2.408 -2.064,-5.68747 -0.4988,-8.31333l6.98893,-11.7304c1.38173,-2.31627 1.3932,-5.20013 0.02867,-7.52787l-17.21147,-29.40053c-1.6684,-2.84373 -4.98227,-4.24267 -8.1872,-3.4572c-3.1132,0.7568 -7.1552,2.60293 -11.39213,6.8456c-13.26693,13.26693 -20.3132,35.64413 29.57827,85.5356c49.89147,49.89147 72.26293,42.85093 85.5356,29.57827c4.2484,-4.2484 6.0888,-8.29613 6.85133,-11.41507c0.774,-3.1992 -0.602,-6.49013 -3.44,-8.1528c-7.0864,-4.1452 -22.37147,-13.09493 -29.45787,-17.24587z"></path>
                                    </g>
                                </g>
                            </svg>
                            <a href="#" target="blank" style="font-size: 14px;">
                                {{$information->information_contact_address}}
                            </a>
                        </li>
                        @endif
                        <!-- correo -->
                        @if($information->information_contact_email)
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="30" height="30"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                            <a href="mailto:{{$information->information_contact_email}}" target="blank" style="font-size: 14px;">{{$information->information_contact_email}}</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <h4 class="widget-title text-center">Enlaces de interés</h4>
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-sm-6 text-center">
                            <ul class="links">
                                <li><a href="{{ route("tenant.restaurant.menu") }}">Inicio</a></li>
                                <li><a href="{{ route('restaurant.detail.cart') }}">Ver Carrito</a></li>
                                @guest
                                <li><a href="{{route('tenant_ecommerce_login')}}" class="login-link">Login</a></li>
                                @else
                                <li><a role="menuitem" href="{{ route('logout') }}" class="login-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Salir
                                </a></li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="widget">
                    <h4 class="widget-title text-right">Redes Sociales</h4>
                    <div class="social-icons d-flex justify-content-end">

                        <!-- @if($information->link_facebook)
                            <a href="{{$information->link_facebook}}" class="social-icon" target="_blank"></a>
                        @endif -->

                        <!-- @if($information->link_twitter)
                            <a href="{{$information->link_twitter}}" class="social-icon" target="_blank"><i class="icon-twitter"></i></a>
                        @endif -->

                        <!-- @if($information->link_instagram)
                            <a href="{{$information->link_instagram}}" class="social-icon" target="_blank"><i class="fab fa-youtube"></i></a>
                        @endif -->

                        <a href="{{$information->link_facebook}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-facebook"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path></svg>
                        </a>
                        <a href="{{$information->link_twitter}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
                        </a>
                        <a href="{{$information->link_tiktok}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-tiktok"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M21 7.917v4.034a9.948 9.948 0 0 1 -5 -1.951v4.5a6.5 6.5 0 1 1 -8 -6.326v4.326a2.5 2.5 0 1 0 4 2v-11.5h4.083a6.005 6.005 0 0 0 4.917 4.917z"></path></svg>
                        </a>
                        <a href="{{$information->link_instagram}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-instagram"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z"></path><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path><path d="M16.5 7.5v.01"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container container-footer d-flex align-items-center justify-content-between px-0">
    <p class="text-center copy-text mt-3 mb-3">&copy; Copyright {{ date('Y') }} {{ $company->name }}. Todos los derechos reservados</p>
    <div class="footer-bottom" style="padding-bottom: 2rem;">
        <!-- <p class="footer-copyright">Facturador Pro 4. &copy; {{ now()->year }}. Todos los Derechos Reservados</p> -->
        <img src="{{ asset('porto-ecommerce/assets/images/payments.svg') }}" alt="payment methods"
            class="footer-payments">
    </div>
</div>

<div class="modal fade" id="moda-succes-add-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <i class="icon-ok"></i> Tu producto se agregó al carrito
                </div>
                <div class="row">
                    <div id="product_added_image" class="col-md-4">
                    </div>
                    <!-- Esta parte es del agregar el producto al carrito -->
                    <div class="col-md-8">
                        <div id="product_added" class="product-single-details-restaurant">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('restaurant.detail.cart') }}" class="btn btn-primary text-white">Ir a Carrito</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Seguir Comprando</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-already-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div style="font-size: 1em;" class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation"></i> Tu Producto ya está agregado al carrito.
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('restaurant.detail.cart') }}" class="btn btn-primary text-white">Ir al Carrito</a>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Seguir Comprando</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="login_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div id="tony" class="modal-body-restaurant">
                    <div class="contenedor-form" id="contenedor-form">
                        <!-- contenedor de login -->
                         <!-- <div class="contenedor-column-form"> -->
                        <div id="first-column" class="first-column">
                        <form action="#" id="form_login" class="iniciar-sesion" data-login-url="{{ route('tenant_ecommerce_login') }}">
                                <h4 class="title mb-2">Iniciar sesión</h4>
                                <div id="msg_login" class="alert alert-danger" role="alert" style="display:none;" aria-live="polite">
                                    Usuario o Contraseña Incorrectos.
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo Electronico:</label>
                                    <input type="email" required class="form-control" id="email"
                                        placeholder="Enter email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Contraseña:</label>
                                    <input type="password" required class="form-control" id="pwd"
                                        placeholder="Enter password" name="password">
                                </div>
                                <button type="submit" class="btn btn-primary">Ingresar</button>
                            </form>
                        </div>
                        <!-- contenedor de registro -->
                        <div id="second-column" class="second-column">
                        <form autocomplete="off" action="#" id="form_register" class="registrarse" data-register-url="{{ route('tenant_ecommerce_store_user') }}">
                                <h4 class="title mb-2">Nuevo Registro</h4>
                                <div id="msg_register" class="alert alert-danger" role="alert" style="display:none;" aria-live="polite">
                                    <p id="msg_register_p" style="margin:0;"></p>
                                </div>
                                <div class="form-group">
                                    <label for="email">Nombres:</label>
                                    <input type="text" required autocomplete="off" class="form-control" id="name_reg"
                                        placeholder="Enter name" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="ruc">RUC/DNI:</label>
                                    <input type="text" required autocomplete="off" maxlength="11" class="form-control" id="ruc_reg"
                                        placeholder="Ingrese ruc/dni" name="ruc">
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo Electronico:</label>
                                    <input type="email" required autocomplete="off" class="form-control" id="email_reg"
                                        placeholder="Enter email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Contraseña:</label>
                                    <input type="password" required autocomplete="off" class="form-control" id="pwd_reg"
                                        placeholder="Ingrese contraseña" name="pswd">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Repita la Contraseña:</label>
                                    <input type="password" required autocomplete="off" class="form-control"
                                        id="pwd_repeat_reg" placeholder="Repita contraseña" name="pswd_rpt">
                                </div>
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </form>
                        </div>
                        <!-- </div> -->
                        <!-- contenedor overlay -->
                        <div class="terceary-column">
                            <div class="contenedor-iniciar-sesion">
                                <h3>Hola!</h3>
                                <p>Por favor ingrese sus datos para registrarse</p>
                                <button id="iniciar-sesion" class="btn-iniciar-sesion">Iniciar Sesión</button>
                            </div>
                            <div class="contenedor-registro">
                                <h3>Bienvenido!</h3>
                                <p>Por favor ingrese sus credenciales para iniciar sesión</p>
                                <button id="registrarse" class="btn-registrarse">Registrarse!</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

</div>
<script>
// Animación y lógica de login/registro + set de color dinámico
document.addEventListener("DOMContentLoaded", () => {
    const formContainer = document.getElementById("contenedor-form");
    const btnLoginView = document.getElementById("iniciar-sesion");
    const btnRegisterView = document.getElementById("registrarse");
    if (formContainer && btnLoginView && btnRegisterView) {
        btnLoginView.addEventListener("click", () => {
            formContainer.classList.remove("active");
        });
        btnRegisterView.addEventListener("click", () => {
            formContainer.classList.add("active");
        });
    }
});

function hexToHSL(hex) {
  let r = 0, g = 0, b = 0;

  if (hex.length === 4) {
    r = "0x" + hex[1] + hex[1];
    g = "0x" + hex[2] + hex[2];
    b = "0x" + hex[3] + hex[3];
  } else if (hex.length === 7) {
    r = "0x" + hex[1] + hex[2];
    g = "0x" + hex[3] + hex[4];
    b = "0x" + hex[5] + hex[6];
  }

  r /= 255;
  g /= 255;
  b /= 255;

  const max = Math.max(r, g, b),
        min = Math.min(r, g, b);
  let h, s, l = (max + min) / 2;

  if (max === min) {
    h = s = 0; // gris
  } else {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

    switch (max) {
      case r: h = (g - b) / d + (g < b ? 6 : 0); break;
      case g: h = (b - r) / d + 2; break;
      case b: h = (r - g) / d + 4; break;
    }
    h /= 6;
  }

  return [
    Math.round(h * 360),
    Math.round(s * 100) + "%",
    Math.round(l * 100) + "%"
  ];
}

// Fetch a Laravel
fetch('/ecommerce/color-ecommerce')
  .then(response => response.json())
  .then(data => {
    console.log('Color ecommerce:', data.color);

    // Convertir el HEX recibido a HSL
    const hsl = hexToHSL(data.color);

    // Guardar en variables CSS globales
    document.documentElement.style.setProperty("--primary-h", hsl[0]);
    document.documentElement.style.setProperty("--primary-s", hsl[1]);
    document.documentElement.style.setProperty("--primary-l", hsl[2]);

  })
  .catch(error => console.error('Error obteniendo el color:', error));

</script>
@push('scripts')
<script type="text/javascript">
    // Inicialización
    matchPassword();
    initAuthForms();

    function matchPassword() {
        const password = document.getElementById("pwd_reg");
        const confirmPassword = document.getElementById("pwd_repeat_reg");
        if (!password || !confirmPassword) return;
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("El Password no coincide.");
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
    }

    function setLoading(btn, isLoading, textOriginal) {
        if (!btn) return;
        if (isLoading) {
            btn.dataset.originalText = textOriginal || btn.textContent;
            btn.textContent = 'Procesando...';
            btn.classList.add('is-loading');
            btn.setAttribute('disabled','disabled');
        } else {
            btn.textContent = btn.dataset.originalText || textOriginal || 'Enviar';
            btn.classList.remove('is-loading');
            btn.removeAttribute('disabled');
        }
    }

    function initAuthForms() {
        const loginForm = $('#form_login');
        const registerForm = $('#form_register');
        const msgLogin = $('#msg_login');
        const msgRegister = $('#msg_register');
        if (msgLogin.length) msgLogin.hide();
        if (msgRegister.length) msgRegister.hide();

        if (loginForm.length) {
            loginForm.on('submit', function(e){
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                setLoading(btn, true, 'Ingresar');
                $.ajax({
                    type:'POST',
                    dataType:'JSON',
                    headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: $(this).data('login-url') || "{{route('tenant_ecommerce_login')}}",
                    data: $(this).serialize(),
                    success:function(data){
                        if (data.success) {
                            location.reload();
                        } else {
                            if (msgLogin.length) msgLogin.show();
                        }
                    },
                    error:function(err){
                        console.log(err);
                        if (msgLogin.length) msgLogin.show();
                    },
                    complete:function(){
                        setLoading(btn, false, 'Ingresar');
                    }
                });
            });
        }

        if (registerForm.length) {
            registerForm.on('submit', function(e){
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                setLoading(btn, true, 'Registrarse');
                $.ajax({
                    type:'POST',
                    dataType:'JSON',
                    headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: $(this).data('register-url') || "{{route('tenant_ecommerce_store_user')}}",
                    data: $(this).serialize(),
                    success:function(data){
                        if (data.success) {
                            location.reload();
                        } else {
                            if (msgRegister.length) {
                                msgRegister.show();
                                $('#msg_register_p').text(data.message || 'Error en el registro');
                            }
                        }
                    },
                    error:function(err){
                        console.log(err);
                        if (msgRegister.length) {
                            msgRegister.show();
                            $('#msg_register_p').text('Ocurrió un error inesperado');
                        }
                    },
                    complete:function(){
                        setLoading(btn, false, 'Registrarse');
                    }
                });
            });
        }
    }
</script>
@endpush
