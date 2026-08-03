<?php

namespace Modules\Offline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfflineConfigurationRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'is_client'     => ['required', 'boolean'],
            'url_server'    => ['required_if:is_client,true', 'nullable', 'url'],
            'token_server'  => ['required_if:is_client,true', 'nullable', 'string'],
            // El código del terminal identifica el origen de cada venta y se
            // usa como clave del bloque de correlativos: sin él no se puede
            // sincronizar.
            'terminal_code' => ['required_if:is_client,true', 'nullable', 'string', 'max:20'],
            'terminal_name' => ['nullable', 'string', 'max:100'],
            'sync_enabled'  => ['nullable', 'boolean'],
            'sync_interval' => ['nullable', 'integer', 'min:15', 'max:3600'],
            'git_branch'    => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages() {
        return [
            'url_server.required_if'    => 'Indicá la URL del servidor online.',
            'url_server.url'            => 'La URL del servidor no tiene un formato válido.',
            'token_server.required_if'  => 'Indicá el token de acceso al servidor.',
            'terminal_code.required_if' => 'Asigná un código a este terminal (por ejemplo T01).',
            'sync_interval.min'         => 'El intervalo mínimo de sincronización es de 15 segundos.',
        ];
    }
}
