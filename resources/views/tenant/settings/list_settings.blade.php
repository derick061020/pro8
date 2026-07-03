@extends('tenant.layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
<style>
.card-report-links { list-style: none; padding-left: 0; }
.card-report-links .ti { font-size: 1.1rem; margin-right: 8px; opacity: 0.85; }
</style>
@endpush

@section('content')
    <?php
    use App\Models\Tenant\Configuration;
    $configuration = Configuration::first();
    ?>
<div class="page-header pr-0">
    <span class="module-title-marker" data-page-title="Configuración"></span>
    <h2>
        <a href="/dashboard">
            <i class="fas fa-home"></i>
        </a>
    </h2>
    <ol class="breadcrumbs">
        <li class="active">
            <span>Dashboard</span>
        </li>
        <li>
            <span class="text-muted">Configuración</span>
        </li>
    </ol>
</div>

<div class="row tab-content-default row-new bg-transparent border-0" style="background: transparent !important;">
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">General</h6>
                <ul class="card-report-links">
                    @if($user->type != 'integrator')
                    <li>
                        <a href="{{ url('list-banks') }}"><i class="ti ti-building-bank"></i>Listado de bancos</a>
                    </li>
                    <li>
                        <a href="{{url('list-bank-accounts')}}"><i class="ti ti-wallet"></i>Listado de cuentas bancarias</a>
                    </li>
                    <li>
                        <a href="{{url('list-currencies')}}"><i class="ti ti-currency-dollar"></i>Lista de monedas</a>
                    </li>
                    <li>
                        <a href="{{url('list-cards')}}"><i class="ti ti-credit-card"></i>Listado de tarjetas</a>
                    </li>
                    <li>
                        <a href="{{url('list-platforms')}}"><i class="ti ti-apps"></i>Plataformas</a>
                    </li>
                    @if(in_array('production_app', $vc_modules) && $configuration->isShowExtraInfoToItem())
                        <li>
                            <a href="{{route('extra_info_items.index')}}"><i class="ti ti-list-details"></i>Datos extra de items</a>
                        </li>
                    @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @if(!empty($companyMenu))
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Empresa</h6>
                <ul class="card-report-links">
                    <li>
                        <a href="{{route('tenant.companies.create')}}"><i class="ti ti-building"></i>Empresa</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.bussiness_turns.index')}}"><i class="ti ti-briefcase"></i>Giro de negocio</a>
                    </li>
                    <li>
                        <a href="#" class="style-switcher-open"><i class="ti ti-palette"></i>Estilos y temas</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.index')}}"><i class="ti ti-settings"></i>Avanzado</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.index')}}?tab=mail"><i class="ti ti-mail"></i>Configuración de correo</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.payment.generate.index')}}"><i class="ti ti-link"></i>Generador de link de pago</a>
                    </li>
                    <li>
                        <a href="{{route('tenant_ecommerce_configuration')}}"><i class="ti ti-shopping-cart"></i>Tienda Virtual/Restaurante</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">SUNAT</h6>
                <ul class="card-report-links">
                    @if($user->type != 'integrator')
                    <li>
                        <a href="{{url('list-attributes')}}"><i class="ti ti-tag"></i>Listado de Atributos</a>
                    </li>
                    <li>
                        <a href="{{url('list-detractions')}}"><i class="ti ti-receipt-refund"></i>Listado de tipos de detracciones</a>
                    </li>
                    <li>
                        <a href="{{url('list-units')}}"><i class="ti ti-ruler"></i>Listado de unidades</a>
                    </li>
                    <li>
                        <a href="{{url('list-item-affectations')}}"><i class="ti ti-percentage"></i>Listado de afectación por producto
                            <sup style="background: #ffc300;padding: 3px 3px;border-radius: 4px;">Nuevo</sup>
                        </a>
                    </li>
                    <li>
                        <a href="{{url('list-transfer-reason-types')}}"><i class="ti ti-transfer"></i>Tipos de motivos de transferencias</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Ingresos/Egresos</h6>
                <ul class="card-report-links">
                    @if($user->type != 'integrator')
                    <li>
                        <a href="{{url('list-payment-methods')}}"><i class="ti ti-cash-banknote"></i>Métodos de pago - ingreso / gastos</a>
                    </li>
                    <li>
                        <a href="{{url('list-incomes')}}"><i class="ti ti-cash"></i>Motivos de ingresos / Gastos</a>
                    </li>
                    <li>
                        <a href="{{url('list-payments')}}"><i class="ti ti-coins"></i>Listado de métodos de pago</a>
                    </li>
                    @endif
                    @if($user->type != 'integrator')
                    <li>
                        <a href="{{url('list-vouchers-type')}}"><i class="ti ti-receipt-2"></i>Tipos de comprobantes INGRESOS Y GASTOS</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Plantillas PDF</h6>
                <ul class="card-report-links">
                    <li>
                        <a href="{{route('tenant.advanced.pdf_templates')}}"><i class="ti ti-file-type-pdf"></i>PDF</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.pdf_ticket_templates')}}"><i class="ti ti-ticket"></i>PDF - Ticket</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.advanced.pdf_preprinted_templates')}}"><i class="ti ti-printer"></i>Pre Impresos</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @if(!empty($advanceMenu))
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Avanzado</h6>
                <ul class="card-report-links">
                    @if($user->type != 'integrator' && $vc_company->soap_type_id != '03')
                    <li>
                        <a href="{{route('tenant.tasks.index')}}"><i class="ti ti-clock"></i>Tareas programadas</a>
                    </li>
                    @endif
                    @if($vc_company->soap_type_id != '03')
                        {{-- <li>
                            <a href="{{route('tenant.offline_configurations.index')}}"><i class="ti ti-cloud-off"></i>Modo offline</a>
                        </li> --}}
                        <li>
                            <a href="{{route('tenant.series_configurations.index')}}"><i class="ti ti-hash"></i>Numeración de facturación</a>
                        </li>
                    @endif
                    <li>
                        <a href="{{route('tenant.company_accounts.create')}}"><i class="ti ti-calculator"></i>Avanzado - Contable</a>
                    </li>
                    @if($user->type != 'integrator' && $vc_company->soap_type_id != '03')
                    <li>
                        <a href="{{route('tenant.inventories.configuration.index')}}"><i class="ti ti-packages"></i>Inventarios</a>
                    </li>
                    @endif
                    @if($user->type === 'admin')
                    <li>
                        <a href="{{route('tenant.sale_notes.configuration')}}"><i class="ti ti-notes"></i>Nota de ventas</a>
                    </li>
                    @endif
                    @if($configuration->isMiTiendaPe()== true)
                    <li>
                        <a href="{{route('tenant.mi_tienda_pe.configuration.index')}}">
                            <i class="ti ti-shopping-bag"></i>MiTienda.PE
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{route('tenant.item-editor-tag.index')}}" target="_blank"><i class="ti ti-tags"></i>Editor de etiqueta
                            <sup style="background: #ffc300;padding: 3px 3px;border-radius: 4px;">Nuevo</sup>
                        </a>
                    </li>
                    @if (Route::has('tenant.custom-fields.index'))
                    <li>
                        <a href="{{route('tenant.custom-fields.index')}}">
                            <i class="ti ti-forms"></i>Campos personalizados
                            <sup style="background: #ffc300;padding: 3px 3px;border-radius: 4px;">Nuevo</sup>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif
    @if (! $useLoginGlobal)
    @if(!empty($visualMenu))
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Visual</h6>
                <ul class="card-report-links">
                    {{-- @if($user->type != 'integrator')
                    <li class="{{($path[0] === 'catalogs') ? 'nav-active' : ''}}">
                        <a class="nav-link" href="{{route('tenant.catalogs.index')}}"><i class="ti ti-folder"></i>Catálogos</a>
                    </li>
                    @endif --}}
                    <li>
                        <a href="{{route('tenant.login_page')}}"><i class="ti ti-login"></i>Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
    @endif
    <div class="col-6 col-md-4 mb-4">
        <div class="card card-dashboard card-reports">
            <div class="card-body">
                <h6 class="card-title">Comisiones</h6>
                <ul class="card-report-links">
                    <li>
                        <a href="{{route('tenant.user_commissions.index')}}"><i class="ti ti-users"></i>Vendedores</a>
                    </li>
                    <li>
                        <a href="{{route('tenant.incentives.index')}}"><i class="ti ti-box"></i>Productos</a>
                    </li>
                    <li>
                        <a href="{{ route('tenant.pending_account_commissions.index') }}"><i class="ti ti-wallet-off"></i>Cuentas pendientes</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
