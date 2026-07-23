<?php
namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\DocumentCollection;
use App\Models\Tenant\Document;
use App\Models\Tenant\StateType;
use Exception;
use Facades\App\Http\Controllers\Tenant\DocumentController as DocumentControllerSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class DocumentController extends Controller
{
    use StorageDocument;

    public function __construct()
    {
        $this->middleware('input.request:document,api', ['only' => ['store', 'storeServer']]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $print_result = ['auto_printed' => false, 'print_order_id' => null, 'reason' => null];

        $fact = DB::connection('tenant')->transaction(function () use ($request, &$print_result) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());
            $facturalo->createXmlUnsigned();
            $service_pse_xml = $facturalo->servicePseSendXml();
            $facturalo->signXmlUnsigned($service_pse_xml['xml_signed']);
            $facturalo->updateHash($service_pse_xml['hash']);
            $facturalo->updateQr();
            $facturalo->createPdf();
            // Impresión automática: se dispara antes del envío a SUNAT para no
            // hacer esperar al cliente. Solo actúa si actions.auto_print === true.
            $print_result = $facturalo->generatePrintOrder();
            $facturalo->senderXmlSignedBill($service_pse_xml['code']);
            $facturalo->sendEmail();

            return $facturalo;
        });

        $document = $fact->getDocument();
        $response = $fact->getResponse();

        return [
            'success' => true,
            'data' => [
                'number' => $document->number_full,
                'filename' => $document->filename,
                'external_id' => $document->external_id,
                'state_type_id' => $document->state_type_id,
                'state_type_description' => $this->getStateTypeDescription($document->state_type_id),
                'number_to_letter' => $document->number_to_letter,
                'hash' => $document->hash,
                'qr' => $document->qr,
                'id' => $document->id,
                'print_ticket' =>  $document->getUrlPrintByFormat('ticket'),
            ],
            // Resultado de la impresión automática server-side.
            // Si auto_printed=true, el cliente NO debe ejecutar su flujo de
            // descarga+base64+POST a /print-orders (evita doble impresión).
            'print' => $print_result,
            'data_ws' => [
                'message_text' => "Su comprobante de pago electrónico {$document->number_full} ha sido generado correctamente, puede revisarlo en el siguiente enlace: ".url('')."/print/document/{$document->external_id}/ticket"."",
                "pdf_a4_filename" => url('')."/api/document-file/document/{$document->external_id}/a4",
                "pdf_ticket_filename" => url('')."/api/document-file/document/{$document->external_id}/ticket",
                "full_filename" => $document->filename.".pdf",
                "customer_telephone" => optional($document->person)->telephone
            ],
            'links' => [
                'xml' => $document->download_external_xml,
                'pdf' => $document->download_external_pdf,
                'cdr' => ($response['sent']) ? $document->download_external_cdr : '',
            ],
            'response' => ($response['sent']) ? Arr::except($response, 'sent') : [],
        ];
    }

    public function send(Request $request)
    {
        if ($request->has('external_id')) {
            $external_id = $request->input('external_id');
            $document = Document::where('external_id', $external_id)->first();
            if (!$document) {
                throw new Exception("El documento con código externo {$external_id}, no se encuentra registrado.");
            }
            if ($document->group_id !== '01') {
                throw new Exception("El tipo de documento {$document->document_type_id} es inválido, no es posible enviar.");
            }
            $fact = new Facturalo();
            $fact->setDocument($document);
            $fact->loadXmlSigned();
            $fact->onlySenderXmlSignedBill();
            $response = $fact->getResponse();
            return [
                'success' => true,
                'data' => [
                    'number' => $document->number_full,
                    'filename' => $document->filename,
                    'external_id' => $document->external_id,
                    'state_type_id' => $document->state_type_id,
                    'state_type_description' => $this->getStateTypeDescription($document->state_type_id),
                ],
                'links' => [
                    'cdr' => $document->download_external_cdr,
                ],
                'response' => Arr::except($response, 'sent'),
            ];
        }
    }

    public function storeServer(Request $request)
    {
        $fact = DB::connection('tenant')->transaction(function () use ($request) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());

            return $facturalo;
        });

        $document = $fact->getDocument();
        $data_json = $document->data_json;

        // $zipFly = new ZipFly();

        $this->uploadStorage($document->filename, base64_decode($data_json->file_xml_signed), 'signed');
        $this->uploadStorage($document->filename, base64_decode($data_json->file_pdf), 'pdf');

        $document->external_id = $data_json->external_id;
        $document->hash = $data_json->hash;
        $document->qr = $data_json->qr;
        $document->save();

        // Send SUNAT
        if ($document->group_id === '01') {
            if ($data_json->query) {
                DocumentControllerSend::send($document->id);
            }

        }

        return [
            'success' => true,
        ];
    }

    public function documentCheckServer($external_id)
    {
        $document = Document::where('external_id', $external_id)->first();

        if ($document->state_type_id === '05' && $document->group_id === '01') {
            $file_cdr = base64_encode($this->getStorage($document->filename, 'cdr'));
        } else {
            $file_cdr = null;
        }

        return [
            'success' => true,
            'state_type_id' => $document->state_type_id,
            'file_cdr' => $file_cdr,
        ];
    }

    private function getStateTypeDescription($id)
    {
        return StateType::find($id)->description;
    }

    public function lists($startDate = null, $endDate = null)
    {

        if ($startDate == null)
        {
            $record = Document::whereTypeUser()
                                ->orderBy('date_of_issue', 'desc')
                                ->take(50)
                                ->get();
        }
        else
        {
            $record = Document::whereBetween('date_of_issue', [$startDate, $endDate])
                ->orderBy('date_of_issue', 'desc')
                ->get();
        }

        $records = new DocumentCollection($record);
        return $records;
    }

    public function updatestatus(Request $request)
    {
        $record = Document::whereExternal_id($request->externail_id)->first();
        $record->state_type_id = $request->state_type_id;
        $record->save();

        return [
            'success' => true,
        ];
    }

}
