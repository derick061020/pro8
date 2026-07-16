<?php

namespace Modules\Document\Helpers;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\CoreFacturalo\WS\Services\ConsultCdrService;
use App\CoreFacturalo\Facturalo;
use App\CoreFacturalo\WS\Validator\XmlErrorCodeProvider;
use App\CoreFacturalo\WS\Client\WsClient;
use App\CoreFacturalo\WS\Services\SunatEndpoints;
use App\Models\Tenant\Company;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\PseService\Http\Gior\Service;
use Modules\PseService\Http\Gior\ServiceOseSendFact;
use Modules\PseService\Http\Gior\ServiceSendFact;

class ConsultCdr
{
    
    protected $wsClient;
    protected $document;
    protected $response;
    protected $modelPse;
    use StorageDocument;
    const REGISTERED = '01';
    const SENT = '03';
    const ACCEPTED = '05';
    const OBSERVED = '07';
    const REJECTED = '09';
    const CANCELING = '13';
    const VOIDED = '11';


    public function search($document){

        $this->document = $document;
        $document_type_id = $this->document->document_type_id;
        $series = $this->document->series;
        $number = $this->document->number;

        $company = Company::active();
        
        $response = null;
        if ($company->send_document_to_pse) {

            $model = in_array($company->pse_provider_id, [2,3,5]) ? new Service() : new ServiceSendFact();
            $this->modelPse = $model;
            $response = $this->consultaCdrSendfact($model);
        } else {

            if ($company->soap_send_id === '04') {
                $model = new ServiceOseSendFact;
                $this->modelPse = $model;
                $response = $this->consultaCdrSendfact($model);
            } else {
                $response = $this->consultaCdrSunat($company, $document_type_id, $series, $number);

            }
        }
        return $response;

    }

    public function consultaCdrSunat(Company $company, $document_type_id = null, $series = null, $number = null) 
    {
        $wsdl = 'consultCdrStatus';
        $username = $company->soap_username;
        $password = $company->soap_password;

        $company_number = $company->number;
        $this->wsClient = new WsClient($wsdl);
        $this->wsClient->setCredentials($username, $password);
        $this->wsClient->setService(SunatEndpoints::FE_CONSULTA_CDR.'?wsdl');

        $consultCdrService = new ConsultCdrService();
        $consultCdrService->setClient($this->wsClient);
        $consultCdrService->setCodeProvider(new XmlErrorCodeProvider());
        $res = $consultCdrService->getStatusCdr($company_number,$document_type_id,$series,$number);

        if(!$res->isSuccess()) {

            // No se pudo obtener la constancia (CDR). Consultamos el estado de
            // aceptación del comprobante para informar si SUNAT lo aceptó o no.
            $acceptance = $this->consultAcceptanceStatus($consultCdrService, $company_number, $document_type_id, $series, $number);

            $cdrError = "Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}";

            Log::info("Consulta CDR SUNAT [{$document_type_id}-{$series}-{$number}] => {$acceptance['text']} | {$cdrError}");

            return [
                'success' => false,
                'accepted' => $acceptance['accepted'],
                'status_code' => $acceptance['code'],
                'status_message' => $acceptance['message'],
                'message' => "{$acceptance['text']} {$cdrError}",
            ];
            // throw new Exception("Code: {$res->getError()->getCode()}; Description: {$res->getError()->getMessage()}");

        } else {

            $cdrResponse = $res->getCdrResponse();
            $code = $cdrResponse->getCode();
            $description = $cdrResponse->getDescription();

            // dd($cdrResponse, $res->getCdrZip());
            // $this->updateState(self::ACCEPTED);

            $this->response = [
                'sent' => true,
                'code' => $cdrResponse->getCode(),
                'description' => $cdrResponse->getDescription(),
                'notes' => $cdrResponse->getNotes()
            ];

            $this->validationCodeResponse($code, $description, $res);

            return [
                'success' => true,
                'message' => $description
            ];

        }

    }

