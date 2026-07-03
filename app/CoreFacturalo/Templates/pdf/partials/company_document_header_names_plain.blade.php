@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as CompanyDocHeaderPlain;
    $primary = CompanyDocHeaderPlain::commercialLine($company);
    $legal = CompanyDocHeaderPlain::legalLine($company);
@endphp
{{ $primary }}@if($legal)<br><span style="font-size:9pt;font-weight:normal;line-height:1.2;">{{ $legal }}</span>@endif
