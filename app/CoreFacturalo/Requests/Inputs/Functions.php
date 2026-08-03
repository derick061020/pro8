<?php

namespace App\CoreFacturalo\Requests\Inputs;

use App\Models\Tenant\Document;
use App\Models\Tenant\Series;
use Carbon\Carbon;
use Exception;
use Modules\Document\Models\SeriesConfiguration;
use Modules\Offline\Services\NumberRangeService;

class Functions
{
    public static function newNumber($soap_type_id, $document_type_id, $series, $number, $model)
    {

        if ($number === '#') {

            // En un terminal offline el correlativo sale del bloque que le
            // reservó el servidor, no del último número emitido: así dos
            // instalaciones desconectadas entre sí no pueden repetir número.
            $offline_number = NumberRangeService::nextForModel($model, $document_type_id, $series);

            if ($offline_number !== null) {
                return $offline_number;
            }

            $document = $model::select('number')
                                    ->where('soap_type_id', $soap_type_id)
                                    ->where('document_type_id', $document_type_id)
                                    ->where('series', $series)
                                    ->orderBy('number', 'desc')
                                    ->first();

            if($document){

                $next_number = (int)$document->number+1;

            }else{

                $series_configuration = SeriesConfiguration::where([['document_type_id',$document_type_id],['series',$series]])->first();
                $next_number = ($series_configuration) ? (int) $series_configuration->number:1;

            }

            // En el servidor: si el número cae dentro de un bloque prestado a
            // un terminal, se saltea hasta después del bloque.
            return NumberRangeService::skipReservedForModel($model, $document_type_id, $series, $next_number);

        }

        return $number;

        // if ($number === '#') {
        //     $document = $model::select('number')
        //                         ->where('soap_type_id', $soap_type_id)
        //                         ->where('document_type_id', $document_type_id)
        //                         ->where('series', $series)
        //                         ->orderBy('number', 'desc')
        //                         ->first();
        //     return ($document)?(int)$document->number+1:1;
        // }
        // return $number;
    }

    public static function filename($company, $document_type_id, $series, $number)
    {
        return join('-', [$company->number, $document_type_id, $series, $number]);
    }

    public static function validateUniqueDocument($soap_type_id, $document_type_id, $series, $number, $model)
    {
        $document = $model::where('soap_type_id', $soap_type_id)
                        ->where('document_type_id', $document_type_id)
                        ->where('series', $series)
                        ->where('number', $number)
                        ->first();
        if($document) {
            throw new Exception("El documento: {$document_type_id} {$series}-{$number} ya se encuentra registrado.");
        }
    }

    public static function identifier($soap_type_id, $date_of_issue, $model)
    {
        $documents = $model::where('soap_type_id', $soap_type_id)
                        ->where('date_of_issue', $date_of_issue)
                        ->get();
        $numeration = count($documents) + 1;
        $path = explode('\\', $model);
        switch (array_pop($path)) {
            case 'Voided':
                $prefix = 'RA';
                break;
            default:
                $prefix = 'RC';
                break;
        }

        return join('-', [$prefix, Carbon::parse($date_of_issue)->format('Ymd'), $numeration]);
    }

    /**
     * @param      $inputs
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public static function valueKeyInArray($inputs, $key, $default = null)
    {
        return (isset($inputs[$key]) && null !== $inputs[$key]) ? $inputs[$key] : $default;
    }
}
