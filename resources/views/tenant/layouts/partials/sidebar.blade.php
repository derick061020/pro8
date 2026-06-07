<?php

use App\Models\Tenant\Configuration;
use Modules\Inventory\Models\InventoryConfiguration;
use Modules\Hotel\Models\HotelRoom;

$configuration = Configuration::first();
$firstLevel = $path[0] ?? null;
$secondLevel = $path[1] ?? null;
$thridLevel = $path[2] ?? null;

$inventory_configuration = InventoryConfiguration::getSidebarPermissions();

// Obtener configuración visual para selector de establecimiento
$visual = $configuration->visual ?? null;
$showInSidebar = null; // will be resolved after multi-user calculation

$establishments = App\Models\Tenant\Establishment::select('id', 'description')->get();

// Obtener contador de habitaciones ocupadas
$occupiedRoomsCount = 0;
if(in_array('hotels', $vc_modules)) {
    $user = auth()->user();
    $establishment_id = $user->establishment_id;
    $occupiedRoomsCount = HotelRoom::where('establishment_id', $establishment_id)
        ->where('status', 'OCUPADO')
        ->count();
}

$multiUserCount = 0;
if(config('configuration.multi_user_enabled')) {
    try {
        $website = app(\Hyn\Tenancy\Environment::class)->tenant();
        $currentClient = \App\Models\System\Client::currentClientByWebsite($website)->first();
        if($currentClient && auth()->check()) {
            $currentUser = auth()->user();
            if(!empty($currentUser->is_multi_user) && $currentUser->is_multi_user) {
                $originMulti = \Modules\MultiUser\Models\System\MultiUser::find($currentUser->multi_user_id);
                if($originMulti) {
                    $multiUserCount = \Modules\MultiUser\Models\System\MultiUser::where('origin_client_id', $originMulti->origin_client_id)
                        ->where('origin_user_id', $originMulti->origin_user_id)
                        ->count();
                    $multiUserCount = $multiUserCount + 1;
                } else {
                    $multiUserCount = 0;
                }
            } else {
                $multiUserCount = \Modules\MultiUser\Models\System\MultiUser::where('origin_client_id', $currentClient->id)
                    ->where('origin_user_id', $currentUser->id)
                    ->count();
                $multiUserCount = $multiUserCount + 1;
            }
        }
    } catch (\Exception $e) {
        $multiUserCount = 0;
    }
}
$showMultiUser = $multiUserCount > 1 && config('configuration.multi_user_enabled');

if (is_null($showInSidebar)) {
    if (is_object($visual) && property_exists($visual, 'branch_selector_in_sidebar')) {
        $showInSidebar = (bool)$visual->branch_selector_in_sidebar;
    } elseif (is_array($visual) && array_key_exists('branch_selector_in_sidebar', $visual)) {
        $showInSidebar = (bool)$visual['branch_selector_in_sidebar'];
    } else {
        $showInSidebar = (count($establishments) > 1) || $showMultiUser;
    }
}

$current = auth()->user()->establishment_id;
$canShowBranchSelector = auth()->user()->type == 'admin' && count($establishments) > 0;

try {
    $website = app(\Hyn\Tenancy\Environment::class)->tenant();
    $currentClient = \App\Models\System\Client::currentClientByWebsite($website)->first();
    $current_client_fqdn = $currentClient->hostname->fqdn ?? '';
} catch (\Exception $e) {
    $current_client_fqdn = '';
}

