@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as ReportCompanyHdr;
    $showEmpresaLabel = $showEmpresaLabel ?? true;
@endphp
@if($showEmpresaLabel)
<strong>Empresa: </strong>
@endif
<strong>{{ ReportCompanyHdr::commercialLine($company) }}</strong>@if($l = ReportCompanyHdr::legalLine($company))<br><span style="font-weight:normal;">{{ $l }}</span>@endif
