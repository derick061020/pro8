<?php

namespace App\CoreFacturalo\WS\Services;

use App\CoreFacturalo\WS\Response\StatusCdrResult;

/**
 * Class ConsultCdrService.
 */
class ConsultCdrService extends BaseSunat
{
    /**
     * Obtiene el estado del comprobante.
     *
     * @param string $ruc
     * @param string $tipo
     * @param string $serie
     * @param int    $numero
     *
     * @return StatusCdrResult
     */
    public function getStatus($ruc, $tipo, $serie, $numero)
    {
        return $this->getStatusResult('getStatus', 'status', $ruc, $tipo, $serie, $numero);
    }

    /**
     * Obtiene el CDR del comprobante.
     *
     * @param string $ruc
     * @param string $tipo
     * @param string $serie
     * @param int    $numero
     *
     * @return StatusCdrResult
     */
    public function getStatusCdr($ruc, $tipo, $serie, $numero)
    {
        return $this->getStatusResult('getStatusCdr', 'statusCdr', $ruc, $tipo, $serie, $numero);
    }

    private function getStatusResult($method, $resultName, $ruc, $tipo, $serie, $numero)
    {
        $client = $this->getClient();
        $result = new StatusCdrResult();

        try {
            $params = [
                'rucComprobante' => $ruc,
                'tipoComprobante' => $tipo,
                'serieComprobante' => $serie,
                'numeroComprobante' => $numero,
            ];
            $response = $client->call($method, ['parameters' => $params]);
            $vars = get_object_vars($response);
            if (isset($vars['document']) && strlen($vars['document']) > 200) {
                $vars['document'] = '[ZIP: ' . strlen($vars['document']) . ' bytes]';
            }
            $statusResponse = $response->{$resultName} ?? null;

            if (!$statusResponse) {
                \Log::warning('No se recibió un documento válido desde el servicio SOAP');
                return $result->setSuccess(false)
                    ->setError($this->getErrorByCode('', 'La SUNAT no devolvió una respuesta válida para la consulta del CDR.'));
            }

            // Estado del comprobante informado por SUNAT (siempre presente).
            $statusCode = $statusResponse->statusCode ?? null;
            $statusMessage = $statusResponse->statusMessage ?? '';

            // El CDR (zip base64) solo viene cuando SUNAT lo tiene disponible.
            // En getStatus (sin CDR) la respuesta no incluye 'content'.
            $document = ($resultName === 'statusCdr')
                ? ($statusResponse->content ?? null)
                : null;

            if ($document) {
                $result->setCdrZip($document);
                $cdrResponse = $this->extractResponse($document);
                $code = $cdrResponse->getCode();
                $message = $cdrResponse->getDescription();

                $result->setCdrResponse($cdrResponse)
                    ->setCode($code)
                    ->setMessage($message)
                    ->setSuccess(true);

                if ($this->isExceptionCode($code)) {
                    $this->loadErrorByCode($result, $code);
                }
            } else {
                // SUNAT respondió pero sin CDR (comprobante no informado, rechazado,
                // aún en proceso, etc.). Devolver el estado para un mensaje claro.
                \Log::warning("Consulta CDR sin contenido. statusCode: {$statusCode}; statusMessage: {$statusMessage}");
                $error = $this->getErrorByCode($statusCode, $statusMessage);
                if (empty($error->getMessage())) {
                    $error->setMessage($statusMessage ?: 'La SUNAT no devolvió el CDR para este comprobante.');
                }
                $result->setCode($statusCode)
                    ->setMessage($statusMessage)
                    ->setSuccess(false)
                    ->setError($error);
            }

            // $statusCdr = $response->{$resultName};

            // $code = $statusCdr->statusCode;
            // $result->setCode($code)
            //     ->setMessage($statusCdr->statusMessage)
            //     ->setSuccess(true);

            // if (isset($statusCdr->content)) {
            //     $result->setCdrZip($statusCdr->content)
            //            ->setCdrResponse($this->extractResponse($statusCdr->content));
            //     $code = $result->getCdrResponse()->getCode();
            // }

            // if ($this->isExceptionCode($code)) {
            //     $this->loadErrorByCode($result, $code);
            // }
        } catch (\SoapFault $e) {
            $result->setError($this->getErrorFromFault($e));
        }

        return $result;
    }
}
