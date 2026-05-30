<?php

namespace Modules\CustomField\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomFieldRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,textarea,select,checkbox,date',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'enabled_for_documents' => 'boolean',
            'enabled_for_sale_notes' => 'boolean',
            'enabled_for_dispatches' => 'boolean',
            'enabled_for_order_notes' => 'boolean',
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
            'name.required' => 'El nombre del campo es requerido',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'type.required' => 'El tipo de campo es requerido',
            'type.in' => 'El tipo de campo no es válido',
            'options.array' => 'Las opciones deben ser un array válido',
        ];
    }
}