$showTransfer = collect($vc_module_levels)->intersect(['inventory', 'inventory_devolutions', 'inventory_report_kardex', 'inventory_report', 'inventory_report_valued_kardex'])->isEmpty() && in_array('inventory_transfers', $vc_module_levels);
?>
<aside id="sidebar-left" class="sidebar-left {{ ($showInSidebar && $canShowBranchSelector && $showMultiUser) ? 'show-both-selectors' : (($showInSidebar && ($canShowBranchSelector || $showMultiUser)) ? 'show-branch-selector' : '') }}">
    <div class="sidebar-header sidebar-header-desktop">
        <div class="logo-container-sidebar pe-2">
            <a href="{{ route('tenant.dashboard.index') }}" class="logo pt-2 pt-md-0">
                @if($vc_company->logo)
                    <img src="{{ asset('storage/uploads/logos/' . $vc_company->logo) }}" alt="Logo" class="logo-light"
                        style="{{ $vc_company->logo_dark ? '' : '--show-light-logo: block;' }}" />
                @else
                    <img src="{{ asset('logo/tulogo.png') }}" alt="Logo" />
                @endif

                @if($vc_company->logo_dark)
                    <img src="{{ asset('storage/uploads/logos/' . $vc_company->logo_dark) }}" alt="Logo" class="logo-dark" />
                @endif
            </a>
        </div>
        <div class="sidebar-toggle-container d-none d-md-flex">
            <div class="sidebar-toggle position-relative ms-0 p-0" data-toggle-class="sidebar-left-collapsed" data-target="html"
                data-fire-event="sidebar-left-toggle" title="Colapsar menú lateral">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-layout-sidebar-left-collapse fa-angle-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M9 4v16" /> <path class="path-left" d="M15 10l-2 2l2 2" /> </svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-layout-sidebar-right-collapse fa-angle-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M9 4v16" /><path class="path-right" d="M14 10l2 2l-2 2" /></svg>
            </div>
        </div>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html"
            data-fire-event="sidebar-left-opened">
            <i class="fas fa-times"></i>
        </div>
    </div>

    @if ($canShowBranchSelector || $showMultiUser)
        <div class="establishment-selector-container mb-2" id="sidebar-selectors-container" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
            @if ($canShowBranchSelector)
                <div class="form-group mb-1 form-establishment" id="sidebar-establishment-selector-container" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
                    <label class="control-label mt-1">Cambiar sucursal</label>
                    <select
                        class="el-input__inner input-select-establishment"
                        name="establishment_selector"
                        id="sidebar-establishment-selector"
                        onchange="changeSidebarEstablishment(this.value)"
                    >
                        @foreach($establishments as $establishment)
                            <option
                                value="{{ $establishment->id }}"
                                {{ $establishment->id == $current ? 'selected' : '' }}
                            >
                                {{ $establishment->description }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif            

            @if($showMultiUser)
                <div class="sidebar-multi-user-selector-container" id="sidebar-multi-user-selector-container" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
                    <div class="sidebar-multi-user-selector-wrapper">
                        <div class="sidebar-multi-user-placeholder" aria-hidden="true">
                            <?xml version="1.0" encoding="utf-8"?><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 2400 2400" xml:space="preserve"><g stroke-width="200" stroke-linecap="round" stroke="currentColor" fill="none" id="spinner"><line x1="1200" y1="600" x2="1200" y2="100"/><line opacity="0.5" x1="1200" y1="2300" x2="1200" y2="1800"/><line opacity="0.917" x1="900" y1="680.4" x2="650" y2="247.4"/><line opacity="0.417" x1="1750" y1="2152.6" x2="1500" y2="1719.6"/><line opacity="0.833" x1="680.4" y1="900" x2="247.4" y2="650"/><line opacity="0.333" x1="2152.6" y1="1750" x2="1719.6" y2="1500"/><line opacity="0.75" x1="600" y1="1200" x2="100" y2="1200"/><line opacity="0.25" x1="2300" y1="1200" x2="1800" y2="1200"/><line opacity="0.667" x1="680.4" y1="1500" x2="247.4" y2="1750"/><line opacity="0.167" x1="2152.6" y1="650" x2="1719.6" y2="900"/><line opacity="0.583" x1="900" y1="1719.6" x2="650" y2="2152.6"/><line opacity="0.083" x1="1750" y1="247.4" x2="1500" y2="680.4"/><animateTransform attributeName="transform" attributeType="XML" type="rotate" keyTimes="0;0.08333;0.16667;0.25;0.33333;0.41667;0.5;0.58333;0.66667;0.75;0.83333;0.91667" values="0 1199 1199;30 1199 1199;60 1199 1199;90 1199 1199;120 1199 1199;150 1199 1199;180 1199 1199;210 1199 1199;240 1199 1199;270 1199 1199;300 1199 1199;330 1199 1199" dur="0.83333s" begin="0s" repeatCount="indefinite" calcMode="discrete"/></g></svg>
                            Cargando...
                        </div>
                        <tenant-multi-users-change-client class="sidebar-multi-user-selector"></tenant-multi-users-change-client>
                    </div>
                </div>
            @endif
        </div>
    @endif
    @if ($canShowBranchSelector || $showMultiUser)
        <div class="contain-icon-establishment-wrapper" id="sidebar-establishment-icon-wrapper" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
            <div class="contain-icon-establishment" id="establishment-icon-trigger" role="button" tabindex="0" aria-label="Cambiar sucursal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 21l18 0"></path><path d="M4 21l0 -10"></path><path d="M20 21l0 -10"></path><path d="M5 11l14 0"></path><path d="M5 11l1 -6h12l1 6"></path><path d="M9 21l0 -8l6 0l0 8"></path></svg>
            </div>
            <div class="tooltip-right" role="tooltip">Cambiar @if ($canShowBranchSelector) sucursal @endif @if ($canShowBranchSelector && $showMultiUser) o @endif @if ($showMultiUser) empresa @endif</div>
            <div class="establishment-dropdown" id="establishment-dropdown">
                <div class="establishment-dropdown-header">
                    <span>Cambiar @if ($canShowBranchSelector) Sucursal @endif @if ($canShowBranchSelector && $showMultiUser) / @endif @if ($showMultiUser) Empresa @endif</span>
                </div>
                <div class="establishment-dropdown-content">
                    @if ($canShowBranchSelector)
                        <div id="branch-selector-dropdown" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
                            <label class="control-label mt-0">Cambiar sucursal:</label>
                            <select
                                class="el-input__inner input-select-establishment"
                                name="establishment_selector_dropdown"
                                id="dropdown-establishment-selector"
                                onchange="changeSidebarEstablishment(this.value)"
                            >
                                @foreach($establishments as $establishment)
                                    <option
                                        value="{{ $establishment->id }}"
                                        {{ $establishment->id == $current ? 'selected' : '' }}
                                    >
                                        {{ $establishment->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @if($showMultiUser)
                        <div class="sidebar-multi-user-selector-container" id="multi-user-selector-dropdown" style="display: {{ $showInSidebar ? 'block' : 'none' }};">
                            <div class="sidebar-multi-user-selector-wrapper">
                                <div class="sidebar-multi-user-placeholder" aria-hidden="true"></div>
                                <tenant-multi-users-change-client class="sidebar-multi-user-selector"></tenant-multi-users-change-client>
                            </div>
                        </div>
                    @endif
                </div>                
            </div>
        </div>
    @endif
    <div class="nano">
        <div class="sidebar-header sidebar-header-mobile">
            <a href="{{route('tenant.dashboard.index')}}" class="logo pt-2 pt-md-0 logo-container-sidebar">
                @if($vc_company->logo)
                    <img src="{{ asset('storage/uploads/logos/' . $vc_company->logo) }}" alt="Logo" class="logo-light"
                        style="{{ $vc_company->logo_dark ? '' : '--show-light-logo: block;' }}" />
                @else
                    <img src="{{ asset('logo/tulogo.png') }}" alt="Logo" />
                @endif

                @if($vc_company->logo_dark)
                    <img src="{{ asset('storage/uploads/logos/' . $vc_company->logo_dark) }}" alt="Logo"
                        class="logo-dark" />
                @endif
            </a>
            <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html"
                data-fire-event="sidebar-left-opened">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div class="nano-content nano-content-mobile pt-0">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main nav-main-mobile">

                    @if(in_array('dashboard', $vc_modules))
                        <li class="{{ ($firstLevel === 'dashboard') ? 'nav-active' : '' }}">
                            <a class="nav-link dashboard-link" href="{{ route('tenant.dashboard.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-dashboard">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M13.45 11.55l2.05 -2.05" />
                                    <path d="M6.4 20a9 9 0 1 1 11.2 0z" />
                                </svg>
                                <span>DASHBOARD</span>
                            </a>
                        </li>
                    @endif
                    {{-- @if(in_array('hotels', $vc_modules) || in_array('documentary-procedure', $vc_modules))
                    <li class="nav-description">Módulos extras</li>
                    @endif --}}
                    @if(in_array('hotels', $vc_modules))
                        <li class=" nav-parent {{ ($firstLevel === 'hotels') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-building-skyscraper">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 21l18 0" />
                                    <path d="M5 21v-14l8 -4v18" />
                                    <path d="M19 21v-10l-6 -4" />
                                    <path d="M9 9l0 .01" />
                                    <path d="M9 12l0 .01" />
                                    <path d="M9 15l0 .01" />
                                    <path d="M9 18l0 .01" />
                                </svg>
                                <span>Hoteles</span>
                                @if($occupiedRoomsCount > 0)
                                    <span class="hotel-rooms-counter">{{ $occupiedRoomsCount }}</span>
                                @endif
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('hotels_reception', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'reception')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/reception') }}">Recepción</a>
                                    </li>
                                @endif
                                @if(in_array('hotels_rates', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'rates')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/rates') }}">Tarifas</a>
                                    </li>
                                @endif
                                @if(in_array('hotels_floors', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'floors')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/floors') }}">Ubicaciones</a>
                                    </li>
                                @endif
                                @if(in_array('hotels_cats', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'categories')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/categories') }}">Categorías</a>
                                    </li>
                                @endif
                                @if(in_array('hotels_rooms', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'rooms')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/rooms') }}">Habitaciones</a>
                                    </li>
                                @endif
                                @if(in_array('hotels_reception', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'hotels') && ($secondLevel === 'reservations')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ url('hotels/reservations/calendar') }}">Calendario de Reservas</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Preventas --}}
                    @if(in_array('preventa', $vc_modules))
                        <li
                            class="
                                                                                                                                            nav-parent
                                                                                                                                            {{ ($firstLevel === 'quotations') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'order-notes') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'sale-opportunities') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'contracts') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'production-orders') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'technical-services') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                ">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                <span>Preventa</span>
                            </a>
                            <ul class="nav nav-children" style="">
                                @if(in_array('sale-opportunity', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'sale-opportunities') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.sale_opportunities.index')}}">
                                            Oportunidad de venta
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('quotations', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'quotations') ? 'nav-active' : '' }} nav-item-with-action">
                                        <a class="nav-link pe-5" href="{{ route('tenant.quotations.index') }}">
                                            Cotizaciones
                                        </a>
                                        <button
                                            type="button"
                                            class="{{ ($firstLevel === 'quotations') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                            title="Crear cotización"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.quotations.create') }}';"
                                        >
                                            Crear
                                        </button>
                                    </li>
                                @endif

                                @if(in_array('contracts', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'contracts') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('tenant.contracts.index') }}">
                                            Contratos
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('order-note', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'order-notes') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.order_notes.index')}}">
                                            Pedidos
                                        </a>
                                    </li>
                                @endif

                                @if(in_array('technical-service', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'technical-services') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.technical_services.index')}}">
                                            Servicio de soporte técnico
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Ventas --}}
                    @if(in_array('documents', $vc_modules))
                        <li
                            class="
                                                                                                                                            nav-parent
                                                                                                                                            {{ ($firstLevel === 'documents' && $secondLevel !== 'create' && $secondLevel !== 'not-sent' && $secondLevel !== 'regularize-shipping') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'documents' && $secondLevel === 'create') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'sale-notes') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'regularize-shipping') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'pos') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                ">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-receipt-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /><path d="M14 8h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5m2 0v1.5m0 -9v1.5" /></svg>
                                <span>VENTAS</span>
                            </a>
                            <ul class="nav nav-children" style="">
                                {{-- @if(auth()->user()->type != 'integrator' && $vc_company->soap_type_id != '03')
                                    @if(in_array('documents', $vc_modules))
                                        @if(in_array('new_document', $vc_module_levels))
                                            <li
                                                class="{{ ($firstLevel === 'documents' && $secondLevel === 'create') ? 'nav-active' : '' }}">
                                                <a class="nav-link" href="{{route('tenant.documents.create')}}">Nuevo Comprobante</a>
                                            </li>
                                        @endif
                                    @endif
                                @endif --}}

                                @if(in_array('documents', $vc_modules) && $vc_company->soap_type_id != '03')
                                    @if(in_array('list_document', $vc_module_levels))
                                        <li
                                            class="{{ ($firstLevel === 'documents' && $secondLevel != 'create' && $secondLevel != 'not-sent' && $secondLevel != 'regularize-shipping') ? 'nav-active' : '' }} {{ ($firstLevel === 'documents' && $secondLevel === 'create') ? 'nav-active' : '' }} nav-item-with-action">
                                            <a class="nav-link pe-5" href="{{route('tenant.documents.index')}}">Boleta/factura</a>
                                            <button
                                                type="button"
                                                class="{{ ($firstLevel === 'documents') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                title="Crear comprobante"
                                                onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.documents.create') }}';"
                                            >
                                                Crear
                                            </button>
                                        </li>
                                    @endif
                                @endif

                                @if(in_array('sale_notes', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'sale-notes') ? 'nav-active' : '' }} nav-item-with-action">
                                        <a class="nav-link pe-5" href="{{route('tenant.sale_notes.index')}}">Notas de Venta</a>
                                        <button
                                            type="button"
                                            class="{{ ($firstLevel === 'sale-notes') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                            title="Crear nota de venta"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.sale_notes.create') }}';"
                                        >
                                            Crear
                                        </button>
                                    </li>
                                @endif

                                @if(in_array('pos', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'pos' && !$secondLevel) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('tenant.pos.index') }}">Punto de venta</a>
                                    </li>
                                @endif

                                {{-- Venta Rápida --}}
                                @if(in_array('pos_garage', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'pos' && $secondLevel === 'garage') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('tenant.pos.garage') }}">Venta rápida <span
                                                style="font-size:.65rem;">(Grifos y Markets)</span></a>
                                    </li>
                                @endif

                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->type != 'integrator')
                                    @if(in_array('purchases', $vc_modules))
                                                    <li
                                                        class="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            nav-parent
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ (
                                            $firstLevel === 'purchases' ||
                                            ($firstLevel === 'persons' && $secondLevel === 'suppliers') ||
                                            $firstLevel === 'expenses' ||
                                            $firstLevel === 'purchase-quotations' ||
                                            $firstLevel === 'purchase-orders' ||
                                            $firstLevel === 'fixed-asset'
                                        ) ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ">
                                                        <a class="nav-link" href="#">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-bag">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path
                                                                    d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1 -2.966 2.544h-6.852a3 3 0 0 1 -2.965 -2.544l-1.255 -8.152a2 2 0 0 1 1.977 -2.304z" />
                                                                <path d="M9 11v-5a3 3 0 0 1 6 0v5" />
                                                            </svg>
                                                            <span>Compras</span>
                                                        </a>
                                                        <ul class="nav nav-children">
                                                            {{-- @if(in_array('purchases_create', $vc_module_levels))
                                                                <li
                                                                    class="{{ ($firstLevel === 'purchases' && $secondLevel === 'create') ? 'nav-active' : '' }}">
                                                                    <a class="nav-link" href="{{route('tenant.purchases.create')}}">Nuevo</a>
                                                                </li>
                                                            @endif --}}
                                                            @if(in_array('purchases_list', $vc_module_levels))
                                                                <li
                                                                    class="{{ ($firstLevel === 'purchases' && $secondLevel != 'create') ? 'nav-active' : '' }} {{ ($firstLevel === 'purchases' && $secondLevel === 'create') ? 'nav-active' : '' }} nav-item-with-action">
                                                                    <a class="nav-link pe-5" href="{{route('tenant.purchases.index')}}">Listado</a>
                                                                    <button
                                                                        type="button"
                                                                        class="{{ ($firstLevel === 'purchases') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                                        title="Nuevo compra"
                                                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.purchases.create') }}';"
                                                                    >
                                                                        Crear
                                                                    </button>
                                                                </li>
                                                            @endif
                                                            @if(in_array('purchases_orders', $vc_module_levels))
                                                                <li class="{{ ($firstLevel === 'purchase-orders') ? 'nav-active' : '' }} nav-item-with-action">
                                                                    <a class="nav-link pe-5" href="{{route('tenant.purchase-orders.index')}}">Ord. de compra</a>
                                                                    <button
                                                                        type="button"
                                                                        class="{{ ($firstLevel === 'purchase-orders') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                                        title="Nueva orden de compra"
                                                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.purchase-orders.create') }}';"
                                                                    >
                                                                        Crear
                                                                    </button>
                                                                </li>
                                                            @endif

                                                            @if(in_array('purchases_expenses', $vc_module_levels))
                                                                <li class="{{ ($firstLevel === 'expenses') ? 'nav-active' : '' }} nav-item-with-action">
                                                                    <a class="nav-link pe-5" href="{{route('tenant.expenses.index')}}">Gastos diversos</a>
                                                                    <button
                                                                        type="button"
                                                                        class="{{ ($firstLevel === 'expenses') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                                        title="Nuevo gasto diverso"
                                                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.expenses.create') }}';"
                                                                    >
                                                                        Crear
                                                                    </button>
                                                                </li>
                                                            @endif
                                                            @if(in_array('purchases_suppliers', $vc_module_levels))
                                                                <li class="{{ ($firstLevel === 'persons') ? 'nav-active' : '' }}">
                                                                    <a class="nav-link" href="{{route('tenant.persons.index', ['type' => 'suppliers'])}}">
                                                                        Proveedores
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if(in_array('purchases_quotations', $vc_module_levels))
                                                                <li class="{{ ($firstLevel === 'purchase-quotations') ? 'nav-active' : '' }} nav-item-with-action">
                                                                    <a class="nav-link pe-5" href="{{route('tenant.purchase-quotations.index')}}">
                                                                        Solicitar cotización
                                                                    </a>
                                                                    <button
                                                                        type="button"
                                                                        class="{{ ($firstLevel === 'purchase-quotations') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                                        title="Nueva solicitud de cotización"
                                                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.purchase-quotations.create') }}';"
                                                                    >
                                                                        Crear
                                                                    </button>
                                                                </li>
                                                            @endif
                                                            @if(in_array('purchases_fixed_assets_items', $vc_module_levels))
                                                                <li
                                                                    class="{{ ($firstLevel === 'fixed-asset' && $secondLevel === 'items') ? 'nav-active' : '' }}">
                                                                    <a class="nav-link" href="{{ route('tenant.fixed_asset_items.index') }}">Activos
                                                                        fijos</a>
                                                                </li>
                                                            @endif
                                                            @if(in_array('purchases_fixed_assets_purchases', $vc_module_levels))
                                                                <li
                                                                    class="{{ ($firstLevel === 'fixed-asset' && $secondLevel === 'purchases') ? 'nav-active' : '' }} nav-item-with-action">
                                                                    <a class="nav-link pe-5" href="{{ route('tenant.fixed_asset_purchases.index') }}">Comprar activo fijo</a>
                                                                    <button
                                                                        type="button"
                                                                        class="{{ ($firstLevel === 'fixed-asset' && $secondLevel === 'purchases') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                                                        title="Nueva compra de activo fijo"
                                                                        onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('tenant.fixed_asset_purchases.create') }}';"
                                                                    >
                                                                        Crear
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </li>
                                    @endif

                                    {{-- Clientes --}}
                                    @if(in_array('persons', $vc_modules))
                                        <li
                                            class="nav-parent
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'persons' && $secondLevel === 'customers') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $firstLevel === 'person-types' ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $firstLevel === 'agents' ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ">
                                            <a class="nav-link" href="#">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-users-group">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                                                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                                                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                                                </svg>
                                                <span>Clientes</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                @if(in_array('clients', $vc_module_levels))
                                                    <li
                                                        class="{{ ($firstLevel === 'persons' && $secondLevel === 'customers') ? 'nav-active' : '' }}">
                                                        <a class="nav-link"
                                                            href="{{route('tenant.persons.index', ['type' => 'customers'])}}">Clientes</a>
                                                    </li>
                                                @endif
                                                @if(in_array('clients_types', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'person-types') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.person_types.index')}}">Tipos de clientes</a>
                                                    </li>
                                                @endif

                                                @if($configuration->enabled_sales_agents)
                                                    <li class="{{ ($firstLevel === 'agents') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.agents.index')}}">Agentes</a>
                                                    </li>
                                                @endif

                                            </ul>
                                        </li>
                                    @endif



                                    {{-- Productos --}}
                                    @if(in_array('items', $vc_modules))
                                        <li
                                            class="nav-parent
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'items') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'services') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'categories') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'brands') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'item-lots') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ ($firstLevel === 'item-sets') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ">
                                            <a class="nav-link" href="#">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-category-2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M14 4h6v6h-6z" />
                                                    <path d="M4 14h6v6h-6z" />
                                                    <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                    <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                </svg>
                                                <span>Productos/Servicios</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                @if(in_array('items', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'items') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.items.index')}}">Productos</a>
                                                    </li>
                                                @endif
                                                @if(in_array('items_packs', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'item-sets') ? 'nav-active' : '' }}">
                                                        <a class="nav-link"
                                                            href="{{route('tenant.item_sets.index')}}">Conjuntos y Packs</a>
                                                    </li>
                                                @endif
                                                @if(in_array('items_services', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'services') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.services')}}">Servicios</a>
                                                    </li>
                                                @endif
                                                @if(in_array('items_categories', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'categories') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.categories.index')}}">Categorías</a>
                                                    </li>
                                                @endif
                                                @if(in_array('items_brands', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'brands') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.brands.index')}}">Marcas</a>
                                                    </li>
                                                @endif
                                                @if(in_array('items_lots', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'item-lots') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.item-lots.index')}}">Series</a>
                                                    </li>
                                                @endif

                                                <!-- <li class="{{ ($firstLevel === 'zones')?'nav-active':'' }}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <a class="nav-link"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        href="{{route('tenant.zone.index')}}">Zonas</a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </li> -->

                                            </ul>
                                        </li>
                                    @endif


                                    {{-- Inventario --}}
                                    @if(in_array('inventory', $vc_modules))
                                        <li
                                            class="nav-parent
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ (in_array($firstLevel, ['inventory', 'moves', 'transfers', 'devolutions', 'extra_info_items', 'inventory-review']) | ($firstLevel === 'reports' && in_array($secondLevel, ['kardex', 'inventory', 'valued-kardex']))) ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ">
                                            <a class="nav-link" href="#">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-building-warehouse">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 21v-13l9 -4l9 4v13" />
                                                    <path d="M13 13h4v8h-10v-6h6" />
                                                    <path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3" />
                                                </svg>
                                                <span>Inventario</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                @if(in_array('inventory', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'inventory') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('inventory.index')}}">Movimientos</a>
                                                    </li>
                                                @endif
                                                @if($showTransfer)
                                                    <li class="{{ ($firstLevel === 'transfers') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('transfers.index')}}">Traslados</a>
                                                    </li>
                                                @endif
                                                @if(in_array('inventory_devolutions', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'devolutions') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('devolutions.index')}}">Devolucion a proveedor</a>
                                                    </li>
                                                @endif
                                                @if(in_array('inventory_report_kardex', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'reports') && ($secondLevel === 'kardex')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('reports.kardex.index')}}">Reporte Kardex</a>
                                                    </li>
                                                @endif
                                                @if(in_array('inventory_report', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'reports') && ($secondLevel == 'inventory')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('reports.inventory.index')}}">Reporte Inventario</a>
                                                    </li>
                                                @endif
                                                @if(in_array('inventory_report_valued_kardex', $vc_module_levels))
                                                    {{-- <li class="{{ ($firstLevel === 'warehouses')?'nav-active':'' }}">
                                                        <a class="nav-link" href="{{route('warehouses.index')}}">Almacenes</a>
                                                    </li> --}}
                                                    <li
                                                        class="{{(($firstLevel === 'reports') && ($secondLevel === 'valued-kardex')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('reports.valued_kardex.index')}}">Kardex
                                                            valorizado</a>
                                                    </li>
                                                @endif
                                                @if(in_array('production_app', $vc_modules) && $configuration->isShowExtraInfoToItem())
                                                    <li class="{{($firstLevel === 'extra_info_items') ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('extra_info_items.index')}}">Datos extra de items</a>
                                                    </li>
                                                @endif
                                                @if($inventory_configuration->inventory_review)
                                                    <li class="{{ ($firstLevel === 'inventory-review') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.inventory-review.index')}}">Revisión de
                                                            inventario</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    @endif

                    @endif

                    @if(in_array('finance', $vc_modules))

                                        <li
                                            class="nav-parent {{ $firstLevel === 'finances' && in_array($secondLevel, [
                            'global-payments',
                            'balance',
                            'payment-method-types',
                            'unpaid',
                            'to-pay',
                            'income',
                            'transactions',
                            'movements'
                        ]) ? 'nav-active nav-expanded' : ''}}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ ($firstLevel === 'cash') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {{ ($firstLevel === 'bank_loan') ? 'nav-active nav-expanded' : '' }}">
                                            <a class="nav-link" href="#">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calculator">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                    <path
                                                        d="M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" />
                                                    <path d="M8 14l0 .01" />
                                                    <path d="M12 14l0 .01" />
                                                    <path d="M16 14l0 .01" />
                                                    <path d="M8 17l0 .01" />
                                                    <path d="M12 17l0 .01" />
                                                    <path d="M16 17l0 .01" />
                                                </svg>
                                                <span>Finanzas</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                @if(in_array('cash', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'cash') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.cash.index')}}">Caja general</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_movements', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'movements')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.movements.index')}}">Movimientos</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_movements', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'transactions')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link"
                                                            href="{{route('tenant.finances.transactions.index')}}">Transacciones</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_incomes', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'income')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.income.index')}}">Ingresos</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_unpaid', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'unpaid')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.unpaid.index')}}">Cuentas por
                                                            cobrar</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_to_pay', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'to-pay')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.to_pay.index')}}">Cuentas por
                                                            pagar</a>
                                                    </li>
                                                @endif
                                                @if(in_array('finances_payments', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'global-payments')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.global_payments.index')}}">Pagos</a>
                                                    </li>
                                                @endif
                                                {{-- @if(in_array('finances_balance', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'balance')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link" href="{{route('tenant.finances.balance.index')}}">Balance</a>
                                                    </li>
                                                @endif --}}
                                                @if(in_array('finances_payment_method_types', $vc_module_levels))
                                                    <li
                                                        class="{{(($firstLevel === 'finances') && ($secondLevel == 'payment-method-types')) ? 'nav-active' : ''}}">
                                                        <a class="nav-link"
                                                            href="{{route('tenant.finances.payment_method_types.index')}}">Ingresos y
                                                            Egresos - M. Pago</a>
                                                    </li>
                                                @endif
                                                @if(in_array('purchases_expenses', $vc_module_levels))
                                                    <li class="{{ ($firstLevel === 'bank_loan') ? 'nav-active' : '' }}">
                                                        <a class="nav-link" href="{{route('tenant.bank_loan.index')}}">Credito Bancario</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                    @endif

                    @if(in_array('guia', $vc_modules) && $vc_company->soap_type_id != '03')
                        <li
                            class="nav-parent
                                                                                                                                                    {{ ($firstLevel === 'dispatches') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                    {{ ($firstLevel === 'drivers') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                    {{ ($firstLevel === 'dispatchers') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                    {{ ($firstLevel === 'transports') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                    {{ ($firstLevel === 'dispatch_carrier') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                                    {{ ($firstLevel === 'dispatch_addresses') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-truck">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" />
                                </svg>
                                <span>Guías de remisión</span>
                            </a>
                            <ul class="nav nav-children" style="">
                                @if(in_array('dispatches', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'dispatches') ? 'nav-active' : '' }} nav-item-with-action">
                                        <a class="nav-link pe-5" href="{{route('tenant.dispatches.index')}}">G.R. Remitente</a>
                                        <button
                                            type="button"
                                            class="{{ ($firstLevel === 'dispatches') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                            title="Nueva guía de remisión"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ url('dispatches/create') }}';"
                                        >
                                            Crear
                                        </button>
                                    </li>
                                @endif
                                @if(in_array('dispatch_carrier', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'dispatch_carrier') ? 'nav-active' : '' }} nav-item-with-action">
                                        <a class="nav-link pe-5" href="{{route('tenant.dispatch_carrier.index')}}">G.R. Transportista</a>
                                        <button
                                            type="button"
                                            class="{{ ($firstLevel === 'dispatch_carrier') ? 'second-buton' : 'btn-primary' }} btn btn-xs nav-action m-0 py-0"
                                            title="Nueva guía de remisión"
                                            onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ url('dispatch_carrier/create') }}';"
                                        >
                                            Crear
                                        </button>
                                    </li>
                                @endif
                                @if(in_array('dispatchers', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'dispatchers') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.dispatchers.index')}}">Transportistas</a>
                                    </li>
                                @endif
                                @if(in_array('drivers', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'drivers') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.drivers.index')}}">Conductores</a>
                                    </li>
                                @endif
                                @if(in_array('transports', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'transports') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.transports.index')}}">Vehículos</a>
                                    </li>
                                @endif
                                {{--
                                @if(in_array('origin_addresses', $vc_module_levels))
                                <li class="{{ ($firstLevel === 'origin_addresses')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.origin_addresses.index')}}">Direcciones de
                                        partida</a>
                                </li>
                                @endif
                                @if(in_array('dispatch_addresses', $vc_module_levels))
                                <li class="{{ ($firstLevel === 'dispatch_addresses')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant.dispatch-addresses.index')}}">Direcciones de
                                        llegada</a>
                                </li>
                                @endif--}}
                            </ul>
                        </li>
                    @endif

                    @if(in_array('comprobante', $vc_modules))
                        <li
                            class="nav-parent
                                                                                                                                            {{ ($secondLevel === 'not-sent') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($secondLevel === 'regularize-shipping') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'summaries') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'voided') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-unknown">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M12 17v.01" />
                                    <path d="M12 14a1.5 1.5 0 1 0 -1.14 -2.474" />
                                </svg>
                                <span>Comprobantes pendientes</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('comprobante', $vc_modules) && $vc_company->soap_type_id != '03')
                                    @if(in_array('document_not_sent', $vc_module_levels))
                                        <li
                                            class="{{ ($firstLevel === 'documents' && $secondLevel === 'not-sent') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.documents.not_sent')}}">
                                                Comprobantes no enviados
                                            </a>
                                        </li>
                                    @endif
                                    @if(in_array('regularize_shipping', $vc_module_levels))
                                        <li
                                            class="{{ ($firstLevel === 'documents' && $secondLevel === 'regularize-shipping') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.documents.regularize_shipping')}}">
                                                CPE pendientes de rectificación
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                @if(in_array('summary_voided', $vc_module_levels) && $vc_company->soap_type_id != '03')

                                    <li class="{{ ($firstLevel === 'summaries') ? 'nav-active' : '' }}">
                                        <a class="nav-link text-danger" href="{{route('tenant.summaries.index')}}">
                                            Resúmenes
                                        </a>
                                    </li>
                                    <li class="{{ ($firstLevel === 'voided') ? 'nav-active' : '' }}">
                                        <a class="nav-link text-danger" href="{{route('tenant.voided.index')}}">
                                            Anulaciones
                                        </a>
                                    </li>

                                @endif
                            </ul>
                        </li>
                    @endif


                    @if(in_array('advanced', $vc_modules) && $vc_company->soap_type_id != '03')
                        <li
                            class="nav-parent
                                                                                                                                            {{ ($firstLevel === 'retentions') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'perceptions') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'order-forms') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'contingencies') ? 'nav-active nav-expanded' : '' }}
                                                                                                                                            {{ ($firstLevel === 'purchase-settlements') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-text">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <path
                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    <path d="M9 12h6" />
                                    <path d="M9 16h6" />
                                </svg>
                                <span>Comprobantes avanzados</span>
                            </a>
                            <ul class="nav nav-children" style="">
                                @if(in_array('advanced_retentions', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'retentions') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.retentions.index')}}">Retenciones</a>
                                    </li>
                                @endif
                                @if(in_array('advanced_perceptions', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'perceptions') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.perceptions.index')}}">Percepciones</a>
                                    </li>
                                @endif
                                @if(in_array('advanced_purchase_settlements', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'purchase-settlements') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.purchase-settlements.index')}}">Liquidaciones
                                            de
                                            compra</a>
                                    </li>
                                @endif
                                @if(in_array('advanced_order_forms', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'order-forms') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.order_forms.index')}}">Ordenes de pedido</a>
                                    </li>
                                @endif
                                @if(auth()->user()->type != 'integrator' && in_array('documents', $vc_modules))
                                    @if(auth()->user()->type != 'integrator' && in_array('document_contingengy', $vc_module_levels) && $vc_company->soap_type_id != '03')
                                        <li class="{{ ($firstLevel === 'contingencies') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.contingencies.index')}}">
                                                Documentos de contingencia
                                            </a>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(in_array('accounting', $vc_modules))
                        <li
                            class="nav-parent {{ ($firstLevel === 'account' || $firstLevel === 'accounting_ledger' || $firstLevel === 'sire') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-chart-histogram">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 3v18h18" />
                                    <path d="M20 18v3" />
                                    <path d="M16 16v5" />
                                    <path d="M12 13v8" />
                                    <path d="M8 16v5" />
                                    <path d="M3 11c6 0 5 -5 9 -5s3 5 9 5" />
                                </svg>
                                <span>Contabilidad</span>
                            </a>
                            <ul class="nav nav-children" style="">
                                @if(in_array('account_report', $vc_module_levels))
                                    <li
                                        class="{{(($firstLevel === 'account') && ($secondLevel === 'format')) ? 'nav-active' : ''}}">
                                        <a class="nav-link" href="{{ route('tenant.account_format.index') }}">Exportar
                                            reporte</a>
                                    </li>
                                @endif
                                @if(in_array('account_formats', $vc_module_levels))
                                    <li class="{{(($firstLevel === 'account') && ($secondLevel == '')) ? 'nav-active' : ''}}">
                                        <a class="nav-link" href="{{ route('tenant.account.index') }}">Exportar formatos - Sis.
                                            Contable</a>
                                    </li>
                                @endif
                                @if(in_array('account_summary', $vc_module_levels))
                                    <li
                                        class="{{(($firstLevel === 'account') && ($secondLevel == 'summary-report')) ? 'nav-active' : ''}}">
                                        <a class="nav-link" href="{{ route('tenant.account_summary_report.index') }}">Reporte
                                            resumido -
                                            Ventas</a>
                                    </li>
                                @endif
                                <li class="{{(($firstLevel === 'accounting_ledger')) ? 'nav-active' : ''}}">
                                    <a class="nav-link" href="{{ route('tenant.accounting_ledger.create') }}">
                                        Libro Mayor
                                    </a>
                                </li>
                                <li class="nav-parent {{ ($firstLevel === 'sire') ? 'nav-active nav-expanded' : '' }}">
                                    <a class="nav-link" href="#">
                                        <span>SIRE</span>
                                    </a>
                                    <ul class="nav nav-children" style="">
                                        <li class="{{ ($secondLevel === 'sale') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.sire.sale')}}">Ventas</a>
                                        </li>
                                        <li class="{{ ($secondLevel === 'purchase') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.sire.purchase')}}">Compras</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(in_array('reports', $vc_modules))
                        <li
                            class="{{  ($firstLevel === 'reports' && in_array($secondLevel, ['purchases', 'search', 'sales', 'customers', 'items', 'general-items', 'consistency-documents', 'quotations', 'sale-notes', 'cash', 'commissions', 'document-hotels', 'validate-documents', 'document-detractions', 'commercial-analysis', 'order-notes-consolidated', 'order-notes-general', 'sales-consolidated', 'user-commissions', 'fixed-asset-purchases', 'massive-downloads', 'tips'])) ? 'nav-active' : ''}} {{ in_array($firstLevel, ['list-reports', 'system-activity-logs']) ? 'nav-active' : '' }}">
                            {{--
                        <li
                            class="{{  ($firstLevel === 'reports' && in_array($secondLevel, ['purchases', 'search','sales','customers','items', 'general-items','consistency-documents', 'quotations', 'sale-notes','cash','commissions','document-hotels', 'validate-documents', 'document-detractions','commercial-analysis', 'order-notes-consolidated', 'order-notes-general', 'sales-consolidated', 'user-commissions', 'fixed-asset-purchases', 'massive-downloads', 'tips'])) ? 'nav-active' : ''}} {{ $firstLevel === 'list-reports' ? 'nav-active' : '' }}">
                            --}}
                            <a class="nav-link dashboard-link" href="{{ url('list-reports') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-analytics">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    <path d="M9 17l0 -5" />
                                    <path d="M12 17l0 -1" />
                                    <path d="M15 17l0 -3" />
                                </svg>
                                <span>Reportes</span>
                            </a>
                        </li>
                    @endif

                    {{-- Tienda virtual --}}
                    @if(in_array('ecommerce', $vc_modules))
                        <li
                            class="nav-parent
                                                                                                                                            {{ in_array($firstLevel, ['ecommerce', 'items_ecommerce', 'tags', 'promotions', 'orders', 'configuration']) ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M17 17h-11v-14h-2" />
                                    <path d="M6 5l14 1l-1 7h-13" />
                                </svg>
                                <span>Tienda Virtual</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('ecommerce', $vc_module_levels))
                                    <li class="">
                                        <a class="nav-link" onclick="window.open( '{{ route("tenant.ecommerce.index") }} ')">Ir
                                            a
                                            Tienda</a>
                                    </li>
                                @endif
                                @if(in_array('ecommerce_orders', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'orders') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant_orders_index')}}">Pedidos</a>
                                    </li>
                                @endif
                                @if(in_array('ecommerce_items', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'items_ecommerce') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.items_ecommerce.index')}}">Productos Tienda
                                            Virtual</a>
                                    </li>
                                @endif

                                <li class="{{ ($secondLevel === 'item-sets') ? 'nav-active' : '' }}">
                                    <a class="nav-link"
                                        href="{{route('tenant.ecommerce.item_sets.index')}}">Conjuntos y Packs</a>
                                </li>

                                @if(in_array('ecommerce_tags', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'tags') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.tags.index')}}">Tags -
                                            Categorias(Etiquetas)</a>
                                    </li>
                                @endif
                                @if(in_array('ecommerce_promotions', $vc_module_levels))
                                    <li class="{{ ($firstLevel === 'promotions') ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{route('tenant.promotion.index')}}">Promociones(Banners)</a>
                                    </li>
                                @endif
                                {{-- @if(in_array('ecommerce_settings', $vc_module_levels))
                                <li class="{{ ($secondLevel === 'configuration')?'nav-active':'' }}">
                                    <a class="nav-link" href="{{route('tenant_ecommerce_configuration')}}">Configuración</a>
                                </li>
                                @endif --}}
                            </ul>
                        </li>
                    @endif

                    {{-- Restaurante --}}
                    @if(in_array('restaurant_app', $vc_modules))
                        <li class=" nav-parent {{ ($firstLevel === 'restaurant') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12zm0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" />
                                </svg>
                                <span>Restaurante</span>
                            </a>
                            <ul class="nav nav-children">
                                {{-- <li
                                    class="nav-parent
                                                                                                                                                    {{ ($secondLevel != null && $secondLevel == 'cash' && $thridLevel == 'pos')?'nav-active nav-expanded':'' }}">
                                    <a class="nav-link" href="#">
                                        POS
                                    </a>
                                    <ul class="nav nav-children">
                                        <li
                                            class="{{ ($secondLevel != null && $secondLevel == 'cash' && $thridLevel == 'pos')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.restaurant.cash.filter-pos')}}">
                                                Caja Chica
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}
                                {{-- <li
                                    class="nav-parent {{ ($secondLevel != null && $secondLevel == 'cash' && $thridLevel == '')?'nav-active nav-expanded':'' }}">
                                    <a class="nav-link" href="#">
                                        Mesas
                                    </a>
                                    <ul class="nav nav-children">
                                        <li
                                            class="{{ ($secondLevel != null && $secondLevel == 'cash' && $thridLevel == '')?'nav-active':'' }}">
                                            <a class="nav-link" href="{{route('tenant.restaurant.cash.index')}}">
                                                Caja Chica
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}

                                <li
                                    class="{{ ($secondLevel != null && $secondLevel == 'list' && $firstLevel === 'restaurant') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.restaurant.list_items') }}">
                                        Productos
                                    </a>
                                </li>

                                <li
                                    class="{{ ($secondLevel != null && $secondLevel == 'supplies' && $firstLevel === 'restaurant') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.restaurant.supplies.index') }}">
                                        Insumos
                                    </a>
                                </li>

                                <li class="{{ ($secondLevel != null && $secondLevel == 'modifier-groups' && $firstLevel === 'restaurant') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ url('restaurant/modifier-groups') }}">
                                        Modificadores
                                    </a>
                                </li>

                                <li
                                    class="nav-parent
                                                                                                                                                    {{ ($secondLevel != null && $secondLevel == 'promotions') || ($secondLevel != null && $secondLevel == 'orders') ? 'nav-active nav-expanded' : '' }}">
                                    <a class="nav-link" href="#">
                                        Pedidos Delivery
                                    </a>
                                    <ul class="nav nav-children">
                                        <li class="">
                                            <a class="nav-link" href="{{ route('tenant.restaurant.menu') }}" target="blank">
                                                Ver pedidos en linea
                                            </a>
                                        </li>
                                        <li
                                            class="{{ ($secondLevel != null && $secondLevel == 'orders') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.restaurant.order.index')}}">
                                                Listado de pedidos
                                            </a>
                                        </li>
                                        <li
                                            class="{{ ($secondLevel != null && $secondLevel == 'promotions') ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{route('tenant.restaurant.promotion.index')}}">
                                                Promociones(Banners)
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li
                                    class="{{ ($secondLevel != null && $secondLevel == 'configuration' && $firstLevel === 'restaurant') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.restaurant.configuration') }}">
                                        Config. Mesas/Cocina
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    {{-- DIGEMID --}}
                    @if(in_array('digemid', $vc_modules) && $configuration->isPharmacy())
                        <li class=" nav-parent {{ ($firstLevel === 'digemid') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-medicine-syrup">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M8 21h8a1 1 0 0 0 1 -1v-10a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v10a1 1 0 0 0 1 1z" />
                                    <path d="M10 14h4" />
                                    <path d="M12 12v4" />
                                    <path d="M10 7v-3a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v3" />
                                </svg>
                                <span>Farmacia</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('digemid', $vc_module_levels))
                                    {{-- <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'offices')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.offices') }}">Oficinas</a>
                                    </li> --}}
                                    <li
                                        class="{{ (($firstLevel === 'digemid') && ($secondLevel === 'digemid')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('tenant.digemid.index') }}">Productos</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- @if(in_array('cuenta', $vc_modules))
                    <li class=" nav-parent
                        {{ ($firstLevel === 'cuenta')?'nav-active nav-expanded':'' }}">
                        <a class="nav-link" href="#">
                            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                            <span>Mis Pagos</span>
                        </a>
                        <ul class="nav nav-children">
                            @if(in_array('account_users_settings', $vc_module_levels))
                            <li
                                class="{{ (($firstLevel === 'cuenta') && ($secondLevel === 'configuration')) ?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.configuration.index')}}">Configuracion</a>
                            </li>
                            @endif
                            @if(in_array('account_users_list', $vc_module_levels))
                            <li
                                class="{{ (($firstLevel === 'cuenta') && ($secondLevel === 'payment_index')) ?'nav-active':'' }}">
                                <a class="nav-link" href="{{route('tenant.payment.index')}}">Lista de Pagos</a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif --}}
                    {{-- Suscription --}}
                    @if(in_array('suscription_app', $vc_modules))
                        <li class=" nav-parent {{ ($firstLevel === 'full_suscription') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M7 14h.013" />
                                    <path d="M10.01 14h.005" />
                                    <path d="M13.01 14h.005" />
                                    <path d="M16.015 14h.005" />
                                    <path d="M13.015 17h.005" />
                                    <path d="M7.01 17h.005" />
                                    <path d="M10.01 17h.005" />
                                </svg>
                                <span>
                                    Suscripción <sup
                                        style="background: #ffc300;padding: 0px 3px;border-radius: 4px;">Beta</sup>
                                </span>
                            </a>
                            <ul class="nav nav-children">
                                <li
                                    class="{{ ($firstLevel === 'full_suscription' && $secondLevel === 'client') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.fullsuscription.client.index') }}">
                                        Clientes
                                    </a>
                                </li>
                                <li
                                    class="{{ (($firstLevel === 'full_suscription') && ($secondLevel === 'plans')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.fullsuscription.plans.index') }}">
                                        Planes
                                    </a>
                                </li>
                                <li
                                    class="{{ (($firstLevel === 'full_suscription') && ($secondLevel === 'payments')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.fullsuscription.payments.index') }}">
                                        Suscripciones
                                    </a>
                                </li>
                                <li
                                    class="{{ (($firstLevel === 'full_suscription') && ($secondLevel === 'payment_receipt')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.fullsuscription.payment_receipt.index') }}">
                                        Recibos de pago
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    {{-- Suscription Escolar--}}
                    @if(in_array('full_suscription_app', $vc_modules))
                        <li class=" nav-parent {{ ($firstLevel === 'suscription') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                </svg>
                                <span>Suscripción Escolar <sup
                                        style="background: #ffc300;padding: 0px 3px;border-radius: 4px;">Beta</sup></span>
                            </a>
                            <ul class="nav nav-children">
                                {{-- @if(in_array('suscription_app_client', $vc_module_levels))--}}
                                <li
                                    class="nav-parent {{ (($firstLevel === 'suscription') && ($secondLevel === 'client')) ? ' nav-active nav-expanded ' : '' }}
                                                                                                                                                        ">

                                    <a class="nav-link" href="#">
                                        Clientes
                                    </a>
                                    <ul class="nav nav-children">
                                        <li
                                            class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'client') && ($thridLevel !== 'childrens')) ? 'nav-active' : '' }}">
                                            <a class="nav-link" href="{{ route('tenant.suscription.client.index') }}">
                                                Padres
                                            </a>
                                        </li>
                                        <li
                                            class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'client') && ($thridLevel === 'childrens')) ? 'nav-active' : '' }}">
                                            <a class="nav-link"
                                                href="{{ route('tenant.suscription.client_children.index') }}">
                                                Hijos
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- @endif--}}
                                {{--
                                @todo suscription_app_service borrar de modulo de permisos admin y cliente

                                @if(in_array('suscription_app_service', $vc_module_levels))
                                <li
                                    class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'service')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.suscription.service.index') }}">
                                        Servicio
                                    </a>
                                </li>
                                @endif
                                --}}
                                {{-- @if(in_array('suscription_app_plans', $vc_module_levels))--}}
                                <li
                                    class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'plans')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.suscription.plans.index') }}">
                                        Planes
                                    </a>
                                </li>
                                {{-- @endif--}}

                                {{-- @if(in_array('suscription_app_payments', $vc_module_levels))--}}
                                <li
                                    class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'payments')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.suscription.payments.index') }}">
                                        Matrículas
                                    </a>
                                </li>
                                {{-- @endif--}}
                                {{-- @if(in_array('suscription_app_payments', $vc_module_levels))--}}
                                <li
                                    class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'payment_receipt')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.suscription.payment_receipt.index') }}">
                                        Recibos de pago
                                    </a>
                                </li>
                                {{-- @endif--}}

                                <li
                                    class="{{ (($firstLevel === 'suscription') && ($secondLevel === 'grade_section')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('tenant.suscription.grade_section.index') }}">
                                        Grados y Secciones
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(in_array('documentary-procedure', $vc_modules))
                        <li
                            class=" nav-parent {{ ($firstLevel === 'documentary-procedure') ? 'nav-active nav-expanded' : '' }}">
                            <a class="nav-link" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-folder-open"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 19l2.757 -7.351a1 1 0 0 1 .936 -.649h12.307a1 1 0 0 1 .986 1.164l-.996 5.211a2 2 0 0 1 -1.964 1.625h-14.026a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2" /></svg>
                                <span>Trámite documentario</span>
                            </a>
                            <ul class="nav nav-children">
                                @if(in_array('documentary_offices', $vc_module_levels))
                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'offices')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.offices') }}">Listado de Etapas</a>
                                    </li>
                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'status')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.status') }}">Listado de Estados</a>
                                    </li>
                                @endif
                                @if(in_array('documentary_process', $vc_module_levels))


                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'requirements')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.requirements') }}">Listado de
                                            requisitos</a>
                                    </li>

                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'processes')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.processes') }}">Tipos de Trámites</a>
                                    </li>
                                @endif
                                {{--
                                @if(in_array('documentary_documents', $vc_module_levels))
                                <li
                                    class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'documents')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('documentary.documents') }}">Tipos de Documento</a>
                                </li>
                                @endif
                                @if(in_array('documentary_actions', $vc_module_levels))
                                <li
                                    class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'actions')) ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('documentary.actions') }}">Acciones</a>
                                </li>
                                @endif
                                --}}
                                @if(in_array('documentary_files', $vc_module_levels))
                                    {{--
                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && ($secondLevel === 'files')) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.files') }}">Listado de Trámites</a>
                                    </li>
                                    --}}
                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && (($secondLevel === 'files_simplify') || ($secondLevel === 'files'))) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.files_simplify') }}">Listado de
                                            Trámites</a>
                                    </li>
                                    <li
                                        class="{{ (($firstLevel === 'documentary-procedure') && (($secondLevel === 'stadistic'))) ? 'nav-active' : '' }}">
                                        <a class="nav-link" href="{{ route('documentary.stadistic') }}">Estadisticas de
                                            Trámites</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Produccion 
                    @if(in_array('production_app', $vc_modules))

                                        <li class=" nav-parent {{ (
                            ($firstLevel === 'production') ||
                            ($firstLevel === 'machine-production') ||
                            ($firstLevel === 'packaging') ||
                            ($firstLevel === 'machine-type-production') ||
                            ($firstLevel === 'workers') ||
                            ($firstLevel === 'mill-production')
                        ) ? 'nav-active nav-expanded' : '' }}">
                                            <a class="nav-link" href="#">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-building-factory-2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 21h18" />
                                                    <path d="M5 21v-12l5 4v-4l5 4h4" />
                                                    <path
                                                        d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" />
                                                    <path d="M9 17h1" />
                                                    <path d="M14 17h1" />
                                                </svg>
                                                <span>Producción</span>
                                            </a>
                                            <ul class="nav nav-children">
                                                <li class="{{ (($firstLevel === 'production')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.production.index') }}">
                                                        Productos Fabricados
                                                    </a>
                                                </li>
                                                <li class="{{ (($firstLevel === 'mill-production')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.mill_production.index') }}">
                                                        Ingreso de Insumos
                                                    </a>
                                                </li>

                                                <li class="{{ (($firstLevel === 'machine-type-production')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.machine_type_production.index') }}">
                                                        Tipos de maquinaria
                                                    </a>
                                                </li>


                                                <li class="{{ (($firstLevel === 'machine-production')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.machine_production.index') }}">
                                                        Maquinaria
                                                    </a>
                                                </li>
                                                <li class="{{ (($firstLevel === 'packaging')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.packaging.index') }}">
                                                        Zona de embalaje
                                                    </a>
                                                </li>

                                                <li class="{{ (($firstLevel === 'workers')) ? 'nav-active' : '' }}">
                                                    <a class="nav-link" href="{{ route('tenant.workers.index') }}">
                                                        Empleados
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                    @endif
                    --}}

                    <!-- @if(in_array('generate_link_app', $vc_modules))
                <li class="{{ ($firstLevel === 'payment-links')?'nav-active':'' }}">
                    <a class="nav-link"
                        href="{{ route('tenant.payment.generate.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="feather feather-share-2">
                            <circle cx="18"
                                cy="5"
                                r="3"></circle>
                            <circle cx="6"
                                cy="12"
                                r="3"></circle>
                            <circle cx="18"
                                cy="19"
                                r="3"></circle>
                            <line x1="8.59"
                                y1="13.51"
                                x2="15.42"
                                y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                        <span>Generador de link de pago</span>
                    </a>
                </li>
                @endif -->
                </ul>
            </nav>
        </div>
        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>

    @if(in_array('users_establishments', $vc_module_levels) || in_array('users', $vc_module_levels) || in_array('configuration', $vc_modules) || in_array('app_2_generator', $vc_modules) || in_array('apps', $vc_modules))
        <div class="more-config more-config-mobile">
            <div class="nano-content nano-content-config pt-0">
                <ul class="nav nav-main">
                    <li>
                        <a class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-settings">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                </path>
                            </svg>
                            <span>Configuración y más</span>
                        </a>
                    </li>
                </ul>
            </div>

            <ul class="nav list-config">

                @if(in_array('users', $vc_module_levels))
                    <li>
                        <a class="nav-link" href="{{route('tenant.users.index')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                            </svg>
                            Usuarios</a>
                    </li>
                @endif
                @if(in_array('users_establishments', $vc_module_levels))
                    <li>
                        <a class="nav-link" href="{{route('tenant.establishments.index')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-list-numbers">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11 6h9" />
                                <path d="M11 12h9" />
                                <path d="M12 18h8" />
                                <path d="M4 16a2 2 0 1 1 4 0c0 .591 -.5 1 -1 1.5l-3 2.5h4" />
                                <path d="M6 10v-6l-2 2" />
                            </svg>
                            Sucursales & Series</a>
                    </li>
                @endif
                @if(in_array('app_2_generator', $vc_modules))
                    <li>
                        <a class="nav-link" href="{{ route('tenant.liveapp.configuration') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z" />
                                <path d="M11 4h2" />
                                <path d="M12 17v.01" />
                            </svg>
                            APP 3.1
                        </a>
                    </li>
                @endif
                @if(in_array('apps', $vc_modules))
                    <li>
                        <a class="nav-link" href="{{url('list-extras')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-packages">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M2 13.5v5.5l5 3" />
                                <path d="M7 16.545l5 -3.03" />
                                <path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M12 19l5 3" />
                                <path d="M17 16.5l5 -3" />
                                <path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" />
                                <path d="M7 5.03v5.455" />
                                <path d="M12 8l5 -3" />
                            </svg>
                            Apps
                        </a>
                    </li>
                @endif
                @if(in_array('configuration', $vc_modules))
                    <li
                        class="{{in_array($firstLevel, ['list-platforms', 'list-cards', 'list-currencies', 'list-bank-accounts', 'list-banks', 'list-attributes', 'list-detractions', 'list-units', 'list-payment-methods', 'list-incomes', 'list-payments', 'company_accounts', 'list-vouchers-type', 'companies', 'advanced', 'tasks', 'inventories', 'bussiness_turns', 'offline-configurations', 'series-configurations', 'configurations', 'login-page', 'list-settings']) ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{ url('list-settings') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                <path d="M12 12l0 .01" />
                                <path d="M3 13a20 20 0 0 0 18 0" />
                            </svg>
                            Configuraciones Globales</a>
                    </li>
                @endif

            </ul>
        </div>
    @endif
