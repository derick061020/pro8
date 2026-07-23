<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

<style>
    .page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .page-wrapper > main.main {
        flex: 1;
    }

    /* ── Footer middle ── */
    .footer-middle {
        padding: 60px 0;
    }
    .footer-col-title {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #fff;
        margin-bottom: 6px;
    }
    .footer-col-divider {
        width: 36px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px;
        margin-bottom: 22px;
    }

    /* Links */
    .footer-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-nav li {
        margin-bottom: 4px;
    }
    .footer-nav li a {
        font-size: 15px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 0;
        opacity: 0.75;
        transition: opacity 0.2s, gap 0.2s;
    }
    .footer-nav li a i {
        font-size: 14px;
        opacity: 0.6;
    }
    .footer-nav li a:hover {
        opacity: 1;
        gap: 12px;
    }

    /* Social icons */
    .footer-socials {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 4px;
    }
    .footer-social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: hsl(var(--primary-h), var(--primary-s), 92%);
        color: var(--primary-color) !important;
        font-size: 20px;
        transition: background 0.2s, color 0.2s, transform 0.15s;
    }
    .footer-social-btn:hover {
        background: var(--primary-color);
        color: #fff !important;
        transform: translateY(-3px);
    }

    /* Contact items */
    .footer-contact-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-contact-list li {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }
    .footer-contact-list li:last-child {
        margin-bottom: 0;
    }
    .fci-icon {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: hsl(var(--primary-h), var(--primary-s), 92%);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .fci-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .fci-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.45;
        line-height: 1;
        margin-bottom: 4px;
    }
    .fci-value,
    .fci-value a {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        word-break: break-word;
        opacity: 0.85;
    }
    .fci-value a:hover {
        opacity: 1;
    }

    /* Logo tagline */
    .footer-tagline {
        font-size: 14px;
        line-height: 1.65;
        opacity: 0.6;
        margin: 10px 0 20px;
    }
    .register-extra-fields {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transform: translateY(-6px);
        transition: max-height 0.45s ease, opacity 0.35s ease, transform 0.35s ease;
        pointer-events: none;
    }
    .register-extra-fields.is-visible {
        max-height: 600px;
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    #name_reg[readonly] {
        cursor: not-allowed;
    }
    .select-part, .input-part {
        box-shadow: none !important
    }
</style>

