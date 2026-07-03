<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Payment\Http\Resources\PaymentConfigurationResource;
use Modules\Payment\Models\PaymentConfiguration;
use Modules\Payment\Http\Requests\PaymentConfigurationRequest;
use Illuminate\Http\Request;
use Modules\Finance\Helpers\UploadFileHelper;


class PaymentConfigurationController extends Controller
{

    /**
     * @return PaymentConfigurationResource
     */
    public function record()
    {
        return new PaymentConfigurationResource(PaymentConfiguration::firstOrFail());
    }


    /**
     * @return array
     */
    public function recordPermissions()
    {
        return [
            'data' => PaymentConfiguration::getPaymentPermissions()
        ];
    }


    /**
     * Actualizar configuracion
     *
     * @param  PaymentConfigurationRequest $request
     * @return array
     */
    public function store(PaymentConfigurationRequest $request)
    {

        $type = $request->type;
        $record = PaymentConfiguration::firstOrFail();

        $response = match ($type) {
            '01' => $this->setDataYape($record, $request),
            '02' => $this->setDataMP($record, $request),
            '03' => $this->setDataCulqi($record, $request),
            '04' => $this->setDataIzipay($record, $request),
            default => null,
        };

        $record->save();

        return $response;
    }


    /**
     *
     * @param  PaymentConfiguration $record
     * @param  PaymentConfigurationRequest $request
     * @return void
     */
    public function setDataMP(PaymentConfiguration &$record, $request)
    {
        $record->enabled_mp = $request->enabled_mp;
        $record->public_key_mp = $request->public_key_mp;

        if($request->access_token_mp)
        {
            $record->access_token_mp = $request->access_token_mp;
        }

        return [
            'success' => true,
            'message' => 'Configuración actualizada'
        ];
    }

    public function setDataCulqi(PaymentConfiguration &$record, $request)
    {
        if ($record->enabled_izipay === true &&  $request->enabled_culqi === true) {
            return [
                'success' => false,
                'message' => 'No se puede habilitar Culqi si Izipay está habilitado'
            ];
        }

        $record->enabled_culqi = $request->enabled_culqi;
        if ($request->publickey_culqi) {
            $record->publickey_culqi = $request->publickey_culqi;
        }

        if ($request->privatekey_culqi) {
            $record->privatekey_culqi = $request->privatekey_culqi;
        }

        if ($request->idrsa_culqi) {
            $record->idrsa_culqi = $request->idrsa_culqi;
        }
    
        if ($request->rsa_culqi) {
            $record->rsa_culqi = $request->rsa_culqi;
        }

        return [
            'success' => true,
            'message' => 'Configuración actualizada'
        ];
    }

    public function setDataIzipay(PaymentConfiguration &$record, $request)
    {
        if ($record->enabled_culqi === true &&  $request->enabled_izipay === true) {
            return [
                'success' => false,
                'message' => 'No se puede habilitar Izipay si Culqi está habilitado'
            ];
        }

        $record->enabled_izipay = $request->enabled_izipay;

        if ($request->username_izipay) {
            $record->username_izipay = $request->username_izipay;
        }

        if ($request->password_izipay) {
            $record->password_izipay = $request->password_izipay;
        }

        if ($request->publickey_izipay) {
            $record->publickey_izipay = $request->publickey_izipay;
        }

        if ($request->sha256key_izipay) {
            $record->sha256key_izipay = $request->sha256key_izipay;
        }

        return [
            'success' => true,
            'message' => 'Configuración actualizada'
        ];
    }

    /**
     *
     * @param  PaymentConfiguration $record
     * @param  PaymentConfigurationRequest $request
     * @return void
     */
    public function setDataYape(PaymentConfiguration &$record, $request)
    {
        $record->enabled_yape = $request->enabled_yape;
        $record->name_yape = $request->name_yape;
        $record->telephone_yape = $request->telephone_yape;

        if($request->qrcode_yape && $request->temp_path_yape)
        {
            $filename = UploadFileHelper::uploadFileFromTempFile('payment_configurations', $request->qrcode_yape, $request->temp_path_yape, $record->id, 'qr_yape');
            $record->qrcode_yape = $filename;
        }
    }


    /**
     * Cargar qr yape
     *
     * @param  Request $request
     * @return array
     */
    public function uploadQrcodeYape(Request $request)
    {

        $validate_upload = UploadFileHelper::validateUploadFile($request, 'file', 'jpg,jpeg,png,svg,webp');
        if(!$validate_upload['success']) return $validate_upload;

        if ($request->hasFile('file'))
        {
            $new_request = [
                'file' => $request->file('file'),
                'type' => $request->input('type'),
            ];

            return UploadFileHelper::getTempFile($new_request);
        }

        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }


}