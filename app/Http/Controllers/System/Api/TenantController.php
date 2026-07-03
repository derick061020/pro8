<?php

namespace App\Http\Controllers\System\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System\Plan;
use App\Http\Requests\System\ClientRequest;
use App\Http\Controllers\System\ClientController;
use Illuminate\Support\Str;
use App\Traits\GuestRegisterTrait;
use App\Rules\SubdomainNotLatin;

class TenantController extends Controller
{
    use GuestRegisterTrait;

    // Funcion para crear un nuevo tenant mediante API 
    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|numeric|unique:system.clients,number',
            'subdomain' => ['required', 'alpha_dash', new SubdomainNotLatin],
            'email' => 'required|email',
            'plan_id' => 'required|exists:system.plans,id'
        ]);

        $password = Str::random(10);
        $plan = Plan::find($request->plan_id);
        $basic_module_levels = $this->getBasicModuleLevels();

        $inputs = [
            'name' => 'EMPRESA ' . $request->ruc, 
            'email' => $request->email,
            'identity_document_type_id' => '6',
            'number' => $request->ruc,
            'password' => $password,
            'subdomain' => strtolower($request->subdomain),
            'plan_id' => $plan->id,
            'plan_period_id' => '1',
            'price' => $plan->pricing,
            'locked_emission' => false,
            'type' => 'admin',
            'config_system_env' => true,
            'soap_send_id' => '01',
            'soap_type_id' => '01',
            'soap_username' => null,
            'soap_password' => null,
            'soap_url' => null,
            'regex_password_client' => false,
            'modules' => $basic_module_levels['modules'],
            'levels' => $basic_module_levels['levels'],
            'from_guest_register' => false,
            'enable_list_product' => true
        ];

        $clientRequest = new ClientRequest();
        $clientRequest->merge($inputs);

        $response = app(ClientController::class)->store($clientRequest);

        if (!$response['success']) {
            return response()->json($response, 400);
        }

        return response()->json([
            'message' => 'Empresa creada exitosamente',
            'password' => $password
        ]);
    }
}