<div class="footer-middle">
    <div class="container">
        <div class="row">

            {{-- Columna 1: Logo + Redes Sociales --}}
            <div class="col-md-3 col-sm-6">
                @php($footerLogo = data_get($company ?? null, 'logo_dark') ?: data_get($company ?? null, 'logo') ?: data_get($information ?? null, 'logo'))
                <a href="{{ route('tenant.ecommerce.index') }}" class="d-inline-block mb-1">
                    @if($footerLogo)
                        <img src="{{ asset('storage/uploads/logos/'.$footerLogo) }}" alt="{{ $company->name ?? 'Logo' }}" style="max-height: 64px;">
                    @else
                        <img src="{{ asset('logo/tulogo.png') }}" alt="Logo" style="max-height: 64px;">
                    @endif
                </a>
                @if(!empty($company->trade_name)  && $company->name !== $company->trade_name)
                    <p class="footer-tagline m-0">{{ $company->trade_name }}</p>
                @endif
                @if(!empty($company->name))
                    <p class="footer-tagline m-0">{{ $company->name }}</p>
                @endif
                @if(!empty($company->number))
                    <p class="footer-tagline m-0 pb-4">RUC: {{ $company->number }}</p>
                @endif

                <p class="footer-col-title">Síguenos</p>
                <div class="footer-col-divider"></div>
                <div class="footer-socials">
                    @if($information->link_facebook)
                        <a href="{{ $information->link_facebook }}" class="footer-social-btn" target="_blank" title="Facebook">
                            <i class="ti ti-brand-facebook"></i>
                        </a>
                    @endif
                    @if($information->link_twitter)
                        <a href="{{ $information->link_twitter }}" class="footer-social-btn" target="_blank" title="X / Twitter">
                            <i class="ti ti-brand-x"></i>
                        </a>
                    @endif
                    @if($information->link_tiktok)
                        <a href="{{ $information->link_tiktok }}" class="footer-social-btn" target="_blank" title="TikTok">
                            <i class="ti ti-brand-tiktok"></i>
                        </a>
                    @endif
                    @if($information->link_instagram)
                        <a href="{{ $information->link_instagram }}" class="footer-social-btn" target="_blank" title="Instagram">
                            <i class="ti ti-brand-instagram"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Columna 2: Información --}}
            <div class="col-md-3 col-sm-6">
                <p class="footer-col-title">Información</p>
                <div class="footer-col-divider"></div>
                <ul class="footer-nav">
                    @if(!empty($information->about_us))
                        <li>
                            <a href="{{ route('tenant_ecommerce_about_us') }}">
                                <i class="ti ti-chevron-right"></i> Sobre Nosotros
                            </a>
                        </li>
                    @endif
                    @if(!empty($information->terms_conditions))
                        <li>
                            <a href="{{ route('tenant_ecommerce_terms_conditions') }}">
                                <i class="ti ti-chevron-right"></i> Términos y Condiciones
                            </a>
                        </li>
                    @endif
                    @if(!empty($information->privacy_policy))
                        <li>
                            <a href="{{ route('tenant_ecommerce_privacy_policy') }}">
                                <i class="ti ti-chevron-right"></i> Política de Privacidad
                            </a>
                        </li>
                    @endif
                        <li>
                            <a href="{{ route('search.index') }}">
                                <i class="ti ti-chevron-right"></i> Consulta de Comprobantes Electrónicos
                            </a>
                        </li>

                    @if(!empty($information->customised_link_one) && !empty($information->title_one_customised_link))
                        <li>
                            <a href="{{ $information->customised_link_one }}">
                                <i class="ti ti-chevron-right"></i> {{ $information->title_one_customised_link }}
                            </a>
                        </li>
                    @endif
                    @if(!empty($information->customised_link_two) && !empty($information->title_two_customised_link))
                        <li>
                            <a href="{{ $information->customised_link_two }}">
                                <i class="ti ti-chevron-right"></i> {{ $information->title_two_customised_link }}
                            </a>
                        </li>
                    @endif
                    @if(!empty($information->customised_link_three) && !empty($information->title_three_customised_link))
                        <li>
                            <a href="{{ $information->customised_link_three }}">
                                <i class="ti ti-chevron-right"></i> {{ $information->title_three_customised_link }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Columna 3: Categorías --}}
            <div class="col-md-3 col-sm-6">
                <p class="footer-col-title">Categorías</p>
                <div class="footer-col-divider"></div>
                <ul class="footer-nav">
                    <li>
                        <a href="{{ route('tenant.ecommerce.index') }}">
                            <i class="ti ti-chevron-right"></i> Ver todas
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('tenant.ecommerce.category', \Illuminate\Support\Str::slug($category->name, '-')) }}">
                                <i class="ti ti-chevron-right"></i> {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Columna 4: Contacto & Ayuda --}}
            <div class="col-md-3 col-sm-6">
                <p class="footer-col-title">Contacto &amp; Ayuda</p>
                <div class="footer-col-divider"></div>
                <ul class="footer-contact-list">
                    @if($information->phone_whatsapp)
                        <li>
                            <div class="fci-icon"><i class="ti ti-brand-whatsapp"></i></div>
                            <div class="fci-body">
                                <span class="fci-label">WhatsApp</span>
                                <span class="fci-value">
                                    <a href="https://wa.me/{{ $information->phone_whatsapp }}" target="_blank">
                                        {{ $information->phone_whatsapp }}
                                    </a>
                                </span>
                            </div>
                        </li>
                    @endif
                    @if($information->information_contact_phone)
                        <li>
                            <div class="fci-icon"><i class="ti ti-phone"></i></div>
                            <div class="fci-body">
                                <span class="fci-label">Teléfono</span>
                                <span class="fci-value">
                                    <a href="tel:{{ $information->information_contact_phone }}">
                                        {{ $information->information_contact_phone }}
                                    </a>
                                </span>
                            </div>
                        </li>
                    @endif
                    @if($information->information_contact_email)
                        <li>
                            <div class="fci-icon"><i class="ti ti-mail"></i></div>
                            <div class="fci-body">
                                <span class="fci-label">Email</span>
                                <span class="fci-value">
                                    <a href="mailto:{{ $information->information_contact_email }}">
                                        {{ $information->information_contact_email }}
                                    </a>
                                </span>
                            </div>
                        </li>
                    @endif
                    @if($information->information_contact_address)
                        <li>
                            <div class="fci-icon"><i class="ti ti-clock"></i></div>
                            <div class="fci-body">
                                <span class="fci-label">Horario de atención</span>
                                <span class="fci-value">{{ $information->information_contact_address }}</span>
                            </div>
                        </li>
                    @endif
                        <li>
                            <div style="text-align: center">
                                <span class="fci-label">Libro de Reclamaciones</span>
                                <a class="pt-4" style="display: block" href="{{ route('tenant.ecommerce.claims_book') }}"><img src="{{ asset('porto-ecommerce/assets/images/libro-de-reclamaciones.png') }}" style="margin: auto" width="96px" alt="Libro de Reclamaciones"></a>
                            </div>
                        </li>
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="container-footer ">
    <div class="container d-flex align-items-center justify-content-between">
        <p class="text-center copy-text mt-3 mb-3">&copy; Copyright {{ date('Y') }} {{ $company->name }}. Todos los derechos reservados</p>
        <div class="footer-bottom" style="padding-bottom: 2rem;">
            <!-- <p class="footer-copyright">Facturador Pro 4. &copy; {{ now()->year }}. Todos los Derechos Reservados</p> -->
            <img src="{{ asset('porto-ecommerce/assets/images/payments.svg') }}" alt="payment methods"
                class="footer-payments">
        </div>
    </div>
