<?php

namespace Modules\Webhook\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Webhook\Services\WebhookEvents;

class WebhookSubscriptionRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para hacer esta solicitud
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtener las reglas de validación
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:500',
            'events' => 'required|array|min:1',
            'events.*' => [Rule::in(WebhookEvents::keys())],
            'is_active' => 'boolean',
        ];
    }

    /**
     * Obtener los mensajes de validación
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede exceder 100 caracteres',
            'url.required' => 'La URL de destino es requerida',
            'url.url' => 'La URL de destino no es válida',
            'url.max' => 'La URL no puede exceder 500 caracteres',
            'events.required' => 'Debe seleccionar al menos un evento',
            'events.min' => 'Debe seleccionar al menos un evento',
            'events.*.in' => 'Uno de los eventos seleccionados no es válido',
        ];
    }
}