    /**
     * Consulta a la SUNAT el estado de ACEPTACIÓN del comprobante (getStatus),
     * independientemente de si la constancia (CDR) está disponible o no.
     *
     * @return array{accepted: bool|null, code: string|null, message: string, text: string}
     */
    public function consultAcceptanceStatus($consultCdrService, $company_number, $document_type_id, $series, $number)
    {
        // Códigos de estado del servicio getStatus de SUNAT (solo los inequívocos;
        // para el resto se muestra el mensaje literal que devuelve SUNAT).
        $labels = [
            '0001' => 'ACEPTADO',
            '0002' => 'RECHAZADO',
        ];

        try {
            $status = $consultCdrService->getStatus($company_number, $document_type_id, $series, $number);

            $code = $status->getCode();
            $message = $status->getMessage();
            $label = $labels[$code] ?? null;
            $accepted = ($code === '0001');

            $text = $label
                ? "El comprobante fue {$label} por SUNAT (código {$code}: {$message})."
                : "Estado del comprobante en SUNAT (código {$code}: {$message}).";

            return [
                'accepted' => $accepted,
                'code' => $code,
                'message' => $message,
                'text' => $text,
            ];
        } catch (\Throwable $e) {
            Log::error('No se pudo consultar el estado de aceptación en SUNAT: '.$e->getMessage());

            return [
                'accepted' => null,
                'code' => null,
                'message' => $e->getMessage(),
                'text' => 'No se pudo determinar si el comprobante fue aceptado en SUNAT.',
            ];
        }
    }

    public function consultaCdrSendfact($model)
    {

        $response = $model->querySummary($this->document->filename);

        if ($model instanceof Service) {
            $code = $response['code'];
        } else {
            $code = $response['document_status'];
        }

        $this->response = [
            'sent' => true,
            'code' => $code,
            'description' => $response['message'],
            'notes' => isset($response['notes']) ? $response['notes'] : []
        ];

        if ($model instanceof Service) {
            $this->validationCodeResponse($code, $response['message'], $response['cdr']);
        } else {
            $this->validationCodeResponseIntegration($code, $response['message'], $response);
        }

        return [
            'success' => true,
            'message' => $response['message']
        ];
    }

    public function validationCodeResponseIntegration($document_status, $message, $cdrResponse)
    {
        $code = (int)$document_status;
        $response = null;
        //Errors

        switch ($code) {
            case 0:
                if($this->document->state_type_id == '01'){
                    $this->updateState(self::REJECTED);
                }
                break;
            case 1:
                $this->uploadFile($cdrResponse['cdr'], 'cdr_b64');
                if($this->document->state_type_id == '01'){ $this->updateState(self::ACCEPTED);
                }
                break;
            case 2:
                if($this->document->state_type_id == '01'){
                    $company = Company::active();
                    if ($company->soap_send_id == 4 ) {
                        $this->updateState(self::REGISTERED);
                    } else {
                        $this->updateState(self::REJECTED);
                    }
                }
                break;
            case 3:
                $xml_signed_data = $this->getStorage($this->document->filename, 'signed');
                $response = $this->modelPse->sendXmlSigned($this->document->filename, $xml_signed_data);

                $this->response = [
                    'sent' => true,
                    'code' => $response['code'],
                    'description' => $response['message'],
                    'notes' => $response['observations'] ?? []
                ];


                $this->uploadFile($response['cdr'], 'cdr_b64');

                if($this->document->state_type_id == '01'){
                    $this->updateState(self::ACCEPTED);
                }
                break;
            case 4:
                if($this->document->state_type_id == '01'){
                    $this->updateState(self::SENT);
                }
                break;

            case 5:
                if($this->document->state_type_id == '01'){
                    $this->updateState(self::REGISTERED);
                }
                Log::error("ERROR: Code {$document_status}; Description: {$message}");
                break;
        }

    }

    public function validationCodeResponse($code, $message, $res)
    {
        //Errors
        if($code === 'ERROR_CDR') {
            return;
        }

        if($code === 'HTTP') {
            throw new Exception("Code: {$code}; Description: {$message}");
        }

        if((int)$code === 0) {

            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::ACCEPTED);
            }

            return;
        }

        if((int)$code < 2000) {
            //Excepciones
            throw new Exception("Code: {$code}; Description: {$message}");

        } elseif ((int)$code < 4000) {
            //Rechazo
            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::REJECTED);
            }

        } else {

            $this->uploadFile($res->getCdrZip(), 'cdr');

            if($this->document->state_type_id == '01'){
                $this->updateState(self::OBSERVED);
            }
            //Observaciones
        }
        return;
    }


    public function uploadFile($file_content, $file_type)
    {
        $this->uploadStorage($this->document->filename, $file_content, $file_type);
    }


    public function updateState($state_type_id)
    {
        $this->document->update([
            'state_type_id' => $state_type_id,
            'soap_shipping_response' => isset($this->response['sent']) ? $this->response:null
        ]);
    }


}
