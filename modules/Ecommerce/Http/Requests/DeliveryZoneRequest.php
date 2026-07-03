<?php

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida el payload de creación / edición de una zona de delivery.
 */
class DeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => ['required', 'string', 'max:150'],
            'price'                      => ['required', 'numeric', 'min:0'],
            'specify_zone'               => ['nullable', 'boolean'],
            'active'                     => ['nullable', 'boolean'],
            'locations'                  => ['required', 'array', 'min:1'],
            'locations.*.department_id'  => ['required', 'string', 'size:2'],
            'locations.*.province_id'    => ['nullable', 'string', 'size:4'],
            'locations.*.district_id'    => ['nullable', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                      => 'El nombre de la zona es obligatorio.',
            'price.required'                     => 'El precio de envío es obligatorio.',
            'price.min'                          => 'El precio no puede ser negativo.',
            'locations.required'                 => 'Debe agregar al menos una ubicación de cobertura.',
            'locations.min'                      => 'Debe agregar al menos una ubicación de cobertura.',
            'locations.*.department_id.required' => 'Cada ubicación debe tener un departamento.',
        ];
    }
}
