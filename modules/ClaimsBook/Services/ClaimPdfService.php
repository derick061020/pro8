<?php

// Modules/ClaimsBook/Services/ClaimPdfService.php

namespace Modules\ClaimsBook\Services;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Modules\ClaimsBook\Models\Tenant\Claim;
use App\Models\Tenant\Company;
use App\Models\Tenant\Establishment;

class ClaimPdfService
{
    /**
     * Genera o regenera el PDF de constancia del reclamo.
     * Almacena en disco tenant bajo claims/pdf/{public_code}.pdf
     * Actualiza claim->pdf_path con la ruta relativa.
     */
    public function generate(Claim $claim): string
    {
        // Eager load all relationships needed for the PDF
        $claim->loadMissing([
            'statusClaim',
            'assignedUser',
            'district',
            'identityDocumentType',
        ]);

        $company       = Company::active();
        $establishment = Establishment::first();

        $channelEstablishment = null;
        if (!empty($claim->channel)) {
            $channelEstablishment = Establishment::with(['district', 'province', 'department'])
                ->whereRaw('LOWER(TRIM(description)) = ?', [mb_strtolower(trim($claim->channel))])
                ->first();
        }

        // Render the Blade view to HTML
        $html = view('claimsbook::pdf.claim', [
            'claim'                => $claim,
            'company'              => $company,
            'establishment'        => $establishment,
            'channelEstablishment' => $channelEstablishment,
        ])->render();

        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => 'A4',
            'margin_top'        => 15,
            'margin_bottom'     => 15,
            'margin_left'       => 15,
            'margin_right'      => 15,
            'tempDir'           => sys_get_temp_dir(),
            'autoScriptToLang'  => true,
        ]);

        $mpdf->SetTitle('Constancia de Reclamo - ' . $claim->code);
        $mpdf->SetAuthor(config('app.name'));
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');

        $path = 'claims/pdf/' . $claim->public_code . '.pdf';

        // Put (create or overwrite) the file on the tenant disk
        Storage::disk('tenant')->put($path, $pdfContent);

        // Persist the path on the claim if it changed
        if ($claim->pdf_path !== $path) {
            $claim->pdf_path = $path;
            $claim->saveQuietly();
        }

        return $path;
    }
}
