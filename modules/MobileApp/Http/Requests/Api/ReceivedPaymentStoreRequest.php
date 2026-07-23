<?php

namespace Modules\MobileApp\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\MobileApp\Models\ReceivedPayment;

/**
 * Validacion del push del equipo-oido.
 *
 * La idempotencia se resuelve en el controlador a partir de dedup_hash,
 * aqui solo se valida que el payload del pago capturado este completo.
 */
class ReceivedPaymentStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'provider'     => ['required'],
            'amount'       => ['required', 'numeric', 'gt:0'],
            'sender_name'  => ['nullable', 'string', 'max:255'],
            'raw_text'     => ['required', 'string'],
            'package_name' => ['required', 'string', 'max:255'],
            'posted_at'    => ['required', 'date'],
            'device_id'    => ['required', 'string', 'max:255'],
            'dedup_hash'   => ['required', 'string', 'max:255'],
        ];
    }
}
