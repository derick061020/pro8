<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Tenant\{
    Configuration,
    Document,
    PaymentCondition,
    Series,
    PaymentMethodType,
    Person,
};
use App\Services\SeriesResolver;
use App\Http\Resources\Tenant\DocumentCollection;
use App\Models\Tenant\StateType;
use App\Http\Resources\Tenant\DocumentResource;
use App\Models\Tenant\Catalogs\{
    DocumentType,
    ChargeDiscountType
};
use Modules\Finance\Traits\FinanceTrait;
use Modules\MobileApp\Http\Requests\Api\SendDocumentWhatsappRequest;
use Modules\WhatsAppApi\Services\WhatsAppCloudApi;
use Modules\Document\Helpers\DocumentHelper;


class DocumentController extends Controller
{

    use FinanceTrait;

    /**
     *
     * @return array
     */
    public function record($id)
    {
        return new DocumentResource(Document::findOrFail($id));
    }


    /**
     *
     * @return array
     */
    public function tables()
    {
        $state_types = StateType::getDataApiApp();

        return compact('state_types');
    }


    /**
     *
     * Modo POS App
     *
     * @return array
     */
    public function getTablesSaleDetail()
    {
        $affectation_igv_types = app(ItemController::class)->table('affectation_igv_types');

        $document_types = $this->table('document_types');

        $item_discount_types = ChargeDiscountType::whereType('discount')->whereLevel('item')->get();

        return compact('affectation_igv_types', 'document_types', 'item_discount_types');
    }


    /**
     * Tablas individuales
     *
     * @param  string $table
     * @return array
     */
    public function table($table)
    {
        $data = [];

        switch ($table)
        {
            case 'document_types':
                $data = DocumentType::onlySaleDocuments()->get();
                break;

        }

        return $data;
    }


    /**
     *
     * Modo POS App
     *
     * @return array
     */
    public function getTablesSalePayment()
    {
        $payment_method_types = PaymentMethodType::get();
        $payment_destinations = $this->getPaymentDestinations();
        $payment_conditions = PaymentCondition::selectGeneralColumns()->get();
        $terms_condition = Configuration::first()->terms_condition_sale;

        $customers = Person::filterApiInitialCustomers()->get()
                            ->transform(function($row) {
                                return $row->getApiRowResource();
                            });

        $series = app(SeriesResolver::class)->applyContext(Series::onlySaleDocuments())->get()
                        ->transform(function($row) {
                            return $row->getApiRowResource();
                        });

        return compact(
            'series',
            'payment_conditions',
            'payment_method_types',
            'payment_destinations',
            'customers',
            'terms_condition'
        );
    }


    /**
     *
     * Listado de documentos
     *
     * @param  Request $request
     * @return DocumentCollection
     */
    public function records(Request $request)
    {
        $records = Document::filterRecordsAppApi($request);

        return new DocumentCollection($records->latest()->take(config('tenant.items_per_page'))->get());
    }

    /**
     *
     * Obtener notificaciones
     *
     * Comprobantes enviados/por enviar
     * Comprobantes pendientes de rectificación
     *
     * @return array
     */
    public function getNotifications()
    {

        $documents_not_sent = Document::whereNotSent()->count();
        $documents_regularize_shipping = Document::whereRegularizeShipping()->count();

        return [
            'success' => true,
            'data' => [
                'documents_not_sent' => $documents_not_sent,
                'documents_regularize_shipping' => $documents_regularize_shipping,
            ]
        ];
    }


    /**
     *
     * Enviar comprobante directo a Whatsapp (Texto/Archivo pdf)
     *
     * @param  SendDocumentWhatsappRequest $request
     * @return array
     */
    public function sendDocumentToWhatsapp(SendDocumentWhatsappRequest $request)
    {
        $document_helper = new DocumentHelper();

        $model = $document_helper->getModelByDocumentType($request->document_type_id);
        $document = $document_helper->getDocumentDataForSendMessage($model, $request->id);
        $params = $document_helper->getParamsForAppSendMessage($request->phone_number, $request->format, $document);

        // dd($params, $model, $document);

        $whatsapp_cloud_api = new WhatsAppCloudApi();

        $send_text_message = $whatsapp_cloud_api->sendMessage($params);
        if(!$send_text_message['success']) return $send_text_message;

        $params['send_type'] = 'document';

        return $whatsapp_cloud_api->sendMessage($params);
    }




    /**
     *
     * Listado de documentos con scroll infinito (cursor-based pagination)
     *
     * Parámetros soportados:
     * - limit: cantidad de registros (máximo 100, default 15)
     * - cursor: posición actual (null en primera petición)
     * - state_type_id: filtrar por estado (01, 03, 05, 07, 09, 11, 13...)
     * - document_type_id: filtrar por tipo (01=Factura, 03=Boleta, 07=N.Crédito, 08=N.Débito...)
     * - series: filtrar por serie exacta (F001, B001...)
     * - number: buscar por número (búsqueda parcial)
     * - customer_id: filtrar por cliente
     * - date_start: fecha inicio rango (Y-m-d)
     * - date_end: fecha fin rango (Y-m-d)
     *
     * @param  Request $request
     * @return array
     */
    public function byScroll(Request $request)
    {
        $limit = min((int) $request->input('limit', 15), 100);
        $cursor = $request->input('cursor');

        $query = Document::with(['person', 'user', 'state_type', 'document_type', 'currency_type'])
            ->whereTypeUser()
            ->orderBy('date_of_issue', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('state_type_id')) {
            $query->where('state_type_id', $request->input('state_type_id'));
        }

        if ($request->filled('document_type_id')) {
            $query->where('document_type_id', $request->input('document_type_id'));
        }

        if ($request->filled('series')) {
            $query->where('series', $request->input('series'));
        }

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->input('number') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->filterRangeDateOfIssue($request->input('date_start'), $request->input('date_end'));
        }

        $records = $cursor
            ? $query->cursorPaginate($limit, ['*'], 'cursor', $cursor)
            : $query->cursorPaginate($limit);

        return [
            'success' => true,
            'data' => new DocumentCollection($records),
            'pagination' => [
                'next_cursor' => $records->nextCursor()?->encode() ?? null,
                'has_more'    => $records->hasMorePages(),
            ],
        ];
    }
}
