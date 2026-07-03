@php
    use App\CoreFacturalo\Helpers\CompanyDocumentDisplay as CompanyDocHeader;
    $tp = $tagPrimary ?? 'h4';
    $tl = $tagLegal ?? 'h5';
    $primary = CompanyDocHeader::commercialLine($company);
    $legal = CompanyDocHeader::legalLine($company);
    $legalInlineStyle = $legalLineStyle ?? 'font-weight:400;font-size:0.95em;margin:0.1rem 0 0 0;line-height:1.25;';
@endphp
<{{ $tp }}@if(!empty($primaryClass)) class="{{ $primaryClass }}"@endif @if(!empty($primaryStyle)) style="{{ $primaryStyle }}"@endif>{{ $primary }}</{{ $tp }}>
@if($legal)
<{{ $tl }}@if(!empty($legalClass)) class="{{ $legalClass }}"@endif style="{{ $legalInlineStyle }}">{{ $legal }}</{{ $tl }}>
@endif
