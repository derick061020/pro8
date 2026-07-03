<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyPseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'send_document_to_pse' => ['required', 'boolean'],
            'pse_provider_id'      => ['required_if:send_document_to_pse,true'],
            'user_pse'             => ['required_if:send_document_to_pse,true'],
            'password_pse'         => [
                // requerido solo si está habilitado Y no hay contraseña guardada aún
                Rule::requiredIf(function () {
                    return $this->boolean('send_document_to_pse')
                        && empty(\App\Models\Tenant\Company::first()->password_pse);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'pse_provider_id.required_if' => 'Debe seleccionar un proveedor PSE.',
            'user_pse.required_if'        => 'Debe ingresar el usuario de autenticación.',
            'password_pse.required'       => 'Debe ingresar la contraseña de autenticación.',
        ];
    }
}