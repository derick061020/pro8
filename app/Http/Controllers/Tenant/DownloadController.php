<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Tenant\QuotationController;
use App\CoreFacturalo\Template;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use Modules\Document\Helpers\ConsultCdr;
use Mpdf\Mpdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    use StorageDocument;

    public function downloadExternal($model, $type, $external_id, $format = null) {
        $document_type = $model;
        $model = "App\\Models\\Tenant\\".ucfirst($model);
        $document = $model::where('external_id', $external_id)->first();

        if (!$document) throw new Exception("El código {$external_id} es inválido, no se encontró documento relacionado");

        $type_pdf = $document_type;
        if ($document_type == 'document') {
             $type_pdf = 'invoice';
             if($document->document_type_id === '07') $type_pdf = 'credit';
             if($document->document_type_id === '08') $type_pdf = 'debit';
        }

        if ($format != null) {
            $this->reloadPDF($document, $type_pdf, $format);
        } else {
            // Validar la existencia física del PDF. 
            // Si el formato es null y no existe en disco, forzar 'a4' y regenerar preventivamente con el tipo correcto.
            if (!$this->existFileInStorage($document->filename, 'pdf')) {
                $format = 'a4';
                $this->reloadPDF($document, $type_pdf, $format);
            }
        }

        // Cambio para que se refleje el qr_url de ose o sunat  dentro del pdf de gre ("a4") para el listado
        if(isset($document->document_type) && $document->document_type->id == '09' && $document->qr_url) $this->reloadPDF($document, 'dispatch', 'a4');

        if(in_array($document->document_type_id, ['09', '31']) && $type === 'cdr') {
            if((new Facturalo)->hasPseSend()) {
                $type = 'cdr';
            } else {
                $type = 'cdr_xml';
            }

        }
        return $this->download($type, $document);
    }

    public function download($type, $document) {
        switch ($type) {
            case 'pdf':
                $folder = 'pdf';
                break;
            case 'xml':
                $folder = 'signed';
                break;
            case 'cdr_xml':
                $folder = 'cdr_xml';
                break;
            case 'cdr':
                $folder = 'cdr';
                break;
            case 'quotation':
                $folder = 'quotation';
                break;
            case 'sale_note':
                $folder = 'sale_note';
                break;

            default:
                throw new Exception('Tipo de archivo a descargar es inválido');
        }

        // Si se solicita el CDR de un comprobante electrónico y no existe físicamente
        // en el storage, intentar recuperarlo consultando a la SUNAT (o al PSE/OSE según
        // configuración) antes de fallar con "Unable to retrieve the file_size...".
        if ($type === 'cdr'
            && $document instanceof Document
            && !$this->existFileInStorage($document->filename, 'cdr')) {
            $fallback = $this->recoverCdrFromSunat($document);
            // Para boletas, SUNAT no entrega CDR individual: se devuelve el CDR del
            // resumen diario (constancia válida) si está disponible.
            if ($fallback !== null) {
                return $fallback;
            }
        }

        //borrar despues
        // solo desarrollo
        // $this->reloadPDF($document, 'dispatch', 'a4');
        // $temp = tempnam(sys_get_temp_dir(), 'pdf');
        // file_put_contents($temp, $this->getStorage($document->filename, 'pdf'));

        // return response()->file($temp);
        //borrar antes
        return $this->downloadStorage($document->filename, $folder);
    }

    /**
     * Recupera el CDR desde la SUNAT (o PSE/OSE según configuración) cuando el
     * archivo no existe en el storage.
     *
     * SUNAT solo entrega el CDR individual (getStatusCdr) para FACTURAS y notas
     * de crédito/débito relacionadas a facturas, y solo en producción. Para las
     * BOLETAS (tipo 03) no existe CDR individual: la constancia válida es el CDR
     * de su RESUMEN DIARIO (RC-...). Por eso, si la consulta a SUNAT no deja el
     * archivo, se busca el CDR del resumen que reportó el comprobante.
     *
     * @param  Document $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null  Respuesta
     *         con el CDR del resumen si aplica; null si el CDR propio quedó disponible.
     * @throws Exception
     */
    private function recoverCdrFromSunat(Document $document)
    {
        $result = null;

        // La consulta directa solo tiene sentido para facturas/notas (01, 07, 08).
        if (in_array($document->document_type_id, ['01', '07', '08'], true)) {
            try {
                $result = (new ConsultCdr)->search($document);
            } catch (Exception $e) {
                Log::error('Error al recuperar CDR desde SUNAT: '.$e->getMessage());
                throw new Exception('No se encontró el CDR almacenado y falló la consulta a la SUNAT: '.$e->getMessage());
            }

            // Si la consulta dejó el CDR propio en storage, se descarga normalmente.
            if ($this->existFileInStorage($document->filename, 'cdr')) {
                return null;
            }
        }

        // Fallback: buscar el CDR del resumen diario que reportó el comprobante.
        $summaryCdr = $this->downloadSummaryCdr($document);
        if ($summaryCdr !== null) {
            return $summaryCdr;
        }

        // No se pudo obtener ninguna constancia: mensaje claro según el caso.
        $message = is_array($result) && isset($result['message'])
            ? $result['message']
            : 'La SUNAT no devolvió un CDR para este comprobante.';

        if ($document->document_type_id === '03') {
            throw new Exception('Las boletas no tienen CDR individual en SUNAT; la constancia es el CDR del resumen diario, que aún no está disponible para este comprobante. '.$message);
        }

        if (is_array($result) && ($result['accepted'] ?? null) === true) {
            throw new Exception('El comprobante está ACEPTADO por SUNAT, pero no se pudo obtener la constancia (CDR) para descargarla individualmente. Detalle: '.$message);
        }

        throw new Exception('No fue posible recuperar el CDR desde la SUNAT. '.$message);
    }

    /**
     * Devuelve el CDR del resumen diario (RC-...) que reportó el comprobante,
     * si el resumen existe y su CDR está almacenado. Constancia válida de boletas.
     *
     * @param  Document $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    private function downloadSummaryCdr(Document $document)
    {
        $summaryDocument = \App\Models\Tenant\SummaryDocument::where('document_id', $document->id)
            ->latest('id')
            ->first();

        $summary = $summaryDocument
            ? \App\Models\Tenant\Summary::find($summaryDocument->summary_id)
            : null;

        if (!$summary) {
            return null;
        }

        // Si el CDR del resumen no está en storage pero el resumen tiene ticket,
        // recuperarlo desde SUNAT (getStatus por ticket) para que quede disponible.
        if (!$this->existFileInStorage($summary->filename, 'cdr') && !empty($summary->ticket)) {
            try {
                $facturalo = new Facturalo();
                $facturalo->setDocument($summary);
                $facturalo->setType('summary');
                if ($facturalo->hasPseSend()) {
                    $facturalo->pseQuerySummary();
                } else {
                    $facturalo->statusSummary($summary->ticket);
                }
            } catch (Exception $e) {
                Log::error("No se pudo recuperar el CDR del resumen {$summary->filename} desde SUNAT: ".$e->getMessage());
            }
        }

        if ($this->existFileInStorage($summary->filename, 'cdr')) {
            Log::info("CDR de boleta {$document->filename} servido desde el resumen {$summary->filename}.");
            return $this->downloadStorage($summary->filename, 'cdr');
        }

        return null;
    }

    /**
     * @param      $model
     * @param      $external_id
     * @param null $format
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function toPrint($model, $external_id, $format = 'a4') {
        $document_type = $model;

        $model = "App\\Models\\Tenant\\".ucfirst($model);

        $document = $model::where('external_id', $external_id)->first();

        if (!$document) {
            throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");
        }

        if ($document_type == 'quotation'){
            // Las cotizaciones tienen su propio controlador, si se generan por este medio, dará error
            $quotation = new QuotationController();
            return $quotation->toPrint($external_id,$format);
        }elseif($document_type =='salenote'){
            $saleNote = new SaleNoteController();
            return $saleNote->toPrint($external_id,$format);
        }
        $type = 'invoice';
        if ($document_type == 'dispatch') {
            $type = 'dispatch';
        }
        if($document->document_type_id === '07') {
            $type = 'credit';
        }
        if($document->document_type_id === '08') {
            $type = 'debit';
        }

        $this->reloadPDF($document, $type, $format);

        $temp = tempnam(sys_get_temp_dir(), 'pdf');

        file_put_contents($temp, $this->getStorage($document->filename, 'pdf'));

        /*
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$document->filename.'.pdf'.'"'
        ];
        */

        return response()->file($temp, $this->generalPdfResponseFileHeaders($document->filename));
    }

    public function toTicket($model, $external_id, $format = null) {
        $model = "App\\Models\\Tenant\\".ucfirst($model);
        $document = $model::where('id', $external_id)->first();

        if (!$document) throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");

        if ($format != null) return $this->reloadTicket($document, 'invoice', $format);

    }

    /**
     * Reload Ticket
     * @param  ModelTenant $document
     * @param  string $format
     * @return void
     */
    private function reloadTicket($document, $type, $format) {
        return (new Facturalo)->createPdf($document, $type, $format, 'html');
    }

    /**
     * Reload PDF
     * @param  ModelTenant $document
     * @param  string $format
     * @return void
     */
    private function reloadPDF($document, $type, $format) {
        (new Facturalo)->createPdf($document, $type, $format);
    }
}
