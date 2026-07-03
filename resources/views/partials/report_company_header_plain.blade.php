@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as ReportCompanyPlain;
@endphp
{{ ReportCompanyPlain::commercialLine($company) }}@if($l = ReportCompanyPlain::legalLine($company))<br><span style="font-weight:normal;">{{ $l }}</span>@endif