</div>

@if($information->phone_whatsapp)
    <div class="ws-tooltip" id="wsTooltip">
        <span id="wsTooltipText">¿En qué podemos ayudarte?</span>
    </div>

    @if(strlen($information->phone_whatsapp) > 0)
    <a class='ws-flotante-ecommerce d-flex align-items-center justify-content-center' href='https://wa.me/{{$information->phone_whatsapp}}' target="BLANK" style="color: #fff !important;">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" /><path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" /></svg>
    </a>
    @endif
@endif

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
                    <div class="col-md-8">
                        <div id="product_added" class="product-single-details-ecommerce">

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('tenant_detail_cart') }}" class="btn btn-primary text-white">Ir a Carrito</a>
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

                <div style="font-size: 2em;" class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation"></i> Tu Producto ya está agregado al carrito.
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('tenant_detail_cart') }}" class="btn btn-primary text-white">Ir al Carrito</a>
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
                <div id="msg_login" class="alert alert-danger" role="alert" style="display: none;">
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
                                <div class="forgot-password-container">
                                    <span class="forgot-password-title">
                                        ¿Olvidaste tu contraseña?
                                    </span>
                                    <p class="forgot-password-text">
                                        Ponte en contacto con tu administrador o proveedor para que te genere una nueva clave de acceso.
                                    </p>
                                </div>
                            </form>
                        </div>
                        <!-- contenedor de registro -->
                        <div id="second-column" class="second-column">
            <form autocomplete="off" action="#" id="form_register" class="registrarse" data-register-url="{{ route('tenant_ecommerce_store_user') }}">
                                <h4 class="title mb-2">Nuevo Registro</h4>
                <div id="msg_register" class="alert alert-danger" role="alert" style="display: none;">
                                    <p id="msg_register_p"></p>
                                </div>
                                <div class="form-group">
                                    <label for="ruc">Tipo de Documento:</label>
                                    <div class="unified-input-group position-relative">
                                        <select class="select-part" id="selectDocument">
                                            <option value="dni" selected>DNI</option>
                                            <option value="ruc">RUC</option>
                                        </select>
                                        <input type="number" required autocomplete="off" maxlength="11" class="input-part" id="ruc_reg" placeholder="Ingrese su número de documento" name="ruc">
                                        <span id="counter" class="text-center counter-part">0/8</span>
                                    </div>
                                    <button type="button" id="btn_verify_document" class="btn btn-outline-primary btn-sm mt-2">
                                        <i class="ti ti-search"></i> Verificar
                                    </button>
                                    <small id="document_status" class="d-block mt-1" style="display:none;"></small>
                                    <small id="document_hint" class="d-block mt-1 text-muted">
                                        Ingresa tu DNI o RUC y verifícalo para continuar.
                                    </small>
                                </div>
                                <div id="register-fields" class="register-extra-fields px-2">
                                <div class="form-group">
                                    <label for="email">Nombres:</label>
                                    <input type="text" autocomplete="off" class="form-control" id="name_reg"
                                        placeholder="Nombre completo / Razón social" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="email">Correo Electronico:</label>
                                    <input type="email" autocomplete="off" class="form-control" id="email_reg"
                                        placeholder="Enter email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Contraseña:</label>
                                    <input type="password" autocomplete="off" class="form-control" id="pwd_reg"
                                        placeholder="Ingrese contraseña" name="pswd">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">Repita la Contraseña:</label>
                                    <input type="password" autocomplete="off" class="form-control"
                                        id="pwd_repeat_reg" placeholder="Repita contraseña" name="pswd_rpt">
                                </div>
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                                </div>
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
function setDocumentsCounter() {
    let select = document.getElementById('selectDocument');
    let counter = document.getElementById('counter');
    let ruc_reg = document.getElementById('ruc_reg');
    if (select && counter && ruc_reg) {
        // Limpiar el input y remover clases del contador
        ruc_reg.value = '';
        counter.classList.remove('warning', 'success', 'error');

        // Limpiar nombre autocompletado y mensaje de estado
        let name_reg = document.getElementById('name_reg');
        if (name_reg) {
            name_reg.value = '';
            name_reg.readOnly = false;
            name_reg.classList.remove('bg-light');
        }

        // Volver a ocultar los demás campos hasta nueva verificación
        if (typeof hideRegisterFields === 'function') {
            hideRegisterFields();
        }
        let status = document.getElementById('document_status');
        if (status) {
            status.style.display = 'none';
            status.textContent = '';
            status.className = 'd-block mt-1';
        }
        if (select.value === 'dni') {
            ruc_reg.setAttribute('maxlength', '8');
            ruc_reg.setAttribute('placeholder', 'Ingrese su DNI');
            counter.textContent = '0/8';
        } else if (select.value === 'ruc') {
            ruc_reg.setAttribute('maxlength', '11');
            ruc_reg.setAttribute('placeholder', 'Ingrese su RUC');
            counter.textContent = '0/11';
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const firstColumn = document.getElementById("contenedor-form");

    const btnIniciarSesion = document.getElementById("iniciar-sesion");

    const btnRegistrarse = document.getElementById("registrarse");

    btnIniciarSesion.addEventListener("click", () => {
        firstColumn.classList.remove("active");

    });
    btnRegistrarse.addEventListener("click", () => {
        firstColumn.classList.add("active");

    });
    setDocumentsCounter();
});
</script>



@push('scripts')
<!-- <script type="text/javascript" src="{{ asset('porto-ecommerce/assets/js/cart.js') }}"></script> -->
<script type="text/javascript">
    const SEARCH_DOC_URL = "{{ url('ecommerce/search-document') }}";

    matchPassword();
    submitLogin();
    submitRegister();
    changeDocument();
    setupDocumentInput();

    function matchPassword() {
        var password = document.getElementById("pwd_reg"),
            confirm_password = document.getElementById("pwd_repeat_reg");

        function validatePassword() {
            if (password.value != confirm_password.value) {
                confirm_password.setCustomValidity("El Password no coincide.");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    }

    function startButtonLoading(button, baseText) {
        if (!button || !button.length) {
            return function () {};
        }

        var originalHtml = button.html();
        var dots = 0;

        button.prop('disabled', true);
        button.html(baseText);

        var interval = setInterval(function () {
            dots = (dots + 1) % 4; // 0,1,2,3 puntos
            button.html(baseText + new Array(dots + 1).join('.'));
        }, 350);

        return function () {
            clearInterval(interval);
            button.prop('disabled', false);
            button.html(originalHtml);
        };
    }

    function submitLogin() {
        $('#msg_login').hide();

        $('#form_login').submit(function (e) {
            e.preventDefault()
            var stopLoading = startButtonLoading($(this).find('button[type="submit"]'), 'Ingresando');
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('tenant_ecommerce_login')}}",
                data: $(this).serialize(),
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        stopLoading();
                        $('#msg_login').show();
                    }
                },
                error: function (error_data) {
                    stopLoading();
                    console.log(error_data)
                }
            });
        })

    }

    function submitRegister() {
        $('#msg_register').hide();

        $('#form_register').submit(function (e) {
            e.preventDefault()
            var stopLoading = startButtonLoading($(this).find('button[type="submit"]'), 'Registrando');
            $.ajax({
                type: "POST",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('tenant_ecommerce_store_user')}}",
                data: $(this).serialize(),
                success: function (data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        stopLoading();
                        $('#msg_register').show();
                        $('#msg_register_p').text(data.message)
                    }
                },
                error: function (error_data) {
                    stopLoading();
                    console.log(error_data)
                }
            });
        })
    }
    function changeDocument(){
        let select = document.getElementById('selectDocument');

        if (select) {
            select.addEventListener('change', function() {
                setDocumentsCounter();
            });
        }
    }
    function setupDocumentInput(){
        let ruc_reg = document.getElementById('ruc_reg');
        let counter = document.getElementById('counter');
        let btnVerify = document.getElementById('btn_verify_document');

        if (!ruc_reg) return;

        ruc_reg.addEventListener('input', function() {
            const maxLength = parseInt(ruc_reg.getAttribute('maxlength'));

            // Limitar la longitud si excede el máximo (type=number ignora maxlength)
            if (ruc_reg.value.length > maxLength) {
                ruc_reg.value = ruc_reg.value.slice(0, maxLength);
            }

            const currentLength = ruc_reg.value.length;

            // Actualizar el texto del contador
            counter.textContent = `${currentLength}/${maxLength}`;

            // El texto de ayuda solo se muestra cuando el campo está vacío (sin verificar)
            toggleDocumentHint(currentLength === 0);

            // Remover clases previas
            counter.classList.remove('warning', 'success', 'error');

            // Si el número cambia y ya no está completo, liberar el nombre bloqueado
            if (currentLength !== maxLength) {
                let name_reg = document.getElementById('name_reg');
                if (name_reg && name_reg.readOnly) {
                    name_reg.readOnly = false;
                    name_reg.classList.remove('bg-light');
                }
            }

            // Agregar clase según el estado
            if (currentLength === 0) {
                resetDocumentStatus();
                hideRegisterFields();
            } else if (currentLength < maxLength * 0.5) {
                // Menos del 50% - sin clase especial
            } else if (currentLength < maxLength) {
                counter.classList.add('warning');
            } else if (currentLength === maxLength) {
                counter.classList.add('success');
                // Verificación automática al completar todos los dígitos
                verifyDocument();
            }
        });

        if (btnVerify) {
            btnVerify.addEventListener('click', function() {
                verifyDocument();
            });
        }
    }

    function toggleDocumentHint(show){
        let hint = document.getElementById('document_hint');
        if (!hint) return;
        hint.classList.toggle('d-none', !show);
        hint.classList.toggle('d-block', show);
    }

    function revealRegisterFields(){
        let wrapper = document.getElementById('register-fields');
        if (!wrapper) return;
        wrapper.classList.add('is-visible');
        toggleDocumentHint(false);
        wrapper.querySelectorAll('input').forEach(function(input) {
            if (input.id !== 'name_reg') {
                input.setAttribute('required', 'required');
            }
        });
        let name_reg = document.getElementById('name_reg');
        if (name_reg) name_reg.setAttribute('required', 'required');
    }

    function hideRegisterFields(){
        let wrapper = document.getElementById('register-fields');
        if (!wrapper) return;
        wrapper.classList.remove('is-visible');
        toggleDocumentHint(true);
        wrapper.querySelectorAll('input').forEach(function(input) {
            input.removeAttribute('required');
        });
    }

    function resetDocumentStatus(){
        let status = document.getElementById('document_status');
        if (status) {
            status.style.display = 'none';
            status.textContent = '';
            status.className = 'd-block mt-1';
        }
    }

    function setDocumentStatus(message, type){
        let status = document.getElementById('document_status');
        if (!status) return;
        status.textContent = message;
        status.className = 'd-block mt-1 ' + (type === 'error' ? 'text-danger' : (type === 'loading' ? 'text-muted' : 'text-success'));
        status.style.display = 'block';
    }

    function setDocumentExistsStatus(message){
        let status = document.getElementById('document_status');
        if (!status) return;
        status.className = 'd-block mt-2 alert alert-warning p-3 h4 font-weight-normal';
        status.innerHTML =
            '<div class="d-flex align-items-start" style="gap:8px;">' +
                '<i class="ti ti-info-circle" style="font-size:18px;line-height:1.4;"></i>' +
                '<div><span>' + message + '</span>' +
                '<a href="#" id="go_to_login" class="fw-bold text-primary"> Iniciar sesión</a></div>' +
            '</div>';
        status.style.display = 'block';

        let goToLogin = document.getElementById('go_to_login');
        if (goToLogin) {
            goToLogin.addEventListener('click', function(e) {
                e.preventDefault();
                let container = document.getElementById('contenedor-form');
                if (container) container.classList.remove('active');
            });
        }
    }

    function verifyDocument(){
        let ruc_reg = document.getElementById('ruc_reg');
        let name_reg = document.getElementById('name_reg');
        let btnVerify = document.getElementById('btn_verify_document');
        if (!ruc_reg) return;

        const number = ruc_reg.value.trim();
        const maxLength = parseInt(ruc_reg.getAttribute('maxlength'));

        if (number.length !== maxLength) {
            setDocumentStatus(`Ingrese los ${maxLength} dígitos del documento.`, 'error');
            return;
        }

        setDocumentStatus('Consultando...', 'loading');
        if (btnVerify) btnVerify.disabled = true;

        fetch(`${SEARCH_DOC_URL}/${number}`, {
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    hideRegisterFields();
                    name_reg.value = '';
                    name_reg.readOnly = false;
                    name_reg.classList.remove('bg-light');
                    setDocumentExistsStatus(data.message);
                    return;
                }

                if (data.success && data.name) {
                    name_reg.value = data.name;
                    name_reg.readOnly = true;
                    name_reg.classList.add('bg-light');
                    setDocumentStatus('✓ ' + data.name, 'success');
                } else {
                    name_reg.readOnly = false;
                    name_reg.classList.remove('bg-light');
                    setDocumentStatus(data.message || 'No se encontraron datos. Ingrese el nombre manualmente.', 'error');
                }
                revealRegisterFields();
            })
            .catch(() => {
                name_reg.readOnly = false;
                name_reg.classList.remove('bg-light');
                setDocumentStatus('No se pudo consultar. Ingrese el nombre manualmente.', 'error');
                revealRegisterFields();
            })
            .finally(() => {
                if (btnVerify) btnVerify.disabled = false;
            });
    }

    document.addEventListener("DOMContentLoaded", function () {

        const mensajes = [
            "¿En qué podemos ayudarte?",
            "¡Escríbenos por WhatsApp!",
            "¿Tienes alguna duda?",
            "Te respondemos al instante 😉",
            "Habla con nosotros 📲",
            "Estamos para ayudarte"
        ];

        const tooltip = document.getElementById("wsTooltip");
        const texto = document.getElementById("wsTooltipText");
        /*
        function mostrarMensaje() {
            const random = mensajes[Math.floor(Math.random() * mensajes.length)];
            texto.innerText = random;

            tooltip.classList.add("show");

            setTimeout(() => {
                tooltip.classList.remove("show");
            }, 4000);
        }
        */
        // aparece cada cierto tiempo
        // setInterval(mostrarMensaje, 10000);

        // primera vez
        setTimeout(mostrarMensaje, 2000);
    });

</script>
@endpush
