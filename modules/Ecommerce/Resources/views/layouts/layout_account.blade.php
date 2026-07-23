@extends('ecommerce::layouts.layout_ecommerce_cart.index')

@section('content')
<style>
    .account-sidebar {
        background: #fff;
        border-radius: 18px;
        box-shadow: var(--shadow);
        padding: 16px;
        border: 1px solid var(--line);
    }
    .account-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .account-sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 13px;
        color: var(--dark-color);
        text-decoration: none;
        font-weight: 500;
        font-size: 14.5px;
        border-radius: 10px;
        transition: all 0.2s ease;
        line-height: 1.6;
    }
    .account-sidebar-menu a svg {
        margin-right: 15px;
        width: 18px;
        height: 18px;
        stroke: currentColor;
        color: var(--subtitle-color)
    }
    .account-sidebar-menu a .arrow {
        margin-left: auto;
        color: inherit;
    }
    .account-sidebar-menu a .arrow svg {
        margin: 0 !important;
    }
    .account-content-wrapper {
        background: #fff;
        border-radius: 18px;
        box-shadow: var(--shadow);
        min-height: 600px;
        margin-left: 15px;
        border: 1px solid var(--line);
    }
    .cart-table-container {
        padding: 35px 25px;
        border-top: 5px solid #ff8000;
        background-color: #fff;
        border-radius: 0 0 12px 12px;
    }
    .form-group-container {
        margin-bottom: 20px;
    }
    .form-group-container label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
        color: var(--dark-color);
        font-size: 13px;
    }

    /* Estilos para botones anaranjados */

    .el-button--primary.is-disabled, 
    .el-button--primary.is-disabled:hover {
        background-color: #ffb366 !important;
        border-color: #ffb366 !important;
    }

    /* Paginación anaranjada */
    .page-item.active .page-link {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .page-link {
        color: var(--primary-color) !important;
    }
    .page-link:hover {
        background-color: #fff7ed !important;
        color: var(--primary-color) !important;
    }
    .user-mini {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 8px 16px;
        border-bottom: 1px solid var(--line);
        margin-bottom: 10px;
    }
    .avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: var(--primary-color);
        color: #fff;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 17px;
        flex: none;
    }
    .user-mini .user-name {
        font-weight: 700;
        font-size: 15px;
        line-height: 1.2;
    }
    .user-mini .user-mail {
        font-size: 12px
    }
    .account-sidebar-menu li {
        margin-bottom: 3px;
    }
</style>

<div class="row mt-5 mb-5 pt-4">
    <!-- Menú Lateral -->
    <div class="col-lg-3 col-md-4 mb-4">
        <div class="account-sidebar">
            @php
                $account_user = auth('ecommerce')->user();
                $account_contact = $account_user->contact;
                $account_first_name = $account_contact && isset($account_contact->first_name) ? $account_contact->first_name : $account_user->name;
                $account_last_name = $account_contact && isset($account_contact->paternal_last_name) ? $account_contact->paternal_last_name : '';
                $account_full_name = trim($account_first_name . ' ' . $account_last_name);
                $account_initials = strtoupper(mb_substr($account_first_name, 0, 1) . mb_substr($account_last_name !== '' ? $account_last_name : mb_substr($account_first_name, 1, 1), 0, 1));
            @endphp
            <div class="user-mini">
              <div class="avatar">{{ $account_initials }}</div>
              <div>
                <div class="user-name text-dark">{{ $account_full_name }}</div>
                <div class="user-mail text-muted">{{ $account_user->email }}</div>
              </div>
            </div>
            <ul class="account-sidebar-menu">
                <li>
                    <a href="{{ route('tenant_ecommerce_account') }}" class="{{ request()->routeIs('tenant_ecommerce_account') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                        Mi perfil
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenant_order_list') }}" class="{{ request()->routeIs('tenant_order_list') ? 'active' : '' }}">
                        <svg clip-rule="evenodd" fill="currentcolor" fill-rule="evenodd" height="20" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg" id="fi_4893746">
                            <path d="m211.892 383.468c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm176.22 0c24.344 0 44.108 19.764 44.108 44.108s-19.764 44.108-44.108 44.108-44.108-19.764-44.108-44.108 19.764-44.108 44.108-44.108zm-288.464-273.226s63.534 222.705 63.534 222.705c6.591 23.103 27.703 39.034 51.727 39.034h157.478c33.502 0 61.98-24.47 67.023-57.59 4.821-31.664 11.838-77.75 17.065-112.081 2.869-18.84-2.626-37.994-15.046-52.449-12.42-14.454-30.529-22.769-49.586-22.769h-235.394l-8.72-30.567c-7.633-26.757-32.085-45.209-59.91-45.209-23.033 0-51.825 0-51.825 0-13.798 0-25 11.202-25 25s11.202 25 25 25h51.825c5.494 0 10.321 3.643 11.829 8.926zm71.066 66.85h221.129c4.482 0 8.741 1.956 11.663 5.355 2.921 3.4 4.213 7.905 3.539 12.337 0 0-17.066 112.081-17.066 112.081-1.323 8.693-8.798 15.116-17.592 15.116h-157.478c-1.693 0-3.181-1.122-3.645-2.751 0 0-40.55-142.138-40.55-142.138z"></path>
                        </svg>
                        Mis pedidos
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tenant_document_list') }}" class="{{ request()->routeIs('tenant_document_list') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"/></svg>
                        Mis comprobantes
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </span>
                    </a>
                </li>
                @if(false) {{-- Oculto hasta que se implemente la funcionalidad de favoritos --}}
                <li>
                    <a href="#" class="">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" /></svg>
                        Mis favoritos
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </span>
                    </a>
                </li>
                @endif
                @if(auth('ecommerce')->user()->addresses->count() > 0)
                <li>
                    <a href="#" class="">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                        Mis direcciones
                        <span class="arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                        </span>
                    </a>
                </li>
                @endif
                <li style="margin-top: 10px; border-top: 1px solid #f0f0f0; padding-top: 15px;">
                    <a class="text-danger option-delete" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-danger"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                        Cerrar sesión
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('tenant_ecommerce_logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="col-lg-9 col-md-8">
        <div class="account-content-wrapper">
            @yield('account_content')
        </div>
    </div>
</div>
@endsection
