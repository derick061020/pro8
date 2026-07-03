<?php

namespace App\Observers;

use App\CoreFacturalo\Requests\Inputs\Functions;
use App\Models\Tenant\Cash;
use App\Models\Tenant\CashDocument;
use App\Models\Tenant\Company;
use App\Models\Tenant\Document;
use Modules\Finance\Traits\FinanceTrait;

class DocumentObserver
{
    use FinanceTrait;
    /**
     * Handle the document "creating" event.
     *
     * @param  \App\Models\Tenant\Document  $document
     * @return void
     */
    public function creating(Document $document)
    {
        $company = Company::active();
        $number = Functions::newNumber($document->soap_type_id,
                                       $document->document_type_id,
                                       $document->series,
                                       $document->number, Document::class);
        $document->number = $number;

        $document->filename = Functions::filename($company, $document->document_type_id, $document->series, $number);
        $document->unique_filename = $document->filename; //campo único para evitar duplicados

    }

    /**
     * Handle the document "updated" event.
     *
     * @param  \App\Models\Tenant\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        //
    }

    /**
     * Handle the document "deleted" event.
     *
     * @param  \App\Models\Tenant\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        //
    }

    /**
     * Handle the document "restored" event.
     *
     * @param  \App\Models\Tenant\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        //
    }

    /**
     * Handle the document "force deleted" event.
     *
     * @param  \App\Models\Tenant\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        //
    }

    public function created(Document $document)
    {
        // Esto nos verifica que es un documento generado desde el api
        $cash = Cash::where([
            ['user_id', auth()->id()],
            ['state', true],
        ])->firstOrFail();

        $cash_document = CashDocument::where('cash_id', $cash->id)
                    ->where('document_id', $document->id)->first();
        if (!$cash_document) {
            $this->finance_cash_document( $document, $document->id );
        }
    }
}
