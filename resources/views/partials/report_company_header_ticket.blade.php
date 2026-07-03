@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as ReportCompanyTicket;
@endphp
<strong>Empresa: </strong>{{ ReportCompanyTicket::commercialLine($company) }}@if($l = ReportCompanyTicket::legalLine($company))<br><span style="font-weight:normal;">{{ $l }}</span>@endif
