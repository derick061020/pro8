<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Client;
use App\Models\System\Configuration;
use Hyn\Tenancy\Models\Hostname;
use App\Http\Requests\System\{
    GuestRegisterClientRequest,
    SendEmailGuestRegisterRequest,
};
use App\Traits\GuestRegisterTrait;
use Exception;
use Illuminate\Http\Request;
use App\Helpers\GuestRegisterHelper;
use App\Http\Controllers\System\ClientController;


class GuestRegisterController extends Controller
{
    use GuestRegisterTrait;

    private const DEFAULT_BACKGROUND_IMAGE_LOGIN = 'fondo-5.svg';

    public function index(Request $request)
    {
        $plan_id = $request->query('plan_id');
        return view('system.guest-register.index', $this->getDataRegister($plan_id));
    }

    public function disabled()
    {
        return view('system.guest-register.disabled', $this->getDataRegister());
    }

    /**
     * Verifica si un subdominio está disponible (no existe el hostname/fqdn).
     *
     * @param  string  $subdomain
     * @return array
     */
    public function checkSubdomain($subdomain)
    {
        $subdomain = strtolower(trim($subdomain));

        if ($subdomain === '' || !preg_match('/^[a-z0-9]+$/', $subdomain)) {
            return [
                'success'   => true,
                'available' => false,
                'message'   => 'Solo se permiten letras y números, sin símbolos.',
            ];
        }

        $fqdn = $subdomain . '.' . config('tenant.app_url_base');
        $taken = Hostname::where('fqdn', $fqdn)->exists();

        return [
            'success'   => true,
            'available' => !$taken,
            'message'   => $taken
                ? 'Este subdominio ya está en uso.'
                : 'Subdominio disponible.',
        ];
    }

    /**
     * Verifica si un RUC/número de documento ya está registrado como cliente.
     *
     * @param  string  $number
     * @return array
     */
    public function checkRuc($number)
    {
        $number = trim($number);

        if ($number === '' || !preg_match('/^\d{8,11}$/', $number)) {
            return [
                'success'   => true,
                'available' => false,
                'message'   => 'Ingresa un número de documento válido.',
            ];
        }

        $taken = Client::where('number', $number)->exists();

        return [
            'success'   => true,
            'available' => !$taken,
            'message'   => $taken
                ? 'Este RUC ya está registrado.'
                : 'RUC disponible.',
        ];
    }

    /**
     *
     * @return array
     */
    private function getDataRegister(string $plan_id = null)
    {
        $configuration = Configuration::select(['use_login_global', 'login', 'guest_register_plan_id', 'validate_ruc_register'])->first();
        $use_login_global = $configuration->use_login_global;
        $login = $configuration->login;
        $plan_default = is_null($plan_id) ?  $configuration->guest_register_plan_id : $plan_id;
        $validate_ruc_register = (bool) $configuration->validate_ruc_register;
        $default_background_image_login = asset('images/'.self::DEFAULT_BACKGROUND_IMAGE_LOGIN);
        $base_url = '.' . config('tenant.app_url_base');

        $plans = $this->getRegisterPlans();

        return compact('login', 'use_login_global', 'default_background_image_login', 'base_url', 'plans', 'plan_default', 'validate_ruc_register');
    }

    /**
     * key = encrypt_client_id
     * 
     * @param  Request $request
     * @return array
     */
    public function resendEmail(SendEmailGuestRegisterRequest $request)
    {
        return (new GuestRegisterHelper())->sendEmail($request->user_id, $request->email, $request->key);
    }

    /**
     *
     * @param  string $id
     * @param  string $hash
     * @param  string $client_id
     * @param  Request $request
     * @return void
     */
    public function verifyGuestRegisteredEmail($id, $hash, $client_id, Request $request)
    {
        // $this->validateSignedRoute($request);

        $helper = new GuestRegisterHelper();
        $decrypt_client_id = $helper->decryptValue($client_id);
        $this->validateDecryptClient($decrypt_client_id);

        $client = $this->getClient($decrypt_client_id);
        $this->changeClientConnection($client);

        $user = $this->getFirstUser();
        
        $this->validateUserKey($user, $id);
        $this->validateHash($user, $hash);


        $data = $this->updateDataVerifiedUser($user);

        if($data['success'])
        {
            return redirect()->to($this->redirectUrl($client->hostname->fqdn, $helper->encryptValue("{$user->email_verified_at}_{$user->id}")));
        }

        return abort(500, 'Error al validar correo.');
    }

    /**
     * 
     * Crear cuenta
     *
     * @param  GuestRegisterClientRequest $request
     * @return array
     */
    public function register(GuestRegisterClientRequest $request)
    {
        try
        {
            $inputs = $this->inputsToRegister($request);

            $response = app(ClientController::class)->store($inputs);

            if(!$response['success']) return $response;

            // $payment_uuid = $response['guest_register']['payment_uuid'] ?? null;

            return [
                'success' => true,
                'message' => 'Cuenta registrada correctamente.',
                // 'guest_register' => $response['guest_register'],
                // 'payment_url' => $payment_uuid ? route('payment.public.show', ['uuid' => $payment_uuid]) : null,
            ];

        }
        catch (Exception $e)
        {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }


    }

}