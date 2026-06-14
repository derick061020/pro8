<?php

    namespace App\Http\Controllers\Tenant\Api;

    use App\CoreFacturalo\Helpers\Storage\StorageDocument;
    use App\CoreFacturalo\Services\Dni\Dni;
    use App\CoreFacturalo\Services\Extras\ExchangeRate;
    use App\CoreFacturalo\Services\Extras\ValidateCpe2;
    use App\CoreFacturalo\Services\Ruc\Sunat;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\Tenant\ServiceRequest;
    use App\Models\Tenant\Catalogs\Department;
    use App\Models\Tenant\Catalogs\District;
    use App\Models\Tenant\Catalogs\Province;
    use App\Models\Tenant\Document;
    use Exception;
    use Illuminate\Http\Request;
    use Modules\ApiPeruDev\Data\ServiceData;
    use Modules\Document\Helpers\ConsultCdr;
    use Modules\Services\Data\DocumentApiResolver;


    class ServiceController extends Controller
    {


        public const ACCEPTED = '05';
        protected $wsClient;
        use StorageDocument;
        protected $document;

        public function consultCdrStatus(ServiceRequest $request)
        {

            $document_type_id = $request->codigo_tipo_documento;
            $series = $request->serie_documento;
            $number = $request->numero_documento;

            $this->document = Document::where([['soap_type_id', '02'],
                ['document_type_id', $document_type_id],
                ['series', $series],
                ['number', $number]
            ])->first();

            // if(!$this->document)  throw new Exception("Documento no encontrado");
            if (!$this->document) return [
                'success' => false,
                'message' => "Documento no encontrado"
            ];

            return (new ConsultCdr())->search($this->document);

        }


        /**
         * Consulta de RUC. Usa el proveedor configurado para el tenant desde el admin
         * (apiperu / sunat) con fallback automatico a la otra API.
         *
         * @param int $number
         *
         * @return array
         */
        public function ruc($number)
        {
            return (new DocumentApiResolver())->service('ruc', $number);
        }


        /**
         * Consulta de DNI. Usa el proveedor configurado para el tenant desde el admin
         * (apiperu / sunat) con fallback automatico a la otra API.
         *
         * @param int $number
         *
         * @return array
         */
        public function dni($number)
        {
            return (new DocumentApiResolver())->service('dni', $number);
        }

        public function exchangeRateTest($date)
        {
            return (new ServiceData())->exchange($date);
//            $sale = 1;
//            $purchase = 1;
//            if ($date <= now()->format('Y-m-d')) {
//                /**
//                 * @var \App\Models\Tenant\ExchangeRate $ex_rate
//                 * @var \App\Models\Tenant\ExchangeRate $last_ex_rate
//                 */
//                $ex_rate = \App\Models\Tenant\ExchangeRate::where('date', $date)->first();
//                if ($ex_rate) {
//                    $sale = $ex_rate->sale;
//                    $purchase = $ex_rate->purchase;
//                } else {
//                    $exchange_rate = new ExchangeRate();
//                    $res = $exchange_rate->searchDate($date);
//                    if ($res) {
//                        $ex_rate = \App\Models\Tenant\ExchangeRate::create([
//                            'date' => $date,
//                            'date_original' => $res['date_data'],
//                            'purchase' => $res['data']['purchase'],
//                            'purchase_original' => $res['data']['purchase'],
//                            'sale' => $res['data']['sale'],
//                            'sale_original' => $res['data']['sale']
//                        ]);
//                        $sale = $ex_rate->sale;
//                        $purchase = $ex_rate->purchase;
//                    } else {
//                        $last_ex_rate = \App\Models\Tenant\ExchangeRate::orderBy('date', 'desc')->first();
//                        if ($last_ex_rate) {
//                            $sale = $last_ex_rate->sale;
//                            $purchase = $last_ex_rate->purchase;
//                        } else {
//                            $sale = 0;
//                            $purchase = 0;
//                        }
//                    }
//                }
//            }
//            return [
//                'date' => $date,
//                'sale' => $sale,
//                'purchase' => $purchase,
//            ];
        }

        public function documentStatus(Request $request)
        {
            if ($request->has('external_id') or $request->has('serie_number')) {
                $external_id = $request->input('external_id');
                $request_serie = $request->input('serie_number');
                $serie_number = explode('-', $request_serie);
                $serie = $serie_number[0];
                $number = $serie_number[1];

                if (!$external_id) {
                    $document = Document::where('number', $number)
                        ->where('series', $serie)
                        ->first();
                } else {
                    $document = Document::where('external_id', $external_id)
                        ->where('number', $number)
                        ->where('series', $serie)
                        ->first();
                }

                if (!$document) {
                    throw new Exception("El documento con código externo {$external_id} o numero {$request_serie}, no se encuentra registrado.");
                }
                return [
                    'success' => true,
                    'data' => [
                        'number' => $document->number_full,
                        'filename' => $document->filename,
                        'external_id' => $document->external_id,
                        'status_id' => $document->state_type_id,
                        'status' => $document->state_type->description,
                        'qr' => $document->qr,
                        'number_to_letter' => $document->number_to_letter,
                    ],
                    'links' => [
                        'xml' => $document->download_external_xml,
                        'pdf' => $document->download_external_pdf,
                        'cdr' => ($document->download_external_cdr) ? $document->download_external_cdr : '',
                    ],
                ];
            }
        }

        public function validateCpe(Request $request)
        {

            $company_number = $request->numero_ruc_emisor;
            $document_type_id = $request->codigo_tipo_documento;
            $series = $request->serie_documento;
            $number = $request->numero_documento;
            $date_of_issue = $request->fecha_de_emision;
            $total = $request->total;

            $validate_cpe = new ValidateCpe2();
            $response = $validate_cpe->search($company_number, $document_type_id, $series, $number, $date_of_issue, $total);

            if ($response['success']) {

                return [
                    'success' => true,
                    'data' => $response['data']
                ];

            } else {
                return [
                    'success' => false,
                    'data' => $response
                ];
            }

        }


    }
