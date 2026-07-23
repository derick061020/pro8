<?php

namespace Modules\Dispatch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispatcherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        $doc_type = $this->input('identity_document_type_id');

        $number_rules = [
            'required',
            Rule::unique('tenant.dispatchers')->ignore($id),
        ];

        if ($doc_type === '6') {            // RUC
            $number_rules[] = 'regex:/^(10|15|16|17|20)\d{9}$/';
        } elseif ($doc_type === '1') {      // DNI
            $number_rules[] = 'digits:8';
        } else {                            // CE, pasaporte, otros
            $number_rules[] = 'regex:/^[a-zA-Z0-9]{4,15}$/';
        }

        return [
            'identity_document_type_id' => ['required'],
            'number' => $number_rules,
            'name' => ['required', 'string', 'min:2', 'regex:/[A-Za-zÁÉÍÓÚáéíóúÑñ]/'],
            'address' => ['nullable', 'string', 'min:3', 'regex:/[A-Za-zÁÉÍÓÚáéíóúÑñ]/'],
            'number_mtc' => ['nullable', 'regex:/^[a-zA-Z0-9]+$/', 'max:12'],
        ];
    }

    public function messages()
    {
        return [
            'identity_document_type_id.required' => 'Seleccione el tipo de documento.',
            'number.required' => 'El número es obligatorio.',
            'number.unique' => 'Ya existe un transportista con este número.',
            'number.regex' => 'El número no tiene un formato válido para el tipo de documento seleccionado.',
            'number.digits' => 'El número debe tener 8 dígitos.',
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.regex' => 'El nombre debe contener al menos una letra.',
            'address.min' => 'La dirección debe tener al menos 3 caracteres.',
            'address.regex' => 'La dirección debe contener al menos una letra.',
            'number_mtc.regex' => 'El MTC solo admite letras y números.',
            'number_mtc.max' => 'El MTC no debe superar los 12 caracteres.',
        ];
    }
}
