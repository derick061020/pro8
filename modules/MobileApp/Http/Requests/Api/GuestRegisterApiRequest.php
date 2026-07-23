<?php

namespace Modules\MobileApp\Http\Requests\Api;

use App\Models\System\Configuration;
use App\Rules\SubdomainNotLatin;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestRegisterApiRequest extends FormRequest
{
    /**
     * Solo se permite si el autoregistro esta habilitado (equivale al
     * middleware enable.guest.register del flujo web).
     *
     * @return bool
     */
    public function authorize()
    {
        $configuration = Configuration::first();

        return $configuration && $configuration->enable_guest_register;
    }

    /**
     * Reglas espejo de App\Http\Requests\System\GuestRegisterClientRequest,
     * mas la disponibilidad del subdominio (para devolver 422 limpio en lugar
     * de la excepcion generica que lanza ClientController::store).
     *
     * @return array
     */
    public function rules()
    {
        return [
            'number' => [
                'required',
                'numeric',
                Rule::unique('system.clients', 'number'),
            ],
            'name' => [
                'required',
                Rule::unique('system.clients', 'name'),
            ],
            'subdomain' => [
                'required',
                new SubdomainNotLatin,
                function ($attribute, $value, $fail) {
                    $subdomain = strtolower(trim($value));
                    $fqdn = $subdomain . '.' . config('tenant.app_url_base');

                    if (Hostname::where('fqdn', $fqdn)->exists()) {
                        $fail('Este subdominio ya está en uso.');
                    }
                },
            ],
            'password' => [
                'required',
                'min:6',
            ],
            'email' => [
                'required',
                'email',
            ],
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('system.plans', 'id')->where('locked', 0),
            ],
        ];
    }
}
