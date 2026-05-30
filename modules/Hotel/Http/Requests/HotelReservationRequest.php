<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'customer_id'              => 'required|numeric',
            'customer'                 => 'required',
            'customer.name'            => 'required',
            'customer.address'         => 'nullable|string|max:255',
            'notes'                    => 'nullable|max:250',
            'adults'                   => 'required|numeric|min:1',
            'children'                 => 'required|numeric|min:0',
            'towels'                   => 'required|numeric|min:1',
            'hotel_room_id'            => 'required|numeric',
            'hotel_rate_id'            => 'required|numeric',
            'duration'                 => 'required|numeric|min:1',
            'quantity_persons'         => 'required|numeric|min:1',
            'payment_status'           => 'required|in:PAID,DEBT,ADVANCE',
            'input_date'               => 'required|date_format:Y-m-d',
            'input_time'               => 'required|date_format:H:i',
            'output_date'              => 'required|date_format:Y-m-d',
            'output_time'              => 'required|date_format:H:i',
            'affectation_igv_type_id'  => 'required',
            'data_persons'             => 'required',
            'license_plate'            => 'nullable|string|max:20',
            'travel_reason'            => 'nullable|in:visita,trabajo,estudio,religion,salud,compras,otros',
        ];

        // Product y pagos solo obligatorios si payment_status es PAID o ADVANCE
        if (in_array($this->payment_status, ['PAID', 'ADVANCE'], true)) {
            $rules['product'] = 'required';
            $rules['rent_payment.payment_method_type_id'] = 'required';
            $rules['rent_payment.payment_destination_id'] = 'required';
            $rules['rent_payment.payment'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'rent_payment.payment_method_type_id.required' => 'El método de pago es obligatorio cuando el estado es PAGADO o ADELANTO.',
            'rent_payment.payment_destination_id.required' => 'El destino del pago es obligatorio cuando el estado es PAGADO o ADELANTO.',
        ];
    }
}
