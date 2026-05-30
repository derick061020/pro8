@extends('tenant.layouts.app')

@section('content')
@php
$a = $vc_modules;
$show_welcome_panel = data_get($configuration, 'visual.show_welcome_panel', true);
@endphp
    <div class="card welcome-component" style="display: {{ $show_welcome_panel ? 'block' : 'none' }};">
        <div class="welcome-card-body">
            <div class="row welcome-card-row">
                <div class="col-md-3 welcome-card">
                    <div class="row justify-content-center align-items-center h-100">
                        <div class="p-0">
                            <div class="col-12 text-center mb-2 welcome-title">
                                <span class="h2 font-weight-bold">Hola {{ auth()->user()->name }}!</span>
                            </div>
                            <div class="col-12 text-center welcome-subtitle">
                                <span class="h6">¿Qué deseas hacer hoy?</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 actions-cards-container">
                    @if(in_array('documents', $vc_modules))
                        <div class="card shadow-sm border-top my-2 actions-card">
                            <div class="card-header py-1 font-weight-bold">
                                Facturación
                            </div>
                            <div class="card-body py-2">
                                <div class="row actions-cards-row">
                                    @if(auth()->user()->type != 'integrator' && $vc_company->soap_type_id != '03')
                                        @if(in_array('documents', $vc_modules))
                                            @if(in_array('new_document', $vc_module_levels))
                                                <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                                    <a href="{{route('tenant.documents.create')}}" class="w-100 h-100 d-inline-block border bg-danger text-light rounded p-1">
                                                        <div class="h-100 d-flex justify-content-center align-items-center">
                                                            <div class="contain-quick-actions">
                                                                <div class="svg-actions">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M19 12v7a1.78 1.78 0 0 1 -3.1 1.4a1.65 1.65 0 0 0 -2.6 0a1.65 1.65 0 0 1 -2.6 0a1.65 1.65 0 0 0 -2.6 0a1.78 1.78 0 0 1 -3.1 -1.4v-14a2 2 0 0 1 2 -2h7l5 5v4.25"></path></svg>
                                                                </div>
                                                                <div class="d-block mb-2">Nuevo CPE</div>
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    @endif

                                    @if(in_array('sale_notes', $vc_module_levels))
                                        <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                            <a href="{{route('tenant.sale_notes.create')}}" class="w-100 h-100 d-inline-block border bg-danger text-light rounded p-1">
                                                <div class="h-100 d-flex justify-content-center align-items-center">
                                                    <div class="contain-quick-actions">
                                                        <div class="svg-actions">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 4v17l2 -2l2 2l2 -2l2 2l2 -2l2 2l2 -2v-17z"></path><path d="M14 8h-4"></path><path d="M14 12h-4"></path><path d="M14 16h-4"></path></svg>
                                                        </div>
                                                        <div class="d-block mb-2">Nueva nota de venta</div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(in_array('quotations', $vc_module_levels))
                                        <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                            <a href="{{route('tenant.quotations.create')}}" class="w-100 h-100 d-inline-block border bg-danger text-light rounded p-1">
                                                <div class="h-100 d-flex justify-content-center align-items-center">
                                                    <div class="contain-quick-actions">
                                                        <div class="svg-actions">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-description"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
                                                        </div>
                                                        <div class="d-block mb-2">Nueva cotización</div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(in_array('documents', $vc_modules) && $vc_company->soap_type_id != '03')
                                        @if(in_array('list_document', $vc_module_levels))
                                            <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                                <a href="{{route('tenant.documents.index')}}" class="w-100 h-100 d-inline-block border bg-danger text-light rounded p-1">
                                                    <div class="h-100 d-flex justify-content-center align-items-center">
                                                        <div class="contain-quick-actions">
                                                            <div class="svg-actions">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M12 21h-5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v4.5" /><path d="M14 17.5a2.5 2.5 0 1 0 5 0a2.5 2.5 0 1 0 -5 0" /><path d="M18.5 19.5l2.5 2.5" /></svg>
                                                            </div>
                                                            <div class="d-block mb-2">Búsqueda de documentos</div>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(in_array('items', $vc_modules))
                        <div class="card shadow-sm border-top my-2 actions-card">
                            <div class="card-header py-1 font-weight-bold">
                                Productos y servicios
                            </div>
                            <div class="card-body py-2">
                                <div class="row actions-cards-row">
                                    @if(in_array('items', $vc_module_levels))
                                        <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                            <a href="{{route('tenant.items.index')}}" class="w-100 h-100 d-inline-block border bg-warning text-light rounded p-1">
                                                <div class="h-100 d-flex justify-content-center align-items-center">
                                                    <div class="contain-quick-actions">
                                                        <div class="svg-actions">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-category-2">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M14 4h6v6h-6z"></path>
                                                                <path d="M4 14h6v6h-6z"></path>
                                                                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                                                <path d="M7 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="d-block mb-2">Ver lista de productos</div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(in_array('items_services', $vc_module_levels))
                                        <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                            <a href="{{route('tenant.services')}}" class="w-100 h-100 d-inline-block border bg-warning text-light rounded p-1">
                                                <div class="h-100 d-flex justify-content-center align-items-center">
                                                    <div class="contain-quick-actions">
                                                        <div class="svg-actions">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l11 0" /><path d="M9 12l11 0" /><path d="M9 18l11 0" /><path d="M5 6l0 .01" /><path d="M5 12l0 .01" /><path d="M5 18l0 .01" /></svg>
                                                        </div>
                                                        <div class="d-block mb-2">Ver lista de servicios</div>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if(in_array('inventory', $vc_modules))
                                        @if(in_array('inventory', $vc_module_levels))
                                            <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                                <a href="{{route('inventory.index')}}" class="w-100 h-100 d-inline-block border bg-warning text-light rounded p-1">
                                                    <div class="h-100 d-flex justify-content-center align-items-center">
                                                        <div class="contain-quick-actions">
                                                            <div class="svg-actions">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-warehouse">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M3 21v-13l9 -4l9 4v13"></path>
                                                                    <path d="M13 13h4v8h-10v-6h6"></path>
                                                                    <path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3"></path>
                                                                </svg>
                                                            </div>
                                                            <div class="d-block mb-2">Ver inventarios</div>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->type != 'integrator')
                        @if(in_array('pos', $vc_modules_levels))
                            <div class="card shadow-sm border-top my-2 actions-card">
                                <div class="card-header py-1 font-weight-bold">
                                    Punto de venta
                                </div>
                                <div class="card-body py-2">
                                    <div class="row actions-cards-row">
                                        @if(in_array('pos', $vc_module_levels))
                                            <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                                <a href="{{route('tenant.pos.index')}}" class="w-100 h-100 d-inline-block border bg-primary text-light rounded p-1">
                                                    <div class="h-100 d-flex justify-content-center align-items-center">
                                                        <div class="contain-quick-actions">
                                                            <div class="svg-actions">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M15 21h-9a3 3 0 0 1 -3 -3v-1h10v2a2 2 0 0 0 4 0v-14a2 2 0 1 1 2 2h-2m2 -4h-11a3 3 0 0 0 -3 3v11"></path><path d="M9 7l4 0"></path><path d="M9 11l4 0"></path></svg>                                                            
                                                            </div>
                                                            <div class="d-block mb-2">Ir a POS</div>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif

                                        @if(in_array('cash', $vc_module_levels))
                                            <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                                <a href="{{route('tenant.cash.index')}}" class="w-100 h-100 d-inline-block border bg-primary text-light rounded p-1">
                                                    <div class="h-100 d-flex justify-content-center align-items-center">
                                                        <div class="contain-quick-actions">
                                                            <div class="svg-actions">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash-register"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" /><path d="M19 21v1m0 -8v1" /><path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" /><path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" /><path d="M8 14v.01" /><path d="M8 17v.01" /><path d="M12 13.99v.01" /><path d="M12 17v.01" /></svg>
                                                            </div>
                                                            <div class="d-block mb-2">Ver listado de cajas</div>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if(in_array('accounting', $vc_modules) || in_array('reports', $vc_modules))
                    <div class="card shadow-sm border-top my-2 actions-card last-actions-card">
                        <div class="card-header py-1 font-weight-bold">
                            Reportes
                        </div>
                        <div class="card-body py-2">
                            <div class="row actions-cards-row">
                                @if(in_array('account_report', $vc_module_levels))
                                    <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                        <a href="{{route('tenant.account_format.index')}}" class="w-100 h-100 d-inline-block border bg-success text-light rounded p-1">
                                            <div class="h-100 d-flex justify-content-center align-items-center">
                                                <div class="contain-quick-actions">
                                                    <div class="svg-actions">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-pie"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-6.8a2 2 0 0 1 -2 -2v-7a.9 .9 0 0 0 -1 -.8" /><path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a1 1 0 0 1 -1 -1v-4.5" /></svg>
                                                    </div>
                                                    <div class="d-block mb-2">Reporte contable</div>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                    <a href="{{route('tenant.reports.sales.index')}}" class="w-100 h-100 d-inline-block border bg-success text-light rounded p-1">
                                        <div class="h-100 d-flex justify-content-center align-items-center">
                                            <div class="contain-quick-actions">
                                                <div class="svg-actions">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-coin" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12.5 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path><path d="M17 7.5l-2 9"></path></svg>
                                                </div>
                                                <div class="d-block mb-2">Reporte de ventas</div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                    <a href="{{route('tenant.reports.purchases.index')}}" class="w-100 h-100 d-inline-block border bg-success text-light rounded p-1">
                                        <div class="h-100 d-flex justify-content-center align-items-center">
                                            <div class="contain-quick-actions">
                                                <div class="svg-actions">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shopping-cart"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 19a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17h-11v-14h-2" /><path d="M6 5l14 1l-1 7h-13" /></svg>
                                                </div>
                                                <div class="d-block mb-2">Reporte de compras</div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col px-1 text-center" style="height: 100px; max-width: 25%">
                                    <a href="{{route('tenant.reports.general_items.index')}}" class="w-100 h-100 d-inline-block border bg-success text-light rounded p-1">
                                        <div class="h-100 d-flex justify-content-center align-items-center">
                                            <div class="contain-quick-actions">
                                                <div class="svg-actions">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-layout-grid"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M14 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M4 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /><path d="M14 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" /></svg>
                                                </div>
                                                <div class="d-block mb-2">Reporte de productos</div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" /></svg>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- GENERA ERROR VITE UPGRADE --}}
    {{-- <script>
        new Vue({
            el: '#app',
            data() {
                return {
                    // Obtén el valor desde localStorage
                    showWelcome: localStorage.getItem('show_welcome_panel') === 'true'
                };
            },
            watch: {
                // Reaccionamos a cambios en `showWelcome` y actualizamos `localStorage`
                showWelcome(newValue) {
                    localStorage.setItem('show_welcome_panel', newValue);
                }
            }
        });
    </script> --}}

    <tenant-dashboard-index
    	:type-user="{{ json_encode(auth()->user()->type) }}"
    	:soap-company="{{ json_encode($soap_company) }}"
        :configuration="{{ json_encode($configuration) }}">
    </tenant-dashboard-index>

@endsection
