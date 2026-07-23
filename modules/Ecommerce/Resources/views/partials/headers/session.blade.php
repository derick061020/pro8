@php
    // Detectar contexto de restaurant por nombre de ruta
    $isRestaurant = request()->routeIs('tenant.restaurant.*', 'restaurant.*');
@endphp
<div class="dropdown cart-dropdown" style="margin-left: 16px;">

    @guest('ecommerce')
        <a class="header-contact mr-0 login-link" href="#" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle" style="color: #fff;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 10a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
            <strong class="ml-2 log-in-text" style="font-size: 15px; color: #fff;">Iniciar sesión</strong>

            <div class="user-name-ecommerce ml-2">
                <span class="text-name">Ingresar</span>
            </div>
        </a>
    @elseauth('ecommerce')
        <a href="#" class="dropdown-toggle dropdown-toggle-ecommerce" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            data-display="static">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user icon-user-restaurant"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>

            <div class="user-name-ecommerce d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 10a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" /></svg>
                <span class="text-name ml-2 mr-1">{{ Auth::guard('ecommerce')->user()->name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right user-menu">
            <div class="dropdownmenu-wrapper dropdown-ecommerce">
                <div class="user-menu-head">
                    <span class="user-menu-name">{{ Auth::guard('ecommerce')->user()->name }}</span>
                    <span class="user-menu-email text-muted">{{ Auth::guard('ecommerce')->user()->email }}</span>
                </div>

                @if(!$isRestaurant)
                    <a href="{{ route('tenant_ecommerce_account') }}" class="dropdown-options user-menu-item">
                        <span class="user-menu-ic">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4c0 1.104 .896 2 2 2" /><path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" /></svg>
                        </span>
                        <span>Mi cuenta</span>
                    </a>
                    <a href="{{ route('tenant_document_list') }}" class="dropdown-options user-menu-item">
                        <span class="user-menu-ic">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
                        </span>
                        <span>Mis comprobantes</span>
                    </a>
                    <a href="{{ route('tenant_order_list') }}" class="dropdown-options user-menu-item">
                        <span class="user-menu-ic">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M17 17h-11v-14h-2"></path><path d="M6 5l14 1l-1 7h-13"></path></svg>
                        </span>
                        <span>Mis pedidos</span>
                    </a>
                    <div class="dropdown-divider"></div>
                @endif

                <a href="#" role="menuitem" class="dropdown-options user-menu-item user-menu-item--danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                    <span class="user-menu-ic">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-logout"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                    </span>
                    <span>Cerrar sesión</span>
                </a>

            </div>
        </div>
        <form id="logout-form-header" action="{{ route('tenant_ecommerce_logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @endauth

</div>
