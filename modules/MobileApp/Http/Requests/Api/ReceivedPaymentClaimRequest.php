<?php

namespace Modules\MobileApp\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validacion del reclamo / actualizacion de un pago recibido.
 *
 * El cliente envia el metodo de pago (Yape/Plin se registran manualmente en el
 * catalogo de metodos de pago y el front los reconoce). El monto y la fecha del
 * pago se toman del propio registro capturado, no del body.
 */
class ReceivedPaymentClaimRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'document_id'            => ['required', 'integer', 'exists:tenant.documents,id'],
            'payment_method_type_id' => ['required', 'exists:tenant.payment_method_types,id'],
            'payment_destination_id' => ['required'],
        ];
    }
}
