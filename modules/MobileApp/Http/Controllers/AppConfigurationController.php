<?php

namespace Modules\MobileApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MobileApp\Http\Resources\Api\AppConfigurationResource;
use Modules\MobileApp\Models\AppConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\MobileApp\Http\Controllers\Api\AppConfigurationController as AppConfigurationControllerApi;


class AppConfigurationController extends Controller
{

    /**
     * Retorna la vista de configuración de la app
     */
    public function view()
    {
        return view('mobileapp::configuration.index');
    }

    /**
     * @return array
     */
    public function record()
    {
        return app(AppConfigurationControllerApi::class)->record();
    }


    /**
     *
     * Actualizar configuracion gráfica de la app
     *
     * @param  Request $request
     * @return array
     */
    public function store(Request $request)
    {

        $record = AppConfiguration::firstOrFail();
        $record->theme_color = $request->theme_color;
        $record->card_color = $request->card_color;
        $record->header_waves = $request->header_waves;
        $record->app_mode = $request->app_mode;
        $record->direct_send_documents_whatsapp = $request->direct_send_documents_whatsapp;
        $record->primary_color = $request->primary_color;
        $record->save();

        // Invalidar caché de app.json al actualizar la configuración
        Cache::forget('app_json');

        return [
            'success' => true,
            'message' => 'Configuración gráfica actualizada',
            'data' => $record->getRowResource(),
        ];
    }


}