</aside>

<script>
    // Función para cambiar establecimiento desde el sidebar
    function changeSidebarEstablishment(establishmentId) {
        const payload = {
            establishment_id: establishmentId
        };
        
        const selector = document.getElementById('sidebar-establishment-selector');
        if (selector) {
            selector.disabled = true;
        }

        fetch('/hotels/reception/change-user-establishment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const mainWrapper = document.getElementById('main-wrapper');
                if (mainWrapper && mainWrapper.__vue__) {
                    const vueInstance = mainWrapper.__vue__;
                    
                    if (vueInstance.$message) {
                        vueInstance.$message({
                            type: 'success',
                            message: data.message
                        });
                    }
                    
                    if (vueInstance.$eventHub) {
                        vueInstance.$eventHub.$emit('establishmentChanged', establishmentId);
                    }
                }
                
                if (selector) {
                    selector.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Error al cambiar establecimiento:', error);
            
            const mainWrapper = document.getElementById('main-wrapper');
            if (mainWrapper && mainWrapper.__vue__ && mainWrapper.__vue__.$message) {
                mainWrapper.__vue__.$message({
                    type: 'error',
                    message: 'Error al cambiar establecimiento'
                });
            }
            
            if (selector) {
                selector.disabled = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.querySelector('.sidebar-toggle');

        if (sidebarToggle) {
            function updateToggleTitle() {
                const isCollapsed = document.documentElement.classList.contains('sidebar-left-collapsed');
                sidebarToggle.setAttribute('title', isCollapsed ? 'Expandir menú lateral' : 'Colapsar menú lateral');
            }

            updateToggleTitle();

            sidebarToggle.addEventListener('click', function() {
                setTimeout(updateToggleTitle, 50);
            });

            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        updateToggleTitle();
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Dropdown de establecimiento
        const establishmentIcon = document.getElementById('establishment-icon-trigger');
        const establishmentDropdown = document.getElementById('establishment-dropdown');
        
        if (establishmentIcon && establishmentDropdown) {
            
            establishmentIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                establishmentDropdown.classList.toggle('show');
                establishmentIcon.classList.toggle('active');

            });

            
            document.addEventListener('click', function(e) {
                if (!establishmentIcon.contains(e.target) && !establishmentDropdown.contains(e.target)) {
                    establishmentDropdown.classList.remove('show');
                    establishmentIcon.classList.remove('active');
                }
            });

            
            establishmentDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Listener para cambios de visibilidad del selector de establecimiento en sidebar
        window.addEventListener('branchSelectorVisibilityChanged', function(event) {
            const selectorContainer = document.getElementById('sidebar-establishment-selector-container');
            const sidebarSelectorsContainer = document.getElementById('sidebar-selectors-container');
            const multiuserSelector = document.getElementById('sidebar-multi-user-selector-container');
            const iconWrapper = document.getElementById('sidebar-establishment-icon-wrapper');
            const branchSelectorDropdown = document.getElementById('branch-selector-dropdown');
            const multiUserSelectorDropdown = document.getElementById('multi-user-selector-dropdown');
            const sidebar = document.getElementById('sidebar-left');

            let showInSidebarEvent;
            if (event.detail) {
                if (typeof event.detail.showInSidebar !== 'undefined') {
                    showInSidebarEvent = !!event.detail.showInSidebar;
                } else if (typeof event.detail.showInHeader !== 'undefined') {
                    showInSidebarEvent = !event.detail.showInHeader;
                }
            }

            if (typeof showInSidebarEvent !== 'undefined') {
                
                if (selectorContainer) {
                    selectorContainer.style.display = showInSidebarEvent ? 'block' : 'none';
                }
                if (sidebarSelectorsContainer) {
                    sidebarSelectorsContainer.style.display = showInSidebarEvent ? 'block' : 'none';
                }
                if (multiuserSelector) {
                    multiuserSelector.style.display = showInSidebarEvent ? 'block' : 'none';
                }
                if (iconWrapper) {
                    iconWrapper.style.display = showInSidebarEvent ? 'block' : 'none';
                }
                if (branchSelectorDropdown) {
                    branchSelectorDropdown.style.display = showInSidebarEvent ? 'block' : 'none';
                }
                if (multiUserSelectorDropdown) {
                    multiUserSelectorDropdown.style.display = showInSidebarEvent ? 'block' : 'none';
                }

                var shouldShowBranch = !!selectorContainer && showInSidebarEvent;
                var shouldShowMulti = !!multiuserSelector && showInSidebarEvent;

                if (sidebar) {
                    sidebar.classList.remove('show-branch-selector', 'show-both-selectors', 'no-branch-selector');
                    if (shouldShowBranch && shouldShowMulti) {
                        sidebar.classList.add('show-both-selectors');
                    } else if (shouldShowBranch || shouldShowMulti) {
                        sidebar.classList.add('show-branch-selector');
                    } else {
                        sidebar.classList.add('no-branch-selector');
                    }
                }
            }
        });

        // Función global para actualizar la visibilidad del selector desde cualquier script sin recargar
        window.updateBranchSelectorVisibility = function(showInSidebar) {
            const ev = new CustomEvent('branchSelectorVisibilityChanged', { detail: { showInSidebar: !!showInSidebar } });
            window.dispatchEvent(ev);
        };

        document.addEventListener('DOMContentLoaded', function() {
            const wrappers = document.querySelectorAll('.sidebar-multi-user-selector-wrapper');
            wrappers.forEach(function(wrap) {
                const placeholder = wrap.querySelector('.sidebar-multi-user-placeholder');
                const tenantEl = wrap.querySelector('tenant-multi-users-change-client');
                if (!tenantEl || !placeholder) return;

                const removePlaceholder = function() {
                    placeholder.style.transition = 'opacity .25s ease';
                    placeholder.style.opacity = '0';
                    setTimeout(function() { if (placeholder.parentNode) placeholder.parentNode.removeChild(placeholder); }, 300);
                };

                const checkAndRemove = function() {
                    try {
                        if (tenantEl.offsetHeight > 0) { removePlaceholder(); return true; }
                        if (tenantEl.innerHTML && tenantEl.innerHTML.trim().length > 0) { removePlaceholder(); return true; }
                    } catch (e) { }
                    return false;
                };

                if (checkAndRemove()) return;

                const mo = new MutationObserver(function() {
                    if (checkAndRemove()) mo.disconnect();
                });

                mo.observe(tenantEl, { childList: true, subtree: true, attributes: true });

                setTimeout(function() { if (document.body.contains(placeholder)) removePlaceholder(); mo.disconnect(); }, 3000);
            });
        });

        document.addEventListener('tenant-multi-users-mounted', function(e) {
            try {
                const wrapper = e.target.closest && e.target.closest('.sidebar-multi-user-selector-wrapper');
                const placeholder = wrapper ? wrapper.querySelector('.sidebar-multi-user-placeholder') : null;
                if (placeholder) {
                    placeholder.style.transition = 'opacity .18s ease';
                    placeholder.style.opacity = '0';
                    setTimeout(function() { if (placeholder.parentNode) placeholder.parentNode.removeChild(placeholder); }, 220);
                }
            } catch (err) { }
        });
    });
</script>

<style>
    html.no-overflowscrolling .nano {
        height: calc(100% - 50px);
    }
    @media only screen and (min-width: 767px) {
        html.no-overflowscrolling .sidebar-left.show-branch-selector .nano {
            height: calc(100% - 130px);
        }
        html.no-overflowscrolling .sidebar-left.show-both-selectors .nano {
            height: calc(100% - 184px);
        }
    }
    .more-config {
        position: relative;
        display: inline-block;
        overflow: visible;
        width: 100%;
    }

    .list-config {
        position: absolute;
        z-index: 1;
        display: none;
        background-color: #fff;
        min-width: 230px;
        border: 1px solid #e0e6f8;
        box-shadow: 0 0 16px 0px rgb(0 36 96 / 12%);
        bottom: 105px;
        left: 15px;
        border-radius: 8px;
        padding: 15px;
    }

    .more-config .nano-content:hover~.list-config,
    .list-config:hover {
        display: block;
    }

    ul.nav.list-config i {
        width: 18px;
        text-align: center;
    }

    ul.nav.list-config li:hover {
        background: #f3f4fb;
        border-radius: 5px;
    }

    .more-config ul.nav-main>li a:hover {
        padding-left: 10px !important;
    }

    .sidebar-red .more-config a,
    .sidebar-blue .more-config a,
    .sidebar-green .more-config a,
    .sidebar-dark .sidebar-left .more-config a {
        color: #60769a !important;
    }

    .sidebar-red .more-config a:hover,
    .sidebar-blue .more-config a:hover,
    .sidebar-green .more-config a:hover {
        color: #fff !important;
    }

    .nav-main .nav-children li.nav-item-with-action {
        position: relative;
    }

    .nav-main .nav-children li.nav-item-with-action > .nav-action {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        padding: 2px 6px;
        line-height: 1.2;
    }
    .nav-item-with-action button{
        padding: 0 4px !important;
        font-size: 12px !important;
    }

    .contain-icon-establishment-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .contain-icon-establishment {
        transition: all 0.3s ease;
    }

    .establishment-dropdown {
        position: absolute;
        left: 50px;
        top: 94px;
        transform: translateY(-50%);
        background-color: #fff;
        border: 1px solid #e0e6f8;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        min-width: 250px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 9999;
    }

    .establishment-dropdown.show {
        opacity: 1;
        visibility: visible;
    }

    .establishment-dropdown-header {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e6f8;
        font-weight: 600;
        font-size: 13px;
        color: #333;
        background-color: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }

    .establishment-dropdown-content {
        padding: 10px 15px 15px 15px;
    }

    .tooltip-right {
        position: absolute;
        left: calc(100% + 0px);
        top: 50%;
        transform: translateY(-50%);
        background-color: #000;
        color: #fff;
        font-size: 12px;
        padding: 4px 7px;
        border-radius: 6px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
        pointer-events: none;
        z-index: 10000;
    }

    .tooltip-right::after {
        content: '';
        position: absolute;
        top: 50%;
        left: -14px;
        transform: translateY(-50%);
        border-width: 8px;
        border-style: solid;
        border-color: transparent #000 transparent transparent;
    }

    .contain-icon-establishment-wrapper:hover .tooltip-right,
    .contain-icon-establishment-wrapper:focus-within .tooltip-right {
        opacity: 1;
        visibility: visible;
    }

    .contain-icon-establishment.active ~ .tooltip-right {
        opacity: 0;
        visibility: hidden;
    }

    .establishment-selector-container {
        min-height: 76px;
    }

    .sidebar-multi-user-selector-wrapper {
        min-height: 56px;
        display: block;
        position: relative;
    }

    .sidebar-multi-user-placeholder {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 58px;
        border-radius: 4px;
        background: linear-gradient(90deg, #eeeeee50 25%, #f5f5f598 50%, #eeeeee4f 75%);
        background-size: 200% 100%;
        animation: shimmer 1.2s linear infinite;
        z-index: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-multi-user-placeholder svg{
        width: 12px;
        height: 12px;
        margin-right: 5px
    }        
    tenant-multi-users-change-client.sidebar-multi-user-selector {
        display: block;
        position: relative;
        z-index: 1;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Estilos para contador de habitaciones ocupadas */
    .nav-parent > a.nav-link:has(.hotel-rooms-counter) {
        display: flex;
        align-items: center;
    }

    .hotel-rooms-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        margin-right: 10px;
        min-width: 20px;
        height: 20px;
        padding: 2px 6px;
        background-color: #dc3545;
        color: white;
        font-size: 11px;
        font-weight: 600;
        border-radius: 10px;
        line-height: 1;
        text-align: center;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .nav-parent:hover .hotel-rooms-counter {
        background-color: #c82333;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4);
    }
</style>