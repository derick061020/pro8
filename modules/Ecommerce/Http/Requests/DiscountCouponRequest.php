<?php

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación para creación y edición de cupones de descuento.
 */
class DiscountCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'code' se valida en el Controller
            'type'                  => ['required', Rule::in(['percentage', 'fixed'])],
            'amount'                => ['required', 'numeric', 'min:0'],
            'has_purchase_limits'   => ['boolean'],
            'min_amount'            => ['nullable', 'numeric', 'min:0'],
            'max_amount'            => ['nullable', 'numeric', 'min:0', 'gte:min_amount'],
            'has_usage_limits'      => ['boolean'],
            'max_total_uses'        => ['nullable', 'integer', 'min:1'],
            'max_uses_per_customer' => ['nullable', 'integer', 'min:1'],
            'expires_at'            => ['nullable', 'date'],
            'free_shipping'         => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'    => 'Debe seleccionar el tipo de descuento.',
            'type.in'          => 'El tipo debe ser porcentaje o monto fijo.',
            'amount.required'  => 'El monto del descuento es obligatorio.',
            'amount.numeric'   => 'El monto debe ser un número válido.',
            'amount.min'       => 'El monto no puede ser negativo.',
            'max_amount.gte'   => 'El monto máximo debe ser mayor o igual al monto mínimo.',
            'expires_at.date'  => 'La fecha de vencimiento no es válida.',
        ];
    }
}
