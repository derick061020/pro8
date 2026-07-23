<?php

namespace Modules\MobileApp\Http\Controllers\Api;

use App\Http\Controllers\System\ClientController;
use App\Models\System\Client;
use App\Models\System\Configuration;
use App\Traits\GuestRegisterTrait;
use Exception;
use Illuminate\Routing\Controller;
use Modules\MobileApp\Http\Requests\Api\GuestRegisterApiRequest;

class GuestRegisterController extends Controller
{
    use GuestRegisterTrait;

    /**
     * Datos necesarios para pintar el formulario de registro en la app:
     * planes disponibles, plan por defecto y base_url del subdominio.
     *
     * @return array
     */
    public function init()
    {
        $configuration = Configuration::select(['enable_guest_register', 'guest_register_plan_id'])->first();

        return [
            'success' => true,
            'data' => [
                'enable_guest_register' => (bool) ($configuration->enable_guest_register ?? false),
                'base_url' => '.' . config('tenant.app_url_base'),
                'plan_default' => $configuration->guest_register_plan_id ?? null,
                'plans' => $this->getRegisterPlans(),
            ],
        ];
    }

    /**
     * Registra un tenant desde la app. Valida todo en GuestRegisterApiRequest;
     * si pasa, crea el tenant y lo activa de inmediato (sin verificacion por correo).
     *
     * @param  GuestRegisterApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(GuestRegisterApiRequest $request)
    {
        try
        {
            $request->merge([
                'identity_document_type_id' => $request->input('identity_document_type_id', '6'),
            ]);

            $inputs = $this->inputsToRegister($request);

            $response = app(ClientController::class)->store($inputs);

            if (!$response['success']) {
                return response()->json($response, 400);
            }

            $client = Client::where('number', $request->number)->firstOrFail();

            // Activar de inmediato: marca el usuario admin como verificado.
            $this->changeClientConnection($client);
            $this->updateDataVerifiedUser($this->getFirstUser());

            $protocol = config('tenant.force_https') ? 'https' : 'http';

            return response()->json([
                'success' => true,
                'message' => 'Cuenta registrada correctamente.',
                'data' => [
                    'name' => $client->name,
                    'email' => $client->email,
                    'subdomain' => $request->subdomain,
                    'url' => "{$protocol}://{$client->hostname->fqdn}",
                ],
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
